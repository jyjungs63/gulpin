# 📚 EPLAT Ebook - 데이터베이스 설정 시스템 설치 가이드

## 🎯 개요

책 설정을 데이터베이스에 저장하고 웹 인터페이스로 관리하는 시스템입니다.

## 📁 파일 구조

```
project/
├── database_schema.sql          # 데이터베이스 스키마
├── db_config.php                # DB 연결 설정
├── api_book_types.php           # 책 타입 API
├── api_system_config.php        # 시스템 설정 API
├── generate_book_config.php     # JS 파일 생성 API
├── admin.html                   # 관리 페이지
└── admin.js                     # 관리 페이지 JavaScript
```

## 🚀 설치 방법

### 1단계: 데이터베이스 생성

#### MySQL/MariaDB 사용 시

```bash
# MySQL 접속
mysql -u root -p

# SQL 파일 실행
source database_schema.sql;
```

또는 phpMyAdmin 사용:
1. phpMyAdmin 접속
2. "Import" 탭 선택
3. `database_schema.sql` 파일 업로드
4. "Go" 버튼 클릭

### 2단계: 데이터베이스 연결 설정

`db_config.php` 파일을 열어 데이터베이스 정보 수정:

```php
// 데이터베이스 설정
define('DB_HOST', 'localhost');      // 호스트
define('DB_NAME', 'eplat_ebook');    // 데이터베이스 이름
define('DB_USER', 'root');           // 사용자명
define('DB_PASS', '');               // 비밀번호
define('DB_CHARSET', 'utf8mb4');     // 문자셋
```

### 3단계: 파일 업로드

모든 파일을 웹 서버 디렉토리에 업로드:

```bash
# 예: Apache
/var/www/html/ebook/

# 예: XAMPP
C:/xampp/htdocs/ebook/
```

### 4단계: 권한 설정 (Linux/Mac)

```bash
# PHP 파일 실행 권한
chmod 644 *.php

# 디렉토리 권한
chmod 755 .
```

### 5단계: 관리 페이지 접속

브라우저에서 다음 주소로 접속:

```
http://localhost/ebook/admin.html
```

## 📊 데이터베이스 구조

### 1. book_types 테이블

책의 타입 정보를 저장합니다.

| 필드 | 타입 | 설명 |
|------|------|------|
| id | INT | 고유 ID (자동 증가) |
| type_code | VARCHAR(50) | 타입 코드 (예: phonics) |
| type_name | VARCHAR(100) | 타입 한글명 (예: 파닉스) |
| directory_path | VARCHAR(255) | 디렉토리 경로 |
| is_active | TINYINT(1) | 활성화 여부 (0/1) |
| display_order | INT | 표시 순서 |
| created_at | TIMESTAMP | 생성 시간 |
| updated_at | TIMESTAMP | 수정 시간 |

### 2. system_config 테이블

시스템 전역 설정을 저장합니다.

| 필드 | 타입 | 설명 |
|------|------|------|
| id | INT | 고유 ID (자동 증가) |
| config_key | VARCHAR(100) | 설정 키 (예: ROOT_PATH) |
| config_value | TEXT | 설정 값 |
| config_type | VARCHAR(20) | 데이터 타입 |
| description | VARCHAR(255) | 설명 |
| is_editable | TINYINT(1) | 편집 가능 여부 |
| created_at | TIMESTAMP | 생성 시간 |
| updated_at | TIMESTAMP | 수정 시간 |

### 3. books 테이블 (선택사항)

개별 책 정보를 저장합니다.

| 필드 | 타입 | 설명 |
|------|------|------|
| id | INT | 고유 ID |
| book_code | VARCHAR(50) | 책 코드 (예: phonics.v1.1) |
| type_code | VARCHAR(50) | 타입 코드 (FK) |
| step | VARCHAR(20) | 단계 (예: v1) |
| volume | VARCHAR(20) | 권수 (예: 1) |
| title | VARCHAR(200) | 책 제목 |
| page_count | INT | 페이지 수 |
| img_format | VARCHAR(10) | 이미지 포맷 |
| sound_format | VARCHAR(10) | 사운드 포맷 |
| is_active | TINYINT(1) | 활성화 여부 |

## 🔧 API 엔드포인트

### 책 타입 API (`api_book_types.php`)

#### GET - 목록 조회
```
GET /api_book_types.php
GET /api_book_types.php?id=1
GET /api_book_types.php?active_only=true
```

#### POST - 추가
```json
POST /api_book_types.php
Content-Type: application/json

{
  "type_code": "new_type",
  "type_name": "새 타입",
  "directory_path": "bookshelf-3/new/",
  "is_active": 1,
  "display_order": 10
}
```

#### PUT - 수정
```json
PUT /api_book_types.php
Content-Type: application/json

{
  "id": 1,
  "type_name": "수정된 이름"
}
```

#### DELETE - 삭제
```
DELETE /api_book_types.php?id=1
```

### 시스템 설정 API (`api_system_config.php`)

#### GET - 조회
```
GET /api_system_config.php
GET /api_system_config.php?key=ROOT_PATH
```

#### POST - 추가/수정
```json
POST /api_system_config.php
Content-Type: application/json

{
  "config_key": "ROOT_PATH",
  "config_value": "../new_path/",
  "config_type": "string",
  "description": "루트 경로"
}
```

### Config 생성 API (`generate_book_config.php`)

```
GET /generate_book_config.php
```

JavaScript 파일로 출력됩니다.

## 💻 사용 방법

### 1. 관리 페이지 사용

