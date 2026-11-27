-- MariaDB: PG 도입 전 관리자 확인용 암호화 사본 저장 컬럼.
-- 기존 password 값(로그인용 해시)은 변경하지 않는다.
ALTER TABLE `gulpin_user`
  ADD COLUMN IF NOT EXISTS `password_admin_view` TEXT NULL
  COMMENT 'PG 도입 전 관리자 확인용 암호화 사본' AFTER `password`;

-- PG 적용 시 _private/password_view.php의 enabled를 false로 바꾼 뒤
-- 아래 SQL을 별도로 실행해 확인용 사본을 삭제한다.
-- UPDATE `gulpin_user` SET `password_admin_view` = NULL;
