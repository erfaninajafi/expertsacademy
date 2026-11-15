<?php
@session_start();
include_once './include/config.php';
include_once './include/function.php';
include_once './include/jdf.php';
$result = Query("Select * from tbl_setting");
$read = mysqli_fetch_array($result);
$site_url = $read['tbl_setting_url'];
$mainText = $read['tbl_setting_text_index'];
if (isset($_GET['action'])) {
    $action = $_GET['action'];
}
if (isset($_GET['page'])) {
    $page = toEnglishNum(sqi($_GET['page']));
    if (!is_numeric($page)) {
        $page = 1;
    }
} else {
    $page = 1;
}
if (isset($_GET['t'])) {
    $t = sqi($_GET['t']);
}
if (isset($_GET['id'])) {
    $id = sqi($_GET['id']);
}
if (isset($_GET['do'])) {
    $do = strtolower(sqi($_GET['do']));
}
if (isset($_GET['set'])) {
    $set = sqi($_GET['set']);
}
if (isset($_GET['status'])) {
    $status = sqi($_GET['status']);
}
if (isset($_GET['code'])) {
    $code = sqi($_GET['code']);
}

if (isset($_GET['keyword'])) {
    $keyword = sqi($_GET['keyword']);
} else {
    $keyword = '';
}

if (isset($_GET['lng'])) {
    $lng = strtolower(sqi($_GET['lng']));
} else {
    $lng = 'fa';
}

if ($lng != 'fa' && $lng != 'en' && $lng != 'ar') {
    $lng = 'fa';
}

$show_language_bar = '';

if ($lng == 'fa') {
    $show_language_bar = 'en';
} else {
    $show_language_bar = 'fa';
}

include_once './include/lang/site_' . $lng . '.php';


if (isset($checklogin) && $checklogin == 1) {
    if (!isset($_SESSION['memberid'])) {
        $_SESSION['error'] = $lang[5];
        header("Location: login.php?lng=$lng");
    }
}

