<?php

// include
include './includer.php';
$show_per_page = '30';

//type id 

if (isset($t) && $t == '1') {
    $pagetitle = $lang[66];
    $Tabpage = '11';
    $contentSection = '1';
    $label = $lang[67];
    $titleFa = 'عنوان فارسی';
    $titleEn = 'عنوان انگلیسی';
    $thisCodeTitle = $lang[1141];
}
if (isset($t) && $t == '2') {
    $pagetitle = $lang[66] . '&nbsp;' . $lang[149];
    $Tabpage = '24';
    $contentSection = '1';
    $label = $lang[67];
    $titleFa = 'عنوان فارسی';
    $titleEn = 'عنوان انگلیسی';
    $thisCodeTitle = $lang[1141];
}
if (isset($t) && $t == '3') {
    $pagetitle = $lang[1140];
    $Tabpage = '25';
    $thisaccess = '1';
    $contentSection = '1';
    $label = $lang[67];
    $titleFa = 'عنوان فارسی';
    $titleEn = 'عنوان انگلیسی';
    $thisCodeTitle = $lang[1141];
}
if (isset($t) && $t == '4') {
    $pagetitle = 'لیست مهارت ها';
    $Tabpage = '91';
    $thisaccess = '1';
    $contentSection = '1';
    $label = 'افزودن مهارت جدید';
    $titleFa = 'عنوان فارسی مهارت';
    $titleEn = 'عنوان انگلیسی مهارت';
    $thisCodeTitle = 'کد مهارت';
}
if (isset($t) && $t == '5') {
    $pagetitle = 'لیست بخش های آکادمی';
    $Tabpage = '91';
    $thisaccess = '1';
    $contentSection = '0';
    $label = 'افزودن بخش جدید';
    $titleFa = 'عنوان فارسی بخش';
    $titleEn = 'عنوان انگلیسی بخش';
    $thisCodeTitle = 'کد بخش';
}
if (isset($t) && $t == '6') {
    $pagetitle = 'تعریف حوزه کاربرد';
    $Tabpage = '91';
    $thisaccess = '1';
    $contentSection = '0';
    $label = 'افزودن کاربرد جدید';
    $titleFa = 'عنوان فارسی کاربرد';
    $titleEn = 'عنوان انگلیسی کاربرد';
    $thisCodeTitle = 'کد کاربرد';
}

include './header.inc.php';


