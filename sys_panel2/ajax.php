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

$this_user_id = $_SESSION['id_login'];
$result_member = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
$read_member = mysqli_fetch_array($result_member);
$userCode = $read_member['tbl_user_usercode'];

if (!empty($_GET['do'])) {
    $do = $_GET['do'];
}

if (isset($do) && $do == 'checkcodefile') {
    if (isset($_POST["title"])) {
        $title = $_POST['title'];
        $code = $_POST['code'];
        $row = 1;
        if ($title != '') {
            if (!empty($code)) {
                $results = Query("select * from tbl_mohtava where tbl_mohtava_code != '$code' and tbl_mohtava_codefile ='$title'  ");
            } else {
                $results = Query("select * from tbl_mohtava where  tbl_mohtava_codefile ='$title' ");
            }
        } else {
            $results = Query("select * from tbl_mohtava where tbl_mohtava_codefile ='' ");
        }

        $row = mysqli_num_rows($results); //records count
        if ($row == 0) {
            echo '<span style="color:white;background:green;padding: 2px;">This Code Is Ok</span><style>.checkname {color:white;background:green !important;border:1px solid green;padding: 2px;}</style>';
        } else {
            echo '<span style="background:red;color:#fff;padding: 2px;">Warning Duplicate Code</span><style>.checkname {color:red;border:1px solid red;padding: 2px;}</style>';
        }
    }
}

if (isset($do) && $do == 'planmodel') {

    if (isset($_POST["val"])) {
        if (!empty($_POST["val"])) {
            $val = explode("-", $_POST["val"]);
            $planModel = $val[0];
            $userId = $val[1];
            $contractId = $val[2];
            $skillId = $val[3];

            $result_contract = Query("SELECT tbl_contract_enddate,tbl_contract_startdate From tbl_contract where tbl_contract_id='$contractId' ");
            $read_contract = mysqli_fetch_array($result_contract);

            $datepickerStart = $read_contract['tbl_contract_startdate'];
            $datepickerEnd = $read_contract['tbl_contract_enddate'];


            if ($planModel == '1236' || $planModel == '1237' || $planModel == '1233' || $planModel == '1234') {
                $resultPlan = Query("SELECT * From tbl_plan where (tbl_plan_model ='1236' or tbl_plan_model ='1237' or tbl_plan_model ='1233' or tbl_plan_model ='1234') and tbl_plan_skill='$skillId' and tbl_plan_contract='$contractId' and tbl_plan_menti='$userId' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
                if (mysqli_num_rows($resultPlan) > 0) {
                    echo '<div class="form-group planarchive"><h1>' . $lang[1678] . '</h1>';
                    while ($readPlan = mysqli_fetch_array($resultPlan)) {
                        echo '<h2>' . PlanModel($readPlan['tbl_plan_model']) . ' / ' . ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : $lang[1636] . ' : ')) . ' ' . toPersianNum($readPlan['tbl_plan_expire']) .
                            ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1679] . ' : ' . toPersianNum($readPlan['tbl_plan_timestart']) . '-' . toPersianNum($readPlan['tbl_plan_timeend']) : '') . '</h2>';
                    }
                    echo '</div>';
                }
            }

            if ($planModel == '1230') {
                echo '<div class="form-group">
                    <label>' . $lang[1194] . '</label>
                    <select name="class" class="form-control select2" style="width:100%"><option value="">' . $lang[70] . '</option>';
                $resultClass = Query("SELECT * From tbl_class where tbl_class_skill like '%$skillId%' and tbl_class_lng='$language_default' order by tbl_class_id DESC ");
                while ($readClass = mysqli_fetch_array($resultClass)) {
                    echo '<option value="' . $readClass['tbl_class_code'] . '" >' . $readClass['tbl_class_title'] . '</option>';
                }
                echo '</select> </div><div class="showfile"></div>';
                for ($i = 1; $i <= $language_count; $i++) {
                    echo '<div class="form-group ">
                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                          <textarea name="text_' . $language[$i] . '" class="form-control" rows="8"  style="height:150px;"></textarea>
                          </div>';
                }
                echo '<div class="form-group">
            <label>' . $lang[1636] . '</label>
            <input type="text" name="expire" required class="form-control datepicker" >
            </div>';
            }

            if ($planModel == '1231') {
                echo '<div class="form-group planhide">
            <label>' . $lang[1675] . '</label>
            <select name="file" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                $resultFile = Query("SELECT * From tbl_mohtava Where tbl_mohtava_delete = '0' and tbl_mohtava_lng='$language_default' order by tbl_mohtava_id DESC ");
                while ($readFile = mysqli_fetch_array($resultFile)) {
                    echo '<option value="' . $readFile['tbl_mohtava_code'] . '" >' . $readFile['tbl_mohtava_codefile'] . '-' . $readFile['tbl_mohtava_title'] . '</option>';
                }
                echo '</select>
            </div><div class="showfile"></div>';
                echo '<div class="form-group">
            <label>' . $lang[1636] . '</label>
            <input type="text" name="expire" required class="form-control datepicker" >
            </div>';
            }

            if ($planModel == '1232' || $planModel == '1233' || $planModel == '1234') {
                for ($i = 1; $i <= $language_count; $i++) {
                    echo '<div class="form-group ">
                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                          <textarea name="text_' . $language[$i] . '"
                          class="form-control" rows="8" style="height:150px;"></textarea>
                          </div>';
                }
                echo '<div class="form-group">
                <label>' . $lang[83] . ' </label>
                <input type="text" name="link" class="form-control"  >
                </div>';
                echo '<div class="form-group">
                <hr>
                <label><input type="radio" name="visitmodel" value="0" checked> ' . $lang[1820] . ' </label>
                <label><input type="radio" name="visitmodel" value="1"> ' . $lang[1821] . ' </label>
                </div>';



                echo '<div class="form-group">
                <label>' . ($planModel == '1234' ? $lang[62] : $lang[1636]) . '</label>
                <input type="text" name="expire" required class="form-control datepicker" >
                </div>';
            }


            if ($planModel == '1233' || $planModel == '1234') {

                echo '<div class="form-group">
                <label>' . $lang[1822] . ' </label>
                <select name="leader" class="form-control select2" ><option value="">' . $lang[70] . '</option>';
                $resultSkill = Query("SELECT tbl_memberskill_user From tbl_memberskill Where tbl_memberskill_data like '%$skillId%' and tbl_memberskill_contract='$contractId'  ");
                while ($readSkill = mysqli_fetch_array($resultSkill)) {
                    $user = $readSkill['tbl_memberskill_user'];
                    $result_member = Query("SELECT * From tbl_member where tbl_member_id='$user'");
                    $read_member = mysqli_fetch_array($result_member);
                    echo '<option value="' . $user . '" >' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</option>';
                }
                echo '</select>
                </div>';

                echo '<div class="form-group">
                <label>' . $lang[1823] . ' </label>
                <select name="team[]" class="form-control select2" multiple="multiple">
                <option value="">' . $lang[70] . '</option>';
                $resultSkill = Query("SELECT tbl_memberskill_user From tbl_memberskill Where tbl_memberskill_data like '%$skillId%' and tbl_memberskill_contract='$contractId'  ");
                while ($readSkill = mysqli_fetch_array($resultSkill)) {
                    $user = $readSkill['tbl_memberskill_user'];
                    $result_member = Query("SELECT * From tbl_member where tbl_member_id='$user'");
                    $read_member = mysqli_fetch_array($result_member);
                    echo '<option value="' . $user . '" >' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</option>';
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
                    echo '<option value="' . $readCoach['tbl_user_id'] . '" >' . $readCoach['tbl_user_name'] . '</option>';
                }
                echo '</select>
            </div>';
                for ($i = 1; $i <= $language_count; $i++) {
                    echo '<div class="form-group ">
                <label>' . $lang[1784] . '<span class="language">' . $language[$i] . '</span></label>
                <textarea name="text_' . $language[$i] . '"
                class="form-control" rows="8" style="height:150px;"></textarea>
                </div>';
                }
                echo '<div class="form-group">
            <label>' . $lang[62] . '</label>
            <input type="text" name="expire" required class="form-control datepicker" >
            </div>';
            }


            if ($planModel == '1236') {
                echo '<div class="form-group">
            <label>' . $lang[1632] . ' </label>
            <input type="text" name="azmoon" class="form-control" >
            </div>';
                echo '<div class="form-group">
            <label>' . $lang[1669] . ' </label>
            <select name="timestart" class="form-control select2" >
            <option value="">' . $lang[70] . '</option>';
                for ($i = 8; $i <= 22; $i++) {
                    echo '<option value="' . $i . '" >' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                }
                echo '</select></div>';

                echo '<div class="form-group">
            <label>' . $lang[1670] . '  </label>
            <select name="timeend" class="form-control select2" >
            <option value="">' . $lang[70] . '</option>';
                for ($i = 8; $i <= 22; $i++) {
                    echo '<option value="' . $i . '" >' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                }
                echo '</select></div>';
                echo '<div class="form-group">
            <label>' . $lang[147] . '</label>
            <input type="text" name="location" class="form-control" >
            </div>';
                echo '<div class="form-group">
            <label>' . $lang[1668] . '</label>
            <input type="text" name="karfarma" class="form-control" >
            </div>';
                echo '<div class="form-group">
            <label>' . $lang[1637] . '</label>
            <input type="text" name="expire" required class="form-control datepicker" >
            </div>';
            }

            if ($planModel == '1237') {
                echo '<div class="form-group">
            <label>' . $lang[1632] . ' </label>
            <input type="text" name="azmoon" class="form-control"  >
            </div>';
                echo '<div class="form-group">
            <label>' . $lang[1669] . ' </label>
            <select name="timestart" class="form-control select2" >
            <option value="">' . $lang[70] . '</option>';
                for ($i = 8; $i <= 22; $i++) {
                    echo '<option value="' . $i . '" >' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                }
                echo '</select></div>';

                echo '<div class="form-group">
            <label>' . $lang[1670] . '  </label>
            <select name="timeend" class="form-control select2" >
            <option value="">' . $lang[70] . '</option>';
                for ($i = 8; $i <= 22; $i++) {
                    echo '<option value="' . $i . '" >' . toPersianNum($i) . ' ' . ($i <= 12 ? $lang[1672] : ($i <= 16 ? $lang[1673] : $lang[1674])) . '</option>';
                }
                echo '</select></div>';
                echo '<div class="form-group">
            <label>' . $lang[147] . '</label>
            <input type="text" name="location" class="form-control" >
            </div>';

                echo '<div class="col-lg-6">
            <h5>' . $lang[1671] . '</h5></div>';

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
            <label>' . $lang[1637] . '</label>
            <input type="text" name="expire" required class="form-control datepicker" >
            </div>';
            }
        } else {
            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[70] . '</div>';
        }
    }
