<?php
/**
 * .env 파일을 읽어 환경변수로 등록한다. (phpdotenv 의 간이 버전)
 *
 * 형식 : KEY=VALUE  (# 으로 시작하는 줄은 주석, 값의 따옴표는 제거)
 *
 * @param  string $path .env 파일 경로
 * @return void
 */

function loadEnv($path) {
    // 파일이 없으면 조용히 종료 (운영 서버에서 실제 환경변수를 쓰는 경우 대비)
    if (!is_readable($path)) return;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);

        // 주석 줄과 '=' 가 없는 줄은 건너뛴다
        if ($line === '' || $line[0] === '#' || strpos($line, '=') === false) continue;

        // 첫 번째 '=' 기준으로 키/값 분리 (값 안의 '=' 는 그대로 유지)
        list($key, $value) = explode('=', $line, 2);
        $key   = trim($key);
        $value = trim($value);

        // 값을 감싼 따옴표("..." 또는 '...') 제거
        if (strlen($value) >= 2 && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
            $value = substr($value, 1, -1);
        }

        // 서버에 이미 같은 이름의 환경변수가 있으면 덮어쓰지 않는다 (서버 설정 우선)
        if (getenv($key) !== false) continue;

        putenv("$key=$value");   // getenv() 로 읽기용
        $_ENV[$key] = $value;    // $_ENV 로 읽기용
    }
}
/**
 * 환경변수 값을 읽는다. 없으면 기본값을 돌려준다.
 *
 * @param  string $key     변수 이름
 * @param  mixed  $default 값이 없을 때 돌려줄 기본값
 * @return mixed
 */
function env($key, $default = null) {
    $v = getenv($key);
    return ($v === false) ? $default : $v;
}