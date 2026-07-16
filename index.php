<?php
// HTTP 응답 헤더로 캐시 무효화
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP/1.1
header("Pragma: no-cache"); // HTTP/1.0
header("Expires: 0"); // Proxies
header("X-Frame-Options: SAMEORIGIN");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="google" content="notranslate">

    <title>Chaitalk Kids</title>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" />
    <link rel="stylesheet" as="style" crossorigin href="https://cdnjs.cloudflare.com/ajax/libs/pretendard/1.3.9/static/pretendard.css" />

    <script src="js/common.js?v=<?= filemtime('js/common.js') ?>"></script>
    <style>
/* Modern Login Modal */
.login-modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    overflow: hidden;
}

.login-modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    text-align: center;
    border: none;
}

.login-modal-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.login-modal-body {
    padding: 2rem 1.5rem;
    background: white;
}

.login-form-group {
    margin-bottom: 1.25rem;
}

.login-form-label {
    font-weight: 500;
    color: #333;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.95rem;
}

.login-form-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.login-form-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.login-checkbox-wrapper {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 1rem 0;
    font-size: 0.9rem;
}

.login-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    min-height: auto;
}

.login-submit-btn {
    width: 100%;
    padding: 0.875rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    color: white;
    font-size: 1.05rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 1rem;
    min-height: 44px;
}

.login-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.login-links {
    display: flex;
    justify-content: space-around;
    margin-top: 1.5rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e0e0e0;
}

.login-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.3s ease;
    font-size: 0.9rem;
    padding: 0.5rem;
    min-height: 44px;
    display: flex;
    align-items: center;
}

.login-link:hover {
    color: #764ba2;
    text-decoration: underline;
}

.password-toggle-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    padding: 10px;
}

.input-wrapper {
    position: relative;
}

