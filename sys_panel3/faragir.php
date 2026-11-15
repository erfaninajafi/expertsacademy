<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = 'لیست فراگیرها';
$thisaccess = '1,5';
include './header.inc.php';

$select = '';
if (!empty($_GET['user'])) {
    $select = " and tbl_member_id= '$_GET[user]'  ";
}
?>
<style>
.select2-container .select2-selection--multiple {

    height: auto;
}
</style>
<div class="col-md-12 mb-12">
    <div class="card">
        <?php if (empty($do)) { ?>
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <?php } ?>
        <div class="card-body">
            <?php
                    echo '<section class="content"><div class="col-md-12">
            <div class="box box-body '.(empty($do) ? 'table-responsive' : '').'">';


                    if (!empty($_GET['contract'])) {
                        $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$_GET[contract]]%' ";
                        $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
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
                    } else {
                        $thiscontract = " where tbl_member_delete='0'";
                    }
                    if (!isset($do)) {
                        echo '<table><tr><td class="col-sm-9">
                        <a href="contract.php?do=import&id=' . $_GET['contract'] . '" class="btn btn-primary margin-left" >ورود فراگیر با اکسل</a>
                                <a href="contract.php?do=importother&id=' . $_GET['contract'] . '" class="btn btn-primary margin-left">ورود فراگیر دستی</a>
                                </td>
                        <td class="col-sm-6" style="padding-left: 0px;"> 
                        <form action="faragir.php?do=search" method="POST">
                        <input type="text" class="form-control" name="keyword" value="' . $keyword . '">
                        <input type="hidden" class="form-control" name="contract" value="' . $_GET['contract'] . '">
                        </td>
                        <td class="col-sm-2" style="padding-left: 0px;">
                        <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                        </form></td></tr></table>';
                        // start count db result for pagination 
                        $result_for_count = Query("SELECT * From tbl_member  $thiscontract  and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%' ) $select ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination         
                        $result = Query("SELECT * From tbl_member $thiscontract and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%') $select ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
                        }
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>نام </th>
                                    <th>کدملی</th>
                                    <th>موبایل</th>
                                    <th>مهارت</th>
                                    <th>تاریخ ویرایش رزومه</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $memberContract = $_GET['contract'];
                            //find contract bakhsh
                            $result_contract = Query("SELECT tbl_contract_codesection From tbl_contract where tbl_contract_id='$memberContract' ");
                            $read_contract = mysqli_fetch_array($result_contract);
                            $codeSection = $read_contract['tbl_contract_codesection'];
                            // find bakhsh info
                            $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codeSection' and tbl_categories_lng='$language_default'");
                            $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                            $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read[tbl_member_id]' and tbl_memberquestion_contract='$_GET[contract]' ");
                            if (mysqli_num_rows($resultSelectQuestion) > 0) {
                                $readSelectQuestion = mysqli_fetch_array($resultSelectQuestion);
                                $arzyabi = json_decode($readSelectQuestion['tbl_memberquestion_data'], True);
                                $arzyabi = count($arzyabi);
                                $check = $readSelectQuestion['tbl_memberquestion_check'];
                                $adminCheck = $readSelectQuestion['tbl_memberquestion_admincheck'];
                            } else {
                                $arzyabi = 0;
                                $check = 0;
                                $adminCheck = 0;
                            }
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-2">' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="col-sm-1">' . toPersianNum($read['tbl_member_nationalcode']) . '</td>
                                <td class="col-sm-1">' . toPersianNum($read['tbl_member_mobile']) . '</td>
                                <td class="col-sm-4">
                                <form action="" id="formwork">
                                <select name="status[]" style="width: 100%;"
                                class="select2 status" multiple="multiple" >';
                            $result_skill = Query("SELECT tbl_categories_code,tbl_categories_title From tbl_categories Where tbl_categories_type='4' and tbl_categories_bakhsh = '$read_bakhsh[tbl_categories_code]' and tbl_categories_lng='$language_default'");
                            while ($read_skill = mysqli_fetch_array($result_skill)) {
                                $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
                                $read_memberskill = mysqli_fetch_array($result_memberskill);
                                $skillCode = $read_skill['tbl_categories_code'];
                                echo '<option value="' . $read_skill['tbl_categories_code'] . '" ' . (mysqli_num_rows($result_memberskill) > 0 ? (stripos($read_memberskill['tbl_memberskill_data'], "$skillCode") !== false ? 'selected' : '') : '') . '>' . $read_skill['tbl_categories_title'] . '</option>';
                            }
                            // <option value="0" ' . ($read['tbl_member_level'] == 0 ? 'selected' : '') . '>سطح مقدماتی</option>
                            // <option value="1" ' . ($read['tbl_member_level'] == 1 ? 'selected' : '') . '>سطح متوسط</option>
                            // <option value="2" ' . ($read['tbl_member_level'] == 2 ? 'selected' : '') . '>سطح پیشرفته</option>

                            echo ' </select>
                                <input type="hidden" id="userid" name="userid" value="' . $read['tbl_member_id'] . '">
                                <input type="hidden" id="contract" name="contract" value="' . $_GET['contract'] . '">
                                <input name="submit" type="submit" class="sendstatus" value="بروزرسانی" class="btn btn-xs btn-secondary">
                                </form>';
                                $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'  ");
                                $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$read[tbl_member_id]' and tbl_interview_delete='0'  ");
                                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
    
                                $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
    
    
                                $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                                $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");
        
                                $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");

                                echo '</td>
                                <td class="col-sm-1">' . $read['tbl_member_updateresume'] . '</td>
                                </tr>
                                <tr>
                                <td class="col-sm-12 btn-class" colspan="6">
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn btn-xs btn-primary " target="_blank"> رزومه</a><a href="?do=question&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-warning " > سوالات ارزیابی (' . toPersianNum($arzyabi) . ')</a><a href="?do=answer&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-secondary " target="_blank">جواب  (' . ($check == '0' ? 'شرکت نکرده' : ($check == '1' && $adminCheck == 0 ? ' بررسی نشده' : 'بررسی شده')) . ')</a><a href="interview.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-dark " >مصاحبه (' . toPersianNum(mysqli_num_rows($result_for_interview)) . ')</a><a href="plan.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-info " >';
                                if (mysqli_num_rows($resultPlan) == '0') {
                                    echo 'برنامه ریزی نشده است';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                    echo 'برنامه ریزی شده ابلاغ نشده';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                    echo 'برنامه ریزی و ابلاغ شده';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                    echo 'در حال اجرا';
                                }
                                else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                    echo 'خاتمه یافته';
                                }
                                if($access == '1' && mysqli_num_rows($resultCheckFinal) > 0){
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$_GET[contract]' and tbl_opinionend_model='1' and tbl_opinionend_delete='0' ");
                                if(mysqli_num_rows($resultOp) > 0)
                                {
                                    echo '<a href="?do=op&model=1&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-secondary " >نظرسنجی سامانه</a>';
                                }
                                $resultOp = Query("select tbl_opinionend_id from tbl_opinionend where tbl_opinionend_check='1' and tbl_opinionend_userid='$read[tbl_member_id]' and tbl_opinionend_contract='$_GET[contract]' and tbl_opinionend_model='2' and tbl_opinionend_delete='0' ");
                                if(mysqli_num_rows($resultOp) > 0)
                                {
                                    echo '<a href="?do=op&model=2&user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-secondary " >نظرسنجی منتور</a>';
                                }
                            }
                                echo '<a href="?do=delete&id=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-danger " >حذف</a>
                                <a href="allmessage.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-light " >پیام - پاسخ داده شده ('.toPersianNum(mysqli_num_rows($resultMessageReply)).') - پاسخ داده نشده ('.toPersianNum(mysqli_num_rows($resultMessage)).')</a>               
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
                            <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')   ?>"><?= $lang[75] ?></a>
                            </li>
                            <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                            </li>
                            <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['contract']) ? "&contract=" . $_GET['contract'] : '')  ?>"><?= $lang[74] ?></a>
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

                    if (isset($do) && $do == 'answer') {

                        $result_member = Query("SELECT * From tbl_member where tbl_member_id='$id'");
                        $read_member = mysqli_fetch_array($result_member);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span>نام و نام خانوادگی فراگیر : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read_member['tbl_member_nationalcode']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read_member['tbl_member_mobile']) . '</span></div>
                    </div></div>';

                        $result = 0;
                        $allQuestion = 0;
                        $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$_GET[contract]' ");
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        if ($read_select['tbl_memberquestion_check'] == '0') {
                            echo '<div class="show_msg_error">فراگیر هنوز این آزمون را انجام نداده است</div>';
                        } else {
                            $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                            $k=1;
                            foreach ($data as $keyexam => $valueexam) {
                                if ($keyexam != 'submit' && $keyexam != 'code') {
                                    $questionId = $keyexam;
                                    $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                    $read_question = mysqli_fetch_array($result_question);
                                    $all_value = json_decode($read_question['tbl_question_answer'], true);
                                    echo '<div class="form-group exam"><h2>'.toPersianNum($k) .' . ' . $read_question['tbl_question_title'] . '</h2>';
                                    $allQuestion++;
                                    foreach ($all_value as $key => $value) {
                                        $answervalue = $value["answervalue"];
                                        //var_dump($answervalue);
                                        if ($answervalue == 1 && $valueexam == $key) {
                                            $result++;
                                        }

                                        echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_question_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                                    }
                                    echo '</div><hr>';
                                    $k++;
                                }
                            }
                            echo '<div class="resultexam">
                            <span>مجموع سوالات : ' . $allQuestion . '</span>
                            <span>سوالات درست جواب داده : ' . $result . '</span>
                                    </div><br>';
                            if ($read_select['tbl_memberquestion_admincheck'] == '0') {
                                echo '<form action="?do=confrimexam" method="post">
                                            <input type="hidden" name="user" value="' . $id . '">
                                            <input type="hidden" name="contract" value="' . $_GET['contract'] . '">
                                            <input type="submit" name="updateitem" value="تایید آزمون" class="btn btn-primary">
                                    </form>';
                            } else {
                                echo '<div class="error-success">قبلا شما این آزمون را تایید کرده اید</div>';
                            }
                        }
                    }
                    if (isset($do) && $do == 'confrimexam') {
                        if (isset($_POST['updateitem'])) {
                            $user = $_POST['user'];
                            $contract = $_POST['contract'];
                            $date = jdate("Y/m/d : a:h:s");
                            Query("update tbl_memberquestion set tbl_memberquestion_admincheck='1',tbl_memberquestion_dateconfirm='$date',tbl_memberquestion_mentorconfirm='$this_user_id' where tbl_memberquestion_userid='$user' and tbl_memberquestion_contract='$contract' ");
                            header("Location: faragir.php?contract=$contract");
                        }
                    }
                    if (isset($do) && $do == 'question') {

                        $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
                        $read = mysqli_fetch_array($result);

                        echo '<div class="box-info">
                            <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span>نام و نام خانوادگی فراگیر : <a href="faragir.php?contract='.$_GET['contract'].'&user='.$id.'">' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</a></span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read['tbl_member_nationalcode']) . '</span></div>
                            <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read['tbl_member_mobile']) . '</span></div>
                        </div>';



                        $level = $read['tbl_member_level'];
                        $contract = $_GET['contract'];
                        echo '<div class="col-lg-6">
                        <h1>انتخاب از لیست سوالات</h1>
                        <div class="form-group">
                        <label><input type="checkbox" id="select_all"> انتخاب 10 سوال به صورت رندوم </label>
                        <hr>
                        </div>
                        <form action="?do=updatequestion" method="POST" class="checkboxlist">                    
                        <input type="hidden" name="user" value="' . $id . '">
                        <input type="hidden" name="contract" value="' . $contract . '">';

                        $result_memberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$id' and tbl_memberskill_contract='$contract' ");
                        $read_memberskill = mysqli_fetch_array($result_memberskill);
                        $skill = explode(",", $read_memberskill['tbl_memberskill_data']);
                        $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$contract' ");
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        @$array = json_decode($read_select['tbl_memberquestion_data'], true);
                        foreach ($skill as $key => $value) {
                            if (!empty($value)) {
                                $result_skill = Query("select tbl_categories_title from tbl_categories where tbl_categories_lng='$language_default' and tbl_categories_code='$value' ");
                                $read_skill = mysqli_fetch_array($result_skill);
                                echo '<div class="form-cat"><h3>' . $read_skill['tbl_categories_title'] . '</h3>';
                                $resultQuestion = Query("SELECT * From tbl_question where tbl_question_skill='$value' and tbl_question_lng='$language_default' and tbl_question_disable='0' ORDER BY tbl_question_order DESC , tbl_question_id DESC ");
                                if (mysqli_num_rows($resultQuestion) > 0) {
                                    while ($readQuestion = mysqli_fetch_array($resultQuestion)) {
                                        $ids = $readQuestion['tbl_question_code'];
                                        echo '<div class="form-group">
                            <label><input type="checkbox" class="checkrandom" name="question[]" value="' . $readQuestion['tbl_question_code'] . '" ' . (mysqli_num_rows($resultSelectQuestion) > 0 ? (!empty($array[$ids]) ? 'checked' : '') : '') . '> ' . $readQuestion['tbl_question_title'] . ' / ' . ($readQuestion['tbl_question_level'] == 0 ? 'سطح مقدماتی' : ($readQuestion['tbl_question_level'] == 1 ? 'سطح متوسط' : 'سطح پیشرفته')) . (!empty($readQuestion['tbl_question_img']) ? '<a href="../upload/question/'.$readQuestion['tbl_question_img'].'" target="_blank"> [تصویر]</a>' : '').'</label>
                            </div>';
                                    }
                                }
                            }
                        }

                        echo '<hr><div class="form-group">
                        <label>مهلت</label>
                        <input type="text" name="expire" class="form-control datepicker" value="' . $read_select['tbl_memberquestion_expire'] . '">
                        </div>
                        <div class="form-group">
                        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary" >
                        </div>';
                        echo '</form></div>';
                    }

                    if (isset($do) && $do == 'updatequestion') {
                        if (isset($_POST['updateitem'])) {
                            $userid = $_POST['user'];
                            $contract = $_POST['contract'];
                            $date = jdate("Y/m/d : a:h:s");
                            $question = $_POST['question'];
                            $expire = $_POST['expire'];
                            $all_value = array();
                            foreach ($question as $key => $value) {
                                if (!empty($value)) {
                                    $all_value[$value] = '1';
                                }
                            }
                            $all_value = json_encode($all_value, JSON_UNESCAPED_UNICODE);
                            $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$userid' and tbl_memberquestion_contract='$contract' ");
                            if (mysqli_num_rows($resultSelectQuestion) > 0) {
                                Query("update tbl_memberquestion set tbl_memberquestion_expire='$expire',tbl_memberquestion_admincheck='0',tbl_memberquestion_check='0',tbl_memberquestion_data='$all_value',tbl_memberquestion_date='$date',tbl_memberquestion_mentor='$this_user_id' where tbl_memberquestion_userid='$userid' and tbl_memberquestion_contract='$contract' ");
                            } else {
                                Query("insert into tbl_memberquestion (tbl_memberquestion_expire,tbl_memberquestion_check,tbl_memberquestion_userid,tbl_memberquestion_contract,tbl_memberquestion_data,tbl_memberquestion_date,tbl_memberquestion_mentor,tbl_memberquestion_admincheck) values('$expire','0','$userid','$contract','$all_value','$date','$this_user_id','0')");
                            }
                            header("Location: faragir.php?contract=$contract");
                        }
                    }

                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: faragir.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br>' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '
                        <form action="#" method="POST">
                            <input type="hidden" name="id_user" value="' . $id . '" >
                            <input type="hidden" name="contract" value="' . $_GET['contract'] . '" >
                            <div class="form-group margin-top">
                            <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success">
                            <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger">
                            </form></div>';

                        if (isset($_POST['submit'])) {
                            $id_user = $_POST['id_user'];
                            $contract = $_POST['contract'];
                            Query("update tbl_member set tbl_member_delete='1' Where tbl_member_id ='$id_user' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: faragir.php?contract=$contract");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            $contract = $_POST['contract'];
                            header("Location: faragir.php?contract=$contract");
                        }
                    }
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            $contract = $_POST['contract'];
                            header("Location: faragir.php?keyword=$keyword_search&contract=$contract");
                        }
                    }

                    if (isset($do) && $do == 'op') {

                        $result_member = Query("SELECT * From tbl_member where tbl_member_id='$_GET[user]'");
                        $read_member = mysqli_fetch_array($result_member);
                        echo '<div class="col-md-12"><div class="box-info">
                        <div class="item"><img src="asset/img/icons/item-icon/person.svg" class="img-fluid"><span>نام و نام خانوادگی فراگیر : ' . $read_member['tbl_member_fname'] . ' ' . $read_member['tbl_member_lname'] . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/number.svg" class="img-fluid"><span>کدملی :' . toPersianNum($read_member['tbl_member_nationalcode']) . '</span></div>
                        <div class="item"><img src="asset/img/icons/item-icon/mobile.svg" class="img-fluid"><span>شماره موبایل :' . toPersianNum($read_member['tbl_member_mobile']) . '</span></div>
                    </div></div>';

                        $allQuestion = 0;
                        $resultSelectQuestion = Query("SELECT * From tbl_opinionend where tbl_opinionend_delete='0' and tbl_opinionend_model='$_GET[model]' and tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_contract='$_GET[contract]' ");
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        
                            $data = json_decode($read_select['tbl_opinionend_data'], true);
                            $k=1;
                            foreach ($data as $keyexam => $valueexam) {
                                if ($keyexam != 'submit' && $keyexam != 'code') {
                                    $questionId = $keyexam;
                                    $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId' ");
                                    $read_question = mysqli_fetch_array($result_question);
                                    $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                                    echo '<div class="form-group exam"><h2>'.toPersianNum($k) .' . ' . $read_question['tbl_questionpublic_title'] . '</h2>';
                                    $allQuestion++;
                                    foreach ($all_value as $key => $value) {
                                        $answervalue = $value["answervalue"];
                                        echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_questionpublic_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_questionpublic_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                                    }
                                    echo '</div><hr>';
                                    $k++;
                                }
                            }
                        
                    }

                    
                    ?>
        </div>

    </div>
</div>
<?php
include './footer.inc.php';

?>