-- 과정 저장 값 '홈스쿨' → '방문수업' (MariaDB). 소스 배포 후 한 번 실행, 다시 실행해도 안전하다.
-- 실행 전 DB 백업. 지사명·이름(owner, name)은 사용자 입력값이라 바꾸지 않는다.

-- 1) 바뀔 건수 확인
SELECT 'student step' AS target, COUNT(*) AS cnt FROM gulpin_user WHERE role = 300 AND step = '홈스쿨'
UNION ALL SELECT 'branch class', COUNT(*) FROM gulpin_user WHERE role IN (3, 30) AND etc = '홈스쿨'
UNION ALL SELECT 'order title', COUNT(*) FROM gulpin_home_order_stat WHERE material_title LIKE '%홈스쿨%'
UNION ALL SELECT 'branch not 전체', COUNT(*) FROM gulpin_user WHERE role = 3 AND (etc IS NULL OR etc <> '전체')
UNION ALL SELECT 'teacher course', COUNT(*) FROM gulpin_user WHERE role = 30 AND (etc IS NULL OR etc NOT IN ('방문수업', '급파한'));

-- 2) 변환
START TRANSACTION;
UPDATE gulpin_user SET step = '방문수업' WHERE role = 300 AND step = '홈스쿨';
UPDATE gulpin_user SET etc = '방문수업' WHERE role IN (3, 30) AND etc = '홈스쿨';
UPDATE gulpin_home_order_stat SET material_title = REPLACE(material_title, '홈스쿨', '방문수업') WHERE material_title LIKE '%홈스쿨%';
-- 방문수업 지사는 과정 '전체', 교사는 방문수업/급파한 중 하나 — 예전 '전체' 등은 방문수업으로 (Server/mgr_policy.php gpHomeschoolClass)
UPDATE gulpin_user SET etc = '전체' WHERE role = 3 AND (etc IS NULL OR etc <> '전체');
UPDATE gulpin_user SET etc = '방문수업' WHERE role = 30 AND (etc IS NULL OR etc NOT IN ('방문수업', '급파한'));
COMMIT;
