<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>상품 상세</title>
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
        <article id="productDetail" class="detail"></article>
    </main>

    <script src="../js/common.js"></script>
    <script src="js/api.js"></script>
    <script src="js/product.js"></script>
</body>
</html>
