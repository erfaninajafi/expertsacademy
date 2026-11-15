<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[822];
$img_item = '1';
$Tabpage = '90';
$thisaccess = '1,3,5';
include './header.inc.php';
// end include 
if (!empty($_GET['contract'])) {
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$_GET[contract]]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    $result_project = Query("SELECT tbl_user_name From tbl_user where tbl_user_usercode='$read_contract[tbl_contract_project]'");
    $read_project = mysqli_fetch_array($result_project);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_project['tbl_user_name'] . '</h5>
            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

    </div>
    </div>';


    $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
    $read_member = mysqli_fetch_array($result_member);
    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[1614].'  : <a href="faragir.php?contract='.$_GET['contract'].'&user='.$_GET['user'].'">' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</a></h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[385].'  : ' . toPersianNum($read_member['tbl_member_nationalcode']) . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[378].'  : ' . toPersianNum($read_member['tbl_member_mobile']) . '</h5>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>                    
    </div>
    </div>';
}
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
                                <a href="?do=add&skill=' . $_GET['skill'] . '&plan=' . $_GET['plan'] . '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
</div>

                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <?php
                            echo '<input type="hidden" name="skill" value="' . (!empty($_GET['skill']) ? $_GET['skill'] : '') . '" >
                            <input type="hidden" name="plan" value="' . (!empty($_GET['plan']) ? $_GET['plan'] : '') . '" >
                            <input type="hidden" name="contract" value="' . (!empty($_GET['contract']) ? $_GET['contract'] : '') . '" >
                            <input type="hidden" name="user" value="' . $_GET['user'] . '" >';
                               ?>
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
                    <?php
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_reply='0' and tbl_skillmessage_plan='$_GET[plan]' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_skill='$_GET[skill]' and tbl_skillmessage_user='$_GET[user]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_title LIKE '%$keyword%' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_skillmessage Where tbl_skillmessage_reply='0' and tbl_skillmessage_plan='$_GET[plan]' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_skill='$_GET[skill]' and tbl_skillmessage_user='$_GET[user]' and tbl_skillmessage_title LIKE '%$keyword%' and tbl_skillmessage_delete ='0'  ORDER BY tbl_skillmessage_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                            <th>'.$lang[60].'</th>
                            <th>'.$lang[541].'</th>
                            <th>'.$lang[61].'</th>
                            <th>'.$lang[600].'</th>
                            <th>'.$lang[1436].'</th>
                            <th>'.$lang[1340].'</th>
                            <th>'.$lang[63].'</th>
                            </tr>
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
                            echo '<tr><td class="align-middle p-2">' . $i . '</td>'
                                . '<td class="align-middle p-2">' . ($read['tbl_skillmessage_sender'] == '1' ? 'فراگیر' : @$readMentor['tbl_user_name']) . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_skillmessage_title'] . '</td>'
                                . '<td class="align-middle p-2">' . $read['tbl_skillmessage_dataadd'] . '</td>'
                                . '<td class="align-middle p-2">' . ($read['tbl_skillmessage_note'] == '0' ? 'پیام' : 'اطلاعیه') . '</td>'
                                . '<td class="align-middle p-2">' . ($read['tbl_skillmessage_status'] == '0' ? 'منتظر پاسخ' : ' پاسخ داده شده') . '</td>'
                                . '<td class="align-middle p-2"><a href="?do=view&code=' . $read['tbl_skillmessage_code'] . (!empty($_GET['contract']) ? '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] : '' ). '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '"><span class="bi-eye-fill"></span></a>'
                                . '<a href="?do=delete&code=' . $read['tbl_skillmessage_code'] . (!empty($_GET['contract']) ? '&user=' . $_GET['user'] . '&contract=' . $_GET['contract'] : '' ) . '"  class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a></td>'
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
                                <form action="skillmessage.php?do=gotopage" class="search-form" method="POST">
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
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-8 col-12 p-3">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data" >
                           <input type="hidden" name="code" value="' . Code("", "no", 7) . '" >
                           <input type="hidden" name="skill" value="' . (!empty($_GET['skill']) ? $_GET['skill'] : '') . '" >
                           <input type="hidden" name="plan" value="' . (!empty($_GET['plan']) ? $_GET['plan'] : '') . '" >
                           <input type="hidden" name="contract" value="' . (!empty($_GET['contract']) ? $_GET['contract'] : '') . '" >
                           <input type="hidden" name="user" value="' . $_GET['user'] . '" >';

                        echo '<div class="form-group">
                              <label>' . $lang[50] . '</label>
                              <input type="text" name="title" class="form-control">
                              </div>';

                        echo '<div class="form-group">
                                  <label>' . $lang[51] . '</label>
                                  <textarea name="content" class="editor editor_fa"></textarea>
                                  </div>';

                        for ($i = 1; $i <= $img_item; $i++) {
                            echo '<div class="form-group file-margin"><table><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[851] . '&nbsp; ' . $i . '</label></td>
                                  <td><input type="file" class="form-control" name="attach_' . $i . '"></td>
                                  </tr></table></div>';
                        }
                        echo '<hr><div class="form-group check-style">
                              <label><input type="checkbox" name="note" value="1">'.$lang[1472].' 
                              </label>
                              </div>';
                        echo '<div class="form-group">
                             <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                             </div>
                        </form></div>';
                    }
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            $code = $_POST['code'];
                            $note = (!empty($_POST['note']) ? $_POST['note'] : '0');
                            $title = addslashes($_POST['title']);
                            $content = addslashes($_POST['content']);
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
                            Query("INSERT INTO tbl_skillmessage (
                                tbl_skillmessage_user,
                                tbl_skillmessage_skill,
                                tbl_skillmessage_plan,
                                tbl_skillmessage_contract,
                                tbl_skillmessage_text,
                                tbl_skillmessage_attach,
                                tbl_skillmessage_code,
                                tbl_skillmessage_note,
                                tbl_skillmessage_mentor,
                                tbl_skillmessage_title,
                                tbl_skillmessage_dataadd,
                                tbl_skillmessage_delete,
                                tbl_skillmessage_reply,
                                tbl_skillmessage_sender,
                                tbl_skillmessage_status,
                                tbl_skillmessage_view) 
                                values('$user',
                                '$skill',
                                '$plan',
                                '$contract',
                                '$content',
                                '$images_name',
                                '$code',
                                '$note',
                                '$this_user_id',
                                '$title',
                                '$date',
                                '0',
                                '0',
                                '0',
                                '0',
                                '0')");

                            $_SESSION['error'] = $lang[32];
                            header("Location: skillmessage.php?skill=$skill&plan=$plan&user=$user&contract=$contract");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            header("Location: skillmessage.php?keyword=$keyword_search&skill=$skill&plan=$plan&user=$user&contract=$contract");
                        }
                    }
                    // end = search result  
                    if (isset($do) && $do == 'view') {
                        $result = Query("SELECT * FROM tbl_skillmessage Where tbl_skillmessage_code = '$code' order by tbl_skillmessage_id DESC");
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_skillmessage_sender'] == 0) {
                               $sender = '<span class="usersender">'.$lang[1628].'</span>';
                           } else {
                               $sender = '<span class="adminsender">'.$lang[1629].'</span>';
                           }
                           $attach = explode(",",$read['tbl_skillmessage_attach']);
                       ?>
               
               
               
               <div class="col-xxl-12 col-xl-12">
               
                   <div class=" mt-3">
                       <div class="card-header bg-light">
                           <h5><svg class="svg-inline--fa fa-envelope fa-w-16 me-2" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="envelope" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                   <path fill="currentColor" d="M502.3 190.8c3.9-3.1 9.7-.2 9.7 4.7V400c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V195.6c0-5 5.7-7.8 9.7-4.7 22.4 17.4 52.1 39.5 154.1 113.6 21.1 15.4 56.7 47.8 92.2 47.6 35.7.3 72-32.8 92.3-47.6 102-74.1 131.6-96.3 154-113.7zM256 320c23.2.4 56.6-29.2 73.4-41.4 132.7-96.3 142.8-104.7 173.4-128.7 5.8-4.5 9.2-11.5 9.2-18.9v-19c0-26.5-21.5-48-48-48H48C21.5 64 0 85.5 0 112v19c0 7.4 3.4 14.3 9.2 18.9 30.6 23.9 40.7 32.4 173.4 128.7 16.8 12.2 50.2 41.8 73.4 41.4z"></path>
                               </svg><span>#<?= $read['tbl_skillmessage_code'] ?></span></h5>
                       </div>
                       <div class="p-3">
                           <div class="d-md-flex d-xl-inline-block d-xxl-flex align-items-center justify-content-between mb-x1">
                               <div class="d-flex align-items-center gap-2">
                                   <p class="mb-0"><?=$sender?></p>
                               </div>
                               <p class="mb-0 fs--2 fs-sm--1 fw-semi-bold mt-2 mt-md-0 mt-xl-2 mt-xxl-0 ms-5"><?= $read['tbl_skillmessage_dataadd'] ?><svg class="svg-inline--fa fa-star fa-w-18 ms-2 text-warning" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="star" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg="">
                                       <path fill="currentColor" d="M259.3 17.8L194 150.2 47.9 171.5c-26.2 3.8-36.7 36.1-17.7 54.6l105.7 103-25 145.5c-4.5 26.3 23.2 46 46.4 33.7L288 439.6l130.7 68.7c23.2 12.2 50.9-7.4 46.4-33.7l-25-145.5 105.7-103c19-18.5 8.5-50.8-17.7-54.6L382 150.2 316.7 17.8c-11.7-23.6-45.6-23.9-57.4 0z"></path>
                                   </svg>
                               </p>
                           </div>
                           <div>
                               <h6 class="mb-3 fw-semi-bold text-1000"><?=$read['tbl_skillmessage_title']?></h6>
                               <p><?= $read['tbl_skillmessage_text'] ?></p>
                               <?php if(!empty($attach[0])) {?>
                               <div class="p-x1 bg-light rounded-3 mt-3">
                                   <div class="d-inline-flex flex-column">
                                       <div class="border p-2 rounded-3 d-flex bg-white dark__bg-1000 fs--1 mb-2"><svg class="svg-inline--fa fa-image fa-w-16 fs-1" aria-hidden="true" focusable="false" data-prefix="far" data-icon="image" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                                               <path fill="currentColor" d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 336H54a6 6 0 0 1-6-6V118a6 6 0 0 1 6-6h404a6 6 0 0 1 6 6v276a6 6 0 0 1-6 6zM128 152c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40zM96 352h320v-80l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L192 304l-39.515-39.515c-4.686-4.686-12.284-4.686-16.971 0L96 304v48z"></path>
                                           </svg><span class="ms-2 me-3"><?=$read['tbl_skillmessage_attach']?></span><a class="text-300 ms-auto" href="<?=$site_url?>upload/attach/<?=$read['tbl_skillmessage_attach']?>" target="_blank" data-bs-toggle="tooltip" data-bs-placement="right" aria-label="Download" data-bs-original-title="Download"><svg class="svg-inline--fa fa-arrow-down fa-w-14" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="arrow-down" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" data-fa-i2svg="">
                                                   <path fill="currentColor" d="M413.1 222.5l22.2 22.2c9.4 9.4 9.4 24.6 0 33.9L241 473c-9.4 9.4-24.6 9.4-33.9 0L12.7 278.6c-9.4-9.4-9.4-24.6 0-33.9l22.2-22.2c9.5-9.5 25-9.3 34.3.4L184 343.4V56c0-13.3 10.7-24 24-24h32c13.3 0 24 10.7 24 24v287.4l114.8-120.5c9.3-9.8 24.8-10 34.3-.4z"></path>
                                               </svg></a></div>
               
                                   </div>
                               </div>
                               <?php } ?>
                           </div>
               
               
                       </div>
               
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
                        <div class="col-lg-12 p-3">
                            <hr>
                            <h3><?= $lang[606] ?></h3>
                            <div class="form-group">
                                <label><?= $lang[94] ?></label>
                                <textarea rows="8" name="content" data-parsley-length="[10, 1000]" class="form-control"></textarea>
                            </div>
                            <?php
                                for ($i = 1; $i <= $img_item; $i++) {
                                    echo '<div class="form-group file-margin"><table><tr>
                                          <td><label class="col-sm-12 control-label">' . $lang[851] . '&nbsp; ' . $i . '</label></td>
                                          <td><input type="file" class="form-control" name="attach_' . $i . '"></td>
                                          </tr></table></div>';
                                }
                                ?>
                            <hr>
                            <div class="form-group">
                                <input type="submit" name="submit" class="btn btn-primary mt-3" value="<?= $lang[606] ?>">
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
                            header("Location: skillmessage.php?skill=$skill&plan=$plan&user=$user&contract=$contract");
                            exit();
                        }
                    }
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_skillmessage Where tbl_skillmessage_code = '$code' ");
                        $read = mysqli_fetch_array($result);
                    ?>
                    
                    <div class="col-lg-8 col-12 p-3">
                    <p><?= $lang[25] . '</p><h3>' . $read['tbl_skillmessage_title'] ?></h3>
                        <form action="#" method="POST">
                            <input type="hidden" name="code" value="<?= $code ?>">
                            <input type="hidden" name="skill" value="<?= (!empty($_GET['skill']) ? $_GET['skill'] : '') ?>">
                            <input type="hidden" name="plan" value="<?= (!empty($_GET['plan']) ? $_GET['plan'] : '') ?>">
                            <input type="hidden" name="contract" value="<?= (!empty($_GET['contract']) ? $_GET['contract'] : '') ?>">
                            <input type="hidden" name="user" value="<?= $_GET['user'] ?>">
                            <div class="form-group margin-top">
                                <input type="submit" name="submit" value="<?= $lang[26] ?>" class="btn btn-xs btn-success mt-3">
                                <input type="submit" name="nodelete" value="<?= $lang[27] ?>" class="btn btn-xs btn-danger mt-3">
                            </div>
                        </form>
                    </div>
                    <?php
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            Query("update tbl_skillmessage set  tbl_skillmessage_delete='1' Where tbl_skillmessage_code = '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: skillmessage.php?skill=$skill&plan=$plan&user=$user&contract=$contract");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            $skill = $_POST['skill'];
                            $plan = $_POST['plan'];
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            header("Location: skillmessage.php?&skill=$skill&plan=$plan&user=$user&contract=$contract");
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