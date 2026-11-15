<?php
include './includer.php';
$show_per_page = '500';

if (!empty($do) && $do == 'report') {
    $pagetitle = $lang[1615];
} else {
    $pagetitle = $lang[1594];
}
$Tabpage = '90';
$thisaccess = '1,5';
$img_item = '4';
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
    $datepickerStart = $read_contract['tbl_contract_startdate'];
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
$result_for_count = Query("SELECT * From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0' limit 0,1 ");
$count_result = mysqli_num_rows($result_for_count);
$start_contnet =  ($page - 1) * $show_per_page;
$page_number = ceil($count_result / $show_per_page);
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) ? '<div class="plus">
                                ' . ($count_result <= 0 ? '<a href="?do=add&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" ><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>' : '') . '
                            </div>' : '') ?>
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

                    $result = Query("SELECT * From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'   ORDER BY tbl_interview_id DESC limit 0,1");


                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<form action="?do=runupdate" method="POST">
                                <input type="hidden" name="user" value="' . $_GET['user'] . '">
                        <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        <div class="table-responsive scrollbar">
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
                            <a href="?do=edit&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                            <a href="?do=delete&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                            <a href="?do=report&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . (!empty($read['tbl_interview_rank']) && $read['tbl_interview_rank'] != '0' ? $lang[858] : $lang[856]) . '"><span class="bi-list-check"></span></a></td>
                            </tr>';
                        $i++;
                    }
                    echo '</tbody></table><input type="submit" name="runupdate" value="' . $lang[1342] . '" class="btn btn-secondary update-btn mt-3" style="display:none;"></form>';
                }
                if (isset($do) && $do == 'runupdate') {
                    $levelcode = $_POST['levelcode'];
                    $code = $_POST['code'];
                    $user = $_POST['user'];
                    $contract = $_POST['contract'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_interview set tbl_interview_order='$val' Where tbl_interview_code = '$value' ");
                    }
                    header("Location: interview.php?user=$user&contract=$contract");
                }

                // end = pagination
                // start = do add
                if (isset($do) && $do == 'add') {
                    $contract = $_GET['contract'];
                    echo '<div class="col-lg-10 col-12 p-3">
                            <form action="?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
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
                                      <textarea name="text_' . $language[$i] . '" rows="8"  class="editor editor_' . $language[$i] . '"></textarea>
                                      </div>';
                    }

                    echo '<div class="form-group">
                        <label>' . $lang[83] . '</label>
                        <input type="text" name="link" class="form-control" >
                        </div><div class="form-group">
                    <label>' . $lang[62] . '</label>
                    <input type="text" name="expire" class="form-control datepicker" readonly>
                    </div>
                    <div class="form-group">
                    <label>' . $lang[1621] . '</label>
                    <input type="text" name="time" class="form-control" >
                    </div>
                    <div class="form-group">
                    <label>' . $lang[1622] . '</label>
                    <input type="text" name="location" class="form-control" >
                    </div>';
                    for ($i = 1; $i <= $img_item; $i++) {
                        echo '<div class="form-group file-margin">
                            <label class="col-sm-12 control-label">' . $lang[52] . '&nbsp; ' . $i . '</label>
                                  <input type="file" name="img_' . $i . '" class="form-control">
                                  </div>';
                    }



                    echo '<div class="col-12 col-md-12"><div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3"></div>
                            </div></form></div>';
                }
                // start = add record to db
                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        $date = $_POST['expire'];
                        $user = $_POST['user'];
                        $time = $_POST['time'];
                        $link = $_POST['link'];
                        $location = $_POST['location'];
                        $contract = $_POST['contract'];
                        $order = (!empty($_POST['order']) ? $_POST['order'] : '0');

                        if (!$images_name) {
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
                                    $images_name .= ',';
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
                                    tbl_interview_contract,
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
                                    tbl_interview_link) 
                                values(
                                    '$user',
                                    '$this_user_id',
                                    '$contract',
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
                                    '0',
                                    '$link')");
                        }
                        header("Location: interview.php?user=$user&contract=$contract");
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
                        $result = Query("SELECT * FROM tbl_interview Where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    $userId = read_fild(NULL, 'tbl_interview_menti');
                    $contractId = read_fild(NULL, 'tbl_interview_contract');
                    // form for edit
                    echo '<div class="col-lg-10 col-12 p-3">
                            <form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" rows="8"  class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_interview_reporttext') . '</textarea>
                                          </div>';
                    }
                    echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_reportfile') . '">';
                    for ($i = 1; $i <= $img_item; $i++) {
                        @$exp = explode(",", read_fild(NULL, 'tbl_interview_reportfile'));
                        if (!empty($exp[$i - 1])) {
                            echo '<div class="form-group file-margin"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '" class="form-control"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                      <a href="?code=' . $code . '&do=deletereportimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group file-margin"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '" class="form-control">
                                      </td></tr></table></div>';
                        }
                    }
                    echo '<hr>';

                    echo '<div class="form-group">
                            <label>' . $lang[1623] . '</label>
                            <input type="text" name="date" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_reportdate') . '" readonly >
                            </div>';
                    echo '<div class="form-group">
                            <label>' . $lang[1624] . ' </label>
                            <select class="form-control select2" name="rank" ><option value="0">' . $lang[1625] . '</option>';
                    for ($i = 10; $i <= 100; $i++) {
                        echo '<option value="' . $i . '" ' . (read_fild(NULL, 'tbl_interview_rank') == $i ? 'selected' : '') . '>' . $i . '</option>';
                    }
                    echo '</select>
                            </div>';
                    echo '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[857] . '" class="btn btn-primary mt-3" >
                            </div>
                            </form></div>';
                }

                if (isset($do) && $do == 'reportupdate') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $contract = $_POST['contract'];
                        $rank = (!empty($_POST['rank']) ? $_POST['rank'] : '0');
                        $user = $_POST['user'];
                        $allimages = $_POST['allimages'];
                        $date = $_POST['date'];
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
                                    if ($extn == 'XLS' || $extn == 'xls' || $extn == 'XLSX' || $extn == 'xlsx' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
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
                        header("Location: interview.php?user=$user&contract=$contract ");
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
                        $result = Query("SELECT * FROM tbl_interview Where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    $userId = read_fild(NULL, 'tbl_interview_menti');
                    $contractId = read_fild(NULL, 'tbl_interview_contract');
                    // form for edit
                    echo '<div class="col-lg-10 col-12 p-3">
                            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>' . $lang[61] . '<span class="language">' . $language[$i] . '</span></label>
                                          <input type="text" name="title_' . $language[$i] . '"
                                                                class="form-control" value="' . read_fild($language[$i], 'tbl_interview_title') . '">
                                          </div>';
                    }

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group ">
                                          <label>' . $lang[1527] . '<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_interview_text') . '</textarea>
                                          </div>';
                    }

                    echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_file') . '">';
                    for ($i = 1; $i <= $img_item; $i++) {

                        $exp = explode(",", read_fild(NULL, 'tbl_interview_file'));
                        if (!empty($exp[$i - 1])) {
                            echo '<div class="form-group file-margin"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '" class="form-control"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                      <a href="?code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group file-margin"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '" class="form-control"">
                                      </td></tr></table></div>';
                        }
                    }
                    echo '<hr>';

                    echo '<div class="form-group">
                            <label>' . $lang[83] . '</label>
                            <input type="text" name="link" class="form-control" value="' . read_fild(NULL, 'tbl_interview_link') . '" >
                            </div>
                            <div class="form-group">
                            <label>' . $lang[62] . '</label>
                            <input type="text" name="expire" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_date') . '" readonly>
                            </div>
                            <div class="form-group">
                            <label>' . $lang[1621] . '</label>
                            <input type="text" name="time" class="form-control" value="' . read_fild(NULL, 'tbl_interview_time') . '">
                            </div>
                            <div class="form-group">
                            <label>' . $lang[1622] . '</label>
                            <input type="text" name="location" class="form-control" value="' . read_fild(NULL, 'tbl_interview_location') . '">
                            </div>';
                    echo '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                            </div>
                            </form>';

                    echo '<hr>
                            <h4>توضیحات فراگیر</h4>
                            <div class="form-group">
                            <label>توضیحات </label><p>' . read_fild(NULL, 'tbl_interview_usercomment') . '</p></div>';
                    $userFile = explode(",", read_fild(NULL, 'tbl_interview_userfile'));
                    $k = 1;
                    foreach ($userFile as $key => $value) {
                        if (!empty($value)) {

                            echo '<div class="form-group file-margin"><table><tr>
                                          <td><label class="col-sm-12 control-label">' . $lang[1212] . '&nbsp; ' . $k . '</label></td>
                                          <td><a href="../upload/userinterview/' . $value . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                          </td></tr></table></div>';
                            $k++;
                        }
                    }


                    echo '</div>';
                }
                $images_name = '';
                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $expire = $_POST['expire'];
                        $contract = $_POST['contract'];
                        $user = $_POST['user'];
                        $time = $_POST['time'];
                        $link = $_POST['link'];
                        $location = $_POST['location'];
                        $allimages = $_POST['allimages'];
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
                                    if ($extn == 'XLS' || $extn == 'xls' || $extn == 'XLSX' || $extn == 'xlsx' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'doc' || $extn == 'DOC' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
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
                                    tbl_interview_link='$link',
                                    tbl_interview_location='$location' where tbl_interview_lng='$language_name' and tbl_interview_code='$code'");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: interview.php?user=$user&contract=$contract ");
                    }
                }

                if (isset($do) && $do == 'deleteimage') {
                    $result = Query("SELECT * from tbl_interview Where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-10 p-3"><h3>' . $lang[25] . '<h3><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
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
                        $contract = $_POST['contract'];
                        $record = str_replace("$img", "", $read['tbl_interview_file']);
                        $external_file = $directory . $img;
                        unlink("$external_file");
                        Query("Update tbl_interview SET tbl_interview_file = '$record' Where tbl_interview_code = '$code' ");
                        header("Location: interview.php?do=edit&code=$code&user=$user&contract=$contract");
                    } elseif (isset($_POST['nodelete'])) {
                        $code = $_POST['code'];
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        header("Location: interview.php?do=edit&code=$code&user=$user&contract=$contract");
                    }
                }

                // start = delete record
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_interview Where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-10 col-12 p-3">
                        <p>' . $lang[25] . '</p>
                        <h3>' . $read['tbl_interview_title'] . '</h3>
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
                        Query("update tbl_interview set  tbl_interview_delete='1' Where tbl_interview_code = '$id' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: interview.php?user=$user&contract=$contract");
                        exit();
                    } elseif (isset($_POST['nodelete'])) {
                        $user = $_POST['user'];
                        $contract = $_POST['contract'];
                        header("Location: interview.php?user=$user&contract=$contract");
                        exit();
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        header("Location: ?keyword=$keyword_search");
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