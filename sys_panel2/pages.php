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
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
                <?php

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
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[83] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2"><a href="?do=edit&code=' . $read['tbl_pages_code'] . '" >' . $read['tbl_pages_title'] . '</a></td>
                            <td class="align-middle p-2">
                            <a href="?do=edit&code=' . $read['tbl_pages_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                            <a href="?do=delete&code=' . $read['tbl_pages_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                            <a href="pageview.php?code=' . $read['tbl_pages_code'] . '" target="_blank" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '"><span class="bi-eye-fill"></span></a>
                            </td>
                            </tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
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
                            <form action="page.php?do=gotopage" class="search-form" method="POST">
                                <div class="form-group">
                                    <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
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
                        echo '<div class="col-lg-8 col-12 p-3">
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
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
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
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_pages_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
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
                        echo '<div class="col-lg-8 col-12 p-3">
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
                                  <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3">
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