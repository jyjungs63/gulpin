# 게임 데이터 구조 통합 TODO

## 목표

`game/matching-game`, `game/memory-game`, `game/paza-game`이 동일한 데이터 카탈로그와 로더를 사용하게 한다. 게임 코드는 URL 파라미터 해석, 데이터 선택, 경로 조합, 파일명 접미사 추론을 직접 하지 않고, 로더가 반환한 정규화된 게임 세트만 소비한다.

## 현재 구조 분석

### matching-game

- `index.php`가 `items.js`를 전역 변수로 먼저 적재한 뒤 `game.js`를 실행한다.
- `items.js`는 `{ vol, step, path, cover, name[] }` 구조이며 `name[]`의 항목은 `{ imageName, textName }`이다.
- `game.js`가 `step`, `vol` 쿼리 파라미터를 읽지만, AlaSQL 조회는 `step`만 사용한다. 선택 결과의 `path` 뒤에 다시 `vol/step/`을 붙이며 `step=T`이면 `matching/`도 추가한다.
- 카드 이미지와 음성 경로를 `imageName + 확장자`, 커버 경로를 `path + cover` 방식으로 런타임에 조합한다.
- 매칭 판정은 드래그 항목의 `imageName`과 드롭 항목의 `textName`에 의존한다.

### memory-game

- 별도 데이터 파일이 없고 `game.js`/`game1.js` 안에 `PH-1.png`~`PH-8.png`, `PS-1.png`~`PS-8.png` 배열이 하드코딩되어 있다.
- `vol`, `clas`, `sor` 쿼리 파라미터와 조건문으로 데이터 종류 및 경로를 결정한다. 동일 개념의 이름이 다른 게임의 `step`과 일치하지 않는다.
- `game.js`는 같은 이미지 목록을 복제해 일반적인 동일 카드 쌍을 만든다.
- `game1.js`는 원본 파일명에서 `t.png` 파일명을 생성하고, 파일명의 앞 4글자를 비교해 서로 다른 표현의 쌍을 판정한다.
- 음성 파일도 이미지 URL의 확장자 또는 `t.png` 접미사를 문자열 치환하여 추론한다.
- `index.html`과 `index1.html`, `game.js`와 `game1.js`가 병존하므로 통합 시 어느 버전이 운영 진입점인지 먼저 확정해야 한다.

### paza-game

- `index.php`가 `items.js`를 전역 변수로 먼저 적재한 뒤 `game.js`를 실행한다.
- 데이터 외형은 matching-game과 같지만, `v1`~`v10`마다 `{ vol, step, path, cover, name[] }`를 반복 선언한다.
- `game.js`는 `vol`, `step`으로 AlaSQL 조회하지만 곧바로 `step = "A"`로 덮어쓴다. 조회 실패 시 `v1/A`로 폴백한다.
- `items.js`의 `path`는 이미 볼륨까지 포함한다. matching-game과 달리 게임 코드가 `vol/step`을 추가하지 않는다.
- 카드 이미지는 `imageName + ".png"`, 진행 이미지는 `textName + "-T.png"` 규칙으로 추론한다. 매칭 판정은 `imageName`과 `textName` 값에 의존한다.

## 확인된 문제

- 같은 의미의 선택 키가 `step`, `clas`, `sor`로 분산되어 있다.
- `path`가 어떤 게임에서는 루트이고 다른 게임에서는 완성된 세트 경로여서 의미가 일정하지 않다.
- `cover`는 확장자 포함 여부가 데이터마다 다르다.
- `name`, `imageName`, `textName`은 실제 역할을 설명하지 못하며 게임별로 의미도 다르다.
- 이미지·음성·짝 이미지 관계가 파일명 접미사와 문자열 치환에 암묵적으로 결합되어 있다.
- 전역 `items`, AlaSQL, 게임 내부 하드코딩 배열이라는 세 가지 로딩 방식이 공존한다.
- 데이터가 없거나 잘못된 경우 조용히 다른 세트로 폴백하여 잘못된 콘텐츠가 표시될 수 있다.

