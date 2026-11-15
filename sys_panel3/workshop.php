<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مدیریت کارگاه ها ';
$Tabpage = '25';
$img_item = 1;
$thisaccess = '1,4';
include './header.inc.php';
$result = Query("SELECT * From tbl_workshop ORDER BY tbl_workshop_id DESC");
// start count db result for pagination               
$count_result = mysqli_num_rows($result);
$start_contnet =  ($page - 1) * $show_per_page;
$page_number = ceil($count_result / $show_per_page);
// end count db result for pagination 
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php

                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary big_button" href="?do=add" >افزودن دوره</a></div>';
                        $result = Query("SELECT * From tbl_workshop ORDER BY tbl_workshop_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>کد دوره</th>
                                    <th>تاریخ برگزاری</th>
                                    <th>تاریخ شروع ثبت نام</th>
                                    <th>تاریخ پایان ثبت نام</th>
                                    <th>مبلغ</th>
                                    <th>ظرفیت</th>
                                    <th>ثبت نام ها</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_workshop_admin'];
                            $result_admin = Query("select * from tbl_user where tbl_user_id='$admin' ");
                            $read_admin = mysqli_fetch_array($result_admin);


                            $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_workshop_code]'");
                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>'
                                . '<td class="col-sm-2"><span style="' . ($read['tbl_workshop_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_workshop_title'] . ($read['tbl_workshop_free'] == 0 ? ''  : ' [رایگان]') . '</span></td>'
                                . '<td class="col-sm-1">' . $read['tbl_workshop_classcode'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_workshop_update'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_workshop_start'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_workshop_end'] . '</td>'
                                . '<td class="col-sm-1">' . number_format($read['tbl_workshop_price']) . '</td>'
                                . '<td class="col-sm-1">' . number_format($read['tbl_workshop_count']) . '</td>'
                                . '<td class="col-sm-1">' . mysqli_num_rows($result_register) . '</td></tr><tr>'
                                . '<td class="col-sm-1 btn-class" colspan="9"><a href="register.php?historyworkshop=' . $read['tbl_workshop_code'] . '" target="_blank" class="btn btn-xs btn-warning " > ثبت نام ها</a>'
                                . '<a href="?do=edit&id=' . $read['tbl_workshop_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
               <a href="?do=delete&id=' . $read['tbl_workshop_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table>';

                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        if ($count_result > $show_per_page) {
                            if ($page == 1) {
                                $back_page_number = 2;
                            } else {
                                $back_page_number = $page;
                            }

                            // $other_page_keyword = '';
                            if (isset($keyword) && $keyword == TRUE) {
                                $other_page_keyword = "&keyword=";
                            } else {
                                $other_page_keyword = '';
                            }
                    ?>
                    <table class="table">
                        <tr>
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="workshop.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                        <input type="text" class="form-control go-fld" name="page_number">
                                    </div>
                                </form>
                            </td>
                        </tr>
                    </table>
                    <?php
                        }
                    }

                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: workshop.php?page=$page_number");
                        }
                    }
                    if (isset($do) && $do == 'add') {
                        ?>
                    <div class="col-lg-6">
                        <form action="workshop.php?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                            <div class="form-group">
                                <label>گروه بندی کلاس</label><select name="cat" class="form-control select2">
                                    <?php
                                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='3' order by tbl_categories_id DESC ");
                                        while ($read_access = mysqli_fetch_array($result_access)) {
                                            echo '<option value="' . $read_access['tbl_categories_id'] . '-' . $read_access['tbl_categories_catcode'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                        }
                                        ?>
                                </select>
                            </div>
                            <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" required>
                  </div>';
                                }
                                ?>
                            <div class="form-group">
                                <label>کد دوره</label>
                                <input type="text" name="classcode" class="form-control">
                            </div>
                            <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                            <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                            </div>';
                                }
                                ?>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?= $lang[1152] ?></label>
                            <input type="text" name="price" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>مدت زمان دوره</label>
                            <input type="text" name="time" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>دوره ها و منابع مرتبط</label>
                            <input type="text" name="mortabet" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>سطح فراگیران</label>
                            <input type="text" name="sath" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>پیش نیاز دوره</label>
                            <input type="text" name="pish" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>نحوه ارائه دوره</label>
                            <input type="text" name="modelclass" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>انتخاب گواهی نامه</label>
                            <?php
                                $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '1' ");
                                while ($read_cer = mysqli_fetch_array($result_cer)) {
                                    echo '<div class="checkbox"><label><input type="checkbox" name="cer[]" value="' . $read_cer['tbl_certificate_id'] . '"> ' . $read_cer['tbl_certificate_title'] . ' ، قیمت : ' . number_format($read_cer['tbl_certificate_price']) . '</label></div>';
                                }
                                ?>
                        </div>
                        <div class="form-group">
                            <label>تاریخ برگزاری</label>
                            <input type="text" name="update" class="form-control datepicker">
                        </div>
                        <div class="form-group">
                            <label>تاریخ شروع ثبت نام</label>
                            <input type="text" name="start" class="form-control datepicker">
                        </div>
                        <div class="form-group">
                            <label>تاریخ پایان ثبت نام</label>
                            <input type="text" name="end" class="form-control datepicker">
                        </div>
                        <div class="form-group">
                            <label>ظرفیت</label>
                            <input type="text" name="count" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>انتخاب استاد</label><select name="teacher[]" class="form-control select2" multiple="multiple">
                                <?php
                                    $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_teacher_id'] . '">' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                        <?php
                            for ($i = 1; $i <= $img_item; $i++) {
                                echo '<div class="form-group"><table><tr>
                      <td><label class="col-sm-12 control-label">' . $lang[1153] . '&nbsp; ' . $i . '</label></td>
                      <td><input type="file" name="img_' . $i . '"></td>
                      </tr></table></div>';
                            }
                            ?>
                        <div class="form-group">
                            <label>انتخاب برگزار کننده</label><select name="logo" class="form-control select2">
                                <?php
                                    $result_access = Query("Select * from tbl_logo order by tbl_logo_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_logo_id'] . '">' . $read_access['tbl_logo_title'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>رده بندی</label>
                            <input type="text" name="order" class="form-control">
                        </div>
                        <hr>
                        <div class="form-group check-style">
                            <label>کارگاه فعال باشد<input type="checkbox" name="status" value="1"> </label>
                            <label>کارگاه رایگان<input type="checkbox" name="free" value="1"> </label>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                        </div>
                        </form>
                    </div>
                    <?php
                    }

                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $question = $_POST['question'];
                            $order = $_POST['order'];
                            $start = $_POST['start'];

                            $mortabet = $_POST['mortabet'];
                            $modelclass = $_POST['modelclass'];
                            $sath = $_POST['sath'];
                            $pish = $_POST['pish'];
                            $time = $_POST['time'];

                            $end = $_POST['end'];
                            $free = $_POST['free'];
                            $admin = $_POST['admin'];
                            $count = $_POST['count'];
                            $logo = $_POST['logo'];
                            $systemcode = $_POST['systemcode'];
                            $classcode = $_POST['classcode'];
                            $cat = explode("-", $_POST['cat']);
                            $price = $_POST['price'];
                            $update = $_POST['update'];
                            $cer = $_POST['cer'];
                            foreach ($cer as $key => $value) {
                                $certificate .= $value . ',';
                            }
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $special = (!empty($_POST['special']) ? $_POST['special'] : '0');
                            $teacher = $_POST['teacher'];
                            $teacher_all = array();
                            foreach ($teacher as $key => $value) {
                                $teacher_all[$key + 1] = $value;
                            }
                            $teacher_all = json_encode($teacher_all);

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = imjection(strip_tags($_POST['title_' . $language[$i]]));
                                $content_[$i] = imjection(strip_tags($_POST['content_' . $language[$i]]));
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: workshop.php");
                                    exit();
                                }
                            }
                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                    if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                        $images_name .= $img_[$i_image] . ',';
                                        move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                    } elseif (!empty($exp[$i_image])) {
                                        $images_name .= $exp[$i_image] . ',';
                                    } else {
                                        $images_name .= ',';
                                    }
                                }
                            }
                            $language_name = 'fa';
                            $date = jdate("Y/m/d : A:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("INSERT INTO tbl_workshop (
                tbl_workshop_content,
                tbl_workshop_cat,
                tbl_workshop_title,
                tbl_workshop_classcode, 
                tbl_workshop_code, 
                tbl_workshop_price, 
                tbl_workshop_teacher, 
                tbl_workshop_img, 
                tbl_workshop_status, 
                tbl_workshop_dateadd,
                tbl_workshop_admin,
                tbl_workshop_parentcode,
                tbl_workshop_special,
                tbl_workshop_order,
                tbl_workshop_update,
                tbl_workshop_certificate,
                tbl_workshop_question,
                tbl_workshop_count,
                tbl_workshop_free,
                tbl_workshop_logo,
                tbl_workshop_start,
                tbl_workshop_mortabet,
                tbl_workshop_modelclass,
                tbl_workshop_sath,
                tbl_workshop_pish,
                tbl_workshop_time,
                tbl_workshop_end) 
                VALUES (
                    '$content_[$i]',
                    '$cat[0]', 
                    '$title_[$i]', 
                    '$classcode', 
                    '$systemcode', 
                    '$price', 
                    '$teacher_all', 
                    '$images_name', 
                    '$status',
                    '$date', 
                    '$admin',
                    '$cat[1]',
                    '$special',
                    '$order',
                    '$update',
                    '$certificate',
                    '$question',
                    '$count',
                    '$free',
                    '$logo',
                    '$start',
                    '$mortabet,
                    '$modelclass',
                    '$sath',
                    '$pish',
                    '$time',
                    '$end')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: workshop.php");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_workshop Where tbl_workshop_code = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_workshop_title'];
                        echo '<form action="#" method="POST">
        <input type="hidden" name="id" value="' . $id . '" >
        <input type="hidden" name="code" value="' . $read['tbl_workshop_code'] . '" >
        <div class="form-group margin-top">
        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
        </form></div>';

                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $result_course = Query("select * from tbl_course where tbl_course_class='$_POST[code]' ");
                            if (mysqli_num_rows($result_course) != 0) {
                                $_SESSION['error'] = 'به دلیل داشتن درس شما نمی توانید این دوره را حذف نمایید';
                            } else {
                                Query("Delete From tbl_workshop Where tbl_workshop_code = '$id' ");
                                $_SESSION['error'] = $lang[28];
                            }

                            header("Location: workshop.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: workshop.php");
                        }
                    }
                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        $id = $_GET['id'];
                        $result = Query("SELECT * FROM tbl_workshop Where tbl_workshop_code ='$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $id . '" >
                  <input type="hidden" name="code" value="' . $read['tbl_workshop_code'] . '" >';
                        // end read categories
                        echo '<div class="form-group" >
                  <label>گروه بندی کلاس</label><select name="cat" class="form-control select2" >';
                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='3' order by tbl_categories_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_categories_id'] . '-' . $read_access['tbl_categories_catcode'] . '" ' . ($read['tbl_workshop_cat'] == $read_access['tbl_categories_id'] ? 'selected' : '') . '> ' . $read_access['tbl_categories_title'] . '</option>';
                        }

                        echo '</select></div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . $read['tbl_workshop_title'] . '">
                  </div>';
                        }
                        echo '<div class="form-group" >
                  <label>کد دوره</label>
                  <input type="text" name="classcode" class="form-control" value="' . $read['tbl_workshop_classcode'] . '">
                  </div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection($read['tbl_workshop_content']) . '</textarea>
                              </div>';
                        }
                        echo '</div><div class="col-lg-6"><div class="form-group" >

    <div class="form-group">
            <label>مدت زمان دوره</label>
            <input type="text" name="time" class="form-control" value="' . $read['tbl_workshop_time'] . '">
        </div>
        <div class="form-group">
            <label>دوره ها و منابع مرتبط</label>
            <input type="text" name="mortabet" class="form-control" value="' . $read['tbl_workshop_mortabet'] . '">
        </div>
        <div class="form-group">
            <label>سطح فراگیران</label>
            <input type="text" name="sath" class="form-control" value="' . $read['tbl_workshop_sath'] . '">
        </div>
        <div class="form-group">
            <label>پیش نیاز دوره</label>
            <input type="text" name="pish" class="form-control" value="' . $read['tbl_workshop_pish'] . '">
        </div>
        <div class="form-group">
            <label>نحوه ارائه دوره</label>
            <input type="text" name="modelclass" class="form-control" value="' . $read['tbl_workshop_modelclass'] . '">
        </div>

                  <label>' . $lang[1152] . '</label>
                  <input type="text" name="price" class="form-control" value="' . $read['tbl_workshop_price'] . '">
                  </div>';

                        echo '<div class="form-group">
    <label>انتخاب گواهی نامه</label>';
                        $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '1' ");
                        while ($read_cer = mysqli_fetch_array($result_cer)) {
                            echo '<div class="checkbox"><label><input type="checkbox" ' . (strpos($read['tbl_workshop_certificate'], $read_cer['tbl_certificate_id'] . ',') !== false ? 'checked' : '') . ' name="cer[]" value="' . $read_cer['tbl_certificate_id'] . '"> ' . $read_cer['tbl_certificate_title'] . ' ، قیمت : ' . number_format($read_cer['tbl_certificate_price']) . '</label></div>';
                        }
                        echo '</div>';
                        echo '<div class="form-group">
                  <label>تاریخ برگزاری</label>
                  <input type="text" name="updatedate" class="form-control datepicker" value="' . $read['tbl_workshop_update'] . '">
              </div>
              <div class="form-group">
            <label>تاریخ شروع ثبت نام</label>
            <input type="text" name="start" class="form-control datepicker" value="' . $read['tbl_workshop_start'] . '">
        </div>
        <div class="form-group">
            <label>تاریخ پایان ثبت نام</label>
            <input type="text" name="end" class="form-control datepicker" value="' . $read['tbl_workshop_end'] . '">
        </div>
              <div class="form-group">
                  <label>ظرفیت</label>
                  <input type="text" name="count" class="form-control" value="' . $read['tbl_workshop_count'] . '">
              </div>';
                        $teacher = json_decode($read['tbl_workshop_teacher'], true);
                        echo '<div class="form-group" >
        <label>انتخاب استاد</label><select name="teacher[]" class="form-control select2" multiple="multiple">';
                        $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            $thisid = '[' . $read_access['tbl_teacher_id'] . ']';
                            echo '<option value="' . $read_access['tbl_teacher_id'] . '"  ' . (array_search("$read_access[tbl_teacher_id]", $teacher) == '' ? '' : 'selected') . '>' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                        }

                        echo '</select></div>';
                        echo '<input type="hidden" name="allimages" value="' . $read['tbl_workshop_img'] . '">';
                        for ($i = 1; $i <= $img_item; $i++) {
                            $exp = explode(",", $read['tbl_workshop_img']);
                            if ($exp[$i - 1]) {
                                echo '<div class="form-group"><table><tr>
                              <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                              <td><input type="file" name="img_' . $i . '"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a><a href="?id=' . $id . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a>
                              </td></tr></table></div>';
                            } else {
                                echo '<div class="form-group"><table ><tr>
                              <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                              <td><input type="file" name="img_' . $i . '">
                              </td></tr></table></div>';
                            }
                        }
                        echo '<div class="form-group">
            <label>انتخاب برگزار کننده</label><select name="logo" class="form-control select2">';
                        $result_access = Query("Select * from tbl_logo order by tbl_logo_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_logo_id'] . '" ' . ($read_access['tbl_logo_id'] == $read['tbl_workshop_logo']  ? 'selected' : '') . '>' . $read_access['tbl_logo_title'] . '</option>';
                        }
                        echo '</select>
        </div><div class="form-group" >
                <label>رده بندی</label>
                <input type="text" name="order" class="form-control" value="' . $read['tbl_workshop_order'] . '">
                </div><hr>
                <div class="form-group check-style">
                  <label>کارگاه فعال باشد<input type="checkbox" name="status" value="1" ' . ($read['tbl_workshop_status'] == 1 ? 'checked' : '') . '> </label>
                  <label>کارگاه رایگان<input type="checkbox" name="free" value="1" ' . ($read['tbl_workshop_free'] == 1 ? 'checked' : '') . '> </label>
                  </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                    }
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['update'])) {
                            $start = $_POST['start'];
                            $end = $_POST['end'];
                            $free = $_POST['free'];
                            $order = $_POST['order'];
                            $admin = $_POST['admin'];
                            $update = $_POST['updatedate'];
                            $code = $_POST['code'];
                            $count = $_POST['count'];

                            $mortabet = $_POST['mortabet'];
                            $modelclass = $_POST['modelclass'];
                            $sath = $_POST['sath'];
                            $pish = $_POST['pish'];
                            $time = $_POST['time'];


                            $cer = $_POST['cer'];
                            foreach ($cer as $key => $value) {
                                $certificate .= $value . ',';
                            }
                            $question = $_POST['question'];
                            $id = $_POST['id'];
                            $logo = $_POST['logo'];
                            $classcode = $_POST['classcode'];
                            $cat = explode("-", $_POST['cat']);
                            $price = $_POST['price'];
                            $status = $_POST['status'];
                            $teacher = $_POST['teacher'];
                            $special = $_POST['special'];
                            $teacher_all = array();
                            foreach ($teacher as $key => $value) {
                                $teacher_all[$key + 1] = $value;
                            }
                            $teacher_all = json_encode($teacher_all);

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = imjection(strip_tags($_POST['title_' . $language[$i]]));
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: workshop.php");
                                    exit();
                                }
                            }
                            $allimages = $_POST['allimages'];
                            $read_allimages = explode(",", $allimages);
                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                    if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    } else {
                                        // remove old image and replace with new                               

                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                        // $images_name = all images after rename array
                                        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                            // remove old image
                                            unlink($directory . $read_allimages[$i_image - 1]);
                                        } else {
                                            $images_name .= $read_allimages[$i_image - 1] . ',';
                                        }
                                    }
                                }
                            }
                            // end upload image
                            Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("Update tbl_workshop SET  
            tbl_workshop_order='$order',
            tbl_workshop_special='$special',
            tbl_workshop_admin='$admin',
            tbl_workshop_cat='$cat[0]',
            tbl_workshop_parentcode='$cat[1]', 
            tbl_workshop_content='$content_[$i]',
            tbl_workshop_question='$question',
            tbl_workshop_title='$title_[$i]', 
            tbl_workshop_classcode='$classcode', 
            tbl_workshop_price='$price', 
            tbl_workshop_teacher='$teacher_all', 
            tbl_workshop_img='$images_name', 
            tbl_workshop_status='$status',
            tbl_workshop_update='$update',
            tbl_workshop_free='$free',
            tbl_workshop_count='$count',
            tbl_workshop_certificate='$certificate',
            tbl_workshop_logo='$logo',
            tbl_workshop_start='$start', 
            tbl_workshop_mortabet='$mortabet', 
            tbl_workshop_modelclass='$modelclass', 
            tbl_workshop_sath='$sath', 
            tbl_workshop_pish='$pish', 
            tbl_workshop_time='$time', 
            tbl_workshop_end='$end' 
             where  tbl_workshop_code='$id'");

                                $_SESSION['error'] = $lang[21];
                                header("Location: workshop.php");
                            }
                        }
                    }
                    if (isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_workshop Where tbl_workshop_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $id = $_POST['id'];
                            // read old image record
                            $record = str_replace("$img", "", $read['tbl_workshop_img']);
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_workshop SET tbl_workshop_img = '$record' Where tbl_workshop_id = '$id' ");
                            header("Location: workshop.php?do=edit&id=$id");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: workshop.php?do=edit&id=$id");
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