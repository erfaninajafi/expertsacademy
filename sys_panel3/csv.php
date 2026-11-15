<?php
include './includer.php';
if(isset($_GET['do']))
{
    $do = $_GET['do'];
}
$section = $_GET['section'];
$do = $_GET['do'];
$lng = $_GET['lng'];
include '../include/lang/'.$lng.'.php';
$all = '';
$allfinal = '';
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$do.'-'.$section.'-'.$lng.'-'.time().'.csv'.'"'); 
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: binary');
echo "\xEF\xBB\xBF";

if($do == 'review') {
$result = Query("Select `tbl_work_code`, "
        . "`tbl_work_status`, "
        . "`tbl_work_userid`, "
        . "`tbl_work_review` from tbl_work where tbl_work_review !='0' and tbl_work_incomplete !='1' and tbl_work_coursecode ='$section' ");

while ($read = mysqli_fetch_array($result))
{
   $user_id = $read['tbl_work_userid'];
   $result_member = Query("SELECT * From tbl_member Where tbl_member_id='$user_id' " );
   $read_member = mysqli_fetch_assoc($result_member);
                    
   $all .=$read['tbl_work_code'].'/[]/'.check_work($read['tbl_work_status']).'/[]/'.($lng == 'fa' ? $read_member['tbl_member_fname'].' '.$read_member['tbl_member_lname'] : $read_member['tbl_member_fnameen'].' '.$read_member['tbl_member_lnameen']).'/[]/'.$read['tbl_work_review'].'/[]/';
   $all .='/++/';
}
echocsv(array("$lang[466]","$lang[479]","$lang[92]","$lang[1130]",""));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}
else {
    $result = Query("Select `tbl_work_code`, "
        . "`tbl_work_status`, "
        . "`tbl_work_userid`, "
        . "`tbl_work_referee` from tbl_work where tbl_work_referee !='0' and tbl_work_incomplete !='1' and tbl_work_coursecode ='$section' ");

while ($read = mysqli_fetch_array($result))
{
   $user_id = $read['tbl_work_userid'];
   $result_member = Query("SELECT * From tbl_member Where tbl_member_id='$user_id' " );
   $read_member = mysqli_fetch_assoc($result_member);
                    
   $all .=$read['tbl_work_code'].'/[]/'.check_work($read['tbl_work_status']).'/[]/'.($lng == 'fa' ? $read_member['tbl_member_fname'].' '.$read_member['tbl_member_lname'] : $read_member['tbl_member_fnameen'].' '.$read_member['tbl_member_lnameen']).'/[]/'.$read['tbl_work_referee'].'/[]/';
   $all .='/++/';
}
echocsv(array("$lang[466]","$lang[479]","$lang[92]","$lang[1129]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}