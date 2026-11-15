<?php

@session_start();
include_once './include_site/config.php';
include_once './include_site/function.php';
include_once './include_site/jdf.php';

$result = Query("Select * from tbl_setting");
$read = mysqli_fetch_array($result);
$site_url = $read['tbl_setting_url'];
$siteTitle = $read['tbl_setting_title_fa'];

$insta_setting = $read['tbl_setting_insta'];
$whatsapp_setting = $read['tbl_setting_whatsapp'];
$linkedin_setting = $read['tbl_setting_linkedin'];
$twitter_setting = $read['tbl_setting_twitter'];
$facebook_setting = $read['tbl_setting_facebook'];
$telegram_setting = $read['tbl_setting_telegram'];
$aparat_setting = $read['tbl_setting_aparat'];
$youtube_setting = $read['tbl_setting_youtube'];

$index = basename($_SERVER['PHP_SELF']);
if (isset($_SESSION['membercode'])) {
    global  $id_user;
    $id_user = $_SESSION['membercode'];
}
if (isset($_GET['page'])) {
    $page = sqi($_GET['page']);
} else {
    $page = 1;
}
if (isset($_GET['id'])) {
    $id = sqi($_GET['id']);
}
if (isset($_GET['do'])) {
    $do = sqi($_GET['do']);
}
if (isset($_GET['type'])) {
    $type_url = $_GET['type'];
}
if (isset($_GET['code'])) {
    $code = sqi($_GET['code']);
}
if (isset($_GET['categories'])) {
    $categories = sqi($_GET['categories']);
}
if (isset($_GET['cat'])) {
    $cat_url = sqi($_GET['cat']);
}
if (isset($_GET['subcat'])) {
    $subcat_url = sqi($_GET['subcat']);
}
if (isset($_GET['state'])) {
    $state_url = sqi($_GET['state']);
}
if (isset($_GET['city'])) {
    $city_url = sqi($_GET['city']);
}
if (isset($_GET['area'])) {
    $area_url = sqi($_GET['area']);
}
if (isset($_GET['keyword'])) {
    $keyword = sqi($_GET['keyword']);
}



$languageInput = $_GET['lng'] ?? 'fa';
$lng = is_string($languageInput) ? sqi($languageInput) : 'fa';
if (!in_array($lng, ['fa', 'en'], true)) {
    $lng = 'fa';
}

include_once "./include_site/lang/" . $lng . "_lng.php";

$show_language_bar = '';
