<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1325];
$Tabpage = '6';
$img_item = 10;
$thisaccess = '1,4';
include './header.inc.php';

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
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
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

                        $result = Query("SELECT * From tbl_certificate where tbl_certificate_id !='' and tbl_certificate_lng='$language_default' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_certificate where tbl_certificate_id !='' and tbl_certificate_lng='$language_default' ORDER BY tbl_certificate_id DESC  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        $allfield = '';
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>'.$lang[61].'</th>
                                    <th>'.$lang[1770].'</th>
                                    <th>'.$lang[63].'</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $result_skill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code='$read[tbl_certificate_skill]'  and tbl_categories_lng='$language_default'");
                            $read_skill = mysqli_fetch_array($result_skill);
                            echo '<tr><td class="align-middle p-2">' . $i . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_certificate_title'] . '</td>
                                <td class="align-middle p-2">' . $read_skill['tbl_categories_title'] . '</td>
                                <td class="align-middle p-2">'
                                . '<a href="?do=edit&code=' . $read['tbl_certificate_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '" ><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&code=' . $read['tbl_certificate_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table>';

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
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="certificate.php?do=gotopage" class="search-form" method="POST">
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
                <div class="col-lg-8 col-12 p-3">
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
                                        <label>'.$lang[1467].'<span class="language">' . $language[$i] . '</span></label>
                                        <input type="text" name="partner_' . $language[$i] . '" class="form-control" >
                                        </div>';
                                }

                                for ($i = 1; $i <= $language_count; $i++) {
                                    echo '<div class="form-group">
                                    <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                    <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                    </div>';
                                }
                                echo '<div class="form-group"><label>'.$lang[1468].'</label><select name="skill" style="width: 100%;" class="select2">';
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
                            <label><?=$lang[53]?></label>
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
                            <input type="submit" name="additem" value="<?= $lang[31] ?>" class="btn btn-primary mt-3">
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
                        $result = Query("SELECT * from tbl_certificate Where tbl_certificate_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_certificate_title'].'</h3>';
                        echo '<form action="#" method="POST">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <div class="form-group margin-top">
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3">
                            </form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            Query("Delete From tbl_certificate Where tbl_certificate_code = '$code' ");
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
                        echo '<div class="col-lg-8 col-12 p-3">
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
                                <label>'.$lang[1469].'<span class="language">' . $language[$i] . '</span></label>
                                <input type="text" name="partner_' . $language[$i] . '" class="form-control" value="' . read_fild($language[$i], 'tbl_certificate_partner') . '">
                                </div>';
                        }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_certificate_text') . '</textarea>
                            </div>';
                        }

                        echo '<div class="form-group"><label>'.$lang[1468].'</label><select name="skill" style="width: 100%;" class="select2">';
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
                                      <td><input type="file" name="img" class="form-control"></td>
                                      <td><a href="../upload/' . $img . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                      </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                      <td><input type="file" name="img" class="form-control">
                                      </td></tr></table></div>';
                        }

//                         echo '<div class="form-group check-style">
//     <label>مبلغ به تومان</label>
//     <input type="text" name="price" class="form-control" value="' . $read['tbl_certificate_price'] . '" required>
// </div><div class="form-group check-style">
// <label>مدرک برای کارگاه<input type="checkbox" name="workshop" value="1" ' . ($read['tbl_certificate_workshop'] == 1 ? 'checked' : '') . '> </label>
// </div>';
                        echo '<div class="form-group">
                   <input type="submit" name="update" value="' . $lang[20] . '" class="btn btn-primary mt-3">
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