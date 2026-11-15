<?php
session_start();
include './includer.php';
include '../include/jdf.php';
$currentPage = basename($_SERVER['REQUEST_URI']);
$index = basename($_SERVER['PHP_SELF']);
$tabstyle = 'class="active"';
if (empty($_SESSION['id_login'])) {
    header("Location: login.php");
    exit();
} else {
     $this_user_id = $_SESSION['id_login'];
    $result_member = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
    $read_member = mysqli_fetch_array($result_member);
    $user_role = $read_member['tbl_user_role'];
    $member_name = $read_member['tbl_user_username'];

    $access = !empty($_SESSION['access'])
        ? $_SESSION['access']
        : $read_member['tbl_user_access'];
    
    //echo $access;
    $accessBakhsh = $read_member['tbl_user_bakhsh'];
    
    $themecolor = $read_member['tbl_user_color'];
    $fluid = $read_member['tbl_user_fluid'];
    $userImg = $read_member['tbl_user_img'];
    $navbarStyle = $read_member['tbl_user_navbarStyle'];
    @$myContract = explode(",",$read_member['tbl_user_contract']);
    if ($access != '1') {
        $adminQuestion = $read_member['tbl_user_question'];
        $adminFile = $read_member['tbl_user_file'];
    } else {
        $adminQuestion = '1';
        $adminFile = '1';
    }
     $userCode = $read_member['tbl_user_usercode'];

    $op = $_SESSION['username_login'];
    $now_login = $_SESSION['username_login'];
    if (!empty($this_user_id) && strpos($thisaccess, $access) !== FALSE) {
        if ($access == 2 || $access == 5 || $access == 6) {
            $admin_course = $this_user_id;
        } else {
            $admin_course = '';
        }
    } elseif (empty($this_user_id)) {
        header("location: login.php");
        exit();
    } else {
        // header("location: access.php");
        // exit();
    }
}
$result =  Query("Select * From tbl_setting");
$read = mysqli_fetch_array($result);
?>

