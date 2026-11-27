<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>주문 내역</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">Shopping Mall</a>
        <nav>
            <a href="index.php">상품</a>
            <a href="cart.php">장바구니 <span id="cartCount">0</span></a>
            <a href="orders.php">주문내역</a>
            <a href="admin.php">Admin</a>
        </nav>
    </header>

    <main class="container">
        <section class="page-head">
            <div>
                <h1>주문 내역</h1>
                <p>결제 전 주문은 여기서 다시 결제할 수 있습니다. 취소·반품은 고객센터로 요청해 주세요.</p>
            </div>
        </section>
        <div id="orderList" class="list"></div>
        <p id="orderMessage" class="notice"></p>
    </main>

    <?php $gpFooterBase = '../'; include __DIR__ . '/../libs/footer.php'; ?>

    <script src="https://js.tosspayments.com/v2/standard"></script>
    <script src="../js/common.js"></script>
    <script src="../js/payment.js?v=2"></script>
    <script src="js/api.js"></script>
    <script src="js/orders.js?v=2"></script>
</body>
</html>
