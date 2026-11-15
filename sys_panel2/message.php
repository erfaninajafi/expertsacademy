<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1199];
$Tabpage = '6';
$thisaccess = '1,3,7,9';
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
                            <?=(!isset($do) && !empty($_GET['karfarma']) ? '<div class="plus">
                                <a href="?do=add&karfarma='.$_GET['karfarma'].'"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
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

                    if (isset($_GET['karfarma'])) {
                        $karfarma = " tbl_message_karfarma = '$_GET[karfarma]' and tbl_message_model='1' and ";
                    }
                    else if ($_SESSION['user_access'] == 'karfarma') {
                        $karfarma = " tbl_message_karfarma = '$this_user_id' and tbl_message_model='1' and ";
                    }
                     else {
                        $karfarma = " tbl_message_model='0' and ";
                    }

                    if (!isset($do)) {
                        // search and add button
                        echo  '<table><tr><td class="col-sm-9 top-worklist-menu">';

                        if(empty($_GET['karfarma']) && $_SESSION['user_access'] != 'karfarma') {
                       echo '<span><a href="message.php"> '.$lang[1542].' </a></span>
                       <span><a href="message.php?status=2">'.$lang[1543].'</a></span> 
                       <span><a href="message.php?status=1"> '.$lang[1544].'</a></span> 
                       <span><a href="message.php?status=0"> '.$lang[1545].'</a></span>';
                    }

                       echo '<td class="col-sm-6"> 

                       <form action="message.php?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="'. $keyword .'" name="keyword">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                       </td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_message where $member $karfarma $status tbl_message_userid !='' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_message Where $member $karfarma $status tbl_message_userid !='' and tbl_message_code LIKE '%$keyword%' ORDER BY tbl_message_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr><th>' . $lang[60] . '</th>'
                            . '<th>' . $lang[466] . '</th>'
                            . '<th>'.$lang[541].'</th>'
                            . '<th>' . $lang[61] . '</th>'
                            . '<th>' . $lang[600] . '</th>'
                            . '<th>' . $lang[1340] . '</th>'
                            . '<th>' . $lang[63] . '</th>
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
                            $result_userid = Query("SELECT * from tbl_member  Where tbl_member_id = '$thisuser' ");
                            $read_userid = mysqli_fetch_array($result_userid);
                            echo '<tr><td class="align-middle p-2">' . $i . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_message_code'] . '</td>'
                                . '<td class="align-middle p-2">' . @$read_userid['tbl_member_fname'] . ''.$read_userid['tbl_member_lname'] . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_message_title'] . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_message_date'] . '</td>'
                                . '<td class="align-middle p-2">' . ticket($read['tbl_message_status']) . '</td>'
                                . '<td class="align-middle p-2">
                                <a href="?do=view&code=' . $read['tbl_message_code'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '"><span class="bi-pencil-square"></span></a>'
                                . '<a href="?do=delete&code=' . $read['tbl_message_code'] . '"  class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a></td>'
                                . '</tr>';
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
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '')  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword(isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="message.php?do=gotopage" class="search-form" method="POST">
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

                    if (isset($do) && $do == 'add') { ?>
                <form action="?do=addticket" class="p-3" data-parsley-validate="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="karfarma" value="<?= $_GET['karfarma'] ?>">
                    <div class="col-lg-12 col-xs-12">
                        <div class="form-group">
                            <label><?= $lang[94] ?></label>
                            <textarea rows="8" name="msg" data-parsley-length="[10, 1000]" class="form-control"></textarea>
                        </div>

                        <div class="form-group file-attach">
                            <label><?= $lang[1153] ?></label>
                            <input type="file" name="attach" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="submit" class="btn btn-success mt-3" value="<?= $lang[1819] ?>">
                        </div>
                    </div>
                </form>
                <?php
                    }

                    if (isset($do) && $do == 'addticket') {
                    if (isset($_POST['submit'])) {
                        $date = jdate("Y/m/d - g:i a");
                        $title = sqi($_POST['title']);
                        $text = sqi($_POST['msg']);
                        $karfarma = sqi($_POST['karfarma']);
                        $code = Code("", "no", 9);
            
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
                                    move_uploaded_file($_FILES['attach']["tmp_name"], "upload/attach/" . $attach);
                                    $attachfinal = $attach;
                                } else {
                                    $_SESSION['error'] = $lang[1193];
                                    $attachfinal = '';
                                }
                            }
                        }
                        Query("insert into tbl_message (
                        tbl_message_userid,
                        tbl_message_title,
                        tbl_message_text,
                        tbl_message_date,
                        tbl_message_status,
                        tbl_message_code,
                        tbl_message_attach,
                        tbl_message_model,
                        tbl_message_karfarma) 
                        values(
                            '$this_user_id',
                            '$title',
                            '$text',
                            '$date',
                            '0',
                            '$code',
                            '$attachfinal',
                            '1',
                            '$karfarma') ");
                            header("Location: message.php?karfarma=$karfarma");
                        exit();
                    }
                }

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
                <div class="col-lg-12 ticketview p-3"><?= $sender ?>
                    <div class="col-lg-12"><label><?= $lang[466] ?> : </label><?= $read['tbl_message_code'] ?></div>
                    <div class="col-lg-12"><label><?= $lang[600] ?> : </label><?= $read['tbl_message_date'] ?></div>
                    <?= (!empty($read['tbl_message_title']) ? '<div class="col-lg-12"><label>' . $lang[61] . ' : </label>' . $read['tbl_message_title'] . '</div>' : '') ?>
                    <div class="col-lg-12"><label><?= $lang[94] ?> : </label><?= $read['tbl_message_text'] ?></div>
                    <?= (!empty($file) ? '<div class="col-12 col-md-12"><label>فایل ضمیمه : </label><a href="../upload/attach/' . $file . '" target="_blank">مشاهده</a></div>' : '') ?>
                    <div class="col-lg-12">
                        <hr>
                    </div>
                </div>
                <?php
                        }
                        ?>
                <form action="?do=sendreplayticket" class="p-3" data-parsley-validate="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="tcode" value="<?= $code ?>">
                    <div class="col-lg-12 col-xs-12">
                        <h1><?= $lang[606] ?></h1>
                        <div class="form-group">
                            <label><?= $lang[94] ?></label>
                            <textarea rows="8" name="msg" data-parsley-length="[10, 1000]" class="form-control"></textarea>
                        </div>

                        <div class="form-group file-attach">
                            <label><?= $lang[1153] ?></label>
                            <input type="file" name="attach" class="form-control">
                        </div>
                        <div class="form-group">
                            <label><?=$lang[1340]?></label>
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

                <div class="col-lg-6 col-12 p-3">
                    <?= $lang[25] . '<br /><br />' . $read['tbl_message_code'] ?>
                    <form action="#" method="POST">
                        <input type="hidden" name="code" value="<?= $code ?>">
                        <div class="form-group margin-top">
                            <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-success mt-3">
                            <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-danger mt-3">
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