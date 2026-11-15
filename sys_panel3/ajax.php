<?php
session_start();
include_once '../include/config.php';
include_once '../include/function.php';
include_once '../include/jdf.php';
include_once '../include/lang/panel_' . $language_default . '.php';
if (empty($_SESSION['id_login'])) {
    header("Location: login.php");
    exit();
}
if (!empty($_GET['do'])) {
    $do = $_GET['do'];
}

if (isset($do) && $do == 'planmodel') {
?>
<script type="text/javascript">
$(function() {
    $(".select2").select2();
    $(".datepicker").persianDatepicker({
        cellWidth: 38,
        cellHeight: 30,
        fontSize: 12,
    });
});
</script>

<?php
    if (isset($_POST["val"])) {
        $val = explode("-", $_POST["val"]);
        $planModel = $val[0];
        $userId = $val[1];
        $contractId = $val[2];
        $skillId = $val[3];

        if ($planModel == '1236' || $planModel == '1237' || $planModel == '1233' || $planModel == '1234') {
            $resultPlan = Query("SELECT * From tbl_plan where (tbl_plan_model ='1236' or tbl_plan_model ='1237' or tbl_plan_model ='1233' or tbl_plan_model ='1234') and tbl_plan_skill='$skillId' and tbl_plan_contract='$contractId' and tbl_plan_menti='$userId' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
            if (mysqli_num_rows($resultPlan) > 0) {
                echo '<div class="form-group planarchive"><h1>توجه تاریخ های برنامه ریزی شده این فراگیر</h1>';
                while ($readPlan = mysqli_fetch_array($resultPlan)) {
                    echo '<h2>' . PlanModel($readPlan['tbl_plan_model']) . ' / ' . ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? 'تاریخ آزمون : ' : ($readPlan['tbl_plan_model'] == '1234' ? 'تاریخ : ' : 'مهلت پایان : ')) . ' ' . toPersianNum($readPlan['tbl_plan_expire']) .
                    ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? 'ساعت شروع و پایان : '.toPersianNum($readPlan['tbl_plan_timestart']).'-'.toPersianNum($readPlan['tbl_plan_timeend']) : '') . '</h2>';
                }
                echo '</div>';
            }
        }

        if ($planModel == '1230') {
            // find member skill

            echo '<div class="form-group">
                            <label>دوره های مهارت</label>
                            <select name="class" class="form-control select2" style="width:100%"><option value="">انتخاب کنید</option>';
            $resultClass = Query("SELECT * From tbl_class where tbl_class_skill like '%$skillId%' and tbl_class_lng='$language_default' order by tbl_class_id DESC ");
            while ($readClass = mysqli_fetch_array($resultClass)) {
                echo '<option value="' . $readClass['tbl_class_code'] . '">' . $readClass['tbl_class_title'] . '</option>';
            }
            echo '</select>
                            </div>';
            for ($i = 1; $i <= $language_count; $i++) {
                echo '<div class="form-group ">
                                          <label>توضیحات<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '"
                                                                class="form-control" rows="8"  style="height:150px;"></textarea>
                                          </div>';
            }
            echo '<div class="form-group">
                            <label>مهلت پایان</label>
                            <input type="text" name="expire" class="form-control datepicker" >
                            </div>';
        }
        if ($planModel == '1231') {
            echo '<div class="form-group planhide">
            <label>انتخاب فایل</label>
            <select name="file" class="form-control select2" ><option value="">انتخاب کنید</option>';
            $resultFile = Query("SELECT * From tbl_bankfile Where tbl_bankfile_skill = '$skillId' and tbl_bankfile_lng='$language_default' and tbl_bankfile_delete = '0' order by tbl_bankfile_id DESC ");
            while ($readFile = mysqli_fetch_array($resultFile)) {
                echo '<option value="' . $readFile['tbl_bankfile_code'] . '">' . $readFile['tbl_bankfile_title'] . '</option>';
            }
            echo '</select>
            </div>';
            echo '<div class="form-group">
                            <label>مهلت پایان</label>
                            <input type="text" name="expire" class="form-control datepicker" >
                            </div>';
        }
        if ($planModel == '1232' || $planModel == '1233' || $planModel == '1234') {
            for ($i = 1; $i <= $language_count; $i++) {
                echo '<div class="form-group ">
                          <label>توضیحات<span class="language">' . $language[$i] . '</span></label>
                          <textarea name="text_' . $language[$i] . '" class="form-control" rows="8"  style="height:150px;"></textarea>
                          </div>';
            }
            echo '<div class="form-group">
                            <label>لینک </label>
                            <input type="text" name="link" class="form-control" >
                            </div>';
            echo '<div class="form-group">
                            <label>' . ($planModel == '1234' ? 'تاریخ' : 'مهلت پایان') . '</label>
                            <input type="text" name="expire" class="form-control datepicker" >
                            </div>';
        }
        if ($planModel == '1235') {
            echo '<div class="form-group planhide plan1">
            <label>انتخاب مربی </label>
            <select name="coach" class="form-control select2" ><option value="">انتخاب کنید</option>';
            $resultCoach = Query("SELECT * From tbl_user Where tbl_user_access = '6' ");
            while ($readCoach = mysqli_fetch_array($resultCoach)) {
                echo '<option value="' . $readCoach['tbl_user_id'] . '">' . $readCoach['tbl_user_name'] . '</option>';
            }
            echo '</select></div>';
            echo '<div class="form-group">
                    <label>تاریخ</label>
                    <input type="text" name="expire" class="form-control datepicker" >
                    </div>';
        }
        if ($planModel == '1236') {

            echo '<div class="form-group">
            <label>محل آزمون </label>
            <input type="text" name="azmoon" class="form-control" >
            </div>';
            echo '<div class="form-group">
            <label>ساعت شروع </label>
            <select name="timestart" class="form-control select2" >
            <option value="">انتخاب کنید</option>';
            for($i=8; $i <= 22; $i++)
            {
                echo '<option value="'.$i.'" >'.toPersianNum($i).' '.($i <=12 ? 'صبح' : ($i <=16 ? 'ظهر' : 'عصر')).'</option>';
            }
            echo '</select></div>';

            echo '<div class="form-group">
            <label>ساعت پایان  </label>
            <select name="timeend" class="form-control select2" >
            <option value="">انتخاب کنید</option>';
            for($i=8; $i <= 22; $i++)
            {
                echo '<option value="'.$i.'" >'.toPersianNum($i).' '.($i <=12 ? 'صبح' : ($i <=16 ? 'ظهر' : 'عصر')).'</option>';
            }
            echo '</select></div>';
            echo '<div class="form-group">
            <label>آدرس</label>
            <input type="text" name="location" class="form-control" >
            </div>';
            echo '<div class="form-group">
            <label>نماینده کارفرما</label>
            <input type="text" name="karfarma" class="form-control" >
            </div>';
            echo '<div class="form-group">
                            <label>تاریخ آزمون</label>
                            <input type="text" name="expire" class="form-control datepicker" >
                            </div>';
        }
        if ($planModel == '1237') {
            echo '<div class="form-group">
            <label>محل آزمون </label>
            <input type="text" name="azmoon" class="form-control" >
            </div>';
            echo '<div class="form-group">
            <label>ساعت شروع </label>
            <select name="timestart" class="form-control select2" >
            <option value="">انتخاب کنید</option>';
            for($i=8; $i <= 22; $i++)
            {
                echo '<option value="'.$i.'" >'.toPersianNum($i).' '.($i <=12 ? 'صبح' : ($i <=16 ? 'ظهر' : 'عصر')).'</option>';
            }
            echo '</select></div>';

            echo '<div class="form-group">
            <label>ساعت پایان  </label>
            <select name="timeend" class="form-control select2" >
            <option value="">انتخاب کنید</option>';
            for($i=8; $i <= 22; $i++)
            {
                echo '<option value="'.$i.'" >'.toPersianNum($i).' '.($i <=12 ? 'صبح' : ($i <=16 ? 'ظهر' : 'عصر')).'</option>';
            }
            echo '</select></div>';
            echo '<div class="form-group">
            <label>آدرس</label>
            <input type="text" name="location" class="form-control" >
            </div>';

            echo '<div class="col-lg-6">
            <h5>انتخاب از لیست سوالات</h5></div>';

            $resultQuestion = Query("SELECT * From tbl_question where tbl_question_model='0' and tbl_question_skill='$skillId' and tbl_question_lng='$language_default' and tbl_question_disable='0' ORDER BY tbl_question_order DESC , tbl_question_id DESC ");
            if (mysqli_num_rows($resultQuestion) > 0) {
                while ($readQuestion = mysqli_fetch_array($resultQuestion)) {
                    $ids = $readQuestion['tbl_question_code'];
                    echo '<div class="form-group">
                <label><input type="checkbox" name="question[]" value="' . $readQuestion['tbl_question_code'] . '" > ' . $readQuestion['tbl_question_title'] . '</label>
                </div>';
                }
            }

            echo '</div>';
            echo '<div class="form-group">
                            <label>تاریخ آزمون</label>
                            <input type="text" name="expire" class="form-control datepicker" >
                            </div>';
        }
    }
}

