<?php
ob_start();
include_once '../include/config.php';
include_once '../include/function.php';
include '../include/lang/panel_fa.php';
$language_count = count($language);

if (isset($_GET['t'])) {
    $t = sqi($_GET['t']);
}
if (isset($_GET['set'])) {
    $set_number = $_GET['set'];
}
if (isset($_GET['do'])) {
    $do = sqi($_GET['do']);
}
if (isset($_GET['id'])) {
    $id = sqi($_GET['id']);
}
if (isset($_GET['img'])) {
    $img = sqi($_GET['img']);
}
if (isset($_GET['code'])) {
    $code = sqi($_GET['code']);
}
if (isset($_GET['file'])) {
    $file = sqi($_GET['file']);
}
if (isset($_GET['catid'])) {
    $catid = sqi($_GET['catid']);
}
if (isset($_GET['page'])) {
    $page = sqi($_GET['page']);
} else {
    $page = 1;
}
if (isset($_GET['keyword'])) {
    $keyword = sqi($_GET['keyword']);
} else {
    $keyword = '';
}
if (isset($_GET['skill'])) {
    $getskill = sqi($_GET['skill']);
} else {
    $getskill = '';
}
if (isset($_GET['class'])) {
    $getclass = sqi($_GET['class']);
} else {
    $getclass = '';
}
if (isset($_GET['contract'])) {
    $getcontract = sqi($_GET['contract']);
} else {
    $getcontract = '';
}

if (isset($_GET['mentor'])) {
    $getmentor = sqi($_GET['mentor']);
} else {
    $getmentor = '';
}
if (isset($_GET['model'])) {
    $getmodel = sqi($_GET['model']);
} else {
    $getmodel = '';
}

if (isset($_GET['orderby'])) {
    $getorderby = sqi($_GET['orderby']);
} else {
    $getorderby = '';
}

if (isset($_GET['checklist'])) {
    $getchecklist = sqi($_GET['checklist']);
} else {
    $getchecklist = '';
}
if (isset($_GET['plan'])) {
    $getplan = sqi($_GET['plan']);
} else {
    $getplan = '';
}



if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
    unset($_SESSION['error']);
} else {
    $error = '';
}
