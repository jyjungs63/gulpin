-- MariaDB: 홈스쿨 교재 신청 기간 설정 + 다음 단계 자동 변경 기록 (_private/dbinit.php ensureHomeOrderTable 과 같음)
-- dbinit.php 가 요청 때 자동으로 만들지만, DB 계정에 ALTER/CREATE 권한이 없으면 아래를 직접 실행한다.
CREATE TABLE IF NOT EXISTS `gulpin_setting` (
  `skey` VARCHAR(50) NOT NULL PRIMARY KEY COMMENT '설정 이름',
  `svalue` VARCHAR(255) NOT NULL COMMENT '설정 값'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `gulpin_user`
  ADD COLUMN IF NOT EXISTS `material_auto_month` CHAR(7) NULL COMMENT '다음 단계 자동 변경을 처리한 달 YYYY-MM';

-- 기본 신청 기간은 매월 10일~15일 (행이 없으면 코드 기본값). 본사 주문현황 화면에서 바꿀 수 있다.
