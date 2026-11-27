# Shopping Mall Frontend

HTML/CSS/Vanilla JavaScript로 만든 쇼핑몰 구매/관리 프론트엔드입니다.

## 구성

- 사용자: `index.php`, `product.php`, `cart.php`, `orders.php`
- 관리자: `admin.php`
- API/DB: `../Server/SMethods.php`, `../_private/dbinit.php`
- 공통 호출: `../js/common.js`

## 실행

브라우저에서 접속합니다.

- 사용자 페이지: `http://localhost:8000/shopping/index.php`
- 관리자 페이지: `http://localhost:8000/shopping/admin.php`

## 관리자 계정

- 기존 `Slogon` 로그인을 사용합니다.
- DB에서 활성 상태인 본사 계정(`gulpin_user.role = 9`)이면 관리자 기능에 접근할 수 있습니다.

## API

프론트는 `../js/common.js`의 `CallAjax`로 `../Server/SMethods.php`를 호출합니다.

- `SShoppingListProducts`
- `SShoppingGetProduct`
- `SShoppingSaveProduct`
- `SShoppingDeleteProduct`
- `SShoppingCreateOrder`
- `SShoppingMyOrders`, `SShoppingAdminOrders`
- `SShoppingMe`
- `SShoppingLogout`

토스페이먼츠 카드결제와 본사 환불 설정은 [결제 배포 문서](../doc/toss-payment-deploy.md)를 참고합니다.