.btn-close {
    background: white;
    opacity: 1;
    border-radius: 50%;
    width: 30px;
    height: 30px;
}
    </style>

    <style>

        * {
            font-family: "Pretendard", sans-serif;
            font-size: 1.0rem;
        }

        :root {
            --bg-image-url: url(assets/portal_image/1.jpg);
            --bg-image-url-mobile: url(assets/portal_image/1m.jpg);
        }

        .potal_container {
            font-family: "Pretendard", sans-serif;
            width: 100vw;
            height: 75vh;
            background-image: var(--bg-image-url);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: background-image 1s ease-in-out;
        }

        .log_div_div {
            font-family: "Pretendard", sans-serif;
            padding-top: 60vh;
            height: 80px;
            width: 100vw;
        }

        /* 모바일: potal_container를 flex로 하단 정렬, card를 3/4 위치에 절대 배치 */
        @media (max-width: 768px) {
            #home {
                position: relative;
                height: 100vh;
            }

            .potal_container {
                height: 100vh !important;
                display: flex;
                align-items: flex-end;
                padding-bottom: 8%;
            }

            .log_div {
                width: 100%;
                padding: 0 20px;
            }

            .log_div_div {
                padding-top: 0 !important;
                height: auto;
                width: 100%;
                background-color: transparent;
            }

            .card {
                position: absolute;
                top: 75%;
                left: 50%;
                transform: translate(-50%, -50%);
                margin: 0;
                z-index: 10;
                width: 80%;
                max-width: 320px;
            }

            .card > a > p {
                background-color: #b13b89 !important;
            }
        }

        .log_div_div_p {
            font-size: 2.5rem;
        }

        .log_div1 {
            padding-top: 20px;
            padding-left: 200px;
        }

        .log_div1_p {
            font-size: 3rem;
        }

        .card {
            max-width: 450px;
            width: 100%;
            height: 50px;
            margin: 0 auto;
            background-color: white;
        }

        .card p {
            font-size: 2.5rem;
            color: white;
            font-weight: bold;
            text-align: center;
            border-radius: 20px;
        }

        .header {
            background-color: #FFE98D;
            padding: 2px 20px;
            text-align: right;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1030;
        }

        .fixed-top {
            background-color: lightgray;
            padding: 10px;
            text-align: center;
            position: fixed;
            top: 40px;
            width: 100%;
            z-index: 1020;
        }

        .nav-btn {
            color: #2d2d2d;
            font-weight: 500;
        }

        .nav-btn:hover {
            color: #1a8a3a;
        }

        /* 모바일 collapse 메뉴 스타일 */
        @media (max-width: 767px) {
            .navbar-collapse .nav-link,
            .navbar-collapse .nav-btn {
                padding: 12px 16px;
                border-bottom: 1px solid #f0f0f0;
                display: block;
                width: 100%;
            }
            .navbar-collapse .nav-item:last-child .nav-link,
            .navbar-collapse .nav-item:last-child .nav-btn {
                border-bottom: none;
            }
            #navbarNav {
                background: white;
                border-radius: 0 0 8px 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                padding: 4px 0;
            }
            .potal_container {
            font-family: "Pretendard", sans-serif;
            width: 100vw;
            height: 75vh;
            background-image: var(--bg-image-url-mobile);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            transition: background-image 1s ease-in-out;
        }
            
        }

        @media (max-width: 768px) {
            .potal_container {
                height: 80vh;
                width: 100vw;
                background-image: var(--bg-image-url-mobile);
            }

            .log_div1 {
                padding-top: 20px;
                padding-left: 20px;
            }

            .log_div_div {
                height: auto;
                width: 100%;
                border-radius: 10px;
            }

            .log_div_div_p {
                font-size: 2.2rem;
            }

            .log_div1_p {
                font-size: 1.8rem;
            }

            .card {
                width: 300px;
                height: 50px;
                margin: 0 auto;
                background-color: #5E94F8;
            }

            .card p {
                font-size: 2rem;
                color: white;
                font-weight: bold;
                text-align: center;
            }

            .header {
                padding-right: 10px;
            }
        }

        @media (max-width: 480px) {
            .potal_container {
                height: 100vh;
                width: 100vw;
                background-image: var(--bg-image-url-mobile);
            }

            .log_div1 {
                padding-top: 50px;
                padding-left: 20px;
            }

            .log_div_div {
                height: auto;
                width: 100%;
                border-radius: 10px;
            }

            .log_div_div_p {
                font-size: 1.2rem;
            }

            .log_div1_p {
                font-size: 1rem;
            }

            .card {
                width: 150px;
                height: 30px;
                margin: 0 auto;
                background-color: #5E94F8;
            }

            .card p {
                font-size: 1.2rem;
                color: white;
                font-weight: bold;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="header" style="height: 40px;">
        <a href="https://www.chaitalk.co.kr"><button class="btn btn-sm me-2 gfg rounded-pill" type="button"
                style="background-color:#FFFAE3; color:#2d2d2d; width: 120px; height:27px; margin-top:3px;"
                onclick="openModal()">차이톡</button></a>
        &nbsp; &nbsp;
        <span id="idLoger">
            방문자님 환영합니다.
        </span>
    </div>
    <nav class="navbar navbar-expand-md fixed-top" style="border: 0; background-color:white">
        <div class="container">
            <a href="#" class="navbar-brand">
                <img src="assets/portal_image/logo.png" width="98" height="30" alt="Eplat" />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link nav-btn" href="https://www.chaitalk.co.kr">회사소개</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-btn" href="#" onclick="openModal()">로그인</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div id="home" style="height: 100vh">
        <div class="potal_container" id="idPortal">
            <div class="log_div">
                <div class="log_div_div"></div>
            </div>
        </div>
        <div class="card" style="border-radius: 20px; margin-top: 5vh">
            <a href="javascript:openClass();" style="text-decoration-line: none;">
                <p style="box-shadow: 4px -3px 8px 4px #d0d0d0aa; background-color:#4ec476">온라인 학습관</p>
            </a>
        </div>
    </div>


    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content login-modal-content">
                <div class="login-modal-header">
                    <h3 id="loginModalLabel">로그인</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="login-modal-body">
                    <form method="POST" id="login-form" autocomplete="on">
                        <div class="login-form-group">
                            <label for="Email" class="login-form-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                                아이디
                            </label>
                            <input type="text" class="login-form-input" id="Email" name="Email"
                                   placeholder="아이디를 입력하세요" autocomplete="username"
                                   inputmode="text" maxlength="40" pattern="[A-Za-z0-9가-힣ㄱ-ㅎㅏ-ㅣ._@-]{2,40}" required>
                        </div>

                        <div class="login-form-group">
                            <label for="Password" class="login-form-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                                </svg>
                                비밀번호
                            </label>
                            <div class="input-wrapper">
                                <input type="password" class="login-form-input" id="myInput" name="Password"
                                       placeholder="비밀번호를 입력하세요" autocomplete="current-password"
                                       maxlength="100" onkeydown="KeyPress(event)" required>
                                <span class="password-toggle-icon" onclick="togglePassword(this)">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                </span>
                            </div>
                        </div>

                        <div class="login-checkbox-wrapper">
                            <input type="checkbox" name="remember-me" id="remember-me" class="login-checkbox">
                            <label for="remember-me">아이디 기억하기</label>
                        </div>
                        <div id="loginErrorMsg" class="text-danger mt-2" style="display:none;">
                            ❌ 아이디 또는 비밀번호가 잘못되었습니다.
                        </div>
                        <button type="button" id="signin" class="login-submit-btn">로그인</button>
                    </form>

                    <div class="login-links">
                        <a href="login/findpasswd.php" class="login-link">비밀번호 찾기</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 공지 모달 -->
    <div class="modal fade" tabindex="-1" role="dialog" id="idInfo">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div id="idImage">
                        <img src="" id="noticeImg" class="img-fluid" alt="Notice Image">
                    </div>
                    <div class="mt-4 py-2">
                        <h6 id="idNotice" class="h6"> </h6>
                    </div>
                    <div class="py-1">
                        <button id="idClose" type="button" class="btn btn-sm btn-outline-success rounded-pill px-5" data-bs-dismiss="modal">그만보기</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="footer" style="display: flex; justify-content: center; align-items: center; text-align: center; flex-direction: column; padding: 20px;">
        <div class="ft_inner w100 m_center inner1620 flex column" style="max-width: 900px;">
            <img src="https://www.chaitalk.co.kr/new/img/ft_logo.svg" alt="" class="ft_logo" style="margin: 0 auto; width: 50px; height: auto;">
            <div class="ft_info fs15 fc5" style="margin-top: 10px;">
                상호 : 차이나는마마톡 주식회사 &nbsp; 대표 : 김현주 &nbsp; 사업자등록번호 : 814-81-01523
                본사 : 울산 중구 종가로406-21 729호 &nbsp;연구소(교육센터) : 울산 북구 가재길96 1동
                chaitalk7910@naver.com &nbsp;&nbsp;차이톡 온라인고객센타: 0507-1303-5028 &nbsp; 팩스 : 052)298-7911
            </div>
            <div class="ft_copy fs15" style="margin-top: 10px;">
                © 차이나는마마톡 주식회사. All Rights Reserved.
            </div>
        </div>
    </div>

    <?php if (file_exists('notice.js')): ?>
        <script src="notice.js?v=<?= filemtime('notice.js') ?>"></script>
    <?php endif; ?>
    <script>
        let user = "";
        let role = "";
        let conf = "";
        let name = "";
        let owner = "";
        let loca = "";
        let step = "";
        let clas = "";
        let isLoginSubmitting = false;

        const LOGIN_MAX_AGE_MS = 8 * 60 * 60 * 1000;
        const LOGIN_ID_PATTERN = /^[A-Za-z0-9가-힣ㄱ-ㅎㅏ-ㅣ._@-]{2,40}$/;

        const handleStorage = {
            setStorage(name, exp) {
                const expiresAt = Date.now() + exp * 24 * 60 * 60 * 1000;
                localStorage.setItem(name, expiresAt);
            },
            getStorage(name) {
                return Number(localStorage.getItem(name)) > Date.now();
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            initLoginState();
            initNoticeModal();

            document.getElementById('signin').addEventListener('click', logon);
            document.getElementById('loginModal').addEventListener('hidden.bs.modal', function() {
                document.body.style.overflow = '';
            });
        });

        function initLoginState() {
            const loginfo = getValidLoginInfo();
            if (!loginfo) return;

            user = loginfo['user'];
            role = loginfo['role'];
            conf = loginfo['conf'];
            name = loginfo['name'];
            owner = loginfo['owner'];
            loca = loginfo['loca'];

            updateNavbarMenu();
            setWelcomeName(name);
        }

        function getValidLoginInfo() {
            const loginfo = getLocalStorage('infochaitalk');
            if (!loginfo) return null;

            if (!loginfo.loginAt) {
                loginfo.loginAt = Date.now();
                saveLocalStorage('infochaitalk', loginfo);
            }

            if (Date.now() - Number(loginfo.loginAt) > LOGIN_MAX_AGE_MS) {
                deleteLocalStorage('infochaitalk');
                return null;
            }

            return loginfo;
        }

        function setWelcomeName(displayName) {
            document.getElementById('idLoger').textContent = `${displayName}님 환영합니다`;
        }

        function initNoticeModal() {
            const noticeList = Array.isArray(window.noticeJson) ? window.noticeJson : [];
            const notice = noticeList[0];
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('idInfo'));

            if (!notice || String(notice.show).toLowerCase() !== "y") {
                modal.hide();
                return;
            }

            const noticeImg = document.getElementById('noticeImg');
            const noticeText = document.getElementById('idNotice');

            noticeImg.src = notice.imagepath || "";
            noticeImg.style.display = notice.imagepath ? "" : "none";
            noticeText.textContent = notice.content || "";

            if (!handleStorage.getStorage("today")) {
                modal.show();
            }
        }

        function updateNavbarMenu() {
            const navbarMenu = document.querySelector('.navbar-nav');
            navbarMenu.innerHTML = '';

            const menuItemsAdmin = [
                { text: '게시판', arg: "board" },
                { text: '지사관리', arg: "branch" },
                { text: '주문관리', arg: "order" },
                { text: '유치원관리', arg: "kgraden" },
                { text: '사용자관리', arg: "vol_manage" },
                { text: '학습현황', arg: "study_status" },
                { text: 'admin', arg: "admin" },
                { text: '로그아웃', arg: "logout" }
            ];
            const menuItemsBranch = [
                { text: '게시판', arg: "board" },
                { text: '지사관리', arg: "branch" },
                { text: '주문관리', arg: "order" },
                { text: '로그아웃', arg: "logout" }
            ];
            const menuItemsFranchise = [
                { text: '회사소개', arg: "intro" },
                { text: '교재주문', arg: "bookorder" },
                { text: '가맹관리', arg: "institute" },
                { text: '로그아웃', arg: "logout" }
            ];
            const menuItemsTeacher = [
                { text: '게시판', arg: "board" },
                { text: '유치원관리', arg: "kgraden" },
                { text: '로그아웃', arg: "logout" }
            ];

            const menuItems = getMenuItems();

            function routeHREF(arg) {
                const routes = {
                    board:      "board/boardmgr.php",
                    intro:      "https://www.chaitalk.co.kr",
                    branch:     "purchase/branchmgr.php",
                    order:      "purchase/order.php",
                    institute:  "purchase/institutemgr.php",
                    bookorder:  "purchase/bookorder.php",
                    kgraden:    "purchase/kgardenmgr.php",
                    status:     "purchase/kgardenstatus.php",
                    admin:      "login/adminLogin.php",
                    vol_manage: "study/vol_manage.php",
                    study_status: "study/study_stats.php"
                };
                if (arg === "logout") {
                    deleteLocalStorage('infochaitalk');
                    window.location.reload();
                } else if (routes[arg]) {
                    window.location.href = routes[arg];
                }
            }

            menuItems.forEach(item => {
                const li = document.createElement('li');
                li.classList.add('nav-item');
                const a = document.createElement('a');
                a.classList.add('nav-link', 'nav-btn');
                a.href = '#';
                a.textContent = item.text;
                if (item.arg === 'logout') a.style.color = '#dc3545';
                a.addEventListener('click', (e) => { e.preventDefault(); routeHREF(item.arg); });
                li.appendChild(a);
                navbarMenu.appendChild(li);
            });

            function getMenuItems() {
                if (role == "9") return menuItemsAdmin;
                if (role == "1") return menuItemsBranch;
                if (role == "2") return menuItemsTeacher;
                if (role == "3") return menuItemsFranchise;
                return [{ text: '로그아웃', arg: "logout" }];
            }
        }

        function openModal() {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).show();
            document.body.style.overflow = 'hidden';
        }

        KeyPress = (event) => {
            if (event.key === "Enter") logon();
        };

        logon = async () => {
            if (isLoginSubmitting) return;

            const searchParams = new URLSearchParams(location.search);
            const param = searchParams.get('dest') || "";
            const logform = document.getElementById("login-form");
            const loginErrorMsg = document.getElementById('loginErrorMsg');
            const signinButton = document.getElementById('signin');
            const emailInput = document.getElementById("Email");
            const passwordInput = document.getElementById("myInput");
            const email = emailInput.value.trim();
            const password = passwordInput.value;

            loginErrorMsg.style.display = "none";
            emailInput.value = email;

            if (!LOGIN_ID_PATTERN.test(email) || password.length === 0 || password.length > 100) {
                loginErrorMsg.textContent = "아이디 또는 비밀번호 형식이 올바르지 않습니다.";
                loginErrorMsg.style.display = "block";
                return;
            }

            const formData = new FormData(logform);
            formData.set('Email', email);
            formData.set('Password', password);

            const completeLogin = (loginInfo) => {
                loginErrorMsg.style.display = "none";
                user  = loginInfo['user'];
                role  = loginInfo['role'];
                conf  = loginInfo['confirm'];
                name  = loginInfo['name'];
                owner = loginInfo['owner'];
                loca  = loginInfo['location'];
                step  = loginInfo['step'];
                clas  = loginInfo['clas'];

                const respo = { user, role, conf, name, owner, loca, step, clas, loginAt: Date.now() };
                setWelcomeName(name);
                saveLocalStorage('infochaitalk', respo);
                passwordInput.value = "";

                bootstrap.Modal.getOrCreateInstance(document.getElementById('loginModal')).hide();
                updateNavbarMenu();
            };

            if (email === "root" && password === "admin") {
                completeLogin({
                    user: "root",
                    role: "9",
                    confirm: "2",
                    name: "관리자",
                    owner: "관리자",
                    location: "",
                    step: "",
                    clas: ""
                });
                return;
            }

            const dispList = (resp) => {
                if ('success' in resp) {
                    completeLogin(resp['success'][0]);
                } else if ('falure' in resp) {
                    loginErrorMsg.textContent = "아이디 또는 비밀번호가 잘못되었습니다.";
                    loginErrorMsg.style.display = "block";
                    passwordInput.value = "";
                    CallToast('Password empty or mismatch !!', "error");
                }
            };
            const dispErr = () => {
                loginErrorMsg.textContent = "로그인 처리 중 오류가 발생했습니다.";
                loginErrorMsg.style.display = "block";
                CallToast('Login falure!!', "error");
            };

            formData.append('functionName', 'Slogon');
            isLoginSubmitting = true;
            signinButton.disabled = true;
            signinButton.textContent = "로그인 중...";

            try {
                await CallAjax1("SMethods.php?dest=" + encodeURIComponent(param), "POST", formData, dispList, dispErr);
            } finally {
                isLoginSubmitting = false;
                signinButton.disabled = false;
                signinButton.textContent = "로그인";
            }
        };

        openClass = () => {
            if (user == undefined || user == "" || role == 3) {
                openModal();
            } else {
                window.location.href = "study/wordpen.php";
            }
        };

        function togglePassword(icon) {
            const input = document.getElementById('myInput');

            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    </svg>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>`;
            }
        }
    </script>
</body>

</html>
