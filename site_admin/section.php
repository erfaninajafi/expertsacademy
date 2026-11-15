<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// include
include './includer.php';
$img_item = '20';
$show_per_page = '20';
$pagetitle = $section;
$Tabpage = '58';
$cat_manager_id = '0';
$content_section = '1';
$file_section = '1';
$seo_section = '0';
$link_section = '1';
$color_section = '1';
$position_section = '0';
$t = 58;

include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    // list content
                    if (!isset($do) && $section == 'شعار بالای فوتر') {

                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtextone" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld1_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld1') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن دکمه<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld2_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld2') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>لینک<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld3_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld3') : '') ?>">
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }
                    if (isset($do) && $do == 'addtextone') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $fld1_[$i] = addslashes($_POST['fld1_' . $language[$i]]);
                                $fld2_[$i] = addslashes($_POST['fld2_' . $language[$i]]);
                                $fld3_[$i] = addslashes($_POST['fld3_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_fld1='$fld1_[$i]',
                                    tbl_textsection_fld2='$fld2_[$i]',
                                    tbl_textsection_fld3='$fld3_[$i]'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_fld1,
                                tbl_textsection_fld2,
                                tbl_textsection_fld3,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$fld1_[$i]',
                                    '$fld2_[$i]',
                                    '$fld3_[$i]',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }



                    if (!isset($do) && $section == 'مدل نقشه') {

                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtexttwo" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>زیر عنوان<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="undertitle_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_undertitle') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld1_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld1') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن دکمه<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld2_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld2') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>لینک<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld3_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld3') : '') ?>">
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }
                    if (isset($do) && $do == 'addtexttwo') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $undertitle_[$i] = addslashes($_POST['undertitle_' . $language[$i]]);
                                $fld1_[$i] = addslashes($_POST['fld1_' . $language[$i]]);
                                $fld2_[$i] = addslashes($_POST['fld2_' . $language[$i]]);
                                $fld3_[$i] = addslashes($_POST['fld3_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_undertitle='$undertitle_[$i]',
                                    tbl_textsection_fld1='$fld1_[$i]',
                                    tbl_textsection_fld2='$fld2_[$i]',
                                    tbl_textsection_fld3='$fld3_[$i]'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_undertitle,
                                tbl_textsection_fld1,
                                tbl_textsection_fld2,
                                tbl_textsection_fld3,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$undertitle_[$i]',
                                    '$fld1_[$i]',
                                    '$fld2_[$i]',
                                    '$fld3_[$i]',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }


                    if (!isset($do) && ($section == 'مدل کاستومایز یک عنوان' || $section == 'مدل کاستومایز دوم عنوان' || $section == 'مدل کاستومایز سوم عنوان' || $section == 'مدل کاستومایز چهارم عنوان')) {
                        $img_item = 3;
                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                if ($language_id == NULL) {
                                    $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  ");
                                } else {
                                    $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                }

                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtextfive" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>زیر عنوان<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="undertitle_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_undertitle') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>عنوان کاتالوگ یک<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld1_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld1') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>عنوان کاتالوگ دو<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld2_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld2') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن کاتالوگ یک<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld3_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld3') : '') ?>">
                                    </div>
                                <?php
                                }
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>متن کاتالوگ دو<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="fld4_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld4') : '') ?>">
                                    </div>
                                    <?php
                                }
                                ?>
                                <h6 class="cms-section-heading">تصاویر و فایل‌ها</h6>
                                <?php
                                if ($row > 0) {
                                    echo '<input type="hidden" name="allimages" value="' . ($row > 0  ? read_fild($code, $section, NULL, 'tbl_textsection_img') : '') . '">';
                                    for ($i = 1; $i <= $img_item; $i++) {

                                        @$exp = explode(",", read_fild($code, $section, NULL, 'tbl_textsection_img'));
                                        if ($exp[$i - 1]) {
                                    ?>
                                            <div class="form-group">
                                                <table>
                                                    <tr>
                                                        <td><label class="col-sm-12 control-label"><?= ($i == '1' ? 'تصویر'  : 'فایل کاتالوگ') . '&nbsp; ' . $i ?></label></td>
                                                        <td><input type="file" name="img_<?= $i ?>" class="form-control"></td>
                                                        <td><a href="../upload_site/<?= $exp[$i - 1] ?>" target="_blank">
                                                                <span class="btn btn-xs btn-success">
                                                                    <span class="bx bxs-show  btn-xs"></span>
                                                                </span></a>
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
                                                        <td><label class="col-sm-12 control-label"><?= ($i == '1' ? 'تصویر'  : 'فایل کاتالوگ') . '&nbsp; ' . $i ?></label></td>
                                                        <td><input type="file" name="img_<?= $i ?>" class="form-control">
                                                        </td>
                                                    </tr>
                                                </table>
                                            </div>
                                        <?php
                                        }
                                    }
                                } else {
                                    for ($i = 1; $i <= $img_item; $i++) {
                                        ?>
                                        <div class="form-group">
                                            <table>
                                                <tr>
                                                    <td><label class="col-sm-12 control-label"><?= ($i == '1' ? 'تصویر'  : 'فایل کاتالوگ') . '&nbsp; ' . $i ?></label></td>
                                                    <td><input type="file" name="img_<?= $i ?>" class="form-control">
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                <?php
                                    }
                                }

                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }

                    if (isset($do) && $do == 'addtextfive') {
                        $img_item = 3;
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            $allimages = (!empty($_POST['allimages']) ? $_POST['allimages'] : ',,,,');
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
                                $undertitle_[$i] = addslashes($_POST['undertitle_' . $language[$i]]);
                                $fld1_[$i] = addslashes($_POST['fld1_' . $language[$i]]);
                                $fld2_[$i] = addslashes($_POST['fld2_' . $language[$i]]);
                                $fld3_[$i] = addslashes($_POST['fld3_' . $language[$i]]);
                                $fld4_[$i] = addslashes($_POST['fld4_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_undertitle='$undertitle_[$i]',
                                    tbl_textsection_fld1='$fld1_[$i]',
                                    tbl_textsection_fld2='$fld2_[$i]',
                                    tbl_textsection_fld3='$fld3_[$i]',
                                    tbl_textsection_fld4='$fld4_[$i]',
                                    tbl_textsection_img='$images_name'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_undertitle,
                                tbl_textsection_fld1,
                                tbl_textsection_fld2,
                                tbl_textsection_fld3,
                                tbl_textsection_fld4,
                                tbl_textsection_img,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$undertitle_[$i]',
                                    '$fld1_[$i]',
                                    '$fld2_[$i]',
                                     '$fld3_[$i]',
                                    '$fld4_[$i]',
                                    '$images_name',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }







                    if (!isset($do) && $section == 'تصویر بالای صفحه') {
                        $img_item = 2;
                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                if ($language_id == NULL) {
                                    $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  ");
                                } else {
                                    $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                }

                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtextfour" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>لینک<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                    <?php
                                }
                                ?>
                                <h6 class="cms-section-heading">تصاویر و فایل‌ها</h6>
                                <?php
                                if ($row > 0) {
                                    echo '<input type="hidden" name="allimages" value="' . ($row > 0  ? read_fild($code, $section, NULL, 'tbl_textsection_img') : '') . '">';
                                    for ($i = 1; $i <= $img_item; $i++) {

                                        @$exp = explode(",", read_fild($code, $section, NULL, 'tbl_textsection_img'));
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
                                } else {
                                    for ($i = 1; $i <= $img_item; $i++) {
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

                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }

                    if (isset($do) && $do == 'addtextfour') {
                        $img_item = 2;
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            $allimages = (!empty($_POST['allimages']) ? $_POST['allimages'] : ',,,,');
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
                                $undertitle_[$i] = addslashes($_POST['undertitle_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_img='$images_name'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_img,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$images_name',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }


                    if (!isset($do) && ($section == 'تایتل مدل یک' || $section == 'تایتل مدل دو' || $section == 'تایتل مدل سه' || $section == 'تایتل مدل چهار')) {

                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtextthree" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label>زیر عنوان<span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="undertitle_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_undertitle') : '') ?>">
                                    </div>
                                <?php
                                }
                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }
                    if (isset($do) && $do == 'addtextthree') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $undertitle_[$i] = addslashes($_POST['undertitle_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_undertitle='$undertitle_[$i]'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_undertitle,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$undertitle_[$i]',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }



                    if (!isset($do) && ($section == 'صفحه خام')) {

                        $results = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$code' ");
                        $row = mysqli_num_rows($results);
                        if ($row > 0) {
                            function read_fild($code, $section, $language_id = NULL, $field = NULL)
                            {
                                $result = Query("SELECT * FROM tbl_textsection Where tbl_textsection_section =  '$section' and tbl_textsection_parent =  '$code'  and tbl_textsection_lng = '$language_id' ");
                                $read = mysqli_fetch_array($result);
                                $read = $read[$field];
                                return $read;
                            }
                        }

                    ?>
                        <div class="col-lg-10 cms-editor-wrap">
                            <form action="?do=addtextfour" method="POST" enctype="multipart/form-data" class="cms-editor-form">
                                <input type="hidden" name="code" value="<?= Code("", "no", 7) ?>">
                                <input type="hidden" name="typeid" value="<?= $t ?>">
                                <input type="hidden" name="section" value="<?= $section ?>">
                                <input type="hidden" name="parent" value="<?= $code ?>">
                                <h6 class="cms-section-heading">محتوای این بخش در زبان‌های مختلف</h6>
                                <?php
                                // end = if need cat manager show  
                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>
                                    <div class="form-group">
                                        <label><?= $lang[50] ?><span class="language"><?= $language[$i] ?></span></label>
                                        <input type="text" name="title_<?= $language[$i] ?>" class="form-control" value="<?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_title') : '') ?>">
                                    </div>
                                <?php
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                ?>

                                    <div class="form-group">
                                        <label>توضیحات صفحه<span class="language"><?= $language[$i] ?></span></label>
                                        <textarea style="height: 120px;" name="discription_<?= $language[$i] ?>" class=" editor editor_<?= $language[$i] ?>" rows="5"><?= ($row > 0 ? read_fild($code, $section, $language[$i], 'tbl_textsection_fld4') : '') ?></textarea>
                                    </div>

                                <?php
                                }
                                ?>
                                <div class="cms-save-bar">
                                    <input type="submit" name="additem" value="بروز رسانی" class="btn btn-primary">
                                    <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                </div>
                            </form>
                        </div>
                    <?php
                    }
                    if (isset($do) && $do == 'addtextfour') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $tid = $_POST['typeid'];
                            $section = $_POST['section'];
                            $parent = $_POST['parent'];


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $discription_[$i] = addslashes($_POST['discription_' . $language[$i]]);
                                $language_name = $language[$i];
                                $result = Query("select * from tbl_textsection where tbl_textsection_section ='$section' and tbl_textsection_parent ='$parent' and tbl_textsection_lng='$language_name' ");
                                $row = mysqli_num_rows($result);
                                if ($row > 0) {
                                    Query("update tbl_textsection set 
                                    tbl_textsection_title='$title_[$i]',
                                    tbl_textsection_fld4='$discription_[$i]'
                                    where 
                                    tbl_textsection_section='$section' and
                                    tbl_textsection_lng ='$language_name' and
                                    tbl_textsection_parent ='$parent' ");
                                } else {
                                    Query("INSERT INTO tbl_textsection (
                                tbl_textsection_type,
                                tbl_textsection_code,
                                tbl_textsection_title,
                                tbl_textsection_fld4,
                                tbl_textsection_section,
                                tbl_textsection_lng,
                                tbl_textsection_parent) 
                                values(
                                    '$tid',
                                    '$code',
                                    '$title_[$i]',
                                    '$discription_[$i]',
                                    '$section',
                                    '$language_name',
                                    '$parent')");
                                }
                                $_SESSION['error'] = $lang[32];
                                header("Location: section.php?code=$parent&section=$section");
                            }
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo '<script src="asset/js/cms-editor-ui.js"></script>';
include './footer.inc.php';
