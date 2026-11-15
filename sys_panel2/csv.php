<?php
session_start();
include './includer.php';
if(isset($_GET['do']))
{
    $do = $_GET['do'];
}
$do = $_GET['do'];
include '../include/lang/panel_fa.php';
$all = '';
$allfinal = '';
header('Content-Encoding: UTF-8');
header('Content-type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$do.'-'.time().'.csv'.'"'); 
header("Pragma: no-cache");
header("Expires: 0");
header('Content-Transfer-Encoding: binary');
echo "\xEF\xBB\xBF";



if($do == 'report6') {

    $result = Query("SELECT 
    p.tbl_projectmember_mentor,
    p.tbl_projectmember_contract,
    p.tbl_projectmember_id,
    p.tbl_projectmember_user,
    p.tbl_projectmember_user AS tbl_projectmember, 
    m.tbl_member_lastcontract, 
    m.tbl_member_id,
    m.tbl_member_delete AS tbl_member
FROM
    tbl_member m
INNER JOIN tbl_projectmember p 
ON p.tbl_projectmember_contract = '$getcontract' 
and p.tbl_projectmember_user= m.tbl_member_id 
and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
and m.tbl_member_delete='0' ORDER BY p.tbl_projectmember_id DESC ");

    $i = 1;
while ($read = mysqli_fetch_array($result))
{
   
    $mentor = $read['tbl_projectmember_mentor'];
                            $resultMentor = Query("select * from tbl_user where tbl_user_usercode='$mentor'");
                            $readMentor= mysqli_fetch_array($resultMentor);
                            $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
                            $read_m = mysqli_fetch_array($result_m);
                            if(mysqli_num_rows($result_m) > 0 ){
                                $maharat = '';
                                $countPlan = 0;
                                $countPlanConfirm = 0;
                                $resultMemberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                $readMemberskill = mysqli_fetch_array($resultMemberskill);
                                 if(!empty($readMemberskill['tbl_memberskill_data'])){
                                    $data = explode(",",$readMemberskill['tbl_memberskill_data']);
                                    foreach ($data as $key => $value) {
                                        if(!empty($value))
                                        {
                                            $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default'");
                                            $readSkill = mysqli_fetch_array($resultSkill);
                                            $resultPlan = Query("select * from tbl_plan where tbl_plan_model='1235' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_contract='$getcontract' and tbl_plan_skill='$value' ");   
                                            if(mysqli_num_rows($resultPlan) > 0) {  
                                            while ($readPlan = mysqli_fetch_array($resultPlan)) {
                                                
                                                $resultCoach = Query("select * from tbl_user where tbl_user_id='$readPlan[tbl_plan_coach]'");
                                                $readCoach= mysqli_fetch_array($resultCoach);         
                                                
                                                $all .= $i.'/[]/';
                                                $all .= (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '').'/[]/';
                                                $all .= $read_m['tbl_member_nationalcode'].'/[]/';
                                                $all .= $read_m['tbl_member_mobile'].'/[]/';
                                                $all .= $readSkill['tbl_categories_title'].'/[]/';
                                                $all .= $readMentor['tbl_user_name'].'/[]/';
                                                $all .= $readPlan['tbl_plan_coach'].'/[]/';
                                                 $all .='/++/';
                                                    $i++;


                                            }                                       
                                        }
                                        else {
                                                    $all .= $i.'/[]/';
                                                $all .= (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '').'/[]/';
                                                $all .= $read_m['tbl_member_nationalcode'].'/[]/';
                                                $all .= $read_m['tbl_member_mobile'].'/[]/';
                                                $all .= $readSkill['tbl_categories_title'].'/[]/';
                                                $all .= $readMentor['tbl_user_name'].'/[]/';
                                                $all .= '/[]/';
                                                 $all .='/++/';
                                                    $i++;
                                        }

                                        }
                                    }
                                }
                               
                                
                            }

    
}

echocsv(array("$lang[60]","$lang[92]","$lang[385]","$lang[378]","$lang[1337]","$lang[1447]","$lang[2054]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);

}
}






if($do == 'report5') {

    $result = Query("SELECT 
    p.tbl_projectmember_mentor,
    p.tbl_projectmember_contract,
    p.tbl_projectmember_id,
    p.tbl_projectmember_user,
    p.tbl_projectmember_user AS tbl_projectmember, 
    m.tbl_member_lastcontract, 
    m.tbl_member_id,
    m.tbl_member_delete AS tbl_member
FROM
    tbl_member m
INNER JOIN tbl_projectmember p 
ON p.tbl_projectmember_contract = '$getcontract' 
and p.tbl_projectmember_user= m.tbl_member_id 
and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
and m.tbl_member_delete='0' ORDER BY p.tbl_projectmember_id DESC ");

    $i = 1;
while ($read = mysqli_fetch_array($result))
{
    $mentor = $read['tbl_projectmember_mentor'];
        $resultMentor = Query("select * from tbl_user where tbl_user_usercode='$mentor'");
        $readMentor= mysqli_fetch_array($resultMentor);
        $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
        $read_m = mysqli_fetch_array($result_m);
        if(mysqli_num_rows($result_m) > 0 ){
            
            $allQuestion = 0;
            $resultQuestion = 0;
            $resultSelectQuestion = Query("SELECT * From tbl_opinionend where tbl_opinionend_delete='0' and tbl_opinionend_model='1' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' ");
            if(mysqli_num_rows($resultSelectQuestion) > 0 ){
            $read_select = mysqli_fetch_array($resultSelectQuestion);
            
                $data = json_decode($read_select['tbl_opinionend_data'], true);
                $k=1;
                foreach ($data as $keyexam => $valueexam) {
                    if ($keyexam != 'submit' && $keyexam != 'code') {
                        $questionId = explode("-",$keyexam);
                        $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId[1]' ");
                        $read_question = mysqli_fetch_array($result_question);
                        $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                        $allQuestion++;
                        foreach ($all_value as $key => $value) {
                            $answervalue = $value["answervalue"];
                            if ($answervalue == 1 && $valueexam == $key) {
                                $resultQuestion++;
                            }
                        }
                        $k++;
                    }
                }
            }  


            $allQuestion2 = 0;
            $resultQuestion2 = 0;
            $resultSelectQuestion = Query("SELECT * From tbl_opinionend where tbl_opinionend_delete='0' and tbl_opinionend_model='0' and tbl_opinionend_userid='$read_m[tbl_member_id]' and tbl_opinionend_contract='$getcontract' ");
            if(mysqli_num_rows($resultSelectQuestion) > 0 ){
            $read_select = mysqli_fetch_array($resultSelectQuestion);
            
                $data = json_decode($read_select['tbl_opinionend_data'], true);
                $k=1;
                foreach ($data as $keyexam => $valueexam) {
                    if ($keyexam != 'submit' && $keyexam != 'code') {
                        $questionId = explode("-",$keyexam);
                        $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId[1]' ");
                        $read_question = mysqli_fetch_array($result_question);
                        $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                        $allQuestion2++;
                        foreach ($all_value as $key => $value) {
                            $answervalue = $value["answervalue"];
                            if ($answervalue == 1 && $valueexam == $key) {
                                $resultQuestion2++;
                            }
                        }
                        $k++;
                    }
                }
            }  
                                
    $all .= $i.'/[]/';
    $all .= (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '').'/[]/';
    $all .= $read_m['tbl_member_nationalcode'].'/[]/';
    $all .= $read_m['tbl_member_mobile'].'/[]/';
    $all .= $resultQuestion.'/[]/';
    $all .= $resultQuestion.'/[]/';
   $all .='/++/';
$i++;
}
}
echocsv(array("$lang[60]","$lang[92]","$lang[385]","$lang[378]","$lang[2048]","$lang[2049]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);

}
}



if($do == 'report4') {

    $result = Query("SELECT 
    p.tbl_projectmember_mentor,
    p.tbl_projectmember_contract,
    p.tbl_projectmember_id,
    p.tbl_projectmember_user,
    p.tbl_projectmember_user AS tbl_projectmember, 
    m.tbl_member_lastcontract, 
    m.tbl_member_id,
    m.tbl_member_delete AS tbl_member
FROM
    tbl_member m
INNER JOIN tbl_projectmember p 
ON p.tbl_projectmember_contract = '$getcontract' 
and p.tbl_projectmember_user= m.tbl_member_id 
and m.tbl_member_lastcontract like CONCAT ('%', p.tbl_projectmember_contract ,'%') 
and m.tbl_member_delete='0' ORDER BY p.tbl_projectmember_id DESC ");

    $i = 1;
while ($read = mysqli_fetch_array($result))
{
   
    $mentor = $read['tbl_projectmember_mentor'];
    $resultMentor = Query("select * from tbl_user where tbl_user_usercode='$mentor'");
    $readMentor= mysqli_fetch_array($resultMentor);
    $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
    $read_m = mysqli_fetch_array($result_m);
    if(mysqli_num_rows($result_m) > 0 ){
        $maharat = '';
        $countPlan = 0;
        $countPlanConfirm = 0;
        $resultMemberskill = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
        $readMemberskill = mysqli_fetch_array($resultMemberskill);
            if(!empty($readMemberskill['tbl_memberskill_data'])){
            $data = explode(",",$readMemberskill['tbl_memberskill_data']);
            foreach ($data as $key => $value) {
                if(!empty($value))
                {
                    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default'");
                    $readSkill = mysqli_fetch_array($resultSkill);

                    $resultCer = Query("SELECT tbl_number_id From tbl_number Where tbl_number_user ='$read_m[tbl_member_id]' and tbl_number_contract='$getcontract' and  tbl_number_skill='$value' ");

                    $maharat .= $readSkill['tbl_categories_title'].'-';

                    $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                    $countPlan +=mysqli_num_rows($result_for_count);
                    $result_for_confirm = Query("SELECT * From tbl_plan where tbl_plan_status='1' and tbl_plan_skill='$value' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                    $countPlanConfirm +=mysqli_num_rows($result_for_confirm);


                }
            }
        }
        
        $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
        $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
        $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
        $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
    
        if (mysqli_num_rows($resultPlan) == '0') {
            $pishraft= $lang[1595];
        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
            $pishraft= $lang[1596];
        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
            $pishraft= $lang[1597];
        } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
            $pishraft= $lang[1598];
        }
        else if (mysqli_num_rows($resultCheckFinal) > '0') {
            $pishraft= $lang[1599];
        }

                                
    $all .= $i.'/[]/';
    $all .= (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '').'/[]/';
    $all .= $read_m['tbl_member_nationalcode'].'/[]/';
    $all .= $read_m['tbl_member_mobile'].'/[]/';
    $all .= $maharat.'/[]/';
    $all .= $readMentor['tbl_user_name'].'/[]/';
    $all .= $pishraft.'/[]/';
    $all .= (mysqli_num_rows($resultCer) > 0 ? $lang[2045] : $lang[2046]).'/[]/';
    $all .= ($countPlanConfirm == 0 ? $countPlanConfirm : round(abs(((($countPlan - $countPlanConfirm) / $countPlan) * 100)-100))).'%/[]/';
   $all .='/++/';
$i++;
}
}
echocsv(array("$lang[60]","$lang[92]","$lang[385]","$lang[378]","$lang[1337]","$lang[1447]","$lang[1340]","$lang[2048]","$lang[1955]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);

}
}



if($do == 'report1') {

    $select = '';
    if(!empty($_GET['project']))
    {
        $select .= " tbl_contract_code like '%$_GET[project]%' and ";
    }
    if(!empty($_GET['status']))
    {
        if(!empty($_GET['status']))
        {
            if($_GET['status'] == 1)
            {
                $select .= " tbl_contract_reportdate = '' and ";
            }
            if($_GET['status'] == 2)
            {
                $select .= " tbl_contract_reportdate != '' and ";
            }
        }
        
    }

    if(!empty($_GET['startdate']))
    {
        $select .= " tbl_contract_startdate = '$_GET[startdate]' and ";
    }

    if(!empty($_GET['enddate']))
    {
        $select .= " tbl_contract_enddate = '$_GET[enddate]' and ";
    }

    if(!empty($_GET['karfarma']))
    {
        $select .= " tbl_contract_customer like '%$_GET[karfarma]%' and ";
    }
    
    if(!empty($_GET['maharat']))
    {
        $select .= " tbl_contract_namesection like '%$_GET[maharat]%' and ";
    }

    if(!empty($_GET['admin']))
    {
        $select .= " tbl_contract_project = '$_GET[admin]' and ";
    }
    $result = Query("SELECT * From tbl_contract where $select tbl_contract_id !='' ORDER BY tbl_contract_id DESC  ");
    $i = 1;
while ($read = mysqli_fetch_array($result))
{
   
    $admin = $read['tbl_contract_project'];
    $resultAdmin = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$admin'  ");
    $readAdmin = mysqli_fetch_array($resultAdmin);

   
    $contractId = $read['tbl_contract_id'];
    $contractIdStr = '['.$contractId.'],';
    $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
    $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId'  ");
    $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId'  ");
    $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
    $resultPlanCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$contractId' ");
    $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");
    
    $allFaragir = mysqli_num_rows($result_member);
    $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
    $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
    $noPlan = mysqli_num_rows($resultPlan);
    $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
    $endPlan =  mysqli_num_rows($resultCheckEndPlan);
    $thisCustomer = $read['tbl_contract_customer'];


    $resultChecklist = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                $resultChecklistConfirm = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_confirm='1' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                $count= mysqli_num_rows($resultChecklist);
                $confirm= mysqli_num_rows($resultChecklistConfirm);
                $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100)-100)));

    $all .= $i.'/[]/';
    $all .= $read['tbl_contract_code'].'/[]/';
    $all .= $read['tbl_contract_customer'].'/[]/';
    $all .= $read['tbl_contract_startdate'].'/[]/';
    $all .= $read['tbl_contract_enddate'].'/[]/';
    $all .= @$readAdmin['tbl_user_name'].'/[]/';
    $all .= @$read['tbl_contract_namesection'].'/[]/';
    $all .= (!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]).'/[]/';
    $all .= abs($allFaragir-$allFaragirHaveQuestion).'/[]/';
    $all .= ($allFaragir-$allFaragirHaveQuestionCheck).'/[]/';
    $all .= $eblaghNashode.'/[]/';
    $all .= abs($allFaragir-$endPlan).'/[]/';
    $all .= toPersianNum($persent).'%/[]/';
    $all .= (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0').'/[]/';
   $all .='/++/';
$i++;
}
echocsv(array("$lang[60]","$lang[1980]","$lang[1981]","$lang[1982]","$lang[1983]","$lang[1984]","$lang[1985]","$lang[1986]","$lang[2014]","$lang[2015]","$lang[2016]","$lang[2017]","$lang[2018]","$lang[1993]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}


if($do == 'report2') {

    $select = '';
    if(!empty($_GET['project']))
    {
        $select .= " tbl_contract_code like '%$_GET[project]%' and ";
    }
    if(!empty($_GET['status']))
    {
        if(!empty($_GET['status']))
        {
            if($_GET['status'] == 1)
            {
                $select .= " tbl_contract_reportdate = '' and ";
            }
            if($_GET['status'] == 2)
            {
                $select .= " tbl_contract_reportdate != '' and ";
            }
        }
        
    }

    if(!empty($_GET['startdate']))
    {
        $select .= " tbl_contract_startdate = '$_GET[startdate]' and ";
    }

    if(!empty($_GET['enddate']))
    {
        $select .= " tbl_contract_enddate = '$_GET[enddate]' and ";
    }

    if(!empty($_GET['karfarma']))
    {
        $select .= " tbl_contract_customer like '%$_GET[karfarma]%' and ";
    }
    
    if(!empty($_GET['maharat']))
    {
        $select .= " tbl_contract_namesection like '%$_GET[maharat]%' and ";
    }

    if(!empty($_GET['admin']))
    {
        $select .= " tbl_contract_mentor like '%$_GET[admin]%' and ";
    }

    $result = Query("SELECT * From tbl_contract where $select tbl_contract_id !='' ORDER BY tbl_contract_id DESC ");
    $i = 1;
while ($read = mysqli_fetch_array($result))
{
   
   
    $mentor = explode(",",$read['tbl_contract_mentor']);
                             $mentor = explode(",",$read['tbl_contract_mentor']);
                            foreach ($mentor as $key => $value) {
                                if(!empty($value))
                                {

                            $admin = $read['tbl_contract_project'];
                            $resultAdmin = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$admin'  ");
                            $readAdmin = mysqli_fetch_array($resultAdmin);

                            $resultMentor = Query("select tbl_user_name,tbl_user_id from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$value'  ");
                            $readMentor = mysqli_fetch_array($resultMentor);

                           
                            $contractId = $read['tbl_contract_id'];
                            $contractIdStr = '['.$contractId.'],';
                            $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
                            $result_member_question = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$readMentor[tbl_user_id]' ");
                            $result_member_question_check = Query("select tbl_memberquestion_id from tbl_memberquestion where tbl_memberquestion_admincheck='1' and tbl_memberquestion_contract='$contractId' and tbl_memberquestion_mentor='$readMentor[tbl_user_id]' ");
                            $resultPlan = Query("SELECT DISTINCT  tbl_plan_menti From tbl_plan where tbl_plan_lng='$language_default' and tbl_plan_contract='$contractId' and tbl_plan_delete='0'");
                            $resultPlanCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_contract='$contractId' ");
                            $resultCheckEndPlan = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_contract='$contractId' ");
                            
                            $allFaragir = mysqli_num_rows($result_member);
                            $allFaragirHaveQuestion = mysqli_num_rows($result_member_question);
                            $allFaragirHaveQuestionCheck = mysqli_num_rows($result_member_question_check);
                            $noPlan = mysqli_num_rows($resultPlan);
                            $eblaghNashode = $allFaragir - mysqli_num_rows($resultPlanCheck);
                            $endPlan =  mysqli_num_rows($resultCheckEndPlan);
                            $thisCustomer = $read['tbl_contract_customer'];


                            $resultChecklist = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                            $resultChecklistConfirm = Query("SELECT tbl_checklist_confirm FROM tbl_checklist where tbl_checklist_confirm='1' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  and tbl_checklist_contract ='$contractId' ");
                            $count= mysqli_num_rows($resultChecklist);
                            $confirm= mysqli_num_rows($resultChecklistConfirm);
                            $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100)-100)));

                            $resultCer = Query("SELECT *  From tbl_number where tbl_number_contract='$read[tbl_contract_id]' ");
                            $userCerCount = mysqli_num_rows($resultCer);

                                        $all .= $i.'/[]/';
                                        $all .= $readMentor['tbl_user_name'].'/[]/';
                                        $all .= $read['tbl_contract_code'].'/[]/';
                                        $all .= $readAdmin['tbl_user_name'].'/[]/';
                                        $all .= $read['tbl_contract_customer'].'/[]/';
                                        $all .= $read['tbl_contract_startdate'].'-'.$read['tbl_contract_enddate'].'/[]/';
                                        $all .= $read['tbl_contract_namesection'].'/[]/';
                                        $all .= (!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]).'/[]/';
                                        $all .= abs($allFaragir-$allFaragirHaveQuestion).'/[]/';
                                        $all .= abs($allFaragir-$allFaragirHaveQuestionCheck).'/[]/';
                                        $all .= abs($allFaragir-$userCerCount).'/[]/';
                                        $all .= $eblaghNashode.'/[]/';
                                        $all .= toPersianNum($persent).'%/[]/';
                                        $all .= (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0').'/[]/';
                                       $all .='/++/';
                                    $i++;


                                }
                            }

}




echocsv(array("$lang[60]","$lang[1999]","$lang[1980]","$lang[1984]","$lang[1981]","$lang[2037]","$lang[1985]","$lang[1986]","$lang[2038]","$lang[2039]","$lang[2042]","$lang[2040]","$lang[1991]","$lang[1993]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}







// if($do == 'report3') {

//     $select = '';
//                         if(!empty($_GET['project']))
//                         {
//                             $select .= " tbl_mohtava_codefile like '%$_GET[code]%' and ";
//                         }
//                         if(!empty($_GET['title']))
//                         {
//                             $select .= " tbl_mohtava_title like '%$_GET[title]%' and ";
//                         }

//                         if(!empty($_GET['ghaleb']))
//                         {
//                             $select .= " tbl_mohtava_ghaleb = '$_GET[ghaleb]' and ";
//                         }

//                         if(!empty($_GET['mozo']))
//                         {
//                             $select .= " tbl_mohtava_subtitle1 like '%$_GET[mozo]%' and ";
//                         }
                        
//                         if(!empty($_GET['department']))
//                         {
//                             $select .= " tbl_mohtava_department like '%$_GET[department]%' and ";
//                         }

//                         if(!empty($_GET['creator']))
//                         {
//                             $select .= " tbl_mohtava_creator like '%$_GET[creator]%' and ";
//                         }

//                         if(!empty($_GET['producer']))
//                         {
//                             $select .= " tbl_mohtava_producer like '%$_GET[producer]%' and ";
//                         }
                        

//     $result = Query("SELECT * From tbl_mohtava Where $select tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0'  ORDER BY tbl_mohtava_id DESC  ");
//     $i = 1;
// while ($read = mysqli_fetch_array($result))
// {
   
//     $select = '';
//     if(!empty($_GET['project']))
//     {
//         $select .= " tbl_mohtava_codefile like '%$_GET[code]%' and ";
//     }
//     if(!empty($_GET['title']))
//     {
//         $select .= " tbl_mohtava_title like '%$_GET[title]%' and ";
//     }

//     if(!empty($_GET['ghaleb']))
//     {
//         $select .= " tbl_mohtava_ghaleb = '$_GET[ghaleb]' and ";
//     }

//     if(!empty($_GET['mozo']))
//     {
//         $select .= " tbl_mohtava_subtitle1 like '%$_GET[mozo]%' and ";
//     }
    
//     if(!empty($_GET['department']))
//     {
//         $select .= " tbl_mohtava_department like '%$_GET[department]%' and ";
//     }

//     if(!empty($_GET['creator']))
//     {
//         $select .= " tbl_mohtava_creator like '%$_GET[creator]%' and ";
//     }

//     if(!empty($_GET['producer']))
//     {
//         $select .= " tbl_mohtava_producer like '%$_GET[producer]%' and ";
//     }


//     $department = $read['tbl_mohtava_department'];
//                             $resultKey = Query("SELECT * From tbl_categories Where tbl_categories_catcode = '$department' and tbl_categories_lng='$language_default' ");
//                             $readKey = mysqli_fetch_array($resultKey);
//                             $creator = $read['tbl_mohtava_creator'];
//                             $resultCreator = Query("SELECT * From tbl_user Where tbl_user_id='$creator' and tbl_user_contentcreator='1' and tbl_user_bakhsh='2' ORDER BY tbl_user_id DESC ");
//                             $readCreator = mysqli_fetch_array($resultCreator);
//                             $producer = $read['tbl_mohtava_producer'];
//                             $resultProducer = Query("SELECT * From tbl_user Where tbl_user_id='$producer'  ");
//                             $readProducer = mysqli_fetch_array($resultProducer);

//                             $resultCountRegister = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_code = '$read[tbl_mohtava_code]'");
//                             $resultCourse = Query("Select * from tbl_course where tbl_course_mohtava = '$read[tbl_mohtava_code]' and tbl_course_lng='$language_default'");




//                                         $all .= $i.'/[]/';
//                                         $all .= (!empty($read['tbl_mohtava_codefile']) ? strtoupper($read['tbl_mohtava_codefile']) : '' ).'/[]/';
//                                         $all .= $read['tbl_mohtava_title'].'/[]/';
//                                         $all .= (!empty($read['tbl_mohtava_ghaleb']) ? '<br>'.$lang[1832].' : '.$lang[$read['tbl_mohtava_ghaleb']] : '').'/[]/';
//                                         $all .= $read['tbl_mohtava_subtitle1'].'/[]/';
//                                         $all .= @$readKey['tbl_categories_title'].'/[]/';
//                                         $all .= @$readProducer['tbl_user_name'].'/[]/';
//                                         $all .= @$read['tbl_mohtava_enteshar'].'/[]/';
//                                         $all .= '/[]/';
//                                         $all .= @$read['tbl_mohtava_dateadd'].'/[]/';
//                                         $all .= $read['tbl_mohtava_view'].'/[]/';
//                                         $all .= mysqli_num_rows($resultCountRegister).'/[]/';
//                                        $all .='/++/';
//                                     $i++;


// }

// echocsv(array("$lang[60]","$lang[1831]","$lang[1856]","$lang[1832]","$lang[1838]","$lang[2012]","$lang[2013]","$lang[2004]","$lang[1713]","$lang[2005]","$lang[2006]","$lang[2007]","$lang[2008]","$lang[2009]","$lang[2010]"));
// $allfinal = explode("/++/", $all);
// foreach ($allfinal as $col => $val) {
//     $thisval = explode("/[]/", $val);
//     echocsv($thisval);
// }
// }

if($do == 'report3') {

    $select = '';

    if(!empty($_GET['mohatavafile']))
    {
        $mohatavafile = sqi($_GET['mohatavafile']);
        $select .= " tbl_mohtava_codefile like '%$mohatavafile%' and  ";
    }
    
    if(!empty($_GET['subtitle']))
    {
        $subtitle = sqi($_GET['subtitle']);
        $select .= " tbl_mohtava_subtitle1 like '%$subtitle%' and  ";
    }
    
    
    
    
    if(!empty($_GET['mohatavatitle']) && $_GET['mohatavatitle'] !='0')
    {
        $mohatavatitle = sqi($_GET['mohatavatitle']);
        $select .= " tbl_mohtava_title like '%$mohatavatitle%' and  ";
    }
    
    
    if(!empty($_GET['mohatavacreator']) && $_GET['mohatavacreator'] !='0')
    {
        $mohatavacreator = sqi($_GET['mohatavacreator']);
        $select .= " tbl_mohtava_admin = '$mohatavacreator' and  ";
    }
    
    if(!empty($_GET['malek']) && $_GET['malek'] !='0')
    {
        $mohatavacreator = sqi($_GET['malek']);
        $select .= " tbl_mohtava_producer like '%$mohatavacreator%' and  ";
    }
    
    if(!empty($_GET['department']) && $_GET['department'] !='0')
    {
        $department = sqi($_GET['department']);
        $select .= " tbl_mohtava_department = '$department' and  ";
    }
    
    if(!empty($_GET['producer']) && $_GET['producer'] !='0')
    {
        $producer = sqi($_GET['producer']);
        $select .= " tbl_mohtava_producer = '$producer' and  ";
    }
    
    $selectRegisterMohtava = '';

    $selectPoint = "" ;
    if(!empty($_GET['enddate']))
    {
        $enddate = strip_tags($_GET['enddate']);
        if(empty($_GET['startdate']))
        {
            $jdate = jdate("Y/m/d");
            $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$jdate' and '$enddate' ";
            $selectPoint = " and dateend between '$jdate' and '$enddate' " ;
        }
        else {
            $startdate = strip_tags($_GET['startdate']);
            $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$enddate' ";
            $selectPoint = " and dateend between '$startdate' and '$enddate' " ;
        }
    }
    if(!empty($_GET['startdate']))
    {
        $startdate = strip_tags($_GET['startdate']);
        if(empty($_GET['enddate']))
        {
            $jdate = jdate("Y/m/d");
            $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$jdate' ";
            $selectPoint = " and dateend between '$startdate' and '$jdate' " ;
        }
        else {
            $enddate = strip_tags($_GET['enddate']);
            $selectRegisterMohtava = " and tbl_mohtavaregister_dateen between '$startdate' and '$enddate' ";
            $selectPoint = " and dateend between '$startdate' and '$enddate' " ;
        }
    }
                        
$result = Query("SELECT * From tbl_mohtava Where $select tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0'  ORDER BY tbl_mohtava_id DESC  ");
    $i = 1;
while ($read = mysqli_fetch_array($result))
{
   

$department = $read['tbl_mohtava_department'];
$resultKey = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$department' and tbl_categories_lng='$language_default' ");
$readKey = mysqli_fetch_array($resultKey);
$creator = $read['tbl_mohtava_creator'];
$resultCreator = Query("SELECT tbl_user_name From tbl_user Where tbl_user_id='$creator' and tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ORDER BY tbl_user_id DESC ");
$readCreator = mysqli_fetch_array($resultCreator);
$producer = $read['tbl_mohtava_producer'];
$resultProducer = Query("SELECT tbl_user_name From tbl_user Where tbl_user_id='$producer'  ");
$readProducer = mysqli_fetch_array($resultProducer);

$resultCountRegister = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='0' and tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]' $selectRegisterMohtava ");
$countresultCountRegister = mysqli_num_rows($resultCountRegister);
$resultCountUseInCourse = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_model='1' and  tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]' $selectRegisterMohtava ");
$countresultCountUseInCourse= mysqli_num_rows($resultCountUseInCourse);

$resultRegisterLastDate = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_mohtava = '$read[tbl_mohtava_code]'  order by tbl_mohtavaregister_id DESC limit 0,1");
$readRegisterLastDate = mysqli_fetch_array($resultRegisterLastDate);

$resultCourse = Query("Select * from tbl_course where tbl_course_mohtava = '$read[tbl_mohtava_code]' and tbl_course_lng='$language_default'");

$resultFilePlan = Query("SELECT * From tbl_plan where tbl_plan_delete='0' and tbl_plan_file='$read[tbl_mohtava_code]' and tbl_plan_lng='$language_default'  ");
$countresultFilePlan= mysqli_num_rows($resultFilePlan);
$res_rate = Query("SELECT total_points,rating_number, FORMAT((total_points / rating_number),1) as average_rating FROM tbl_rating WHERE post_id = '$read[tbl_mohtava_id]' $selectPoint ");
$read_rate = mysqli_fetch_array($res_rate);
$status = $read['tbl_mohtava_statustext'];

                $all .= $i.'/[]/';
                $all .= (!empty($read['tbl_mohtava_codefile']) ? strtoupper($read['tbl_mohtava_codefile']) : '' ).'/[]/';
                $all .= $read['tbl_mohtava_title'].'/[]/';
                $all .= $lang[$status+1908].'/[]/';
                $all .= (!empty($read['tbl_mohtava_ghaleb']) ? '-'.$lang[1832].' : '.$lang[$read['tbl_mohtava_ghaleb']] : '').'/[]/';
                $all .= $read['tbl_mohtava_subtitle1'].'/[]/';
                $all .= @$readKey['tbl_categories_title'].'/[]/';
                $all .= @$readCreator['tbl_user_name'].'/[]/';
                $all .= @$readProducer['tbl_user_name'].'/[]/';
                $all .= @$read['tbl_mohtava_enteshar'].'/[]/';
                $all .= (!empty($read_rate['total_points']) ? round($read_rate['total_points'] / $read_rate['rating_number']) : '0').'/[]/';
                $all .= @$readRegisterLastDate['tbl_mohtavaregister_dateregister'].'/[]/';
                $all .= $read['tbl_mohtava_view'].'/[]/';
                $all .= $countresultFilePlan+$countresultCountUseInCourse+$countresultCountRegister.'/[]/';
                $all .= $countresultCountRegister.'/[]/';
                $all .= $countresultFilePlan+$countresultCountUseInCourse.'/[]/';
                $all .='/++/';
            $i++;


}

echocsv(array("$lang[60]","$lang[1831]","$lang[1856]","$lang[1843]","$lang[1832]","$lang[1833]","$lang[1838]","$lang[2013]","$lang[2012]","$lang[2004]","$lang[2005]","$lang[2006]","$lang[2007]","$lang[2008]","$lang[2009]","$lang[2010]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}


if($do == 'mohtava') {

    $select = '';
    
    if (!empty($_SESSION['access']) && @$_SESSION['access'] == 8) {
        $this_user_id = $_SESSION['id_login'];
        $select .= " tbl_mohtava_admin='$this_user_id' and  ";
    } else {
        $select .= '';
    }

    if(!empty($_GET['mohatavafile']))
    {
        $select .= " tbl_mohtava_codefile like '%$_GET[mohatavafile]%' and ";
    }
    if(!empty($_GET['mohatavatitle']))
    {
        $select .= " tbl_mohtava_title like '%$_GET[mohatavatitle]%' and ";
    }
        if(!empty($_GET['subtitle']))
        {
            $subtitle = sqi($_GET['subtitle']);
            $select .= " tbl_mohtava_subtitle1 like '%$subtitle%' and  ";
        }

    if(!empty($_GET['mohatavacreator']))
    {
        $select .= " tbl_mohtava_admin = '$_GET[mohatavacreator]' and ";
    }

    if(!empty($_GET['department']))
    {
        $select .= " tbl_mohtava_department = '$_GET[department]' and ";
    }
    
    if(!empty($_GET['status']))
    {
        $select .= " tbl_mohtava_statustext = '$_GET[status]' and ";
    }

                       

                        $result = Query("SELECT * From tbl_mohtava Where $select tbl_mohtava_lng='$language_default' and tbl_mohtava_delete='0'  ORDER BY tbl_mohtava_id DESC  ");
    $i = 1;
while ($read = mysqli_fetch_array($result))
{

    $admin = $read['tbl_mohtava_admin'];
    $resultAdmin = Query("SELECT * From tbl_user Where tbl_user_id='$admin' ");
    $readAdmin = mysqli_fetch_array($resultAdmin);
                         
    $resultEn = Query("SELECT tbl_mohtava_title From tbl_mohtava Where  tbl_mohtava_lng='en' and tbl_mohtava_code='$read[tbl_mohtava_code]'");
    $readEn = mysqli_fetch_array($resultEn);
    $file = explode(",",$read['tbl_mohtava_file']);

    

    $all .= $i.'/[]/';
    $all .= (!empty($read['tbl_mohtava_codefile']) ? strtoupper($read['tbl_mohtava_codefile']) : 'T-'.$read['tbl_mohtava_code'] ).'/[]/';
    $all .=  $read['tbl_mohtava_title']. '<br>' . @$readEn['tbl_mohtava_title']. '' . (!empty($read['tbl_mohtava_ghaleb']) ? '<br>'.$lang[1832].' : '.$lang[$read['tbl_mohtava_ghaleb']] : '').'/[]/';
    $all .= $read['tbl_mohtava_subtitle1'].'/[]/';
    $all .= $readAdmin['tbl_user_name'].'/[]/';
    $all .= $lang[$read['tbl_mohtava_statustext']+1908].'/[]/';
    $all .= $read['tbl_mohtava_dateadd'].'/[]/';
    $all .= $read['tbl_mohtava_enteshar'].'/[]/';
    $all .='/++/';
    $i++;


}

echocsv(array("$lang[60]","$lang[1831]","$lang[1334]","$lang[854]","$lang[1845]","$lang[1340]","$lang[1844]","$lang[1842]"));
$allfinal = explode("/++/", $all);
foreach ($allfinal as $col => $val) {
    $thisval = explode("/[]/", $val);
    echocsv($thisval);
}
}