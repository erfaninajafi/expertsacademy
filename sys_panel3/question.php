<?php
include './includer.php';
$show_per_page = '100';
$pagetitle = 'مدیریت سوالات';
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';
if (!empty($_GET['orderby'])) {
    if ($_GET['orderby'] == 'order') {
        $orderby = ' ORDER BY tbl_question_order ASC ';
    }
    if ($_GET['orderby'] == 'title') {
        $orderby = ' ORDER BY tbl_question_title ASC ';
    }
} else {
    $orderby = ' ORDER BY tbl_question_order ASC ';
}
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header">
            <?= $pagetitle ?>
        </h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    echo '<section class="content"><div class="col-md-12">
<div class="box box-body table-responsive">';

                        if (!empty($_GET['class'])) {
                            $resultCourse = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$_GET[class]' ");
                            $readCourse = mysqli_fetch_array($resultCourse);
                            $thisClass = $readCourse['tbl_courseparent_class'];

                            $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$thisClass' and tbl_class_lng='$language_default' ");
                            $readClass = mysqli_fetch_array($resultClass);
                            $thisSkill = $readClass['tbl_class_skill'];

                            echo '<div class="col-12 col-gl-12 col-md-12">
                        <h5 class="skill-title">
                        دوره : ' . $readClass['tbl_class_title'] . ' / 
                        درس : <a href="parentcourse.php?class=' . $thisClass . '">' . $readCourse['tbl_courseparent_title'] . '</a> 
                        </h5>
                        </div>';
                        }

                    if (!empty($_GET['class']) && !empty($_GET['skill'])) {
                        $resultCourse = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$_GET[class]' ");
                        $readCourse = mysqli_fetch_array($resultCourse);
                        $thisClass = $readCourse['tbl_courseparent_class'];

                        $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$thisClass' and tbl_class_lng='$language_default' ");
                        $readClass = mysqli_fetch_array($resultClass);
                        $thisSkill = $readClass['tbl_class_skill'];
                        // $skill_all = explode(",", $thisSkill);
                        // $skillData = '';
                        // foreach ($skill_all as $key => $value) {
                        //     if (!empty($value)) {
                        //         $resultSkillData = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                        //         $readSkillData = mysqli_fetch_array($resultSkillData);
                        //         $skillData .= $readSkillData['tbl_categories_title'] . ',';
                        //     }
                        // }

                        $result_skill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$_GET[skill]' ");
                        $readSkill = mysqli_fetch_array($result_skill);

                        echo '<div class="col-12 col-gl-12 col-md-12">
                            <h5 class="skill-title">
                            <a href="category_manage.php?t=4">لیست مهارت ها</a>  / 
                            <a href="class.php?skill=' . $_GET['skill'] . '">دوره های مهارت ' . $readSkill['tbl_categories_title'] . '</a> / 
                            دوره : ' . $readClass['tbl_class_title'] . ' / 
                            درس : <a href="parentcourse.php?class=' . $thisClass . '&skill=' . $_GET['skill'] . '">' . $readCourse['tbl_courseparent_title'] . '</a> 
                            </h5>
                            </div>';
                    }

                    // list content
                    if (!isset($do)) {
                        if (isset($_GET['class'])) {
                            $select = " and tbl_question_parent='$_GET[class]' ";
                        } else {
                            $select = "";
                        }
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>

                       <table><tr><td class="col-sm-9">' . (!empty($_GET['class']) ? '<a class="btn btn-primary margin-left" href="?do=add&class=' . $_GET['class'] . '" >' . $lang[31] . '</a>' : '') . '</td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="question.php?do=search" method="POST">
                       <input type="text" class="form-control" name="keyword" value="' . $keyword . '">
                       <input type="hidden" class="form-control" name="class" value="' . $_GET['class'] . '">
                       </td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_question where tbl_question_title like '%$keyword%' and  tbl_question_lng='$language_default' and tbl_question_id !='' and tbl_question_disable='0' $select ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_question where tbl_question_title like '%$keyword%' and  tbl_question_lng='$language_default' and tbl_question_id !='' and tbl_question_disable='0' $select  $orderby LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="class" value="' . $_GET['class'] . '">
                        <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th><a href="?orderby=order&class=' . $_GET['class'] . '">ترتیب <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th><a href="?orderby=title&class=' . $_GET['class'] . '">عنوان سوال <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $level = $read['tbl_question_sub'];
                            $meyar = $read['tbl_question_parent'];
                            $result_level = Query("select tbl_categories_title from tbl_categories where tbl_categories_code='$level' ");
                            $read_level = mysqli_fetch_array($result_level);
                            $result_meyar = Query("select tbl_categories_title from tbl_categories where tbl_categories_code='$meyar' ");
                            $read_meyar = mysqli_fetch_array($result_meyar);
                            echo '<tr>
                        <td style="width: 1%">' . $i . '</td>
                        <td class="col-sm-1">
                        <input type="hidden" name="code[]" value="' . $read['tbl_question_code'] . '">
                        <input type="text" name="o' . $read['tbl_question_code'] . '" value="' . $read['tbl_question_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                        </td>
                        <td class="col-sm-5"><a href="?do=edit&code=' . $read['tbl_question_code'] . '" >' . $read['tbl_question_title'] . '</a></td>
                        </tr><tr>
                        <td class="col-sm-1 btn-class" colspan="4">
                        <a href="?do=edit&code=' . $read['tbl_question_code'] . '" class="btn btn-xs btn-success">' . $lang[23] . '</a>
                        <a href="?do=delete&code=' . $read['tbl_question_code'] . '&class=' . $_GET['class'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                        </tr>';
                            $i++;
                        }

                        echo '</tbody></table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning update-btn" ></form>';
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                        </tr>
                    </table>
                    <?php
                        }
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $class = $_POST['class'];
                        $code = $_POST['code'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_question set tbl_question_order='$val' Where tbl_question_code = '$value' ");
                        }
                        header("Location: question.php?class=$class");
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
                        <form action="?do=additem" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="class" value="' . $_GET['class'] . '">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="title_' . $language[$i] . '" class="form-control">
                                  </div>';
                        }
                        echo '<div class="col-12 col-lg-12 col-md-12"><div class="plus">افزودن جواب</div></div><div class="price_input">
                        <div class="col-12 col-lg-4 col-md-12">
                        <div class="form-group">
                        <label>جواب سوال فارسی</label>
                        <input type="text" name="answerfa[]" class="form-control" >
                        </div>
                        </div>
                        <div class="col-12 col-lg-4 col-md-12">
                        <div class="form-group">
                        <label>جواب سوال انگلیسی</label>
                        <input type="text" name="answeren[]" class="form-control" >
                        </div>
                        </div>
                        <div class="col-12 col-lg-2 col-md-12">
                        <div class="form-group">
                        <label>مقدار</label>
                        <input type="text" name="answervalue[]" class="form-control" data-type="number" >
                        </div>
                        </div>
                        </div>
                        <div class="result row"></div>';
                        echo '<div class="form-group">
                        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                        </div></form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = Code("", "no", 7);
                            $date = jdate("Y/m/d : a:h:s");
                            $class = $_POST['class'];
                            $answer_valuefa = $_POST['answerfa'];
                            $answer_valueen = $_POST['answeren'];
                            $answervalue_value = $_POST['answervalue'];
                            $all_value = array();
                            foreach ($answer_valuefa as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                    $all_value[$key]['answerfa'] = $value;
                                    $all_value[$key]['answeren'] = $answer_valueen[$key];
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_name = $language[$i];
                                Query("insert into tbl_question (
                                tbl_question_parent,
                                tbl_question_title,
                                tbl_question_answer,
                                tbl_question_dateadd,
                                tbl_question_lng,
                                tbl_question_disable,
                                tbl_question_code) values('$class','$title_[$i]','$all_value','$date','$language_name','0','$code')");
                            }
                            $_SESSION['error'] = $lang[32];
                            header("Location: question.php?class=$class");
                            exit();
                        }
                    }
                    // end  = add record  
                    if (isset($do) && $do == 'edit') {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_question_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_question Where tbl_question_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // form for edit
                        echo '<div class="col-lg-6">
            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="code" value="' . $code . '" >
            <input type="hidden" name="class" value="' . (read_fild(NULL, 'tbl_question_parent')) . '" >';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
            <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
            <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_question_title')) . '" class="form-control">
            </div>';
                        }
                        $all_value = json_decode(read_fild(NULL, 'tbl_question_answer'));
                        foreach ($all_value as $key => $value) {
                            echo '<div id="d' . $key . '" style="display: flex;">
            <div class="col-12 col-lg-1 col-md-12">
            <div class="delete-item" data="' . $key . '">حذف</div>
            </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>جواب سوال فارسی</label>
                <input type="text" name="answerfa[]" class="form-control" value="' . $value->answerfa . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>جواب سوال انگلیسی</label>
                <input type="text" name="answeren[]" class="form-control" value="' . $value->answeren . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-2 col-md-12">
                <div class="form-group">
                <label>مقدار</label>
                <input type="text" name="answervalue[]" class="form-control" value="' . $value->answervalue . '" data-type="number" required>
                </div>
                </div>
              </div>';
                        }
                        echo '<hr><div class="col-12 col-lg-12 col-md-12"><div class="plus">افزودن جواب</div></div>
        <div class="price_input">
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>جواب سوال فارسی</label>
        <input type="text" name="answerfa[]" class="form-control">
        </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>جواب سوال انگلیسی</label>
        <input type="text" name="answeren[]" class="form-control">
        </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>مقدار</label>
        <input type="text" name="answervalue[]" class="form-control" data-type="number" >
        </div>
        </div>
          </div>
          <div class="result row"></div>
        <div class="form-group">
        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary" >
        </div>
</form></div>';
                    }

                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $date = jdate("Y/m/d : a:h:s");
                            $title = $_POST['title'];
                            $class = $_POST['class'];
                            $answer_valuefa = $_POST['answerfa'];
                            $answer_valueen = $_POST['answeren'];
                            $answervalue_value = $_POST['answervalue'];
                            $all_value = array();
                            foreach ($answer_valuefa as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                    $all_value[$key]['answerfa'] = $value;
                                    $all_value[$key]['answeren'] = imjection($answer_valueen[$key]);
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_record = $language[$i];

                                Query("Update tbl_question SET tbl_question_dateupdate='$date',tbl_question_title='$title_[$i]' , tbl_question_answer = '$all_value'  Where tbl_question_code= '$code' and tbl_question_lng='$language_record' ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: question.php?class=$class");
                        }
                    }

                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_question Where tbl_question_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_question_title'] . '</h2><br /><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $_GET['class'] . '" name="class" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $class = $_POST['class'];
                            Query("update tbl_question set  tbl_question_disable='1' Where tbl_question_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: question.php?class=$class");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: question.php?class=$class");
                            exit();
                        }
                    }
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $class = $_POST['class'];
                            header("Location: ?keyword=$keyword_search&class=$class");
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