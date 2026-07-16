**[역할 설정]**
너는 HTML5, Vanilla JavaScript, CSS3(Modern CSS) 전문가이자, 모바일 우선(Mobile-First) UI/UX 트렌드에 정통한 시니어 프론트엔드 개발자야.

**[프로젝트 기술 스택]**
- Frontend: HTML5, CSS3, Vanilla JavaScript (No Framework, No Libraries like Tailwind/Bootstrap)
- Backend API Integration: Vanilla JS `fetch` API를 활용한 asynchronous RESTful CRUD 통신
- Target Design Trend: 모바일 우선(Mobile-First) 반응형 디자인, 컴팩트하고 직관적인 대시보드 스타일 UI (Modern Minimalist 또는 Refined Glassmorphism 감성 적용)

**[요청 사항]**
다음 요구사항을 충족하는 프론트엔드 컴포넌트 구조와 코드를 생성해줘:

1. **CSS Architecture (Modern CSS3)**
   - `@media (min-width: ...)` 구조를 사용하는 철저한 모바일 우선(Mobile-First) 반응형 레이아웃 구현.
   - CSS Variables(`:root`)를 적극 활용하여 테마 컬러(Primary, Secondary, Background, Surface, Text), Border-radius, Spacing 등을 모듈화.
   - 모바일 환경에서의 터치 타깃(최소 44x44px), 부드러운 트랜지션, 모던한 그림자(`box-shadow`) 또는 블러(Glassmorphic `backdrop-filter`) 효과 적용.
   - Layout은 CSS Flexbox와 Grid만 사용하여 깔끔하게 구현.

2. **Vanilla JavaScript & Fetch CRUD**
   - 외부 라이브러리 없이 순수 JS로 작성. DOM이 로드된 후(`DOMContentLoaded`) 실행되도록 캡슐화.
   - 백엔드 PHP API 엔드포인트(예: `api/read.php`, `api/create.php`)와 통신하는 `fetch` 기반의 비동기(`async/await`) CRUD 함수 작성.
   - 에러 핸들링(`try/catch`, `response.ok` 체크)을 명확히 하고, 사용자에게 직관적인 UI 피드백(로딩 스피너 토글, 성공/실패 토스트 메시지 등)을 제공하는 로직 포함.
   - State(상태) 변화에 따라 UI가 부드럽게 리렌더링되거나 DOM이 동적으로 업데이트되는 구조.

3. **HTML5 Semantic & UX component**
   - 시맨틱 태그(`<main>`, `<section>`, `<article>`, `<nav>`)를 준수하여 구조 설계.
   - 모바일 스와이프나 한 손 조작을 고려한 하단 탭 바(Navigation) 또는 깔끔한 드롭다운 메뉴 구조 포함.

**[구현할 구체적인 화면/기능]**
- [여기에 구현하고자 하는 구체적인 페이지나 컴포넌트를 적으세요. 예: 홈 화면 대시보드 리스트 및 등록 폼 모달]

위 조건에 맞는 클린하고 주석이 잘 달린 HTML, CSS, JS 코드를 분리된 형태로 제공해줘. 중복되거나 생략된 부분 없이 바로 테스트 가능한 코드로 작성해줘.