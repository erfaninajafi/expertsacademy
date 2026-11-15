<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مدیریت دوره ها ';
$Tabpage = '25';
$img_item = 3;
$thisaccess = '1,4';
include './header.inc.php';
?>

<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!empty($_GET['cat'])) {
                        $cat_select = " and tbl_class_cat = '$_GET[cat]'  ";
                    } else {
                        $cat_select = "";
                    }


                    $result = Query("SELECT * From tbl_class where tbl_class_id !='' $cat_select ORDER BY tbl_class_id DESC");
                    // start count db result for pagination               
                    $count_result = mysqli_num_rows($result);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);


                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary big_button" href="?do=add" >افزودن دوره</a></div>';
                        $result = Query("SELECT * From tbl_class where tbl_class_id !='' $cat_select ORDER BY tbl_class_order DESC,tbl_class_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="page" value="' . $page . '">
                       <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>ترتیب</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>کد دوره</th>
                                    <th>نام دسته</th>
                                    <th>تاریخ بروز رسانی</th>
                                    <th>مبلغ</th>
                                    <th>درسها</th>
                                    <th>ثبت نام ها</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_class_admin'];
                            $result_admin = Query("select * from tbl_user where tbl_user_id='$admin' ");
                            $read_admin = mysqli_fetch_array($result_admin);
                            $cat = $read['tbl_class_cat'];

                            $result_cat = Query("SELECT tbl_categories_title FROM tbl_categories Where tbl_categories_id =  '$cat'");
                            $read_cat = mysqli_fetch_array($result_cat);

                            $result_course = Query("select * from tbl_courseparent where tbl_courseparent_class='$read[tbl_class_code]' ");
                            $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_class_code]'");

                            $result_question = Query("select * from tbl_question where tbl_question_disable='0' and tbl_question_parent='$read[tbl_class_code]'");

                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>
        <td class="col-sm-1"><input type="hidden" name="code[]" value="' . $read['tbl_class_code'] . '"><input type="text" name="o' . $read['tbl_class_code'] . '" value="' . $read['tbl_class_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>'
                                . '<td class="col-sm-2"><span style="' . ($read['tbl_class_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_class_title'] . ($read['tbl_class_special'] == 1 ? '&nbsp;[رایگان]' : '') . '</span></td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_classcode'] . '</td>'
                                . '<td class="col-sm-1">' . $read_cat['tbl_categories_title'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_class_update'] . '</td>'
                                . '<td class="col-sm-1">' . number_format($read['tbl_class_price']) . '</td>'
                                . '<td class="col-sm-1">' . mysqli_num_rows($result_course) . '</td>'
                                . '<td class="col-sm-1">' . mysqli_num_rows($result_register) . '</td>'
                                . '<td class="col-sm-3">'
                                . '<a href="parentcourse.php?class=' . $read['tbl_class_code'] . '" class="btn btn-xs btn-success" >مدیریت درس ها</a>
                <a href="questionclass.php?code=' . $read['tbl_class_code'] . '" class="btn btn-xs btn-warning" >سوالات چهار جوابی (' . mysqli_num_rows($result_question) . ')</a>'
                                . '<a href="?do=edit&id=' . $read['tbl_class_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
               <a href="?do=delete&id=' . $read['tbl_class_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning" ></form>';

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
                                            <li><a class="page-link" href="?page=<?= ($back_page_number - 1) . (!empty($_GET['cat']) ? '&cat=' . $_GET['cat'] : '') . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a></li>
                                            <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a></li>

                                            <li><a class="page-link" href="?page=<?= ($page + 1) . (!empty($_GET['cat']) ? '&cat=' . $_GET['cat'] : '') . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a></li>
                                        </ul>
                                    </td>
                                    <td class="col-lg-6">
                                        <form action="class.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                            <div class="form-group">
                                                <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                                <input type="text" class="form-control" name="page_number" style="height: 31px; width:30px; padding:6px 8px;float: left;margin-left: 10px;">
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        <?php
                        }
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $code = $_POST['code'];
                        $page = $_POST['page'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_class set tbl_class_order='$val' Where tbl_class_code = '$value' ");
                        }
                        header("Location: class.php?page=$page");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: class.php?page=$page_number");
                        }
                    }
                    if (isset($do) && $do == 'add') {
                        ?>
                        <div class="col-lg-6">
                            <form action="class.php?do=additem" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                                <div class="form-group">
                                    <label>گروه بندی کلاس</label><select name="cat" class="form-control select3">
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
                  <label>' . $lang[50] . '</label>
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
                            <label>' . $lang[51] . '</label>
                            <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                            </div>';
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                            <label>توضیحات امتحان پایان دوره</label>
                            <textarea name="contentend_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
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
                                <label>انتخاب گواهی نامه</label>
                                <?php
                                $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '0' ");
                                while ($read_cer = mysqli_fetch_array($result_cer)) {
                                    echo '<div class="checkbox"><label><input type="checkbox" name="cer[]" value="' . $read_cer['tbl_certificate_id'] . '">' . $read_cer['tbl_certificate_title'] . ' ، قیمت : ' . number_format($read_cer['tbl_certificate_price']) . '</label></div>';
                                }
                                ?>
                            </div>
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
                                <label>آخرین تاریخ بروز رسانی</label>
                                <input type="text" name="update" class="form-control datepicker">
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
                                <label>تعداد سوالات هر کاربر</label>
                                <input type="text" name="question" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>رده بندی</label>
                                <input type="text" name="order" class="form-control">
                            </div>
                            <?php
                            for ($i = 1; $i <= $img_item; $i++) {
                                echo '<div class="form-group"><table><tr>
                      <td><label class="col-sm-12 control-label">' . $lang[1153] . '&nbsp; ' . $i . '</label></td>
                      <td><input type="file" name="img_' . $i . '">
                      <input type="text" name="ftplink_' . $i . '" class="form-control" placeholder="لینک فایل"></td>
                      </tr></table></div>';
                            }
                            ?>
                            <hr>
                            <div class="form-group check-style">
                                <label>دوره فعال باشد<input type="checkbox" name="status" value="1"> </label>
                            </div>
                            <div class="form-group check-style">
                                <label>دوره رایگان<input type="checkbox" name="special" value="1"> </label>
                            </div>
                            <div class="form-group check-style">
                                <label>ارسال پاسخگویی برای مدیر دوره<input type="checkbox" name="qmodir" value="1"> </label>
                            </div>
                            <div class="form-group check-style">
                                <label>ارسال تمرین پایان دوره بعد از اتمام درس ها<input type="checkbox" name="tamrin" value="1"> </label>
                            </div>
                            <div class="form-group check-style">
                                <label>فعال بودن امتحان پایان دوره<input type="checkbox" name="emtehan" value="1"> </label>
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
                            $logo = $_POST['logo'];
                            $emtehan = $_POST['emtehan'];
                            $tamrin = $_POST['tamrin'];
                            $question = $_POST['question'];
                            $mortabet = $_POST['mortabet'];
                            $modelclass = $_POST['modelclass'];
                            $sath = $_POST['sath'];
                            $pish = $_POST['pish'];
                            $time = $_POST['time'];
                            $order = $_POST['order'];
                            $admin = $_POST['admin'];
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
                            $qmodir = $_POST['qmodir'];
                            $teacher_all = '';
                            foreach ($teacher as $key => $value) {
                                $teacher_all .= '[' . $value . '],';
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $contentend_[$i] = addslashes($_POST['contentend_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: class.php");
                                    exit();
                                }
                            }


                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                    if (!empty($img_[$i_image])) {
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                        if ($extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                        } else {
                                            $images_name .= ',';
                                        }
                                    } else {
                                        if (!empty($_POST['ftplink_' . $i_image])) {
                                            $images_name .= $_POST['ftplink_' . $i_image] . ',';
                                        } else {
                                            $images_name .= ',';
                                        }
                                    }
                                }
                            }

                            $language_name = 'fa';
                            $date = jdate("Y/m/d : A:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("INSERT INTO tbl_class (
                tbl_class_content,
                tbl_class_contentend,
                tbl_class_cat,
                tbl_class_title,
                tbl_class_classcode, 
                tbl_class_code, 
                tbl_class_price, 
                tbl_class_teacher, 
                tbl_class_img, 
                tbl_class_status, 
                tbl_class_dateadd,
                tbl_class_admin,
                tbl_class_parentcode,
                tbl_class_special,
                tbl_class_order,
                tbl_class_update,
                tbl_class_certificate,
                tbl_class_question,
                tbl_class_logo,
                tbl_class_mortabet,
                tbl_class_sath,
                tbl_class_time,
                tbl_class_pish,
                tbl_class_tamrin,
                tbl_class_emtehan,
                tbl_class_modelclass,
                tbl_class_qmodir) 
                VALUES (
                    '$content_[$i]',
                    '$contentend_[$i]',
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
                    '$logo',
                    '$mortabet',
                    '$sath',
                    '$time',
                    '$pish',
                    '$tamrin',
                    '$emtehan',
                    '$modelclass',
                    '$qmodir'
                    )");
                                $_SESSION['error'] = $lang[32];
                                header("Location: class.php");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_class Where tbl_class_code = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_class_title'];
                        echo '<form action="#" method="POST">
        <input type="hidden" name="id" value="' . $id . '" >
        <input type="hidden" name="code" value="' . $read['tbl_class_code'] . '" >
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
                                Query("Delete From tbl_class Where tbl_class_code = '$id' ");
                                $_SESSION['error'] = $lang[28];
                            }

                            header("Location: class.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: class.php");
                        }
                    }
                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        $id = $_GET['id'];
                        $result = Query("SELECT * FROM tbl_class Where tbl_class_code ='$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $id . '" >
                  <input type="hidden" name="code" value="' . $read['tbl_class_code'] . '" >';
                        // end read categories
                        echo '<div class="form-group" >
                  <label>گروه بندی کلاس</label><select name="cat" class="form-control select3" >';
                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='3' order by tbl_categories_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_categories_id'] . '-' . $read_access['tbl_categories_catcode'] . '" ' . ($read['tbl_class_cat'] == $read_access['tbl_categories_id'] ? 'selected' : '') . '>' . $read_access['tbl_categories_title'] . '</option>';
                        }

                        echo '</select></div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . $read['tbl_class_title'] . '">
                  </div>';
                        }
                        echo '<div class="form-group" >
                  <label>کد دوره</label>
                  <input type="text" name="classcode" class="form-control" value="' . $read['tbl_class_classcode'] . '">
                  </div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection($read['tbl_class_content']) . '</textarea>
                              </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                    <label>توضیحات امتحان پایان دوره</label>
                    <textarea name="contentend_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . $read['tbl_class_contentend'] . '</textarea>
                    </div>';
                        }
                        echo '</div><div class="col-lg-6"><div class="form-group" >
                  <label>' . $lang[1152] . '</label>
                  <input type="text" name="price" class="form-control" value="' . $read['tbl_class_price'] . '">
                  </div><div class="form-group">
                  <label>انتخاب گواهی نامه</label>';
                        $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '0' ");
                        while ($read_cer = mysqli_fetch_array($result_cer)) {
                            echo '<div class="checkbox"><label><input type="checkbox" ' . (strpos($read['tbl_class_certificate'], $read_cer['tbl_certificate_id'] . ',') !== false ? 'checked' : '') . ' name="cer[]" value="' . $read_cer['tbl_certificate_id'] . '">' . $read_cer['tbl_certificate_title'] . ' ، قیمت : ' . number_format($read_cer['tbl_certificate_price']) . '</label></div>';
                        }
                        echo '</div>';

                        echo '<div class="form-group">
                  <label>مدت زمان دوره</label>
                  <input type="text" name="time" class="form-control" value="' . $read['tbl_class_time'] . '">
              </div>
              <div class="form-group">
                  <label>دوره ها و منابع مرتبط</label>
                  <input type="text" name="mortabet" class="form-control" value="' . $read['tbl_class_mortabet'] . '">
              </div>
              <div class="form-group">
                  <label>سطح فراگیران</label>
                  <input type="text" name="sath" class="form-control" value="' . $read['tbl_class_sath'] . '">
              </div>
              <div class="form-group">
                  <label>پیش نیاز دوره</label>
                  <input type="text" name="pish" class="form-control" value="' . $read['tbl_class_pish'] . '">
              </div><div class="form-group">
              <label>نحوه ارائه دوره</label>
              <input type="text" name="modelclass" class="form-control" value="' . $read['tbl_class_modelclass'] . '">
          </div>';
                        echo '<div class="form-group" >
                  <label>آخرین تاریخ بروز رسانی</label>
                  <input type="text" name="updatedate" class="form-control datepicker" value="' . $read['tbl_class_update'] . '" >
                  </div>';
                        $teacher = $read['tbl_class_teacher'];
                        echo '<div class="form-group" >
        <label>انتخاب استاد</label><select name="teacher[]" class="form-control select2" multiple="multiple">';
                        $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            $thisid = '[' . $read_access['tbl_teacher_id'] . ']';
                            echo '<option value="' . $read_access['tbl_teacher_id'] . '"  ' . (strpos($teacher, "$thisid") !== false ? 'selected' : '') . '>' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                        }

                        echo '</select></div>';


                        echo '<div class="form-group">
            <label>انتخاب برگزار کننده</label><select name="logo" class="form-control select2">';
                        $result_access = Query("Select * from tbl_logo order by tbl_logo_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_logo_id'] . '" ' . ($read_access['tbl_logo_id'] == $read['tbl_class_logo']  ? 'selected' : '') . '>' . $read_access['tbl_logo_title'] . '</option>';
                        }
                        echo '</select>
        </div><div class="form-group">
    <label>تعداد سوالات هر کاربر</label>
    <input type="text" name="question" class="form-control" value="' . $read['tbl_class_question'] . '">
