<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = 'سوالات ارزیابی مهارت';
$Tabpage = '18';
$thisaccess = '1,4';
include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <section class="content">
                        <div class="col-md-12">
                            <div class="box box-body table-responsive">
                                <?php
                                if (!empty($_GET['orderby'])) {
                                    if ($_GET['orderby'] == 'order') {
                                        $orderby = ' ORDER BY tbl_questionpublic_order ASC ';
                                    }
                                    if ($_GET['orderby'] == 'title') {
                                        $orderby = ' ORDER BY tbl_questionpublic_title ASC ';
                                    }
                                } else {
                                    $orderby = ' ORDER BY tbl_questionpublic_order ASC ';
                                }
                                
                                if (!isset($do)) {
                                 
                                ?>
                                <div class="form-group">
                                    <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                                </div>

                                <table>
                                    <tr>
                                        <td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add"><?= $lang[31] ?></a></td>
                                        <td class="col-sm-6" style="padding-left: 0px;">
                                            <form action="questionpublic.php?do=search" method="POST">
                                                <input type="text" class="form-control" name="keyword" value="<?= $keyword ?>">
                                        </td>
                                        <td class="col-sm-2" style="padding-left: 0px;">
                                            <button type="submit" name="go" class="btn btn-primary"><?= $lang[77] ?></button>
                                            </form>
                                        </td>
                                    </tr>
                                </table>

                                <?php
                                    $result_for_count = Query("SELECT * From tbl_questionpublic where tbl_questionpublic_title like '%$keyword%' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_disable='0'  ");

                                    $count_result = mysqli_num_rows($result_for_count);
                                    $start_contnet =  ($page - 1) * $show_per_page;
                                    $page_number = ceil($count_result / $show_per_page);
                                    $result = Query("SELECT * From tbl_questionpublic where tbl_questionpublic_title like '%$keyword%' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_id !='' and tbl_questionpublic_disable='0'   $orderby LIMIT $start_contnet,$show_per_page ");
                                    $row = mysqli_num_rows($result);
                                    if (!isset($do) && $row == '0') {
                                    ?>
                                <div class="margin-top"><?= $lang[57] ?></div>
                                <?php
                                    }
                                    ?>
                                <form action="?do=runupdate" method="POST">

                                    <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>ردیف</th>
                                                <th><a href="?orderby=order">ترتیب <span class="<?= (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') ?>"></span></a></th>
                                                <th><a href="?orderby=title">عنوان سوال <span class="<?= (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') ?>"></span></a></th>
                                                <th>نوع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                $i = 1;
                                                while ($read = mysqli_fetch_array($result)) {
                                                  
                                                ?>
                                            <tr>
                                                <td style="width: 1%"><?= $i ?></td>
                                                <td class="col-sm-1">
                                                    <input type="hidden" name="code[]" value="<?= $read['tbl_questionpublic_code'] ?>">
                                                    <input type="text" name="o<?= $read['tbl_questionpublic_code'] ?>" value="<?= $read['tbl_questionpublic_order'] ?>" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                                                </td>
                                                <td class="col-sm-6"><a href="?do=edit&code=<?= $read['tbl_questionpublic_code'] ?>"><?= $read['tbl_questionpublic_title'] ?></a>
                                                </td>
                                                <td class="col-sm-1">
                                                    <?= ($read['tbl_questionpublic_model'] == 0 ? 'ارزیابی سامانه' : ($read['tbl_questionpublic_model'] == 1 ? 'ارزیابی منتور' : 'ارزیابی کوچ')) ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="col-sm-1 btn-class" colspan="4">
                                                    <a href="?do=edit&code=<?= $read['tbl_questionpublic_code']  ?>" class="btn btn-xs btn-success"><?= $lang[23] ?></a>
                                                    <a href="?do=delete&code=<?= $read['tbl_questionpublic_code'] ?>" class="btn btn-xs btn-danger"><?= $lang[24] ?></a>
                                                </td>
                                            </tr>
                                            <?php
                                                    $i++;
                                                }
                                                ?>
                                        </tbody>
                                    </table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning">
                                </form>
                                <?php
                                    if ($page > $page_number) {
                                        echo '<div class="margin-top">' . $lang[57] . '</div>';
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
                                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') ?>"><?= $lang[75] ?></a>
                                                </li>
                                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                                </li>
                                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') ?>"><?= $lang[74] ?></a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="col-lg-6">

                                        </td>
                                    </tr>
                                </table>
                                <?php
                                    }
                                }
                                if (isset($do) && $do == 'runupdate') {
                                    $class = $_POST['class'];
                                    $skill = $_POST['skill'];
                                    $code = $_POST['code'];
                                    foreach ($code as $key => $value) {
                                        $val = $_POST['o' . $value];
                                        Query("update tbl_question set tbl_questionpublic_order='$val' Where tbl_questionpublic_code = '$value' ");
                                    }
                                    header("Location: questionpublic.php?class=$class&skill=$skill");
                                }
                                if (isset($do) && $do == 'add') {
                                    ?>
                                <div class="col-lg-6">
                                    <form action="?do=additem" method="POST" enctype="multipart/form-data">
                                        <?php
                                            for ($i = 1; $i <= $language_count; $i++) {
                                            ?>
                                        <div class="form-group">
                                            <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                            <input type="text" name="title_<?= $language[$i] ?>" class="form-control">
                                        </div>
                                        <?php
                                            }
                                            ?>
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="model" value="0" checked> ارزیابی سامانه
                                            </label>
                                            <label>
                                                <input type="radio" name="model" value="1"> ارزیابی منتور
                                            </label>
                                            <label style="display: none;">
                                                <input type="radio" name="model" value="2"> ارزیابی کوچ
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
                                                    <input type="text" name="answervalue[]" class="form-control" data-type="number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="result row"></div>
                                        <div class="form-group">
                                            <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                                <?php
                                }
                                if (isset($do) && $do == 'additem') {
                                    if (isset($_POST['additem'])) {
                                        $date = jdate("Y/m/d : a:h:s");
                                        $title = $_POST['title'];
                                        $code = Code("", "no", 7);
                                        $model = $_POST['model'];
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
                                            Query("insert into tbl_questionpublic (
                                                    tbl_questionpublic_title,
                                                    tbl_questionpublic_answer,
                                                    tbl_questionpublic_dateadd,
                                                    tbl_questionpublic_lng,
                                                    tbl_questionpublic_code,
                                                    tbl_questionpublic_model,
                                                    tbl_questionpublic_disable) values(
                                                        '$title_[$i]',
                                                        '$all_value',
                                                        '$date',
                                                        '$language_name',
                                                        '$code',
                                                        '$model',
                                                        '0')");
                                        }
                                        header("Location: questionpublic.php");
                                        exit();
                                    }
                                }
                                if (isset($do) && $do == 'edit') {
                                    function read_fild($language_id = NULL, $field = NULL)
                                    {
                                        if ($language_id == NULL) {
                                            $check_language_id = "";
                                        } else {
                                            $check_language_id = "and tbl_questionpublic_lng = '$language_id'";
                                        }
                                        $code = $_GET['code'];
                                        $result = Query("SELECT * FROM tbl_questionpublic Where tbl_questionpublic_code =  '$code'  $check_language_id ");
                                        $read = mysqli_fetch_array($result);
                                        $read = $read[$field];
                                        return $read;
                                    }
                                ?>
                                <div class="col-lg-6">
                                    <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="code" value="<?= $code ?>">
                                        <?php
                                            for ($i = 1; $i <= $language_count; $i++) {
                                            ?>
                                        <div class="form-group">
                                            <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                            <input type="text" name="title_<?= $language[$i] ?>" value="<?= exjection(read_fild($language[$i], 'tbl_questionpublic_title')) ?>" class="form-control">
                                        </div>
                                        <?php } ?>
                                        <div class="form-group">
                                            <label>
                                                <input type="radio" name="model" <?= (read_fild(NULL, 'tbl_questionpublic_model') == 0 ? 'checked' : '') ?> value="0" checked> ارزیابی سامانه
                                            </label>
                                            <label>
                                                <input type="radio" name="model" <?= (read_fild(NULL, 'tbl_questionpublic_model') == 1 ? 'checked' : '') ?> value="1"> ارزیابی منتور
                                            </label>
                                            <label style="display: none;">
                                                <input type="radio" name="model" <?= (read_fild(NULL, 'tbl_questionpublic_model') == 2 ? 'checked' : '') ?> value="2"> ارزیابی کوچ
                                            </label>
                                        </div>
                                        <hr>
                                        <?php
                                            $all_value = json_decode(read_fild(NULL, 'tbl_questionpublic_answer'));
                                            foreach ($all_value as $key => $value) {
                                            ?>
                                        <div id="d<?= $key ?>" style="display: flex;">
                                            <div class="col-12 col-lg-1 col-md-12">
                                                <div class="delete-item" data="<?= $key ?>">حذف</div>
                                            </div>
                                            <div class="col-12 col-lg-4 col-md-12">
                                                <div class="form-group">
                                                    <label>جواب سوال فارسی</label>
                                                    <input type="text" name="answerfa[]" class="form-control" value="<?= $value->answerfa ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-4 col-md-12">
                                                <div class="form-group">
                                                    <label>جواب سوال انگلیسی</label>
                                                    <input type="text" name="answeren[]" class="form-control" value="<?= $value->answeren ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-lg-2 col-md-12">
                                                <div class="form-group">
                                                    <label>مقدار</label>
                                                    <input type="text" name="answervalue[]" class="form-control" value="<?= $value->answervalue ?>" data-type="number" required>
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
                                                    <input type="text" name="answervalue[]" class="form-control" data-type="number">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="result row"></div>
                                        <div class="form-group">
                                            <input type="submit" name="updateitem" value="<?= $lang[20] ?>" class="btn btn-primary">
                                        </div>
                                    </form>
                                </div>
                                <?php
                                }
                                if (isset($do) && $do == 'edititem') {
                                    if (isset($_POST['updateitem'])) {
                                        $code = $_POST['code'];
                                        $model = $_POST['model'];
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
                                            Query("Update tbl_questionpublic SET 
                                                tbl_questionpublic_model='$model',
                                                tbl_questionpublic_dateupdate='$date',
                                                tbl_questionpublic_title='$title_[$i]', 
                                                tbl_questionpublic_answer = '$all_value'  Where tbl_questionpublic_code= '$code' and tbl_questionpublic_lng='$language_record' ");
                                        }
                                        $_SESSION['error'] = $lang[21];
                                        header("Location: questionpublic.php");
                                    }
                                }
                                if (isset($do) && $do == 'delete') {
                                    $result = Query("SELECT * from tbl_questionpublic Where tbl_questionpublic_code = '$code' ");
                                    $read = mysqli_fetch_array($result);
                                ?>
                                <div class="col-lg-6"><?= $lang[25] ?><br />
                                    <h2><?= $read['tbl_questionpublic_title'] ?></h2><br /><br />
                                    <form action="" method="POST">
                                        <div class="form-group margin-top">
                                            <input type="hidden" value="<?= $code ?>" name="code">
                                            <input type="hidden" value="<?= $_GET['class'] ?>" name="class">
                                            <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-xs btn-success">
                                            <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-xs btn-danger">
                                    </form>
                                </div>
                                <?php
                                    if (isset($_POST['submit'])) {
                                        $code = $_POST['code'];
                                        $class = $_POST['class'];
                                        Query("update tbl_questionpublic set  tbl_questionpublic_disable='1' Where tbl_questionpublic_code = '$code' ");
                                        $_SESSION['error'] = $lang[28];
                                        header("Location: questionpublic.php");
                                        exit();
                                    } elseif (isset($_POST['nodelete'])) {
                                        $class = $_POST['class'];
                                        header("Location: questionpublic.php");
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