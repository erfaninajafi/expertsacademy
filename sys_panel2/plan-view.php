<?php
include './includer.php';
$show_per_page = '500';
$pagetitle = $lang[1658];
$Tabpage = '90';
$thisaccess = '9';
include './header.inc.php';

checkValid($getcontract, $access, $this_user_id);

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
    $datepickerEnd = $read_contract['tbl_contract_enddate'];
    $thisUser = sqi($_GET['user']);
    $result_member = Query("SELECT * From tbl_member where tbl_member_id='$thisUser'");
    if (mysqli_num_rows($result_member) <= 0) {
        header("Location: index.php");
        exit();
    }
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
                ?>
                <div class="accordion m-2" id="accordionSkill">
                    <?php
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
                              </h2>';
                                    ?>
                    <div class="accordion-collapse collapse <?= ($key == '0' ? 'show' : '') ?>" id="collapse<?= $key ?>" aria-labelledby="heading<?= $key ?>" data-bs-parent="#accordionSkill">
                        <div class="accordion-body p-1">
                            <?php
                                            echo '<div class="col-12 col-gl-12 col-md-12 p-3"><h5>' . $lang[1337] . ' : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
                                            if (empty($do)) {
                                                echo (mysqli_num_rows($result_for_count) > 0 ? '<div class="col-12 col-gl-12 col-md-12 pb-2"><div class="progress-plan"> ' . $lang[1721] . ' : 
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
                                            ?>
                            <div class="table-responsive scrollbar">
                                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                    <thead class="text-800 bg-light">
                                        <tr>
                                            <th><?= $lang[60] ?></th>
                                            <th><?= $lang[1631] ?></th>
                                            <th><?= $lang[20] ?></th>
                                            <th><?= $lang[1602] ?></th>
                                            <th><?= $lang[1340] ?></th>
                                            <th><?= $lang[1619] ?></th>
                                            <?= ($access == '7' ? '<th>' . $lang[63] . '</th>' : '') ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
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


                                                                @$resultcourse = Query("SELECT tbl_course_code From tbl_course where tbl_course_status='1' and  tbl_course_delete='0' and tbl_course_lng ='$language_default' and tbl_course_class='$read_course_parent[tbl_courseparent_code]'   ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                                                                $readcourse = mysqli_fetch_array($resultcourse);
                                                            }


                                                            $resultSkillMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_plan='$read[tbl_plan_code]' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_skill='$read[tbl_plan_skill]' and tbl_skillmessage_user='$_GET[user]'  and tbl_skillmessage_delete ='0'  ");

                                                            echo '<tr ' . ($read['tbl_plan_status'] == '1' ? 'class="confirm-row"' : (!empty($read['tbl_plan_dateenglish']) && $read['tbl_plan_dateenglish'] < date("Y/m/d") ? 'class="expire-rows"' : '')) . '>
                                    <td class="align-middle p-2">' . $k . '</td>
                                    <td class="align-middle p-2">' . ($read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233' || $read['tbl_plan_model'] == '1234' ? '' . PlanModel($read['tbl_plan_model']) . '' : PlanModel($read['tbl_plan_model']));
                                                            if ($read['tbl_plan_model'] == '1230' && mysqli_num_rows($resultcourse) > 0 && mysqli_num_rows($resultClass) > 0) {
                                                                echo '<br><br><span class="orange">' . $lang[710] . ' : ' . $readClass['tbl_class_title'] . '</span>';
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
                                                                ($read['tbl_plan_model'] == '1237' ? '<br><br><span class="orange">' . $lang[1633] . ' : ' . toPersianNum($question) . '<br>' . ($read['tbl_plan_checkexam'] == 0 ? $lang[1634] : $lang[1635] . ' ' . $read['tbl_plan_dateexam']) . '</span>' : '');
                                                            if ($read['tbl_plan_model'] == '1231') {
                                                                echo '<br><br><span class="orange">' . $lang[52] . ' : ' . @$readFile['tbl_mohtava_title'] . '</span>';
                                                                $finalPrice += (!empty($readFile['tbl_mohtava_sellprice']) ? $readFile['tbl_mohtava_sellprice'] : 0);
                                                            }
                                                            echo '</td>
                                        <td class="align-middle p-2">' . (!empty($read['tbl_plan_editdate']) ? toPersianNum($read['tbl_plan_editdate']) : '') . '</td>

                                        <td class="align-middle p-2">
                                    ' . ($read['tbl_plan_model'] == '1230' || $read['tbl_plan_model'] == '1231' || $read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                                ($read['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                                ($read['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                                ($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                                toPersianNum($read['tbl_plan_expire']) . '</td>';



                                                            if ($read['tbl_plan_model'] == '1230') {
                                                                $resultCheckTeacher = Query("select * from tbl_opinionend where tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_class='$thisParentClass' and tbl_opinionend_model='3' and tbl_opinionend_delete='0' ");
                                                            }
                                                            echo '<td class="align-middle p-2">
                                                            <label><input  type="radio" name="status' . $read['tbl_plan_code'] . '" value="0" ' . ($read['tbl_plan_status'] == 0 ? 'checked' : '') . ' disabled> ' . $lang[1556] . '</label>
                                                            <label><input   type="radio" name="status' . $read['tbl_plan_code'] . '" value="1" ' . ($read['tbl_plan_status'] == 1 ? 'checked' : '') . ' disabled> ' . $lang[1557] . '</label>
                                                            </td>';
                                                            echo '<td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '<br>' . $lang[1641] . ' :' . toPersianNum($read['tbl_plan_adddate']) . '</td>
                                                            <td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '<br>' . $lang[1641] . ' :' . toPersianNum($read['tbl_plan_adddate']) . '</td>
                                                            ' . ($read['tbl_plan_model'] == '1230' ? '<td class="align-middle p-2"><a href="exam.php?class=' . $thisParentClass . '&user=' . $_GET['user'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1812] . '"  target="_blank"><span class="bi-list-check"></span></a></td>' : '') . '
                                                            </tr>';

                                                            $k++;
                                                        }
                                                        if ($access != '7') {
                                                            echo '<tr class="dark-price"><td colspan="5">' . $lang[1416] . '</td><td>' . (!empty($finalPrice) ? toPersianNum(number_format($finalPrice)) : toPersianNum('0')) . '</td></tr>';
                                                        }
                                                        ?>
                                </table>
                            </div>

                            <?php
                                    }
                                        ?>
                        </div>
                    </div>
                    <?php
                        }
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