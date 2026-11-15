<?php
ob_start();
include '../include_site/config.php';
include_once '../include_site/function.php';
include '../include_site/lang/fa.php';


$language_count = count($language);
$index = basename($_SERVER['PHP_SELF']);
// type check
if (isset($_GET['t'])) {
    $t = $_GET['t'];
}
if (isset($_GET['set'])) {
    $set_number = $_GET['set'];
}
if (isset($_GET['page'])) {
    $page = sqi($_GET['page']);
} else {
    $page = 0;
}
if (isset($_GET['code'])) {
    $code = sqi($_GET['code']);
}
if (isset($_GET['section'])) {
    $section = sqi($_GET['section']);
}
// check level
$now_login = @$_SESSION['id_login'];
$result_level = Query("Select * from tbl_user where tbl_user_id = '$now_login' ");
$read_level = mysqli_fetch_array($result_level);
@$user_level = $read_level['tbl_user_status'];