if (isset($do) && $do == 'planstatus') {
    $val = explode("-", $_POST['val']);
    Query("update tbl_plan set tbl_plan_status='$val[0]' where tbl_plan_code='$val[1]' ");
    echo 'آیتم بروز شد';
}

if (isset($do) && $do == 'contractstatus') {
    $val = explode("-", $_POST['val']);
    Query("update tbl_contract set tbl_contract_status='$val[0]' where tbl_contract_id='$val[1]' ");
    echo 'آیتم بروز شد';
}

if (isset($do) && $do == 'documentstatus') {
    $val = explode("-", $_POST['val']);
    Query("update tbl_document set tbl_document_status='$val[0]' where tbl_document_id='$val[1]' ");
    echo 'آیتم بروز شد';
}

if (isset($do) && $do == 'subcat') {
    $code = $_POST['code'];
    $result_festival = Query("select * from tbl_festcourse where tbl_festcourse_festival='$code' and tbl_festcourse_lng='fa' order by tbl_festcourse_id DESC");
    while ($read_festival = mysqli_fetch_array($result_festival)) {
        echo '<option value=' . $read_festival['tbl_festcourse_systemcode'] . '>' . $read_festival['tbl_festcourse_title'] . '</option>';
    }
}
if (isset($do) && $do == 'checkbox') {
    $name = $_POST["dataName"];
    $editdate = jdate("Y/m/d - H:m:s");
    $id = $_POST["dataId"];
    if ($name === 'register') {
        //data-name
        $result = Query("SELECT tbl_register_confirm From tbl_register Where tbl_register_id='$id' ");
        $read = mysqli_fetch_array($result);
        $endwork = $read['tbl_register_confirm'];
        if ($endwork == 1) {
            Query("update tbl_register set  tbl_register_confirm='0',tbl_register_confirmdate='$editdate' Where tbl_register_id='$id' ");
            echo 0;
        } else {
            Query("update tbl_register set  tbl_register_confirm='1',tbl_register_confirmdate='$editdate' Where tbl_register_id='$id' ");
            echo 1;
        }
    }
}

