<?php

include './includer.php';

$show_per_page = '10';

if (isset($t) && $t == '1') {
    $pagetitle = $lang[41];
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,3';
    $img_item = '30';
}
if (isset($t) && $t == '2') {
    $pagetitle = $lang[503];
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,3';
    $img_item = '30';
}
if (isset($t) && $t == '3') {
    $pagetitle = $lang[1208];
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,2,3';
    $img_item = '30';
}
if (isset($t) && $t == '4') {
    $pagetitle = $lang[1202];
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,2,3';
    $img_item = '1';
}
if (isset($t) && $t == '5') {
    $pagetitle = 'سوالات متداول';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,2,3';
    $img_item = '1';
}
if (isset($t) && $t == '20') {
    $pagetitle = 'مقالات اساتید';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $thisaccess = '1,2,3';
    $img_item = '1';
}


include './header.inc.php';

if (isset($_GET['section'])) {
    $section = " tbl_content_class='$_GET[section]' and ";
} else {
    $section = "  ";
}

if (isset($_GET['teacher'])) {
    $teacher = " tbl_content_teacher='$_GET[teacher]' and ";
} else {
    $teacher = "  ";
}


if ($access == 2) {
    $select = " tbl_content_admin='$this_user_id' and  ";
} else {
    $select = '';
}
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php

                    if (isset($t) && !isset($do)) {

                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9 padding-clear"><a class="btn btn-primary margin-left big_button" href="?t=' . $t . '&do=add' . (isset($_GET['teacher']) ? '&teacher=' . $_GET['teacher'] : '') . '' . (isset($_GET['section']) ? '&section=' . $_GET['section'] : '') . '">' . $lang[31] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="misc.php?t=' . $t . '&do=search" method="POST">
                       <input type="text" class="form-control" id="search_content_title" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';


                        $result_for_count = Query("SELECT * From tbl_content Where $teacher $select $section tbl_content_type = '$t' and tbl_content_fld1='$language_default' and tbl_content_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);

                        $result = Query("SELECT * From tbl_content Where $teacher $select $section tbl_content_type = '$t' and tbl_content_fld1='$language_default' and tbl_content_title LIKE '%$keyword%' ORDER BY tbl_content_fld5 DESC , tbl_content_id DESC LIMIT $start_contnet,$show_per_page ");

                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }

                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="t" value="' . $t . '"><input type="hidden" name="page" value="' . $page . '">
                        <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                    <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>ترتیب</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>' . $lang[55] . '</th>
                                    <th>کاربر</th>
                                    ' . ($t == 3 ? '<th>دوره</th>' : '') . '
                                   
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $result_class = Query("select * from tbl_class where tbl_class_code='$read[tbl_content_class]' ");
                            $read_class = mysqli_fetch_array($result_class);
                            echo '<tr>
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-1"><input type="hidden" name="code[]" value="' . $read['tbl_content_code'] . '"><input type="text" name="o' . $read['tbl_content_code'] . '" value="' . $read['tbl_content_fld5'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>
            <td class="col-sm-4"><a href="?t=' . $t . '&do=edit&code=' . $read['tbl_content_code'] . '" >' . $read['tbl_content_title'] . ($read['tbl_content_note'] == 1 ? ' [اطلاعیه]' : '') . '</a></td>
            <td class="col-sm-1">' . $read['tbl_content_date'] . '</td>
            <td class="col-sm-1">' . $read['tbl_content_op'] . '</td>
            ' . @($t == 3 ? '<td class="col-sm-2"><a href="class.php?do=edit&id=' . $read['tbl_content_class'] . '" target="_blank">' . $read_class['tbl_class_title'] . '</a></td>' : '') . '
            </tr><tr>
            <td class="col-sm-1 btn-class" colspan="6">
            <a href="?t=' . $t . '&do=edit&code=' . $read['tbl_content_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
            <a href="?t=' . $t . '&do=delete&code=' . $read['tbl_content_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
            </tr>';
                            $i++;
                        }

                        echo '</tbody></table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning update-btn" ></form>';

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
                                    <li><a class="page-link" href="<?= '?t=' . $t . '&page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (isset($_GET['section']) ? '&section=' . $_GET['section'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?t=' . $t . '&page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['section']) ? '&section=' . $_GET['section'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="misc.php?t=<?= $t ?>&do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                        $t = $_POST['t'];
                        $page = $_POST['page'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_content set tbl_content_fld5='$val' Where tbl_content_code = '$value' ");
                        }
                        header("Location: misc.php?t=$t&page=$page");
                    }
                    if (isset($t) && isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: misc.php?t=$t&page=$page_number");
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: misc.php?t=$t&keyword=$keyword_search");
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'add') {
                        echo '<div class="col-lg-8">
                          <form action="?t=' . $t . '&do=additem" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >
                           <input type="hidden" name="teacher" value="' . (!empty($_GET['teacher']) ? $_GET['teacher'] : '') . '" >
                           <input type="hidden" name="time" value="' . time() . '" class="form-control">
                           <input type="hidden" name="typeid" value="' . $t . '" >';
                        if ($cat_manager_id == '1') {
                            $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                            echo '<div class="form-group">
                              <label>' . $lang[68] . '</label><div>
                              <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="chzn-select" ><option value="0">' . $lang[70] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';

                                    $parentcode = $read_cat['tbl_categories_code'];
                                    $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                        echo '<option value="' . $read_sub['tbl_categories_code'] . '" class="group-sub">' . $read_sub['tbl_categories_title'] . '</option>';
                                        $subcode = $read_sub['tbl_categories_code'];
                                        $result_sub = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$subcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                        $row = mysqli_num_rows($result_sub);
                                        while ($read_sub_sub = mysqli_fetch_array($result_sub)) {
                                            echo '<option value="' . $read_sub_sub['tbl_categories_code'] . '" class="group-sub-sub">' . $read_sub_sub['tbl_categories_title'] . '</option>';
                                        }
                                    }
                                }
                            }
                            echo '</select></div></div><hr>';
                        }
                        if ($t == '3') {
                            if ($access == 2) {
                                $select = " where tbl_class_admin='$this_user_id' ";
                            } else {
                                $select = '';
                            }
                            $result = Query("SELECT * From tbl_class $select  ORDER BY tbl_class_id DESC ");
                            echo '<div class="form-group">
                            <label>' . $lang[1194] . '</label><div>
                            <select name="class_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="select2" ><option value="0">' . $lang[70] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                echo '<option ' . (isset($_GET['section']) && $_GET['section'] == $read_cat['tbl_class_code'] ? 'selected' : '') . ' value="' . $read_cat['tbl_class_code'] . '" class="group-cat">' . $read_cat['tbl_class_title'] . '</option>';
                            }
                            echo '</select></div></div><hr>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        if ($t == 4) {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                              <label>سمت</label>
                              <input type="text" name="semat" class="form-control">
                              </div>';
                            }
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>لید<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="lid_' . $language[$i] . '" class="form-control" rows="6"></textarea>
                              </div>';
                        }
                        if ($t != 4) {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                  <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                  <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                  </div>';
                            }
                        }
                        for ($i = 1; $i <= $img_item; $i++) {
                            echo '<div class="form-group"><table><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                                  <td><input type="file" name="img_' . $i . '"></td>
                                  </tr></table></div>';
                        }
                        echo '<hr>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group"><table><tr>
                                  <td><label class="col-sm-2 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                  <td><input type="file" name="file_attach_' . $language[$i] . '"></td>
                                  </tr></table></div>';
                        }
                        if (isset($t) && $t == 1) {
                            echo '<div class="form-group">
                              <label>رده بندی</label>
                              <input type="text" name="rank"  class="form-control">
                              </div>';
                        }
                        if (isset($t) && $t == 3) {
                            echo '<div class="form-group">
                              <label>اطلاعیه 
                              <input type="checkbox" name="note" value="1"></label>
                              </div>';
                        }
                        echo '<hr><div class="form-group">
                              <label>' . $lang[55] . '</label>
                              <input type="text" name="date" value="' . jdate('Y/n/j') . '" class="form-control">
                              </div>';

                        echo '<div class="form-group">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                             </div>
                </form></div>';
                    }
                    if (isset($t) && isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $teacher = $_POST['teacher'];
                            $semat = $_POST['semat'];
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $hot = $_POST['hot'];
                            $time = $_POST['time'];
                            $rank = $_POST['rank'];
                            $class_id = $_POST['class_id'];
                            $note = $_POST['note'];
                            if ($cat_manager_id == '1') {
                                $cat_id = $_POST['cat_id'];
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = imjection($_POST['title_' . $language[$i]]);
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                $lid_[$i] = imjection($_POST['lid_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }
                                if (!$images_name) {
                                    for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                        $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;

                                        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                        } elseif (!empty($exp[$i_image])) {
                                            $images_name .= $exp[$i_image] . ',';
                                        } else {
                                            $images_name .= ',';
                                        }
                                    }
                                }

                                if ($_FILES['file_attach_' . $language[$i]]['name']) {
                                    $file_attach_[$language[$i]] = $_FILES['file_attach_' . $language[$i]]['name'];
                                    $read_file_name = explode(".", $file_attach_[$language[$i]]);
                                    $extn = end($read_file_name);
                                    if ($extn == 'pdf' || $extn == 'PDF' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'doc' || $extn == 'DOC' || $extn == 'docx' || $extn == 'DOCX') {
                                        $file_name_[$i] = 'file_' . time() . '_' . $language[$i] . '.' . $extn;
                                        move_uploaded_file($_FILES['file_attach_' . $language[$i]]["tmp_name"], "$directory" . $file_name_[$i]);
                                    }
                                } else {
                                    $file_name_[$i] = '';
                                }
                                $language_name = $language[$i];
                                $date = $_POST['date'];
                                $op = $_SESSION['username_name'];
                                $admin = $_SESSION['id_login'];
                                Query("INSERT INTO tbl_content (
                tbl_content_type,
                tbl_content_code,
                tbl_content_title,
                tbl_content_text,
                tbl_content_img,
                tbL_content_file,
                tbl_content_fld1,
                tbl_content_catid,
                tbl_content_date,
                tbl_content_lid,
                tbl_content_fld4,
                tbl_content_fld5,
                tbl_content_op,
                tbl_content_class,
                tbl_content_note,
                tbl_content_admin,
                tbl_content_teacher,
                tbl_content_semat) 
                values('$tid',
                '$code',
                '$title_[$i]',
                '$content_[$i]',
                '$images_name',
                '$file_name_[$i]',
                '$language_name',
                '$cat_id',
                '$date',
                '$lid_[$i]',
                '$time',
                '$rank',
                '$op',
                '$class_id',
                '$note',
                '$admin',
                '$teacher',
                '$semat')");

                                $_SESSION['error'] = $lang[32];
                                header("Location: misc.php?t=$t");
                            }
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'delete') {
                        if ($access == 2) {
                            $select = " tbl_content_admin='$this_user_id' and ";
                        } else {
                            $select = '';
                        }

                        $result = Query("SELECT * from tbl_content  Where $select tbl_content_code = '$code' and tbl_content_fld1 = '$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-8">' . $lang[25] . '<br /><h2>' . $read['tbl_content_title'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $read['tbl_content_img'] . '" >';

                        $files_name = '';
                        for ($i = 1; $i <= $language_count; $i++) {

                            $result_file = Query("SELECT * from tbl_content  Where tbl_content_code = '$code' and tbl_content_fld1 = '$language[$i]' ");
                            $read_file = mysqli_fetch_array($result_file);
                            $files_name .= $read_file['tbl_content_file'] . ',';
                        }

                        echo  ' <input type="hidden" name="file" value="' . $files_name . '" >
                            <input type="hidden" value="' . $code . '" name="code" >
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $file = $_POST['file'];
                            unset($i);
                            $exp_img = explode(",", $img);
                            for ($i = 0; $i <= $img_item; $i++) {
                                if ($exp_img[$i]) {
                                    unlink("$directory" . $exp_img[$i]);
                                }
                            }
                            unset($i);
                            $exp_file = explode(",", $file);
                            for ($i = 0; $i <= $language_count; $i++) {
                                if ($exp_file[$i]) {
                                    unlink("$directory" . $exp_file[$i]);
                                }
                            }
                            Query("DELETE from tbl_content Where tbl_content_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: misc.php?t=$t");
                            exit();
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: misc.php?t=$t");
                            exit();
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'edit') {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_content_fld1 = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_content Where tbl_content_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }

                        echo '<div class="col-lg-8">
                          <form action="?t=' . $t . '&do=edititem" method="POST" enctype="multipart/form-data" >
                          <input type="hidden" name="time" value="' . time() . '" class="form-control">
                          <input type="hidden" name="code" value="' . $code . '" >';

                        $current_cat_post = read_fild(NULL, 'tbl_content_catid');
                        if ($cat_manager_id == '1') {
                            $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                            echo '<div class="form-group">
                              <label>' . $lang[68] . '</label>
                              <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="chzn-select" ><option value="0">' . $lang[70] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                if ($current_cat_post == $read_cat['tbl_categories_code']) {
                                    $style = 'selected';
                                } else {
                                    $style = '';
                                }

                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option ' . $style . ' value="' . $read_cat['tbl_categories_code'] . '" class="group-cat" >' . $read_cat['tbl_categories_title'] . '</option>';

                                    $parentcode = $read_cat['tbl_categories_code'];
                                    $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                        if ($current_cat_post == $read_sub['tbl_categories_code']) {
                                            $style = 'selected';
                                        } else {
                                            $style = '';
                                        }
                                        echo '<option ' . $style . ' value="' . $read_sub['tbl_categories_code'] . '" class="group-sub" >' . $read_sub['tbl_categories_title'] . '</option>';

                                        $subcode = $read_sub['tbl_categories_code'];
                                        $result_sub = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$subcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                        $row = mysqli_num_rows($result_sub);
                                        while ($read_sub_sub = mysqli_fetch_array($result_sub)) {
                                            if ($current_cat_post == $read_sub_sub['tbl_categories_code']) {
                                                $style = 'selected';
                                            } else {
                                                $style = '';
                                            }
                                            echo '<option ' . $style . ' value="' . $read_sub_sub['tbl_categories_code'] . '" class="group-sub-sub" >' . $read_sub_sub['tbl_categories_title'] . '</option>';
                                        }
                                    }
                                }
                            }
                            echo '</select></div><hr>';
                        }

                        if ($t == '3') {
                            if ($access == 2) {
                                $select = " where tbl_class_admin='$this_user_id' ";
                            } else {
                                $select = '';
                            }
                            $result = Query("SELECT * From tbl_class $select  ORDER BY tbl_class_id DESC ");
                            echo '<div class="form-group">
                            <label>' . $lang[1194] . '</label><div>
                            <select name="class_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="select2" ><option value="0">' . $lang[70] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                echo '<option ' . (read_fild(NULL, 'tbl_content_class') == $read_cat['tbl_class_code'] ? 'selected' : '') . ' value="' . $read_cat['tbl_class_code'] . '" class="group-cat">' . $read_cat['tbl_class_title'] . '</option>';
                            }
                            echo '</select></div></div><hr>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_content_title')) . '" class="form-control">
                              </div>';
                        }
                        if ($t == 4) {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                              <label>سمت</label>
                              <input type="text" name="semat" class="form-control" value="' . read_fild(NULL, 'tbl_content_semat') . '">
                              </div>';
                            }
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>لید<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="lid_' . $language[$i] . '" class="form-control" rows="6">' . exjection(read_fild($language[$i], 'tbl_content_lid')) . '</textarea>
                              </div>';
                        }
                        if ($t != 4) {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection(read_fild($language[$i], 'tbl_content_text')) . '</textarea>
                              </div>';
                            }
                        }
                        echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_content_img') . '">';
                        for ($i = 1; $i <= $img_item; $i++) {

                            $exp = explode(",", read_fild(NULL, 'tbl_content_img'));
                            if ($exp[$i - 1]) {
                                echo '<div class="form-group"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '"></td><td><a href="../upload/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a><a href="?t=' . $t . '&code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a>
                                      </td></tr></table></div>';
                            } else {
                                echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '">
                                      </td></tr></table></div>';
                            }
                        }
                        echo '<hr>';

                        for ($i = 1; $i <= $language_count; $i++) {
                            if (read_fild($language[$i], 'tbl_content_file')) {
                                echo '<input type="hidden" name="old_file_' . $language[$i] . '" value="' . read_fild($language[$i], 'tbl_content_file') . '">
                                      <div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                      <td><input type="file" name="file_attach_' . $language[$i] . '"></td><td><a href="../upload/' . read_fild($language[$i], 'tbl_content_file') . '"  target="_blank"><span class="btn btn-xs btn-success"><span class="bx bxs-trash"></span></span></a><a href="?t=' . $t . '&code=' . read_fild(NULL, 'tbl_content_code') . '&do=deletefile&file=' . read_fild($language[$i], 'tbl_content_file') . '"  target="_blank"><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a>
                                      </td></tr></table></div>';
                            } else {
                                echo '<input type="hidden" name="old_file" value="' . read_fild($language[$i], 'tbl_content_file') . '">
                                    <div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                      <td><input type="file" name="file_attach_' . $language[$i] . '">
                                      </td></tr></table></div>';
                            }
                        }
                        @$hot_value = read_fild(NULL, 'tbl_content_fld5');
                        if ($hot_value == '1') {
                            $style_hot = 'checked';
                        } else {
                            $style_hot = '';
                        }

                        if (isset($t) && $t == 1) {
                            echo '<div class="form-group">
                              <label>رده بندی</label>
                              <input type="text" name="rank"  class="form-control" value="' . read_fild(NULL, 'tbl_content_fld5') . '">
                              </div>';
                        }
                        if (isset($t) && $t == 3) {
                            echo '<div class="form-group">
                              <label>اطلاعیه 
                              <input type="checkbox" name="note" value="1" ' . (read_fild(NULL, 'tbl_content_note') == 1 ? 'checked' : '') . '></label>
                              </div>';
                        }
                        echo '<hr><div class="form-group">
                                  <label>' . $lang[55] . '</label>
                                  <input type="text" name="date" value="' . read_fild(NULL, 'tbl_content_date') . '" class="form-control">
                                  </div>';

                        echo  '<div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                                  </div>
                                  </form></div>';
                    }

                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $semat = $_POST['semat'];
                            $code = $_POST['code'];
                            $cat_id = $_POST['cat_id'];
                            $rank = $_POST['rank'];
                            $time = $_POST['time'];
                            $class_id = $_POST['class_id'];
                            $note = $_POST['note'];
                            $allimages = $_POST['allimages'];
                            $read_allimages = explode(",", $allimages);
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = imjection($_POST['title_' . $language[$i]]);
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                $lid_[$i] = imjection($_POST['lid_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }
                                if (!$images_name) {
                                    for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                        $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                        if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                            $images_name .= $read_allimages[$i_image - 1] . ',';
                                        } else {
                                            $read_file_name = explode(".", $img_[$i_image]);
                                            $extn = end($read_file_name);
                                            $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                            if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                                $images_name .= $img_[$i_image] . ',';
                                                move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                                unlink($directory . $read_allimages[$i_image - 1]);
                                            } else {
                                                $images_name .= $read_allimages[$i_image - 1] . ',';
                                            }
                                        }
                                    }
                                }

                                if (!$_FILES['file_attach_' . $language[$i]]['error'] > 0) {
                                    $file_attach_[$language[$i]] = $_FILES['file_attach_' . $language[$i]]['name'];
                                    $read_file_name = explode(".", $file_attach_[$language[$i]]);
                                    $extn = end($read_file_name);
                                    $file_name_[$i] = 'file_' . time() . '_' . $language[$i] . '.' . $extn;
                                    if ($extn == 'pdf' || $extn == 'PDF' || $extn == 'mp4' || $extn == 'MP4' || $extn == 'doc' || $extn == 'DOC' || $extn == 'docx' || $extn == 'DOCX') {
                                        move_uploaded_file($_FILES['file_attach_' . $language[$i]]["tmp_name"], "$directory" . $file_name_[$i]);
                                        $file_old_[$i] = $_POST['old_file_' . $language[$i]];
                                        unlink($directory . $file_old_[$i]);
                                    }
                                } else {
                                    echo $file_name_[$i] = $_POST['old_file_' . $language[$i]];
                                }
                                $language_record = $language[$i];
                                $date = $_POST['date'];

                                if ($access == 2) {
                                    $select = " and tbl_content_admin='$this_user_id'  ";
                                } else {
                                    $select = '';
                                }
                                Query("Update tbl_content SET 
            tbl_content_note='$note',
            tbl_content_class='$class_id',
            tbl_content_op='$op' ,
            tbl_content_title='$title_[$i]' ,
            tbl_content_text = '$content_[$i]' ,
            tbl_content_img = '$images_name' ,
            tbL_content_file = '$file_name_[$i]' ,
            tbl_content_date = '$date' ,
            tbl_content_catid = '$cat_id',
            tbl_content_lid = '$lid_[$i]',
            tbl_content_fld5 = '$rank',
            tbl_content_fld4 = '$time',
            tbl_content_semat = '$semat' 
            Where tbl_content_code= '$code' and tbl_content_fld1 = '$language_record' $select ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: misc.php?t=$t");
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'deleteimage') {
                        if ($access == 2) {
                            $select = " tbl_content_admin='$this_user_id' and  ";
                        } else {
                            $select = '';
                        }

                        $result = Query("SELECT * from tbl_content Where $select tbl_content_code = '$code' ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-8 col-lg-offset-6">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $code = $_POST['code'];
                            $record = str_replace("$img", "", $read['tbl_content_img']);
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_content SET tbl_content_img = '$record' Where tbl_content_code = '$code' ");
                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        } elseif (isset($_POST['nodelete'])) {

                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        }
                    }
                    if (isset($t) && isset($do) && $do == 'deletefile') {
                        if ($access == 2) {
                            $select = " tbl_content_admin='$this_user_id' and  ";
                        } else {
                            $select = '';
                        }

                        $result = Query("SELECT * from tbl_content  Where $select tbl_content_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        $which_file_lng = explode("_", $file);
                        $which_file_lng = end($which_file_lng);
                        $which_file_lng = explode(".", $which_file_lng);
                        $which_file_lng = reset($which_file_lng);

                        echo '<div class="col-lg-6 col-lg-offset-6">' . $lang[25] . '<br /><a href="' . $directory . $file . '" >' . $lang[58] . '</a><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="language" value="' . $which_file_lng . '" >
                         <input type="hidden" name="file" value="' . $file . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $file = $_POST['file'];
                            $code = $_POST['code'];
                            $lang = $_POST['language'];


                            $external_file = $directory . $file;
                            unlink("$external_file");
                            $record = '';
                            Query("Update tbl_content SET tbl_content_file = '$record' Where tbl_content_code = '$code' and tbl_content_fld1 = '$lang' ");
                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        } elseif (isset($_POST['nodelete'])) {

                            header("Location: misc.php?t=$t&code=$code&do=edit");
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