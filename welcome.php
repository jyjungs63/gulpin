<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>글핀 | 초등 문해력 골든타임 완성 학습 프로그램</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Jua&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="welcome.css?v=<?= filemtime('welcome.css') ?>">
<script src="js/common.js?v=<?= filemtime('js/common.js') ?>"></script>
</head>
<body>

<script src="js/welcome-images.js"></script>

<!-- ============================================================ MAIN PAGE ============================================================ -->
<div id="page-main" class="page active">

  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고"  onerror="this.style.display='none';">
    <span class="pill">글핀</span><span id="gpWelcome">방문자님 환영합니다</span>
  </div>

  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
    <button class="burger" onclick="document.getElementById('mainNav').classList.toggle('open')">☰</button>
    <nav class="main-nav" id="mainNav">
      <button class="nav-toggle" onclick="toggleNav(this)">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this)">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>

  <!-- Carousel: 4 real banners, 3 visible at a time, arrows move by 1 -->
  <div class="carousel-wrap">
    <button class="car-arrow prev" id="prevBtn" onclick="slide(-1)">‹</button>
    <button class="car-arrow next" id="nextBtn" onclick="slide(1)">›</button>
    <div class="carousel-viewport">
      <div class="carousel-track" id="track">
        <div class="slide" onclick="goTo('page-gp')">
          <div class="imgbox">
            <img data-src="gp_partner_bn.png" alt="글에 어휘의 핀을 꽂다 - 가맹점 전용" onerror="onImgErr(this)">
            <div class="fallback">📖<br>gp_partner_bn.png<br>가맹점 전용</div>
          </div>
        </div>
        <div class="slide" onclick="goTo('page-mj')">
          <div class="imgbox">
            <img data-src="mjm_kinder_bn.png" alt="말잼글잼 - 유치원 전용" onerror="onImgErr(this)">
            <div class="fallback">🐢<br>mjm_kinder_bn.png<br>유치원 전용</div>
          </div>
        </div>
        <div class="slide" onclick="goTo('page-class')">
          <div class="imgbox">
            <img data-src="bn_online_class.png" alt="온라인 화상수업" onerror="onImgErr(this)">
            <div class="fallback">💻<br>bn_online_class.png<br>온라인 화상수업</div>
          </div>
        </div>
        <!-- <div class="slide" onclick="window.open('https://www.chaitalkkid.co.kr/simple/','_blank')"> -->
        <div class="slide" onclick="location.href='simple/'">
          <div class="imgbox">
            <img data-src="bn_afterschool.png" alt="학교 방과후 전용" onerror="onImgErr(this)">
            <div class="fallback">🏫<br>bn_afterschool.png<br>학교 방과후 전용</div>
          </div>
        </div>
      </div>
    </div>
    <div class="car-controls">
      <span class="dot" id="dot1">1</span>
      <span class="dot" id="dot2">2</span>
      <button class="play-toggle" id="playBtn" onclick="togglePlay()">❚❚</button>
    </div>
    <button class="kakao-btn" onclick="openKakao()">
      <img data-src="btn_kakao.png" alt="카카오톡 상담·문의" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=&quot;background:#f9e000;border-radius:50%;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.6rem;color:#391b1b;&quot;>TALK</div>';">
    </button>
  </div>

  <!-- Icons: swap to *_click.png on hover, navigate on click -->
  <div class="icon-row">
    <button class="icon-btn" data-base="icon_gp" onmouseenter="iconHover(this,true)" onmouseleave="iconHover(this,false)" onclick="goTo('page-gp')">
      <span class="icon-box"><div class="imgbox"><img data-src="icon_gp.png" alt="글핀 가맹점" onerror="onImgErr(this)"><div class="fallback">🗂️</div></div></span>
      <span>글핀 가맹점</span>
    </button>
    <button class="icon-btn" data-base="icon_child" onmouseenter="iconHover(this,true)" onmouseleave="iconHover(this,false)" onclick="goTo('page-mj')">
      <span class="icon-box"><div class="imgbox"><img data-src="icon_child.png" alt="말잼글잼" onerror="onImgErr(this)"><div class="fallback">🧒</div></div></span>
      <span>말잼글잼</span>
    </button>
    <button class="icon-btn" data-base="icon_monitor" onmouseenter="iconHover(this,true)" onmouseleave="iconHover(this,false)" onclick="goTo('page-class')">
      <span class="icon-box"><div class="imgbox"><img data-src="icon_monitor.png" alt="온라인 화상 수업" onerror="onImgErr(this)"><div class="fallback">🖥️</div></div></span>
      <span>온라인 화상 수업</span>
    </button>
    <button class="icon-btn" data-base="icon_school" onmouseenter="iconHover(this,true)" onmouseleave="iconHover(this,false)" onclick="window.open('simple/index.php','_blank')">
      <span class="icon-box"><div class="imgbox"><img data-src="icon_school.png" alt="방과후 한자" onerror="onImgErr(this)"><div class="fallback">🏫</div></div></span>
      <span>방과후 한자</span>
    </button>
    <button class="icon-btn" data-base="icon_check" onmouseenter="iconHover(this,true)" onmouseleave="iconHover(this,false)" onclick="alert('문해력 테스트 페이지는 준비 중입니다.')">
      <span class="icon-box"><div class="imgbox"><img data-src="icon_check.png" alt="문해력 테스트" onerror="onImgErr(this)"><div class="fallback">📝</div></div></span>
      <span>문해력 테스트</span>
    </button>
  </div>

  <section class="review-showcase" aria-labelledby="reviewTitle">
    <div class="review-heading">
      <p class="review-eyebrow">아이와 학부모님이 직접 경험한</p>
      <h2 id="reviewTitle">생생 <span>리얼 후기</span></h2>
      <p>글핀과 함께 달라진 아이들의 이야기를 만나보세요.</p>
    </div>
    <div class="review-viewport" id="reviewViewport">
      <div class="review-track" id="reviewTrack" aria-live="polite"><p class="review-empty">후기를 불러오는 중입니다.</p></div>
    </div>
    <div class="review-controls">
      <button class="review-arrow" id="reviewPrev" type="button" aria-label="이전 후기">←</button>
      <span class="review-status" id="reviewStatus">- / -</span>
      <button class="review-arrow" id="reviewNext" type="button" aria-label="다음 후기">→</button>
    </div>
  </section>

  <footer></footer>