</div><div class="form-group" >
                <label>رده بندی</label>
                <input type="text" name="order" class="form-control" value="' . $read['tbl_class_order'] . '">
                </div>';
                        echo '<input type="hidden" name="allimages" value="' . $read['tbl_class_img'] . '">';
                        for ($i = 1; $i <= $img_item; $i++) {
                            $exp = explode(",", $read['tbl_class_img']);
                            if ($exp[$i - 1]) {
                                echo '<div class="form-group"><table><tr>
                          <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                          <td><input type="file" name="img_' . $i . '">
                          <input type="text" name="ftplink_' . $i . '" value="' . $exp[$i - 1] . '" class="form-control" placeholder="لینک فایل">
                          </td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="glyphicon glyphicon-camera  btn-xs"></span></span></a><a href="?id=' . $id . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn btn-xs btn-danger"><span class="glyphicon glyphicon-remove  btn-xs"></span></span></a>
                          </td></tr></table></div>';
                            } else {
                                echo '<div class="form-group"><table ><tr>
                          <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                          <td><input type="file" name="img_' . $i . '">
                          <input type="text" name="ftplink_' . $i . '" class="form-control" placeholder="لینک فایل">
                          </td></tr></table></div>';
                            }
                        }
                        echo '<hr>
                <div class="form-group check-style">
                  <label>دوره فعال باشد<input type="checkbox" name="status" value="1" ' . ($read['tbl_class_status'] == 1 ? 'checked' : '') . '> </label>
                  </div>
                  <div class="form-group check-style">
                  <label>دوره رایگان<input type="checkbox" name="special" value="1" ' . ($read['tbl_class_special'] == 1 ? 'checked' : '') . '> </label>
                  </div><div class="form-group check-style">
                  <label>ارسال پاسخگویی برای مدیر دوره<input type="checkbox" name="qmodir" value="1" ' . ($read['tbl_class_qmodir'] == 1 ? 'checked' : '') . '> </label>
              </div><div class="form-group check-style">
              <label>ارسال تمرین پایان دوره بعد از اتمام درس ها<input type="checkbox" name="tamrin" value="1" ' . ($read['tbl_class_tamrin'] == 1 ? 'checked' : '') . '> </label>
          </div><div class="form-group check-style">
          <label>فعال بودن امتحان پایان دوره<input type="checkbox" name="emtehan" value="1" ' . ($read['tbl_class_emtehan'] == 1 ? 'checked' : '') . '> </label>
      </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                    }
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['update'])) {
                            $order = $_POST['order'];
                            $admin = $_POST['admin'];
                            $tamrin = $_POST['tamrin'];
                            $emtehan = $_POST['emtehan'];
                            $update = $_POST['updatedate'];
                            $code = $_POST['code'];
                            $modelclass = $_POST['modelclass'];
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
                            $mortabet = $_POST['mortabet'];
                            $sath = $_POST['sath'];
                            $pish = $_POST['pish'];
                            $time = $_POST['time'];
                            $qmodir = $_POST['qmodir'];
                            $teacher_all = '';
                            foreach ($teacher as $key => $value) {
                                $teacher_all .= '[' . $value . '],';
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $contentend_[$i] = addslashes($_POST['contentend_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: class.php");
                                    exit();
                                }
                            }
                            $allimages = $_POST['allimages'];
                            $read_allimages = explode(",", $allimages);


                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                    if (!empty($img_[$i_image])) {
                                        if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                            $images_name .= $read_allimages[$i_image - 1] . ',';
                                        } else {
                                            // remove old image and replace with new
                                            $read_file_name = explode(".", $img_[$i_image]);
                                            $extn = end($read_file_name);
                                            $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                            // $images_name = all images after rename array
                                            if ($extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
                                                $images_name .= $img_[$i_image] . ',';
                                                move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                                unlink($directory . $read_allimages[$i_image - 1]);
                                            } else {
                                                $images_name .= $read_allimages[$i_image - 1] . ',';
                                            }
                                        }
                                    } else {
                                        if (!empty($_POST['ftplink_' . $i_image])) {
                                            $images_name .= $_POST['ftplink_' . $i_image] . ',';
                                        } else {
                                            $images_name .= ',';
                                        }
                                    }
                                }
                            }

                            // end upload image
                            Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("Update tbl_class SET  
            tbl_class_order='$order',
            tbl_class_special='$special',
            tbl_class_admin='$admin',
            tbl_class_cat='$cat[0]',
            tbl_class_parentcode='$cat[1]', 
            tbl_class_content='$content_[$i]',
            tbl_class_contentend='$contentend_[$i]',
            tbl_class_question='$question',
            tbl_class_title='$title_[$i]', 
            tbl_class_classcode='$classcode', 
            tbl_class_price='$price', 
            tbl_class_teacher='$teacher_all', 
            tbl_class_img='$images_name', 
            tbl_class_status='$status',
            tbl_class_update='$update',
            tbl_class_certificate='$certificate',
            tbl_class_logo='$logo',
            tbl_class_mortabet='$mortabet',
            tbl_class_sath='$sath',
            tbl_class_time='$time',
            tbl_class_pish='$pish',
            tbl_class_pish='$pish',
            tbl_class_emtehan='$emtehan',
            tbl_class_modelclass='$modelclass',
            tbl_class_tamrin='$tamrin'
             where  tbl_class_code='$id'");

                                $_SESSION['error'] = $lang[21];
                                header("Location: class.php");
                            }
                        }
                    }
                    if (isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_class Where tbl_class_id = '$id' ");
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
                            $record = str_replace("$img", "", $read['tbl_class_img']);
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_class SET tbl_class_img = '$record' Where tbl_class_id = '$id' ");
                            header("Location: class.php?do=edit&id=$id");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: class.php?do=edit&id=$id");
                        }
                    }
                    echo '</div></div></section>';
                    include './footer.inc.php';
