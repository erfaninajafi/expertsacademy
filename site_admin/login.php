<?php
ob_start();
session_start();
if (isset($_GET['do'])) {
    $do = $_GET['do'];
}
include '../include_site/config.php';
include '../include_site/function.php';

?>

<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="asset/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login Panel</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="asset/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="asset/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="asset/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="asset/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="asset/css/demo.css" />
    <link rel="stylesheet" href="asset/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="asset/vendor/css/pages/page-auth.css" />
    <link rel="stylesheet" href="asset/css/custom.css" />
    <script src="asset/vendor/js/helpers.js"></script>
    <script src="asset/js/config.js"></script>
</head>

<body>


    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <div class="card">
                    <div class="card-body">

                        <?php if (!isset($do)) { ?>

                        <?php

                            if (isset($_SESSION['error'])) {
                                echo '<p class="error">' . $_SESSION['error'] . '</p>';
                                unset($_SESSION['error']);
                            };

                            ?>
                        <form id="formAuthentication" class="mb-3" action="?do=checklogin" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus required />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>

                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password" required />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" type="submit" name="login">Sign in</button>
                            </div>
                            <div class="text-center">
                                <a href="?do=forgotpassword" class="d-flex align-items-center justify-content-center">
                                    <small>Forgot Password?</small>
                                    <i class="bx bx-chevron-right scaleX-n1-rtl bx-sm"></i>
                                </a>
                            </div>

                        </form>
                        <?php } else if (isset($do) && $do == 'forgotpassword') { ?>
                        <h4 class="mb-2">Forgot Password? 🔒</h4>
                        <p class="mb-4">Enter your email and we'll send you instructions to reset your password</p>
                        <?php

                            if (isset($_SESSION['error'])) {
                                echo '<p class="error">' . $_SESSION['error'] . '</p>';
                                unset($_SESSION['error']);
                            };

                            ?>
                        <form id="formAuthentication" class="mb-3" action="?do=checkemail" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email" placeholder="Enter your email" autofocus required />
                            </div>
                            <button class="btn btn-primary d-grid w-100" name="sendpassword">Send Reset Link</button>
                        </form>
                        <div class="text-center">
                            <a href="login.php" class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>
                                Back to login
                            </a>
                        </div>
                        <?php } else if (isset($do) && $do == 'checkemail') {

                            if (isset($_POST['sendpassword'])) {
                                $email =  $_POST['email'];
                                if (empty($email)) {
                                    $_SESSION['error'] = 'Please enter email address';
                                    header("Location: login.php?do=forgotpassword");
                                    exit();
                                } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                    $_SESSION['error'] = 'Your email format not correct!';
                                    header("Location: login.php?do=forgotpassword");
                                    exit();
                                } else {
                                    $result = Query("SELECT * from tbl_user Where tbl_user_email = '$email' ");
                                    $count = mysqli_num_rows($result);
                                    if ($count == '1') {
                                        $read = mysqli_fetch_array($result);
                                        $npass = randomPassword();
                                        $md5pass = hash("md5", $npass);
                                        $userid = $read['tbl_user_id'];
                                        $to = $email;
                                        $subject = 'New Password From ' . $_SERVER['SERVER_NAME'];
                                        $headers = "MIME-Version: 1.0" . "\r\n";
                                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                                        $headers .= 'From: <info@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
                                        $message = 'New password is =<b>' .  $npass . '</b><br />For login in page please click here:<a href="http://www.' . $_SERVER['SERVER_NAME'] . '/system/login.php" >[Login in site]</a>';
                                        mail($to, $subject, $message, $headers);
                                        $update = Query("Update tbl_user SET tbl_user_password = '$md5pass' Where tbl_user_id = '$userid' ");
                                        $_SESSION['error'] = 'Password has been sent';
                                        header("Location: login.php");
                                        exit();
                                    } else {
                                        $_SESSION['error'] = 'Your Email Not found in database !';
                                        header("Location: login.php?do=forgotpassword");
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
                                    $_SESSION['error'] = 'Please enter all account information';
                                    header("Location: login.php");
                                    exit();
                                } else {
                                    $password = hash("md5", $password);
                                    $query = Query("SELECT * from tbl_user WHERE tbl_user_username = '$username'  AND tbl_user_password = '$password' ");
                                    $count  = mysqli_num_rows($query);
                                    if ($count == '1') {
                                        $read = mysqli_fetch_array($query);
                                        $_SESSION['siteadmin_login'] = $read['tbl_user_id'];
                                        $_SESSION['username_login'] = $read['tbl_user_username'];
                                        $_SESSION['user_lng'] = $read['tbl_user_lng'];
                                        header("Location: index.php");
                                    } else {
                                        $_SESSION['error'] = 'Sorry !! Not Found with this information';
                                        header("Location: login.php");
                                    }
                                }
                            }
                        } else if (isset($do) && $do == 'logout') {
                            unset($_SESSION['siteadmin_login']);
                            unset($_SESSION['username_login']);
                            unset($_SESSION['user_lng']);
                            //        setcookie($get_domain, '', time() - 3600);
                            
                            header("Location: index.php");
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="asset/vendor/libs/jquery/jquery.js"></script>
    <script src="asset/vendor/libs/popper/popper.js"></script>
    <script src="asset/vendor/js/bootstrap.js"></script>
    <script src="asset/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="asset/vendor/js/menu.js"></script>
    <script src="asset/js/main.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>