<?php
$Tabpage = '16';
$thisaccess = '1,2,3,4,5,6';
include './header.inc.php';
if(isset($do) && $do != 'stats') {
?>

<section class="content">
    <div class="col-md-12 mb-12">
        <div class="card">
            <div class="card-body">
                <div class="divider">
                    <div class="text-nowrap">
                        <?php
                    echo '<section class="content"><div class="col-md-12">
                    <div class="box box-body table-responsive">';
}
                    // list content

                    if (!isset($do)) {

                                $result = Query("SELECT * From tbl_contract where tbl_contract_karmobile='$this_user_id'   ORDER BY tbl_contract_id DESC");
                                $row = mysqli_num_rows($result);
                                if (!isset($do) && $row == '0') {
                                    echo '<div class="margin-top">' . $lang[57] . '</div>';
                                }
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
                                    <th>استاد راهنما </th>
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
                                <td class="col-sm-1">' . @$read_m['tbl_user_name'] . '</td>
                                
                                 </tr>
                                 <tr>
                                <td class="col-sm-1 btn-class" colspan="10">
                                <a href="?do=list&contract=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-dark " > (' . (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0') . ') لیست فراگیرها</a><a href="?do=stats&contract=' . $read['tbl_contract_id'] . '" class="btn btn-xs btn-info " > گزارش آماری پروژه</a>
                                </td>
                                </tr>';
                    $i++;
                }
                                echo '</tbody></table>';
                            }
                            else if (isset($do) && $do == 'list') {
                                $contractIdStr = '['.$_GET['contract'].'],';
                                $result = Query("SELECT * From tbl_member Where tbl_member_delete='0' and tbl_member_lastcontract = '$contractIdStr' ORDER BY tbl_member_id DESC ");
                                $row = mysqli_num_rows($result);
                                if ($row == '0') {
                                    echo '<div class="margin-top">' . $lang[57] . '</div>';
                                }
                                else {

                                    echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>نام </th>
                                    <th>کدملی</th>
                                    <th>موبایل</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $memberContract = $read['tbl_member_lastcontract'];
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
                                <td class="col-sm-1">' . toPersianNum($read['tbl_member_mobile']) . '</td>';
                                $result_for_plan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'  ");
                                $result_for_interview = Query("SELECT tbl_interview_id From tbl_interview where tbl_interview_lng='$language_default' and tbl_interview_contract='$_GET[contract]' and tbl_interview_menti='$read[tbl_member_id]' and tbl_interview_delete='0'  ");
                                $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read[tbl_member_id]' and tbl_memberskill_contract='$_GET[contract]' ");
    
                                $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
                                $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$_GET[contract]' and tbl_plan_menti='$read[tbl_member_id]' and tbl_plan_delete='0'");
    
    
                                $resultMessage = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_reply='0' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='1' ");
                                $resultMessageReply = Query("SELECT * From tbl_skillmessage where tbl_skillmessage_note='0' and tbl_skillmessage_status ='1' and tbl_skillmessage_user='$read[tbl_member_id]' and  tbl_skillmessage_contract='$_GET[contract]' and tbl_skillmessage_delete ='0' and tbl_skillmessage_view='0' ");
        
                                echo '</tr>
                                <tr>
                                <td class="col-sm-12 btn-class" colspan="6">
                                <a href="planview.php?user=' . $read['tbl_member_id'] . '&contract=' . $_GET['contract'] . '" class="btn btn-xs btn-info " >وضعیت پلن : ';
                                if (mysqli_num_rows($resultPlan) == '0') {
                                    echo 'برنامه ریزی نشده است';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0') {
                                    echo 'برنامه ریزی شده ابلاغ نشده';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0') {
                                    echo 'برنامه ریزی و ابلاغ شده';
                                } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0') {
                                    echo 'در حال اجرا';
                                }
                                echo '</a>             
                                    </td>
                                    </tr>';
                                $i++;
                                
                        }
                        // end show status   
                        echo '</tbody></table>';
                                }
                            }
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
                            if(isset($do) && $do != 'stats') {
                    ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
                            }
include './footer.inc.php';
?>