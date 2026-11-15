<?php

// include
include './includer.php';
$show_per_page = '1000';
$Tabpage = '90';
if (!isset($do)) {
    $pagetitle = $lang[1451];
} else if (isset($do) && $do == 'question') {
    $pagetitle = $lang[1589];
} else if (isset($do) && $do == 'answer') {
    $pagetitle = $lang[1608];
} else if (isset($do) && $do == 'interview') {
    $pagetitle = $lang[1594];
} else if (isset($do) && $do == 'report') {
    $pagetitle = $lang[1615];
} else if (isset($do) && $do == 'op') {
    $pagetitle = $lang[1491];
} else {
    $pagetitle = $lang[1451];
}
$thisaccess = '1,7';
include './header.inc.php';

$select = '';
if (!empty($_GET['user'])) {
    $select = " and tbl_member_id= '$_GET[user]'  ";
}

echo '<div class="modal fade" id="result" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . $lang[1760] . '</button>
        </div>
      </div>
    </div>
  </div>';
if (!empty($_GET['contract'])) {
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$_GET[contract]]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

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
            <h5 class="fs-0">' . $lang[1566] . '  : ' . $read_contract['tbl_contract_manager'] . '</h5>
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

    $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
    $read = mysqli_fetch_array($result);
    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1614] . '  : ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</h5>
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


