<?php
include './includer.php';
$show_per_page = '500';
if (!empty($do) && $do == 'report') {
    $pagetitle = $lang[1615];
} else {
    $pagetitle = $lang[1738];
}
$Tabpage = '90';
$thisaccess = '1,5,6';
$img_item = '1';
include './header.inc.php';

if (!empty($_GET['user'])) {
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
            <h5 class="fs-0">' . $lang[1737] . '  : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</h5>
            <h5 class="fs-0">' . $lang[385] . '  : ' . toPersianNum($read_member['tbl_member_nationalcode']) . '</h5>
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

if (!empty($_GET['contract'])) {
    $result_contract = Query("SELECT tbl_contract_enddate,tbl_contract_startdate From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    $datepickerStart = $read_contract['tbl_contract_startdate'];
    $datepickerEnd = $read_contract['tbl_contract_enddate'];
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
                            <?= (!isset($do) && $access == '6' ?  '<div class="plus">
                                <a href="?do=add&user=' . $_GET['user'] . '&skill=' . $_GET['skill'] . '&contract=' . $_GET['contract'] . '&plan=' . $_GET['plan'] . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">


                <?php


                if (!isset($do)) {

                    $result_for_count = Query("SELECT * From tbl_interview where tbl_interview_plan='$getplan' and tbl_interview_mentor='$this_user_id' and tbl_interview_model='1'  and tbl_interview_contract='$_GET[contract]' and tbl_interview_skill='$_GET[skill]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'  ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination               
                    $result = Query("SELECT * From tbl_interview where tbl_interview_plan='$getplan' and tbl_interview_mentor='$this_user_id' and tbl_interview_model='1' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'   ORDER BY tbl_interview_order DESC , tbl_interview_id DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-success m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<form action="?do=runupdate" method="POST"><div class="table-responsive scrollbar">
                    <input type="hidden" name="user" value="' . $_GET['user'] . '">
                        <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        <input type="hidden" name="plan" value="' . $_GET['plan'] . '">
                        <input type="hidden" name="skill" value="' . $_GET['skill'] . '">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[54] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>' . $lang[1741] . '</th>
                                    <th>' . $lang[62] . '</th>
                                    <th>' . $lang[1618] . '</th>
                                    <th>' . $lang[1619] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $mentor = $read['tbl_interview_mentor'];
                        $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                        $readMentor = mysqli_fetch_array($resultMentor);

                        echo '<tr >
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2">
                            <input type="hidden" name="code[]" value="' . $read['tbl_interview_code'] . '">
                            <input type="text" name="o' . $read['tbl_interview_code'] . '" value="' . $read['tbl_interview_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                            </td>
                            <td class="align-middle p-2">' . $read['tbl_interview_title'] . '</td>
                            <td class="align-middle p-2">';
                        if (!empty($read['tbl_interview_group'])) {
                            $group = explode(",", $read['tbl_interview_group']);
                            $k = 1;
                            if (count($group) > 2) {
                                foreach ($group as $key => $value) {
                                    if (!empty($value)) {
                                        $resultMember = Query("SELECT tbl_member_fname,tbl_member_lname From tbl_member Where tbl_member_id='$value' ");
                                        $readMember = mysqli_fetch_array($resultMember);
                                        echo $k . ' - ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '<br>';
                                        $k++;
                                    }
                                }
                            } else {
                                echo $lang[1495];
                            }
                        } else {
                            echo $lang[1495];
                        }
                        echo '</td>
                            <td class="align-middle p-2">' . toPersianNum($read['tbl_interview_date']) . '</td>
                            <td class="align-middle p-2">' . toPersianNum($read['tbl_interview_viewdate']) . '</td>
                            <td class="align-middle p-2">' . @$readMentor['tbl_user_name'] . '</td>
                            <td class="align-middle p-2">
                            <a href="?do=edit&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . ($access == '6' ? $lang[23] : $lang[1383]) . '"><span class="bi-pencil-square"></span></a>
                            ' . ($access == '6' ? '<a href="?do=delete&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '') . '
                            <a href="?do=report&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . (!empty($read['tbl_interview_reporttext']) ? $lang[858] : $lang[856]) . '"><span class="bi-eye-fill"></span></a></td>
                            </tr>';
                        $i++;
                    }
                    echo '</tbody></table><input type="submit" name="runupdate" value="' . $lang[1342] . '" class="btn btn-success update-btn mt-3 mb-3" ></form>';
                }

                if (isset($do) && $do == 'runupdate') {
                    $code = $_POST['code'];
                    $user = $_POST['user'];
                    $skill = $_POST['skill'];
                    $plan = $_POST['plan'];
                    $contract = $_POST['contract'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_interview set tbl_interview_order='$val' Where tbl_interview_code = '$value' ");
                    }
                    header("Location: interviewcoach.php?user=$user&skill=$skill&contract=$contract&plan=$plan");
                }

                // end = pagination
                // start = do add
                if (isset($do) && $do == 'add') {
                    echo '<div class="col-lg-10 p-3">
                            <form action="?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['skill'] . '" name="skill">
                            <input type="hidden" value="' . $_GET['plan'] . '" name="plan">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                      <label>عنوان<span class="language">' . $language[$i] . '</span></label>
                                      <input type="text" name="title_' . $language[$i] . '"
                                                            class="form-control" >
                                      </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                      <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                      <textarea name="text_' . $language[$i] . '"
                                                            class="form-control" rows="8"  style="height:150px;"></textarea>
                                      </div>';
                    }

                    for ($i = 1; $i <= $img_item; $i++) {
                        echo '<div class="form-group file-margin"><table><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                  <td><input type="file" name="img_' . $i . '"></td>
                                  </tr></table></div>';
                    }

                    echo '<div class="form-group">
                        <label>' . $lang[1778] . '</label>
                        <form action="" id="formwork">
                        <select name="group[]" style="width: 100%;"
                        class="select2 status" multiple="multiple" >';
                    $mentiAll = array();
                    $result = Query("SELECT * From tbl_plan where tbl_plan_contract='$_GET[contract]' and  tbl_plan_skill='$_GET[skill]' and tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
                    while ($read = mysqli_fetch_array($result)) {
                        $menti = $read['tbl_plan_menti'];
                        $mentiAll[$menti] = $menti;
                    }
                    foreach ($mentiAll as $key => $value) {
                        if (!empty($value)) {
                            $checkSkillConfirm = Query("select * from tbl_memberskill where tbl_memberskill_user='$value' and tbl_memberskill_contract='$_GET[contract]' and tbl_memberskill_startdate !='' ");
                            if (mysqli_num_rows($checkSkillConfirm) > 0) {
                                $resultMember = Query("SELECT tbl_member_fname,tbl_member_lname,tbl_member_id From tbl_member Where tbl_member_id='$value' ");
                                $readMember = mysqli_fetch_array($resultMember);
                                echo '<option value="' . $readMember['tbl_member_id'] . '" ' . ($readMember['tbl_member_id'] == $_GET['user'] ? 'selected' : '') . '>' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</option>';
                            }
                        }
                    }

                    echo ' </select>
                        </div>';


                    echo '<div class="form-group">
                        <label>' . $lang[1802] . '</label>
                        <input type="text" name="expire" class="form-control datepicker" readonly>
                        </div>';

                    echo '<div class="col-12 col-md-12"><div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3"></div>
                            </div></form></div>';
                }
                // start = add record to db
                if (isset($do) && $do == 'additem' && $access == '6') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        $date = $_POST['expire'];

                        if (!empty($date)) {
                            $expireOne = explode("/", $date);
                            $expireEnglish = jalali_to_gregorian($expireOne[0], $expireOne[1], $expireOne[2]);
                            $expireEnglish = $expireEnglish[0] . '/' . ($expireEnglish[1] < 10 ? '0' . $expireEnglish[1] : $expireEnglish[1]) . '/' . ($expireEnglish[2] < 10 ? '0' . $expireEnglish[2] : $expireEnglish[2]);
                        } else {
                            $expireEnglish = '';
                        }

                        $group = @$_POST['group'];
                        $dataGroup = '';
                        if (!empty($_POST['group'])) {
                            foreach ($group as $key => $value) {
                                if (!empty($value)) {
                                    $dataGroup .= $value . ',';
                                }
                            }
                        }

                        $user = $_POST['user'];
                        $time = @$_POST['time'];
                        $skill = $_POST['skill'];
                        $contract = $_POST['contract'];
                        $plan = $_POST['plan'];
                        $location = @$_POST['location'];
                        $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                        if (@!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                $read_file_name = explode(".", $img_[$i_image]);
                                $extn = end($read_file_name);
                                $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;

                                if ($extn == 'pdf' || $extn == 'PDF' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    $images_name .= $img_[$i_image] . ',';
                                    move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                } elseif (!empty($exp[$i_image])) {
                                    $images_name .= $exp[$i_image] . ',';
                                } else {
                                    @$images_name .= ',';
                                }
                            }
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            $text_[$i] = (!empty($_POST['text_' . $language[$i]]) ? addslashes($_POST['text_' . $language[$i]]) : '');
                            $title_[$i] = (!empty($_POST['title_' . $language[$i]]) ? addslashes($_POST['title_' . $language[$i]]) : '');

                            $language_name = $language[$i];
                            Query("insert into tbl_interview (
                                    tbl_interview_menti,
                                    tbl_interview_mentor,
                                    tbl_interview_title,
                                    tbl_interview_text,
                                    tbl_interview_date,
                                    tbl_interview_file,
                                    tbl_interview_delete,
                                    tbl_interview_lng,
                                    tbl_interview_order,
                                    tbl_interview_code,
                                    tbl_interview_time,
                                    tbl_interview_location,
                                    tbl_interview_model,
                                    tbl_interview_skill,
                                    tbl_interview_contract,
                                    tbl_interview_plan,
                                    tbl_interview_group,
                                    tbl_interview_dateenglish) 
                                values(
                                    '$user',
                                    '$this_user_id',
                                    '$title_[$i]',
                                    '$text_[$i]',
                                    '$date',
                                    '$images_name',
                                    '0',
                                    '$language_name',
                                    '$order',
                                    '$code',
                                    '$time',
                                    '$location',
                                    '1',
                                    '$skill',
                                    '$contract',
                                    '$plan',
                                    '$dataGroup',
                                    '$expireEnglish')");
                        }
                        header("Location: interviewcoach.php?user=$user&contract=$contract&skill=$skill&plan=$plan");
                        exit();
                    }
                }
                // end  = add record  
                if (isset($do) && $do == 'report') {
                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_interview_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_interview Where  tbl_interview_model='1'  and tbl_interview_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    $userId = read_fild(NULL, 'tbl_interview_menti');
                    // form for edit
                    echo '<div class="col-lg-10 p-3">
                            ' . ($access == '6' ? ' <form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' .  read_fild(NULL, 'tbl_interview_plan') . '" name="plan">
                            <input type="hidden" value="' . read_fild(NULL, 'tbl_interview_contract') . '" name="contract">
                            <input type="hidden" value="' . read_fild(NULL, 'tbl_interview_skill') . '" name="skill">' : '');

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" class="form-control" rows="8"  style="height:150px;">' . read_fild($language[$i], 'tbl_interview_reporttext') . '</textarea>
                                          </div>';
                    }
                    echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_reportfile') . '">';
                    for ($i = 1; $i <= $img_item; $i++) {
                        @$exp = explode(",", read_fild(NULL, 'tbl_interview_reportfile'));
                        if ($exp[$i - 1]) {
                            echo '<div class="form-group file-margin"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '"></td><td>
                                      <a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span>
                                      </a><a href="?code=' . $code . '&do=deletereportimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group file-margin"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '">
                                      </td></tr></table></div>';
                        }
                    }
                    echo '<hr>';

                    echo '<div class="form-group">
                            <label>' . $lang[1623] . '</label>
                            <input type="text" name="date" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_reportdate') . '" readonly>
                            </div>';
                    echo '<div class="form-group">
                            <label>' . $lang[1779] . ' </label>
                            <select class="form-control select2" name="rank" ><option value="0">' . $lang[1625] . '</option>';
                    for ($i = 10; $i <= 100; $i++) {
                        echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_interview_rank') == $i ? 'selected' : '') . '>' . $i . '</option>';
                    }
                    echo '</select>
                            </div>';
                    echo ($access == '6' ? '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[857] . '" class="btn btn-primary mt-3" >
                            </div>
                            </form>' : '');
                    echo '</div>';
                }

                if (isset($do) && $do == 'reportupdate') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $rank = (!empty($_POST['rank']) ? $_POST['rank'] : '0');
                        $user = $_POST['user'];
                        $allimages = $_POST['allimages'];
                        $date = $_POST['date'];
                        $contract = $_POST['contract'];
                        $skill = $_POST['skill'];
                        $plan = $_POST['plan'];
                        $read_allimages = explode(",", $allimages);
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                    $images_name .= $read_allimages[$i_image - 1] . ',';
                                } else {
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                    if ($extn == 'pdf' || $extn == 'PDF' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                        $images_name .= $img_[$i_image] . ',';
                                        move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                        unlink($directory . $read_allimages[$i_image - 1]);
                                    } else {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    }
                                }
                            }
                        }


                        for ($i = 1; $i <= $language_count; $i++) {
                            $text_[$i] = (!empty($_POST['text_' . $language[$i]]) ? addslashes($_POST['text_' . $language[$i]]) : '');

                            $language_name = $language[$i];
                            Query("Update tbl_interview SET
                                    tbl_interview_reportmentor='$this_user_id',
                                    tbl_interview_reporttext='$text_[$i]',
                                    tbl_interview_reportdate='$date',
                                    tbl_interview_rank='$rank',
                                    tbl_interview_file='$images_name' where tbl_interview_lng='$language_name' and tbl_interview_code='$code'");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: interviewcoach.php?user=$user&skill=$skill&contract=$contract&plan=$plan ");
                    }
                }

                if (isset($do) && $do == 'edit') {
                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_interview_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_interview Where tbl_interview_model='1'  and tbl_interview_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    $userId = read_fild(NULL, 'tbl_interview_menti');
                    $contractId = read_fild(NULL, 'tbl_interview_contract');
                    $skilId = read_fild(NULL, 'tbl_interview_skill');
                    // form for edit
                    echo '<div class="col-lg-8 p-3">
                            ' . ($access == '6' ? '<form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . read_fild(NULL, 'tbl_interview_plan') . '" name="plan">
                            <input type="hidden" value="' . $contractId . '" name="contract">
                            <input type="hidden" value="' . $skilId . '" name="skill">' : '');

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>عنوان<span class="language">' . $language[$i] . '</span></label>
                                          <input type="text" name="title_' . $language[$i] . '"
                                                                class="form-control" value="' . read_fild($language[$i], 'tbl_interview_title') . '">
                                          </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '"
                                                                class="form-control" rows="8"  style="height:150px;">' . read_fild($language[$i], 'tbl_interview_text') . '</textarea>
                                          </div>';
                    }

                    echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_file') . '">';
                    for ($i = 1; $i <= $img_item; $i++) {

                        $exp = explode(",", read_fild(NULL, 'tbl_interview_file'));
                        if ($exp[$i - 1]) {
                            echo '<div class="form-group file-margin"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '"></td><td>
                                      <a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                      <a href="?code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group file-margin"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '">
                                      </td></tr></table></div>';
                        }
                    }
                    echo '<hr>';
                    $group = read_fild(Null, 'tbl_interview_group');

                    echo '<div class="form-group">
                        <label>' . $lang[1778] . '</label>
                        <form action="" id="formwork">
                        <select name="group[]" style="width: 100%;"
                        class="select2 status" multiple="multiple" >';
                    $mentiAll = array();
                    $result = Query("SELECT * From tbl_plan where tbl_plan_contract='$contractId' and  tbl_plan_skill='$skilId' and tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0'  ");
                    while ($read = mysqli_fetch_array($result)) {
                        $menti = $read['tbl_plan_menti'];
                        $mentiAll[$menti] = $menti;
                    }
                    foreach ($mentiAll as $key => $value) {
                        if (!empty($value)) {
                            $checkSkillConfirm = Query("select * from tbl_memberskill where tbl_memberskill_user='$value' and tbl_memberskill_contract='$contractId' and tbl_memberskill_startdate !='' ");
                            if (mysqli_num_rows($checkSkillConfirm) > 0) {
                                $resultMember = Query("SELECT tbl_member_fname,tbl_member_lname,tbl_member_id From tbl_member Where tbl_member_id='$value' ");
                                $readMember = mysqli_fetch_array($resultMember);
                                echo '<option value="' . $readMember['tbl_member_id'] . '" ' . (strpos($group, $readMember['tbl_member_id']) !== False ? 'selected' : '') . '>' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</option>';
                            }
                        }
                    }

                    echo ' </select>
                        </div>';


                    echo '<div class="form-group">
                            <label>' . $lang[1802] . '</label>
                            <input type="text" name="expire" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_date') . '" readonly>
                            </div>';
                    echo ($access == '6' ? '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                            </div>
                            </form>' : '');
                    echo '</div>';
                }

                if (isset($do) && $do == 'edititem' && $access == '6') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $expire = $_POST['expire'];
                        $expireOne = explode("/", $expire);
                        $expireEnglish = jalali_to_gregorian($expireOne[0], $expireOne[1], $expireOne[2]);
                        $expireEnglish = $expireEnglish[0] . '/' . ($expireEnglish[1] < 10 ? '0' . $expireEnglish[1] : $expireEnglish[1]) . '/' . ($expireEnglish[2] < 10 ? '0' . $expireEnglish[2] : $expireEnglish[2]);
                        $user = $_POST['user'];
                        $time = $_POST['time'];
                        $location = $_POST['location'];
                        $allimages = $_POST['allimages'];
                        $contract = $_POST['contract'];
                        $plan = $_POST['plan'];
                        $skill = $_POST['skill'];

                        $group = $_POST['group'];
                        $dataGroup = '';
                        foreach ($group as $key => $value) {
                            if (!empty($value)) {
                                $dataGroup .= $value . ',';
                            }
                        }


                        $read_allimages = explode(",", $allimages);
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                    $images_name .= $read_allimages[$i_image - 1] . ',';
                                } else {
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                    if ($extn == 'pdf' || $extn == 'PDF' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                        $images_name .= $img_[$i_image] . ',';
                                        move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                        unlink($directory . $read_allimages[$i_image - 1]);
                                    } else {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    }
                                }
                            }
                        }


                        for ($i = 1; $i <= $language_count; $i++) {
                            $text_[$i] = (!empty($_POST['text_' . $language[$i]]) ? addslashes($_POST['text_' . $language[$i]]) : '');
                            $title_[$i] = (!empty($_POST['title_' . $language[$i]]) ? addslashes($_POST['title_' . $language[$i]]) : '');

                            $language_name = $language[$i];
                            Query("Update tbl_interview SET
                                    tbl_interview_mentor='$this_user_id',
                                    tbl_interview_title='$title_[$i]',
                                    tbl_interview_text='$text_[$i]',
                                    tbl_interview_date='$expire',
                                    tbl_interview_file='$images_name',
                                    tbl_interview_time='$time',
                                    tbl_interview_dateenglish='$expireEnglish',
                                    tbl_interview_location='$location',tbl_interview_group='$dataGroup' where tbl_interview_lng='$language_name' and tbl_interview_code='$code'");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: interviewcoach.php?user=$user&skill=$skill&contract=$contract&plan=$plan ");
                    }
                }

                if (isset($do) && $do == 'deleteimage') {

                    $result = Query("SELECT * from tbl_interview Where tbl_interview_model='1'  and tbl_interview_code = '$code' ");
                    $read = mysqli_fetch_array($result);

                    echo '<div class="col-lg-8 p-3">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $read['tbl_interview_menti'] . '" name="user" >
                         <input type="hidden" value="' . $read['tbl_interview_contract'] . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-xs btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $img = $_POST['img'];
                        $code = $_POST['code'];
                        $user = $_POST['user'];
                        $record = str_replace("$img", "", $read['tbl_interview_file']);
                        $external_file = $directory . $img;
                        unlink("$external_file");
                        Query("Update tbl_interview SET tbl_interview_file = '$record' Where tbl_interview_code = '$code' ");
                        header("Location: interviewcoach.php?do=edit&code=$code&user=$user");
                    } elseif (isset($_POST['nodelete'])) {
                        $code = $_POST['code'];
                        $user = $_POST['user'];
                        header("Location: interviewcoach.php?do=edit&code=$code&user=$user");
                    }
                }

                // start = delete record
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_interview Where tbl_interview_model='1' and tbl_interview_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 p-3">' . $lang[25] . '<br /><h2>' . $read['tbl_interview_title'] . '</h2><br /><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="id" >
                         <input type="hidden" value="' . $_GET['user'] . '" name="user" >
                         <input type="hidden" value="' . $read['tbl_interview_plan'] . '" name="plan" >
                         <input type="hidden" value="' . $read['tbl_interview_skill'] . '" name="skill" >
                         <input type="hidden" value="' . $read['tbl_interview_contract'] . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $id = $_POST['id'];
                        $user = $_POST['user'];
                        $plan = $_POST['plan'];
                        $skill = $_POST['skill'];
                        $contract = $_POST['contract'];
                        Query("update tbl_interview set  tbl_interview_delete='1' Where tbl_interview_code = '$id' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: interviewcoach.php?user=$user&skill=$skill&contract=$contract&plan=$plan");
                        exit();
                    }
                    // no delete
                    elseif (isset($_POST['nodelete'])) {
                        $user = $_POST['user'];
                        $plan = $_POST['plan'];
                        $skill = $_POST['skill'];
                        $contract = $_POST['contract'];
                        header("Location: interviewcoach.php?user=$user&skill=$skill&contract=$contract&plan=$plan");
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
</div>
<?php
include './footer.inc.php';

?>