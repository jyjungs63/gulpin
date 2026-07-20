# EPLAT Ebook - 운영 서버 배포 시나리오

> 대상 서버: www.eplat.co.kr  
> 작업일: 2026-04-09  
> 담당: William Jung

---

## 사전 확인 사항

```
[ ] 운영 서버 SSH / FTP 접속 정보 확인
[ ] DB 접속 정보 확인 (happyzip DB)
[ ] 배포 전 현재 운영 소스 백업 완료
[ ] 배포 전 DB 백업 완료
[ ] 점검 시간 확보 (예상 소요: 약 15~20분)
```

---

## STEP 1. 운영 DB 백업

> ⚠️ **반드시 먼저 실행** — 롤백 시 필요

```bash
# SSH 접속 후 실행
mysqldump -u happyzip -p happyzip \
  book_types system_config \
  > backup_$(date +%Y%m%d_%H%M%S).sql

# 백업 파일 확인
ls -lh backup_*.sql
```

또는 phpMyAdmin → 데이터베이스 선택 → 내보내기 → `book_types`, `system_config` 테이블 선택 후 다운로드

---

## STEP 2. 소스 파일 배포

### 2-1. 신규 파일 업로드

아래 파일을 서버의 `/ebooks/` 디렉토리에 업로드:

| 로컬 경로 | 서버 경로 | 비고 |
|-----------|-----------|------|
| `ebooks/scan_folder.php` | `/ebooks/scan_folder.php` | 신규 |
| `ebooks/api_categories.php` | `/ebooks/api_categories.php` | 신규 |
| `ebooks/database_migration_v2.sql` | `/ebooks/database_migration_v2.sql` | 신규 |

### 2-2. 수정 파일 업로드

> 기존 파일을 **덮어쓰기** 전 서버에서 원본 백업 권장

```bash
# 서버에서 원본 백업 (SSH)
cd /path/to/ebooks
cp book-config.php        book-config.php.bak
cp ebook-config.js        ebook-config.js.bak
cp ebook.js               ebook.js.bak
cp admin.php              admin.php.bak
cp admin.js               admin.js.bak
cp api_book_types.php     api_book_types.php.bak
cp api_system_config.php  api_system_config.php.bak
cp get_display_mode.php   get_display_mode.php.bak
```

업로드할 수정 파일 목록:

| 로컬 경로 | 서버 경로 |
|-----------|-----------|
| `ebooks/book-config.php` | `/ebooks/book-config.php` |
| `ebooks/ebook-config.js` | `/ebooks/ebook-config.js` |
| `ebooks/ebook.js` | `/ebooks/ebook.js` |
| `ebooks/admin.php` | `/ebooks/admin.php` |
| `ebooks/admin.js` | `/ebooks/admin.js` |
| `ebooks/api_book_types.php` | `/ebooks/api_book_types.php` |
| `ebooks/api_system_config.php` | `/ebooks/api_system_config.php` |
| `ebooks/get_display_mode.php` | `/ebooks/get_display_mode.php` |
| `purchase/kgardenstatus.php` | `/purchase/kgardenstatus.php` |

---

## STEP 3. DB 마이그레이션 실행

> ⚠️ **한 번만 실행** — 중복 실행 시 오류 발생 가능 (IF NOT EXISTS 조건 있음)

### 방법 A — phpMyAdmin (권장)

1. phpMyAdmin 접속
2. 좌측에서 `happyzip` 데이터베이스 선택
3. 상단 `SQL` 탭 클릭
4. `database_migration_v2.sql` 파일 내용 붙여넣기
5. `실행` 클릭
6. 결과 메시지 확인:
   ```
   Migration v2 완료: system_config_categories 생성 및 category_code 컬럼 추가
   ```

### 방법 B — SSH 커맨드

```bash
# SSH 접속 후
mysql -u happyzip -p happyzip < /path/to/ebooks/database_migration_v2.sql

# 실행 결과 확인
mysql -u happyzip -p happyzip -e "
  SELECT '=== system_config_categories ===' AS info;
  SELECT category_code, category_name, root_path, is_default FROM system_config_categories;
  SELECT '=== system_config (category_code 확인) ===' AS info;
  SELECT config_key, category_code FROM system_config LIMIT 10;
  SELECT '=== book_types (category_code 확인) ===' AS info;
  SELECT type_code, display_mode, category_code FROM book_types;
"
```

### 마이그레이션 실행 결과 확인

```sql
-- phpMyAdmin SQL 탭에서 실행
SELECT * FROM system_config_categories;
-- → 'general' 카테고리 1건 있어야 함

SELECT config_key, category_code FROM system_config;
-- → 모든 항목의 category_code = 'general' 이어야 함

SHOW COLUMNS FROM book_types;
-- → category_code 컬럼 존재해야 함
```

