<?php

require_once 'sqlite_dbinit.php';

class SQLiteCompatResult
{
    private array $rows;
    private int $index = 0;
    public int $num_rows = 0;

    public function __construct(array $rows)
    {
        $this->rows = $rows;
        $this->num_rows = count($rows);
    }

    public function fetch_assoc()
    {
        return $this->fetch_array();
    }

    public function fetch_array()
    {
        if ($this->index >= $this->num_rows) {
            return null;
        }

        return $this->rows[$this->index++];
    }

    public function free(): void
    {
        $this->rows = array();
        $this->index = 0;
        $this->num_rows = 0;
    }
}

class SQLiteCompatStatement
{
    private PDO $pdo;
    private string $sql;
    private array $params = array();
    public string $error = '';

    public function __construct(PDO $pdo, string $sql)
    {
        $this->pdo = $pdo;
        $this->sql = sqlite_convert_sql($sql);
    }

    public function bind_param(string $types, &...$params): bool
    {
        $this->params = &$params;
        return true;
    }

    public function execute(): bool
    {
        try {
            $stmt = $this->pdo->prepare($this->sql);
            return $stmt->execute(array_values($this->params));
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    public function close(): void
    {
    }
}

class SQLiteCompatConnection
{
    private PDO $pdo;
    public string $error = '';
    public string $connect_error = '';
    public int $insert_id = 0;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function query(string $sql)
    {
        return sqlite_query($this, $sql);
    }

    public function prepare(string $sql)
    {
        return new SQLiteCompatStatement($this->pdo, $sql);
    }

    public function close(): void
    {
    }

    public function escape(string $value): string
    {
        return str_replace("'", "''", $value);
    }

    public function pdo(): PDO
    {
        return $this->pdo;
    }
}

function sqlite_convert_sql(string $sql): string
{
    $sql = preg_replace('/\bNOW\s*\(\s*\)/i', "datetime('now','localtime')", $sql);
    $sql = preg_replace('/DATE_FORMAT\s*\(\s*([^,]+)\s*,\s*[\'"]%Y-%m[\'"]\s*\)/i', "strftime('%Y-%m', $1)", $sql);
    $sql = preg_replace('/\b([A-Za-z_][A-Za-z0-9_]*)\.order\b/i', '$1."order"', $sql);
    return $sql;
}

function sqlite_escape_string(SQLiteCompatConnection $conn, string $value): string
{
    return $conn->escape($value);
}

function sqlite_query(SQLiteCompatConnection $conn, string $sql)
{
    try {
        $sql = sqlite_convert_sql($sql);
        $pdo = $conn->pdo();
        $stmt = $pdo->query($sql);

        if ($stmt === false) {
            $conn->error = 'SQLite query failed';
            return false;
        }

        if (preg_match('/^\s*(SELECT|PRAGMA|WITH)\b/i', $sql)) {
            return new SQLiteCompatResult($stmt->fetchAll(PDO::FETCH_ASSOC));
        }

        $conn->insert_id = (int)$pdo->lastInsertId();
        return true;
    } catch (Throwable $e) {
        $conn->error = $e->getMessage();
        return false;
    }
}

function sqlite_fetch_assoc($result)
{
    return $result instanceof SQLiteCompatResult ? $result->fetch_assoc() : null;
}

function sqlite_fetch_array($result)
{
    return $result instanceof SQLiteCompatResult ? $result->fetch_array() : null;
}

function sqlite_free_result($result): void
{
    if ($result instanceof SQLiteCompatResult) {
        $result->free();
    }
}

function sqlite_num_rows($result): int
{
    return $result instanceof SQLiteCompatResult ? $result->num_rows : 0;
}

function sqlite_start_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        @session_start();
    }
}

$conn = new SQLiteCompatConnection($pdo);
