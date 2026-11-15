<?php
ob_start();
session_start();
if (isset($_GET['do'])) {
    $do = $_GET['do'];
}
include '../include/jdf.php';
include '../include/config.php';
include '../include/function.php';
?>
<!DOCTYPE html>
<html data-bs-theme="<?=(!empty($themecolor) ? $themecolor : 'light')?>" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>پنل کارفرما</title>
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png?ver=1">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png?ver=1">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png?ver=1">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png?ver=1">
    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="assets/img/favicons/mstile-150x150.png?ver=1">
    <meta name="theme-color" content="#ffffff">
    <script src="assets/js/config.js"></script>
    <script src="vendors/simplebar/simplebar.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="assets/css/theme-rtl.css?ver=<?=rand(10,1000)?>" rel="stylesheet" id="style-rtl">
    <link href="assets/css/theme.css?ver=<?=rand(10,1000)?>" rel="stylesheet" id="style-default">
    <link href="assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
    <link href="assets/css/user.css" rel="stylesheet" id="user-style-default">
    <link rel="stylesheet" href="assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="assets/css/plyr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css" />
    <link href="assets/css/custom_fa.css?ver=<?=rand(10,1000)?>" rel="stylesheet" id="user-style-default">
    <script>
    var isRTL = JSON.parse(localStorage.getItem('isRTL'));
    if (isRTL) {
        var linkDefault = document.getElementById('style-default');
        var userLinkDefault = document.getElementById('user-style-default');
        linkDefault.setAttribute('disabled', true);
        userLinkDefault.setAttribute('disabled', true);
        document.querySelector('html').setAttribute('dir', 'rtl');
    } else {
        var linkRTL = document.getElementById('style-rtl');
        var userLinkRTL = document.getElementById('user-style-rtl');
        linkRTL.setAttribute('disabled', true);
        userLinkRTL.setAttribute('disabled', true);
    }
    </script>
</head>

