<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Eplat User Login</title>

    <link rel="icon" type="image/x-icon" href="favicon.ico" />
    <?php
    include '../libs/include.php';
    ?>

    <!-- Font Icon -->
    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <!-- Main css -->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <div class="main">
        <!-- Sing in  Form -->
        <section class="sign-in">
            <div class="container">
                <div class="signin-content">
                    <div class="signin-image">
                        <figure><img src="images/signin-image.jpg" alt="sing up image"></figure>
                        <!-- <a href="register.php" class="signup-image-link">회원가입</a></br> -->
                        <a href="findpasswd.php" class="signup-image-link">비밀번호 찾기</a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">로그인</h2>
                        <?php
                        session_start();
                        if (isset($_SESSION['user'])) {
                            // echo "<h5 align='center'>Welcome eplat study home</h5>";
                            // echo "<h5 align='center'>".$_SESSION["user"]."</h5>";  
                            // echo "<p align='center'><a href='logout.php'>Logout</a></p>";  
                        } else {
                            //header('location:login.php');  
                        }
                        ?>
                        <form method="POST" class="register-form" id="login-form">
                            <!-- <form method="POST" class="register-form" id="login-form" action="../Server/Slogon.php"> -->
                            <div class="form-group">
                                <label for="Email"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <?php
                                if (!isset($_SESSION['id'])) {
                                    echo '<input type="text" name="Email" id="Email" value = "", placeholder="아이디"/>';
                                } else {
                                    echo '<input type="text" name="Email" id="Email" value = "", placeholder=' . $_SESSION['id'] . '>';
                                }
                                ?>
                            </div>

                            <div class="form-group">
                                <label for="Password"><i class="zmdi zmdi-lock"></i></label>
                                <?php

                                if (!isset($_SESSION['password'])) {
                                    echo '<input type="password" name="Password" id="your_pass" placeholder="비밀번호"/>';
                                } else {
                                    echo '<input type="text" name="Password" id="your_pass" placeholder=' . $_SESSION["password"] . '>';
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <input type="checkbox" name="remember-me" id="remember-me" class="agree-term" />
                                <label for="remember-me" class="label-agree-term"><span><span></span></span>아이디
                                    기억하기</label>
                            </div>

                            <div class="form-group form-button">
                                <!-- <input type="submit" name="signin" id="signin" class="form-submit" value="Log in" /> -->
                                <input type="button" name="signin" id="signin" class="form-submit" value="Log in" />
                            </div>
                        </form>
                        <div class="social-login">
                            <!-- <span class="social-label">Or login with</span>
                            <ul class="socials">
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-facebook"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-twitter"></i></a></li>
                                <li><a href="#"><i class="display-flex-center zmdi zmdi-google"></i></a></li>
                            </ul> -->
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>

    <?php
    include '../libs/includescr.php';
    ?>
    <!-- JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <script src="../js/common.js"> </script>
    <script>
        var param = "";
        $(document).ready(function() {
            const searchParams = new URLSearchParams(location.search);
            param = searchParams.get('dest');
        });
        $("#signin").click(function() {
            const logform = document.getElementById("login-form");
            const formData = new FormData(logform);

            dispList = (resp) => {
                if ('success' in resp) {
                    CallToast('Login successfully!!', "success")
                    // var url = window.origin+'/' + resp['success'];
                    // window.location.href = url;
                    user = resp['success'][0]['user'];
                    role = resp['success'][0]['role'];
                    conf = resp['success'][0]['confirm'];
                    name = resp['success'][0]['name'];
                    loca = resp['success'][0]['location'];

                    var respo = {
                        user: user,
                        role: role,
                        conf: conf,
                        name: name,
                        loca: loca
                    };

                    saveLocalStorage('info', respo);
                    if (window.origin.includes("localhost"))
                        window.location.href = window.origin + '/' + 'index.php'
                    else
                        window.location.href = window.origin + 'index.php'
                } else if ('falure' in resp) {
                    CallToast('Password emplty or mismatch !!', "error");
                }
            }
            dispErr = (xhr) => {
                CallToast('Login falure!!', "error")
            }

            formData.append('functionName', 'Slogon');
            //CallAjax1("SMethods.php?dest=classroom", "POST", formData, dispList, dispErr);
            CallAjax1("SMethods.php?dest=" + param, "POST", formData, dispList, dispErr);

        })
    </script>
</body>

</html>
