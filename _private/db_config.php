<?php
// db_config.php

// 이 파일은 서버 내부 PHP에서만 불러온다. URL 직접 호출은 거부한다.
if (!defined('GULPIN_INTERNAL_DB_CONFIG')) {
    http_response_code(404);
    exit;
}

$fullURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
          . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


$isLocal  = (strpos($fullURL, "localhost") !== false)
         || (strpos($fullURL, "127.0.0.1") !== false)
         || (strpos($fullURL, "192.168.") !== false)
         || (strpos($fullURL, "10.") !== false);

// DB 접속 정보는 _private/.env 에서 읽는다 (env.php)
require_once __DIR__ . '/env.php';
loadEnv(__DIR__ . '/.env');

$charset = 'utf8mb4';     // 이모지 및 다국어 지원을 위한 설정

if ($isLocal) {
    $host = env('LOCAL_DB_HOST', 'localhost');
    $db   = env('LOCAL_DB_NAME');
    $user = env('LOCAL_DB_USER');
    $pass = env('LOCAL_DB_PASS');
}
else
{
    $host = env('PROD_DB_HOST', 'localhost');
    $db   = env('PROD_DB_NAME');
    $user = env('PROD_DB_USER');
    $pass = env('PROD_DB_PASS');
}


// PDO 접속 옵션 설정
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // 에러 발생 시 예외를 던짐
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // 결과를 연관 배열로 가져옴
    PDO::ATTR_EMULATE_PREPARES   => false,                  // 실제 Prepared Statement 사용 (보안 강화)
];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    // 전역 변수 $pdo 생성
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // 상세 접속 오류는 서버 로그에만 남기고 외부에는 DB 정보를 노출하지 않는다.
    error_log('[db_config] database connection failed: ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(
        ['status' => 'error', 'message' => '데이터베이스 연결 중 오류가 발생했습니다.'],
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
    );
    exit;
}
?>
