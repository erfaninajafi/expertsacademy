<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مدیریت درسها  ';
$Tabpage = '91';
$img_item = 10;
$thisaccess = '1,4';
include './header.inc.php';
if (!empty($_GET['orderby'])) {
    if ($_GET['orderby'] == 'order') {
        $orderby = ' ORDER BY tbl_courseparent_order ASC ';
    }
    if ($_GET['orderby'] == 'title') {
        $orderby = ' ORDER BY tbl_courseparent_title ASC ';
    }
} else {
    $orderby = ' ORDER BY tbl_courseparent_order ASC ';
}

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!empty($_GET['class']) && !empty($_GET['skill'])) {
                        $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$_GET[class]' and tbl_class_lng='$language_default' ");
                        $readClass = mysqli_fetch_array($resultClass);
                        $thisSkill = $readClass['tbl_class_skill'];
                        $skill_all = explode(",", $thisSkill);
                        $skillData = '';
                        foreach ($skill_all as $key => $value) {
                            if (!empty($value)) {
                                $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                                $readSkill = mysqli_fetch_array($resultSkill);
                                $skillData .= $readSkill['tbl_categories_title'] . ',';
                            }
                        }
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="category_manage.php?t=4">لیست مهارت ها</a>  / <a href="class.php?skill=' . $_GET['skill'] . '"> مهارت ' . $readSkill['tbl_categories_title'] . '</a>  /  درس های دوره  : ' . $readClass['tbl_class_title'] . '</h5></div>';
                    }
                    else if (!empty($_GET['class']))
                    {
                        $result_class = Query("Select tbl_class_title from tbl_class where tbl_class_code = '$_GET[class]' and tbl_class_lng='$language_default'");
                        $read_class = mysqli_fetch_array($result_class);
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="class-uniq.php">نام دوره</a>  / <a href="class-uniq.php">' . $read_class['tbl_class_title'] . '</a></h5></div>';
                    }


                    echo '<section class="content"><div class="col-md-12">
            <div class="box box-body table-responsive">';
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
                       <table><tr><td class="col-sm-9"><a class="btn btn-primary big_button" href="?do=add&class=' . $_GET['class'] . '" >افزودن درس</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="parentcourse.php?do=search" method="POST">
                       <input type="text" class="form-control" name="keyword" value="' . $keyword . '">
                       <input type="hidden" class="form-control" name="class" value="' . $_GET['class'] . '">
                       </td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        $result_for_count = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' and tbl_courseparent_title like '%$keyword%' and tbl_courseparent_lng='$language_default' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' and tbl_courseparent_title like '%$keyword%' and tbl_courseparent_lng='$language_default' $orderby  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        $allfield = '';
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="class" value="' . $_GET['class'] . '"><input type="hidden" name="page" value="' . $page . '">
                            <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                            <thead class="table-dark">
                                    <tr>
                                        <th>' . $lang[60] . '</th>
                                        <th><a href="?orderby=order&class=' . $_GET['class'] . '">ترتیب <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                        <th>کد کلاس</th>
                                        <th><a href="?orderby=title&class=' . $_GET['class'] . '">عنوان درس <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                        <th>مباحث</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_courseparent_admin'];
                            $result_cat = Query("SELECT tbl_course_title FROM tbl_course Where tbl_course_lng='$language_default' and tbl_course_class =  '$read[tbl_courseparent_code]'");
                            $result_register = Query("select * from tbl_question where tbl_question_lng='$language_default' and tbl_question_disable='0' and tbl_question_parent='$read[tbl_courseparent_code]'");

                            // parent menu code
                            echo '<tr><td style="width: 1%">' . $i . '</td>
                                 <td class="col-sm-1"><input type="hidden" name="code[]" value="' . $read['tbl_courseparent_code'] . '"><input type="text" name="o' . $read['tbl_courseparent_code'] . '" value="' . $read['tbl_courseparent_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>
                                 <td class="col-sm-1">' . $read['tbl_courseparent_classcode'] . '</td>
                                <td class="col-sm-4"><span style="' . ($read['tbl_courseparent_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_courseparent_title'] . '' . ($read['tbl_courseparent_free'] == 1 ? ' [رایگان]' : '') . '</span></td>
                                <td class="col-sm-3">';
                                $k=1;
                                while ($readCourse = mysqli_fetch_array($result_cat)) {
                                    echo '<label>'.$k.' - '.$readCourse['tbl_course_title'].'</label><br>';
                                    $k++;
                                }
                                echo '</td></tr><tr>
                                <td class="col-sm-1 btn-class" colspan="5">'
                                . '<a href="?do=edit&code=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                                <a href="?do=delete&code=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
                                <a href="course.php?class=' . $read['tbl_courseparent_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '' ).'" class="btn btn-xs btn-secondary" >مباحث (' . (mysqli_num_rows($result_cat) > 0 ? mysqli_num_rows($result_cat) : '0') . ')</a>
                                <a href="question.php?class=' . $read['tbl_courseparent_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '' ).'" class="btn btn-xs btn-warning" >سوالات چهار جوابی (' . mysqli_num_rows($result_register) . ')</a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning update-btn" ></form>';

                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="parentcourse.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            <div class="form-group">
                                <label>کد کلاس</label>
                                <input type="text" name="code" class="form-control">
                            </div>
                            <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                    <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                    <input type="text" name="title_' . $language[$i] . '" class="form-control" required>
                                    </div>';
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                                <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                                <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
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
                            $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                            $class = $_POST['class'];
                            $code = $_POST['code'];
                            $systemcode = $_POST['systemcode'];
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $free = (!empty($_POST['free']) ? $_POST['free'] : '0');
                            $date = jdate("Y/m/d : A:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: parentcourse.php?class=$class");
                                    exit();
                                }
                                $language_name = $language[$i];
                                Query("INSERT INTO tbl_courseparent (
                                tbl_courseparent_title,
                                tbl_courseparent_code, 
                                tbl_courseparent_class, 
                                tbl_courseparent_status, 
                                tbl_courseparent_dateadd,
                                tbl_courseparent_free,
                                tbl_courseparent_order,
                                tbl_courseparent_lng,
                                tbl_courseparent_classcode,
                                tbl_courseparent_content) 
                                VALUES (
                                    '$title_[$i]', 
                                    '$systemcode', 
                                    '$class', 
                                    '$status',
                                    '$date', 
                                    '$free',
                                    '$order',
                                    '$language_name',
                                    '$code',
                                    '$content_[$i]'
                                    )");
                                $_SESSION['error'] = $lang[32];
                                header("Location: parentcourse.php?class=$class");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_courseparent Where tbl_courseparent_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_courseparent_title'];
                        echo '<form action="#" method="POST">
        <input type="hidden" name="code" value="' . $code . '" >
        <input type="hidden" value="' . $_GET['class'] . '" name="class" >
        <div class="form-group margin-top">
        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
        </form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $class = $_POST['class'];
                            Query("Delete From tbl_courseparent Where tbl_courseparent_code = '$code' ");
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

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_courseparent_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_courseparent Where tbl_courseparent_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $code . '" >
                  <input type="hidden" name="class" value="' .  read_fild(NULL, 'tbl_courseparent_class') . '" >
                  <input type="hidden" name="code" value="' . read_fild(NULL, 'tbl_courseparent_code') . '" >
                  <div class="form-group">
                        <label>کد کلاس</label>
                        <input type="text" name="classcode" class="form-control" value="' . read_fild(NULL, 'tbl_courseparent_classcode') . '">
                    </div>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                  <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_courseparent_title') . '">
                  </div>';
                        }


                        echo '<div class="form-group" >
                <label>رده بندی</label>
                <input type="text" name="order" class="form-control" value="' . read_fild(NULL, 'tbl_courseparent_order') . '">
                </div><hr>
                <div class="form-group check-style">
                  <label>درس فعال باشد<input type="checkbox" name="status" value="1" ' . (read_fild(NULL, 'tbl_courseparent_status') == 1 ? 'checked' : '') . '> </label>
                  </div>
                  <div class="form-group check-style">
                  <label>درس رایگان<input type="checkbox" name="free" value="1" ' . (read_fild(NULL, 'tbl_courseparent_free') == 1 ? 'checked' : '') . '> </label>
                  </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                    }
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['update'])) {
                            $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                            $code = $_POST['code'];
                            $class = $_POST['class'];
                            $classcode = $_POST['classcode'];
                            $id = $_POST['id'];
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $free = (!empty($_POST['free']) ? $_POST['free'] : '0');
                            //Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: parentcourse.php?class=$class");
                                    exit();
                                }
                                $language_name = $language[$i];
                                Query("Update tbl_courseparent SET  
                                    tbl_courseparent_order='$order',
                                    tbl_courseparent_free='$free',
                                    tbl_courseparent_title='$title_[$i]', 
                                    tbl_courseparent_status='$status',
                                    tbl_courseparent_classcode='$classcode',
                                    tbl_courseparent_content='$content_[$i]'
                                    where  tbl_courseparent_code='$id' and tbl_courseparent_lng='$language_name' ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: parentcourse.php?class=$class");
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
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $class = $_POST['class'];
                            header("Location: ?keyword=$keyword_search&class=$class");
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