// 글핀 계정 role 정의 — 관리페이지.xlsx 기준 (서버 쪽: Server/roles.php, 두 파일을 같게 유지)
//   첫째 자리 = 그룹 (1 유치원, 2 학원, 3 홈스쿨, 4 화상수업, 5 방과후)
//   자릿수   = 단계 (1자리 지사, 2자리 선생, 3자리 학생),  9 = 본사(admin)
(function () {
  const NAMES = {
    9: '본사',
    1: '유치원지사', 10: '유치원교사', 100: '유치원생',
    2: '학원지사', 20: '학원교사', 200: '학원생',
    3: '방문수업지사', 30: '방문수업교사', 300: '방문수업생',
    4: '화상수업지사', 40: '화상수업교사', 400: '화상수업생',
    5: '방과후지사', 50: '방과후교사', 500: '방과후생',
  };
  // 초기 화면 영역 => 로그인할 수 있는 그룹 (franchise = 홈스쿨·화상수업·방과후 공용 어휘담 화면)
  const AREA_GROUP = { kinder: [1], academy: [2], homeschool: [3], online: [4], afterschool: [5], franchise: [3, 4, 5] };
  const AREA_NAMES = { kinder: '유치원', academy: '학원', homeschool: '방문수업', online: '화상수업', afterschool: '방과후', franchise: '방문수업·화상수업·방과후' };
  const num = (role) => Number(role);

  const GP_ROLE = {
    ADMIN: 9,
    NAMES,
    AREA_NAMES,
    name: (role) => NAMES[num(role)] || '기타',
    // 예전 데이터의 '홈스쿨'(과정 값, 주문 교재명)도 화면에는 '방문수업'으로 보여 준다
    courseLabel: (text) => String(text ?? '').replace(/홈스쿨/g, '방문수업'),
    isAdmin: (role) => num(role) === 9,
    group: (role) => (num(role) === 9 ? 0 : Number(String(num(role)).charAt(0))),
    isBranch: (role) => [1, 2, 3, 4, 5].includes(num(role)),
    isTeacher: (role) => [10, 20, 30, 40, 50].includes(num(role)),
    isStudent: (role) => [100, 200, 300, 400, 500].includes(num(role)),
    // 유치원생 외 학생 (개인 단계(step)로 학습)
    isAcademyStudent: (role) => [200, 300, 400, 500].includes(num(role)),
    // 본사는 모든 영역, 그 외는 같은 그룹 영역에서만 로그인/이용
    canUseArea: (role, area) => !area || GP_ROLE.isAdmin(role) || (AREA_GROUP[area] || []).includes(GP_ROLE.group(role)),

    // 주문 화면 제목용 호칭
    orderTitle: (role) => (GP_ROLE.isTeacher(role) ? '교사' : GP_ROLE.group(role) >= 3 ? '가맹점' : '지사장'),

    // 로그인 후 관리 메뉴 — 관리페이지.xlsx "각 로그인 경우 메인 바 상태" (로그아웃은 화면마다 따로 붙인다)
    //   본사: 게시판·지사관리·주문관리·학생관리·사용자권한관리·학습현황·admin (+ 리뷰추가)
    //   유치원지사: 게시판·유치원관리·주문관리,  학원지사: 게시판·학원 등록·주문관리·학생관리
    //   홈스쿨·화상수업·방과후 지사: 게시판·선생님 등록·주문관리·학생관리
    //   선생님: 게시판·학생관리 (홈스쿨선생은 학생관리에서 교재 주문),  학생: 없음
    menus(role, basePath = '') {
      const board = ['게시판', `${basePath}board/boardmgr.php`];
      const branchmgr = (text) => [text, `${basePath}purchase/branchmgr.php`];
      const order = ['주문관리', `${basePath}purchase/order.php`];
      const branchStudents = ['학생관리', `${basePath}purchase/branchstudents.php`];
      const homeOrders = ['주문현황', `${basePath}purchase/homeorderstatus.php`];   // 홈스쿨 교재 주문현황
      switch (num(role)) {
        case 9:
          return [
            board,
            branchmgr('지사관리'),
            order,
            homeOrders,
            branchStudents,
            ['사용자권한관리', `${basePath}study/vol_manage.php`],
            ['학습현황', `${basePath}study/study_stats.php`],
            ['admin', `${basePath}login/adminLogin.php`],
            ['리뷰추가', `${basePath}reviews/admin_reviews.php`],
          ];
        case 1: return [board, branchmgr('유치원관리'), order];
        case 2: return [board, branchmgr('학원 등록'), order, branchStudents];
        case 3: return [board, branchmgr('교사 등록'), order, homeOrders, branchStudents];
        case 4:
        case 5: return [board, branchmgr('교사 등록'), order, branchStudents];
        // 유치원·홈스쿨 학생은 학습 화면에 로그인 → 아이디 생성 화면 (kgardenmgr)
        case 10: return [board, ['학생관리', `${basePath}purchase/kgardenmgr.php`]];
        case 30: return [board, ['학생관리', `${basePath}purchase/homeschoolstudents.php`], homeOrders];
        case 20:
        case 40:
        case 50: return [board, ['학생관리', `${basePath}purchase/institutemgr.php`]];
        default: return [];
      }
    },
  };

  window.GP_ROLE = GP_ROLE;
})();
