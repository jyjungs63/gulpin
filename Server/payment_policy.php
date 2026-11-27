<?php

// 토스페이먼츠 카드/간편결제 통합결제창 v2
require_once __DIR__ . '/../_private/env.php';

const GP_TOSS_API = 'https://api.tosspayments.com/v1/payments';

final class GpPaymentError extends RuntimeException {}

function gpPaymentAction(callable $action): void
{
    header('Cache-Control: no-store');
    try {
        gpMgrRespond(['success' => $action()]);
    } catch (GpPaymentError $e) {
        gpMgrRespond(['error' => $e->getMessage()], $e->getCode());
    } catch (Throwable $e) {
        error_log('[payment] database or configuration error: ' . $e->getMessage());
        gpMgrRespond(['error' => '결제 상태를 저장하지 못했습니다. 주문 내역에서 다시 확인해 주세요.'], 503);
    }
}

function gpPaymentRequireBrowser(): void
{
    // 브라우저의 다른 출처에서는 CORS 사전 요청 없이 이 헤더를 보낼 수 없다.
    if (($_SERVER['HTTP_X_GULPIN_PAYMENT'] ?? '') !== '1') {
        throw new GpPaymentError('허용되지 않은 요청입니다.', 403);
    }
}

function gpPaymentConfig(): array
{
    loadEnv(__DIR__ . '/../_private/.env');
    $client = (string) env('TOSS_CLIENT_KEY', '');
    $secret = (string) env('TOSS_SECRET_KEY', '');
    $merchant = trim((string) env('TOSS_MERCHANT_ID', ''));
    if (!preg_match('/^(test|live)_ck_[A-Za-z0-9_-]+$/D', $client, $mode)
        || !preg_match('/^' . $mode[1] . '_sk_[A-Za-z0-9_-]+$/D', $secret)
        || $merchant === '' || !function_exists('curl_init')) {
        throw new GpPaymentError('카드결제 설정을 확인 중입니다. 관리자에게 문의해 주세요.', 503);
    }
    if ($mode[1] === 'live' && !gpIsHttpsRequest()) {
        throw new GpPaymentError('운영 결제는 HTTPS에서 이용할 수 있습니다.', 403);
    }
    return ['client' => $client, 'secret' => $secret, 'merchant' => $merchant, 'mode' => $mode[1]];
}

function gpTossRequest(array $config, string $path, ?array $body = null, string $idempotencyKey = ''): array
{
    $headers = ['Authorization: Basic ' . base64_encode($config['secret'] . ':'), 'Accept: application/json'];
    if ($body !== null) {
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Idempotency-Key: ' . $idempotencyKey;
    }
    $ch = curl_init(GP_TOSS_API . $path);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => $body === null ? 5 : 60,
        CURLOPT_FOLLOWLOCATION => false,
    ]);
    if ($body !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($raw === false) {
        error_log('[payment] Toss transport error ' . curl_errno($ch));
    }
    curl_close($ch);
    $data = is_string($raw) ? json_decode($raw, true) : null;
    return [$status, is_array($data) ? $data : []];
}

function gpPaymentQuery(string $sql, array $params = [])
{
    return gpMgrQuery($sql, $params, true);
}

function gpPaymentTransaction(callable $action)
{
    global $conn;
    $sqlite = $conn instanceof mysqli ? null : $conn->pdo();
    if ($sqlite) {
        $sqlite->exec('BEGIN IMMEDIATE');
    } else {
        $conn->begin_transaction();
    }
    try {
        $result = $action();
        if ($sqlite) {
            $sqlite->exec('COMMIT');
        } else {
            $conn->commit();
        }
        return $result;
    } catch (Throwable $e) {
        if ($sqlite) {
            $sqlite->exec('ROLLBACK');
        } else {
            $conn->rollback();
        }
        throw $e;
    }
}

/** 모든 주문 변경은 원본 주문 행을 먼저 잠근다. SQLite는 쓰기 트랜잭션으로 직렬화한다. */
function gpPaymentLockOrder(string $source, string $sourceId): array
{
    global $conn;
    if ($source === 'por') {
        $sql = 'SELECT id AS owner, confirm AS status, por_list, pg_amount AS amount FROM gulpin_porlist WHERE por_id = ?';
    } elseif ($source === 'shop' && $conn instanceof mysqli) {
        $sql = 'SELECT user_id AS owner, status, amount FROM shopping_orders WHERE id = ?';
    } else {
        throw new GpPaymentError('이 주문의 결제 대상을 확인할 수 없습니다.', 400);
    }
    $rows = gpPaymentQuery($sql . ($conn instanceof mysqli ? ' FOR UPDATE' : ''), [$sourceId]);
    if (!$rows) {
        throw new GpPaymentError('주문을 찾을 수 없습니다.', 404);
    }
    return $rows[0];
}

