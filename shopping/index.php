<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Mall</title>
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
                <h1>상품 목록</h1>
                <p>REST API로 불러온 상품을 확인하고 구매해 보세요.</p>
            </div>
        </section>
        <div id="productGrid" class="product-grid"></div>
        <p id="emptyState" class="empty hidden">등록된 상품이 없습니다.</p>
    </main>

    <script src="../js/common.js"></script>
    <script src="js/api.js"></script>
    <script src="js/shop.js"></script>
</body>
</html>