<!DOCTYPE html>
<html data-bs-theme="<?=(!empty($themecolor) ? $themecolor : 'light')?>" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
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
    <link rel="stylesheet" href="assets/css/select2.min.css" />
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
        <div class="<?=($fluid == '1' || $index =='report3.php' || $index =='report2.php' || $index =='report1.php'  || $index =='report4.php' || $index =='report5.php' || $index =='report6.php' ? 'container-fluid' : 'container')?>" data-layout="container">
            <script>
            var isFluid = JSON.parse(localStorage.getItem("isFluid"));
            if (isFluid) {
                var container = document.querySelector("[data-layout]");
                container.classList.remove("container");
                container.classList.add("container-fluid");
            }
            </script>
            <?php if ($index !='report3.php' && $index !='report4.php' && $index !='report5.php' && $index !='report6.php') { ?>
            <nav class="navbar navbar-light navbar-vertical navbar-expand-xl <?=($navbarStyle == '' || $navbarStyle == 'transparent'  ? '' : ($navbarStyle == 'inverted'  ? 'navbar-inverted' : ($navbarStyle == 'card'  ? 'navbar-card' : ($navbarStyle == 'vibrant'  ? 'navbar-vibrant' : ''))))?>">
                <script>
                var navbarStyle = localStorage.getItem("navbarStyle");
                if (navbarStyle && navbarStyle !== "transparent") {
                    document
                        .querySelector(".navbar-vertical")
                        .classList.add(`navbar-${navbarStyle}`);
                }
                </script>
                <div class="d-flex align-items-center">
                    <div class="toggle-icon-wrapper">
                        <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation">
                            <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                        </button>
                    </div>
                    <a class="navbar-brand" href="index.php">
                        <div class="d-flex align-items-center py-3">
                            <img src="assets/img/logo-text.png" alt="" width="40" />
                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <div class="navbar-vertical-content scrollbar">
                        <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                            <li class="nav-item">
                                <a class="nav-link" href="index.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-chart-bar"></span></span><span class="nav-link-text ps-1"><?=$lang[1300]?></span>
                                    </div>
                                </a>
                                <?php if($_SESSION['user_access'] == 'project'){?>
                                <a class="nav-link" href="user.php?id=<?= $this_user_id ?>&do=edit" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-gear-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1701]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="contract.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-list-ul"></span></span><span class="nav-link-text ps-1"><?=$lang[1702]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="checklistdefault.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-card-list"></span></span><span class="nav-link-text ps-1"><?=$lang[1940]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="pageview.php?code=3759070" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-question-octagon-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1705]?></span>
                                    </div>
                                </a>
                                <?php } ?>

                                <?php if($_SESSION['user_access'] == 'coach'){?>
                                <a class="nav-link" href="user.php?id=<?= $this_user_id ?>&do=edit" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-gear-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1701]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="contract-end.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-bricks"></span></span><span class="nav-link-text ps-1"><?=$lang[1783]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="result-coach.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-pie-chart-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1703]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="pageview.php?code=64A0B50" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-patch-question"></span></span><span class="nav-link-text ps-1"><?=$lang[1705]?></span>
                                    </div>
                                </a>
                                <?php } ?>
                                <?php if($_SESSION['user_access'] == 'karfarma'){?>
                                <a class="nav-link" href="user.php?id=<?= $this_user_id ?>&do=edit" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-gear-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1701]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="contract-end.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-bricks"></span></span><span class="nav-link-text ps-1"><?=$lang[1745]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="contract-list.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-info-circle"></span></span><span class="nav-link-text ps-1"><?=$lang[1746]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="pageview.php?code=5F738E5" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-patch-question"></span></span><span class="nav-link-text ps-1"><?=$lang[1705]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="message.php">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-patch-question-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1199]?></span>
                                    </div>
                                </a>
                                <?php } ?>
                                <?php if($_SESSION['user_access'] == 'mentor'){?>
                                <a class="nav-link" href="user.php?id=<?= $this_user_id ?>&do=edit" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-gear-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1701]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="contract.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-list-ul"></span></span><span class="nav-link-text ps-1"><?=$lang[1702]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="result.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-pie-chart-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1703]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="section-class.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-card-list"></span></span><span class="nav-link-text ps-1"><?=$lang[1704]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="pageview.php?code=B96BD33" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-question-octagon-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1705]?></span>
                                    </div>
                                </a>
                                <?php }  ?>

                                <?php  if($_SESSION['user_access'] == 'admin' || $_SESSION['user_access'] == 'mohtava'){?>
                                <a class="nav-link" href="pageview.php?code=DE86E37" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="bi-question-octagon-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1705]?></span>
                                    </div>
                                </a>
                                <?php if (access_level("1,8", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section101" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '101' ? 'true' : 'false'); ?>" aria-controls="section101">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-layers-half"></span></span><span class="nav-link-text ps-1"><?=$lang[1825]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '101' ? 'show' : ''); ?>" id="section101">
                                    <?php if($_SESSION['user_access'] == 'admin') {?>
                                    <li class="nav-item"><a class="nav-link" href="contentcreator.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1950]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="misc.php?t=21">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1835]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php } ?>
                                    <li class="nav-item"><a class="nav-link" href="mohtava.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1830]?></span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <?php } ?>

                                <?php } if (access_level("1", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section1" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '91' ? 'true' : 'false'); ?>" aria-controls="section1">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-layers-half"></span></span><span class="nav-link-text ps-1"><?=$lang[1301]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '91' ? 'show' : ''); ?>" id="section1">
                                    <li class="nav-item"><a class="nav-link" href="category_manage.php?t=5">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1302]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="category_manage.php?t=6">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1303]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="category_manage.php?t=4">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1304]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="class-uniq.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1305]?></span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <?php } ?>

                                <?php if (access_level("1,5", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section2" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '90' ? 'true' : 'false'); ?>" aria-controls="section2">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-menu-button-wide-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1306]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '90' ? 'show' : ''); ?>" id="section2">
                                    <li class="nav-item"><a class="nav-link" href="contract.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1307]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php if ($access == '5') { ?>
                                    <li class="nav-item"><a class="nav-link" href="allmessage.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1308]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                                <?php if (access_level("6", $access) == TRUE) { ?>
                                <a class="nav-link" href="coachlist.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-edit"></span></span><span class="nav-link-text ps-1"><?=$lang[1309]?></span>
                                    </div>
                                </a>
                                <?php } ?>


                                <?php if (access_level("1,4", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section3" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '10' || $Tabpage == '20' ? 'true' : 'false'); ?>" aria-controls="section3">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-people-fill"></span></span><span class="nav-link-text ps-1"><?=$lang[1310]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '10' || $Tabpage == '20' ? 'show' : ''); ?>" id="section3">
                                    <?php
                                $result_count = Query("SELECT tbl_member_id From tbl_member where tbl_member_delete='0'");
                                ?>
                                    <li class="nav-item"><a class="nav-link" href="member.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1451]?> (<?= mysqli_num_rows($result_count) ?>)</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="user.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1311]?></span>
                                            </div>
                                        </a>
                                    </li>

                                    <?php
                                        $result_count = Query("SELECT tbl_document_id From tbl_document");
                                        ?>
                                    <li class="nav-item"><a class="nav-link" href="document.php" style="display:none;">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1312]?> (<?= mysqli_num_rows($result_count) ?>)</span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item"><a class="nav-link" href="export.php?do=member">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1313]?></span>
                                            </div>
                                        </a>
                                    </li>

                                </ul>
                                <?php } ?>

                                <?php if (access_level("1", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section4" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '18' ? 'true' : 'false'); ?>" aria-controls="section4">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-ui-radios-grid"></span></span><span class="nav-link-text ps-1"><?=$lang[1314]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '18' ? 'show' : ''); ?>" id="section4">
                                    <li class="nav-item"><a class="nav-link" href="questionpublic.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1315]?></span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <?php } ?>


                                <?php if (access_level("1", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section5" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '25' ? 'true' : 'false'); ?>" aria-controls="section5">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-table"></span></span><span class="nav-link-text ps-1"><?=$lang[1316]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '25' ? 'show' : ''); ?>" id="section5">
                                    <li class="nav-item"><a class="nav-link" href="teacher.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1317]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="workshop.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1318]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php
                                $result_count = Query("SELECT tbl_register_id From tbl_register");
                                ?>
                                    <li class="nav-item"><a class="nav-link" href="register.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1319]?> (<?= mysqli_num_rows($result_count) ?>)</span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item"><a class="nav-link" href="invoice.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1320]?></span>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="nav-item"><a class="nav-link" href="logo.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1321]?></span>
                                            </div>
                                        </a>
                                    </li>

                                </ul>
                                <?php } ?>

                                <?php if (access_level("2", $access) == TRUE) { ?>
                                <a class="nav-link" href="classlist.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-map"></span></span><span class="nav-link-text ps-1"><?=$lang[1305]?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="register.php" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-map"></span></span><span class="nav-link-text ps-1"><?=$lang[1319]?></span>
                                    </div>
                                </a>
                                <?php } ?>


                                <?php if (access_level("1,3", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section6" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '11' ? 'true' : 'false'); ?>" aria-controls="section6">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-receipt-cutoff"></span></span><span class="nav-link-text ps-1"><?=$lang[1322]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '11' ? 'show' : ''); ?>" id="section6">
                                    <li class="nav-item"><a class="nav-link" href="misc.php?t=1">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[41]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="misc.php?t=2">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[503]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="misc.php?t=4">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1202]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="misc.php?t=5">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1323]?></span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <?php } ?>

                                <?php if (access_level("1,3", $access) == TRUE) { ?>
                                <a class="nav-link dropdown-indicator" href="#section8" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '901' ? 'true' : 'false'); ?>" aria-controls="section7">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-gear-wide-connected"></span></span><span class="nav-link-text ps-1">گزارشات</span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '901' ? 'show' : ''); ?>" id="section8">

                                    <li class="nav-item"><a class="nav-link" href="report1.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1979]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="report2.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1998]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="report3.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[2000]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="report7.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[2058]?></span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                                <a class="nav-link dropdown-indicator" href="#section7" role="button" data-bs-toggle="collapse" aria-expanded="<?=($Tabpage == '6' ? 'true' : 'false'); ?>" aria-controls="section7">
                                    <div class="d-flex align-items-center"><span class="nav-link-icon"><span class="bi-gear-wide-connected"></span></span><span class="nav-link-text ps-1"><?=$lang[10]?></span>
                                    </div>
                                </a>
                                <ul class="nav collapse <?=($Tabpage == '6' ? 'show' : ''); ?>" id="section7">
                                    <li class="nav-item"><a class="nav-link" href="registeruserlist.php" style="display: none;">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1324]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="pages.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[79]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="certificate.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1325]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php if (access_level("1", $access) == TRUE) { ?>
                                    <li class="nav-item"><a class="nav-link" href="code.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1328]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="message.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1199]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="setting.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1330]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="lng_editorfa.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1"><?=$lang[1331]?></span>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link" href="lng_panelfa.php">
                                            <div class="d-flex align-items-center"><span class="nav-link-text ps-1">زبان مدیریت پنل</span>
                                            </div>
                                        </a>
                                    </li>
                                    <?php } ?>
                                </ul>
                                <?php } ?>
                        </ul>
                    </div>
                </div>
            </nav>
            <?php } ?>
            <div class="content">
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                    </button>
                    <a class="navbar-brand me-1 me-sm-3" href="index.php">
                        <div class="d-flex align-items-center">
                            <img class="me-2" src="assets/img/logo-text.png" alt="" width="40" />
                        </div>
                    </a>
                    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
                        <li class="nav-item px-2">
                            <div class="theme-control-toggle fa-icon-wait">
                                <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
                                <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to light theme"><span class="fas fa-sun "></span></label>
                                <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to dark theme"><span class="fas fa-moon "></span></label>
                            </div>
                        </li>


                        <?php if($_SESSION['user_access'] == 'mentor'){?>

                        <li class="nav-item dropdown">
                            <a class="nav-link px-0 fa-icon-wait" id="navbarDropdownNotificationNote" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-comment-dots" data-fa-transform="shrink-6" style="font-size: 33px"></span></a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotificationNote">
                                <div class="card card-notification shadow-none">
                                    <?php 


                                        $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_mentor ='$this_user_id' and tbl_skillmessage_delete ='0' order by tbl_skillmessage_id DESC limit 0,10");
                                         $messageRows = mysqli_num_rows($resultMessage);
                                        ?>
                                    <div class="scrollbar-overlay <?php ($messageRows <= 0 ? 'message-empty' : '' )?>" style="max-height: 19rem">
                                        <div class="list-group list-group-flush fw-normal fs--1">
                                            <?php
                                                if($messageRows <= 0)
                                                {
                                                    echo '<div class="list-group-item p-3">'.$lang[57].'</div>';
                                                }
                                                else {
                                                    while ($readMessage = mysqli_fetch_array($resultMessage)) {
                                                        echo ' <div class="list-group-item">
                                                        <a class="notification notification-flush" href="allmessage.php?do=view&code='.$readMessage['tbl_skillmessage_code'].'">
        
                                                            <div class="notification-body">
                                                                <p class="mb-1">
                                                                    '.$readMessage['tbl_skillmessage_title'].'
                                                                </p>
                                                                <span class="notification-time">'.toPersianNum($readMessage['tbl_skillmessage_dataadd']).'</span>
                                                            </div>
                                                        </a>
                                                    </div>';
                                                    }
                                                }
                                                ?>
                                        </div>
                                    </div>
                                    <?php 
                                    if($messageRows > 0)
                                    {
                                        ?>
                                    <div class="card-footer text-center border-top">
                                        <a class="card-link d-block" href="allmessage.php"><?=$lang[1699]?></a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>


                        <?php } else if ($_SESSION['user_access'] == 'mohtava') { } else {?>
                        <li class="nav-item dropdown">
                            <a class="nav-link px-0 fa-icon-wait" id="navbarDropdownNotificationNote" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-comment-dots" data-fa-transform="shrink-6" style="font-size: 33px"></span></a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotificationNote">
                                <div class="card card-notification shadow-none">
                                    <?php 
                                        $resultMessage = Query("SELECT * From tbl_message where tbl_message_userid !='' order by tbl_message_id DESC limit 0,10");
                                        $messageRows = mysqli_num_rows($resultMessage);
                                        ?>
                                    <div class="scrollbar-overlay <?php ($messageRows <= 0 ? 'message-empty' : '' )?>" style="max-height: 19rem">
                                        <div class="list-group list-group-flush fw-normal fs--1">
                                            <?php
                                                if($messageRows <= 0)
                                                {
                                                    echo '<div class="list-group-item">'.$lang[57].'</div>';
                                                }
                                                else {
                                                    while ($readMessage = mysqli_fetch_array($resultMessage)) {
                                                        echo ' <div class="list-group-item">
                                                        <a class="notification notification-flush" href="?do=view&code='.$readMessage['tbl_message_code'].'/">
        
                                                            <div class="notification-body">
                                                                <p class="mb-1">
                                                                    '.$readMessage['tbl_message_title'].'
                                                                </p>
                                                                <span class="notification-time">'.toPersianNum($readMessage['tbl_message_date']).'</span>
                                                            </div>
                                                        </a>
                                                    </div>';
                                                    }
                                                }
                                                ?>
                                        </div>
                                    </div>
                                    <?php 
                                    if($messageRows > 0)
                                    {
                                        ?>
                                    <div class="card-footer text-center border-top">
                                        <a class="card-link d-block" href="message.php"><?=$lang[1699]?></a>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </li>
                        <?php } ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-xl">
                                    <?php
                                    // $result_profile = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
                                    // $read_profile = mysqli_fetch_array($result_profile);
                                    ?>
                                    <img class="rounded-circle" src="<?=(!empty($userImg) ? '../upload/'.$userImg : 'assets/img/no-image.jpg')?>" alt="" />
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                                    <?=showRole($user_role)?>
                                    <?=($_SESSION['user_access'] != 'mohtava' ? '<a class="dropdown-item" href="user.php?id='. $this_user_id .'&do=edit">'.$lang[382].'</a>' : '' )?>
                                    <a class="dropdown-item" href="<?=($_SESSION['user_access'] == 'karfarma' ? 'login.php?do=logout' : 'login.php?do=logout')?>"><?= $lang[387] ?></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>