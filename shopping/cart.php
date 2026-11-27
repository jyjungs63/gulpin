<!doctype html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>장바구니 · 주문/결제</title>
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
                <h1>장바구니 · 주문/결제</h1>
                <p>주문 상품과 결제 금액, 배송 정보를 확인한 뒤 결제해 주세요.</p>
            </div>
        </section>
        <div id="cartList" class="list"></div>

        <section id="checkout" class="checkout hidden">
            <div id="loginNotice" class="panel hidden">
                <p>로그인한 회원만 주문할 수 있습니다.</p>
                <a class="primary btn-link" href="../index.php">로그인하러 가기</a>
            </div>

            <form id="checkoutForm" class="panel form hidden">
                <h2>배송 정보</h2>
                <label>받는 분 <input name="receiver_name" maxlength="30" autocomplete="name" required></label>
                <label>휴대전화 <input name="receiver_phone" maxlength="20" inputmode="tel" autocomplete="tel" placeholder="010-0000-0000" required></label>
                <div class="zip-row">
                    <label>우편번호 <input id="idZip" name="zip" maxlength="10" readonly></label>
                    <button type="button" class="secondary" onclick="execDaumPostcode('idZip', 'idAddr')">주소찾기</button>
                </div>
                <label>주소 <input id="idAddr" name="addr" maxlength="255" required placeholder="주소찾기 후 상세주소를 이어서 입력하세요"></label>

                <h2>결제 금액</h2>
                <dl class="summary">
                    <div><dt>상품 금액</dt><dd id="sumSubtotal">0원</dd></div>
                    <div><dt>배송비</dt><dd id="sumShipping">0원</dd></div>
                    <div class="total"><dt>총 결제금액</dt><dd id="sumTotal">0원</dd></div>
                </dl>
                <p class="notice">배송비 4,500원 (100,000원 이상 무료) · 결제 완료 후 7일 이내 발송 · 결제수단: 신용·체크카드 (토스페이먼츠)</p>
                <p class="notice">상품을 받은 날부터 7일 이내 청약철회(반품)할 수 있습니다. 단순 변심 반품 배송비는 구매자 부담이며, 자세한 내용은 <a href="../policy/refund.php" target="_blank">교환·반품·환불 안내</a>를 확인해 주세요.</p>

                <h2>약관 동의</h2>
                <div class="agree">
                    <label class="check all"><input type="checkbox" id="agreeAll"> 아래 내용에 모두 동의합니다.</label>
                    <label class="check"><input type="checkbox" class="agree-item" required> [필수] 주문 상품, 결제 금액, 배송 정보를 확인했으며 구매에 동의합니다.</label>
                    <label class="check"><input type="checkbox" class="agree-item" required> [필수] <a href="../policy/terms.php" target="_blank">이용약관</a> 및 <a href="../policy/refund.php" target="_blank">교환·반품·환불 안내</a>를 확인했습니다.</label>
                    <label class="check"><input type="checkbox" class="agree-item" required> [필수] 개인정보 수집·이용에 동의합니다.</label>
                    <table class="agree-table">
                        <tr><th>수집 항목</th><td>받는 분 성명, 휴대전화번호, 배송지 주소, 주문 내역</td></tr>
                        <tr><th>이용 목적</th><td>상품 배송, 주문 확인·취소, 교환·반품·환불 처리</td></tr>
                        <tr><th>보유 기간</th><td>전자상거래법에 따라 5년 보관 후 파기</td></tr>
                    </table>
                    <p class="notice">동의를 거부할 수 있으나, 거부하시면 주문할 수 없습니다. 결제 정보는 결제대행사(토스페이먼츠)가 처리합니다. 자세한 내용은 <a href="../policy/privacy.php" target="_blank">개인정보처리방침</a>을 확인해 주세요.</p>
                </div>

                <button id="payBtn" class="primary pay" type="submit">결제하기</button>
                <p id="cartMessage" class="notice"></p>
            </form>
        </section>
    </main>

    <?php $gpFooterBase = '../'; include __DIR__ . '/../libs/footer.php'; ?>

    <script src="//t1.daumcdn.net/mapjsapi/bundle/postcode/prod/postcode.v2.js"></script>
    <script src="https://js.tosspayments.com/v2/standard"></script>
    <script src="../js/common.js?v=2"></script>
    <script src="../js/payment.js?v=2"></script>
    <script src="js/api.js?v=2"></script>
    <script src="js/cart.js?v=2"></script>
</body>
</html>
