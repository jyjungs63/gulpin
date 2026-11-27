<?php

// 이 파일은 Server API에서만 불러온다. URL로 직접 실행하는 요청은 거부한다.
if (!defined('GULPIN_INTERNAL_BOOTSTRAP')) {
    http_response_code(404);
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// DB 접속 정보는 _private/.env 에서 읽는다 (env.php)
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/.env');

$location = "localhost";
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = stripos($host, 'localhost') !== false
    || stripos($host, '127.0.0.1') !== false
    || stripos($host, '::1') !== false
    || strpos($host, '10.15') !== false
    || strpos($host, '192.168.') !== false
    || strpos($host, 'jyjungs1963') !== false;
$dbEnvironment = env('GP_DB_ENV');
if ($dbEnvironment === 'production' || $dbEnvironment === 'local') {
    $isLocal = $dbEnvironment === 'local';
}

try {
    if ($isLocal) {
        $dbHost = env('LOCAL_DB_HOST', 'localhost');
        $dbUser = env('LOCAL_DB_USER');
        $dbPass = env('LOCAL_DB_PASS');
        $dbName = env('LOCAL_DB_NAME');
    } else {
        $dbHost = env('PROD_DB_HOST', 'localhost');
        $dbUser = env('PROD_DB_USER');
        $dbPass = env('PROD_DB_PASS');
        $dbName = env('PROD_DB_NAME');
        $location = "chaitalk";
    }

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');
    try {
        ensureColumn($conn, 'gulpin_user', 'birthday', 'DATE NULL');
    } catch (mysqli_sql_exception $e) {
        error_log('[dbinit] birthday migration: ' . $e->getMessage());
    }
    // 테이블/컬럼 준비 — 실패해도(DB 권한·버전 차이) 로그인 등 다른 요청은 계속 처리한다
    try {
        ensureBranchTables($conn);
        ensureShoppingTables($conn);
        ensureStudentColumns($conn);
        ensureHomeOrderTable($conn);
        ensurePaymentTable($conn);
    } catch (mysqli_sql_exception $e) {
        error_log('[dbinit] ' . $e->getMessage());
    }
    // 비밀번호 해시/권한 기반 재설정에 필요한 컬럼
    try {
        ensurePasswordColumns($conn);
    } catch (mysqli_sql_exception $e) {
        error_log('[dbinit] ' . $e->getMessage());
    }
} catch (mysqli_sql_exception $e) {
    error_log('[dbinit] database connection failed: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'error' => 'database_connection_failed',
        'message' => '데이터베이스 연결 중 오류가 발생했습니다.'
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function ensureBranchTables(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS gulpin_centers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            sido VARCHAR(50) NOT NULL,
            gugun VARCHAR(50) NOT NULL,
            name VARCHAR(255) NOT NULL,
            area VARCHAR(500) NOT NULL DEFAULT '',
            dong TEXT NOT NULL,
            address VARCHAR(500) NOT NULL,
            phone VARCHAR(50) NOT NULL,
            zip_code VARCHAR(20) NOT NULL DEFAULT '',
            road_address VARCHAR(500) NOT NULL DEFAULT '',
            address_detail VARCHAR(500) NOT NULL DEFAULT '',
            INDEX idx_gulpin_centers_region (sido, gugun)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 기존 테이블에도 등록 폼의 주소 세부 항목을 보존할 컬럼을 추가합니다.
    ensureColumn($conn, 'gulpin_centers', 'zip_code', "VARCHAR(20) NOT NULL DEFAULT ''");
    ensureColumn($conn, 'gulpin_centers', 'road_address', "VARCHAR(500) NOT NULL DEFAULT ''");
    ensureColumn($conn, 'gulpin_centers', 'address_detail', "VARCHAR(500) NOT NULL DEFAULT ''");
}

// 선생님 학생관리(student_policy.php) 수업 시작/종료일
function ensureStudentColumns(mysqli $conn): void
{
    ensureColumn($conn, 'gulpin_user', 'birth_year', 'SMALLINT UNSIGNED NULL');
    ensureColumn($conn, 'gulpin_user', 'material_code', 'VARCHAR(64) NULL');
    ensureColumn($conn, 'gulpin_user', 'sdate', "DATE NULL COMMENT '수업시작일'");
    ensureColumn($conn, 'gulpin_user', 'edate', "DATE NULL COMMENT '수업종료일'");
}

// 카드 결제 기록 — Server/payment_policy.php (전자상거래법: 대금결제 기록 5년 보관, 삭제하지 않는다)
function ensurePaymentTable(mysqli $conn): void
{
    ensureColumn($conn, 'gulpin_porlist', 'pg_amount', 'INT NULL');
    $conn->query("
        CREATE TABLE IF NOT EXISTS gulpin_payments (
            order_id VARCHAR(64) NOT NULL PRIMARY KEY COMMENT '토스 orderId',
            source VARCHAR(10) NOT NULL COMMENT 'por 교재주문 / shop 쇼핑몰',
            source_id VARCHAR(64) NOT NULL,
            user_id VARCHAR(30) NOT NULL,
            order_name VARCHAR(100) NOT NULL,
            amount INT NOT NULL,
            status VARCHAR(10) NOT NULL DEFAULT 'READY' COMMENT 'READY DONE CANCELED FAILED',
            payment_key VARCHAR(200) NULL,
            method VARCHAR(30) NULL,
            approved_at DATETIME NULL,
            receipt_url VARCHAR(500) NULL,
            fail_reason VARCHAR(255) NULL,
            canceled_at DATETIME NULL,
            cancel_reason VARCHAR(255) NULL,
            merchant_id VARCHAR(100) NULL,
            key_mode VARCHAR(4) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_gulpin_payments_source (source, source_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    ensureColumn($conn, 'gulpin_payments', 'merchant_id', 'VARCHAR(100) NULL');
    ensureColumn($conn, 'gulpin_payments', 'key_mode', 'VARCHAR(4) NULL');
}

// 홈스쿨 교재 주문 — Server/homeschool_policy.php (doc/homeschool-orders.sql 과 같게 유지)
function ensureHomeOrderTable(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS gulpin_home_order_stat (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            branch_id VARCHAR(30) NOT NULL COMMENT '홈스쿨 지사 ID',
            teacher_id VARCHAR(30) NOT NULL COMMENT '홈스쿨 선생 ID',
            student_id VARCHAR(30) NOT NULL COMMENT '홈스쿨 학생 ID',
            material_title VARCHAR(100) NOT NULL COMMENT '교재명',
            material_code VARCHAR(64) NOT NULL COMMENT '교재 코드',
            order_date DATETIME NOT NULL COMMENT '주문일',
            complete_date DATETIME NULL COMMENT '주문완료일',
            status TINYINT NULL COMMENT '주문상태 NULL 대기, 1 완료',
            order_month CHAR(7) NOT NULL COMMENT '주문월 YYYY-MM (학생·교재당 월 1건)',
            UNIQUE KEY uq_home_order_student_month_material (student_id, order_month, material_code),
            KEY idx_home_order_branch_month (branch_id, order_month)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    // 이전 키(학생당 월 1건) → 학생·교재당 월 1건
    if ($conn->query("SHOW INDEX FROM gulpin_home_order_stat WHERE Key_name = 'uq_home_order_student_month'")->num_rows > 0) {
        $conn->query("ALTER TABLE gulpin_home_order_stat DROP INDEX uq_home_order_student_month,
                      ADD UNIQUE KEY uq_home_order_student_month_material (student_id, order_month, material_code)");
    }
    // 교재 신청 기간 설정, 다음 단계 자동 변경 기록 (doc/homeschool-order-window.sql 과 같게 유지)
    $conn->query("
        CREATE TABLE IF NOT EXISTS gulpin_setting (
            skey VARCHAR(50) NOT NULL PRIMARY KEY,
            svalue VARCHAR(255) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    ensureColumn($conn, 'gulpin_user', 'material_auto_month', "CHAR(7) NULL COMMENT '다음 단계 자동 변경을 처리한 달 YYYY-MM'");
}

// 본사·지사·선생님 비밀번호 해시(60자+) 저장, 임시 비밀번호 표시, 본인 재설정 시도 기록 — Server/password_policy.php
function ensurePasswordColumns(mysqli $conn): void
{
    ensureColumn($conn, 'gulpin_user', 'password_admin_view', 'TEXT NULL');
    $len = $conn->query("SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
                         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gulpin_user' AND COLUMN_NAME = 'password'")->fetch_row();
    if ($len && (int) $len[0] < 255) {
        $conn->query("ALTER TABLE gulpin_user MODIFY password VARCHAR(255) NOT NULL");
    }
    ensureColumn($conn, 'gulpin_user', 'pw_temp', "TINYINT(1) NOT NULL DEFAULT 0 COMMENT '임시 비밀번호 — 다음 로그인 때 변경'");
    $conn->query("
        CREATE TABLE IF NOT EXISTS gulpin_pw_reset (
            id VARCHAR(30) NOT NULL,
            ip VARCHAR(45) NOT NULL,
            ok TINYINT(1) NOT NULL DEFAULT 0,
            rdate DATETIME NOT NULL,
            KEY idx_pw_reset_id (id, rdate),
            KEY idx_pw_reset_ip (ip, rdate)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
}

/** 컬럼이 없을 때만 추가 — MySQL 은 ADD COLUMN IF NOT EXISTS 를 지원하지 않아(MariaDB 전용) 먼저 확인한다 */
function ensureColumn(mysqli $conn, string $table, string $column, string $definition): void
{
    if ($conn->query("SHOW COLUMNS FROM {$table} LIKE '{$column}'")->num_rows === 0) {
        $conn->query("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}

function ensureShoppingTables(mysqli $conn): void
{
    $conn->query("
        CREATE TABLE IF NOT EXISTS shopping_products (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            image_url VARCHAR(500) NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $conn->query("
        CREATE TABLE IF NOT EXISTS shopping_orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id VARCHAR(30) NULL,
            product_id INT NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            status VARCHAR(30) NOT NULL DEFAULT 'pending',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_shopping_orders_product_id (product_id),
            CONSTRAINT fk_shopping_orders_product
                FOREIGN KEY (product_id) REFERENCES shopping_products(id)
                ON DELETE RESTRICT
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    // 주문서 방식 — 받는 분·배송지·결제금액(서버 계산)·동의 시각. 상품은 shopping_order_items (product_id/quantity 는 첫 상품/총수량)
    ensureColumn($conn, 'shopping_orders', 'amount', "INT NOT NULL DEFAULT 0 COMMENT '결제금액(택배비 포함)'");
    ensureColumn($conn, 'shopping_orders', 'shipping_fee', 'INT NOT NULL DEFAULT 0');
    ensureColumn($conn, 'shopping_orders', 'receiver_name', 'VARCHAR(30) NULL');
    ensureColumn($conn, 'shopping_orders', 'receiver_phone', 'VARCHAR(20) NULL');
    ensureColumn($conn, 'shopping_orders', 'zip', 'VARCHAR(10) NULL');
    ensureColumn($conn, 'shopping_orders', 'addr', 'VARCHAR(255) NULL');
    ensureColumn($conn, 'shopping_orders', 'agreed_at', "DATETIME NULL COMMENT '구매조건·개인정보 수집 동의 시각'");
    ensureColumn($conn, 'shopping_orders', 'checkout_token', 'CHAR(32) CHARACTER SET ascii COLLATE ascii_bin NULL UNIQUE');
    $conn->query("
        CREATE TABLE IF NOT EXISTS shopping_order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            price INT NOT NULL,
            quantity INT NOT NULL,
            INDEX idx_shopping_order_items_order (order_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $conn->query("
        INSERT INTO gulpin_user (id, name, password, role, confirm, status)
        VALUES ('admin', '관리자', 'password', 9, 1, 1)
        ON DUPLICATE KEY UPDATE id = id
    ");

    $result = $conn->query("SELECT COUNT(*) AS count FROM shopping_products");
    $row = $result->fetch_assoc();
    if ((int) $row['count'] > 0) {
        return;
    }

    $conn->query("
        INSERT INTO shopping_products (name, description, price, image_url)
        VALUES
        ('데일리 코튼 셔츠', '가볍고 통기성이 좋은 데일리 셔츠입니다.', 39000, 'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?auto=format&fit=crop&w=900&q=80'),
        ('미니멀 백팩', '노트북과 일상 소지품을 깔끔하게 수납할 수 있는 백팩입니다.', 69000, 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=900&q=80'),
        ('세라믹 머그컵', '따뜻한 음료를 오래 즐기기 좋은 심플한 머그컵입니다.', 16000, 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&w=900&q=80')
    ");
}
