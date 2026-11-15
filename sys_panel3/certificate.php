<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'مدیریت گواهی نامه ها  ';
$Tabpage = '6';
$img_item = 10;
$thisaccess = '1,4';
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
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <div class="col-lg-12 padding-clear margin-bottom"><a class="btn btn-primary big_button" href="?do=add" >افزودن نوع گواهی نامه</a></div>';

                        $result = Query("SELECT * From tbl_certificate where tbl_certificate_id !='' and tbl_certificate_lng='$language_default' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_certificate where tbl_certificate_id !='' and tbl_certificate_lng='$language_default' ORDER BY tbl_certificate_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        $allfield = '';
                        echo '  <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>عنوان</th>
                                    <th>مبلغ</th>
                                    <th>مدل مدرک</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {

                            echo '<tr><td style="width: 1%">' . $i . '</td>'
                                . '<td class="col-sm-2">' . $read['tbl_certificate_title'] . '</td>'
                                . '<td class="col-sm-1">' . $read['tbl_certificate_price'] . '</td>
                                <td class="col-sm-1">' . ($read['tbl_certificate_workshop'] == 0 ? 'دوره' : 'کارگاه') . '</td></tr><tr>
                                <td class="col-sm-1 btn-class" colspan="4">'
                                . '<a href="?do=edit&code=' . $read['tbl_certificate_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                                <a href="?do=delete&code=' . $read['tbl_certificate_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table>';

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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="certificate.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: certificate.php?page=$page_number");
                        }
                    }
                    if (isset($do) && $do == 'add') {
                        ?>
                    <div class="col-lg-6">
                        <form action="certificate.php?do=additem" method="POST" enctype="multipart/form-data">
                            <?php
                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                        <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="title_' . $language[$i] . '" class="form-control" '.($i == '1' ? 'required' : '').'>
                                        </div>';
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                        <label>مرجع صدور گواهی نامه<span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="partner_' . $language[$i] . '" class="form-control" >
                                        </div>';
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                    <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                    <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                    </div>';
                                }

                                echo '<div class="form-group"><label>انتخاب مهارت</label><select name="skill" style="width: 100%;"
                                class="select2">';
                            $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4'  and tbl_categories_lng='$language_default'");
                            while ($read_skill = mysqli_fetch_array($result_skill)) {
                                $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
                                $read_memberskill = mysqli_fetch_array($result_memberskill);
                                $skillCode = $read_skill['tbl_categories_code'];
                                echo '<option value="' . $read_skill['tbl_categories_code'] . '" >' . $read_skill['tbl_categories_title'] . '</option>';
                            }
                            echo ' </select></div>';
                                ?>

                            <div class="form-group">
                                <label>تصویر</label>
                                <input type="file" name="img" class="form-control">
                            </div>

                            <!-- <div class="form-group check-style">
                                <label>مبلغ به تومان</label>
                                <input type="text" name="price" class="form-control" required>
                            </div>
                            <div class="form-group check-style">
                                <label>مدرک برای کارگاه<input type="checkbox" name="workshop" value="1"> </label>
                            </div> -->
                            <div class="form-group">
                                <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary">
                            </div>
                        </form>
                    </div>
                    <?php
                    }

                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = Code("", "no", 7);
                            $skill = $_POST['skill'];
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $partner_[$i] = addslashes($_POST['partner_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: certificate.php");
                                    exit();
                                }
                            }

                            $img = $_FILES['img']['name'];
                            $read_file_name = explode(".", $img);
                            $extn = end($read_file_name);
                            // upload images after rename 
                            if (empty($img) && $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'swf' || $extn == 'SWF') {
                                $img_name = 'certificate_' . time() . '.' . $extn;
                                move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                            } else {
                                $img_name = '';
                            }

                            
                            $date = jdate("Y/m/d : A:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                $language_record = $language[$i];
                                Query("INSERT INTO tbl_certificate (
                                    tbl_certificate_title,
                                    tbl_certificate_text,
                                    tbl_certificate_img,
                                    tbl_certificate_skill,
                                    tbl_certificate_partner,
                                    tbl_certificate_lng,
                                    tbl_certificate_code) 
                                    VALUES (
                                        '$title_[$i]',
                                        '$content_[$i]',
                                        '$img_name',
                                        '$skill',
                                        '$partner_[$i]',
                                        '$language_record',
                                        '$code'
                                        )");
                                $_SESSION['error'] = $lang[32];
                                header("Location: certificate.php");
                            }
                        }
                    }
                    // end = add item
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_certificate Where tbl_certificate_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br><br>' . $read['tbl_certificate_title'];
                        echo '<form action="#" method="POST">
                            <input type="hidden" name="id" value="' . $id . '" >
                            <div class="form-group margin-top">
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
                            </form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            Query("Delete From tbl_certificate Where tbl_certificate_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: certificate.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            $class = $_POST['class'];
                            header("Location: certificate.php");
                        }
                    }
                    // edit item start
                    if (isset($do) && $do == 'edit') {
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_certificate_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_certificate Where tbl_certificate_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        echo '<div class="col-lg-6">
                  <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                  <input type="hidden" name="code" value="' . $code . '" >
                  <input type="hidden" name="old_img" value="' . read_fild(NULL, 'tbl_certificate_img') . '" >';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                            <input type="text" name="title_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_certificate_title') . '" '.($i == '1' ? 'required' : '').'>
                            </div>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                <label>مرجع صدور گواهی نامه<span class="language">' . $language[$i] . '</span></label>
                                <input type="text" name="partner_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_certificate_partner') . '">
                                </div>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_certificate_text') . '</textarea>
                            </div>';
                        }

                        echo '<div class="form-group"><label>انتخاب مهارت</label><select name="skill" style="width: 100%;"
                                class="select2">';
                            $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4'  and tbl_categories_lng='$language_default'");
                            while ($read_skill = mysqli_fetch_array($result_skill)) {
                                $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
                                $read_memberskill = mysqli_fetch_array($result_memberskill);
                                $skillCode = $read_skill['tbl_categories_code'];
                                echo '<option value="' . $read_skill['tbl_categories_code'] . '" '.(read_fild(NULL, 'tbl_certificate_skill') == $read_skill['tbl_categories_code']? 'selected' : '').'>' . $read_skill['tbl_categories_title'] . '</option>';
                            }
                            echo ' </select></div>';

                            $img = read_fild(NULL, 'tbl_certificate_img');

                        if ($img) {
                            echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                      <td><input type="file" name="img"></td><td><a href="../upload/' . $img . '" target="_blank" ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                      <td><input type="file" name="img">
                                      </td></tr></table></div>';
                        }