function gpPaymentCanPay(string $source, array $order): bool
{
    return $source === 'por' ? in_array((string) $order['status'], ['0', '3'], true) : $order['status'] === 'pending';
}

function gpPaymentActive(string $source, string $sourceId): ?array
{
    $rows = gpPaymentQuery("SELECT * FROM gulpin_payments WHERE source = ? AND source_id = ?
        AND status IN ('CONFIRMING', 'DONE', 'CANCELING', 'CANCELED', 'PARTIAL') ORDER BY created_at DESC, order_id DESC LIMIT 1",
        [$source, $sourceId]);
    return $rows[0] ?? null;
}

function gpPaymentOrderId($value): string
{
    if (!is_string($value) || !preg_match('/^[A-Za-z0-9_=-]{6,64}$/D', $value)) {
        throw new GpPaymentError('결제번호가 올바르지 않습니다.', 400);
    }
    return $value;
}

function gpPaymentById(string $id): ?array
{
    $payment = gpPaymentQuery('SELECT * FROM gulpin_payments WHERE order_id = ?', [$id])[0] ?? null;
    return $payment && $payment['order_id'] === $id ? $payment : null;
}

function gpPaymentCheckMode(array $payment, array $config): void
{
    if (($payment['merchant_id'] !== null && $payment['merchant_id'] !== $config['merchant'])
        || ($payment['key_mode'] !== null && $payment['key_mode'] !== $config['mode'])) {
        throw new GpPaymentError('결제 상점 설정이 주문과 다릅니다. 관리자에게 문의해 주세요.', 409);
    }
}

function gpPaymentReturnToken(string $id, array $config): string
{
    return hash_hmac('sha256', 'return:' . $id, $config['secret']);
}

function gpPaymentVerifyWebhook(string $raw, string $sentAt, string $signature, string $secret): bool
{
    $time = strtotime($sentAt);
    if ($secret === '' || strlen($raw) > 65536 || $time === false || abs(time() - $time) > 300) {
        return false;
    }
    $expected = hash_hmac('sha256', $raw . ':' . $sentAt, $secret, true);
    foreach (explode(',', $signature) as $part) {
        $part = trim($part);
        $decoded = strncmp($part, 'v1:', 3) === 0 ? base64_decode(substr($part, 3), true) : false;
        if (is_string($decoded) && hash_equals($expected, $decoded)) {
            return true;
        }
    }
    return false;
}

