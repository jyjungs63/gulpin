<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>사용자 등록</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico" />

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/css/bootstrap.min.css" />

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="main">

        <!-- Sign up form -->
        <section class="signup">
            <div class="container">
                <div class="signup-content">
                    <div class="signup-form">
                        <p style="font-size:14px"><span style="color: black;font-size:14px"><b>1)지사아이디 2)유치원아이디 3)유료
                                    일반학생 아이디는 </b> </span> 여기에서 회원 가입을 하며, 관리자 승인후에 사용이 가능합니다</p></br>
                        <h4 class="form-title">회원가입</h4>
                        <form method="POST" class="register-form" id="register-form">
                            <!-- <form method="POST" class="register-form" id="register-form" action="../Server/Sregister.php"> -->
                            <div class="form-group">
                                <input type="checkbox" name="idrolebm" id="idrolebm" class="agree-term" />
                                <label for="idrolebm" class="label-agree-term"><span><span></span></span>지사회원</label>
                                &nbsp;&nbsp;&nbsp;
                                <input type="checkbox" name="idrolet" id="idrolet" class="agree-term" />
                                <label for="idrolet" class="label-agree-term"><span><span></span></span>원장 및 선생님
                                </label>&nbsp;&nbsp;
                                <input type="checkbox" name="idrolesite" id="idrolesite" class="agree-term" />
                                <label for="idrolesite" class="label-agree-term"><span><span></span></span>가맹관리
                                </label>&nbsp;&nbsp;
                                <input type="checkbox" name="idroleother" id="idroleother" class="agree-term" />
                                <label for="idroleother" class="label-agree-term"><span><span></span></span>일반학생회원
                                </label>
                            </div>
                            <div class="form-group">
                                <label for="id"><i class="zmdi zmdi-email"></i></label>
                                <input type="text" name="id" id="id" placeholder="아이디" />
                            </div>
                            <div class="form-group">
                                <label for="name"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="name" id="name" placeholder="이름" />
                            </div>
                            <div class="form-group">
                                <label for="mobile"><i class="zmdi zmdi-smartphone-iphone"></i></label>
                                <input type="text" name="mobile" id="mobile" placeholder="전화번호" />
                            </div>
                            <div class="form-group" style="margin-top: -15px">
                                <div class="row d-flex">
                                    <div style="margin-top: -5px">
                                        <button type="button" class="btn btn-sm btn-primary" style="border-radius: 30%;"
                                            onclick="execDaumPostcode('zipcode','addr')" data-bs-toggle="tooltip"
                                            data-bs-placement="top" title="우편번호 찾기">검색</button>
                                    </div>
                                    <div class="d-inline" style="margin-top: -5px;">
                                        <input type="text" name="zipcode" id="zipcode" placeholder="우편번호" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="addr"><i class="zmdi zmdi-home"></i></label>
                                <input type="text" name="addr" id="addr" placeholder="주소" />
                            </div>
                            <div class="form-group">
                                <label for="password"><i class="zmdi zmdi-lock"></i></label>
                                <input type="password" name="password" id="password" placeholder="비밀번호" />
                            </div>
                            <div class="form-group">
                                <label for="re-pass"><i class="zmdi zmdi-lock-outline"></i></label>
                                <input type="password" name="re_pass" id="re_pass" placeholder="비밀번호확인" />
                            </div>

                            <div class="form-group">
                                <input type="checkbox" name="agree-term" id="agree-term" class="agree-term" />
                                <label for="agree-term" class="label-agree-term"><span><span></span></span>나는 회원가입을 위하여
                                    약관 및 개인정보 취급방침에 동의 합니다.
                                    <a href="members.pdf" target="_blank" class="term-service">&nbsp;*동의서</a></label>
                            </div>
                            <div class="form-group form-button ">
                                <input type="submit" name="signup" id="signup" class="form-submit btn-primary"
                                    style="background-color: blue" value="등록" />
                            </div>
                        </form>
                    </div>
                    <div class="signup-image">
                        <figure><img src="images/signup-image.jpg" alt="sing up image"></figure>
                        <a href="login.php" class="signup-image-link">등록된 사용자 입장하기</a>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <?php
    include '../libs/includescr.php';
    ?>
    <script src="../js/common.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.1/js/bootstrap.min.js"></script>

    <script>
    document.getElementById('register-form').addEventListener('submit', function(event) {
        event.preventDefault(); // Prevent the default form submission

        // Create FormData object and append form data to it

        const logform = document.getElementById("register-form");
        var formData = new FormData(logform);
        dispList = (resp) => {
            if ('success' in resp) {
                CallToast('Login successfully!!', "success")
                window.location.href = resp['success'];
            } else if ('falure' in resp) {
                CallToast('Password emplty or mismatch !!', "error")
            }
        }
        dispErr = (xhr) => {

            CallToast('find password falure!', "error")
        }

        formData.append('functionName', 'SRegister');
        CallAjax1("SMethods.php", "POST", formData, dispList, dispErr);
    });
    </script>
</body>

</html>