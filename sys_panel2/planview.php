<?php
include './includer.php';
$show_per_page = '500';
$pagetitle = 'نمایش پلن';
$Tabpage = '90';
$thisaccess = '1,6';
include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    echo '<section class="content"><div class="col-md-12">
                    <div class="box box-body table-responsive">';


                    if (!isset($do)) {
                        $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        $resultSkillList = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$_GET[user]' and tbl_memberskill_contract='$_GET[contract]' ");
                        if(mysqli_num_rows($resultSkillList) <= 0)
                        {
                            echo '<div class="error" ><span>هنوز مهارتی انتخاب نشده است</span></div>';
                        }
                        else {
                        $readSkillList = mysqli_fetch_array($resultSkillList);
                        $skillList = explode(",", $readSkillList['tbl_memberskill_data']);
                        foreach ($skillList as $key => $value) {
                            if (!empty($value)) {
                                $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                                $readSkill = mysqli_fetch_array($resultSkill);
                                $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                                $result_for_confirm = Query("SELECT * From tbl_plan where tbl_plan_status='1' and tbl_plan_skill='$value' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$_GET[user]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");

                                echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">
                                پروژه : <a href="faragir.php?contract=' . $_GET['contract'] . '">' . $read_contract['tbl_contract_code'] . '</a>  /  
                                مهارت : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
                                
                                if (empty($do)) {
                                    echo (mysqli_num_rows($result_for_count) > 0 ? '<div class="col-12 col-gl-12 col-md-12"><div class="progress-plan"> نمودار درصد انجام شده آیتم های پلن : 
                                <div class="animated-progress progress-green">
                                    <span data-progress="' . progress(mysqli_num_rows($result_for_count), mysqli_num_rows($result_for_confirm)) . '"></span>
                                </div>
                                </div></div>' : '');
                                }

                                echo  '<div class="form-group">
                                ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                                </div>
                                <table><tr><td class="col-sm-9"></td>';
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
                                else {
                                // start show list
                                echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>مدل </th>
                                    <th>بروزرسانی</th>
                                    <th>مهلت</th>
                                    <th>وضعیت</th>
                                    </tr>
                                </thead>
                                <tbody>';
                                $k = 1;
                                while ($read = mysqli_fetch_array($result)) {
                                    $mentor = $read['tbl_plan_mentor'];
                                    $coach = $read['tbl_plan_coach'];
                                    $file = $read['tbl_plan_file'];
                                    $question = (!empty($read['tbl_plan_question']) ? json_decode($read['tbl_plan_question'], true) : '0');
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
                                        
                                        $result_course_parent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_lng ='$language_default' and  tbl_courseparent_class ='$class' ORDER BY tbl_courseparent_order DESC ,tbl_courseparent_id DESC limit 0,1");
                                        $read_course_parent = mysqli_fetch_array($result_course_parent);
                                        $resultcourse = Query("SELECT tbl_course_code From tbl_course where tbl_course_lng ='$language_default' and tbl_course_class='$read_course_parent[tbl_courseparent_code]'   ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                                        $readcourse = mysqli_fetch_array($resultcourse);
                                    }


                                    $resultSkillMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_plan='$read[tbl_plan_code]' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_skill='$read[tbl_plan_skill]' and tbl_skillmessage_user='$_GET[user]'  and tbl_skillmessage_delete ='0'  ");

                                    echo '<tr ' . ($read['tbl_plan_status'] == '1' ? 'class="confirm-row"' : (!empty($read['tbl_plan_dateenglish']) && $read['tbl_plan_dateenglish'] < date("Y/m/d") ? 'class="expire-rows"' : '')) . '>
                                    <td style="width: 1%">' . toPersianNum($k) . '</td>
                                   
                                    <td class="col-sm-2">' . ($read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233' || $read['tbl_plan_model'] == '1234' ? '<a href="' . $read['tbl_plan_link'] . '" target="_blank">' . PlanModel($read['tbl_plan_model']) . '</a>' : PlanModel($read['tbl_plan_model']));
                                        if ($read['tbl_plan_model'] == '1230')
                                        {  echo '<br><br><span class="orange">نام دوره : ' . $readClass['tbl_class_title'] . '</span>';



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
                                <div class="skill-tits">
                       درصد پیشرفت
                   </div>
                   <div class="skill margin-bottom-clear">
                                   <div class="skill-bar" data-bar="'.($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100)-100))).'"><span></span></div>
                               </div></div>';

                               
                                        }
                                        echo ($read['tbl_plan_model'] == '1235' ? '<br><br><span class="orange">مربی  : ' . $readCoach['tbl_user_name'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1236' ? '<br><br><span class="orange">محل آزمون : ' . $read['tbl_plan_azmoon'] . '</span>' : '') .
                                        ($read['tbl_plan_model'] == '1237' ? '<br><br><span class="orange">تعداد سوالات : ' . toPersianNum(count($question)) . '<br>'.($read['tbl_plan_checkexam'] == 0 ? 'شرکت نکرده است' : 'شرکت کرده است <br> تاریخ شرکت در آزمون '.$read['tbl_plan_dateexam'] ).'</span>' : '') .
                                        ($read['tbl_plan_model'] == '1231' ? '<br><br><span class="orange">فایل : <a href="upload/' . $readFile['tbl_bankfile_file'] . '" target="_blank">' . $readFile['tbl_bankfile_title'] . '</a></span>' : '') .
                                        '</td>
                                    <td class="col-sm-1">' . (!empty($read['tbl_plan_editdate']) ? toPersianNum($read['tbl_plan_editdate']) : '') . '</td>
                                    <td class="col-sm-1">
                                    ' . ($read['tbl_plan_model'] == '1230' || $read['tbl_plan_model'] == '1231' || $read['tbl_plan_model'] == '1232' || $read['tbl_plan_model'] == '1233'  ? 'مهلت پایان : ' : '') .
                                        ($read['tbl_plan_model'] == '1234' ? 'تاریخ : ' : '') .
                                        ($read['tbl_plan_model'] == '1235' ? 'تاریخ : ' : '') .
                                        ($read['tbl_plan_model'] == '1236' || $read['tbl_plan_model'] == '1237' ? 'تاریخ آزمون : ' : '') .
                                        toPersianNum($read['tbl_plan_expire']) . '</td>
                                    <td class="col-sm-1">
                                   <div class="form-group">
                                    <label>' . ($read['tbl_plan_status'] == 0 ? 'تایید نشده' : 'تایید شده') . '</label>
                                    </div>
                                    </td>
                                    </tr>';
                                    $k++;
                                }
                                echo '</tbody></table>';
                            }
                        }
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