function SPaymentPrepare($data)
{
    gpPaymentAction(function () use ($data) {
        gpPaymentRequireBrowser();
        $source = $data['source'] ?? '';
        $sourceId = $data['id'] ?? '';
        if (!is_string($sourceId) || !(
            ($source === 'por' && preg_match('/^[A-Za-z0-9_-]{1,32}$/D', $sourceId))
            || ($source === 'shop' && preg_match('/^[1-9][0-9]{0,9}$/D', $sourceId))
        )) {
            throw new GpPaymentError('결제 대상이 올바르지 않습니다.', 400);
        }
        $user = $source === 'por' ? gpOrderActor() : gpSessionUser();
        $config = gpPaymentConfig();

        return gpPaymentTransaction(function () use ($user, $source, $sourceId, $config) {
            $order = gpPaymentLockOrder($source, $sourceId);
            if ($order['owner'] !== $user['id']) {
                throw new GpPaymentError('본인 주문만 결제할 수 있습니다.', 403);
            }
            $active = gpPaymentActive($source, $sourceId);
            if ($active && $active['status'] === 'CONFIRMING') {
                gpPaymentCheckMode($active, $config);
                return ['resumeOrderId' => $active['order_id']];
            }
            if ($active || !gpPaymentCanPay($source, $order)) {
                throw new GpPaymentError('이미 처리되었거나 결제할 수 없는 주문입니다.', 409);
            }

            if ($source === 'por') {
                $amount = (int) ($order['amount'] ?? gpOrderAmount($user, $order['por_list']));
                $lines = json_decode($order['por_list'], true) ?: [];
                $items = array_values(array_filter($lines, function ($line) {
                    return is_array($line) && (string) ($line['uid'] ?? '') !== '';
                }));
                $name = (string) ($items[0]['title'] ?? '교재');
            } else {
                $amount = (int) $order['amount'];
                $items = gpPaymentQuery('SELECT name FROM shopping_order_items WHERE order_id = ? ORDER BY id', [$sourceId]);
                $name = (string) ($items[0]['name'] ?? '상품');
            }
            if ($amount < 100 || $amount > 2147483647) {
                throw new GpPaymentError('결제 금액이 올바르지 않습니다.', 400);
            }
            if ($source === 'por' && $order['amount'] === null) {
                gpPaymentQuery('UPDATE gulpin_porlist SET pg_amount = ? WHERE por_id = ?', [$amount, $sourceId]);
            }
            $name = trim($name) ?: ($source === 'por' ? '교재' : '상품');
            $orderName = mb_substr($name, 0, 80) . (count($items) > 1 ? ' 외 ' . (count($items) - 1) . '건' : '');
            $orderId = 'GP' . strtoupper($source) . bin2hex(random_bytes(16));
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'FAILED', fail_reason = 'SUPERSEDED'
                WHERE source = ? AND source_id = ? AND status = 'READY'", [$source, $sourceId]);
            gpPaymentQuery("INSERT INTO gulpin_payments
                (order_id, source, source_id, user_id, order_name, amount, status, merchant_id, key_mode)
                VALUES (?, ?, ?, ?, ?, ?, 'READY', ?, ?)",
                [$orderId, $source, $sourceId, $user['id'], $orderName, $amount, $config['merchant'], $config['mode']]);

            return [
                'clientKey' => $config['client'], 'orderId' => $orderId, 'orderName' => $orderName,
                'amount' => $amount, 'returnToken' => gpPaymentReturnToken($orderId, $config),
            ];
        });
    });
}

function SPaymentConfirm($data)
{
    gpPaymentAction(function () use ($data) {
        gpPaymentRequireBrowser();
        $user = gpSessionUser();
        $id = gpPaymentOrderId($data['orderId'] ?? null);
        $payment = gpPaymentById($id);
        if (!$payment || $payment['user_id'] !== $user['id']) {
            throw new GpPaymentError('결제 정보를 찾을 수 없습니다.', 404);
        }
        $config = gpPaymentConfig();
        gpPaymentCheckMode($payment, $config);
        $key = $data['paymentKey'] ?? '';
        $amount = $data['amount'] ?? '';
        $token = $data['returnToken'] ?? '';
        $resume = $key === '' && $amount === '' && $token === '';
        if (!$resume && (
            !is_string($token) || !hash_equals(gpPaymentReturnToken($id, $config), $token)
            || !is_string($key) || $key === '' || strlen($key) > 200 || preg_match('/[\x00-\x20\x7F]/', $key)
            || !is_string($amount) || !preg_match('/^[0-9]{1,10}$/D', $amount) || (int) $amount !== (int) $payment['amount']
        )) {
            throw new GpPaymentError('결제 정보가 주문과 다릅니다.', 400);
        }

        // 승인 시도를 먼저 남겨 타임아웃이나 DB 오류 뒤에도 같은 키로 결과를 복구한다.
        $payment = gpPaymentTransaction(function () use ($payment, $id, $key, $resume) {
            $order = gpPaymentLockOrder($payment['source'], $payment['source_id']);
            $current = gpPaymentById($id);
            if ($current['status'] === 'DONE' && ($resume || $current['payment_key'] === $key)) {
                return $current;
            }
            if ($current['status'] === 'CONFIRMING' && ($resume || $current['payment_key'] === $key)) {
                return $current;
            }
            if ($resume || $current['status'] !== 'READY' || $order['owner'] !== $current['user_id']
                || !gpPaymentCanPay($current['source'], $order) || gpPaymentActive($current['source'], $current['source_id'])
                || ($order['amount'] !== null && (int) $order['amount'] !== (int) $current['amount'])) {
                throw new GpPaymentError('승인할 수 없는 결제입니다. 주문 내역을 확인해 주세요.', 409);
            }
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'CONFIRMING', payment_key = ? WHERE order_id = ?", [$key, $id]);
            return gpPaymentById($id);
        });
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if ($payment['status'] === 'DONE') {
            return gpPaymentView($payment);
        }

        if ($resume) {
            [$status, $result] = gpPaymentLookup($config, $id);
            if ($status === 200 && gpPaymentApplyToss($payment, $config, $result)) {
                return gpPaymentConfirmedView($id);
            }
        }
        [$status, $result] = gpTossRequest($config, '/confirm', [
            'paymentKey' => $payment['payment_key'], 'orderId' => $id, 'amount' => (int) $payment['amount'],
        ], 'confirm-' . $id);
        if ($status === 200 && gpPaymentApplyToss($payment, $config, $result)) {
            return gpPaymentConfirmedView($id);
        }

        [$lookupStatus, $lookup] = gpPaymentLookup($config, $id);
        if ($lookupStatus === 200 && gpPaymentApplyToss($payment, $config, $lookup)) {
            return gpPaymentConfirmedView($id);
        }
        $code = $result['code'] ?? '';
        if (in_array($status, [400, 403, 404], true)
            && $lookupStatus === 404 && ($lookup['code'] ?? '') === 'NOT_FOUND_PAYMENT'
            && in_array($code, ['NOT_FOUND_PAYMENT_SESSION', 'NOT_FOUND_PAYMENT', 'INVALID_REJECT_CARD',
                'REJECT_CARD_PAYMENT', 'INVALID_CARD_EXPIRATION', 'INVALID_STOPPED_CARD', 'EXCEED_MAX_PAYMENT_AMOUNT'], true)) {
            gpPaymentMarkFailed($payment, $code);
            return gpPaymentConfirmedView($id);
        }
        throw new GpPaymentError('카드 승인 결과를 확인 중입니다. 잠시 후 이 화면이나 주문 내역에서 다시 확인해 주세요.', 503);
    });
}

