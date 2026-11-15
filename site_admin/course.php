<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مباحث ';
$Tabpage = '25';
$img_item = 10;
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
                    $result = Query("SELECT * From tbl_courseparent where tbl_courseparent_code='$_GET[class]' ");
                    $read = mysqli_fetch_array($result);

                    echo '<section class="content-header">
                  <h1>' . $pagetitle . ' / <a href="parentcourse.php?class=' . $read['tbl_courseparent_class'] . '">' . @$read['tbl_courseparent_title'] . '</a></h1>
                </section>
                    <section class="content"><div class="col-md-12">
            <div class="box box-body">';
                    // list content
                    if (!isset($do)) {
                        if (!empty($_GET['class'])) {
                            $thiscalss = " tbl_course_class = '$_GET[class]' and ";
                        } else {
                            header("Location: class.php");
                            exit();
                        }
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary big_button" href="?do=add&class=' . $_GET['class'] . '" >افزودن مبحث</a></div>';

                        $result = Query("SELECT * From tbl_course where $thiscalss tbl_course_id !='' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_course where $thiscalss tbl_course_id !='' ORDER BY tbl_course_order DESC ,tbl_course_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="class" value="' . $_GET['class'] . '"><input type="hidden" name="page" value="' . $page . '">
    <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>ترتیب</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_course_admin'];
                            $result_admin = Query("select * from tbl_user where tbl_user_id='$admin' ");
                            $read_admin = mysqli_fetch_array($result_admin);

                            $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_course_code]'");
                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>
               <td class="col-sm-1"><input type="hidden" name="code[]" value="' . $read['tbl_course_code'] . '"><input type="text" name="o' . $read['tbl_course_code'] . '" value="' . $read['tbl_course_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>'
                                . '<td class="col-sm-2"><span style="' . ($read['tbl_course_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_course_title'] . '' . ($read['tbl_course_free'] == 1 ? ' [رایگان]' : '') . '</span></td>'
                                . '<td class="col-sm-3">'
                                . '<a href="?do=edit&id=' . $read['tbl_course_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                <a href="?do=delete&id=' . $read['tbl_course_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
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
                            echo '<table class="table-striped margin-top"><tr><td class="col-sm-6"><ul class="pagination pagination-sm">
       <li><a href="?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . '">' . $lang[74] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . '">' . $lang[75] . '</a></li></ul></td>
       <td class="col-sm-1">
       <form action="course.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">' . $lang[76] . '</button>
       </form></td></tr></table>';
                        }
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $code = $_POST['code'];
                        $class = $_POST['class'];
                        $page = $_POST['page'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_course set tbl_course_order='$val' Where tbl_course_code = '$value' ");
                        }
                        header("Location: course.php?class=$class&page=$page");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: course.php?page=$page_number");
                        }
                    }
                    if (isset($do) && $do == 'add') {
                    ?>
                        <div class="col-lg-6">
                            <form action="course.php?do=additem" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="class" value="<?= $_GET['class'] ?>">
                                <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                  <label>' . $lang[50] . '</label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" required>
                  </div>';
                                }
                                ?>
                                <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                            <label>' . $lang[51] . '</label>
                            <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                            </div>';
                                }
                                ?>
                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label>مدل آیکون</label>
                                <select name="icon" class="form-control select2">
                                    <option value="film">ویدیو</option>
                                    <option value="file-text2">متن</option>
                                    <option value="book">پاورپوینت</option>
                                    <option value="file">فایل</option>
                                    <option value="mic">پادکست</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>انتخاب استاد</label><select name="teacher" class="form-control select2">
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
                      <td><input type="file" name="img_' . $i . '">
                      <input type="text" name="ftplink_' . $i . '" class="form-control" placeholder="لینک فایل"></td>
                      </tr></table></div>';
                            }
                            ?>
                            <div class="form-group">
                                <label>رده بندی</label>
                                <input type="text" name="order" class="form-control">
                            </div>
                            <hr>
                            <div class="form-group check-style">
                                <label>مبحث فعال باشد<input type="checkbox" name="status" value="1"> </label>
                            </div>
                            <div class="form-group check-style">
                                <label>مبحث رایگان<input type="checkbox" name="free" value="1"> </label>
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
                            $order = $_POST['order'];
                            $class = $_POST['class'];
                            $systemcode = $_POST['systemcode'];
                            $classcode = $_POST['classcode'];
                            $icon = $_POST['icon'];
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $free = (!empty($_POST['free']) ? $_POST['free'] : '0');
                            $teacher = $_POST['teacher'];

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = $_POST['content_' . $language[$i]];
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: course.php?class=$class");
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
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/course/" . $img_[$i_image]);
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
                                Query("INSERT INTO tbl_course (
                tbl_course_content,
                tbl_course_title,
                tbl_course_classcode, 
                tbl_course_code, 
                tbl_course_class, 
                tbl_course_icon, 
                tbl_course_teacher, 
                tbl_course_img, 
                tbl_course_status, 
                tbl_course_dateadd,
                tbl_course_free,
                tbl_course_order) 
                VALUES (
                    '$content_[$i]',
                    '$title_[$i]', 
                    '$classcode', 
                    '$systemcode', 
                    '$class', 
                    '$icon',
                    '$teacher', 
                    '$images_name', 
                    '$status',
                    '$date', 
                    '$free',
                    '$order')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: course.php?class=$class");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_course Where tbl_course_code = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_course_title'];
                        echo '<form action="#" method="POST">
        <input type="hidden" name="id" value="' . $id . '" >
        <input type="hidden" value="' . $_GET['class'] . '" name="class" >
        <div class="form-group margin-top">
        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
        </form></div>';

                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $class = $_POST['class'];
                            Query("Delete From tbl_course Where tbl_course_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: course.php?class=$class");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: course.php?class=$class");
                        }
                    }
                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        $id = $_GET['id'];
                        $result = Query("SELECT * FROM tbl_course Where tbl_course_code ='$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $id . '" >
                  <input type="hidden" name="class" value="' . $read['tbl_course_class'] . '" >
                  <input type="hidden" name="code" value="' . $read['tbl_course_code'] . '" >';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . $read['tbl_course_title'] . '">
                  </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection($read['tbl_course_content']) . '</textarea>
                              </div>';
                        }
                        echo '</div><div class="col-lg-6"><div class="form-group">
    <label>مدل آیکون</label>
    <select name="icon" class="form-control select2">
        <option value="film" ' . ($read['tbl_course_icon'] == 'film' ? 'selected' : '') . '>ویدیو</option>
                <option value="file-text2" ' . ($read['tbl_course_icon'] == 'file-text2' ? 'selected' : '') . '>متن</option>
                <option value="book" ' . ($read['tbl_course_icon'] == 'text' ? 'selected' : '') . '>پاورپوینت</option>
                <option value="file" ' . ($read['tbl_course_icon'] == 'video' ? 'selected' : '') . '>فایل</option>
                <option value="mic" ' . ($read['tbl_course_icon'] == 'mic' ? 'selected' : '') . '>پادکست</option>

    </select>
