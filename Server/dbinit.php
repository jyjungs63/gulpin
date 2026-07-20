<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$location = "localhost";
$host = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = stripos($host, 'localhost') !== false || strpos($host, '10.15') !== false || strpos($host, "jyjungs1963") !== false;;

try {
    if ($isLocal) {
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbUser = getenv('DB_USER') ?: 'root';
        $dbPass = getenv('DB_PASS') ?: 'manager';
        $dbName = getenv('DB_NAME') ?: 'happyzip1';
    } else {
        $dbHost = getenv('DB_HOST') ?: 'localhost';
        $dbUser = getenv('DB_USER') ?: 'happyzip1';
        $dbPass = getenv('DB_PASS') ?: 'skl329350281@';
        $dbName = getenv('DB_NAME') ?: 'happyzip1';
        $location = "chaitalk";
    }

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    $conn->set_charset('utf8mb4');
    ensureShoppingTables($conn);
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'error' => 'database_connection_failed',
        'message' => $e->getMessage(),
        'code' => $e->getCode(),
        'database' => array(
            'host' => $dbHost,
            'name' => $dbName
        )
    ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
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
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $conn->query("
        INSERT INTO chaitalk_user (id, name, password, role, confirm, status)
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
