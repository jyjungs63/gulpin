<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin 상품 관리</title>
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

    <main class="container admin-layout">
        <section id="loginPanel" class="panel">
            <h1>Admin 로그인</h1>
            <form id="loginForm" class="form">
                <label>아이디 <input name="username" autocomplete="username" required></label>
                <label>비밀번호 <input name="password" type="password" autocomplete="current-password" required></label>
                <button class="primary" type="submit">로그인</button>
                <p id="loginMessage" class="notice"></p>
            </form>
        </section>

        <section id="adminPanel" class="hidden">
            <div class="page-head">
                <div>
                    <h1>상품 관리</h1>
                    <p>상품 등록, 수정, 삭제를 관리합니다.</p>
                </div>
                <button id="logoutBtn" class="secondary">로그아웃</button>
            </div>

            <form id="productForm" class="panel form" enctype="multipart/form-data">
                <input type="hidden" name="id">
                <label>상품명 <input name="name" required></label>
                <label>가격 <input name="price" type="number" min="1" step="1" required></label>
                <label>설명 <textarea name="description" rows="4" required></textarea></label>
                <label>이미지 URL <input name="image_url" placeholder="업로드 대신 URL 사용 가능"></label>
                <label>이미지 업로드 <input name="image" type="file" accept="image/*"></label>
                <div class="actions">
                    <button class="primary" type="submit">저장</button>
                    <button id="resetBtn" class="secondary" type="button">새 상품</button>
                </div>
                <p id="formMessage" class="notice"></p>
            </form>

            <div id="adminProducts" class="list"></div>
        </section>
    </main>

    <script src="../js/common.js"></script>
    <script src="js/api.js"></script>
    <script src="js/admin.js"></script>
</body>
</html>
