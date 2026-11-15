<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
if (!isset($do)) {
    $pagetitle = $lang[1451];
} else if (isset($do) && $do == 'question') {
    $pagetitle = $lang[1589];
} else if (isset($do) && $do == 'answer') {
    $pagetitle = $lang[1608];
} else if (isset($do) && $do == 'op') {
    $pagetitle = $lang[1491];
} else {
    $pagetitle = $lang[1451];
}
$thisaccess = '1,5,9';
include './header.inc.php';

$select = '';
if (!empty($_GET['user'])) {
    $select = " and tbl_member_id= '$_GET[user]'  ";
}


if (!empty($_GET['contract'])) {
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$getcontract]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$getcontract' ");
    if (mysqli_num_rows($result_contract) <= 0) {
        header("Location: index.php");
        exit();
    }
    $read_contract = mysqli_fetch_array($result_contract);

    $result_project = Query("SELECT tbl_user_name From tbl_user where tbl_user_usercode='$read_contract[tbl_contract_project]'");
    $read_project = mysqli_fetch_array($result_project);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">' . $lang[1440] . '  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">' . $lang[1567] . '  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">' . $lang[1611] . '  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">' . $lang[1612] . '  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">' . $lang[1613] . '  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">' . $lang[1566] . '  : ' . $read_project['tbl_user_name'] . '</h5>
            <h5 class="fs-0">' . $lang[1571] . '  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">' . $lang[1572] . '  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    </div>
    </div>';
} else {
    $thiscontract = " where tbl_member_delete='0'";
}

if (isset($do) && ($do == 'question' || $do == 'answer')) {

    $result = Query("SELECT * From tbl_member where tbl_member_delete='0' and tbl_member_id='$id'");
    if (mysqli_num_rows($result) <= 0) {
        header("Location: index.php");
        exit();
    }
    $read = mysqli_fetch_array($result);
    $fullName = $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'];
    $nationalCode = toPersianNum($read['tbl_member_nationalcode']);
    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1614] . '  : <a href="faragir.php?contract=' . $_GET['contract'] . '&user=' . $id . '">' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</a></h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[385] . '  : ' . toPersianNum($read['tbl_member_nationalcode']) . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[378] . '  : ' . toPersianNum($read['tbl_member_mobile']) . '</h5>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>                    
    </div>
    </div>';
}


if (isset($do) && $do == 'op') {
    $result_member = Query("SELECT * From tbl_member where tbl_member_delete='0' and tbl_member_id='$_GET[user]'");
    $read_member = mysqli_fetch_array($result_member);

    echo '<div class="col-md-12 mb-3">
<div class="box box-body ">
    <div class="card mb-3">
        <div class="card-body custom-h5">
        <div class="row justify-content-between align-items-center">
            <div class="col">
            <div class="d-flex">
                <div class="flex-1 fs--1">
                <h5 class="fs-0">' . $lang[1614] . '  : <a href="faragir.php?contract=' . $_GET['contract'] . '&user=' . $_GET['user'] . '">' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</a></h5>
                </div>
                <div class="flex-1 fs--1">
                <h5 class="fs-0">' . $lang[385] . '  : ' . toPersianNum($read_member['tbl_member_nationalcode']) . '</h5>
                </div>
                <div class="flex-1 fs--1">
                <h5 class="fs-0">' . $lang[378] . '  : ' . toPersianNum($read_member['tbl_member_mobile']) . '</h5>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>                    
</div>
</div>';
}

