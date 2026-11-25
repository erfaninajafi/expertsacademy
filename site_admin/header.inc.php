<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
include './includer.php';
include '../include_site/jdf.php';
$currentPage = basename($_SERVER['REQUEST_URI']);
$tabstyle = 'class="active"';

if (!isset($_SESSION['siteadmin_login'])) {
    header("Location: login.php");
    exit;
}
if (isset($_GET['do'])) {
    $do = $_GET['do'];
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}
if (isset($_GET['img'])) {
    $img = $_GET['img'];
}
if (isset($_GET['code'])) {
    $code = $_GET['code'];
}
if (isset($_GET['file'])) {
    $file = $_GET['file'];
}
if (isset($_GET['catid'])) {
    $catid = $_GET['catid'];
}
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
if (isset($_GET['keyword'])) {
    $keyword = $_GET['keyword'];
} else {
    $keyword = '';
}

if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error = '';
}
$result =  Query("Select * From tbl_setting");
$read = mysqli_fetch_array($result);
$adminMenuFile = $index;
$adminMenuType = isset($_GET['t']) ? (string) $_GET['t'] : '';
$adminMenuGroups = [
    [
        'label' => 'محتوا و صفحات',
        'icon' => 'bx-edit',
        'items' => [
            ['label' => 'طراحی صفحات', 'href' => 'category_manage.php?t=4', 'page' => 'category_manage.php', 'type' => '4', 'related' => ['misctwo.php', 'section.php', 'page_builder.php']],
            ['label' => 'Page Studio', 'href' => 'page_studio.php', 'page' => 'page_studio.php'],
            ['label' => 'راهنمای Page Studio', 'href' => 'page_studio_help.php', 'page' => 'page_studio_help.php'],
            ['label' => 'صفحات متنی', 'href' => 'misc.php?t=1', 'page' => 'misc.php', 'type' => '1'],
            ['label' => 'راهنمای قسمت‌ها', 'href' => 'help.php', 'page' => 'help.php'],
        ],
    ],
    [
        'label' => 'منوهای سایت',
        'icon' => 'bx-menu',
        'items' => [
            ['label' => 'منوی بالای سایت', 'href' => 'menu_manager.php', 'page' => 'menu_manager.php'],
            ['label' => 'منوی پایین سایت', 'href' => 'menu_footer.php', 'page' => 'menu_footer.php'],
        ],
    ],
    [
        'label' => 'فرم‌ها و درخواست‌ها',
        'icon' => 'bx-envelope',
        'items' => [
            ['label' => 'پیام‌های تماس عمومی', 'href' => 'form_contact.php', 'page' => 'form_contact.php'],
            ['label' => 'فرم همکاری سوالات', 'href' => 'form1.php', 'page' => 'form1.php'],
            ['label' => 'فرم پیوستن به ما', 'href' => 'form2.php', 'page' => 'form2.php'],
            ['label' => 'تماس سریع پیوستن به ما', 'href' => 'form3.php', 'page' => 'form3.php'],
            ['label' => 'درخواست مشاوره', 'href' => 'form4.php', 'page' => 'form4.php'],
        ],
    ],
    [
        'label' => 'تنظیمات',
        'icon' => 'bx-cog',
        'items' => [
            ['label' => 'کاربران پنل', 'href' => 'user.php', 'page' => 'user.php'],
            ['label' => 'زبان فارسی', 'href' => 'lng_sitefa.php', 'page' => 'lng_sitefa.php'],
            ['label' => 'زبان انگلیسی', 'href' => 'lng_siteen.php', 'page' => 'lng_siteen.php'],
            ['label' => 'تنظیمات عمومی', 'href' => 'setting.php', 'page' => 'setting.php'],
        ],
    ],
];
function exacdAdminMenuItemActive($item, $currentFile, $currentType)
{
    if (!empty($item['related']) && in_array($currentFile, $item['related'], true)) {
        return true;
    }
    if ($currentFile !== $item['page']) {
        return false;
    }
    return !isset($item['type']) || $currentType === $item['type'];
}
?>
<!DOCTYPE html>
<html lang="fa" class="light-style layout-menu-fixed" dir="rtl" data-theme="theme-default" data-assets-path="asset/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title><?= $lang[11] . ' - ' . @$pagetitle; ?></title>
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
    <link rel="stylesheet" href="asset/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="asset/css/select2.min.css" />
    <link rel="stylesheet" href="asset/css/persianDatepicker-default.css">
    <link rel="stylesheet" href="asset/css/chartist.min.css">

    <script src="asset/vendor/libs/jquery/jquery.js"></script>
    <script src="asset/js/chartist.min.js"></script>
    <script src="asset/vendor/js/helpers.js"></script>
    <script src="asset/js/config.js"></script>
    <?php
    if (isset($do) && $do == 'add' || @$do == 'edit') {
        echo '<script src="asset/plugins/jQueryUI/jquery-ui.min.js"></script>';
    }
    ?>
    <link rel="stylesheet" href="asset/css/custom.css?var=<?= rand(10, 10000) ?>" />
    <link rel="stylesheet" href="asset/css/listing-ui.css" />
    <link rel="stylesheet" href="asset/css/cms-editor-ui.css" />
    <?php if ($index === 'page_builder.php') : ?>
        <link rel="stylesheet" href="asset/css/page-builder.css" />
    <?php endif; ?>
    <link rel="stylesheet" href="asset/css/admin-dashboard-nav.css" />
