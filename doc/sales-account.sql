-- MariaDB: 영업(시연)용 계정 — 말잼별글잼별(maljam/) 에서 잼잼별·말잼별·글잼별 1호만 열린다.
-- 영업 계정 = 유치원생(100) 중 과정(etc)이 '영업'인 계정 (Server/mgr_policy.php gpIsSalesAccount).
-- 보통은 본사 로그인 → purchase/branchmgr.php 구분 "영업" 으로 등록한다. 아래는 guest1 을 SQL 로 직접 만들 때.
-- 학생 비밀번호는 평문 저장 규칙(password_policy.php)을 따른다.
INSERT INTO `gulpin_user` (`id`, `name`, `password`, `role`, `confirm`, `status`, `classnm`, `etc`, `pw_temp`)
VALUES ('guest1', '영업시연', 'guest1', 100, 1, 1, '영업시연', '영업', 0);
