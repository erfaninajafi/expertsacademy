<?php
include './includer.php';
$pagetitle = $lang[80];
$show_per_page = '10';
$Tabpage = '6';
$thisaccess = '1,3';
include './header.inc.php';

$result = Query("SELECT * From tbl_menu Where tbl_menu_lng='$language_default' ");
// start count db result for pagination               
$count_result = mysqli_num_rows($result);
$start_contnet =  ($page - 1) * $show_per_page;
$page_number = ceil($count_result / $show_per_page);
// end count db result for pagination 

$result = Query("SELECT * From tbl_menu Where tbl_menu_lng='$language_default'  ORDER BY tbl_menu_order DESC ");
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(!isset($do) ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary m-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?=(!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">
                       
                    </td>
                    </tr>
                </table>
                <?php
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        $result = Query("SELECT * From tbl_menu Where tbl_menu_parentid = '0' and tbl_menu_lng='$language_default' ORDER BY tbl_menu_order DESC ");
                        // check parent

                        while ($read = mysqli_fetch_array($result)) {
                            // parent menu code
                            $code = $read['tbl_menu_code'];
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-6" style="line-height:34px;" >';
                            echo $read['tbl_menu_title'] . ($read['tbl_menu_style'] == 1 ? '<span style="color:green;">[img]</span>' : '')
                                . '<a href="?code=' . $read['tbl_menu_code'] . '&do=edit" class="btn m-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>'
                                . '<a href="?code=' . $read['tbl_menu_code'] . '&do=delete" class="btn m-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>';
                            // read sub menu 
                            $result_sub = Query("Select * from tbl_menu Where tbl_menu_parentid = '$code' and tbl_menu_lng = '$language_default' ");
                            $row = mysqli_num_rows($result_sub);
                            if ($row !== 0) {
                                while ($read =  mysqli_fetch_array($result_sub)) {
                                    echo '<br>|_________' . $read['tbl_menu_title'] . ($read['tbl_menu_style'] == 1 ? '<span style="color:green;">[img]</span>' : '') . '
                                    <a href="?code=' . $read['tbl_menu_code'] . '&do=edit" class="btn m-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                    <a href="?code=' . $read['tbl_menu_code'] . '&do=delete" class="btn m-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>';
                                    // sub menu code
                                    $code = $read['tbl_menu_code'];
                                    // read sub sub menu
                                    $result_sub_sub = Query("Select * from tbl_menu Where tbl_menu_parentid = '$code' and tbl_menu_lng = '$language_default' ");
                                    $row = mysqli_num_rows($result_sub_sub);
                                    if ($row !== 0) {
                                        while ($read =  mysqli_fetch_array($result_sub_sub)) {
                                            echo '<br>|_____________________' . $read['tbl_menu_title'] . ($read['tbl_menu_style'] == 1 ? '<span style="color:green;">[img]</span>' : '') . '
                                            <a href="?code=' . $read['tbl_menu_code'] . '&do=edit" class="btn m-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                            <a href="?code=' . $read['tbl_menu_code'] . '&do=delete" class="btn m-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>';
                                        }
                                    }
                                }
                            }

                            echo ' </td></tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
                    }
                    // start = add item
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6 col-12 p-3">
                          <form action="menu_manager.php?do=additem" method="POST" enctype="multipart/form-data">
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >';

                        echo '<div class="form-group">
                           <label>' . $lang[68] . '</label><div>
                           <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="form-control select2" ><option value="0">' . $lang[84] . '</option>';
                        // read menu
                        while ($read_cat =  mysqli_fetch_array($result)) {
                            $parnetid = $read_cat['tbl_menu_parentid'];
                            if ($parnetid == '0') {
                                echo '<option value="' . $read_cat['tbl_menu_code'] . '" class="group-cat">' . $read_cat['tbl_menu_title'] . '</option>';

                                $result_cat = Query("SELECT * From tbl_menu Where tbl_menu_parentid='$read_cat[tbl_menu_code]' and tbl_menu_lng='$language_default' ORDER BY tbl_menu_id DESC ");
                                while ($read_sub = mysqli_fetch_array($result_cat)) {
                                    echo '<option value="' . $read_sub['tbl_menu_code'] . '" class="group-sub">' . $read_sub['tbl_menu_title'] . ' ________</option>';
                                }
                            }
                        }
                        echo '</select></div></div><hr>';

                        // end read menu
                        // add title
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        // add link                   
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[83] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="link_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        echo '<div class="form-group">
                            <label>'.$lang[1463].'</label>
                            <input type="text" name="font" class="form-control">
                            </div>
                            <div class="form-group">
                            <label>' . $lang[85] . '</label>
                            <input type="text" name="order_by" class="form-control">
                            </div><hr>
                            <div class="form-group check-style">
                            <label><input type="checkbox" name="blank" value="1"> '.$lang[1464].'
                            </label>
                            </div><hr>
                            <div class="form-group">
                            <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                            </div></form></div>';
                    }


                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $font = $_POST['font'];
                            $blank = $_POST['blank'];
                            $cat_id = $_POST['cat_id'];
                            if (!empty($_POST['order_by'])) {
                                $order = $_POST['order_by'];
                            } else {
                                $order = 0;
                            }
                            $discount = $_POST['discount'];
                            $style = $_POST['style'];

                            if ($_FILES['img']['name']) {
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
                                $title_[$i] = SafeInput($_POST['title_' . $language[$i]]);
                                $link_[$i] = $_POST['link_' . $language[$i]];

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: menu_manager.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // insert query
                                Query("INSERT INTO tbl_menu (tbl_menu_blank,tbl_menu_code,tbl_menu_title,tbl_menu_parentid,tbl_menu_link,tbl_menu_order,tbl_menu_lng,tbl_menu_discount,tbl_menu_style,tbl_menu_img,tbl_menu_font) values('$blank','$code','$title_[$i]','$cat_id','$link_[$i]','$order','$language_name','$discount','$style','$img','$font')");
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
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_menu_title'].'</h3>.';
                        echo '<form action="#" method="POST">
                                <input type="hidden" name="code" value="' . $code . '" >
                                <div class="form-group margin-top">
                                <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                                <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3">
                                </form></div>';

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
                        $result_style = Query("SELECT * FROM tbl_menu Where tbl_menu_code =  '$code' and tbl_menu_lng='$language_default' ");
                        $result_style_read = mysqli_fetch_array($result_style);
                        $parent_id_style = $result_style_read['tbl_menu_parentid'];

                        //end style selected item

                        echo '<div class="col-lg-6 col-12 p-3">
                            <form action="menu_manager.php?&do=edit" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >';
                                    echo '<div class="form-group">
                            <label>' . $lang[68] . '</label><div>
                            <select name="cat_id"  data-placeholder="' . $lang[70] . '" style="width:100%;" class="form-control select2" ><option value="0">' . $lang[69] . '</option>';
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
                                $result_cat = Query("SELECT * From tbl_menu Where tbl_menu_parentid='$read_cat[tbl_menu_code]' and tbl_menu_lng='$language_default' ORDER BY tbl_menu_id DESC ");
                                while ($read_sub = mysqli_fetch_array($result_cat)) {
                                    echo '<option ' . ($parent_id_style == $read_sub['tbl_menu_code'] ? 'selected' : '') . ' value="' . $read_sub['tbl_menu_code'] . '" class="group-sub">' . $read_sub['tbl_menu_title'] . ' ________</option>';
                                }
                            }
                        }
                        echo '</select></div></div><hr>';
                        // end read menu
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . @read_fild($language[$i], 'tbl_menu_title') . '">
                              </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[83] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="link_' . $language[$i] . '" class="form-control" value="' . @read_fild($language[$i], 'tbl_menu_link') . '">
                              </div>';
                        }
                        echo '<div class="form-group">
                        <label>'.$lang[1463].'</label>
                        <input type="text" name="font" class="form-control" value="' . read_fild(NULL, 'tbl_menu_font') . '">
                        </div><div class="form-group">
                        <label>' . $lang[85] . '</label>
                        <input type="text" name="order_by" class="form-control" value="' . read_fild(NULL, 'tbl_menu_order') . '">
                        </div>
                        <hr><div class="form-group check-style">
                        <label><input type="checkbox" name="blank" value="1" ' . (read_fild(NULL, 'tbl_menu_blank') == 1 ? 'checked' : '') . '>'.$lang[1464].'
                        </label>
                        </div><hr>
                        <div class="form-group">
                        <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                        </div></form></div>';
                        if (isset($_POST['update'])) {
                            $cat_id = $_POST['cat_id'];
                            $code = $_POST['code'];
                            $font = $_POST['font'];
                            $blank = $_POST['blank'];
                            if (!empty($_POST['order_by'])) {
                                $order = $_POST['order_by'];
                            } else {
                                $order = 0;
                            }
                            $discount = $_POST['discount'];
                            $style = $_POST['style'];
                            $img_old = $_POST['img_old'];

                            if ($_FILES['img']['name'] == "") {
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
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $link_[$i] = $_POST['link_' . $language[$i]];
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: menu_manager.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // update query
                                Query("Update tbl_menu SET tbl_menu_blank = '$blank',tbl_menu_font = '$font',tbl_menu_img = '$img_name',tbl_menu_style='$style',tbl_menu_discount='$discount',tbl_menu_parentid= '$cat_id' , tbl_menu_title = '$title_[$i]' , tbl_menu_link = '$link_[$i]' , tbl_menu_order = '$order' where tbl_menu_code= '$code' and tbl_menu_lng='$language[$i]'");
                                $_SESSION['error'] = $lang[21];
                                header("Location: menu_manager.php");
                            }
                        }
                    }
                    //delete image item
                    if (isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_menu  Where tbl_menu_code = '$id' ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-6 ">' . $lang[25] . '<br /><img style="width:35%; height:35%;" src="' . $directory . $img . '" ><br />
         <form action=" " method="POST">
         <div class="form-group margin-top">
         <input type="hidden" name="img" value="' . $img . '" >
         <input type="hidden" value="' . $id . '" name="id" >
         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success">
         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $id = $_POST['id'];
                            // read old image record
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_menu SET tbl_menu_img = '' Where tbl_menu_code = '$id' ");
                            header("Location: menu_manager.php?code=$id&do=edit");
                        } elseif (isset($_POST['nodelete'])) {
                            header("Location: menu_manager.php?code=$id&do=edit");
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
</div>
<?php
include './footer.inc.php';

?>