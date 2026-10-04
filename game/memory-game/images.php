<?php
// 홈스쿨 한자게임 이미지 목록: 과정 폴더에서 짝(N-1.png, N-2.png)이 모두 있는 번호를 돌려줌
// 사용: images.php?level=01&unit=A&course=1-A1 (1단계 알아보기: course=알아보기)
// 폴더: assets/gulpin/programs/homeschool/level-01/unit-0{1~5}/{course}
//       assets/gulpin/programs/homeschool/level-02/hanja-game/2단계 {unit}호/{course}
header('Content-Type: application/json; charset=utf-8');

$level = $_GET['level'] ?? '';
$unit = $_GET['unit'] ?? '';
$course = $_GET['course'] ?? '';

if (!preg_match('/^0[12]$/', $level) || !preg_match('/^[A-E]$/', $unit)
    || !preg_match('/^([12]-[A-E][1-9]|알아보기)$/u', $course)) {
    http_response_code(400);
    echo json_encode(['dir' => '', 'pairs' => []]);
    exit;
}

$unitNo = ord($unit) - ord('A') + 1;
$dir = $level === '01'
    ? "level-01/unit-0{$unitNo}/{$course}"
    : "level-02/hanja-game/2단계 {$unit}호/{$course}";

$pairs = [];
foreach (glob(__DIR__ . "/../../assets/gulpin/programs/homeschool/{$dir}/*-1.png") ?: [] as $file) {
    $pair = basename($file, '-1.png');
    if (ctype_digit($pair) && is_file(dirname($file) . "/{$pair}-2.png")) $pairs[] = (int)$pair;
}
sort($pairs);

echo json_encode(['dir' => $dir, 'pairs' => $pairs], JSON_UNESCAPED_UNICODE);
