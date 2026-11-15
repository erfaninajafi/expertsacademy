<?php
include './includer.php';
$show_per_page = '500';
$pagetitle = 'مصاحبه';
$Tabpage = '90';
$thisaccess = '1,5';
$img_item = '1';
include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= (!empty($do) && $do == 'report' ? 'گزارش مصاحبه' : $pagetitle) ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    echo '<section class="content"><div class="col-md-12">
                            <div class="box box-body table-responsive">';
                    // list content
                    if (!empty($_GET['contract'])) {
                        $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :<a href="faragir.php?contract='.$_GET['contract'].'">' . $read_contract['tbl_contract_code'] . '</a></span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                        </div></div>';

                        $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
                        $read_member = mysqli_fetch_array($result_member);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span>نام و نام خانوادگی فراگیر : <a href="faragir.php?contract=' . $_GET['contract'] . '&user=' . $_GET['user'] . '">' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</a></span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read_member['tbl_member_nationalcode']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read_member['tbl_member_mobile']) . '</span></div>
                    </div></div>';
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">پروژه : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5></div>';
                    }

                    if (!isset($do)) {

                        $result_for_count = Query("SELECT * From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0' limit 0,1 ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table><tr><td class="col-sm-9">'.($count_result <=0 ? '<a class="btn btn-primary margin-left" href="?do=add&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" >افزودن مصاحبه </a>' : '').'</td>';
                        // start count db result for pagination 
                        
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_interview where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$_GET[user]' and tbl_interview_lng='$language_default' and tbl_interview_delete='0'   ORDER BY tbl_interview_id DESC limit 0,1");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<form action="?do=runupdate" method="POST">
                                <input type="hidden" name="user" value="' . $_GET['user'] . '">
                        <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                        <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                
                                <thead class="table-dark">
                                <tr>
                                    <th style="display:none;">' . $lang[60] . '</th>
                                    <th>رده بندی</th>
                                    <th>عنوان</th>
                                    <th>تاریخ مصاحبه</th>
                                    <th>تاریخ مشاهده</th>
                                    <th>ایجادکننده</th>
                                    <th>نمره</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mentor = $read['tbl_interview_mentor'];
                            $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                            $readMentor = mysqli_fetch_array($resultMentor);
                            if(!empty($read['tbl_interview_rank']) && $read['tbl_interview_rank'] !='0')
                            {
                                $rank = $read['tbl_interview_rank'];
                            }
                            else {
                                $rank = 'داده نشده';
                            }
                            echo '<tr >
                            <td style="width: 1%">' . $i . '</td>
                            <td class="col-sm-1" style="display:none;">
                            <input type="hidden" name="code[]" value="' . $read['tbl_interview_code'] . '">
                            <input type="text" name="o' . $read['tbl_interview_code'] . '" value="' . $read['tbl_interview_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                            </td>
                            <td class="col-sm-2">' . $read['tbl_interview_title'] . '</td>
                            <td class="col-sm-1">' . toPersianNum($read['tbl_interview_date']) . '</td>
                            <td class="col-sm-1">' . toPersianNum($read['tbl_interview_viewdate']) . '</td>
                            <td class="col-sm-1">' . @$readMentor['tbl_user_name'] . '</div>
                            <td class="col-sm-1">' . $rank . '</div></td>
                            </tr>
                            <tr>
                            <td class="col-sm-1 btn-class" colspan="9">
                            <a href="?do=edit&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-success">' . $lang[23] . '</a>
                            <a href="?do=delete&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
                            <a href="?do=report&code=' . $read['tbl_interview_code'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-warning">' . (!empty($read['tbl_interview_rank']) && $read['tbl_interview_rank'] !='0' ? $lang[858] : $lang[856]) . '</a></td>
                            </tr>';
                            $i++;
                        }
                        echo '</tbody></table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-secondary update-btn" style="display:none;"></form>';
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
                        echo '<div class="col-lg-6">
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
                                      <label>توضیحات<span class="language">' . $language[$i] . '</span></label>
                                      <textarea name="text_' . $language[$i] . '" rows="8"  class="editor editor_' . $language[$i] . '"></textarea>
                                      </div>';
                        }

                        for ($i = 1; $i <= $img_item; $i++) {
                            echo '<div class="form-group"><table><tr>
                                  <td><label class="col-sm-12 control-label">فایل&nbsp; ' . $i . '</label></td>
                                  <td><input type="file" name="img_' . $i . '"></td>
                                  </tr></table></div>';
                        }

                        echo '<div class="form-group">
                        <label>تاریخ</label>
                        <input type="text" name="expire" class="form-control datepicker" >
                        </div>
                        <div class="form-group">
                        <label>ساعت</label>
                        <input type="text" name="time" class="form-control" >
                        </div>
                        <div class="form-group">
                        <label>محل مصاحبه</label>
                        <input type="text" name="location" class="form-control" >
                        </div>';

                        echo '<div class="col-12 col-md-12"><div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary"></div>
                            </div></form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = Code("", "no", 7);
                            $date = $_POST['expire'];
                            $user = $_POST['user'];
                            $time = $_POST['time'];
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
                                    tbl_interview_model) 
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
                                    '0')");
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
                        echo '<div class="col-lg-6">
                            <form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>توضیحات<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" rows="8"  class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_interview_reporttext') . '</textarea>
                                          </div>';
                        }
                        echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_reportfile') . '">';
                        for ($i = 1; $i <= $img_item; $i++) {
                            @$exp = explode(",", read_fild(NULL, 'tbl_interview_reportfile'));
                            if ($exp[$i - 1]) {
                                echo '<div class="form-group"><table><tr>
                                      <td><label class="col-sm-12 control-label">فایل&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a><a href="?code=' . $code . '&do=deletereportimage&img=' . $exp[$i - 1] . '" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt"></span></span></a>
                                      </td></tr></table></div>';
                            } else {
                                echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">فایل&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '">
                                      </td></tr></table></div>';
                            }
                        }
                        echo '<hr>';

                        echo '<div class="form-group">
                            <label>تاریخ گزارش و نتیجه</label>
                            <input type="text" name="date" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_reportdate') . '" >
                            </div>';
                            echo '<div class="form-group">
                            <label>نمره مصاحبه </label>
                            <select class="form-control select2" name="rank" ><option value="0">نمره را انتخاب نمایید</option>';
                                for ($i=10; $i <= 100 ; $i++) { 
                                    echo '<option value="'.$i.'" ' . (read_fild(NULL, 'tbl_interview_rank') == $i ? 'selected' : '') . '>'.$i.'</option>';
                                }
                            echo '</select>
                            </div>';
                        echo '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[857] . '" class="btn btn-primary" >
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
                        echo '<div class="col-lg-6">
                            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" value="' . $_GET['user'] . '" name="user">
                            <input type="hidden" value="' . $_GET['contract'] . '" name="contract">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>عنوان<span class="language">' . $language[$i] . '</span></label>
                                          <input type="text" name="title_' . $language[$i] . '"
                                                                class="form-control" value="' . read_fild($language[$i], 'tbl_interview_title') . '">
                                          </div>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                                          <label>توضیحات<span class="language">' . $language[$i] . '</span></label>
                                          <textarea name="text_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_interview_text') . '</textarea>
                                          </div>';
                        }

                        echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_interview_file') . '">';
                        for ($i = 1; $i <= $img_item; $i++) {

                            $exp = explode(",", read_fild(NULL, 'tbl_interview_file'));
                            if ($exp[$i - 1]) {
                                echo '<div class="form-group"><table><tr>
                                      <td><label class="col-sm-12 control-label">فایل&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a><a href="?code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt"></span></span></a>
                                      </td></tr></table></div>';
                            } else {
                                echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">فایل&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '">
                                      </td></tr></table></div>';
                            }
                        }
                        echo '<hr>';

                        echo '<div class="form-group">
                            <label>تاریخ</label>
                            <input type="text" name="expire" class="form-control datepicker" value="' . read_fild(NULL, 'tbl_interview_date') . '" >
                            </div>
                            <div class="form-group">
                            <label>ساعت</label>
                            <input type="text" name="time" class="form-control" value="' . read_fild(NULL, 'tbl_interview_time') . '">
                            </div>
                            <div class="form-group">
                            <label>محل مصاحبه</label>
                            <input type="text" name="location" class="form-control" value="' . read_fild(NULL, 'tbl_interview_location') . '">
                            </div>';
                        echo '<div class="form-group">
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary" >
                            </div>
                            </form></div>';
                    }

                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $expire = $_POST['expire'];
                            $contract = $_POST['contract'];
                            $user = $_POST['user'];
                            $time = $_POST['time'];
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
                                    tbl_interview_location='$location' where tbl_interview_lng='$language_name' and tbl_interview_code='$code'");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: interview.php?user=$user&contract=$contract ");
                        }
                    }

                    if (isset($do) && $do == 'deleteimage') {

                        $result = Query("SELECT * from tbl_interview Where ( tbl_interview_model='0' or tbl_interview_model is NULL) and tbl_interview_code = '$code' ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-8 col-lg-offset-6">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $read['tbl_interview_menti'] . '" name="user" >
                         <input type="hidden" value="' . $read['tbl_interview_contract'] . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-xs btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger"></form></div>';
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
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_interview_title'] . '</h2><br /><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="id" >
                         <input type="hidden" value="' . $_GET['user'] . '" name="user" >
                         <input type="hidden" value="' . $_GET['contract'] . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            Query("update tbl_interview set  tbl_interview_delete='1' Where tbl_interview_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: interview.php?user=$user&contract=$contract");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
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