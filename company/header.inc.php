<?php
session_start();
include './includer.php';
include '../include/jdf.php';
$currentPage = basename($_SERVER['REQUEST_URI']);
$tabstyle = 'class="active"';
if (empty($_SESSION['id_login'])) {
    header("Location: login.php");
    exit();
} else {
    $this_user_id = $_SESSION['id_login'];

}
$result =  Query("Select * From tbl_setting");
$read = mysqli_fetch_array($result);
?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="rtl" data-theme="theme-default" data-assets-path="asset/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $lang[11] . ' - ' . @$pagetitle; ?></title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="asset/img/favicon/favicon.ico?ver=1" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="asset/vendor/fonts/boxicons.css" />
    <link rel="stylesheet" href="asset/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="asset/vendor/css/theme-default.css?var=<?= rand(10, 100) ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="asset/css/demo.css" />
    <link rel="stylesheet" href="asset/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="asset/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="asset/css/select2.min.css" />
    <link rel="stylesheet" href="asset/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="asset/css/chartist.min.css">
    <link rel="stylesheet" href="asset/css/custom-new.css?var=<?= rand(10, 100000) ?>" />
    <script src="asset/vendor/libs/jquery/jquery.js"></script>
    <script src="asset/js/chartist.min.js"></script>
    <script src="asset/vendor/js/helpers.js"></script>
    <script src="asset/js/config.js"></script>
    <?php
    if (isset($do) && $do == 'add' || @$do == 'edit') {
        echo '<script src="asset/plugins/jQueryUI/jquery-ui.min.js"></script>';
    }
    ?>
</head>

<body>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="asset/img/logo.svg">
                        </span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="bx bx-chevron-left bx-sm align-middle"></i>
                    </a>
                </div>
                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1">
                    <li class="menu-item active">
                        <a href="index.php" class="menu-link">
                            <i class="menu-icon tf-icons bx bx-home-circle"></i>
                            <div data-i18n="Analytics">داشبورد</div>
                        </a>
                    </li>

                </ul>
            </aside>
            <div class="layout-page">

                <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="bx bx-menu bx-sm"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex " id="navbar-collapse">
                        <ul class="navbar-nav flex-row">

                            <li>
                                <div class="dropdown-divider"></div>
                            </li>

                            <li>
                                <a class="dropdown-item" href="#" target="_blank">
                                    <i class="bx bx-cog me-2"></i>
                                    <span class="align-middle">نمایش سامانه</span>
                                </a>
                            </li>
                            <div class="dropdown-divider"></div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="login.php?do=logout">
                                    <i class="bx bx-power-off me-2"></i>
                                    <span class="align-middle">خروج از پنل</span>
                                </a>
                            </li>
                        </ul>

                        </ul>
                    </div>
                </nav>

                <div class="content-wrapper">

                    <div class="container-fluid flex-grow-1 container-p-y">