<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<!--
  말잼별글잼별(유치원 전용) 분리 페이지 — 2차스팩 1) 별도 도메인용
  - 원본: index.php 의 page-mj / page-jj / page-mjplay / page-gj (index.php 는 그대로 두고 복사)
  - 공용 파일(js/, welcome.css, Server/, assets/, game/)은 상위 폴더 것을 같이 쓴다 — 경로는 모두 ../ 로 시작
    · localhost:3000/maljam/   → ../assets = /assets
    · 새 도메인 /  (.htaccess 가 maljam/index.php 로 내부 연결) → ../assets = /assets
  - 로그인 영역은 항상 유치원(kinder) — 유치원 계정·본사만 로그인 가능
-->
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>말잼별글잼별 | 유치원 전용 학습 프로그램</title>
<meta name="theme-color" content="#32a55f">
<link rel="apple-touch-icon" href="/assets/pwa/maljam/apple-touch-icon.png">
<script src="../js/pwa.js?v=<?= filemtime(__DIR__ . '/../js/pwa.js') ?>" data-manifest="/maljam/manifest.json"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Jua&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link rel="stylesheet" href="../welcome.css?v=<?= filemtime(__DIR__ . '/../welcome.css') ?>">
<link rel="stylesheet" href="../role-nav.css?v=<?= filemtime(__DIR__ . '/../role-nav.css') ?>">
<script src="../js/common.js?v=<?= filemtime(__DIR__ . '/../js/common.js') ?>"></script>
<script src="../js/roles.js?v=<?= filemtime(__DIR__ . '/../js/roles.js') ?>"></script>
</head>
<body>

<script src="../js/welcome-images.js?v=<?= filemtime(__DIR__ . '/../js/welcome-images.js') ?>"></script>

<!-- ============================================================ 말잼별글잼별 메인 ============================================================ -->
<div id="page-mj" class="page active">
  <div class="visitor-strip">
    <span class="pill">말잼별글잼별</span><span data-login-welcome>방문자님 환영합니다</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-mj')">🏠 Home</a>
    <span class="header-welcome" data-login-welcome>방문자님 환영합니다</span>
    <button class="gp-utility-toggle" type="button" aria-label="관리 메뉴 열기" aria-expanded="false" onclick="toggleGpUtilityMenu(this)">☰ 메뉴</button>
  </header>
  <div class="header-simple" id="gpUtilityMenu">
    <div></div>
    <nav id="gpUtilityNav"></nav>
  </div>

  <div class="mj-hero">
    <div class="imgbox mj-online-box">
      <picture>
        <source media="(max-width: 840px)" srcset="../assets/banner/mj_online_2_mobile.png">
        <img data-src="mj_online_2.png" alt="말잼별글잼별 - 지구팡의 대모험" onerror="onImgErr(this)">
      </picture>
      <div class="fallback">🐢 말잼별글잼별 - 지구팡의 대모험 🐢<br>(mj_online_2.png)</div>
    </div>
    <div class="mj-tabs">
      <button class="mj-tab-btn on" data-t="0" onclick="goToAfterLogin('page-jj')">
        <span class="mj-tab-face"><span class="mj-tab-number">0</span><span class="mj-tab-label">잼잼별</span></span>
      </button>
      <button class="mj-tab-btn" data-t="1" onclick="goToAfterLogin('page-mjplay')">
        <span class="mj-tab-face"><span class="mj-tab-number">1</span><span class="mj-tab-label">말잼별</span></span>
      </button>
      <button class="mj-tab-btn" data-t="2" onclick="goToAfterLogin('page-gj')">
        <span class="mj-tab-face"><span class="mj-tab-number">2</span><span class="mj-tab-label">글잼별</span></span>
      </button>
    </div>
  </div>
</div>

