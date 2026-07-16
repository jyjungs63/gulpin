-- happyzip1.book_types definition

CREATE TABLE `book_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_code` varchar(50) NOT NULL COMMENT '타입 코드 (예: phonics)',
  `type_name` varchar(100) NOT NULL COMMENT '타입 한글명 (예: 파닉스)',
  `directory_path` varchar(255) NOT NULL COMMENT '디렉토리 경로',
  `display_mode` varchar(10) DEFAULT 'double',
  `category_code` varchar(50) DEFAULT NULL COMMENT '시스템 설정 카테고리 (NULL=기본 카테고리 사용)',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성화 여부',
  `display_order` int(11) DEFAULT 0 COMMENT '표시 순서',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`type_code`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='책 타입 정보';


-- happyzip1.chaitalk_addrlist definition

CREATE TABLE `chaitalk_addrlist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL,
  `owner` varchar(30) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `addr` varchar(100) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `rdate` date DEFAULT current_timestamp(),
  `mid` varchar(30) DEFAULT NULL,
  `etc` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_online_ma definition

CREATE TABLE `chaitalk_online_ma` (
  `id` varchar(30) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `status` enum('all','partial','none') DEFAULT 'all',
  `mon` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_parcel definition

CREATE TABLE `chaitalk_parcel` (
  `id` varchar(30) DEFAULT NULL COMMENT '지사 아이디',
  `name` varchar(45) DEFAULT NULL COMMENT '지사이름',
  `date` datetime DEFAULT current_timestamp(),
  `price` int(11) DEFAULT NULL,
  UNIQUE KEY `unique_con_parcel` (`id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_porlist definition

CREATE TABLE `chaitalk_porlist` (
  `por_id` varchar(32) NOT NULL,
  `id` varchar(30) NOT NULL,
  `order` varchar(30) DEFAULT NULL,
  `addr` varchar(128) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `por_list` text NOT NULL,
  `rdate` datetime NOT NULL DEFAULT current_timestamp(),
  `confirm` int(1) DEFAULT 0 COMMENT '0 -  물품준비 10- 배송중 1 - 배송완료  2 - 입금완료  99 - 환불처리 999-환불완료',
  `pdfname` varchar(45) NOT NULL,
  `zip` varchar(10) NOT NULL,
  `invoice` varchar(30) DEFAULT NULL COMMENT '송장번호',
  `refund` varchar(64) DEFAULT NULL COMMENT '환불금액',
  PRIMARY KEY (`por_id`),
  KEY `idx_por_id` (`por_id`),
  KEY `idx_id` (`id`),
  KEY `idx_rdate` (`rdate`),
  KEY `idx_confirm` (`confirm`),
  KEY `idx_id_rdate` (`id`,`rdate`),
  KEY `idx_por_id_confirm` (`por_id`,`confirm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_price definition

CREATE TABLE `chaitalk_price` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `price` longtext DEFAULT NULL,
  `rdate` date NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_repository definition

CREATE TABLE `chaitalk_repository` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `id` varchar(20) NOT NULL,
  `contents` varchar(5000) NOT NULL,
  `rdate` datetime NOT NULL,
  `desc` varchar(512) DEFAULT NULL,
  `cate` varchar(24) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.chaitalk_user definition

CREATE TABLE `chaitalk_user` (
  `id` varchar(30) NOT NULL,
  `name` varchar(20) NOT NULL,
  `owner` varchar(30) DEFAULT NULL,
  `password` varchar(30) NOT NULL,
  `mobile` varchar(20) DEFAULT NULL,
  `p_mobile` varchar(20) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `addr` varchar(64) DEFAULT NULL,
  `zipcode` varchar(12) DEFAULT NULL,
  `role` int(2) DEFAULT NULL COMMENT '0:유치 1:지사장 2:선생 3:가맹 30:학원생 4:일반등록 7: 방과후9:어드민 ',
  `confirm` int(2) DEFAULT NULL COMMENT '계정 활성화 1  비활성화 0',
  `rdate` date NOT NULL DEFAULT current_timestamp(),
  `tid` varchar(30) DEFAULT NULL COMMENT '유치원/학원 선생님',
  `mid` varchar(30) DEFAULT NULL COMMENT '부모 지사장 아이디',
  `etc` varchar(20) DEFAULT NULL,
  `classnm` varchar(45) DEFAULT NULL COMMENT '유치원 및 학교이름',
  `step` varchar(20) DEFAULT NULL COMMENT '단계 및 학년',
  `status` int(2) NOT NULL DEFAULT 1 COMMENT '학원 아이디 1 등원/ 0 퇴원 ',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.repository definition

CREATE TABLE `repository` (
  `num` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(45) NOT NULL,
  `id` varchar(20) NOT NULL,
  `contents` varchar(5000) NOT NULL,
  `rdate` datetime NOT NULL,
  `desc` varchar(256) DEFAULT NULL,
  `cate` varchar(24) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=304 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.study_record definition

CREATE TABLE `study_record` (
  `id` varchar(20) NOT NULL,
  `step` varchar(3) NOT NULL,
  `volume` varchar(3) NOT NULL,
  `uid` varchar(7) NOT NULL,
  `rdate` datetime NOT NULL DEFAULT current_timestamp(),
  `no` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`no`),
  KEY `idx_study_record_id_rdate` (`id`,`rdate`),
  KEY `idx_study_record_id_uid_rdate` (`id`,`uid`,`rdate`)
) ENGINE=MyISAM AUTO_INCREMENT=32048 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_uca1400_ai_ci;


-- happyzip1.system_config definition

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


-- happyzip1.system_config_categories definition

CREATE TABLE `system_config_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_code` varchar(50) NOT NULL COMMENT '카테고리 코드',
  `category_name` varchar(100) NOT NULL COMMENT '카테고리 이름',
  `description` varchar(255) DEFAULT NULL COMMENT '카테고리 설명',
  `root_path` varchar(255) DEFAULT '../ebook_new_eplat/' COMMENT '이 카테고리 책들의 루트 경로',
  `is_default` tinyint(1) DEFAULT 0 COMMENT '기본 카테고리 여부 (1개만 true)',
  `display_order` int(11) DEFAULT 0 COMMENT '표시 순서',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='시스템 설정 카테고리';


-- happyzip1.books definition

CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `book_code` varchar(50) NOT NULL COMMENT '책 코드 (예: phonics.v1.1)',
  `type_code` varchar(50) NOT NULL COMMENT '타입 코드',
  `step` varchar(20) NOT NULL COMMENT '단계 (예: v1)',
  `volume` varchar(20) NOT NULL COMMENT '권수 (예: 1)',
  `title` varchar(200) DEFAULT NULL COMMENT '책 제목',
  `page_count` int(11) DEFAULT 0 COMMENT '페이지 수',
  `img_format` varchar(10) DEFAULT 'jpg' COMMENT '이미지 포맷',
  `sound_format` varchar(10) DEFAULT '.mp3' COMMENT '사운드 포맷',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '활성화 여부',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `book_code` (`book_code`),
  KEY `type_code` (`type_code`),
  CONSTRAINT `books_ibfk_1` FOREIGN KEY (`type_code`) REFERENCES `book_types` (`type_code`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci COMMENT='책 정보';