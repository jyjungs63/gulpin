# 도토리 배움터

유치원생과 초등학생을 위한 한국어 온라인 학습 랜딩 페이지입니다. 한글·수학·관찰 놀이 각 2개를 바로 체험할 수 있습니다. 보호자 계정은 완료한 활동을 저장하고 다시 확인할 수 있습니다.

## 실행

Node.js 22 이상과 PostgreSQL이 필요합니다. PostgreSQL에 전용 데이터베이스와 접속 계정을 먼저 준비하세요. 마이그레이션을 실행하는 계정에는 대상 스키마의 `USAGE`와 `CREATE` 권한이 필요합니다.

```powershell
npm ci
Copy-Item .env.example .env
```

`.env`의 `DATABASE_URL`을 준비한 데이터베이스의 접속 문자열로 변경합니다. 비밀번호에 URL 예약 문자가 들어 있으면 해당 문자를 URL 인코딩하세요.

```powershell
npm run db:migrate
npm start
```

브라우저에서 [도토리 배움터](http://127.0.0.1:3000)를 열어 **보호자 로그인 → 보호자 계정 만들기**를 선택합니다. 로그인한 상태로 활동을 완료한 뒤 **기록 저장**을 누르면 **내 학습 기록**에서 확인할 수 있습니다.

데이터베이스 없이 첫 화면과 맛보기 활동을 확인하려면 `.env` 없이 `npm start`를 실행합니다.

### PostgreSQL 인증 오류 (28P01)

PostgreSQL이 `DATABASE_URL`의 계정 인증을 거부한 상태입니다. pgAdmin이나 PostgreSQL 관리 도구에서 같은 호스트와 포트의 서버를 열고, URL에 적힌 역할이 존재하며 로그인 권한이 있는지 확인하세요. `.env`에는 그 역할의 비밀번호를 사용합니다. 예제의 `dotori` 역할과 데이터베이스는 PostgreSQL에 직접 만들거나 이미 만든 앱 전용 역할과 데이터베이스로 바꿔야 합니다.

비밀번호의 `@`, `#`, `%` 등은 URL에서 각각 `%40`, `%23`, `%25`로 입력합니다. `.env`를 저장한 뒤 `npm run db:migrate`가 성공하면, 실행 중인 앱을 종료하고 `npm start`로 다시 시작하세요.

### public 스키마 권한 오류 (42501)

처음 설치하는 데이터베이스에서 앱 계정에 데이터베이스의 `CREATE` 권한이 있고 `search_path`가 기본값(`"$user", public`)이면, 앱 계정과 이름이 같은 전용 스키마를 사용할 수 있습니다. 예를 들어 `dotory` 계정으로 접속해 다음 SQL을 실행합니다.

```sql
CREATE SCHEMA IF NOT EXISTS dotory AUTHORIZATION dotory;
```

기존 앱 테이블이 `public`에 있거나 `public`에 설치하려면, 데이터베이스 소유자로 `DATABASE_URL`에 지정한 데이터베이스에 접속해 앱 계정에 필요한 권한을 부여합니다.

```sql
GRANT USAGE, CREATE ON SCHEMA public TO dotory;
```

다른 앱 계정을 사용하는 경우 SQL의 `dotory`를 해당 계정 이름으로 바꿉니다. PowerShell에서 `npm run db:migrate`를 실행하면 앱 계정으로 테이블이 생성됩니다.

## 구성

- `public/` — HTML5, CSS3, 브라우저 JavaScript, 6개 활동과 삽화
- `server/` — Node.js/Express, 보호자 계정·세션·학습 기록 API
- `db/schema.sql` — PostgreSQL 테이블과 인덱스

보호자 이메일과 비밀번호 해시, 세션 토큰 해시, 완료한 활동 ID를 저장합니다. 학습 기록은 보호자 계정 단위입니다. 이메일 인증과 비밀번호 재설정은 이 버전에 포함되어 있지 않습니다. HTTPS 환경에서 서비스할 때는 `COOKIE_SECURE=true`를 설정하세요.
