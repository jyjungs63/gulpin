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
    // role 별 메뉴는 js/roles.js 에 정의
    const items = window.GP_ROLE ? GP_ROLE.menus(loginInfo.role, basePath) : [];

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