//                         echo '<div class="form-group check-style">
//     <label>مبلغ به تومان</label>
//     <input type="text" name="price" class="form-control" value="' . $read['tbl_certificate_price'] . '" required>
// </div><div class="form-group check-style">
// <label>مدرک برای کارگاه<input type="checkbox" name="workshop" value="1" ' . ($read['tbl_certificate_workshop'] == 1 ? 'checked' : '') . '> </label>
// </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                    }
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['update'])) {
                            $code = $_POST['code'];
                            $skill = $_POST['skill'];
                            

                            $old_img = $_POST['old_img'];
                            $img = $_FILES['img']['name'];
                            $read_file_name = explode(".", $img);
                            $extn = end($read_file_name);

                            // upload images after rename 
                            if (!empty($img)) {

                                if ($extn == 'swf' || $extn == 'SWF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    $img_name = 'ads_' . time() . '.' . $extn;
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                                } else {
                                    $img_name = '';
                                }
                            } else {
                                $img_name = $old_img;
                            }


                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $content_[$i] = addslashes($_POST['content_' . $language[$i]]);
                                $partner_[$i] = addslashes($_POST['partner_' . $language[$i]]);

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: certificate.php");
                                    exit();
                                }
                            }
                            // end upload image
                            for ($i = 1; $i <= $language_count; $i++) {
                                $language_record = $language[$i];
                                Query("Update tbl_certificate SET 
                                    tbl_certificate_title='$title_[$i]', 
                                    tbl_certificate_text='$content_[$i]',
                                    tbl_certificate_img='$img_name',
                                    tbl_certificate_skill='$skill',
                                    tbl_certificate_partner='$partner_[$i]'
                                    where  tbl_certificate_code='$code' and tbl_certificate_lng = '$language_record' ");

                                $_SESSION['error'] = $lang[21];
                                header("Location: certificate.php");
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
include './footer.inc.php';

?>