<?php
// db_config.php
$fullURL = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") 
          . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";


$isLocal  = (strpos($fullURL, "localhost") !== false)
         || (strpos($fullURL, "127.0.0.1") !== false)
         || (strpos($fullURL, "192.168.") !== false)
         || (strpos($fullURL, "10.") !== false);

$host = 'localhost';      // 데이터베이스 서버 주소
$db   = 'happyzip1';      // 데이터베이스 이름
$charset = 'utf8mb4';     // 이모지 및 다국어 지원을 위한 설정

if ($isLocal) {
    $user = 'root';           // 데이터베이스 사용자 계정
    $pass = 'manager';        // 데이터베이스 비밀번호
}
else
{
    $user = 'happyzip1';           // 데이터베이스 사용자 계정
    $pass = 'skl329350281@';        // 데이터베이스 비밀번호
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
    // 접속 실패 시 JSON 형태로 에러 출력 (API 환경 대응)
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'DB 접속 실패: ' . $e->getMessage()]);
    exit;
}
?>