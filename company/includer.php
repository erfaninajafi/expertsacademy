<?php
ob_start();
include_once '../include/config.php';
include_once '../include/function.php';
include '../include/lang/panel_fa.php';
$language_count = count($language);

if (isset($_GET['t'])) {
    $t = $_GET['t'];
}
if (isset($_GET['set'])) {
    $set_number = $_GET['set'];
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
