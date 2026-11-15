<?php

// include
include './includer.php';
require_once __DIR__ . '/../include/SimpleXLSX.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = 'لیست پروژه ها';
$thisaccess = '1,5';
include './header.inc.php';

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <?php if (empty($do)) { ?>
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <?php }
        if (!empty($do) && $do == 'karfarma') { ?>
        <h5 class="card-header">کارفرمای پروژه</h5>
        <?php }
        if (!empty($do) && $do == 'import') { ?>
        <h5 class="card-header">ورود فراگیر ها</h5>
        <?php }
        if (!empty($do) && $do == 'importupload') { ?>
        <h5 class="card-header">ورود فراگیر ها</h5>
        <?php }
        if (!empty($do) && $do == 'importother') { ?>
        <h5 class="card-header">ورود فراگیر ها</h5>
        <?php } ?>

        <div class="card-body">
            <?php
            echo '<section class="content"><div class="col-md-12">
            <div class="box box-body table-responsive">';
            if (!empty($do) && $do == 'karfarma') {
                $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
                $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
                $read_contract = mysqli_fetch_array($result_contract);
                echo '<div class="box-info">
                            <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :' . $read_contract['tbl_contract_code'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                        </div>';

                echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">پروژه : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5></div>';
            }
            if (!empty($do) && $do == 'import') {
                $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
                $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
                $read_contract = mysqli_fetch_array($result_contract);
                echo '<div class="box-info">
                            <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :' . $read_contract['tbl_contract_code'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                        </div>';
                echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">پروژه : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a> / <a href="faragir.php?contract=' . $_GET['id'] . '"> لیست فراگیر ها </a></h5></div>';
            }
            if (!empty($do) && $do == 'importother') {
                $thiscontract = "Where tbl_attach_contract = '$_GET[id]'";
                $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[id]' ");
                $read_contract = mysqli_fetch_array($result_contract);
                echo '<div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :' . $read_contract['tbl_contract_code'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                    </div>';
                echo '<div class="col-12 col-gl-12 col-md-12"><h5 class="skill-title">پروژه : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a> / <a href="faragir.php?contract=' . $_GET['id'] . '"> لیست فراگیر ها </a></h5></div>';
            }
            // list content
            if ($access != 1) {
                if (!empty($access)) {
                    $thisaccess = "Where tbl_contract_mentor = '$userCode'";
                } else {
                    $thisaccess = "";
                }
            } else {
                $thisaccess = "";
            }
            if (!isset($do)) {
                // start count db result for pagination 

                $result_for_count = Query("SELECT * From tbl_contract $thisaccess ");
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);
                // end count db result for pagination         
                $result = Query("SELECT * From tbl_contract $thisaccess ORDER BY tbl_contract_id DESC LIMIT $start_contnet,$show_per_page ");
                // error : if not find record show no item for view
                $row = mysqli_num_rows($result);
                if (!isset($do) && $row == '0') {
                    echo '<div class="margin-top">' . $lang[57] . '</div>';
                }
                $result = Query("Select * from tbl_contract $thisaccess order by tbl_contract_id DESC LIMIT $start_contnet,$show_per_page");
                echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>کد کیس / پروژه </th>
                                    <th>ذینفع پروژه  / مشتری </th>
                                    <th> شروع </th>
                                    <th> پایان</th>
                                    <th> ویرایش</th>
                                    <th>زمان</th>
                                    <th>الحاقیه</th>
                                    <th>استاد راهنما </th>
                                    <th>وضعیت</th>
                                </tr>
                            </thead>
                            <tbody>';
                $i = 1;

                while ($read = mysqli_fetch_array($result)) {
                    $thisMentor = $read['tbl_contract_mentor'];
                    $result_m = Query("select tbl_user_name from tbl_user where tbl_user_usercode = '$thisMentor'  ");
                    $read_m = mysqli_fetch_array($result_m);
                    $contractId = $read['tbl_contract_id'];
                    $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                    $contractIdStr = '['.$contractId.'],';
                    $result_member = Query("select * from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
                    echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1"><a href="?do=view&id=' . $read['tbl_contract_id'] . '" >' . $read['tbl_contract_code'] . '<br>' . (!empty($read['tbl_contract_number']) ? 'شماره قرارداد :' . $read['tbl_contract_number'] : '') . '</a></td>
                                <td class="col-sm-1">' . $read['tbl_contract_customer'] . '</td>
                                <td class="col-sm-1">' . toPersianNum($read['tbl_contract_startdate']) . '</td>
                                <td class="col-sm-1">' . toPersianNum($read['tbl_contract_enddate']) . '</td>
                                <td class="col-sm-1">' . toPersianNum($read['tbl_contract_editdate']) . '</td>
                                <td class="col-sm-1">
                                ' . (!empty($read['tbl_contract_enddate']) ? toPersianNum(dateToTimeEnd($read['tbl_contract_enddate'])) : '') . ' 
                                </td>
                                <td class="col-sm-1">' . (@mysqli_num_rows($result_attach) > 0 ? @toPersianNum(mysqli_num_rows($result_attach)) : '') . '</td>
                                <td class="col-sm-1">' . @$read_m['tbl_user_name'] . '</td>
                                <td class="col-sm-1">
                                <label>
                                <input class="contractstatus" type="radio" name="n' . $read['tbl_contract_id'] . '" value="0-' . $read['tbl_contract_id'] . '" ' . ($read['tbl_contract_status'] == 0 ? 'checked' : '') . '> درحال اجرا </label><br><br>
                                 <label>
                                 <input  class="contractstatus" type="radio" name="n' . $read['tbl_contract_id'] . '" value="1-' . $read['tbl_contract_id'] . '" ' . ($read['tbl_contract_status'] == 1 ? 'checked' : '') . '> خاتمه پروژه </label>
                                 </td>
                                 </tr>
                                 <tr>
                                <td class="col-sm-1 btn-class" colspan="10">
                                <a href="?do=view&id=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-success " >نمایش</a>' . (mysqli_num_rows($result_attach) > 0 ? '<a  target="_blank" href="attach.php?contract=' . $read['tbl_contract_id'] . '" class="btn btn-warning btn-xs" target="_blank">الحاقیه ها</a>' : '') . '<a href="?do=karfarma&id=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-danger " >کارفرما پروژه</a><a href="faragir.php?contract=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-dark " > (' . (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') . ') لیست فراگیرها</a><a href="?do=stats&contract=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-info " > گزارش آماری پروژه</a>
                                </td>
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
                        <form action="contract.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
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


            // start = edit record
            if (isset($do) && $do == 'view') {
                $result = Query("SELECT * FROM tbl_contract Where tbl_contract_id = '$id'");
                $read = mysqli_fetch_array($result);
                ?>
            <div class="col-lg-8">
                <div class="view-detail">
                    <div class="form-group">
                        <label>کد کیس / پروژه : </label>
                        <p><?= $read['tbl_contract_code'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>شماره قرارداد : </label>
                        <p><?= $read['tbl_contract_number'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>ذینفع پروژه / مشتری : </label>
                        <p><?= $read['tbl_contract_customer'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>کد ملی : </label>
                        <p><?= $read['tbl_contract_nationalcode'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>شرح پروژه : </label>
                        <p><?= $read['tbl_contract_text'] ?></p>
                    </div>

                    <div class="form-group">
                        <label>تاریخ شروع : </label>
                        <p><?= $read['tbl_contract_startdate'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>تاریخ پایان : </label>
                        <p><?= $read['tbl_contract_enddate'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>تاریخ ویرایش : </label>
                        <p><?= $read['tbl_contract_editdate'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>کد بخش مرتبط :</label>
                        <p><?= $read['tbl_contract_codesection'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>نام بخش مرتبط : </label>
                        <p><?= $read['tbl_contract_namesection'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>مدیر پروژه : </label>
                        <p><?= $read['tbl_contract_manager'] ?></p>
                    </div>

                    <div class="form-group">
                        <label> شرح خدمات قرارداد : </label>
                        <p><?= $read['tbl_contract_textservice'] ?></p>
                    </div>
                    <?php if (!empty($read["tbl_contract_file"])) { ?>
                    <div class="form-group">
                        <label>فایل :</label>
                        <p><?php
                                    $file = explode(",", $read['tbl_contract_file']);
                                    foreach ($file as $key => $value) {
                                        if (!empty($value)) {
                                            echo '<a href="' . $value . '">فایل [' . $key . ']</a>';
                                        }
                                    } ?>
                        </p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            <?php
            }
            if (isset($do) && $do == 'karfarma') {
                $result = Query("SELECT * FROM tbl_contract Where tbl_contract_id = '$id'");
                $read = mysqli_fetch_array($result);
                echo '<div class="col-lg-6"><form action="?do=update" method="POST" >
                        <input type="hidden" name="id"  value="' . $id . '" class="form-control">
                        <div class="form-group">
                            <label>' . $lang[92] . '</label>
                            <input type="text" name="name"  value="' . $read['tbl_contract_karname'] . '" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>شماره موبایل</label>
                            <input type="text" name="mobile"  value="' . $read['tbl_contract_karmobile'] . '"  class="form-control" maxlength="11">
                        </div>
                        <div class="form-group">
                            <label>کلمه عبور</label>
                            <input type="text" name="password"  class="form-control" >
                        </div>';
                echo '<div class="form-group">
                            <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary">
                        </div></form></div>';
            }
            if (isset($do) && $do == 'update') {
                if (isset($_POST['submit'])) {
                    $id = $_POST['id'];
                    $name = $_POST['name'];
                    $mobile = $_POST['mobile'];
                    $password = $_POST['password'];
                    $date = date('Y-m-d H:i:s');
                    if (empty($password)) {
                        Query("Update tbl_contract SET tbl_contract_karname='$name' ,tbl_contract_karmobile='$mobile' ,tbl_contract_updatementor='$date' ,tbl_contract_idmentor='$this_user_id' Where tbl_contract_id = '$id' ");
                        $_SESSION['error'] = $lang[21];
                        header("Location: contract.php");
                        exit();
                    } else {
                        $npassword = hash("md5", $password);
                        Query("Update tbl_contract SET tbl_contract_karname='$name' ,tbl_contract_karmobile='$mobile' , tbl_contract_updatementor='$date' , tbl_contract_karpassword = '$npassword' , tbl_contract_idmentor='$this_user_id' Where tbl_contract_id = '$id' ");
                        $_SESSION['error'] = $lang[21];
                        header("Location: contract.php");
                        exit();
                    }
                }
            }
            if (isset($do) && $do == 'importother') {
                echo '<hr><h5>ورود فراگیر دستی</h5>
                        <div class="col-12 col-lg-7 col-md-12">

                            <form action="?do=importotheradd" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="contract" value="' . $id . '" required />
                            <div class="form-group ">
                            <label>نام</label>
                            <input type="text" name="fname" class="form-control">
                            </div>
                            <div class="form-group ">
                            <label>نام خانوادگی</label>
                            <input type="text" name="lname" class="form-control">
                            </div>
                            <div class="form-group ">
                            <label>کدملی</label>
                            <input type="text" name="nationalcode" class="form-control" maxlength="10">
                            </div>
                            <div class="form-group ">
                            <label>موبایل</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11">
                            </div>
                            <div class="form-group ">
                            <label>ایمیل</label>
                            <input type="text" name="email" class="form-control" >
                            </div>
                            
                            <div class="form-group" >
                                    <label>متن پیامک اولیه برای کاربران </label>
                                    <textarea rows="7" name="firstsmstext" class="form-control"></textarea>
                            </div>
                            <div class="form-group ">
                            <input type="submit" name="submit" value="افزودن فراگیر" class="btn btn-primary" />
                            </div>
                            </form></div>';
            }

            if (isset($do) && $do == 'importotheradd') {
                if (isset($_POST['submit'])) {
                    $firstsmstext = $_POST['firstsmstext'];
                    $contract = $_POST['contract'];
                    $fname = $_POST['fname'];
                    $lname = $_POST['lname'];
                    $nationalcode = number_convert($_POST['nationalcode']);
                    $mobile = number_convert($_POST['mobile']);
                    $email = $_POST['email'];
                    $date = jdate("Y/m/d - h:i:s A");
                    $systemcode = Code("", "no", 11);
                    $npass = password_hash("$nationalcode", PASSWORD_BCRYPT, array('cost' => 12));

                    $result = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode'");
                    $resultCheckContract = Query("select tbl_member_id,tbl_member_lastcontract from tbl_member where tbl_member_lastcontract like '%[$contract]%' and tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode'");
                    if (mysqli_num_rows($resultCheckContract) > 0) {
                    }
                    else if (mysqli_num_rows($result) > 0) {
                        $read = mysqli_fetch_array($result);
                        $json = $read['tbl_member_lastcontract'];
                        $json .=   '['.$contract.'],';
                        Query("update tbl_member set tbl_member_lastcontract='$json' where tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode'  ");
                    }
                     else {
                        $json= '['.$contract.'],';
                        Query("insert into tbl_member (tbl_member_firstcontract,
                                        tbl_member_importdate,
                                        tbl_member_fname,
                                        tbl_member_lname,
                                        tbl_member_nationalcode,
                                        tbl_member_mobile,
                                    tbl_member_systemcode,
                                    tbl_member_firstsms,
                                    tbl_member_firstsmstext,
                                    tbl_member_delete,
                                    tbl_member_lastcontract,
                                    tbl_member_level,
                                    tbl_member_password) values('$contract',
                                    '$date',
                                    '$fname',
                                    '$lname',
                                    '$nationalcode',
                                    '$mobile',
                                    '$systemcode',
                                    '1',
                                    '$firstsmstext',
                                    '0',
                                    '$json',
                                    '0',
                                    '$npass')");
                        if ($firstsmstext != '') {
                            //smsTwo($mobile_convert, $firstsmstext);
                        }
                        header("Location: contract.php");
                    }
                }
            }


            if (isset($do) && $do == 'import') {
                echo '<hr><h5>ورود فراگیر با اکسل</h5>
                            <form action="?do=importupload" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="' . $id . '" required />
                            <div class="form-group ">
                            <label>انتخاب فایل *.XLSX </label>
                            <input type="file" name="file"  required />
                            </div>
                            <hr>
                            <div class="form-group ">
                            <input type="submit" value="افزودن فایل" class="btn btn-primary" />
                            </div>
                            </form>';
            }

            if (isset($do) && $do == 'importupload') {
                echo '<hr><h5>لیست کاربران</h5>';

                if (isset($_FILES['file'])) {
                    $id = $_POST['id'];
                    unset($_SESSION['filename']);
                    if ($_FILES['file']) {
                        $file_attach = $_FILES['file']['name'];
                        $read_file_name = explode(".", $file_attach);
                        $extn = end($read_file_name);
                        $file_name = 'file_import_' . time() . '.' . $extn;
                        if ($extn == 'xlsx' || $extn == 'XLSX') {
                            move_uploaded_file($_FILES['file']["tmp_name"], "../upload/import/" . $file_name);
                        }
                    }
                    echo '<div class="import"><form action="?do=runaddmember" method="post" enctype="multipart/form-data">
        <input type="hidden" name="file" value="' . $file_name . '">
                <input type="hidden" name="contract" value="' . $id . '">
                <div class="input-wrapper">
                    <input type="text" name="fname" class="form-control formatnumber" maxlength="2">
                    <label> نام</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="lname" class="form-control formatnumber" maxlength="2">
                    <label> نام خانوادگی</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="nationalcode" class="form-control formatnumber" maxlength="2" required>
                    <label> کدملی</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="mobile" class="form-control formatnumber" maxlength="2">
                    <label> موبایل</label> 
                </div>
                <div class="input-wrapper">
                    <input type="text" name="email" class="form-control formatnumber" maxlength="2">
                    <label> ایمیل</label> 
                </div>
                </div>
                <div class="form-group" >
                        <label>متن پیامک اولیه برای کاربران </label>
                        <textarea rows="7" name="firstsmstext" class="form-control"></textarea>
                </div>

                <div class="form-group ">
                    <input type="submit" name="submit" value="افزودن کاربران" class="btn btn-primary" style="color: #fff;" />
                    </div>
              </form><hr>';
                    $_SESSION['filename'] = $file_name;
                    if ($xlsx = SimpleXLSX::parse('../upload/import/' . $file_name)) {
                        echo '<h5>فایل ارسال شده</h5><p>نکته ردیف اول وارد دیتابیس نمیشود<p>';
                        echo '<table class="table table-bordered table-hover table-striped margin-top">';
                        $dim = $xlsx->dimension();
                        $cols = $dim[0];
                        $m = 1;
                        $_SESSION['row'] = count($xlsx->rows());
                        for ($b = 0; $b <= 1; $b++) {
                            $h = $xlsx->rows();
                            $r = $h[$b];
                            if ($m == 1) {
                                echo '<tr>';
                                for ($i = 0; $i < $cols; $i++) {
                                    echo '<th>' . (isset($r[$i]) ? $r[$i] : '&nbsp;') . '(' . $i . ')</th>';
                                }
                                echo '</tr>';
                            } else {
                                echo '<tr>';
                                for ($i = 0; $i < $cols; $i++) {
                                    echo '<td>' . (isset($r[$i]) ? $r[$i] : '&nbsp;') . '</td>';
                                }
                                echo '</tr>';
                            }
                            $m++;
                        }
                        echo '</table>';
                    } else {
                        echo SimpleXLSX::parseError();
                    }
                }
            }


            if (isset($do) && $do == 'runaddmember') {

                $firstsmstext = $_POST['firstsmstext'];
                $file = $_POST['file'];
                $contract = $_POST['contract'];
                $fname = $_POST['fname'];
                $lname = $_POST['lname'];
                $nationalcode = $_POST['nationalcode'];
                $mobile = $_POST['mobile'];
                $email = $_SESSION['email'];
                $date = jdate("Y/m/d - h:i:s A");

                if (empty($_SESSION['count'])) {
                    $_SESSION['count'] = 0;
                }

                if ($xlsx = SimpleXLSX::parse('../upload/import/' . $file)) {
                    $dim = $xlsx->dimension();
                    $allData = count($xlsx->rows());
                    //$count = ($page * 10);
                    //echo $_SESSION['count'];
                    echo '<table class="table table-bordered" style="width:200px">';
                    if ($_SESSION['count'] == ($allData - 1)) {
                        echo '<div class="table_chart"><div>
                تعداد اعضای اضافه شده : ' . @$_SESSION['user_add'] . '</div><div>';
                        echo 'تعداد اعضای تکراری : ' . @$_SESSION['dublicate_user'] . '</div><div>';
                        unset($_SESSION['user_add']);
                        unset($_SESSION['dublicate_user']);
                        unset($_SESSION['count']);
                    } else {
                        for ($i = 1; $i < $allData; $i++) {
                            $cols = $dim[0];
                            $m = 1;
                            $mobile_convert = '';
                            $nationalcode_convert = '';
                            $systemcode = Code("", "no", 11);
                            @$r = $xlsx->rows()[$i];
                            @$mobile_convert = number_convert($r[$mobile]);
                            @$nationalcode_convert = number_convert($r[$nationalcode]);
                            $npass = password_hash("$nationalcode_convert", PASSWORD_BCRYPT, array('cost' => 12));
                            @$read_fname = $r[$fname];
                            @$read_lname = $r[$lname];
                            @$read_email = $r[$email];
                            @$read_father = $r[$father];
                            $result = Query("select tbl_member_id from tbl_member where tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode_convert'");
                            if (mysqli_num_rows($result) > 0) {
                                $read = mysqli_fetch_array($result);
                                Query("update tbl_member set tbl_member_lastcontract='$contract' where tbl_member_delete='0' and tbl_member_nationalcode = '$nationalcode_convert'  ");
                                @$_SESSION['dublicate_user']++;
                            } else {
                                echo '<tr><td>' . ($i + (($page - 1) * 50)) . '</td><td>' . @$r[$nationalcode] . '</td></tr>';
                                Query("insert into tbl_member (tbl_member_firstcontract,
                                        tbl_member_importdate,
                                        tbl_member_fname,
                                        tbl_member_lname,
                                        tbl_member_nationalcode,
                                        tbl_member_mobile,
                                    tbl_member_systemcode,
                                    tbl_member_firstsms,
                                    tbl_member_firstsmstext,
                                    tbl_member_delete,
                                    tbl_member_lastcontract,
                                    tbl_member_level,
                                    tbl_member_password) values('$contract',
                                    '$date',
                                    '$read_fname',
                                    '$read_lname',
                                    '$nationalcode_convert',
                                    '$mobile_convert',
                                    '$systemcode',
                                    '1',
                                    '$firstsmstext',
                                    '0',
                                    '$contract',
                                    '0',
                                    '$npass')");
                                @$_SESSION['user_add']++;
                                if ($firstsmstext != '') {
                                    //smsTwo($mobile_convert, $firstsmstext);
                                }
                            }
                            $_SESSION['count']++;
                        }
                    }
                } else {
                    header("Location: contract.php");
                }
            }

            if (isset($do) && $do == 'search') {
                if (isset($_POST['go'])) {
                    $keyword_search = $_POST['keyword'];
                    header("Location: contract.php?keyword=$keyword_search");
                }
            }
            echo '</div>
            </div></div>
        </div>';
            if (isset($do) && $do == 'stats') {

                if (!empty($_GET['contract'])) {
                    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
                    $read_contract = mysqli_fetch_array($result_contract);
                    echo '<div class="card"><div class="card-body"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/case.svg" class="img-fluid"><span>کد کیس / پروژه :' . $read_contract['tbl_contract_code'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>شماره قرارداد :' . $read_contract['tbl_contract_number'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/office.svg" class="img-fluid"><span>ذینفع پروژه / مشتری :' . $read_contract['tbl_contract_customer'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/section.svg" class="img-fluid"><span>کد بخش مرتبط :' . $read_contract['tbl_contract_codesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/name.svg" class="img-fluid"><span>نام بخش مرتبط :' . $read_contract['tbl_contract_namesection'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/manage.svg" class="img-fluid"><span>مدیر پروژه  :' . $read_contract['tbl_contract_manager'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ شروع :' . toPersianNum($read_contract['tbl_contract_startdate']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/time.svg" class="img-fluid"><span>تاریخ پایان :' . toPersianNum($read_contract['tbl_contract_enddate']) . '</span></div>
                    </div></div></div>';
                }

                $resultMember = Query("SELECT tbl_member_id From tbl_member where tbl_member_lastcontract='$_GET[contract]' ");
                $resultSkill = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_data !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultInterview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$_GET[contract]' and tbl_interview_delete='0'  ");
                
                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultCheckEnd = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$_GET[contract]' ");
                $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");
                
                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_status ='1' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");

                $resultPlans = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$_GET[contract]' and tbl_plan_delete='0'");

                $resultMember = Query("SELECT tbl_member_id From tbl_member where tbl_member_lastcontract='$_GET[contract]' ");
                
                $resultMessage = Query("SELECT tbl_skillmessage_id From tbl_skillmessage Where tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' ");
                $resultMessageRead = Query("SELECT tbl_skillmessage_id From tbl_skillmessage Where tbl_skillmessage_status ='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' ");
                $resultMessageCoach = Query("SELECT tbl_coachmessage_id From tbl_coachmessage Where tbl_coachmessage_reply='0' and tbl_coachmessage_contract='$_GET[contract]' and tbl_coachmessage_delete ='0' ");
                $resultMessageReadCoach = Query("SELECT tbl_coachmessage_id From tbl_coachmessage Where tbl_coachmessage_status ='0' and tbl_coachmessage_reply='0' and tbl_coachmessage_contract='$_GET[contract]' and tbl_coachmessage_delete ='0' ");

                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$_GET[contract]' ");
                

                $koleFaragir = mysqli_num_rows($resultMember);
                $bedonMaharat = mysqli_num_rows($resultMember) - mysqli_num_rows($resultSkill);
                $mosahebeha = mysqli_num_rows($resultInterview);
                $barnameRizinashode = $koleFaragir - mysqli_num_rows($resultPlan);
                $barnameRizishodeEblaghnashode = abs(mysqli_num_rows($resultPlan) - mysqli_num_rows($resultCheck));
                $barnameRizishodeEblaghShode = mysqli_num_rows($resultCheck) - mysqli_num_rows($resultPlan);
                $barnameRizishodeEblaghShodeEnd = mysqli_num_rows($resultCheckEnd) - mysqli_num_rows($resultPlan);
                echo '<div class="stats">
                        <div class="stats-item">
                        <span class="icon-dashboard"></span>
                            <h5>تاریخ امروز</h5>
                            <h6>'.toPersianNum(jdate("Y/m/d")).'</h6>
                        </div>
                        <div class="clear"></div>
                        <h1>آمار فراگیر</h1>
                        
                        <div class="stats-item">
                        <span class="icon-account_box"></span>
                        <h5>تعداد کل فراگیر ها</h5>
                        <h6>'.toPersianNum($koleFaragir).'</h6>
                        </div>

                        <div class="stats-item">
                        <span class="icon-assignment_ind"></span>
                        <h5>فراگیرهای بدون مهارت</h5>
                        <h6>'.toPersianNum($bedonMaharat).'</h6>
                        </div>
                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع مصاحبه ها</h5>
                        <h6>'.toPersianNum($mosahebeha).'</h6>
                        </div>
                        <div class="clear"></div>

                        <h1>آمار پلن</h1>

                        <div class="stats-item"><span class="icon-announcement"></span>
                        <h5>پلن برنامه ریزی نشده است</h5>
                        <h6>'.toPersianNum($barnameRizinashode).'</h6>
                        </div>


                        <div class="stats-item"><span class="icon-featured_play_list"></span>
                        <h5>پلن برنامه ریزی شده ابلاغ نشده</h5>
                        <h6>'.toPersianNum($barnameRizishodeEblaghnashode).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-assignment"></span>
                        <h5>برنامه ریزی و ابلاغ شده</h5>
                        <h6>'.toPersianNum($barnameRizishodeEblaghShode).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-broken_image"></span>
                        <h5>در حال اجرا</h5> 
                        <h6>'.toPersianNum(abs($barnameRizinashode+$barnameRizishodeEblaghnashode+$barnameRizishodeEblaghShode - $koleFaragir) - mysqli_num_rows($resultCheckEnd)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-broken_image"></span>
                        <h5>خاتمه یافته</h5> 
                        <h6>'.toPersianNum(mysqli_num_rows($resultCheckEnd)).'</h6>
                        </div>


                        <div class="clear"></div>
                        <h1>آمار پیامهای منتور</h1>
                        
                        <div class="clear"></div>
                        <div class="stats-item"><span class="icon-poll"></span>
                        <h5>مجموع  پیامها منتور</h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessage)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع  پیامهای بدون پاسخ منتور </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageRead)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat"></span>
                        <h5>مجموع  پیامهای پاسخ داده شده منتور </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessage) - mysqli_num_rows($resultMessageRead)).'</h6>
                        </div>
                        <div class="clear"></div>
                        <h1>آمار پیامهای مربی</h1>
                        <div class="stats-item"><span class="icon-poll"></span>
                        <h5>مجموع  پیامها مربی</h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageCoach)).'</h6>
                        </div>


                        <div class="stats-item"><span class="icon-chat_bubble"></span>
                        <h5>مجموع  پیامهای بدون پاسخ مربی  </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageReadCoach)).'</h6>
                        </div>

                        <div class="stats-item"><span class="icon-chat"></span>
                        <h5>مجموع  پیامهای پاسخ داده شده مربی </h5>
                        <h6>'.toPersianNum(mysqli_num_rows($resultMessageCoach) - mysqli_num_rows($resultMessageReadCoach)).'</h6>
                        </div>

                </div>';
            }
            ?>
            <?php
include './footer.inc.php';

?>