</div>

<!-- ============================================================ 글핀 페이지 ============================================================ -->
<div id="page-gp" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span id="gpWelcome2">방문자님 환영합니다</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>
  <div class="header-simple" id="gpUtilityMenu">
    <div></div>
    <nav>
      <a id="gpBoardMenu" href="board/boardmgr.php" style="display:none;">게시판</a>
      <a id="gpBranchMenu" href="purchase/branchmgr.php" style="display:none;">지사관리</a>
      <a id="gpLoginMenu" style="color:#26301f;" onclick="handleGpLoginMenu()">로그인</a>
    </nav>
  </div>

  <div class="gp-hero gp-hero-full">
    <div class="imgbox" style="aspect-ratio:160/49;">
      <img data-src="gp_online.png" alt="초등 문해력 골든타임 완성 학습 프로그램, 글핀" onerror="onImgErr(this)">
      <div class="fallback">초등 문해력 골든타임 완성 학습 프로그램, 글핀<br>(gp_online.png)</div>
    </div>
  </div>

  <div class="gp-pillbar">
    <img class="logo-img" data-src="gp_logo_03.png" alt="글핀" onerror="onImgErr(this)">
    <a class="pill-link" onclick="goTo('page-hp')">한자파닉스 ›</a>
    <a class="pill-link" onclick="goTo('page-wj')">어휘잼 ›</a>
    <a class="pill-link" onclick="goTo('page-hj')">한자로 어휘를 사고한다 ›</a>
    <a class="pill-link" onclick="goTo('page-gs')">급수파자한자 ›</a>
    <a class="pill-link" onclick="goTo('page-wjs')">어휘력의 신 ›</a>
  </div>
</div>

<div class="login-overlay" id="gpLoginModal" role="dialog" aria-modal="true" aria-labelledby="gpLoginTitle" onclick="closeGpLogin(event)">
  <div class="login-dialog">
    <div class="login-header">
      <h3 id="gpLoginTitle">로그인</h3>
      <button type="button" class="login-close" aria-label="닫기" onclick="closeGpLogin()">×</button>
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
      </form>
    </div>
  </div>
</div>

<!-- ============================================================ 한자파닉스 ============================================================ -->
<div id="page-hp" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>한자파닉스</span>
  </div>
  <header class="site-header">
    <button class="home-link back-icon-link" type="button" aria-label="뒤로 가기" data-tooltip="뒤로 가기" onclick="goTo('page-gp')">↩</button>
  </header>
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/hp_online.png');" role="img" aria-label="그림으로 배우는 한자파닉스">
      <section class="video-learning-menu" data-program="hj-phonics" aria-label="한자파닉스 동영상 학습"></section>
    </div>
  </div>
</div>

