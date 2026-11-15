<?php
include './includer.php';
$show_per_page = '20';
$pagetitle = 'لیست دوره ها ';
$Tabpage = '91';
$img_item = 3;
$thisaccess = '1,4';
include './header.inc.php';

if (!empty($_GET['cat'])) {
    $cat_select = " and tbl_class_cat = '$_GET[cat]'  ";
} else {
    $cat_select = "";
}
if (!empty($_GET['orderby'])) {
    if ($_GET['orderby'] == 'order') {
        $orderby = ' ORDER BY tbl_class_order ASC ';
    }
    if ($_GET['orderby'] == 'title') {
        $orderby = ' ORDER BY tbl_class_title ASC ';
    }
} else {
    $orderby = ' ORDER BY tbl_class_order ASC ';
}

if (!empty($_GET['teacher'])) {
    $teacher_select = " and tbl_class_teacher like '%[$_GET[teacher]]%'  ";
    $result_teacher = Query("Select * from tbl_teacher where tbl_teacher_id = '$_GET[teacher]' ");
    $read_teacher = mysqli_fetch_array($result_teacher);
    echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">لیست دوره ها استاد : ' . $read_teacher['tbl_teacher_fname'] . ' ' . $read_teacher['tbl_teacher_lname'] . '</h5></div>';
} else {
    $teacher_select = "";
}
if (!empty($_GET['skill'])) {
    $skill_select = " and tbl_class_skill like '%$_GET[skill]%'  ";
    $result_skill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$_GET[skill]' ");
    $readSkill = mysqli_fetch_array($result_skill);
    echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="category_manage.php?t=4">لیست مهارت ها</a>  /  مهارت : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
} else {
    $skill_select = "";
}


