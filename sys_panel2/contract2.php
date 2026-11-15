<?php
phpinfo();
// include
include './includer.php';
use Shuchkin\SimpleXLSX;
include '../include/SimpleXLSX.php';
$show_per_page = '50';
$Tabpage = '90';
$pagetitle = $lang[1307];
if (empty($do)) { 
    $pagetitle =$pagetitle;
    }
    if (!empty($do) && $do == 'karfarma') { 
        $pagetitle =$lang[1438];
     }
    if (!empty($do) && $do == 'import') { 
        $pagetitle=$lang[1439];
     }
    if (!empty($do) && $do == 'importupload') { 
    $pagetitle = $lang[1439];
     }
    if (!empty($do) && $do == 'importother') { 
        $pagetitle=$lang[1439];
    } 
    if (!empty($do) && $do == 'mentor') { 
        $pagetitle=$lang[1923];
    } 
    if (!empty($do) && $do == 'attach') { 
        $pagetitle=$lang[1446];
    } 
    if (!empty($do) && $do == 'mentoradd') { 
        $pagetitle=$lang[1965];
    } 
$thisaccess = '1,5,9';



$dbserverSql='178.252.166.106,1440';
//$dbserverSql='192.168.0.10,17110';
$dbnameSql='IGX';
$dbusernameSql='sa';
$dbpasswordSql='123@@sa';

$connectionInfo = array(
    "Database" => $dbnameSql,
    "UID" => $dbusernameSql,
    "PWD" => $dbpasswordSql,
    "TrustServerCertificate" => true,
    "CharacterSet" => "UTF-8"
);

$conn = sqlsrv_connect($dbserverSql, $connectionInfo);
if (!$conn) {
    //die(print_r(sqlsrv_errors(), true));
}
$query = "SELECT * FROM W_4AHSNCaseContractQP";
$resultSql = sqlsrv_query($conn, $query);

while ($readObj = sqlsrv_fetch_object($resultSql)) {
    //print_r($readObj);
    $tbl_contract_code = $readObj->CaseNo;
    $tbl_contract_customer = $readObj->SherkatNameFull;
    $tbl_contract_title = $readObj->SUBJECT;
    $tbl_contract_text = $readObj->SharhKhdmtSharh;
    $tbl_contract_startdate = $readObj->CaseDate;
    $tbl_contract_namesection = $readObj->MarkazName;
    $tbl_contract_project = $readObj->PRGMNGR001;
    $date = jdate("Y/m/d : a:h:s");
$resultUser= Query("select * from tbl_user where tbl_user_username='$tbl_contract_code'");
$readUser = mysqli_fetch_array($resultUser);
$userId = $readUser['tbl_user_code'];
    $resultCheck= Query("select * from tbl_contract where tbl_contract_code='$tbl_contract_code'");
    if(mysqli_num_rows($resultCheck) > 0)
    {
        Query("update tbl_contract set 
        tbl_contract_customer= '$tbl_contract_customer', 
        tbl_contract_title= '$tbl_contract_title', 
        tbl_contract_text= '$tbl_contract_text', 
        tbl_contract_startdate= '$tbl_contract_startdate', 
        tbl_contract_namesection= '$tbl_contract_namesection', 
        tbl_contract_project= '$userId',
        tbl_contract_dateread = '$date'
        where tbl_contract_code='$tbl_contract_code'");
    }
    else {
        Query("insert into tbl_contract (
        tbl_contract_customer, 
        tbl_contract_title, 
        tbl_contract_text, 
        tbl_contract_startdate, 
        tbl_contract_namesection, 
        tbl_contract_project,
        tbl_contract_code,
        tbl_contract_dateread
        ) values(
        '$tbl_contract_customer', 
        '$tbl_contract_title', 
        '$tbl_contract_text', 
        '$tbl_contract_startdate', 
        '$tbl_contract_namesection', 
        '$userId',
        '$tbl_contract_code',
        '$date')");
    }
}

include './header.inc.php';


if (!empty($do) && $do == 'karfarma') {
    $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
    $read_contract = mysqli_fetch_array($result_contract);
    $thisProject = $read_contract['tbl_contract_project'];
    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
    $read_p = mysqli_fetch_array($result_p);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_p['tbl_user_name'] . '</h5>
            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>

            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1582].'  : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    </div>
    </div>';


}
if (!empty($do) && $do == 'import') {
    $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
    $read_contract = mysqli_fetch_array($result_contract);
    $thisProject = $read_contract['tbl_contract_project'];
    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
    $read_p = mysqli_fetch_array($result_p);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_p['tbl_user_name'] . '</h5>
            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>

            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1582].'  : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5>
            <h5 class="fs-0">'.$lang[1700].'  : <a href="faragir.php?contract=' . $_GET['id'] . '">'.$lang[1383].'</a></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    </div>
    </div>';


}




