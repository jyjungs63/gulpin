-- MariaDB: 홈스쿨 교재 주문 테이블 (_private/dbinit.php ensureHomeOrderTable 과 같음)
CREATE TABLE IF NOT EXISTS `gulpin_home_order_stat` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `branch_id` VARCHAR(30) NOT NULL COMMENT '홈스쿨 지사 ID',
  `teacher_id` VARCHAR(30) NOT NULL COMMENT '홈스쿨 선생 ID',
  `student_id` VARCHAR(30) NOT NULL COMMENT '홈스쿨 학생 ID',
  `material_title` VARCHAR(100) NOT NULL COMMENT '교재명',
  `material_code` VARCHAR(64) NOT NULL COMMENT '교재 코드',
  `order_date` DATETIME NOT NULL COMMENT '주문일',
  `complete_date` DATETIME NULL COMMENT '주문완료일',
  `status` TINYINT NULL COMMENT '주문상태 NULL 대기, 1 완료',
  `order_month` CHAR(7) NOT NULL COMMENT '주문월 YYYY-MM (학생·교재당 월 1건)',
  UNIQUE KEY `uq_home_order_student_month_material` (`student_id`, `order_month`, `material_code`),
  KEY `idx_home_order_branch_month` (`branch_id`, `order_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 이전 키(학생당 월 1건)로 이미 만든 테이블만: 아래를 한 번 실행
-- ALTER TABLE `gulpin_home_order_stat` DROP INDEX `uq_home_order_student_month`,
--   ADD UNIQUE KEY `uq_home_order_student_month_material` (`student_id`, `order_month`, `material_code`);
