<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>글핀 - HNK 한중상용한자능력시험</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700;800;900&display=swap" rel="stylesheet">
<script src="../js/common.js"></script>
<style>
  :root{
    --lv0:#5DA831;  --lv0-tint:#e8f6d7;
    --lvA:#F3A12B;  --lvA-tint:#fdeecb;
    --lvB:#4CC3EE;  --lvB-tint:#d9f2fb;
    --lvT:#B7A8E8;  --lvT-tint:#ece6fa;
    --bg-default:#eef0f4;
    --panel-radius:18px;
  }

  *{box-sizing:border-box;}
  html,body{margin:0;padding:0;}

  body{
    font-family:'Noto Sans KR',sans-serif;
    -webkit-font-smoothing:antialiased;
    color:#222;
  }

  .page{
    min-height:100vh;
    background:var(--bg-default);
    padding:24px;
    display:flex;
    align-items:center;
  }

  .wrap{
    width:100%;
    max-width:95%;
    margin:0 auto;
    display:flex;
    flex-direction:column;
    gap:22px;
  }

  /* ---- rows ---- */
  .row{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:22px;
    align-items:stretch;
  }
  .row--bottom{align-items:stretch;}

  /* ============ HERO ============ */
  .hero{
    border-radius:var(--panel-radius);
    overflow:hidden;
    min-height:380px;
    background:linear-gradient(160deg,#c4e6a8,#92cf6c 55%,#5aa93c);
    background-size:cover;
    background-position:center;
    box-shadow:0 3px 14px rgba(0,0,0,.10);
    display:flex;
    align-items:center;
    justify-content:center;
    color:rgba(255,255,255,.92);
    text-align:center;
    padding:24px;
  }
  .hero__inner{display:flex;flex-direction:column;align-items:center;gap:14px;}
  #heroCover .hero__inner{display:none;}
  .hero__badge{
    font-size:40px;font-weight:900;letter-spacing:.04em;
    text-shadow:0 2px 6px rgba(0,0,0,.25);
  }
  .hero__sub{
    font-size:18px;font-weight:800;
    background:rgba(0,0,0,.18);padding:8px 20px;border-radius:999px;
  }
  .hero__note{font-size:14px;font-weight:700;opacity:.85;}

  /* ============ SELECTOR ============ */
  .selector{
    border-radius:var(--panel-radius);
    overflow:hidden;
    background:#FBF3D5;
    border:1px solid #ecdfa8;
    box-shadow:0 3px 14px rgba(0,0,0,.06);
    display:flex;flex-direction:column;
  }
  .selector__head{
    background:#F5E27A;padding:16px;text-align:center;
    font-size:22px;font-weight:900;color:#5a4d12;letter-spacing:-.01em;
  }
  .selector__body{
    padding:22px;flex:1;
    display:grid;grid-template-columns:1fr 1fr;gap:14px;
  }
  .col{display:flex;flex-direction:column;gap:14px;}

  /* level buttons */
  .lv{
    display:flex;align-items:center;justify-content:center;
    min-height:58px;border:none;border-radius:11px;
    color:#fff;font-size:19px;font-weight:800;cursor:pointer;
    font-family:inherit;letter-spacing:-.01em;
    box-shadow:0 2px 6px rgba(0,0,0,.12);
    transition:box-shadow .18s, transform .1s;
  }
  .lv:active{transform:scale(.98);}
  .lv:disabled{opacity:.45;cursor:not-allowed;filter:grayscale(.35);}
  .lv--0{background:var(--lv0);}
  .lv--A{background:var(--lvA);}
  .lv--B{background:var(--lvB);}
  .lv--T{background:var(--lvT);}
  .lv--0.is-active{box-shadow:0 0 0 4px #fff, 0 0 0 8px var(--lv0);}
  .lv--A.is-active{box-shadow:0 0 0 4px #fff, 0 0 0 8px var(--lvA);}
  .lv--B.is-active{box-shadow:0 0 0 4px #fff, 0 0 0 8px var(--lvB);}
  .lv--T.is-active{box-shadow:0 0 0 4px #fff, 0 0 0 8px var(--lvT);}

  /* controls */
  .ctrl{
    display:flex;align-items:center;min-height:58px;
    background:#fff;border:1px solid #e6dcb8;border-radius:12px;
  }
  .ctrl--ebook{
    justify-content:center;gap:10px;cursor:pointer;
    background:#ECE6FA;border:2px solid #C9BCEF;border-radius:999px;
    transition:transform .1s;
    text-decoration:none;
    position:relative;
  }
  .ctrl--ebook:active{transform:scale(.98);}
  .ebook__t{
    width:34px;height:34px;border-radius:999px;background:#8E5BE0;color:#fff;
    display:flex;align-items:center;justify-content:center;
    font-weight:900;font-size:16px;border:2px solid #F4A9D0;
  }
  .ebook__label{color:#6B4BA8;font-weight:800;letter-spacing:.05em;}

  .ctrl--vol{gap:10px;padding:0 14px;}
  .ctrl--vol .lab{font-weight:800;color:#6a6a6a;}
  .ctrl--vol select{
    flex:1;min-height:38px;border-radius:8px;border:1.5px solid #d6cdb0;
    background:#fff;padding:0 10px;font-weight:700;color:#333;
    font-family:inherit;font-size:15px;cursor:pointer;
  }
  .ctrl--vol option:not(:disabled){
    color:#003f9e;
    font-weight:900;
  }
  .ctrl--vol option:disabled{
    background:#ededed;
    color:#9a9a9a;
    font-weight:800;
  }
  .ctrl--vol option:checked{
    background:#0057d9;
    color:#fff;
    font-weight:900;
  }

  .ctrl--icons{justify-content:center;gap:30px;}
  .ctrl--icons svg{width:34px;height:34px;cursor:pointer;}
  .ctrl--icons a,.ctrl--icons button{
    display:inline-flex;align-items:center;justify-content:center;
    padding:0;border:0;background:transparent;cursor:pointer;
  }

  .ctrl--spacer{background:transparent;border:none;}
  .selector__actions{display:flex;flex-direction:column;gap:14px;--action-bg:#fff;}
  .selector__actions .ctrl--ebook,
  .selector__actions .ctrl--vol,
  .selector__actions .ctrl--icons,
  .selector__actions .ctrl--review{background:var(--action-bg);border-color:var(--action-bg);}
  .selector__actions .ctrl--review{
    justify-content:center;
    min-height:58px;
    text-decoration:none;
  }
  .selector__actions .ctrl--review.is-hidden{display:none;}
  .selector__actions .ctrl--review img{width:90px;height:45px;}

  /* ============ SECTION PANELS ============ */
  .panel{
    border-radius:var(--panel-radius);overflow:hidden;background:#fff;
    border:1px solid #ece8f6;box-shadow:0 3px 14px rgba(0,0,0,.06);
    height:100%;
    display:flex;
    flex-direction:column;
  }
  .panel__head{
    background:var(--panel-head-bg,#B9A9EC);padding:14px;text-align:center;
    font-size:20px;font-weight:900;color:#3f2f72;
  }
  .panel__body{padding:24px;display:grid;grid-template-columns:repeat(3,1fr);gap:16px;flex:1;}

  /* video cards */
  .vcard{
    display:flex;flex-direction:column;justify-content:space-between;align-items:center;
    height:210px;border-radius:14px;padding:24px 12px;cursor:pointer;
    transition:transform .15s;text-decoration:none;
    position:relative;
  }
  .vcard:hover{transform:translateY(-4px);}
  .vcard--blue{background:#7FD0F5;}
  .vcard--purple{background:#9489E2;}
  .vcard--green{background:#84D88A;}
  .vcard__play{
    width:62px;height:62px;border-radius:999px;background:rgba(255,255,255,.35);
    display:flex;align-items:center;justify-content:center;
  }
  .vcard__label{color:#fff;font-weight:800;font-size:23px;text-align:center;line-height:1.2;}

  /* game cards */
  .panel__body--game{gap:12px;}
  .gcard{
    display:flex;flex-direction:column;align-items:center;gap:14px;
    padding:14px 4px;border-radius:14px;cursor:pointer;transition:transform .15s;
    color:inherit;text-decoration:none;
    position:relative;
  }
  .gcard:hover{transform:translateY(-4px);}
  .gcard__label{font-weight:800;font-size:22px;color:#333;}

  .gicon{position:relative;width:120px;height:96px;}
  .gicon .tile{
    position:absolute;width:64px;height:80px;border-radius:11px;
    background:#EDE7FB;border:3px solid #B7A8E8;
    display:flex;align-items:center;justify-content:center;font-size:26px;
  }
  .gicon .tile--a{left:6px;top:0;color:#B7A8E8;}
  .gicon .tile--b{right:4px;bottom:0;color:#E94FB0;}

  .gmemo{
    display:grid;grid-template-columns:repeat(2,40px);grid-template-rows:repeat(2,40px);
    gap:7px;width:96px;height:96px;align-content:center;justify-content:center;
  }
  .gmemo div{
    border-radius:9px;display:flex;align-items:center;justify-content:center;
    color:#fff;font-size:19px;
  }
  .gmemo .o{background:#F3A12B;}
  .gmemo .g{background:#84D88A;}

  .gcard--off{opacity:.62;cursor:not-allowed;}
  .gcard--off:hover{transform:none;}
  .gcard--off{pointer-events:none;}
  #linkGamePaza .gicon .sq{position:absolute;width:40px;height:40px;border-radius:9px;background:#dcdcdc;}
  .sq--tl{left:8px;top:2px;}.sq--tr{right:8px;top:2px;}
  .sq--bl{left:8px;bottom:2px;}.sq--br{right:8px;bottom:2px;}
  .sq--c{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);
    width:46px;height:46px;border-radius:9px;background:#c6c6c6;border:3px solid #fff;}
  .gcard--off .gcard__label{color:#9a9a9a;}
  #linkGamePaza:not(.gcard--off) .gcard__label{color:#333;}
  .disabled-link{pointer-events:none;opacity:.45;filter:grayscale(1);}
  .badge-count{
    position:absolute;right:-8px;top:-8px;min-width:20px;height:20px;
    border-radius:999px;background:#e74c3c;color:#fff;font-size:12px;
    display:none;align-items:center;justify-content:center;padding:0 5px;
    font-weight:900;
  }
  .click-wrap{position:relative;display:inline-flex;}

  /* ============ RESPONSIVE ============ */
  @media (max-width:900px){
    .page{padding:16px;}
    .row{grid-template-columns:1fr;}
    .hero{min-height:280px;}
  }
  @media (max-width:560px){
    .selector__body{grid-template-columns:1fr;}
    .ctrl--spacer{display:none;}
    .panel__body{grid-template-columns:1fr 1fr;}
    .vcard{height:170px;}
    .vcard__label{font-size:20px;}
    .selector__head,.hero__badge{font-size:30px;}
  }
  @media (max-width:380px){
    .panel__body{grid-template-columns:1fr;}
  }
</style>
</head>
<body>
  <div class="page" id="page">
  <div class="wrap">

    <!-- TOP ROW -->
    <div class="row">

      <!-- HERO -->
      <div class="hero" id="heroCover">
        <div class="hero__inner">
          <div class="hero__badge">글핀</div>
          <div class="hero__sub">HNK 한중상용한자능력시험</div>
          <div class="hero__note">중국교육부 공인 · 글핀 × HNK</div>
        </div>
      </div>

      <!-- SELECTOR -->
      <div class="selector">
        <div class="selector__head">글핀 교재 선택</div>
        <div class="selector__body">

          <div class="col">
            <button class="lv lv--0" data-level="0">글핀 0</button>
            <button class="lv lv--A" data-level="A">글핀 A</button>
            <button class="lv lv--B" data-level="B">글핀 B</button>
            <button class="lv lv--T" data-level="T">글핀 T</button>
          </div>

          <div class="col selector__actions" id="selectorActions">
            <a class="ctrl ctrl--ebook" id="linkEbook" href="#">
              <span class="ebook__t" id="ebookMark">T</span>
              <span class="ebook__label">E-Book</span>
              <span class="badge-count" id="badgeEbook">0</span>
            </a>

            <div class="ctrl ctrl--vol">
              <span class="lab">Vol:</span>
              <select id="volumeSelect"></select>
            </div>

            <div class="ctrl ctrl--icons">
              <a href="../index.php" aria-label="홈">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="#8E5BE0"><path d="M12 3l9 8h-2.4v9.2h-4.2v-5.6h-2.8v5.6H5.4V11H3z"></path></svg>
              </a>
              <button id="playBtn" type="button" aria-label="음원 재생">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="#333"><path d="M7 4.5l13 7.5-13 7.5z"></path></svg>
              </button>
              <button id="muteBtn" type="button" aria-label="음소거">
                <svg width="26" height="26" viewBox="0 0 24 24"><path d="M4 9v6h3.5L13 20V4L7.5 9H4z" fill="#8E5BE0"></path><path d="M16.5 8.5a5 5 0 010 7" stroke="#8E5BE0" stroke-width="2" fill="none" stroke-linecap="round"></path></svg>
              </button>
            </div>

            <a class="ctrl ctrl--review" id="linkReview" href="#">
              <img src="../assets/portal_image/review.png" alt="복습영상">
            </a>
          </div>

        </div>
      </div>
    </div>

    <!-- BOTTOM ROW -->
    <div class="row row--bottom">

      <!-- VIDEOS -->
      <div class="panel">
        <div class="panel__head">영상</div>
        <div class="panel__body">
          <a class="vcard vcard--blue" id="linkVideoLearn" href="#">
            <span class="vcard__play"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M8 5l11 7-11 7z"></path></svg></span>
            <span class="vcard__label">한자송</span>
            <span class="badge-count" id="badgeVideoLearn">0</span>
          </a>
          <a class="vcard vcard--purple" id="linkVideoQuiz" href="#">
            <span class="vcard__play"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M8 5l11 7-11 7z"></path></svg></span>
            <span class="vcard__label">학습영상</span>
            <span class="badge-count" id="badgeVideoQuiz">0</span>
          </a>
          <a class="vcard vcard--green" id="linkVideoCard" href="#">
            <span class="vcard__play"><svg width="24" height="24" viewBox="0 0 24 24" fill="#fff"><path d="M8 5l11 7-11 7z"></path></svg></span>
            <span class="vcard__label">사자성어</br>인성동화</span>
            <span class="badge-count" id="badgeVideoCard">0</span>
          </a>
        </div>
      </div>

      <!-- GAMES -->
      <div class="panel">
        <div class="panel__head">게임</div>
        <div class="panel__body panel__body--game">

          <a class="gcard" id="linkGameMatching" href="#">
            <div class="gicon">
              <div class="tile tile--a">&#9829;</div>
              <div class="tile tile--b">&#9829;</div>
            </div>
            <div class="gcard__label">매칭게임</div>
            <span class="badge-count" id="badgeGameMatching">0</span>
          </a>

          <a class="gcard" id="linkGameMemory" href="#">
            <div class="gmemo">
              <div class="o">&#9829;</div>
              <div class="g">&#10047;</div>
              <div class="g">&#10047;</div>
              <div class="o">&#9829;</div>
            </div>
            <div class="gcard__label">메모리게임</div>
            <span class="badge-count" id="badgeGameMemory">0</span>
          </a>

          <a class="gcard gcard--off" id="linkGamePaza" href="#">
            <div class="gmemo">
              <div class="o">&#9829;</div>
              <div class="g">&#10047;</div>
              <div class="g">&#10047;</div>
              <div class="o">&#9829;</div>
            </div>
            <div class="gcard__label">파자게임</div>
            <span class="badge-count" id="badgeGamePaza">0</span>
          </a>

        </div>
      </div>
    </div>

  </div>
  </div>
  <audio id="audio"></audio>

<script>
  (function () {
    'use strict';

    /* ============================================================
       Constants & configuration
       ============================================================ */
    var API_URL = 'api.php';
    var DEFAULT_BG = '#eef0f4';

    var LEVELS = ['0', 'A', 'B', 'T'];          // 선택 가능한 교재 레벨
    var PAZA_ENABLED_LEVEL = 'B';               // 파자게임이 열리는 레벨

    // 레벨별 페이지 배경(틴트) 색상
    var LEVEL_TINTS = {
      '0': '#e8f6d7',
      'A': '#fdeecb',
      'B': '#d9f2fb',
      'T': '#ece6fa'
    };

    var LEVEL_COLORS = {
      '0': '#5DA831',
      'A': '#F3A12B',
      'B': '#4CC3EE',
      'T': '#B7A8E8'
    };

    // 일반 레벨용 볼륨 옵션(v1~v12)
    var VOLUME_OPTIONS_DEFAULT = Array.from({ length: 12 }, function (_, i) {
      var value = 'v' + (i + 1);
      return { value: value, text: value };
    });

    // T 레벨용 볼륨 옵션(급수 표기)
    var VOLUME_OPTIONS_LEVEL_T = [
      { value: 'v1', text: '8급 T-1' }, { value: 'v2', text: '8급 T-2' },
      { value: 'v3', text: '8급 T-3' }, { value: 'v4', text: '8급 T-4' },
      { value: 'v5', text: '8급 T-5' }, { value: 'v6', text: '7급 T-1' },
      { value: 'v7', text: '7급 T-2' }, { value: 'v8', text: '7급 T-3' },
      { value: 'v9', text: '7급 T-4' }, { value: 'v10', text: '7급 T-5' }
    ];

    // 클릭 횟수 배지를 가진 모든 활동(영상/이북/게임)
    // recordKey: 서버 기록 및 카운트 응답에서 사용하는 키
    var ACTIVITIES = [
      { recordKey: 'w1_1', linkId: 'linkVideoLearn', badgeId: 'badgeVideoLearn' },
      { recordKey: 'w1_2', linkId: 'linkVideoQuiz',  badgeId: 'badgeVideoQuiz' },
      { recordKey: 'w1_3', linkId: 'linkEbook',      badgeId: 'badgeEbook' },
      { recordKey: 'w1_3_1', linkId: 'linkVideoCard', badgeId: 'badgeVideoCard' },
      { recordKey: 'w2_1', linkId: 'linkGameMatching', badgeId: 'badgeGameMatching' },
      { recordKey: 'w2_2', linkId: 'linkGameMemory',   badgeId: 'badgeGameMemory' },
      { recordKey: 'w2_3', linkId: 'linkGamePaza',     badgeId: 'badgeGamePaza' }
    ];

    /* ============================================================
       DOM references
       ============================================================ */
    var els = {
      page:         document.getElementById('page'),
      hero:         document.getElementById('heroCover'),
      volumeSelect: document.getElementById('volumeSelect'),
      levelButtons: document.querySelectorAll('.lv'),
      panelHeads:   document.querySelectorAll('.panel__head'),
      ebookMark:    document.getElementById('ebookMark'),
      ebookLink:    document.getElementById('linkEbook'),
      reviewLink:   document.getElementById('linkReview'),
      selectorActions: document.getElementById('selectorActions'),
      memoryCard:   document.getElementById('linkGameMemory'),
      pazaCard:     document.getElementById('linkGamePaza'),
      audio:        document.getElementById('audio'),
      playBtn:      document.getElementById('playBtn'),
      muteBtn:      document.getElementById('muteBtn')
    };

    var ICONS = {
      play: '<svg width="22" height="22" viewBox="0 0 24 24" fill="#333"><path d="M7 4.5l13 7.5-13 7.5z"></path></svg>',
      pause: '<svg width="22" height="22" viewBox="0 0 24 24" fill="#333"><path d="M6 5h4v14H6zM14 5h4v14h-4z"></path></svg>',
      volume: '<svg width="26" height="26" viewBox="0 0 24 24"><path d="M4 9v6h3.5L13 20V4L7.5 9H4z" fill="#8E5BE0"></path><path d="M16.5 8.5a5 5 0 010 7" stroke="#8E5BE0" stroke-width="2" fill="none" stroke-linecap="round"></path></svg>',
      muted: '<svg width="26" height="26" viewBox="0 0 24 24"><path d="M4 9v6h3.5L13 20V4L7.5 9H4z" fill="#8E5BE0"></path><path d="M17 9l4 4m0-4l-4 4" stroke="#8E5BE0" stroke-width="2" fill="none" stroke-linecap="round"></path></svg>'
    };

    /* ============================================================
       Session (common.js 연동) & state
       ============================================================ */
    var user = safeCall('getUser') || '';
    var role = safeCall('getRole') || '';

    if (!user) {
      window.location.href = '../index.php';
      return;
    }

    var savedState = loadState();
    var currentLevel  = savedState.stp || normalizeLevel(safeCall('getStep')) || 'T';
    var currentVolume = savedState.vol || 'v1';
    var userSettings = {
      status: 'all',
      activeMonths: []
    };
    var allowedLevels = LEVELS.slice();

    /* ============================================================
       Helpers
       ============================================================ */
    // common.js의 전역 함수를 안전하게 호출(없거나 예외면 빈 문자열)
    function safeCall(fnName) {
      try {
        return typeof window[fnName] === 'function' ? window[fnName]() : '';
      } catch (e) {
        return '';
      }
    }

    // 임의 문자열에서 마지막 글자를 레벨(0/A/B/T)로 정규화
    function normalizeLevel(value) {
      if (!value) return '';
      var last = String(value).slice(-1).toUpperCase();
      return LEVELS.indexOf(last) >= 0 ? last : '';
    }

    function storageKey() {
      return 'hanmunwang:' + (user || 'guest');
    }

    function loadState() {
      try {
        return JSON.parse(localStorage.getItem(storageKey())) || {};
      } catch (e) {
        return {};
      }
    }

    function saveState() {
      localStorage.setItem(storageKey(), JSON.stringify({
        stp: currentLevel,
        vol: currentVolume
      }));
    }

    function addLevelsFrom(value, target) {
      if (!value) return;
      value = String(value);
      LEVELS.forEach(function (level) {
        if (value.indexOf(level) >= 0 && target.indexOf(level) < 0) target.push(level);
      });
    }

    function getAllowedLevels() {
      var levels = [];
      var classValue = safeCall('getClass');
      var stepValue = safeCall('getStep');

      if (role === '30' || role === '1' || role === '2') {
        if ((!classValue || classValue === 'undefined') && role === '30') addLevelsFrom(stepValue, levels);
        addLevelsFrom(classValue, levels);
      } else {
        if (stepValue) addLevelsFrom(stepValue, levels);
        else addLevelsFrom(classValue, levels);
      }

      return levels.length ? levels : LEVELS.slice();
    }

    function isLevelAllowed(level) {
      return allowedLevels.indexOf(level) >= 0;
    }

    async function getUserSettings(id) {
      try {
        var response = await fetch(API_URL + '?mode=get&id=' + encodeURIComponent(id));
        var result = await response.json();

        if (result.status === 'success') {
          return {
            status: result.data.status,
            activeMonths: result.data.mon ? result.data.mon.split(',') : []
          };
        }

        throw new Error(result.message);
      } catch (error) {
        console.error('데이터 조회 중 오류 발생:', error);
        return {
          status: 'all',
          activeMonths: []
        };
      }
    }

    function getMonthVolumes(targetMonth) {
      var monthToV = {
        3:'v1',  4:'v2',  5:'v3',  6:'v4',
        7:'v5',  8:'v6',  9:'v7', 10:'v8',
        11:'v9', 12:'v10', 1:'v11', 2:'v12'
      };

      var months = targetMonth == null ? [(new Date().getMonth() + 1)]
        : Array.isArray(targetMonth) ? targetMonth
        : [targetMonth];

      var activeSet = new Set();
      months.forEach(function (m) {
        m = Number(m);
        if (monthToV[m]) activeSet.add(monthToV[m]);

        var prev = m === 1 ? 12 : m - 1;
        if (monthToV[prev]) activeSet.add(monthToV[prev]);
      });

      return activeSet;
    }

    function getActiveVolumes(targetMonth) {
      var allVolumes = VOLUME_OPTIONS_DEFAULT.map(function (opt) {
        return opt.value;
      });
      var settings = userSettings || { status: 'all', activeMonths: [] };
      var status = settings.status;
      var activeMonths = settings.activeMonths || [];

      if (user === 'chaitalk') return new Set(allVolumes);
      if (status === 'none') return new Set();

      if (status === 'partial') {
        var currentMonth = (new Date().getMonth() + 1).toString();
        if (activeMonths.indexOf(currentMonth) >= 0) return new Set(allVolumes);

        return new Set(activeMonths.map(function (month) {
          return 'v' + month;
        }));
      }

      if (status === 'all') return getMonthVolumes(targetMonth == null ? [6,7] : targetMonth);

      return new Set(allVolumes);
    }

    function isVolumeDisabled(value) {
      return !getActiveVolumes().has(value);
    }

    function updateAudioButtons() {
      els.playBtn.innerHTML = els.audio.paused ? ICONS.play : ICONS.pause;
      els.playBtn.setAttribute('aria-label', els.audio.paused ? '음원 재생' : '음원 일시정지');
      els.muteBtn.innerHTML = els.audio.muted ? ICONS.muted : ICONS.volume;
      els.muteBtn.setAttribute('aria-label', els.audio.muted ? '음소거 해제' : '음소거');
    }

    /* ============================================================
       View: 볼륨 옵션 렌더링
       ============================================================ */
    function renderVolumeOptions() {
      var options = ((currentLevel === 'T') ? VOLUME_OPTIONS_LEVEL_T : VOLUME_OPTIONS_DEFAULT)
        .map(function (opt) {
          return {
            value: opt.value,
            text: opt.text,
            disabled: isVolumeDisabled(opt.value)
          };
        });
      var previousVolume = currentVolume;

      var fragment = document.createDocumentFragment();
      options.forEach(function (opt) {
        var optionEl = document.createElement('option');
        optionEl.value = opt.value;
        optionEl.textContent = opt.text;
        optionEl.disabled = opt.disabled;
        fragment.appendChild(optionEl);
      });

      els.volumeSelect.innerHTML = '';
      els.volumeSelect.appendChild(fragment);

      // 이전 선택값이 새 옵션 목록에 있으면 유지, 없으면 첫 번째로
      var hasPrevious = options.some(function (opt) {
        return opt.value === previousVolume && !opt.disabled;
      });
      var firstEnabled = options.find(function (opt) { return !opt.disabled; });
      currentVolume = hasPrevious ? previousVolume : (firstEnabled || options[0]).value;
      els.volumeSelect.value = currentVolume;
    }

    /* ============================================================
       Links: 영상 / 이북 / 게임 링크 구성
       ============================================================ */
    // 클릭 시 sessionStorage에 값을 저장하고 페이지 이동하는 링크
    function bindNavLink(el, pageUrl, sessionKey, sessionValue) {
      if (!el) return;
      el.href = '#';
      el.onclick = function (e) {
        e.preventDefault();
        sessionStorage.setItem(sessionKey, sessionValue);
        location.href = pageUrl;
      };
    }

    function buildEbookLink(weekNumber) {
      var level = currentLevel.toLowerCase();
      var bookKey = (level === '0')
        ? 'step_' + level + '.3_Fchar.v' + weekNumber
        : 'step_' + level + '.v' + weekNumber;
      bindNavLink(els.ebookLink, '../ebook/index.php', 'ebookBook', 'gulpin.' + bookKey);
      // bindNavLink(els.ebookLink, '../ebook_new_chaitalk/ebook.html', 'ebookBook', bookKey);
    }

    function buildGameLinks() {
      document.getElementById('linkGameMatching').href =
        '../game/matching-game/index.php?vol=' + currentVolume + '&step=' + currentLevel;
      document.getElementById('linkGameMemory').href =
        '../game/memory-game/index1.html?vol=' + currentVolume + '&clas=' + currentLevel;
      document.getElementById('linkGamePaza').href =
        '../game/paza-game/index.php?vol=' + currentVolume + '&step=' + currentLevel;
    }

    function updatePazaState() {
      var enabled = (currentLevel === PAZA_ENABLED_LEVEL);
      els.pazaCard.classList.toggle('gcard--off', !enabled);
      els.pazaCard.setAttribute('aria-disabled', enabled ? 'false' : 'true');
      els.pazaCard.tabIndex = enabled ? 0 : -1;
    }

    function updateMemoryState() {
      var enabled = (currentLevel !== '0');
      els.memoryCard.classList.toggle('gcard--off', !enabled);
      els.memoryCard.setAttribute('aria-disabled', enabled ? 'false' : 'true');
      els.memoryCard.tabIndex = enabled ? 0 : -1;
    }

    // 현재 레벨/볼륨에 맞춰 모든 링크, 배경, 오디오, 상태 갱신
    function refreshLinks() {
      var level = currentLevel.toLowerCase();
      var weekNumber = currentVolume.substring(1);   // 'v3' -> '3'
      var mediaPrefix = level + weekNumber;           // 예: 'b3'

      bindNavLink(document.getElementById('linkVideoLearn'), 'videoplay.html',  'videoSrc', mediaPrefix + '/1.mp4');
      bindNavLink(document.getElementById('linkVideoQuiz'),  'videoplay2.html', 'videoSrc', mediaPrefix + '/2.mp4');
      bindNavLink(document.getElementById('linkVideoCard'),  'videoplay2.html', 'videoSrc', mediaPrefix + '/3.mp4');
      bindNavLink(els.reviewLink, 'videoplay2.html', 'videoSrc', 'review_' + level + '/' + level + weekNumber + '_study_review.mp4');

      buildEbookLink(weekNumber);
      buildGameLinks();

      els.audio.src = currentLevel + '.mp3';
      els.hero.style.backgroundImage =
        'linear-gradient(160deg, rgba(0,0,0,.12), rgba(0,0,0,.05)), ' +
        'url("../assets/portal_image/' + mediaPrefix + '.jpg")';

      els.ebookMark.textContent = currentLevel;
      els.page.style.background = LEVEL_TINTS[currentLevel] || DEFAULT_BG;
      els.selectorActions.style.setProperty('--action-bg', LEVEL_COLORS[currentLevel] || '#fff');
      els.panelHeads.forEach(function (head) {
        head.style.setProperty('--panel-head-bg', LEVEL_COLORS[currentLevel] || '#B9A9EC');
      });
      els.reviewLink.classList.toggle('is-hidden', currentLevel !== 'B');

      var ebookLocked = (String(role) === '0' || String(role) === '30');
      els.ebookLink.classList.toggle('disabled-link', ebookLocked);

      updateMemoryState();
      updatePazaState();
    }

    /* ============================================================
       Level selection
       ============================================================ */
    function selectLevel(nextLevel) {
      if (!isLevelAllowed(nextLevel)) return;
      currentLevel = nextLevel;
      els.levelButtons.forEach(function (btn) {
        btn.classList.toggle('is-active', btn.getAttribute('data-level') === currentLevel);
      });
      renderVolumeOptions();
      refreshLinks();
      saveState();
    }

    function applyLevelButtonState() {
      els.levelButtons.forEach(function (btn) {
        btn.disabled = !isLevelAllowed(btn.getAttribute('data-level'));
      });
    }

    /* ============================================================
       Records & badges
       ============================================================ */
    function setBadge(badgeId, count) {
      var el = document.getElementById(badgeId);
      if (!el) return;
      el.textContent = count;
      el.style.display = count > 0 ? 'flex' : 'none';
    }

    function postRecord(recordKey) {
      fetch(API_URL + '?mode=record', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          id: user,
          step: currentLevel,
          volume: currentVolume,
          uid: recordKey
        })
      }).catch(function () {});
    }

    function initRecordBadges() {
      var counts = {};

      // 서버에서 초기 카운트 로드
      fetch(API_URL + '?mode=record_count&id=' + encodeURIComponent(user))
        .then(function (res) { return res.json(); })
        .then(function (res) {
          if (res.status !== 'success') return;
          ACTIVITIES.forEach(function (activity) {
            counts[activity.recordKey] = res.data[activity.recordKey] || 0;
            setBadge(activity.badgeId, counts[activity.recordKey]);
          });
        })
        .catch(function () {});

      // 각 활동 클릭 시 카운트 증가 + 서버 기록
      ACTIVITIES.forEach(function (activity) {
        var el = document.getElementById(activity.linkId);
        if (!el) return;
        el.addEventListener('click', function (e) {
          if (el.classList.contains('gcard--off') || el.classList.contains('disabled-link')) {
            e.preventDefault();
            return;
          }
          counts[activity.recordKey] = (counts[activity.recordKey] || 0) + 1;
          setBadge(activity.badgeId, counts[activity.recordKey]);
          postRecord(activity.recordKey);
        });
      });
    }

    /* ============================================================
       Event bindings
       ============================================================ */
    els.levelButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        selectLevel(btn.getAttribute('data-level'));
      });
    });

    // 비활성화된 게임 카드 클릭 차단
    [els.memoryCard, els.pazaCard].forEach(function (card) {
      card.addEventListener('click', function (e) {
        if (this.classList.contains('gcard--off')) {
          e.preventDefault();
          e.stopPropagation();
        }
      });
    });

    els.volumeSelect.addEventListener('change', function () {
      currentVolume = els.volumeSelect.value;
      refreshLinks();
      saveState();
    });

    els.playBtn.addEventListener('click', function () {
      if (els.audio.paused) {
        var playPromise = els.audio.play();
        if (playPromise && typeof playPromise.catch === 'function') {
          playPromise.catch(function () {
            updateAudioButtons();
          });
        }
      } else {
        els.audio.pause();
      }
      updateAudioButtons();
    });

    els.muteBtn.addEventListener('click', function () {
      els.audio.muted = !els.audio.muted;
      updateAudioButtons();
    });

    els.audio.addEventListener('ended', updateAudioButtons);
    els.audio.addEventListener('pause', updateAudioButtons);
    els.audio.addEventListener('play', updateAudioButtons);

    /* ============================================================
       Init
       ============================================================ */
    async function init() {
      userSettings = await getUserSettings(user);
      allowedLevels = getAllowedLevels();
      applyLevelButtonState();
      if (!isLevelAllowed(currentLevel)) currentLevel = allowedLevels[0] || currentLevel;
      selectLevel(currentLevel);   // renderVolumeOptions + refreshLinks 포함
      updateAudioButtons();
      initRecordBadges();
    }

    init();
  })();
</script>
</body>
</html>
