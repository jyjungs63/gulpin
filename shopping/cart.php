<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>장바구니</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="topbar">
        <a class="brand" href="index.php">Shopping Mall</a>
        <nav>
            <a href="index.php">상품</a>
            <a href="cart.php">장바구니 <span id="cartCount">0</span></a>
            <a href="admin.php">Admin</a>
        </nav>
    </header>

    <main class="container">
        <section class="page-head">
            <div>
                <h1>장바구니</h1>
                <p>담아둔 상품을 주문합니다.</p>
            </div>
            <button id="checkoutBtn" class="primary">구매하기</button>
        </section>
        <div id="cartList" class="list"></div>
        <p id="cartMessage" class="notice"></p>
    </main>

    <script src="../js/common.js"></script>
    <script src="js/api.js"></script>
    <script src="js/cart.js"></script>
</body>
</html>
