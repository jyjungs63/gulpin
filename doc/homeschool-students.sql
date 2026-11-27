-- MariaDB: 기존 데이터 보존. 신규 테이블은 생성하지 않는다.
ALTER TABLE `gulpin_user`
  ADD COLUMN IF NOT EXISTS `birth_year` SMALLINT UNSIGNED NULL COMMENT '출생년도',
  ADD COLUMN IF NOT EXISTS `material_code` VARCHAR(64) NULL COMMENT '학생별 선택 교재 코드',
  ADD COLUMN IF NOT EXISTS `birthday` DATE NULL COMMENT '생년월일',
  ADD COLUMN IF NOT EXISTS `sdate` DATE NULL COMMENT '수업 시작일',
  ADD COLUMN IF NOT EXISTS `edate` DATE NULL COMMENT '수업 종료일';