if (!empty($_POST['type']) && $_POST['type'] == 'cat_title') {
    $keyword = $_POST['keyword'];
    $result = Query("SELECT * FROM tbl_member where tbl_member_mobile LIKE '%$keyword%' || tbl_member_nationalcode LIKE '%$keyword%' LIMIT 0,10");
    $data = array();
    while ($row = mysqli_fetch_array($result)) {
        $title = $row['tbl_member_code'] . ',' . $row['tbl_member_name'];
        $b_title = '' . $keyword . '';
        $final_title = str_ireplace($keyword, $b_title, $title);
        array_push($data, $final_title);
    }
    echo json_encode($data);
}
if (isset($do) && $do == 'registerupdate') {
    $id = $_POST['id'];
    $date = jdate("Y/m/d a:h:s");
    $result = Query("select * from tbl_register where tbl_register_id='$id' ");
    $read = mysqli_fetch_array($result);
    $class = $read['tbl_register_class'];
    $result = Query("select * from tbl_class where tbl_class_code='$class' ");
    $read = mysqli_fetch_array($result);
    $new_count = $read['tbl_class_count'] - 1;
    Query("update tbl_class set tbl_class_count='$new_count' where tbl_class_code='$class'");

    Query("update tbl_register set tbl_register_status='1',tbl_register_dateupdate='$date',tbl_register_adminupdate='$_SESSION[id_login]' where tbl_register_id ='$id' ");
    echo '1';
}

if (isset($do) && $do == 'mentor') {
    $this_user_id = $_SESSION['id_login'];
    $status = json_decode($_POST['status']);
    $data = '';
    foreach ($status as $key => $value) {
        if (!empty($value)) {
            $data .= $value . ',';
        }
    }
    $userid = $_POST['userid'];
    $contract = $_POST['contract'];
    $date = jdate("Y/m/d a:h:s");
    $result = Query("select * from tbl_memberskill where tbl_memberskill_user='$userid' and tbl_memberskill_contract='$contract' ");
    if (mysqli_num_rows($result) > 0) {
        if(count($status) == 0)
        {
            Query("DELETE from tbl_memberskill where tbl_memberskill_user='$userid' and tbl_memberskill_contract='$contract' ");
        }
        else { Query("update tbl_memberskill set tbl_memberskill_data = '$data',tbl_memberskill_mentor = '$this_user_id',tbl_memberskill_dateupdate='$date' where tbl_memberskill_user='$userid' and tbl_memberskill_contract='$contract' "); }
    } else {
        Query("insert into tbl_memberskill (tbl_memberskill_data,tbl_memberskill_user,tbl_memberskill_contract,tbl_memberskill_mentor,tbl_memberskill_date) values('$data','$userid','$contract','$this_user_id','$date') ");
    }

    echo 'بروز شد';
}
if (isset($do) && $do == 'changebox') {
    if (isset($_POST["val"])) {

        $val = $_POST["val"];
        $result = Query("select * from tbl_work where tbl_work_systemcode = '$val' ");
        $read = mysqli_fetch_array($result);
        $box = $read['tbl_work_box'];
        if ($box == 1) {
            $newval = 0;
        } else {
            $newval = 1;
        }
        Query("update tbl_work set tbl_work_box='$newval', tbl_work_status='1' Where tbl_work_systemcode='$val'");
        echo $newval;
    }
}
if (isset($do) && $do == 'userupdate') {
    if (isset($_POST["id"])) {
        $id = $_POST["id"];
        $status = $_POST["status"];
        $result = Query("select * from tbl_member where tbl_member_id = '$id' ");
        $read = mysqli_fetch_array($result);
        $box = $read['tbl_member_showimage'];
        if ($box == 1) {
            $newval = 0;
        } else {
            $newval = 1;
        }
        Query("update tbl_member set tbl_member_showimage='$newval' Where tbl_member_id='$id'");
        echo $newval;
    }
}