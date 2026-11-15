<?php
include './includer.php';
$show_per_page = '100';
$pagetitle = 'مدیریت سوالات';
$Tabpage = '25';
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
                    if (empty($do)) {
                        $result = Query("SELECT * From tbl_class where tbl_class_code='$_GET[code]' ");
                        $read = mysqli_fetch_array($result);
                    }
                    echo '<section class="content-header">
      <h1>
      ' . $pagetitle . (empty($do) ? ' / <a href="class.php">' . @$read['tbl_class_title'] . '</a>' : '') .  '
      </h1>
    </section>
        <section class="content"><div class="col-md-12">
<div class="box box-body">';
                    // list content
                    if (!isset($do)) {
                        if (isset($code)) {
                            $select = " and tbl_question_parent='$code' ";
                        } else {
                            $select = "";
                        }
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table><tr><td class="col-sm-9">' . (!empty($code) ? '<a class="btn btn-primary margin-left" href="?do=add&code=' . $code . '" >' . $lang[31] . '</a>' : '') . '</td>';
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_question where tbl_question_id !='' and tbl_question_disable='0' $select ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_question where tbl_question_id !='' and tbl_question_disable='0' $select  ORDER BY tbl_question_order DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="levelcode" value="' . $code . '">
                        <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>رده بندی</th>
                                    <th>عنوان سوال</th>
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
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-1">
            <input type="hidden" name="code[]" value="' . $read['tbl_question_id'] . '">
            <input type="text" name="o' . $read['tbl_question_id'] . '" value="' . $read['tbl_question_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
            </td>
            <td class="col-sm-2"><a href="?do=edit&id=' . $read['tbl_question_id'] . '" >' . $read['tbl_question_title'] . '</a></td>
            <td class="col-sm-2">
            <a href="?do=edit&id=' . $read['tbl_question_id'] . '" class="btn btn-xs btn-success">' . $lang[23] . '</a>
            <a href="?do=delete&id=' . $read['tbl_question_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
            </tr>';
                            $i++;
                        }

                        echo '</tbody></table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning" ></form>';
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
       <li><a href="?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($code) ? '&code=' . $_GET['code'] : '') . '">' . $lang[74] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($code) ? '&code=' . $_GET['code'] : '') . '">' . $lang[75] . '</a>
       </li></ul></td>
       <td class="col-sm-1">
       <form action="page.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">' . $lang[76] . '</button>
       </form></td></tr></table>';
                        }
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $levelcode = $_POST['levelcode'];
                        $code = $_POST['code'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_question set tbl_question_order='$val' Where tbl_question_id = '$value' ");
                        }
                        header("Location: questionclass.php?code=$levelcode");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: questionclass.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
        <form action="?do=additem" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="code" value="' . $code . '">
        <div class="form-group">
        <label>عنوان</label>
        <input type="text" name="title" class="form-control" >
        </div><hr>';
                        echo '<div class="col-12 col-lg-12 col-md-12"><div class="plus">افزودن جواب</div></div><div class="price_input">
                <div class="col-12 col-lg-8 col-md-12">
                <div class="form-group">
                <label>جواب سوال</label>
                <input type="text" name="answer[]" class="form-control" >
                </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>مقدار</label>
                <input type="text" name="answervalue[]" class="form-control" data-type="number" >
                </div>
                </div>
      </div>
      <div class="result"></div>';

                        echo '<div class="form-group">
        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
        </div></form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $date = jdate("Y/m/d : a:h:s");
                            $title = $_POST['title'];
                            $code = $_POST['code'];
                            $answer_value = $_POST['answer'];
                            $answervalue_value = $_POST['answervalue'];
                            $all_value = array();
                            foreach ($answer_value as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                    $all_value[$key]['answer'] = $value;
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                            Query("insert into tbl_question (tbl_question_parent,tbl_question_title,tbl_question_answer,tbl_question_dateadd) values('$code','$title','$all_value','$date')");
                            header("Location: questionclass.php?code=$code");
                            exit();
                        }
                    }
                    // end  = add record  
                    if (isset($do) && $do == 'edit') {
                        $result = Query("SELECT * FROM tbl_question Where tbl_question_id =  '$id'  ");
                        $read = mysqli_fetch_array($result);
                        // form for edit
                        echo '<div class="col-lg-6">
            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="' . $id . '" >
            <input type="hidden" name="parent" value="' . $read['tbl_question_parent'] . '" >';
                        echo '<div class="form-group">
            <label>عنوان </label>
            <input type="text" name="title" class="form-control" required value="' . $read['tbl_question_title'] . '">
            </div><hr>';
                        $all_value = json_decode($read['tbl_question_answer']);
                        foreach ($all_value as $key => $value) {
                            echo '<div id="d' . $key . '" style="display: flex;">
            
            <div class="col-12 col-lg-1 col-md-12">
            <div class="delete-item" data="' . $key . '">حذف</div>
            </div>
                <div class="col-12 col-lg-8 col-md-12">
                <div class="form-group">
                <label>جواب سوال</label>
                <input type="text" name="answer[]" class="form-control" value="' . $value->answer . '" required>
                </div>
                </div>
                <div class="col-12 col-lg-4 col-md-12">
                <div class="form-group">
                <label>مقدار</label>
                <input type="text" name="answervalue[]" class="form-control" value="' . $value->answervalue . '" data-type="number" required>
                </div>
                </div>
              </div>';
                        }
                        echo '<hr><div class="col-12 col-lg-12 col-md-12"><div class="plus">افزودن جواب</div></div>
        <div class="price_input">
        <div class="col-12 col-lg-8 col-md-12">
        <div class="form-group">
        <label>جواب سوال</label>
        <input type="text" name="answer[]" class="form-control">
        </div>
        </div>
        <div class="col-12 col-lg-4 col-md-12">
        <div class="form-group">
        <label>مقدار</label>
        <input type="text" name="answervalue[]" class="form-control" data-type="number" >
        </div>
        </div>
          </div>
          <div class="result"></div>
        <div class="form-group">
        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary" >
        </div>
</form></div>';
                    }

                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $date = jdate("Y/m/d : a:h:s");
                            $title = $_POST['title'];
                            $parent = $_POST['parent'];
                            $answer_value = $_POST['answer'];
                            $answervalue_value = $_POST['answervalue'];
                            $all_value = array();
                            foreach ($answer_value as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$key]['answervalue'] = imjection($answervalue_value[$key]);
                                    $all_value[$key]['answer'] = $value;
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                            Query("Update tbl_question SET tbl_question_dateupdate='$date',tbl_question_title='$title' , tbl_question_answer = '$all_value'  Where tbl_question_id= '$id' ");
                            $_SESSION['error'] = $lang[21];
                            header("Location: questionclass.php?code=$parent");
                        }
                    }

                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_question Where tbl_question_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_question_title'] . '</h2><br /><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            Query("update tbl_question set  tbl_question_disable='1' Where tbl_question_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: questionclass.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: questionclass.php");
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

                    echo '</div></div></section>';
                    include './footer.inc.php';
