<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// include
include './includer.php';
$img_item = '5';
$show_per_page = '20';
//type id 
if (isset($t) && $t == '1') {
    $pagetitle = 'صفحات متنی';
    $Tabpage = '6';
    $img_item = '1';
    $cat_manager_id = '0';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '1';
    $link_section = '1';
    $color_section = '0';
    $position_section = '1';
}
if (isset($t) && $t == '2') {
    $pagetitle = 'باکس صفحه اول';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '3') {
    $pagetitle = 'نظام استاد شاگردی';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '4') {
    $pagetitle = 'چرا سامانه ExAcd';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '5') {
    $pagetitle = 'گنجینه مهارت ها و دوره ها';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '15') {
    $pagetitle = 'مشتریان';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '16') {
    $pagetitle = 'تصویر اسلایدی';
    $Tabpage = '13';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '0';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '17') {
    $pagetitle = 'نتایج استاد شاگردی';
    $Tabpage = '13';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '0';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '18') {
    $pagetitle = 'باکس محتوی';
    $Tabpage = '13';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '19') {
    $pagetitle = 'باکس اسلاید';
    $Tabpage = '11';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '20') {
    $pagetitle = 'قسمت تب ها';
    $Tabpage = '14';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '1';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '21') {
    $pagetitle = 'مدیریت محتوی';
    $Tabpage = '14';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '1';
    $position_section = '0';
}
if (isset($t) && $t == '22') {
    $pagetitle = 'قسمت اسلاید بالا';
    $Tabpage = '15';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '23') {
    $pagetitle = 'مدیریت لیست';
    $Tabpage = '15';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '24') {
    $pagetitle = 'مدیریت محتوی متحرک';
    $Tabpage = '15';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '25') {
    $pagetitle = 'مدیریت محتوی ';
    $Tabpage = '15';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '1';
    $position_section = '0';
}
if (isset($t) && $t == '26') {
    $pagetitle = 'همکاران ما  ';
    $Tabpage = '16';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '0';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '27') {
    $pagetitle = 'قسمت تب ها';
    $Tabpage = '16';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '1';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '28') {
    $pagetitle = 'قسمت اکاردئونی';
    $Tabpage = '16';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '29') {
    $pagetitle = 'مدیریت محتوی';
    $Tabpage = '17';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '1';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '1';
    $position_section = '0';
}
if (isset($t) && $t == '30') {
    $pagetitle = 'جای شما اینجاست';
    $Tabpage = '17';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '1';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '31') {
    $pagetitle = 'قسمت اکاردئونی';
    $Tabpage = '17';
    $cat_manager_id = '0';
    $img_item = '0';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
if (isset($t) && $t == '32') {
    $pagetitle = 'مدیریت محتوی';
    $Tabpage = '25';
    $cat_manager_id = '0';
    $img_item = '1';
    $content_section = '1';
    $file_section = '0';
    $seo_section = '0';
    $link_section = '0';
    $color_section = '0';
    $position_section = '0';
}
include './header.inc.php';
?>
<div class="col-md-12 mb-12 admin-list-page">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    // list content
                    if (isset($t) && !isset($do)) {
                        // search and add button
                    ?>
                    <div class="form-group">
                        <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                    </div>
                    <div class="admin-list-toolbar">
                        <a class="btn btn-primary" href="?t=<?= (int) $t ?>&do=add"><?= $lang[31] ?></a>
                        <form action="misc.php?t=<?= (int) $t ?>&do=search" method="POST" class="admin-list-toolbar__field" role="search">
                            <label for="search_content_title">جست‌وجو در محتوا</label>
                            <div class="admin-list-search-controls">
                                <input type="search" class="form-control" id="search_content_title" name="keyword"
                                    value="<?= htmlspecialchars((string) $keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="عنوان محتوا">
                                <button type="submit" name="go" class="btn btn-primary"><?= $lang[77] ?></button>
                            </div>
                        </form>
                        <p class="admin-list-count" data-admin-list-count aria-live="polite">0 مورد</p>
                    </div>
                    <?php
                        // start count db result for pagination               
                        $keyword = is_string($keyword) ? $keyword : '';
                        $search_connection = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
                        mysqli_set_charset($search_connection, 'utf8');
                        $search_keyword = mysqli_real_escape_string($search_connection, $keyword);
                        mysqli_close($search_connection);
                        $result_for_count = Query("SELECT * From tbl_content Where tbl_content_type = '$t' and tbl_content_fld1='$language_default' and tbl_content_title LIKE '%$search_keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $page_number = max(1, (int) ceil($count_result / $show_per_page));
                        $page = max(1, min((int) $page, $page_number));
                        $start_contnet = ($page - 1) * $show_per_page;
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_content Where tbl_content_type = '$t' and tbl_content_fld1='$language_default' and tbl_content_title LIKE '%$search_keyword%' ORDER BY tbl_content_order ASC , tbl_content_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
                        } else {
                        // start show list
                        ?>
                    <form method="POST" action="?do=runupdate">
                        <table class="table table-bordered table-hover table-striped margin-top" data-admin-list="content" style="text-align:right;">
                            <thead class="table-dark">
                                <tr>
                                    <th><?= $lang[60] ?></th>
                                    <th>رده بندی</th>
                                    <th><?= $lang[50] ?></th>
                                    <th><?= $lang[55] ?></th>
                                    <?= ($t == '1' ? '<th>لینک</th>' : '') ?>
                                    <?= ($t == '1' ? '<th>قسمت</th>' : '') ?>
                                    <th><?= $lang[63] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $i = 1;
                                    while ($read = mysqli_fetch_array($result)) {
                                    ?>
                                <tr>
                                    <td style="width: 1%"><?= toPersianNum($i) ?></td>
                                    <td style="width: 1%"><input type="text" class="form-control" name="order[]" value="<?= $read['tbl_content_order'] ?>" style="width: 40px;float: right;margin-left: 4px;height: 25px;padding: 2px;text-align: center;">
                                        <input type="hidden" class="form-control" name="code[]" value="<?= $read['tbl_content_code'] ?>">
                                    </td>
                                    <td class="col-sm-6"><a href="?t=<?= $t . '&do=edit&code=' . $read['tbl_content_code']  ?>"><?= htmlspecialchars((string) $read['tbl_content_title'], ENT_QUOTES, 'UTF-8') . ($read['tbl_content_type'] == 3 && $read['tbl_content_slide'] == 1 ? '[اسلاید]' : '') . ($read['tbl_content_type'] == 3 && $read['tbl_content_top'] == 1 ? '[تاپ]' : '') ?></a></td>
                                    <td class="col-sm-2"><?= toPersianNum($read['tbl_content_date']) ?></td>
                                    <?= ($t == '1' ? '<td class="col-sm-2"><a href="../fa/page/' . $read['tbl_content_url'] . '/" target="_blank">نمایش صفحه</a></td>' : '') ?>
                                    <?= ($t == '1' ? '<td class="col-sm-2">' . htmlspecialchars((string) $read['tbl_content_position'], ENT_QUOTES, 'UTF-8') . '</td>' : '') ?>
                                    <td class="col-sm-2 admin-list-actions"><a href="?t=<?= $t  ?>&do=edit&code=<?= $read['tbl_content_code'] ?>" class="btn btn-xs btn-success "><?= $lang[23] ?></a>
                                        <a href="?t=<?= $t  ?>&do=delete&code=<?= $read['tbl_content_code'] ?>" class="btn btn-xs btn-danger"><?= $lang[24] ?></a>
                                    </td>
                                </tr>
                                <?php
                                        $i++;
                                    }
                                    echo '</tbody></table><input type="hidden" name="type" value="' . (int) $t . '"><input type="submit" name="submitorder" value="بروز رسانی رده بندی" class="btn btn-success update-btn"></form>';
                                    // start pagination                
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
                                                <li><a class="page-link" href="?t=<?= $t . '&page=' . ($back_page_number - 1) . '' . $other_page_keyword . rawurlencode($keyword) ?>"><?= $lang[75] ?></a></li>
                                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a></li>
                                                <li><a class="page-link" href="?t=<?= $t . '&page=' . min($page + 1, $page_number) . '' . $other_page_keyword . rawurlencode($keyword) ?>"><?= $lang[74] ?></a></li>
                                            </ul>
                                        </td>
                                        <td class="col-lg-6">
                                            <form action="misc.php?t=<?= $t ?>&do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                                <div class="form-group">
                                                    <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
                                                    <input type="text" class="form-control" name="page_number" style="height: 31px; width:30px; padding:6px 8px;float: left;margin-left: 10px;">
                                                </div>
                                            </form>
                                        </td>
                                    </tr>
                                </table>


                                <?php
                                    }
                                    ?>
                    <?php } ?>
                    <?php
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $order = sqi($_POST['order']);
                        $code = sqi($_POST['code']);
                        $t = sqi($_POST['type']);
                        foreach ($code as $key => $value) {
                            $val = $order[$key];
                            Query("update tbl_content set tbl_content_order='$val' Where tbl_content_type='$t' and tbl_content_code = '$value' ");
                        }
                        $_SESSION['error'] = 'ترتیب نمایش به‌روزرسانی شد.';
                        header("Location: misc.php?t=$t");
                    }
                    if (isset($t) && isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = max(1, (int) $_POST['page_number']);
                            header("Location: misc.php?t=$t&page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($t) && isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = trim((string) ($_POST['keyword'] ?? ''));
                            header('Location: misc.php?t=' . (int) $t . '&keyword=' . rawurlencode($keyword_search));
                        }
                    }
                    // end = search result  
                    // ////////////////////////////////////////////////////////////////////////////
                    // /////////////////////////////// start actions//////////////////////////////
                    // start = do add
                    if (isset($t) && isset($do) && $do == 'add') {
                    ?>
                    <div class="col-lg-10 cms-editor-wrap">
                        <form action="?t=<?= $t ?>&do=additem" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                            <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                            <input type="hidden" name="time" value="<?= time() ?>" class="form-control">
                            <input type="hidden" name="typeid" value="<?= $t ?>">
                            <h6 class="cms-section-heading">عنوان و دسته‌بندی</h6>
                            <?php
                                // start = if need cat manager show                    
                                if ($cat_manager_id == '1') {
                                    $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                ?>
                            <div class="form-group">
                                <label><?= $lang[68] ?></label>
                                <div>
                                    <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="select2">
                                        <option value="0"><?= $lang[70] ?></option>
                                        <?php
                                                // read categories
                                                while ($read_cat =  mysqli_fetch_array($result)) {
                                                    $parnetid = $read_cat['tbl_categories_parentid'];
                                                    if ($parnetid == '0') {
                                                        echo '<option value="' . $read_cat['tbl_categories_code'] . '" class="group-cat">' . $read_cat['tbl_categories_title'] . '</option>';
                                                        // read sub category                   
                                                        $parentcode = $read_cat['tbl_categories_code'];
                                                        $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                        while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                            echo '<option value="' . $read_sub['tbl_categories_code'] . '" class="group-sub">' . $read_sub['tbl_categories_title'] . '</option>';
                                                            // read sub sub category

                                                            $subcode = $read_sub['tbl_categories_code'];
                                                            $result_sub = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$subcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                            $row = mysqli_num_rows($result_sub);
                                                            while ($read_sub_sub = mysqli_fetch_array($result_sub)) {
                                                                echo '<option value="' . $read_sub_sub['tbl_categories_code'] . '" class="group-sub-sub">' . $read_sub_sub['tbl_categories_title'] . '</option>';
                                                            }
                                                            // end subb sub category
                                                        }
                                                        // end sub category        
                                                    }
                                                }
                                                ?>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <?php
                                }
                                // end = if need cat manager show  

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                            <div class="form-group">
                                <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                <input type="text" name="title_<?= $language[$i] ?>" class="form-control" <?= ($i == 1 ? 'required' : '') ?>>
                            </div>
                            <?php
                                }
                                if ($content_section == '1') {
                                    echo '<h6 class="cms-section-heading">متن اصلی در زبان‌های مختلف</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                            <div class="form-group">
                                <label><?= $lang[51] ?><span class="language"><?= $language[$i] ?></span></label>
                                <textarea name="content_<?= $language[$i] ?>" class="editor editor_<?= $language[$i] ?>"></textarea>
                            </div>
                            <?php
                                    }
                                }

                                if ($img_item > 0) {
                                    echo '<h6 class="cms-section-heading">تصاویر</h6>';
                                }
                                for ($i = 1; $i <= $img_item; $i++) {
                                    ?>
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-12 control-label"><?= $lang[53] ?>&nbsp; <?= $i ?></label></td>
                                        <td><input type="file" name="img_<?= $i ?>" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                }

                                echo '<hr>';
                                if ($file_section  == '1') {
                                    echo '<h6 class="cms-section-heading">فایل‌های پیوست</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-2 control-label"><?= $lang[52] ?><span class="language"><?= $language[$i] ?></span></label></td>
                                        <td><input type="file" name="file_attach_<?= $language[$i] ?>" class="form-control"></td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                    }
                                }
                                if ($seo_section == '1') {
                                    if ($t != '10') {
                                        echo '<h6 class="cms-section-heading">تنظیمات نمایش و سئو</h6>';
                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                    <label>کلمات کلیدی<span class="language">' . $language[$i] . '</span></label>
                                                    <textarea style="height: 120px;" name="keyword_' . $language[$i] . '" class="form-control textarea-height" rows="5"></textarea>
                                                    </div>';
                                        }
                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                    <label>توضیحات صفحه<span class="language">' . $language[$i] . '</span></label>
                                                    <textarea style="height: 120px;" name="discription_' . $language[$i] . '" class="form-control textarea-height" rows="5"></textarea>
                                                    </div>';
                                        }

                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                    <label>لینک نمایش دهنده  <span class="language">' . $language[$i] . '</span><div class="checknameresult-' . $language[$i] . '"></div></label>
                                                    <input type="text" name="url_' . $language[$i] . '" class="form-control checkname checkname-' . $language[$i] . '" data-lng="' . $language[$i] . '" data-code="" data-section="misc-' . $t . '-' . $language[$i] . '" ' . ($i == '1' ? 'required' : '') . '>
                                                    </div>';
                                        }
                                    }
                                }
                                echo '<h6 class="cms-section-heading">جزئیات انتشار</h6>';
                                if ($link_section == '1') {
                                    echo '<div class="form-group">
                                                <label>لینک </label>
                                                <input type="text" name="pageurl" class="form-control" >
                                                </div>';
                                }
                                if ($position_section == '1') {
                                    echo '<div class="form-group">
                                                <label>قسمت قرارگیری </label>
                                                <input type="text" name="position" class="form-control" >
                                                </div>';
                                }
                                if ($color_section == '1') {
                                    echo '<div class="form-group">
                                                <label>رنگ قاب </label>
                                                <input type="color" name="color" class="form-control" >
                                                </div>';
                                }
                                if (isset($t) && $t == 12) {
                                    ?>
                            <div class="form-group">
                                <label>دسته های این برند</label>
                                <select class="form-control select2" name="cat[]" multiple>
                                    <?php
                                            $result_tag = Query("select * from tbl_categories where tbl_categories_type='1' and tbl_categories_parentid !='0' order by tbl_categories_id DESC ");
                                            while ($read_tag = mysqli_fetch_array($result_tag)) {
                                                echo '<option value="' . $read_tag['tbl_categories_id'] . '" >' . $read_tag['tbl_categories_title'] . '</option>';
                                            }
                                            ?>
                                </select>
                            </div>

                            <?php
                                }
                                if (isset($t) && $t == 12) {
                                ?>
                            <div class="form-group">
                                <label>نام لاتین برند</label>
                                <input type="text" name="brandname" class="form-control">
                            </div>
                            <?php
                                }
                                ?>
                            <div class="form-group">
                                <label><?= $lang[55] ?></label>
                                <input type="text" name="date" value="<?= jdate('Y/n/j') ?>" class="form-control">
                            </div>
                            <?php
                                if (isset($t) && $t == 66) {
                                ?>
                            <div class="form-group">
                                <label>تاپ خبر <input type="checkbox" name="top" value="1"></label>
                                <label>اسلاید خبر <input type="checkbox" name="slide" value="1"></label>
                            </div>
                            <?php
                                }
                                ?>
                            <div class="cms-save-bar">
                                <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                                <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                            </div>
                        </form>
                    </div>
                    <?php
                    }
                    // start = add record to db
                    if (isset($t) && isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $position = (!empty($_POST['position']) ? $_POST['position'] : '');
                            $hot = (!empty($_POST['hot']) ? $_POST['hot'] : '0');
                            $color = (!empty($_POST['color']) ? $_POST['color'] : '');
                            $time = (!empty($_POST['time']) ? $_POST['time'] : '0');
                            $rank = (!empty($_POST['rank']) ? $_POST['rank'] : '0');
                            $tag = (!empty($_POST['tag']) ? $_POST['tag'] : '');
                            $top = (!empty($_POST['top']) ? $_POST['top'] : '0');
                            $slide = (!empty($_POST['slide']) ? $_POST['slide'] : '0');
                            $brandname = (!empty($_POST['brandname']) ? trim(strtolower($_POST['brandname'])) : '');
                            $pageurl = (!empty($_POST['pageurl']) ? $_POST['pageurl'] : '');
                            $tag_all = '';
                            if (!empty($_POST['tag'])) {
                                foreach ($tag as $key => $value) {
                                    if (!empty($value)) {
                                        $result_tag = Query("select * from tbl_tag where tbl_tag_title ='$value' ");
                                        if (mysqli_num_rows($result_tag) == 0) {
                                            $date = jdate("Y/m/d : a:s");
                                            Query("insert into tbl_tag (tbl_tag_title,tbl_tag_cat,tbl_tag_date) values('$value','$cat_id','$date') ");
                                        }
                                        $tag_all .= $value . ',';
                                    }
                                }
                            }

                            $cat_all = '';
                            if (!empty($_POST['cat'])) {
                                $cat = $_POST['cat'];
                                foreach ($cat as $key => $value) {
                                    if (!empty($value)) {
                                        $cat_all .= $value . ',';
                                    }
                                }
                            }
                            // if category add need
                            if ($cat_manager_id == '1') {
                                $cat_id = $_POST['cat_id'];
                            }
                            // for language
                            // filter add 
                            $filter_count = $_POST['filter_count'];

                            $filter_select = '';
                            for ($i = 1; $i <= $filter_count; $i++) {
                                $filter_[$i] = $_POST['filter_group_' . $i] . '/' . implode(',', $_POST['filter_' . $i]) . '.';
                                $filter_select .= $filter_[$i];
                            }

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $keyword_[$i] = addslashes($_POST['keyword_' . $language[$i]]);
                                $discription_[$i] = addslashes($_POST['discription_' . $language[$i]]);
                                $url_[$i] = addslashes($_POST['url_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }

                                $url_product_[$i] = '';
                                if (empty($url_[$i])) {
                                    $url_product_[$i] = str_replace(" ", "-", $title_[$i]);
                                    $url_product_[$i] = str_replace("(", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(")", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace("/", "-", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(".", "-", $url_product_[$i]);
                                    $url_product_[$i] = strtolower($url_product_[$i]);
                                } else {
                                    $url_product_[$i] = str_replace(" ", "-", $url_[$i]);
                                    $url_product_[$i] = str_replace("(", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(")", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace("/", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(".", "", $url_product_[$i]);
                                    $url_product_[$i] = strtolower($url_product_[$i]);
                                }

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }


                                // image record
                                if (!$images_name) {
                                    for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                        $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;

                                        // $images_name = all images after rename array

                                        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                        } elseif (!empty($exp[$i_image])) {
                                            $images_name .= $exp[$i_image] . ',';
                                        } else {
                                            $images_name .= ',';
                                        }
                                    }
                                }
                                // end upload image
                                // upload file

                                if ($_FILES['file_attach_' . $language[$i]]['name']) {
                                    $file_attach_[$language[$i]] = $_FILES['file_attach_' . $language[$i]]['name'];
                                    $read_file_name = explode(".", $file_attach_[$language[$i]]);
                                    $extn = end($read_file_name);
                                    $file_name_[$i] = 'file_' . time() . '_' . $language[$i] . '.' . $extn;
                                    if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'zip' || $extn == 'ZIP'  || $extn == 'mp4' || $extn == 'MP4') {
                                        move_uploaded_file($_FILES['file_attach_' . $language[$i]]["tmp_name"], "$directory" . $file_name_[$i]);
                                    }
                                } else {
                                    $file_name_[$i] = '';
                                }
                                // end file upload
                                $language_name = $language[$i];
                                $date = $_POST['date'];
                                // insert query
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
                                            tbl_content_fld2,
                                            tbl_content_fld4,
                                            tbl_content_fld5,
                                            tbl_content_tag,
                                            tbl_content_brandname,
                                            tbl_content_brandcat,
                                            tbl_content_top,
                                            tbl_content_url,
                                            tbl_content_keyword,
                                            tbl_content_discription,
                                            tbl_content_slide,
                                            tbl_content_pageurl,
                                            tbl_content_color,
                                            tbl_content_order,
                                            tbl_content_position,
                                            tbl_content_section,
                                            tbl_content_parent) 
                                            values(
                                                '$tid',
                                                '$code',
                                                '$title_[$i]',
                                                '$content_[$i]',
                                                '$images_name',
                                                '$file_name_[$i]',
                                                '$language_name',
                                                '$cat_id',
                                                '$date',
                                                '$filter_select',
                                                '$time',
                                                '$rank',
                                                '$tag_all',
                                                '$brandname',
                                                '$cat_all',
                                                '$top',
                                                '$url_product_[$i]',
                                                '$keyword_[$i]',
                                                '$discription_[$i]',
                                                '$slide',
                                                '$pageurl',
                                                '$color',
                                                '0',
                                                '$position',
                                                '',
                                                '')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: misc.php?t=$t");
                            }
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($t) && isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_content  Where tbl_content_code = '$code' and tbl_content_fld1 = '$language_default' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    <div class="col-lg-12" style="text-align:right;"><?= $lang[25] ?><br />
                        <h2><?= $read['tbl_content_title'] ?></h2><br />
                        <form action="#" method="POST">
                            <div class="form-group margin-top">
                                <input type="hidden" name="img" value="<?= $read['tbl_content_img'] ?>">
                                <?php
                                    // find file form languages for delete
                                    $files_name = '';
                                    for ($i = 1; $i <= $language_count; $i++) {

                                        $result_file = Query("SELECT * from tbl_content  Where tbl_content_code = '$code' and tbl_content_fld1 = '$language[$i]' ");
                                        $read_file = mysqli_fetch_array($result_file);
                                        $files_name .= $read_file['tbl_content_file'] . ',';
                                    }
                                    ?>
                                <input type="hidden" name="file" value="<?= $files_name ?>">
                                <input type="hidden" value="<?= $code ?>" name="code">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $file = $_POST['file'];

                            //start = delete images
                            unset($i);
                            $exp_img = explode(",", $img);
                            for ($i = 0; $i <= $img_item; $i++) {
                                if ($exp_img[$i]) {
                                    unlink("$directory" . $exp_img[$i]);
                                }
                            }

                            // start = delete files
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
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {

                            header("Location: misc.php?t=$t");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($t) && isset($do) && $do == 'edit') {

                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
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
                        // end function

                        ?>
                    <div class="col-lg-10 cms-editor-wrap">
                        <form action="?t=<?= $t ?>&do=edititem" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                            <input type="hidden" name="time" value="<?= time() ?>" class="form-control">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <h6 class="cms-section-heading">عنوان و دسته‌بندی</h6>
                            <?php
                                $current_cat_post = read_fild(NULL, 'tbl_content_catid');
                                // start = if need cat manager show                    
                                if ($cat_manager_id == '1') {
                                    $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                ?>
                            <div class="form-group">
                                <label><?= $lang[68] ?></label>
                                <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="select2">
                                    <option value="0"><?= $lang[70] ?></option>
                                    <?php
                                            // read categories
                                            while ($read_cat =  mysqli_fetch_array($result)) {
                                                if ($current_cat_post == $read_cat['tbl_categories_code']) {
                                                    $style = 'selected';
                                                } else {
                                                    $style = '';
                                                }

                                                $parnetid = $read_cat['tbl_categories_parentid'];
                                                if ($parnetid == '0') {
                                                    echo '<option ' . $style . ' value="' . $read_cat['tbl_categories_code'] . '" class="group-cat" >' . $read_cat['tbl_categories_title'] . '</option>';

                                                    // read sub category                   
                                                    $parentcode = $read_cat['tbl_categories_code'];
                                                    $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                        if ($current_cat_post == $read_sub['tbl_categories_code']) {
                                                            $style = 'selected';
                                                        } else {
                                                            $style = '';
                                                        }
                                                        echo '<option ' . $style . ' value="' . $read_sub['tbl_categories_code'] . '" class="group-sub" >' . $read_sub['tbl_categories_title'] . '</option>';
                                                        // read sub sub category

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
                                                        // end subb sub category
                                                    }
                                                    // end sub category        
                                                }
                                            }
                                            ?>
                                </select>
                            </div>
                            <hr>
                            <?php
                                }

                                // end read categories
                                //////////////////////////////////////////////////////////////////////////////////////////////

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                            <div class="form-group">
                                <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                <input type="text" name="title_<?= $language[$i] ?>" value="<?= stripslashes(read_fild($language[$i], 'tbl_content_title')) ?>" class="form-control" <?= ($i == 1 ? 'required' : '') ?>>
                            </div>
                            <?php
                                }
                                if ($content_section == '1') {
                                    echo '<h6 class="cms-section-heading">متن اصلی در زبان‌های مختلف</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                            <div class="form-group">
                                <label><?= $lang[51] ?><span class="language"><?= $language[$i] ?></span></label>
                                <textarea name="content_<?= $language[$i] ?>" class="editor editor_<?= $language[$i] ?>"><?= stripslashes(read_fild($language[$i], 'tbl_content_text')) ?></textarea>
                            </div>
                            <?php
                                    }
                                }
                                // read image's
                                if ($img_item > 0) {
                                    echo '<h6 class="cms-section-heading">تصاویر</h6>';
                                }
                                echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_content_img') . '">';
                                for ($i = 1; $i <= $img_item; $i++) {

                                    $exp = explode(",", read_fild(NULL, 'tbl_content_img'));
                                    if ($exp[$i - 1]) {
                                    ?>
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-12 control-label"><?= $lang[53] . '&nbsp; ' . $i ?></label></td>
                                        <td><input type="file" name="img_<?= $i ?>" class="form-control"></td>
                                        <td><a href="../upload_site/<?= $exp[$i - 1] ?>" target="_blank">
                                                <span class="btn btn-xs btn-success">
                                                    <span class="bx bxs-show  btn-xs"></span>
                                                </span></a>
                                            <a href="?t=<?= $t . '&code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] ?>">
                                                <span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt  btn-xs"></span></span>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                    } else {
                                    ?>
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-12 control-label"><?= $lang[53] . '&nbsp; ' . $i ?></label></td>
                                        <td><input type="file" name="img_<?= $i ?>" class="form-control">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                    }
                                }
                                if ($file_section  == '1') {
                                    echo '<h6 class="cms-section-heading">فایل‌های پیوست</h6>';
                                    echo '<hr>';

                                    // Read file's
                                    for ($i = 1; $i <= $language_count; $i++) {
                                        if (read_fild($language[$i], 'tbl_content_file')) {
                                        ?>
                            <input type="hidden" name="old_file_<?= $language[$i] ?>" value="<?= read_fild($language[$i], 'tbl_content_file') ?>">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-12 control-label"><?= $lang[52] ?><span class="language"><?= $language[$i] ?></span></label></td>
                                        <td><input type="file" name="file_attach_<?= $language[$i] ?>" class="form-control"></td>
                                        <td><a href="../upload_site/<?= read_fild($language[$i], 'tbl_content_file') ?>" target="_blank">
                                                <span class="btn btn-xs btn-success"><span class="bx bxs-show  btn-xs"></span></span>
                                            </a>
                                            <a href="?t=<?= $t ?>&code=<?= read_fild(NULL, 'tbl_content_code') ?>&do=deletefile&file=<?= read_fild($language[$i], 'tbl_content_file') ?>" target="_blank">
                                                <span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt  btn-xs"></span></span></a>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                        } else {
                                        ?>
                            <input type="hidden" name="old_file" value="<?= read_fild($language[$i], 'tbl_content_file') ?>">
                            <div class="form-group">
                                <table>
                                    <tr>
                                        <td><label class="col-sm-12 control-label"><?= $lang[52] ?><span class="language"><?= $language[$i] ?></span></label></td>
                                        <td><input type="file" name="file_attach_<?= $language[$i] ?>" class="form-control">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <?php
                                        }
                                    }
                                }

                                if (isset($t) && $t == 12) {
                                    ?>
                            <div class="form-group">
                                <label>دسته های این برند</label>
                                <select class="form-control select2" name="cat[]" multiple>
                                    <?php
                                            $result_tag = Query("select * from tbl_categories where tbl_categories_type='1' order by tbl_categories_id DESC ");
                                            while ($read_tag = mysqli_fetch_array($result_tag)) {
                                                echo '<option value="' . $read_tag['tbl_categories_id'] . '" ' . (tag(read_fild(NULL, 'tbl_content_brandcat'), $read_tag['tbl_categories_id']) === TRUE ? 'selected' : '') . '>' . $read_tag['tbl_categories_title'] . '</option>';
                                            }
                                            ?>
                                </select>
                            </div>
                            <?php
                                }
                                if (isset($t) && $t == 12) {
                                ?>
                            <div class="form-group">
                                <label>نام لاتین برند</label>
                                <input type="text" name="brandname" class="form-control" value="<?= read_fild(NULL, 'tbl_content_brandname') ?>">
                                <input type="hidden" name="brandnameold" value="<?= read_fild(NULL, 'tbl_content_brandname') ?>">
                            </div>
                            <?php
                                }
                                if ($seo_section == '1') {
                                    if ($t != '10') {
                                        echo '<h6 class="cms-section-heading">تنظیمات نمایش و سئو</h6>';
                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                <label>کلمات کلیدی<span class="language">' . $language[$i] . '</span></label>
                                                <textarea style="height: 120px;" name="keyword_' . $language[$i] . '" class="form-control textarea-height" rows="5">' . read_fild($language[$i], 'tbl_content_keyword') . '</textarea>
                                                </div>';
                                        }
                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                <label>توضیحات صفحه<span class="language">' . $language[$i] . '</span></label>
                                                <textarea style="height: 120px;" name="discription_' . $language[$i] . '" class="form-control textarea-height" rows="5">' . read_fild($language[$i], 'tbl_content_discription') . '</textarea>
                                                </div>';
                                        }

                                        for ($i = 1; $i <= $language_count; $i++) {
                                            echo '<div class="form-group">
                                                <label>لینک نمایش دهنده  <span class="language">' . $language[$i] . '</span><div class="checknameresult-' . $language[$i] . '"></div></label>
                                                <input type="text" name="url_' . $language[$i] . '" class="form-control checkname" data-code="' . read_fild(NULL, 'tbl_content_code') . '" data-section="misc-' . $t  . '" ' . ($i == '1' ? 'required' : '') . ' value="' . read_fild($language[$i], 'tbl_content_url') . '">
                                                </div>';
                                        }
                                    }
                                }
                                echo '<h6 class="cms-section-heading">جزئیات انتشار</h6>';
                                if ($link_section == '1') {
                                    echo '<div class="form-group">
                                                <label>لینک </label>
                                                <input type="text" name="pageurl" class="form-control" value="' . read_fild(null, 'tbl_content_pageurl') . '">
                                                </div>';
                                }
                                if ($position_section == '1') {
                                    echo '<div class="form-group">
                                                <label>قسمت قرارگیری </label>
                                                <input type="text" name="position" class="form-control" value="' . read_fild(null, 'tbl_content_position') . '">
                                                </div>';
                                }
                                if ($color_section == '1') {
                                    echo '<div class="form-group">
                                                <label>رنگ قاب </label>
                                                <input type="color" name="color" class="form-control" value="' . read_fild(null, 'tbl_content_color') . '">
                                                </div>';
                                }
                                if (isset($t) && $t == 66) {
                                ?>
                            <div class="form-group">
                                <label>تاپ خبر <input type="checkbox" name="top" value="1" <?= (read_fild(NULL, 'tbl_content_top') == 1 ? 'checked' : '') ?>></label>
                                <label>اسلاید خبر <input type="checkbox" name="slide" value="1" <?= (read_fild(NULL, 'tbl_content_slide') == 1 ? 'checked' : '') ?>></label>
                            </div>
                            <?php
                                }
                                ?>
                            <hr>
                            <div class="form-group">
                                <label><?= $lang[55] ?></label>
                                <input type="text" name="date" value="<?= jdate('Y/n/j') ?>" value="<?= read_fild(NULL, 'tbl_content_date') ?>" class="form-control">
                            </div>
                            <div class="cms-save-bar">
                                <input type="submit" name="updateitem" value="<?= $lang[20] ?>" class="btn btn-primary">
                                <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                            </div>
                        </form>
                    </div>
                    <?php
                    }
                    // end = edit item
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $images_name = '';
                            $code = $_POST['code'];
                            $cat_id = $_POST['cat_id'];
                            $position = (!empty($_POST['position']) ? $_POST['position'] : '');
                            $color = (!empty($_POST['color']) ? $_POST['color'] : '');
                            $time = (!empty($_POST['time']) ? $_POST['time'] : '0');
                            $rank = (!empty($_POST['rank']) ? $_POST['rank'] : '0');
                            $tag = (!empty($_POST['tag']) ? $_POST['tag'] : '');
                            $top = (!empty($_POST['top']) ? $_POST['top'] : '0');
                            $slide = (!empty($_POST['slide']) ? $_POST['slide'] : '0');
                            $brandname = (!empty($_POST['brandname']) ? trim(strtolower($_POST['brandname'])) : '');
                            $brandnameold = (!empty($_POST['brandnameold']) ? trim(strtolower($_POST['brandnameold'])) : '');
                            $pageurl = (!empty($_POST['pageurl']) ? $_POST['pageurl'] : '');
                            // filter add 
                            $filter_count = (!empty($_POST['filter_count']) ? $_POST['filter_count'] : '');

                            $filter_select = '';
                            for ($i = 1; $i <= $filter_count; $i++) {
                                $filter_[$i] = $_POST['filter_group_' . $i] . '/' . implode(',', $_POST['filter_' . $i]) . '.';
                                $filter_select .= $filter_[$i];
                            }
                            $tag_all = '';
                            if (!empty($_POST['tag'])) {
                                foreach ($tag as $key => $value) {
                                    if (!empty($value)) {
                                        $result_tag = Query("select * from tbl_tag where tbl_tag_title ='$value' ");
                                        if (mysqli_num_rows($result_tag) == 0) {
                                            $date = jdate("Y/m/d : a:s");
                                            Query("insert into tbl_tag (tbl_tag_title,tbl_tag_cat,tbl_tag_date) values('$value','$cat_id','$date') ");
                                        }
                                        $tag_all .= $value . ',';
                                    }
                                }
                            }
                            @$cat = $_POST['cat'];
                            $cat_all = '';
                            if (!empty($_POST['cat'])) {
                                foreach ($cat as $key => $value) {
                                    if (!empty($value)) {
                                        $cat_all .= $value . ',';
                                    }
                                }
                            }
                            // read old images for check changer or no
                            $allimages = $_POST['allimages'];
                            $read_allimages = explode(",", $allimages);

                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['img_' . $i_image]['name'];

                                    if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    } else {
                                        // remove old image and replace with new                               

                                        $read_file_name = explode(".", $img_[$i_image]);
                                        $extn = end($read_file_name);
                                        $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;

                                        // $images_name = all images after rename array

                                        if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'zip' || $extn == 'ZIP') {
                                            $images_name .= $img_[$i_image] . ',';
                                            move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "$directory" . $img_[$i_image]);
                                            // remove old image
                                            unlink($directory . $read_allimages[$i_image - 1]);
                                        } else {
                                            $images_name .= $read_allimages[$i_image - 1] . ',';
                                        }
                                    }
                                }
                            }


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $keyword_[$i] = addslashes($_POST['keyword_' . $language[$i]]);
                                $discription_[$i] = addslashes($_POST['discription_' . $language[$i]]);
                                $url_[$i] = addslashes($_POST['url_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }
                                $url_product_[$i] = '';
                                if (empty($url_[$i])) {
                                    $url_product_[$i] = str_replace(" ", "-", $title_[$i]);
                                    $url_product_[$i] = str_replace("(", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(")", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace("/", "-", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(".", "-", $url_product_[$i]);
                                    $url_product_[$i] = strtolower($url_product_[$i]);
                                } else {
                                    $url_product_[$i] = str_replace(" ", "-", $url_[$i]);
                                    $url_product_[$i] = str_replace("(", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(")", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace("/", "", $url_product_[$i]);
                                    $url_product_[$i] = str_replace(".", "", $url_product_[$i]);
                                    $url_product_[$i] = strtolower($url_product_[$i]);
                                }
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: misc.php?t=$t");
                                    exit();
                                }


                                // image record


                                // end upload image
                                // upload file

                                if (!$_FILES['file_attach_' . $language[$i]]['error'] > 0) {
                                    $file_attach_[$language[$i]] = $_FILES['file_attach_' . $language[$i]]['name'];
                                    $read_file_name = explode(".", $file_attach_[$language[$i]]);
                                    $extn = end($read_file_name);
                                    $file_name_[$i] = 'file_' . time() . '_' . $language[$i] . '.' . $extn;
                                    if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'PDF' || $extn == 'pdf' || $extn == 'zip' || $extn == 'ZIP' || $extn == 'mp4' || $extn == 'MP4') {
                                        move_uploaded_file($_FILES['file_attach_' . $language[$i]]["tmp_name"], "$directory" . $file_name_[$i]);
                                    }
                                    $file_old_[$i] = (!empty($_POST['old_file_' . $language[$i]]) ? $_POST['old_file_' . $language[$i]] : '');
                                    unlink($directory . $file_old_[$i]);
                                } else {
                                    $file_name_[$i] = (!empty($_POST['old_file_' . $language[$i]]) ? $_POST['old_file_' . $language[$i]] : '');
                                }
                                // end file upload
                                $language_record = $language[$i];
                                $date = $_POST['date'];
                                $result_check = Query("select tbl_content_id,tbl_content_type from tbl_content where tbl_content_code= '$code' ");
                                $read_check = mysqli_fetch_array($result_check);
                                if ($read_check['tbl_content_type'] == '12' && $brandnameold != $brandname) {
                                    $thisid = $read_check['tbl_content_id'];
                                    Query("update tbl_product set tbl_product_brandname='$brandname' where tbl_product_brand='$thisid' ");
                                }
                                // update query
                                Query("Update tbl_content SET 
                                        tbl_content_slide='$slide',
                                        tbl_content_top='$top',
                                        tbl_content_brandcat='$cat_all',
                                        tbl_content_brandname='$brandname',
                                        tbl_content_tag='$tag_all' ,
                                        tbl_content_title='$title_[$i]' , 
                                        tbl_content_text = '$content_[$i]' ,
                                        tbl_content_img = '$images_name' ,
                                        tbL_content_file = '$file_name_[$i]' ,
                                        tbl_content_date = '$date' ,
                                        tbl_content_catid = '$cat_id',
                                        tbl_content_fld2 = '$filter_select',
                                        tbl_content_fld5 = '$rank',
                                        tbl_content_fld4 = '$time',
                                        tbl_content_url = '$url_product_[$i]',
                                        tbl_content_keyword = '$keyword_[$i]',
                                        tbl_content_discription = '$discription_[$i]',
                                        tbl_content_pageurl='$pageurl',
                                        tbl_content_position='$position',
                                        tbl_content_color = '$color' 
                                        Where tbl_content_code= '$code' and tbl_content_fld1 = '$language_record'");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: misc.php?t=$t");
                        }
                    }
                    // end = update item

                    // strat = delete image item
                    if (isset($t) && isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_content Where tbl_content_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    <div class="col-lg-12"><?= $lang[25] ?><br /><img style="width:35%; height:35%;" src="<?= $directory . $img ?>"><br />
                        <form action=" " method="POST">
                            <div class="form-group margin-top">
                                <input type="hidden" name="img" value="<?= $img ?>">
                                <input type="hidden" value="<?= $code ?>" name="code">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $code = $_POST['code'];
                            // read old image record
                            $record = str_replace("$img", "", $read['tbl_content_img']);
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_content SET tbl_content_img = '$record' Where tbl_content_code = '$code' ");
                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        }
                    }
                    // delete file item
                    if (isset($t) && isset($do) && $do == 'deletefile') {
                        $result = Query("SELECT * from tbl_content  Where tbl_content_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        // which file language : read language file
                        $which_file_lng = explode("_", $file);
                        $which_file_lng = end($which_file_lng);
                        $which_file_lng = explode(".", $which_file_lng);
                        // read file language for exp  . en or fa in before extension
                        $which_file_lng = reset($which_file_lng);
                        ?>
                    <div class="col-lg-12"><?= $lang[25] ?><br /><a href="<?= $directory . $file ?>"><?= $lang[58] ?></a><br />
                        <form action=" " method="POST">
                            <div class="form-group margin-top">
                                <input type="hidden" name="language" value="<?= $which_file_lng ?>">
                                <input type="hidden" name="file" value="<?= $file ?>">
                                <input type="hidden" value="<?= $code ?>" name="code">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $file = $_POST['file'];
                            $code = $_POST['code'];
                            $lang = $_POST['language'];

                            // read old file record

                            $external_file = $directory . $file;
                            unlink("$external_file");
                            $record = '';
                            Query("Update tbl_content SET tbl_content_file = '$record' Where tbl_content_code = '$code' and tbl_content_fld1 = '$lang' ");
                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        } elseif (isset($_POST['nodelete'])) {

                            header("Location: misc.php?t=$t&code=$code&do=edit");
                        }
                    }
                    // end = delete image item
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo '<script src="asset/js/listing-ui.js"></script>';
echo '<script src="asset/js/cms-editor-ui.js"></script>';
include './footer.inc.php';