<div class="login-overlay" id="gpLoginModal" role="dialog" aria-modal="true" aria-labelledby="gpLoginTitle">
  <div class="login-dialog">
    <div class="login-header">
      <h3 id="gpLoginTitle">로그인</h3>
      <button type="button" class="login-close" aria-label="로그인 창 닫기" onclick="event.stopPropagation(); closeGpLogin()">×</button>
    </div>
    <div class="login-body">
      <form id="gpLoginForm" autocomplete="on">
        <div class="login-field">
          <label for="gpEmail">아이디</label>
          <input type="text" id="gpEmail" name="Email" autocomplete="username" maxlength="40" pattern="[A-Za-z0-9가-힣ㄱ-ㅎㅏ-ㅣ._@-]{2,40}" required>
        </div>
        <div class="login-field">
          <label for="gpPassword">비밀번호</label>
          <input type="password" id="gpPassword" name="Password" autocomplete="current-password" maxlength="100" required>
        </div>
        <p class="login-error" id="gpLoginError" role="alert"></p>
        <button type="submit" class="login-submit" id="gpSignin">로그인</button>
        <a href="../login/findpasswd.php" style="display:block; margin-top:12px; text-align:center; font-size:14px; color:#666;">비밀번호를 잊으셨나요?</a>
      </form>
    </div>
  </div>
</div>

<!-- ============================================================ 잼잼 / 말잼 / 글잼 개별 페이지 ============================================================ -->
<div id="page-jj" class="page">
  <div class="visitor-strip">
    <span class="pill">말잼별글잼별</span><span>잼잼별</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-mj')">🏠 Home</a>
    <span class="header-welcome" data-login-welcome>방문자님 환영합니다</span>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼별 글잼별으로</button>
  <section class="star-learning-panel star-card-panel" data-video-prefix="JJ" data-video-free style="--star-bg:url('../assets/banner/jj_main_2.png')" aria-label="잼잼별 영상 학습">
    <div class="star-levels" aria-label="잼잼별 호 선택"></div>
    <div class="star-learning-actions">
      <div class="star-card">
        <img src="../assets/banner/song.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-jj/level-01/어휘송.mp4')">어휘송</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-jj/level-01/한자송.mp4')">한자송</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/video.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-jj/level-01/학습영상1.mp4')">학습영상1</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-jj/level-01/학습영상2.mp4')">학습영상2</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/game.png" alt="">
        <button type="button" onclick="markStarItem(this); CallToast('준비 중입니다.', 'info')">게임1</button>
        <button type="button" onclick="markStarItem(this); CallToast('준비 중입니다.', 'info')">게임2</button>
      </div>
    </div>
    <div class="learning-video star-video-popup" role="dialog" aria-modal="true" aria-label="잼잼별 영상 재생">
      <div class="learning-video-header"><p class="learning-video-title">동영상 재생</p><button class="learning-video-close" type="button" aria-label="동영상 닫기" onclick="closeStarVideo(this)">×</button></div>
      <video controls controlsList="nodownload" disablePictureInPicture playsinline><source type="video/mp4"></video>
    </div>
  </section>
</div>

<div id="page-mjplay" class="page">
  <div class="visitor-strip">
    <span class="pill">말잼별글잼별</span><span>말잼별</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-mj')">🏠 Home</a>
    <span class="header-welcome" data-login-welcome>방문자님 환영합니다</span>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼별글잼별으로</button>
  <section class="star-learning-panel star-card-panel" data-video-prefix="MJ" style="--star-bg:url('../assets/banner/mj_main_2.png')" aria-label="말잼별 영상 학습">
    <div class="star-levels" aria-label="말잼별 호 선택"></div>
    <div class="star-learning-actions">
      <div class="star-card">
        <img src="../assets/banner/song.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-mjplay/level-01/어휘송.mp4')">어휘송</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-mjplay/level-01/한자송.mp4')">한자송</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/video.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-mjplay/level-01/학습영상1.mp4')">학습영상1</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-mjplay/level-01/학습영상2.mp4')">학습영상2</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/game.png" alt="">
        <a class="star-game-link" href="../game/word-game/index.html?star=mjplay&level=01" onclick="return setStarGameLevel(this)">게임1</a>
        <a class="star-game-link" href="../game/memory-game/index.html?star=mjplay&level=01" onclick="return setStarGameLevel(this)">게임2</a>
      </div>
    </div>
    <div class="learning-video star-video-popup" role="dialog" aria-modal="true" aria-label="말잼별 영상 재생">
      <div class="learning-video-header"><p class="learning-video-title">동영상 재생</p><button class="learning-video-close" type="button" aria-label="동영상 닫기" onclick="closeStarVideo(this)">×</button></div>
      <video controls controlsList="nodownload" disablePictureInPicture playsinline><source type="video/mp4"></video>
    </div>
  </section>
