<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>글핀 | 초등 문해력 골든타임 완성 학습 프로그램</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Jua&family=Noto+Sans+KR:wght@400;500;700;900&display=swap" rel="stylesheet">
<style>
  :root{
    --green-900:#1f5233;
    --green-700:#2e7d46;
    --green-600:#3da35d;
    --green-100:#e3f3e6;
    --cream:#ffffff;
    --muted:#7c8577;
    --cyan:#4fc3e8;
    --orange:#f2994a;
    --radius:18px;
    --shadow: 0 10px 30px rgba(31,82,51,.14);
  }
  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}
  body{font-family:'Noto Sans KR', sans-serif; color:#26301f; background:var(--cream); line-height:1.5; overflow-x:hidden;}
  a{text-decoration:none;color:inherit;}
  button{font-family:inherit;cursor:pointer;border:none;background:none;}

  /* fallback tile shown only if a real image 404s */
  .imgbox{position:relative; width:100%; height:100%; overflow:hidden;}
  .imgbox img{display:block; width:100%; height:100%; object-fit:cover;}
  .imgbox .fallback{display:none; position:absolute; inset:0; align-items:center; justify-content:center; background:linear-gradient(135deg,var(--green-100),#fdf1da); color:var(--muted); font-size:.78rem; font-weight:700; text-align:center; padding:10px; flex-direction:column; gap:6px;}
  .imgbox img.err{display:none;}
  .imgbox img.err + .fallback{display:flex;}

  .page{display:none;}
  .page.active{display:block; animation:fadeIn .35s ease;}
  #page-gp.active{display:flex; flex-direction:column; min-height:100vh; animation:fadeIn .35s ease;}
  @keyframes fadeIn{from{opacity:0; transform:translateY(6px);} to{opacity:1; transform:translateY(0);}}

  .visitor-strip{background:var(--green-900); color:#eafff0; font-size:.78rem; padding:6px 20px; display:flex; align-items:center; gap:10px;}
  .visitor-strip .strip-logo{height:24px; display:block; margin-right:auto;filter: brightness(0) invert(1) grayscale(100%) contrast(90%);}
  .visitor-strip .pill{background:rgba(255,255,255,.15); border-radius:999px; padding:3px 12px; font-weight:700;}
  .home-link{display:flex; align-items:center; gap:6px; color:#fff; font-weight:700; font-size:.95rem; cursor:pointer; text-decoration:none;}
  .home-link:hover{opacity:.85;}
  .back-icon-link{position:relative; width:40px; height:40px; justify-content:center; border-radius:50%; font-size:1.45rem;}
  .back-icon-link::after{content:attr(data-tooltip); position:absolute; top:calc(100% + 9px); left:0; width:max-content; max-width:220px; padding:7px 11px; border-radius:8px; background:rgba(31,45,36,.94); color:#fff; font-size:.76rem; font-weight:700; opacity:0; visibility:hidden; transform:translateY(-4px); transition:opacity .18s ease,transform .18s ease,visibility .18s ease; pointer-events:none; z-index:80;}
  .back-icon-link:hover::after,.back-icon-link:focus-visible::after{opacity:1; visibility:visible; transform:translateY(0);}

  header.site-header{background:linear-gradient(180deg,var(--green-600),var(--green-700)); padding:14px 28px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40; box-shadow:0 4px 18px rgba(0,0,0,.08);}
  .logo-img{height:34px; display:block;}
  .logo-fallback{color:#fff; font-family:'Jua'; font-size:1.4rem; display:none;}
  nav.main-nav{display:flex; gap:28px; align-items:center;}
  nav.main-nav button{color:rgba(255,255,255,.55); font-weight:700; font-size:.95rem; transition:color .2s ease;}
  nav.main-nav button.on{color:#fff;}
  .header-simple{background:#fff; padding:14px 28px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid #eee;}
  .header-simple[hidden]{display:none;}
  .header-simple nav{display:flex; gap:16px; font-weight:700; color:#8a9184; font-size:.82rem; flex-wrap:wrap;}
  .header-simple nav a{cursor:pointer;}
  .site-header .header-simple{margin-left:auto; padding:0; border:0; background:transparent;}
  .site-header .header-simple nav{gap:28px; font-size:.95rem;}
  .site-header .header-simple nav a{color:rgba(255,255,255,.7);}
  .site-header .header-simple nav a:last-child{color:#fff !important;}
  nav.main-nav[hidden]{display:none;}
  .burger{display:none; color:#fff; font-size:1.5rem;}

  @media(max-width:840px){
    nav.main-nav{position:fixed; top:0; right:-100%; height:100%; width:70%; max-width:300px; background:var(--green-700); flex-direction:column; padding:90px 30px; gap:26px; transition:right .3s ease; z-index:60;}
    nav.main-nav.open{right:0;}
    .burger{display:block;}
    header.site-header{padding:12px 16px;}
  }

  .carousel-wrap{position:relative; padding:34px 60px 10px; background:var(--cream); max-width:80%; margin:0 auto;}
  .carousel-viewport{overflow:hidden; border-radius:var(--radius);}
  .carousel-track{display:flex; gap:18px; transition:transform .5s cubic-bezier(.4,0,.2,1);}
  .slide{flex:0 0 calc((100% - 36px)/3); border-radius:var(--radius); overflow:hidden; aspect-ratio:1/1; cursor:pointer; box-shadow:var(--shadow); transition:transform .25s ease;}
  .slide:hover{transform:translateY(-4px);}
  .car-arrow{position:absolute; top:50%; transform:translateY(-50%); width:42px; height:42px; border-radius:50%; background:#fff; box-shadow:var(--shadow); display:flex; align-items:center; justify-content:center; font-size:1.2rem; color:var(--green-700); z-index:5;}
  .car-arrow.prev{left:14px;} .car-arrow.next{right:14px;}
  .car-arrow:disabled{opacity:.3; cursor:default;}
  .car-controls{display:flex; align-items:center; justify-content:center; gap:12px; margin-top:16px;}
  .dot{font-weight:700; font-size:.85rem; color:var(--muted); padding:4px 10px; border-radius:999px;}
  .dot.on{background:var(--green-600); color:#fff;}
  .play-toggle{width:30px;height:30px;border-radius:50%; background:#fff; box-shadow:var(--shadow); display:flex;align-items:center;justify-content:center; font-size:.8rem; color:var(--green-700);}

  .icon-row{display:flex; justify-content:center; flex-wrap:wrap; gap:30px; padding:38px 20px 50px;}
  .icon-btn{display:flex; flex-direction:column; align-items:center; gap:8px; width:100px;}
  .icon-box{width:74px;height:74px;border-radius:18px; overflow:hidden; transition:transform .2s ease; display:block;}
  .icon-btn.on .icon-box{transform:translateY(-3px); box-shadow:0 6px 16px rgba(61,163,93,.3);}
  .icon-btn span{font-size:.8rem; font-weight:700; color:var(--muted); text-align:center;}
  .icon-btn.on span{color:var(--green-700);}

  .review-showcase{padding:8px 0 54px; overflow:hidden; background:linear-gradient(180deg,#fff 0%,#f7fbf7 100%);}
  .review-heading{text-align:center; padding:0 20px 22px;}
  .review-eyebrow{margin:0 0 5px; color:var(--green-600); font-size:.78rem; font-weight:800;}
  .review-heading h2{margin:0; font-family:'Jua'; font-size:1.65rem; color:#26301f;}
  .review-heading h2 span{color:var(--green-600);}
  .review-heading p:last-child{margin:7px 0 0; color:var(--muted); font-size:.82rem;}
  .review-viewport{width:100%; overflow:hidden; padding:24px 0 30px; touch-action:pan-y;}
  .review-track{display:flex; align-items:center; gap:14px; transition:transform .65s cubic-bezier(.22,.61,.36,1); will-change:transform;}
  .review-card{flex:0 0 76vw; max-width:360px; min-height:245px; padding:14px; border:1px solid #e2e9e0; border-radius:16px; background:#fff; box-shadow:0 6px 20px rgba(31,82,51,.08); opacity:.52; transform:scale(.9); transition:opacity .45s ease,transform .45s ease,box-shadow .45s ease; display:flex; flex-direction:column; overflow:hidden;}
  .review-card.active{opacity:1; transform:scale(1.06); border-color:var(--green-600); box-shadow:0 16px 34px rgba(31,82,51,.19);}
  .review-stars{color:#f2a93b; letter-spacing:2px; font-size:.85rem;}
  .review-image{width:calc(100% + 28px); height:150px; margin:-14px -14px 14px; object-fit:cover; background:var(--green-100);}
  .review-card h3{margin:11px 0 8px; font-size:1.02rem; color:#26301f;}
  .review-card p{margin:0 0 18px; color:#5f685a; font-size:.84rem; line-height:1.65;}
  .review-meta{margin-top:auto; padding-top:12px; border-top:1px solid #eef1ed; display:flex; justify-content:space-between; gap:10px; color:var(--muted); font-size:.74rem; font-weight:700;}
  .review-controls{display:flex; justify-content:center; align-items:center; gap:12px;}
  .review-arrow{width:44px; height:44px; border-radius:50%; background:#fff; color:var(--green-700); box-shadow:var(--shadow); font-size:1.1rem;}
  .review-status{min-width:52px; text-align:center; color:var(--muted); font-size:.78rem; font-weight:700;}
  .review-empty{text-align:center; width:100%; color:var(--muted); padding:30px 20px;}

  @media(min-width:640px){
    .review-showcase{padding-bottom:64px;}
    .review-heading h2{font-size:2rem;}
    .review-track{gap:20px;}
    .review-card{flex-basis:320px; min-height:260px; padding:18px;}
    .review-image{width:calc(100% + 36px); height:165px; margin:-18px -18px 16px;}
  }

  .kakao-btn{position:absolute; right:60px; bottom:-26px; z-index:20; width:58px; border-radius:50%; box-shadow:0 8px 24px rgba(0,0,0,.18); overflow:hidden;}
  .kakao-btn img{width:100%; display:block;}
  .talk-fab{position:fixed; right:20px; bottom:20px; z-index:60; width:64px; border-radius:50%; box-shadow:0 8px 24px rgba(0,0,0,.22); overflow:hidden;}
  .talk-fab img{width:100%; display:block;}

  .banner-hero{margin:24px; border-radius:22px; overflow:hidden; box-shadow:var(--shadow);}
  .banner-hero img{width:100%; display:block;}
  .video-learning-menu{margin:0 24px 32px; padding:26px; border-radius:22px; background:#fff8dc; box-shadow:var(--shadow); text-align:center;}
  .video-learning-menu h3{margin:0 0 16px; font-family:'Jua'; font-size:1.35rem; color:var(--green-900);}
  .learning-levels,.learning-units{display:flex; justify-content:center; align-items:center; gap:12px; margin-bottom:16px; flex-wrap:wrap;}
  .learning-level-btn{min-width:120px; padding:11px 20px; border-radius:999px; background:#fff; color:#777; box-shadow:0 4px 12px rgba(0,0,0,.12); font-weight:800;}
  .learning-unit-btn{width:44px; height:44px; border-radius:50%; background:#fff; color:#888; box-shadow:0 4px 12px rgba(0,0,0,.12); font-weight:800;}
  .learning-level-btn.active,.learning-unit-btn.active{background:#188441; color:#fff;}
  .learning-course-grid{display:grid; grid-template-columns:repeat(4,minmax(180px,1fr)); gap:20px; max-width:1200px; margin:24px auto 0;}
  .learning-course-card{padding:18px; border-radius:26px; background:#fff; box-shadow:0 6px 16px rgba(0,0,0,.18);}
  .learning-course-title{padding:10px; margin-bottom:14px; border-radius:999px; background:#60bf4c; color:#fff; font-weight:900;}
  .learning-course-title.level-2{background:#f2a916;}
  .learning-action-btn{display:block; width:100%; margin-top:10px; padding:11px 14px; border-radius:999px; background:#fff; color:#555; box-shadow:0 2px 7px rgba(0,0,0,.13); font-weight:700;}
  .learning-action-btn:hover{background:#f4f8f3; transform:scale(1.02);}
  .learning-video{display:none; max-width:900px; margin:24px auto 0; padding:16px; border-radius:20px; background:#fff; box-shadow:var(--shadow);}
  .learning-video.active{display:block;}
  .learning-video-header{display:flex; align-items:center; justify-content:space-between; gap:16px; margin-bottom:12px;}
  .learning-video-title{margin:0; font-weight:800; color:var(--green-900);}
  .learning-video-close{flex:0 0 36px; width:36px; height:36px; border-radius:50%; background:#eef4ed; color:var(--green-900); font-size:1.3rem; font-weight:900; line-height:1;}
  .learning-video-close:hover{background:var(--green-600); color:#fff;}
  .learning-video video{display:block; width:100%; border-radius:14px; background:#000;}
  .embedded-learning-hero{height:auto !important; min-height:760px; padding:260px 5% 36px; background-size:cover; background-position:center top; background-repeat:no-repeat;}
  .embedded-learning-hero .video-learning-menu{width:100%; margin:0; background:transparent; box-shadow:none; backdrop-filter:none; transform:translateY(50px);}
  @media(max-width:900px){.learning-course-grid{grid-template-columns:repeat(2,minmax(150px,1fr));}}
  @media(max-width:520px){.video-learning-menu{margin:0 12px 24px;padding:18px 12px}.learning-course-grid{grid-template-columns:1fr}.learning-level-btn{min-width:90px}.embedded-learning-hero{min-height:680px;padding:170px 3% 24px}.embedded-learning-hero .video-learning-menu{margin:0}}
  .back-btn{display:inline-flex; align-items:center; gap:6px; margin:20px 24px 0; font-weight:800; color:var(--green-700); font-size:.95rem;}

  .gp-hero{margin:0 24px 16px; border-radius:24px; overflow:hidden; box-shadow:var(--shadow);}
  .gp-hero-full{margin:0 0 16px; width:100%; border-radius:0; box-shadow:none;}
  .gp-hero-full .imgbox{background:var(--cream);}
  .gp-hero-full .imgbox img{object-fit:contain;}
  .steps{display:flex; flex-wrap:wrap; background:#fff; border-radius:18px; box-shadow:var(--shadow); overflow:hidden; margin:0 24px 16px;}
  .step{flex:1 1 200px; padding:20px 18px; border-right:1px solid #f0f0eb; cursor:pointer; transition:background .2s ease;}
  .step:last-child{border-right:none;}
  .step:hover{background:var(--green-100);}
  .step .num{color:var(--green-600); font-weight:900; font-size:.78rem;}
  .step h4{margin:6px 0; font-size:1rem;}
  .step p{margin:0; font-size:.76rem; color:var(--muted);}
  .gp-pillbar{margin:auto 24px 24px; background:#fdf6e3; border-radius:999px; box-shadow:var(--shadow); display:flex; align-items:center; justify-content:center; gap:14px; padding:12px 24px; flex-wrap:wrap;}
  .gp-pillbar .logo-img{height:28px;}
  .pill-link{background:var(--green-600); color:#fff; font-weight:700; font-size:.85rem; padding:9px 18px; border-radius:999px;}
  .pill-link:hover{background:var(--green-700);}

  .mj-hero{margin:0; overflow:hidden;}
  .mj-online-box img{width:100%; height:90%;}
  .mj-tabs{display:flex; justify-content:center; gap:16px; margin:-30px 0 20px; position:relative; z-index:5; flex-wrap:wrap;}
  .mj-tab-btn{width:180px; border-radius:999px; overflow:hidden; box-shadow:var(--shadow); opacity:.7; transition:all .2s ease; border:3px solid transparent;}
  .mj-tab-btn.on{opacity:1; transform:translateY(-2px);}
  .mj-tab-btn[data-t="0"].on{border-color:var(--cyan);}
  .mj-tab-btn[data-t="1"].on{border-color:var(--green-600);}
  .mj-tab-btn[data-t="2"].on{border-color:var(--orange);}
  .mj-header{background:linear-gradient(180deg,var(--green-600),var(--green-700)); padding:14px 28px; display:flex; align-items:center; justify-content:space-between;}
  .mj-header-nav{display:flex; gap:22px; font-weight:700; color:#eafff0; font-size:.9rem;}
  .mj-header-nav a{cursor:pointer;}

  .class-photo{margin:24px 24px 0; border-radius:22px; overflow:hidden; box-shadow:var(--shadow); background:var(--cream);}
  .class-body{padding:24px 28px 8px; text-align:center;}
  .class-title{font-family:'Jua'; font-size:1.6rem; color:#26301f; margin:0 0 10px;}
  .class-title span{color:var(--green-600);}
  .class-sub{color:var(--muted); font-size:.92rem; line-height:1.6; margin:0 0 22px;}
  .class-features{display:flex; justify-content:center; flex-wrap:wrap; gap:22px; max-width:760px; margin:0 auto;}
  .feature{display:flex; flex-direction:column; align-items:center; gap:8px; width:140px;}
  .f-icon{font-size:1.8rem; width:56px; height:56px; border-radius:50%; background:var(--green-100); display:flex; align-items:center; justify-content:center;}

  .class-wrap{max-width:70%; margin:0 auto; padding:0 12px 40px;}
  @media(max-width:640px){ .class-wrap{max-width:94%;} }
  .cls-section{margin:44px 0 0;}
  .cls-h2{font-family:'Jua'; font-size:1.25rem; color:#26301f; text-align:center; margin:0 0 6px;}
  .cls-h2 span{color:var(--green-600);}
  .cls-h2-sub{text-align:center; color:var(--muted); font-size:.85rem; margin:0 0 22px;}

  .cls-process{display:flex; flex-wrap:wrap; justify-content:center; gap:18px;}
  .cls-process-step{flex:1 1 180px; max-width:220px; background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:22px 16px; text-align:center; position:relative;}
  .cls-process-step .cls-step-num{width:30px; height:30px; border-radius:50%; background:var(--green-600); color:#fff; font-weight:800; display:flex; align-items:center; justify-content:center; margin:0 auto 10px; font-size:.85rem;}
  .cls-process-step h4{margin:0 0 6px; font-size:.98rem; color:#26301f;}
  .cls-process-step p{margin:0; font-size:.8rem; color:var(--muted); line-height:1.5;}
  .cls-arrow{align-self:center; color:var(--green-600); font-size:1.4rem; font-weight:700;}

  .cls-schedule{width:100%; border-collapse:collapse; background:#fff; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow);}
  .cls-schedule th, .cls-schedule td{padding:12px 10px; text-align:center; font-size:.85rem; border-bottom:1px solid #f0f0eb;}
  .cls-schedule th{background:var(--green-600); color:#fff; font-weight:700;}
  .cls-schedule tr:last-child td{border-bottom:none;}
  .cls-schedule td:first-child{font-weight:700; color:#26301f; background:#fbf8ef;}

  .cls-level-cards{display:flex; flex-wrap:wrap; gap:16px; justify-content:center;}
  .cls-level-card{flex:1 1 220px; max-width:260px; background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:20px; border-top:6px solid var(--green-600);}
  .cls-level-card h4{margin:0 0 8px; font-size:1rem; color:#26301f;}
  .cls-level-card p{margin:0; font-size:.82rem; color:var(--muted); line-height:1.6;}

  .cls-teacher-cards{display:flex; flex-wrap:wrap; gap:18px; justify-content:center;}
  .cls-teacher-card{flex:1 1 180px; max-width:200px; background:#fff; border-radius:var(--radius); box-shadow:var(--shadow); padding:22px 14px; text-align:center;}
  .cls-teacher-avatar{width:64px; height:64px; border-radius:50%; background:var(--green-100); display:flex; align-items:center; justify-content:center; font-size:1.8rem; margin:0 auto 10px;}
  .cls-teacher-card h4{margin:0 0 4px; font-size:.95rem; color:#26301f;}
  .cls-teacher-card p{margin:0; font-size:.78rem; color:var(--muted);}

  .cls-review-cards{display:flex; flex-wrap:wrap; gap:16px; justify-content:center;}
  .cls-review-card{flex:1 1 260px; max-width:320px; background:#fdf6e3; border-radius:var(--radius); padding:20px; box-shadow:var(--shadow);}
  .cls-review-stars{color:#f5a623; font-size:.9rem; margin-bottom:8px;}
  .cls-review-card p{margin:0 0 10px; font-size:.85rem; color:#26301f; line-height:1.6;}
  .cls-review-card span{font-size:.78rem; color:var(--muted); font-weight:700;}

  .cls-faq{max-width:640px; margin:0 auto;}
  .cls-faq details{background:#fff; border-radius:14px; box-shadow:var(--shadow); margin-bottom:10px; padding:14px 18px;}
  .cls-faq summary{cursor:pointer; font-weight:700; color:#26301f; font-size:.9rem; list-style:none;}
  .cls-faq summary::-webkit-details-marker{display:none;}
  .cls-faq summary::before{content:'Q. '; color:var(--green-600);}
  .cls-faq p{margin:10px 0 0; font-size:.83rem; color:var(--muted); line-height:1.6;}

  .cls-cta{display:flex; flex-wrap:wrap; justify-content:center; gap:14px;}
  .cls-cta-btn{border:none; border-radius:999px; padding:14px 28px; font-weight:800; font-size:.92rem; cursor:pointer;}
  .cls-cta-btn.primary{background:var(--green-600); color:#fff;}
  .cls-cta-btn.secondary{background:#fff; color:var(--green-600); border:2px solid var(--green-600);}
  .feature p{margin:0; font-size:.82rem; font-weight:700; color:#26301f; text-align:center; line-height:1.4;}

  footer{text-align:center; padding:30px 20px 50px; color:var(--muted); font-size:.8rem;}

  .login-overlay{position:fixed; inset:0; z-index:100; display:none; align-items:center; justify-content:center; padding:20px; background:rgba(0,0,0,.45);}
  .login-overlay.open{display:flex;}
  .login-dialog{width:min(420px,100%); overflow:hidden; border-radius:20px; background:#fff; box-shadow:0 18px 50px rgba(0,0,0,.25);}
  .login-header{position:relative; padding:22px; color:#fff; text-align:center; background:linear-gradient(135deg,#667eea,#764ba2);}
  .login-header h3{margin:0; font-size:1.35rem;}
  .login-close{position:absolute; top:10px; right:12px; padding:8px; color:#fff; font-size:1.4rem; line-height:1;}
  .login-body{padding:26px 24px;}
  .login-field{margin-bottom:16px;}
  .login-field label{display:block; margin-bottom:7px; font-size:.9rem; font-weight:700; color:#333;}
  .login-field input{width:100%; min-height:44px; padding:10px 12px; border:2px solid #e0e0e0; border-radius:10px; font:inherit;}
  .login-field input:focus{outline:none; border-color:#667eea; box-shadow:0 0 0 3px rgba(102,126,234,.1);}
  .login-error{display:none; margin:0 0 12px; color:#dc3545; font-size:.85rem;}
  .login-submit{width:100%; min-height:46px; border-radius:10px; color:#fff; font-size:1rem; font-weight:700; background:linear-gradient(135deg,#667eea,#764ba2);}
  .login-submit:disabled{opacity:.65; cursor:wait;}

  @media(max-width:840px){
    .carousel-wrap{padding:20px 46px 6px;}
    .slide{flex:0 0 100%; aspect-ratio:1/1;}
    .kakao-btn{right:46px;}
  }
</style>
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
    <!-- <a class="home-link" onclick="history.back()">🏠 Home</a> -->
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
    <button class="burger" onclick="document.getElementById('gpNav').classList.toggle('open')">☰</button>
    <nav class="main-nav" id="gpNav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
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
    <!-- <a class="home-link" onclick="goTo('page-main')">🏠 Home</a> -->
    <button class="home-link back-icon-link" type="button" aria-label="글핀 페이지로 돌아가기" data-tooltip="글핀 페이지로 돌아가기" onclick="goTo('page-gp')">↩</button>
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-gp')">← 글핀 페이지로</button> -->
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/hp_online.png');" role="img" aria-label="그림으로 배우는 한자파닉스">
      <section class="video-learning-menu" aria-label="한자파닉스 동영상 학습"></section>
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
    <!-- <a class="home-link" onclick="goTo('page-main')">🏠 Home</a> -->
    <button class="home-link back-icon-link" type="button" aria-label="글핀 페이지로 돌아가기" data-tooltip="글핀 페이지로 돌아가기" onclick="goTo('page-gp')">↩</button>
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-gp')">← 글핀 페이지로</button> -->
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/wj_online.png');" role="img" aria-label="우리 아이의 공부 자신감, 어휘잼">
      <section class="video-learning-menu" aria-label="어휘잼 동영상 학습"></section>
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
    <!-- <a class="home-link" onclick="goTo('page-main')">🏠 Home</a> -->
    <button class="home-link back-icon-link" type="button" aria-label="글핀 페이지로 돌아가기" data-tooltip="글핀 페이지로 돌아가기" onclick="goTo('page-gp')">↩</button>
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-gp')">← 글핀 페이지로</button> -->
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/hj_vocab.png');" role="img" aria-label="한자로 어휘를 사고한다">
      <section class="video-learning-menu" aria-label="한자로 어휘를 사고한다 동영상 학습"></section>
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
    <!-- <a class="home-link" onclick="goTo('page-main')">🏠 Home</a> -->
    <button class="home-link back-icon-link" type="button" aria-label="글핀 페이지로 돌아가기" data-tooltip="글핀 페이지로 돌아가기" onclick="goTo('page-gp')">↩</button>
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-gp')">← 글핀 페이지로</button> -->
  <div class="banner-hero">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/gs_online.png');" role="img" aria-label="한자기반 어휘사고력, 급수파자한자">
      <section class="video-learning-menu" aria-label="급수파자한자 동영상 학습"></section>
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
    <a class="home-link" onclick="goTo('page-main')">🏠 Home</a>
    
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-gp')">← 글핀 페이지로</button> -->
  <div class="banner-hero" style="background:#1c2b4a;">
    <div class="imgbox embedded-learning-hero" style="background-image:url('https://www.chaitalkkid.co.kr/gulpin/portal_image/ws_online.png');" role="img" aria-label="어휘력의 신을 깨우다">
      <section class="video-learning-menu" aria-label="어휘력의 신 동영상 학습"></section>
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
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>
  <!-- <button class="back-btn" onclick="goTo('page-main')">← 메인으로</button> -->

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
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
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
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
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
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
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
    <nav class="main-nav">
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀이란?</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-main')">글핀 중국어</button>
      <button class="nav-toggle" onclick="toggleNav(this); openKakao()">상담·신청</button>
      <button class="nav-toggle" onclick="toggleNav(this); goTo('page-gp')">글핀 가맹점 모집</button>
    </nav>
  </header>

  <div class="class-photo">
    <div class="imgbox" style="aspect-ratio:20/9;">
      <img data-src="Gulpin_Live_Class.png" alt="글핀 화상수업 - 실시간 화상 수업 장면" onerror="onImgErr(this)">
      <div class="fallback">화상수업 장면<br>(Gulpin_Live_Class.png)</div>
    </div>
  </div>

  <!-- <button class="back-btn" onclick="goTo('page-main')" style="margin:16px 24px 0;">← 메인으로</button> -->

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

  const learningUnits={A:'unit_a',B:'unit_b',C:'unit_c',D:'unit_d',E:'unit_e'};
  const learningActions=[{id:'study',name:'학습 영상'},{id:'voca',name:'어휘 영상'},{id:'song',name:'한자송'}];
  const learningIntroActions={
    A:[{id:'number',name:'숫자학습영상'}],
    B:[{id:'study',name:'주제별 부수 학습영상'},{id:'song1',name:'한자송1'},{id:'song2',name:'한자송2'}],
    C:[{id:'study',name:'주제별 부수 학습영상'},{id:'song1',name:'한자송1'},{id:'song2',name:'한자송2'}],
    D:[{id:'study',name:'주제별 부수 학습영상'},{id:'song1',name:'한자송1'},{id:'song2',name:'한자송2'},{id:'song3',name:'한자송3'}],
    E:[]
  };

  function initVideoLearningMenu(menu){
    const state={level:'all',unit:'A'};
    menu.innerHTML=`
      <h3>동영상 학습</h3>
      <div class="learning-levels"></div>
      <div class="learning-units"><span>호수:</span></div>
      <div class="learning-video">
        <div class="learning-video-header">
          <p class="learning-video-title">동영상 재생</p>
          <button class="learning-video-close" type="button" aria-label="동영상 닫기">×</button>
        </div>
        <video controls controlsList="nodownload" disablePictureInPicture><source type="video/mp4"></video>
      </div>
      <div class="learning-course-grid"></div>`;

    const levelWrap=menu.querySelector('.learning-levels');
    [['all','전체'],['1-level','1단계'],['2-level','2단계']].forEach(([value,label])=>{
      const button=document.createElement('button');
      button.className='learning-level-btn';
      button.textContent=label;
      button.addEventListener('click',()=>{state.level=value;renderLearningMenu(menu,state);});
      levelWrap.appendChild(button);
    });

    const unitWrap=menu.querySelector('.learning-units');
    Object.keys(learningUnits).forEach(unit=>{
      const button=document.createElement('button');
      button.className='learning-unit-btn';
      button.textContent=`${unit}호`;
      button.addEventListener('click',()=>{state.unit=unit;renderLearningMenu(menu,state);});
      unitWrap.appendChild(button);
    });
    menu.querySelector('.learning-video-close').addEventListener('click',()=>closeLearningVideo(menu));
    renderLearningMenu(menu,state);
  }

  function renderLearningMenu(menu,state){
    menu.querySelectorAll('.learning-level-btn').forEach((button,index)=>button.classList.toggle('active',['all','1-level','2-level'][index]===state.level));
    menu.querySelectorAll('.learning-unit-btn').forEach(button=>button.classList.toggle('active',button.textContent.startsWith(state.unit)));
    const grid=menu.querySelector('.learning-course-grid');
    grid.innerHTML='';
    const levels=state.level==='all'?['1-level','2-level']:[state.level];

    const addCard=(level,title,courseId,actions)=>{
      const card=document.createElement('article');
      card.className='learning-course-card';
      card.innerHTML=`<div class="learning-course-title ${level==='2-level'?'level-2':''}">${title}</div>`;
      actions.forEach(action=>{
        const button=document.createElement('button');
        button.className='learning-action-btn';
        button.textContent=action.name;
        button.addEventListener('click',()=>playLearningVideo(menu,state,level,courseId,action,title));
        card.appendChild(button);
      });
      grid.appendChild(card);
    };

    levels.forEach(level=>{
      const levelNumber=level==='1-level'?'1':'2';
      const count=state.unit==='E'?(level==='1-level'?4:5):3;
      for(let index=1;index<=count;index++) addCard(level,`${levelNumber}-${state.unit}${index}`,`${index}g`,learningActions);
      if(level==='1-level'&&learningIntroActions[state.unit].length) addCard(level,'알아보기','4g',learningIntroActions[state.unit]);
    });
  }

  function playLearningVideo(menu,state,level,courseId,action,courseTitle){
    if(!gpLoginInfo){
      handleGpLoginMenu();
      return;
    }
    const videoBox=menu.querySelector('.learning-video');
    const video=videoBox.querySelector('video');
    videoBox.querySelector('.learning-video-title').textContent=`${level==='1-level'?'1단계':'2단계'} ${state.unit}호 - ${courseTitle} ${action.name}`;
    video.querySelector('source').src=`/assets/simple/videos/${level}/${learningUnits[state.unit]}/${courseId}_${action.id}.mp4`;
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

  document.querySelectorAll('.video-learning-menu').forEach(initVideoLearningMenu);

  function goTo(id){
    document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
    document.querySelectorAll('nav.main-nav').forEach(nav=>nav.hidden = false);
    window.scrollTo(0, 0);
    const page = document.getElementById(id);
    page.classList.add('active');
    const utilityMenu = document.getElementById('gpUtilityMenu');
    utilityMenu.hidden = id === 'page-main';
    if(!utilityMenu.hidden){
      const header = page.querySelector('.site-header');
      const pageNav = header.querySelector('nav.main-nav');
      if(pageNav) pageNav.hidden = true;
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