<!-- ============================================================ 어휘잼 ============================================================ -->
<div id="page-wj" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>어휘잼</span>
  </div>
  <header class="site-header">
    <button class="home-link back-icon-link" type="button" aria-label="뒤로 가기" data-tooltip="뒤로 가기" onclick="goTo('page-gp')">↩</button>
  </header>
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/wj_online.png');" role="img" aria-label="우리 아이의 공부 자신감, 어휘잼">
      <section class="video-learning-menu" data-program="word-jam" aria-label="어휘잼 동영상 학습"></section>
    </div>
  </div>
</div>

<!-- ============================================================ 한자로 사고한다 ============================================================ -->
<div id="page-hj" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>한자로 어휘를 사고한다</span>
  </div>
  <header class="site-header">
    <button class="home-link back-icon-link" type="button" aria-label="뒤로 가기" data-tooltip="뒤로 가기" onclick="goTo('page-gp')">↩</button>
  </header>
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/hj_vocab.png');" role="img" aria-label="한자로 어휘를 사고한다">
      <section class="video-learning-menu" data-program="legacy-hj" aria-label="한자로 어휘를 사고한다 동영상 학습"></section>
    </div>
  </div>
</div>

<!-- ============================================================ 급수파자한자 ============================================================ -->
<div id="page-gs" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>급수파자한자</span>
  </div>
  <header class="site-header">
    <button class="home-link back-icon-link" type="button" aria-label="뒤로 가기" data-tooltip="뒤로 가기" onclick="goTo('page-gp')">↩</button>
  </header>
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/gs_online.png');" role="img" aria-label="한자기반 어휘사고력, 급수파자한자">
      <section class="video-learning-menu" data-program="gs-paja" aria-label="급수파자한자 동영상 학습"></section>
    </div>
  </div>
</div>

<!-- ============================================================ 어휘력의 신 ============================================================ -->
<div id="page-wjs" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>어휘력의 신</span>
  </div>
  <header class="site-header">
    <button class="home-link back-icon-link" type="button" aria-label="뒤로 가기" data-tooltip="뒤로 가기" onclick="goTo('page-gp')">↩</button>
  </header>
  <div class="banner-hero" style="background:#1c2b4a;">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/ws_online.png');" role="img" aria-label="어휘력의 신을 깨우다">
      <section class="video-learning-menu" data-program="voca-genius" aria-label="어휘력의 신 동영상 학습"></section>
    </div>
  </div>
</div>

<!-- ============================================================ 말잼글잼 페이지 ============================================================ -->
<div id="page-mj" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">말잼글잼</span><span>방문자님 환영합니다</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>

  <div class="mj-hero">
    <div class="imgbox mj-online-box" style="aspect-ratio:16/6;">
      <img data-src="mj_online.png" alt="말잼글잼 - 지구팡의 대모험" onerror="onImgErr(this)">
      <div class="fallback">🐢 말잼글잼 - 지구팡의 대모험 🐢<br>(mj_online.png)</div>
    </div>
  </div>

  <div class="mj-tabs">
    <button class="mj-tab-btn on" data-t="0" onclick="goTo('page-jj')">
      <div class="imgbox" style="aspect-ratio:3/1;"><img data-src="btn_jj_online_01.png" alt="0 잼잼" onerror="onImgErr(this)"><div class="fallback" style="font-size:.9rem;">0 잼잼</div></div>
    </button>
    <button class="mj-tab-btn" data-t="1" onclick="goTo('page-mjplay')">
      <div class="imgbox" style="aspect-ratio:3/1;"><img data-src="btn_mj_online_02.png" alt="1 말잼" onerror="onImgErr(this)"><div class="fallback" style="font-size:.9rem;">1 말잼</div></div>
    </button>
    <button class="mj-tab-btn" data-t="2" onclick="goTo('page-gj')">
      <div class="imgbox" style="aspect-ratio:3/1;"><img data-src="btn_gj_online_03.png" alt="2 글잼" onerror="onImgErr(this)"><div class="fallback" style="font-size:.9rem;">2 글잼</div></div>
    </button>
  </div>
</div>

<!-- ============================================================ 잼잼 / 말잼 / 글잼 개별 페이지 ============================================================ -->
<div id="page-jj" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">말잼글잼</span><span>잼잼</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼글잼으로</button>
  <div class="mj-hero">
    <div class="imgbox" style="aspect-ratio:16/9;">
      <img data-src="jj_main.png" alt="잼잼" onerror="onImgErr(this)">
      <div class="fallback">🐢 잼잼 🐢<br>(jj_main.png)</div>
    </div>
  </div>
</div>

