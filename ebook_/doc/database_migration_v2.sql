-- ========================================
-- EPLAT Ebook - DB Migration v2
-- 시스템 설정 카테고리 추가
-- ========================================

USE happyzip1;



-- 기본 카테고리 삽입 (기존 ROOT_PATH 값 사용)
INSERT IGNORE INTO system_config_categories
    (category_code, category_name, description, root_path, is_default, display_order)
VALUES
    ('general', '기본 설정', '기본 시스템 카테고리',
     (SELECT config_value FROM system_config WHERE config_key='ROOT_PATH' LIMIT 1),
     1, 0);

-- root_path가 NULL인 경우 기본값 적용
UPDATE system_config_categories SET root_path='../ebook_new_eplat/' WHERE root_path IS NULL OR root_path='';



-- 기존 데이터 카테고리 지정
UPDATE system_config SET category_code='general' WHERE category_code IS NULL OR category_code='';


-- ========================================
-- 인덱스
-- ========================================
CREATE INDEX IF NOT EXISTS idx_sysconf_category ON system_config(category_code);
CREATE INDEX IF NOT EXISTS idx_booktype_category ON book_types(category_code);

-- ========================================
-- 완료
-- ========================================
SELECT 'Migration v2 완료: system_config_categories 생성 및 category_code 컬럼 추가' AS result;
