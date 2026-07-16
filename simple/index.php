<?php
const SIMPLE_IMAGE_PATH = '/assets/simple/images';
const SIMPLE_VIDEO_PATH = '/assets/simple/videos';
?>
<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글핀 - 한자 학습</title>
    <link href="https://fonts.googleapis.com/css2?family=Nanum+Gothic:wght@400;700;800&family=Jua&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="google" content="notranslate">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            min-height: 100vh;
        }

        body {
            font-family: 'Nanum Gothic', sans-serif;
            background-color: #fff4ce;
            background-image: url('<?= SIMPLE_IMAGE_PATH ?>/potal_land.jpg');
            background-size: 100% 100%;
            background-position: center top;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
        }

        .visitor-strip {
            min-height: 48px;
            padding: 6px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            background: #1f5233;
            color: #eafff0;
            font-size: 14px;
        }

        .visitor-strip .strip-logo { margin-right: auto; font-size: 20px; }
        .visitor-strip .pill { padding: 4px 14px; border-radius: 999px; background: rgba(255,255,255,.15); font-weight: 700; }
        .site-header { min-height: 64px; padding: 14px 40px; display: flex; align-items: center; justify-content: space-between; background: linear-gradient(180deg,#3da35d,#2e7d46); }
        .site-header a { color: rgba(255,255,255,.65); text-decoration: none; font-weight: 700; }
        .site-header .home-link { color: #fff; font-size: 18px; }
        .site-header nav { display: flex; align-items: center; gap: 42px; }
        .utility-nav { display: flex; align-items: center; gap: 18px; margin-left: auto; }
        .utility-nav a { color: #8a9184; text-decoration: none; font-weight: 700; cursor: pointer; }

        @media (max-width: 767px) {
            body {
                background-image: url('<?= SIMPLE_IMAGE_PATH ?>/potal_port.jpg');
            }
            #banner {
                height: 0.8rem;
            }
        }

        /* Top Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: #FDF9F6;
            font-size: 16px;
        }

        .top-bar .left {
            color: #333;
            font-weight: 600;
        }

        .utility-nav .right {
            color: #fff;
            padding: 8px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: bolder;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .utility-nav .right:hover {
            background: rgba(255,255,255,.12);
            transform: translateY(-1px);
        }

        .utility-nav .right::before {
            content: '🔒';
            font-size: 14px;
        }

        .utility-nav .right.logged-in::before {
            content: '👤';
        }

        /* Back Button */
        /* .back-button {
            position: absolute;
            left: 20px;
            top: 80px;
            width: 40px;
            height: 40px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            color: #666;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        } */

        /* .back-button:hover {
            background: #f5f5f5;
        } */

        /* Main Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 100%;
        }

        /* Level Buttons */
        .level-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin: 25px 0;
        }

        .level-btn {
            padding: 12px 50px;
            font-size: 18px;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            background: white;
            color: gray;
            font-family: 'Jua', sans-serif;
            transition: all 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .level-btn:hover:not(:disabled) {
            background: #1A7A3A;
            transform: translateY(-2px);
        }

        .level-btn.active {
            background: #1A7A3A;
            color: white;
        }

        .level-btn:disabled {
            background: #cccccc;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Unit Selection */
        .unit-selection {
            text-align: center;
            margin: 20px 0;
            color: #999;
            font-size: 16px;
        }

        .unit-label {
            display: inline-block;
            margin-right: 15px;
        }

        .unit-buttons {
            display: inline-flex;
            gap: 20px;
        }

        .unit-btn {
            background: white;
            border: none;
            border-radius: 50%;
            color: #999;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.2s;
        }

        .unit-btn:hover:not(:disabled) {
            color: #555;
            transform: scale(1.15);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
        }

        .unit-btn.active {
            background: #0B3819;
            color: white;
            box-shadow: 0 4px 12px rgba(11, 56, 25, 0.5);
        }

        .unit-btn:disabled {
            background: #e0e0e0;
            color: #bbb;
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Course Cards */
        .course-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 30px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .course-card {
            background: white;
            border-radius: 30px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
            transition: all 0.2s;
        }

        .course-header {
            background: #61BF4D;
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 20px;
            font-family: 'Jua', sans-serif;
        }

        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .action-btn {
            background: #ffffff;
            color: #555555;
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .action-btn:hover:not(:disabled) {
            background: #f5f5f5;
            transform: scale(1.03);
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
        }

        .action-btn:disabled {
            background: #e0e0e0;
            color: #999999;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Video Player Section */
        .video-section {
            display: none;
            max-width: 1200px;
            margin: 30px auto;
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .video-section.active {
            display: block;
        }

        .video-header {
            background: #1a1a1a;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }

        .video-title {
            font-size: 16px;
            font-weight: 600;
        }

        .close-video-btn {
            background: #ff4757;
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .close-video-btn:hover {
            background: #e84118;
            transform: rotate(90deg);
        }

        #videoPlayer {
            width: 100%;
            height: auto;
            max-height: 70vh;
            display: block;
            background: #000;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .course-container {
                grid-template-columns: repeat(2, 1fr) !important;
            }
        }

        @media (max-width: 768px) {
            .course-container {
                grid-template-columns: 1fr !important;
            }

            .container {
                position: relative;
                top: auto;
                left: auto;
                transform: none;
                width: 100%;
            }

            .level-buttons {
                flex-direction: row;
                flex-wrap: wrap;
                gap: 10px;
            }

            .level-btn {
                flex: 1;
                min-width: 80px;
                padding: 10px 15px;
                font-size: 15px;
            }

            .unit-buttons {
                flex-wrap: wrap;
                justify-content: center;
            }

            .top-bar {
                padding: 15px 20px;
                font-size: 14px;
            }

            .site-header { padding: 12px 16px; align-items: flex-start; gap: 12px; }
            .site-header nav { gap: 10px; flex-wrap: wrap; justify-content: flex-end; }
            .utility-nav { gap: 12px; flex-wrap: wrap; justify-content: flex-end; }

            /* .back-button {
                left: 10px;
                top: 70px;
            } */

            .login-modal {
                align-items: flex-start;
            }

            .login-box {
                margin-top: 20%;
            }
        }

        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #555; }

        /* Login Modal */
        .login-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .login-modal.active {
            display: flex;
        }

        .login-box {
            background: white;
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            width: 90%;
            max-width: 400px;
            position: relative;
            overflow: hidden;
        }

        .login-image {
            width: 40%;
            display: block;
            margin: 20px auto 0;
        }

        .login-body {
            padding: 30px 40px 40px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            font-size: 14px;
        }

        .form-input {
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.2s;
        }

        .form-input:focus {
            outline: none;
            border-color: #1DD1A1;
        }

        .login-btn-submit {
            color: white;
            border: none;
            padding: 14px;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 10px;
            font-family: 'Jua', sans-serif;
        }

        .login-btn-submit:hover {
            background: linear-gradient(135deg, #10ac84, #1DD1A1);
            transform: translateY(-2px);
        }

        .close-login-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #f5f5f5;
            border: none;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 20px;
            color: #666;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .close-login-btn:hover {
            background: #e0e0e0;
            transform: rotate(90deg);
        }

        .error-message {
            color: #ff4757;
            font-size: 13px;
            text-align: center;
            margin-top: 10px;
            display: none;
        }

        .error-message.show {
            display: block;
        }
    </style>
</head>

<body>
    <div class="visitor-strip">
        <span class="strip-logo">▣</span>
        <span class="pill">글핀</span>
        <span id="idUserName">방문자님 환영합니다</span>
    </div>
    <header class="site-header">
        <a class="home-link" href="../welcome.php">🏠 Home</a>
        <nav class="utility-nav">
            <a id="simpleBoardMenu" href="../board/boardmgr.php" style="display:none;">게시판</a>
            <a id="simpleBranchMenu" href="../purchase/branchmgr.php" style="display:none;">지사관리</a>
            <div class="right" onclick="openLogin()">로그인</div>
        </nav>
    </header>

    <!-- Back Button -->
    <!-- <div class="back-button" onclick="goBack()">&#8249;</div> -->

    <!-- Main Container -->
    <div class="container">
        <!-- Level Selection -->
        <div class="level-buttons">
            <button class="level-btn" onclick="selectLevel('all')">전체</button>
            <button class="level-btn" onclick="selectLevel('1-level')">1단계</button>
            <button class="level-btn" onclick="selectLevel('2-level')">2단계</button>
        </div>

        <!-- Unit Selection -->
        <div class="unit-selection">
            <span class="unit-label">호수:</span>
            <div class="unit-buttons" id="unitButtons">
                <button class="unit-btn" onclick="selectUnit('A')">A호</button>
                <button class="unit-btn" onclick="selectUnit('B')">B호</button>
                <button class="unit-btn" onclick="selectUnit('C')">C호</button>
                <button class="unit-btn" onclick="selectUnit('D')">D호</button>
                <button class="unit-btn" onclick="selectUnit('E')">E호</button>
                <button class="unit-btn" onclick="selectUnit('F')">F호</button>
            </div>
        </div>

        <!-- Video Section -->
        <div class="video-section" id="videoSection">
            <div class="video-header">
                <div class="video-title" id="videoTitle">동영상 재생</div>
                <button class="close-video-btn" onclick="closeVideo()">×</button>
            </div>
            <video
                id="videoPlayer"
                controls
                controlsList="nodownload"
                disablePictureInPicture
                oncontextmenu="return false;">
                <source id="videoSource" src="" type="video/mp4">
                동영상을 재생할 수 없습니다.
            </video>
        </div>

        <!-- Course Cards -->
        <div class="course-container" id="courseContainer"></div>
    </div>

    <!-- Login Modal -->
    <div id="loginModal" class="login-modal">
        <div class="login-box">
            <button class="close-login-btn" onclick="closeLogin()">×</button>
            <img src="<?= SIMPLE_IMAGE_PATH ?>/login.png" alt="로그인" class="login-image">
            <div class="login-body">
                <form id="logForm" class="login-form" onsubmit="handleLogin(event)">
                    <div class="form-group">
                        <label class="form-label">아이디</label>
                        <input type="text" id="loginId" class="form-input" placeholder="아이디를 입력하세요" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">비밀번호</label>
                        <input type="password" id="loginPassword" class="form-input" placeholder="비밀번호를 입력하세요" required>
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; margin-bottom:10px;">
                        <input type="checkbox" id="chkSaveId" style="width:16px; height:16px; cursor:pointer;">
                        <label for="chkSaveId" style="font-size:13px; color:#555; cursor:pointer; margin:0;">아이디 저장</label>
                    </div>
                    <button type="submit" class="login-btn-submit" style="background-color: #1A7A3A;">로그인</button>
                    <div id="errorMessage" class="error-message">
                        아이디 또는 비밀번호가 올바르지 않습니다.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/common.js?v=<?= filemtime('../js/common.js') ?>"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script>
        let currentMode = '';
        let currentUnit = '';
        let isLoggedIn = false;
        const SIMPLE_IMAGE_PATH = '<?= SIMPLE_IMAGE_PATH ?>';
        const SIMPLE_VIDEO_PATH = '<?= SIMPLE_VIDEO_PATH ?>';

        // 알아보기 버튼 설정 (1단계 전용)
        const level1Alabogi = {
            'A': [{ id: 'number', name: '숫자학습영상' }],
            'B': [{ id: 'study', name: '주제별 부수 학습영상' }, { id: 'song1', name: '한자송1' }, { id: 'song2', name: '한자송2' }],
            'C': [{ id: 'study', name: '주제별 부수 학습영상' }, { id: 'song1', name: '한자송1' }, { id: 'song2', name: '한자송2' }],
            'D': [{ id: 'study', name: '주제별 부수 학습영상' }, { id: 'song1', name: '한자송1' }, { id: 'song2', name: '한자송2' }, { id: 'song3', name: '한자송3' }],
            'E': []
        };

        function updateMainBackground() {
            if (!isLoggedIn) return;
            document.body.style.backgroundImage = window.innerWidth <= 768
                ? `url('${SIMPLE_IMAGE_PATH}/main_port.jpg')`
                : `url('${SIMPLE_IMAGE_PATH}/main_land.jpg')`;
        }

        window.addEventListener('resize', updateMainBackground);

        function restoreSimpleLogin(){
            const info = getLocalStorage('infochaitalk');
            const isCurrent = info && info.loginAt && Date.now() - Number(info.loginAt) <= 8 * 60 * 60 * 1000;
            if(!isCurrent || (info.role !== '9' && info.role !== '7')) return;

            isLoggedIn = true;
            const loginBtn = document.querySelector('.utility-nav .right');
            loginBtn.textContent = '로그아웃';
            loginBtn.classList.add('logged-in');
            loginBtn.onclick = logout;
            document.getElementById('simpleBoardMenu').style.display = '';
            document.getElementById('simpleBranchMenu').style.display = '';
            document.getElementById('idUserName').textContent = (info.name || info.user) + '님 환영합니다!';
            document.getElementsByClassName('container')[0].style.display = 'block';
            updateMainBackground();
            enableAllButtons();
            applyLevelAccess(info.clas);
        }

        function openLogin() {
            document.getElementById('loginModal').classList.add('active');
            const savedId = localStorage.getItem('savedLoginId');
            if (savedId) {
                document.getElementById('loginId').value = savedId;
                document.getElementById('chkSaveId').checked = true;
            } else {
                document.getElementById('loginId').value = '';
                document.getElementById('chkSaveId').checked = false;
            }
            document.getElementById('loginPassword').value = '';
            document.getElementById('errorMessage').classList.remove('show');
        }

        function closeLogin() {
            document.getElementById('loginModal').classList.remove('active');
        }

        function handleLogin(event) {
            event.preventDefault();
            const param = new URLSearchParams(location.search).get('dest');
            const errorMessage = document.getElementById('errorMessage');
            const formData = new FormData();
            formData.append('Email', document.getElementById('loginId').value);
            formData.append('Password', document.getElementById('loginPassword').value);
            formData.append('functionName', 'Slogon');

            const dispList = (resp) => {
                if ('success' in resp) {
                    if ( resp.success[0].role !== '9' && resp.success[0].role !== '7' ) {
                        errorMessage.classList.add('show');
                        CallToast('어휘담 계정 로그인 해주세요!!', "error");
                        return;
                    }
                    isLoggedIn = true;
                    saveLocalStorage('infochaitalk', {
                        user: resp.success[0].user,
                        role: resp.success[0].role,
                        conf: resp.success[0].confirm,
                        name: resp.success[0].name,
                        owner: resp.success[0].owner,
                        loca: resp.success[0].location,
                        step: resp.success[0].step,
                        clas: resp.success[0].clas,
                        loginAt: Date.now()
                    });
                    const loginIdVal = document.getElementById('loginId').value;
                    if (document.getElementById('chkSaveId').checked) {
                        localStorage.setItem('savedLoginId', loginIdVal);
                    } else {
                        localStorage.removeItem('savedLoginId');
                    }
                    closeLogin();

                    const loginBtn = document.querySelector('.utility-nav .right');
                    loginBtn.textContent = '로그아웃';
                    loginBtn.classList.add('logged-in');
                    loginBtn.onclick = logout;
                    document.getElementById('simpleBoardMenu').style.display = '';
                    document.getElementById('simpleBranchMenu').style.display = '';

                    document.getElementsByClassName('container')[0].style.display = 'block';
                    document.getElementById('idUserName').textContent = resp.success[0].name + '님 환영합니다!';
                    updateMainBackground();
                    enableAllButtons();
                    applyLevelAccess(resp.success[0].clas);
                } else if ('falure' in resp) {
                    errorMessage.classList.add('show');
                    CallToast('Password empty or mismatch !!', "error");
                }
            };

            const dispErr = (xhr) => {
                errorMessage.classList.add('show');
                setTimeout(() => errorMessage.classList.remove('show'), 3000);
            };

            CallAjax1("SMethods.php?dest=" + param, "POST", formData, dispList, dispErr);
        }

        function logout() {
            isLoggedIn = false;
            deleteLocalStorage('infochaitalk');
            const loginBtn = document.querySelector('.utility-nav .right');
            loginBtn.textContent = 'LOGIN';
            loginBtn.classList.remove('logged-in');
            loginBtn.onclick = openLogin;
            document.getElementById('simpleBoardMenu').style.display = 'none';
            document.getElementById('simpleBranchMenu').style.display = 'none';
            document.getElementsByClassName('container')[0].style.display = 'none';
            document.getElementById('idUserName').textContent = '방문자님 환영합니다';
            document.body.style.backgroundImage = `url('${SIMPLE_IMAGE_PATH}/potal_land.jpg')`;
            disableAllButtons();
        }

        function enableAllButtons() {
            document.querySelectorAll('.level-btn, .unit-btn, .action-btn').forEach(btn => btn.disabled = false);
        }

        function disableAllButtons() {
            document.querySelectorAll('.level-btn, .unit-btn, .action-btn').forEach(btn => btn.disabled = true);
        }

        function applyLevelAccess(classValue) {
            const btns = document.querySelectorAll('.level-btn');
            // btns[0]=전체, btns[1]=1단계, btns[2]=2단계
            if (classValue === '전체') {
                btns.forEach(btn => btn.disabled = false);
                btns[0].click(); // 전체 자동 선택
            } else if (classValue === '1단계') {
                btns[0].disabled = true;
                btns[1].disabled = false;
                btns[2].disabled = true;
                btns[1].click(); // 1단계 자동 선택
            } else if (classValue === '2단계') {
                btns[0].disabled = true;
                btns[1].disabled = true;
                btns[2].disabled = false;
                btns[2].click(); // 2단계 자동 선택
            } else {
                btns.forEach(btn => btn.disabled = false);
            }
        }

        function selectLevel(mode, isInit = false) {
            if (!isInit && !isLoggedIn) {
                alert('로그인이 필요합니다.');
                openLogin();
                return;
            }

            currentMode = mode;

            if (!isInit) {
                document.querySelectorAll('.level-btn').forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
            } else {
                document.querySelectorAll('.level-btn')[0].classList.add('active');
            }

            // 모바일: 모든 모드 relative, 데스크탑: 전체=relative / 1·2단계=absolute 중앙
            const container = document.querySelector('.container');
            const isMobile = window.innerWidth <= 768;

            if (isMobile) {
                container.style.position = 'relative';
                container.style.top = 'auto';
                container.style.left = 'auto';
                container.style.transform = 'none';
                container.style.marginTop = '20px';
            } else if (mode === 'all') {
                container.style.position = 'relative';
                container.style.top = 'auto';
                container.style.left = 'auto';
                container.style.transform = 'none';
                container.style.marginTop = '150px';
            } else {
                container.style.position = 'absolute';
                container.style.top = '50%';
                container.style.left = '50%';
                container.style.transform = 'translate(-50%, -50%)';
                container.style.marginTop = '0px';
            }

            updateUnitButtons();
            generateCourseCards();
        }

        function updateUnitButtons() {
            document.querySelectorAll('.unit-btn').forEach((btn, index) => {
                // F호는 현재 표시 대상에서 제외
                btn.style.display = index >= 5 ? 'none' : 'inline-block';
            });

            if (currentUnit === 'F') {
                selectUnit('A', true);
            }
        }

        function selectUnit(unit, isInit = false) {
            if (!isInit && !isLoggedIn) {
                alert('로그인이 필요합니다.');
                openLogin();
                return;
            }

            currentUnit = unit;

            if (!isInit) {
                document.querySelectorAll('.unit-btn').forEach(btn => btn.classList.remove('active'));
                event.target.classList.add('active');
            } else {
                document.querySelectorAll('.unit-btn').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.unit-btn')[0].classList.add('active');
            }

            generateCourseCards();
        }

        function generateCourseCards() {
            const container = document.getElementById('courseContainer');
            container.innerHTML = '';

            const standardActions = [
                { id: 'study', name: '학습 영상' },
                { id: 'voca', name: '어휘 영상' },
                { id: 'song', name: '한자송' }
            ];

            const levels = currentMode === 'all' ? ['1-level', '2-level'] : [currentMode];

            levels.forEach(level => {
                // F호는 표시 대상에서 제외
                if (currentUnit === 'F') return;

                const levelNum = level === '1-level' ? '1' : '2';
                const courseCount = level === '2-level' && currentUnit === 'E'
                    ? 5
                    : level === '1-level' && currentUnit === 'E'
                        ? 4
                        : 3;
                const courses = Array.from({ length: courseCount }, (_, index) => ({
                    id: `${index + 1}g`,
                    name: `${levelNum}-${currentUnit}${index + 1}`
                }));

                courses.forEach(course => container.appendChild(createCourseCard(level, course.name, standardActions, course.id)));

                if (level === '1-level') {
                    const alabogiActions = level1Alabogi[currentUnit] || [];
                    if (alabogiActions.length > 0) {
                        container.appendChild(createCourseCard(level, '알아보기', alabogiActions, '4g'));
                    }
                }
            });

            // 그리드 컬럼 수 결정
            if (currentMode === 'all') {
                container.style.gridTemplateColumns = 'repeat(4, 1fr)';
            } else {
                const is3Col = currentMode === '2-level';
                container.style.gridTemplateColumns = is3Col ? 'repeat(3, 1fr)' : 'repeat(4, 1fr)';
            }
        }

        function createCourseCard(level, courseName, actions, courseId) {
            const card = document.createElement('div');
            card.className = `course-card course-${courseId.replace('g', '')}`;

            const header = document.createElement('div');
            header.className = 'course-header';
            header.textContent = courseName;
            header.style.background = level === '1-level' ? '#60BF4C' : '#F2A916';
            card.appendChild(header);

            const buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'action-buttons';

            actions.forEach(action => {
                const btn = document.createElement('button');
                btn.className = 'action-btn';
                btn.textContent = action.name;
                btn.disabled = !isLoggedIn;
                btn.onclick = () => playVideo(level, courseId, action.id, `${courseName} ${action.name}`);
                buttonsContainer.appendChild(btn);
            });

            card.appendChild(buttonsContainer);
            return card;
        }

        // Play Video
        function playVideo(level,courseId, actionId, title) {
            if (!isLoggedIn) {
                alert('로그인이 필요합니다.');
                openLogin();
                return;
            }
            
            if (!level || !currentUnit) {
                alert('단계와 호수를 먼저 선택해주세요.');
                return;
            }

            const unitMap = {
                'A': 'unit_a',
                'B': 'unit_b',
                'C': 'unit_c',
                'D': 'unit_d',
                'E': 'unit_e',
                'F': 'unit_f'
            };

            const videoPath = `${SIMPLE_VIDEO_PATH}/${level}/${unitMap[currentUnit]}/${courseId}_${actionId}.mp4`;
            console.log('videoPath:', videoPath);
            
            // Update video source
            const videoSource = document.getElementById('videoSource');
            const videoPlayer = document.getElementById('videoPlayer');
            const videoTitle = document.getElementById('videoTitle');
            const videoSection = document.getElementById('videoSection');
            
            videoSource.src = videoPath;
            videoPlayer.load();
            
            // Update title
            const levelName = level === '1-level' ? '1단계' : '2단계';
            videoTitle.textContent = `${levelName} ${currentUnit}호수 - ${title}`;
            
            // Show video section
            videoSection.classList.add('active');
            
            // Scroll to video
            videoSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Auto play
            videoPlayer.play().catch(err => {
                console.log('Auto-play prevented:', err);
            });
        }
        function closeVideo() {
            document.getElementById('videoPlayer').pause();
            document.getElementById('videoSection').classList.remove('active');
        }

        function goBack() {
            closeVideo();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.getElementById('loginModal').classList.contains('active') ? closeLogin() : closeVideo();
            }
        });

        document.getElementById('loginModal').addEventListener('click', (e) => {
            if (e.target.id === 'loginModal') closeLogin();
        });

        document.addEventListener('DOMContentLoaded', function() {
            restoreSimpleLogin();
            const video = document.getElementById('videoPlayer');
            video.addEventListener('contextmenu', e => e.preventDefault());
            video.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 's') e.preventDefault();
            });
            selectLevel('all', true);
            selectUnit('A', true);
            disableAllButtons();
        });
    </script>
</body>

</html>
