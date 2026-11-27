<?php

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// A local MariaDB fixture is optional. The default exercises an in-memory SQLite database.
if (getenv('GP_TEST_PAYMENT_DB') === 'mariadb') {
    if (getenv('GP_DB_ENV') !== 'local' || getenv('LOCAL_DB_HOST') !== '127.0.0.1'
        || getenv('LOCAL_DB_NAME') !== 'gulpin_pg_fixture') {
        throw new RuntimeException('Use an isolated local gulpin_pg_fixture database.');
    }
    define('GULPIN_INTERNAL_BOOTSTRAP', true);
    require_once __DIR__ . '/../_private/dbinit.php';
} else {
    putenv('SQLITE_DB_PATH=:memory:');
    require_once __DIR__ . '/../Server/sqlite_compat.php';
}
require_once __DIR__ . '/../Server/mgr_policy.php';
require_once __DIR__ . '/../Server/payment_policy.php';

$checks = 0;
function checkPayment(bool $condition, string $message): void
{
    global $checks;
    if (!$condition) {
        throw new RuntimeException($message);
    }
    $checks++;
}

function fixturePaymentStatus(string $id): int
{
    return (int) gpPaymentQuery('SELECT confirm FROM gulpin_porlist WHERE por_id = ?', [$id])[0]['confirm'];
}

$id = 'GPTEST' . bin2hex(random_bytes(8));
$sourceId = 'P' . bin2hex(random_bytes(8));
$owner = 'payment-fixture';
$key = 'test-payment-fixture';
$config = ['merchant' => 'test-merchant', 'mode' => 'test', 'secret' => 'test-fixture'];
gpPaymentQuery("INSERT INTO gulpin_porlist (por_id, id, por_list, confirm, pdfname, zip, pg_amount) VALUES (?, ?, '[]', 0, '', '', 12500)", [$sourceId, $owner]);
gpPaymentQuery("INSERT INTO gulpin_payments (order_id, source, source_id, user_id, order_name, amount, status, payment_key, merchant_id, key_mode)
    VALUES (?, 'por', ?, ?, 'Test book', 12500, 'CONFIRMING', ?, ?, 'test')", [$id, $sourceId, $owner, $key, $config['merchant']]);
try {
    $payment = gpPaymentById($id);
    checkPayment(gpPaymentActive('por', $sourceId)['status'] === 'CONFIRMING', 'Pending approval is reserved');
    try {
        gpPaymentTransaction(function () use ($sourceId) {
            gpPaymentLockOrder('por', $sourceId);
            gpPaymentGuardPorEdit($sourceId, null);
        });
        throw new RuntimeException('A pending approval must prevent deletion');
    } catch (GpPaymentError $e) {
        checkPayment($e->getCode() === 409, 'Pending approval blocks order deletion');
    }

    $response = [
        'orderId' => $id, 'paymentKey' => $key, 'mId' => $config['merchant'], 'currency' => 'KRW',
        'totalAmount' => 12500, 'balanceAmount' => 12500, 'status' => 'DONE', 'method' => '카드',
        'approvedAt' => '2026-11-25T10:00:00+09:00',
        'receipt' => ['url' => 'https://dashboard.tosspayments.com/receipt/test'],
    ];
    foreach (['totalAmount' => 100, 'orderId' => 'GPOTHER', 'paymentKey' => 'wrong',
        'mId' => 'other-merchant', 'currency' => 'USD', 'balanceAmount' => 5000] as $field => $value) {
        try {
            gpPaymentApplyToss($payment, $config, array_replace($response, [$field => $value]));
            throw new RuntimeException('Provider response mismatch was accepted: ' . $field);
        } catch (GpPaymentError $e) {
            checkPayment($e->getCode() === 502, 'Mismatched ' . $field . ' is rejected');
        }
    }
    checkPayment(fixturePaymentStatus($sourceId) === 0 && gpPaymentById($id)['status'] === 'CONFIRMING', 'Mismatched responses preserve both records');

    try {
        gpPaymentTransaction(function () use ($id, $sourceId) {
            gpPaymentLockOrder('por', $sourceId);
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'DONE' WHERE order_id = ?", [$id]);
            throw new RuntimeException('simulated failure before updating the order');
        });
    } catch (RuntimeException $e) {
        checkPayment(gpPaymentById($id)['status'] === 'CONFIRMING', 'A failed transaction rolls back payment status');
    }

    gpPaymentApplyToss($payment, $config, $response);
    gpPaymentApplyToss($payment, $config, $response);
    checkPayment(fixturePaymentStatus($sourceId) === 2 && gpPaymentById($id)['status'] === 'DONE', 'Repeated approval keeps both records paid');
    checkPayment(gpPaymentConfirmedView($id)['receiptUrl'] === $response['receipt']['url'], 'Verified Toss receipt is available');
    checkPayment(gpPaymentReceipt('https://tosspayments.com.example.test/receipt') === null, 'Foreign receipt host is rejected');
    checkPayment(gpPaymentReceipt('javascript:alert(1)') === null, 'Non-HTTPS receipt is rejected');

    $partial = array_replace($response, ['status' => 'PARTIAL_CANCELED', 'balanceAmount' => 5000]);
    gpPaymentApplyToss($payment, $config, $partial);
    gpPaymentApplyToss($payment, $config, $response);
    checkPayment(gpPaymentById($id)['status'] === 'PARTIAL' && fixturePaymentStatus($sourceId) === 2, 'Partial cancellation is preserved after older approval');
    $canceled = array_replace($response, ['status' => 'CANCELED', 'balanceAmount' => 0]);
    gpPaymentApplyToss($payment, $config, $canceled);
    gpPaymentApplyToss($payment, $config, $canceled);
    gpPaymentApplyToss($payment, $config, $response);
    checkPayment(gpPaymentById($id)['status'] === 'CANCELED' && fixturePaymentStatus($sourceId) === 999, 'Repeated refund and late approval leave the order refunded');
} finally {
    gpPaymentQuery('DELETE FROM gulpin_payments WHERE order_id = ?', [$id]);
    gpPaymentQuery('DELETE FROM gulpin_porlist WHERE por_id = ?', [$sourceId]);
}

$now = gmdate('c');
$body = '{"eventType":"PAYMENT_STATUS_CHANGED"}';
$signature = 'v1:' . base64_encode(hash_hmac('sha256', $body . ':' . $now, $config['secret'], true));
checkPayment(gpPaymentVerifyWebhook($body, $now, 'v1:invalid,' . $signature, $config['secret']), 'A valid webhook signature is accepted during key rotation');
checkPayment(!gpPaymentVerifyWebhook($body . ' ', $now, $signature, $config['secret']), 'Webhook contents must match the signature');
$old = gmdate('c', time() - 400);
$oldSignature = 'v1:' . base64_encode(hash_hmac('sha256', $body . ':' . $old, $config['secret'], true));
checkPayment(!gpPaymentVerifyWebhook($body, $old, $oldSignature, $config['secret']), 'An old webhook signature is rejected');

echo "PASS {$checks} payment checks\n";