?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>

            <div class="card-body p-0 position-relative">
                <?php


                $result_for_count = Query("SELECT * From tbl_class where ( tbl_class_title like '%$keyword%' or tbl_class_classcode like '%$keyword%' ) and tbl_class_lng='$language_default' and tbl_class_id !='' $teacher_select $skill_select $cat_select ORDER BY tbl_class_id ASC");
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);
                if (!isset($do)) {
                ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="class-uniq.php?do=search" method="POST" class="search-form">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <input type="hidden" class="form-control" value="<?= (!empty($_GET['skill']) ? $_GET['skill'] : '') ?>" name="skill">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>
                    <?php
                    $result = Query("SELECT * From tbl_class where ( tbl_class_title like '%$keyword%' or tbl_class_classcode like '%$keyword%' ) and tbl_class_lng='$language_default' and tbl_class_id !='' $teacher_select $skill_select $cat_select $orderby LIMIT $start_contnet,$show_per_page ");
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    $allfield = '';
                    ?>
                    <form action="?do=runupdate" method="POST"><input type="hidden" name="page" value="<?= $page ?>">
                        <div class="table-responsive scrollbar">
                            <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                <thead class="text-800 bg-light">
                                    <tr>
                                        <th><?= $lang[60] ?></th>
                                        <th><a href="?orderby=order"><?= $lang[1336] ?> <span class="<?= (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') ?>"></span></a></th>
                                        <th><?= $lang[1412] ?></th>
                                        <th><a href="?orderby=title"><?= $lang[1413] ?> <span class="<?= (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') ?>"></span></a></th>
                                        <th><?= $lang[1337] ?></th>
                                        <th><?= $lang[1359] ?></th>
                                        <th style="display: none;"><?= $lang[1360] ?></th>
                                        <th><?= $lang[1340] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    while ($read = mysqli_fetch_array($result)) {
                                        $admin = $read['tbl_class_admin'];
                                        $result_admin = Query("select * from tbl_user where tbl_user_id='$admin' ");
                                        $read_admin = mysqli_fetch_array($result_admin);
                                        $thisskill = explode(",", $read['tbl_class_skill']);
                                        $all_skill = '';
                                        foreach ($thisskill as $key => $value) {
                                            if (!empty($value)) {
                                                $result_skill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default'");
                                                $read_skill = mysqli_fetch_array($result_skill);
                                                $all_skill .= @$read_skill['tbl_categories_title'] . '<br>';
                                            }
                                        }
                                        $thisTeacher = explode(",", $read['tbl_class_teacher']);
                                        $all_teacher = '';
                                        foreach ($thisTeacher as $key => $value) {
                                            if (!empty($value)) {
                                                $value = str_replace("[", "", str_replace("]", "", $value));
                                                $result_teacher = Query("Select * from tbl_teacher where tbl_teacher_id = '$value' ");
                                                while ($read_teacher = mysqli_fetch_array($result_teacher)) {
                                                    $all_teacher .= $read_teacher['tbl_teacher_fname'] . ' ' . $read_teacher['tbl_teacher_lname'] . '<br>';
                                                }
                                            }
                                        }
                                        $karbord = $read['tbl_class_karbord'];
                                        $result_karbord = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$karbord' and tbl_categories_lng='$language_default'");
                                        $read_karbord = mysqli_fetch_array($result_karbord);
                                        $result_course = Query("select * from tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_class='$read[tbl_class_code]' ");
                                        $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_class_code]'");
                                        $result_question = Query("select * from tbl_question where tbl_question_lng='$language_default' and tbl_question_disable='0' and tbl_question_parent='$read[tbl_class_code]'");
                                    ?>
                                        <tr>
                                            <td class="align-middle p-2"><?= $i ?></td>
                                            <td class="align-middle p-2"><input type="hidden" name="code[]" value="<?= $read['tbl_class_code'] ?>"><input type="text" name="o<?= $read['tbl_class_code'] ?>" value="<?= $read['tbl_class_order'] ?>" maxlength="5" style="width: 40px;text-align: center;"></td>
                                            <td class="align-middle p-2"><?= $read['tbl_class_classcode'] ?></td>
                                            <td class="col-sm-2"><span style="<?= ($read['tbl_class_status'] == 0 ? 'color:red;' : 'color:green;') ?>"><?= $read['tbl_class_title'] . ($read['tbl_class_special'] == 1 ? '&nbsp;' . $lang[1364] : '') ?></span></td>
                                            <td class="align-middle p-2"><?= $all_skill ?></td>
                                            <td class="align-middle p-2"><?= ($read['tbl_class_level'] == 0 ? $lang[1361] : ($read['tbl_class_level'] == 1 ? $lang[1362] : $lang[1363])) ?></td>
                                            <td class="align-middle p-2" style="display: none;"><?= $all_teacher ?></td>
                                            <td class="align-middle p-2">
                                                <a href="?do=edit&id=<?= $read['tbl_class_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($_GET['teacher']) ? '&teacher=' . $_GET['teacher'] : '') ?>" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[23] ?>"><span class="bi-pencil-square"></span></a>
                                                <a href="?do=delete&id=<?= $read['tbl_class_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($_GET['teacher']) ? '&teacher=' . $_GET['teacher'] : '') ?>" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[24] ?>"><span class="bi-trash"></span></a>
                                                <a href="register.php?class=<?= $read['tbl_class_code'] ?><?= (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') ?>" target="_blank" class="btn me-1 mb-1 btn-sm btn-warning " data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1365] ?>"><span class="bi-pencil-square"></span></a>
                                                <a href="parentcourse.php?class=<?= $read['tbl_class_code'] ?><?= (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') ?>" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1366] ?>"><span class="bi-book-half"></span></a>
                                                <a style="display:none;" href="questionclass.php?class=<?= $read['tbl_class_code'] ?><?= (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') ?>" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1367] ?> (<?= mysqli_num_rows($result_question) ?>)"><span class="bi-patch-question"></span></a>
                                            </td>
                                        </tr>
                                    <?php
                                        $i++;
                                    }
                                    ?>
                            </table><input type="submit" name="runupdate" value="<?= $lang[1342] ?>" class="btn btn-warning update-btn m-3">
                    </form>
                    <?php
                    if ($page > $page_number) {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
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
                                <td class="col-lg-9">
                                    <ul class="pagination">
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . (!empty($_GET['teacher']) ? '&teacher=' . $_GET['teacher'] : '') . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($_GET['cat']) ? '&cat=' . $_GET['cat'] : '') . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . (!empty($_GET['teacher']) ? '&teacher=' . $_GET['teacher'] : '') . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . (!empty($_GET['cat']) ? '&cat=' . $_GET['cat'] : '') . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="col-lg-3">
                                    <form action="class-uniq.php?do=gotopage" class="search-form" method="POST">
                                        <div class="form-group">
                                            <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
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
                    $page = $_POST['page'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_class set tbl_class_order='$val' Where tbl_class_code = '$value' ");
                    }
                    header("Location: class-uniq.php?page=$page");
                }
                if (isset($do) && $do == 'gotopage') {
                    if (isset($_POST['gotopage'])) {
                        $page_number = $_POST['page_number'];
                        header("Location: class-uniq.php?page=$page_number");
                    }
                }
                if (isset($do) && $do == 'add') {
                    ?>
                    <div class="col-lg-10 p-3">
                        <form action="class-uniq.php?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                            <input type="hidden" name="skillurl" value="<?= (!empty($_GET['skill']) ? $_GET['skill'] : '') ?>">
                            <input type="hidden" name="teacherurl" value="<?= (!empty($_GET['teacher']) ? $_GET['teacher'] : '') ?>">

                            <div class="form-group">
                                <label><?= $lang[1337] ?></label><select name="skill[]" style="width: 100%;" class="select2" multiple="multiple">
                                    <option value="0"><?= $lang[70] ?></option>
                                    <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='4' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option ' . (!empty($_GET['skill']) && $_GET['skill'] == $read_access['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $lang[1838] ?><span class="required">*</span></label>
                                <div>
                                    <select name="department" data-placeholder="<?= $lang[1838] ?>" style="width:100%;" class="select2" required>
                                        <option value=""><?= $lang[70] ?></option>
                                        <?php
                                        $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                        while ($readKey = mysqli_fetch_array($resultKey)) {
                                            echo '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat" >' . $readKey['tbl_categories_title'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <hr>

                            <div class="form-group">
                                <label><?= $lang[1303] ?></label><select name="karbord" class="form-control select2">
                                    <option value="0"><?= $lang[70] ?></option>
                                    <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='6' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                        <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                        <input type="text" name="title_' . $language[$i] . '" class="form-control" ' . ($i == '1' ? 'required' : '') . '>
                        </div>';
                            }
                            ?>
                            <div class="form-group">
                                <label><?= $lang[1412] ?></label>
                                <input type="text" name="classcode" class="form-control">
                            </div>
                            <?php

                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                            <label>' . $lang[2067] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="shortcontent_' . $language[$i] . '" class="form-control"></textarea>
                            </div>';
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                        <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                        <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                        </div>';
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                            <label>' . $lang[1414] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="contentend_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                            </div>';
                            }
                            ?>

                            <div class="form-group">
                                <label><?= $lang[1152] ?></label>
                                <input type="text" name="price" class="form-control">
                            </div>

                            <div class="form-group" style="display: none;">
                                <label><?= $lang[1415] ?></label>
                                <?php
                                $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '0' ");
                                while ($read_cer = mysqli_fetch_array($result_cer)) {
                                    echo '<div class="checkbox">
                                    <label>
                                    <input type="checkbox" name="cer[]" value="' . $read_cer['tbl_certificate_id'] . '"> ' . $read_cer['tbl_certificate_title'] . ' ، ' . $lang[1416] . ' : ' . number_format($read_cer['tbl_certificate_price']) . '</label></div>';
                                }
                                ?>
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1417] ?></label>
                                <input type="text" name="time" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1418] ?></label>
                                <input type="text" name="mortabet" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1419] ?></label>
                                <input type="text" name="sath" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1420] ?></label>
                                <input type="text" name="pish" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1421] ?></label>
                                <input type="text" name="modelclass" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1422] ?></label>
                                <input type="text" name="update" class="form-control datepicker">
                            </div>
                            <div class="form-group" style="display: none;">
                                <label><?= $lang[1423] ?></label><select name="teacher[]" class="form-control select2" multiple="multiple">
                                    <?php
                                    $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_teacher_id'] . '">' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1424] ?></label><select name="logo" class="form-control select2">
                                    <?php
                                    $result_access = Query("Select * from tbl_logo order by tbl_logo_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_logo_id'] . '">' . $read_access['tbl_logo_title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1425] ?></label><select name="model" class="form-control select2">
                                    <?php
                                    for ($i = 1230; $i <= 1235; $i++) {
                                        echo '<option value="' . $i . '">' . $lang[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1426] ?></label>
                                <input type="text" name="question" class="form-control">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1427] ?></label>
                                <input type="text" name="order" class="form-control">
                            </div>
                            <?php
                            for ($i = 1; $i <= $img_item; $i++) {
                            ?>
                                <div class="form-group file-margin">
                                    <table>
                                        <tr>
                                            <td><label class="col-sm-12 control-label"><?= $lang[1153] . '&nbsp; ' . $i ?></label>
                                            </td>
                                            <td><input type="file" name="img_<?= $i ?>">
                                                <input type="text" name="ftplink_<?= $i ?>" class="form-control" placeholder="<?= $lang[1428] ?>">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            <?php
                            }
                            ?>
                            <hr>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="status" value="1"> <?= $lang[1429] ?> </label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="special" value="1"><?= $lang[1430] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="qmodir" value="1"><?= $lang[1431] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="tamrin" value="1"><?= $lang[1432] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="emtehan" value="1"><?= $lang[1433] ?></label>
                            </div>
                            <hr>
                            <div class="form-group check-style">
                                <label><input type="radio" name="level" value="0" checked><?= $lang[1361] ?></label>
                                <label><input type="radio" name="level" value="1"><?= $lang[1362] ?></label>
                                <label><input type="radio" name="level" value="2"><?= $lang[1363] ?></label>
                            </div>
                            <hr>
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
                        $level = (!empty($_POST['level']) ? $_POST['level'] : '0');
                        $emtehan = (!empty($_POST['emtehan']) ? $_POST['emtehan'] : '0');
                        $tamrin = (!empty($_POST['tamrin']) ? $_POST['tamrin'] : '0');
                        $question = (!empty($_POST['question']) ? $_POST['question'] : '0');
                        $mortabet = (!empty($_POST['mortabet']) ? $_POST['mortabet'] : '0');
                        $modelclass = $_POST['modelclass'];

                        $departmentRef = explode("-", $_POST['department']);

                        $department = $departmentRef[0];
                        $departmentCode = strtolower($departmentRef[1]);

                        $model = $_POST['model'];
                        $sath = $_POST['sath'];

                        $skill = '';
                        if (!empty($_POST['skill'])) {
                            $skill_orginal = $_POST['skill'];
                            foreach ($skill_orginal as $key => $value) {
                                if (!empty($value)) {
                                    $skill .= $value . ',';
                                }
                            }
                        }

                        $pish = $_POST['pish'];
                        $time = $_POST['time'];
                        $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                        $admin = (!empty($_POST['admin']) ? $_POST['admin'] : '0');
                        $systemcode = $_POST['systemcode'];
                        $classcode = $_POST['classcode'];
                        $karbord = $_POST['karbord'];
                        //$cat = explode("-", $_POST['cat']);
                        $cat = explode("-", "0-0");
                        $skillurl = $_POST['skillurl'];
                        $teacherurl = $_POST['teacherurl'];
                        $redirect = '';
                        if (!empty($skillurl)) {
                            $redirect .= '&skill=' . $skillurl;
                        }
                        if (!empty($teacherurl)) {
                            $redirect .= '&teacher=' . $teacherurl;
                        }

                        $price = (!empty($_POST['price']) ? $_POST['price'] : '0');
                        $update = $_POST['update'];

                        $certificate  = '';
                        if (!empty($_POST['cer'])) {
                            $cer = $_POST['cer'];
                            foreach ($cer as $key => $value) {
                                $certificate .= $value . ',';
                            }
                        }

                        $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                        $special = (!empty($_POST['special']) ? $_POST['special'] : '0');

                        $qmodir = (!empty($_POST['qmodir']) ? $_POST['qmodir'] : '0');
                        $teacher_all = '';
                        if (!empty($_POST['teacher'])) {
                            $teacher = $_POST['teacher'];
                            foreach ($teacher as $key => $value) {
                                $teacher_all .= '[' . $value . '],';
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
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                            $shortcontent_[$i] = addslashes($_POST['shortcontent_' . $language[$i]]);
                            $contentend_[$i] = addslashes($_POST['contentend_' . $language[$i]]);
                            if (empty($title_[1])) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: class-uniq.php?$redirect");

                                exit();
                            }

                            $language_name = $language[$i];
                            Query("INSERT INTO tbl_class (
                tbl_class_content,
                tbl_class_shortcontent,
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
                tbl_class_qmodir,
                tbl_class_level,
                tbl_class_skill,
                tbl_class_karbord,
                tbl_class_lng,
                tbl_class_model,
                tbl_class_department,
                tbl_class_departmentcode) 
                VALUES (
                    '$content_[$i]',
                    '$shortcontent_[$i]',
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
                    '$qmodir',
                    '$level',
                    '$skill',
                    '$karbord',
                    '$language_name',
                    '$model',
                    '$department',
                    '$departmentcode'
                    )");
                        }
                    }
                    header("Location: class-uniq.php?$redirect");
                    exit();
                }
                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_class Where tbl_class_code = '$id' ");
                    $read = mysqli_fetch_array($result);
                ?>
                    <div class="col-lg-6 col-12 p-4">
                        <p><?= $lang[25] ?></p>
                        <h3><?= $read['tbl_class_title'] ?></h3>
                        <form action="#" method="POST">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="code" value="<?= $read['tbl_class_code'] ?>">
                            <input type="hidden" name="skillurl" value="<?= (!empty($_GET['skill']) ? $_GET['skill'] : '') ?>">
                            <input type="hidden" name="teacherurl" value="<?= (!empty($_GET['teacher']) ? $_GET['teacher'] : '') ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <div class="form-group margin-top">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success mt-3">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger mt-3">
                        </form>
                    </div>
                    <?php
                    if (isset($_POST['submit'])) {
                        $id = $_POST['id'];
                        $skillurl = $_POST['skillurl'];
                        $teacherurl = $_POST['teacherurl'];
                        $redirect = '';
                        if (!empty($skillurl)) {
                            $redirect .= '&skill=' . $skillurl;
                        }
                        if (!empty($teacherurl)) {
                            $redirect .= '&teacher=' . $teacherurl;
                        }
                        $result_course = Query("select * from tbl_course where tbl_course_class='$_POST[code]' ");
                        if (mysqli_num_rows($result_course) != 0) {
                            $_SESSION['error'] = $lang[1368];
                        } else {
                            Query("Delete From tbl_class Where tbl_class_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                        }

                        header("Location: class-uniq.php$redirect");

                        exit();
                    }
                    if (isset($_POST['nodelete'])) {
                        $skillurl = $_POST['skillurl'];
                        $teacherurl = $_POST['teacherurl'];
                        $redirect = '';
                        if (!empty($skillurl)) {
                            $redirect .= '&skill=' . $skillurl;
                        }
                        if (!empty($teacherurl)) {
                            $redirect .= '&teacher=' . $teacherurl;
                        }
                        header("Location: class-uniq.php?$redirect");
                    }
                }
                if (isset($do) && $do == 'edit') {
                    $id = $_GET['id'];


                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        global $this_user_id, $access;
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_class_lng = '$language_id'";
                        }
                        $id = $_GET['id'];
                        if ($access == '1') {
                            $result = Query("SELECT * FROM tbl_class Where tbl_class_code =  '$id'  $check_language_id ");
                        } else {
                            $result = Query("SELECT * FROM tbl_class Where tbl_class_code =  '$id'  $check_language_id ");
                        }

                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }

                    ?>
                    <div class="col-lg-10 col-12 p-3">
                        <form action="?do=edititem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="skillurl" value="<?= (!empty($_GET['skill']) ? $_GET['skill'] : '') ?>">
                            <input type="hidden" name="teacherurl" value="<?= (!empty($_GET['teacher']) ? $_GET['teacher'] : '') ?>">
                            <input type="hidden" name="id" value="<?= $id ?>">
                            <input type="hidden" name="code" value="<?= read_fild(Null, 'tbl_class_code') ?>">

                            <div class="form-group">
                                <label><?= $lang[1337] ?></label><select name="skill[]" style="width:100%;" class="select2" multiple="multiple">
                                    <option value="0"><?= $lang[70] ?></option>
                                    <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='4' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        $skill_code = $read_access['tbl_categories_code'];
                                        echo '<option value="' . $read_access['tbl_categories_code'] . '" ' . (stripos(read_fild(Null, 'tbl_class_skill'), "$skill_code") !== false ? 'selected' : '') . '>' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $lang[1838] ?><span class="required">*</span></label>
                                <div>
                                    <select name="department" data-placeholder="<?= $lang[1838] ?>" style="width:100%;" class="select2" required>
                                        <option value=""><?= $lang[70] ?></option>
                                        <?php
                                        $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                        while ($readKey = mysqli_fetch_array($resultKey)) {
                                            echo '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat" ' . (strpos(read_fild(Null, 'tbl_class_department'), $readKey['tbl_categories_code']) !== false ? 'selected' : '') . '>' . $readKey['tbl_categories_title'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <hr>

                            <div class="form-group">
                                <label><?= $lang[1353] ?></label><select name="karbord" class="form-control select2">
                                    <option value="0"><?= $lang[70] ?></option>
                                    <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='6' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option ' . (read_fild(Null, 'tbl_class_karbord')  == $read_access['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>


                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                            ?>
                                <div class="form-group">
                                    <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                    <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= read_fild($language[$i], 'tbl_class_title') ?>" <?= ($i == 1 ? 'required' : '') ?>>
                                </div>
                            <?php
                            }
                            ?>
                            <div class="form-group">
                                <label><?= $lang[1412] ?></label>
                                <input type="text" name="classcode" class="form-control" value="<?= read_fild(Null, 'tbl_class_classcode') ?>">
                            </div>
                            <?php

                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                            <label>' . $lang[2067] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="shortcontent_' . $language[$i] . '" class="form-control">' . read_fild($language[$i], 'tbl_class_shortcontent') . '</textarea>
                            </div>';
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                            ?>
                                <div class="form-group">
                                    <label><?= $lang[51] ?><span class="language"><?= $language[$i] ?></span></label>
                                    <textarea name="content_<?= $language[$i] ?>" class="editor editor_<?= $language[$i] ?>"><?= read_fild($language[$i], 'tbl_class_content') ?></textarea>
                                </div>
                            <?php
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                            ?>
                                <div class="form-group">
                                    <label><?= $lang[1414] ?></label>
                                    <textarea name="contentend_<?= $language[$i] ?>" class="editor editor_<?= $language[$i] ?>"><?= read_fild($language[$i], 'tbl_class_contentend') ?></textarea>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="form-group">
                                <label><?= $lang[1152] ?></label>
                                <input type="text" name="price" class="form-control" value="<?= read_fild(Null, 'tbl_class_price') ?>">
                            </div>
                            <div class="form-group" style="display: none;">
                                <label><?= $lang[1415] ?></label>
                                <?php
                                $result_cer = Query("SELECT * from tbl_certificate where tbl_certificate_workshop = '0' ");
                                while ($read_cer = mysqli_fetch_array($result_cer)) {
                                ?>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" <?= (strpos(read_fild(Null, 'tbl_class_certificate'), $read_cer['tbl_certificate_id'] . ',') !== false ? 'checked' : '') ?> name="cer[]" value="<?= $read_cer['tbl_certificate_id'] ?>">
                                            <?= $read_cer['tbl_certificate_title'] ?> ، <?= $lang[1416] ?> :
                                            <?= number_format($read_cer['tbl_certificate_price']) ?></label>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>

                            <div class="form-group">
                                <label><?= $lang[1417] ?></label>
                                <input type="text" name="time" class="form-control" value="<?= read_fild(Null, 'tbl_class_time')  ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1418] ?></label>
                                <input type="text" name="mortabet" class="form-control" value="<?= read_fild(Null, 'tbl_class_mortabet')  ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1419] ?></label>
                                <input type="text" name="sath" class="form-control" value="<?= read_fild(Null, 'tbl_class_sath')  ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1420] ?></label>
                                <input type="text" name="pish" class="form-control" value="<?= read_fild(Null, 'tbl_class_pish')  ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1421] ?></label>
                                <input type="text" name="modelclass" class="form-control" value="<?= read_fild(Null, 'tbl_class_modelclass')  ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1422] ?></label>
                                <input type="text" name="updatedate" class="form-control datepicker" value="<?= read_fild(Null, 'tbl_class_update')  ?>">
                            </div>
                            <?php
                            $teacher = read_fild(Null, 'tbl_class_teacher');
                            ?>
                            <div class="form-group" style="display: none;">
                                <label><?= $lang[1423] ?></label><select name="teacher[]" class="form-control select2" multiple="multiple">
                                    <?php
                                    $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        $thisid = '[' . $read_access['tbl_teacher_id'] . ']';
                                    ?>
                                        <option value="<?= $read_access['tbl_teacher_id'] ?>" <?= (strpos($teacher, "$thisid") !== false ? 'selected' : '') ?>>
                                            <?= $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] ?>
                                        </option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1424] ?></label><select name="logo" class="form-control select2">
                                    <?php
                                    $result_access = Query("Select * from tbl_logo order by tbl_logo_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                    ?>
                                        <option value="<?= $read_access['tbl_logo_id'] ?>" <?= ($read_access['tbl_logo_id'] == read_fild(Null, 'tbl_class_logo')    ? 'selected' : '') ?>>
                                            <?= $read_access['tbl_logo_title'] ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $lang[1425] ?></label><select name="model" class="form-control select2">
                                    <?php
                                    for ($i = 1230; $i <= 1235; $i++) {
                                        echo '<option ' . ($i == read_fild(Null, 'tbl_class_model') ? 'selected' : '') . ' value="' . $i . '">' . $lang[$i] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><?= $lang[1426] ?></label>
                                <input type="text" name="question" class="form-control" value="<?= read_fild(Null, 'tbl_class_question') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[1427] ?></label>
                                <input type="text" name="order" class="form-control" value="<?= read_fild(Null, 'tbl_class_order') ?>">
                            </div>

                            <input type="hidden" name="allimages" value="<?= read_fild(Null, 'tbl_class_img') ?>">
                            <?php
                            for ($i = 1; $i <= $img_item; $i++) {
                                $exp = explode(",", read_fild(Null, 'tbl_class_img'));
                                if ($exp[$i - 1]) {
                            ?>
                                    <div class="form-group file-margin">
                                        <table>
                                            <tr>
                                                <td><label class="col-sm-12 control-label"><?= $lang[53] . '&nbsp; ' . $i ?></label>
                                                </td>
                                                <td><input type="file" name="img_<?= $i ?>">
                                                    <input type="text" name="ftplink_<?= $i ?>" value="<?= $exp[$i - 1] ?>" class="form-control" placeholder="<?= $lang[1428] ?>">
                                                </td>
                                                <td>
                                                    <a href="../upload/<?= $exp[$i - 1] ?>" target="_blank"><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                                    <a href="?id=<?= $id ?>&do=deleteimage&img=<?= $exp[$i - 1] ?>"><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php
                                } else {
                                ?>
                                    <div class="form-group file-margin">
                                        <table>
                                            <tr>
                                                <td><label class="col-sm-12 control-label"><?= $lang[53] . '&nbsp; ' . $i ?></label>
                                                </td>
                                                <td><input type="file" name="img_<?= $i ?>">
                                                    <input type="text" name="ftplink_<?= $i ?>" class="form-control" placeholder="<?= $lang[1428] ?>">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                            <?php
                                }
                            }
                            ?>
                            <hr>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="status" value="1" <?= (read_fild(Null, 'tbl_class_status') == 1 ? 'checked' : '') ?>> <?= $lang[1429] ?>
                                </label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="special" value="1" <?= (read_fild(Null, 'tbl_class_special') == 1 ? 'checked' : '') ?>><?= $lang[1430] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="qmodir" value="1" <?= (read_fild(Null, 'tbl_class_qmodir') == 1 ? 'checked' : '') ?>><?= $lang[1431] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="tamrin" value="1" <?= (read_fild(Null, 'tbl_class_tamrin') == 1 ? 'checked' : '') ?>><?= $lang[1432] ?></label>
                            </div>
                            <div class="form-group check-style">
                                <label> <input type="checkbox" name="emtehan" value="1" <?= (read_fild(Null, 'tbl_class_emtehan') == 1 ? 'checked' : '') ?>><?= $lang[1433] ?></label>
                            </div>
                            <hr>
                            <div class="form-group check-style">
                                <label><input type="radio" name="level" value="0" <?= (read_fild(Null, 'tbl_class_img') == 0 ? 'checked' : '') ?>><?= $lang[1361] ?></label>
                                <label><input type="radio" name="level" value="1" <?= (read_fild(Null, 'tbl_class_img') == 1 ? 'checked' : '') ?>><?= $lang[1362] ?></label>
                                <label><input type="radio" name="level" value="2" <?= (read_fild(Null, 'tbl_class_img') == 2 ? 'checked' : '') ?>><?= $lang[1363] ?></label>
                            </div>
                            <hr>
                            <div class="form-group">
                                <input type="submit" name="update" value="<?= $lang[20] ?>" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                <?php
                }
                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['update'])) {
                        $order = $_POST['order'];
                        $admin = (!empty($_POST['admin']) ? $_POST['admin'] : '0');
                        $level = (!empty($_POST['level']) ? $_POST['level'] : '0');
                        $tamrin = (!empty($_POST['tamrin']) ? $_POST['tamrin'] : '0');
                        $emtehan = (!empty($_POST['emtehan']) ? $_POST['emtehan'] : '0');
                        $departmentRef = explode("-", $_POST['department']);
                        $department = $departmentRef[0];
                        $departmentCode = strtolower($departmentRef[1]);
                        $update = $_POST['updatedate'];
                        $code = $_POST['code'];
                        $skill_orginal = $_POST['skill'];
                        $skill = '';
                        foreach ($skill_orginal as $key => $value) {
                            if (!empty($value)) {
                                $skill .= $value . ',';
                            }
                        }
                        $modelclass = $_POST['modelclass'];
                        $model = $_POST['model'];
                        $cer = (!empty($_POST['cer']) ? $_POST['cer'] : '0');
                        if (!empty($_POST['cer'])) {
                            $certificate = '';
                            foreach ($cer as $key => $value) {
                                $certificate .= $value . ',';
                            }
                        } else {
                            $certificate = '';
                        }

                        $skillurl = $_POST['skillurl'];
                        $teacherurl = $_POST['teacherurl'];
                        $redirect = '';
                        if (!empty($skillurl)) {
                            $redirect .= '&skill=' . $skillurl;
                        }
                        if (!empty($teacherurl)) {
                            $redirect .= '&teacher=' . $teacherurl;
                        }

                        $question = $_POST['question'];
                        $id = $_POST['id'];
                        $logo = $_POST['logo'];
                        $classcode = $_POST['classcode'];
                        $karbord = $_POST['karbord'];
                        $cat = explode("-", "0-0");
                        //$cat = explode("-", $_POST['cat']);
                        $price = $_POST['price'];
                        $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                        $teacher = (!empty($_POST['teacher']) ? $_POST['teacher'] : '');
                        $special = (!empty($_POST['special']) ? $_POST['special'] : '0');
                        $mortabet = $_POST['mortabet'];
                        $sath = $_POST['sath'];
                        $pish = $_POST['pish'];
                        $time = $_POST['time'];
                        $qmodir = (!empty($_POST['qmodir']) ? $_POST['qmodir'] : '');
                        if (!empty($_POST['teacher'])) {
                            $teacher_all = '';
                            foreach ($teacher as $key => $value) {
                                $teacher_all .= '[' . $value . '],';
                            }
                        } else {
                            $teacher_all = '';
                        }


                        $allimages = $_POST['allimages'];
                        $read_allimages = explode(",", $allimages);

                        $images_name = '';
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if (!empty($img_[$i_image])) {
                                    if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    } else {
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
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
                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                            $shortcontent_[$i] = addslashes($_POST['shortcontent_' . $language[$i]]);
                            $contentend_[$i] = addslashes($_POST['contentend_' . $language[$i]]);

                            if (empty($title_[1])) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: class-uniq.php?$redirect");

                                exit();
                            }

                            Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");
                            $language_name = $language[$i];

                            Query("Update tbl_class SET  
                                                    tbl_class_order='$order',
                                                    tbl_class_special='$special',
                                                    tbl_class_admin='$admin',
                                                    tbl_class_cat='$cat[0]',
                                                    tbl_class_parentcode='$cat[1]', 
                                                    tbl_class_content='$content_[$i]',
                                                    tbl_class_shortcontent='$shortcontent_[$i]',
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
                                                    tbl_class_tamrin='$tamrin',
                                                    tbl_class_level='$level',
                                                    tbl_class_skill='$skill',
                                                    tbl_class_karbord='$karbord',
                                                    tbl_class_model='$model',
                                                    tbl_class_department='$department',
                                                    tbl_class_departmentcode='$departmentCode'
                                                    where  tbl_class_lng='$language_name' and tbl_class_code='$id'");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: class-uniq.php?$redirect");
                    }
                }
                if (isset($do) && $do == 'deleteimage') {
                    $result = Query("SELECT * from tbl_class Where tbl_class_id = '$id' ");
                    $read = mysqli_fetch_array($result);
                ?>
                    <div class="col-lg-6 p-3 col-12">
                        <h4><?= $lang[25] ?></h4><img style="width:35%; height:35%;" src="<?= $directory . $img ?>"><br />
                        <form action=" " method="POST">
                            <div class="form-group margin-top">
                                <input type="hidden" name="img" value="<?= $img ?>">
                                <input type="hidden" value="<?= $id ?>" name="id">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success mt-3">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger mt-3">
                        </form>
                    </div>
                <?php
                    if (isset($_POST['submit'])) {
                        $img = $_POST['img'];
                        $id = $_POST['id'];
                        $record = str_replace("$img", "", $read['tbl_class_img']);
                        $external_file = $directory . $img;
                        unlink("$external_file");
                        Query("Update tbl_class SET tbl_class_img = '$record' Where tbl_class_id = '$id' ");
                        header("Location: class-uniq.php?do=edit&id=$id");
                    } elseif (isset($_POST['nodelete'])) {
                        header("Location: class-uniq.php?do=edit&id=$id");
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        $skill = (!empty($_POST['skill']) ? '&skill=' . $_POST['skill'] : '');
                        header("Location: ?keyword=$keyword_search&$skill");
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