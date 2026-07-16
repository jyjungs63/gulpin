<?php
/**
 * ========================================
 * EPLAT Ebook - displayMode + 카테고리 정보 조회 API
 * ========================================
 *
 * 사용법: get_display_mode.php?type=phonics
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once 'db_config.php';

try {
    $db = Database::getInstance()->getConnection();

    $typeCode = $_GET['type'] ?? '';
    if (empty($typeCode)) {
        throw new Exception('타입 코드가 필요합니다. (예: ?type=phonics)');
    }

    // book_types + category JOIN
    $stmt = $db->prepare("
        SELECT bt.display_mode, bt.category_code,
               COALESCE(c.category_code, dc.category_code) AS effective_category_code,
               COALESCE(c.category_name, dc.category_name) AS effective_category_name,
               COALESCE(c.root_path,     dc.root_path)     AS effective_root_path
        FROM book_types bt
        LEFT JOIN system_config_categories c  ON bt.category_code = c.category_code
        LEFT JOIN system_config_categories dc ON dc.is_default = 1
        WHERE bt.type_code = ? AND bt.is_active = 1
        LIMIT 1
    ");
    $stmt->execute([$typeCode]);
    $result = $stmt->fetch();

    if (!$result) {
        // 타입 없으면 기본 카테고리 정보만 반환
        $defStmt = $db->query("SELECT category_code, category_name, root_path FROM system_config_categories WHERE is_default=1 LIMIT 1");
        $def = $defStmt->fetch() ?: ['category_code'=>'general','category_name'=>'기본 설정','root_path'=>'../ebook_new_eplat/'];
        sendSuccessResponse([
            'display_mode'            => 'double',
            'category_code'           => null,
            'effective_category_code' => $def['category_code'],
            'effective_category_name' => $def['category_name'],
            'effective_root_path'     => $def['root_path'],
            'message'                 => "'{$typeCode}' 타입을 찾을 수 없어 기본값을 사용합니다."
        ]);
        return;
    }

    sendSuccessResponse([
        'display_mode'            => $result['display_mode'] ?? 'double',
        'category_code'           => $result['category_code'],
        'effective_category_code' => $result['effective_category_code'],
        'effective_category_name' => $result['effective_category_name'],
        'effective_root_path'     => $result['effective_root_path'],
        'type_code'               => $typeCode
    ]);

} catch (Exception $e) {
    sendErrorResponse($e->getMessage(), 500);
}
?>
