<?php

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

    $schemaPath = __DIR__ . DIRECTORY_SEPARATOR . 'sqlite_schema.sql';
    if (is_file($schemaPath)) {
        $pdo->exec(file_get_contents($schemaPath));
    }

    sqliteEnsureColumn($pdo, 'chaitalk_user', 'p_mobile', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'chaitalk_user', 'phone', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'chaitalk_porlist', 'invoice', "TEXT DEFAULT ''");
    sqliteEnsureColumn($pdo, 'chaitalk_porlist', 'refund', "TEXT DEFAULT ''");
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array("result:" => $e->getMessage()), JSON_UNESCAPED_UNICODE);
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
