# 홈스쿨 학생관리 1차 배포

## 반영 범위

- 홈스쿨 선생님(role 30) 로그인 → 학생관리 → `purchase/homeschoolstudents.php`.
- 입력: 과정, 학생명, 아이디, 비번, 출생년도, 학교명, 부모님 연락처,
  단계(교재 선택), 수업 시작일, 수업 종료일.
- 반선택/반이름 입력 없이 학생 단위로 등록·수정·삭제·검색.
- 기존 `Server/price_catalog.json` 홈스쿨 그룹의 전체 항목을 사용.
  구성품이 있는 항목도 포함하며 임의의 세트 상품/가격을 추가하지 않는다.
- `step`은 기존 과정값을 유지하고 실제 교재 코드는 `material_code`에 별도로 저장.
  학교명은 `classnm`, 부모님 연락처는 `p_mobile`에 저장하고 기존 조회 호환을 위해
  `mobile`에도 같은 연락처를 저장한다. 학생 비밀번호는 기존 아이디 학생 정책 유지.
- 기존 학생은 자동 교재 배정 없이 조회되며, 수정 시 교재를 선택해 저장한다.
  학생 정보 10개 항목은 등록·수정 시 모두 필수 입력이다.
- 로그인 API와 사용자 권한 범위는 유지한다. 다른 선생님의 학생은 수정할 수 없다.

## 배포 순서

1. `doc/homeschool-students.sql` 실행. 신규 테이블은 없다.
2. `Server/homeschool_policy.php` 업로드.
3. `Server/student_policy.php`, `Server/SMethods.php`, `_private/dbinit.php` 업로드.
4. `purchase/homeschoolstudents.php`, `purchase/homeschoolstudents.js` 업로드.
5. `purchase/kgardenmgr.php`, `js/roles.js` 업로드.
6. 홈스쿨 선생님 계정으로 재접속하고 Ctrl+F5로 메뉴 캐시 갱신.

SQLite 환경은 `Server/sqlite_dbinit.php`, `Server/SMethodsSQLite.php`도 업로드한다.
기존 유치원 선생님의 학생관리 주소는 유지한다.

## 후속 작업

월별 교재 신청 버튼, 신청 내역 저장, 지사별 취합 및 기존 주문관리의 일괄 주문 확정은
이번 1차 작업에 포함하지 않는다. 다음 단계에서 신청월·수량·중복 신청·주문 상태와
기존 주문번호/결제 흐름을 연결한다. 이번 학생별 `material_code` 매핑이 그 기준이 된다.

## 2차: 교재 주문 (gulpin_home_order_stat)

- 학생 목록 단계(교재명) select 즉시 저장, 행마다 '교재 주문' 버튼(삭제 앞), 삭제 버튼 붉은색.
- 교재 주문은 학생·교재당 월 1건 (UNIQUE student_id + order_month + material_code). 현재 교재가 이번 달 대기/완료면 버튼 잠금,
  단계를 바꾸면 새 교재 기준으로 다시 활성, 다음 달 다시 활성.
- 홈스쿨 지사장(role 3) order.php '홈스쿨 교재 신청': 월별 조회, 주문 불러오기(장바구니), 선택 완료 처리.
  구매내역서 저장 시 불러온 신청 중 주문서에 남은 교재만 status = 1, complete_date = 저장 시각.

- 과정은 '홈스쿨' 고정(step = 홈스쿨), 단계는 급파한/홈스쿨 교재 전체에서 선택. 기존 '급파한' 학생도 홈스쿨로 표시.
- 학생 목록 비번 표시, 관리 칸 수업중/수업종료 선택(status 1/0, 수업종료는 로그인 불가).
- 홈스쿨 선생님 등록(branchmgr 구분 30)은 주소검색/우편번호/주소 비활성화, 주소 없이 등록.

배포 순서

1. `doc/homeschool-orders.sql` 실행 (`_private/dbinit.php`도 접속 시 자동 생성).
2. `Server/homeschool_policy.php`, `Server/mgr_policy.php`, `Server/SMethods.php`, `_private/dbinit.php` 업로드.
3. `purchase/homeschoolstudents.php`, `purchase/homeschoolstudents.js`, `purchase/order.php`, `purchase/branchmgr.php` 업로드.

SQLite(`SMethodsSQLite.php`)는 구매내역서 저장 시 자동 완료 처리를 하지 않는다 (수동 완료 처리는 동작).
