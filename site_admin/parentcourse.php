<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مدیریت درسها  ';
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
                    $result = Query("SELECT * From tbl_class where tbl_class_code='$_GET[class]' ");
                    $read = mysqli_fetch_array($result);

                    // list content
                    if (!isset($do)) {
                        if (!empty($_GET['class'])) {
                            $thiscalss = " tbl_courseparent_class = '$_GET[class]' and ";
                        } else {
                            header("Location: class.php");
                            exit();
                        }
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary big_button" href="?do=add&class=' . $_GET['class'] . '" >افزودن درس</a></div>';

                        $result = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' ORDER BY tbl_courseparent_order DESC ,tbl_courseparent_id DESC  LIMIT $start_contnet,$show_per_page ");
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
                                    <th>تعداد مباحث</th>
                                    <th>عملیات</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_courseparent_admin'];
                            $result_cat = Query("SELECT * FROM tbl_course Where tbl_course_class =  '$read[tbl_courseparent_code]'");

                            $result_register = Query("select * from tbl_question where tbl_question_disable='0' and tbl_question_parent='$read[tbl_courseparent_code]'");
                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>
               <td class="col-sm-1"><input type="hidden" name="code[]" value="' . $read['tbl_courseparent_code'] . '"><input type="text" name="o' . $read['tbl_courseparent_code'] . '" value="' . $read['tbl_courseparent_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>'
                                . '<td class="col-sm-2"><span style="' . ($read['tbl_courseparent_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_courseparent_title'] . '' . ($read['tbl_courseparent_free'] == 1 ? ' [رایگان]' : '') . '</span></td>'
                                . '<td class="col-sm-1">' . mysqli_num_rows($result_cat) . '</td>
            <td class="col-sm-3">'
                                . '<a href="?do=edit&id=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
            <a href="?do=delete&id=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
            <a href="course.php?class=' . $read['tbl_courseparent_code'] . '" class="btn btn-xs btn-warning" >مباحث</a>
            <a href="question.php?code=' . $read['tbl_courseparent_code'] . '" class="btn btn-xs btn-warning" >سوالات چهار جوابی (' . mysqli_num_rows($result_register) . ')</a></td>'
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
       <li><a href="?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . '">' . $lang[74] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . '">' . $lang[75] . '</a></li></ul></td>
       <td class="col-sm-1">
       <form action="parentcourse.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            Query("update tbl_courseparent set tbl_courseparent_order='$val' Where tbl_courseparent_code = '$value' ");
                        }
                        header("Location: parentcourse.php?class=$class&page=$page");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: parentcourse.php?page=$page_number");
                        }
                    }
                    if (isset($do) && $do == 'add') {
                    ?>
                        <div class="col-lg-6">
                            <form action="parentcourse.php?do=additem" method="POST" enctype="multipart/form-data">
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

                                <div class="form-group">
                                    <label>رده بندی</label>
                                    <input type="text" name="order" class="form-control">
                                </div>
                                <hr>
                                <div class="form-group check-style">
                                    <label>درس فعال باشد<input type="checkbox" name="status" value="1"> </label>
                                </div>
                                <div class="form-group check-style">
                                    <label>درس رایگان<input type="checkbox" name="free" value="1"> </label>
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
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $free = (!empty($_POST['free']) ? $_POST['free'] : '0');

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: parentcourse.php?class=$class");
                                    exit();
                                }
                            }
                            $language_name = 'fa';
                            $date = jdate("Y/m/d : A:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("INSERT INTO tbl_courseparent (
                tbl_courseparent_title,
                tbl_courseparent_classcode, 
                tbl_courseparent_code, 
                tbl_courseparent_class, 
                tbl_courseparent_status, 
                tbl_courseparent_dateadd,
                tbl_courseparent_free,
                tbl_courseparent_order) 
                VALUES (
                    '$title_[$i]', 
                    '$classcode', 
                    '$systemcode', 
                    '$class', 
                    '$status',
                    '$date', 
                    '$free',
                    '$order')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: parentcourse.php?class=$class");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_courseparent Where tbl_courseparent_code = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_courseparent_title'];
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
                            Query("Delete From tbl_courseparent Where tbl_courseparent_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: parentcourse.php?class=$class");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: parentcourse.php?class=$class");
                        }
                    }
                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        $id = $_GET['id'];
                        $result = Query("SELECT * FROM tbl_courseparent Where tbl_courseparent_code ='$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $id . '" >
                  <input type="hidden" name="class" value="' . $read['tbl_courseparent_class'] . '" >
                  <input type="hidden" name="code" value="' . $read['tbl_courseparent_code'] . '" >';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . $read['tbl_courseparent_title'] . '">
                  </div>';
                        }


                        echo '<div class="form-group" >
                <label>رده بندی</label>
                <input type="text" name="order" class="form-control" value="' . $read['tbl_courseparent_order'] . '">
                </div><hr>
                <div class="form-group check-style">
                  <label>درس فعال باشد<input type="checkbox" name="status" value="1" ' . ($read['tbl_courseparent_status'] == 1 ? 'checked' : '') . '> </label>
                  </div>
                  <div class="form-group check-style">
                  <label>درس رایگان<input type="checkbox" name="free" value="1" ' . ($read['tbl_courseparent_free'] == 1 ? 'checked' : '') . '> </label>
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
                            $free = $_POST['free'];
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: parentcourse.php?class=$class");
                                    exit();
                                }
                            }
                            // end upload image
                            Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");
                            for ($i = 1; $i <= $language_count; $i++) {
                                Query("Update tbl_courseparent SET  
            tbl_courseparent_order='$order',
            tbl_courseparent_free='$free',
            tbl_courseparent_title='$title_[$i]', 
            tbl_courseparent_classcode='$classcode', 
            tbl_courseparent_status='$status'
             where  tbl_courseparent_code='$id'");

                                $_SESSION['error'] = $lang[21];
                                header("Location: parentcourse.php?class=$class");
                            }
                        }
                    }
                    if (isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_courseparent Where tbl_courseparent_id = '$id' ");
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
                            $record = str_replace("$img", "", $read['tbl_courseparent_img']);
                            $external_file = '../upload/course/' . $img;
                            unlink("$external_file");
                            Query("Update tbl_courseparent SET tbl_courseparent_img = '$record' Where tbl_courseparent_id = '$id' ");
                            header("Location: parentcourse.php?do=edit&id=$id&class=$class");
                        } elseif (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: parentcourse.php?do=edit&id=$id&class=$class");
                        }
                    }
                    echo '</div></div></section>';
                    include './footer.inc.php';