if (!empty($do) && ($do == 'importother' || $do == 'mentor')) {
    $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    $thisProject = $read_contract['tbl_contract_project'];
    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
    $read_p = mysqli_fetch_array($result_p);
                    
    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_p['tbl_user_name'] . '</h5>
            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>

            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1582].'  : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5>
            <h5 class="fs-0">'.$lang[1700].'  : <a href="faragir.php?contract=' . $_GET['id'] . '">'.$lang[1383].'</a></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </div>
    </div>';
}


            // list content
            if ($access != 1) {
                if (!empty($access)) {
                    if($access == '5'){
                    $thisaccess = "Where tbl_contract_mentor like '%$userCode%'";
                    }
                    if($access == '9'){
                        $thisaccess = "Where tbl_contract_project = '$userCode'";
                        }
                } else {
                    $thisaccess = "";
                }
            } else {
                $thisaccess = "";
            }
            if (@$do !='view') {
            ?>

<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(isset($do) && $access == '9' && $do =='mentor' ? '
                            <div class="plus">
                            <a href="contract.php?do=mentoradd&id=' . $_GET['id'] . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1937] . '"><span class="bi-person-lines-fill"></span></a></div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php 
                    }
                    if (!isset($do)) { ?>
                <?php
                // start count db result for pagination 

                $result_for_count = Query("SELECT * From tbl_contract $thisaccess ");
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);
                // end count db result for pagination         
                $result = Query("SELECT * From tbl_contract $thisaccess ORDER BY tbl_contract_id DESC LIMIT $start_contnet,$show_per_page ");
                // error : if not find record show no item for view
                $row = mysqli_num_rows($result);
                if (!isset($do) && $row == '0') {
                    echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                }
                $result = Query("Select * from tbl_contract $thisaccess order by tbl_contract_id DESC LIMIT $start_contnet,$show_per_page");
                echo '<div class="table-responsive scrollbar">
                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                    <thead class="text-800 bg-light">
                                <tr >
                                    <th>'.$lang[60] .'</th>
                                    <th>'.$lang[1440].'</th>
                                    <th>'.$lang[1441].'</th>
                                    <th>'.$lang[1566].'</th>
                                    <th>'.$lang[1442].'</th>
                                    <th>'.$lang[1443].'</th>
                                    <th>'.$lang[1444].'</th>
                                    <th>'.$lang[1445].'</th>
                                    <th>'.$lang[1446].'</th>
                                    '.($access !='9' ? '<th>'.$lang[1340].'</th>' : '' ).'
                                    <th>'.$lang[63] .'</th>
                                </tr>
                            </thead>
                            <tbody>';
                $i = 1;

                while ($read = mysqli_fetch_array($result)) {
                    $thisMentor = $read['tbl_contract_mentor'];
                    $result_m = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisMentor'  ");
                    $read_m = mysqli_fetch_array($result_m);
                    $contractId = $read['tbl_contract_id'];
                    $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                    $contractIdStr = '['.$contractId.'],';

                    if($access == '9' || $access == '1')
                    {
                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractId%' ");
                    }
                    else {

                        $result_member = Query("SELECT 
                            p.tbl_projectmember_mentor, 
                            p.tbl_projectmember_contract,
                            p.tbl_projectmember_mentor ,
                            p.tbl_projectmember_user AS tbl_projectmember, 
                            m.tbl_member_lastcontract, 
                            m.tbl_member_id,
                            m.tbl_member_delete AS tbl_member
                        FROM
                            tbl_member m
                        INNER JOIN tbl_projectmember p 
                        ON p.tbl_projectmember_contract = '$contractId' 
                        and p.tbl_projectmember_mentor='$userCode'  
                        and p.tbl_projectmember_user= m.tbl_member_id 
                        and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
                        and m.tbl_member_delete='0' ");

                    }
                    

                        $resultUser = Query("select * from tbl_user where tbl_user_karfarma='1' and tbl_user_contract='$contractId' ");
                        if(mysqli_num_rows($resultUser) <= 0)
                        {
                            $linkUser = 'user.php?do=add&contract='.$contractId;
                        }
                        else {
                            $readUser = mysqli_fetch_array($resultUser);
                            $linkUser = 'user.php?do=edit&id='.$readUser['tbl_user_id'];
                        }
                        
                  
                        $thisProject = $read['tbl_contract_project'];
                        $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode = '$thisProject'  ");
                        $read_p = mysqli_fetch_array($result_p);

                    echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2"><a href="?do=view&id=' . $read['tbl_contract_id'] . '" >' . $read['tbl_contract_code'] . '<br>' . (!empty($read['tbl_contract_number']) ? $lang[1567].' : ' . $read['tbl_contract_number'] : '') . '</a></td>
                                <td class="align-middle p-2">' . $read['tbl_contract_customer'] . '</td>
                                <td class="align-middle p-2">' .(mysqli_num_rows($result_p) > 0 ? $read_p['tbl_user_name'] : '') . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_contract_startdate']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_contract_enddate']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_contract_editdate']) . '</td>
                                <td class="align-middle p-2">
                                ' . (!empty($read['tbl_contract_enddate']) ? toPersianNum(dateToTimeEnd($read['tbl_contract_enddate'])) : '') . ' 
                                </td>
                                <td class="align-middle p-2">' . (@mysqli_num_rows($result_attach) > 0 ? @toPersianNum(mysqli_num_rows($result_attach)) : '') . '</td>
                                
                                '.($access !='9' ? '<td class="align-middle p-2">
                                <label class="w-100-px">
                                <input class="contractstatus" type="radio" name="n' . $read['tbl_contract_id'] . '" value="0-' . $read['tbl_contract_id'] . '" ' . ($read['tbl_contract_status'] == 0 ? 'checked' : '') . '> '.$lang[1568].' </label>
                                 <label class="w-100-px">
                                 <input  class="contractstatus" type="radio" name="n' . $read['tbl_contract_id'] . '" value="1-' . $read['tbl_contract_id'] . '" ' . ($read['tbl_contract_status'] == 1 ? 'checked' : '') . '> '.$lang[1569].' </label>
                                 </td>' : '' ).'
                                
                                <td class="align-middle p-2">
                                <a href="?do=view&id=' . $read['tbl_contract_id'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '"><span class="bi-pencil-square"></span></a>' . (mysqli_num_rows($result_attach) > 0 ? '
                                <a  href="attach.php?contract=' . $read['tbl_contract_id'] . '" class="btn me-1 mb-1 btn-sm  btn-warning " data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1448] . '"><span class="bi-file-arrow-down-fill"></span></a>' : '') . '
                                '.($access == '9' ? '<a href="'.$linkUser.'" class="btn me-1 mb-1 btn-sm btn-danger "   data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1449] . '"><span class="bi-file-person"></span></a>' : '' ).'
                                <a href="faragir.php?do='.($access=='5' ? 'listall' : 'list').'&contract=' . $read['tbl_contract_id'] . '" class="btn me-1 mb-1 btn-sm btn-dark " data-bs-toggle="tooltip" data-bs-placement="top" title="(' . (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') . ') '.$lang[1451].'"><span class="bi-list-stars"></span></a>
                                '.($access == '9' ? '<a href="checklist.php?contract='.$read['tbl_contract_id'].'" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $lang[1927] .'"><span class="bi-card-checklist"></span></a>' : '' ).'
                                <a style="display:none;" href="?do=stats&contract=' . $read['tbl_contract_id'] . '" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1450] . '"><span class="bi-bar-chart-fill"></span></a>
                                '.($access == '9' ? '<a href="endreport.php?contract='. $read['tbl_contract_id'] .'" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="'. $lang[1947] .'"><span class="bi-calendar-check-fill"></span></a>' : '' ).'
                                </td>
                                </tr>';
                    $i++;
                }
                // end show status   
                echo '</tbody></table>';
                // start pagination                


                if ($page > $page_number) {
                    echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                }
                if ($count_result > $show_per_page) {
                    if ($page == 1) {
                        $back_page_number = 2;
                    } else {
                        $back_page_number = $page;
                    }

                    // $other_page_keyword = '';
                    if (isset($keyword) && $keyword == TRUE) {
                        $other_page_keyword = "&keyword=";
                    } else {
                        $other_page_keyword = '';
                    }
            ?>
                <table class="table">
                    <tr>
                        <td class="col-lg-6">
                            <ul class="pagination">
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '')  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-6">
                            <form action="contract.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                <div class="form-group">
                                    <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                    <input type="text" class="form-control go-fld" name="page_number">
                                </div>
                            </form>
                        </td>
                    </tr>
                </table>
                <?php
                }
            }
            if (isset($do) && $do == 'gotopage') {
                if (isset($_POST['gotopage'])) {
                    $page_number = $_POST['page_number'];
                    header("Location: document.php?page=$page_number");
                }
            }
            // end = pagination


            // start = edit record
            if (isset($do) && $do == 'view') {
                $result = Query("SELECT * FROM tbl_contract Where tbl_contract_id = '$id'");
                $read = mysqli_fetch_array($result);
                $contractIdStr = '['.$id.'],';
                $result_member = Query("select * from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");

                    $thisMentor = $read['tbl_contract_mentor'];
                    
                    $result_m = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisMentor'  ");
                    $read_m = mysqli_fetch_array($result_m);

                    $thisProject = $read['tbl_contract_project'];
                    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
                    $read_p = mysqli_fetch_array($result_p);

                //find end plan
                $resultPlan =Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_contract='$id' and tbl_memberskill_datereport !='' ");  
                // added plan
                $resultPlanAdded =Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_contract='$id' ");  

                $allFaragir = mysqli_num_rows($result_member);
                $faragirHavePlan = mysqli_num_rows($resultPlanAdded);
                $faragirFinishd = mysqli_num_rows($resultPlan);

                ?>

                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <div class="d-flex">
                                    <div class="flex-1 fs--1">
                                        <h5 class="fs-0"><?= $lang[1565].' : '.$read['tbl_contract_code'] ?></h5>
                                        <h5 class="fs-0"><?=$lang[1566].' : '.$read_p['tbl_user_name']?></h5>
                                    </div>
                                    <div class="flex-1 fs--1">
                                        <h5 class="fs-0"><?=$lang[1571].' : '.$read['tbl_contract_startdate']?></h5>
                                        <h5 class="fs-0"><?=$lang[1572].' : '.$read['tbl_contract_enddate']?></h5>
                                        <?=(!empty($read['tbl_contract_editdate']) ? '<h5 class="fs-0">'.$lang[1573].' : '. $read['tbl_contract_editdate'] .'</h5>' : '' ) ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto mt-4 mt-md-0">
                                <?php
                        $resultUser = Query("select * from tbl_user where tbl_user_karfarma='1' and tbl_user_contract like '%[$id]%' ");
                        if(mysqli_num_rows($resultUser) <= 0)
                        {
                            $linkUser = 'user.php?do=add&contract='.$id;
                        }
                        else {
                            $readUser = mysqli_fetch_array($resultUser);
                            $linkUser = 'user.php?do=edit&contract='.$id.'&id='.$readUser['tbl_user_id'];
                        }
                        if($access =='9') {
                    ?>
                                <a href="<?=$linkUser ?>"><button class="btn btn-falcon-primary btn-sm px-4 px-sm-5" type="button"><?=$lang[1449]?></button></a>
                                <?=(mysqli_num_rows($resultUser) > 0 ? '<a href="message.php?karfarma='.$readUser['tbl_user_id'].'"><button class="btn btn-falcon-primary btn-sm px-4 px-sm-5" type="button">'.$lang[1818].'</button></a>' : '')?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-0">
                    <div class="col-lg-9 pe-lg-2">
                        <div class="card mb-3 mb-lg-0">
                            <div class="card-body">
                                <h4 class="mb-3"><?=$lang[1570]?></h4>
                                <p><?= $read['tbl_contract_text'] ?></p>
                                <?php if (!empty($read["tbl_contract_file"])) { ?>

                                <h4><?=$lang[52]?> :</h4>
                                <ul>
                                    <?php
                            $file = explode(",", $read['tbl_contract_file']);
                            foreach ($file as $key => $value) {
                                if (!empty($value)) {
                                    echo '<li><a href="' . $value . '">'.$lang[52].' [' . $key . ']</a></li>';
                                }
                            } ?>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <?php 

$resultAttach = Query("Select * from tbl_attach Where tbl_attach_contract ='$id' order by tbl_attach_id DESC");

?>



                    <div class="card mt-3">
                        <div class="card-header border-bottom border-200 px-0">
                            <div class="d-lg-flex justify-content-between">
                                <div class="row flex-between-center gy-2 px-x1">
                                    <div class="col-auto pe-0">
                                        <h4 class="mb-0"><?= $lang[1448]?></h4>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0 position-relative">
                            <?php
                $i=1;
    ?>
                            <div class="scrollbar">
                                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                    <thead class="text-800 bg-light">
                                        <tr>
                                            <th><?= $lang[60] ?></th>
                                            <th><?=$lang[1580]?></th>
                                            <th><?=$lang[1581]?></th>
                                            <th><?=$lang[1582]?></th>
                                            <th><?=$lang[63] ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        <?php
                                    while ($readAttach=mysqli_fetch_array($resultAttach)) {
                                        $resultAttachContact = Query("select * from tbl_contract where tbl_contract_id = '$id'  ");
                                        $readContact = mysqli_fetch_array($resultAttachContact);
                                        echo '<tr>
                                        <td class="align-middle p-2">' . $i . '</td>
                                        <td class="align-middle p-2">' . $readContact['tbl_contract_number'] . '</td>
                                        <td class="align-middle p-2">' . $readAttach['tbl_attach_number'] . '</td>
                                        <td class="align-middle p-2">' . $readAttach['tbl_attach_title'] . '</td>
                                        <td class="align-middle p-2"><a href="?do=attach&id=' . $readAttach['tbl_attach_id'] . '&contract=' . $id . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '" ><span class="bi-eye-fill"></span></a>                                
                                        </td>
                                        </tr>';
                                    $i++;
                                    }
                            ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 ps-lg-2">

                    <div class="sticky-sidebar">
                        <div class="card mb-3 fs--1">
                            <div class="card-body">

                                <div class="col-lg-12 col-xxl-12">
                                    <div class="row mx-0">
                                        <div class="col-6 border-end <?=($access != '9' ? 'border-bottom' : '')?> py-3">
                                            <h5 class="fw-normal text-600"><?=$allFaragir.' '.$lang[1575]?></h5>
                                            <h6 class="text-500 mb-0"><?=$lang[714]?></h6>
                                        </div>
                                        <div class="col-6 <?=($access != '9' ? 'border-bottom' : '')?> py-3">
                                            <h5 class="fw-normal text-600"><?=$faragirFinishd.' '.$lang[1575]?></h5>
                                            <h6 class="text-500 mb-0"><?=$lang[1576]?></h6>
                                        </div>
                                        <?php if($access != '9') {?>
                                        <div class="col-6 border-end py-3">
                                            <h5 class="fw-normal text-600"><?=$faragirHavePlan.' '.$lang[1575]?></h5>
                                            <h6 class="text-500 mb-0"><?=$lang[1577]?></h6>
                                        </div>
                                        <div class="col-6 py-3">
                                            <h5 class="fw-normal text-600"><?=$allFaragir-$faragirHavePlan.' '.$lang[1575]?></h5>
                                            <h6 class="text-500 mb-0"><?=$lang[1578]?></h6>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sticky-sidebar">
                        <div class="card mb-3 fs--1">
                            <div class="card-body">
                                <h4><?=$lang[1574]?></h4>
                                <div class=" h-100">
                                    <div class="card-body d-flex flex-center flex-column">
                                        <div class="circle_percent" data-percent="<?=($allFaragir !='0' ? abs(((($allFaragir - $faragirFinishd) / $allFaragir) * 100)-100) : '0')?>">
                                            <div class="circle_inner">
                                                <div class="round_per"></div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <h6 class="fs-0 mb-1">
                                                <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span><?=ceil($allFaragir !='0' ? abs(((($allFaragir - $faragirFinishd) / $allFaragir) * 100)-100) : '0')?>
                                            </h6>
                                            <p class="fs--1 mb-0"><?=$lang[1579]?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>






            <?php
            }
            if (isset($do) && $do == 'attach') {

                $result = Query("SELECT * FROM tbl_attach Where tbl_attach_id = '$id'");
                $read = mysqli_fetch_array($result);

                $contractId = $read['tbl_attach_contract'];
                $resultAttach = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                $readContact = mysqli_fetch_array($resultAttach);

                ?>
            <div class="col-12 p-3">
                <div class="form-group">
                    <p><?=$lang[1580]?> :
                        <?= toPersianNum($readContact['tbl_contract_number']) ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[1581]?> :
                        <?= toPersianNum($read['tbl_attach_number']) ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[61]?> :
                        <?= $read['tbl_attach_title'] ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[1458]?> :
                        <?= toPersianNum($read['tbl_attach_startdate']) ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[1459]?> :
                        <?= toPersianNum($read['tbl_attach_enddate']) ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[1573]?> :
                        <?= toPersianNum($read['tbl_attach_editdate']) ?></p>
                </div>
                <div class="form-group">
                    <p><?=$lang[1527]?> :
                        <?= $read['tbl_attach_text'] ?></p>
                </div>
                <div class="form-group">
                    <label><?=$lang[52]?></label>
                    <p><?php
                                $file = explode(",", $read['tbl_attach_file']);
                                foreach ($file as $key => $value) {
                                    if (!empty($value)) {
                                        echo '<a href="' . $value . '">'.$lang[52].'  [' . $key . ']</a>';
                                    }
                                } ?>
                    </p>
                </div>
            </div>
            <?php
            }
            if (isset($do) && $do == 'karfarma') {
                $result = Query("SELECT * FROM tbl_contract Where tbl_contract_id = '$id'");
                $read = mysqli_fetch_array($result);
                echo '<div class="col-lg-6 col-12 p-3"><form action="?do=update" method="POST" >
                        <input type="hidden" name="id"  value="' . $id . '" class="form-control">
                        <div class="form-group">
                            <label>' . $lang[92] . '</label>
                            <input type="text" name="name"  value="' . $read['tbl_contract_karname'] . '" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>'.$lang[378].'</label>
                            <input type="text" name="mobile"  value="' . $read['tbl_contract_karmobile'] . '"  class="form-control" maxlength="11">
                        </div>
                        <div class="form-group">
                            <label>'.$lang[3].'</label>
                            <input type="text" name="password"  class="form-control" >
                        </div>';
                echo '<div class="form-group">
                            <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                        </div></form></div>';
            }
            if (isset($do) && $do == 'update') {
                if (isset($_POST['submit'])) {
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $mobile = $_POST['mobile'];
                    $password = $_POST['password'];
                    $date = date('Y-m-d H:i:s');
                    if (empty($password)) {
                        Query("Update tbl_contract SET tbl_contract_karname='$name' ,tbl_contract_karmobile='$mobile' ,tbl_contract_updatementor='$date' ,tbl_contract_idmentor='$this_user_id' Where tbl_contract_id = '$id' ");
                        $_SESSION['error'] = $lang[21];
                        header("Location: contract.php");
                        exit();
                    } else {
                        $npassword = hash("md5", $password);
                        Query("Update tbl_contract SET tbl_contract_karname='$name' ,tbl_contract_karmobile='$mobile' , tbl_contract_updatementor='$date' , tbl_contract_karpassword = '$npassword' , tbl_contract_idmentor='$this_user_id' Where tbl_contract_id = '$id' ");
                        $_SESSION['error'] = $lang[21];
                        header("Location: contract.php");
                        exit();
                    }
                }
            }



            if (!empty($do) && $do == 'mentor' && $access == '9') {
                
                echo '<div class="table-responsive scrollbar">
                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                    <thead class="text-800 bg-light">
                        <tr>
                            <th>'.$lang[60].'</th>
                            <th>'.$lang[92].'</th>
                            <th>'.$lang[63].'</th>
                        </tr>
                    </thead>
                    <tbody>';
                $i = 1;
                $result = Query("Select * from tbl_contract where tbl_contract_project = '$userCode' and tbl_contract_id = '$id' order by tbl_contract_id DESC ");
                $read = mysqli_fetch_array($result);
                $mentor = (!empty($read['tbl_contract_mentor']) ? explode(",",$read['tbl_contract_mentor']) : '');
                if(!empty($read['tbl_contract_mentor'])){
                foreach ($mentor as $key => $value) {
                    if(!empty($value))
                    {
                        $result_m = Query("select tbl_user_name,tbl_user_id from tbl_user where tbl_user_usercode = '$value'  ");
                        $read_m = mysqli_fetch_array($result_m);
                        echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                        <td class="align-middle p-2">' . $read_m['tbl_user_name'] . '</td>
                        <td class="align-middle p-2">
                        <a href="?do=deletementro&id=' . $value . '&contract=' . $_GET['id'] . '"  class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                        <a href="result.php?id=' . $read_m['tbl_user_id'] . '"  class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1703] . '"><span class="bi-bar-chart-line-fill"></span></a>
                        <a href="result-contract.php?id=' . $read_m['tbl_user_id'] . '&contract='.$id.'"  class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="نتایچ این پروژه"><span class="bi-bar-chart-line-fill"></span></a></td>
                        </tr>';
                    $i++;
                    }
                }
            }
                echo '</tbody></table>';

        
            }
            if (isset($do) && $do == 'deletementro') {
                $result = Query("SELECT * from tbl_contract  Where tbl_contract_project = '$userCode' and tbl_contract_id = '$_GET[contract]' ");
                if(mysqli_num_rows($result) > 0){
                $read = mysqli_fetch_array($result);

                echo '<div class="col-lg-6 col-12 p-3"><h3>' . $lang[25] . '</h3><br />
                 <form action=" " method="POST">
                 <div class="form-group margin-top">
                 <input type="hidden" name="user" value="' . $id . '" >
                 <input type="hidden" name="contract" value="' .$_GET['contract'] . '" >
                 <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                 <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                if (isset($_POST['submit'])) {
                    $user = $_POST['user'];
                    $contract = $_POST['contract'];
                    $result_contract = Query("select tbl_contract_mentor from tbl_contract Where tbl_contract_id = '$contract' ");
                    $read_contract = mysqli_fetch_array($result_contract);
                    $mentor = str_replace("$user,","",$read_contract['tbl_contract_mentor']);
                    Query("Update tbl_contract SET tbl_contract_mentor = '$mentor' Where tbl_contract_project = '$userCode' and tbl_contract_id = '$contract' ");
                    header("Location: contract.php?do=mentor&id=$contract");
                } elseif (isset($_POST['nodelete'])) {
                    $contract = $_POST['contract'];
                    header("Location: contract.php?do=mentor&id=$contract");
                }
            }
            }
            if (isset($do) && $do == 'mentoradd') {
                $result_contract = Query("select tbl_contract_codesection from tbl_contract Where tbl_contract_id = '$id' ");
                $read_contract = mysqli_fetch_array($result_contract);
                $codesection = $read_contract['tbl_contract_codesection'];
                $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codesection' ");
                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                $thisBakhsh = $read_bakhsh['tbl_categories_code'];
?>
            <div class="col-12 p-3">
                <form action="#" method="post">
                    <input type="hidden" name="contract" value="<?= $id ?>" required />
                    <div class="form-group">
                        <label><?=$lang[1923]?></label>
                        <select name="mentor" class="form-control select2">
                            <?php
                                $resultCoach = Query("SELECT * From tbl_user Where tbl_user_access = '5' and tbl_user_bakhsh like '%$thisBakhsh%' ");
                                while ($readCoach = mysqli_fetch_array($resultCoach)) {
                                    $resultContract = Query("select * from tbl_contract Where tbl_contract_mentor ='$readCoach[tbl_user_usercode]' and tbl_contract_id = '$id' ");
                                    echo '<option value="' . $readCoach['tbl_user_id'] . '" '.(mysqli_num_rows($resultContract) > 0 ? 'checked' : '').'>' . $readCoach['tbl_user_name'] . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                    <div class="form-group ">
                        <input type="submit" name="submit" value="<?=$lang[1924]?>" class="btn btn-primary mt-3 mb-4 checkmentor" />
                    </div>
                </form>
                <div class="ajaxresult">

                </div>
            </div>
            <?php
            }
             if (isset($do) && $do == 'mentoradditem') {
                if (isset($_POST['submit'])) {
                    $contract=sqi($_POST['contract']);
                    $user=sqi($_POST['user']);
                    $date = date('Y-m-d H:i:s');
                    $result = Query("Select * from tbl_contract where tbl_contract_project = '$userCode' and tbl_contract_id = '$contract' ");
                    $read = mysqli_fetch_array($result);
                    $mentor = (!empty($read['tbl_contract_mentor']) ? $read['tbl_contract_mentor'].$user.',' : $user.',');
                    $resultCheckMentor = Query("select * from tbl_contract where tbl_contract_mentor like  '%$user%' and tbl_contract_id = '$contract' ");
                    if(mysqli_num_rows($resultCheckMentor) > 0)
                    {
                        header("Location: contract.php?do=mentor&id=$contract");
                        exit();
                    }
                    else {
                        Query("Update tbl_contract SET tbl_contract_mentor='$mentor' ,tbl_contract_projectupdate='$date' Where tbl_contract_id = '$contract' ");
                        header("Location: contract.php?do=mentor&id=$contract");
                        exit();
                    }
                    
                }
            }
            // if (isset($do) && $do == 'mentoradd') {
            //     if (isset($_POST['submit'])) {
            //         $contract=sqi($_POST['contract']);
            //         $user=sqi($_POST['user']);
            //         $date = date('Y-m-d H:i:s');
            //         Query("Update tbl_contract SET tbl_contract_mentor='$user' ,tbl_contract_projectupdate='$date' Where tbl_contract_id = '$contract' ");
            //         header("Location: index.php");
            //         exit();
            //     }
            // }

            if (isset($do) && $do == 'importother') {
                echo '<div class="col-10 p-3">
                            <form action="?do=importotheradd" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="contract" value="' . $id . '" required />
                            <div class="form-group ">
                            <label>'.$lang[127].'</label>
                            <input type="text" name="fname" class="form-control">
                            </div>
                            <div class="form-group ">
                            <label>'.$lang[128].'</label>
                            <input type="text" name="lname" class="form-control">
                            </div>
                            <div class="form-group ">
                            <label>'.$lang[385].'</label>
                            <input type="text" name="nationalcode" class="form-control" maxlength="10">
                            </div>
                            <div class="form-group ">
                            <label>'.$lang[378].'</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11">
                            </div>
                            <div class="form-group ">
                            <label>'.$lang[30].'</label>
                            <input type="text" name="email" class="form-control" >
                            </div>
                            
                            <div class="form-group" >
                                    <label>'.$lang[1718].' </label>
                                    <textarea rows="7" name="firstsmstext" class="form-control"></textarea>
                            </div>
                            <div class="form-group ">
                            <input type="submit" name="submit" value="'.$lang[1719].'" class="btn btn-primary mt-3" />
                            </div>
                            </form></div>';
            }

            if (isset($do) && $do == 'importotheradd') {
                if (isset($_POST['submit'])) {
                    $firstsmstext = $_POST['firstsmstext'];
                    $contract = $_POST['contract'];
                    $fname = $_POST['fname'];
                    $lname = $_POST['lname'];
                    $nationalcode = number_convert($_POST['nationalcode']);
                    $mobile = number_convert($_POST['mobile']);
                    $email = $_POST['email'];
                    $date = jdate("Y/m/d - h:i:s A");
                    $systemcode = Code("", "no", 11);
                    $npass = password_hash("$nationalcode", PASSWORD_BCRYPT, array('cost' => 12));

                    
                    $result_nationalcode = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_delete='0' and  tbl_member_nationalcode = '$nationalcode' ");
                    $result_mobile = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_delete='0' and tbl_member_mobile = '$mobile' ");
                       // $resultCheckContract = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_lastcontract like '%[$contract]%' and tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode'");
                    if (mysqli_num_rows($result_nationalcode) > 0) {
                        $read = mysqli_fetch_array($result);
                        $json = $read['tbl_member_lastcontract'];
                        if(strpos($json,"[$contract],") !== false)
                        {
                            $json =   $json;
                        }
                        else {
                            $json .=   '['.$contract.'],';
                        }
                        
                        Query("update tbl_member set tbl_member_lastcontract='$json',tbl_member_model='0' where tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode'   ");
                        $_SESSION['error'] = $lang[1935];
                        header("Location: faragir.php?contract=$contract");
                    }
                    else if (mysqli_num_rows($result_mobile) > 0) {
                        $read = mysqli_fetch_array($result);
                        $json = $read['tbl_member_lastcontract'];
                        if(strpos($json,"[$contract],") !== false)
                        {
                            $json =   $json;
                        }
                        else {
                            $json .=   '['.$contract.'],';
                        }
                        
                        Query("update tbl_member set tbl_member_lastcontract='$json',tbl_member_model='0' where tbl_member_delete='0' and tbl_member_mobile = '$mobile'  ");
                        $_SESSION['error'] = $lang[1936];
                        header("Location: faragir.php?contract=$contract");
                    }
                     else {
                        $json= '['.$contract.'],';
                        Query("insert into tbl_member (tbl_member_firstcontract,
                                        tbl_member_importdate,
                                        tbl_member_fname,
                                        tbl_member_lname,
                                        tbl_member_nationalcode,
                                        tbl_member_mobile,
                                        tbl_member_systemcode,
                                        tbl_member_firstsms,
                                        tbl_member_firstsmstext,
                                        tbl_member_delete,
                                        tbl_member_lastcontract,
                                        tbl_member_level,
                                        tbl_member_password,
                                        tbl_member_email,
                                        tbl_member_model) values('$contract',
                                        '$date',
                                        '$fname',
                                        '$lname',
                                        '$nationalcode',
                                        '$mobile',
                                        '$systemcode',
                                        '1',
                                        '$firstsmstext',
                                        '0',
                                        '$json',
                                        '0',
                                        '$npass',
                                        '$email',
                                        '0')");
                        if ($firstsmstext != '') {
                            //smsTwo($mobile_convert, $firstsmstext);
                        }
                        $_SESSION['error'] = $lang[1734];
                        header("Location: faragir.php?contract=$contract");
                    }
                }
            }
            if (isset($do) && $do == 'import') {
                echo '<div class="col-12 p-3"><h5>'.$lang[1726].'</h5>
                            <form action="?do=importupload" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="' . $id . '" required />
                            <div class="form-group ">
                            <label>'.$lang[52].' *.XLSX </label>
                            <input type="file" name="file"  class="form-control" required />
                            </div>
                            <hr>
                            <div class="form-group ">
                            <input type="submit" value="'.$lang[1727].'" class="btn btn-primary" />
                            </div>
                            </form></div>';
            }

            if (isset($do) && $do == 'importupload') {
                unset($_SESSION['user_add']);
                unset($_SESSION['dublicate_user']);
                unset($_SESSION['count']);
                echo '<div class="col-12 p-3"><h5>'.$lang[1609].'</h5>';
                if (isset($_FILES['file'])) {
                    $id = $_POST['id'];
                    unset($_SESSION['filename']);
                    if ($_FILES['file']) {
                        $file_attach = $_FILES['file']['name'];
                        $read_file_name = explode(".", $file_attach);
                        $extn = end($read_file_name);
                        $file_name = 'file_import_' . time() . '.' . $extn;
                        if ($extn == 'xlsx' || $extn == 'XLSX') {
                            move_uploaded_file($_FILES['file']["tmp_name"], "../upload/import/" . $file_name);
                        }
                    }
                    echo '<div class="import p-3"><form action="?do=runaddmember" method="post" enctype="multipart/form-data">
                <input type="hidden" name="file" value="' . $file_name . '">
                <input type="hidden" name="contract" value="' . $id . '">
                <div class="input-wrapper">
                    <input type="text" name="fname" class="form-control formatnumber" maxlength="2">
                    <label>'.$lang[127].'</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="lname" class="form-control formatnumber" maxlength="2">
                    <label>'.$lang[128].'</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="nationalcode" class="form-control formatnumber" maxlength="2" required>
                    <label>'.$lang[132].'</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="mobile" class="form-control formatnumber" maxlength="2">
                    <label>'.$lang[378].'</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="email" class="form-control formatnumber" maxlength="2">
                    <label> '.$lang[30].'</label> 
                </div>
                <div class="form-group" >
                        <label>'.$lang[1718].' </label>
                        <textarea rows="7" name="firstsmstext" class="form-control"></textarea>
                </div>
                <div class="form-group ">
                    <input type="submit" name="submit" value="'.$lang[1729].'" class="btn btn-primary mt-3"  />
                    </div>
              </form></div>';
                    $_SESSION['filename'] = $file_name;
                    if ($xlsx = SimpleXLSX::parse('../upload/import/' . $file_name)) {
                        echo '<div class="col-12 p-3"><h5>'.$lang[1730].'</h5><p>'.$lang[1731].'<p>';
                        echo '<table class="table table-bordered table-hover table-striped margin-top">';
                        $dim = $xlsx->dimension();
                        $cols = $dim[0];
                        $m = 1;
                        $_SESSION['row'] = count($xlsx->rows());
                        for ($b = 0; $b <= 1; $b++) {
                            $h = $xlsx->rows();
                            $r = $h[$b];
                            if ($m == 1) {
                                echo '<tr>';
                                for ($i = 0; $i < $cols; $i++) {
                                    echo '<th>' . (isset($r[$i]) ? $r[$i] : '&nbsp;') . '(' . $i . ')</th>';
                                }
                                echo '</tr>';
                            } else {
                                echo '<tr>';
                                for ($i = 0; $i < $cols; $i++) {
                                    echo '<td>' . (isset($r[$i]) ? $r[$i] : '&nbsp;') . '</td>';
                                }
                                echo '</tr>';
                            }
                            $m++;
                        }
                        echo '</table></div>';
                    } else {
                        echo SimpleXLSX::parseError();
                    }
                }
            }

            if (isset($do) && $do == 'runaddmember') {
                $firstsmstext = $_POST['firstsmstext'];
                $file = $_POST['file'];
                $contract = $_POST['contract'];
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $nationalcode = $_POST['nationalcode'];
                $mobile = $_POST['mobile'];
                $email = $_POST['email'];
                $date = jdate("Y/m/d - h:i:s A");
                if (empty($_SESSION['count'])) {
                    $_SESSION['count'] = 0;
                }
                if ($xlsx = SimpleXLSX::parse('../upload/import/' . $file)) {
                    $dim = $xlsx->dimension();
                    $allData = count($xlsx->rows());
                    //$count = ($page * 10);
                    // echo $_SESSION['count'];
                    if ($_SESSION['count'] == ($allData - 1)) {
                        echo '<div class="table_chart p-3">
                        '.$lang[1732].' : ' . @$_SESSION['user_add'] . '</div>';
                        echo '<div class="table_chart p-3">'.$lang[1733].' : ' . @$_SESSION['dublicate_user'] . '</div>';
                        unset($_SESSION['user_add']);
                        unset($_SESSION['dublicate_user']);
                        unset($_SESSION['count']);
                    } else {
                        echo '<table class="table table-bordered" style="width:200px">';
                        for ($i = 1; $i < $allData; $i++) {
                            
                            $cols = $dim[0];
                            $m = 1;
                            $mobile_convert = '';
                            $nationalcode_convert = '';
                            $systemcode = Code("", "no", 11);
                            @$r = $xlsx->rows()[$i];
                            @$mobile_convert = number_convert($r[$mobile]);
                            @$nationalcode_convert = number_convert($r[$nationalcode]);
                            $npass = password_hash("$nationalcode_convert", PASSWORD_BCRYPT, array('cost' => 12));
                            @$read_fname = $r[$fname];
                            @$read_lname = $r[$lname];
                            @$read_email = $r[$email];
                            @$read_father = $r[$father];
                            $result = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_delete='0' and ( tbl_member_nationalcode = '$nationalcode_convert' or tbl_member_mobile = '$mobile_convert' ) ");
                            if (mysqli_num_rows($result) > 0) {
                                $read = mysqli_fetch_array($result);

                                $json = $read['tbl_member_lastcontract'];
                                if(strpos($json,"[$contract],") !== false)
                                {
                                    $json =   $json;
                                }
                                else {
                                    $json .=   '['.$contract.'],';
                                }
                                Query("update tbl_member set tbl_member_lastcontract='$json',tbl_member_model='0' where tbl_member_delete='0' and ( tbl_member_nationalcode = '$nationalcode_convert' or tbl_member_mobile = '$mobile_convert' )  ");
                                @$_SESSION['dublicate_user']++;
                            } else {
                                //echo 'add<br>';
                                echo '<tr><td>' . ($i + (($page - 1) * 50)) . '</td><td>' . @$r[$nationalcode] . '</td></tr>';
                                @$contractNew =   '['.$contract.'],';
                                Query("insert into tbl_member (tbl_member_firstcontract,
                                        tbl_member_importdate,
                                        tbl_member_fname,
                                        tbl_member_lname,
                                        tbl_member_nationalcode,
                                        tbl_member_mobile,
                                        tbl_member_systemcode,
                                        tbl_member_firstsms,
                                        tbl_member_firstsmstext,
                                        tbl_member_delete,
                                        tbl_member_lastcontract,
                                        tbl_member_level,
                                        tbl_member_password,
                                        tbl_member_email,
                                        tbl_member_model) values('$contract',
                                        '$date',
                                        '$read_fname',
                                        '$read_lname',
                                        '$nationalcode_convert',
                                        '$mobile_convert',
                                        '$systemcode',
                                        '1',
                                        '$firstsmstext',
                                        '0',
                                        '$contractNew',
                                        '0',
                                        '$npass',
                                        '$read_email',
                                        '0')");
                                @$_SESSION['user_add']++;
                                if ($firstsmstext != '') {
                                    //smsTwo($mobile_convert, $firstsmstext);
                                }
                            }
                            $_SESSION['count']++;
                        }
                        echo '</table>';
                        echo '<div class="table_chart p-3">
                        '.$lang[1732].' : ' . @$_SESSION['user_add'] . '</div>';
                        echo '<div class="table_chart p-3">'.$lang[1733].' : ' . @$_SESSION['dublicate_user'] . '</div>';
                    }
                } else {
                    unset($_SESSION['user_add']);
                        unset($_SESSION['dublicate_user']);
                        unset($_SESSION['count']);
                    $_SESSION['error'] = $lang[1734];
                    header("Location: faragir.php?contract=$contract");
                }
            }

            if (isset($do) && $do == 'search') {
                if (isset($_POST['go'])) {
                    $keyword_search = $_POST['keyword'];
                    header("Location: contract.php?keyword=$keyword_search");
                }
            }
            echo '</div>
            </div></div>
        </div>';
            if (isset($do) && $do == 'stats') {
                if (!empty($_GET['contract'])) {
                    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                    $read_contract = mysqli_fetch_array($result_contract);
              
                    $thisProject = $read_contract['tbl_contract_project'];
                    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
                    $read_p = mysqli_fetch_array($result_p);
                    echo '<div class="col-md-12">
                    <div class="box box-body ">
                <div class="card mb-3">
                    <div class="card-body custom-h5">
                      <div class="row justify-content-between align-items-center">
                        <div class="col">
                          <div class="d-flex">
                            <div class="flex-1 fs--1">
                              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
                              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
                              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
                              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
                            </div>
                            <div class="flex-1 fs--1">
                            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
                            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_p['tbl_user_name'] . '</h5>
                            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
                            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
                            </div>
                
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                    </div>
                    </div>';
                }

                $resultMember = Query("SELECT tbl_member_id From tbl_member where tbl_member_lastcontract='$_GET[contract]' ");
                $resultSkill = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_data !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultInterview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$_GET[contract]' and tbl_interview_delete='0'  ");
                
                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultCheckEnd = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");
                
                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_status ='1' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");

                $resultPlans = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");

                $resultMember = Query("SELECT tbl_member_id From tbl_member where tbl_member_lastcontract='$_GET[contract]' ");
                
                $resultMessage = Query("SELECT tbl_skillmessage_id From tbl_skillmessage Where tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' ");
                $resultMessageRead = Query("SELECT tbl_skillmessage_id From tbl_skillmessage Where tbl_skillmessage_status ='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' ");
                $resultMessageCoach = Query("SELECT tbl_coachmessage_id From tbl_coachmessage Where tbl_coachmessage_reply='0' and tbl_coachmessage_contract='$_GET[contract]' and tbl_coachmessage_delete ='0' ");
                $resultMessageReadCoach = Query("SELECT tbl_coachmessage_id From tbl_coachmessage Where tbl_coachmessage_status ='0' and tbl_coachmessage_reply='0' and tbl_coachmessage_contract='$_GET[contract]' and tbl_coachmessage_delete ='0' ");

                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$_GET[contract]' ");
                

                $koleFaragir = mysqli_num_rows($resultMember);
                $bedonMaharat = mysqli_num_rows($resultMember) - mysqli_num_rows($resultSkill);
                $mosahebeha = mysqli_num_rows($resultInterview);
                $barnameRizinashode = $koleFaragir - mysqli_num_rows($resultPlan);
                $barnameRizishodeEblaghnashode = abs(mysqli_num_rows($resultPlan) - mysqli_num_rows($resultCheck));
                $barnameRizishodeEblaghShode = mysqli_num_rows($resultCheck) - mysqli_num_rows($resultPlan);
                $barnameRizishodeEblaghShodeEnd = mysqli_num_rows($resultCheckEnd) - mysqli_num_rows($resultPlan);
                echo '<div class="stats">
                        <div class="stats-item">
                        <span class="icon-dashboard"></span>
                            <h5>تاریخ امروز</h5>
                            <h6>'.toPersianNum(jdate("Y/m/d")).'</h6>
                        </div>
                        <div class="clear"></div>
                        <h1>آمار فراگیر</h1>
                        
                        <div class="stats-item">
                        <span class="icon-account_box"></span>
                        <h5>تعداد کل فراگیر ها</h5>
                        <h6>'.toPersianNum($koleFaragir).'</h6>
                        </div>

                        <div class="stats-item">
                        <span class="icon-assignment_ind"></span>
                        <h5>فراگیرهای بدون مهارت</h5>
                        <h6>'.toPersianNum($bedonMaharat).'</h6>
                        </div>
                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع مصاحبه ها</h5>
                        <h6>'.toPersianNum($mosahebeha).'</h6>
                        </div>
                        <div class="clear"></div>

                        <h1>آمار پلن</h1>

                        <div class="stats-item"><span class="icon-announcement"></span>
                        <h5>پلن برنامه ریزی نشده است</h5>
                        <h6>'.toPersianNum($barnameRizinashode).'</h6>
                        </div>


                        <div class="stats-item"><span class="icon-featured_play_list"></span>
                        <h5>پلن برنامه ریزی شده ابلاغ نشده</h5>
                        <h6>'.toPersianNum($barnameRizishodeEblaghnashode).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-assignment"></span>
                        <h5>برنامه ریزی و ابلاغ شده</h5>
                        <h6>'.toPersianNum($barnameRizishodeEblaghShode).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-broken_image"></span>
                        <h5>در حال اجرا</h5> 
                        <h6>'.toPersianNum(abs($barnameRizinashode+$barnameRizishodeEblaghnashode+$barnameRizishodeEblaghShode - $koleFaragir) - mysqli_num_rows($resultCheckEnd)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-broken_image"></span>
                        <h5>خاتمه یافته</h5> 
                        <h6>'.toPersianNum(mysqli_num_rows($resultCheckEnd)).'</h6>
                        </div>


                        <div class="clear"></div>
                        <h1>آمار پیامهای منتور</h1>
                        
                        <div class="clear"></div>
                        <div class="stats-item"><span class="icon-poll"></span>
                        <h5>مجموع  پیامها منتور</h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessage)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع  پیامهای بدون پاسخ منتور </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageRead)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat"></span>
                        <h5>مجموع  پیامهای پاسخ داده شده منتور </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessage) - mysqli_num_rows($resultMessageRead)).'</h6>
                        </div>
                        <div class="clear"></div>
                        <h1>آمار پیامهای مربی</h1>
                        <div class="stats-item"><span class="icon-poll"></span>
                        <h5>مجموع  پیامها مربی</h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageCoach)).'</h6>
                        </div>


                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع  پیامهای بدون پاسخ مربی  </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageReadCoach)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat"></span>
                        <h5>مجموع  پیامهای پاسخ داده شده مربی </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageCoach) - mysqli_num_rows($resultMessageReadCoach)).'</h6>
                        </div>

                </div>';
            }
            ?>
            <?php
include './footer.inc.php';

?>