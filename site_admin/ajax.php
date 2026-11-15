<?php

session_start();
if (empty($_SESSION['siteadmin_login'])) {
    http_response_code(403);
    exit;
}

include_once '../include_site/config.php';
include_once '../include_site/function.php';
include_once '../include_site/lang/' . $language_default . '.php';
if (!empty($_GET['do'])) {
    $do = $_GET['do'];
}
// ajax content

if (isset($do) && $do == 'select2ajax') {

    if ($_GET['section'] == 'tag') {
        $type = '14';
    }

    if (strlen($_GET['searchTerm']) >= 2) {
        if (!isset($_GET['searchTerm'])) {
            $json = [];
        } else {
            $search = $_GET['searchTerm'];
            $result = Query("SELECT tbl_tag_title From tbl_tag Where  tbl_tag_title LIKE '%$search%' ORDER BY tbl_tag_title DESC ");
            $json = [];
            while ($read = mysqli_fetch_assoc($result)) {
                $json[] = ['id' => $read['tbl_tag_title'], 'text' => $read['tbl_tag_title']];
            }
        }
        echo json_encode($json);
    }
}
if (isset($do) && $do == 'itemUpdate') {
    $item = explode("-", $_POST['item']);
    $result = Query("select tbl_product_hot,tbl_product_limit,tbl_product_hide from tbl_product where tbl_product_id='$item[0]' ");
    $read = mysqli_fetch_array($result);
    if ($item[1] == 'hot') {
        if ($read['tbl_product_hot'] == 0) {
            Query("update tbl_product set tbl_product_hot='1' where tbl_product_id='$item[0]' ");
        } else {
            Query("update tbl_product set tbl_product_hot='0' where tbl_product_id='$item[0]' ");
        }
    } else if ($item[1] == 'limit') {
        if ($read['tbl_product_limit'] == 0) {
            Query("update tbl_product set tbl_product_limit='1' where tbl_product_id='$item[0]' ");
        } else {
            Query("update tbl_product set tbl_product_limit='0' where tbl_product_id='$item[0]' ");
        }
    } else if ($item[1] == 'hide') {
        if ($read['tbl_product_hide'] == 0) {
            Query("update tbl_product set tbl_product_hide='1' where tbl_product_id='$item[0]' ");
        } else {
            Query("update tbl_product set tbl_product_hide='0' where tbl_product_id='$item[0]' ");
        }
    }
    echo 'Updated';
}
if (isset($do) && $do == 'checkname') {
    if (isset($_POST["title"])) {
        $title = $_POST['title'];
        $code = $_POST['code'];
        $lng = $_POST['lng'];
        $section = explode("-", $_POST['section']);
        $row = 1;
        if ($title != '') {
            $url_product = str_replace(" ", "-", $title);
            $url_product = str_replace("(", "", $url_product);
            $url_product = str_replace(")", "", $url_product);
            $url_product = str_replace("/", "", $url_product);
            $url_product = str_replace(".", "", $url_product);
            $url_product = str_replace("+", "", $url_product);
            $url_product = str_replace("@", "", $url_product);
            $url_product = str_replace("?", "", $url_product);
            $url_product = str_replace("&", "", $url_product);
            $url_product = strtolower($url_product);
            if (!empty($code)) {
                $results = Query("select * from tbl_content where tbl_content_code != '$code' and tbl_content_type='$section[1]' and tbl_content_url ='$url_product' and tbl_content_fld1 = '$lng' ");
            } else {
                $results = Query("select * from tbl_content where tbl_content_type='$section[1]' and tbl_content_url ='$url_product' and tbl_content_fld1 = '$lng' ");
            }

            if (mysqli_num_rows($results) == 0) {
                echo '<span style="background:green;color:#fff;">This name is ok</span><style>.checkname-' . $section[2] . ' {background:green;color:#fff;border:1px solid green;}</style>';
            } else {
                echo '<span style="background:red;color:#fff;">Warning duplicate name</span><style>.checkname-' . $section[2] . ' {background:red;color:#fff;border:1px solid red;}</style>';
            }
        }
    }
}


