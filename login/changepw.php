<!DOCTYPE html>
<html lang="ko">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>비밀번호 변경</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <?php
    include '../libs/include.php';
    ?>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
    <style>
        .pw-guide { margin: 0 0 24px; color: #555; font-size: 14px; line-height: 1.6; }
        .pw-guide.temp { color: #c0392b; font-weight: 700; }
        .pw-message { min-height: 20px; margin: 0 0 12px; font-size: 14px; color: #c0392b; }
    </style>
</head>

<body>

    <div class="main">
        <!-- 비밀번호 변경 — 임시 비밀번호로 로그인하면 이 화면으로 온다 (Server/password_policy.php SChangePassword) -->
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="images/signin-image.jpg" alt="비밀번호 변경"></figure>
                        <a href="../index.php" class="signup-image-link">처음 화면으로</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">비밀번호 변경</h2>
                        <p class="pw-guide" id="pwGuide">현재 비밀번호와 새 비밀번호를 입력하세요. 새 비밀번호는 영문/숫자 4~30자입니다.</p>
                        <form class="register-form" id="pw-form" autocomplete="off">
                            <div class="form-group">
                                <label for="pwCurrent"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" id="pwCurrent" placeholder="현재(임시) 비밀번호" autocomplete="current-password" required />
                            </div>
                            <div class="form-group">
                                <label for="pwNext"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" id="pwNext" placeholder="새 비밀번호 (영문/숫자 4~30자)" maxlength="30" autocomplete="new-password" required />
                            </div>
                            <div class="form-group">
                                <label for="pwNext2"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" id="pwNext2" placeholder="새 비밀번호 확인" maxlength="30" autocomplete="new-password" required />
                            </div>
                            <p class="pw-message" id="pwMessage" role="alert"></p>
                            <div class="form-group form-button">
                                <input type="submit" id="pwSubmit" class="form-submit" value="변경" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php
    include '../libs/includescr.php';
    ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="../js/common.js"></script>
    <script>
        if (new URLSearchParams(location.search).get('temp') === '1') {
            const guide = document.getElementById('pwGuide');
            guide.classList.add('temp');
            guide.textContent = '임시 비밀번호로 로그인했습니다. 계속 이용하려면 새 비밀번호로 변경해 주세요.';
        }

        document.getElementById('pw-form').addEventListener('submit', (event) => {
            event.preventDefault();
            const message = document.getElementById('pwMessage');
            const current = document.getElementById('pwCurrent').value;
            const next = document.getElementById('pwNext').value.trim();
            const next2 = document.getElementById('pwNext2').value.trim();

            if (!/^[A-Za-z0-9]{4,30}$/.test(next)) {
                message.textContent = '새 비밀번호는 영문/숫자 4~30자로 입력해 주세요.';
                return;
            }
            if (next !== next2) {
                message.textContent = '새 비밀번호 확인이 일치하지 않습니다.';
                return;
            }
            message.textContent = '';

            CallAjax("SMethods.php", "POST", { functionName: "SChangePassword", otherData: { current, next } }, () => {
                alert('비밀번호가 변경되었습니다.');
                window.location.href = '../index.php';
            }, (e) => {
                message.textContent = (e && e.responseJSON && e.responseJSON.error) || '비밀번호 변경에 실패했습니다.';
                if (e && e.status === 401) {
                    localStorage.removeItem('infochaitalk');
                }
            });
        });
    </script>
</body>

</html>
