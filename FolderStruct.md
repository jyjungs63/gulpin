# 교육 프로그램 자료 폴더 표준안

이 문서는 교육 프로그램 자료의 **목표 폴더 구조와 명명 규칙**을 정의합니다. 현재 서버의 파일을 즉시 이동한다는 의미가 아니며, 신규 자료와 기존 자료 마이그레이션 시 적용할 기준입니다.

## 1. 설계 원칙

모든 프로그램은 다음과 같은 **4단계 고정 계층**을 사용합니다.

```text
프로그램(program) → 단계(level) → 단원(unit) → 미디어 유형
```

- 폴더와 파일 이름은 영문 소문자 `kebab-case`를 사용합니다.
- 순서가 있는 폴더와 파일에는 정렬이 보장되도록 두 자리 번호를 사용합니다.
- `img/video`처럼 실제 파일 유형과 무관한 중간 폴더는 만들지 않습니다.
- 영상, 이미지, 음원, 문서는 각각 `videos`, `images`, `audio`, `docs`에 저장합니다.
- 단계 구분이 없는 경우에도 `level-01`을 사용해 폴더 깊이를 유지합니다.
- 독립적으로 운영되는 세부 과정은 프로그램 폴더로 분리합니다.
- 한 파일은 한 위치에만 저장합니다. 여러 프로그램이 함께 쓰는 자료만 `shared`에 둡니다.

## 2. 공통 구조

```text
assets/
└── gulpin/
    ├── programs/
    │   └── {program}/
    │       └── level-{nn}/
    │           └── unit-{nn}/
    │               ├── videos/
    │               ├── images/
    │               ├── audio/
    │               └── docs/
    └── shared/
        ├── images/
        ├── audio/
        ├── fonts/
        └── docs/
```

비어 있는 미디어 폴더는 미리 만들지 않습니다. 예를 들어 영상만 있는 단원은 `videos`만 생성합니다.

## 3. 프로그램별 목표 구조

반복되는 단계와 단원은 첫 항목만 상세히 표시하고 범위를 주석으로 명시합니다.

### 3.1 그림으로 배우는 한자파닉스 (`hj-phonics`)

기존 `book1`~`book4`를 `level-01`~`level-04`로 통일합니다. 각 권에 별도 단원 구분이 없으므로 `unit-01`을 사용합니다.

```text
programs/hj-phonics/
├── level-01/
│   └── unit-01/videos/
│       ├── game-01.mp4
│       ├── hanja-song.mp4
│       └── voca-song.mp4
├── level-02/                         # 동일 구조
├── level-03/                         # 동일 구조
└── level-04/                         # 게임 영상이 없으면 두 노래만 저장
```

### 3.2 어휘잼 (`word-jam`)

7개 단계와 단계별 4개 단원을 같은 규칙으로 배치합니다.

```text
programs/word-jam/
├── level-01/
│   ├── unit-01/
│   │   └── videos/
│   │       ├── study-01.mp4
│   │       ├── study-02.mp4
│   │       └── study-03.mp4
│   ├── unit-02/                      # 동일 구조
│   ├── unit-03/                      # 동일 구조
│   └── unit-04/                      # 동일 구조
├── level-02/                         # 동일 구조
├── level-03/
├── level-04/
├── level-05/
├── level-06/
└── level-07/
```

### 3.3 급수파자한자 (`gs-paja`)

2개 단계와 단계별 5개 단원을 같은 규칙으로 배치합니다.

```text
programs/gs-paja/
├── level-01/
│   ├── unit-01/
│   │   └── videos/
│   │       ├── game-01.mp4
│   │       ├── game-02.mp4
│   │       ├── hanja-song.mp4
│   │       ├── study-01.mp4
│   │       └── voca-song.mp4
│   ├── unit-02/                      # 동일 구조
│   ├── unit-03/
│   ├── unit-04/
│   └── unit-05/
└── level-02/                         # 동일 구조
```

### 3.4 어휘력의 신 / 어휘담 (`voca-genius`)

파일명에 포함된 `1g`, `2g` 등의 단원 정보는 폴더 경로와 중복되므로 제거합니다. `알아보기` 자료도 별도 예외 이름 대신 순서에 맞는 단원 번호를 사용합니다.

```text
programs/voca-genius/
├── level-01/
│   ├── unit-01/
│   │   └── videos/
│   │       ├── song.mp4
│   │       ├── study.mp4
│   │       └── voca.mp4
│   ├── unit-02/                      # 동일 구조
│   ├── unit-03/                      # 동일 구조
│   ├── unit-04/                      # 알아보기
│   │   └── videos/
│   │       ├── song-01.mp4
│   │       ├── song-02.mp4
│   │       ├── song-03.mp4
│   │       └── study.mp4
│   └── unit-05/
└── level-02/
    ├── unit-01/
    ├── unit-02/
    ├── unit-03/
    ├── unit-04/
    ├── unit-05/
    └── unit-06/
```