// end include
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>

        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!empty($_GET['orderby'])) {
                        if ($_GET['orderby'] == 'order') {
                            $orderby = ' ORDER BY tbl_categories_order ASC ';
                        }
                        if ($_GET['orderby'] == 'title') {
                            $orderby = ' ORDER BY tbl_categories_title ASC ';
                        }
                    } else {
                        $orderby = ' ORDER BY tbl_categories_order ASC ';
                    }
                    if ($t == 4 && !empty($_GET['bakhsh'])) {
                        $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$_GET[bakhsh]' and tbl_categories_lng='$language_default'");
                        $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                        $thisQuery = " tbl_categories_bakhsh='$_GET[bakhsh]' and ";
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">بخش آکادمی : ' . $read_bakhsh['tbl_categories_title'] . '</h5></div>';
                    } else {
                        $thisQuery = '';
                    }

                    $result_count = Query("SELECT * From tbl_categories Where $thisQuery tbl_categories_type = '$t' and tbl_categories_parentid = '0' and tbl_categories_lng='$language_default' and tbl_categories_title LIKE '%$keyword%'  ");
                    $count_result = mysqli_num_rows($result_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);

                    echo '<section class="content"><div class="col-md-12">
                        <div class="box box-body table-responsive">';

                    if (isset($t) && !isset($do)) {
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr>
                       <td class="col-sm-9"><a class="btn btn-primary margin-left" href="?t=' . $t . '&do=add" >' . $label . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="category_manage.php?t=' . $t . '&do=search" method="POST">
                       <input type="text" class="form-control" value="' . $keyword . '" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';
                        $result = Query("SELECT * From tbl_categories Where $thisQuery tbl_categories_parentid = '0' and tbl_categories_type = '$t' and tbl_categories_lng='$language_default' and tbl_categories_title LIKE '%$keyword%' $orderby  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '<form action="?do=runupdate" method="POST"><input type="hidden" name="t" value="' . $t . '"><input type="hidden" name="page" value="' . $page . '">
                        <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th><a href="?t=' . $t . '&orderby=order">ترتیب <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>
                                    <th>' . $thisCodeTitle . '</th>
                                    ' . ($t == '4' ? '<th><a href="?t=' . $t . '&orderby=title"><span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span> مهارت</a></th><th> کاربرد</th><th> بخش</th>' : '<th><a href="?t=' . $t . '&orderby=title">' . $lang[50] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'title' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>') . '
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $code = $read['tbl_categories_code'];
                            $resultkill = Query("SELECT * From tbl_bankfile Where tbl_bankfile_skill='$code' and tbl_bankfile_lng='$language_default' ");

                            if ($t == '4') {
                                $bakhsh = $read['tbl_categories_bakhsh'];
                                $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
                                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                                $karbord = $read['tbl_categories_karbord'];
                                $result_karbord = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$karbord' and tbl_categories_lng='$language_default'");
                                $read_karbord = mysqli_fetch_array($result_karbord);

                                $result_skill = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_skill like '%$code%'");
                                $result_question = Query("SELECT * From tbl_question where tbl_question_lng='$language_default' and tbl_question_skill = '$code' ");
                            }
                            if ($t == '5') {
                                $result_bakhsh = Query("SELECT tbl_categories_title,tbl_categories_code From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$code' and tbl_categories_lng='$language_default'");
                                $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                            }
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1">
                                <input type="hidden" name="code[]" value="' . $read['tbl_categories_code'] . '">
                                <input type="text" name="o' . $read['tbl_categories_code'] . '" value="' . $read['tbl_categories_order'] . '" maxlength="5" style="width: 40px;text-align: center;"></td>
                                <td class="col-sm-1">' . strtoupper($read['tbl_categories_catcode']) . '</td>
                                ' . ($t == '4' ? '<td class="col-sm-1">' . $read['tbl_categories_title'] . '</td>
                                <td class="col-sm-1">' . $read_karbord['tbl_categories_title'] . '</td>
                                <td class="col-sm-1">' . $read_bakhsh['tbl_categories_title'] . '</td>
                                
                                ' : '<td class="col-sm-1">' . $read['tbl_categories_title'] . '</td>') . '
                                </tr><tr>
                                <td class="col-sm-1 btn-class" colspan="4">
                                <a href="?t=' . $t . '&code=' . $read['tbl_categories_code'] . '&do=edit" class="btn btn-xs btn-warning" >' . $lang[23] . '</a>
                                <a href="?t=' . $t . '&code=' . $read['tbl_categories_code'] . '&do=delete" class="btn btn-xs btn-danger" >' . $lang[24] . '</a>
                                ' . ($t == 3 ? '<a href="class.php?cat=' . $read['tbl_categories_id'] . '" class="btn btn-xs btn-success" target="_blank">لیست دوره ها (' . toPersianNum(mysqli_num_rows($result_skill)) . ')</a>' : '') . ''
                                . ($t == 4 ? '
                                <a href="bankfile.php?skill=' . $read['tbl_categories_code'] . '" class="btn btn-xs btn-success" > فایل (' . toPersianNum(mysqli_num_rows($resultkill)) . ')</a>
                                <a href="levelquestion.php?skill=' . $read['tbl_categories_code'] . '" class="btn btn-xs  btn-dark" >سوالات  (' . toPersianNum(mysqli_num_rows($result_question)) . ')</a>
                                <a href="class.php?skill=' . $code . '"  class="btn btn-xs btn-secondary" target="_blank">لیست دوره ها (' . toPersianNum(mysqli_num_rows($result_skill)) . ')</a>
                                ' : '') . '
                                ' . ($t == '5' ? (mysqli_num_rows($result_bakhsh) > 0 ? '<a href="category_manage.php?t=4&bakhsh=' . $code . '" class="btn btn-xs  btn-dark">مهارت ها (' . mysqli_num_rows($result_bakhsh) . ')</a>' : '') : '') . '</td>';
                            echo '</tr>';
                            $i++;
                        }
                        echo '</tr></table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning update-btn" ></form>';

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
                    ?>
                    <table class="table">
                        <tr>
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= '?t=' . $t . '&page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?t=' . $t . '&page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['orderby']) ? '&orderby=' . $_GET['orderby'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="category_manage.php?t=<?= $t ?>&do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            Query("update tbl_categories set tbl_categories_order='$val' Where tbl_categories_code = '$value' ");
                        }
                        header("Location: category_manage.php?t=$t&page=$page");
                    }
                    if (isset($t) && isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: category_manage.php?t=$t&page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = add item
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
                          <form action="category_manage.php?t=' . $t . '&do=additem" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >
                           <input type="hidden" name="typeid" value="' . $t . '" ><input type="hidden" name="cat_id" value="0" >';
                        if ($t == 1) {
                            echo '<div class="form-group">
                           <label>' . $lang[68] . '</label><div>
                           <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">' . $lang[69] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '__</option>';
                                    $parentcode = $read_cat['tbl_categories_code'];
                                    $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language[1]' ORDER BY tbl_categories_id DESC ");
                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                        echo '<option value="' . $read_sub['tbl_categories_code'] . '" class="group-sub">' . $read_sub['tbl_categories_title'] . '________</option>';
                                    }
                                }
                            }
                            echo '</select></div></div><hr>';
                        }

                        if ($t == 4) {
                            $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_order DESC,tbl_categories_id DESC ");
                            echo '<div class="form-group">
                           <label>انتخاب بخش</label><div>
                           <select name="bakhsh_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">انتخاب بخش</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                }
                            }
                            echo '</select></div></div><hr>';

                            $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '6' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_order DESC,tbl_categories_id DESC ");
                            echo '<div class="form-group">
                           <label>انتخاب حوزه کاربرد</label><div>
                           <select name="karbord_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">انتخاب کاربرد</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                }
                            }
                            echo '</select></div></div><hr>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . ($i == '1' ? $titleFa : $titleEn) . '</label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        if ($contentSection == '1') {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                  <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                  <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                  </div>';
                            }
                        }
                        if ($t != '6') {
                            echo '<div class="form-group">
                              <label>' . $thisCodeTitle . '</label>
                              <input type="text" name="cat_code" class="form-control" required>
                              </div>';
                        }
                        echo   '<div class="form-group">
                              <label>' . $lang[54] . '</label>
                              <input type="text" name="order" class="form-control">
                              </div>';
                        if ($t == 1 || $t == 3 || $t == 4) {
                            echo '<hr><div class="form-group"><table><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                  <td><input type="file" name="img"></td>
                                  </tr></table></div>';
                        }
                        echo '<div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                            </div></form></div>';
                    }


                    if (isset($t) && isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $cat_id = $_POST['cat_id'];
                            @$bakhsh_id = $_POST['bakhsh_id'];
                            @$karbord_id = $_POST['karbord_id'];
                            $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                            $cat_code = strtolower($_POST['cat_code']);
                            if ($tid == 3) {
                                $result = Query("SELECT * From tbl_categories Where tbl_categories_type='3' and tbl_categories_catcode ='$cat_code'");
                                if ($row = mysqli_num_rows($result) > 0) {
                                    $_SESSION['error'] = 'کد دوره واره شده قبلا استفاده شده است';
                                    header("Location: category_manage.php?t=$tid");
                                    exit();
                                }
                            }
                            if ($_FILES['img']['name']) {
                                $img = $_FILES['img']['name'];
                                $read_file_name = explode(".", $img);
                                $extn = end($read_file_name);
                                $img = 'cat_' . time() . '.' . $extn;
                                if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img);
                                }
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = strip_tags($_POST['title_' . $language[$i]]);
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: category_manage.php?t=$t");
                                    exit();
                                }
                                $language_name = $language[$i];
                                Query("INSERT INTO tbl_categories (
                                    tbl_categories_type,
                                    tbl_categories_code,
                                    tbl_categories_title,
                                    tbl_categories_parentid,
                                    tbl_categories_img,
                                    tbl_categories_lng,
                                    tbl_categories_order,
                                    tbl_categories_catcode,
                                    tbl_categories_text,
                                    tbl_categories_bakhsh,
                                    tbl_categories_karbord) 
                                    values(
                                        '$tid',
                                        '$code',
                                        '$title_[$i]',
                                        '$cat_id',
                                        '$img',
                                        '$language_name',
                                        '$order',
                                        '$cat_code',
                                        '$content_[$i]',
                                        '$bakhsh_id',
                                        '$karbord_id')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: category_manage.php?t=$t");
                            }
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_categories Where tbl_categories_code = '$code' and tbl_categories_lng='$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><br />' . $read['tbl_categories_title'];
                        echo '<form action="#" method="POST">
                <input type="hidden" name="code" value="' . $code . '" >
                <input type="hidden" name="img" value="' . $read['tbl_categories_img'] . '" >
                <div class="form-group margin-top">
                <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success">
                <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger">
                </form></div>';

                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $img = $_POST['img'];

                            // $result = Query("SELECT * from tbl_categories Where tbl_categories_parentid = '$code' and tbl_categories_lng='$language_default' ");
                            // $row_parent = mysqli_num_rows($result);
                            // $read = mysqli_fetch_array($result);
                            // if ($row_parent > 0) {
                            //     $_SESSION['error'] = $lang[71];
                            //     header("Location: category_manage.php?t=$t");
                            //     exit();
                            // }
                            if (isset($img)) {
                                unlink($directory . $img);
                            }
                            Query("Delete From tbl_categories Where tbl_categories_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: category_manage.php?t=$t");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: category_manage.php?t=$t");
                        }
                    }

                    if (isset($t) && isset($do) && $do == 'edit') {
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_categories_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_categories Where tbl_categories_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        $result_style = Query("SELECT * FROM tbl_categories Where tbl_categories_code =  '$code' ");
                        $result_style_read = mysqli_fetch_array($result_style);
                        $parent_id_style = $result_style_read['tbl_categories_parentid'];
                        $parent_id_bakhsh = $result_style_read['tbl_categories_bakhsh'];
                        $parent_id_karbord = $result_style_read['tbl_categories_karbord'];

                        echo '<div class="col-lg-6">
                          <form action="category_manage.php?t=' . $t . '&do=edit" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . $code . '" >
                           <input type="hidden" name="tid" value="' . read_fild(NULL, 'tbl_categories_type') . '" >
                           <input type="hidden" name="cat_id" value="0" >';
                        if ($t == 1) {
                            echo '<div class="form-group"><label>' . $lang[68] . '</label>
                <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">' . $lang[69] . '</option>';
                            while ($read_cat =  mysqli_fetch_array($result)) {

                                $parentid = $read_cat['tbl_categories_parentid'];
                                if ($parentid == '0') {
                                    echo '<option ' . ($parent_id_style == $read_cat['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                    $parentcode = $read_cat['tbl_categories_code'];
                                    $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language[1]' ORDER BY tbl_categories_id DESC ");
                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                        $parentid_sub = $read_sub['tbl_categories_code'];
                                        $result_cat_sub = Query("SELECT * From tbl_categories Where tbl_categories_code='$parentid_sub' and tbl_categories_lng='$language[1]' ORDER BY tbl_categories_id DESC ");
                                        $read_cat_sub = mysqli_fetch_array($result_cat_sub);
                                        echo '<option ' . ($result_style_read['tbl_categories_parentid'] == $read_cat_sub['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_sub['tbl_categories_code'] . '" class="group-sub">' . $read_sub['tbl_categories_title'] . '______</option>';
                                    }
                                }
                            }
                            echo '</select></div><hr>';
                        }

                        if ($t == 4) {
                            $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_order DESC,tbl_categories_id DESC ");
                            echo '<div class="form-group">
                           <label>انتخاب بخش</label><div>
                           <select name="bakhsh_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">انتخاب بخش</option>';
                            while ($read_cat =  mysqli_fetch_array($result_cat)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option ' . ($parent_id_bakhsh == $read_cat['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                }
                            }
                            echo '</select></div></div><hr>';

                            $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_type = '6' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_order DESC,tbl_categories_id DESC ");
                            echo '<div class="form-group">
                           <label>انتخاب حوزه کاربرد</label><div>
                           <select name="karbord_id"  data-placeholder="' . $lang[70] . '" style="width:100%;"  class="form-control select2" ><option value="0">انتخاب کاربرد</option>';
                            while ($read_cat =  mysqli_fetch_array($result_cat)) {
                                $parnetid = $read_cat['tbl_categories_parentid'];
                                if ($parnetid == '0') {
                                    echo '<option ' . ($parent_id_karbord == $read_cat['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                }
                            }
                            echo '</select></div></div><hr>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . ($i == '1' ? $titleFa : $titleEn) . '</label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_categories_title') . '">
                              </div>';
                        }
                        if ($contentSection == '1') {
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . exjection(read_fild($language[$i], 'tbl_categories_text')) . '</textarea>
                              </div>';
                            }
                        }
                        if ($t != '6') {
                            echo '<div class="form-group">
                          <label>' . $thisCodeTitle . '</label>
                          <input type="text" name="cat_code" value="' . read_fild(NULL, 'tbl_categories_catcode') . '" class="form-control" required> 
                          </div>';
                        }
                        echo '<div class="form-group">
                          <label>' . $lang[54] . '</label>
                          <input type="text" name="order" value="' . read_fild(NULL, 'tbl_categories_order') . '" class="form-control">
                          </div>';
                        if ($t == 1 || $t == 3 || $t == 4) {
                            $read_img = read_fild(NULL, 'tbl_categories_img');
                            echo '<hr><div class="form-group"><table><tr>
                            <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                            <td><input type="hidden" name="img_old" value="' . $read_img . '"><input type="file" name="img"></td>';
                            if (!empty($read_img)) {
                                echo '<td>
                                <a href="../upload/' . $read_img . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a>
                                <a href="?t=' . $t . '&do=deleteimage&code=' . $code . '&img=' . $read_img . '" ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt"></span></span></a>
                            </tr></table></div>';
                            } else {
                                echo '</tr></table></div>';
                            }
                        }
                        echo '<div class="form-group">
                            <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                            </div></form></div>';


                        if (isset($_POST['update'])) {
                            $cat_id = $_POST['cat_id'];
                            $code = $_POST['code'];
                            $img_old = $_POST['img_old'];
                            @$bakhsh_id = $_POST['bakhsh_id'];
                            @$karbord_id = $_POST['karbord_id'];
                            $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                            $tid = $_POST['tid'];
                            $cat_code = strtolower($_POST['cat_code']);
                            // image update

                            if ($tid == 3) {
                                $result = Query("SELECT * From tbl_categories Where tbl_categories_code !='$code' and tbl_categories_type='3' and tbl_categories_catcode ='$cat_code'");
                                if ($row = mysqli_num_rows($result) > 0) {
                                    $_SESSION['error'] = 'کد دوره واره شده قبلا استفاده شده است';
                                    header("Location: category_manage.php?t=$tid");
                                    exit();
                                }
                            }

                            if ($_FILES['img']['name'] == "") {
                                $img_name = $img_old;
                            } else {
                                $img = $_FILES['img']['name'];
                                $read_img_name = explode(".", $img);
                                $extn = end($read_img_name);
                                $img_name = 'img_' . time() . '.' . $extn;
                                if ($extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                                    unlink($directory . $img_old);
                                }
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = strip_tags($_POST['title_' . $language[$i]]);
                                $content_[$i] = imjection($_POST['content_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: category_manage.php?t=$t");
                                    exit();
                                }
                                $language_name = $language[$i];

                                Query("Update tbl_categories SET 
                                tbl_categories_text='$content_[$i]',
                                tbl_categories_catcode= '$cat_code',
                                tbl_categories_order= '$order' ,
                                tbl_categories_parentid= '$cat_id' ,
                                tbl_categories_title = '$title_[$i]' , 
                                tbl_categories_bakhsh = '$bakhsh_id' ,
                                tbl_categories_karbord = '$karbord_id' ,
                                tbl_categories_img = '$img_name' where 
                                tbl_categories_code= '$code' and tbl_categories_lng='$language[$i]'");
                                $_SESSION['error'] = $lang[21];
                                header("Location: category_manage.php?t=$t");
                            }
                        }
                    }
                    //delete image item
                    if (isset($t) && isset($do) && $do == 'deleteimage') {
                        echo 'reddddd';
                        $result = Query("SELECT * from tbl_categories  Where tbl_categories_code = '$code' ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-6 col-lg-offset-6">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $code = $_POST['code'];

                            // read old image record

                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_categories SET tbl_categories_img = '' Where tbl_categories_code = '$code' ");
                            header("Location: category_manage.php?t=$t&code=$code&do=edit");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: category_manage.php?t=$t&code=$code&do=edit");
                        }
                    }
                    // search result               
                    if (isset($t) && isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: category_manage.php?t=$t&keyword=$keyword_search");
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