<body>
    <main class="main" id="top">
        <div class="container-fluid">
            <script>
            var isFluid = JSON.parse(localStorage.getItem("isFluid"));
            if (isFluid) {
                var container = document.querySelector("[data-layout]");
                container.classList.remove("container");
                container.classList.add("container-fluid");
            }
            </script>
            <div class="row min-vh-100 flex-center g-0">
                <div class="col-lg-8 col-xxl-5 py-3 position-relative"><img class="bg-auth-circle-shape" src="assets/img/icons/spot-illustrations/bg-shape.png" alt="" width="250"><img class="bg-auth-circle-shape-2" src="assets/img/icons/spot-illustrations/shape-1.png" alt="" width="150">
                    <div class="card overflow-hidden z-1">
                        <div class="card-body p-0">
                            <div class="row g-0 h-100">
                                <div class="col-md-5 text-center bg-card-gradient">
                                    <div class="position-relative p-4 pt-md-1 pb-md-7" data-bs-theme="light">
                                        <div class="bg-holder bg-auth-card-shape" style="background-image:url(assets/img/icons/spot-illustrations/half-circle.png);">
                                        </div>

                                    </div>
                                    <div class="z-1 position-relative"> <img src="assets/img/full-logo.png" alt="" style="height: 120px;" />
                      </div>
                                    <div class="mt-3 mb-4 mt-md-4 mb-md-5" data-bs-theme="light">
                                        <p class="text-white" style="font-size: 20px;">سامانه جامع مهارت آموزی <br> آکادمی کارشناسان <br>" رویکردی برای تعیین هدف و <br> راهبردی فرایند یادگیری مهارت "</p>
                                        
                                    </div>
                                </div>
                                
                                <?php if (!isset($do)) { ?>
                                <div class="col-md-7 d-flex flex-center">
                                    <div class="p-4 p-md-5 flex-grow-1">
                                        <div class="row flex-between-center">
                                            <div class="col-auto mb-3">
                                                <h5>ورود به حساب کاربری</h5>
                                            </div>
                                        </div>
                                        <?php
                                    if (isset($_SESSION['error'])) {
                                        echo '<p class="error">' . $_SESSION['error'] . '</p>';
                                        unset($_SESSION['error']);
                                    };
                                    ?>
                                        <form action="?do=checklogin" method="POST">
                                            <div class="mb-3">
                                                <label class="form-label" for="card-email">نام کاربری</label>
                                                <input class="form-control" name="username" id="card-email" type="text" required />
                                            </div>
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between">
                                                    <label class="form-label" for="card-password">کلمه عبور</label>
                                                </div>
                                                <input class="form-control" id="card-password" name="password" type="password" required />
                                            </div>
                                            <div class="row flex-between-center">

                                                <div class="col-auto"><a class="fs--1" href="?do=forgotpassword">یادآوری کلمه عبور</a></div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="login">ورود به پنل</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <?php } else if (isset($do) && $do == 'forgotpassword') { ?>
                                <div class="col-md-7 d-flex flex-center">
                                    <div class="p-4 p-md-5 flex-grow-1">
                                        <div class="row flex-between-center">
                                            <div class="col-auto mb-3">
                                                <h5>یادآوری کلمه عبور</h5>
                                            </div>
                                        </div>
                                        <?php
                                    if (isset($_SESSION['error'])) {
                                        echo '<p class="error">' . $_SESSION['error'] . '</p>';
                                        unset($_SESSION['error']);
                                    };
                                    ?>
                                        <form action="?do=checkemail" method="POST">
                                            <div class="mb-3">
                                                <label class="form-label" for="card-email">پست الکترونیک</label>
                                                <input class="form-control" name="email" id="card-email" type="text" required />
                                            </div>
                                            <div class="row flex-between-center">

                                                <div class="col-auto"><a class="fs--1" href="login-company.php">ورود به پنل</a></div>
                                            </div>
                                            <div class="mb-3">
                                                <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="sendpassword">ارسال کلمه عبور جدید</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <?php }
            else if (isset($do) && $do == 'checkemail') {

                if (isset($_POST['sendpassword'])) {
                    $email =  $_POST['email'];
                    if (empty($email)) {
                        $_SESSION['error'] = 'پست الکترونیک خود را وارد نمایید';
                        header("Location: login-company.php?do=forgotpassword");
                        exit();
                    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $_SESSION['error'] = 'پست الکترونیک شما صحیح نمی باشد';
                        header("Location: login-company.php?do=forgotpassword");
                        exit();
                    } else {
                        $result = Query("SELECT * from tbl_user Where tbl_user_email = '$email' ");
                        $count = mysqli_num_rows($result);
                        if ($count == '1') {
                            $read = mysqli_fetch_array($result);
                            $npass = rand(10, 10000000);
                            $md5pass = hash("md5", $npass);
                            $userid = $read['tbl_user_id'];
                            $to = $email;
                            $subject = 'New Password From ' . $_SERVER['SERVER_NAME'];
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            $headers .= 'From: <info@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
                            $message = 'New password is =<b>' .  $npass . '</b><br />For login in page please click here:<a href="http://www.' . $_SERVER['SERVER_NAME'] . '/site_admin/login-company.php" >[Login in site]</a>';
                            mail($to, $subject, $message, $headers);
                            $update = Query("Update tbl_user SET tbl_user_password = '$md5pass' Where tbl_user_id = '$userid' ");
                            $_SESSION['error'] = 'Password has been sent';
                            header("Location: login-company.php");
                            exit();
                        } else {
                            $_SESSION['error'] = 'پست الکترونیک شما یافت نشد !';
                            header("Location: login-company.php?do=forgotpassword");
                            exit();
                        }
                    }
                }
            } else if (isset($do) && $do == 'checklogin') {
                $username = '';
                $password = '';
                if (isset($_POST['login'])) {
                    $username = $_POST['username'];
                    $password = $_POST['password'];

                    if (empty($username) || empty($password)) {
                        $_SESSION['error'] = 'اطلاعات خود را به صورت کامل وارد نمایید';
                        header("Location: login-company.php");
                        exit();
                    } else {
                        $password = hash("md5", $password);
                        $query = Query("SELECT * from tbl_user WHERE tbl_user_username = '$username'  AND tbl_user_password = '$password' ");
                        $count  = mysqli_num_rows($query);
                        if ($count == '1') {
                            $read = mysqli_fetch_array($query);
                            $userid = $read['tbl_user_id'];
                            $date = jdate("Y/m/d : a:h:s");
                            $_SESSION['id_login'] = $userid;
                            Query("update tbl_user set tbl_user_lastlogin = '$date'  where tbl_user_id = '$userid' ");
                            $_SESSION['username_login'] = $read['tbl_user_username'];
                            $_SESSION['user_lng'] = $read['tbl_user_lng'];
                            $_SESSION['user_name'] = $read['tbl_user_name'];
                            $_SESSION['user_code'] = $read['tbl_user_code'];
                            $_SESSION['user_access'] = ($read['tbl_user_access'] == '5' ? 'mentor' : ($read['tbl_user_access'] == '6' ? 'coach' : ($read['tbl_user_access'] == '7' ? 'karfarma' : 'admin')));
                            header("Location: index.php");
                        } else {
                            $_SESSION['error'] = 'متاسفانه اطلاعتی پیدا نشد !';
                            header("Location: login-company.php");
                        }
                    }
                }
            } else if (isset($do) && $do == 'logout') {
                unset($_SESSION['username_login']);
                unset($_SESSION['id_login']);
                unset($_SESSION['user_lng']);
                unset($_SESSION['user_name']);
                unset($_SESSION['user_access']);
                unset($_SESSION['user_code']);
                //        setcookie($get_domain, '', time() - 3600);
                header("Location: login-company.php");
            }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>


    <footer class="footer">
        <div class="row g-0 fs--1 mt-4 mb-3">
            <div class="col-12">
                <p class="mb-0 text-600">
                    2023 &copy; <a href="<?=$site_url.$lng?>/">Experts Academy</a> . v1.1.0
                </p>
            </div>
        </div>
    </footer>
    </div>
    </div>
    </main>
    <script src="assets/js/jquery-3.2.1.min.js"></script>
    <script src="vendors/popper/popper.min.js"></script>
    <script src="vendors/bootstrap/bootstrap.min.js"></script>
    <script src="vendors/anchorjs/anchor.min.js"></script>
    <script src="vendors/is/is.min.js"></script>
    <script src="vendors/echarts/echarts.min.js"></script>
    <script src="vendors/fontawesome/all.min.js"></script>
    <script src="vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="vendors/list.js/list.min.js"></script>
    <script src="assets/js/theme.js?var=<?=rand(100,1000)?>"></script>
    <script src="assets/js/persianDatepicker.js"></script>
    <script src="assets/js/plyr.min.js"></script>
    <script src="assets/js/custom_<?=$lng?>.js?var=<?=rand(100,1000)?>"></script>
</body>

</html>