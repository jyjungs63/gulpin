<?php
/**
 * ========================================
 * EPLAT Ebook - 시스템 설정 API (category 지원)
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
        case 'POST':
        case 'PUT':    handleUpdate($db); break;
        case 'DELETE': handleDelete($db); break;
        default:
            sendErrorResponse('지원하지 않는 메서드입니다.', 405);
    }
} catch (Exception $e) {
    sendErrorResponse($e->getMessage(), 500);
}

/**
 * GET - 시스템 설정 조회 (카테고리 필터 지원)
 */
function handleGet($db) {
    $key      = isset($_GET['key'])      ? $_GET['key']      : null;
    $category = isset($_GET['category']) ? $_GET['category'] : null;

    if ($key) {
        // 단일 설정 조회
        $stmt = $db->prepare("
            SELECT s.*, c.category_name
            FROM system_config s
            LEFT JOIN system_config_categories c ON s.category_code = c.category_code
            WHERE s.config_key = ?
        ");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        if (!$result) sendErrorResponse('설정을 찾을 수 없습니다.', 404);
        sendSuccessResponse($result);
    } else {
        // 전체/카테고리 별 조회
        $sql = "
            SELECT s.*, c.category_name, c.is_default AS cat_is_default
            FROM system_config s
            LEFT JOIN system_config_categories c ON s.category_code = c.category_code
        ";
        $params = [];
        if ($category) {
            $sql .= " WHERE s.category_code = ?";
            $params[] = $category;
        }
        $sql .= " ORDER BY s.category_code, s.config_key";

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        sendSuccessResponse($stmt->fetchAll());
    }
}

/**
 * POST/PUT - 시스템 설정 추가/수정
 */
function handleUpdate($db) {
    $data = getPostData();

    if (empty($data['config_key'])) sendErrorResponse('config_key가 필요합니다.');
    if (!isset($data['config_value'])) sendErrorResponse('config_value가 필요합니다.');

    // 수정 가능 여부 확인
    $stmt = $db->prepare("SELECT is_editable FROM system_config WHERE config_key = ?");
    $stmt->execute([$data['config_key']]);
    $existing = $stmt->fetch();

    if ($existing && !$existing['is_editable']) {
        sendErrorResponse('이 설정은 수정할 수 없습니다.');
    }

    if ($existing) {
        // 업데이트 (category_code도 수정 가능)
        $fields = ['config_value = ?', 'updated_at = CURRENT_TIMESTAMP'];
        $params = [$data['config_value']];
        if (isset($data['category_code'])) {
            $fields[] = 'category_code = ?';
            $params[] = $data['category_code'];
        }
        if (isset($data['config_type'])) {
            $fields[] = 'config_type = ?';
            $params[] = $data['config_type'];
        }
        if (isset($data['description'])) {
            $fields[] = 'description = ?';
            $params[] = $data['description'];
        }
        $params[] = $data['config_key'];
        $db->prepare("UPDATE system_config SET " . implode(', ', $fields) . " WHERE config_key = ?")
           ->execute($params);
        $message = '설정이 수정되었습니다.';
    } else {
        // 새로 추가
        $stmt = $db->prepare("
            INSERT INTO system_config
                (config_key, category_code, config_value, config_type, description, is_editable)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['config_key'],
            $data['category_code'] ?? 'general',
            $data['config_value'],
            $data['config_type'] ?? 'string',
            $data['description'] ?? '',
            isset($data['is_editable']) ? intval($data['is_editable']) : 1
        ]);
        $message = '설정이 추가되었습니다.';
    }

    sendSuccessResponse([], $message);
}

/**
 * DELETE - 시스템 설정 삭제
 */
function handleDelete($db) {
    $key = isset($_GET['key']) ? $_GET['key'] : null;
    if (!$key) sendErrorResponse('key가 필요합니다.');

    $stmt = $db->prepare("SELECT is_editable FROM system_config WHERE config_key = ?");
    $stmt->execute([$key]);
    $row = $stmt->fetch();
    if (!$row) sendErrorResponse('설정을 찾을 수 없습니다.', 404);
    if (!$row['is_editable']) sendErrorResponse('이 설정은 삭제할 수 없습니다.');

    $db->prepare("DELETE FROM system_config WHERE config_key = ?")->execute([$key]);
    sendSuccessResponse([], '설정이 삭제되었습니다.');
}
?>
