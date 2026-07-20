<?php
/**
 * ========================================
 * EPLAT Ebook - 시스템 설정 카테고리 API
 * ========================================
 */

require_once 'db_config.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$db = Database::getInstance()->getConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':    handleGet($db);    break;
        case 'POST':   handlePost($db);   break;
        case 'PUT':    handlePut($db);    break;
        case 'DELETE': handleDelete($db); break;
        default: sendErrorResponse('지원하지 않는 메서드입니다.', 405);
    }
} catch (Exception $e) {
    sendErrorResponse($e->getMessage(), 500);
}

/**
 * GET - 카테고리 목록/단일 조회
 */
function handleGet($db) {
    $code = isset($_GET['code']) ? $_GET['code'] : null;

    if ($code) {
        $stmt = $db->prepare("SELECT * FROM system_config_categories WHERE category_code = ?");
        $stmt->execute([$code]);
        $result = $stmt->fetch();
        if (!$result) sendErrorResponse('카테고리를 찾을 수 없습니다.', 404);
        sendSuccessResponse($result);
    } else {
        $stmt = $db->query("SELECT * FROM system_config_categories ORDER BY display_order, category_name");
        sendSuccessResponse($stmt->fetchAll());
    }
}

/**
 * POST - 카테고리 추가
 */
function handlePost($db) {
    $data = getPostData();

    if (empty($data['category_code']) || empty($data['category_name'])) {
        sendErrorResponse('category_code와 category_name은 필수입니다.');
    }

    // 중복 확인
    $stmt = $db->prepare("SELECT id FROM system_config_categories WHERE category_code = ?");
    $stmt->execute([$data['category_code']]);
    if ($stmt->fetch()) sendErrorResponse('이미 존재하는 카테고리 코드입니다.');

    $isDefault = !empty($data['is_default']) ? 1 : 0;
    if ($isDefault) {
        $db->exec("UPDATE system_config_categories SET is_default = 0");
    }

    $rootPath = !empty($data['root_path']) ? $data['root_path'] : '../ebook_new_eplat/';

    $stmt = $db->prepare("
        INSERT INTO system_config_categories
            (category_code, category_name, description, root_path, is_default, display_order)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $data['category_code'],
        $data['category_name'],
        $data['description'] ?? '',
        $rootPath,
        $isDefault,
        intval($data['display_order'] ?? 0)
    ]);

    sendSuccessResponse(['id' => $db->lastInsertId()], '카테고리가 추가되었습니다.');
}

/**
 * PUT - 카테고리 수정 또는 기본 카테고리 지정
 */
function handlePut($db) {
    $data = getPostData();

    if (empty($data['category_code'])) sendErrorResponse('category_code가 필요합니다.');
    $code = $data['category_code'];

    // 기본 카테고리 설정 전용 액션
    if (!empty($data['set_default'])) {
        $stmt = $db->prepare("SELECT id FROM system_config_categories WHERE category_code = ?");
        $stmt->execute([$code]);
        if (!$stmt->fetch()) sendErrorResponse('카테고리를 찾을 수 없습니다.', 404);

        $db->exec("UPDATE system_config_categories SET is_default = 0");
        $stmt = $db->prepare("UPDATE system_config_categories SET is_default = 1 WHERE category_code = ?");
        $stmt->execute([$code]);
        sendSuccessResponse([], "'{$code}' 카테고리가 기본으로 설정되었습니다.");
        return;
    }

    // 일반 수정
    $stmt = $db->prepare("SELECT id FROM system_config_categories WHERE category_code = ?");
    $stmt->execute([$code]);
    if (!$stmt->fetch()) sendErrorResponse('카테고리를 찾을 수 없습니다.', 404);

    // is_default 변경 시 다른 카테고리 해제
    if (isset($data['is_default']) && $data['is_default']) {
        $db->exec("UPDATE system_config_categories SET is_default = 0");
    }

    $fields = [];
    $values = [];
    foreach (['category_name', 'description', 'root_path', 'is_default', 'display_order'] as $f) {
        if (isset($data[$f])) {
            $fields[] = "$f = ?";
            $values[] = $data[$f];
        }
    }

    if (!empty($fields)) {
        $values[] = $code;
        $db->prepare("UPDATE system_config_categories SET " . implode(', ', $fields) . " WHERE category_code = ?")
           ->execute($values);
    }

    sendSuccessResponse([], '카테고리가 수정되었습니다.');
}

/**
 * DELETE - 카테고리 삭제
 */
function handleDelete($db) {
    $code = isset($_GET['code']) ? $_GET['code'] : null;
    if (!$code) sendErrorResponse('code가 필요합니다.');

    $stmt = $db->prepare("SELECT is_default FROM system_config_categories WHERE category_code = ?");
    $stmt->execute([$code]);
    $cat = $stmt->fetch();
    if (!$cat) sendErrorResponse('카테고리를 찾을 수 없습니다.', 404);
    if ($cat['is_default']) sendErrorResponse('기본 카테고리는 삭제할 수 없습니다.');

    // 이 카테고리를 사용하는 system_config 항목 확인
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM system_config WHERE category_code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetch()['cnt'] > 0) {
        sendErrorResponse('이 카테고리를 사용하는 설정 항목이 있습니다. 먼저 항목을 다른 카테고리로 이동하세요.');
    }

    // 이 카테고리를 사용하는 book_types 확인
    $stmt = $db->prepare("SELECT COUNT(*) AS cnt FROM book_types WHERE category_code = ?");
    $stmt->execute([$code]);
    if ($stmt->fetch()['cnt'] > 0) {
        sendErrorResponse('이 카테고리를 사용하는 책 타입이 있습니다. 먼저 책 타입의 카테고리를 변경하세요.');
    }

    $db->prepare("DELETE FROM system_config_categories WHERE category_code = ?")->execute([$code]);
    sendSuccessResponse([], '카테고리가 삭제되었습니다.');
}
?>