<div id="page-mjplay" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">말잼글잼</span><span>말잼</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼글잼으로</button>
  <div class="mj-hero">
    <div class="imgbox" style="aspect-ratio:16/9;">
      <img data-src="mj_main.png" alt="말잼" onerror="onImgErr(this)">
      <div class="fallback">🐢 말잼 🐢<br>(mj_main.png)</div>
    </div>
  </div>
</div>

<div id="page-gj" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">말잼글잼</span><span>글잼</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>
  <button class="back-btn" onclick="goTo('page-mj')">← 말잼글잼으로</button>
  <div class="mj-hero">
    <div class="imgbox" style="aspect-ratio:16/9;">
      <img data-src="gj_bg_03.png" alt="글잼" onerror="onImgErr(this)">
      <div class="fallback">🐢 글잼 🐢<br>(gj_bg_03.png)</div>
    </div>
  </div>
</div>

<!-- ============================================================ 온라인 화상수업 페이지 ============================================================ -->
<div id="page-class" class="page">
  <div class="visitor-strip">
    <img class="strip-logo" data-src="gp_logo_03.png" alt="글핀 로고" onerror="this.style.display='none';">
    <span class="pill">글핀</span><span>온라인 화상수업</span>
  </div>
  <header class="site-header">
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
  </header>

  <div class="class-photo">
    <div class="imgbox" style="aspect-ratio:20/9;">
      <img data-src="Gulpin_Live_Class.png" alt="글핀 화상수업 - 실시간 화상 수업 장면" onerror="onImgErr(this)">
      <div class="fallback">화상수업 장면<br>(Gulpin_Live_Class.png)</div>
    </div>
  </div>


  <div class="class-wrap">
    <div class="class-body">
      <h2 class="class-title">글핀 <span>화상수업</span></h2>
      <p class="class-sub">거리의 제약 없이, 전국 어디에서나!<br>글핀은 전국 어디에서나 참여할 수 있는 실시간 화상수업을 운영합니다.</p>
      <div class="class-features">
        <div class="feature"><span class="f-icon">🗣️</span><p>실시간<br>1:1 그룹 수업</p></div>
        <div class="feature"><span class="f-icon">👩‍🏫</span><p>전문 선생님과<br>체계적인 수업</p></div>
        <div class="feature"><span class="f-icon">💡</span><p>교재·영상 연계로<br>학습 효과 UP!</p></div>
        <div class="feature"><span class="f-icon">🖥️</span><p>집에서<br>편리하게, 꾸준히!</p></div>
      </div>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">수업 진행 <span>3단계</span></h3>
      <p class="cls-h2-sub">신청부터 첫 수업까지, 이렇게 진행돼요</p>
      <div class="cls-process">
        <div class="cls-process-step"><div class="cls-step-num">1</div><h4>상담·신청</h4><p>카카오톡 또는 전화로<br>아이 레벨을 확인하고 신청해요</p></div>
        <div class="cls-arrow">›</div>
        <div class="cls-process-step"><div class="cls-step-num">2</div><h4>화상 접속</h4><p>정해진 시간에 링크를 눌러<br>화상수업방에 입장해요</p></div>
        <div class="cls-arrow">›</div>
        <div class="cls-process-step"><div class="cls-step-num">3</div><h4>1:1 맞춤 수업</h4><p>선생님과 실시간으로<br>교재를 보며 학습해요</p></div>
      </div>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">수업 <span>시간표</span></h3>
      <p class="cls-h2-sub">희망하는 요일과 시간대를 선택해 주세요 (1회 30분)</p>
      <table class="cls-schedule">
        <thead><tr><th>시간</th><th>월</th><th>화</th><th>수</th><th>목</th><th>금</th></tr></thead>
        <tbody>
          <tr><td>15:00</td><td>●</td><td>-</td><td>●</td><td>-</td><td>●</td></tr>
          <tr><td>16:00</td><td>●</td><td>●</td><td>-</td><td>●</td><td>-</td></tr>
          <tr><td>17:00</td><td>-</td><td>●</td><td>●</td><td>-</td><td>●</td></tr>
          <tr><td>18:00</td><td>●</td><td>-</td><td>●</td><td>●</td><td>-</td></tr>
        </tbody>
      </table>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">학년별 <span>커리큘럼</span></h3>
      <p class="cls-h2-sub">아이 수준에 맞춘 단계별 학습 구성</p>
      <div class="cls-level-cards">
        <div class="cls-level-card"><h4>초등 1~2학년</h4><p>한자파닉스로 기초 부수를 익히고 한글 어휘력의 기틀을 다져요.</p></div>
        <div class="cls-level-card"><h4>초등 3~4학년</h4><p>어휘잼·한자로 어휘를 사고한다 과정으로 사고력을 확장해요.</p></div>
        <div class="cls-level-card"><h4>초등 5~6학년</h4><p>급수파자한자·어휘력의 신 과정으로 상급 어휘를 완성해요.</p></div>
      </div>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">선생님 <span>소개</span></h3>
      <p class="cls-h2-sub">경험 많은 전문 선생님이 함께합니다</p>
      <div class="cls-teacher-cards">
        <div class="cls-teacher-card"><div class="cls-teacher-avatar">👩‍🏫</div><h4>김민지 선생님</h4><p>초등 문해력 8년차</p></div>
        <div class="cls-teacher-card"><div class="cls-teacher-avatar">🧑‍🏫</div><h4>박준호 선생님</h4><p>한자·어휘 지도 6년차</p></div>
        <div class="cls-teacher-card"><div class="cls-teacher-avatar">👩‍🏫</div><h4>이수연 선생님</h4><p>독서·논술 지도 5년차</p></div>
      </div>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">학부모 <span>후기</span></h3>
      <p class="cls-h2-sub">글핀 화상수업을 먼저 경험한 학부모님들의 이야기</p>
      <div class="cls-review-cards">
        <div class="cls-review-card"><div class="cls-review-stars">★★★★★</div><p>"이동 시간 없이 집에서 바로 수업을 들을 수 있어서 편해요. 아이도 선생님과 눈을 맞추며 집중을 잘합니다."</p><span>초2 학부모</span></div>
        <div class="cls-review-card"><div class="cls-review-stars">★★★★★</div><p>"한자를 어려워했는데 그림과 스토리로 설명해주시니 재미있어 하고 어휘력이 눈에 띄게 늘었어요."</p><span>초4 학부모</span></div>
        <div class="cls-review-card"><div class="cls-review-stars">★★★★★</div><p>"1:1 수업이라 아이 속도에 맞춰 진행되고, 피드백도 꼼꼼하게 챙겨주셔서 만족합니다."</p><span>초6 학부모</span></div>
      </div>
    </div>

    <div class="cls-section">
      <h3 class="cls-h2">자주 묻는 <span>질문</span></h3>
      <div class="cls-faq">
        <details><summary>수업은 어떤 프로그램으로 진행되나요?</summary><p>줌(Zoom) 화상회의 프로그램을 이용하며, 신청 후 접속 링크와 이용 방법을 안내해 드립니다.</p></details>
        <details><summary>준비물이 따로 필요한가요?</summary><p>인터넷이 연결된 PC 또는 태블릿과 카메라·마이크만 있으면 됩니다. 교재는 신청 시 함께 발송해 드립니다.</p></details>
        <details><summary>수업을 못 들었을 때 보강이 가능한가요?</summary><p>사전에 문의 주시면 같은 주 내 다른 시간대로 보강 수업을 도와드립니다.</p></details>
      </div>
    </div>

    <div class="cls-section cls-cta">
      <button class="cls-cta-btn primary" onclick="openKakao()">카카오톡 상담 신청</button>
      <button class="cls-cta-btn secondary" onclick="alert('무료체험 신청 페이지는 준비 중입니다.')">무료체험 신청</button>
    </div>
  </div>

  <button class="talk-fab" onclick="openKakao()">
    <img data-src="btn_kakao.png" alt="카카오톡 상담·문의" onerror="this.style.display='none'; this.parentElement.innerHTML='<div style=&quot;background:#f9e000;border-radius:50%;aspect-ratio:1;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.65rem;color:#391b1b;&quot;>TALK</div>';">
  </button>
