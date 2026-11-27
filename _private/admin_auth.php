<?php

if (!defined('GULPIN_INTERNAL_BOOTSTRAP')) {
    define('GULPIN_INTERNAL_BOOTSTRAP', true);
}
require_once __DIR__ . '/dbinit.php';
require_once __DIR__ . '/../Server/mgr_policy.php';

header('Cache-Control: no-store');
if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'HEAD'], true)
    && !in_array($_SERVER['HTTP_SEC_FETCH_SITE'] ?? 'same-origin', ['same-origin', 'none'], true)) {
    gpMgrRespond(['error' => '허용되지 않은 요청입니다.'], 403);
}
if (!gpMgrIsAdmin(gpMgrActor())) {
    gpMgrRespond(['error' => '본사 관리자만 이용할 수 있습니다.'], 403);
}