</div>

<div id="page-gj" class="page">
  <div class="visitor-strip">
    <span class="pill">말잼별글잼별</span><span>글잼</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-mj')">🏠 Home</a>
    <span class="header-welcome" data-login-welcome>방문자님 환영합니다</span>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼별글잼별으로</button>
  <section class="star-learning-panel star-card-panel" data-video-prefix="GJ" style="--star-bg:url('../assets/banner/gj_bg_04.png')" aria-label="글잼별 영상 학습">
    <div class="star-levels" aria-label="글잼별 호 선택"></div>
    <div class="star-learning-actions">
      <div class="star-card">
        <img src="../assets/banner/song.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-gj/level-01/어휘송.mp4')">어휘송</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-gj/level-01/한자송.mp4')">한자송</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/video.png" alt="">
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-gj/level-01/학습영상1.mp4')">학습영상1</button>
        <button type="button" onclick="playStarVideo(this,'../assets/gulpin/programs/jj/page-gj/level-01/학습영상2.mp4')">학습영상2</button>
      </div>
      <div class="star-card">
        <img src="../assets/banner/game.png" alt="">
        <a class="star-game-link" href="../game/paza-game/index.php" target="_blank" rel="noopener" onclick="markStarItem(this)">문장마스터</a>
        <a class="star-game-link" href="../game/word-game/index.html?star=gj&level=01" onclick="return setStarGameLevel(this)">게임1</a>
        <a class="star-game-link" href="../game/memory-game/index.html?star=gj&level=01" onclick="return setStarGameLevel(this)">게임2</a>
      </div>
    </div>
    <div class="learning-video star-video-popup" role="dialog" aria-modal="true" aria-label="글잼별 영상 재생">
      <div class="learning-video-header"><p class="learning-video-title">동영상 재생</p><button class="learning-video-close" type="button" aria-label="동영상 닫기" onclick="closeStarVideo(this)">×</button></div>
      <video controls controlsList="nodownload" disablePictureInPicture playsinline><source type="video/mp4"></video>
    </div>
  </section>
</div>

