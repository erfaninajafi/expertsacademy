<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
if(!isset($do)) {
$pagetitle = $lang[1451];
}
else if(isset($do) && $do=='question') {
    $pagetitle = $lang[1589];
}
else if(isset($do) && $do=='answer') {
    $pagetitle = $lang[1608];
}
else if(isset($do) && $do=='op') {
    $pagetitle = $lang[1491];
}
else {
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
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_project['tbl_user_name'] . '</h5>
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
} else {
    $thiscontract = " where tbl_member_delete='0'";
}


?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(!isset($do) && $access == '9' ? '
                            <div class="plus">
                            <a href="contract.php?do=import&id=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1609] . '"><span class="bi-person-lines-fill"></span></a>
                            <a href="contract.php?do=importother&id=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1610] . '"><span class="bi-person-plus-fill"></span></a></div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>

            <div class="card-body p-0 position-relative">
             
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
                        $result = Query("SELECT * From tbl_contract where tbl_contract_mentor like '%$userCode%' and tbl_contract_id='$getcontract' ");
                        $read =  mysqli_fetch_array($result);
                        $contractId = $read['tbl_contract_id'];
                        $contractIdStr = '['.$contractId.'],';
                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
                        $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
                        $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='0' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
                        $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
                        $resultPlanCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_mentor='$this_user_id' and tbl_memberskill_contract='$contractId' ");
                        $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");
                        $allFaragir = mysqli_num_rows($result_member);
                        $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
                        $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
                        $noPlan = mysqli_num_rows($resultPlan);
                        $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
                        $endPlan =  mysqli_num_rows($resultCheckEndPlan);
                        $thisCustomer = $read['tbl_contract_customer'];


                        if($getmodel == '1')
                        {

                        }
                        else if($getmodel == '2')
                        {
                            $allResult = mysqli_fetch_all($result_member_question_check);
                        }
                        
                        if(count($allResult) == '0')
                        {
                            
                                echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                            
                        }
                        else {
                        foreach ($allResult as $key => $value) {
                            if(!empty($value)){
                        $result = Query("SELECT * From tbl_projectmember where tbl_projectmember_id='$value[0]'   ");
                        
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>'.$lang[53].' </th>
                                    <th>'.$lang[127].' </th>
                                    <th>'.$lang[385].'</th>
                                    <th>'.$lang[378].'</th>
                                    <th>'.$lang[1583].'</th>
                                    '.($access == '5' ? '<th>'.$lang[1337].'</th>' : '' ).'                                    
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;

                            $read = mysqli_fetch_array($result);
                            $memberContract = $getcontract;
                            echo $read['tbl_projectmember_user'];
                            $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
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
                        }
                        else {
                            $arzyabi = 0;
                            $check = 0;
                            $adminCheck = 0;
                        }
                            echo '<tr>
                                <td class="align-middle p-2">' . ($key+1) . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="'. (!empty($read_m['tbl_member_id']) ?  (!empty($read_m['tbl_member_profile']) ? '../upload/profile/' . $read_m['tbl_member_profile'] : 'assets/img/no-image.jpg') : '' ) .'" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '') . '</td>
                                <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? toPersianNum($read_m['tbl_member_nationalcode']) : '') . '</td>
                                <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? toPersianNum($read_m['tbl_member_mobile']) : '') . '</td>
                               <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_updateresume'] : '' ) . '</td>';
                                if ($access == '5'){ // start if user is mentor
                                        echo '<td class="align-middle p-2"><form action="" id="formwork">
                                        <select name="status[]" style="width: 100%;"
                                        class="select2 status" multiple="multiple" >';
                                    $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                                    echo "SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'";
                                    while ($read_skill = mysqli_fetch_array($result_skill)) {
                                        $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                        $reademberskill = mysqli_fetch_array($result_memberskill);
                                        $skillCode = $read_skill['tbl_categories_code'];
                                        echo '<option value="' . $read_skill['tbl_categories_code'] . '" ' . (mysqli_num_rows($result_memberskill) > 0 ? (stripos($reademberskill['tbl_memberskill_data'], "$skillCode") !== false ? 'selected' : '') : '') . '>' . $read_skill['tbl_categories_title'] . '</option>';
                                    }
                                    echo ' </select>
                                        <input type="hidden" id="userid" name="userid" value="' . $read_m['tbl_member_id'] . '">
                                        <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                        <input name="submit" type="submit" class="sendstatus btn btn-dark" value="'.$lang[20].'" class="btn btn-xs btn-secondary">
                                        </form></td>';
                                }

                                if(!empty($read_m['tbl_member_id'])) {
                                $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'  ");
                                $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$getcontract' and tbl_interview_menti='$read_m[tbl_member_id]' and tbl_interview_delete='0'  ");
                                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
    
                                $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
                                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
    
    
                                $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read_m[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                                $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read_m[tbl_member_id]' and  tbl_skillmessage_contract='$getcontract' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");
        
                                $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                            }
                                echo '<td class="align-middle p-2">'.(!empty($read_m['tbl_member_id']) ? '
                                <a href="../fa/resume/' . $read_m['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588]. '" target="_blank"><span class="bi-file-earmark-person"></span></a>' : '');
                                if ($access == '5'){ // start if user is mentor
                                    if (!empty($read_m['tbl_member_id'])) {
                                        echo '<a href="?do=question&id=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1589].' (' . toPersianNum($arzyabi) . ')" ><span class="bi-patch-question-fill"></span></a>
                                        <a href="?do=answer&id=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1590].'  (' . ($check == '0' ? $lang[1591] : ($check == '1' && $adminCheck == 0 ? $lang[1592] : $lang[1593])) . ')" target="_blank"><span class="bi-check-circle-fill"></span></a>
                                        <a href="interview.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1594].' (' . toPersianNum(mysqli_num_rows($result_for_interview)) . ')"  ><span class="bi-mic-fill"></span></a>
                                        <a href="plan.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="';
                                        if (mysqli_num_rows($resultPlan) == '0') {
                                            echo $lang[1595];
                                        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                            echo $lang[1596];
                                        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                            echo $lang[1597];
                                        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                            echo $lang[1598];
                                        }
                                        else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                            echo $lang[1599];
                                        }
                                        echo '" ><span class="bi-layout-text-window-reverse"></span></a>';
                                        if($access == '1' && mysqli_num_rows($resultCheckFinal) > 0){
                                        $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='1' and tbl_opinionend_delete='0' ");
                                    
                                        
                                        if(mysqli_num_rows($resultOp) > 0)
                                        {
                                            echo '<a href="?do=op&model=1&user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1587]. '" ><span class="bi-bar-chart"></span></a>';
                                        }
                                        $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' and tbl_opinionend_model='2' and tbl_opinionend_delete='0' ");
                                        if(mysqli_num_rows($resultOp) > 0)
                                        {
                                            echo '<a href="?do=op&model=2&user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1586] . '"><span class="bi-bar-chart-fill"></span></a>';
                                        }
                                    }
                                    }
                                    if (!empty($read_m['tbl_member_id'])) {
                                    echo '<a href="allmessage.php?user=' . $read_m['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="'.$lang[1584].'('.toPersianNum(mysqli_num_rows($resultMessageReply)).') ----- '.$lang[1585].' ('.toPersianNum(mysqli_num_rows($resultMessage)).')"><span class="bi-chat-dots-fill"></span></a>';
                                    echo ($access != '1' ? '<a href="archive.php?user=' . $read_m['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>' : '' );
                                    }
                            }
                            
                                    echo '</td>
                                    </tr>';
                            }  
                        }
                    }
                        // end show status   
                        echo '</tbody></table>';
                    
             

                    ?>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>