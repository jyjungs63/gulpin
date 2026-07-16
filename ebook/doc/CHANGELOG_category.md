# EPLAT Ebook - 카테고리 관리 기능 추가

> 작업일: 2026-04-09  
> 작업자: William Jung

---

## 개요

시스템 설정을 **카테고리 단위로 관리**하고, 각 카테고리마다 독립적인 **루트 경로(root_path)** 를 지정할 수 있도록 기능을 확장했습니다.  
책 타입(book_types)이 카테고리를 선택하면 해당 카테고리의 루트 경로가 자동으로 적용됩니다.

---

## 배경 / 목적

| 기존 문제 | 개선 내용 |
|-----------|-----------|
| 시스템 설정 항목이 한 묶음으로 나열됨 | 카테고리별 그룹 표시 |
| 모든 책 타입이 하나의 ROOT_PATH 공유 | 카테고리별 독립 ROOT_PATH 지정 가능 |
| 책 타입 모달에 `표시 순서`만 있었음 | `카테고리 선택` 드롭다운으로 변경 |
| default 카테고리 개념 없음 | 카테고리 중 하나를 기본(default)으로 지정 |

---

## DB 변경사항

### 신규 테이블 — `system_config_categories`

```sql
CREATE TABLE system_config_categories (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    category_code VARCHAR(50)  NOT NULL UNIQUE,   -- 카테고리 코드 (예: general, premium)
    category_name VARCHAR(100) NOT NULL,           -- 카테고리 이름 (예: 기본 설정)
    description   VARCHAR(255),                   -- 설명
    root_path     VARCHAR(255),                   -- 이 카테고리 책들의 루트 경로
    is_default    TINYINT(1)   DEFAULT 0,          -- 기본 카테고리 여부 (1개만 true)
    display_order INT          DEFAULT 0,
    created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

### 기존 테이블 컬럼 추가

| 테이블 | 추가 컬럼 | 설명 |
|--------|-----------|------|
| `system_config` | `category_code VARCHAR(50) DEFAULT 'general'` | 어느 카테고리 소속인지 |
| `book_types` | `category_code VARCHAR(50) NULL` | 사용할 카테고리 (NULL = 기본 카테고리 자동 사용) |

### 마이그레이션 스크립트

```
ebooks/database_migration_v2.sql
```

- 기존 `system_config` 데이터는 자동으로 `general` 카테고리에 할당
- 기존 `book_types` 데이터는 `category_code = NULL` (기본 카테고리 사용)
- **한 번만 실행**하면 됨

---

## 신규 파일

### `api_categories.php`

카테고리 CRUD REST API

| 메서드 | 파라미터 | 기능 |
|--------|----------|------|
| GET    | _(없음)_  | 전체 카테고리 목록 |
| GET    | `?code=general` | 단일 카테고리 조회 |
| POST   | JSON body | 카테고리 추가 |
| PUT    | JSON body | 카테고리 수정 |
| PUT    | `{ set_default: true, category_code }` | 기본 카테고리 지정 |
| DELETE | `?code=general` | 카테고리 삭제 (기본 카테고리·참조 중인 항목 삭제 불가) |

**POST/PUT body 예시:**
```json
{
  "category_code":  "premium",
  "category_name":  "프리미엄",
  "root_path":      "../ebook_premium/",
  "description":    "프리미엄 콘텐츠",
  "is_default":     0,
  "display_order":  1
}
```

---

## 수정 파일

### `api_system_config.php`

- GET: `?category=general` 필터 지원, 응답에 `category_name` 포함
- POST/PUT: `category_code` 필드 저장 가능
- **DELETE 메서드 신규 추가** (`?key=ROOT_PATH`)

### `api_book_types.php`

- GET: `category_code`, `effective_category_code`, `effective_category_name` 반환
  - `effective_*` = 지정 카테고리가 없으면 기본 카테고리 값으로 COALESCE
- POST: `category_code` 저장
- PUT: `category_code` 수정 가능 (allowedFields 에 추가)

### `book-config.php` ⭐ 핵심 변경

카테고리별 `root_path` 기반의 **ROOT_PATH_MAP** 생성 후 JS에 출력

```js
// 출력 예시
const BOOK_CONFIG = {
    ROOT_PATH: '../ebook_new_eplat/',   // 기본 카테고리 루트
    ROOT_PATH_MAP: {
        "story":       "../ebook_new_eplat/",
        "phonics":     "../ebook_new_eplat/",
        "premium_book":"../ebook_premium/"   // 카테고리 = premium
    },
    DIRECTORY_MAP: { ... },
    VALID_TYPES: [...],
    DEFAULT_BOOK: '...'
};
```

`parseBookParameter()` 와 `createBookDirectory()` 가 `ROOT_PATH_MAP[type]` 을 우선 사용하도록 수정:

```js
// 변경 전
const directory = `${BOOK_CONFIG.ROOT_PATH}${BOOK_CONFIG.DIRECTORY_MAP[type]}${step}/${vol}/`;