#### 책 타입 추가
1. "책 타입 관리" 탭 클릭
2. "➕ 새 책 타입 추가" 버튼 클릭
3. 정보 입력:
   - 타입 코드: `phonics` (영문, 소문자)
   - 타입 이름: `파닉스` (한글)
   - 디렉토리 경로: `bookshelf-1/2-1_phonics/`
   - 표시 순서: `1` (숫자, 작을수록 앞)
   - 활성화: 체크
4. "저장" 버튼 클릭

#### 책 타입 수정
1. 수정할 항목의 "✏️ 수정" 버튼 클릭
2. 정보 수정
3. "저장" 버튼 클릭

#### 책 타입 삭제
1. 삭제할 항목의 "🗑️ 삭제" 버튼 클릭
2. 확인 대화상자에서 "확인" 클릭

#### 시스템 설정 수정
1. "시스템 설정" 탭 클릭
2. 수정할 항목의 "✏️ 수정" 버튼 클릭
3. 값 수정
4. "저장" 버튼 클릭

#### Config 파일 생성
1. "설정 내보내기" 탭 클릭
2. "📄 config 파일 생성" 버튼 클릭
3. 미리보기 확인
4. "💾 파일 다운로드" 버튼으로 다운로드

### 2. 프론트엔드에서 사용

#### book-config.js 파일 교체

1. `generate_book_config.php`에서 생성된 파일 다운로드
2. 기존 `book-config.js` 파일 백업
3. 새 파일로 교체

#### 자동 업데이트 (권장)

HTML에서 직접 PHP 파일 사용:

```html
<!-- 기존 -->
<script src="book-config.js"></script>

<!-- 변경 후 -->
<script src="generate_book_config.php"></script>
```

이렇게 하면 항상 최신 설정을 사용합니다!

## 🔐 보안 설정

### 1. 관리 페이지 접근 제한

`.htaccess` 파일 생성 (Apache):

```apache
# admin.html 접근 제한
<Files "admin.html">
    AuthType Basic
    AuthName "Admin Area"
    AuthUserFile /path/to/.htpasswd
    Require valid-user
</Files>
```

`.htpasswd` 파일 생성:

```bash
htpasswd -c .htpasswd admin
# 비밀번호 입력
```

### 2. API 접근 제한

`api_*.php` 파일 상단에 추가:

```php
<?php
// 세션 체크
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    die('Unauthorized');
}
?>
```

### 3. SQL 인젝션 방지

이미 PDO prepared statements 사용 중 ✅

### 4. XSS 방지

`htmlspecialchars()` 함수 사용 중 ✅

## 🐛 문제 해결

### 데이터베이스 연결 오류

```
Error: SQLSTATE[HY000] [1045] Access denied
```

**해결:**
1. `db_config.php`의 DB 정보 확인
2. MySQL 사용자 권한 확인:
   ```sql
   GRANT ALL PRIVILEGES ON eplat_ebook.* TO 'root'@'localhost';
   FLUSH PRIVILEGES;
   ```

### 테이블이 없다는 오류

```
Error: Table 'eplat_ebook.book_types' doesn't exist
```

**해결:**
1. `database_schema.sql` 파일 다시 실행
2. phpMyAdmin에서 테이블 확인

### CORS 오류

```
Access to fetch at '...' from origin '...' has been blocked
```

**해결:**
PHP 파일 상단에 추가:
```php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
```

### 한글 깨짐

**해결:**
1. 데이터베이스 문자셋 확인:
   ```sql
   ALTER DATABASE eplat_ebook CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. PHP 파일 인코딩 UTF-8로 저장

## 📈 확장 기능

### 1. 책 정보 테이블 활용

```sql
-- 책 추가
INSERT INTO books (book_code, type_code, step, volume, title, page_count) 
VALUES ('phonics.v1.1', 'phonics', 'v1', '1', 'Phonics Volume 1', 20);

-- 책 조회
SELECT b.*, bt.type_name 
FROM books b
JOIN book_types bt ON b.type_code = bt.type_code
WHERE b.is_active = 1;
```

### 2. 동영상 링크 관리

```sql
-- 동영상 링크 추가
INSERT INTO video_links (book_id, page_number, video_url) 
VALUES (1, 5, 'https://youtube.com/watch?v=....');

-- 책의 동영상 링크 조회
SELECT page_number, video_url 
FROM video_links 
WHERE book_id = 1;
```

### 3. 로그 테이블 추가

```sql
CREATE TABLE admin_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    action VARCHAR(50),
    table_name VARCHAR(50),
    record_id INT,
    user_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## ✅ 체크리스트

설치 완료 후 확인:

- [ ] 데이터베이스 생성 확인
- [ ] 테이블 5개 생성 확인 (book_types, system_config, books, video_links, v_book_config)
- [ ] 초기 데이터 삽입 확인
- [ ] `db_config.php` 설정 확인
- [ ] 관리 페이지 접속 확인 (admin.html)
- [ ] 책 타입 목록 표시 확인
- [ ] 책 타입 추가/수정/삭제 테스트
- [ ] 시스템 설정 수정 테스트
- [ ] Config 파일 생성 테스트
- [ ] 다운로드 기능 테스트
- [ ] 프론트엔드 연동 테스트

## 🎓 추가 학습 자료

- [PDO Tutorial](https://www.php.net/manual/en/book.pdo.php)
- [MySQL Best Practices](https://dev.mysql.com/doc/refman/8.0/en/sql-syntax.html)
- [REST API Design](https://restfulapi.net/)
- [Web Security Guide](https://owasp.org/www-project-web-security-testing-guide/)

## 📞 지원

문제가 있으면 다음을 확인하세요:
1. PHP 에러 로그: `/var/log/apache2/error.log`
2. MySQL 에러 로그: `/var/log/mysql/error.log`
3. 브라우저 콘솔 (F12)

완료! 데이터베이스 기반 설정 시스템이 준비되었습니다! 🎉