</div>';

                        echo '<div class="form-group" >
        <label>انتخاب استاد</label><select name="teacher" class="form-control select2" >';
                        $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_teacher_id'] . '"  ' . ($read_access['tbl_teacher_id'] == $read['tbl_course_teacher'] ? 'selected' : '') . '>' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                        }

                        echo '</select></div>';
                        echo '<input type="hidden" name="allimages" value="' . $read['tbl_course_img'] . '">';
                        for ($i = 1; $i <= $img_item; $i++) {
                            $exp = explode(",", $read['tbl_course_img']);
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
                        echo '<div class="form-group" >
                <label>رده بندی</label>
                <input type="text" name="order" class="form-control" value="' . $read['tbl_course_order'] . '">
                </div><hr>
                <div class="form-group check-style">
                  <label>مبحث فعال باشد<input type="checkbox" name="status" value="1" ' . ($read['tbl_course_status'] == 1 ? 'checked' : '') . '> </label>
                  </div>
                  <div class="form-group check-style">
                  <label>مبحث رایگان<input type="checkbox" name="free" value="1" ' . ($read['tbl_course_free'] == 1 ? 'checked' : '') . '> </label>
                  </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                    }
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['update'])) {
                            $order = $_POST['order'];
                            $code = $_POST['code'];
                            $class = $_POST['class'];
                            $id = $_POST['id'];
                            $icon = $_POST['icon'];
                            $status = $_POST['status'];
                            $teacher = $_POST['teacher'];
                            $free = $_POST['free'];
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = $_POST['content_' . $language[$i]];
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: course.php?class=$class");
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
                                                move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/course/" . $img_[$i_image]);
                                                unlink('../upload/course/' . $read_allimages[$i_image - 1]);
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
                                Query("Update tbl_course SET  
            tbl_course_order='$order',
            tbl_course_free='$free',
            tbl_course_content='$content_[$i]',
            tbl_course_title='$title_[$i]', 
            tbl_course_classcode='$classcode', 
            tbl_course_icon='$icon', 
            tbl_course_teacher='$teacher', 
            tbl_course_img='$images_name', 
            tbl_course_status='$status'
             where  tbl_course_code='$id'");

                                $_SESSION['error'] = $lang[21];
                                header("Location: course.php?class=$class");
                            }
                        }
                    }
                    if (isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_course Where tbl_course_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="../upload/course/'  . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="hidden" value="' . $_GET['class'] . '" name="class" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $id = $_POST['id'];
                            $class = $_POST['class'];
                            // read old image record
                            $record = str_replace("$img", "", $read['tbl_course_img']);
                            $external_file = '../upload/course/' . $img;
                            unlink("$external_file");
                            Query("Update tbl_course SET tbl_course_img = '$record' Where tbl_course_id = '$id' ");
                            header("Location: course.php?do=edit&id=$id&class=$class");
                        } elseif (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: course.php?do=edit&id=$id&class=$class");
                        }
                    }
                    echo '</div></div></section>';
                    include './footer.inc.php';
