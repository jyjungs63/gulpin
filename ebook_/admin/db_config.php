<?php
/**
 * ========================================
 * EPLAT Ebook - 데이터베이스 연결 설정
 * ========================================
 */
$fullURL  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")
          . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";

$isLocal  = (strpos($fullURL, "localhost") !== false)
         || (strpos($fullURL, "127.0.0.1") !== false)
         || (strpos($fullURL, "192.168.") !== false)
         || (strpos($fullURL, "jyjungs1963") !== false)  // nas server 
         || (strpos($fullURL, "10.") !== false);

// 데이터베이스 설정
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_NAME', 'happyzip1');
if ($isLocal) {
    define('DB_USER', 'root');                   // 데이터베이스 사용자명
    define('DB_PASS', 'manager');                // 데이터베이스 비밀번호 (XAMPP 기본값은 빈 문자열) 
}
else {
    // 운영 서버 데이터베이스 설정
    define('DB_USER', 'happyzip1');
    define('DB_PASS', 'skl329350281@');
}


/**
 * 데이터베이스 연결 클래스
 */
class Database {
    private static $instance = null;
    private $conn;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch(PDOException $e) {
            die("데이터베이스 연결 실패: " . $e->getMessage());
        }
    }
    
    /**
     * 싱글톤 인스턴스 가져오기
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * 연결 객체 반환
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * 복제 방지
     */
    private function __clone() {}
    
    /**
     * unserialize 방지
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}

/**
 * JSON 응답 헬퍼 함수
 */
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * 에러 응답 헬퍼 함수
 */
function sendErrorResponse($message, $statusCode = 400) {
    sendJsonResponse([
        'success' => false,
        'error' => $message
    ], $statusCode);
}

/**
 * 성공 응답 헬퍼 함수
 */
function sendSuccessResponse($data = [], $message = 'Success') {
    sendJsonResponse([
        'success' => true,
        'message' => $message,
        'data' => $data
    ]);
}

/**
 * SQL 인젝션 방지를 위한 입력값 정제
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * POST 데이터 가져오기
 */
function getPostData() {
    $data = json_decode(file_get_contents('php://input'), true);
    return $data ?: $_POST;
}
?>
