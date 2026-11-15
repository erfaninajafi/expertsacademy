<?php
session_start();
include './includer.php';
if (empty($_SESSION['id_login'])) {
    header("Location: login.php");
    exit();
}
if(isset($_GET['historyclass']) && $do == 'register') {
$id = $_GET['historyclass'];
$all = '';
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Register-' . $id . '-' . time() . '.csv' . '"');
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: binary');
echo "\xEF\xBB\xBF";
    $i = 0;
    $result = Query("Select * from tbl_register where tbl_register_view='0' and tbl_register_class ='$id' order by tbl_register_id DESC ");
    while ($read = mysqli_fetch_array($result)) {
        $class= $read['tbl_register_class'];
        $result_class = Query("select * from tbl_class where tbl_class_code='$class'");
        $read_class= mysqli_fetch_array($result_class);
        $member = $read['tbl_register_user'];
        $result_member = Query("select * from tbl_member where tbl_member_id='$member' ");
        $read_member = mysqli_fetch_array($result_member);
        $madrak = str_replace(",","-",$read_member['tbl_member_madrak']);
        $address = (!empty($read_member['tbl_member_address']) ? str_replace(",","-",str_replace("،","-",$read_member['tbl_member_address'])) : '-');
        $all .= ($i+1) . '/[]/';
        $all .= $read_member['tbl_member_fname'].' '.$read_member['tbl_member_lname']. '/[]/';
        $all .= $read_member['tbl_member_mobile'] . '/[]/';
        $all .= (!empty($read_member['tbl_member_phone']) ? $read_member['tbl_member_phone'] : '-') . '/[]/';
        $all .= str_replace(",","-",$address) . '/[]/';
        $all .= (!empty($read_member['tbl_member_postcode']) ? $read_member['tbl_member_postcode'] : '-') . '/[]/';
        $all .= (isset($madrak) ? $lang["$madrak"] : '-') . '/[]/';
        $all .= (!empty($read_member['tbl_member_reshteh']) ? str_replace(",","-",$read_member['tbl_member_reshteh']) : '-'). '/[]/';
        $all .= $read_member['tbl_member_email'] . '/[]/';
        $all .= $read_member['tbl_member_nationalcode'] . '/[]/';
        $all .= ($read['tbl_register_pish'] == 1 ? 'پیش ثبت نام' : 'ثبت نام') . '/[]/';
        $all .= str_replace(",","-",$read['tbl_register_date']) . '/[]/';
        $all .= str_replace(",","-",$read_class['tbl_class_title']) . '/[]/';

        $all .= '/++/';
        $i++;
    }
    echocsv(array("ردیف", "نام و نام خانوادگی", "شماره موبایل", "تلفن", "آدرس", "کدپستی", "آخرین مدرک تحصیلی", "رشته تحصیلی", "پست الکترونیک", "کد ملی", "نوع پرداخت", "تاریخ ثبت نام ", "نام دوره"));
    $allfinal = explode("/++/", $all);
    foreach ($allfinal as $col => $val) {
        $thisval = explode("/[]/", $val);
        echocsv($thisval);
    }
}
    
elseif(isset($do) && $do=='member'){
$all = '';
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="Member-' . time() . '.csv' . '"');
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: binary');
echo "\xEF\xBB\xBF";
    $i = 0;
    $result = Query("Select * from tbl_member where tbl_member_delete='0' order by tbl_member_id DESC ");
    while ($read = mysqli_fetch_array($result)) {
        $madrak = $read['tbl_member_madrak'];
        $all .= ($i+1) . '/[]/';
        $all .= $read['tbl_member_fname'].'/[]/';
        $all .= $read['tbl_member_lname']. '/[]/';
        $all .= $read['tbl_member_nationalcode'] . '/[]/';
        $all .= ($read['tbl_member_sex'] == 1 ? 'مرد' : 'زن') . '/[]/';
        $all .= $read['tbl_member_birthday'] . '/[]/';
        $all .= $read['tbl_member_mobile'] . '/[]/';
        $all .= $read['tbl_member_postcode'] . '/[]/';
        $all .= $read['tbl_member_state'] . '/[]/';
        $all .= $read['tbl_member_city'] . '/[]/';
        $all .= @str_replace(",","-",$read['tbl_member_address']) . '/[]/';
        $all .= $read['tbl_member_email'] . '/[]/';
        $all .= $read['tbl_member_phone'] . '/[]/';
        $all .= (!empty($read['tbl_member_reshteh']) ? str_replace(",","-",$read['tbl_member_reshteh']) : '-') . '/[]/';
        $all .= @$lang[$madrak] . '/[]/';
        $all .= '/++/';
        $i++;
    }
    echocsv(array("ردیف", "نام","نام خانوادگی", "کدملی", "جنسیت", "تولد", "موبایل", "کدپستی", "استان", "شهر", "آدرس", "ایمیل", "شماره تلفن ", "رشته تحصیلی","آخرین مدرک تحصیلی"));
    $allfinal = explode("/++/", $all);
    foreach ($allfinal as $col => $val) {
        $thisval = explode("/[]/", $val);
        echocsv($thisval);
    }
}
?>