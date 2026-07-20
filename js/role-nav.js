(() => {
  const root = document.getElementById('roleNav');
  if (!root) return;

  let loginInfo = null;
  try {
    loginInfo = JSON.parse(localStorage.getItem('infochaitalk'));
  } catch (_) {
    localStorage.removeItem('infochaitalk');
  }

  const role = String(loginInfo?.role ?? '0').toLowerCase();
  const menus = {
    admin: [
      ['게시판', '../board/boardmgr.php'],
      ['지사관리', '../purchase/branchmgr.php'],
      ['주문관리', '../purchase/order.php'],
      ['사용자관리', '../study/vol_manage.php'],
      ['학습현황', '../study/study_stats.php']
    ],
    branch: [
      ['게시판', '../board/boardmgr.php'],
      ['지사관리', '../purchase/branchmgr.php'],
      ['주문관리', '../purchase/order.php']
    ],
    teacher: [
      ['게시판', '../board/boardmgr.php'],
      ['유치원관리', '../purchase/kgardenmgr.php']
    ]
  };

  let items = [];
  if (role === '9' || role === 'admin') items = menus.admin;
  else if (role === '1') items = menus.branch;
  else if (role === '2') items = menus.teacher;

  root.className = 'role-nav';
  root.innerHTML = `
    <div class="role-nav__inner">
      <a class="role-nav__home" href="../index.php">대치동<span>셈수학</span></a>
      <span class="role-nav__welcome" id="gpWelcome"></span>
      <button class="role-nav__toggle" type="button" aria-label="메뉴 열기" aria-expanded="false">☰</button>
      <nav class="role-nav__menu" aria-label="관리 메뉴"></nav>
    </div>`;

  const menu = root.querySelector('.role-nav__menu');
  root.querySelector('#gpWelcome').textContent = loginInfo?.name ? `${loginInfo.name}님 환영합니다` : '';
  items.forEach(([text, href]) => {
    const link = document.createElement('a');
    link.href = href;
    link.textContent = text;
    menu.appendChild(link);
  });

  const logout = document.createElement('a');
  logout.href = '#';
  logout.className = 'role-nav__logout';
  logout.textContent = '로그아웃';
  logout.addEventListener('click', event => {
    event.preventDefault();
    localStorage.removeItem('infochaitalk');
    window.location.href = '../index.php';
  });
  menu.appendChild(logout);

  const toggle = root.querySelector('.role-nav__toggle');
  toggle.addEventListener('click', () => {
    const open = menu.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
  });
})();
