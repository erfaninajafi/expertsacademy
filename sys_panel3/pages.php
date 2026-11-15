<?php
// include
include './includer.php';
$show_per_page = '10';
$Tabpage = '6';
$pagetitle = $lang[82];
$thisaccess = '1';
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
                    // list content
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >' . $lang[31] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="?do=search" method="POST">
                       <input type="text" class="form-control" id="search_pages_title" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary">' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_pages Where tbl_pages_title LIKE '%$keyword%' and tbl_pages_lng='$language_default' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_pages Where tbl_pages_lng='$language_default' and tbl_pages_title LIKE '%$keyword%' ORDER BY tbl_pages_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[83] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
                            <td style="width: 1%">' . $i . '</td>
                            <td class="col-sm-9"><a href="?do=edit&code=' . $read['tbl_pages_code'] . '" >' . $read['tbl_pages_title'] . '</a></td>
                            </tr><tr>
                            <td class="col-sm-1 btn-class" colspan="2">
                            <a href="?do=edit&code=' . $read['tbl_pages_code'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                            <a href="?do=delete&code=' . $read['tbl_pages_code'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
                            <a href="../page/' . $read['tbl_pages_code'] . '/' . (str_replace(" ", "-", $read['tbl_pages_title'])) . '/" target="_blank" class="btn btn-xs btn-warning">نمایش</a>
                            </td>
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="page.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            header("Location: pages.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = do add
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6">
                          <form action="?do=additem" method="POST" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" class="form-control">
                              </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                                  <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                                  <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '"></textarea>
                                  </div>';
                        }
                        echo '<hr><div class="form-group">
                              <label>' . $lang[55] . '</label>
                              <input type="text" name="date" value="' . jdate('Y/n/j ,H:i:s') . '" class="form-control">
                              </div>
                             <div class="form-group">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary">
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
                        echo '<div class="col-lg-6">
                          <form action="?do=edititem" method="POST" >
                          <input type="hidden" name="code" value="' . $code . '" >';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[50] . '<span class="language">' . $language[$i] . '</span></label>
                              <input type="text" name="title_' . $language[$i] . '" value="' . read_fild($language[$i], 'tbl_pages_title') . '" class="form-control">
                              </div>';
                        }
                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                              <label>' . $lang[51] . '<span class="language">' . $language[$i] . '</span></label>
                              <textarea name="content_' . $language[$i] . '" class="editor editor_' . $language[$i] . '">' . read_fild($language[$i], 'tbl_pages_text') . '</textarea>
                              </div>';
                        }
                        echo '<hr><div class="form-group">
                                  <label>' . $lang[55] . '</label>
                                  <input type="text" name="date" value="' . jdate('Y/n/j ,H:i:s') . '" value="' . read_fild(NULL, 'tbl_pages_time') . '" class="form-control">
                                  </div>
                                  <div class="form-group">
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
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

                    // end = delete image item
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: pages.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result            
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>