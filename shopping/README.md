# Shopping Mall Frontend

HTML/CSS/Vanilla JavaScript로 만든 쇼핑몰 구매/관리 프론트엔드입니다.

## 구성

- 사용자: `index.php`, `product.php`, `cart.php`
- 관리자: `admin.php`
- API/DB: `../Server/SMethods.php`, `../Server/dbinit.php`
- 공통 호출: `../js/common.js`

## 실행

브라우저에서 접속합니다.

- 사용자 페이지: `http://localhost:8000/shopping/index.php`
- 관리자 페이지: `http://localhost:8000/shopping/admin.php`

## 관리자 계정

- 기존 `Slogon` 로그인을 사용합니다.
- `chaitalk_user.role = 9` 또는 `admin/chaitalk` 계정이면 관리자 기능에 접근할 수 있습니다.

## API

프론트는 `../js/common.js`의 `CallAjax`로 `../Server/SMethods.php`를 호출합니다.

- `SShoppingListProducts`
- `SShoppingGetProduct`
- `SShoppingSaveProduct`
- `SShoppingDeleteProduct`
- `SShoppingCreateOrder`
- `SShoppingMe`
- `SShoppingLogout`
