<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EPLAT HOME</title>
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
                        <a href="login.php" class="signup-image-link">Login </a>
                    </div>

                    <div class="signin-form">
                        <h2 class="form-title">비밀번호 찾기</h2>
                        <form method="POST" class="register-form" id="login-form" >
                            <div class="form-group">
                                <label for="id"><i class="zmdi zmdi-account material-icons-name"></i></label>
                                <input type="text" name="id" id="id" placeholder="아이디" />
                            </div>
                            <div class="form-group">
                                <label for="mobile"><i class="zmdi zmdi-smartphone-iphone"></i></label>
                                <input type="text" name="mobile" id="mobile" placeholder="전화번호" />
                            </div>
                            <div class="form-group">
                                <label for="your_pass"><i class="zmdi zmdi-lock"></i></label>
                                <input type="text" name="your_pass" id="your_pass" placeholder="비밀번호" />
                            </div>

                            <div class="form-group form-button">
                                <input type="submit" name="signin" id="signin" class="form-submit" value="찾기" />
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- JS -->
    <?php
    include '../includescr.php';
    ?>
    <script src="https://unpkg.com/bootstrap-table@1.22.1/dist/bootstrap-table.min.js"></script>
    <script src="../js/common.js"></script>
    <script>
    document.getElementById('login-form').addEventListener('submit', function(event) {
      event.preventDefault(); // Prevent the default form submission

      // Create FormData object and append form data to it
      var formData = new FormData(this);
      dispList = (resp) => {
            var element = document.getElementById('your_pass');
            if (resp['success'])
            {
                element.style.color = 'blue';
                element.value = resp['success'];
                CallToast('find password success!!', "success")
            }
            else {
                element.style.color = 'red';
                element.value = resp['error'];
                CallToast('find password  falure!', "error")
            }

        }
        dispErr = (xhr) => {
            document.getElementById('your_pass').value = "사용자 또는 휴대폰 번호가 일치 하지 않습니다.";
            CallToast('find password  falure!', "error")
        }

        formData.append('functionName', 'Sfindpassword');
        CallAjax1("SMethods.php", "POST", formData, dispList, dispErr);
    });

    </script>
</body>
</html>