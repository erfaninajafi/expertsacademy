<?php
include './includer.php';
$pagetitle = 'مدیریت منو';
$show_per_page = '10';
$Tabpage = '6';
include './header.inc.php';
?>
<div class="col-md-12 mb-12 admin-list-page">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    $result = Query("SELECT * From tbl_menu Where tbl_menu_lng='$language_default' ");
                    $count_result = mysqli_num_rows($result);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    $result = Query("SELECT * From tbl_menu Where tbl_menu_lng='$language_default'  ORDER BY tbl_menu_order DESC ");
                    if (!isset($do)) {
                        // search and add button
                    ?>
                    <div class="form-group">
                        <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                    </div>
                    <div class="admin-list-toolbar">
                        <a class="btn btn-primary" href="?do=add"><?= $lang[86] ?></a>
                        <form action="menu_manager.php?do=search" method="POST" class="admin-list-toolbar__field" role="search">
                            <label for="menu_title">جست‌وجو در منوهای نمایش‌داده‌شده</label>
                            <div class="admin-list-search-controls">
                                <input type="search" class="form-control" id="menu_title" name="keyword" data-admin-list-search
                                    value="<?= htmlspecialchars((string) $keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="عنوان منو">
                                <button type="submit" name="go" class="btn btn-primary"><?= $lang[77] ?></button>
                            </div>
                        </form>
                        <p class="admin-list-count" data-admin-list-count aria-live="polite"></p>
                    </div>
                    <p class="admin-list-empty" data-admin-list-empty hidden>موردی مطابق جست‌وجو در این صفحه پیدا نشد.</p>
                    <?php
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
                        } else {
                        ?>
                    <form action="?do=runupdate" method="POST">
                        <table class="table table-bordered table-hover table-striped margin-top" data-admin-list="menu" style="text-align:right;">
                            <thead class="table-dark">
                                <tr>
                                    <th><?= $lang[60] ?></th>
                                    <th><?= $lang[50] ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;  // for record number
                                $result = Query("SELECT * From tbl_menu Where tbl_menu_parentid = '0' and tbl_menu_lng='$language_default' ORDER BY tbl_menu_order ASC ");
                                // check parent
                                while ($read = mysqli_fetch_array($result)) {
                                    // parent menu code
                                    $code = $read['tbl_menu_code'];
                                ?>
                                <tr>
                                    <td style="width: 1%"><?= toPersianNum($i) ?></td>
                                    <td class="col-sm-6" style="line-height:34px;">
                                        <?= '<input type="hidden" name="code[]" value="' . $read['tbl_menu_code'] . '"><input type="text" name="o' . $read['tbl_menu_code'] . '" value="' . $read['tbl_menu_order'] . '" maxlength="5" style="width: 40px;text-align: center;height: 27px;margin-left: 10px;">'.htmlspecialchars((string) $read['tbl_menu_title'], ENT_QUOTES, 'UTF-8') ?>
                                        <a href="?code=<?= $read['tbl_menu_code'] ?>&do=edit" class="btn btn-xs btn-success" style="font-size:8px;"><?= $lang[23] ?></a>
                                        <a href="?code=<?= $read['tbl_menu_code'] ?>&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;"><?= $lang[24] ?></a>
                                        <?php
                                            $result_sub = Query("Select * from tbl_menu Where tbl_menu_parentid = '$code' and tbl_menu_lng = '$language_default' ");
                                            $row = mysqli_num_rows($result_sub);
                                            if ($row !== 0) {
                                                while ($read =  mysqli_fetch_array($result_sub)) {
                                                    echo '<br><input type="hidden" name="code[]" value="' . $read['tbl_menu_code'] . '"><input type="text" name="o' . $read['tbl_menu_code'] . '" value="' . $read['tbl_menu_order'] . '" maxlength="5" style="width: 40px;text-align: center;height: 27px;margin-left: 10px;"><span class="admin-menu-indent admin-menu-indent--one" aria-hidden="true"></span>' . htmlspecialchars((string) $read['tbl_menu_title'], ENT_QUOTES, 'UTF-8') . '<a href="?code=' . $read['tbl_menu_code'] . '&do=edit" class="btn btn-xs btn-success" style="font-size:8px;">' . $lang[23] . '</a><a href="?code=' . $read['tbl_menu_code'] . '&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;">' . $lang[24] . '</a>';
                                                    // sub menu code
                                                    $code = $read['tbl_menu_code'];
                                                    // read sub sub menu
                                                    $result_sub_sub = Query("Select * from tbl_menu Where tbl_menu_parentid = '$code' and tbl_menu_lng = '$language_default' ");
                                                    $row = mysqli_num_rows($result_sub_sub);
                                                    if ($row !== 0) {
                                                        while ($read =  mysqli_fetch_array($result_sub_sub)) {
                                                            echo '<br><input type="hidden" name="code[]" value="' . $read['tbl_menu_code'] . '"><input type="text" name="o' . $read['tbl_menu_code'] . '" value="' . $read['tbl_menu_order'] . '" maxlength="5" style="width: 40px;text-align: center;height: 27px;margin-left: 10px;"><span class="admin-menu-indent admin-menu-indent--two" aria-hidden="true"></span>' . htmlspecialchars((string) $read['tbl_menu_title'], ENT_QUOTES, 'UTF-8') . '<a href="?code=' . $read['tbl_menu_code'] . '&do=edit" class="btn btn-xs btn-success" style="font-size:8px;">' . $lang[23] . '</a><a href="?code=' . $read['tbl_menu_code'] . '&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;">' . $lang[24] . '</a>';
                                                        }
                                                    }
                                                }
                                            }
                                            ?>
                                    </td>
                                </tr>

                                <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table><input type="submit" name="runupdate" value="بروز رسانی ترتیب نمایش" class="btn btn-warning update-btn">
                    </form>
                    <?php
                        }
                    }
                    // start = add item
                    if (isset($do) && $do == 'runupdate') {
                        $code = $_POST['code'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_menu set tbl_menu_order='$val' Where tbl_menu_code = '$value' ");
                        }
                        $_SESSION['error'] = 'ترتیب نمایش به‌روزرسانی شد.';
                        header("Location: menu_manager.php");
                    }
                    if (isset($do) && $do == 'add') {
                    ?>
                    <div class="col-lg-6">
                        <form action="menu_manager.php?do=additem" method="POST">
                            <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                            <div class="form-group">
                                <label><?= $lang[68] ?></label>
                                <div>
                                    <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="form-control select2">
                                        <option value="0"><?= $lang[84] ?></option>
                                        <?php
                                            // read menu
                                            while ($read_cat =  mysqli_fetch_array($result)) {
                                                $parnetid = $read_cat['tbl_menu_parentid'];
                                                if ($parnetid == '0') {
                                                    echo '<option value="' . $read_cat['tbl_menu_code'] . '" class="group-cat">' . $read_cat['tbl_menu_title'] . '</option>';

                                                    $result_cat = Query("SELECT * From tbl_menu Where tbl_menu_parentid='$read_cat[tbl_menu_code]' ORDER BY tbl_menu_id DESC ");
                                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                        ///echo '<option value="' . $read_sub['tbl_menu_code'] . '" class="group-sub">' . $read_sub['tbl_menu_title'] . ' ________</option>';
                                                    }
                                                }
                                            }
                                            echo '</select></div></div><hr>';

                                            // end read menu
                                            // add title
                                            for ($i = 1; $i <= $language_count; $i++) {
                                            ?>
                                        <div class="form-group">
                                            <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                            <input type="text" name="title_<?= $language[$i] ?>" class="form-control">
                                        </div>
                                        <?php
                                            }
                                            // add link                   
                                            for ($i = 1; $i <= $language_count; $i++) {
                                            ?>
                                        <div class="form-group">
                                            <label><?= $lang[83] ?><span class="language"><?= $language[$i] ?></span></label>
                                            <input type="text" name="link_<?= $language[$i] ?>" class="form-control">
                                        </div>
                                        <?php
                                            }
                                            ?>
                                        <hr>
                                        <div class="form-group">
                                            <label>رنگ زیر منو</label>
                                            <input type="color" name="color" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>رنگ زیر منو متن</label>
                                            <input type="color" name="textcolor" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label><?= $lang[85] ?></label>
                                            <input type="text" name="order_by" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                                        </div>
                        </form>
                    </div>
                    <?php
                    }


                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $cat_id = $_POST['cat_id'];
                            $order_by = (!empty($_POST['order_by']) ? $_POST['order_by'] : '0');
                            $discount = (!empty($_POST['discount']) ? $_POST['discount'] : '0');
                            $style = (!empty($_POST['style']) ? $_POST['style'] : '0');
                            $color = $_POST['color'];
                            $textcolor = $_POST['textcolor'];

                            // for language
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = SafeInput($_POST['title_' . $language[$i]]);
                                $link_[$i] = $_POST['link_' . $language[$i]];

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: menu_manager.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // insert query
                                Query("INSERT INTO tbl_menu (tbl_menu_code,tbl_menu_title,tbl_menu_parentid,tbl_menu_link,tbl_menu_order,tbl_menu_lng,tbl_menu_discount,tbl_menu_style,tbl_menu_color,tbl_menu_textcolor) values('$code','$title_[$i]','$cat_id','$link_[$i]','$order_by','$language_name','$discount','$style','$color','$textcolor')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: menu_manager.php");
                            }
                        }
                    }
                    // end = add item
                    // start = delete category
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_menu Where tbl_menu_code = '$code' and tbl_menu_lng='$language_default' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    <div class="col-lg-6"><?= $lang[25] . '<br /><br />' . $read['tbl_menu_title'] ?>
                        <form action="#" method="POST">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <div class="form-group margin-top">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];

                            $result = Query("SELECT * from tbl_menu Where tbl_menu_parentid = '$code' and tbl_menu_lng='$language_default' ");
                            $row_parent = mysqli_num_rows($result);
                            $read = mysqli_fetch_array($result);

                            if ($row_parent > 0) {
                                $_SESSION['error'] = $lang[71];
                                header("Location: menu_manager.php");
                                exit();
                            }
                            Query("Delete From tbl_menu Where tbl_menu_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: menu_manager.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: menu_manager.php");
                        }
                    }
                    // end delete category

                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_menu_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_menu Where tbl_menu_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function
                        //start style selected item and read now select code
                        $result_style = Query("SELECT * FROM tbl_menu Where tbl_menu_code =  '$code' ");
                        $result_style_read = mysqli_fetch_array($result_style);
                        $parent_id_style = $result_style_read['tbl_menu_parentid'];

                        //end style selected item
                        ?>
                    <div class="col-lg-6">
                        <form action="menu_manager.php?&do=update" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <div class="form-group">
                                <label><?= $lang[68] ?></label>
                                <div>
                                    <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="form-control select2">
                                        <option value="0"><?= $lang[69] ?></option>
                                        <?php
                                            // read menu
                                            while ($read_cat =  mysqli_fetch_array($result)) {
                                                // Creat style select
                                                if ($parent_id_style == $read_cat['tbl_menu_code']) {
                                                    $style = 'selected';
                                                } else {
                                                    $style = '';
                                                }
                                                $parentid = $read_cat['tbl_menu_parentid'];
                                                if ($parentid == '0') {
                                                    echo '<option  ' . $style . ' value="' . $read_cat['tbl_menu_code'] . '" class="group-cat">' . $read_cat['tbl_menu_title'] . '</option>';
                                                    // end sub category       
                                                    $result_cat = Query("SELECT * From tbl_menu Where tbl_menu_parentid='$read_cat[tbl_menu_code]' ORDER BY tbl_menu_id DESC ");
                                                    while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                        //echo '<option ' . ($parent_id_style == $read_sub['tbl_menu_code'] ? 'selected' : '') . ' value="' . $read_sub['tbl_menu_code'] . '" class="group-sub">' . $read_sub['tbl_menu_title'] . ' ________</option>';
                                                    }
                                                }
                                            }
                                            ?>

                                    </select>
                                </div>
                            </div>
                            <hr>
                            <?php
                                // end read menu
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                            <div class="form-group">
                                <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= read_fild($language[$i], 'tbl_menu_title') ?>">
                            </div>
                            <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                            <div class="form-group">
                                <label><?= $lang[83] ?><span class="language"><?= $language[$i] ?></span></label>
                                <input type="text" name="link_<?= $language[$i] ?>" class="form-control" value="<?= read_fild($language[$i], 'tbl_menu_link') ?>">
                            </div>
                            <?php
                                }
                                ?>
                            <div class="form-group">
                                <label>رنگ زیر منو</label>
                                <input type="color" name="color" class="form-control" value="<?= read_fild(null, 'tbl_menu_color') ?>">
                            </div>
                            <div class="form-group">
                                <label>رنگ زیر منو متن</label>
                                <input type="color" name="textcolor" class="form-control" value="<?= read_fild(null, 'tbl_menu_textcolor') ?>">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[85] ?></label>
                                <input type="text" name="order_by" class="form-control" value="<?= read_fild(NULL, 'tbl_menu_order') ?>">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="update" value="<?= $lang[20] ?>" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                    <?php
                       
                    }
                    if (isset($do) && $do == 'update') {
                    if (isset($_POST['update'])) {
                        $cat_id = $_POST['cat_id'];
                        $code = $_POST['code'];
                        $color = $_POST['color'];
                        $order = (!empty($_POST['order_by']) ? $_POST['order_by'] : '0');
                            $discount = (!empty($_POST['discount']) ? $_POST['discount'] : '0');
                            $style = (!empty($_POST['style']) ? $_POST['style'] : '0');
                        $textcolor = $_POST['textcolor'];
                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = $_POST['title_' . $language[$i]];
                            $link_[$i] = $_POST['link_' . $language[$i]];
                            if (empty($title_[1])) {
                                $_SESSION['error'] = $lang[56];
                                header("Location: menu_manager.php");
                                exit();
                            }
                            $language_name = $language[$i];
                            // update query
                            Query("Update tbl_menu SET tbl_menu_textcolor='$textcolor',tbl_menu_color='$color',tbl_menu_style='$style',tbl_menu_discount='$discount',tbl_menu_parentid= '$cat_id' , tbl_menu_title = '$title_[$i]' , tbl_menu_link = '$link_[$i]' , tbl_menu_order = '$order' where tbl_menu_code= '$code' and tbl_menu_lng='$language[$i]'");
                            $_SESSION['error'] = $lang[21];
                            header("Location: menu_manager.php");
                        }
                    }
                }
                    // search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: menu_manager.php?keyword=$keyword_search");
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo '<script defer src="asset/js/listing-ui.js"></script>';
    include './footer.inc.php';