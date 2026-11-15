<?php
ob_start();
include './includer.php';
$index = basename($_SERVER['PHP_SELF']);
$seo_code = Query("Select * from tbl_seo");
$read_seo = mysqli_fetch_array($seo_code);
$ip = myip();
$upload_profile_dir = "upload/profile/";

?>
<!DOCTYPE html>
<html data-bs-theme="<?= (!empty($themecolor) ? $themecolor : 'light') ?>" lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $read['tbl_setting_title_' . $lng] . @($pagetitle) ?></title>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $site_url ?>assets/img/favicons/apple-touch-icon.png?ver=1">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $site_url ?>assets/img/favicons/favicon-32x32.png?ver=1">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $site_url ?>assets/img/favicons/favicon-16x16.png?ver=1">
    <link rel="shortcut icon" type="image/png" sizes="16x16" href="<?= $site_url ?>assets/img/favicons/favicon-16x16.png?ver=1">
    <link rel="manifest" href="<?= $site_url ?>assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="<?= $site_url ?>assets/img/favicons/mstile-150x150.png?ver=1">
    <meta name="theme-color" content="#ffffff">
    <script src="<?= $site_url ?>assets/js/config.js"></script>
    <script src="<?= $site_url ?>vendors/simplebar/simplebar.min.js"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="<?= $site_url ?>vendors/simplebar/simplebar.min.css" rel="stylesheet">
    <link href="<?= $site_url ?>assets/css/theme-rtl.css?ver=<?= rand(10, 1000) ?>" rel="stylesheet" id="style-rtl">
    <link href="<?= $site_url ?>assets/css/theme.css?ver=<?= rand(10, 1000) ?>" rel="stylesheet" id="style-default">
    <link href="<?= $site_url ?>assets/css/user-rtl.css" rel="stylesheet" id="user-style-rtl">
    <link href="<?= $site_url ?>assets/css/user.css" rel="stylesheet" id="user-style-default">
    <link rel="stylesheet" href="<?= $site_url ?>assets/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="<?= $site_url ?>assets/css/plyr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.0/font/bootstrap-icons.css" />
    <link href="<?= $site_url ?>assets/css/custom_<?= $lng ?>.css?ver=<?= rand(10, 1000) ?>" rel="stylesheet" id="user-style-default">
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

        <?= ($index == 'register.php' ? '<div class="container-fluid">' : '<div class="' . ($fluid == '1' ? 'container-fluid' : 'container') . '" data-layout="container">') ?>
        <script>
            var isFluid = JSON.parse(localStorage.getItem("isFluid"));
            if (isFluid) {
                var container = document.querySelector("[data-layout]");
                container.classList.remove("container");
                container.classList.add("container-fluid");
            }
        </script>
        <?php if ($index != 'register.php') { ?>
            <nav class="navbar navbar-light navbar-vertical navbar-expand-xl <?= ($navbarStyle == '' || $navbarStyle == 'transparent'  ? '' : ($navbarStyle == 'inverted'  ? 'navbar-inverted' : ($navbarStyle == 'card'  ? 'navbar-card' : ($navbarStyle == 'vibrant'  ? 'navbar-vibrant' : '')))) ?>">
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
                    <a class="navbar-brand" href="<?= $site_url . $lng ?>/mypanel/">
                        <div class="d-flex align-items-center py-3">
                            <img src="<?= $site_url ?>assets/img/logo-text.png" alt="" width="40" />
                        </div>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <div class="navbar-vertical-content scrollbar">
                        <ul class="navbar-nav flex-column mb-3" id="navbarVerticalNav">
                            <li class="nav-item">

                                <?php if ($userModel == '0') { ?>
                                    <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/" role="button">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="far fa-chart-bar"></span></span><span class="nav-link-text ps-1"><?= $lang[1177] ?></span>
                                        </div>
                                    </a>
                                <?php } ?>
                                <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/editprofile/" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-edit"></span></span><span class="nav-link-text ps-1"><?= $lang[382] ?></span>
                                    </div>
                                </a>
                                <?php if ($userModel == '0') { ?>
                                    <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/assessment/" role="button">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="fab fa-facebook-messenger"></span></span><span class="nav-link-text ps-1"><?= $lang[1295] ?></span>
                                        </div>
                                    </a>
                                    <!-- <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/resume/" role="button">
                                <div class="d-flex align-items-center">
                                    <span class="nav-link-icon"><span class="far fa-id-card"></span></span><span class="nav-link-text ps-1"><?= $lang[411] ?></span>
                                </div>
                            </a> -->
                                    <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/skilllist/" role="button">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="far fa-map"></span></span><span class="nav-link-text ps-1"><?= $lang[718] ?></span>
                                        </div>
                                    </a>
                                    <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/certificate/" role="button">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="far fa-object-group"></span></span><span class="nav-link-text ps-1"><?= $lang[851] ?></span>
                                        </div>
                                    </a>
                                    <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/skilllistarchive/" role="button">
                                        <div class="d-flex align-items-center">
                                            <span class="nav-link-icon"><span class="fas fa-list-alt"></span></span><span class="nav-link-text ps-1"><?= $lang[1364] ?></span>
                                        </div>
                                    </a>
                                <?php } ?>

                                <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/ticket/" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="far fa-comment-dots"></span></span><span class="nav-link-text ps-1"><?= $lang[598] ?></span>
                                    </div>
                                </a>

                                <a class="nav-link" href="<?= $site_url . $lng . '/mypanel/page/0546D36/' ?>" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-question-circle"></span></span><span class="nav-link-text ps-1"><?= $lang[1343] ?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="https://expertsacademy.ir/cat_site.php?lng=fa&do=cat" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-book"></span></span><span class="nav-link-text ps-1"><?= $lang[1344] ?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/mycontent/" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-list"></span></span><span class="nav-link-text ps-1"><?= $lang[1411] ?></span>
                                    </div>
                                </a>
                                <a class="nav-link" href="<?= $site_url . $lng ?>/mypanel/mycourse/" role="button">
                                    <div class="d-flex align-items-center">
                                        <span class="nav-link-icon"><span class="fas fa-list"></span></span><span class="nav-link-text ps-1"><?= $lang[1417] ?></span>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            <div class="content">
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation">
                        <span class="navbar-toggle-icon"><span class="toggle-line"></span></span>
                    </button>
                    <a class="navbar-brand me-1 me-sm-3" href="index.html">
                        <div class="d-flex align-items-center">
                            <img class="me-2" src="<?= $site_url ?>assets/img/logo-text.png" alt="" width="40" />
                        </div>
                    </a>
                    <ul class="navbar-nav align-items-center d-none d-lg-block">
                        <li class="nav-item topname">
                            <?php
                            // $result_profile = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
                            // $read_profile = mysqli_fetch_array($result_profile);
                            // echo $read_profile['tbl_member_fname'].' '.$read_profile['tbl_member_lname'].' '.$lang[486];
                            ?>
                        </li>
                    </ul>
                    <ul class="navbar-nav navbar-nav-icons ms-auto flex-row align-items-center">
                        <li class="nav-item px-2">
                            <div class="theme-control-toggle fa-icon-wait">
                                <input class="form-check-input ms-0 theme-control-toggle-input" id="themeControlToggle" type="checkbox" data-theme-control="theme" value="dark" />
                                <label class="mb-0 theme-control-toggle-label theme-control-toggle-light" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to light theme"><span class="fas fa-sun fs-0"></span></label>
                                <label class="mb-0 theme-control-toggle-label theme-control-toggle-dark" for="themeControlToggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Switch to dark theme"><span class="fas fa-moon fs-0"></span></label>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link px-0 fa-icon-wait" id="navbarDropdownNotification" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-bell" data-fa-transform="shrink-6" style="font-size: 33px"></span></a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotification">
                                <div class="card card-notification shadow-none">
                                    <?php
                                    $date = strtotime(date("Y/m/d"));
                                    $all = '';
                                    $resultPlan = Query("SELECT * From tbl_plan where tbl_plan_menti='$user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
                                    while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                        $endate = strtotime($readPlan['tbl_plan_dateenglish']);
                                        if (($endate - $date) <= 259200) {
                                            $all .= '<div class="list-group-item">
                                                <a class="notification notification-flush" href="#">

                                                    <div class="notification-body">
                                                        <p class="mb-1">
                                                            ' . $lang[855] . ' ' . $lang[714] . '
                                                        </p>
                                                    </div>
                                                </a>
                                            </div>';
                                            break;
                                        } else {
                                            $all .= '';
                                        }
                                    }
                                    $resultQuestion = Query("select * from tbl_memberquestion where tbl_memberquestion_check='0' and tbl_memberquestion_admincheck='0'  and tbl_memberquestion_userid='$user_id' ");
                                    if (mysqli_num_rows($resultQuestion) > 0) {
                                        $all .= '<div class="list-group-item">
                                                <a class="notification notification-flush" href="#">

                                                    <div class="notification-body">
                                                        <p class="mb-1">
                                                            ' . $lang[855] . ' ' . $lang[1330] . '
                                                        </p>
                                                    </div>
                                                </a>
                                            </div>';
                                    }

                                    $resultInterview = Query("SELECT * From tbl_interview where ( tbl_interview_reportdate='' or tbl_interview_reportdate is NULL ) and (tbl_interview_model='0' or tbl_interview_model is NULL ) and tbl_interview_menti='$user_id' and tbl_interview_lng='$lng' and tbl_interview_delete='0'  ");
                                    if (mysqli_num_rows($resultInterview) > 0) {
                                        $all .= '<div class="list-group-item">
                                                <a class="notification notification-flush" href="#">

                                                    <div class="notification-body">
                                                        <p class="mb-1">
                                                            ' . $lang[855] . ' ' . $lang[1332] . '
                                                        </p>
                                                    </div>
                                                </a>
                                            </div>';
                                    }
                                    ?>
                                    <div class="scrollbar-overlay <?= ($all == '' ? 'message-empty' : '') ?>" style="max-height: 19rem">
                                        <div class="list-group list-group-flush fw-normal fs--1">
                                            <?php
                                            if ($all == '') {
                                                echo '<div class="list-group-item">' . $lang[1331] . '</div>';
                                            } else {
                                                echo $all;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>


                        <li class="nav-item dropdown">
                            <a class="nav-link px-0 fa-icon-wait" id="navbarDropdownNotificationNote" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-hide-on-body-scroll="data-hide-on-body-scroll"><span class="fas fa-comment-dots" data-fa-transform="shrink-6" style="font-size: 33px"></span></a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end dropdown-menu-card dropdown-menu-notification dropdown-caret-bg" aria-labelledby="navbarDropdownNotificationNote">
                                <div class="card card-notification shadow-none">
                                    <?php
                                    $resultMessage = Query("SELECT tbl_skillmessage_code,tbl_skillmessage_title,tbl_skillmessage_dataadd From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$user_id' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' order by tbl_skillmessage_id DESC limit 0,5 ");
                                    $messageRows = mysqli_num_rows($resultMessage);
                                    ?>
                                    <div class="scrollbar-overlay <?= ($messageRows <= 0 ? 'message-empty' : '') ?>" style="max-height: 19rem">
                                        <div class="list-group list-group-flush fw-normal fs--1">
                                            <?php
                                            if ($messageRows <= 0) {
                                                echo '<div class="list-group-item">' . $lang[1282] . '</div>';
                                            } else {
                                                while ($readMessage = mysqli_fetch_array($resultMessage)) {
                                                    echo ' <div class="list-group-item">
                                                        <a class="notification notification-flush" href="' . $site_url . $lng . '/mypanel/viewskillmessage/' . $readMessage['tbl_skillmessage_code'] . '/">
        
                                                            <div class="notification-body">
                                                                <p class="mb-1">
                                                                    ' . $readMessage['tbl_skillmessage_title'] . '
                                                                </p>
                                                                <span class="notification-time">' . toPersianNum($readMessage['tbl_skillmessage_dataadd']) . '</span>
                                                            </div>
                                                        </a>
                                                    </div>';
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <?php
                                    // if($messageRows > 0)
                                    // {
                                    ?>
                                    <div class="card-footer text-center border-top">
                                        <a class="card-link d-block" href="<?= $site_url . $lng ?>/mypanel/message/"><?= $lang[852] ?></a>
                                    </div>
                                    <?php //} 
                                    ?>
                                </div>
                            </div>
                        </li>


                        <li class="nav-item dropdown">
                            <a class="nav-link pe-0 ps-2" id="navbarDropdownUser" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <div class="avatar avatar-xl">
                                    <?php
                                    $result_profile = Query("Select * from tbl_member where tbl_member_id = '$user_id' ");
                                    $read_profile = mysqli_fetch_array($result_profile);
                                    ?>
                                    <img class="rounded-circle" src="<?= (!empty($read_profile['tbl_member_profile']) ? $site_url . 'upload/profile/' . $read_profile['tbl_member_profile'] : $site_url . 'assets/img/no-image.jpg') ?>" alt="" />
                                </div>
                            </a>
                            <div class="dropdown-menu dropdown-caret dropdown-caret dropdown-menu-end py-0" aria-labelledby="navbarDropdownUser">
                                <div class="bg-white dark__bg-1000 rounded-2 py-2">
                                    <a class="dropdown-item" href="<?= $site_url . $lng ?>/mypanel/editprofile/"><?= $lang[382] ?></a>
                                    <a class="dropdown-item" href="<?= $site_url . $lng ?>/mypanel/logout/"><?= $lang[387] ?></a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </nav>
            <?php } ?>
            <?= (!empty($navigation) ? '<div class="col-12 col-lg-12 col-md-12 navigation ">' . $navigation . '</div>'  : ''); ?>