?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) && $access == '9' ? '
                            <div class="plus">
                            <a href="contract.php?do=import&id=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1609] . '"><span class="bi-person-lines-fill"></span></a>
                            <a href="contract.php?do=importother&id=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1610] . '"><span class="bi-person-plus-fill"></span></a></div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>

            <div class="card-body p-0 position-relative">
                <?php
                if (!isset($do)) { ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="?do=search" method="POST" class="search-form">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <input type="hidden" class="form-control" name="contract" value="<?= $_GET['contract'] ?>">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>

                    <?php
                    // start count db result for pagination 
                    $result_for_count = Query("SELECT * From tbl_member $thiscontract and tbl_member_delete='0' and  (tbl_member_fname like '%$keyword%' or tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%') $select ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination         
                    $result = Query("SELECT * From tbl_member $thiscontract and tbl_member_delete='0' and (tbl_member_fname like '%$keyword%' or tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%') $select ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . ' </th>
                                    <th>' . $lang[127] . ' </th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[1583] . '</th>
                                    ' . ($access == '5' ? '<th>' . $lang[1337] . '</th>' : '') . '
                                    ' . ($access == '9' ? '<th>' . $lang[1923] . '</th>' : '') . '
                                    
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $memberContract = $_GET['contract'];
                        //find contract bakhsh
                        $result_contract = Query("SELECT tbl_contract_codesection From tbl_contract where tbl_contract_id='$memberContract' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        $codeSection = $read_contract['tbl_contract_codesection'];
                        // find bakhsh info
                        $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codeSection' and tbl_categories_lng='$language_default'");
                        $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                        $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read[tbl_member_id]' and tbl_memberquestion_contract='$getcontract' ");
                        if (mysqli_num_rows($resultSelectQuestion) > 0) {
                            $readSelectQuestion = mysqli_fetch_array($resultSelectQuestion);
                            $arzyabi = json_decode($readSelectQuestion['tbl_memberquestion_data'], True);
                            $arzyabi = count($arzyabi);
                            $check = $readSelectQuestion['tbl_memberquestion_check'];
                            $adminCheck = $readSelectQuestion['tbl_memberquestion_admincheck'];
                        } else {
                            $arzyabi = 0;
                            $check = 0;
                            $adminCheck = 0;
                        }
                        echo '<tr>
                                <td class="align-middle p-2">' . ($page != 1 ? (($page - 1) * $show_per_page) + $i : $i) . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="' . (!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_nationalcode']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_mobile']) . '</td>';
                        if ($access == '5') { // start if user is mentor
                            echo '<td class="align-middle p-2"><form action="" id="formwork">
                                        <select name="status[]" style="width: 100%;"
                                        class="select2 status" multiple="multiple" >';
                            $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                            while ($read_skill = mysqli_fetch_array($result_skill)) {
                                $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                $read_memberskill = mysqli_fetch_array($result_memberskill);
                                $skillCode = $read_skill['tbl_categories_code'];
                                echo '<option value="' . $read_skill['tbl_categories_code'] . '" ' . (mysqli_num_rows($result_memberskill) > 0 ? (stripos($read_memberskill['tbl_memberskill_data'], "$skillCode") !== false ? 'selected' : '') : '') . '>' . $read_skill['tbl_categories_title'] . '</option>';
                            }
                            echo ' </select>
                                        <input type="hidden" id="userid" name="userid" value="' . $read['tbl_member_id'] . '">
                                        <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                        <input name="submit" type="submit" class="sendstatus btn btn-dark" value="' . $lang[20] . '" class="btn btn-xs btn-secondary">
                                        </form></td>';
                        }

                        $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'  ");
                        $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$getcontract' and tbl_interview_menti='$read[tbl_member_id]' and tbl_interview_delete='0'  ");
                        $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");

                        $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                        $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");


                        $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                        $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");

                        $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");

                        echo '<td class="align-middle p-2">' . $read['tbl_member_updateresume'] . '</td>';
                        if ($access == '9') {
                            echo '<td class="align-middle p-2"><form action="" id="selectmentor">
                                    <select name="mentor" class="select2 mentor"  ><option value="0">' . $lang[70] . '</option>';
                            $result_mentor = Query("Select * from tbl_contract where tbl_contract_project = '$userCode' and tbl_contract_id = '$getcontract' order by tbl_contract_id DESC ");
                            $read_mentor = mysqli_fetch_array($result_mentor);
                            $mentor = (!empty($read_mentor['tbl_contract_mentor']) ? explode(",", $read_mentor['tbl_contract_mentor']) : '');
                            if (!empty($read_mentor['tbl_contract_mentor'])) {
                                foreach ($mentor as $key => $value) {
                                    if (!empty($value)) {
                                        $result_m = Query("select * from tbl_user where tbl_user_usercode = '$value'  ");
                                        $read_m = mysqli_fetch_array($result_m);

                                        $result_check = Query("select * from tbl_projectmember where tbl_projectmember_status='0' and tbl_projectmember_admin='$this_user_id' and tbl_projectmember_mentor='$value' and tbl_projectmember_user='$read[tbl_member_id]' and tbl_projectmember_contract='$getcontract' ");

                                        echo '<option value="' . $read_m['tbl_user_usercode'] . '" ' . (mysqli_num_rows($result_check) > 0 ? 'selected' : '') . ' >' . $read_m['tbl_user_name'] . '</option>';
                                    }
                                }
                            }
                            echo ' </select>
                                    <input type="hidden" id="userid" name="userid" value="' . $read['tbl_member_id'] . '">
                                    <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                    <input name="submit" type="submit" class="sendmentor btn btn-dark" value="' . $lang[20] . '" class="btn btn-xs btn-secondary">
                                    </form></td>';
                        }
                        echo '<td class="align-middle p-2">
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588] . '" target="_blank"><span class="bi-file-earmark-person"></span></a>';
                        if ($access == '5') { // start if user is mentor

                            echo '<a href="?do=question&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1589] . ' (' . toPersianNum($arzyabi) . ')" ><span class="bi-patch-question-fill"></span></a>
                                        <a href="?do=answer&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1590] . '  (' . ($check == '0' ? $lang[1591] : ($check == '1' && $adminCheck == 0 ? $lang[1592] : $lang[1593])) . ')" target="_blank"><span class="bi-check-circle-fill"></span></a>
                                        <a href="interview.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1594] . ' (' . toPersianNum(mysqli_num_rows($result_for_interview)) . ')"  ><span class="bi-mic-fill"></span></a>
                                        <a href="plan.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="';
                            if (mysqli_num_rows($resultPlan) == '0') {
                                echo $lang[1595];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1596];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1597];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1598];
                            } else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                echo $lang[1599];
                            }
                            echo '" ><span class="bi-layout-text-window-reverse"></span></a>';
                            if ($access == '1' && mysqli_num_rows($resultCheckFinal) > 0) {
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='1' and tbl_opinionend_delete='0' ");


                                if (mysqli_num_rows($resultOp) > 0) {
                                    echo '<a href="?do=op&model=1&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1587] . '" ><span class="bi-bar-chart"></span></a>';
                                }
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='2' and tbl_opinionend_delete='0' ");
                                if (mysqli_num_rows($resultOp) > 0) {
                                    echo '<a href="?do=op&model=2&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1586] . '"><span class="bi-bar-chart-fill"></span></a>';
                                }
                            }
                        }
                        if ($access == '5') { // start if user is mentor
                            echo '<a href="allmessage.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1584] . '(' . toPersianNum(mysqli_num_rows($resultMessageReply)) . ') ----- ' . $lang[1585] . ' (' . toPersianNum(mysqli_num_rows($resultMessage)) . ')"><span class="bi-chat-dots-fill"></span></a>';
                            echo ($access != '1' ? '<a href="archive.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>' : '');
                        }
                        echo ($access == '9' ? '<a href="?do=delete&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '');


                        $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                        $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                        $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                        $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");


                        if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                            echo ($access == '9' ? '<a href="plan-view.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1598] . '"><span class="bi-layout-text-window-reverse"></span></a>' : '');
                        }
                        echo '</td>
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
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')  ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')   ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="col-lg-6">
                                    <form action="contract.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                        <div class="form-group">
                                            <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                            <input type="text" class="form-control go-fld" $read style="width: 200px;float: left;">
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    <?php
                    }
                }


                if (isset($do) && $do == 'list') {
                    ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="?do=search" method="POST" class="search-form" style="display: none;">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <input type="hidden" class="form-control" name="contract" value="<?= $_GET['contract'] ?>">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>

                    <?php
                    if ($access != '9' && $access != '1') {
                        $querry = " and tbl_projectmember_mentor='$userCode' ";
                    } else {
                        $querry = " ";
                    }
                    // start count db result for pagination 

                    $result_for_count = Query("SELECT * From tbl_member Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$getcontract]%' ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination         
                    $result = Query("SELECT * From tbl_member Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$getcontract]%' ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . ' </th>
                                    <th>' . $lang[127] . ' </th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[1583] . '</th>
                                    ' . ($access == '5' ? '<th>' . $lang[1337] . '</th>' : '') . '                                    
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $memberContract = $_GET['contract'];

                        $result_contract = Query("SELECT tbl_contract_codesection From tbl_contract where tbl_contract_id='$memberContract' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        $codeSection = $read_contract['tbl_contract_codesection'];
                        // find bakhsh info
                        $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codeSection' and tbl_categories_lng='$language_default'");
                        $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                        $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read[tbl_member_id]' and tbl_memberquestion_contract='$getcontract' ");
                        if (mysqli_num_rows($resultSelectQuestion) > 0) {
                            $readSelectQuestion = mysqli_fetch_array($resultSelectQuestion);
                            $arzyabi = json_decode($readSelectQuestion['tbl_memberquestion_data'], True);
                            $arzyabi = count($arzyabi);
                            $check = $readSelectQuestion['tbl_memberquestion_check'];
                            $adminCheck = $readSelectQuestion['tbl_memberquestion_admincheck'];
                        } else {
                            $arzyabi = 0;
                            $check = 0;
                            $adminCheck = 0;
                        }
                        echo '<tr>
                                <td class="align-middle p-2">' . ($page != 1 ? (($page - 1) * $show_per_page) + $i : $i) . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="' . (!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_nationalcode']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_mobile']) . '</td>
                               <td class="align-middle p-2">' . $read['tbl_member_updateresume'] . '</td>';
                        if ($access == '5') { // start if user is mentor
                            echo '<td class="align-middle p-2"><form action="" id="formwork">
                                        <select name="status[]" style="width: 100%;"
                                        class="select2 status" multiple="multiple" >';
                            $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                            echo "SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'";
                            while ($read_skill = mysqli_fetch_array($result_skill)) {
                                $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                $read_memberskill = mysqli_fetch_array($result_memberskill);
                                $skillCode = $read_skill['tbl_categories_code'];
                                echo '<option value="' . $read_skill['tbl_categories_code'] . '" ' . (mysqli_num_rows($result_memberskill) > 0 ? (stripos($read_memberskill['tbl_memberskill_data'], "$skillCode") !== false ? 'selected' : '') : '') . '>' . $read_skill['tbl_categories_title'] . '</option>';
                            }
                            echo ' </select>
                                        <input type="hidden" id="userid" name="userid" value="' . $read['tbl_member_id'] . '">
                                        <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                        <input name="submit" type="submit" class="sendstatus btn btn-dark" value="' . $lang[20] . '" class="btn btn-xs btn-secondary">
                                        </form></td>';
                        }

                        $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'  ");
                        $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$getcontract' and tbl_interview_menti='$read[tbl_member_id]' and tbl_interview_delete='0'  ");
                        $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");

                        $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                        $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");


                        $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                        $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");

                        $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");

                        echo '<td class="align-middle p-2">
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588] . '" target="_blank"><span class="bi-file-earmark-person"></span></a>';
                        if ($access == '5') { // start if user is mentor

                            echo '<a href="?do=question&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1589] . ' (' . toPersianNum($arzyabi) . ')" ><span class="bi-patch-question-fill"></span></a>
                                        <a href="?do=answer&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1590] . '  (' . ($check == '0' ? $lang[1591] : ($check == '1' && $adminCheck == 0 ? $lang[1592] : $lang[1593])) . ')" target="_blank"><span class="bi-check-circle-fill"></span></a>
                                        <a href="interview.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1594] . ' (' . toPersianNum(mysqli_num_rows($result_for_interview)) . ')"  ><span class="bi-mic-fill"></span></a>
                                        <a href="plan.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="';
                            if (mysqli_num_rows($resultPlan) == '0') {
                                echo $lang[1595];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1596];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1597];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                echo $lang[1598];
                            } else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                echo $lang[1599];
                            }
                            echo '" ><span class="bi-layout-text-window-reverse"></span></a>';
                            if ($access == '1' && mysqli_num_rows($resultCheckFinal) > 0) {
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='1' and tbl_opinionend_delete='0' ");


                                if (mysqli_num_rows($resultOp) > 0) {
                                    echo '<a href="?do=op&model=1&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1587] . '" ><span class="bi-bar-chart"></span></a>';
                                }
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='2' and tbl_opinionend_delete='0' ");
                                if (mysqli_num_rows($resultOp) > 0) {
                                    echo '<a href="?do=op&model=2&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1586] . '"><span class="bi-bar-chart-fill"></span></a>';
                                }
                            }

                            echo '<a href="allmessage.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1584] . '(' . toPersianNum(mysqli_num_rows($resultMessageReply)) . ') ----- ' . $lang[1585] . ' (' . toPersianNum(mysqli_num_rows($resultMessage)) . ')"><span class="bi-chat-dots-fill"></span></a>';
                            echo ($access != '1' ? '<a href="archive.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>' : '');
                        }

                        echo '</td>
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
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['do']) ? "&do=" . $_GET['do'] : '') . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')  ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['do']) ? "&do=" . $_GET['do'] : '') . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')   ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="col-lg-6">
                                    <form action="faragir.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                        <div class="form-group">
                                            <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                            <input type="text" class="form-control go-fld" name="page_number" style="width: 200px;float: left;">
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    <?php
                    }
                }


                if (isset($do) && $do == 'listall') {
                    ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="?do=search" method="POST" class="search-form">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <input type="hidden" class="form-control" name="contract" value="<?= $_GET['contract'] ?>">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>

                    <?php

                    $querry = " and tbl_projectmember_mentor='$userCode' ";

                    // start count db result for pagination 

                    $result_for_count = Query("SELECT 
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
                        ON p.tbl_projectmember_contract = '$getcontract' 
                        and p.tbl_projectmember_mentor='$userCode'  
                        and p.tbl_projectmember_user= m.tbl_member_id 
                        and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
                        and m.tbl_member_delete='0' ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination         
                    $result = Query("SELECT 
                            p.tbl_projectmember_mentor, 
                            p.tbl_projectmember_contract,
                            p.tbl_projectmember_id,
                            p.tbl_projectmember_mentor ,
                            p.tbl_projectmember_user,
                            p.tbl_projectmember_user AS tbl_projectmember, 
                            m.tbl_member_lastcontract, 
                            m.tbl_member_id,
                            m.tbl_member_delete AS tbl_member
                        FROM
                            tbl_member m
                        INNER JOIN tbl_projectmember p 
                        ON p.tbl_projectmember_contract = '$getcontract' 
                        and p.tbl_projectmember_mentor='$userCode'  
                        and p.tbl_projectmember_user= m.tbl_member_id 
                        and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
                        and m.tbl_member_delete='0' ORDER BY p.tbl_projectmember_id DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . ' </th>
                                    <th>' . $lang[127] . ' </th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[1583] . '</th>
                                    ' . ($access == '5' ? '<th>' . $lang[1337] . '</th>' : '') . '                                    
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $memberContract = $getcontract;
                        //find contract bakhsh
                        $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
                        if (mysqli_num_rows($result_m) > 0) {
                            $read_m = mysqli_fetch_array($result_m);

                            $result_contract = Query("SELECT tbl_contract_codesection From tbl_contract where tbl_contract_id='$memberContract' ");
                            $read_contract = mysqli_fetch_array($result_contract);
                            $codeSection = $read_contract['tbl_contract_codesection'];
                            // find bakhsh info
                            if (!empty($read_m['tbl_member_id'])) {
                                $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codeSection' and tbl_categories_lng='$language_default'");
                                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                                $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read_m[tbl_member_id]' and tbl_memberquestion_contract='$getcontract' ");
                                if (mysqli_num_rows($resultSelectQuestion) > 0) {
                                    $readSelectQuestion = mysqli_fetch_array($resultSelectQuestion);
                                    $arzyabi = json_decode($readSelectQuestion['tbl_memberquestion_data'], True);
                                    $arzyabi = count($arzyabi);
                                    $check = $readSelectQuestion['tbl_memberquestion_check'];
                                    $adminCheck = $readSelectQuestion['tbl_memberquestion_admincheck'];
                                } else {
                                    $arzyabi = 0;
                                    $check = 0;
                                    $adminCheck = 0;
                                }
                            } else {
                                $arzyabi = 0;
                                $check = 0;
                                $adminCheck = 0;
                            }

                            echo '<tr>
                                <td class="align-middle p-2">' . ($page != 1 ? (($page - 1) * $show_per_page) + $i : $i) . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="' . (!empty($read_m['tbl_member_id']) ?  (!empty($read_m['tbl_member_profile']) ? '../upload/profile/' . $read_m['tbl_member_profile'] : 'assets/img/no-image.jpg') : '') . '" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '') . '</td>
                                <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? toPersianNum($read_m['tbl_member_nationalcode']) : '') . '</td>
                                <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? toPersianNum($read_m['tbl_member_mobile']) : '') . '</td>
                               <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_updateresume'] : '') . '</td>';
                            if ($access == '5') { // start if user is mentor
                                echo '<td class="align-middle p-2"><form action="" id="formwork">
                                        <select name="status[]" style="width: 100%;"
                                        class="select2 status" multiple="multiple" >';
                                $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                                //echo "SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'";
                                while ($read_skill = mysqli_fetch_array($result_skill)) {
                                    $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                    $read_memberskill = mysqli_fetch_array($result_memberskill);
                                    $skillCode = $read_skill['tbl_categories_code'];
                                    echo '<option value="' . $read_skill['tbl_categories_code'] . '" ' . (mysqli_num_rows($result_memberskill) > 0 ? (stripos($read_memberskill['tbl_memberskill_data'], "$skillCode") !== false ? 'selected' : '') : '') . '>' . $read_skill['tbl_categories_title'] . '</option>';
                                }
                                echo ' </select>
                                        <input type="hidden" id="userid" name="userid" value="' . $read_m['tbl_member_id'] . '">
                                        <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                        <input name="submit" type="submit" class="sendstatus btn btn-dark" value="' . $lang[20] . '" class="btn btn-xs btn-secondary">
                                        </form></td>';
                            }

                            if (!empty($read_m['tbl_member_id'])) {
                                $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'  ");
                                $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_lng='$language_default' and tbl_interview_contract='$getcontract' and tbl_interview_menti='$read_m[tbl_member_id]' and tbl_interview_delete='0'  ");
                                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");

                                $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
                                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");


                                $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read_m[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                                $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read_m[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");

                                $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                            }
                            echo '<td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? '
                                <a href="../fa/resume/' . $read_m['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588] . '" target="_blank"><span class="bi-file-earmark-person"></span></a>' : '');
                            if ($access == '5') { // start if user is mentor
                                if (!empty($read_m['tbl_member_id'])) {
                                    echo '<a href="?do=question&id=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1589] . ' (' . toPersianNum($arzyabi) . ')" ><span class="bi-patch-question-fill"></span></a>
                                        <a href="?do=answer&id=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1590] . '  (' . ($check == '0' ? $lang[1591] : ($check == '1' && $adminCheck == 0 ? $lang[1592] : $lang[1593])) . ')" target="_blank"><span class="bi-check-circle-fill"></span></a>
                                        <a href="interview.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1594] . ' (' . toPersianNum(mysqli_num_rows($result_for_interview)) . ')"  ><span class="bi-mic-fill"></span></a>
                                        <a href="plan.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="';
                                    if (mysqli_num_rows($resultPlan) == '0') {
                                        echo $lang[1595];
                                    } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                        echo $lang[1596];
                                    } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                        echo $lang[1597];
                                    } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                        echo $lang[1598];
                                    } else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                        echo $lang[1599];
                                    }
                                    echo '" ><span class="bi-layout-text-window-reverse"></span></a>';
                                    if ($access == '1' && mysqli_num_rows($resultCheckFinal) > 0) {
                                        $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='1' and tbl_opinionend_delete='0' ");


                                        if (mysqli_num_rows($resultOp) > 0) {
                                            echo '<a href="?do=op&model=1&user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1587] . '" ><span class="bi-bar-chart"></span></a>';
                                        }
                                        $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='2' and tbl_opinionend_delete='0' ");
                                        if (mysqli_num_rows($resultOp) > 0) {
                                            echo '<a href="?do=op&model=2&user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1586] . '"><span class="bi-bar-chart-fill"></span></a>';
                                        }
                                    }
                                }
                                if (!empty($read_m['tbl_member_id'])) {
                                    echo '<a href="allmessage.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1584] . '(' . toPersianNum(mysqli_num_rows($resultMessageReply)) . ') ----- ' . $lang[1585] . ' (' . toPersianNum(mysqli_num_rows($resultMessage)) . ')"><span class="bi-chat-dots-fill"></span></a>';
                                    echo ($access != '1' ? '<a href="archive.php?user=' . $read_m['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>' : '');
                                }
                            }

                            echo '</td>
                                    </tr>';
                            $i++;
                        }
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
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['do']) ? "&do=" . $_GET['do'] : '') . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')  ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['do']) ? "&do=" . $_GET['do'] : '') . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')   ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="col-lg-6">
                                    <form action="faragir.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                        <div class="form-group">
                                            <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                            <input type="text" class="form-control go-fld" name="page_number" style="width: 200px;float: left;">
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                <?php
                    }
                }


                if (isset($do) && $do == 'answer' && $access == '5') {

                    $result = 0;
                    $allQuestion = 0;
                    $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$getcontract' ");
                    if (mysqli_num_rows($resultSelectQuestion) > 0) {
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        echo '<div class="p-3">';
                        if ($read_select['tbl_memberquestion_check'] == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1603] . '</div>';
                        } else {
                            if (!empty($read_select['tbl_memberquestion_dataanswer'])) {
                                $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                                $k = 1;
                                echo (count($data) > 0 ? '<div id="content">
                                <h4 class="mb-0 red">' . $lang[1614] . ' : ' . $fullName . ' , ' . $lang[385] . ' : ' . $nationalCode . '</h4>
                                <hr><h4>' . $lang[2063] . '</h4>' : '');
                                foreach ($data as $keyexam => $valueexam) {
                                    if ($keyexam != 'submit' && $keyexam != 'code') {
                                        $questionId = $keyexam;
                                        $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                        if (mysqli_num_rows($result_question) > 0) {
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_question_answer'], true);
                                            echo '<div class="form-group exam"><h2 class="mb-4">' . toPersianNum($k) . ' . ' . $read_question['tbl_question_title'] . '</h2>';
                                            $allQuestion++;
                                            foreach ($all_value as $key => $value) {
                                                $answervalue = $value["answervalue"];
                                                //var_dump($answervalue);
                                                if ($answervalue == 1 && $valueexam == $key) {
                                                    $result++;
                                                }

                                                echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_question_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                                            }
                                            echo '</div><hr>';
                                            $k++;
                                        }
                                    }
                                }
                                echo (count($data) > 0 ? '</div><button onclick="downloadPDF(' . rand(100, 10000000) . ')" class="btn btn-success">دانلود PDF</button>
                                <button id="saveDoc" class="btn btn-warning">دانلود DOC</button>
                                <hr>' : '');
                                if (!empty($result)) {
                                    $a = @round((($allQuestion - $result) / $allQuestion) * 100);
                                    $b = 100 - $a;
                                    echo '<div class="resultexam">
                                        <span>' . $lang[1604] . ' : ' . $allQuestion . '</span>
                                        <span>' . $lang[1605] . ' : ' . $result . '</span>
                                    </div><br>
                                    <div class="progress-stacked overflow-visible mt-4 rounded-3 mb-4" style="height: 6px;">
                                        <div class="progress" role="progressbar" style="width: ' . $b . '%; height: 6px;" aria-valuenow="' . $b . '" aria-valuemin="0" aria-valuemax="100">
                                        <div class="overflow-visible progress-bar bg-progress-gradient border-end border-white border-2 rounded-end rounded-pill"><span class="mt-n4 text-900 fw-bold">' . $b . '% ' . $lang[2021] . '</span></div>
                                        </div>
                                        <div class="progress" role="progressbar" style="width: ' . $a . '%; height: 6px;" aria-valuenow="' . $a . '" aria-valuemin="0" aria-valuemax="100">
                                        <div class="overflow-visible progress-bar bg-warning border-end border-white border-2"><span class="mt-n4 text-900 fw-bold">' . $a . '% ' . $lang[2020] . '</span></div>
                                        </div>
                                    </div>';
                                }
                            }


                            if (!empty($read_select['tbl_memberquestion_dataanswertext'])) {
                                $data = json_decode($read_select['tbl_memberquestion_dataanswertext'], true);
                                $k = 1;
                                echo '<div id="content">
                                <h4 class="mb-0 red">' . $lang[1614] . ' : ' . $fullName . ' , ' . $lang[385] . ' : ' . $nationalCode . '</h4>
                                <hr>
                                <h4>' . $lang[2062] . '</h4>';
                                foreach ($data as $keyexam => $valueexam) {
                                    if ($keyexam != 'submit' && $keyexam != 'code') {
                                        $questionId = $keyexam;
                                        $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                        if (mysqli_num_rows($result_question) > 0) {
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_question_answer'], true);
                                            echo '<div class="form-group exam"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_question_title'] . '</h2><p>' . toPersianNum($valueexam) . '</p>';
                                            echo '</div><hr>';
                                            $k++;
                                        }
                                    }
                                }
                                echo '</div>    <button onclick="downloadPDF(' . rand(100, 10000000) . ')" class="btn btn-success">دانلود PDF</button>
                                <button id="saveDoc" class="btn btn-warning">دانلود DOC</button><hr>';
                            }


                            if ($read_select['tbl_memberquestion_admincheck'] == '0') {
                                echo '<form action="?do=confrimexam" method="post">
                                        <div class="form-group mb-4">
                                        <label>' . $lang[2064] . '</label>
                                        <input type="text" class="form-control" name="number" value="">
                                        </div>
                                        <div class="form-group mb-4">
                                        <label>' . $lang[1216] . '</label>
                                        <textarea class="form-control height-textarea" name="comment" rows="5"></textarea>
                                        </div>
                                    <input type="hidden" name="user" value="' . $id . '">
                                    <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                                    <input type="submit" name="updateitem" value="' . $lang[1606] . '" class="btn btn-primary">
                                    </form>';
                            } else {
                                echo '<div class="form-group mb-4">
                                <label>' . $lang[2064] . '</label>
                                <input type="text" class="form-control"  value="' . (!empty($read_select['tbl_memberquestion_adminnumber']) ? $read_select['tbl_memberquestion_adminnumber'] : '') . '" readonly>
                                </div>';
                                echo '<div class="form-group mb-4">
                                <label>' . $lang[1216] . '</label>
                                <textarea  class="form-control height-textarea" rows="5" readonly>' . (!empty($read_select['tbl_memberquestion_admincomment']) ? $read_select['tbl_memberquestion_admincomment'] : '') . '</textarea>
                                </div>';
                                echo '<div class="alert alert-primary" role="alert">' . $lang[1607] . '</div>';
                            }
                        }
                        echo '</div>';
                    }
                }
                if (isset($do) && $do == 'confrimexam') {
                    if (isset($_POST['updateitem'])) {
                        $user = $_POST['user'];
                        $number = sqi($_POST['number']);
                        $comment = htmlspecialchars($_POST['comment']);
                        $contract = $_POST['contract'];
                        $date = jdate("Y/m/d : a:h:s");
                        Query("update tbl_memberquestion set tbl_memberquestion_admincomment='$comment',tbl_memberquestion_adminnumber='$number',tbl_memberquestion_admincheck='1',tbl_memberquestion_dateconfirm='$date',tbl_memberquestion_mentorconfirm='$this_user_id' where tbl_memberquestion_userid='$user' and tbl_memberquestion_contract='$contract' ");
                        header("Location: faragir.php?contract=$contract");
                    }
                }
                if (isset($do) && $do == 'question') {

                    $result = Query("SELECT * From tbl_member where tbl_member_delete='0' and tbl_member_id='$id'");
                    if (mysqli_num_rows($result) <= 0) {
                        header("Location: index.php");
                        exit();
                    }
                    $read = mysqli_fetch_array($result);

                    $level = $read['tbl_member_level'];
                    $contract = $_GET['contract'];
                    echo '<div class="col-lg-12 col-12 p-3">
                        <h4>' . $lang[1600] . '</h4>
                        <div class="form-group">
                        <label><input type="checkbox" id="select_all"> ' . $lang[1601] . ' </label>
                        <hr>
                        </div>
                        <form action="?do=updatequestion" method="POST" class="checkboxlist">                    
                        <input type="hidden" name="user" value="' . $id . '">
                        <input type="hidden" name="contract" value="' . $contract . '">';

                    $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$id' and tbl_memberskill_contract='$contract' ");
                    $read_memberskill = mysqli_fetch_array($result_memberskill);

                    if (!empty($read_memberskill['tbl_memberskill_data'])) {
                        $skill = explode(",", $read_memberskill['tbl_memberskill_data']);
                        $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$contract' ");
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        @$array = json_decode($read_select['tbl_memberquestion_data'], true);
                        foreach ($skill as $key => $value) {
                            if (!empty($value)) {
                                $result_skill = Query("select tbl_categories_title from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$value' ");
                                $read_skill = mysqli_fetch_array($result_skill);
                                echo '<div class="form-cat"><h4>' . $read_skill['tbl_categories_title'] . '</h4>';
                                $resultQuestion = Query("SELECT * From tbl_question where tbl_question_skill='$value' and tbl_question_lng='$language_default' and tbl_question_disable='0' ORDER BY tbl_question_order DESC , tbl_question_id DESC ");
                                if (mysqli_num_rows($resultQuestion) > 0) {
                                    while ($readQuestion = mysqli_fetch_array($resultQuestion)) {
                                        $ids = $readQuestion['tbl_question_code'];
                                        echo '<div class="form-group">
                            <label>
                            <input type="checkbox" class="checkrandom" name="question[]" value="' . $readQuestion['tbl_question_code'] . '" ' . (mysqli_num_rows($resultSelectQuestion) > 0 ? (!empty($array[$ids]) ? 'checked' : '') : '') . '> ' . $readQuestion['tbl_question_title'] . ' / ' . ($readQuestion['tbl_question_level'] == 0 ? $lang[1361] : ($readQuestion['tbl_question_level'] == 1 ? $lang[1362] : $lang[1363])) . ($readQuestion['tbl_question_model'] == 2 ? ' / <span style="color: red;">' . $lang[2062] . '</span>' : '') . (!empty($readQuestion['tbl_question_img']) ? '<a href="../upload/question/' . $readQuestion['tbl_question_img'] . '" target="_blank"> [' . $lang[53] . ']</a>' : '') . '</label>
                            </div>';
                                    }
                                }
                            }
                        }
                    }
                    echo '<hr><div class="form-group">
                        <label>' . $lang[1602] . '</label>
                        <input type="text" name="expire" class="form-control datepicker" value="' . @$read_select['tbl_memberquestion_expire'] . '">
                        </div>
                        <div class="form-group">
                        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                        </div>';
                    echo '</form></div>';
                }

                if (isset($do) && $do == 'updatequestion') {
                    if (isset($_POST['updateitem'])) {
                        $userid = $_POST['user'];
                        $contract = $_POST['contract'];
                        $date = jdate("Y/m/d : a:h:s");
                        $question = $_POST['question'];
                        $expire = $_POST['expire'];
                        $all_value = array();
                        foreach ($question as $key => $value) {
                            if (!empty($value)) {
                                $all_value[$value] = '1';
                            }
                        }
                        $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                        $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$userid' and tbl_memberquestion_contract='$contract' ");
                        if (mysqli_num_rows($resultSelectQuestion) > 0) {
                            Query("update tbl_memberquestion set tbl_memberquestion_expire='$expire',tbl_memberquestion_admincheck='0',tbl_memberquestion_check='0',tbl_memberquestion_data='$all_value',tbl_memberquestion_date='$date',tbl_memberquestion_mentor='$this_user_id' where tbl_memberquestion_userid='$userid' and tbl_memberquestion_contract='$contract' ");
                        } else {
                            Query("insert into tbl_memberquestion (tbl_memberquestion_expire,tbl_memberquestion_check,tbl_memberquestion_userid,tbl_memberquestion_contract,tbl_memberquestion_data,tbl_memberquestion_date,tbl_memberquestion_mentor,tbl_memberquestion_admincheck) values('$expire','0','$userid','$contract','$all_value','$date','$this_user_id','0')");
                        }
                        if ($access == '5') {
                            header("Location: faragir.php?do=listall&contract=$contract");
                        } else {
                            header("Location: faragir.php?do=list&contract=$contract");
                        }
                    }
                }

                if (isset($do) && $do == '$read') {
                    if (isset($_POST['$read'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: faragir.php?page=$page_number");
                    }
                }
                // end = pagination
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * From tbl_member where tbl_member_delete='0' and tbl_member_id='$id'");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</h3>
                        <form action="#" method="POST">
                            <input type="hidden" name="id_user" value="' . $id . '" >
                            <input type="hidden" name="contract" value="' . $_GET['contract'] . '" >
                            <div class="form-group margin-top">
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success mt-3">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger mt-3">
                            </form></div>';

                    if (isset($_POST['submit'])) {
                        $id_user = $_POST['id_user'];
                        $contract = $_POST['contract'];
                        if (!empty($contract)) {

                            $result = Query("select * from tbl_member where tbl_member_id ='$id_user'");
                            $read = mysqli_fetch_array($result);
                            $lastcontract = str_replace("[$contract]", "", $read['tbl_member_lastcontract']);
                            Query("update tbl_member set tbl_member_lastcontract='$lastcontract' Where tbl_member_id ='$id_user' ");
                            Query("update tbl_projectmember set tbl_projectmember_status='1' Where tbl_projectmember_user ='$id_user' and tbl_projectmember_contract='$contract' ");
                        } else {
                            Query("update tbl_member set tbl_member_delete='1' Where tbl_member_id ='$id_user' ");
                        }

                        $_SESSION['error'] = $lang[28];
                        header("Location: faragir.php?contract=$contract");
                        exit();
                    }
                    if (isset($_POST['nodelete'])) {
                        $contract = $_POST['contract'];
                        header("Location: faragir.php?contract=$contract");
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        $contract = $_POST['contract'];
                        header("Location: faragir.php?keyword=$keyword_search&contract=$contract");
                    }
                }

                if (isset($do) && $do == 'op') {




                    $allQuestion = 0;
                    $result = 0;
                    $resultSelectQuestion = Query("SELECT * From tbl_opinionend where tbl_opinionend_delete='0' and tbl_opinionend_model='$_GET[model]' and tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_contract='$getcontract' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);

                    $data = json_decode($read_select['tbl_opinionend_data'], true);
                    $k = 1;
                    foreach ($data as $keyexam => $valueexam) {
                        if ($keyexam != 'submit' && $keyexam != 'code') {
                            $questionId = explode("-", $keyexam);
                            $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId[1]' ");
                            $read_question = mysqli_fetch_array($result_question);
                            $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                            echo '<div class="form-group exam p-3"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_questionpublic_title'] . '</h2>';
                            $allQuestion++;
                            foreach ($all_value as $key => $value) {
                                //var_dump($value);
                                $answervalue = $value["answervalue"];
                                if ($answervalue == 1 && $valueexam == $key) {
                                    $result++;
                                }
                                echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_questionpublic_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_questionpublic_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                            }
                            echo '</div><hr>';
                            $k++;
                        }
                    }
                    echo '<div class="resultexam m-3">
                                <span>' . $lang[1604] . ' : ' . $allQuestion . '</span>
                                <span>' . $lang[1605] . ' : ' . $result . '</span>
                                        </div><br>';
                }


                ?>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>