if (isset($do) && ($do == 'op' || $do == 'interview' || $do == 'report')) {
    $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
    $read_member = mysqli_fetch_array($result_member);

    echo '<div class="col-md-12 mb-3">
<div class="box box-body ">
    <div class="card mb-3">
        <div class="card-body custom-h5">
        <div class="row justify-content-between align-items-center">
            <div class="col">
            <div class="d-flex">
                <div class="flex-1 fs--1">
                <h5 class="fs-0">' . $lang[1614] . '  : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</h5>
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

                        </td>
                        </tr>
                    </table>

                    <?php
                    // start count db result for pagination 
                    $result_for_count = Query("SELECT * From tbl_member  $thiscontract  and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%' ) $select ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination         
                    $result = Query("SELECT * From tbl_member $thiscontract and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%') $select ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                    <th>' . $lang[1787] . '</th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[1337] . '</th>
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
                        $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read[tbl_member_id]' and tbl_memberquestion_contract='$_GET[contract]' ");
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
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="' . (!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_lastlogin']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_nationalcode']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_mobile']) . '</td>
                                <td class="align-middle p-2 label-section">';
                        $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                        while ($read_skill = mysqli_fetch_array($result_skill)) {
                            $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
                            $read_memberskill = mysqli_fetch_array($result_memberskill);
                            $skillCode = $read_skill['tbl_categories_code'];
                            if (!empty($read_memberskill['tbl_memberskill_data'])) {
                                $data = explode(",", $read_memberskill['tbl_memberskill_data']);

                                foreach ($data as $key => $value) {
                                    if (!empty($value) && $skillCode == $value) {
                                        echo '<label><a href="#" class="timelinecontent" data-user="' . $read['tbl_member_id'] . '" data-contract="' . $getcontract . '" data-skill="' . $skillCode . '" data-lng="' . $language_default . '">' . $read_skill['tbl_categories_title'] . '</a></label>';
                                    }
                                }
                            }
                        }
                        echo '';
                        $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'  ");
                        $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$read[tbl_member_id]' and tbl_interview_delete='0'  ");
                        $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");

                        $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                        $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");


                        $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                        $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");

                        $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");

                        echo '</td>
                                <td class="align-middle p-2">
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588] . '" target="_blank"><span class="bi-file-earmark-person"></span></a>
                                <a href="?do=answer&id=' . $read['tbl_member_id'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1766] . '" ><span class="bi-card-checklist"></span></a>
                                <a href="?do=interview&user=' . $read['tbl_member_id'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1594] . '"  ><span class="bi-mic-fill"></span></a>';
                        if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                            echo '<a href="plan-view.php?user=' . $read['tbl_member_id'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2066] . '"  ><span class="bi-layout-text-window-reverse"></span></a>';
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
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')   ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')  ?>"><?= $lang[74] ?></a>
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

                if (isset($do) && $do == 'interview') {

                    $result = Query("SELECT * From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'   ORDER BY tbl_interview_id DESC limit 0,1");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>' . $lang[1617] . '</th>
                                    <th>' . $lang[1618] . '</th>
                                    <th>' . $lang[1619] . '</th>
                                    <th>' . $lang[1620] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $mentor = $read['tbl_interview_mentor'];
                        $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                        $readMentor = mysqli_fetch_array($resultMentor);
                        if (!empty($read['tbl_interview_rank']) && $read['tbl_interview_rank'] != '0') {
                            $rank = $read['tbl_interview_rank'];
                        } else {
                            $rank = $lang[1616];
                        }
                        echo '<tr >
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2" style="display:none;">
                            <input type="hidden" name="code[]" value="' . $read['tbl_interview_code'] . '">
                            <input type="text" name="o' . $read['tbl_interview_code'] . '" value="' . $read['tbl_interview_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                            </td>
                            <td class="align-middle p-2">' . $read['tbl_interview_title'] . '</td>
                            <td class="align-middle p-2">' . toPersianNum($read['tbl_interview_date']) . '</td>
                            <td class="align-middle p-2">' . toPersianNum($read['tbl_interview_viewdate']) . '</td>
                            <td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '</div>
                            <td class="align-middle p-2">' . $rank . '</div></td>
                            
                            <td class="align-middle p-2">
                            <a href="?do=report&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1615] . '"><span class="bi-list-check"></span></a></td>
                            </tr>';
                        $i++;
                    }
                    echo '</tbody></table>';
                }

                if (isset($do) && $do == 'report') {

                    $result = Query("SELECT * From tbl_interview where tbl_interview_code='$code' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0' ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[1527] . '</th>
                                    <th>' . $lang[1623] . '</th>
                                    <th>' . $lang[1624] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {

                        echo '<tr >
                            <td class="align-middle p-2">' . $read['tbl_interview_reporttext'] . '</td>
                            <td class="align-middle p-2">' . toPersianNum($read['tbl_interview_reportdate']) . '</td>
                            <td class="align-middle p-2">' . @$read['tbl_interview_rank'] . '</div>
                            </tr>';
                        $i++;
                    }
                    echo '</tbody></table>';
                }

                if (isset($do) && $do == 'answer') {

                    $result = 0;
                    $allQuestion = 0;
                    $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$_GET[contract]' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);
                    echo '<div class="p-3">';
                    if ($read_select['tbl_memberquestion_check'] == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1603] . '</div>';
                    } else {
                        if (!empty($read_select['tbl_memberquestion_dataanswer'])) {
                            $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                            $k = 1;
                            echo (count($data) > 0 ? '<div id="content">
                            <h4>' . $lang[2063] . '</h4>' : '');
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
                            echo (count($data) > 0 ? '</div>' : '');
                            if (!empty($result)) {
                                $a = @round((($allQuestion - $result) / $allQuestion) * 100);
                                $b = 100 - $a;
                                echo '<div class="resultexam">
                                    <span>' . $lang[1604] . ' : ' . $allQuestion . '</span>
                                    <span>' . $lang[1605] . ' : ' . $result . '</span>
                                </div>';
                            }
                        }


                        if (!empty($read_select['tbl_memberquestion_dataanswertext'])) {
                            $data = json_decode($read_select['tbl_memberquestion_dataanswertext'], true);
                            $k = 1;
                            echo '<div id="content">
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
                            echo '</div>
                            <div class="resultexam">
                                        <span>' . $lang[2064] . ' : ' . (!empty($read_select['tbl_memberquestion_adminnumber']) ? $read_select['tbl_memberquestion_adminnumber'] : '') . '</span>
                                    </div>';
                        }

                        // if ($read_select['tbl_memberquestion_admincheck'] == '0') {
                        //     echo '<form action="?do=confrimexam" method="post">
                        //                     <input type="hidden" name="user" value="' . $id . '">
                        //                     <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        //                     <input type="submit" name="updateitem" value="' . $lang[1606] . '" class="btn btn-primary">
                        //             </form>';
                        // } else {
                        //     echo '<div class="alert alert-primary" role="alert">' . $lang[1607] . '</div>';
                        // }
                    }
                    echo '</div>';
                }
                if (isset($do) && $do == 'confrimexam') {
                    if (isset($_POST['updateitem'])) {
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        $date = jdate("Y/m/d : a:h:s");
                        Query("update tbl_memberquestion set tbl_memberquestion_admincheck='1',tbl_memberquestion_dateconfirm='$date',tbl_memberquestion_mentorconfirm='$this_user_id' where tbl_memberquestion_userid='$user' and tbl_memberquestion_contract='$contract' ");
                        header("Location: faragir.php?contract=$contract");
                    }
                }
                if (isset($do) && $do == 'question') {

                    $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
                    $read = mysqli_fetch_array($result);

                    $level = $read['tbl_member_level'];
                    $contract = $_GET['contract'];
                    echo '<div class="col-lg-12 col-12 p-3">
                        <h1>' . $lang[1600] . '</h1>
                        <div class="form-group">
                        <label><input type="checkbox" id="select_all"> ' . $lang[1601] . ' </label>
                        <hr>
                        </div>
                        <form action="?do=updatequestion" method="POST" class="checkboxlist">                    
                        <input type="hidden" name="user" value="' . $id . '">
                        <input type="hidden" name="contract" value="' . $contract . '">';

                    $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$id' and tbl_memberskill_contract='$contract' ");
                    $read_memberskill = mysqli_fetch_array($result_memberskill);
                    $skill = explode(",", $read_memberskill['tbl_memberskill_data']);
                    $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$contract' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);
                    @$array = json_decode($read_select['tbl_memberquestion_data'], true);
                    foreach ($skill as $key => $value) {
                        if (!empty($value)) {
                            $result_skill = Query("select tbl_categories_title from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$value' ");
                            $read_skill = mysqli_fetch_array($result_skill);
                            echo '<div class="form-cat"><h3>' . $read_skill['tbl_categories_title'] . '</h3>';
                            $resultQuestion = Query("SELECT * From tbl_question where tbl_question_skill='$value' and tbl_question_lng='$language_default' and tbl_question_disable='0' ORDER BY tbl_question_order DESC , tbl_question_id DESC ");
                            if (mysqli_num_rows($resultQuestion) > 0) {
                                while ($readQuestion = mysqli_fetch_array($resultQuestion)) {
                                    $ids = $readQuestion['tbl_question_code'];
                                    echo '<div class="form-group">
                            <label><input type="checkbox" class="checkrandom" name="question[]" value="' . $readQuestion['tbl_question_code'] . '" ' . (mysqli_num_rows($resultSelectQuestion) > 0 ? (!empty($array[$ids]) ? 'checked' : '') : '') . '> ' . $readQuestion['tbl_question_title'] . ' / ' . ($readQuestion['tbl_question_level'] == 0 ? $lang[1361] : ($readQuestion['tbl_question_level'] == 1 ? $lang[1362] : $lang[1363])) . (!empty($readQuestion['tbl_question_img']) ? '<a href="../upload/question/' . $readQuestion['tbl_question_img'] . '" target="_blank"> [' . $lang[53] . ']</a>' : '') . '</label>
                            </div>';
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
                        header("Location: faragir.php?contract=$contract");
                    }
                }

                if (isset($do) && $do == 'gotopage') {
                    if (isset($_POST['gotopage'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: faragir.php?page=$page_number");
                    }
                }
                // end = pagination
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
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
                        Query("update tbl_member set tbl_member_delete='1' Where tbl_member_id ='$id_user' ");
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
                    $resultSelectQuestion = Query("SELECT * From tbl_opinionend where tbl_opinionend_delete='0' and tbl_opinionend_model='$_GET[model]' and tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_contract='$_GET[contract]' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);

                    $data = json_decode($read_select['tbl_opinionend_data'], true);
                    $k = 1;
                    foreach ($data as $keyexam => $valueexam) {
                        if ($keyexam != 'submit' && $keyexam != 'code') {
                            $questionId = $keyexam;
                            $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId' ");
                            $read_question = mysqli_fetch_array($result_question);
                            $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                            echo '<div class="form-group exam p-3"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_questionpublic_title'] . '</h2>';
                            $allQuestion++;
                            foreach ($all_value as $key => $value) {
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