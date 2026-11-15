<?php

// include
include './includer.php';
$show_per_page = '20';
$Tabpage = '6';
$pagetitle = $lang[82];


include './header.inc.php';
// end include 
?>
<div class="col-md-12 mb-12 admin-list-page">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    // list content
                    if (!isset($do)) {
                    ?>
                        <div class="form-group">
                            <?= (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') ?>
                        </div>
                        <div class="admin-list-toolbar">
                            <a class="btn btn-primary" href="?do=add"><?= $lang[31] ?></a>
                            <form action="?do=search" method="POST" class="admin-list-toolbar__field" role="search">
                                <label for="search_pages_title">جست‌وجو در همهٔ صفحه‌ها</label>
                                <div class="admin-list-search-controls">
                                    <input type="search" class="form-control" id="search_pages_title" name="keyword"
                                        value="<?= htmlspecialchars((string) $keyword, ENT_QUOTES, 'UTF-8') ?>" placeholder="عنوان صفحه">
                                    <button type="submit" name="go" class="btn btn-primary"><?= $lang[77] ?></button>
                                </div>
                            </form>
                            <p class="admin-list-count" data-admin-list-count aria-live="polite"></p>
                        </div>
                        <?php
                        // start count db result for pagination               
                        $search_connection = mysqli_connect($dbserver, $dbusername, $dbpassword, $dbname);
                        mysqli_set_charset($search_connection, 'utf8');
                        $search_keyword = mysqli_real_escape_string($search_connection, (string) $keyword);
                        mysqli_close($search_connection);
                        $result_for_count = Query("SELECT * From tbl_pages Where tbl_pages_lng='$language_default' and tbl_pages_title LIKE '%$search_keyword%'");
                        $count_result = mysqli_num_rows($result_for_count);
                        $page_number = max(1, (int) ceil($count_result / $show_per_page));
                        $page = max(1, min((int) $page, $page_number));
                        $start_contnet = ($page - 1) * $show_per_page;
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_pages Where tbl_pages_lng='$language_default' and tbl_pages_title LIKE '%$search_keyword%' ORDER BY tbl_pages_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="admin-list-empty">' . $lang[57] . '</div>';
                        } else {
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" data-admin-list="pages" style="text-align:right;" >
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[50] . '</th>
                                    <th>' . $lang[83] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '
                            <tr>
                                <td style="width: 1%">' . toPersianNum($i) . '</td>
                                <td class="col-sm-9"><a href="?do=edit&code=' . $read['tbl_pages_code'] . '" >' . htmlspecialchars((string) $read['tbl_pages_title'], ENT_QUOTES, 'UTF-8') . '</a></td>
                                <td class="col-sm-1"><a href="../fa/page/' . rawurlencode((string) $read['tbl_pages_code']) . '/" target="_blank" rel="noopener">نمایش</a></td>
                                <td class="col-sm-2 admin-list-actions"><a href="?do=edit&code=' . $read['tbl_pages_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                                <a href="?do=delete&code=' . $read['tbl_pages_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                                </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
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
                                            <li><a class="page-link" href="?page=<?= ($back_page_number - 1) . '' . $other_page_keyword . rawurlencode((string) $keyword) ?>"><?= $lang[75] ?></a></li>
                                            <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a></li>
                                            <li><a class="page-link" href="?page=<?= min($page + 1, $page_number) . '' . $other_page_keyword . rawurlencode((string) $keyword) ?>"><?= $lang[74] ?></a></li>
                                        </ul>
                                    </td>
                                    <td class="col-lg-6">
                                        <form action="pages.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: pages.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6 cms-editor-wrap">
                          <form action="?do=additem" method="POST" class="cms-editor-form">
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >';

                        echo '<h6 class="cms-section-heading">عنوان صفحه در زبان‌های مختلف</h6>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        echo '<h6 class="cms-section-heading">محتوای صفحه در زبان‌های مختلف</h6>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                  <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                  <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                  </div>';
                        }
                        echo '<h6 class="cms-section-heading">جزئیات انتشار</h6><div class="form-group">
                              <label>' . $lang[55] . '</label>
                              <input type="text" name="date" value="' . jdate('Y/n/j ,H:i:s') . '" class="form-control">
                              </div>
                             <div class="cms-save-bar">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
                             <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                             </div>
                </form></div>';
                    }
                    // start = add record to db
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $code = $_POST['code'];
                            $date = $_POST['date'];
                            // for language
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = $_POST['content_' . $language[$i]];

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: pages.php");
                                    exit();
                                }
                                $language_name = $language[$i];
                                // insert query
                                Query("INSERT INTO tbl_pages (tbl_pages_code,tbl_pages_title,tbl_pages_text,tbl_pages_lng,tbl_pages_time) values('$code','$title_[$i]','$content_[$i]','$language_name','$date')");
                                $_SESSION['error'] = $lang[32];
                                header("Location: pages.php");
                            }
                        }
                    }
                    // end  = add record          
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_pages Where tbl_pages_code = '$code' and tbl_pages_lng = '$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_pages_title'] . '</h2><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];

                            Query("DELETE from tbl_pages Where tbl_pages_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: pages.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {

                            header("Location: pages.php");
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
                                $check_language_id = "and tbl_pages_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_pages Where tbl_pages_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // end function

                        // form for edit
                        echo '<div class="col-lg-6 cms-editor-wrap">
                          <form action="?do=edititem" method="POST" class="cms-editor-form">
                          <input type="hidden" name="code" value="' . $code . '" >';

                        echo '<h6 class="cms-section-heading">عنوان صفحه در زبان‌های مختلف</h6>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" value="' . read_fild($language[$i], 'tbl_pages_title') . '" class="form-control">
                              </div>';
                        }
                        echo '<h6 class="cms-section-heading">محتوای صفحه در زبان‌های مختلف</h6>';
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_pages_text') . '</textarea>
                              </div>';
                        }
                        echo '<h6 class="cms-section-heading">جزئیات انتشار</h6><div class="form-group">
                                  <label>' . $lang[55] . '</label>
                                  <input type="text" name="date" value="' . jdate('Y/n/j ,H:i:s') . '" value="' . read_fild(NULL, 'tbl_pages_time') . '" class="form-control">
                                  </div>
                                  <div class="cms-save-bar">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                                  <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
                                  </div>
                                  </form></div>';
                    }
                    // end = edit item
                    // 
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $date = $_POST['date'];

                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = $_POST['title_' . $language[$i]];
                                $content_[$i] = $_POST['content_' . $language[$i]];

                                if (empty($title_[1])) {
                                    $_SESSION['error'] = $lang[56];
                                    header("Location: pages.php");
                                    exit();
                                }

                                $language_name = $language[$i];
                                // update query
                                Query("Update tbl_pages SET tbl_pages_title='$title_[$i]' , tbl_pages_text = '$content_[$i]' ,tbl_pages_time = '$date'  Where tbl_pages_code= '$code' and tbl_pages_lng = '$language_name'");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: pages.php");
                        }
                    }
                    // end = update item

                    // strat = delete image item
                    if (isset($t) && isset($do) && $do == 'deleteimage') {
                        $result = Query("SELECT * from tbl_content  Where tbl_content_code = '$code' ");
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

                            $record = str_replace("$img", "", $read['tbl_content_img']);
                            $external_file = $directory . $img;
                            unlink("$external_file");
                            Query("Update tbl_content SET tbl_content_img = '$record' ");
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
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = trim((string) ($_POST['keyword'] ?? ''));
                            header('Location: pages.php?keyword=' . rawurlencode($keyword_search));
                            exit;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo '<script src="asset/js/listing-ui.js"></script>';
    echo '<script src="asset/js/cms-editor-ui.js"></script>';
    include './footer.inc.php';
