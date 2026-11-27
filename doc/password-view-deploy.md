# PG 도입 전 관리자 비밀번호 확인

로그인용 `password` 해시는 유지한다. 확인용 사본만 `password_admin_view`에
AES-256-GCM으로 암호화해 저장하며, 복호화 키는 `_private/password_view_key.php`에 둔다.
키 파일은 Git에서 제외되어 있으므로 FTP로 별도 배포하고 동일한 키를 유지해야 한다.
서버가 여러 대면 같은 키를 사용한다. 키 파일은 접근이 차단된 `_private`에 두고
웹 공개 경로 밖에 둘 수 있다면 환경변수 `GP_PASSWORD_VIEW_KEY`로 대체할 수 있다.

## 배포

1. `doc/password-view-migration.sql`의 ALTER 구문 실행(기존 password는 유지).
2. `_private/password_view.php`, `_private/password_view_key.php` 업로드.
3. `_private/dbinit.php`, `Server/password_policy.php`, `Server/mgr_policy.php`, `Server/SMethods.php` 업로드.
4. `login/adminLogin.php`, `login/adminLogin.js` 업로드.
5. SQLite 사용 환경만 `Server/sqlite_dbinit.php`, `Server/SMethodsSQLite.php`도 업로드.
6. 본사 관리자 로그인 → 비밀번호 열의 '확인' → 추가 비밀번호 입력 없이 조회.

이미 해시로 저장된 비밀번호는 복원이 불가능하다. 적용 이후 로그인에 성공하거나
등록·변경·재설정한 지사/선생 계정부터 확인할 수 있다. 로그인 실패 시에는 저장하지 않는다.
본사 계정 비밀번호는 확인용 사본을 만들지 않는다. 학생의 기존 저장 정책은 유지한다.
확인은 로그인한 본사 계정에만 허용되며 세션과 DB에서 현재 권한을 확인한다.
확인 이력은 비밀번호 없이 서버 로그에 남긴다.

## PG 도입 시 종료

1. `_private/password_view.php`의 `'enabled' => false` 설정을 배포한다. 현재 코드의 기본값이다.
   환경변수 `GP_PASSWORD_VIEW_ENABLED`를 사용했다면 `0`으로 설정한다.
2. `UPDATE gulpin_user SET password_admin_view = NULL;` 실행.
3. 확인용 키 파일을 운영에서 제거한다. 필요한 백업은 별도 보관 정책에 따라 처리한다.

이후에도 기존 `password` 해시로 동일한 비밀번호를 계속 사용한다.

## 로그인 후 확인용 컬럼이 비어 있을 때

`_private/password_view.php`와 `_private/password_view_key.php` 두 파일이 모두
운영 서버에 있어야 한다. 특히 키 파일은 Git에서 제외되므로 별도 FTP 업로드를 확인한다.
키 파일을 새로 생성하거나 기존 키를 덮어쓰지 말고 현재 로컬 키를 사용한다.
`enabled`는 `true`여야 하며, 환경변수 `GP_PASSWORD_VIEW_ENABLED`가 있으면 `1`이어야 한다.
PHP OpenSSL의 AES-256-GCM 지원도 필요하다.
수정된 `Server/password_policy.php`는 본사 세션 확인 후 키/암호화 환경 오류를
화면에 구분해 표시한다. 로그인 중 암호화가 실패하면 기존 사본을 지우지 않는다.

로그인 성공 경로는 `Server/SMethods.php`의 `Slogon`이다. 정책 파일과 함께 업로드한다.
저장 직후 값을 다시 조회해 일치 여부를 검증한다. 브라우저 개발자 도구의 로그인 요청
응답 헤더 `X-Gulpin-Password-View`와 PHP 오류 로그에 저장 결과가 남는다.
`saved`: 저장·재조회 성공, `disabled`: 설정 비활성화,
`column_missing`: 로그인 서버 DB에 컬럼 없음,
`encryption_failed`: 암호화 실패, `write_mismatch`: DB 저장 후 값 불일치,
`excluded_role`: 본사 또는 학생으로 확인용 사본 저장 제외.
헤더 자체가 없다면 배포한 로그인 파일/실제 요청 URL/PHP OPcache 갱신 여부를 확인한다.
이 진단에는 비밀번호, 키, 암호문을 포함하지 않는다.
