유-- ========================================
-- EPLAT Ebook - 데이터베이스 스키마
-- ========================================


CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE happyzip1;

-- ========================================
-- 1. 책 타입 테이블
-- ========================================
CREATE TABLE `book_types` (
  `id` int(11) NOT NULL,
  `type_code` varchar(50) NOT NULL COMMENT '타입 코드 (예: phonics)',
  `type_name` varchar(100) NOT NULL COMMENT '타입 한글명 (예: 파닉스)',
  `directory_path` varchar(255) NOT NULL COMMENT '디렉토리 경로',
  `display_mode` varchar(10) DEFAULT 'double',
  `category_code` varchar(50) DEFAULT NULL COMMENT '시스템 설정 카테고리 (NULL=기본 카테고리 사용)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성화 여부',
  `display_order` int(11) DEFAULT 0 COMMENT '표시 순서',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='책 타입 정보';

-- ========================================
-- 2. 시스템 설정 테이블
-- ========================================
CREATE TABLE `system_config` (
  `id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL COMMENT '설정 키',
  `category_code` varchar(50) NOT NULL DEFAULT 'general' COMMENT '카테고리 코드',
  `config_value` text NOT NULL COMMENT '설정 값',
  `config_type` varchar(20) DEFAULT 'string' COMMENT '데이터 타입 (string, number, boolean, json)',
  `description` varchar(255) DEFAULT NULL COMMENT '설명',
  `is_editable` tinyint(1) DEFAULT 1 COMMENT '편집 가능 여부',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='시스템 설정';

-- ========================================
-- 3. 책 정보 테이블 (선택사항)
-- ========================================
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_code VARCHAR(50) NOT NULL UNIQUE COMMENT '책 코드 (예: phonics.v1.1)',
    type_code VARCHAR(50) NOT NULL COMMENT '타입 코드',
    step VARCHAR(20) NOT NULL COMMENT '단계 (예: v1)',
    volume VARCHAR(20) NOT NULL COMMENT '권수 (예: 1)',
    title VARCHAR(200) COMMENT '책 제목',
    page_count INT DEFAULT 0 COMMENT '페이지 수',
    img_format VARCHAR(10) DEFAULT 'jpg' COMMENT '이미지 포맷',
    sound_format VARCHAR(10) DEFAULT '.mp3' COMMENT '사운드 포맷',
    is_active TINYINT(1) DEFAULT 1 COMMENT '활성화 여부',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (type_code) REFERENCES book_types(type_code) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='책 정보';

-- ========================================
-- 4. 동영상 링크 테이블 (선택사항)
-- ========================================
CREATE TABLE IF NOT EXISTS video_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    book_id INT NOT NULL COMMENT '책 ID',
    page_number INT NOT NULL COMMENT '페이지 번호',
    video_url VARCHAR(500) NOT NULL COMMENT '동영상 URL',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE,
    UNIQUE KEY unique_book_page (book_id, page_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='페이지별 동영상 링크';


CREATE TABLE `system_config_categories` (
  `id` int(11) NOT NULL,
  `category_code` varchar(50) NOT NULL COMMENT '카테고리 코드',
  `category_name` varchar(100) NOT NULL COMMENT '카테고리 이름',
  `description` varchar(255) DEFAULT NULL COMMENT '카테고리 설명',
  `root_path` varchar(255) DEFAULT '../ebook_new_eplat/' COMMENT '이 카테고리 책들의 루트 경로',
  `is_default` tinyint(1) DEFAULT 0 COMMENT '기본 카테고리 여부 (1개만 true)',
  `display_order` int(11) DEFAULT 0 COMMENT '표시 순서',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='시스템 설정 카테고리';
-- ========================================
-- 초기 데이터 삽입
-- ========================================

-- 책 타입 데이터
INSERT INTO book_types (type_code, type_name, directory_path, display_order) VALUES
('story', '스토리북', 'bookshelf-1/1-1_story/', 1),
('phonics', '파닉스', 'bookshelf-1/2-1_phonics/', 2),
('ph_sentence', '파닉스 문장', 'bookshelf-1/2-2_phonics_sentence/', 3),
('st_sentence', '스토리 문장', 'bookshelf-1/1-2_story_sentence/', 4),
('block', '블록', 'bookshelf-2/1-1_blocks/', 5);

-- 시스템 설정 데이터
INSERT INTO system_config (config_key, config_value, config_type, description) VALUES
('ROOT_PATH', '../ebook_new_eplat/', 'string', '루트 경로'),
('DEFAULT_BOOK', 'story.basic.v1', 'string', '기본 책'),
('IMG_FORMAT', 'png', 'string', '이미지 포맷'),
('SOUND_FORMAT', 'mp3', 'string', '사운드 포맷');

-- ========================================
-- 인덱스 추가
-- ========================================
CREATE INDEX idx_book_types_active ON book_types(is_active);
CREATE INDEX idx_books_type ON books(type_code);
CREATE INDEX idx_books_active ON books(is_active);
CREATE INDEX idx_video_links_book ON video_links(book_id);

-- ========================================
-- 뷰 생성 (전체 설정 조회용)
-- ========================================
CREATE OR REPLACE VIEW v_book_config AS
SELECT 
    bt.type_code,
    bt.type_name,
    bt.directory_path,
    bt.is_active,
    bt.display_order
FROM book_types bt
WHERE bt.is_active = 1
ORDER BY bt.display_order;

-- ========================================
-- 완료 메시지
-- ========================================
SELECT 'Database schema created successfully!' AS message;
