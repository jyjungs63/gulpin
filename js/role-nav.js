(() => {
  function renderRoleNav() {
    const root = document.getElementById('roleNav');
    if (!root) return;

    let loginInfo = null;
    try {
      loginInfo = JSON.parse(localStorage.getItem('infochaitalk'));
    } catch (_) {
      localStorage.removeItem('infochaitalk');
    }

    if (!loginInfo) {
      root.replaceChildren();
      root.removeAttribute('class');
      return;
    }

    const basePath = root.dataset.basePath ?? '../';
    const role = String(loginInfo?.role ?? '0').toLowerCase();
    const user = String(loginInfo?.user ?? '').trim().toLowerCase();
  const menus = {
    admin: [
      ['게시판', `${basePath}board/boardmgr.php`],
      ['지사관리', `${basePath}purchase/branchmgr.php`],
      ['주문관리', `${basePath}purchase/order.php`],
      ['사용자관리', `${basePath}study/vol_manage.php`],
      ['학습현황', `${basePath}study/study_stats.php`],
      ['리뷰추가', `${basePath}reviews/admin_reviews.php`]
    ],
    branch: [
      ['게시판', `${basePath}board/boardmgr.php`],
      ['지사관리', `${basePath}purchase/branchmgr.php`],
      ['주문관리', `${basePath}purchase/order.php`]
    ],
    teacher: [
      ['게시판', `${basePath}board/boardmgr.php`],
      ['유치원관리', `${basePath}purchase/kgardenmgr.php`]
    ]
  };

    let items = [];
  if (role === '9' || role === 'admin' || user === 'admin' || user === 'chaitalk') items = menus.admin;
  else if (role === '1') items = menus.branch;
  else if (role === '2') items = menus.teacher;

    root.className = 'role-nav';
  root.innerHTML = `
    <div class="role-nav__inner">
      <a class="role-nav__home" href="${basePath}index.php">🏠 Home</a>
      <span class="role-nav__welcome"></span>
      <button class="role-nav__toggle" type="button" aria-label="메뉴 열기" aria-expanded="false">☰</button>
      <nav class="role-nav__menu" aria-label="관리 메뉴"></nav>
    </div>`;

    const menu = root.querySelector('.role-nav__menu');
  root.querySelector('.role-nav__welcome').textContent = loginInfo?.name ? `${loginInfo.name}님 환영합니다` : '';
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
    window.location.href = `${basePath}index.php`;
  });
    menu.appendChild(logout);

    const toggle = root.querySelector('.role-nav__toggle');
    toggle.addEventListener('click', () => {
      const open = menu.classList.toggle('open');
      toggle.setAttribute('aria-expanded', String(open));
    });
  }

  window.renderRoleNav = renderRoleNav;
  renderRoleNav();
})();
