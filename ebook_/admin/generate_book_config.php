<?php
/**
 * ========================================
 * EPLAT Ebook - book-config.js 생성 (display_mode 포함)
 * ========================================
 */

header('Content-Type: application/javascript; charset=utf-8');

require_once 'db_config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // ========================================
    // 시스템 설정 가져오기
    // ========================================
    $stmt = $db->query("SELECT config_key, config_value, config_type FROM system_config");
    $configs = [];
    
    while ($row = $stmt->fetch()) {
        $configs[$row['config_key']] = [
            'value' => $row['config_value'],
            'type' => $row['config_type']
        ];
    }
    
    // ========================================
    // 활성화된 책 타입 가져오기
    // ========================================
    $stmt = $db->query("
        SELECT type_code, type_name, directory_path, display_mode 
        FROM book_types 
        WHERE is_active = 1 
        ORDER BY display_order ASC, type_name ASC
    ");
    $bookTypes = $stmt->fetchAll();
    
    // ========================================
    // JavaScript 설정 파일 생성
    // ========================================
    
    $output = "/**\n";
    $output .= " * ========================================\n";
    $output .= " * EPLAT Ebook - 자동 생성된 설정 파일\n";
    $output .= " * 생성 시간: " . date('Y-m-d H:i:s') . "\n";
    $output .= " * ========================================\n";
    $output .= " */\n\n";
    
    // ========================================
    // 시스템 설정 출력
    // ========================================
    $output .= "// ========================================\n";
    $output .= "// 시스템 설정\n";
    $output .= "// ========================================\n";
    
    foreach ($configs as $key => $config) {
        $value = $config['value'];
        $type = $config['type'];
        
        if ($type === 'number') {
            $output .= "const {$key} = {$value};\n";
        } elseif ($type === 'boolean') {
            $output .= "const {$key} = " . ($value === 'true' || $value === '1' ? 'true' : 'false') . ";\n";
        } elseif ($type === 'json') {
            $output .= "const {$key} = {$value};\n";
        } else {
            $output .= "const {$key} = \"{$value}\";\n";
        }
    }
    
    $output .= "\n";
    
    // ========================================
    // 책 타입별 설정
    // ========================================
    $output .= "// ========================================\n";
    $output .= "// 책 타입별 설정\n";
    $output .= "// ========================================\n\n";
    
    foreach ($bookTypes as $bookType) {
        $typeCode = $bookType['type_code'];
        $typeName = $bookType['type_name'];
        $dirPath = $bookType['directory_path'];
        $displayMode = $bookType['display_mode'] ?? 'double';  // ✨ displayMode
        
        $output .= "// {$typeName}\n";
        $output .= "var BOOK_TYPES = BOOK_TYPES || {};\n";
        $output .= "BOOK_TYPES['{$typeCode}'] = {\n";
        $output .= "    name: '{$typeName}',\n";
        $output .= "    path: ROOT_PATH + '{$dirPath}',\n";
        $output .= "    displayMode: '{$displayMode}'  // ✨ 페이지 표시 모드 (single/double)\n";
        $output .= "};\n\n";
    }
    
    // ========================================
    // ebook-config.js 생성 함수
    // ========================================
    $output .= "// ========================================\n";
    $output .= "// ebook-config.js 생성 함수\n";
    $output .= "// ========================================\n\n";
    
    $output .= "/**\n";
    $output .= " * 특정 책 타입의 ebook-config.js 내용 생성\n";
    $output .= " * @param {string} typeCode - 책 타입 코드\n";
    $output .= " * @param {string} level - 레벨 (예: basic, level1)\n";
    $output .= " * @param {string} version - 버전 (예: v1, v2)\n";
    $output .= " */\n";
    $output .= "function generateEbookConfig(typeCode, level, version) {\n";
    $output .= "    const bookType = BOOK_TYPES[typeCode];\n";
    $output .= "    if (!bookType) {\n";
    $output .= "        console.error('책 타입을 찾을 수 없습니다:', typeCode);\n";
    $output .= "        return;\n";
    $output .= "    }\n\n";
    
    $output .= "    const config = `\n";
    $output .= "/**\n";
    $output .= " * EPLAT Ebook - \${bookType.name} 설정\n";
    $output .= " */\n\n";
    
    $output .= "// ✨ 페이지 표시 모드 (데이터베이스에서 로드)\n";
    $output .= "const displayMode = \"\${bookType.displayMode}\";\n\n";
    
    $output .= "// 이미지 경로\n";
    $output .= "const imgpath = \"\${bookType.path}\${level}/\${version}/\";\n\n";
    
    $output .= "// 오디오 파일 확장자\n";
    $output .= "const sound = \"mp3\";\n\n";
    
    $output .= "// 이미지 목록 (자동 생성 필요)\n";
    $output .= "const images = [\n";
    $output .= "    // 여기에 이미지 파일 경로를 추가하세요\n";
    $output .= "];\n\n";
    
    $output .= "// 동영상 링크 (선택사항)\n";
    $output .= "const videoLinks = {};\n";
    $output .= "`;\n\n";
    
    $output .= "    return config;\n";
    $output .= "}\n\n";
    
    // ========================================
    // 사용 예제
    // ========================================
    $output .= "// ========================================\n";
    $output .= "// 사용 예제\n";
    $output .= "// ========================================\n";
    $output .= "/*\n";
    
    $count = 0;
    foreach ($bookTypes as $bookType) {
        if ($count >= 3) break;
        $typeCode = $bookType['type_code'];
        $typeName = $bookType['type_name'];
        $output .= "// {$typeName} 설정 생성\n";
        $output .= "const {$typeCode}Config = generateEbookConfig('{$typeCode}', 'basic', 'v1');\n";
        $output .= "console.log({$typeCode}Config);\n\n";
        $count++;
    }
    
    $output .= "*/\n";
    
    echo $output;
    
} catch (Exception $e) {
    echo "// 오류: " . $e->getMessage();
}
?>
