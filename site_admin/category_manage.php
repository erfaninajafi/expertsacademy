<?php

// include
include './includer.php';
$show_per_page = '30';

//type id 
if (isset($t) && $t == '1') {
    $pagetitle = 'گنجینه مهارت ها و دوره ها';
    $Tabpage = '11';
    $seo_section = '0';
    $link_section = '0';
    $file_section = '0';
    $design_section = '0';
    $parent_section = '0';
}
if (isset($t) && $t == '3') {
    $pagetitle = 'گنجینه مهارت ها و دوره ها';
    $Tabpage = '25';
    $seo_section = '0';
    $link_section = '0';
    $file_section = '0';
    $design_section = '0';
    $parent_section = '0';
}
if (isset($t) && $t == '4') {
    $pagetitle = 'طراحی صفحات';
    $seo_section = '1';
    $link_section = '1';
    $file_section = '0';
    $Tabpage = '58';
    $design_section = '1';
    $parent_section = '0';
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
                    $keyword = is_string($keyword) ? $keyword : '';
                    $search_connection = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
                    mysqli_set_charset($search_connection, 'utf8');
                    $search_keyword = mysqli_real_escape_string($search_connection, $keyword);
                    mysqli_close($search_connection);
                    $parent_filter = trim($keyword) === '' ? "and tbl_categories_parentid = '0'" : '';
                    $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' $parent_filter and tbl_categories_title LIKE '%$search_keyword%' ORDER BY tbl_categories_order ASC");
                    $count_result = mysqli_num_rows($result);
                    $page_number = max(1, (int) ceil($count_result / $show_per_page));
                    $page = max(1, min((int) $page, $page_number));
                    $start_contnet = ($page - 1) * $show_per_page;
                    if (isset($t) && !isset($do)) {
                    ?>
                        <div class="form-group">
                            <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                        </div>
                        <div class="admin-list-toolbar">
                            <a class="btn btn-primary" href="?t=<?= (int) $t ?>&do=add"><?= $lang[67] ?></a>
                            <form action="category_manage.php?t=<?= (int) $t ?>&do=search" method="POST" class="admin-list-toolbar__field" role="search">
                                <label for="search_title">جست‌وجو در دسته‌بندی‌ها</label>
                                <div class="admin-list-search-controls">
                                    <input type="search" class="form-control" id="search_title" name="keyword"
                                        value="<?= htmlspecialchars($keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="عنوان دسته‌بندی">
                                    <button type="submit" name="go" class="btn btn-primary"><?= $lang[77] ?></button>
                                </div>
                            </form>
                            <p class="admin-list-count" data-admin-list-count aria-live="polite">0 مورد</p>
                        </div>
                        <?php
                        $result = Query("SELECT * From tbl_categories Where tbl_categories_type = '$t' and tbl_categories_lng='$language_default' $parent_filter and tbl_categories_title LIKE '%$search_keyword%' ORDER BY tbl_categories_order ASC LIMIT $start_contnet,$show_per_page");

                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
                        }
                        $allfield = '';
                        if ($row > 0) {
                        ?>
                        <form method="POST" action="?do=runupdate">
                        <table class="table table-bordered table-hover table-striped margin-top" data-admin-list="categories">
                                <thead class="table-dark">
                                    <tr>
                                        <th><?= $lang[60] ?></th>
                                        <th><?= $lang[50] ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;  // for record number
                                    while ($read = mysqli_fetch_array($result)) {
                                        // parent menu code
                                        $id = $read['tbl_categories_id'];
                                        $allfield .= $read['tbl_categories_code'] . ',';

                                        $resultEn = Query("SELECT * From tbl_categories Where tbl_categories_code = '$read[tbl_categories_code]' and tbl_categories_lng='en'  ");
                                        $readEn = mysqli_fetch_array($resultEn);

                                    ?>
                                        <tr>
                                            <td style="width: 1%"><?= toPersianNum($i) ?></td>
                                            <td class="col-sm-6">
                                                <input type="hidden" name="code[]" value="<?= $read['tbl_categories_code'] ?>">
                                                <input type="text" class="form-control" name="o<?= $read['tbl_categories_code'] ?>" value="<?= $read['tbl_categories_order'] ?>" style="width: 40px;float: right;margin-left: 4px;height: 25px;padding: 2px;text-align: center;">
                                                <strong class="cms-page-row__title"><?= htmlspecialchars((string) $read['tbl_categories_title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                                <?php if ($read['tbl_categories_index'] == '1') : ?><span class="admin-list-badge admin-list-badge--reviewed">صفحه اصلی</span><?php endif; ?>
                                                <?php if ($t == '4') : ?>
                                                    <a class="btn btn-xs btn-primary" href="page_builder.php?code=<?= rawurlencode((string) $read['tbl_categories_code']) ?>">باز کردن صفحه‌ساز</a>
                                                <?php endif; ?>
                                                <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=edit" class="btn btn-xs btn-warning" style="font-size:8px;"><?= $lang[23] ?></a>
                                                <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;"><?= $lang[24] ?></a>
                                                <a href="<?= ($read['tbl_categories_index'] == '1' ? '../fa/' : '../fa/' . $read['tbl_categories_url'] . '/') ?>" target="_blank" class="btn btn-xs btn-success" style="font-size:8px;">لینک فارسی</a>
                                                <a href="<?= ($read['tbl_categories_index'] == '1' ? '../en/' : '../en/' . ($readEn['tbl_categories_url'] ?? '') . '/') ?>" target="_blank" class="btn btn-xs btn-success" style="font-size:8px;">لینک انگلیسی</a>
                                                <?php


                                                if ($t == '4') {
                                                    echo '<details class="cms-legacy-components"><summary>ویرایش سکشن‌های قدیمی</summary><div class="group-btn">';
                                                    $design = $read['tbl_categories_design'];
                                                    $designList = explode(PHP_EOL, $design);
                                                    foreach ($designList as $key => $value) {
                                                        if (!empty($value)) {
                                                            $value = trim($value);
                                                            if ($value == '[صفحه خام]' || $value == '[مدل کاستومایز چهارم عنوان]' || $value == '[مدل کاستومایز سوم عنوان]' ||  $value == '[مدل کاستومایز یک عنوان]' || $value == '[مدل کاستومایز دوم عنوان]' || $value == '[تصویر بالای صفحه]' || $value == '[شعار بالای فوتر]' || $value == '[مدل نقشه]' || $value == '[تایتل مدل یک]' || $value == '[تایتل مدل دو]' || $value == '[تایتل مدل سه]' || $value == '[تایتل مدل چهار]') {
                                                                echo '<a href="section.php?code=' . $read['tbl_categories_code'] . '&section=' . $value . '" class="btn btn-dark">' . $value . '</a>';
                                                            } else {
                                                                echo '<a href="misctwo.php?catcode=' . $read['tbl_categories_code'] . '&section=' . $value . '" class="btn btn-dark">' . $value . '</a>';
                                                            }
                                                        }
                                                    }
                                                    echo '</div></details>';
                                                }

                                                $result_sub = Query("Select * from tbl_categories Where tbl_categories_parentid = '$id' and tbl_categories_lng = '$language_default' ORDER BY tbl_categories_order DESC");
                                                $row = mysqli_num_rows($result_sub);
                                                if ($row !== 0) {
                                                    while ($read =  mysqli_fetch_array($result_sub)) {
                                                        $allfield .= $read['tbl_categories_code'] . ',';
                                                ?>
                                                        <br>
                                                        <input type="hidden" name="code[]" value="<?= $read['tbl_categories_code'] ?>">
                                                        <input type="text" class="form-control" name="o<?= $read['tbl_categories_code'] ?>" value="<?= $read['tbl_categories_order'] ?>" style="width: 40px;float: right;margin-left: 4px;height: 25px;padding: 2px;text-align: center;">
                                                        <span class="admin-menu-indent admin-menu-indent--one" aria-hidden="true"></span><?= htmlspecialchars((string) $read['tbl_categories_title'], ENT_QUOTES, 'UTF-8') . ($read['tbl_categories_status'] == 1 ? '[مخفی]' : '') ?>
                                                        <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=edit" class="btn btn-xs btn-warning" style="font-size:8px;"><?= $lang[23] ?></a>
                                                        <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;"><?= $lang[24] ?></a>
                                                        <a href="../category/<?= $read['tbl_categories_url'] . '/' ?>" target="_blank" class="btn btn-xs btn-success" style="font-size:8px;">لینک</a>
                                                        <?php
                                                        $id = $read['tbl_categories_code'];
                                                        // read sub sub menu
                                                        $result_sub_sub = Query("Select * from tbl_categories Where tbl_categories_parentid = '$id' and tbl_categories_lng = '$language_default' ORDER BY tbl_categories_order DESC");
                                                        $row = mysqli_num_rows($result_sub_sub);
                                                        if ($row !== 0) {
                                                            while ($read =  mysqli_fetch_array($result_sub_sub)) {
                                                                $allfield .= $read['tbl_categories_code'] . ',';
                                                        ?>
                                                                <br>
                                                                <input type="hidden" name="code[]" value="<?= $read['tbl_categories_code'] ?>">
                                                                <input type="text" class="form-control" name="o<?= $read['tbl_categories_code'] ?>" value="<?= $read['tbl_categories_order'] ?>" style="width: 40px;float: right;margin-left: 4px;height: 25px;padding: 2px;text-align: center;">
                                                                <span class="admin-menu-indent admin-menu-indent--two" aria-hidden="true"></span><?= htmlspecialchars((string) $read['tbl_categories_title'], ENT_QUOTES, 'UTF-8') . ($read['tbl_categories_status'] == 1 ? '[مخفی]' : '') ?>
                                                                <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=edit" class="btn btn-xs btn-warning" style="font-size:8px;"><?= $lang[23] ?></a>
                                                                <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;"><?= $lang[24] ?></a>
                                                                <a href="../category/<?= $read['tbl_categories_url'] . '/' ?>" target="_blank" class="btn btn-xs btn-success" style="font-size:8px;">لینک</a>
                                                                <?php



                                                                $id = $read['tbl_categories_code'];
                                                                // read sub sub menu
                                                                $result_sub_sub_sub = Query("Select * from tbl_categories Where tbl_categories_parentid = '$id' and tbl_categories_lng = '$language_default' ORDER BY tbl_categories_order DESC");
                                                                $row = mysqli_num_rows($result_sub_sub_sub);
                                                                if ($row !== 0) {
                                                                    while ($read =  mysqli_fetch_array($result_sub_sub_sub)) {
                                                                        $allfield .= $read['tbl_categories_code'] . ',';
                                                                ?>
                                                                        <br>
                                                                        <input type="hidden" name="code[]" value="<?= $read['tbl_categories_code'] ?>">
                                                                        <input type="text" class="form-control" name="o<?= $read['tbl_categories_code'] ?>" value="<?= $read['tbl_categories_order'] ?>" style="width: 40px;float: right;margin-left: 4px;height: 25px;padding: 2px;text-align: center;">
                                                                        <span class="admin-menu-indent admin-menu-indent--three" aria-hidden="true"></span><?= htmlspecialchars((string) $read['tbl_categories_title'], ENT_QUOTES, 'UTF-8') . ($read['tbl_categories_status'] == 1 ? '[مخفی]' : '') ?>
                                                                        <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=edit" class="btn btn-xs btn-warning" style="font-size:8px;"><?= $lang[23] ?></a>
                                                                        <a href="?t=<?= $t . '&id=' . $read['tbl_categories_code'] ?>&do=delete" class="btn btn-xs btn-danger" style="font-size:8px;"><?= $lang[24] ?></a>
                                                                        <a href="../category/<?= $read['tbl_categories_url'] . '/' ?>" target="_blank" class="btn btn-xs btn-success" style="font-size:8px;">لینک</a>
                                                <?php
                                                                    }
                                                                }
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
                                <tfoot><tr>
                                    <td colspan="2"><input type="submit" name="submitorder" value="بروز رسانی رده بندی" class="btn btn-success update-btn"></td>
                                </tr></tfoot>
                        </table>
                        <input type="hidden" name="allfield" value="<?= htmlspecialchars($allfield, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="type" value="<?= (int) $t ?>">
                        </form>
                        <?php } ?>
                        <?php
                        // start pagination                
                        if ($page > $page_number) {
                            //echo '<div class="margin-top">' . $lang[57] . '</div>';
                        } else if ($count_result > $show_per_page) {
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
                                            <li><a class="page-link" href="?t=<?= (int) $t . '&page=' . ($back_page_number - 1) . '' . $other_page_keyword . rawurlencode($keyword) ?>"><?= $lang[75] ?></a></li>
                                            <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a></li>
                                            <li><a class="page-link" href="?t=<?= (int) $t . '&page=' . min($page + 1, $page_number) . '' . $other_page_keyword . rawurlencode($keyword) ?>"><?= $lang[74] ?></a></li>
                                        </ul>
                                    </td>
                                    <td class="col-lg-6">
                                        <form action="category_manage.php?t=<?= $t ?>&do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                    }
                    if (isset($do) && $do == 'runupdate') {
                        $code = $_POST['code'];
                        $t = $_POST['type'];
                        foreach ($code as $key => $value) {
                            $val = $_POST['o' . $value];
                            Query("update tbl_categories set tbl_categories_order='$val' Where tbl_categories_code = '$value' ");
                        }
                        $_SESSION['error'] = 'ترتیب دسته‌بندی‌ها به‌روزرسانی شد.';
                        header("Location: category_manage.php?t=$t");
                    }

                    if (isset($t) && isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = max(1, (int) $_POST['page_number']);
                            header("Location: category_manage.php?t=$t&page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = add item
                    if (isset($do) && $do == 'add') {
                        ?>
                        <div class="col-lg-8 cms-editor-wrap">
                            <form action="category_manage.php?t=<?= $t ?>&do=additem" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>"><input type="hidden" name="cat_id" value="0">
                                <?php if ($t == '4') : ?><p class="cms-page-form-note">ابتدا اطلاعات صفحه را ذخیره کنید؛ سپس از فهرست صفحات، صفحه‌ساز را برای چیدمان و محتوای سکشن‌ها باز کنید.</p><?php endif; ?>
                                <h6 class="cms-section-heading">عنوان و دسته‌بندی</h6>
                                <?php
                                if ($parent_section == '1') {
                                    if ($t != 3) {
                                ?>
                                        <div class="form-group">
                                            <label><?= $lang[68] ?></label>
                                            <div>
                                                <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="form-control select2">
                                                    <option value="0"><?= $lang[69] ?></option>
                                                    <?php
                                                    while ($read_cat =  mysqli_fetch_array($result)) {
                                                        $parnetid = $read_cat['tbl_categories_parentid'];
                                                        if ($parnetid == '0') {
                                                            echo '<option value="' . $read_cat['tbl_categories_id'] . '" class="group-cat">________' . $read_cat['tbl_categories_title'] . '</option>';
                                                            // read sub category                   
                                                            $parentcode = $read_cat['tbl_categories_id'];
                                                            $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                            while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                                echo '<option value="' . $read_sub['tbl_categories_id'] . '" class="group-sub">________________________' . $read_sub['tbl_categories_title'] . '</option>';

                                                                $parentcode = $read_sub['tbl_categories_id'];
                                                                $result_sub_sub = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                                while ($read_sub_sub = mysqli_fetch_array($result_sub_sub)) {
                                                                    echo '<option value="' . $read_sub_sub['tbl_categories_id'] . '" class="group-sub">___________________________________' . $read_sub_sub['tbl_categories_title'] . '</option>';
                                                                }
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
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control">
                                    </div>
                                    <?php
                                }
                                if ($seo_section == '1') {
                                    echo '<h6 class="cms-section-heading">اطلاعات نمایش در جست‌وجو</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                        <div class="form-group">
                                            <label>کلمات کلیدی<span class="language"><?= $language[$i] ?></span></label>
                                            <textarea name="keyword_<?= $language[$i] ?>" class="form-control textarea-height" rows="3"></textarea>
                                        </div>
                                    <?php
                                    }
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                        <div class="form-group">
                                            <label>توضیحات صفحه<span class="language"><?= $language[$i] ?></span></label>
                                            <textarea name="discription_<?= $language[$i] ?>" class="form-control textarea-height" rows="3"></textarea>
                                        </div>
                                    <?php
                                    }
                                }
                                if ($design_section == '1') {
                                    echo '<h6 class="cms-section-heading">چیدمان صفحه</h6>';
                                    echo '<details class="cms-legacy-design"><summary>چیدمان قدیمی با نشانگرها</summary><p>برای صفحه‌های قدیمی، نشانگرها را در هر زبان جداگانه ویرایش کنید. برای نمونه‌های مستقل، پس از ذخیره صفحه‌ساز را باز کنید.</p>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                        <div class="design">
                                            <div>[اسلایدر]</div>
                                            <div>[اسلایدر دایره]</div>
                                            <div>[اسلایدر باکس]</div>
                                            <div>[اسلایدر باکس لینکی]</div>
                                            <div>[اسلایدر تصویر]</div>
                                            <div>[اسلایدر محتوی]</div>
                                            <hr>
                                            <div>[مدل نقشه]</div>
                                            <div>[شعار بالای فوتر]</div>
                                            <div>[لوگو تولتیپ]</div>
                                            <div>[لوگو بدون تولتیپ]</div>
                                            <div>[سوالات متداول]</div>
                                            <div>[فرم همکاری سوالات]</div>
                                            <div>[سوالات متداول دوم]</div>
                                            <div>[پیوستن به ما]</div>
                                            <div>[لیست زیر هم]</div>
                                            <div>[لیست کنار هم]</div>
                                            <div>[لیست کنار هم دوم]</div>
                                            <div>[محتوی تب]</div>
                                            <div>[باکس میانی]</div>
                                            <div>[باکس میانی دو]</div>
                                            <div>[باکس میانی سوم]</div>
                                            <div>[محتوی تکی]</div>
                                            <div>[محتوی چپ و راست]</div>
                                            <div>[محتوی چپ و راست دوم]</div>
                                            <div>[تصویر بالای صفحه]</div>
                                            <div>[صفحه خام]</div>
                                            <div>[فرم تماس با ما]</div>
                                            <hr>
                                            <div>[تایتل مدل یک]</div>
                                            <div>[تایتل مدل دو]</div>
                                            <div>[تایتل مدل سه]</div>
                                            <div>[تایتل مدل چهار]</div>
                                            <hr>
                                            <div>[مدل کاستومایز یک عنوان]</div>
                                            <div>[مدل کاستومایز یک لیست]</div>
                                            <div>[مدل کاستومایز دوم عنوان]</div>
                                            <div>[مدل کاستومایز دوم لیست]</div>
                                            <hr>
                                            <div>[مدل کاستومایز سوم عنوان]</div>
                                            <div>[مدل کاستومایز سوم لیست]</div>
                                            <div>[مدل کاستومایز چهارم عنوان]</div>
                                            <div>[مدل کاستومایز چهارم لیست]</div>

                                        </div>
                                        <div class="form-group">
                                            <label>طراحی صفحه<span class="language"><?= $language[$i] ?></span></label>
                                            <textarea name="design_<?= $language[$i] ?>" class="form-control textarea-height" rows="3"></textarea>
                                        </div>
                                    <?php
                                    }
                                    echo '</details>';
                                }

                                if ($link_section == '1') {
                                    echo '<h6 class="cms-section-heading">آدرس صفحه</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                        <div class="form-group">
                                            <label>لینک نمایش دهنده <span class="language"><?= $language[$i] ?></span>
                                                <div class="checknameresult-<?= $language[$i] ?>"></div>
                                            </label>
                                            <input type="text" name="url_<?= $language[$i] ?>" class="form-control checknamecat" data-lng="<?= $language[$i] ?>" data-code="" data-section="cat" <?= ($i == '1' ? 'required' : '') ?>>
                                        </div>
                                <?php
                                    }
                                }
                                ?>
                                <h6 class="cms-section-heading">انتشار و تصویر</h6>
                                <div class="form-group">
                                    <label>صفحه اصلی<input type="checkbox" name="index" value="1"></label>

                                </div>
                                <?php
                                if ($file_section == '1') {
                                ?>
                                    <hr>
                                    <div class="form-group">
                                        <table>
                                            <tr>
                                                <td>
                                                    <div class="mb-3">
                                                        <input class="form-control" type="file" id="formFile" name="img">
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php } ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }
                    if (isset($t) && isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $cat_id = $_POST['cat_id'];
                            $order = (!empty($_POST['order']) ? $_POST['order'] : '0');
                            $index = (!empty($_POST['index']) ? $_POST['index'] : '0');

                            // image record
                            $img = '';
                            if (isset($_FILES['img']['name']) && $_FILES['img']['name'] != '') {
                                $img = $_FILES['img']['name'];
                                $read_file_name = explode(".", $img);
                                $extn = end($read_file_name);
                                $img = 'cat_' . time() . '.' . $extn;
                                // $images_name = all images after rename array
                                if ($extn == 'svg' || $extn == 'SVG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img);
                                }
                            }
                            // for language
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                @$keyword_[$i] = addslashes($_POST['keyword_' . $language[$i]]);
                                @$discription_[$i] = addslashes($_POST['discription_' . $language[$i]]);
                                $url_[$i] = addslashes($_POST['url_' . $language[$i]]);
                                $design_[$i] = addslashes($_POST['design_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: category_manage.php?t=$t");
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

                                // end upload image
                                $language_name = $language[$i];
                                // insert query
                                Query("INSERT INTO tbl_categories (
                                    tbl_categories_url,
                                    tbl_categories_keyword,
                                    tbl_categories_discription,
                                    tbl_categories_design,
                                    tbl_categories_type,
                                    tbl_categories_code,
                                    tbl_categories_title,
                                    tbl_categories_parentid,
                                    tbl_categories_img,
                                    tbl_categories_lng,
                                    tbl_categories_order,
                                    tbl_categories_index) 
                                values('$url_product_[$i]',
                                '$keyword_[$i]',
                                '$discription_[$i]',
                                '$design_[$i]',
                                '$tid',
                                '$code',
                                '$title_[$i]',
                                '$cat_id',
                                '$img',
                                '$language_name',
                                '$order',
                                '$index')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: category_manage.php?t=$t");
                            }
                        }
                    }
                    // end = add item

                    // start = delete category
                    if (isset($t) && isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_categories Where tbl_categories_id = '$id' and tbl_categories_lng='$language_default' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                        <div class="col-lg-6"><?= $lang[25] ?><br />
                            <form action="#" method="POST">
                                <input type="hidden" name="id" value="<?= $id  ?>">
                                <div class="form-group margin-top">
                                    <input type="submit" name="submit" value="<?= $lang[26]  ?>" class="btn btn-success">
                                    <input type="submit" name="nodelete" value="<?= $lang[27]  ?>" class="btn btn-danger">
                            </form>
                        </div>
                        <?php
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $img = $_POST['img'];
                            // $result = Query("SELECT * from tbl_categories Where tbl_categories_parentid = '$id' and tbl_categories_lng='$language_default' ");
                            // $row_parent = mysqli_num_rows($result);
                            // if ($row_parent > 0) {
                            //     $_SESSION['error'] = $lang[71];
                            //     header("Location: category_manage.php?t=$t");
                            //     exit();
                            // }
                            // $result = Query("SELECT * from tbl_product Where tbl_product_catid like '%$id%' and tbl_product_lng='$language_default' ");
                            // $row_parent = mysqli_num_rows($result);
                            // if ($row_parent > 0) {
                            //     $_SESSION['error'] = $lang[71];
                            //     header("Location: category_manage.php?t=$t");
                            //     exit();
                            // }
                            // if (isset($img)) {
                            //     unlink($directory . $img);
                            // }
                            Query("Delete From tbl_categories Where tbl_categories_code = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: category_manage.php?t=$t");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: category_manage.php?t=$t");
                        }
                    }
                    if (isset($t) && isset($do) && $do == 'edit') {
                        $result = Query("SELECT * From tbl_categories Where tbl_categories_id !='$id' and tbl_categories_parentid = '0' and tbl_categories_type = '$t' and tbl_categories_lng='$language_default'  ORDER BY tbl_categories_order DESC  LIMIT $start_contnet,$show_per_page ");
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_categories_lng = '$language_id'";
                            }
                            $id = $_GET['id'];
                            $result = Query("SELECT * FROM tbl_categories Where tbl_categories_code =  '$id'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        $result_style = Query("SELECT * FROM tbl_categories Where tbl_categories_code =  '$id' ");
                        $result_style_read = mysqli_fetch_array($result_style);
                        $parent_id_style = $result_style_read['tbl_categories_parentid'];
                        ?>
                        <div class="col-lg-6 cms-editor-wrap">
                            <form action="category_manage.php?t=<?= $t ?>&do=updateitem" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= $id ?>"><input type="hidden" name="cat_id" value="0">
                                <?php if ($t == '4') : ?><div class="cms-page-form-note"><a class="btn btn-primary" href="page_builder.php?code=<?= rawurlencode((string) $id) ?>">باز کردن صفحه‌ساز</a><span>ساختار و محتوای نمونه‌های مستقل را در صفحه‌ساز ویرایش کنید.</span></div><?php endif; ?>
                                <h6 class="cms-section-heading">عنوان و دسته‌بندی</h6>
                                <?php
                                if ($parent_section == '1') {
                                    if ($t != 3) {
                                ?>
                                        <div class="form-group"><label><?= $lang[68] ?></label>
                                            <div>
                                                <select name="cat_id" data-placeholder="<?= $lang[70] ?>" style="width:100%;" class="form-control select2">
                                                    <option value="0"><?= $lang[69] ?></option>
                                                    <?php
                                                    while ($read_cat =  mysqli_fetch_array($result)) {
                                                        $parentid = $read_cat['tbl_categories_parentid'];
                                                        if ($parentid == '0') {
                                                            echo '<option ' . ($parent_id_style == $read_cat['tbl_categories_id'] ? 'selected' : '') . ' value="' . $read_cat['tbl_categories_id'] . '" class="group-cat">__' . $read_cat['tbl_categories_title'] . '</option>';
                                                            $parentcode = $read_cat['tbl_categories_id'];
                                                            $result_cat = Query("SELECT * From tbl_categories Where tbl_categories_id !='$id' and tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                            while ($read_sub = mysqli_fetch_array($result_cat)) {
                                                                $parentid_sub = $read_sub['tbl_categories_code'];
                                                                echo '<option ' . ($result_style_read['tbl_categories_parentid'] == $read_sub['tbl_categories_id'] ? 'selected' : '') . ' value="' . $read_sub['tbl_categories_id'] . '" class="group-sub">____________' . $read_sub['tbl_categories_title'] . '</option>';

                                                                $parentcode = $read_sub['tbl_categories_id'];
                                                                $result_sub_sub = Query("SELECT * From tbl_categories Where tbl_categories_parentid='$parentcode' and tbl_categories_lng='$language_default' ORDER BY tbl_categories_id DESC ");
                                                                while ($read_sub_sub = mysqli_fetch_array($result_sub_sub)) {
                                                                    echo '<option ' . ($result_style_read['tbl_categories_parentid'] == $read_sub_sub['tbl_categories_id'] ? 'selected' : '') . ' value="' . $read_sub_sub['tbl_categories_id'] . '" class="group-sub">____________________________________' . $read_sub_sub['tbl_categories_title'] . '</option>';
                                                                }
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <hr>
                                    <?php
                                    }
                                }
                                // end read categories
                                for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= read_fild($language[$i], 'tbl_categories_title') ?>">
                                    </div>
                                    <?php
                                }
                                if ($seo_section == '1') {
                                    echo '<h6 class="cms-section-heading">اطلاعات نمایش در جست‌وجو</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                        echo '<div class="form-group">
                                        <label>کلمات کلیدی<span class="language">' . $language[$i] . '</span></label>
                                        <textarea name="keyword_' . $language[$i] . '" class="form-control textarea-height" rows="5">' . read_fild($language[$i], 'tbl_categories_keyword') . '</textarea>
                                        </div>';
                                    }
                                    for ($i = 1; $i <= $language_count; $i++) {
                                        echo '<div class="form-group">
                                        <label>توضیحات صفحه<span class="language">' . $language[$i] . '</span></label>
                                        <textarea name="discription_' . $language[$i] . '" class="form-control textarea-height" rows="5">' . read_fild($language[$i], 'tbl_categories_discription') . '</textarea>
                                        </div>';
                                    }
                                }
                                if ($design_section == '1') {
                                    echo '<h6 class="cms-section-heading">چیدمان صفحه</h6>';
                                    echo '<details class="cms-legacy-design"><summary>چیدمان قدیمی با نشانگرها</summary><p>این فیلدها برای سازگاری با صفحات قدیمی حفظ شده‌اند. چیدمان جدید را در صفحه‌ساز ویرایش کنید.</p>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                    ?>
                                        <div class="design">
                                            <div>[اسلایدر]</div>
                                            <div>[اسلایدر دایره]</div>
                                            <div>[اسلایدر باکس]</div>
                                            <div>[اسلایدر باکس لینکی]</div>
                                            <div>[اسلایدر تصویر]</div>
                                            <div>[اسلایدر محتوی]</div>
                                            <hr>
                                            <div>[مدل نقشه]</div>
                                            <div>[شعار بالای فوتر]</div>
                                            <div>[لوگو تولتیپ]</div>
                                            <div>[لوگو بدون تولتیپ]</div>
                                            <div>[سوالات متداول]</div>
                                            <div>[فرم همکاری سوالات]</div>
                                            <div>[سوالات متداول دوم]</div>
                                            <div>[پیوستن به ما]</div>
                                            <div>[لیست زیر هم]</div>
                                            <div>[لیست کنار هم]</div>
                                            <div>[لیست کنار هم دوم]</div>
                                            <div>[محتوی تب]</div>
                                            <div>[باکس میانی]</div>
                                            <div>[باکس میانی دو]</div>
                                            <div>[باکس میانی سوم]</div>
                                            <div>[محتوی تکی]</div>
                                            <div>[محتوی چپ و راست]</div>
                                            <div>[محتوی چپ و راست دوم]</div>
                                            <div>[تصویر بالای صفحه]</div>
                                            <div>[صفحه خام]</div>
                                            <div>[فرم تماس با ما]</div>
                                            <hr>
                                            <div>[تایتل مدل یک]</div>
                                            <div>[تایتل مدل دو]</div>
                                            <div>[تایتل مدل سه]</div>
                                            <div>[تایتل مدل چهار]</div>
                                            <hr>
                                            <div>[مدل کاستومایز یک عنوان]</div>
                                            <div>[مدل کاستومایز یک لیست]</div>
                                            <div>[مدل کاستومایز دوم عنوان]</div>
                                            <div>[مدل کاستومایز دوم لیست]</div>
                                            <hr>
                                            <div>[مدل کاستومایز سوم عنوان]</div>
                                            <div>[مدل کاستومایز سوم لیست]</div>
                                            <div>[مدل کاستومایز چهارم عنوان]</div>
                                            <div>[مدل کاستومایز چهارم لیست]</div>
                                        </div>
                                        <div class="form-group">
                                            <label>طراحی صفحه<span class="language"><?= $language[$i] ?></span></label>
                                            <textarea name="design_<?= $language[$i] ?>" class="form-control textarea-height" rows="3"><?= read_fild($language[$i], 'tbl_categories_design') ?></textarea>
                                        </div>
                                <?php
                                    }
                                    echo '</details>';
                                }
                                if ($link_section == '1') {
                                    echo '<h6 class="cms-section-heading">آدرس صفحه</h6>';
                                    for ($i = 1; $i <= $language_count; $i++) {
                                        echo '<div class="form-group">
                                        <label>لینک نمایش دهنده  <span class="language">' . $language[$i] . '</span><div class="checknameresult-' . $language[$i] . '"></div></label>
                                        <input type="text" name="url_' . $language[$i] . '" class="form-control checknamecat"  data-lng="' . $language[$i] . '" data-code="' . read_fild(NULL, 'tbl_categories_code') . '" data-section="cat" ' . ($i == '1' ? 'required' : '') . ' value="' . read_fild($language[$i], 'tbl_categories_url') . '">
                                        </div>';
                                    }
                                }
                                ?>
                                <?php
                                if ($file_section == '1') {
                                    $read_img = read_fild(NULL, 'tbl_categories_img');
                                ?>
                                    <hr>
                                    <div class="form-group">
                                        <table>
                                            <tr>
                                                <td></td>
                                                <td><input type="hidden" name="img_old" value="<?= $read_img ?>">
                                                    <div class="mb-3">
                                                        <input class="form-control" type="file" id="formFile" name="img">
                                                    </div>
                                                </td>
                                                <?php
                                                if (!empty($read_img)) {
                                                ?>
                                                    <td>
                                                        <a href="../upload/<?= $read_img ?>" target="_blank">
                                                            <span class="btn btn-xs btn-success"><span class="bx bxs-show  btn-xs"></span>
                                                            </span></a>
                                                        <a href="?t=<?= $t . '&do=deleteimage&id=' . $id . '&img=' . $read_img ?>">
                                                            <span class="btn btn-xs btn-danger"><span class="bx bxs-trash-alt  btn-xs"></span></span></a>
                                            </tr>
                                        </table>
                                    </div>
                                <?php
                                                } else {
                                ?>
                                    </tr>
                                    </table>
                        </div>
                <?php
                                                }
                                            }
                ?>
                <h6 class="cms-section-heading">انتشار و تصویر</h6>
                <div class="form-group">
                    <label>صفحه اصلی<input type="checkbox" name="index" value="1" <?= (read_fild(NULL, 'tbl_categories_index') == '1' ? 'checked' : '') ?>></label>

                </div>
                <div class="cms-save-bar">
                    <input type="submit" name="update" value="<?= $lang[20] ?>" class="btn btn-primary">
                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                </div>
                </form>
                </div>
            <?php
                    }
                    if (isset($t) && isset($do) && $do == 'updateitem') {
                        if (isset($_POST['update'])) {
                            $cat_id = $_POST['cat_id'];
                            $color = (!empty($_POST['color']) ? $_POST['color'] : '0');
                            $index = (!empty($_POST['index']) ? $_POST['index'] : '0');
                            $code = $_POST['code'];
                            $img_old = @$_POST['img_old'];


                            // image update
                            $img_name = '';
                            if (!isset($_FILES['img']['name'])) {
                                $img_name = $img_old;
                            } else {
                                $img = $_FILES['img']['name'];
                                $read_img_name = explode(".", $img);
                                $extn = end($read_img_name);
                                $img_name = 'img_' . time() . '.' . $extn;
                                if ($extn == 'svg' || $extn == 'SVG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                                    unlink($directory . $img_old);
                                }
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = imjection($_POST['title_' . $language[$i]]);
                                $keyword_[$i] = @addslashes($_POST['keyword_' . $language[$i]]);
                                $discription_[$i] = @addslashes($_POST['discription_' . $language[$i]]);
                                $url_[$i] = addslashes($_POST['url_' . $language[$i]]);
                                $design_[$i] = addslashes($_POST['design_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: category_manage.php?t=$t");
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

                                $language_name = $language[$i];
                                // update query
                                Query("Update tbl_categories SET  
                                tbl_categories_keyword='$keyword_[$i]',
                                tbl_categories_discription='$discription_[$i]',
                                tbl_categories_url='$url_product_[$i]',
                                tbl_categories_design='$design_[$i]',
                                tbl_categories_parentid= '$cat_id' ,
                                tbl_categories_title = '$title_[$i]' , 
                                tbl_categories_index = '$index' , 
                                tbl_categories_img = '$img_name' 
                                where tbl_categories_code= '$code' and tbl_categories_lng='$language[$i]'");
                                $_SESSION['error'] = $lang[21];
                                header("Location: category_manage.php?t=$t");
                            }
                        }
                    }
                    //delete image item
                    if (isset($t) && isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_categories  Where tbl_categories_id = '$id' ");
                        $read = mysqli_fetch_array($result);
            ?>
                <div class="col-lg-6 col-lg-offset-6"><?= $lang[25] ?><br /><img style="width:35%; height:35%;" src="<?= $directory . $img ?>"><br />
                    <form action=" " method="POST">
                        <div class="form-group margin-top">
                            <input type="hidden" name="img" value="<?= $img ?>">
                            <input type="hidden" value="<?= $id ?>" name="id">
                            <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                            <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                    </form>
                </div>
            <?php
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $id = $_POST['id'];
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_categories SET tbl_categories_img = '' Where tbl_categories_id = '$id' ");
                            header("Location: category_manage.php?t=$t&id=$id&do=edit");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: category_manage.php?t=$t&id=$id&do=edit");
                        }
                    }
                    if (isset($t) && isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = trim((string) ($_POST['keyword'] ?? ''));
                            header('Location: category_manage.php?t=' . (int) $t . '&keyword=' . rawurlencode($keyword_search));
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
