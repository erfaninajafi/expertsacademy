<?php
include './includer.php';
$show_per_page = '20';
$pagetitle = 'نظرات کاربران';
$Tabpage = '6';
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
                       <table><tr><td class="col-sm-9"></td>
                      </tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_comment ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_comment ORDER BY tbl_comment_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                    <th>ارسال کننده </th>
                                    <th>تاریخ ارسال</th>
                                    <th>نام دوره </th>
                                    <th>قسمت </th>
                                    <th>وضعیت </th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {

                            $result_user = Query("select tbl_member_name from tbl_member where tbl_member_code='$read[tbl_comment_user]' ");
                            $read_user = mysqli_fetch_array($result_user);

                            if (@$read['tbl_comment_model'] == 0) {
                                $result_pro = Query("select tbl_class_title from tbl_class where tbl_class_code='$read[tbl_comment_class]' ");
                                $read_pro = mysqli_fetch_array($result_pro);
                            } else {
                                $result_pro = Query("select tbl_workshop_title from tbl_workshop where tbl_workshop_code='$read[tbl_comment_class]' ");
                                $read_pro = mysqli_fetch_array($result_pro);
                            }


                            echo '<tr>
                <td style="width: 1%">' . $i . '</td>
                <td class="col-sm-3">' . @$read_user['tbl_member_name'] . '</td>
                <td class="col-sm-2">' . $read['tbl_comment_date'] . '</td>
                <td class="col-sm-2">' . (@$read['tbl_comment_model'] == 0 ? $read_pro['tbl_class_title'] : $read_pro['tbl_workshop_title']) . '</td>
                <td class="col-sm-2">' . (@$read['tbl_comment_model'] == 0 ? 'کلاس' : 'کارگاه') . '</td>
                <td class="col-sm-2">' . ($read['tbl_comment_status'] == 0  ? 'بررسی' : 'تایید شده') . '</td>
                </tr><tr>
                <td class="col-sm-1 btn-class" colspan="6">
                <a href="?do=edit&id=' . $read['tbl_comment_id'] . '" class="btn btn-xs btn-success " >مشاهده</a>
                <a href="?do=delete&id=' . $read['tbl_comment_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="comment.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            header("Location: comment.php?page=$page_number");
                        }
                    }
                    // end = pagination     
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_comment Where tbl_comment_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6 ">' . $lang[25] . '<br /><p>' . $read['tbl_comment_text'] . '</p> <br />
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            Query("DELETE from tbl_comment Where tbl_comment_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: comment.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: comment.php");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($do) && $do == 'edit') {
                        $id = $_GET['id'];
                        $result = Query("SELECT * FROM tbl_comment Where tbl_comment_id =  '$id'  ");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="col-lg-6">
        <form action="?do=edititem" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="' . $id . '" >
        <div class="form-group">
        <label>متن کاربر</label>
        <textarea class="form-control" name="comment" rows="5" >' . $read['tbl_comment_msg'] . '</textarea>
        </div>
        <div class="form-group">
        <label>متن مدیر</label>
        <textarea class="form-control" name="textadmin" rows="5" >' . $read['tbl_comment_textadmin'] . '</textarea>
        </div>
        <div class="form-group">
        <label>وضعیت</label>
        <input type="checkbox" name="status" value="1" ' . ($read['tbl_comment_status'] == 0 ? '' : 'checked') . ' >
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
                            $id = $_POST['id'];
                            $status = $_POST['status'];
                            $comment = $_POST['comment'];
                            $textadmin = $_POST['textadmin'];

                            // update query
                            Query("Update tbl_comment SET tbl_comment_textadmin='$textadmin' ,tbl_comment_msg='$comment' ,tbl_comment_status='$status'  Where tbl_comment_id= '$id' ");
                            $_SESSION['error'] = $lang[21];
                            header("Location: comment.php");
                        }
                    }
                    // end = update item

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>