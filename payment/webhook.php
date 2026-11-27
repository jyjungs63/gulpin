<?php

require_once __DIR__ . '/../Server/payment_policy.php';
loadEnv(__DIR__ . '/../_private/.env');

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit;
}

$raw = file_get_contents('php://input', false, null, 0, 65537);
$secret = (string) env('TOSS_WEBHOOK_SECRET', '');
$sentAt = $_SERVER['HTTP_TOSSPAYMENTS_WEBHOOK_TRANSMISSION_TIME'] ?? '';
$signature = $_SERVER['HTTP_TOSSPAYMENTS_WEBHOOK_SIGNATURE'] ?? '';
if (!is_string($raw) || !gpPaymentVerifyWebhook($raw, $sentAt, $signature, $secret)) {
    http_response_code(403);
    exit;
}

$event = json_decode($raw, true);
if (!is_array($event)) {
    http_response_code(400);
    exit;
}
if (($event['eventType'] ?? '') !== 'PAYMENT_STATUS_CHANGED') {
    http_response_code(204);
    exit;
}

if (env('TOSS_WEBHOOK_DB', 'mariadb') === 'sqlite') {
    require_once __DIR__ . '/../Server/sqlite_compat.php';
} else {
    define('GULPIN_INTERNAL_BOOTSTRAP', true);
    require_once __DIR__ . '/../_private/dbinit.php';
}
require_once __DIR__ . '/../Server/mgr_policy.php';

try {
    $orderId = gpPaymentOrderId($event['data']['orderId'] ?? null);
    $payment = gpPaymentById($orderId);
    if ($payment && $payment['payment_key'] && $payment['status'] !== 'CANCELED'
        && ($event['data']['paymentKey'] ?? '') === $payment['payment_key']) {
        $config = gpPaymentConfig();
        gpPaymentCheckMode($payment, $config);
        [$status, $result] = gpPaymentLookup($config, $orderId);
        if ($status === 200) {
            gpPaymentApplyToss($payment, $config, $result);
        } elseif ($status !== 404 || ($result['code'] ?? '') !== 'NOT_FOUND_PAYMENT'
            || in_array($event['data']['status'] ?? '', ['DONE', 'CANCELED', 'PARTIAL_CANCELED'], true)) {
            throw new RuntimeException('Toss lookup failed');
        } elseif (in_array($event['data']['status'] ?? '', ['ABORTED', 'EXPIRED'], true)) {
            // 토스가 서명한 실패 이벤트이며 조회에서도 승인된 거래가 확인되지 않은 경우.
            gpPaymentMarkFailed($payment, $event['data']['status']);
        }
    }
    http_response_code(204);
} catch (Throwable $e) {
    error_log('[payment webhook] ' . $e->getMessage());
    http_response_code(503);
}
