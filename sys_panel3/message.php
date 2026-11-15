<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'پشتیبانی';
$Tabpage = '6';
$thisaccess = '1,3';
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
                    if (isset($_GET['status'])) {
                        $thisstatus = $_GET['status'];
                        $status = "tbl_message_status = '$thisstatus' and tbl_message_sender='0' and ";
                    } else {
                        $status = '';
                    }
                    if (isset($_GET['member'])) {
                        $member = "tbl_message_userid = '$_GET[member]' and ";
                    } else {
                        $member = '';
                    }
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9 top-worklist-menu">
                       <span><a href="message.php"> همه </a></span>
                       <span><a href="message.php?status=2">بسته شده </a></span> 
                       <span><a href="message.php?status=1"> پاسخ داده شده </a></span> 
                       <span><a href="message.php?status=0"> دردست بررسی </a></span> 
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="message.php?do=search" method="POST">
                       <input type="text" class="form-control" id="message_code" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_message where $member $status tbl_message_userid !='' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_message Where $member $status tbl_message_userid !='' and tbl_message_code LIKE '%$keyword%' ORDER BY tbl_message_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                            exit();
                        }
                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                            <thead class="table-dark">
                                <tr><th>' . $lang[60] . '</th>'
                            . '<th>' . $lang[466] . '</th>'
                            . '<th>ارسال کننده</th>'
                            . '<th>' . $lang[61] . '</th>'
                            . '<th>' . $lang[600] . '</th>'
                            . '<th>' . $lang[479] . '</th>
                            </tr>
                            </thead><tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_message_status'] == 0) {
                                $style_check = "style='color:gray; font-style: italic;'";
                            } else {
                                $style_check = "";
                            }
                            $thisuser = $read['tbl_message_userid'];
                            $result_userid = Query("SELECT * from tbl_member  Where tbl_member_code = '$thisuser' ");
                            $read_userid = mysqli_fetch_array($result_userid);
                            echo '<tr><td>' . $i . '</td>'
                                . '<td>' . $read['tbl_message_code'] . '</td>'
                                . '<td>' . @$read_userid['tbl_member_name'] . '</td>'
                                . '<td>' . $read['tbl_message_title'] . '</td>'
                                . '<td>' . $read['tbl_message_date'] . '</td>'
                                . '<td>' . ticket($read['tbl_message_status']) . '</td>'
                                . '</tr><tr><td class="col-sm-1 btn-class" colspan="6">
                                <a href="?do=view&code=' . $read['tbl_message_code'] . '" class="btn btn-xs btn-warning">' . $lang[87] . '</a>'
                                . '<a href="?do=delete&code=' . $read['tbl_message_code'] . '"  class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
                                . '</tr>';
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword(isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="message.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                            header("Location: message.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: message.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    if (isset($do) && $do == 'view') {
                        $result = Query("SELECT * FROM tbl_message Where tbl_message_code = '$code' order by tbl_message_id DESC");
                        while ($read = mysqli_fetch_array($result)) {
                            $file = $read['tbl_message_attach'];
                            if ($read['tbl_message_sender'] == 0) {
                                $sender = '<span class="usersender">' . $lang[607] . '</span>';
                            } else {
                                $sender = '<span class="adminsender">' . $lang[608] . '</span>';
                            }
                        ?>
                    <div class="col-lg-12 ticketview"><?= $sender ?>
                        <div class="col-lg-12"><label><?= $lang[466] ?> : </label><?= $read['tbl_message_code'] ?></div>
                        <div class="col-lg-12"><label><?= $lang[600] ?> : </label><?= $read['tbl_message_date'] ?></div>
                        <?= (!empty($read['tbl_message_title']) ? '<div class="col-lg-12"><label>' . $lang[61] . ' : </label>' . $read['tbl_message_title'] . '</div>' : '') ?>
                        <div class="col-lg-12"><label><?= $lang[94] ?> : </label><?= $read['tbl_message_text'] ?></div>
                        <?= ($read['tbl_message_sender'] == 0 && !empty($file) ? '<div class="col-12 col-md-12"><label>فایل ضمیمه : </label><a href="../upload/atach/' . $file . '" target="_blank">مشاهده</a></div>' : '') ?>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                    <?php
                        }
                        ?>
                    <form action="?do=sendreplayticket" data-parsley-validate="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="tcode" value="<?= $code ?>">
                        <div class="col-lg-12 col-xs-12">
                            <h1><?= $lang[606] ?></h1>
                            <div class="form-group">
                                <label><?= $lang[94] ?></label>
                                <textarea rows="8" name="msg" data-parsley-length="[10, 1000]" class="form-control"></textarea>
                            </div>

                            <div class="form-group file-attach">
                                <label><?= $lang[851] ?></label>
                                <input type="file" name="attach" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>وضعیت</label>
                                <select name="status" class="form-control select2">
                                    <?php
                                        for ($i = 0; $i <= 2; $i++) {
                                            echo '<option value="' . $i . '" ' . ($read['tbl_message_status'] == $i ? 'selected' : '') . '>' . $lang[1270 + $i] . '</option>';
                                        }
                                        ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-success" value="<?= $lang[606] ?>">
                            </div>
                        </div>
                    </form>
                    <?php
                    }
                    if (isset($do) && $do == 'sendreplayticket') {
                        if (isset($_POST['submit'])) {
                            $date = jdate("Y/m/d - g:i a");
                            $text = strip_tags($_POST['msg']);
                            $code = strip_tags($_POST['tcode']);
                            $status = (!empty($text) ? '1' : strip_tags($_POST['status']));

                            if ($_FILES['attach']['size'] == 0 && $_FILES['attach']['error'] == 0) {
                                $attachfinal = '1';
                            } else {
                                if ($_FILES['attach']['size'] > 2000000) {
                                    $attachfinal = '';
                                }
                                if (isset($_FILES['attach']['name'])) {
                                    $attach = $_FILES['attach']['name'];
                                    $read_file_name = explode(".", $attach);
                                    $extn = end($read_file_name);
                                    $attach = 'attach_' . time() . '.' . $extn;
                                    if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attach']['size'] < 2000000) {
                                        move_uploaded_file($_FILES['attach']["tmp_name"], "../upload/attach/" . $attach);
                                        $attachfinal = $attach;
                                    } else {
                                        $_SESSION['error'] = $lang[1193];
                                        $attachfinal = '';
                                    }
                                }
                            }

                            Query("update tbl_message set tbl_message_status = '$status' , tbl_message_datereplay = '$date' where tbl_message_userid !='0' and tbl_message_code ='$code' ");
                            Query("insert into tbl_message (tbl_message_attach,tbl_message_text,tbl_message_date,tbl_message_code,tbl_message_reply,tbl_message_sender) values('$attachfinal','$text','$date','$code','1','1') ");
                            $_SESSION['error'] = $lang[21];
                            header("Location: message.php");
                            exit();
                        }
                    }
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_message Where tbl_message_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    <?= $lang[25] . '<br /><br />' . $read['tbl_message_code'] ?>
                    <div class="col-lg-6 col-lg-offset-6">
                        <form action="#" method="POST">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <div class="form-group margin-top">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger">
                        </form>
                    </div>
                    <?php

                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            Query("Delete From tbl_message Where tbl_message_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: message.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: message.php");
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