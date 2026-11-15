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
    $result_member = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
    $read_member = mysqli_fetch_array($result_member);
    $member_name = $read_member['tbl_user_username'];
    $access = $read_member['tbl_user_access'];
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
    } else {
        header("location: access.php");
        exit();
    }
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

                    <?php if (access_level("1", $access) == TRUE) { ?>
                    <li class="menu-item <?php echo ($Tabpage == '91') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">مدیریت اطلاعات پایه </div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="category_manage.php?t=5" class="menu-link">
                                    <div data-i18n="Without navbar">لیست بخش ها</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="category_manage.php?t=6" class="menu-link">
                                    <div data-i18n="Without navbar">حوزه کاربرد</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="category_manage.php?t=4" class="menu-link">
                                    <div data-i18n="Without navbar">لیست مهارت ها</div>
                                </a>
                            </li>

                            <?php
                                $result_count = Query("SELECT * From tbl_class where tbl_class_lng = '$language_default' ");
                                ?>
                            <li class="menu-item">
                                <a href="class-uniq.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست دوره ها
                                    </div>
                                </a>
                            </li>



                        </ul>
                    </li>

                    <?php } ?>

                    <?php if (access_level("1,5", $access) == TRUE) { ?>

                    <li class="menu-item <?php echo ($Tabpage == '90') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts"><?=($access == '1' ? 'مدیریت مهارت آموزی' : 'مدیریت مهارت آموزی')?></div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="contract.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست پروژه ها</div>
                                </a>
                            </li>
                            <?php if ($access == '5') { ?>
                            <li class="menu-item">
                                <a href="allmessage.php" class="menu-link">
                                    <div data-i18n="Without navbar">مشاهده تمام پیام ها</div>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($adminQuestion == '1') { ?>
                            <!-- <li class="menu-item">
                                <a href="levelquestion.php" class="menu-link">
                                    <div data-i18n="Without navbar">سوالات ارزیابی</div>
                                </a>
                            </li> -->
                            <?php } ?>
                        </ul>
                    </li>


                    <?php } ?>
                    <?php if (access_level("6", $access) == TRUE) { ?>

                    <li class="menu-item">
                        <a href="coachlist.php" class="menu-link">
                            <div data-i18n="Without navbar">مدیریت پروژه های کوچینگ</div>
                        </a>
                    </li>




                    <?php
                    } ?>
                    <?php if (access_level("1,4", $access) == TRUE) { ?>
                    <li class="menu-item <?php echo ($Tabpage == '10' || $Tabpage == '20') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">مدیریت اعضا</div>
                        </a>
                        <ul class="menu-sub">

                            <?php
                                $result_count = Query("SELECT tbl_member_id From tbl_member where tbl_member_delete='0'");
                                ?>
                            <li class="menu-item">
                                <a href="member.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست اعضا (<?= mysqli_num_rows($result_count) ?>)
                                    </div>
                                </a>
                            </li>

                            <?php
                                $result_count = Query("SELECT tbl_document_id From tbl_document");
                                ?>
                            <li class="menu-item">
                                <a href="document.php" class="menu-link">
                                    <div data-i18n="Without navbar">مدارک (<?= mysqli_num_rows($result_count) ?>)</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="export.php?do=member" class="menu-link">
                                    <div data-i18n="Without navbar">گزارش اعضا</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php }

                    if (access_level("1", $access) == TRUE) { ?>

                    <li class="menu-item <?php echo ($Tabpage == '18') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">مدیریت ارزیابی مهارت آموزی</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="questionpublic.php" class="menu-link">
                                    <div data-i18n="Accordion"> سوالات ارزیابی مهارت</div>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php } 

                    if (access_level("1", $access) == TRUE) { ?>
                    <li class="menu-item <?php echo ($Tabpage == '25') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">مدیریت ثبت نام ها و مالی</div>
                        </a>
                        <ul class="menu-sub">

                            <?php
                                $result_count = Query("SELECT tbl_member_id From tbl_member where tbl_member_delete='0'");
                                ?>
                            <!-- <li class="menu-item">
                                <a href="category_manage.php?t=3" class="menu-link">
                                    <div data-i18n="Without navbar">دسته بندی دوره
                                    </div>
                                </a>
                            </li> -->
                            <li class="menu-item">
                                <a href="teacher.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست اساتید</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="workshop.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست کارگاه ها </div>
                                </a>
                            </li>

                            <?php
                                $result_count = Query("SELECT * From tbl_register");
                                ?>
                            <li class="menu-item">
                                <a href="register.php" class="menu-link">
                                    <div data-i18n="Without navbar">ثبت نام ها (<?= mysqli_num_rows($result_count) ?>)
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="invoice.php" class="menu-link">
                                    <div data-i18n="Without navbar">صورت حسابها</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="logo.php" class="menu-link">
                                    <div data-i18n="Without navbar">مدیریت برگزار کننده </div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php } ?>


                    <?php if (access_level("2", $access) == TRUE) { ?>
                    <li class="menu-item">
                        <a href="classlist.php" class="menu-link">
                            <div data-i18n="Without navbar">لیست دوره ها</div>
                        </a>
                    </li>
                    <li class="menu-item">
                        <a href="register.php" class="menu-link">
                            <div data-i18n="Without navbar">ثبت نام ها</div>
                        </a>
                    </li>
                    <?php } ?>






                    <?php if (access_level("1,3", $access) == TRUE) { ?>
                    <li class="menu-item <?php echo ($Tabpage == '11') ? 'open' : ''; ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-layout"></i>
                            <div data-i18n="Layouts">محتوی متنی عمومی</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="misc.php?t=1" class="menu-link">
                                    <div data-i18n="Without navbar"><?= $lang[41] ?>
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="misc.php?t=2" class="menu-link">
                                    <div data-i18n="Without navbar">گالری
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="misc.php?t=4" class="menu-link">
                                    <div data-i18n="Without navbar"><?= $lang[1202] ?>
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="misc.php?t=3" class="menu-link">
                                    <div data-i18n="Without navbar"><?= $lang[1208] ?>
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="misc.php?t=5" class="menu-link">
                                    <div data-i18n="Without navbar">سوالات متداول
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php }
                    if (access_level("1,3", $access) == TRUE) { ?>

                    <li class="menu-item <?php echo $Tabpage == '6' ? 'open' : ''; ?>">
                        <a href="javascript:void(0)" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-box"></i>
                            <div data-i18n="User interface">تنظیمات عمومی</div>
                        </a>
                        <ul class="menu-sub">

                            <?php
                                if (access_level("1", $access) == TRUE) { ?>

                            <li class="menu-item" style="display: none;">
                                <a href="registeruserlist.php" class="menu-link">
                                    <div data-i18n="Accordion">ثبت نام سازمانی</div>
                                </a>
                            </li>


                            <li class="menu-item">
                                <a href="pages.php" class="menu-link">
                                    <div data-i18n="Accordion">صفحات متنی</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="certificate.php" class="menu-link">
                                    <div data-i18n="Accordion">مدیریت گواهی نامه ها</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="menu_manager.php" class="menu-link">
                                    <div data-i18n="Accordion">منو بالای سایت</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="menu_footer.php" class="menu-link">
                                    <div data-i18n="Accordion">منو پایین سایت</div>
                                </a>
                            </li>

                            <?php } ?>

                            <?php if (access_level("1,3", $access) == TRUE) { ?>
                            <li class="menu-item">
                                <a href="ads.php" class="menu-link">
                                    <div data-i18n="Accordion">مدیریت اسلایدها</div>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (access_level("1", $access) == TRUE) { ?>
                            <li class="menu-item">
                                <a href="code.php" class="menu-link">
                                    <div data-i18n="Accordion">کد تخفیف</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="user.php" class="menu-link">
                                    <div data-i18n="Accordion">مدیریت پنل</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="setting.php" class="menu-link">
                                    <div data-i18n="Accordion">تنظیمات عمومی</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="lng_editorfa.php" class="menu-link">
                                    <div data-i18n="Accordion">تغییرات زبان فارسی</div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="message.php" class="menu-link">
                                    <div data-i18n="Accordion">پشتیبانی</div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="comment.php" class="menu-link">
                                    <div data-i18n="Accordion">نظرات کاربران</div>
                                </a>
                            </li>
                            <?php } ?>



                        </ul>
                    </li>
                    <?php } ?>

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
                            <?php if (access_level("1,6", $access) == TRUE) { ?>
                            <li>
                                <a class="dropdown-item" href="user.php?id=<?= $this_user_id ?>&do=edit">
                                    <i class="bx bx-user me-2"></i>
                                    <span class="align-middle">ویرایش مشخصات</span>
                                </a>
                            </li>
                            <?php } ?>
                            <?php if (access_level("1", $access) == TRUE) { ?>
                            <li>
                                <a class="dropdown-item" href="setting.php">
                                    <i class="bx bx-cog me-2"></i>
                                    <span class="align-middle">تنظیمات عمومی</span>
                                </a>
                            </li>
                            <?php } ?>

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