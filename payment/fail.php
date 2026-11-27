<?php
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
$canceled = in_array($_GET['code'] ?? '', ['USER_CANCEL', 'PAY_PROCESS_CANCELED'], true);
$back = ($_GET['source'] ?? '') === 'por' ? '../purchase/order.php' : '../shopping/orders.php';
if ($back === '../purchase/order.php' && ($_GET['db'] ?? '') === 'sqlite') {
    $back .= '?db=sqlite';
}
?>
<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>결제 실패</title>
    <link rel="stylesheet" href="../shopping/css/style.css">
</head>
<body>
    <main class="container">
        <section class="panel">
            <h1><?= $canceled ? '결제를 취소했습니다' : '결제가 완료되지 않았습니다' ?></h1>
            <p class="notice">주문은 저장되어 있으니 주문 내역에서 다시 결제할 수 있습니다.</p>
            <div class="actions">
                <a class="primary btn-link" href="<?= htmlspecialchars($back, ENT_QUOTES, 'UTF-8') ?>">주문 내역 보기</a>
            </div>
        </section>
    </main>
    <?php $gpFooterBase = '../'; include __DIR__ . '/../libs/footer.php'; ?>
</body>
</html>