function gpPaymentLookup(array $config, string $id): array
{
    return gpTossRequest($config, '/orders/' . rawurlencode($id));
}

function gpPaymentMarkFailed(array $payment, string $reason): void
{
    gpPaymentTransaction(function () use ($payment, $reason) {
        gpPaymentLockOrder($payment['source'], $payment['source_id']);
        gpPaymentQuery("UPDATE gulpin_payments SET status = 'FAILED', fail_reason = ? WHERE order_id = ? AND status = 'CONFIRMING'",
            [$reason, $payment['order_id']]);
    });
}

function gpPaymentConfirmedView(string $id): array
{
    $payment = gpPaymentById($id);
    if ($payment['status'] === 'DONE') {
        return gpPaymentView($payment);
    }
    if ($payment['status'] === 'FAILED') {
        throw new GpPaymentError('카드 승인이 완료되지 않았습니다. 주문 내역에서 다시 결제해 주세요.', 409);
    }
    throw new GpPaymentError('결제의 현재 상태는 주문 내역에서 확인해 주세요.', 409);
}

/** 조회 또는 승인/취소 API가 반환한 Payment만 반영한다. */
function gpPaymentApplyToss(array $payment, array $config, array $result): bool
{
    gpPaymentCheckMode($payment, $config);
    $status = $result['status'] ?? '';
    $balance = $result['balanceAmount'] ?? null;
    if (!$payment['payment_key'] || ($result['paymentKey'] ?? null) !== $payment['payment_key']
        || ($result['orderId'] ?? null) !== $payment['order_id'] || ($result['mId'] ?? null) !== $config['merchant']
        || ($result['currency'] ?? null) !== 'KRW' || ($result['totalAmount'] ?? null) !== (int) $payment['amount']
        || ($status === 'DONE' && $balance !== (int) $payment['amount'])
        || ($status === 'CANCELED' && $balance !== 0)
        || ($status === 'PARTIAL_CANCELED' && (!is_int($balance) || $balance <= 0 || $balance >= (int) $payment['amount']))) {
        error_log('[payment] Toss response mismatch: ' . $payment['order_id']);
        throw new GpPaymentError('PG 결제 정보가 주문과 다릅니다. 관리자에게 문의해 주세요.', 502);
    }
    if (in_array($status, ['ABORTED', 'EXPIRED'], true)) {
        gpPaymentMarkFailed($payment, $status);
        return true;
    }
    if (!in_array($status, ['DONE', 'CANCELED', 'PARTIAL_CANCELED'], true)) {
        return false;
    }

    gpPaymentTransaction(function () use ($payment, $result, $status) {
        $id = $payment['order_id'];
        $source = $payment['source'];
        $sourceId = $payment['source_id'];
        $order = gpPaymentLockOrder($source, $sourceId);
        $current = gpPaymentById($id);
        if ($current['payment_key'] !== $payment['payment_key'] || $order['owner'] !== $payment['user_id']) {
            throw new GpPaymentError('주문과 결제를 연결할 수 없습니다.', 409);
        }
        if ($current['status'] === 'CANCELED') {
            return;
        }
        if ($status === 'DONE' && $current['status'] === 'CONFIRMING') {
            if (!gpPaymentCanPay($source, $order)) {
                throw new GpPaymentError('주문 상태를 확인해 주세요.', 409);
            }
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'DONE', method = ?, approved_at = ?, receipt_url = ?, fail_reason = NULL WHERE order_id = ?",
                [mb_substr((string) ($result['method'] ?? ''), 0, 30), gpPaymentDate($result['approvedAt'] ?? null),
                    gpPaymentReceipt($result['receipt']['url'] ?? null), $id]);
            gpPaymentSetSource($source, $sourceId, true);
        } elseif ($status === 'DONE' && $current['status'] === 'DONE' && gpPaymentCanPay($source, $order)) {
            // 초안에서 승인만 저장되고 주문 반영이 빠진 건도 조회 결과로 복구한다.
            gpPaymentSetSource($source, $sourceId, true);
        } elseif ($status === 'CANCELED' && in_array($current['status'], ['CONFIRMING', 'DONE', 'CANCELING', 'PARTIAL'], true)) {
            $cancels = is_array($result['cancels'] ?? null) ? $result['cancels'] : [];
            $last = end($cancels);
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'CANCELED', canceled_at = ?, cancel_reason = ? WHERE order_id = ?",
                [gpPaymentDate($last['canceledAt'] ?? null), $current['cancel_reason'] ?? mb_substr((string) ($last['cancelReason'] ?? ''), 0, 200), $id]);
            gpPaymentSetSource($source, $sourceId, false);
        } elseif ($status === 'PARTIAL_CANCELED' && in_array($current['status'], ['CONFIRMING', 'DONE', 'CANCELING'], true)) {
            gpPaymentQuery("UPDATE gulpin_payments SET status = 'PARTIAL' WHERE order_id = ?", [$id]);
            if ($current['status'] === 'CONFIRMING') {
                gpPaymentSetSource($source, $sourceId, true);
            }
        }
    });
    return true;
}