// 변경 후
const rootPath = BOOK_CONFIG.ROOT_PATH_MAP[type] || BOOK_CONFIG.ROOT_PATH;
const directory = `${rootPath}${BOOK_CONFIG.DIRECTORY_MAP[type]}${step}/${vol}/`;
```

### `get_display_mode.php`

응답에 카테고리 정보 추가:

```json
{
  "success": true,
  "data": {
    "display_mode":            "double",
    "category_code":           "premium",
    "effective_category_code": "premium",
    "effective_category_name": "프리미엄",
    "effective_root_path":     "../ebook_premium/",
    "type_code":               "phonics"
  }
}
```

### `admin.php`

- 탭 구성 변경: 3개 → **4개**
  - 📖 책 타입 관리
  - 🗂️ **카테고리 관리** ← 신규
  - ⚙️ 시스템 설정
  - 📄 설정 내보내기
- **카테고리 모달** 신규 추가 (추가/수정 공용)
- 책 타입 모달: `표시 순서` 단독 → **카테고리 선택** 드롭다운 + 표시 순서
- 시스템 설정 모달: **카테고리 선택** 드롭다운 추가

### `admin.js`

| 함수 | 설명 |
|------|------|
| `loadCategories()` | 카테고리 목록 로드 |
| `renderCategoriesTable(data)` | 카테고리 테이블 렌더링 |
| `openCategoryModal(code)` | 카테고리 추가/수정 모달 열기 |
| `closeCategoryModal()` | 카테고리 모달 닫기 |
| `saveCategory()` | 카테고리 저장 (POST/PUT) |
| `setDefaultCategory(code)` | 기본 카테고리 지정 |
| `deleteCategory(code)` | 카테고리 삭제 |
| `_populateCategoryDropdowns()` | 책 타입·시스템 설정 모달 드롭다운 동기화 |
| `renderBookTypesTable(data)` | 순서 컬럼 → **카테고리 배지** 표시로 변경 |
| `renderSystemConfigTable(data)` | **카테고리별 그룹 헤더** 로 묶어 표시 |
| `deleteSystemConfig(key)` | 시스템 설정 삭제 기능 추가 |

---

## 동작 흐름

```
[관리자]
  1. 카테고리 관리 탭 → 카테고리 추가
     예) category_code=premium, root_path=../ebook_premium/

  2. 책 타입 관리 탭 → 카테고리 선택
     예) phonics → 카테고리: premium

  3. book-config.php 가 ROOT_PATH_MAP 자동 생성
     ROOT_PATH_MAP = { phonics: '../ebook_premium/', ... }

[Ebook 뷰어]
  4. book-config.php 로드 (JavaScript)
  5. parseBookParameter('phonics.v1.1')
     → rootPath = ROOT_PATH_MAP['phonics'] = '../ebook_premium/'
     → directory = '../ebook_premium/bookshelf-1/2-1_phonics/v1/1/'
```

---

## 카테고리 관리 UI 주요 기능

| 기능 | 설명 |
|------|------|
| 추가 | 코드·이름·루트 경로·설명·순서 입력 후 저장 |
| 수정 | 코드 제외 모든 필드 수정 가능 |
| 기본 지정 | ⭐ 기본 카테고리 변경 (기존 기본은 자동 해제) |
| 삭제 | 기본 카테고리 삭제 불가 / 사용 중인 카테고리 삭제 불가 |

---

## 주의사항

1. **마이그레이션 먼저 실행** — `database_migration_v2.sql` 을 DB에서 실행 후 사용
2. **기본 카테고리는 1개** — 항상 1개의 카테고리에 `is_default=1` 이 유지되어야 함
3. **book_types.category_code = NULL** — 기본 카테고리 자동 적용 (의도된 동작)
4. `ADD COLUMN IF NOT EXISTS` 구문은 MariaDB 10.3+ 에서 지원

---

## 파일 목록

```
ebooks/
├── database_migration_v2.sql   ← 신규 (DB 마이그레이션)
├── api_categories.php          ← 신규 (카테고리 CRUD API)
├── api_system_config.php       ← 수정 (category 지원, DELETE 추가)
├── api_book_types.php          ← 수정 (category_code 추가)
├── book-config.php             ← 수정 (ROOT_PATH_MAP 생성)
├── get_display_mode.php        ← 수정 (category 정보 반환)
├── admin.php                   ← 수정 (카테고리 탭/모달 추가)
└── admin.js                    ← 수정 (카테고리 관리 JS 전면 개편)
```
