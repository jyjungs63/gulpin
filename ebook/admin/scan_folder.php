<?php
/**
 * ========================================
 * EPLAT Ebook - 폴더 스캔 (이미지·오디오 포맷 자동 감지)
 * ========================================
 *
 * 사용법: scan_folder.php?path=../ebook_new_eplat/bookshelf-1/1-1_story/v1/1/
 *
 * 응답:
 * {
 *   "image_count":  10,
 *   "image_format": "png",   // 감지된 이미지 확장자 (null = 없음)
 *   "audio_format": "mp3",   // 감지된 오디오 확장자 (null = 없음)
 *   "path":         "..."
 * }
 */

header('Content-Type: application/json; charset=utf-8');

$path = $_GET['path'] ?? '';

if (empty($path)) {
    echo json_encode(['error' => 'path 파라미터가 필요합니다.']);
    exit;
}

// ── URL → 파일시스템 경로 변환 ──────────────────
// ROOT_PATH 가 https://... 전체 URL 인 경우 서버 파일시스템 경로로 변환
if (preg_match('/^https?:\/\//', $path)) {
    $parsed  = parse_url($path);
    $urlPath = $parsed['path'] ?? '/';          // /ebook_new_eplat/bookshelf-.../

    // 1차 시도: DOCUMENT_ROOT 기반 절대 경로
    $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
    $candidate = $docRoot . '/' . ltrim($urlPath, '/');

    if (is_dir($candidate)) {
        $path = $candidate;
    } else {
        // 2차 시도: 현재 스크립트 디렉터리 기준 상대 경로 계산
        // scan_folder.php 위치: /...ebooks/  → URL path 에서 ebooks 이후를 잘라 역산
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_FILENAME'] ?? __FILE__), '/');
        // urlPath 를 그대로 역추적 (예: /ebook_new_eplat/ → ../ebook_new_eplat/ 상대)
        $relative = realpath($scriptDir . '/..' . $urlPath);
        if ($relative && is_dir($relative)) {
            $path = $relative;
        } else {
            // 마지막 시도: URL 경로를 DOCUMENT_ROOT 기준으로 재시도
            $path = $candidate; // is_dir 체크는 아래에서 한 번 더 수행
        }
    }
}

// 경로 정규화
$path = rtrim($path, '/');

if (!is_dir($path)) {
    echo json_encode([
        'error'        => "디렉토리를 찾을 수 없습니다: $path",
        'image_count'  => 0,
        'image_format' => null,
        'audio_format' => null,
        '_debug'       => [
            'resolved_path' => $path,
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
            'script_file'   => $_SERVER['SCRIPT_FILENAME'] ?? 'N/A',
        ]
    ]);
    exit;
}

// ── 지원 확장자 목록 ───────────────────────
$IMG_EXTS   = ['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp'];
$AUDIO_EXTS = ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'flac'];

/**
 * 폴더에서 숫자 이름(1.ext, 2.ext, ...) 파일만 수집
 * @return array  ['format' => 'png', 'count' => 10]  또는  ['format' => null, 'count' => 0]
 */
function detectNumberedFiles(string $dir, array $extensions): array {
    foreach ($extensions as $ext) {
        $pattern = "$dir/*.$ext";
        $allFiles = glob($pattern);
        if (!$allFiles) continue;

        // 숫자 이름만 필터 (1.png, 2.png, ...)
        $numbered = array_filter($allFiles, function ($f) use ($ext) {
            return preg_match('/[\/\\\\](\d+)\.' . preg_quote($ext, '/') . '$/', $f);
        });

        if (count($numbered) > 0) {
            // 최대 번호 = 페이지 수
            $maxNum = 0;
            foreach ($numbered as $f) {
                preg_match('/[\/\\\\](\d+)\.' . preg_quote($ext, '/') . '$/', $f, $m);
                if (isset($m[1]) && (int)$m[1] > $maxNum) $maxNum = (int)$m[1];
            }
            return ['format' => $ext, 'count' => $maxNum];
        }
    }
    return ['format' => null, 'count' => 0];
}

$imgInfo   = detectNumberedFiles($path, $IMG_EXTS);
$audioInfo = detectNumberedFiles($path, $AUDIO_EXTS);

echo json_encode([
    'image_count'  => $imgInfo['count'],
    'image_format' => $imgInfo['format'],
    'audio_format' => $audioInfo['format'],
    'path'         => $path
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
?>