---

## STEP 4. 배포 후 동작 확인

### 4-1. Admin 페이지 확인

```
https://www.eplat.co.kr/ebooks/admin.php
```

체크리스트:
```
[ ] 탭 4개 정상 표시 (책 타입 / 카테고리 / 시스템 설정 / 내보내기)
[ ] 카테고리 탭 → 'general (기본 설정)' 1건 표시
[ ] 시스템 설정 탭 → 카테고리 그룹 헤더로 묶여 표시
[ ] 책 타입 탭 → '카테고리' 컬럼 표시 (순서 컬럼 대체)
[ ] 책 타입 추가 모달 → 카테고리 드롭다운 표시
```

### 4-2. Ebook 뷰어 동작 확인

```
https://www.eplat.co.kr/ebooks/?book=phonics.step3.v1
```

체크리스트:
```
[ ] 책 정상 로드
[ ] 브라우저 콘솔(F12) 에러 없음
[ ] [scanFolder] 또는 [detectByFetch] 로그에서 이미지 포맷 감지 확인
[ ] 오디오 파일 있으면 플레이어 활성화 확인
[ ] 콘솔에서 ROOT_PATH_MAP 출력 확인
```

기대 콘솔 로그:
```
✅ 책 설정 로드 완료 (카테고리 ROOT_PATH_MAP 포함)
📚 ROOT_PATH_MAP: { phonics: "https://...", story: "https://..." }
[scanFolder] 또는 [detectByFetch] 이미지: N개 (png), 오디오: mp3
```

### 4-3. scan_folder.php 직접 테스트

```
https://www.eplat.co.kr/ebooks/scan_folder.php?path=https://www.eplat.co.kr/ebook_new_eplat/bookshelf-1/2-1_phonics/step3/v1/
```

기대 응답:
```json
{
  "image_count": 24,
  "image_format": "png",
  "audio_format": "mp3",
  "path": "/실제/서버/경로/..."
}
```

응답에 `_debug` 필드가 있고 경로 오류가 나면 → STEP 5 참고

---

## STEP 5. scan_folder.php 경로 문제 발생 시

### 서버 DOCUMENT_ROOT 확인

```php
<!-- 임시 test_path.php 파일 서버에 업로드 후 접속 -->
<?php
echo json_encode([
    'DOCUMENT_ROOT'   => $_SERVER['DOCUMENT_ROOT'],
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'],
    'is_dir_test'     => is_dir($_SERVER['DOCUMENT_ROOT'] . '/ebook_new_eplat/'),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
```

확인 후 `scan_folder.php` 경로 변환 로직의 `DOCUMENT_ROOT` 경로 보정:

```php
// scan_folder.php 상단 URL 변환 블록에서 경로 수동 지정
$docRoot = '/실제/서버/경로';   // DOCUMENT_ROOT 값으로 교체
```

확인 후 `test_path.php` 삭제

---

## STEP 6. 롤백 절차 (문제 발생 시)

### 소스 롤백

```bash
# 서버에서 백업 파일 복원 (SSH)
cd /path/to/ebooks
cp book-config.php.bak    book-config.php
cp ebook-config.js.bak    ebook-config.js
cp ebook.js.bak           ebook.js
cp admin.php.bak          admin.php
cp admin.js.bak           admin.js
cp api_book_types.php.bak api_book_types.php
cp api_system_config.php.bak api_system_config.php
cp get_display_mode.php.bak  get_display_mode.php

# 신규 파일 제거
rm scan_folder.php api_categories.php
```

### DB 롤백

```sql
-- phpMyAdmin SQL 탭에서 실행
DROP TABLE IF EXISTS system_config_categories;
ALTER TABLE system_config  DROP COLUMN IF EXISTS category_code;
ALTER TABLE book_types     DROP COLUMN IF EXISTS category_code;
```

그 후 STEP 1에서 생성한 백업 SQL 임포트

---

## 배포 완료 체크리스트

```
[ ] STEP 1: DB 백업 완료
[ ] STEP 2: 소스 파일 업로드 완료 (신규 3개 + 수정 9개)
[ ] STEP 3: DB 마이그레이션 실행 완료
[ ] STEP 4-1: Admin 페이지 정상 동작 확인
[ ] STEP 4-2: Ebook 뷰어 정상 동작 확인
[ ] STEP 4-3: scan_folder.php 응답 정상 확인
[ ] .bak 파일 정리 (안정화 후 삭제)
```

---

## 문의 / 이슈

- 브라우저 콘솔(F12 → Console) 에러 로그 캡처 후 공유
- `scan_folder.php` 응답의 `_debug` 값 확인
- DB 마이그레이션 오류 시 phpMyAdmin 에러 메시지 전달
