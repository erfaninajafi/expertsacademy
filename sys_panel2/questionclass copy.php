<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = 'مدیریت سوالات';
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';
if (empty($do)) {
    $result = Query("SELECT * From tbl_class where tbl_class_code='$_GET[code]' ");
    $read = mysqli_fetch_array($result);
}
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <section class="content">
                        <div class="col-md-12">
                            <div class="box box-body">
                                <?php
                                if (!isset($do)) {
                                    if (isset($code)) {
                                        $select = " and tbl_question_parent='$code' ";
                                    } else {
                                        $select = "";
                                    }
                                ?>
                                <div class="form-group">
                                    <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                                </div>
                                <table>
                                    <tr>
                                        <td class="col-sm-9">
                                            <?= (!empty($code) ? '<a class="btn btn-primary margin-left" href="?do=add&code=' . $code . '" >' . $lang[31] . '</a>' : '') ?>
                                        </td>
                                        <?php
                                            $result_for_count = Query("SELECT * From tbl_question where tbl_question_lng='$language_default' and tbl_question_disable='0' $select ");
                                            $count_result = mysqli_num_rows($result_for_count);
                                            $start_contnet =  ($page - 1) * $show_per_page;
                                            $page_number = ceil($count_result / $show_per_page);
                                            $result = Query("SELECT * From tbl_question where tbl_question_lng='$language_default' and tbl_question_id !='' and tbl_question_disable='0' $select  ORDER BY tbl_question_order DESC LIMIT $start_contnet,$show_per_page ");
                                            $row = mysqli_num_rows($result);
                                            if (!isset($do) && $row == '0') {
                                            ?>
                                        <div class="margin-top"><?= $lang[57] ?></div>
                                        <?php
                                                exit();
                                            }
                                            ?>
                                        <form action="?do=runupdate" method="POST">
                                            <input type="hidden" name="levelcode" value="<?= $code ?>">
                                            <table class="table table-bordered table-hover table-striped margin-top"
                                                style="text-align:right;">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th>ردیف</th>
                                                        <th>رده بندی</th>
                                                        <th>عنوان سوال</th>
                                                        <th>مهارت</th>
                                                        <th>نوع</th>
                                                        <th>سطح</th>
                                                        <th>عملیات</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                        $i = 1;
                                                        while ($read = mysqli_fetch_array($result)) {
                                                            $skill = $read['tbl_question_skill'];

                                                            $result_skill = Query("select tbl_categories_title from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$skill' ");
                                                            $read_skill = mysqli_fetch_array($result_skill);
                                                        ?>
                                                    <tr>
                                                        <td style="width: 1%"><?= $i ?></td>
                                                        <td class="col-sm-1">
                                                            <input type="hidden" name="code[]"
                                                                value="<?= $read['tbl_question_code'] ?>">
                                                            <input type="text" name="o<?= $read['tbl_question_id'] ?>"
                                                                value="<?= $read['tbl_question_order'] ?>" maxlength="5"
                                                                style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                                                        </td>
                                                        <td class="col-sm-2"><a
                                                                href="?do=edit&code=<?= $read['tbl_question_code'] . '&class=' . $_GET['code'] ?>"><?= $read['tbl_question_title'] ?></a>
                                                        </td>
                                                        <td class="col-sm-2"><?= $read_skill['tbl_categories_title'] ?>
                                                        </td>
                                                        <td class="col-sm-1">
                                                            <?= ($read['tbl_question_model'] == '1' ? 'ارزیابی' : 'دوره') ?>
                                                        </td>
                                                        <td class="col-sm-1">
                                                            <?= ($read['tbl_question_level'] == 0 ? 'سطح مقدماتی' : ($read['tbl_question_level'] == 1 ? 'سطح متوسط' : 'سطح پیشرفته')) ?>
                                                        </td>
                                                        <td class="col-sm-2">
                                                            <a href="?do=edit&code=<?= $read['tbl_question_code'] . '&class=' . $_GET['code'] ?>"
                                                                class="btn btn-xs btn-success"><?= $lang[23] ?></a>
                                                            <a href="?do=delete&code=<?= $read['tbl_question_code'] . '&class=' . $_GET['code'] ?>"
                                                                class="btn btn-xs btn-danger"><?= $lang[24] ?></a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                            $i++;
                                                        }
                                                        ?>
                                                </tbody>
                                            </table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش"
                                                class="btn btn-warning">
                                        </form>
                                        <?php
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
                                                        <li><a class="page-link"
                                                                href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($code) ? '&code=' . $_GET['code'] : '') ?>"><?= $lang[75] ?></a>
                                                        </li>
                                                        <li><a
                                                                class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                                        </li>
                                                        <li><a class="page-link"
                                                                href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($code) ? '&code=' . $_GET['code'] : '') ?>"><?= $lang[74] ?></a>
                                                        </li>
                                                    </ul>
                                                </td>
                                                <td class="col-lg-6">
                                                    <form action="questionclass.php?do=gotopage"
                                                        class="navbar-form navbar-left" role="search" method="POST">
                                                        <div class="form-group">
                                                            <button type="submit" name="gotopage"
                                                                class="btn btn-success"
                                                                style="float: left;"><?= $lang[76] ?></button>
                                                            <input type="text" class="form-control go-fld"
                                                                name="page_number">
                                                        </div>
                                                    </form>
                                                </td>
                                            </tr>
                                        </table>
                                        <?php

                                            }
                                        }
                                        if (isset($do) && $do == 'runupdate') {
                                            $levelcode = $_POST['levelcode'];
                                            $code = $_POST['code'];
                                            foreach ($code as $key => $value) {
                                                $val = $_POST['o' . $value];
                                                Query("update tbl_question set tbl_question_order='$val' Where tbl_question_code = '$value' ");
                                            }
                                            header("Location: questionclass.php?code=$levelcode");
                                        }
                                        if (isset($do) && $do == 'gotopage') {
                                            if (isset($_POST['gotopage'])) {
                                                $page_number = $_POST['page_number'];
                                                header("Location: questionclass.php?page=$page_number");
                                            }
                                        }

                                        if (isset($do) && $do == 'add') {

                                            $result_class = Query("SELECT tbl_class_skill From tbl_class where tbl_class_lng='$language_default' and tbl_class_code ='$code' ");
                                            $read_class = mysqli_fetch_array($result_class);
                                            $skill = explode(",", $read_class['tbl_class_skill']);
                                            ?>

                                        <div class="col-lg-6">
                                            <form action="?do=additem" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="class" value="<?= $code ?>">
                                                <div class="form-group">
                                                    <label>انتخاب مهارت </label>
                                                    <select name="skill" class="form-control select2">
                                                        <?php
                                                            foreach ($skill as $key => $value) {
                                                                if (!empty($value)) {
                                                                    $result_access = Query("Select * from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$value' ");
                                                                    $read_access = mysqli_fetch_array($result_access);
                                                                    echo '<option value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                                                }
                                                            }

                                                            ?>
                                                    </select>
                                                </div>

                                                <?php
                                                    for ($i = 1; $i <= $language_count; $i++) {
                                                    ?>
                                                <div class="form-group">
                                                    <label><?= $lang[50] ?><span
                                                            class="language"><?= $language[$i] ?></span></label>
                                                    <input type="text" name="title_<?= $language[$i] ?>"
                                                        class="form-control">
                                                </div>
                                                <?php
                                                    }
                                                    ?>
                                                <div class="form-group">
                                                    <label>
                                                        <input type="radio" name="level" value="0" checked> سطح مقدماتی
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="level" value="1"> سطح متوسط
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="level" value="2"> سطح پیشرفته
                                                    </label>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label>نوع سوال</label>
                                                    <label>
                                                        <input type="radio" name="model" value="1" checked> سوال ارزیابی
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="model" value="0"> سوال دوره
                                                    </label>
                                                </div>
                                                <hr>
                                                <div class="col-12 col-lg-12 col-md-12">
                                                    <div class="plus">افزودن جواب</div>
                                                </div>
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
                                                    <div class="col-12 col-lg-2 col-md-12">
                                                        <div class="form-group">
                                                            <label>مقدار</label>
                                                            <input type="text" name="answervalue[]" class="form-control"
                                                                data-type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="result row"></div>

                                                <div class="form-group">
                                                    <input type="submit" name="additem" value="<?= $lang[31] ?>"
                                                        class="btn btn-primary">
                                                </div>
                                            </form>
                                        </div>
                                        <?php
                                        }
                                        if (isset($do) && $do == 'additem') {
                                            if (isset($_POST['additem'])) {
                                                $date = jdate("Y/m/d : a:h:s");
                                                $title = $_POST['title'];
                                                $class = $_POST['class'];
                                                $code = Code("", "no", 7);
                                                $level = $_POST['level'];
                                                $model = $_POST['model'];
                                                $skill = $_POST['skill'];

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
                                                    tbl_question_code,
                                                    tbl_question_level,
                                                    tbl_question_model,
                                                    tbl_question_disable,
                                                    tbl_question_skill) values(
                                                        '$class',
                                                        '$title_[$i]',
                                                        '$all_value',
                                                        '$date',
                                                        '$language_name',
                                                        '$code',
                                                        '$level',
                                                        '$model',
                                                        '0',
                                                        '$skill')");
                                                }
                                                header("Location: questionclass.php?code=$class");
                                                exit();
                                            }
                                        }
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

                                            $result_class = Query("SELECT tbl_class_skill From tbl_class where tbl_class_lng='$language_default' and tbl_class_code ='$_GET[class]' ");
                                            $read_class = mysqli_fetch_array($result_class);
                                            $skill = explode(",", $read_class['tbl_class_skill']);

                                        ?>
                                        <div class="col-lg-6">
                                            <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                                                <input type="hidden" name="code" value="<?= $code ?>">
                                                <input type="hidden" name="class" value="<?= $_GET['class'] ?>">
                                                <div class="form-group">
                                                    <label>انتخاب مهارت </label>
                                                    <select name="skill" class="form-control select2">
                                                        <?php
                                                            foreach ($skill as $key => $value) {
                                                                if (!empty($value)) {
                                                                    $result_access = Query("Select * from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$value' ");
                                                                    $read_access = mysqli_fetch_array($result_access);
                                                                    echo '<option ' . (read_fild(NULL, 'tbl_question_skill') == $read_access['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                                                }
                                                            }

                                                            ?>
                                                    </select>
                                                </div>

                                                <?php
                                                    for ($i = 1; $i <= $language_count; $i++) {
                                                    ?>
                                                <div class="form-group">
                                                    <label><?= $lang[50] ?><span
                                                            class="language"><?= $language[$i] ?></span></label>
                                                    <input type="text" name="title_<?= $language[$i] ?>"
                                                        value="<?= exjection(read_fild($language[$i], 'tbl_question_title')) ?>"
                                                        class="form-control">
                                                </div>
                                                <?php } ?>

                                                <div class="form-group">
                                                    <label>
                                                        <input type="radio" name="level"
                                                            <?= (read_fild(NULL, 'tbl_question_level') == 0 ? 'checked' : '') ?>
                                                            value="0" checked> سطح مقدماتی
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="level"
                                                            <?= (read_fild(NULL, 'tbl_question_level') == 1 ? 'checked' : '') ?>
                                                            value="1"> سطح متوسط
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="level"
                                                            <?= (read_fild(NULL, 'tbl_question_level') == 2 ? 'checked' : '') ?>
                                                            value="2"> سطح پیشرفته
                                                    </label>
                                                </div>
                                                <hr>
                                                <div class="form-group">
                                                    <label>نوع سوال</label>
                                                    <label>
                                                        <input type="radio" name="model"
                                                            <?= (read_fild(NULL, 'tbl_question_model') == 1 ? 'checked' : '') ?>
                                                            value="1" checked> سوال ارزیابی
                                                    </label>
                                                    <label>
                                                        <input type="radio" name="model"
                                                            <?= (read_fild(NULL, 'tbl_question_model') == 0 ? 'checked' : '') ?>
                                                            value="0"> سوال دوره
                                                    </label>
                                                </div>
                                                <hr>


                                                <?php
                                                    $all_value = json_decode(read_fild(NULL, 'tbl_question_answer'));
                                                    foreach ($all_value as $key => $value) {
                                                    ?>
                                                <div id="d<?= $key ?>" style="display: flex;">
                                                    <div class="col-12 col-lg-1 col-md-12">
                                                        <div class="delete-item" data="<?= $key ?>">حذف</div>
                                                    </div>
                                                    <div class="col-12 col-lg-4 col-md-12">
                                                        <div class="form-group">
                                                            <label>جواب سوال فارسی</label>
                                                            <input type="text" name="answerfa[]" class="form-control"
                                                                value="<?= $value->answerfa ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-4 col-md-12">
                                                        <div class="form-group">
                                                            <label>جواب سوال انگلیسی</label>
                                                            <input type="text" name="answeren[]" class="form-control"
                                                                value="<?= $value->answeren ?>" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-lg-2 col-md-12">
                                                        <div class="form-group">
                                                            <label>مقدار</label>
                                                            <input type="text" name="answervalue[]" class="form-control"
                                                                value="<?= $value->answervalue ?>" data-type="number"
                                                                required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                    }
                                                    ?>
                                                <hr>
                                                <div class="col-12 col-lg-12 col-md-12">
                                                    <div class="plus">افزودن جواب</div>
                                                </div>
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
                                                            <input type="text" name="answervalue[]" class="form-control"
                                                                data-type="number">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="result row"></div>
                                                <div class="form-group">
                                                    <input type="submit" name="updateitem" value="<?= $lang[20] ?>"
                                                        class="btn btn-primary">
                                                </div>
                                            </form>
                                        </div>
                                        <?php
                                        }

                                        if (isset($do) && $do == 'edititem') {
                                            if (isset($_POST['updateitem'])) {
                                                $code = $_POST['code'];
                                                $class = $_POST['class'];
                                                $level = $_POST['level'];
                                                $model = $_POST['model'];
                                                $skill = $_POST['skill'];

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

                                                $date = jdate("Y/m/d : a:h:s");

                                                for ($i = 1; $i <= $language_count; $i++) {
                                                    $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                                    $language_record = $language[$i];
                                                    Query("Update tbl_question SET 
                                                tbl_question_level='$level',
                                                tbl_question_model='$model',
                                                tbl_question_skill='$skill',
                                                tbl_question_dateupdate='$date',
                                                tbl_question_title='$title_[$i]', 
                                                tbl_question_answer = '$all_value',
                                                tbl_question_cat = '$cat'  Where tbl_question_code= '$code' and tbl_question_lng='$language_record' ");
                                                }
                                                $_SESSION['error'] = $lang[21];
                                                header("Location: questionclass.php?code=$class");
                                            }
                                        }

                                        if (isset($do) && $do == 'delete') {
                                            $result = Query("SELECT * from tbl_question Where tbl_question_code = '$code' ");
                                            $read = mysqli_fetch_array($result);
                                        ?>
                                        <div class="col-lg-6"><?= $lang[25] ?><br />
                                            <h2><?= $read['tbl_question_title'] ?></h2><br /><br />
                                            <form action="" method="POST">
                                                <div class="form-group margin-top">
                                                    <input type="hidden" value="<?= $code ?>" name="code">
                                                    <input type="hidden" value="<?= $_GET['class'] ?>" name="class">
                                                    <input type="submit" name="submit" value="<?= $lang[26] ?>"
                                                        class="btn btn-xs btn-success">
                                                    <input type="submit" name="nodelete" value="<?= $lang[27] ?>"
                                                        class="btn btn-xs btn-danger">
                                            </form>
                                        </div>
                                        <?php
                                            if (isset($_POST['submit'])) {
                                                $code = $_POST['code'];
                                                $class = $_POST['class'];
                                                Query("update tbl_question set  tbl_question_disable='1' Where tbl_question_code = '$code' ");
                                                $_SESSION['error'] = $lang[28];
                                                header("Location: questionclass.php?code=$class");
                                                exit();
                                            } elseif (isset($_POST['nodelete'])) {
                                                $class = $_POST['class'];
                                                header("Location: questionclass.php?code=$class");
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