function SPaymentCancel($data)
{
    gpPaymentAction(function () use ($data) {
        gpPaymentRequireBrowser();
        if (!gpMgrIsAdmin(gpMgrActor())) {
            throw new GpPaymentError('본사만 결제를 취소할 수 있습니다.', 403);
        }
        $id = gpPaymentOrderId($data['orderId'] ?? null);
        $reason = is_string($data['reason'] ?? null) ? trim($data['reason']) : '';
        if ($reason === '' || mb_strlen($reason) > 200) {
            throw new GpPaymentError('취소 사유를 1~200자로 입력해 주세요.', 400);
        }
        $payment = gpPaymentById($id);
        if (!$payment || !in_array($payment['status'], ['DONE', 'CANCELING', 'CANCELED'], true)) {
            throw new GpPaymentError('전액 취소할 수 있는 결제가 없습니다.', 404);
        }
        if ($payment['status'] === 'CANCELED') {
            return true;
        }
        $config = gpPaymentConfig();
        gpPaymentCheckMode($payment, $config);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }

        // 콘솔 환불이나 이전 요청이 이미 반영됐는지 PG에서 먼저 확인한다.
        [$status, $result] = gpPaymentLookup($config, $id);
        if ($status !== 200 || !gpPaymentApplyToss($payment, $config, $result)) {
            throw new GpPaymentError('PG 원거래를 확인하지 못했습니다. 잠시 후 다시 시도해 주세요.', 503);
        }
        $payment = gpPaymentTransaction(function () use ($payment, $id, $reason) {
            gpPaymentLockOrder($payment['source'], $payment['source_id']);
            $current = gpPaymentById($id);
            if ($current['status'] === 'DONE') {
                gpPaymentQuery("UPDATE gulpin_payments SET status = 'CANCELING', cancel_reason = ? WHERE order_id = ?", [$reason, $id]);
                return gpPaymentById($id);
            }
            return $current;
        });
        if ($payment['status'] === 'CANCELED') {
            return true;
        }
        if ($payment['status'] !== 'CANCELING') {
            throw new GpPaymentError('전액 취소할 수 없는 상태입니다. PG 거래 내역을 확인해 주세요.', 409);
        }
        [$status, $result] = gpTossRequest($config, '/' . rawurlencode($payment['payment_key']) . '/cancel',
            ['cancelReason' => $payment['cancel_reason'], 'cancelAmount' => (int) $payment['amount']], 'cancel-' . $id);
        if ($status === 200) {
            gpPaymentApplyToss($payment, $config, $result);
        } else {
            [$status, $result] = gpPaymentLookup($config, $id);
            if ($status === 200) {
                gpPaymentApplyToss($payment, $config, $result);
            }
        }
        if (gpPaymentById($id)['status'] !== 'CANCELED') {
            throw new GpPaymentError('전액 환불을 확인하지 못했습니다. 주문 내역에서 다시 시도해 주세요.', 503);
        }
        return true;
    });
}