?>
    <script>
        $(document).ready(function() {
            $("select[name='file']").on("change", function(event) {
                event.preventDefault();
                var val = $(this).val();
                $(".showfile").html("<a href=shenasname.php?do=view&code=" + val + " target=_blank>شناسنامه فایل</a>");
            });

            $("select[name='class']").on("change", function(event) {
                event.preventDefault();
                var val = $(this).val();
                $(".showfile").html("<a href=courseparent.php?code=" + val + " target=_blank>نمایش دوره</a>");
            });

            $(".select2").select2();
            $(".datepicker").persianDatepicker({
                cellWidth: 38,
                cellHeight: 30,
                fontSize: 12,
                <?php
                if (!empty($datepickerEnd)) {
                ?>
                    startDate: "<?= $datepickerStart ?>",
                    endDate: "<?= $datepickerEnd ?>"
                <?php
                }
                ?>
            });
        });
    </script>
    <?php
}

if (isset($do) && $do == 'checkmentor') {
    $mentor = sqi($_POST['mentor']);
    $contract = sqi($_POST['contract']);
    $result = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
    $read = mysqli_fetch_array($result);
    $userCode = $read['tbl_user_usercode'];
    echo '<div class="table-responsive scrollbar">
            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                <thead class="text-800 bg-light">
                    <tr>
                        <th>' . $lang[53] . '</th>
                        <th>' . $lang[92] . '</th>
                        <th>' . $lang[378] . '</th>
                        <th>' . $lang[95] . '</th>
                        <th>' . $lang[1703] . '</th>
                    </tr>
                </thead>
                <tbody>';
    $userImg = $read['tbl_user_img'];
    echo '<tr>
                    <td class="align-middle p-2">
                        <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                            <img class="rounded-circle" src="' . (!empty($userImg) ? '../upload/' . $userImg : 'assets/img/no-image.jpg') . '" >
                        </div>
                    </td>
                    <td class="align-middle p-2">' . $read['tbl_user_name'] . '</td>
                    <td class="align-middle p-2">' . $read['tbl_user_mobile'] . '</td>
                    <td class="align-middle p-2" >' . $read['tbl_user_email'] . '</td>
                    <td class="align-middle p-2" ><a href="result.php?id=' . $read['tbl_user_id'] . '"  class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1703] . '" target="_blank"><span class="bi-bar-chart-line-fill"></span></a></td>
                    </tr>
                </table>
                </div>';

    $result = Query("SELECT * From tbl_contract where tbl_contract_mentor like '%$userCode%' ORDER BY tbl_contract_id DESC ");
    if (mysqli_num_rows($result) > 0) {
        echo '<hr><div class="col-12 col-lg-12"><h4 class="mb-0">' . $lang[1925] . '</h4></div><hr>';
    ?>
        <div class="table-responsive scrollbar">
            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                <thead class="text-800 bg-light">
                    <tr>
                        <th><?= $lang[1440] ?></th>
                        <th><?= $lang[1352] ?></th>
                        <th><?= $lang[1611] ?></th>
                        <th><?= $lang[1458] ?></th>
                        <th><?= $lang[1459] ?></th>
                        <th><?= $lang[714] ?></th>
                        <th><?= $lang[1340] ?></th>
                    </tr>
                </thead>
                <tbody class="list">
                    <?php
                    while ($read =  mysqli_fetch_array($result)) {
                        $contractId = $read['tbl_contract_id'];
                        $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                        $contractIdStr = '[' . $contractId . '],';
                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
                    ?>
                        <tr>
                            <td class="align-middle pe-3 py-2  cat-code"><?= $read['tbl_contract_code'] ?></td>
                            <td class="align-middle pe-3 py-2"><?= strtoupper($read['tbl_contract_codesection']) ?></td>
                            <td class="align-middle pe-3 py-2 ">
                                <h6 class="mb-0 cat-title"><?= $read['tbl_contract_customer'] ?></h6>
                            </td>
                            <td class="align-middle subject py-2 pe-2"><?= $read['tbl_contract_startdate'] ?></td>
                            <td class="align-middle status py-2 pe-2"><?= $read['tbl_contract_enddate'] ?></td>
                            <td class="align-middle status py-2 pe-2"><?= (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') ?></td>
                            <td class="align-middle subject py-2 pe-2"><?= ($read['tbl_contract_status'] == '0' ? $lang[1568] : $lang[1569]) ?></td>

                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <hr>
    <?php
    }
    echo '<div class="col-12 col-lg-12">
                <form method="POST" action="contract.php?do=mentoradditem">
                    <input type="hidden" name="contract" value="' . $contract . '">
                    <input type="hidden" name="user" value="' . $userCode . '">';
    echo '<div class="form-group ">
                        <input type="submit" name="submit" value="' . $lang[1926] . '" class="btn btn-success mt-3" />
                    </div>
                </form>
        </div><hr>';
}
if (isset($_GET['role'])) {
    $thisrole = $_GET['role'];
    if (!is_string($thisrole) || !preg_match('/^[0-9]{1,2}$/D', $thisrole)) {
        http_response_code(400);
        exit();
    }

    $this_user_id = $_SESSION['id_login'];
    $result = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
    $read = $result ? mysqli_fetch_array($result) : null;
    if (!$read) {
        http_response_code(403);
        exit();
    }
    $role = $read['tbl_user_role'];
    $mobile = $read['tbl_user_mobile'];
    $allowedRoles = array_filter(explode(',', (string) $role), 'strlen');

    if (!in_array($thisrole, $allowedRoles, true) && $thisrole !== (string) $read['tbl_user_access']) {
        http_response_code(403);
        exit();
    }

    $_SESSION['access'] = $thisrole;
    $_SESSION['user_access'] = ($thisrole == '5' ? 'mentor' : ($thisrole == '6' ? 'coach' : ($thisrole == '7' ? 'karfarma' : ($thisrole == '8' ? 'mohtava' : ($thisrole == '9' ? 'project' : 'admin')))));
    if ($thisrole == '8') {

        header("Location: mohtava.php");
        exit();
    }
    if ($thisrole == '10') {

        $resultMember = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_mobile = '$mobile' ");
        $readMember = $resultMember ? mysqli_fetch_array($resultMember) : null;
        if (!$readMember) {
            header("Location: index.php");
            exit();
        }
        $date = jdate("Y/m/d : a:h:s");
        Query("update tbl_member set tbl_member_lastlogin='$date' where tbl_member_delete = '0' and tbl_member_id = '$readMember[tbl_member_id]' ");
        $_SESSION['userid'] = $readMember['tbl_member_id'];
        $_SESSION['username'] = $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'];
        header("Location: $site_url/fa/mypanel/");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}
if (isset($do) && $do == 'selectcontent') {
    $title = sqi($_POST['title']);
    $ghaleb = sqi($_POST['ghaleb']);
    $department = sqi($_POST['department']);
    $creator = sqi($_POST['creator']);
    $producer = sqi($_POST['producer']);

    $department = explode("-", $department);

    $select = '';

    if (!empty($title)) {
        $select .= " tbl_mohtava_title like '%$title%' and ";
    }
    if (!empty($ghaleb)) {
        $select .= " tbl_mohtava_ghaleb like '%$ghaleb%' and ";
    }
    if (!empty($creator)) {
        $select .= " tbl_mohtava_creator like '%$creator%' and ";
    }

    if (!empty($department)) {
        $select .= " tbl_mohtava_department like '%$department[0]%' and ";
    }

    if (!empty($producer)) {
        $select .= " tbl_mohtava_producer like '%$producer%' and ";
    }
    $result = Query("SELECT * FROM tbl_mohtava Where $select
    tbl_mohtava_lng='$language_default' and tbl_mohtava_statustext ='4' and 
    tbl_mohtava_delete='0' ");
    if (mysqli_num_rows($result) > 0) {
        echo '<div class="table-responsive scrollbar width-full">
                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                    <thead class="text-800 bg-light">
                            <tr>
                            <th>' . $lang[1978] . '</th>
                            <th>' . $lang[53] . '</th>
                            <th>' . $lang[1831] . '</th>
                            <th>' . $lang[1334] . '</th>
                            <th>' . $lang[1844] . '</th>
                            <th>' . $lang[1842] . '</th>
                            <th>' . $lang[1383] . '</th>
                        </tr>
                    </thead>
                    <tbody>';
        $i = 1;
        while ($read = mysqli_fetch_array($result)) {
            $resultEn = Query("SELECT tbl_mohtava_title From tbl_mohtava Where  tbl_mohtava_lng='en' and tbl_mohtava_code='$read[tbl_mohtava_code]'");
            $readEn = mysqli_fetch_array($resultEn);
            $file = explode(",", $read['tbl_mohtava_file']);


            $ext = explode(".", $file[0]);
            $ext = end($ext);




            echo '<tr>
                <td class="align-middle p-2"><input type="radio" name="select" value="' . $read['tbl_mohtava_code'] . '" required></td>

                <td class="align-middle p-2">
                <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                <img class="rounded-circle" src="' . (!empty($file[1]) ? '../upload/course/' . $file[1] : 'assets/img/no-image.jpg') . '" >
                </div>
                </td>
                <td class="align-middle p-2">' . strtoupper($read['tbl_mohtava_codefile']) . '</td>
                <td class="align-middle p-2">' . $read['tbl_mohtava_title'] . '<br>' . @$readEn['tbl_mohtava_title'] . '' . (!empty($read['tbl_mohtava_ghaleb']) ? '<br>' . $lang[1832] . ' : ' . $lang[$read['tbl_mohtava_ghaleb']] : '') . '</td>
        
                <td class="align-middle p-2">' . $read['tbl_mohtava_dateadd'] . '</td>
                <td class="align-middle p-2">' . $read['tbl_mohtava_enteshar'] . '</td>
                <td class="align-middle p-2"><a href="../' . (!empty($read['tbl_mohtava_foldername']) ? 'uploadcourse/' . $read['tbl_mohtava_foldername'] . '/' . $file[0] : 'upload/course/' . $file[0])  . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="' . $lang[58] . '" data-bs-original-title="' . $lang[58] . '" target="_blank"><span class="bi-eye-fill"></span></a></td>
                </tr>';
            $i++;
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="alert alert-warning m-3 width-full" role="alert" >' . $lang[57] . '</div>';
    }
}
if (isset($do) && $do == 'shayestegi') {
    $contract = sqi($_POST['contract']);
    $skill = sqi($_POST['skill']);
    $lng = sqi($_POST['lng']);
    $user_id = sqi($_POST['user']);


    $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1236' and tbl_plan_skill='$skill' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
    $readNumberModel = mysqli_fetch_array($resultNumberModel);
    @$firstRank = $readNumberModel['tbl_plan_rank'];
    @$firstVazn = $readNumberModel['tbl_plan_vazn'];
    $resultNumberModel = Query("SELECT tbl_plan_rank,tbl_plan_vazn From tbl_plan where tbl_plan_model='1237' and tbl_plan_skill='$skill' and tbl_plan_contract='$contract' and tbl_plan_menti='$user_id' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'   ORDER BY tbl_plan_id DESC,tbl_plan_order DESC LIMIT 0,1 ");
    $readNumberModel = mysqli_fetch_array($resultNumberModel);
    @$secondRank = $readNumberModel['tbl_plan_rank'];
    //@$secondVazn = $readNumberModel['tbl_plan_vazn'];

    // mokamel first vazn
    @$secondVazn = 100 - $firstVazn;


    if (!empty($firstRank) && !empty($secondRank)) {
        $resultCheckNumber = Query("select * from tbl_number where tbl_number_user='$user_id' and tbl_number_contract='$contract' and tbl_number_skill='$skill' ");
        if (mysqli_num_rows($resultCheckNumber) > 0) {
            $readCheckNumber = mysqli_fetch_array($resultCheckNumber);
            $shayestegi = $readCheckNumber['tbl_number_shayestegi'];
            $emtehan = $readCheckNumber['tbl_number_emtehan'];
        } else {
        }
    }


    $emtehanNumber = (($firstRank * $firstVazn) / 100) + (($secondRank * $secondVazn) / 100);
    if (!empty($firstRank) && !empty($secondRank)) {
        echo '<h3>' . $lang[1773] . '</h3><div class="emtehan">
        <label>' . $lang[1654] . ' : ' . toPersianNum($emtehanNumber) . '</label>
        ' . (!empty($shayestegi) ? '<label>' . $lang[1655] . ': ' . toPersianNum($shayestegi) . '</label>' : '');

        if (!empty($emtehan)) {
            $finalNumber = abs($emtehan - 100);
            $finalEmtehanNumber = ($emtehanNumber * $emtehan) / 100;
            $finalShayestegiNumber = ($shayestegi * $finalNumber) / 100;
            echo '<label>' . $lang[1656] . ' : ' . toPersianNum($finalEmtehanNumber + $finalShayestegiNumber) . '</label>';
        }
        echo '</div>';
    } else {
        echo '<h3>' . $lang[1773] . '</h3><div class="alert alert-warning m-3" role="alert">' . $lang[1765] . '</div>';
    }
}
if (isset($do) && $do == 'timeline') {
    $contract = sqi($_POST['contract']);
    $skill = sqi($_POST['skill']);
    $lng = sqi($_POST['lng']);
    $user_id = sqi($_POST['user']);
    ?>
    <div class="mb-3">
        <h3><?= $lang[1708] ?></h3>
        <div class="timeline-vertical">
            <?php
            $result = Query("SELECT * From tbl_plan where tbl_plan_contract='$contract' and tbl_plan_skill ='$skill' and tbl_plan_delete='0' and tbl_plan_menti='$user_id' and tbl_plan_lng='$lng' ORDER BY tbl_plan_expire ASC ");
            if (mysqli_num_rows($result) > 0) {
                $i = 1;
                while ($read =  mysqli_fetch_array($result)) {
                    $now = strtotime(date("Y/m/d"));
                    $endate = strtotime($read['tbl_plan_dateenglish']);

                    $class = $read['tbl_plan_class'];
                    $resultClass = Query("SELECT * From tbl_class where tbl_class_lng='$lng' and tbl_class_code ='$class' ");
                    $readClass = mysqli_fetch_array($resultClass);


                    $result_course_parent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_lng ='$lng' and  tbl_courseparent_class ='$class' ORDER BY tbl_courseparent_order ASC ,tbl_courseparent_id ASC limit 0,1");
                    $read_course_parent = mysqli_fetch_array($result_course_parent);
                    $resultcourse = Query("SELECT tbl_course_code From tbl_course where tbl_course_status='1' and  tbl_course_delete='0' and tbl_course_lng ='$lng' and tbl_course_class='$read_course_parent[tbl_courseparent_code]'   ORDER BY tbl_course_order DESC , tbl_course_id DESC limit 0,1");
                    $readcourse = mysqli_fetch_array($resultcourse);

                    if ($read['tbl_plan_model'] == '1230' && mysqli_num_rows($resultcourse) > 0 && mysqli_num_rows($resultClass) > 0) {


                        @$result_parent = Query("SELECT tbl_course_class From tbl_course where tbl_course_code ='$readcourse[tbl_course_code]' and tbl_course_lng='$lng' ");
                        if (mysqli_num_rows($result_parent) > 0) {
                            $read_parent = mysqli_fetch_array($result_parent);
                            $thisParent = $read_parent['tbl_course_class'];

                            $result_class = Query("SELECT tbl_courseparent_class From tbl_courseparent where  tbl_courseparent_lng='$lng' and tbl_courseparent_code ='$thisParent' ");
                            $read_class = mysqli_fetch_array($result_class);
                            $thisParentClass = $read_class['tbl_courseparent_class'];

                            $read_lesson = 0;
                            $all_lesson = 0;

                            $resultParent = Query("SELECT tbl_courseparent_code From tbl_courseparent where tbl_courseparent_class='$thisParentClass' and tbl_courseparent_lng ='$lng' ORDER BY tbl_courseparent_order DESC , tbl_courseparent_id DESC ");
                            while ($readParent = mysqli_fetch_array($resultParent)) {

                                $resultcourseclass = Query("SELECT * From tbl_course where tbl_course_class='$readParent[tbl_courseparent_code]' and tbl_course_lng ='$lng'  ");
                                while ($readcourseclass = mysqli_fetch_array($resultcourseclass)) {
                                    $all_lesson += 1;
                                    $resultview_count = Query("SELECT * From tbl_view where tbl_view_class='$readcourseclass[tbl_course_code]' and tbl_view_user ='$user_id'  ");
                                    if (mysqli_num_rows($resultview_count) > 0) {
                                        $read_lesson += 1;
                                    }
                                }
                            }
                        } else {
                            $read_lesson = 0;
                            $all_lesson = 0;
                        }
                    }

                    // echo '<tr '.(($endate - $now) <= 259200 &&  $read['tbl_plan_status'] == '0' ? 'class="expire-soon"' : '').'>
                    // <td class="align-middle subject py-2 pe-4">
                    //                 ' . PlanModel($read['tbl_plan_model']).'</td>';
                    //     echo '</tr>';

                    echo  '<div class="timeline-item timeline-item-' . ($i % 2 == 1 ? 'start' : 'end') . '">
                            <div class="timeline-icon icon-item icon-item-lg text-' . ($now > $endate && $read['tbl_plan_status'] == '0'  ? 'warning' : ($now > $endate  ? 'secondary' : 'primary')) . ' border-300"><span class="fs-1 fas fa-flag"></span></div>
                            <div class="row">
                              <div class="col-lg-6 timeline-item-time">
                                <div>
                                  <p class="fs--2 text-600 pt-2">' . $lang[1459] . ' : ' . $read['tbl_plan_expire'] . '</p>
                                </div>
                              </div>
                              <div class="col-lg-6">
                                <div class="timeline-item-content">
                                  <div class="timeline-item-card" >
                                    <h6 class="mb-2">' . PlanModel($read['tbl_plan_model']) . ($read['tbl_plan_model'] == '1230' && mysqli_num_rows($resultcourse) > 0 && mysqli_num_rows($resultClass) > 0 ? '<br>' . $readClass['tbl_class_title'] . '<br>' . $lang[1772] . '<br>' . ($read_lesson == 0 ? $read_lesson : round(abs(((($all_lesson - $read_lesson) / $all_lesson) * 100) - 100))) . '%' : '') . '</h6>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>';
                    $i++;
                }
            } else {
                echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1765] . '</div>';
            } ?>

        </div>
    </div>

<?php
}
if (isset($do) && $do == 'description') {
    $val = sqi($_POST['val']);
    $val = explode('-', $val);

    $result = Query("SELECT * From tbl_plan where tbl_plan_code ='$val[0]' and tbl_plan_delete='0' and tbl_plan_lng='$val[1]'");
    if (mysqli_num_rows($result) > 0) {
        $readPlan = mysqli_fetch_array($result);
        echo '<h6 class="fs-0 mb-2">' . $lang[1436] . ' : ' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</h6>';
        echo '<p class="mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
            ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
            toPersianNum($readPlan['tbl_plan_expire']) . '</p>';
        echo '<p>' . $readPlan['tbl_plan_text'] . '</p>';
    }
}


if (isset($do) && $do == 'desinterview') {
    $val = sqi($_POST['val']);
    $val = explode('-', $val);

    $result = Query("SELECT * From tbl_interview where tbl_interview_code ='$val[0]' and tbl_interview_delete='0' and tbl_interview_lng='$val[1]'");
    if (mysqli_num_rows($result) > 0) {
        $readPlan = mysqli_fetch_array($result);
        $file = explode(",", $readPlan['tbl_interview_file']);

        echo '<h6 class="fs-0 mb-2">' . $readPlan['tbl_interview_title'] . '</h6>';
        echo '<p class="mb-1">' . $lang[1802] . ' : ' .
            toPersianNum($readPlan['tbl_interview_date']) . '</p>';
        echo '<p>' . $readPlan['tbl_interview_text'] . '</p>';
        foreach ($file as $key => $value) {
            if (!empty($value)) {
                echo '<a href="../upload/' . $value . '" class="btn me-1 mb-1 btn-sm  btn-success">' . $lang[98] . '</a>';
            }
        }
    }
}


if (isset($do) && $do == 'ajaxchecklist') {
    $getcontract = strip_tags(sqi($_POST['contract']));
    $lng = strip_tags(sqi($_POST['lng']));

    $this_user_id = $_SESSION['id_login'];
    $result_member = Query("select * from tbl_user where tbl_user_id = '$this_user_id' ");
    $read_member = mysqli_fetch_array($result_member);

    if (empty($_SESSION['access'])) {
        $access = $read_member['tbl_user_access'];
    } else {
        $access = $_SESSION['access'];
    }

?>
    <div class="row gx-3 mb-3">
        <div class="col-xxl-12 col-xl-12 p-0">
            <div class="card">
                <div class="card-header border-bottom border-200 px-0">
                    <div class="d-lg-flex justify-content-between">
                        <div class="row flex-between-center gy-2 px-x1">
                            <div class="col-auto pe-0">
                                <h4 class="mb-0"><?= $lang[1927] ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 position-relative">
                    <div class="table-responsive scrollbar">
                        <?php
                        ?>
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th><?= $lang[60] ?></th>
                                    <th><?= $lang[1944] ?> </th>
                                    <th><?= $lang[1459] ?> </th>
                                    <?= ($access == '9' ? '<th>' . $lang[1340] . ' </th>' : '') ?>
                                    <?= ($access == '9' ? '<th>' . $lang[1953] . ' </th>' : '') ?>
                                    <?= ($access == '9' ? '<th>' . $lang[1954] . ' </th>' : '') ?>
                                </tr>
                            </thead>
                            <tbody class="list">
                                <tr>
                                    <?php
                                    $result = Query("SELECT * From tbl_checklist where tbl_checklist_contract ='$getcontract' and  tbl_checklist_admin='$userCode' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default' order by tbl_checklist_order ASC , tbl_checklist_order DESC  ");
                                    $i = 1;
                                    while ($read = mysqli_fetch_array($result)) {
                                        $list = $read['tbl_checklist_list'];
                                        if ($list == '0') {
                                            $thisTitle = $read['tbl_checklist_title'];
                                        } else {
                                            $result_checklistdefault = Query("SELECT tbl_checklistdefault_title From tbl_checklistdefault where tbl_checklistdefault_code='$list' and tbl_checklistdefault_lng='$language_default'  ");
                                            $read_checklistdefault = mysqli_fetch_array($result_checklistdefault);
                                            $thisTitle = $read_checklistdefault['tbl_checklistdefault_title'];
                                        }


                                        $result_report_confirm = Query("select * from tbl_checklistreport where tbl_checklistreport_status='1' and tbl_checklistreport_listcode ='$read[tbl_checklist_code]' and tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");
                                        $result_report_notconfirm = Query("select * from tbl_checklistreport where  tbl_checklistreport_status='0' and tbl_checklistreport_listcode ='$read[tbl_checklist_code]' and tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");
                                        $count = mysqli_num_rows($result_report_confirm) + mysqli_num_rows($result_report_notconfirm);
                                        $persent = (mysqli_num_rows($result_report_confirm) == 0 ? mysqli_num_rows($result_report_confirm) : round(abs(((($count - mysqli_num_rows($result_report_confirm)) / $count) * 100) - 100)));
                                        echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                       
                        <td class="align-middle p-2">' . $thisTitle . '</td>
                        <td class="align-middle p-2">' . $read['tbl_checklist_expire'] . '</td>
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . ($read['tbl_checklist_confirm'] == '1' ? $lang[1557] : $lang[1556]) . '</td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . mysqli_num_rows($result_report_confirm) . '</td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . mysqli_num_rows($result_report_notconfirm) . '</td>' : '') . '
                        </tr>';
                                        $i++;
                                    }
                                    ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}

if (isset($do) && $do == 'contract') {
    $contract = strip_tags(sqi($_POST['contract']));
    $lng = strip_tags(sqi($_POST['lng']));
    $this_user_id = $_SESSION['id_login'];
?>

    <?php


    $result = Query("SELECT * From tbl_contract where tbl_contract_id='$contract' and tbl_contract_mentor like '%$userCode%' ");
    if (mysqli_num_rows($result) > 0) {


        $read =  mysqli_fetch_array($result);
        $contractId = $read['tbl_contract_id'];
        $contractIdStr = '[' . $contractId . '],';
        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
        $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
        $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
        $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
        $resultPlanCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$contractId' ");
        $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");

        $allFaragir = mysqli_num_rows($result_member);
        $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
        $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
        $noPlan = mysqli_num_rows($resultPlan);
        $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
        $endPlan =  mysqli_num_rows($resultCheckEndPlan);
        $thisCustomer = $read['tbl_contract_customer'];
        if ($allFaragir > 0) {
    ?>

            <div class="row gx-3 mb-3">
                <div class="col-xxl-12 col-xl-12 p-0">
                    <div class="card">
                        <div class="card-header border-bottom border-200 px-0">
                            <div class="d-lg-flex justify-content-between">
                                <div class="row flex-between-center gy-2 px-x1">
                                    <div class="col-auto pe-0">
                                        <h4 class="mb-0"><?= $lang[1711] . ' ' . $thisCustomer ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                        </div>
                        <div class="card-body p-0 position-relative">
                            <div class="table-responsive scrollbar">
                                <?php
                                ?>
                                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                    <thead class="text-800 bg-light">
                                        <tr>
                                            <th><?= $lang[1711] ?></th>
                                            <th><?= $lang[714] ?></th>
                                            <th><?= $lang[1712] ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        <tr>
                                            <td class="align-middle pe-2 py-2  "><?= $lang[1713] ?></td>
                                            <td class="align-middle pe-2 py-2 "><?= $allFaragir - $allFaragirHaveQuestion ?></td>
                                            <td class="align-middle subject py-2 ">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar rounded-pill" style="width: <?= abs((($allFaragir - $allFaragirHaveQuestion) / $allFaragir * 100) - 100) ?>%;"></div>
                                                    </div>
                                                    <div class="fw-semi-bold ms-2"><?= abs((($allFaragir - $allFaragirHaveQuestion) / $allFaragir * 100) - 100) ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle pe-2 py-2  "><?= $lang[1714] ?></td>
                                            <td class="align-middle pe-2 py-2 "><?= ($allFaragir - $allFaragirHaveQuestionCheck) ?></td>
                                            <td class="align-middle subject py-2 ">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar rounded-pill" style="width: <?= abs((($allFaragir - $allFaragirHaveQuestionCheck) / $allFaragir * 100) - 100) ?>%;"></div>
                                                    </div>
                                                    <div class="fw-semi-bold ms-2"><?= abs((($allFaragir - $allFaragirHaveQuestionCheck) / $allFaragir * 100) - 100) ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle pe-2 py-2  "><?= $lang[1715] ?></td>
                                            <td class="align-middle pe-2 py-2 "><?= $eblaghNashode ?></td>
                                            <td class="align-middle subject py-2 ">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar rounded-pill" style="width: <?= abs((($allFaragir - $eblaghNashode) / $allFaragir * 100) - 100) ?>%;"></div>
                                                    </div>
                                                    <div class="fw-semi-bold ms-2"><?= abs((($allFaragir - $eblaghNashode) / $allFaragir * 100) - 100) ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="align-middle pe-2 py-2  "><?= $lang[1717] ?></td>
                                            <td class="align-middle pe-2 py-2 "><?= ($allFaragir - $endPlan) ?></td>
                                            <td class="align-middle subject py-2 ">
                                                <div class="d-flex align-items-center">
                                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                                        <div class="progress-bar rounded-pill" style="width: <?= abs((($allFaragir - $endPlan) / $allFaragir * 100) - 100) ?>%;"></div>
                                                    </div>
                                                    <div class="fw-semi-bold ms-2"><?= abs((($allFaragir - $endPlan) / $allFaragir * 100) - 100) ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                <?php
                $now = date("Y/m/d");
                $next_days = strtotime('+10 days');
                $nextTenDays = date('Y/m/d', $next_days);
                $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$lng' and tbl_plan_contract='$contract' and tbl_plan_mentor ='$this_user_id' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' ");

                ?>
                <div class="col-lg-12 col-12 mt-3 p-0">
                    <div class="card height-one">
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(https://expertsacademy.ir/assets/img/icons/spot-illustrations/corner-1.png);">
                        </div>
                        <div class="card-body p-0 position-relative">
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-12  p-3 mb-2">
                                    <h4><?= $lang[1720] . ' ' . $thisCustomer ?></h4>
                                    <div class="box-content mt-2">
                                        <?php
                                        if (mysqli_num_rows($resultPlan) <= 0) {
                                            echo '<p>' . $lang[1768] . '</p>';
                                        } else {
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                $contractId = $readPlan['tbl_plan_contract'];
                                                $faragir = $readPlan['tbl_plan_menti'];

                                                $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                $readContract =  mysqli_fetch_array($resultContract);

                                                $resultMember = Query("select * from tbl_member where tbl_member_id ='$faragir' ");
                                                $readMember =  mysqli_fetch_array($resultMember);
                                                $a = jdate_words(array('mm' => $expire[1]));
                                                echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                        <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                        <div class="flex-1 position-relative ps-3">
                                            <h6 class="fs-0 mb-2"><a href="plan.php?user=' . $readPlan['tbl_plan_menti'] . '&contract=' . $contractId . '" target="_blank">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                echo '<p class="fs-0 mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                    toPersianNum($readPlan['tbl_plan_expire']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                        </div>
                                    </div><hr>';
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

    <?php }
    } ?>


    </div>
<?php
}

if (isset($do) && $do == 'ajaxkarfarmamenti') {
?>
    <script>
        $(document).ready(function() {
            $(".shayestegi").on("click", function() {
                var contract = $(this).attr("data-contract");
                var skill = $(this).attr("data-skill");
                var lng = $(this).attr("data-lng");
                var user = $(this).attr("data-user");
                $(".modal-body").html("");
                return (
                    $.ajax({
                        type: "POST",
                        url: (window.location.pathname.indexOf("/expertsacademy/") === 0 ? "/expertsacademy/" : "/") + "sys_panel2/ajax.php?do=shayestegi",
                        data: {
                            contract: contract,
                            skill: skill,
                            lng: lng,
                            user: user
                        },
                        dataType: "html",
                        success: function(data) {
                            $(".modal-body").html(data);
                            $("#result").modal("show");
                        },
                    }),
                    !1
                );
            });
        });
    </script>
    <?php
    $getcontract = strip_tags(sqi($_POST['contract']));
    $language_default = strip_tags(sqi($_POST['lng']));
    $firstValue = strip_tags(sqi($_POST['skill']));
    $this_user_id = $_SESSION['id_login'];


    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$firstValue' and tbl_categories_lng='$language_default' ");
    $readSkill =  mysqli_fetch_array($resultSkill);
    $result = Query("select * from tbl_memberskill where tbl_memberskill_data like '%$firstValue%' and tbl_memberskill_contract = '$getcontract'  ");
    if (mysqli_num_rows($result) > 0) {
    ?>

        <div class="row gx-3 mb-2">
            <div class="col-xxl-12 col-xl-12 p-0">
                <div class="card">
                    <div class="card-header border-bottom border-200 px-0">
                        <div class="d-lg-flex justify-content-between">
                            <div class="row flex-between-center gy-2 px-x1">
                                <div class="col-auto pe-0">
                                    <h4 class="mb-0"><?= $readSkill['tbl_categories_title'] ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div class="table-responsive scrollbar">

                            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                <thead class="text-800 bg-light">
                                    <tr>
                                        <th><?= $lang[53] ?></th>
                                        <th><?= $lang[127] ?></th>
                                        <th><?= $lang[385] ?></th>
                                        <th><?= $lang[1771] ?></th>
                                        <th><?= $lang[1340] ?></th>
                                        <th><?= $lang[1772] ?></th>
                                        <th><?= $lang[1709] ?></th>
                                    </tr>
                                </thead>
                                <tbody class="list">
                                    <?php
                                    while ($read = mysqli_fetch_array($result)) {
                                        $memberContract = $read['tbl_memberskill_contract'];
                                        $mentor = $read['tbl_memberskill_mentor'];
                                        $resultMentor = Query("select tbl_user_name from tbl_user where tbl_user_id = '$mentor' ");
                                        $readMentor = mysqli_fetch_array($resultMentor);
                                        $userId = $read['tbl_memberskill_user'];
                                        $resultMember = Query("SELECT * From tbl_member where tbl_member_id='$userId' ");
                                        $readMember = mysqli_fetch_array($resultMember);
                                        $resultNumber = Query("select * from tbl_number where tbl_number_skill='$firstValue' and tbl_number_user='$userId' and tbl_number_contract='$memberContract' ");
                                        echo '<tr>
                                        <td class="align-middle p-2">
                                        <div class="notification-avatar">
                                        <div class="avatar avatar-xl me-3">
                                            <img class="rounded-circle" src="' . (!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                        </div>
                                        </div>
                                        </td>
                                        <td class="align-middle p-2">
                                        ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</td>
                                        <td class="align-middle p-2">' . toPersianNum($readMember['tbl_member_nationalcode']) . '</td>
                                        <td class="align-middle p-2">' . $readMentor['tbl_user_name'] . '</td>
                                        <td class="align-middle p-2">' . (mysqli_num_rows($resultNumber) <= 0 ? '<small class="badge rounded badge-subtle-danger false" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="' . $lang[1556] . '" data-bs-original-title="' . $lang[1556] . '"><span class="bi-check-circle"></span></small>' : '<small class="badge rounded badge-subtle-success false" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="' . $lang[712] . '" data-bs-original-title="تایید شده"><span class="bi-check-circle-fill"></span></small>') . '</td>';

                                        $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$userId' and tbl_plan_contract ='$memberContract' and tbl_plan_skill='$firstValue'");
                                        $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$userId' and tbl_plan_contract ='$memberContract' and tbl_plan_skill='$firstValue'");
                                        $count = mysqli_num_rows($resultPlan);
                                        $confirm = mysqli_num_rows($resultPlanConfirm);
                                        $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100)));
                                        echo '<td class="align-middle priority pe-4">
                                                    <div class="d-flex align-items-center gap-2">
                                                      <div style="--falcon-circle-progress-bar: ' . $persent . '">
                                                        <svg
                                                          class="circle-progress-svg"
                                                          width="26"
                                                          height="26"
                                                          viewBox="0 0 120 120"
                                                        >
                                                          <circle
                                                            class="progress-bar-rail"
                                                            cx="60"
                                                            cy="60"
                                                            r="54"
                                                            fill="none"
                                                            stroke-linecap="round"
                                                            stroke-width="12"
                                                          ></circle>
                                                          <circle
                                                            class="progress-bar-top"
                                                            cx="60"
                                                            cy="60"
                                                            r="54"
                                                            fill="none"
                                                            stroke-linecap="round"
                                                            stroke="' . ($persent <= 30 ? '#e63757' : ($persent <= 50 ? '#F68F57' : ($persent <= 75 ? '#2A7BE4' : '#00D27B'))) . '"
                                                            stroke-width="12"
                                                          ></circle>
                                                        </svg>
                                                      </div>
                                                      <h6 class="mb-0 text-700">' . toPersianNum($persent) . '%</h6>
                                                    </div>
                                                  </td>';


                                        echo '<td class="align-middle p-2">
                                        <a href="#" data-user="' . $userId . '" data-contract="' . $memberContract . '" data-skill="' . $firstValue . '" data-lng="' . $language_default . '" class=" timelinecontent btn me-1 mb-1 btn-sm  btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1708] . '"><span class="bi-bar-chart-steps"></span></a>
                                        <a href="arzyabi-list.php?do=answer&id=' . $userId . '&contract=' . $memberContract . '" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1751] . '"><span class="bi-pie-chart-fill"></span></a>
                                        <a href="#" class="shayestegi btn me-1 mb-1 btn-sm  btn-success" data-user="' . $userId . '" data-contract="' . $memberContract . '" data-skill="' . $firstValue . '" data-lng="' . $language_default . '" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1773] . '"><span class="bi-person-check-fill"></span></a>
                                        </td>
                                            </tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php }
}
if (isset($do) && $do == 'karfarma') {
    $contract = strip_tags(sqi($_POST['contract']));
    $lng = strip_tags(sqi($_POST['lng']));
    $this_user_id = $_SESSION['id_login'];

    $thisId = $contract;
    $result = Query("SELECT * From tbl_contract where tbl_contract_id='$thisId' ORDER BY tbl_contract_id DESC limit 0,1");
    if (mysqli_num_rows($result) > 0) {

        $read =  mysqli_fetch_array($result);
        $contractId = $read['tbl_contract_id'];
        $contractIdStr = '[' . $contractId . '],';
        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
        $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' ");
        $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$this_user_id' ");
        $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
        $resultPlanCheck = Query("select tbl_memberskill_id,tbl_memberskill_data from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$contractId' ");
        $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");

        $allFaragir = mysqli_num_rows($result_member);
        $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
        $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
        $noPlan = mysqli_num_rows($resultPlan);
        $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
        $endPlan =  mysqli_num_rows($resultCheckEndPlan);
        $thisCustomer = $read['tbl_contract_customer'];



        $resultShayeste = Query("select DISTINCT tbl_number_user from tbl_number where tbl_number_shayestegi !='0' and tbl_number_contract='$contractId' ");

        $allSkill = array();
        while ($readSkill = mysqli_fetch_array($resultPlanCheck)) {
            $skillData = explode(",", $readSkill['tbl_memberskill_data']);
            foreach ($skillData as $key => $value) {
                if (!empty($value)) {
                    $allSkill[$value] = $value;
                }
            }
        }

        // echo 'A = arzyabi shodan'. $itemOne = $allFaragir-$allFaragirHaveQuestion;
        // echo '<br>B = plan daran'. mysqli_num_rows($resultPlanCheck);
        // echo '<br>c = plan tamom shode'. mysqli_num_rows($resultCheckEndPlan);
        // echo '<br>d = shayestegi'. mysqli_num_rows($resultShayeste);
        if ($allFaragir > 0) {
        ?>

            <div class="row">
                <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
                    <div class="card">

                        <div class="card-body pt-0 height-380">
                            <div class="row mb-2 p-3">
                                <div class="col-6 border-end border-200">
                                    <h4 class="mb-0"><?= toPersianNum(mysqli_num_rows($result_member)) ?></h4>
                                    <p class="fs--1 text-600 mb-0"><?= $lang[1752] ?></p>
                                </div>
                                <div class="col-3 border-end text-center border-200">
                                    <h4 class="fs-0 mb-0"><?= toPersianNum(count($allSkill)) ?></h4>
                                    <p class="fs--1 text-600 mb-0"><?= $lang[1753] ?></p>
                                </div>
                                <div class="col-3 text-center">
                                    <h4 class="fs-0 mb-0"><?= toPersianNum(abs(mysqli_num_rows($resultShayeste))) ?></h4>
                                    <p class="fs--1 text-600 mb-0"><?= $lang[1754] ?></p>
                                </div>
                            </div>
                            <!-- <div class="progress-stacked overflow-visible mt-4 rounded-3" style="height: 6px;">
                        <div class="progress" role="progressbar" style="width: 25%; height: 6px;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                          <div class="overflow-visible progress-bar bg-progress-gradient border-end border-white border-2 rounded-end rounded-pill"><span class="mt-n4 text-900 fw-bold">25%</span></div>
                        </div>
                        <div class="progress" role="progressbar" style="width: 45%; height: 6px;" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100">
                          <div class="overflow-visible progress-bar bg-secondary border-end border-white border-2"><span class="mt-n4 text-900 fw-bold">45%</span></div>
                        </div>
                        <div class="progress" role="progressbar" style="width: 15%; height: 6px;" aria-valuenow="15" aria-valuemin="0" aria-valuemax="100">
                          <div class="overflow-visible progress-bar bg-warning border-end border-white border-2"><span class="mt-n4 text-900 fw-bold">15%</span></div>
                        </div>
                        <div class="progress" role="progressbar" style="width: 15%; height: 6px;" aria-valuenow="15" aria-valuemin="15" aria-valuemax="100">
                          <div class="overflow-visible progress-bar bg-info rounded-start rounded-pill"><span class="mt-n4 text-900 fw-bold">15%</span></div>
                        </div>
                      </div> -->

                            <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2 mt-3"><a href="#!">
                                    <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-primary" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                        </svg><?= $lang[1756] ?></h6>
                                </a><a class="fs--2 text-600 mb-0" href="#!">
                                    <?php
                                    if ($allFaragir != 0) {
                                        $darsad = round(abs((($allFaragir - $allFaragirHaveQuestion) / $allFaragir) * 100));
                                    } else {
                                        $darsad = 0;
                                    }
                                    ?>
                                    <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'text-primary' : ($darsad <= '75' ? 'text-primary' : 'text-primary')) ?>" style="width: <?= $darsad ?>%"></div>
                                    </div>
                                    <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                </a>
                            </div>

                            <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                                    <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-warning" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                        </svg><?= $lang[1757] ?></h6>
                                </a>
                                <a class="fs--2 text-600 mb-0" href="#!">

                                    <?php
                                    if ($allFaragir != 0) {
                                        $darsad = round(abs((($allFaragir - mysqli_num_rows($resultPlanCheck)) / $allFaragir) * 100));
                                    } else {
                                        $darsad = 0;
                                    }
                                    ?>
                                    <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-warning' : ($darsad <= '75' ? 'bg-warning' : 'bg-warning')) ?>" style="width: <?= $darsad ?>%"></div>
                                    </div>
                                    <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                </a>

                            </div>


                            <div class="d-flex flex-between-center rounded-3 bg-light p-3 mb-2"><a href="#!">
                                    <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-secondary" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                        </svg><?= $lang[1758] ?></h6>
                                </a>
                                <a class="fs--2 text-600 mb-0" href="#!">

                                    <?php
                                    if ($allFaragir != 0) {
                                        $darsad = round(abs(100 - (($allFaragir - mysqli_num_rows($resultCheckEndPlan)) / $allFaragir) * 100));
                                    } else {
                                        $darsad = 0;
                                    }
                                    ?>
                                    <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-secondary' : ($darsad <= '75' ? 'bg-secondary' : 'bg-secondary')) ?>" style="width: <?= $darsad ?>%"></div>
                                    </div>
                                    <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                </a>

                            </div>
                            <div class="d-flex flex-between-center rounded-3 bg-light p-3"><a href="#!">
                                    <h6 class="mb-0"><svg class="svg-inline--fa fa-circle fa-w-16 fs--1 me-3 text-info" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="circle" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                            <path fill="currentColor" d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8z"></path>
                                        </svg><?= $lang[1759] ?></h6>
                                </a>
                                <a class="fs--2 text-600 mb-0" href="#!">

                                    <?php
                                    if ($allFaragir != 0) {
                                        $darsad = round(abs(100 - (($allFaragir - mysqli_num_rows($resultShayeste)) / $allFaragir) * 100));
                                    } else {
                                        $darsad = 0;
                                    }
                                    ?>
                                    <div class="progress progress-index" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar rounded-3 <?= ($darsad <= '50' ? 'bg-info' : ($darsad <= '75' ? 'bg-info' : 'bg-info')) ?>" style="width: <?= $darsad ?>%"></div>
                                    </div>
                                    <p class="mt-1 p-0 mb-0 f2-11"><?= $darsad ?>%</p>
                                </a>

                            </div>
                        </div>
                    </div>
                </div>

                <?php

                $now = date("Y/m/d");
                $next_days = strtotime('+10 days');
                $nextTenDays = date('Y/m/d', $next_days);
                $resultPlan = Query("select * from tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_status='0' and tbl_plan_namayandeh='1' and tbl_plan_dateenglish BETWEEN '$now' and '$nextTenDays' order by tbl_plan_dateenglish ASC ");

                ?>
                <div class="col-lg-6 col-xl-6 col-xxl-6 mb-2 p-lg-2 ">
                    <div class="card">
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(https://expertsacademy.ir/assets/img/icons/spot-illustrations/corner-1.png);">
                        </div>
                        <div class="card-body p-0 position-relative height-380">
                            <div class="row g-0 justify-content-center">
                                <div class="col-lg-12  p-3 mb-2">
                                    <h4><?= $lang[1720] ?></h4>
                                    <div class="box-content-two mt-2">
                                        <?php
                                        if (mysqli_num_rows($resultPlan) <= 0) {
                                            echo '<p>' . $lang[1768] . '</p>';
                                        } else {
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                $expire = explode("/", $readPlan['tbl_plan_expire']);
                                                $contractId = $readPlan['tbl_plan_contract'];
                                                $faragir = $readPlan['tbl_plan_menti'];

                                                $resultContract = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                                                $readContract =  mysqli_fetch_array($resultContract);

                                                $resultMember = Query("select * from tbl_member where tbl_member_id ='$faragir' ");
                                                $readMember =  mysqli_fetch_array($resultMember);
                                                $a = jdate_words(array('mm' => $expire[1]));
                                                echo '<div class="d-flex btn-reveal-trigger mb-2 mt-2">
                                            <div class="calendar"><span class="calendar-month">' . $a['mm'] . '</span><span class="calendar-day">' . toPersianNum($expire[2]) . '</span></div>
                                            <div class="flex-1 position-relative ps-3">
                                                <h6 class="fs-0 mb-2"><a class="item-info"  href="#" data-value="' . $readPlan['tbl_plan_code'] . '-' . $language_default . '">' . ($readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233' || $readPlan['tbl_plan_model'] == '1234' ? PlanModel($readPlan['tbl_plan_model']) : PlanModel($readPlan['tbl_plan_model'])) . '</a></h6>';
                                                echo '<p class="fs-0  mb-1">' . ($readPlan['tbl_plan_model'] == '1230' || $readPlan['tbl_plan_model'] == '1231' || $readPlan['tbl_plan_model'] == '1232' || $readPlan['tbl_plan_model'] == '1233'  ? $lang[1636] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1234' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1235' ? $lang[62] . ' : ' : '') .
                                                    ($readPlan['tbl_plan_model'] == '1236' || $readPlan['tbl_plan_model'] == '1237' ? $lang[1637] . ' : ' : '') .
                                                    toPersianNum($readPlan['tbl_plan_expire']) . ' ، ' . $lang[1438] . ' : ' . $readContract['tbl_contract_customer'] . ' ، ' . $lang[1780] . ' : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</p>
                                            </div>
                                        </div><hr>';
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


        <?php }
    } ?>


            </div>
        <?php
    }
    if (isset($do) && $do == 'setting') {
        if (empty($_SESSION['id_login'])) {
            header("location: login.php");
            exit();
        } else {
            $user_id = $_SESSION['id_login'];
        }
        $color = strip_tags(sqi($_POST['color']));
        $fluid = strip_tags(sqi($_POST['fluid']));
        $navbarStyle = strip_tags(sqi($_POST['navbarStyle']));
        Query("update tbl_user SET 
    tbl_user_color='$color',
    tbl_user_fluid='$fluid',
    tbl_user_navbarStyle='$navbarStyle'
    where tbl_user_id= '$user_id' ");
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
                echo 'Updated !';
            } else {
                Query("update tbl_register set  tbl_register_confirm='1',tbl_register_confirmdate='$editdate' Where tbl_register_id='$id' ");
                echo 'Updated !';
            }
        }

        if ($name === 'checklist') {
            //data-name
            $result = Query("SELECT tbl_checklist_confirm From tbl_checklist Where tbl_checklist_code='$id' ");
            $read = mysqli_fetch_array($result);
            $endwork = $read['tbl_checklist_confirm'];
            if ($endwork == 1) {
                Query("update tbl_checklist set  tbl_checklist_confirm='0',tbl_checklist_confirmdate='$editdate' Where tbl_checklist_code='$id' ");
                echo 'Updated !';
            } else {
                Query("update tbl_checklist set  tbl_checklist_confirm='1',tbl_checklist_confirmdate='$editdate' Where tbl_checklist_code='$id' ");
                echo 'Updated !';
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
            if (count($status) == 0) {
                Query("DELETE from tbl_memberskill where tbl_memberskill_user='$userid' and tbl_memberskill_contract='$contract' ");
            } else {
                Query("update tbl_memberskill set tbl_memberskill_data = '$data',tbl_memberskill_mentor = '$this_user_id',tbl_memberskill_dateupdate='$date' where tbl_memberskill_user='$userid' and tbl_memberskill_contract='$contract' ");
            }
        } else {
            Query("insert into tbl_memberskill (tbl_memberskill_data,tbl_memberskill_user,tbl_memberskill_contract,tbl_memberskill_mentor,tbl_memberskill_date) values('$data','$userid','$contract','$this_user_id','$date') ");
        }

        echo 'بروز شد';
    }

    if (isset($do) && $do == 'addmentor') {
        $this_user_id = $_SESSION['id_login'];
        $mentor = $_POST['mentor'];
        $userid = $_POST['userid'];
        $contract = $_POST['contract'];
        $date = jdate("Y/m/d a:h:s");
        if ($_POST['mentor'] != '0') {
            $result = Query("select * from tbl_projectmember where tbl_projectmember_admin='$this_user_id'  and tbl_projectmember_user='$userid' and tbl_projectmember_contract='$contract' ");
            if (mysqli_num_rows($result) > 0) {
                Query("update tbl_projectmember set tbl_projectmember_mentor = '$mentor',tbl_projectmember_dateupdate='$date' where tbl_projectmember_admin='$this_user_id' and tbl_projectmember_user='$userid' and tbl_projectmember_contract='$contract' ");
            } else {
                Query("insert into tbl_projectmember (tbl_projectmember_status,tbl_projectmember_mentor,tbl_projectmember_user,tbl_projectmember_contract,tbl_projectmember_admin,tbl_projectmember_date) values('0','$mentor','$userid','$contract','$this_user_id','$date') ");
            }

            echo 'بروز شد';
        } else {
            echo 'یک استاد راهنما را انتخاب کنید';
        }
    }


    if (isset($do) && $do == 'specialmohtava') {
        if (isset($_POST["val"])) {

            $val = explode("-", $_POST["val"]);

            $result = Query("SELECT * From tbl_mohtava Where tbl_mohtava_code='$val[1]'");
            $read = mysqli_fetch_array($result);
            if ($read['tbl_mohtava_special'] == 1) {
                $newval = 0;
            } else {
                $newval = 1;
            }
            Query("update tbl_mohtava set tbl_mohtava_special='$newval' Where tbl_mohtava_code='$val[1]' ");
            echo 'بروز شد';
        }
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
