<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = $lang[1694];
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

if (!empty($_GET['class'])) {
    $resultCourse = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$_GET[class]' ");
    $readCourse = mysqli_fetch_array($resultCourse);
    $thisClass = $readCourse['tbl_courseparent_class'];

    $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$thisClass' and tbl_class_lng='$language_default' ");
    $readClass = mysqli_fetch_array($resultClass);
    $thisSkill = $readClass['tbl_class_skill'];
echo '<div class="col-md-12 mb-3">
<div class="box box-body ">
    <div class="card mb-3">
        <div class="card-body custom-h5">
        <div class="row justify-content-between align-items-center">
            <div class="col">
            <div class="d-flex">
                <div class="flex-1 fs--1">
                <h5 class="fs-0">'.$lang[470].'  : ' .$readClass['tbl_class_title'] . '</h5>
                </div>
                <div class="flex-1 fs--1">
                <h5 class="fs-0">'.$lang[1520].'  : <a href="parentcourse.php?class=' . $thisClass . '">' . $readCourse['tbl_courseparent_title'] . '</a> </h5>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>                    
</div>
</div>'; 


}


if (!empty($_GET['class']) && !empty($_GET['skill'])) {
    $resultCourse = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$_GET[class]' ");
    $readCourse = mysqli_fetch_array($resultCourse);
    $thisClass = $readCourse['tbl_courseparent_class'];

    $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$thisClass' and tbl_class_lng='$language_default' ");
    $readClass = mysqli_fetch_array($resultClass);
    $thisSkill = $readClass['tbl_class_skill'];

    $result_skill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$_GET[skill]' ");
    $readSkill = mysqli_fetch_array($result_skill);

        echo '<div class="col-md-12 mb-3">
<div class="box box-body ">
    <div class="card mb-3">
        <div class="card-body custom-h5">
        <div class="row justify-content-between align-items-center">
            <div class="col">
            <div class="d-flex">
            <div class="flex-1 fs--1">
            <h5 class="fs-0"><a href="category_manage.php?t=4">'.$lang[1304].'</a></h5>
            <h5 class="fs-0">'.$lang[1677].'  : <a href="class.php?skill=' . $_GET['skill'] . '">' .$readSkill['tbl_categories_title'] . '</a></h5>
            </div>

                <div class="flex-1 fs--1">
                <h5 class="fs-0">'.$lang[470].'  : ' . $readClass['tbl_class_title'] . ' </h5>
                <h5 class="fs-0">'.$lang[1520].'  : <a href="parentcourse.php?class=' . $thisClass . '&skill=' . $_GET['skill'] . '">' . $readCourse['tbl_courseparent_title'] . '</a></h5>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>                    
</div>
</div>'; 



}



?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(!isset($do) ? '<div class="plus">
                                <a href="?do=add'.(!empty($_GET['class']) ? '&class='.$_GET['class'] : '' ).'"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <input type="hidden" class="form-control" value="<?= $_GET['class'] ?>" name="class">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
                    <?php
                        if (isset($_GET['class'])) {
                            $select = " and tbl_question_parent='$_GET[class]' ";
                        } else {
                            $select = "";
                        }
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
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="class" value="' . $_GET['class'] . '">
                        <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th><a href="?orderby=order&class=' . $_GET['class'] . '">'.$lang[1336].' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th><a href="?orderby=title&class=' . $_GET['class'] . '">'.$lang[1372].' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th>' . $lang[63] . '</th>
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
                        <td class="align-middle p-2">' . $i . '</td>
                        <td class="align-middle p-2">
                        <input type="hidden" name="code[]" value="' . $read['tbl_question_code'] . '">
                        <input type="text" name="o' . $read['tbl_question_code'] . '" value="' . $read['tbl_question_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                        </td>
                        <td class="align-middle p-2"><a href="?do=edit&code=' . $read['tbl_question_code'] . '" >' . $read['tbl_question_title'] . '</a></td>
                        <td class="align-middle p-2">
                        <a href="?do=edit&code=' . $read['tbl_question_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                        <a href="?do=delete&code=' . $read['tbl_question_code'] . '&class=' . $_GET['class'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                        </td>
                        </tr>';
                            $i++;
                        }

                        echo '</tbody></table><input type="submit" name="runupdate" value="'.$lang[1342].'" class="btn btn-warning update-btn m-3" ></form>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
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
                            <td class="col-lg-9">
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
                        echo '<div class="col-lg-8 col-12 p-3">
                        <form action="?do=additem" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="class" value="' . $_GET['class'] . '">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                  <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="title_' . $language[$i] . '" class="form-control">
                                  </div>';
                        }
                        echo '<div class="col-12 col-lg-12 col-md-12"><div class="plus-add">'.$lang[1375].'</div></div><div class="price_input">
                        <div class="col-12 col-lg-4 col-md-12">
                        <div class="form-group">
                        <label>'.$lang[1376].'</label>
                        <input type="text" name="answerfa[]" class="form-control" >
                        </div>
                        </div>
                        <div class="col-12 col-lg-4 col-md-12">
                        <div class="form-group">
                        <label>'.$lang[1377].'</label>
                        <input type="text" name="answeren[]" class="form-control" >
                        </div>
                        </div>
                        <div class="col-12 col-lg-2 col-md-12">
                        <div class="form-group">
                        <label>'.$lang[1378].'</label>
                        <input type="text" name="answervalue[]" class="form-control" data-type="number" >
                        </div>
                        </div>
                        </div>
                        <div class="result row"></div>';
                        echo '<div class="form-group">
                        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
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
                        echo '<div class="col-lg-8 col-12 p-3">
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
            <div class="delete-item" data="' . $key . '">'.$lang[24].'</div>
            </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>'.$lang[1376].'</label>
                <input type="text" name="answerfa[]" class="form-control" value="' . $value->answerfa . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>'.$lang[1377].'</label>
                <input type="text" name="answeren[]" class="form-control" value="' . $value->answeren . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-2 col-md-12">
                <div class="form-group">
                <label>'.$lang[1378].'</label>
                <input type="text" name="answervalue[]" class="form-control" value="' . $value->answervalue . '" data-type="number" required>
                </div>
                </div>
              </div>';
                        }
                        echo '<hr><div class="col-12 col-lg-12 col-md-12"><div class="plus-add">'.$lang[1375].'</div></div>
        <div class="price_input">
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>'.$lang[1376].'</label>
        <input type="text" name="answerfa[]" class="form-control">
        </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>'.$lang[1377].'</label>
        <input type="text" name="answeren[]" class="form-control">
        </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>'.$lang[1378].'</label>
        <input type="text" name="answervalue[]" class="form-control" data-type="number" >
        </div>
        </div>
          </div>
          <div class="result row"></div>
        <div class="form-group">
        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
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
                        echo '<div class="col-lg-8 col-12 p-2"><p>' . $lang[25] . '</p><h3>' . $read['tbl_question_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $_GET['class'] . '" name="class" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
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