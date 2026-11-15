<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1521];
$Tabpage = '91';
$img_item = 10;
$thisaccess = '1,4';
include './header.inc.php';

$result = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$getclass' ");
$read = mysqli_fetch_array($result);

if (!empty($getorderby)) {
    if ($getorderby == 'order') {
        $orderby = ' ORDER BY tbl_course_order ASC ';
    }
    if ($getorderby == 'title') {
        $orderby = ' ORDER BY tbl_course_title ASC ';
    }
} else {
    $orderby = ' ORDER BY tbl_course_order ASC ';
}

if (!empty($getclass)) {
    $resultCourse = Query("SELECT * From tbl_courseparent where tbl_courseparent_lng='$language_default' and tbl_courseparent_code='$getclass' ");
    $readCourse = mysqli_fetch_array($resultCourse);
    $thisClass = $readCourse['tbl_courseparent_class'];
    $resultClass = Query("SELECT tbl_class_title,tbl_class_skill From tbl_class Where tbl_class_code = '$thisClass' and tbl_class_lng='$language_default' ");
    $readClass = mysqli_fetch_array($resultClass);

    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[470] . '  : ' . $readClass['tbl_class_title'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1681] . '  : <a href="parentcourse.php?class=' . $thisClass . '">' . $readCourse['tbl_courseparent_title'] . '</a></h5>
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
                                <a href="?do=add' . (!empty($getclass) ? '&class=' . $getclass : '') . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
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
                                <input type="hidden" class="form-control" value="<?= $getclass ?>" name="class">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>
                    <?php
                    if (!empty($getclass)) {
                        $thiscalss = " tbl_course_class = '$getclass' and ";
                    } else {
                        header("Location: class.php");
                        exit();
                    }

                    $result = Query("SELECT * From tbl_course where $thiscalss tbl_course_lng ='$language_default' and ( tbl_course_delete='0' or tbl_course_delete is null) and tbl_course_title like '%$keyword%'  ");
                    // start count db result for pagination               
                    $count_result = mysqli_num_rows($result);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    $result = Query("SELECT * From tbl_course where $thiscalss tbl_course_lng ='$language_default' and ( tbl_course_delete='0' or tbl_course_delete is null) and tbl_course_title like '%$keyword%' $orderby  LIMIT $start_contnet,$show_per_page ");
                    $row = mysqli_num_rows($result);
                    // 0 result search error 
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    $allfield = '';
                    echo '<form action="?do=runupdate" method="POST">
                                <input type="hidden" name="class" value="' . $getclass . '">
                                <input type="hidden" name="page" value="' . $page . '">
                                <div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th><a href="?orderby=order&class=' . $getclass . '">' . $lang[1336] . ' <span class="' . (!empty($getorderby) && $getorderby == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th><a href="?orderby=title&class=' . $getclass . '">' . $lang[1687] . ' <span class="' . (!empty($getorderby) && $getorderby == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th>' . $lang[1416] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;  // for record number
                    while ($read = mysqli_fetch_array($result)) {

                        $finalPrice = 0;
                        if (!empty($read['tbl_course_mohtava'])) {
                            $mohtava = explode(",", $read['tbl_course_mohtava']);
                            foreach ($mohtava as $key => $value) {
                                if (!empty($value)) {
                                    $result_mohtava = Query("select tbl_mohtava_sellprice from tbl_mohtava where tbl_mohtava_code = '$value' ");
                                    $read_mohtava = mysqli_fetch_array($result_mohtava);
                                    @$finalPrice += (!empty($read_mohtava['tbl_mohtava_sellprice']) ? $read_mohtava['tbl_mohtava_sellprice'] : '0');
                                }
                            }
                        }
                        $admin = $read['tbl_course_admin'];
                        $result_admin = Query("select * from tbl_user where tbl_user_id='$admin' ");
                        $read_admin = mysqli_fetch_array($result_admin);
                        $img = explode(",", $read['tbl_course_img']);
                        $result_register = Query("select * from tbl_register where tbl_register_view !='1' and tbl_register_class='$read[tbl_course_code]'");
                        // parent menu code
                        echo '<tr><td class="align-middle p-2">' . $i . '</td>
                                    <td class="align-middle p-2"><input type="hidden" name="code[]" value="' . $read['tbl_course_code'] . '"><input type="text" name="o' . $read['tbl_course_code'] . '" value="' . $read['tbl_course_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>'
                            . '<td class="align-middle p-2"><span style="' . ($read['tbl_course_status'] == 0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_course_title'] . '' . ($read['tbl_course_free'] == 1 ? $lang[1364] : '') . '</span></td>'
                            . '<td class="align-middle p-2">' . (!empty($finalPrice) ? toPersianNum(number_format($finalPrice)) : toPersianNum('0')) . '</td>
                                    <td class="align-middle p-2">
                                    <a href="?do=edit&code=' . $read['tbl_course_code'] . '&class=' . $getclass . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&code=' . $read['tbl_course_code'] . '&class=' . $getclass . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                    </td>'
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
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . (!empty($getclass) ? '&class=' . $getclass : '') . (!empty($getorderby) ? '&orderby=' . $getorderby : '') . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . (!empty($getclass) ? '&class=' . $getclass : '') . (!empty($getorderby) ? '&orderby=' . $getorderby : '') . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                        </li>

                                    </ul>
                                </td>
                                <td class="col-lg-3">
                                    <form action="course.php?do=gotopage" class="search-form" method="POST">
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
                    // function findFile($dir, $filename) {
                    //     $result = [];
                    //     $dirs = new RecursiveDirectoryIterator($dir);
                    //     $iterator = new RecursiveIteratorIterator($dirs);

                    //     foreach ($iterator as $file) {
                    //         if ($file->getFilename() == $filename) {
                    //             $result[] = $file->getPathname();
                    //         }
                    //     }

                    //     return $result;
                    // }
                    // // Example usage:
                    // $dir = '../upload';
                    // $filename = 'img_1682371910_1.mp4';

                    // $paths = findFile($dir, $filename);

                    // foreach ($paths as $path) {
                    //     $path = str_replace("\\", "/", $path);
                    //     //echo $path . "\n";
                    // }

                    ?>

                    <div class="col-lg-12 col-12 p-3">
                        <form action="course.php?do=additem" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="systemcode" value="<?= Code("", "no", 7) ?>">
                            <input type="hidden" name="class" value="<?= $getclass ?>">
                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                        <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="title_' . $language[$i] . '" class="form-control" ' . ($i == 1  ? 'required' : '') . '>
                                        </div>';
                            }
                            ?>
                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                        <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                        <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                        </div>';
                            }
                            ?>

                            <div class="form-group">
                                <label><?= $lang[1688] ?></label>
                                <select name="icon" class="form-control select2">
                                    <option value="film"><?= $lang[1689] ?></option>
                                    <option value="file-text2"><?= $lang[51] ?></option>
                                    <option value="book"><?= $lang[1690] ?></option>
                                    <option value="file"><?= $lang[52] ?></option>
                                    <option value="mic"><?= $lang[1691] ?></option>
                                </select>
                            </div>

                            <div class="form-group" style="display: none;">
                                <label><?= $lang[1423] ?></label><select name="teacher" class="form-control select2">
                                    <?php
                                    $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_teacher_id'] . '">' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group check-style">
                                <label><input type="checkbox" name="status" value="1"><?= $lang[1692] ?> </label>
                            </div>
                            <div class="form-group check-style">
                                <label><input type="checkbox" name="free" value="1"><?= $lang[1693] ?> </label>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="additem" value="<?= $lang[1974] ?>" class="btn btn-primary mt-3">
                            </div>
                        </form>
                    </div>
                <?php
                }

                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['additem'])) {
                        $class = $_POST['class'];
                        $systemcode = $_POST['systemcode'];
                        $classcode = (!empty($_POST['classcode']) ? $_POST['classcode'] : '');
                        $icon = $_POST['icon'];
                        $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                        $free = (!empty($_POST['free']) ? $_POST['free'] : '0');
                        $teacher = $_POST['teacher'];


                        $images_name = '';
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if (!empty($img_[$i_image])) {
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                    if ($extn == 'PPTX' || $extn == 'pptx' || $extn == 'mp3' || $extn == 'MP3' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
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

                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                            if (empty($title_[1])) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: course.php?class=$class");
                                exit();
                            }
                            $language_name = $language[$i];
                            $date = jdate("Y/m/d : A:h:s");
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
                                    tbl_course_cat,
                                    tbl_course_lng,
                                    tbl_course_delete) 
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
                                        '0',
                                        '$language_name',
                                        '0')");
                        }
                        $_SESSION['error'] = $lang[1976];
                        header("Location: course.php?do=selectcontent&code=$systemcode&class=$class");
                    }
                }
                // end = add item
                if (isset($do) && $do == 'selectcontent') {

                ?>
                    <div class="col-lg-12 col-12 p-3">
                        <?php
                        echo '<div class="form-group">
                        <label>' . $lang[1977] . '</label>
                        <input type="text" name="title" class="form-control" value="">
                        </div>';

                        echo '<div class="form-group">
                        <label>' . $lang[1832] . '</label><div>
                        <select name="ghaleb"  data-placeholder="' . $lang[1832] . '" style="width:100%;" class="select2" >
                        <option value="">' . $lang[70] . '</option>';
                        for ($i = 1857; $i <= 1873; $i++) {
                            $thiscode = explode("-", $lang[$i]);
                            echo '<option value="' . $i . '-' . str_replace("کد", "", $thiscode[1]) . '" class="group-cat" >' . $lang[$i] . '</option>';
                        }

                        echo '</select></div></div>';

                        echo '<div class="form-group">
                            <label>' . $lang[1838] . '</label><div>
                            <select name="department"  data-placeholder="' . $lang[1838] . '" style="width:100%;" class="select2" >
                            <option value="">' . $lang[70] . '</option>';
                        $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                        while ($readKey = mysqli_fetch_array($resultKey)) {
                            echo '<option value="' . $readKey['tbl_categories_code'] . '-' . $readKey['tbl_categories_catcode'] . '" class="group-cat" >' . $readKey['tbl_categories_title'] . '</option>';
                        }
                        echo '</select></div></div>';

                        echo '<div class="form-group">
                          <label>' . $lang[1948] . '</label><div>
                          <select name="creator"  data-placeholder="' . $lang[1948] . '" style="width:100%;" class="select2" >
                          <option value="0">' . $lang[70] . '</option>';
                        $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
                        while ($readKey = mysqli_fetch_array($resultKey)) {
                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" >' . $readKey['tbl_user_name'] . '</option>';
                        }
                        echo '</select></div></div>';
                        echo '<div class="form-group">
                        <label>' . $lang[1951] . '</label><div>
                        <select name="producer"  data-placeholder="' . $lang[1951] . '" style="width:100%;" class="select2" >
                        <option value="0">' . $lang[70] . '</option>';
                        $resultKey = Query("SELECT * From tbl_user Where tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
                        while ($readKey = mysqli_fetch_array($resultKey)) {
                            echo '<option value="' . $readKey['tbl_user_id'] . '" class="group-cat" >' . $readKey['tbl_user_name'] . '</option>';
                        }
                        echo '</select></div></div>';
                        ?>
                        <form action="course.php?do=selectcontentupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="systemcode" value="<?= $code ?>">
                            <input type="hidden" name="class" value="<?= $getclass ?>">
                            <div class="form-group"><input type="submit" name="search" value="<?= $lang[77] ?>" class="btn btn-success selectcontent"></div>
                            <div class="result width-full">
                                <?php

                                $resultCourse = Query("SELECT * FROM tbl_course Where 
                                tbl_course_lng='$language_default' and 
                                tbl_course_delete='0' and  
                                tbl_course_code='$code' and 
                                tbl_course_class='$getclass' and tbl_course_mohtava !=''  ");
                                $readCourse = mysqli_fetch_array($resultCourse);
                                $mohtava = (!empty($readCourse['tbl_course_mohtava']) ? $readCourse['tbl_course_mohtava'] : '');
                                if (mysqli_num_rows($result) > 0 && !empty($mohtava)) {


                                    $result = Query("SELECT * FROM tbl_mohtava Where 
                                    tbl_mohtava_lng='$language_default' and tbl_mohtava_statustext ='4' and 
                                    tbl_mohtava_delete='0' and tbl_mohtava_code='$mohtava'  ");
                                    $read = mysqli_fetch_array($result);
                                    echo '<div class="table-responsive scrollbar width-full">
                                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                        <thead class="text-800 bg-light">
                                                <tr>
                                                <th>' . $lang[1978] . '</th>
                                                <th>' . $lang[53] . '</th>
                                                <th>' . $lang[1831] . '</th>
                                                <th>' . $lang[1334] . '</th>
                                                <th>' . $lang[1844] . '</th>
                                                <th>' . $lang[1842] . '</th>
                                            </tr>
                                        </thead>
                                        <tbody>';
                                    $resultEn = Query("SELECT tbl_mohtava_title From tbl_mohtava Where  tbl_mohtava_lng='en' and tbl_mohtava_code='$mohtava'");
                                    $readEn = mysqli_fetch_array($resultEn);
                                    if (!empty($read['tbl_mohtava_file'])) {
                                        $file = explode(",", $read['tbl_mohtava_file']);
                                    }
                                    $file = '';

                                    echo '<tr>
                                            <td class="align-middle p-2"><input type="radio" name="select" value="' . $read['tbl_mohtava_code'] . '"  required checked></td>
                                            <td class="align-middle p-2">
                                            <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                            <img class="rounded-circle" src="' . (!empty($file[1]) ? '../upload/course/' . $file[1] : 'assets/img/no-image.jpg') . '" >
                                            </div>
                                            </td>
                                            <td class="align-middle p-2">' . strtoupper($read['tbl_mohtava_codefile']) . '</td>
                                            <td class="align-middle p-2">' . $read['tbl_mohtava_title'] . '<br>' . @$readEn['tbl_mohtava_title'] . '' . (!empty($read['tbl_mohtava_ghaleb']) ? '<br>' . $lang[1832] . ' : ' . $lang[$read['tbl_mohtava_ghaleb']] : '') . '</td>
                                    
                                            <td class="align-middle p-2">' . $read['tbl_mohtava_dateadd'] . '</td>
                                            <td class="align-middle p-2">' . $read['tbl_mohtava_enteshar'] . '</td>
                                            </tr>';

                                    echo '</tbody></table></div>';
                                }
                                ?>
                            </div>
                            <hr>
                            <div class="form-group">
                                <input type="submit" name="additem" value="<?= $lang[1975] ?>" class="btn btn-primary mt-3">
                            </div>
                        </form>
                    </div>
                <?php
                }
                if (isset($do) && $do == 'selectcontentupdate') {
                    $systemcode = $_POST['systemcode'];
                    $class = $_POST['class'];
                    $select = (!empty($_POST['select']) ? $_POST['select'] : '');
                    Query("Update tbl_course SET
                        tbl_course_mohtava='$select'
                        where  tbl_course_code='$systemcode' and tbl_course_class='$class' ");

                    $_SESSION['error'] = $lang[21];
                    header("Location: course.php?class=$class");
                }

                if (isset($do) && $do == 'delete') {
                    $result = Query("SELECT * from tbl_course Where tbl_course_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_course_title'] . '</h3>';
                    echo '<form action="#" method="POST">
                        <input type="hidden" name="code" value="' . $code . '" >
                        <input type="hidden" value="' . $getclass . '" name="class" >
                        <div class="form-group margin-top">
                        <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                        <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3">
                        </form></div>';

                    if (isset($_POST['submit'])) {
                        $code = $_POST['code'];
                        $class = $_POST['class'];
                        Query("update tbl_course set tbl_course_delete='1' Where tbl_course_code = '$code'  ");
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
                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        global $code;
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_course_lng = '$language_id'";
                        }
                        $result = Query("SELECT * FROM tbl_course Where tbl_course_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }

                    echo '<div class="col-lg-12 col-12 p-3">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="id" value="' . $code . '" >
                  <input type="hidden" name="class" value="' . read_fild(NULL, 'tbl_course_class') . '" >
                  <input type="hidden" name="code" value="' . read_fild(NULL, 'tbl_course_code') . '" >';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_course_title') . '" ' . ($i == 1  ? 'required' : '') . '>
                                </div>';
                    }
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection(read_fild($language[$i], 'tbl_course_content')) . '</textarea>
                              </div>';
                    }

                    echo '<div class="form-group">
                            <label>' . $lang[1688] . '</label>
                            <select name="icon" class="form-control select2">
                                <option value="film" ' . (read_fild(NULL, 'tbl_course_icon') == 'film' ? 'selected' : '') . '>' . $lang[1689] . '</option>
                                        <option value="file-text2" ' . (read_fild(NULL, 'tbl_course_icon') == 'file-text2' ? 'selected' : '') . '>' . $lang[51] . '</option>
                                        <option value="book" ' . (read_fild(NULL, 'tbl_course_icon') == 'text' ? 'selected' : '') . '>' . $lang[1690] . '</option>
                                        <option value="file" ' . (read_fild(NULL, 'tbl_course_icon') == 'file' ? 'selected' : '') . '>' . $lang[52] . '</option>
                                        <option value="mic" ' . (read_fild(NULL, 'tbl_course_icon') == 'mic' ? 'selected' : '') . '>' . $lang[1691] . '</option>

                            </select>
                        </div>';

                    echo '<div class="form-group" style="display: none;">
                        <label>' . $lang[1423] . '</label><select name="teacher" class="form-control select2" >';
                    $result_access = Query("Select * from tbl_teacher order by tbl_teacher_id DESC ");
                    while ($read_access = mysqli_fetch_array($result_access)) {
                        echo '<option value="' . $read_access['tbl_teacher_id'] . '"  ' . ($read_access['tbl_teacher_id'] == read_fild(NULL, 'tbl_course_teacher') ? 'selected' : '') . '>' . $read_access['tbl_teacher_fname'] . ' ' . $read_access['tbl_teacher_lname'] . '</option>';
                    }

                    echo '</select></div><hr>
                            <div class="form-group check-style">
                            <label><input type="checkbox" name="status" value="1" ' . (read_fild(NULL, 'tbl_course_status') == 1 ? 'checked' : '') . '>' . $lang[1692] . ' </label>
                            </div>
                            <div class="form-group check-style">
                            <label><input type="checkbox" name="free" value="1" ' . (read_fild(NULL, 'tbl_course_free') == 1 ? 'checked' : '') . '>' . $lang[1693] . ' </label>
                            </div><div class="form-group">
                            <input type="submit" name="update" value="' . $lang[1974] . '" class="btn btn-primary mt-3">
                            </div></form></div>';
                }
                if (isset($do) && $do == 'edititem') {
                    if (isset($_POST['update'])) {
                        $code = $_POST['code'];
                        $class = $_POST['class'];
                        $id = $_POST['id'];
                        $icon = $_POST['icon'];
                        $teacher = $_POST['teacher'];
                        $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                        $free = (!empty($_POST['free']) ? $_POST['free'] : '0');

                        $images_name = '';
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
                                        if ($extn == 'PPTX' || $extn == 'pptx' || $extn == 'mp3' || $extn == 'MP3' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP') {
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
                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                            if (empty($title_[1])) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: course.php?class=$class");
                                exit();
                            }
                            $language_name = $language[$i];
                            Query("Update tbl_register SET  tbl_register_admin ='$admin' where  tbl_register_class='$code' ");

                            Query("Update tbl_course SET
                                        tbl_course_free='$free',
                                        tbl_course_content='$content_[$i]',
                                        tbl_course_title='$title_[$i]', 
                                        tbl_course_icon='$icon', 
                                        tbl_course_teacher='$teacher', 
                                        tbl_course_img='$images_name', 
                                        tbl_course_status='$status'
                                        where  tbl_course_lng='$language_name' and tbl_course_code='$id'");
                        }
                        $_SESSION['error'] = $lang[1976];
                        header("Location: course.php?do=selectcontent&code=$code&class=$class");
                    }
                }
                if (isset($do) && $do == 'deleteimage') {
                    $result = Query("SELECT * from tbl_course Where tbl_course_id = '$id' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 col-12 p-3"><h3>' . $lang[25] . '</h3><img style="width:35%; height:35%;" src="../upload/course/'  . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="hidden" value="' . $getclass . '" name="class" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
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