## 제안 공통 데이터 구조

정적 데이터는 우선 JSON 호환 객체로 관리한다. 각 세트는 선택 메타데이터와 완성된 에셋 참조를 가지며, 각 콘텐츠 항목은 모든 게임에서 동일한 `id`, `pairId`, `assets` 구조를 사용한다.

```js
{
  schemaVersion: 1,
  sets: [
    {
      id: "v1-A",
      volume: "v1",
      level: "A",
      assetBase: "../../assets/game/memory-game/v1/A/",
      cover: "PS03_center.png",
      items: [
        {
          id: "PH-1",
          pairId: "PH-1",
          label: "PH-1",
          assets: {
            primaryImage: "PH-1.png",
            pairedImage: "PH-1t.png",
            progressImage: null,
            audio: "PH-1.wav"
          }
        }
      ]
    }
  ]
}
```

### 필드 원칙

- `volume`, `level`: 모든 게임이 공유하는 선택 키로 통일한다. URL도 최종적으로 `?volume=v1&level=A`를 표준으로 삼는다.
- `assetBase`: 세트 기준 디렉터리만 나타낸다. 볼륨/레벨을 포함할지 여부를 게임 코드가 추론하지 않는다.
- `cover` 및 `assets.*`: 확장자를 포함한 파일명 또는 완전한 URL을 저장한다.
- `id`: 렌더링과 로그에 사용하는 항목 고유 ID이다.
- `pairId`: 매칭 정답을 판정하는 명시적 키이다. 파일명 일부 비교를 대체한다.
- `label`: 화면 표시용 값이다. 정답 판정에 사용하지 않는다.
- `assets.primaryImage`: 기본 카드/드래그 이미지이다.
- `assets.pairedImage`: memory-game의 다른 면처럼 같은 `pairId`에 속한 대체 이미지이다.
- `assets.progressImage`: paza-game의 `*-T.png`처럼 정답 후 표시할 이미지이다.
- `assets.audio`: 재생할 음성 파일이다. 확장자 치환으로 생성하지 않는다.
- 사용하지 않는 역할은 `null`로 두어 모든 항목의 형태를 동일하게 유지한다.

## 게임별 매핑

| 기존 값 | 공통 필드 | 적용 게임 |
|---|---|---|
| `vol` | `volume` | 전체 |
| `step`, `clas` | `level` | 전체 |
| `path` 및 코드 내 `assetBasePath` | `assetBase` | 전체 |
| `cover` | `cover` | matching, paza |
| `name[].imageName` | `id`, `assets.primaryImage` | matching, paza |
| `name[].textName` | `pairId` 또는 `label` | matching, paza |
| 하드코딩 `PH-*`, `PS-*` 배열 | `items[]` | memory |
| `t.png` 접미사 파일 | `assets.pairedImage` | memory |
| `-T.png` 접미사 파일 | `assets.progressImage` | paza |
| 이미지에서 추론한 wav/mp3 | `assets.audio` | 전체 |

## 구현 TODO

### 1. 운영 동작과 데이터 인벤토리 확정

- [ ] `memory-game/index.html`과 `index1.html` 중 실제 운영 진입점 및 필요한 두 매칭 모드를 확정한다.
- [ ] 세 게임이 사용하는 모든 `volume`, `level` 조합과 실제 에셋 파일을 목록화한다.
- [ ] 각 항목의 `primaryImage`, `pairedImage`, `progressImage`, `audio`, `cover` 존재 여부를 검사하는 스크립트를 작성한다.
- [ ] 현재 폴백 규칙(`A`, `v1/A`)을 제품 요구사항으로 유지할지, 오류 표시로 바꿀지 확정한다.

검증: 현재 접근 가능한 모든 URL 조합이 어떤 공통 세트 ID로 매핑되는지 표로 확인하고, 참조 에셋 누락이 0건이어야 한다.

### 2. 공통 카탈로그 도입

