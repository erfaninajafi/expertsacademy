<?php
include './includer.php';
$show_per_page = '500';
$pagetitle = $lang[1658];
$Tabpage = '90';
$thisaccess = '1,5';
include './header.inc.php';


if (!empty($_GET['contract'])) {
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$_GET[contract]]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
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
              <h5 class="fs-0">' . $lang[1440] . '  : <a href="faragir.php?contract=' . $_GET['contract'] . '">' . $read_contract['tbl_contract_code'] . '</a></h5>
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
    $datepickerEnd = $read_contract['tbl_contract_enddate'];

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
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">

                <?php
                if (!isset($do)) {
                    $resultSkillList = Query("select * from tbl_memberskill where tbl_memberskill_user='$_GET[user]' and tbl_memberskill_contract='$_GET[contract]' ");
                    if (mysqli_num_rows($resultSkillList) <= 0) {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1630] . '</div>';
                    } else {
                        $readSkillList = mysqli_fetch_array($resultSkillList);
                        $skillList = explode(",", $readSkillList['tbl_memberskill_data']);
                        $startDate = $readSkillList['tbl_memberskill_startdate'];


                        foreach ($skillList as $key => $value) {

                            if (!empty($value)) {
                                echo '<div class="accordion m-2" id="accordionSkill">';
                                $checkCertificate = Query("select tbl_number_certificate from tbl_number where tbl_number_skill='$value' and tbl_number_contract ='$_GET[contract]' ");
                                if (mysqli_num_rows($checkCertificate) > 0) {
                                    $readCheckCertificate = mysqli_fetch_array($checkCertificate);
                                    $finalConfirmSkill = $readCheckCertificate['tbl_number_certificate'];
                                } else {
                                    $finalConfirmSkill = '0';
                                }

                                $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                                $readSkill = mysqli_fetch_array($resultSkill);
                                $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                                $result_for_confirm = Query("SELECT * From tbl_plan where tbl_plan_status='1' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");

                                echo '<h2 class="accordion-header" id="heading' . $key . '">
                                <button class="accordion-button ' . ($key != '0' ? 'collapsed' : '') . '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' . $key . '" aria-expanded="true" aria-controls="collapse' . $key . '">' . $lang[1337] . ' : ' . $readSkill['tbl_categories_title'] . '</button>
                              </h2>
                              <div class="accordion-collapse collapse ' . ($key == '0' ? 'show' : '') . '" id="collapse' . $key . '" aria-labelledby="heading' . $key . '" data-bs-parent="#accordionSkill">
                                <div class="accordion-body p-1">';

                                echo '<div class="col-12 col-gl-12 col-md-12 p-3"><h5>' . $lang[1337] . ' : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
                                if (empty($do)) {
                                    echo (mysqli_num_rows($result_for_count) > 0 ? '<div class="col-12 col-gl-12 col-md-12 pb-2"><div class="progress-plan"> ' . $lang[1721] . ' : 
                                <div class="progress mb-0 mt-3" style="height:15px" role="progressbar" aria-valuenow="' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: ' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '%">' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '%
                                </div>
                                </div>
                                </div></div>' : '');
                                }

                                echo  '<table><tr><td class="col-sm-9">' . (!empty($readSkillList['tbl_memberskill_datereport']) && $access != 1 ? '' : '<a class="m-3 margin-left" href="?do=add&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '&skill=' . $value . '" ><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>') . '</td>';
                                // start count db result for pagination               
                                $count_result = mysqli_num_rows($result_for_count);
                                $start_contnet =  ($page - 1) * $show_per_page;
                                $page_number = ceil($count_result / $show_per_page);
                                // end count db result for pagination               
                                $result = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_order DESC LIMIT $start_contnet,$show_per_page ");
                                // error : if not find record show no item for view
                                $row = mysqli_num_rows($result);
                                if (!isset($do) && $row == '0') {
                                    //echo '<div class="margin-top">' . $lang[57] . '</div>';
                                }
                                // start show list
                                echo '<form action="?do=runupdate" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="user" value="' . $_GET['user'] . '">
                                <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                                <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1336] . '</th>
                                    <th>' . $lang[1631] . '</th>
                                    <th>' . $lang[20] . '</th>
                                    <th>' . $lang[1602] . '</th>
                                    <th>' . $lang[1340] . '</th>
                                    <th>' . $lang[1619] . '</th>
                                    <th>' . $lang[63] . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                $k = 1;
                                $finalPrice = 0;
                                while ($read = mysqli_fetch_array($result)) {

                                    $mentor = $read['tbl_plan_mentor'];
                                    $coach = $read['tbl_plan_coach'];
                                    $file = $read['tbl_plan_file'];

                                    $question = (!empty($read['tbl_plan_question']) ? count(json_decode($read['tbl_plan_question'], true)) : '0');
                                    $resultFile = Query("SELECT * From tbl_mohtava Where tbl_mohtava_code = '$file' and tbl_mohtava_lng='$language_default' and tbl_mohtava_delete = '0' ");
                                    $readFile = mysqli_fetch_array($resultFile);

                                    $resultCoach = Query("SELECT * From tbl_user Where tbl_user_id = '$coach' ");
                                    $readCoach = mysqli_fetch_array($resultCoach);
                                    $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                                    $readMentor = mysqli_fetch_array($resultMentor);
                                    if ($read['tbl_plan_model'] == '1230') {
                                        $class = $read['tbl_plan_class'];
                                        $resultClass = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_code ='$class' ");
                                        $readClass = mysqli_fetch_array($resultClass);

                                        // course price   

                                        $result_course_parent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_lng ='$language_default' and  tbl_courseparent_class ='$class' ");

                                        while ($read_course_parent = mysqli_fetch_array($result_course_parent)) {
                                            $result_course_price = Query("SELECT tbl_course_mohtava From tbl_course where tbl_course_lng ='$language_default' and  tbl_course_class ='$read_course_parent[tbl_courseparent_code]' ");
                                            while ($read_course_price = mysqli_fetch_array($result_course_price)) {
                                                if (!empty($read_course_price['tbl_course_mohtava'])) {
                                                    $mohtava = explode(",", $read_course_price['tbl_course_mohtava']);
                                                    foreach ($mohtava as $key => $value) {
                                                        if (!empty($value)) {
                                                            $result_mohtava = Query("select tbl_mohtava_sellprice from tbl_mohtava where tbl_mohtava_code = '$value' ");
                                                            $read_mohtava = mysqli_fetch_array($result_mohtava);
                                                            //echo $read_mohtava['tbl_mohtava_sellprice'];
                                                            $finalPrice += (!empty($read_mohtava['tbl_mohtava_sellprice']) ? $read_mohtava['tbl_mohtava_sellprice'] : '0');
                                                        }
                                                    }
                                                }
                                            }
                                        }


                                        $result_course_parent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_lng ='$language_default' and  tbl_courseparent_class ='$class' ORDER BY tbl_courseparent_order DESC ,tbl_courseparent_id DESC limit 0,1");
                                        $read_course_parent = mysqli_fetch_array($result_course_parent);


                                        @$resultcourse = Query("SELECT tbl_course_code From tbl_course where tbl_course_lng ='$language_default' and tbl_course_class='$read_course_parent[tbl_courseparent_code]'   ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                                        $readcourse = mysqli_fetch_array($resultcourse);
                                    }


                                    $resultSkillMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_plan='$read[tbl_plan_code]' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_skill='$read[tbl_plan_skill]' and tbl_skillmessage_user='$_GET[user]'  and tbl_skillmessage_delete ='0'  ");

                                    echo '<tr ' . ($read['tbl_plan_status'] == '1' ? 'class="confirm-row"' : (!empty($read['tbl_plan_dateenglish']) && $read['tbl_plan_dateenglish'] < date("Y/m/d") ? 'class="expire-rows"' : '')) . '>
                                    <td class="align-middle p-2">' . $k . '</td>
                                    <td class="align-middle p-2">
                                        <input type="hidden" name="code[]" value="' . $read['tbl_plan_code'] . '">
                                        <input type="text" name="o' . $read['tbl_plan_code'] . '" value="' . $read['tbl_plan_order'] . '" maxlength="5" class="order-custom">
                                    </td>
                                    <td class="align-middle p-2">' . ($read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233' || $read['tbl_plan_model'] == '1234' ? '<a href="' . $read['tbl_plan_link'] . '" target="_blank">' . PlanModel($read['tbl_plan_model']) . '</a>' : PlanModel($read['tbl_plan_model']));
                                    if ($read['tbl_plan_model'] == '1230' && mysqli_num_rows($resultcourse) > 0 && mysqli_num_rows($resultClass) > 0) {
                                        echo '<br><br><span class="orange"><a href="courseparent.php?code=' . $read['tbl_plan_class'] . '">' . $lang[710] . ' : ' . $readClass['tbl_class_title'] . '</a></span>';
                                        @$result_parent = Query("SELECT tbl_course_class From tbl_course where tbl_course_code ='$readcourse[tbl_course_code]' and tbl_course_lng='$language_default' ");
                                        //echo mysqli_num_rows($result_parent);
                                        if (mysqli_num_rows($result_parent) > 0) {
                                            $read_parent = mysqli_fetch_array($result_parent);

                                            $thisParent = $read_parent['tbl_course_class'];
                                            $result_class = Query("SELECT tbl_courseparent_class From tbl_courseparent where  tbl_courseparent_lng='$language_default' and tbl_courseparent_code ='$thisParent' ");
                                            $read_class = mysqli_fetch_array($result_class);
                                            $thisParentClass = $read_class['tbl_courseparent_class'];
                                            $read_lesson = 0;
                                            $all_lesson = 0;
                                            $resultParent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_class='$thisParentClass' and tbl_courseparent_lng ='$language_default' ORDER BY tbl_courseparent_order DESC , tbl_courseparent_id DESC ");
                                            while ($readParent = mysqli_fetch_array($resultParent)) {
                                                $resultcourseclass = Query("SELECT * From tbl_course where tbl_course_class='$readParent[tbl_courseparent_code]' and tbl_course_lng ='$language_default'  ");
                                                while ($readcourseclass = mysqli_fetch_array($resultcourseclass)) {
                                                    $all_lesson += 1;
                                                    $resultview_count = Query("SELECT * From tbl_view where tbl_view_class='$readcourseclass[tbl_course_code]' and tbl_view_user ='$_GET[user]'  ");
                                                    if (mysqli_num_rows($resultview_count) > 0) {
                                                        $read_lesson += 1;
                                                    }
                                                }
                                            }
                                        } else {
                                            $thisParentClass = '';
                                            $read_lesson = 0;
                                            $all_lesson = 0;
                                        }
                                        echo '<div class="skills padding-right-clear">
                                        <div class="d-flex align-items-center">
                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-pill" style="width: ' . ($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100) - 100))) . '%;"></div>
                                        </div>
                                        <div class="fw-semi-bold ms-2">' . ($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100) - 100))) . '%</div>
                                    </div>
                                            </div>';
                                    }
                                    echo ($read['tbl_plan_model'] == '1235' ? '<br><br><span class="orange">' . $lang[1213] . '  : ' . $readCoach['tbl_user_name'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1236' ? '<br><br><span class="orange">' . $lang[1632] . ' : ' . $read['tbl_plan_azmoon'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1237' ? '<br><br><span class="orange">' . $read['tbl_plan_coach'] . $lang[1633] . ' : ' . toPersianNum($question) . '<br>' . ($read['tbl_plan_checkexam'] == 0 ? $lang[1634] : $lang[1635] . ' ' . $read['tbl_plan_dateexam']) . '</span>' : '');
                                    if ($read['tbl_plan_model'] == '1231') {
                                        if (mysqli_num_rows($resultFile) > 0) {
                                            $files = (!empty($readFile['tbl_mohtava_file']) ? explode(",", $readFile['tbl_mohtava_file']) : '');
                                            echo '<br><br><span class="orange">' . $lang[52] . ' : <a href="upload/' . (!empty($readFile['tbl_mohtava_foldername']) ? 'uploadcourse/' . $readFile['tbl_mohtava_foldername'] . '/' . $files[0] : 'upload/course/' . $files[0]) . '" target="_blank">' . @$readFile['tbl_mohtava_title'] . '</a></span>';
                                            echo '<br><span class="orange"><a href="shenasname.php?do=view&code=' . $file . '" target="_blank">' . $lang[2027] . '</a></span>';
                                            $finalPrice += (!empty($readFile['tbl_mohtava_sellprice']) ? $readFile['tbl_mohtava_sellprice'] : 0);
                                        }
                                    }
                                    echo '</td>
                                        <td class="align-middle p-2">' . (!empty($read['tbl_plan_editdate']) ? toPersianNum($read['tbl_plan_editdate']) : '') . '</td>

                                        <td class="align-middle p-2">
                                    ' . ($read['tbl_plan_model'] == '1230' || $read['tbl_plan_model'] == '1231' || $read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                        ($read['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                        ($read['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                        ($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                        toPersianNum($read['tbl_plan_expire']) . '</td>

                                    <td class="align-middle p-2">
                                        <div class="form-group">
                                        <label><input class="plan" type="radio" name="status' . $read['tbl_plan_code'] . '" value="0" ' . ($read['tbl_plan_status'] == 0 ? 'checked' : '') . '> ' . $lang[1556] . '</label>
                                        <label><input  class="plan" type="radio" name="status' . $read['tbl_plan_code'] . '" value="1" ' . ($read['tbl_plan_status'] == 1 ? 'checked' : '') . '> ' . $lang[1557] . '</label>
                                        </div>
                                        <div class="form-group">
                                        <textarea rows="2" class="form-control" name="itemmsg' . $read['tbl_plan_code'] . '" placeholder="' . $lang[1527] . '">' . $read['tbl_plan_itemmsg'] . '</textarea>
                                        </div>';
                                    if ($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237') {
                                        echo '<div class="form-group"><label>' . $lang[1638] . '</label>
                                        <select class="form-control select2" name="rank' . $read['tbl_plan_code'] . '" placeholder="' . $lang[1638] . '">
                                        <option value="0" ' . ($read['tbl_plan_rank'] == NULL || $read['tbl_plan_rank'] == '0' ? 'selected' : '') . '>' . $lang[1638] . '</option>';
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo '<option value="' . $i . '" ' . ($read['tbl_plan_rank'] != NULL && $read['tbl_plan_rank'] == $i ? 'selected' : '') . '>' . $i . '</option>';
                                        }
                                        echo ' </select>
                                        </div>';
                                        if ($read['tbl_plan_model'] == '1236') {
                                            echo '<div class="form-group"><label>' . $lang[1639] . '</label>
                                        <select class="form-control select2" name="vazn' . $read['tbl_plan_code'] . '" placeholder="' . $lang[1640] . ' ">
                                        <option value="0" ' . ($read['tbl_plan_vazn'] == NULL || $read['tbl_plan_vazn'] == '0' ? 'selected' : '') . '>' . $lang[1639] . '</option>';
                                            for ($i = 1; $i <= 100; $i++) {
                                                echo '<option value="' . $i . '" ' . ($read['tbl_plan_vazn'] != NULL && $read['tbl_plan_vazn'] == $i ? 'selected' : '') . '>' . $i . '</option>';
                                            }
                                            echo '</select></div>';
                                        }
                                    }
                                    echo '</td>';

                                    if ($read['tbl_plan_model'] == '1230') {
                                        $resultCheckTeacher = Query("select * from tbl_opinionend where tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_class='$thisParentClass' and tbl_opinionend_model='3' and tbl_opinionend_delete='0' ");
                                    }
                                    echo '<td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '<br>' . $lang[1641] . ' :' . toPersianNum($read['tbl_plan_adddate']) . '</td>
                                    
                                    <td class="align-middle p-2">
                                        ' . (!empty($readSkillList['tbl_memberskill_datereport']) && $access != 1 ? '' : '<a href="?do=edit&code=' . $read['tbl_plan_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '&skill=' . $value . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                        ' . (empty($startDate) ? '<a href="?do=delete&code=' . $read['tbl_plan_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '')) . '
                                        <a href="skillmessage.php?skill=' . $read['tbl_plan_skill'] . '&plan=' . $read['tbl_plan_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[822] . '(' . mysqli_num_rows($resultSkillMessage) . ')" target="_blank"><span class="bi-chat-dots-fill"></span></a>
                                        ' . ($read['tbl_plan_model'] == '1235' ? '<a href="interviewcoach.php?user=' . $_GET['user'] . '&skill=' . $value . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1642] . '"  target="_blank"><span class="bi-map"></span></a>' : '') . '
                                        ' . ($read['tbl_plan_model'] == '1237' && $read['tbl_plan_checkexam'] == 1 ? '<a href="?do=answer&user=' . $_GET['user'] . '&skill=' . $value . '&code=' . $read['tbl_plan_code'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1643] . ' ' . ($read['tbl_plan_admincheck'] == 1 ? $lang[1644] : $lang[1645]) . '"  target="_blank"><span class="bi-list-check"></span></a>' : '') . '
                                        ' . ($read['tbl_plan_model'] == '1230' ? '<a href="exam.php?class=' . $thisParentClass . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1812] . '"  target="_blank"><span class="bi-list-check"></span></a>' : '') . '
                                        
                                        ' . ($read['tbl_plan_model'] == '1230' && mysqli_num_rows($resultCheckTeacher) > 0 ? '<a href="opteacher.php?class=' . $thisParentClass . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1816] . '"  target="_blank"><span class="bi-bar-chart-line-fill"></span></a>' : '') . '

                                        </td>
                                    </tr>';

                                    $k++;
                                }
                                //echo '<tr class="dark-price"><td colspan="7">'.$lang[1416].'</td><td>'.(!empty($finalPrice) ? toPersianNum(number_format($finalPrice)) : toPersianNum('0')).'</td></tr>';
                                echo '</tbody></table><div class="container p-3"><div class="row"><input type="hidden" name="skill" value="' . $value . '">';


                                $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1236' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                @$firstRank = $readNumberModel['tbl_plan_rank'];
                                @$firstVazn = $readNumberModel['tbl_plan_vazn'];
                                $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1237' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                @$secondRank = $readNumberModel['tbl_plan_rank'];
                                //@$secondVazn = $readNumberModel['tbl_plan_vazn'];
                                // mokamel first vazn
                                @$secondVazn = 100 - $firstVazn;

                                if (!empty($firstRank) && !empty($secondRank)) {
                                    $resultCheckNumber = Query("select * from tbl_number where tbl_number_user='$_GET[user]' and tbl_number_contract='$_GET[contract]' and tbl_number_skill='$value' ");

                                    if (mysqli_num_rows($resultCheckNumber) > 0) {
                                        $readCheckNumber = mysqli_fetch_array($resultCheckNumber);
                                        $shayestegi = $readCheckNumber['tbl_number_shayestegi'];
                                        $emtehan = $readCheckNumber['tbl_number_emtehan'];

                                        echo '<div class="col-12 col-lg-4 col-md-12 p-3">
                                    <div class="form-group"><label>' . $lang[1651] . '</label><input type="text" class="form-control" value="' . $readCheckNumber['tbl_number_rank'] . '" readonly></div>
                                    <div class="form-group"><label>' . $lang[1652] . '</label><input type="text" class="form-control datepickerall" name="datecertificate" value="' . $readCheckNumber['tbl_number_datecertificate'] . '"></div>
                                    <div class="form-group"><label>' . $lang[1808] . '</label><input type="text" class="form-control" name="numbercertificate" value="' . $readCheckNumber['tbl_number_numbercertificate'] . '"></div>
                                    <div class="form-group"><label>' . $lang[1415] . (!empty($readCheckNumber['tbl_number_filecertificate']) ? '<a href="../upload/cer/' . $readCheckNumber['tbl_number_filecertificate'] . '" target="_blank">[' . $lang[98] . ']</a>' : '') . '</label><input type="hidden" value="' . $readCheckNumber['tbl_number_filecertificate'] . '" class="form-control" name="old_filecertificate" ><input type="file" class="form-control" name="filecertificate" ></div>
                                    <div class="form-group"><label>' . $lang[1807] . (!empty($readCheckNumber['tbl_number_filecertificate2']) ? '<a href="../upload/cer/' . $readCheckNumber['tbl_number_filecertificate2'] . '" target="_blank">[' . $lang[98] . ']</a>' : '') . '</label><input type="hidden" value="' . $readCheckNumber['tbl_number_filecertificate2'] . '" class="form-control" name="old_filecertificate2" ><input type="file" class="form-control" name="filecertificate2" ></div>
                                    </div>';

                                        echo '<div class="col-12 col-lg-4 col-md-12 p-3">
                                    <div class="form-group">
                                    <label>' . $lang[1646] . '</label>
                                    <select class="form-control select2" name="shayestegi" >
                                    <option value="0" ' . ($shayestegi == '0' ? 'selected' : '') . '> ' . $lang[1647] . '</option>';
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo '<option value="' . $i . '" ' . ($shayestegi == $i ? 'selected' : '') . '>' . $i . '</option>';
                                        }
                                        echo '</select></div>';

                                        echo '<div class="form-group">
                                    <label>' . $lang[1648] . '</label>
                                    <select class="form-control select2" name="emtehan" >
                                    <option value="0" ' . ($emtehan == '0' ? 'selected' : '') . '> ' . $lang[1649] . '</option>';
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo '<option value="' . $i . '" ' . ($emtehan == $i ? 'selected' : '') . '>' . $i . '</option>';
                                        }
                                        echo '</select></div>';
                                        echo '<div class="form-group"><label>' . $lang[1809] . '</label>
                                    <textarea rows="5" class="form-control" name="textcertificate">' . (!empty($readCheckNumber['tbl_number_textcertificate']) ? $readCheckNumber['tbl_number_textcertificate'] : 'گواهی می شود خانم / آقای [name] فرزند [father] دارنده شماره ملی [national] صادره از [sadere] شاغل در [company] دوره مهارت آموزی [skill] را که از تاریخ [start] لغایت تاریخ [end] برگزار گردید ، با موفقیت و کسب امتیاز به پایان رسانیده است.') . '</textarea></div>';

                                        echo '</div></div>';
                                    } else {
                                        echo '<div class="col-12 col-lg-4 col-md-12 p-3"><div class="form-group">
                                    <label>' . $lang[1653] . '</label>
                                    <select class="form-control select2" name="shayestegi" >
                                    <option value="0" >' . $lang[1647] . '</option>';
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo '<option value="' . $i . '" ' . ($i == '40' ? 'selected' : '') . ' >' . $i . '</option>';
                                        }
                                        echo '</select></div>';
                                        echo '<div class="form-group">
                                    <label>' . $lang[1648] . '</label>
                                    <select class="form-control select2" name="emtehan" >
                                    <option value="0" > ' . $lang[1649] . '</option>';
                                        for ($i = 1; $i <= 100; $i++) {
                                            echo '<option value="' . $i . '" >' . $i . '</option>';
                                        }
                                        echo '</select></div></div>';
                                    }
                                }
                                echo '<div class="col-12 col-lg-4 col-md-12 p-3">';
                                $emtehanNumber = (($firstRank * $firstVazn) / 100) + (($secondRank * $secondVazn) / 100);
                                if (!empty($firstRank) && !empty($secondRank)) {
                                    echo '<div class="emtehan">
                                    <label>' . $lang[1654] . ' : ' . toPersianNum($emtehanNumber) . '</label>
                                    ' . (!empty($shayestegi) ? '<label>' . $lang[1655] . ': ' . toPersianNum($shayestegi) . '</label>' : '');

                                    if (!empty($emtehan)) {
                                        $finalNumber = abs($emtehan - 100);
                                        $finalEmtehanNumber = ($emtehanNumber * $emtehan) / 100;
                                        $finalShayestegiNumber = ($shayestegi * $finalNumber) / 100;
                                        echo '<label>' . $lang[1656] . ' : ' . toPersianNum($finalEmtehanNumber + $finalShayestegiNumber) . '</label>
                                        <input type="hidden" name="shayesteginumber" value="' . $finalEmtehanNumber + $finalShayestegiNumber . '">';
                                    }
                                    echo '</div>';
                                }

                                echo '</div></div></div>' . (!empty($readSkillList['tbl_memberskill_datereport']) && $access != 1 ? '' : '<input type="submit" name="runupdate" value="' . $lang[1657] . ' " class="btn btn-secondary update-btn m-3" >') . '</form><hr>';

                                echo '</div>
                                </div>';
                                echo '</div>';
                            }
                        }



                        echo '<hr><div class="container p-3"><div class="row justify-content-start">
                        <div class="col-12 col-lg-4 col-md-12 p-3">
                        <h3>' . $lang[1658] . '</h3>
                        <form action="?do=runplan" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user" value="' . $_GET['user'] . '">
                        <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        <div class="form-group">
                        <label>' . $lang[1659] . '</label>
                        <input type="text" name="name" class="form-control" value="' . $readSkillList['tbl_memberskill_planname'] . '">
                        </div>
                        <div class="form-group">
                        <label>' . $lang[1660] . '</label>
                        <input type="text" name="comment" class="form-control" value="' . $readSkillList['tbl_memberskill_comment'] . '">
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="oldatach" value="' . $readSkillList['tbl_memberskill_attach'] . '" >
                        <label>' . $lang[1661] . (!empty($readSkillList['tbl_memberskill_attach']) ? '<a href="../upload/' . $readSkillList['tbl_memberskill_attach'] . '" target="_blank">' . $lang[87] . '</a>' : '') . '</label>
                        <input type="file" name="attach" class="form-control" >
                        </div>
                        <div class="form-group">
                        <label>' . $lang[1662] . '</label>
                        <input type="text" name="startdate" class="form-control datepicker" value="' . $readSkillList['tbl_memberskill_startdate'] . '" readonly>
                        </div>
                        ' . (!empty($readSkillList['tbl_memberskill_datereport']) && $access != 1 ? '' : '
                        <input type="submit" name="submit" value="' . $lang[472] . '" class="btn btn-success mt-3" >') . '
                        </form>
                        </div>';

                        echo '<div class="col-12 col-lg-4 col-md-12 p-3"></div><div class="col-12 col-lg-4 col-md-12 p-3">
                        <h3>' . $lang[1663] . ' </h3>
                        <form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="user" value="' . $_GET['user'] . '">
                        <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        <div class="form-group">
                        <label>' . $lang[1664] . '</label>
                        <input type="text" name="commentreport" class="form-control" value="' . $readSkillList['tbl_memberskill_commentreport'] . '">
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="oldatachreport" value="' . $readSkillList['tbl_memberskill_attachreport'] . '" >
                        <label>' . $lang[1665] . (!empty($readSkillList['tbl_memberskill_attachreport']) ? '<a href="../upload/' . $readSkillList['tbl_memberskill_attachreport'] . '" target="_blank">' . $lang[87] . '</a>' : '') . '</label>
                        <input type="file" name="attachreport" class="form-control" >
                        </div>
                        <div class="form-group">
                        <label>' . $lang[1666] . ' </label>
                        <input type="text" name="datereport" class="form-control datepickerall" value="' . $readSkillList['tbl_memberskill_datereport'] . '">
                        </div>
                        ' . (!empty($readSkillList['tbl_memberskill_datereport']) && $access != 1 ? '' : '
                        <input type="submit" name="submit" value="' . $lang[472] . '" class="btn btn-success mt-3" >') . '
                        
                        </form>
                        </div></div></div>';
                    }
                }

                if (isset($do) && $do == 'reportupdate') {
                    $user = $_POST['user'];
                    $contract = $_POST['contract'];
                    $datereport = $_POST['datereport'];
                    $commentreport = $_POST['commentreport'];
                    $oldatachreport = $_POST['oldatachreport'];
                    $date = jdate("Y/m/d - h:i:s A");
                    if ($_FILES['attachreport']['size'] == 0 && $_FILES['attachreport']['error'] == 0) {
                        $attachfinal = $oldatachreport;
                    } else {
                        if (isset($_FILES['attachreport']['name'])) {
                            $attach = $_FILES['attachreport']['name'];
                            $read_file_name = explode(".", $attach);
                            $extn = end($read_file_name);
                            $attach = 'attachreport_' . time() . '.' . $extn;
                            if ($extn == 'docx' || $extn == 'DOCX' || $extn == 'DOC' || $extn == 'doc' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attachreport']['size'] < 20000000) {
                                move_uploaded_file($_FILES['attachreport']["tmp_name"], "../upload/" . $attach);
                                $attachfinal = $attach;
                            } else {
                                $attachfinal = $oldatachreport;
                            }
                        }
                    }

                    Query("update tbl_memberskill set 
                        tbl_memberskill_attachreport='$attachfinal',
                        tbl_memberskill_commentreport = '$commentreport',
                        tbl_memberskill_mentorreport = '$this_user_id',
                        tbl_memberskill_lastuserupdate = '$this_user_id',
                        tbl_memberskill_lastdateupdate = '$date',
                        tbl_memberskill_datereport='$datereport' where tbl_memberskill_user='$user' and tbl_memberskill_contract='$contract' ");
                    header("Location: plan.php?user=$user&contract=$contract");
                }

                if (isset($do) && $do == 'runupdate') {
                    //$levelcode = $_POST['levelcode'];
                    $code = $_POST['code'];
                    $user = $_POST['user'];
                    $contract = $_POST['contract'];
                    $skill = $_POST['skill'];
                    $shayestegi = (!empty($_POST['shayestegi']) ?  $_POST['shayestegi'] : '0');
                    $shayesteginumber = round($_POST['shayesteginumber']);
                    $date = jdate("Y/m/d - h:i:s A");
                    $emtehan = (!empty($_POST['emtehan']) ? $_POST['emtehan']  : '0');
                    $datecertificate = (!empty($_POST['datecertificate']) ? $_POST['datecertificate'] : '0');
                    $numbercertificate = (!empty($_POST['numbercertificate']) ? $_POST['numbercertificate'] : '');
                    $textcertificate = (!empty($_POST['textcertificate']) ? $_POST['textcertificate'] : '');
                    $certificate = (!empty($_POST['certificate']) ? $_POST['certificate'] : '0');

                    foreach ($code as $key => $value) {
                        $result = Query("select * from tbl_plan where tbl_plan_code = '$value' ");
                        $read = mysqli_fetch_array($result);

                        $val = $_POST['o' . $value];
                        $status = $_POST['status' . $value];
                        $itemmsg = $_POST['itemmsg' . $value];
                        $itemvazn = (!empty($_POST['vazn' . $value]) ? toEnglishNum($_POST['vazn' . $value]) : '0');
                        $rank = (!empty($_POST['rank' . $value]) ? toEnglishNum($_POST['rank' . $value]) : '0');
                        if (!empty($_POST['rank' . $value]) && $read['tbl_plan_rank'] != $_POST['rank' . $value]) {
                            $mentor = $this_user_id;
                        } else {
                            $mentor = '';
                        }
                        Query("update tbl_plan set 
                            tbl_plan_vazn='$itemvazn',
                            tbl_plan_rank='$rank',
                            tbl_plan_itemmsg='$itemmsg',
                            tbl_plan_status='$status',
                            tbl_plan_order='$val',
                            tbl_plan_mentorrankupdate='$mentor',
                            tbl_plan_lastuserupdate='$this_user_id',
                            tbl_plan_lastdateupdate ='$date'
                            Where tbl_plan_code = '$value' ");
                    }

                    $old_img = $_POST['old_filecertificate'];
                    $img = $_FILES['filecertificate']['name'];
                    $read_file_name = explode(".", $img);
                    $extn = end($read_file_name);

                    // upload images after rename 
                    if (!empty($img)) {

                        if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                            $img_name = 'cer_' . time() . '.' . $extn;
                            move_uploaded_file($_FILES['filecertificate']["tmp_name"], "../upload/cer/" . $img_name);
                        } else {
                            $img_name = '';
                        }
                    } else {
                        $img_name = $old_img;
                    }


                    $old_img2 = $_POST['old_filecertificate2'];
                    $img2 = $_FILES['filecertificate2']['name'];
                    $read_file_name2 = explode(".", $img2);
                    $extn = end($read_file_name2);

                    // upload images after rename 
                    if (!empty($img2)) {

                        if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                            $img_name2 = 'cer_' . time() . '.' . $extn;
                            move_uploaded_file($_FILES['filecertificate2']["tmp_name"], "../upload/cer/" . $img_name2);
                        } else {
                            $img_name2 = '';
                        }
                    } else {
                        $img_name2 = $old_img2;
                    }


                    $resultCheckNumber = Query("select * from tbl_number where tbl_number_user='$user' and tbl_number_contract='$contract' and tbl_number_skill='$skill' ");
                    if (mysqli_num_rows($resultCheckNumber) <= 0) {
                        $thisCode = strtolower(Code("", "no", 8));
                        Query("insert into tbl_number (
                                tbl_number_user,
                                tbl_number_contract,
                                tbl_number_skill,
                                tbl_number_mentor,
                                tbl_number_shayestegi,
                                tbl_number_dateupdate,
                                tbl_number_emtehan,
                                tbl_number_code,
                                tbl_number_certificate,
                                tbl_number_datecertificate,
                                tbl_number_rank,
                                tbl_number_filecertificate,
                                tbl_number_filecertificate2,
                                tbl_number_numbercertificate,
                                tbl_number_textcertificate) 
                                values('$user','$contract','$skill','$this_user_id','$shayestegi','$date','$emtehan','$thisCode','$certificate','$datecertificate','$shayesteginumber','$img_name','$img_name2','$numbercertificate','$textcertificate')");
                    } else {
                        Query("update tbl_number set
                                tbl_number_shayestegi='$shayestegi',
                                tbl_number_emtehan = '$emtehan',
                                tbl_number_certificate='$certificate',
                                tbl_number_datecertificate = '$datecertificate',
                                tbl_number_dateupdate='$date',
                                tbl_number_lastmentor='$this_user_id',
                                tbl_number_rank='$shayesteginumber' ,
                                tbl_number_filecertificate='$img_name',
                                tbl_number_filecertificate2='$img_name2',
                                tbl_number_numbercertificate='$numbercertificate',
                                tbl_number_textcertificate = '$textcertificate'
                                where tbl_number_user='$user' and tbl_number_contract='$contract' and tbl_number_skill='$skill' ");
                    }

                    header("Location: plan.php?user=$user&contract=$contract");
                }

                if (isset($do) && $do == 'answer') {


                    $result = 0;
                    $allQuestion = 0;
                    $resultSelectQuestion = Query("select * from tbl_plan where tbl_plan_code='$code' and tbl_plan_lng='$language_default' and tbl_plan_menti='$_GET[user]' and tbl_plan_delete ='0' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);
                    if ($read_select['tbl_plan_checkexam'] == '0') {
                        echo '<div  class="alert alert-warning m-3" role="alert">' . $lang[1603] . '</div>';
                    } else {
                        $data = json_decode($read_select['tbl_plan_dataanswer'], true);
                        foreach ($data as $keyexam => $valueexam) {
                            if ($keyexam != 'submit' && $keyexam != 'code') {
                                $questionId = $keyexam;
                                $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                $read_question = mysqli_fetch_array($result_question);
                                $all_value = json_decode($read_question['tbl_question_answer'], true);
                                echo '<div class="form-group exam"><h2>' . $read_question['tbl_question_title'] . '</h2>';
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
                            }
                        }
                        echo '<div class="resultexam">
                            <span>' . $lang[1604] . ' : ' . $allQuestion . '</span>
                            <span>' . $lang[1605] . ' : ' . $result . '</span>
                                    </div><br>';
                        if ($read_select['tbl_plan_admincheck'] == '0') {
                            echo '<form action="?do=confrimexam" method="post">
                                            <input type="hidden" name="code" value="' . $code . '">
                                            <input type="hidden" name="user" value="' . $_GET['user'] . '">
                                            <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                                            <input type="submit" name="updateitem" value="' . $lang[1606] . '" class="btn btn-primary">
                                    </form>';
                        } else {
                            echo '<div class="alert alert-primary" role="alert">' . $lang[1607] . '</div>';
                        }
                    }
                }
                if (isset($do) && $do == 'confrimexam') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        $date = jdate("Y/m/d : a:h:s");
                        Query("update tbl_plan set tbl_plan_admincheck='1',tbl_plan_admincheckdate='$date',tbl_plan_mentorconfirm='$this_user_id' where tbl_plan_menti='$user' and tbl_plan_code='$code' ");
                        header("Location: plan.php?user=$user&contract=$contract");
                    }
                }


                if (isset($do) && $do == 'runplan') {
                    $user = $_POST['user'];
                    $contract = $_POST['contract'];
                    $startdate = $_POST['startdate'];
                    $name = $_POST['name'];
                    $comment = $_POST['comment'];
                    $oldatach = $_POST['oldatach'];
                    if ($_FILES['attach']['size'] == 0 && $_FILES['attach']['error'] == 0) {
                        $attachfinal = $oldatach;
                    } else {
                        if (isset($_FILES['attach']['name'])) {
                            $attach = $_FILES['attach']['name'];
                            $read_file_name = explode(".", $attach);
                            $extn = end($read_file_name);
                            $attach = 'attach_' . time() . '.' . $extn;
                            if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attach']['size'] < 20000000) {
                                move_uploaded_file($_FILES['attach']["tmp_name"], "../upload/" . $attach);
                                $attachfinal = $attach;
                            } else {
                                $attachfinal = $oldatach;
                            }
                        }
                    }

                    Query("update tbl_memberskill set tbl_memberskill_attach='$attachfinal',tbl_memberskill_comment = '$comment',tbl_memberskill_mentorconfirm = '$this_user_id',tbl_memberskill_startdate='$startdate',tbl_memberskill_planname='$name' where tbl_memberskill_user='$user' and tbl_memberskill_contract='$contract' ");
                    header("Location: plan.php?user=$user&contract=$contract");
                }
                if (isset($do) && $do == 'gotopage') {
                    if (isset($_POST['gotopage'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: plan.php?page=$page_number");
                    }
                }
                // end = pagination
                // start = do add
                if (isset($do) && $do == 'add') {
                    $contract = $_GET['contract'];
                    echo '<div class="col-lg-8 col-12 p-3">
                            <form action="?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['skill'] . '" name="skill">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                    echo '<div class="form-group">
                            <label>' . $lang[1667] . '</label>
                            <select name="model" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                    for ($i = 1230; $i <= 1237; $i++) {
                        echo '<option ' . ($i == '1230' ? 'selected' : '') . ' value="' . $i . '-' . $_GET['user'] . '-' . $contract . '-' . $_GET['skill'] . '">' . $lang[$i] . '</option>';
                    }
                    echo '</select>
                            </div>';

                    echo '<div class="resultplan">
                        <div class="form-group">
                                <label>' . $lang[1677] . '</label>
                                <select name="class" class="form-control select2" style="width:100%"><option value="">' . $lang[70] . '</option>';
                    $resultClass = Query("SELECT * From tbl_class where tbl_class_skill like '%$_GET[skill]%' and tbl_class_lng='$language_default' order by tbl_class_id DESC ");
                    while ($readClass = mysqli_fetch_array($resultClass)) {
                        echo '<option value="' . $readClass['tbl_class_code'] . '" >' . $readClass['tbl_class_title'] . '</option>';
                    }
                    echo '</select></div><div class="showfile"></div>';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                    <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                    <textarea name="text_' . $language[$i] . '" class="form-control" rows="8"  style="height:150px;"></textarea>
                                    </div>';
                    }
                    echo '<div class="form-group">
                        <label>' . $lang[1636] . '</label>
                        <input type="text" name="expire" class="form-control datepicker" readonly >
                        </div></div>';
                    echo '<hr><div class="form-group">
                        <label><input type="checkbox" name="namayandeh" value="1" > ' . $lang[1743] . '</label>
                        </div>';
                    echo '<div class="col-12 col-md-12"><div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3"></div>
                            </div></form></div>';
                }
                // start = add record to db
                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        $date = jdate("Y/m/d : a:h:s");

                        $model = (explode("-", $_POST['model']));
                        $namayandeh = (!empty($_POST['namayandeh']) ? $_POST['namayandeh'] : '0');

                        $class = (!empty($_POST['class']) ? $_POST['class'] : '');
                        $coach = (!empty($_POST['coach']) ? $_POST['coach'] : '0');
                        $link = (!empty($_POST['link']) ? $_POST['link'] : '');
                        $file = (!empty($_POST['file']) ? $_POST['file'] : '');

                        $azmoon = (!empty($_POST['azmoon']) ? $_POST['azmoon'] : '');
                        $timestart = (!empty($_POST['timestart']) ? $_POST['timestart'] : '0');
                        $timeend = (!empty($_POST['timeend']) ? $_POST['timeend'] : '0');
                        $location = (!empty($_POST['location']) ? $_POST['location'] : '');
                        $karfarma = (!empty($_POST['karfarma']) ? $_POST['karfarma'] : '');

                        $visitmodel = (!empty($_POST['visitmodel']) ? $_POST['visitmodel'] : '0');
                        $leader = (!empty($_POST['leader']) ? $_POST['leader'] : '');
                        $team = (!empty($_POST['team']) ? $_POST['team'] : '');

                        $dataTeam = '';
                        foreach ($team as $key => $value) {
                            if (!empty($value)) {
                                $dataTeam .= $value . ',';
                            }
                        }

                        $expire = $_POST['expire'];
                        $expireOne = explode("/", $expire);
                        $expireEnglish = jalali_to_gregorian($expireOne[0], $expireOne[1], $expireOne[2]);
                        $expireEnglish = $expireEnglish[0] . '/' . ($expireEnglish[1] < 10 ? '0' . $expireEnglish[1] : $expireEnglish[1]) . '/' . ($expireEnglish[2] < 10 ? '0' . $expireEnglish[2] : $expireEnglish[2]);

                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        $skill = $_POST['skill'];

                        if (!empty($_POST['question'])) {
                            $question = $_POST['question'];
                            $all_value = array();
                            foreach ($question as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$value] = '1';
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                        } else {
                            $all_value = '';
                        }


                        for ($i = 1; $i <= $language_count; $i++) {
                            $text_[$i] = (!empty($_POST['text_' . $language[$i]]) ? addslashes($_POST['text_' . $language[$i]]) : '');
                            $language_name = $language[$i];
                            Query("insert into tbl_plan (
                                tbl_plan_text,
                                tbl_plan_model,
                                tbl_plan_adddate,
                                tbl_plan_delete,
                                tbl_plan_class,
                                tbl_plan_mentor,
                                tbl_plan_code,
                                tbl_plan_lng,
                                tbl_plan_link,
                                tbl_plan_expire,
                                tbl_plan_coach,
                                tbl_plan_menti,
                                tbl_plan_contract,
                                tbl_plan_file,
                                tbl_plan_order,
                                tbl_plan_status,
                                tbl_plan_azmoon,
                                tbl_plan_timestart,
                                tbl_plan_timeend,
                                tbl_plan_location,
                                tbl_plan_karfarma,
                                tbl_plan_skill,
                                tbl_plan_question,
                                tbl_plan_dateenglish,
                                tbl_plan_namayandeh,
                                tbl_plan_visitmodel,
                                tbl_plan_leader,
                                tbl_plan_team) 
                                values(
                                    '$text_[$i]',
                                    '$model[0]',
                                    '$date',
                                    '0',
                                    '$class',
                                    '$this_user_id',
                                    '$code',
                                    '$language_name',
                                    '$link',
                                    '$expire',
                                    '$coach',
                                    '$user',
                                    '$contract',
                                    '$file',
                                    '0',
                                    '0',
                                    '$azmoon',
                                    '$timestart',
                                    '$timeend',
                                    '$location',
                                    '$karfarma',
                                    '$skill',
                                    '$all_value',
                                    '$expireEnglish',
                                    '$namayandeh',
                                    '$visitmodel',
                                    '$leader',
                                    '$dataTeam')");
                        }
                        header("Location: plan.php?user=$user&contract=$contract");
                        exit();
                    }
                }
                // end  = add record  
                if (isset($do) && $do == 'edit') {
                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_plan_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_plan Where tbl_plan_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    $userId = read_fild(NULL, 'tbl_plan_menti');
                    $contractId = read_fild(NULL, 'tbl_plan_contract');
                    $planModel = read_fild(NULL, 'tbl_plan_model');
                    $planSkill = read_fild(NULL, 'tbl_plan_skill');
                    // form for edit
                    echo '<div class="col-lg-8 col-12 p-3">
                            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" name="menti" value="' .  $userId . '" >
                            <input type="hidden" name="contract" value="' . $contractId . '" >';

                    echo '<div class="form-group">
                            <label>' . $lang[1667] . '</label>
                            <select name="model" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                    for ($i = 1230; $i <= 1237; $i++) {
                        echo '<option value="' . $i . '-' . $userId . '-' . $contractId . '-' . $planSkill . '" ' . (read_fild(NULL, 'tbl_plan_model') == $i ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                    }
                    echo '</select>
                            </div>';

                    echo '<div class="resultplan">';

                    if ($planModel == '1230') {
                        echo '<div class="form-group">
                                    <label>' . $lang[1194] . '</label>
                                    <select name="class" class="form-control select2" style="width:100%"><option value="">' . $lang[70] . '</option>';
                        $resultClass = Query("SELECT * From tbl_class where tbl_class_skill like '%$_GET[skill]%' and tbl_class_lng='$language_default' order by tbl_class_id DESC ");
                        while ($readClass = mysqli_fetch_array($resultClass)) {
                            echo '<option value="' . $readClass['tbl_class_code'] . '" ' . (read_fild(NULL, 'tbl_plan_class') == $readClass['tbl_class_code'] ? 'selected' : '') . '>' . $readClass['tbl_class_title'] . '</option>';
                        }
                        echo '</select> </div><div class="showfile"></div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" class="form-control" rows="8"  style="height:150px;">' . read_fild($language[$i], 'tbl_plan_text') . '</textarea>
                                          </div>';
                        }
                        echo '<div class="form-group">
                            <label>' . $lang[1636] . '</label>
                            <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                            </div>';
                    }

                    if ($planModel == '1231') {
                        echo '<div class="form-group planhide">
                            <label>' . $lang[1675] . '</label>
                            <select name="file" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                        $resultFile = Query("SELECT * From tbl_mohtava Where tbl_mohtava_delete = '0' and tbl_mohtava_lng='$language_default' order by tbl_mohtava_id DESC ");
                        while ($readFile = mysqli_fetch_array($resultFile)) {
                            echo '<option value="' . $readFile['tbl_mohtava_code'] . '" class="group-cat" ' . (read_fild(NULL, 'tbl_plan_file') == $readFile['tbl_mohtava_code'] ? 'selected' : '') . '>' . $readFile['tbl_mohtava_codefile'] . '-' . $readFile['tbl_mohtava_title'] . '</option>';
                        }
                        echo '</select>
                            </div><div class="showfile"></div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1636] . '</label>
                            <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                            </div>';
                    }

                    if ($planModel == '1232' || $planModel == '1233' || $planModel == '1234') {
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '"
                                          class="form-control" rows="8" style="height:150px;">' . read_fild($language[$i], 'tbl_plan_text') . '</textarea>
                                          </div>';
                        }
                        echo '<div class="form-group">
                                <label>' . $lang[83] . ' </label>
                                <input type="text" name="link" class="form-control" value="' . read_fild(NULL, 'tbl_plan_link') . '" >
                                </div>';


                        echo '<div class="form-group">
                                <hr>
                                <label><input type="radio" name="visitmodel" value="0" ' . (read_fild(NULL, 'tbl_plan_visitmodel') == 0 ? 'checked' : '') . '> ' . $lang[1820] . ' </label>
                                <label><input type="radio" name="visitmodel" value="1" ' . (read_fild(NULL, 'tbl_plan_visitmodel') == 1 ? 'checked' : '') . '> ' . $lang[1821] . ' </label>
                                </div>';


                        echo '<div class="form-group">
                                <label>' . ($planModel == '1234' ? $lang[62] : $lang[1636]) . '</label>
                                <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                                </div>';
                    }


                    if ($planModel == '1233' || $planModel == '1234') {

                        echo '<div class="form-group">
                                <label>' . $lang[1822] . ' </label>
                                <select name="leader" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                        $resultSkill = Query("SELECT tbl_memberskill_user From tbl_memberskill Where tbl_memberskill_data like '%$planSkill%' and tbl_memberskill_contract='$contractId'  ");
                        while ($readSkill = mysqli_fetch_array($resultSkill)) {
                            $user = $readSkill['tbl_memberskill_user'];
                            $result_member = Query("SELECT * From tbl_member where tbl_member_id='$user'");
                            $read_member = mysqli_fetch_array($result_member);
                            echo '<option value="' . $user . '" ' . (read_fild(NULL, 'tbl_plan_leader') == $user ? 'selected' : '') . '>' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</option>';
                        }
                        echo '</select>
                                </div>';

                        echo '<div class="form-group">
                                <label>' . $lang[1823] . ' </label>
                                <select name="team[]" class="form-control select2" multiple="multiple">
                                <option value="">' . $lang[70] . '</option>';
                        $resultSkill = Query("SELECT tbl_memberskill_user From tbl_memberskill Where tbl_memberskill_data like '%$planSkill%' and tbl_memberskill_contract='$contractId'  ");
                        while ($readSkill = mysqli_fetch_array($resultSkill)) {
                            $user = $readSkill['tbl_memberskill_user'];
                            $result_member = Query("SELECT * From tbl_member where tbl_member_id='$user'");
                            $read_member = mysqli_fetch_array($result_member);
                            echo '<option value="' . $user . '" ' . (stripos(read_fild(NULL, 'tbl_plan_team'), "$user") !== false ? 'selected' : '') . '>' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</option>';
                        }
                        echo '</select>
                                </div>';
                    }

                    if ($planModel == '1235') {
                        echo '<div class="form-group planhide plan1">
                            <label>' . $lang[1676] . '</label>
                            <select name="coach" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                        $resultCoach = Query("SELECT * From tbl_user Where tbl_user_role like '%6%' ");
                        while ($readCoach = mysqli_fetch_array($resultCoach)) {
                            echo '<option value="' . $readCoach['tbl_user_id'] . '" ' . (read_fild(NULL, 'tbl_plan_coach') == $readCoach['tbl_user_id'] ? 'selected' : '') . '>' . $readCoach['tbl_user_name'] . '</option>';
                        }
                        echo '</select>
                            </div>';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>' . $lang[1784] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '"
                                          class="form-control" rows="8" style="height:150px;">' . read_fild($language[$i], 'tbl_plan_text') . '</textarea>
                                          </div>';
                        }

                        echo '<div class="form-group">
                            <label>' . $lang[62] . '</label>
                            <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                            </div>';
                    }


                    if ($planModel == '1236') {
                        echo '<div class="form-group">
                            <label>' . $lang[1632] . ' </label>
                            <input type="text" name="azmoon" class="form-control" value="' . read_fild(NULL, 'tbl_plan_azmoon') . '">
                            </div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1669] . ' </label>
                            <select name="timestart" class="form-control select2" >
                            <option value="">' . $lang[70] . '</option>';
                        for ($i = 8; $i <= 22; $i++) {
                            echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_plan_timestart') == $i ? 'selected' : '') . '>' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                        }
                        echo '</select></div>';

                        echo '<div class="form-group">
                            <label>' . $lang[1670] . '  </label>
                            <select name="timeend" class="form-control select2" >
                            <option value="">' . $lang[70] . '</option>';
                        for ($i = 8; $i <= 22; $i++) {
                            echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_plan_timeend') == $i ? 'selected' : '') . '>' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                        }
                        echo '</select></div>';
                        echo '<div class="form-group">
                            <label>' . $lang[147] . '</label>
                            <input type="text" name="location" class="form-control" value="' . read_fild(NULL, 'tbl_plan_location') . '">
                            </div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1668] . '</label>
                            <input type="text" name="karfarma" class="form-control" value="' . read_fild(NULL, 'tbl_plan_karfarma') . '">
                            </div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1637] . '</label>
                            <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                            </div>';
                    }

                    if ($planModel == '1237') {
                        @$array = json_decode(read_fild(NULL, 'tbl_plan_question'), true);
                        echo '<div class="form-group">
                            <label>' . $lang[1632] . ' </label>
                            <input type="text" name="azmoon" class="form-control" value="' . read_fild(NULL, 'tbl_plan_azmoon') . '" >
                            </div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1669] . ' </label>
                            <select name="timestart" class="form-control select2" >
                            <option value="">' . $lang[70] . '</option>';
                        for ($i = 8; $i <= 22; $i++) {
                            echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_plan_timestart') == $i ? 'selected' : '') . '>' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                        }
                        echo '</select></div>';

                        echo '<div class="form-group">
                            <label>' . $lang[1670] . '  </label>
                            <select name="timeend" class="form-control select2" >
                            <option value="">' . $lang[70] . '</option>';
                        for ($i = 8; $i <= 22; $i++) {
                            echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_plan_timeend') == $i ? 'selected' : '') . '>' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                        }
                        echo '</select></div>';
                        echo '<div class="form-group">
                            <label>' . $lang[147] . '</label>
                            <input type="text" name="location" class="form-control" value="' . read_fild(NULL, 'tbl_plan_location') . '">
                            </div>';

                        echo '<div class="col-lg-6">
                            <h5>' . $lang[1671] . '</h5></div>';

                        $resultQuestion = Query("SELECT * From tbl_question where tbl_question_model='0' and tbl_question_skill='$planSkill' and tbl_question_lng='$language_default' and tbl_question_disable='0' ORDER BY tbl_question_order DESC , tbl_question_id DESC ");
                        if (mysqli_num_rows($resultQuestion) > 0) {
                            while ($readQuestion = mysqli_fetch_array($resultQuestion)) {
                                $ids = $readQuestion['tbl_question_code'];
                                echo '<div class="form-group">
                                <label><input type="checkbox" name="question[]" value="' . $readQuestion['tbl_question_code'] . '" ' . (!empty($array[$ids]) ? 'checked' : '') . '> ' . $readQuestion['tbl_question_title'] . '</label>
                                </div>';
                            }
                        }
                        echo '</div>';
                        echo '<div class="form-group">
                            <label>' . $lang[1637] . '</label>
                            <input type="text" name="expire" required class="form-control datepicker" value="' . read_fild(NULL, 'tbl_plan_expire') . '" readonly>
                            </div>';
                    }

                    echo '<hr><div class="form-group">
                        <label><input type="checkbox" name="namayandeh" value="1" ' . (read_fild(NULL, 'tbl_plan_namayandeh') == '1' ? 'checked' : '') . '> ' . $lang[1743] . '</label>
                        </div>';

                    echo '</div><div class="form-group">
                    <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                    </div>
                    </form></div>';
                }

                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $date = jdate("Y/m/d : a:h:s");
                        $namayandeh = (!empty($_POST['namayandeh']) ? $_POST['namayandeh'] : '0');

                        $model = (explode("-", $_POST['model']));
                        $class = (!empty($_POST['class']) ? $_POST['class'] : '');
                        $coach = (!empty($_POST['coach']) ? $_POST['coach'] : '0');
                        $link = (!empty($_POST['link']) ? $_POST['link'] : '');
                        $file = (!empty($_POST['file']) ? $_POST['file'] : '');
                        $expire = $_POST['expire'];
                        $expireOne = explode("/", $expire);
                        $expireEnglish = jalali_to_gregorian($expireOne[0], $expireOne[1], $expireOne[2]);
                        $expireEnglish = $expireEnglish[0] . '/' . ($expireEnglish[1] < 10 ? '0' . $expireEnglish[1] : $expireEnglish[1]) . '/' . ($expireEnglish[2] < 10 ? '0' . $expireEnglish[2] : $expireEnglish[2]);
                        $azmoon = (!empty($_POST['azmoon']) ? $_POST['azmoon'] : '');
                        $timestart = (!empty($_POST['timestart']) ? $_POST['timestart'] : '0');
                        $timeend = (!empty($_POST['timeend']) ? $_POST['timeend'] : '0');
                        $location = (!empty($_POST['location']) ? $_POST['location'] : '');
                        $karfarma = (!empty($_POST['karfarma']) ? $_POST['karfarma'] : '');

                        $visitmodel = (!empty($_POST['visitmodel']) ? $_POST['visitmodel'] : '0');
                        $leader = (!empty($_POST['leader']) ? $_POST['leader'] : '');
                        $team = (!empty($_POST['team']) ? $_POST['team'] : '');

                        $dataTeam = '';
                        foreach ($team as $key => $value) {
                            if (!empty($value)) {
                                $dataTeam .= $value . ',';
                            }
                        }

                        $contract = $_POST['contract'];
                        $menti = $_POST['menti'];

                        if (!empty($_POST['question'])) {
                            $question = $_POST['question'];
                            $all_value = array();
                            foreach ($question as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$value] = '1';
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                        } else {
                            $all_value = '';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            $text_[$i] = addslashes($_POST['text_' . $language[$i]]);
                            $language_record = $language[$i];

                            Query("Update tbl_plan SET 
                                tbl_plan_text='$text_[$i]',
                                tbl_plan_model='$model[0]',
                                tbl_plan_editdate='$date',
                                tbl_plan_class='$class',
                                tbl_plan_mentor='$this_user_id',
                                tbl_plan_link='$link',
                                tbl_plan_expire='$expire',
                                tbl_plan_coach='$coach',
                                tbl_plan_azmoon='$azmoon',
                                tbl_plan_timestart='$timestart',
                                tbl_plan_timeend='$timeend',
                                tbl_plan_location='$location',
                                tbl_plan_karfarma='$karfarma',
                                tbl_plan_question='$all_value',
                                tbl_plan_dateenglish = '$expireEnglish',
                                tbl_plan_namayandeh = '$namayandeh',
                                tbl_plan_visitmodel = '$visitmodel',
                                tbl_plan_leader= '$leader',
                                tbl_plan_team= '$dataTeam',
                                tbl_plan_file='$file'  Where tbl_plan_code= '$code' and tbl_plan_lng='$language_record' ");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: plan.php?user=$menti&contract=$contract ");
                    }
                }

                // start = delete record
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_plan Where tbl_plan_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_plan_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="id" >
                         <input type="hidden" value="' . $_GET['user'] . '" name="user" >
                         <input type="hidden" value="' . $_GET['contract'] . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {


                        $id = $_POST['id'];
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];

                        $result = Query("select * from tbl_plan where tbl_plan_mentor='$this_user_id' and tbl_plan_code = '$id' ");
                        if (mysqli_num_rows($result) > 0) {
                            Query("update tbl_plan set  tbl_plan_delete='1',tbl_plan_mentor='$this_user_id' Where tbl_plan_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: plan.php?user=$user&contract=$contract");
                            exit();
                        } else {
                            header("Location: plan.php?user=$user&contract=$contract");
                            exit();
                        }
                    }
                    // no delete
                    elseif (isset($_POST['nodelete'])) {
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        header("Location: plan.php?user=$user&contract=$contract");
                        exit();
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        header("Location: ?keyword=$keyword_search");
                    }
                }
                // end = delete item
                ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';
?>