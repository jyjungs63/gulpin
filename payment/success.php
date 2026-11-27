<?php
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>결제 확인</title>
    <link rel="stylesheet" href="../shopping/css/style.css">
</head>
<body>
    <main class="container">
        <section class="panel" aria-live="polite">
            <h1 id="title">결제를 확인하고 있습니다…</h1>
            <p class="notice" id="message">창을 닫지 말고 잠시 기다려 주세요.</p>
            <p id="details" hidden></p>
            <p class="notice" id="orderNumber" hidden></p>
            <div class="actions" id="actions" hidden>
                <a class="secondary btn-link" id="receipt" target="_blank" rel="noopener noreferrer" hidden>영수증 보기</a>
                <button type="button" class="secondary" id="retry">결제 상태 다시 확인</button>
                <a class="primary btn-link" id="back" href="../shopping/orders.php">주문 내역 보기</a>
            </div>
        </section>
    </main>
    <?php $gpFooterBase = '../'; include __DIR__ . '/../libs/footer.php'; ?>

    <script src="../js/common.js"></script>
    <script src="../js/payment.js?v=2"></script>
    <script>
    (async function () {
        const url = new URL(location.href);
        const query = url.searchParams;
        const title = document.getElementById('title');
        const message = document.getElementById('message');
        const details = document.getElementById('details');
        const orderNumber = document.getElementById('orderNumber');
        const actions = document.getElementById('actions');
        const back = document.getElementById('back');
        const retry = document.getElementById('retry');
        const setBack = (source) => {
            back.href = source === 'por' ? '../purchase/order.php' : '../shopping/orders.php';
            if (query.get('db') === 'sqlite' && source === 'por') back.href += '?db=sqlite';
        };
        setBack(query.get('source'));
        retry.addEventListener('click', () => location.reload());
        const id = query.get('orderId') || '';
        if (/^[A-Za-z0-9_=-]{6,64}$/.test(id)) {
            orderNumber.textContent = '결제번호 ' + id;
            orderNumber.hidden = false;
        }

        try {
            const payment = await gpPaymentCall('SPaymentConfirm', {
                orderId: id,
                paymentKey: query.get('paymentKey') || '',
                amount: query.get('amount') || '',
                returnToken: query.get('returnToken') || '',
            });
            if (payment.source === 'shop') {
                try {
                    const saved = JSON.parse(sessionStorage.getItem('gulpin_checkout') || 'null');
                    if (saved && String(saved.orderId) === String(payment.sourceId)) {
                        if (saved.cart === localStorage.getItem('shopping_cart')) localStorage.removeItem('shopping_cart');
                        sessionStorage.removeItem('gulpin_checkout');
                    }
                } catch {}
            }
            title.textContent = '결제가 완료되었습니다';
            message.textContent = payment.orderName + ' · ' + payment.amount.toLocaleString('ko-KR') + '원';
            details.textContent = [payment.method, payment.approvedAt].filter(Boolean).join(' · ');
            details.hidden = details.textContent === '';
            if (payment.receiptUrl) {
                const receipt = document.getElementById('receipt');
                receipt.href = payment.receiptUrl;
                receipt.hidden = false;
            }
            setBack(payment.source);
            retry.hidden = true;
            for (const key of ['paymentKey', 'amount', 'returnToken', 'paymentType']) query.delete(key);
            history.replaceState(null, '', url.href);
        } catch (error) {
            title.textContent = '결제 상태를 확인해 주세요';
            message.textContent = error.message;
        } finally {
            actions.hidden = false;
        }
    })();
    </script>
</body>
</html>
