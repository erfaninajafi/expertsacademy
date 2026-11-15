<?php

// include
include './includer.php';
$show_per_page = '10';
//type id 
$pagetitle = 'بانک فایل';
$Tabpage = '91';
$cat_manager_id = '0';
$thisaccess = '1,5';
$img_item = '0';
include './header.inc.php';
if ($adminFile == '0') {
    header("Location: index.php");
}


?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!empty($_GET['skill'])) {
                        $skill = " tbl_bankfile_skill = '$_GET[skill]' and ";
                        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$_GET[skill]' and tbl_categories_lng='$language_default' ");
                        $readSkill = mysqli_fetch_array($resultSkill);
                        $skillTitle = $readSkill['tbl_categories_title'];
                        echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title"><a href="category_manage.php?t=4">لیست مهارت ها</a>  /  مهارت : ' . $readSkill['tbl_categories_title'] . '</h5></div>';
                    }
                    // list content
                    echo '<section class="content"><div class="col-md-12">
                        <div class="box box-body table-responsive">';
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9 padding-clear"><a class="btn btn-primary margin-left big_button" href="?do=add' . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '">افزودن فایل</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="bankfile.php?do=search" method="POST">
                       <input type="text" class="form-control" name="keyword" value="' . $keyword . '">
                       <input type="hidden" class="form-control" name="skill" value="' . $_GET['skill'] . '"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_bankfile Where $skill tbl_bankfile_delete='0' and tbl_bankfile_lng='$language_default' and tbl_bankfile_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_bankfile Where $skill tbl_bankfile_delete='0' and  tbl_bankfile_lng='$language_default' and tbl_bankfile_title LIKE '%$keyword%' ORDER BY tbl_bankfile_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                    <tr>
                                        <th>' . $lang[60] . '</th>
                                        <th>' . $lang[50] . '</th>
                                        <th>مهارت</th>
                                        <th>تاریخ ایجاد</th>
                                        <th>تاریخ ویرایش</th>
                                        <th>کاربر</th>
                                        <th>' . $lang[63] . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mentor = $read['tbl_bankfile_mentor'];
                            $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                            $readMentor = mysqli_fetch_array($resultMentor);

                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-2"><a href="?do=edit&code=' . $read['tbl_bankfile_code'] . '" >' . $read['tbl_bankfile_title']  . '</a></td>
                                <td class="col-sm-2">' . $skillTitle . '</td>
                                <td class="col-sm-1">' . @toPersianNum($read['tbl_bankfile_dateadd']) . '</td>
                                <td class="col-sm-1">' . @toPersianNum($read['tbl_bankfile_dateedit']) . '</td>
                                <td class="col-sm-1">' . @$readMentor['tbl_user_name'] . '</td>
                                <td class="col-sm-1 btn-class"><a href="?do=edit&code=' . $read['tbl_bankfile_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a><br>
                                <a href="?do=delete&code=' . $read['tbl_bankfile_code'] . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                                </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
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
                                            <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (isset($_GET['section']) ? '&section=' . $_GET['section'] : '') . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '')  ?>"><?= $lang[75] ?></a>
                                            </li>
                                            <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                            </li>
                                            <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['section']) ? '&section=' . $_GET['section'] : '') . (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') ?>"><?= $lang[74] ?></a>
                                            </li>
                                        </ul>
                                    </td>
                                    <td class="col-lg-6">
                                        <form action="bankfile.php?do=gotopage<?= (!empty($_GET['skill']) ? '&skill=' . $_GET['skill'] : '') ?>" class="navbar-form navbar-left" role="search" method="POST">
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
                            Query("update tbl_bankfile set tbl_bankfile_fld5='$val' Where tbl_bankfile_code = '$value' ");
                        }
                        header("Location: bankfile.php?page=$page");
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: bankfile.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $skill = $_POST['skill'];
                            header("Location: bankfile.php?skill=$skill&keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-8">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >
                           <input type="hidden" name="skill" value="' . $_GET['skill'] . '" >';


                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }

                        echo '<hr>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group"><table><tr>
                                  <td><label class="col-sm-2 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                  <td><input type="file" name="file_attach_' . $language[$i] . '"></td>
                                  </tr></table></div>';
                        }

                        echo '<div class="form-group">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                             </div>
                </form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $skill = $_POST['skill'];
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: bankfile.php");
                                    exit();
                                }
                                // upload file
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
                                // end file upload
                                $language_name = $language[$i];
                                $date = jdate("Y/m/d : a:h:s");
                                // insert query
                                Query("INSERT INTO tbl_bankfile (
                                tbl_bankfile_code,
                                tbl_bankfile_title,
                                tbl_bankfile_file,
                                tbl_bankfile_lng,
                                tbl_bankfile_dateadd,
                                tbl_bankfile_mentor,
                                tbl_bankfile_delete,
                                tbl_bankfile_skill) 
                                values(
                                '$code',
                                '$title_[$i]',
                                '$file_name_[$i]',
                                '$language_name',
                                '$date',
                                '$this_user_id',
                                '0',
                                '$skill')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: bankfile.php?skill=$skill");
                            }
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        if ($access == 2) {
                            $select = " tbl_bankfile_admin='$this_user_id' and ";
                        } else {
                            $select = '';
                        }

                        $result = Query("SELECT * from tbl_bankfile  Where $select tbl_bankfile_code = '$code' and tbl_bankfile_lng = '$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-8">' . $lang[25] . '<br /><h2>' . $read['tbl_bankfile_title'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="skill" value="' . $_GET['skill'] . '" >
                         <input type="hidden" name="img" value="' . $read['tbl_bankfile_img'] . '" >';

                        // find file form languages for delete
                        $files_name = '';
                        for ($i = 1; $i <= $language_count; $i++) {

                            $result_file = Query("SELECT * from tbl_bankfile  Where tbl_bankfile_code = '$code' and tbl_bankfile_lng = '$language[$i]' ");
                            $read_file = mysqli_fetch_array($result_file);
                            $files_name .= $read_file['tbl_bankfile_file'] . ',';
                        }

                        echo  ' <input type="hidden" name="file" value="' . $files_name . '" >
                            <input type="hidden" value="' . $code . '" name="code" >
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $img = $_POST['img'];
                            $file = $_POST['file'];
                            $skill = $_POST['skill'];
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
                            Query("update tbl_bankfile set tbl_bankfile_delete='1' Where tbl_bankfile_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: bankfile.php?skill=$skill");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            $skill = $_POST['skill'];
                            header("Location: bankfile.php?skill=$skill");
                            exit();
                        }
                    }
                    // end = delete item
                    // start = edit record
                    if (isset($do) && $do == 'edit') {

                        // this function for read multi language content - check kardan chand zaban baraye value field ha 
                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_bankfile_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_bankfile Where tbl_bankfile_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function

                        // form for edit
                        echo '<div class="col-lg-8">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data" >
                          <input type="hidden" name="code" value="' . $code . '" >
                          <input type="hidden" name="skill" value="' . $_GET['skill'] . '" >';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_bankfile_title')) . '" class="form-control">
                              </div>';
                        }


                        // Read file's
                        for ($i = 1; $i <= $language_count; $i++) {
                            if (read_fild($language[$i], 'tbl_bankfile_file')) {
                                echo '<input type="hidden" name="old_file_' . $language[$i] . '" value="' . read_fild($language[$i], 'tbl_bankfile_file') . '">
                                      <div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                      <td><input type="file" name="file_attach_' . $language[$i] . '"></td><td>
                                      <a href="../upload/' . read_fild($language[$i], 'tbl_bankfile_file') . '"  ><span class="btn btn-xs btn-success"><span class="bx bxs-show"></span></span></a>
                                      <a href="?code=' . read_fild(NULL, 'tbl_bankfile_code') . '&do=deletefile&file=' . read_fild($language[$i], 'tbl_bankfile_file') . '&skill=' . $_GET['skill'] . '"  ><span class="btn btn-xs btn-danger"><span class="bx bxs-trash"></span></span></a>
                                      </td></tr></table></div>';
                            } else {
                                echo '<input type="hidden" name="old_file" value="' . read_fild($language[$i], 'tbl_bankfile_file') . '">
                                    <div class="form-group"><table ><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[52] . '<span class="language">' . $language[$i] . '</span></label></td>
                                      <td><input type="file" name="file_attach_' . $language[$i] . '">
                                      </td></tr></table></div>';
                            }
                        }

                        echo  '<div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                                  </div>
                                  </form></div>';
                    }
                    // end = edit item
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $skill = $_POST['skill'];
                            // filter add 
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: bankfile.php");
                                    exit();
                                }
                                // upload file
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
                                    $file_name_[$i] = $_POST['old_file_' . $language[$i]];
                                }
                                // end file upload
                                $language_record = $language[$i];
                                $date = jdate("Y/m/d : a:h:s");;
                                // update query
                                Query("Update tbl_bankfile SET 
                                tbl_bankfile_title='$title_[$i]' ,
                                tbl_bankfile_dateedit = '$date' ,
                                tbl_bankfile_mentor = '$this_user_id',
                                tbl_bankfile_file = '$file_name_[$i]'
                                Where tbl_bankfile_code= '$code' and tbl_bankfile_lng = '$language_record'  ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: bankfile.php?skill=$skill");
                        }
                    }
                    // end = update item

                    if (isset($do) && $do == 'deletefile') {
                        $result = Query("SELECT * from tbl_bankfile  Where tbl_bankfile_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                        // which file language : read language file
                        $which_file_lng = explode("_", $file);
                        $which_file_lng = end($which_file_lng);
                        $which_file_lng = explode(".", $which_file_lng);
                        // read file language for exp  . en or fa in before extension
                        $which_file_lng = reset($which_file_lng);

                        echo '<div class="col-lg-6 col-lg-offset-6">' . $lang[25] . '<br /><a href="' . $directory . $file . '" >' . $lang[58] . '</a><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="language" value="' . $which_file_lng . '" >
                         <input type="hidden" name="file" value="' . $file . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $_GET['skill'] . '" name="skill" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $file = $_POST['file'];
                            $code = $_POST['code'];
                            $lang = $_POST['language'];
                            $skill = $_POST['skill'];

                            // read old file record
                            $external_file = $directory . $file;
                            unlink("$external_file");
                            $record = '';
                            Query("Update tbl_bankfile SET tbl_bankfile_file = '$record' Where tbl_bankfile_code = '$code' and tbl_bankfile_lng = '$lang' ");
                            header("Location: bankfile.php?code=$code&do=edit&skill=$skill");
                        } elseif (isset($_POST['nodelete'])) {
                            $skill = $_POST['skill'];
                            $code = $_POST['code'];
                            header("Location: bankfile.php?code=$code&do=edit&skill=$skill");
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
include './footer.inc.php';

?>