</head>

<body>
    <?php

    if (isset($check_level)) {
        $check_level_a = explode(",", $check_level);
        $count_level = count($check_level_a);

        if ($user_level == 0) {
        } else {
            if ($check_level_a[1] != $user_level) {
                echo '<div style="text-align:center;font-size:14px;color:red;">شما دسترسی به این صفحه ندارید</div>';
                exit();
            }
        }
    }
    ?>
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="index.php" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <img src="asset/logo.png">
                        </span>
                    </a>
                    <button type="button" class="layout-menu-toggle admin-sidebar-close d-xl-none"
                        aria-label="بستن منوی مدیریت" title="بستن منوی مدیریت">
                        <i class="bx bx-x" aria-hidden="true"></i>

                    </button>
                </div>
                <div class="menu-inner-shadow"></div>
                <ul class="menu-inner py-1 admin-menu">
                    <li class="menu-item <?= $adminMenuFile === 'index.php' ? 'active' : '' ?>">
                        <a href="index.php" class="menu-link" <?= $adminMenuFile === 'index.php' ? 'aria-current="page"' : '' ?>>
                            <i class="menu-icon tf-icons bx bx-grid-alt" aria-hidden="true"></i>
                            <div>داشبورد</div>
                        </a>
                    </li>
                    <?php foreach ($adminMenuGroups as $group) : ?>
                        <?php
                        $groupIsActive = false;
                        foreach ($group['items'] as $item) {
                            if (exacdAdminMenuItemActive($item, $adminMenuFile, $adminMenuType)) {
                                $groupIsActive = true;
                                break;
                            }
                        }
                        ?>
                        <li class="menu-item <?= $groupIsActive ? 'active open' : '' ?>">
                            <a href="javascript:void(0);" class="menu-link menu-toggle" aria-label="<?= $group['label'] ?>">
                                <i class="menu-icon tf-icons bx <?= $group['icon'] ?>" aria-hidden="true"></i>
                                <div><?= $group['label'] ?></div>
                            </a>
                            <ul class="menu-sub">
                                <?php foreach ($group['items'] as $item) : ?>
                                    <?php $itemIsActive = exacdAdminMenuItemActive($item, $adminMenuFile, $adminMenuType); ?>
                                    <li class="menu-item <?= $itemIsActive ? 'active' : '' ?>">
                                        <a href="<?= htmlspecialchars($item['href'], ENT_QUOTES, 'UTF-8') ?>" class="menu-link" <?= $itemIsActive ? 'aria-current="page"' : '' ?>>
                                            <div><?= $item['label'] ?></div>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                    <li class="menu-item <?php echo ($Tabpage == '25') ? 'open' : ''; ?>" style="display:none;">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons bx bx-plus"></i>
                            <div data-i18n="Layouts">گنجینه مهارت ها</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item">
                                <a href="category_manage.php?t=3" class="menu-link">
                                    <div data-i18n="Without navbar"> دسته بندی دوره
                                    </div>
                                </a>
                            </li>

                            <?php
                            $result_count = Query("SELECT * From tbl_class");
                            ?>
                            <li class="menu-item">
                                <a href="class.php" class="menu-link">
                                    <div data-i18n="Without navbar">لیست دوره ها (<?= mysqli_num_rows($result_count) ?>)
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="workshop.php" class="menu-link">
                                    <div data-i18n="Without navbar"> لیست کارگاه ها
                                    </div>
                                </a>
                            </li>
                            <?php
                            $result_count = Query("SELECT * From tbl_teacher");
                            ?>
                            <li class="menu-item">
                                <a href="teacher.php" class="menu-link">
                                    <div data-i18n="Without navbar"> مدیریت اساتید (<?= mysqli_num_rows($result_count) ?>)
                                    </div>
                                </a>
                            </li>

                            <li class="menu-item">
                                <a href="logo.php" class="menu-link">
                                    <div data-i18n="Without navbar"> مدیریت برگزار کننده
                                    </div>
                                </a>
                            </li>
                            <li class="menu-item">
                                <a href="misc.php?t=32" class="menu-link">
                                    <div data-i18n="Without navbar"> مدیریت باکس محتوی
                                    </div>
                                </a>
                            </li>
                        </ul>
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
                        <ul class="navbar-nav flex-row align-items-center admin-navbar-actions">
                            <li class="nav-item">
                                <a class="nav-link" href="user.php?id=<?= rawurlencode((string) $_SESSION['siteadmin_login']) ?>&amp;do=edit">
                                    <i class="bx bx-user" aria-hidden="true"></i><span>مشخصات من</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="../" target="_blank" rel="noopener">
                                    <i class="bx bx-link-external" aria-hidden="true"></i><span>نمایش سایت</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="login.php?do=logout">
                                    <i class="bx bx-power-off" aria-hidden="true"></i><span>خروج</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="content-wrapper">

                    <div class="container-fluid flex-grow-1 container-p-y">