/** 주문 처리 화면에서 결제의 공개 정보만 반환한다. */
function SPaymentPorInfo($data)
{
    gpPaymentAction(function () use ($data) {
        gpPaymentRequireBrowser();
        $actor = gpOrderActor();
        $id = $data['id'] ?? null;
        if (!is_string($id) || !preg_match('/^[A-Za-z0-9_-]{1,32}$/D', $id)) {
            throw new GpPaymentError('주문번호가 올바르지 않습니다.', 400);
        }
        return gpPaymentTransaction(function () use ($actor, $id) {
            $order = gpPaymentLockOrder('por', $id);
            $admin = gpMgrIsAdmin($actor);
            $own = $order['owner'] === $actor['id'];
            if (!$own && !$admin) {
                throw new GpPaymentError('주문을 찾을 수 없습니다.', 404);
            }
            $payment = gpPaymentActive('por', $id);
            return [
                'canPay' => $own && gpPaymentCanPay('por', $order)
                    && (!$payment || $payment['status'] === 'CONFIRMING'),
                'canCancel' => $admin && $payment && in_array($payment['status'], ['DONE', 'CANCELING'], true),
                'orderId' => $payment['order_id'] ?? null,
                'status' => $payment['status'] ?? null,
                'receiptUrl' => gpPaymentReceipt($payment['receipt_url'] ?? null),
            ];
        });
    });
}

function gpPaymentGuardPorEdit(string $id, ?int $newStatus): void
{
    $payment = gpPaymentActive('por', $id);
    if (!$payment) {
        return;
    }
    $allowed = [
        'DONE' => [1, 2, 10],
        'PARTIAL' => [1, 2, 10],
        'CANCELED' => [999],
    ];
    if (!in_array($newStatus, $allowed[$payment['status']] ?? [], true)) {
        throw new GpPaymentError('카드결제 상태는 결제 내역에서 변경해 주세요.', 409);
    }
}

function gpPaymentSetSource(string $source, string $sourceId, bool $paid): void
{
    if ($source === 'por') {
        gpPaymentQuery('UPDATE gulpin_porlist SET confirm = ? WHERE por_id = ?', [$paid ? 2 : 999, $sourceId]);
    } else {
        gpPaymentQuery('UPDATE shopping_orders SET status = ? WHERE id = ?', [$paid ? 'paid' : 'canceled', $sourceId]);
    }
}

function gpPaymentReceipt($url): ?string
{
    if (!is_string($url) || strlen($url) > 500 || !filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }
    $parts = parse_url($url);
    return ($parts['scheme'] ?? '') === 'https' && preg_match('/(^|\.)tosspayments\.com$/iD', $parts['host'] ?? '')
        && !isset($parts['user']) && !isset($parts['port']) ? $url : null;
}

function gpPaymentDate($value): string
{
    $date = is_string($value) ? strtotime($value) : false;
    return gmdate('Y-m-d H:i:s', ($date === false ? time() : $date) + 9 * 3600);
}

function gpPaymentView(array $payment): array
{
    return [
        'orderId' => $payment['order_id'], 'source' => $payment['source'], 'sourceId' => $payment['source_id'],
        'orderName' => $payment['order_name'], 'amount' => (int) $payment['amount'], 'method' => $payment['method'],
        'approvedAt' => $payment['approved_at'], 'receiptUrl' => gpPaymentReceipt($payment['receipt_url']),
    ];
}
