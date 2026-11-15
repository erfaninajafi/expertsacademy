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
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_contract['tbl_contract_manager'] . '</h5>
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
                    <h5 class="fs-0">'.$lang[1614].'  : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[385].'  : ' . toPersianNum($read_member['tbl_member_nationalcode']) . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[378].'  : ' . toPersianNum($read_member['tbl_member_mobile']) . '</h5>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>                    
    </div>
    </div>';
}

// if (!empty($_GET['contract'])) {
//     $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
//     $read_contract = mysqli_fetch_array($result_contract);
//     echo '<div class="col-md-12"><div class="box-info">
//     <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :<a href="faragir.php?contract='.$_GET['contract'].'">' . $read_contract['tbl_contract_code'] . '</a></span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
//     </div></div>';

//     $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
//     $read_member = mysqli_fetch_array($result_member);
//     echo '<div class="col-md-12"><div class="box-info">
//     <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span><a href="faragir.php?contract='.$_GET['contract'].'&user='.$_GET['user'].'">نام و نام خانوادگی فراگیر : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</a></span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read_member['tbl_member_nationalcode']) . '</span></div>
//     <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read_member['tbl_member_mobile']) . '</span></div>
// </div></div>';
//     if (!empty($do) && $do != 'delete' && $do != 'edit') {
//         $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$_GET[skill]' and tbl_categories_lng='$language_default' ");
//         $readSkill = mysqli_fetch_array($resultSkill);
//         echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">
//         پروژه : <a href="faragir.php?contract=' . $_GET['contract'] . '">' . $read_contract['tbl_contract_code'] . '</a>  /  
//         مهارت : ' . $readSkill['tbl_categories_title'] . '
//         </h5></div>';
//     }
// }


?>

