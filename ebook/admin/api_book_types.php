<?php
/**
 * ========================================
 * EPLAT Ebook - 책 타입 API (display_mode 추가)
 * ========================================
 */

require_once 'db_config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Preflight 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGet($db);
            break;
        case 'POST':
            handlePost($db);
            break;
        case 'PUT':
            handlePut($db);
            break;
        case 'DELETE':
            handleDelete($db);
            break;
        default:
            sendErrorResponse('지원하지 않는 메서드입니다.', 405);
    }
} catch (Exception $e) {
    sendErrorResponse($e->getMessage(), 500);
}

/**
 * GET - 책 타입 목록 조회
 */
function handleGet($db) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($id) {
        // 단일 책 타입 조회 (category_name JOIN)
        $stmt = $db->prepare("
            SELECT bt.*, c.category_name,
                   COALESCE(bt.category_code, dc.category_code) AS effective_category_code,
                   COALESCE(c.category_name, dc.category_name) AS effective_category_name
            FROM book_types bt
            LEFT JOIN system_config_categories c  ON bt.category_code = c.category_code
            LEFT JOIN system_config_categories dc ON dc.is_default = 1
            WHERE bt.id = ?
        ");
        $stmt->execute([$id]);
        $result = $stmt->fetch();

        if (!$result) sendErrorResponse('책 타입을 찾을 수 없습니다.', 404);
        sendSuccessResponse($result);
    } else {
        // 전체 목록 조회 (category_name JOIN)
        $activeOnly = isset($_GET['active_only'])
            ? filter_var($_GET['active_only'], FILTER_VALIDATE_BOOLEAN) : false;

        $sql = "
            SELECT bt.*, c.category_name,
                   COALESCE(bt.category_code, dc.category_code) AS effective_category_code,
                   COALESCE(c.category_name, dc.category_name) AS effective_category_name
            FROM book_types bt
            LEFT JOIN system_config_categories c  ON bt.category_code = c.category_code
            LEFT JOIN system_config_categories dc ON dc.is_default = 1
        ";
        if ($activeOnly) $sql .= " WHERE bt.is_active = 1";
        $sql .= " ORDER BY bt.display_order, bt.type_name";

        $stmt = $db->query($sql);
        sendSuccessResponse($stmt->fetchAll());
    }
}

/**
 * POST - 새 책 타입 추가
 */
function handlePost($db) {
    $data = getPostData();
    
    // 필수 필드 검증
    $required = ['type_code', 'type_name', 'directory_path'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendErrorResponse("'{$field}' 필드는 필수입니다.");
        }
    }
    
    // 중복 체크
    $stmt = $db->prepare("SELECT id FROM book_types WHERE type_code = ?");
    $stmt->execute([$data['type_code']]);
    if ($stmt->fetch()) {
        sendErrorResponse('이미 존재하는 타입 코드입니다.');
    }
    
    // ========================================
    // display_mode 기본값 설정
    // ========================================
    $displayMode = isset($data['display_mode']) ? $data['display_mode'] : 'double';
    
    // 유효성 검증 (single 또는 double만 허용)
    if (!in_array($displayMode, ['single', 'double'])) {
        $displayMode = 'double';
    }
    
    // 카테고리 코드 (NULL 허용 - NULL이면 기본 카테고리 사용)
    $categoryCode = !empty($data['category_code']) ? $data['category_code'] : null;

    // 데이터 삽입
    $stmt = $db->prepare("
        INSERT INTO book_types
        (type_code, type_name, directory_path, display_mode, category_code, is_active, display_order)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $isActive     = isset($data['is_active'])     ? intval($data['is_active'])     : 1;
    $displayOrder = isset($data['display_order']) ? intval($data['display_order']) : 0;

    $stmt->execute([
        $data['type_code'],
        $data['type_name'],
        $data['directory_path'],
        $displayMode,
        $categoryCode,
        $isActive,
        $displayOrder
    ]);
    
    $newId = $db->lastInsertId();
    
    sendSuccessResponse([
        'id' => $newId
    ], '책 타입이 추가되었습니다.');
}

/**
 * PUT - 책 타입 수정
 */
function handlePut($db) {
    $data = getPostData();
    
    if (empty($data['id'])) {
        sendErrorResponse('ID가 필요합니다.');
    }
    
    $id = intval($data['id']);
    
    // 존재 여부 확인
    $stmt = $db->prepare("SELECT id FROM book_types WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        sendErrorResponse('책 타입을 찾을 수 없습니다.', 404);
    }
    
    // ========================================
    // display_mode 유효성 검증
    // ========================================
    if (isset($data['display_mode'])) {
        if (!in_array($data['display_mode'], ['single', 'double'])) {
            sendErrorResponse('display_mode는 "single" 또는 "double"만 가능합니다.');
        }
    }
    
    // 업데이트할 필드 준비
    $fields = [];
    $values = [];
    
    // display_mode + category_code 포함
    $allowedFields = ['type_code', 'type_name', 'directory_path', 'display_mode', 'category_code', 'is_active', 'display_order'];
    
    foreach ($allowedFields as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $values[] = $data[$field];
        }
    }
    
    if (empty($fields)) {
        sendErrorResponse('수정할 데이터가 없습니다.');
    }
    
    $values[] = $id;
    
    $sql = "UPDATE book_types SET " . implode(', ', $fields) . " WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute($values);
    
    sendSuccessResponse([], '책 타입이 수정되었습니다.');
}

/**
 * DELETE - 책 타입 삭제
 */
function handleDelete($db) {
    $id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if (!$id) {
        sendErrorResponse('ID가 필요합니다.');
    }
    
    // 존재 여부 확인
    $stmt = $db->prepare("SELECT id FROM book_types WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        sendErrorResponse('책 타입을 찾을 수 없습니다.', 404);
    }
    
    // 삭제
    $stmt = $db->prepare("DELETE FROM book_types WHERE id = ?");
    $stmt->execute([$id]);
    
    sendSuccessResponse([], '책 타입이 삭제되었습니다.');
}
?>