<?php

require_once __DIR__ . '/../_private/env.php';
loadEnv(__DIR__ . '/../_private/.env');
if (PHP_SAPI !== 'cli' && env('GP_DB_ENV') === 'production' && env('GP_ENABLE_SQLITE') !== '1') {
    http_response_code(404);
    exit;
}

$location = "localhost";
$sqlitePath = getenv('SQLITE_DB_PATH') ?: __DIR__ . DIRECTORY_SEPARATOR . 'chaitalk.sqlite';

try {
    $sqliteDir = dirname($sqlitePath);
    if (!is_dir($sqliteDir)) {
        throw new RuntimeException("SQLite directory does not exist: {$sqliteDir}");
    }

    $pdo = new PDO('sqlite:' . $sqlitePath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    $schemaPath = __DIR__ . DIRECTORY_SEPARATOR . 'sqlite_schema.sql';
    if (is_file($schemaPath)) {
        $pdo->exec(file_get_contents($schemaPath));
    }

    sqliteEnsureColumn($pdo, 'gulpin_user', 'p_mobile', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'gulpin_user', 'phone', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'gulpin_user', 'sdate', 'TEXT');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'birthday', 'TEXT');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'birth_year', 'INTEGER');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'material_code', 'TEXT');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'edate', 'TEXT');
    sqliteEnsureColumn($pdo, 'gulpin_porlist', 'invoice', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'gulpin_porlist', 'refund', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'gulpin_porlist', 'pg_amount', 'INTEGER');
    $pdo->exec("CREATE TABLE IF NOT EXISTS gulpin_payments (
        order_id TEXT PRIMARY KEY, source TEXT NOT NULL, source_id TEXT NOT NULL, user_id TEXT NOT NULL,
        order_name TEXT NOT NULL, amount INTEGER NOT NULL, status TEXT NOT NULL DEFAULT 'READY', payment_key TEXT,
        method TEXT, approved_at TEXT, receipt_url TEXT, fail_reason TEXT, canceled_at TEXT, cancel_reason TEXT,
        merchant_id TEXT, key_mode TEXT, created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_gulpin_payments_source ON gulpin_payments (source, source_id)');
    sqliteEnsureColumn($pdo, 'gulpin_payments', 'merchant_id', 'TEXT');
    sqliteEnsureColumn($pdo, 'gulpin_payments', 'key_mode', 'TEXT');
    // 비밀번호 임시 여부 / 본인 재설정 시도 기록 — Server/password_policy.php
    sqliteEnsureColumn($pdo, 'gulpin_user', 'pw_temp', 'INTEGER NOT NULL DEFAULT 0');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'password_admin_view', 'TEXT');
    $pdo->exec('CREATE TABLE IF NOT EXISTS gulpin_pw_reset (id TEXT NOT NULL, ip TEXT NOT NULL, ok INTEGER NOT NULL DEFAULT 0, rdate TEXT NOT NULL)');
    // 홈스쿨 교재 주문 — Server/homeschool_policy.php
    $homeOrderTable = 'CREATE TABLE IF NOT EXISTS gulpin_home_order_stat (id INTEGER PRIMARY KEY AUTOINCREMENT, branch_id TEXT NOT NULL,
        teacher_id TEXT NOT NULL, student_id TEXT NOT NULL, material_title TEXT NOT NULL, material_code TEXT NOT NULL,
        order_date TEXT NOT NULL, complete_date TEXT, status INTEGER, order_month TEXT NOT NULL, UNIQUE (student_id, order_month, material_code))';
    $oldHomeOrder = $pdo->query("SELECT sql FROM sqlite_master WHERE name = 'gulpin_home_order_stat'")->fetchColumn();
    if ($oldHomeOrder && strpos($oldHomeOrder, 'UNIQUE (student_id, order_month)') !== false) {   // 이전 키(학생당 월 1건) → 테이블 재생성
        $pdo->exec('ALTER TABLE gulpin_home_order_stat RENAME TO gulpin_home_order_stat_old');
        $pdo->exec($homeOrderTable);
        $pdo->exec('INSERT INTO gulpin_home_order_stat SELECT * FROM gulpin_home_order_stat_old');
        $pdo->exec('DROP TABLE gulpin_home_order_stat_old');
    }
    $pdo->exec($homeOrderTable);
    // 교재 신청 기간 설정, 다음 단계 자동 변경 기록
    $pdo->exec('CREATE TABLE IF NOT EXISTS gulpin_setting (skey TEXT PRIMARY KEY, svalue TEXT NOT NULL)');
    sqliteEnsureColumn($pdo, 'gulpin_user', 'material_auto_month', 'TEXT');
} catch (Throwable $e) {
    error_log('[sqlite_dbinit] ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('error' => 'DB 연결 중 오류가 발생했습니다.'), JSON_UNESCAPED_UNICODE);
    exit;
}

function sqliteEnsureColumn(PDO $pdo, string $table, string $column, string $definition): void
{
    $stmt = $pdo->query("PRAGMA table_info({$table})");
    $columns = $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN, 1) : array();

    if (!in_array($column, $columns, true)) {
        $pdo->exec("ALTER TABLE {$table} ADD COLUMN {$column} {$definition}");
    }
}