if (isset($do) && $do == 'checknamecat') {
    if (isset($_POST["title"])) {
        $title = $_POST['title'];
        $code = $_POST['code'];
        $lng = $_POST['lng'];
        $section = explode("-", $_POST['section']);
        $row = 1;

        if ($title != '') {

            $url_product = str_replace(" ", "-", $title);
            $url_product = str_replace("(", "", $url_product);
            $url_product = str_replace(")", "", $url_product);
            $url_product = str_replace("/", "", $url_product);
            $url_product = str_replace(".", "", $url_product);
            $url_product = str_replace("+", "", $url_product);
            $url_product = str_replace("@", "", $url_product);
            $url_product = str_replace("?", "", $url_product);
            $url_product = str_replace("&", "", $url_product);
            $url_product = strtolower($url_product);
            if (!empty($code)) {
                $results = Query("select * from tbl_categories where tbl_categories_code != '$code' and  tbl_categories_url ='$url_product' and tbl_categories_lng = '$lng' ");
            } else {
                $results = Query("select * from tbl_categories where tbl_categories_url ='$url_product' and tbl_categories_lng = '$lng' ");
            }

            if (mysqli_num_rows($results) == 0) {
                echo '<span style="background:green;color:#fff;">This name is ok</span><style>.checkname-' . $lng . ' {background:green;color:#fff;border:1px solid green;}</style>';
            } else {
                echo '<span style="background:red;color:#fff;">Warning duplicate name</span><style>.checkname-' . $lng . ' {background:red;color:#fff;border:1px solid red;}</style>';
            }
        }
    }
}



if (!empty($_POST['type']) && $_POST['type'] == 'cat_title') {
    $keyword = $_POST['keyword'];
    $result = Query("SELECT * FROM tbl_member where tbl_member_status ='1' and tbl_member_fld2 LIKE '%$keyword%' || tbl_member_code LIKE '%$keyword%' LIMIT 0,10");
    $data = array();

    while ($row = mysqli_fetch_array($result)) {

        $title = $row['tbl_member_code'] . ' , ' . $row['tbl_member_fld1'] . ' ' . $row['tbl_member_fld2'];
        $b_title = '' . $keyword . '';
        $final_title = str_ireplace($keyword, $b_title, $title);

        array_push($data, $final_title);
    }
    echo json_encode($data);
}

if (!empty($_POST['type']) && $_POST['type'] == 'user_name') {
    $keyword = $_POST['keyword'];
    $result = Query("SELECT * FROM tbl_member where tbl_member_name LIKE '%$keyword%' || tbl_member_mobile LIKE '%$keyword%' LIMIT 0,10");
    $data = array();

    while ($row = mysqli_fetch_array($result)) {

        $title = $row['tbl_member_name'];
        $b_title = '' . $keyword . '';
        $final_title = str_ireplace($keyword, $b_title, $title);

        array_push($data, $final_title);
    }
    echo json_encode($data);
}
if (!empty($_POST['type']) && $_POST['type'] == 'agahi_title') {
    $keyword = $_POST['keyword'];
    $result = Query("SELECT * FROM tbl_agahi where tbl_agahi_title LIKE '%$keyword%' LIMIT 0,10");
    $data = array();

    while ($row = mysqli_fetch_array($result)) {

        $title = $row['tbl_agahi_title'];
        $b_title = '' . $keyword . '';
        $final_title = str_ireplace($keyword, $b_title, $title);

        array_push($data, $final_title);
    }
    echo json_encode($data);
}

if (isset($do) && $do == 'state') {
    $code = $_POST['code'];
    $code = explode("-", $code);
    $result_sub = Query("select * from tbl_categories where tbl_categories_type = '2' and tbl_categories_parentid='$code[0]' and tbl_categories_lng='fa' order by tbl_categories_order DESC");
    $row = mysqli_num_rows($result_sub);
    if ($row > 0) {
        echo '<option value="">یک مورد را انتخاب کنید</option>';
        while ($read_sub =  mysqli_fetch_array($result_sub)) {
            echo '<option value="' . $read_sub['tbl_categories_code'] . '-' . $read_sub['tbl_categories_title'] . '">' . $read_sub['tbl_categories_title'] . '</option>';
        }
        echo '';
    }
}

if (isset($do) && $do == 'city') {
    $code = $_POST['code'];
    $code = explode("-", $code);
    $result_sub = Query("select * from tbl_categories where tbl_categories_type = '2' and tbl_categories_parentid='$code[0]' order by tbl_categories_order DESC");
    $row = mysqli_num_rows($result_sub);
    if ($row > 0) {
        echo '<option value="">یک مورد را انتخاب کنید</option>';
        while ($read_sub =  mysqli_fetch_array($result_sub)) {
            echo '<option value="' . $read_sub['tbl_categories_code'] . '-' . $read_sub['tbl_categories_title'] . '">' . $read_sub['tbl_categories_title'] . '</option>';
        }
        echo '';
    }
}
