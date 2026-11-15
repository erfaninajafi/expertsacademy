<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = 'تمام پیامهای فراگیر در پلن';
$img_item = '4';
$Tabpage = '90';
$thisaccess = '1,3,5';
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
                    if (!empty($_GET['contract'])) {
                        $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :<a href="faragir.php?contract='.$_GET['contract'].'">' . $read_contract['tbl_contract_code'] . '</a></span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                        </div></div>';

                        $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
                        $read_member = mysqli_fetch_array($result_member);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span>نام و نام خانوادگی فراگیر : <a href="faragir.php?contract='.$_GET['contract'].'&user='.$_GET['user'].'">' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</a></span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read_member['tbl_member_nationalcode']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read_member['tbl_member_mobile']) . '</span></div>
                    </div></div>';
                    }

                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table><tr><td class="col-sm-9">
                       
                       </td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="allmessage.php?do=search" method="POST">';
                       if (!empty($_GET['contract'])) {
                        echo '<input type="hidden" name="contract" value="' . (!empty($_GET['contract']) ? $_GET['contract'] : '') . '" >
                        <input type="hidden" name="user" value="' . $_GET['user'] . '" >';
                       }
                       echo '<input type="text" class="form-control" id="message_code" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        if(!empty($_GET['contract'])){
                        $result_for_count = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_user='$_GET[user]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_title LIKE '%$keyword%' ");
                    }
                    else {
                        $result_for_count = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_mentor='$this_user_id' and tbl_skillmessage_delete ='0' and tbl_skillmessage_title LIKE '%$keyword%' ");
                    }
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination       
                        if(!empty($_GET['contract'])){        
                        $result = Query("SELECT * From tbl_skillmessage Where tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_user='$_GET[user]' and tbl_skillmessage_title LIKE '%$keyword%' and tbl_skillmessage_delete ='0'  ORDER BY tbl_skillmessage_id DESC LIMIT $start_contnet,$show_per_page ");
                        }
                        else {
                            $result = Query("SELECT * From tbl_skillmessage Where tbl_skillmessage_reply='0' and tbl_skillmessage_mentor='$this_user_id' and tbl_skillmessage_title LIKE '%$keyword%' and tbl_skillmessage_delete ='0'  ORDER BY tbl_skillmessage_id DESC LIMIT $start_contnet,$show_per_page ");
                        }
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
                            . '<th>ارسال کننده</th>'
                            . '<th>' . $lang[61] . '</th>'
                            . '<th>' . $lang[600] . '</th>'
                            . '<th>نوع</th>'
                            . '<th>' . $lang[479] . '</th>'
                            . '<th>' . $lang[63] . '</th></tr>
                            </thead><tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mentor = $read['tbl_skillmessage_mentor'];
                            $resultMentor = Query("SELECT * From tbl_user Where tbl_user_id = '$mentor' ");
                            $readMentor = mysqli_fetch_array($resultMentor);

                            if ($read['tbl_skillmessage_status'] == 0) {
                                $style_check = "style='color:gray; font-style: italic;'";
                            } else {
                                $style_check = "";
                            }
                            $thisuser = $read['tbl_skillmessage_user'];
                            $result_userid = Query("SELECT * from tbl_member  Where tbl_member_code = '$thisuser' ");
                            $read_userid = mysqli_fetch_array($result_userid);
                            echo '<tr><td>' . $i . '</td>'
                                . '<td>' . ($read['tbl_skillmessage_sender'] == '1' ? 'فراگیر' : @$readMentor['tbl_user_name']) . '</td>'
                                . '<td>' . $read['tbl_skillmessage_title'] . '</td>'
                                . '<td>' . $read['tbl_skillmessage_dataadd'] . '</td>'
                                . '<td>' . ($read['tbl_skillmessage_note'] == '0' ? 'پیام' : 'اطلاعیه') . '</td>'
                                . '<td>' . ($read['tbl_skillmessage_status'] == '0' ? 'منتظر پاسخ' : ' پاسخ داده شده') . '</td>'
                                . '<td><a href="?do=view&code=' . $read['tbl_skillmessage_code'] . (!empty($_GET['contract']) ? '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] : '' ). '" class="btn btn-xs btn-warning">' . $lang[87] . '</a>'
                                . '<a href="?do=delete&code=' . $read['tbl_skillmessage_code'] . (!empty($_GET['contract']) ? '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] : '' ) . '"  class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
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
                                    <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['user']) ? '&user=' . $_GET['user'] : '').(isset($_GET['contract']) ? '&contract=' . $_GET['contract'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword(isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['user']) ? '&user=' . $_GET['user'] : '').(isset($_GET['contract']) ? '&contract=' . $_GET['contract'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="allmessage.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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
                    
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            if(!empty($_POST['contract']))
                            {
                                header("Location: allmessage.php?keyword=$keyword_search&user=$user&contract=$contract");
                            }
                            else {
                                header("Location: allmessage.php?keyword=$keyword_search");
                            }
                            
                        }
                    }
                    // end = search result  
                    if (isset($do) && $do == 'view') {
                        $result = Query("SELECT * FROM tbl_skillmessage Where tbl_skillmessage_code = '$code' order by tbl_skillmessage_id DESC");
                        while ($read = mysqli_fetch_array($result)) {

                            if ($read['tbl_skillmessage_sender'] == 0) {
                                $sender = '<span class="usersender">ارسال منتور</span>';
                            } else {
                                $sender = '<span class="adminsender">ارسال کاربر</span>';
                            }
                        ?>
                    <div class="col-lg-12 ticketview"><?= $sender ?>
                        <?= (!empty($read['tbl_skillmessage_dataadd']) ? '<div class="col-lg-12"><label>' . $lang[600] . ' : </label>' . $read['tbl_skillmessage_dataadd'] . '</div>' : '<div class="col-lg-12"><label>تاریخ جواب : </label>' . $read['tbl_skillmessage_datereplay'] . '</div>') ?>
                        <?= (!empty($read['tbl_skillmessage_title']) ? '<div class="col-lg-12"><label>' . $lang[61] . ' : </label>' . $read['tbl_skillmessage_title'] . '</div>' : '') ?>
                        <div class="col-lg-12"><label><?= $lang[94] ?> : </label><?= $read['tbl_skillmessage_text'] ?></div>
                        <?php if (!empty($read['tbl_skillmessage_attach'])) {
                                    $i = 1;
                                    $file = explode(",", $read['tbl_skillmessage_attach']);
                                    foreach ($file as $key => $value) {
                                        if (!empty($value)) {
                                            echo '<div class="col-lg-12"><label>فایل  ' . $i . ' : </label><a href="../upload/msgattach/' . $value . '"> دریافت فایل</a></div>';
                                            $i++;
                                        }
                                    }
                                } ?>
                        <div class="col-lg-12">
                            <hr>
                        </div>
                    </div>
                    <?php
                        }
                        $result = Query("SELECT * FROM tbl_skillmessage Where tbl_skillmessage_code = '$code' order by tbl_skillmessage_id DESC");
                        $read = mysqli_fetch_array($result);
                        ?>
                    <form action="?do=sendreplayticket" data-parsley-validate="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="skill" value="<?= $read['tbl_skillmessage_skill'] ?>">
                        <input type="hidden" name="plan" value="<?= $read['tbl_skillmessage_plan'] ?>">
                        <input type="hidden" name="contract" value="<?= (!empty($_GET['contract']) ? $_GET['contract'] : '') ?>">
                        <input type="hidden" name="user" value="<?= $_GET['user'] ?>">

                        <input type="hidden" name="tcode" value="<?= $code ?>">
                        <div class="col-lg-12 col-xs-12">
                            <h1><?= $lang[606] ?></h1>
                            <div class="form-group">
                                <label><?= $lang[94] ?></label>
                                <textarea rows="8" name="content" data-parsley-length="[10, 1000]" class="form-control"></textarea>
                            </div>
                            <?php
                                for ($i = 1; $i <= $img_item; $i++) {
                                    echo '<div class="form-group"><table><tr>
                                          <td><label class="col-sm-12 control-label">' . $lang[851] . '&nbsp; ' . $i . '</label></td>
                                          <td><input type="file" name="attach_' . $i . '"></td>
                                          </tr></table></div>';
                                }
                                ?>
                            <hr>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-success" value="<?= $lang[606] ?>">
                            </div>
                        </div>
                    </form>
                    <?php
                    }
                    if (isset($do) && $do == 'sendreplayticket') {
                        if (isset($_POST['submit'])) {
                            $code = strip_tags($_POST['tcode']);
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            $content = $_POST['content'];
                            $date = jdate("Y/m/d : a:h:s");
                            $images_name = '';
                            if (!$images_name) {
                                for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                    $img_[$i_image] = $_FILES['attach_' . $i_image]['name'];
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'attach_' . time() . '_' . $i_image . '.' . $extn;

                                    if ($extn == 'jpg' || $extn == 'JPG' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG'  || $extn == 'XLSX' || $extn == 'xlsx' || $extn == 'zip' || $extn == 'zip' || $extn == 'docx' || $extn == 'DOCX' || $extn == 'DOC' || $extn == 'doc' || $extn == 'PDF' || $extn == 'pdf') {
                                        $images_name .= $img_[$i_image] . ',';
                                        move_uploaded_file($_FILES['attach_' . $i_image]["tmp_name"], "../upload/msgattach/" . $img_[$i_image]);
                                    } elseif (!empty($exp[$i_image])) {
                                        $images_name .= $exp[$i_image] . ',';
                                    } else {
                                        $images_name .= ',';
                                    }
                                }
                            }


                            Query("update tbl_skillmessage set tbl_skillmessage_status='1',tbl_skillmessage_view='0',tbl_skillmessage_datereplay = '$date' where tbl_skillmessage_code ='$code' ");
                            Query("INSERT INTO tbl_skillmessage (
                                tbl_skillmessage_user,
                                tbl_skillmessage_skill,
                                tbl_skillmessage_plan,
                                tbl_skillmessage_contract,
                                tbl_skillmessage_text,
                                tbl_skillmessage_attach,
                                tbl_skillmessage_code,
                                tbl_skillmessage_mentor,
                                tbl_skillmessage_title,
                                tbl_skillmessage_dataadd,
                                tbl_skillmessage_delete,
                                tbl_skillmessage_reply,
                                tbl_skillmessage_sender) 
                                values('$user',
                                '$skill',
                                '$plan',
                                '$contract',
                                '$content',
                                '$images_name',
                                '$code',
                                '$this_user_id',
                                '$title',
                                '$date',
                                '0',
                                '1',
                                '0')");

                            $_SESSION['error'] = $lang[21];
                            header("Location: allmessage.php?skill=$skill&plan=$plan&user=$user&contract=$contract");
                            exit();
                        }
                    }
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_skillmessage Where tbl_skillmessage_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    <?= $lang[25] . '<br /><br />' . $read['tbl_skillmessage_title'] ?>
                    <div class="col-lg-6 col-lg-offset-6">
                        <form action="#" method="POST">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <input type="hidden" name="contract" value="<?= (!empty($_GET['contract']) ? $_GET['contract'] : '') ?>">
                            <input type="hidden" name="user" value="<?= (!empty($_GET['user']) ? $_GET['user'] : '') ?>">
                            <div class="form-group margin-top">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-xs btn-success">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-xs btn-danger">
                            </div>
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            Query("update tbl_skillmessage set  tbl_skillmessage_delete='1' Where tbl_skillmessage_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            if(!empty($_POST['contract'])){
                            header("Location: allmessage.php?user=$user&contract=$contract");
                            exit();
                        }
                        else {
                            header("Location: allmessage.php");
                        }
                        }
                        if (isset($_POST['nodelete'])) {
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            if(!empty($_POST['contract'])){
                                header("Location: allmessage.php?user=$user&contract=$contract");
                                exit();
                            }
                            else {
                                header("Location: allmessage.php");
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