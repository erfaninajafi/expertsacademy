<?php
include './includer.php';
$show_per_page = '500';
$pagetitle = 'طراحی پلن';
$Tabpage = '90';
$thisaccess = '6';
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
                    // list content

                    if (!isset($do)) {
                        $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_plan where tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'   ORDER BY tbl_plan_order DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '
                                <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>پروژه</th>
                                    <th>مهارت</th>
                                    <th>اطلاعات فراگیر</th>
                                    <th>بروزرسانی استاد راهنما</th>
                                    <th>مهلت</th>
                                    <th>وضعیت</th>
                                    <th>استاد راهنما</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $menti = $read['tbl_plan_menti'];

                            $resultMember = Query("SELECT tbl_member_fname,tbl_member_lname,tbl_member_mobile,tbl_member_nationalcode From tbl_member Where tbl_member_id='$menti' ");
                            $readMember = mysqli_fetch_array($resultMember);

                            $skill = $read['tbl_plan_skill'];

                            $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$skill' and tbl_categories_lng='$language_default' ");
                            $readSkill = mysqli_fetch_array($resultSkill);

                            $contract = $read['tbl_plan_contract'];
                            $resultContract = Query("SELECT * From tbl_contract where tbl_contract_id='$contract' ");
                            $readContract = mysqli_fetch_array($resultContract);

                            $mentor = $read['tbl_plan_mentor'];
                            $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                            $readMentor = mysqli_fetch_array($resultMentor);


                            $resultInterview = Query("SELECT * From tbl_interview where tbl_interview_model='1' and tbl_interview_contract='$contract' and tbl_interview_skill='$skill' and tbl_interview_menti='$menti' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'  ");


                            $resultMessage = Query("SELECT * From tbl_coachmessage Where tbl_coachmessage_reply='0' and tbl_coachmessage_plan='$read[tbl_plan_code]' and tbl_coachmessage_contract='$contract' and tbl_coachmessage_skill='$skill' and tbl_coachmessage_user='$menti' and tbl_coachmessage_delete ='0'");

                            $checkSkillConfirm = Query("select * from tbl_memberskill where tbl_memberskill_user='$menti' and tbl_memberskill_contract='$contract' and tbl_memberskill_startdate !='' ");
                            if (mysqli_num_rows($checkSkillConfirm) > 0) {
                                echo '<tr ' . ($read['tbl_plan_status'] == '1' ? 'class="confirm-row"' : (!empty($read['tbl_plan_dateenglish']) && $read['tbl_plan_dateenglish'] < date("Y/m/d") ? 'class="expire-rows"' : '')) . '>
                                    <td style="width: 1%">' . $i . '</td>
                                    <td class="col-sm-2">' . $readContract['tbl_contract_code'] . '</div>
                                    <td class="col-sm-2">' . $readSkill['tbl_categories_title'] . '</div>
                                    <td class="col-sm-1">
                                    <label>نام : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</label><br>
                                    <label>موبایل : ' . toPersianNum($readMember['tbl_member_mobile']) . '</label><br>
                                    <label>کدملی : ' . toPersianNum($readMember['tbl_member_nationalcode']) . '</label><br>
                                    </td>
                                    <td class="col-sm-1">' . (!empty($read['tbl_plan_editdate']) ? toPersianNum($read['tbl_plan_editdate']) : '') . '</td>
                                    <td class="col-sm-1">
                                    ' . toPersianNum($read['tbl_plan_expire']) . '</td>
                                    <td class="col-sm-1">
                                    ' . ($read['tbl_plan_status'] == 0 ? 'تایید نشده' : 'تایید شده') . '
                                    </td>
                                    <td class="col-sm-1">' . @$readMentor['tbl_user_name'] . '</div></td>
                                    </tr>
                                    <tr>
                                    <td class="col-sm-1 btn-class" colspan="8">
                                    <a href="../fa/resume/' . $menti . '/" class="btn btn-xs btn-warning">مشاهده رزومه</a>
                                    <a href="coachmessage.php?user=' . $menti . '&skill=' . $skill . '&contract=' . $contract . '&plan=' . $read['tbl_plan_code'] . '" class="btn btn-xs btn-dark">پیامها (' . toPersianNum(mysqli_num_rows($resultMessage)) . ')</a>
                                    <a href="interviewcoach.php?user=' . $menti . '&skill=' . $skill . '&contract=' . $contract . '&plan=' . $read['tbl_plan_code'] . '" class="btn btn-xs btn-success"> فعالیت عملی (' . toPersianNum(mysqli_num_rows($resultInterview)) . ')</a>
                                    <a href="planview.php?user=' . $menti . '&contract=' . $contract . '" class="btn btn-xs btn-info">نمایش پلن</a>
                                   </td>
                                    </tr>';
                                $i++;
                            }
                        }
                        echo '</tbody></table>';
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: coachlist.php?page=$page_number");
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