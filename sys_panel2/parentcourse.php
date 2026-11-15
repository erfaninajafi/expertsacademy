<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1685];
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

    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0"><a href="category_manage.php?t=4">' . $lang[1304] . '</a></h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1337] . '  : <a href="class.php?skill=' . $_GET['skill'] . '">' . $readSkill['tbl_categories_title'] . '</a></h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1684] . '  : ' . $readClass['tbl_class_title'] . '</h5>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>                    
    </div>
    </div>';
} else if (!empty($_GET['class'])) {
    $result_class = Query("Select tbl_class_title from tbl_class where tbl_class_code = '$_GET[class]' and tbl_class_lng='$language_default'");
    $read_class = mysqli_fetch_array($result_class);

    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[710] . '  : <a href="class-uniq.php">' . $read_class['tbl_class_title'] . '</a></h5>
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
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) ? '<div class="plus">
                                <a href="?do=add' . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
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

                    if (!empty($_GET['class'])) {
                        $thiscalss = " tbl_courseparent_class = '$_GET[class]' and ";
                    } else {
                        header("Location: class.php");
                        exit();
                    }

                    $result_for_count = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' and tbl_courseparent_title like '%$keyword%' and tbl_courseparent_lng='$language_default' ");
                    // start count db result for pagination               
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    $result = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !='' and tbl_courseparent_title like '%$keyword%' and tbl_courseparent_lng='$language_default' $orderby  LIMIT $start_contnet,$show_per_page ");
                    $row = mysqli_num_rows($result);
                    // 0 result search error 
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    $allfield = '';
                    echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="class" value="' . $_GET['class'] . '"><input type="hidden" name="page" value="' . $page . '">
                        <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                    <tr>
                                        <th>' . $lang[60] . '</th>
                                        <th><a href="?orderby=order&class=' . $_GET['class'] . '">' . $lang[1336] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                        <th>' . $lang[1680] . '</th>
                                        <th><a href="?orderby=title&class=' . $_GET['class'] . '">' . $lang[1681] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                        <th>' . $lang[1521] . '</th>
                                        <th>' . $lang[63] . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    $i = 1;  // for record number
                    while ($read = mysqli_fetch_array($result)) {
                        $admin = $read['tbl_courseparent_admin'];
                        $result_cat = Query("SELECT tbl_course_title FROM tbl_course Where tbl_course_delete='0' and tbl_course_lng='$language_default' and tbl_course_class =  '$read[tbl_courseparent_code]'");
                        $result_register = Query("select * from tbl_question where tbl_question_lng='$language_default' and tbl_question_disable='0' and tbl_question_parent='$read[tbl_courseparent_code]'");

                        // parent menu code
                        echo '<tr><td class="align-middle p-2">' . $i . '</td>
                                 <td class="align-middle p-2"><input type="hidden" name="code[]" value="' . $read['tbl_courseparent_code'] . '"><input type="text" name="o' . $read['tbl_courseparent_code'] . '" value="' . $read['tbl_courseparent_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>
                                 <td class="align-middle p-2">' . $read['tbl_courseparent_classcode'] . '</td>
                                <td class="align-middle p-2"><span style="' . ($read['tbl_courseparent_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_courseparent_title'] . '' . ($read['tbl_courseparent_free'] == 1 ? $lang[1364] : '') . '</span></td>
                                <td class="align-middle p-2">';
                        $k = 1;
                        while ($readCourse = mysqli_fetch_array($result_cat)) {
                            echo '<label>' . $k . ' - ' . $readCourse['tbl_course_title'] . '</label><br>';
                            $k++;
                        }
                        echo '</td>
                                <td class="align-middle p-2">'
                            . '<a href="?do=edit&code=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&code=' . $read['tbl_courseparent_code'] . '&class=' . $_GET['class'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                <a href="course.php?class=' . $read['tbl_courseparent_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn me-1 mb-1 btn-sm btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1521] . ' (' . (mysqli_num_rows($result_cat) > 0 ? mysqli_num_rows($result_cat) : '0') . ')"><span class="bi-book-half"></span></a>
                                <a href="question.php?class=' . $read['tbl_courseparent_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1367] . ' (' . mysqli_num_rows($result_register) . ')"><span class="bi-list-ol"></span></a></td>'
                            . '</tr>';
                        $i++;
                    }
                    echo '</table><input type="submit" name="runupdate" value="' . $lang[1342] . '" class="btn btn-warning update-btn m-3" ></form>';

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
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="parentcourse.php?do=gotopage" class="search-form" method="POST">
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
                <div class="col-lg-8 col-12 p-3">
                    <form action="parentcourse.php?do=additem" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                        <input type="hidden" name="class" value="<?= $_GET['class'] ?>">
                        <div class="form-group">
                            <label><?= $lang[1680] ?></label>
                            <input type="text" name="code" class="form-control">
                        </div>
                        <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                    <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                    <input type="text" name="title_' . $language[$i] . '" class="form-control" ' . ($i == 1 ? 'required' : '') . '>
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
                            <label><?= $lang[1427] ?></label>
                            <input type="text" name="order" class="form-control">
                        </div>
                        <hr>
                        <div class="form-group check-style">
                            <label><input type="checkbox" name="status" value="1"><?= $lang[1682] ?> </label>
                        </div>
                        <div class="form-group check-style">
                            <label><input type="checkbox" name="free" value="1"><?= $lang[1683] ?> </label>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary mt-3">
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
                    echo '<div class="col-lg-8 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_courseparent_title'] . '</h3>';
                    echo '<form action="#" method="POST">
        <input type="hidden" name="code" value="' . $code . '" >
        <input type="hidden" value="' . $_GET['class'] . '" name="class" >
        <div class="form-group margin-top">
        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3">
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
                    echo '<div class="col-lg-8 col-12 p-3">
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
                <label>' . $lang[1427] . '</label>
                <input type="text" name="order" class="form-control" value="' . read_fild(NULL, 'tbl_courseparent_order') . '">
                </div><hr>
                <div class="form-group check-style">
                  <label><input type="checkbox" name="status" value="1" ' . (read_fild(NULL, 'tbl_courseparent_status') == 1 ? 'checked' : '') . '>' . $lang[1682] . ' </label>
                  </div>
                  <div class="form-group check-style">
                  <label><input type="checkbox" name="free" value="1" ' . (read_fild(NULL, 'tbl_courseparent_free') == 1 ? 'checked' : '') . '>' . $lang[1683] . ' </label>
                  </div>';
                    echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary mt-3">
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