- [ ] `game/data/game-content.js` 또는 동일 목적의 단일 JSON 파일을 만들고 `schemaVersion`, `sets` 구조를 정의한다.
- [ ] matching-game의 `items.js` 데이터를 공통 구조로 변환한다.
- [ ] paza-game의 `v1`~`v10` 데이터를 공통 구조로 변환한다.
- [ ] memory-game의 하드코딩 목록과 `t.png`/음성 규칙을 명시적 `assets` 값으로 옮긴다.
- [ ] 세트 ID와 항목 ID가 중복되지 않고 필수 필드가 존재하는지 검증하는 테스트를 추가한다.

검증: 세 게임의 기존 데이터 개수와 변환된 세트/항목 개수가 일치하고, 카탈로그가 JSON 직렬화 가능한 형태여야 한다.

### 3. 공통 로더 구현

- [ ] `loadGameSet({ volume, level })` 하나로 세트를 찾고 정규화된 객체를 반환하는 공통 로더를 만든다.
- [ ] `resolveAsset(assetBase, file)`에서만 URL을 조합하도록 경로 책임을 집중한다.
- [ ] URL 파라미터는 표준 키를 우선 읽고, 이행 기간에는 `vol`, `step`, `clas`를 구 키 별칭으로 지원한다.
- [ ] 세트 미존재, 필수 필드 누락, 항목 수 부족을 명시적 오류로 처리한다.
- [ ] 데이터 조회를 위해서만 사용하는 AlaSQL 의존성을 제거한다.

검증: 정상 선택, 구 URL 별칭, 누락 선택, 존재하지 않는 선택, 불완전 데이터에 대한 단위 테스트가 통과해야 한다.

### 4. 게임별 소비 코드 전환

- [ ] matching-game이 `set.items`에서 뽑은 `id`, `pairId`, `assets.primaryImage`, `assets.audio`만 사용하도록 변경한다.
- [ ] memory-game의 두 모드를 공통 항목 기반으로 생성하고, 파일명 앞 4글자 비교를 `pairId` 비교로 교체한다.
- [ ] paza-game이 `assets.primaryImage`, `assets.progressImage`, `pairId`를 사용하도록 변경한다.
- [ ] 세 게임에서 경로 덧붙이기, 확장자 치환, 접미사 생성 코드를 제거한다.
- [ ] 각 HTML/PHP 진입 파일이 공통 카탈로그와 로더를 게임 코드보다 먼저 로드하도록 변경한다.

검증: 각 게임에서 동일 `volume/level` 선택이 같은 공통 세트를 로드하며, 카드 표시·매칭 판정·음성·완료 화면이 기존과 동일하게 동작해야 한다.

### 5. 레거시 제거와 회귀 검증

- [ ] 모든 게임 전환 후에만 기존 `matching-game/items.js`, `paza-game/items.js`, memory-game 내부 데이터 배열을 제거한다.
- [ ] 더 이상 사용하지 않는 AlaSQL 스크립트 태그를 제거한다.
- [ ] 데스크톱 드래그, 터치 드래그, 카드 뒤집기 두 모드를 각각 수동 검증한다.
- [ ] `v1` 기본값, 각 레벨, `paza-game`의 `v1`~`v10`, `level=T` 특수 경로를 회귀 테스트한다.
- [ ] 잘못된 URL 파라미터에서 의도한 오류 또는 합의된 폴백이 표시되는지 확인한다.

검증: 브라우저 콘솔 오류와 404 에셋 요청이 없고, 레거시 데이터 선언 및 파일명 추론 코드 검색 결과가 0건이어야 한다.

## 완료 기준

- 세 게임이 하나의 카탈로그 스키마와 하나의 로더를 사용한다.
- 데이터 선택 키는 `volume`, `level`로 통일되고 구 URL은 정해진 이행 기간 동안만 호환된다.
- 게임 로직에는 콘텐츠 파일명 목록이나 경로/접미사 생성 규칙이 남지 않는다.
- 정답 판정은 파일명이 아닌 `pairId`로 수행한다.
- 데이터 스키마, 에셋 존재, 세트 선택, 세 게임 주요 플레이 흐름에 대한 자동 또는 명시적 수동 검증 절차가 있다.