### 3.5 지구팡의 대모험 (`ep-gjam`, `ep-jjam`, `ep-mjam`)

글잼, 잼잼, 말잼은 각각 독립 프로그램 폴더로 분리합니다. 기존 `1ho`~`10ho`는 공통 단계 이름인 `level-01`~`level-10`으로 통일합니다.

```text
programs/
├── ep-gjam/
│   ├── level-01/
│   │   └── unit-01/videos/
│   │       ├── game-01.mp4
│   │       ├── game-02.mp4
│   │       ├── hanja-song.mp4
│   │       ├── review.mp4
│   │       ├── study-01.mp4
│   │       ├── study-02.mp4
│   │       ├── study-03.mp4
│   │       └── voca-song.mp4
│   ├── level-02/                     # 동일 구조
│   └── level-10/                     # 동일 구조
├── ep-jjam/
│   ├── level-01/                     # 동일 구조
│   └── level-10/
└── ep-mjam/
    ├── level-01/                     # 동일 구조
    └── level-10/
```

## 4. 명명 규칙

| 대상 | 규칙 | 예시 |
|---|---|---|
| 프로그램 | 의미가 드러나는 영문 `kebab-case` | `word-jam`, `ep-gjam` |
| 단계 | `level-{두 자리 번호}` | `level-01`, `level-10` |
| 단원 | `unit-{두 자리 번호}` | `unit-01`, `unit-06` |
| 순번 파일 | `{역할}-{두 자리 번호}.{확장자}` | `study-01.mp4` |
| 단일 파일 | `{역할}.{확장자}` | `review.mp4`, `song.mp4` |
| 미디어 폴더 | 짧은 영문 소문자 | `videos`, `images`, `audio`, `docs` |

파일 역할은 다음 표준 이름을 우선 사용합니다.

| 축약어 | 의미 |
|---|---|
| `hj` | `hanja`(한자) |
| `gs` | `grade-system`(급수) |
| `voca` | `vocabulary`(어휘) |
| `ep` | `earth-pang`(지구팡) |
| `gjam` / `jjam` / `mjam` | 글잼 / 잼잼 / 말잼 |
| `docs` | `documents`(문서) |

| 기존 이름 | 표준 이름 |
|---|---|
| `hj_song`, `hanja_song` | `hanja-song` |
| `vaca_song`, `voca_song` | `voca-song` |
| `vocabulary` | `voca` |
| `game1`, `game2` | `game-01`, `game-02` |
| `study1`, `study2` | `study-01`, `study-02` |

## 5. 기존 경로 대응표

| 기존 경로 | 목표 경로 |
|---|---|
| `gulpin/hj_phonics/book1` | `gulpin/programs/hj-phonics/level-01/unit-01/videos` |
| `gulpin/word_jam/img/video/1-level/unit_a` | `gulpin/programs/word-jam/level-01/unit-01/videos` |
| `gulpin/gs_phaja/img/video/1-level/unit_a` | `gulpin/programs/gs-paja/level-01/unit-01/videos` |
| `gulpin/voca_genius/img/video/1-level/unit_a` | `gulpin/programs/voca-genius/level-01/unit-01/videos` |
| `gulpin/jj-mj-gjam/gjam/1ho` | `gulpin/programs/ep-gjam/level-01/unit-01/videos` |
| `gulpin/jj-mj-gjam/jjam/1ho` | `gulpin/programs/ep-jjam/level-01/unit-01/videos` |
| `gulpin/jj-mj-gjam/mjam/1ho` | `gulpin/programs/ep-mjam/level-01/unit-01/videos` |

## 6. 적용 순서

1. 신규 자료부터 이 표준 구조와 명명 규칙을 적용합니다.
2. 기존 경로를 참조하는 애플리케이션과 데이터베이스 항목을 조사합니다.
3. 프로그램 단위로 파일을 복사하고 파일 수와 체크섬을 비교합니다.
4. 참조 경로를 목표 경로로 변경한 뒤 영상 재생을 확인합니다.
5. 일정 기간 이전 경로를 호환 경로로 유지한 후, 참조가 없음을 확인하고 제거합니다.

> 실제 파일 이동 전에는 대소문자 차이, 누락 파일, 오탈자(예: `song`/`ong`)를 별도 목록으로 확정해야 합니다.
