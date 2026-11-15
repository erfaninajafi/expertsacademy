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
    <style>
    .btn-primary {
        color: #fff;
        background-color: #e65f25 !important;
        border-color: #e65f25 !important;
        box-shadow: unset !important;
        font-size: 13px !important;
        transform: unset !important;
    }

    body {
        background-color: #eee;
    }
    </style>
</head>

<body>


    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">

                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <img src="asset/img/logo.svg">
                        </div>
                        <?php if (!isset($do)) { ?>
                        <h4 class="mb-2">Welcome to Panel! 👋</h4>
                        <p class="mb-4">Please sign-in to your account and start the adventure</p>
                        <?php

                            if (isset($_SESSION['error'])) {
                                echo '<p class="error">' . $_SESSION['error'] . '</p>';
                                unset($_SESSION['error']);
                            };

                            ?>
                        <form id="formAuthentication" class="mb-3" action="?do=checklogin" method="POST">
                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter Your Mobile Number" autofocus required />
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
                                <button class="btn btn-primary d-grid w-100" type="submit" name="login" style="font-size: 13px;">Sign in</button>
                            </div>

                        </form>
                        <?php }  else if (isset($do) && $do == 'checklogin') {
                            $username = '';
                            $password = '';
                            if (isset($_POST['login'])) {
                                $username = $_POST['mobile'];
                                $password = $_POST['password'];

                                if (empty($username) || empty($password)) {
                                    $_SESSION['error'] = 'Please enter all account information';
                                    header("Location: login.php");
                                    exit();
                                } else {
                                    $password = hash("md5", $password);
                                    $query = Query("SELECT * from tbl_contract WHERE tbl_contract_karmobile = '$username'  AND tbl_contract_karpassword = '$password' limit 0,1");
                                    $count  = mysqli_num_rows($query);
                                    if ($count == '1') {
                                        $read = mysqli_fetch_array($query);
                                        $userid = $read['tbl_contract_karmobile'];
                                        $date = jdate("Y/m/d : a:h:s");
                                        $_SESSION['id_login'] = $userid;
                                        Query("update tbl_contract set tbl_contract_karlogin = '$date'  where tbl_contract_karmobile = '$userid' ");
                                        $_SESSION['username_login'] = $read['tbl_contract_karname'];
                                        header("Location: index.php");
                                    } else {
                                        $_SESSION['error'] = 'Sorry !! Not Found with this information';
                                        header("Location: login.php");
                                    }
                                }
                            }
                        } else if (isset($do) && $do == 'logout') {
                            unset($_SESSION['username_login']);
                            unset($_SESSION['id_login']);
                            unset($_SESSION['user_lng']);
                            unset($_SESSION['user_name']);
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