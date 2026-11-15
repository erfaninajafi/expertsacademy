<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[598];
$Tabpage = '6';
$thisaccess = '1,3';
include './header.inc.php';
// end include 
echo '<section class="content-header">
      <h1>
        ' . $pagetitle . '
      </h1>
    </section>
        <section class="content"><div class="col-md-12">
<div class="box box-body">';
// list content
if (isset($_GET['status'])) {
    $thisstatus = $_GET['status'];
    $status = "tbl_qteacher_status = '$thisstatus' and ";
} else {
    $status = '';
}

if (!isset($do)) {
    // search and add button
    echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9 top-worklist-menu">
                       <span><a href="quser.php"> همه </a></span>
                       <span><a href="quser.php?status=1"> پاسخ داده شده </a></span> 
                       <span><a href="quser.php?status=0"> دردست بررسی </a></span> 
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="quser.php?do=search" method="POST">
                       <input type="text" class="form-control" id="message_code" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-default" style="height: 33px; width:50px; padding:4px 8px;">' . $lang[77] . '</button>
                       </form></td></tr></table><div id="result"></div>';

    // start count db result for pagination               
    $result_for_count = Query("SELECT * From tbl_qteacher where $status tbl_qteacher_reply ='0' ");
    $count_result = mysqli_num_rows($result_for_count);
    $start_contnet =  ($page - 1) * $show_per_page;
    $page_number = ceil($count_result / $show_per_page);
    // end count db result for pagination               
    $result = Query("SELECT * From tbl_qteacher Where $status tbl_qteacher_reply ='0' and tbl_qteacher_code LIKE '%$keyword%' ORDER BY tbl_qteacher_id DESC LIMIT $start_contnet,$show_per_page ");
    // error : if not find record show no item for view
    $row = mysqli_num_rows($result);
    if (!isset($do) && $row == '0') {
        echo '<div class="margin-top">' . $lang[57] . '</div>';
        exit();
    }
    // start show list
    echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;" >
                <thead>
                    <tr><th>' . $lang[60] . '</th>
                    <th>ارسال کننده</th>
                    <th>نام دوره</th>
                    <th>' . $lang[61] . '</th>
                    <th>' . $lang[600] . '</th>
                    <th>' . $lang[479] . '</th>
                    <th>' . $lang[63] . '</th></tr>
                </thead>
                <tbody>';
    $i = 1;
    while ($read = mysqli_fetch_array($result)) {
        if ($read['tbl_qteacher_status'] == 0) {
            $style_check = "style='color:gray; font-style: italic;'";
        } else {
            $style_check = "";
        }
        $class = $read['tbl_qteacher_class'];
        $result_class = Query("select * from tbl_class where tbl_class_code='$class'");
        $read_class = mysqli_fetch_array($result_class);

        $thisuser = $read['tbl_qteacher_userid'];
        $result_userid = Query("SELECT * from tbl_member  Where tbl_member_code = '$thisuser' ");
        $read_userid = mysqli_fetch_array($result_userid);
        echo '<tr><td>' . $i . '</td>'
            . '<td>' . $read_userid['tbl_member_name'] . '</td>'
            . '<td>' . $read_class['tbl_class_title'] . '</td>'
            . '<td>' . $read['tbl_qteacher_title'] . '</td>'
            . '<td>' . $read['tbl_qteacher_date'] . '</td>'
            . '<td>' . ticket($read['tbl_qteacher_status']) . '</td>'
            . '<td><a href="?do=view&code=' . $read['tbl_qteacher_code'] . '" class="btn btn-xs btn-warning">' . $lang[87] . '</a>'
            . '<a href="?do=delete&code=' . $read['tbl_qteacher_code'] . '"  class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>'
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
        echo '<table class="table-striped margin-top"><tr><td class="col-sm-6"><ul class="pagination pagination-sm">
       <li><a href="?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '') . '">' . $lang[74] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword(isset($_GET['status']) ? '&status=' . $_GET['status'] : '') . (isset($_GET['member']) ? '&member=' . $_GET['member'] : '') . '">' . $lang[75] . '</a></li>
       </ul></td>
       <td class="col-sm-1">
       <form action="contact_quser.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">' . $lang[76] . '</button>
       </form></td></tr></table>';
    }
}
if (isset($do) && $do == 'gotopage') {
    if (isset($_POST['gotopage'])) {
        $page_number = $_POST['page_number'];
        header("Location: quser.php?page=$page_number");
    }
}
// end = pagination
// start = search result               
if (isset($do) && $do == 'search') {
    if (isset($_POST['go'])) {
        $keyword_search = $_POST['keyword'];
        header("Location: quser.php?keyword=$keyword_search");
    }
}
// end = search result  
// ////////////////////////////////////////////////////////////////////////////
// /////////////////////////////// start actions//////////////////////////////
if (isset($do) && $do == 'view') {
    $result = Query("SELECT * FROM tbl_qteacher Where tbl_qteacher_code = '$code' order by tbl_qteacher_id DESC");
    while ($read = mysqli_fetch_array($result)) {
        $userid = $read['tbl_qteacher_userid'];
        $file = explode(",", $read['tbl_qteacher_file']);
        if ($read['tbl_qteacher_sender'] == 0) {
            $sender = '<span class="usersender">' . $lang[607] . '</span>';
        } else {
            $sender = '<span class="adminsender">' . $lang[608] . '</span>';
        }
        echo '<div class="col-lg-12 ticketview">' . $sender . ''
            . '<div class="col-lg-12"><label>' . $lang[600] . ' : </label>' . $read['tbl_qteacher_date'] . '</div>'
            . (!empty($read['tbl_qteacher_title']) ? '<div class="col-lg-12"><label>' . $lang[61] . ' : </label>' . $read['tbl_qteacher_title'] . '</div>' : '') . ''
            . '<div class="col-lg-12"><label>' . $lang[94] . ' : </label>' . $read['tbl_qteacher_text'] . '</div>'
            . ($read['tbl_qteacher_sender'] == 0 && !empty($file[0]) ? '<div class="col-12 col-md-12"><label>فایل ضمیمه : </label><a href="../upload/document/' . $file[0] . '" target="_blank">مشاهده</a></div>' : '')
            . '<div class="col-lg-12"><hr></div></div>';
    }

    echo '<form action="?do=sendreplayticket" data-parsley-validate="" method="POST">
    <input type="hidden" name="tcode" value="' . $code . '">
    <input type="hidden" name="userid" value="' . $userid . '">'
        . '<div class="col-lg-12 col-xs-12"><h1>' . $lang[606] . '</h1>'
        . '<div class="form-group">'
        . '<label>' . $lang[94] . '</label>'
        . '<textarea rows="8" name="msg" data-parsley-length="[10, 1000]" required="" class="form-control"></textarea>'
        . '</div><input type="submit" name="submit" class="btn btn-success" value="' . $lang[606] . '"></div>'
        . '</form>';
}
if (isset($do) && $do == 'sendreplayticket') {
    if (isset($_POST['submit'])) {
        $date = jdate("Y/m/d - g:i a");
        $text = strip_tags($_POST['msg']);
        $code = strip_tags($_POST['tcode']);
        $userid = $_POST['userid'];

        Query("update tbl_qteacher set tbl_qteacher_status = '1' , tbl_qteacher_datereplay = '$date' where tbl_qteacher_userid !='0' and tbl_qteacher_code ='$code' ");
        Query("insert into tbl_qteacher (tbl_qteacher_text,tbl_qteacher_date,tbl_qteacher_code,tbl_qteacher_reply,tbl_qteacher_sender,tbl_qteacher_userid) values('$text','$date','$code','1','1','$userid') ");
        $_SESSION['error'] = $lang[21];
        header("Location: quser.php");
        exit();
    }
}
if (isset($do) && $do == 'delete') {
    $result = Query("SELECT * from tbl_qteacher Where tbl_qteacher_code = '$code' ");
    $read = mysqli_fetch_array($result);
    echo $lang[25] . '<br /><br />' . $read['tbl_qteacher_code'];
    echo '<div class="col-lg-6 col-lg-offset-6">
                <form action="#" method="POST">
                <input type="hidden" name="code" value="' . $code . '" >
                <div class="form-group margin-top">
                <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
                </form></div>';

    if (isset($_POST['submit'])) {
        $code = $_POST['code'];


        Query("Delete From tbl_qteacher Where tbl_qteacher_code = '$code' ");
        $_SESSION['error'] = $lang[28];
        header("Location: quser.php");
        exit();
    }
    if (isset($_POST['nodelete'])) {
        header("Location: quser.php");
    }
}
echo '</div></div></section>';

include './footer.inc.php';