</div>

<script>
  document.querySelectorAll('img[data-src]').forEach(img=>{
    img.src = src(img.getAttribute('data-src'));
  });

  const learningPrograms={
    'hj-phonics':{
      levels:0,
      units:()=>4,
      files:()=>['song-01.mp4','study-01.mp4','game-01.mp4','game-02.mp4','game-03.mp4']
    },
    'word-jam':{
      levels:7,
      units:()=>4,
      files:()=>['study-01.mp4','study-02.mp4','study-03.mp4']
    },
    'gs-paja':{
      levels:2,
      units:()=>5,
      files:()=>['game-01.mp4','game-02.mp4','hanja-song.mp4','study-01.mp4','voca-song.mp4']
    },
    'voca-genius':{
      levels:2,
      units:level=>level===1?5:6,
      files:(level,unit)=>level===1&&unit===4
        ? ['song-01.mp4','song-02.mp4','song-03.mp4','study.mp4']
        : ['song.mp4','study.mp4','voca.mp4']
    },
    'legacy-hj':{
      levels:7,
      units:()=>4,
      files:()=>['study-01.mp4','study-02.mp4','study-03.mp4','game-01.mp4','game-02.mp4']
      }
  };

  const learningFileLabels={
    'game-01.mp4':'게임 영상 1','game-02.mp4':'게임 영상 2','game-03.mp4':'게임 영상 3',
    'study-01.mp4':'학습 영상 1','study-02.mp4':'학습 영상 2','study-03.mp4':'학습 영상 3',
    'song.mp4':'한자송','study.mp4':'학습 영상','voca.mp4':'어휘 영상',
    'song-01.mp4':'한자송 1','song-02.mp4':'한자송 2','song-03.mp4':'한자송 3'
  };

  function initVideoLearningMenu(menu){
    if(menu.dataset.initialized) return;
    const program=learningPrograms[menu.dataset.program];
    if(!program) return;
    const state={program,programId:menu.dataset.program,level:1,unit:1};
    menu.dataset.initialized='true';
    menu.innerHTML=`
      <h3>동영상 학습</h3>
      <div class="learning-levels"></div>
      <div class="learning-units"></div>
      <div class="learning-video">
        <div class="learning-video-header">
          <p class="learning-video-title">동영상 재생</p>
          <button class="learning-video-close" type="button" aria-label="동영상 닫기">×</button>
        </div>
        <video controls controlsList="nodownload" disablePictureInPicture><source type="video/mp4"></video>
      </div>
      <div class="learning-course-grid"></div>`;

    const levelWrap=menu.querySelector('.learning-levels');
    for(let level=1;level<=program.levels;level++){
      const button=document.createElement('button');
      button.className='learning-level-btn';
      button.dataset.level=level;
      button.textContent=`${level}단계`;
      button.addEventListener('click',()=>{state.level=level;state.unit=1;renderLearningMenu(menu,state);});
      levelWrap.appendChild(button);
    }
    menu.querySelector('.learning-video-close').addEventListener('click',()=>closeLearningVideo(menu));
    renderLearningMenu(menu,state);
  }

  function renderLearningMenu(menu,state){
    menu.querySelectorAll('.learning-level-btn').forEach(button=>button.classList.toggle('active',Number(button.dataset.level)===state.level));
    const unitWrap=menu.querySelector('.learning-units');
    unitWrap.innerHTML='<span>단원:</span>';
    for(let unit=1;unit<=state.program.units(state.level);unit++){
      const button=document.createElement('button');
      button.className=`learning-unit-btn${unit===state.unit?' active':''}`;
      button.textContent=String(unit).padStart(2,'0');
      button.addEventListener('click',()=>{state.unit=unit;renderLearningMenu(menu,state);});
      unitWrap.appendChild(button);
    }
    const grid=menu.querySelector('.learning-course-grid');
    grid.innerHTML='';
    const levelFolder=`level-${String(state.level).padStart(2,'0')}`;
    const unitFolder=`unit-${String(state.unit).padStart(2,'0')}`;
    const card=document.createElement('article');
    card.className='learning-course-card';
    card.innerHTML=`<div class="learning-course-title ${state.level===2?'level-2':''}">${levelFolder} / ${unitFolder}</div>`;

    if(state.program.legacy){
      for(let course=1;course<=3;course++) state.program.files().forEach(file=>{
        const button=document.createElement('button');
        button.className='learning-action-btn';
        button.textContent=`${course}강 ${file==='study'?'학습 영상':file==='voca'?'어휘 영상':'한자송'}`;
        button.addEventListener('click',()=>playLearningVideo(menu,state,`${course}g_${file}.mp4`,button.textContent));
        card.appendChild(button);
      });
    }else{
      state.program.files(state.level,state.unit).forEach(file=>{
        const button=document.createElement('button');
        button.className='learning-action-btn';
        button.textContent=learningFileLabels[file]||file;
        button.addEventListener('click',()=>playLearningVideo(menu,state,file,button.textContent));
        card.appendChild(button);
      });
    }
    grid.appendChild(card);
  }

  function playLearningVideo(menu,state,file,label){
    if(!gpLoginInfo){
      handleGpLoginMenu();
      return;
    }
    const videoBox=menu.querySelector('.learning-video');
    const video=videoBox.querySelector('video');
    videoBox.querySelector('.learning-video-title').textContent=`${state.level}단계 ${state.unit}단원 - ${label}`;
    const level=String(state.level).padStart(2,'0');
    const unit=String(state.unit).padStart(2,'0');
    video.querySelector('source').src=state.program.legacy
      ? `/assets/simple/videos/${state.level}-level/unit_${String.fromCharCode(96+state.unit)}/${file}`
      : `/assets/gulpin/programs/${state.programId}/level-${level}/unit-${unit}/videos/${file}`;
    video.load();
    videoBox.classList.add('active');
    videoBox.scrollIntoView({behavior:'smooth',block:'center'});
  }

  function closeLearningVideo(menu){
    const videoBox=menu.querySelector('.learning-video');
    const video=videoBox.querySelector('video');
    video.pause();
    video.querySelector('source').removeAttribute('src');
    video.load();
    videoBox.classList.remove('active');
  }

  function goTo(id){
    const page = document.getElementById(id);
    if(!page) return;
    document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
    window.scrollTo(0, 0);
    page.classList.add('active');
    page.querySelectorAll('.video-learning-menu').forEach(initVideoLearningMenu);
    const utilityMenu = document.getElementById('gpUtilityMenu');
    utilityMenu.hidden = id === 'page-main';
    if(!utilityMenu.hidden){
      const header = page.querySelector('.site-header');
      header.append(utilityMenu);
    }
    const nav = document.getElementById('mainNav'); if(nav) nav.classList.remove('open');
  }
  function toggleNav(btn){ btn.classList.toggle('on'); }
  function iconHover(btn, on){
    const base = btn.getAttribute('data-base');
    const img = btn.querySelector('img');
    if(!img) return;
    img.classList.remove('err');
    img.src = src(base + (on ? '_click.png' : '.png'));
  }
  function openKakao(){ alert('카카오톡 채널로 상담·문의 연결 (데모)'); }

  const GP_LOGIN_MAX_AGE_MS = 8 * 60 * 60 * 1000;
  const GP_LOGIN_ID_PATTERN = /^[A-Za-z0-9가-힣ㄱ-ㅎㅏ-ㅣ._@-]{2,40}$/;
  let gpLoginInfo = null;
  let gpLoginSubmitting = false;

  function getGpLoginInfo(){
    const info = getLocalStorage('infochaitalk');
    if(!info) return null;
    if(!info.loginAt) info.loginAt = Date.now();
    if(Date.now() - Number(info.loginAt) > GP_LOGIN_MAX_AGE_MS){
      deleteLocalStorage('infochaitalk');
      return null;
    }
    saveLocalStorage('infochaitalk', info);
    return info;
  }

  function renderGpLogin(){
    const welcome = document.getElementById('gpWelcome');
    const welcome2 = document.getElementById('gpWelcome2');
    const menu = document.getElementById('gpLoginMenu');
    const boardMenu = document.getElementById('gpBoardMenu');
    const branchMenu = document.getElementById('gpBranchMenu');
    if(gpLoginInfo){
      const msg = `${gpLoginInfo.name || gpLoginInfo.user}님 환영합니다`;
      welcome.textContent = msg;
      if(welcome2) welcome2.textContent = msg;
      boardMenu.style.display = '';
      branchMenu.style.display = '';
      menu.textContent = '로그아웃';
      menu.style.color = '#dc3545';
    }else{
      welcome.textContent = '방문자님 환영합니다';
      if(welcome2) welcome2.textContent = '방문자님 환영합니다';
      boardMenu.style.display = 'none';
      branchMenu.style.display = 'none';
      menu.textContent = '로그인';
      menu.style.color = '#26301f';
    }
  }

  function handleGpLoginMenu(){
    if(gpLoginInfo){
      deleteLocalStorage('infochaitalk');
      gpLoginInfo = null;
      renderGpLogin();
      return;
    }
    document.getElementById('gpLoginModal').classList.add('open');
    document.body.style.overflow = 'hidden';
    setTimeout(()=>document.getElementById('gpEmail').focus(), 0);
  }

  function closeGpLogin(event){
    if(event && event.target !== event.currentTarget) return;
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
    gpLoginSubmitting = true;
    button.disabled = true;
    button.textContent = '로그인 중...';

    const showError = message=>{
      error.textContent = message;
      error.style.display = 'block';
      passwordInput.value = '';
    };

    try{
      const dest = new URLSearchParams(location.search).get('dest') || '';
      await CallAjax1(`SMethods.php?dest=${encodeURIComponent(dest)}`, 'POST', formData, response=>{
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
          renderGpLogin();
          closeGpLogin();
        }else{
          showError('아이디 또는 비밀번호가 잘못되었습니다.');
        }
      }, ()=>showError('로그인 처리 중 오류가 발생했습니다.'));
    }finally{
      gpLoginSubmitting = false;
      button.disabled = false;
      button.textContent = '로그인';
    }
  });

  document.addEventListener('keydown', event=>{
    if(event.key === 'Escape' && document.getElementById('gpLoginModal').classList.contains('open')) closeGpLogin();
  });

  gpLoginInfo = getGpLoginInfo();
  renderGpLogin();
  const initialPageId = location.hash.slice(1);
  if(initialPageId && document.getElementById(initialPageId)?.classList.contains('page')){
    goTo(initialPageId);
  }

  const totalSlides = 4, visible = 3, maxIndex = totalSlides - visible;
  let idx = 0, playing = true, timer = null;
  const track = document.getElementById('track');
  function render(){
    const slideWidth = track.children[0].getBoundingClientRect().width + 18;
    track.style.transform = `translateX(${-idx*slideWidth}px)`;
    document.getElementById('prevBtn').disabled = idx===0;
    document.getElementById('nextBtn').disabled = idx===maxIndex;
    document.getElementById('dot1').classList.toggle('on', idx===0);
    document.getElementById('dot2').classList.toggle('on', idx===maxIndex);
  }
  function slide(dir){ idx = Math.min(maxIndex, Math.max(0, idx+dir)); render(); }
  function togglePlay(){
    playing = !playing;
    document.getElementById('playBtn').textContent = playing ? '❚❚' : '▶';
    if(playing) startAuto(); else clearInterval(timer);
  }
  function startAuto(){
    clearInterval(timer);
    timer = setInterval(()=>{ idx = idx>=maxIndex ? 0 : idx+1; render(); }, 10000);
  }

  const reviewState={items:[],index:0,timer:null,startX:0};
  const reviewTrack=document.getElementById('reviewTrack');
  const escapeReviewHtml=value=>String(value).replace(/[&<>'"]/g,char=>({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[char]));
  function renderReviews(){
    if(!reviewState.items.length)return;
    reviewTrack.querySelectorAll('.review-card').forEach((card,index)=>card.classList.toggle('active',index===reviewState.index));
    const active=reviewTrack.children[reviewState.index];
    const viewport=document.getElementById('reviewViewport');
    const offset=viewport.clientWidth/2-(active.offsetLeft+active.offsetWidth/2);
    reviewTrack.style.transform=`translateX(${offset}px)`;
    document.getElementById('reviewStatus').textContent=`${reviewState.index+1} / ${reviewState.items.length}`;
  }
  function moveReview(direction){
    if(!reviewState.items.length)return;
    reviewState.index=(reviewState.index+direction+reviewState.items.length)%reviewState.items.length;
    renderReviews();
    startReviewAuto();
  }
  function startReviewAuto(){
    clearInterval(reviewState.timer);
    if(reviewState.items.length>1)reviewState.timer=setInterval(()=>moveReview(1),5000);
  }
  async function loadReviews(){
    try{
      const response=await fetch('reviews/api/reviews.php');
      if(!response.ok)throw new Error('후기를 불러오지 못했습니다.');
      const data=await response.json();
      reviewState.items=data.reviews||[];
      if(!reviewState.items.length){reviewTrack.innerHTML='<p class="review-empty">등록된 후기가 없습니다.</p>';return;}
      reviewTrack.innerHTML=reviewState.items.map((review,index)=>`<article class="review-card${index===0?' active':''}">${review.image_url?`<img class="review-image" src="${escapeReviewHtml(review.image_url)}" alt="${escapeReviewHtml(review.title)} 후기 이미지" loading="lazy">`:''}<div class="review-stars" aria-label="평점 ${review.rating}점">${'★'.repeat(review.rating)}${'☆'.repeat(5-review.rating)}</div><h3>${escapeReviewHtml(review.title)}</h3><p>${escapeReviewHtml(review.content)}</p><div class="review-meta"><span>${escapeReviewHtml(review.author)}</span><span>${escapeReviewHtml(review.student_grade)}</span></div></article>`).join('');
      renderReviews();startReviewAuto();
    }catch(error){reviewTrack.innerHTML='<p class="review-empty">후기를 불러오지 못했습니다. 잠시 후 다시 시도해 주세요.</p>'}
  }
  document.getElementById('reviewPrev').addEventListener('click',()=>moveReview(-1));
  document.getElementById('reviewNext').addEventListener('click',()=>moveReview(1));
  document.getElementById('reviewViewport').addEventListener('touchstart',event=>{reviewState.startX=event.changedTouches[0].clientX},{passive:true});
  document.getElementById('reviewViewport').addEventListener('touchend',event=>{const distance=event.changedTouches[0].clientX-reviewState.startX;if(Math.abs(distance)>45)moveReview(distance>0?-1:1)},{passive:true});
  window.addEventListener('resize', render);
  window.addEventListener('resize',renderReviews);
  window.addEventListener('load', ()=>{ render(); startAuto(); loadReviews(); });
</script>
</body>
</html>
