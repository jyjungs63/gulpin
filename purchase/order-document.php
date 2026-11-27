<?php

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET') {
    header('Allow: GET');
    http_response_code(405);
    exit;
}
header('Cache-Control: no-store, private');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');

if (($_GET['db'] ?? '') === 'sqlite') {
    require_once __DIR__ . '/../Server/sqlite_compat.php';
} else {
    define('GULPIN_INTERNAL_BOOTSTRAP', true);
    require_once __DIR__ . '/../_private/dbinit.php';
}
require_once __DIR__ . '/../Server/mgr_policy.php';

$actor = gpOrderActor();
$id = $_GET['id'] ?? '';
if (!is_string($id) || !preg_match('/^[A-Za-z0-9_-]{1,32}$/D', $id)) {
    http_response_code(404);
    exit;
}
$order = gpMgrQuery('SELECT id, pdfname FROM gulpin_porlist WHERE por_id = ?', [$id])[0] ?? null;
if (!$order || (!gpMgrIsAdmin($actor) && $order['id'] !== $actor['id'])) {
    http_response_code(404);
    exit;
}

$name = (string) $order['pdfname'];
if ($name === '' || basename($name) !== $name || strcasecmp(pathinfo($name, PATHINFO_EXTENSION), 'pdf') !== 0) {
    http_response_code(404);
    exit;
}
foreach (['assets/por/uploads', 'Server/uploads'] as $folder) {
    $directory = realpath(__DIR__ . '/../' . $folder);
    $path = $directory ? realpath($directory . DIRECTORY_SEPARATOR . $name) : false;
    if ($path && dirname($path) === $directory && is_file($path) && is_readable($path)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="order.pdf"');
        readfile($path);
        exit;
    }
}
http_response_code(404);