<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">

                <?php
                    if (!isset($do)) {
                        $resultSkillList = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$_GET[user]' and tbl_memberskill_contract='$_GET[contract]' ");
                        if(mysqli_num_rows($resultSkillList) <= 0)
                        {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1630] . '</div>';
                        }
                        else {
                        $readSkillList = mysqli_fetch_array($resultSkillList);
                        $skillList = explode(",", $readSkillList['tbl_memberskill_data']);
                        echo '<div class="accordion m-2" id="accordionSkill">';
                        foreach ($skillList as $key => $value) {
                            
                            if (!empty($value)) {

                                $checkCertificate = Query("select tbl_number_certificate from tbl_number where tbl_number_skill='$value' and tbl_number_contract ='$_GET[contract]' ");
                                if(mysqli_num_rows($checkCertificate) > 0 )
                                {
                                    $readCheckCertificate = mysqli_fetch_array($checkCertificate);
                                    $finalConfirmSkill = $readCheckCertificate['tbl_number_certificate'];
                                }
                                else {
                                    $finalConfirmSkill = '0';
                                }
                                
                                $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                                $readSkill = mysqli_fetch_array($resultSkill);
                                $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                                $result_for_confirm = Query("SELECT * From tbl_plan where tbl_plan_status='1' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");

                                echo '<h2 class="accordion-header" id="heading1">
                                <button class="accordion-button '.($key != '0' ? 'collapsed' : '').'" type="button" data-bs-toggle="collapse" data-bs-target="#collapse'.$key.'" aria-expanded="true" aria-controls="collapse'.$key.'">'.$lang[1337].' : ' . $readSkill['tbl_categories_title'] . '</button>
                              </h2>
                              <div class="accordion-collapse collapse '.($key == '0' ? 'show' : '').'" id="collapse'.$key.'" aria-labelledby="heading'.$key.'" data-bs-parent="#accordionSkill">
                                <div class="accordion-body p-1">';

                                echo '<div class="col-12 col-gl-12 col-md-12 p-3"><h5>'.$lang[1337].' : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
                                if (empty($do)) {
                                    echo (mysqli_num_rows($result_for_count) > 0 ? '<div class="col-12 col-gl-12 col-md-12 pb-2"><div class="progress-plan"> '.$lang[1721].' : 
                                <div class="progress mb-0 mt-3" style="height:15px" role="progressbar" aria-valuenow="' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '" aria-valuemin="0" aria-valuemax="100">
                                <div class="progress-bar" style="width: ' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '%">' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '%
                                </div>
                                </div>
                                </div></div>' : '');
                                }

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
                                echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>'.$lang[1631].'</th>
                                    <th>'.$lang[20].'</th>
                                    <th>'.$lang[1602].'</th>
                                    <th>'.$lang[1340].'</th>
                                    <th>'.$lang[1619].'</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                $k = 1;
                                $finalPrice=0;
                                while ($read = mysqli_fetch_array($result)) {
                                    $mentor = $read['tbl_plan_mentor'];
                                    $coach = $read['tbl_plan_coach'];
                                    $file = $read['tbl_plan_file'];
                                    $question = (!empty($read['tbl_plan_question']) ? count(json_decode($read['tbl_plan_question'], true)) : '0');
                                    $resultFile = Query("SELECT * From tbl_bankfile Where tbl_bankfile_code = '$file' and tbl_bankfile_lng='$language_default' and tbl_bankfile_delete = '0' ");
                                    $readFile = mysqli_fetch_array($resultFile);
                                    $resultCoach = Query("SELECT * From tbl_user Where tbl_user_id = '$coach' ");
                                    $readCoach = mysqli_fetch_array($resultCoach);
                                    $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                                    $readMentor = mysqli_fetch_array($resultMentor);
                                    if ($read['tbl_plan_model'] == '1230') {
                                        $class = $read['tbl_plan_class'];
                                        $resultClass = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_code ='$class' ");
                                        $readClass = mysqli_fetch_array($resultClass);
            
                                        $result_course_parent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_lng ='$language_default' and  tbl_courseparent_class ='$class' ");
                                        
                                        while ($read_course_parent = mysqli_fetch_array($result_course_parent)) {
                                        $result_course_price = Query("SELECT tbl_course_mohtava From tbl_course where tbl_course_lng ='$language_default' and  tbl_course_class ='$read_course_parent[tbl_courseparent_code]' ");
                                        while ($read_course_price = mysqli_fetch_array($result_course_price)) {
                                            if(!empty($read_course_price['tbl_course_mohtava'])){
                                                $mohtava = explode(",",$read_course_price['tbl_course_mohtava']);
                                            foreach ($mohtava as $key => $value) {
                                                if(!empty($value))
                                                {
                                                    $result_mohtava = Query("select tbl_mohtava_sellprice from tbl_mohtava where tbl_mohtava_code = '$value' ");
                                                    $read_mohtava = mysqli_fetch_array($result_mohtava);
                                                    //echo $read_mohtava['tbl_mohtava_sellprice'];
                                                    $finalPrice +=(!empty($read_mohtava['tbl_mohtava_sellprice']) ? $read_mohtava['tbl_mohtava_sellprice'] : '0');
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
                                    <td class="align-middle p-2">' . ($read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233' || $read['tbl_plan_model'] == '1234' ? '<a href="' . $read['tbl_plan_link'] . '" target="_blank">' . PlanModel($read['tbl_plan_model']) . '</a>' : PlanModel($read['tbl_plan_model']));
                                        if ($read['tbl_plan_model'] == '1230')
                                        {  echo '<br><br><span class="orange">'.$lang[710].' : ' . $readClass['tbl_class_title'] . '</span>';
                                            @$result_parent = Query("SELECT tbl_course_class From tbl_course where tbl_course_code ='$readcourse[tbl_course_code]' and tbl_course_lng='$language_default' ");
                                            //echo mysqli_num_rows($result_parent);
                                            if(mysqli_num_rows($result_parent) > 0 ){
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
                                            }
                                            else {
                                                $read_lesson = 0;
                                                $all_lesson = 0;
                                            }
                                        echo '<div class="skills padding-right-clear">
                                        <div class="d-flex align-items-center">
                                        <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                            <div class="progress-bar rounded-pill" style="width: '.($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100)-100))).'%;"></div>
                                        </div>
                                        <div class="fw-semi-bold ms-2">'.($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100)-100))).'%</div>
                                    </div>
                                            </div>';
                                        }
                                        echo ($read['tbl_plan_model'] == '1235' ? '<br><br><span class="orange">'.$lang[1213].'  : ' . $readCoach['tbl_user_name'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1236' ? '<br><br><span class="orange">'.$lang[1632].' : ' . $read['tbl_plan_azmoon'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1237' ? '<br><br><span class="orange">'.$lang[1633].' : ' . toPersianNum($question) . '<br>'.($read['tbl_plan_checkexam'] == 0 ? $lang[1634] : $lang[1635].' '.$read['tbl_plan_dateexam'] ).'</span>' : '') ;
                                        
                                        if($read['tbl_plan_model'] == '1231') { 
                                            echo '<br><br><span class="orange">'.$lang[52].' : ' . @$readFile['tbl_mohtava_title'] . '</span>'; 
                                            $finalPrice +=(!empty($readFile['tbl_mohtava_sellprice']) ? $readFile['tbl_mohtava_sellprice'] : 0); 
                                            
                                        }
                                        echo '</td>
                                        <td class="align-middle p-2">' . (!empty($read['tbl_plan_editdate']) ? toPersianNum($read['tbl_plan_editdate']) : '') . '</td>
                                        <td class="align-middle p-2">
                                    ' . ($read['tbl_plan_model'] == '1230' || $read['tbl_plan_model'] == '1231' || $read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233'  ? $lang[1636].' : ' : '') .
                                        ($read['tbl_plan_model'] == '1234' ? $lang[62].' : ' : '') .
                                        ($read['tbl_plan_model'] == '1235' ? $lang[62].' : ' : '') .
                                        ($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237' ? $lang[1637].' : ' : '') .
                                        toPersianNum($read['tbl_plan_expire']) . '</td>

                                    <td class="align-middle p-2">
                                        <div class="form-group">
                                        <label><input class="plan" type="radio"  value="0" ' . ($read['tbl_plan_status'] == 0 ? 'checked' : '') . ' disabled> '.$lang[1556].'</label>
                                        <label><input  class="plan" type="radio"  value="1" ' . ($read['tbl_plan_status'] == 1 ? 'checked' : '') . ' disabled> '.$lang[1557].'</label>
                                        </div>
                                        <div class="form-group">
                                        <textarea rows="2" class="form-control" name="itemmsg' . $read['tbl_plan_code'] . '" placeholder="'.$lang[1527].'">'.$read['tbl_plan_itemmsg'].'</textarea>
                                        </div>';
                                    if($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237') {
                                        echo '<div class="form-group"><label>'.$lang[1638].'</label>
                                        <select class="form-control select2" name="rank' . $read['tbl_plan_code'] . '" placeholder="'.$lang[1638].'" disabled>
                                        <option value="0" '.($read['tbl_plan_rank'] == NULL || $read['tbl_plan_rank'] == '0' ? 'selected' : '').'>'.$lang[1638].'</option>';
                                            for ($i=1; $i <= 100; $i++) { 
                                                echo '<option value="'.$i.'" '.($read['tbl_plan_rank'] != NULL && $read['tbl_plan_rank'] == $i ? 'selected' : '').'>'.$i.'</option>';
                                            }
                                        echo' </select>
                                        </div>';
                                        if($read['tbl_plan_model'] == '1236') {
                                        echo '<div class="form-group"><label>'.$lang[1639].'</label>
                                        <select class="form-control select2" name="vazn' . $read['tbl_plan_code'] . '" placeholder="'.$lang[1640].' " disabled>
                                        <option value="0" '.($read['tbl_plan_vazn'] == NULL || $read['tbl_plan_vazn'] == '0' ? 'selected' : '').'>'.$lang[1639].'</option>';
                                        for ($i=1; $i <= 100; $i++) { 
                                            echo '<option value="'.$i.'" '.($read['tbl_plan_vazn'] != NULL && $read['tbl_plan_vazn'] == $i ? 'selected' : '').'>'.$i.'</option>';
                                        }
                                        echo '</select></div>';
                                        }
                                    }
                                    echo '</td>';

                                    echo '<td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '<br>'.$lang[1641].' :' . toPersianNum($read['tbl_plan_adddate']) . '</div></td>
                                    
                                    </tr>';
                                    $k++;
                                }
                                echo '<tr class="dark-price"><td colspan="7">'.$lang[1416].'</td><td>'.(!empty($finalPrice) ? toPersianNum(number_format($finalPrice)) : toPersianNum('0')).'</td></tr></tbody></table><div class="container p-3"><div class="row"><div class="col-12 col-lg-4 col-md-12 p-3"><input type="hidden" name="skill" value="'.$value.'">';


                                $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1236' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                @$firstRank = $readNumberModel['tbl_plan_rank'];
                                @$firstVazn = $readNumberModel['tbl_plan_vazn'];
                                $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1237' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
                                $readNumberModel = mysqli_fetch_array($resultNumberModel);
                                @$secondRank = $readNumberModel['tbl_plan_rank'];
                                //@$secondVazn = $readNumberModel['tbl_plan_vazn'];

                                // mokamel first vazn
                                @$secondVazn = 100-$firstVazn;
                                if(!empty($firstRank) && !empty($secondRank)){
                                $resultCheckNumber = Query("select * from tbl_number where tbl_number_user='$_GET[user]' and tbl_number_contract='$_GET[contract]' and tbl_number_skill='$value' ");
                                if(mysqli_num_rows($resultCheckNumber) > 0)
                                {
                                    $readCheckNumber = mysqli_fetch_array($resultCheckNumber);
                                    $shayestegi = $readCheckNumber['tbl_number_shayestegi'];
                                    $emtehan = $readCheckNumber['tbl_number_emtehan'];
                                    echo '<div class="form-group">
                                    <label>'.$lang[1646].'</label>
                                    <select class="form-control select2" name="shayestegi" disabled>
                                    <option value="0" '.($shayestegi == '0' ? 'selected' : '').'> '.$lang[1647].'</option>';
                                    for ($i=1; $i <= 100; $i++) { 
                                        echo '<option value="'.$i.'" '.($shayestegi == $i ? 'selected' : '').'>'.$i.'</option>';
                                    }
                                    echo '</select></div>';

                                    echo '<div class="form-group">
                                    <label>'.$lang[1648].'</label>
                                    <select class="form-control select2" name="emtehan" disabled>
                                    <option value="0" '.($emtehan == '0' ? 'selected' : '').'> '.$lang[1649].'</option>';
                                    for ($i=1; $i <= 100; $i++) { 
                                        echo '<option value="'.$i.'" '.($emtehan == $i ? 'selected' : '').'>'.$i.'</option>';
                                    }
                                    echo '</select></div>';
                                    echo '<hr><div class="form-group"><label><input type="checkbox" name="certificate" value="1" '.($readCheckNumber['tbl_number_certificate'] == 1 ? 'checked' : '').' disabled> '.$lang[1650].' </label></div>
                                    <div class="form-group"><label>'.$lang[1651].'</label><input type="text" class="form-control" value="'.$readCheckNumber['tbl_number_rank'].'" disabled></div>
                                    <div class="form-group"><label>'.$lang[1652].'</label><input type="text" class="form-control datepicker" name="datecertificate" value="'.$readCheckNumber['tbl_number_datecertificate'].'" disabled></div>';
                                }
                                else {
                                    echo '<div class="form-group">
                                    <label>'.$lang[1653].'</label>
                                    <select class="form-control select2" name="shayestegi" disabled>
                                    <option value="0" >'.$lang[1647].'</option>';
                                    for ($i=1; $i <= 100; $i++) { 
                                        echo '<option value="'.$i.'" '.($i =='40' ? 'selected' : '').' >'.$i.'</option>';
                                    }
                                    echo '</select></div>';
                                    echo '<div class="form-group">
                                    <label>'.$lang[1648].'</label>
                                    <select class="form-control select2" name="emtehan" disabled>
                                    <option value="0" > '.$lang[1649].'</option>';
                                    for ($i=1; $i <= 100; $i++) { 
                                        echo '<option value="'.$i.'" >'.$i.'</option>';
                                    }
                                    echo '</select></div>';

                                }
                                echo '</div><div class="col-12 col-lg-4 col-md-12 p-3">';
                            }
                                $emtehanNumber = (($firstRank * $firstVazn) / 100 )+(($secondRank * $secondVazn) / 100 );
                                if(!empty($firstRank) && !empty($secondRank)){
                                    echo '<div class="emtehan">
                                    <label>'.$lang[1654].' : '.toPersianNum($emtehanNumber ).'</label>
                                    '.(!empty($shayestegi) ? '<label>'.$lang[1655].': '.toPersianNum($shayestegi).'</label>' : '' );

                                    if(!empty($emtehan))
                                    {
                                        $finalNumber = abs($emtehan - 100);
                                        $finalEmtehanNumber = ($emtehanNumber * $emtehan) / 100;
                                        $finalShayestegiNumber = ($shayestegi * $finalNumber) / 100;
                                        echo '<label>'.$lang[1656].' : '.toPersianNum($finalEmtehanNumber+$finalShayestegiNumber).'</label>
                                        <input type="hidden" name="shayesteginumber" value="'.$finalEmtehanNumber+$finalShayestegiNumber.'" disabled>';
                                    }
                                    echo '</div>';
                                }

                                echo '</div></div></div><hr>';

                                echo '</div>
                                </div></div>';
                            }
                        }
                        echo '</div>';
                        $resultCheck = Query("select * from tbl_memberskill where tbl_memberskill_user='$_GET[user]' and tbl_memberskill_contract='$_GET[contract]' ");
                        $readCheck = mysqli_fetch_array($resultCheck);
                        
                        echo '<hr><div class="container p-3"><div class="row justify-content-start">
                        <div class="col-12 col-lg-4 col-md-12 p-3">
                        <h3>'.$lang[1658].'</h3><div class="form-group">
                        <label>'.$lang[1659].'</label>
                        <input type="text" name="name" class="form-control" value="' . $readCheck['tbl_memberskill_planname'] . '" disabled>
                        </div>
                        <div class="form-group">
                        <label>'.$lang[1660].'</label>
                        <input type="text" name="comment" class="form-control" value="' . $readCheck['tbl_memberskill_comment'] . '" disabled>
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="oldatach" value="' . $readCheck['tbl_memberskill_attach'] . '" >
                        <label>'.$lang[1661]. (!empty($readCheck['tbl_memberskill_attach']) ? ' <a href="../upload/' . $readCheck['tbl_memberskill_attach'] . '" target="_blank">'.$lang[87].'</a>' : '') . '</label>
                        <input type="file" name="attach" class="form-control" disabled >
                        </div>
                        <div class="form-group">
                        <label>'.$lang[1662].'</label>
                        <input type="text" name="startdate" class="form-control datepicker" value="' . $readCheck['tbl_memberskill_startdate'] . '" disabled>
                        </div>
                        </div>';

                        echo '<div class="col-12 col-lg-4 col-md-12 p-3"></div><div class="col-12 col-lg-4 col-md-12 p-3">
                        <h3>'.$lang[1663].' </h3>
                        <div class="form-group">
                        <label>'.$lang[1664].'</label>
                        <input type="text" name="commentreport" class="form-control" value="' . $readCheck['tbl_memberskill_commentreport'] . '" disabled>
                        </div>
                        <div class="form-group">
                        <input type="hidden" name="oldatachreport" value="' . $readCheck['tbl_memberskill_attachreport'] . '" >
                        <label>'.$lang[1665]. (!empty($readCheck['tbl_memberskill_attachreport']) ? ' <a href="../upload/' . $readCheck['tbl_memberskill_attachreport'] . '" target="_blank">'.$lang[87].'</a>' : '') . '</label>
                        <input type="file" name="attachreport" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                        <label>'.$lang[1666].' </label>
                        <input type="text" name="datereport" class="form-control datepicker" value="' . $readCheck['tbl_memberskill_datereport'] . '" disabled>
                        </div>
                        </div></div></div>';

                    }
                    }

            

                    ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';
?>