<script>
  // index.php 의 말잼별글잼별 관련 스크립트를 옮겨 온 것 — 로그인 영역은 유치원(kinder) 고정
  document.querySelectorAll('img[data-src]').forEach(img=>{
    img.src = src(img.getAttribute('data-src'));
  });

  const GP_AREA = 'kinder';

  function initStarLevels(panel){
    const levelWrap=panel.querySelector('.star-levels');
    if(!levelWrap || levelWrap.children.length) return;
    panel.dataset.level='01';
    for(let level=1;level<=10;level++){
      const button=document.createElement('button');
      button.type='button';
      button.className=`star-level-btn${level===1?' active':''}`;
      button.textContent=`${level}호`;
      button.addEventListener('click',()=>{
        panel.dataset.level=String(level).padStart(2,'0');
        levelWrap.querySelectorAll('.star-level-btn').forEach(item=>item.classList.toggle('active',item===button));
      });
      levelWrap.appendChild(button);
    }
  }

  // 교재 구매에 따른 단계 열람 권한 (Server/catalog.php SGetLearningAccess)
  //   말잼별 N단계 = 영상코드 MJN, 글잼별 = GJN (data-video-prefix), 주문일로부터 6개월. 접두어 없는 패널은 제한 없음
  //   잼잼별(data-video-free)은 영업 계정(demo)만 제한
  let gpLearningAccess = null;

  function starLevelOpen(panel, level){
    const prefix = panel.dataset.videoPrefix;
    if(!prefix) return true;
    if(panel.dataset.videoFree !== undefined && !gpLearningAccess?.demo) return true;
    if(!gpLearningAccess) return false;
    return gpLearningAccess.all || Boolean(gpLearningAccess.codes[`${prefix}${Number(level)}`]);
  }

  function applyStarAccess(panel){
    if(!panel.dataset.videoPrefix) return;
    const buttons = Array.from(panel.querySelectorAll('.star-level-btn'));
    buttons.forEach((button, index)=>{
      const open = starLevelOpen(panel, index + 1);
      button.disabled = !open;
      button.classList.toggle('locked', !open);
      button.title = open ? '' : '교재 구매 후 이용 가능';
    });
    // 선택된 단계가 잠겨 있으면 구매한 첫 단계로 이동
    if(!starLevelOpen(panel, panel.dataset.level || '01')){
      const firstOpen = buttons.find((button, index)=>starLevelOpen(panel, index + 1));
      if(firstOpen) firstOpen.click();
    }
    const anyOpen = buttons.some((button, index)=>starLevelOpen(panel, index + 1));
    const actions = panel.querySelector('.star-learning-actions');
    actions.querySelectorAll('button, .star-game-link[onclick*="setStarGameLevel"]').forEach(item=>item.classList.toggle('locked', !anyOpen));
    let note = actions.querySelector('.star-locked-note');
    if(!anyOpen && !note){
      note = document.createElement('p');
      note.className = 'star-locked-note';
      note.textContent = '교재 구매 후 이용 가능';
      actions.prepend(note);
    }
    if(note) note.hidden = anyOpen;
  }

  function refreshStarAccess(page){
    const panels = Array.from(page.querySelectorAll('.star-learning-panel[data-video-prefix]'));
    if(!panels.length || !gpLoginInfo) return;
    CallAjax1('SMethods.php', 'POST', { functionName: 'SGetLearningAccess', otherData: {} }, resp=>{
      gpLearningAccess = resp.success || { all: false, codes: {} };
      panels.forEach(applyStarAccess);
    }, e=>{
      gpLearningAccess = { all: false, codes: {} };
      panels.forEach(applyStarAccess);
      if(e && e.status === 401) CallToast('로그인이 만료되었습니다. 다시 로그인해 주세요.', 'error');
    });
  }

  function requireStarLevel(panel){
    if(starLevelOpen(panel, panel.dataset.level || '01')) return true;
    CallToast('교재 구매 후 이용 가능합니다.', 'error');
    return false;
  }

  // 카드 이미지는 고른 버튼 안으로 옮겨 크게 보여 주고, 다른 버튼을 고르면 카드 맨 위로 돌려놓는다
  function clearStarItems(root){
    root.querySelectorAll('.star-card').forEach(card=>{
      card.querySelector('.active')?.classList.remove('active');
      card.prepend(card.querySelector('img'));
    });
  }

  function markStarItem(item){
    clearStarItems(item.closest('.star-learning-panel'));
    item.classList.add('active');
    item.prepend(item.closest('.star-card').querySelector('img'));
  }

  function playStarVideo(button, path){
    const panel=button.closest('.star-learning-panel');
    if(!requireStarLevel(panel)) return;
    const videoBox=panel.querySelector('.learning-video');
    const video=videoBox.querySelector('video');
    const source=video.querySelector('source');
    const selectedLevel=panel.dataset.level || '01';
    const selectedPath=path.replace(/level-\d{2}/,`level-${selectedLevel}`);
    markStarItem(button);
    videoBox.querySelector('.learning-video-title').textContent=`${Number(selectedLevel)}호 - ${button.textContent.trim()}`;
    source.src=selectedPath;
    video.load();
    videoBox.classList.add('active');
    video.play().catch(()=>{});
  }

  function setStarGameLevel(link){
    const panel=link.closest('.star-learning-panel');
    if(!requireStarLevel(panel)) return false;
    const selectedLevel=panel.dataset.level || '01';
    link.href=link.href.replace(/level=\d{2}/,`level=${selectedLevel}`);
    markStarItem(link);
    return true;
  }

  function closeStarVideo(closeButton){
    const videoBox=closeButton.closest('.learning-video');
    const video=videoBox.querySelector('video');
    video.pause();
    video.querySelector('source').removeAttribute('src');
    video.load();
    videoBox.classList.remove('active');
  }

  function stopAllMedia(){
    document.querySelectorAll('video, audio').forEach(media=>{
      media.pause();
      media.currentTime=0;
      media.removeAttribute('src');
      media.querySelectorAll('source').forEach(source=>source.removeAttribute('src'));
      media.load();
    });
    document.querySelectorAll('.learning-video.active').forEach(box=>box.classList.remove('active'));
    clearStarItems(document);
  }

  const GP_PROTECTED_PAGE_IDS = new Set(['page-jj', 'page-mjplay', 'page-gj']);

  function goTo(id, updateHistory=true){
    if(GP_PROTECTED_PAGE_IDS.has(id) && !gpLoginInfo){
      gpLoginDestination = id;
      openGpLogin();
      return;
    }
    const page = document.getElementById(id);
    if(!page) return;
    stopAllMedia();
    document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
    window.scrollTo(0, 0);
    page.classList.add('active');
    page.querySelectorAll('.star-learning-panel').forEach(initStarLevels);
    refreshStarAccess(page);
    // 관리 메뉴는 현재 화면의 헤더로 옮겨 붙인다
    const utilityMenu = document.getElementById('gpUtilityMenu');
    const utilityToggle = document.querySelector('.gp-utility-toggle');
    utilityMenu.classList.remove('open');
    utilityToggle.setAttribute('aria-expanded','false');
    utilityToggle.setAttribute('aria-label','관리 메뉴 열기');
    utilityToggle.textContent = '☰ 메뉴';
    const header = page.querySelector('.site-header');
    header.append(utilityToggle);
    header.append(utilityMenu);
    if(updateHistory && history.state?.gulpinPage !== id){
      history.pushState({gulpinPage:id}, '', location.href);
    }
  }

  const gpRestoredPageId = history.state?.gulpinPage;
  history.replaceState({gulpinPage:'page-mj'}, '', location.href);

  window.addEventListener('popstate', event=>{
    const id = event.state?.gulpinPage || 'page-mj';
    goTo(document.getElementById(id) ? id : 'page-mj', false);
  });

  let gpInternalNavigation = false;
  let gpInternalNavigationResetTimer = null;

  function isGpInternalUrl(url){
    try{
      return new URL(url, location.href).origin === location.origin;
    }catch(error){
      return false;
    }
  }

  function markGpInternalNavigation(){
    gpInternalNavigation = true;
    clearTimeout(gpInternalNavigationResetTimer);
    gpInternalNavigationResetTimer = setTimeout(()=>{
      gpInternalNavigation = false;
    }, 5000);
  }

  document.addEventListener('click', event=>{
    const link = event.target.closest('a[href]');
    if(link && isGpInternalUrl(link.href)) markGpInternalNavigation();
  }, true);

  window.addEventListener('pagehide', ()=>{
    stopAllMedia();
    if(!gpInternalNavigation) logoutGpUser(false);
  });

  document.addEventListener('visibilitychange', ()=>{
    if(document.visibilityState === 'hidden'){
      if(!gpInternalNavigation) logoutGpUser(false);
      return;
    }
    gpInternalNavigation = false;
    gpLoginInfo = getGpLoginInfo();
    renderGpLogin();
  });

  window.addEventListener('pageshow', event=>{
    if(!event.persisted) return;
    gpLoginInfo = getGpLoginInfo();
    renderGpLogin();
  });

  function toggleGpUtilityMenu(button){
    const menu=document.getElementById('gpUtilityMenu');
    const isOpen=menu.classList.toggle('open');
    button.setAttribute('aria-expanded',String(isOpen));
    button.setAttribute('aria-label',isOpen?'관리 메뉴 닫기':'관리 메뉴 열기');
    button.textContent=isOpen?'× 닫기':'☰ 메뉴';
  }

  const GP_LOGIN_MAX_AGE_MS = 8 * 60 * 60 * 1000;
  const GP_LOGIN_ID_PATTERN = /^[A-Za-z0-9가-힣ㄱ-ㅎㅏ-ㅣ._@-]{2,40}$/;
  const GP_IS_MOBILE = window.matchMedia('(pointer: coarse)').matches
    || /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
  let gpLoginInfo = null;
  let gpLoginSubmitting = false;
  let gpLoginDestination = null;

  function getGpLoginInfo(){
    const info = getLocalStorage('infochaitalk');
    if(!info) return null;
    if(!info.loginAt) info.loginAt = Date.now();
    if(Date.now() - Number(info.loginAt) > GP_LOGIN_MAX_AGE_MS){
      deleteLocalStorage('infochaitalk');
      return null;
    }
    // 같은 도메인에서 다른 영역(학원 등)으로 로그인한 정보는 쓰지 않는다
    if(!GP_ROLE.canUseArea(info.role, GP_AREA)) return null;
    saveLocalStorage('infochaitalk', info);
    return info;
  }

  function renderGpLogin(){
    const welcomeMessages = document.querySelectorAll('[data-login-welcome]');
    const utilityNav = document.getElementById('gpUtilityNav');
    utilityNav.replaceChildren();

    const addUtilityMenu = (text, href, id)=>{
      const link = document.createElement('a');
      link.textContent = text;
      if(href) link.href = href;
      if(id) link.id = id;
      utilityNav.appendChild(link);
      return link;
    };

    if(gpLoginInfo){
      const displayName = String(gpLoginInfo.name || gpLoginInfo.user || '').trim();
      const msg = displayName ? `${displayName}님 환영합니다` : '사용자님 환영합니다';
      welcomeMessages.forEach(element=>element.textContent = msg);

      // role 별 메뉴는 js/roles.js 에 정의 — 상위 폴더 기준 경로
      // 모바일(PWA 포함, 판정 기준은 js/pwa.js 와 같음)에서는 로그아웃만 보인다
      if(!GP_IS_MOBILE){
        GP_ROLE.menus(gpLoginInfo.role, '../').forEach(([text, href])=>addUtilityMenu(text, href));
      }
      const logout = addUtilityMenu('로그아웃', '', 'gpLoginMenu');
      logout.onclick = handleGpLoginMenu;
    }else{
      welcomeMessages.forEach(element=>element.textContent = '방문자님 환영합니다');
      const login = addUtilityMenu('로그인', '', 'gpLoginMenu');
      login.onclick = handleGpLoginMenu;
    }
  }

  function handleGpLoginMenu(){
    if(gpLoginInfo){
      logoutGpUser();
      return;
    }
    gpLoginDestination = null;
    openGpLogin();
  }

  function openGpLogin(){
    document.getElementById('gpLoginModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(()=>document.getElementById('gpEmail').focus(), 0);
  }

  function goToAfterLogin(id){
    if(gpLoginInfo){
      goTo(id);
      return;
    }
    gpLoginDestination = id;
    openGpLogin();
  }

  function logoutGpUser(shouldRender=true){
    deleteLocalStorage('infochaitalk');
    gpLoginInfo = null;
    if(shouldRender) renderGpLogin();

    const currentPage = document.querySelector('.page.active');
    if(currentPage?.id === 'page-mj') return;
    goTo('page-mj', shouldRender);
    if(!shouldRender) history.replaceState({gulpinPage:'page-mj'}, '', location.href);
  }

  function closeGpLogin(event){
    if(event && event.target !== event.currentTarget) return;
    gpLoginDestination = null;
    document.getElementById('gpLoginModal').classList.remove('open');
    document.body.style.overflow = '';
    document.getElementById('gpLoginError').style.display = 'none';
  }

  document.getElementById('gpLoginForm').addEventListener('submit', async event=>{
    event.preventDefault();
    if(gpLoginSubmitting) return;

    const form = event.currentTarget;
    const emailInput = document.getElementById('gpEmail');
    const passwordInput = document.getElementById('gpPassword');
    const error = document.getElementById('gpLoginError');
    const button = document.getElementById('gpSignin');
    const email = emailInput.value.trim();
    const password = passwordInput.value;

    error.style.display = 'none';
    if(!GP_LOGIN_ID_PATTERN.test(email) || !password || password.length > 100){
      error.textContent = '아이디 또는 비밀번호 형식이 올바르지 않습니다.';
      error.style.display = 'block';
      return;
    }

    const formData = new FormData(form);
    formData.set('Email', email);
    formData.set('Password', password);
    formData.append('functionName', 'Slogon');
    formData.append('area', GP_AREA);
    gpLoginSubmitting = true;
    button.disabled = true;
    button.textContent = '로그인 중...';

    const showError = message=>{
      error.textContent = message;
      error.style.display = 'block';
      passwordInput.value = '';
    };

    try{
      await CallAjax1('SMethods.php?dest=', 'POST', formData, response=>{
        if(response.success && response.success[0]){
          const login = response.success[0];
          gpLoginInfo = {
            user: login.user,
            role: login.role,
            conf: login.confirm,
            name: login.name,
            owner: login.owner,
            loca: login.location,
            step: login.step,
            clas: login.clas,
            loginAt: Date.now()
          };
          saveLocalStorage('infochaitalk', gpLoginInfo);
          passwordInput.value = '';
          if(login.mustChange){ location.href = '../login/changepw.php?temp=1'; return; }
          renderGpLogin();
          const destination = gpLoginDestination;
          gpLoginDestination = null;
          closeGpLogin();
          if(destination) goTo(destination);
        }else{
          showError(response.message || '아이디 또는 비밀번호가 잘못되었습니다.');
        }
      }, ()=>showError('로그인 처리 중 오류가 발생했습니다.'));
    }finally{
      gpLoginSubmitting = false;
      button.disabled = false;
      button.textContent = '로그인';
    }
  });

  document.addEventListener('keydown', event=>{
    const loginModal = document.getElementById('gpLoginModal');
    if((event.key === 'Escape' || event.key === 'Esc') && loginModal.classList.contains('open')){
      event.preventDefault();
      event.stopPropagation();
      closeGpLogin();
    }
  }, true);

  gpLoginInfo = getGpLoginInfo();
  renderGpLogin();
  // 첫 화면: #page-gj 같은 해시, 또는 게임에서 뒤로 가기로 돌아온 경우 이전 화면 복원
  const initialPageId = location.hash.slice(1) || gpRestoredPageId;
  if(initialPageId && initialPageId !== 'page-mj' && document.getElementById(initialPageId)?.classList.contains('page')){
    goTo(initialPageId, false);
    if(document.getElementById(initialPageId).classList.contains('active')){
      history.replaceState({gulpinPage:initialPageId}, '', location.href);
    }
  }else{
    goTo('page-mj', false);
  }
</script>
</body>
</html>
