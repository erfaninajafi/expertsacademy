<?php

// include
include './includer.php';
$show_per_page = '20';
$Tabpage = '10';
$pagetitle = 'مدارک';
$thisaccess = '1,2';
include './header.inc.php';

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    echo '<section class="content"><div class="col-md-12">
            <div class="box box-body">';
                    // list content
                    if (!empty($history)) {
                        $thishistory = "Where tbl_document_userid = '$history'";
                    } else {
                        $thishistory = "";
                    }
                    if (!isset($do)) {
                        // start count db result for pagination 

                        $result_for_count = Query("SELECT * From tbl_document $thishistory ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination         
                        $result = Query("SELECT * From tbl_document $thishistory ORDER BY tbl_document_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        $result = Query("Select * from tbl_document $thishistory order by tbl_document_id DESC LIMIT $start_contnet,$show_per_page");
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>بخش</th>
                                    <th>عنوان</th>
                                    <th>توضیحات</th>
                                    <th>تاریخ</th>
                                    <th>نام کاربر</th>
                                    <th>وضعیت</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $thisid = $read['tbl_document_userid'];
                            $result_m = Query("select * from tbl_member where tbl_member_id = '$thisid'  ");
                            $read_m = mysqli_fetch_array($result_m);

                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1"><a href="?do=edit&id=' . $read['tbl_document_id'] . '" >' . $read['tbl_document_section'] . '</a></td>
                                <td class="col-sm-1">' . $read['tbl_document_title'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_document_link'] . '</td>
                                <td class="col-sm-1">' . $read['tbl_document_date'] . '</td>
                                <td class="col-sm-1"><a href="member.php?do=edit&code=' . @$read_m['tbl_member_id'] . '" target="_blank">' . $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] . '</a></td>
                                <td class="col-sm-1">
                                <label>
                                <input class="documentstatus" type="radio" name="n' . $read['tbl_document_id'] . '" value="0-' . $read['tbl_document_id'] . '" ' . ($read['tbl_document_status'] == 0 ? 'checked' : '') . '> تایید نشده </label><br><br>
                                 <label>
                                 <input  class="documentstatus" type="radio" name="n' . $read['tbl_document_id'] . '" value="1-' . $read['tbl_document_id'] . '" ' . ($read['tbl_document_status'] == 1 ? 'checked' : '') . '>  تایید شده </label>
                                </td>
                                </tr><tr>
                                <td class="col-sm-1 btn-class" colspan="7">' . ($access != 2 ? '<a href="?do=edit&id=' . $read['tbl_document_id'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                                <a  href="?do=delete&id=' . $read['tbl_document_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>' : '') . '
                                <a  target="_blank" href="../upload/document/' . $read['tbl_document_file'] . '" class="btn btn-warning btn-xs">نمایش</a></td>
                                </tr>';
                            $i++;
                        }
                        // end show status   
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($status) ? "&status=$status" : '') . (!empty($history) ? "&history=$history" : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="document.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            header("Location: document.php?page=$page_number");
                        }
                    }
                    // end = pagination

                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        if ($access != 2) {
                            $result = Query("SELECT * from tbl_document Where tbl_document_id = '$id' ");
                            $read = mysqli_fetch_array($result);
                            echo '<div class="col-lg-6 ">' . $lang[25] . '<br /><h2>' . $read['tbl_document_section'] . '/' . $read['tbl_document_title'] . '</h2><br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="hidden" value="' . $read['tbl_document_file'] . '" name="file" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                            if (isset($_POST['submit'])) {
                                $id = $_POST['id'];
                                $file = $_POST['file'];
                                unlink("../upload/" . $file);
                                Query("DELETE from tbl_document Where tbl_document_id = '$id' ");
                                $_SESSION['error'] = $lang[28];
                                header("Location: document.php");
                                exit();
                            }
                            // no delete
                            elseif (isset($_POST['nodelete'])) {
                                header("Location: document.php");
                                exit();
                            }
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($do) && $do == 'edit') {

                        $result = Query("SELECT * FROM tbl_document Where tbl_document_id = '$id'");
                        $read = mysqli_fetch_array($result);
                        $status = $read['tbl_document_status'];
                        echo '<div class="col-lg-6"><form action="?do=order_update" method="POST">'
                            . '<input type="hidden" name="id" value="' . $id . '">'
                            . '<input type="hidden" name="userid" value="' . $read['tbl_document_userid'] . '">'
                            . '<div class="form-group">
                            <label>نوع مدرک</label>
                            <select type="text" class="form-control" data-parsley-length="[4, 80]" name="section" required="">
                                <option value="کارت ملی" ' . ($read['tbl_document_section'] == 'کارت ملی' ? 'selected' : '') . '>کارت ملی</option>
                                <option value="شناسنامه" ' . ($read['tbl_document_section'] == 'شناسنامه' ? 'selected' : '') . '>شناسنامه</option>
                                <option value="کارت پایان خدمت" ' . ($read['tbl_document_section'] == 'کارت پایان خدمت' ? 'selected' : '') . '>کارت پایان خدمت</option>
                                <option value="آخرین مدرک تحصیلی" ' . ($read['tbl_document_section'] == 'آخرین مدرک تحصیلی' ? 'selected' : '') . '>آخرین مدرک تحصیلی</option>
                                <option value="سایر" ' . ($read['tbl_document_section'] == 'سایر' ? 'selected' : '') . '>سایر</option>
                            </select>
                            </div>'
                            . '<div class="form-group">
                            <label>عنوان</label>
                                <input type="text" name="title" value="' . $read['tbl_document_title'] . '" class="form-control">
                            </div>
                            <div class="form-group">
                            <label>لینک</label>
                            <textarea type="text" class="form-control" data-parsley-length="[4, 500]" maxlength="500" name="link">' . $read['tbl_document_link'] . '</textarea>
                            </div>
                    <div class="form-group">
                    <label>وضعیت</label>
                    <select name="status" class="form-select">';
                        for ($i = 0; $i <= 1; $i++) {
                            if ($i == $status) {
                                $selected = 'selected';
                            } else {
                                $selected = '';
                            }
                            echo '<option value="' . $i . '" ' . $selected . '>' . $lang[632 + $i] . '</option>';
                        }
                        echo '</select></div>
                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
            </div></form>';
                    }
                    // end = edit item
                    // 
                    // start = update items
                    if (isset($do) && $do == 'order_update') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $title = $_POST['title'];
                            $link = $_POST['link'];
                            $status = $_POST['status'];
                            $userid = $_POST['userid'];
                            // end update count 
                            Query("Update tbl_document SET tbl_document_link='$link' ,tbl_document_title='$title' , tbl_document_status = '$status'  Where tbl_document_id= '$id' ");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: document.php?history=$userid");
                    }
                    // end = update item

                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: document.php?keyword=$keyword_search");
                        }
                    }
                    if (isset($do) && $do == 'searchname') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keywordname'];
                            header("Location: document.php?keywordname=$keyword_search");
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