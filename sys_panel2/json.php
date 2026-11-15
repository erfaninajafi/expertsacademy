<?php

include './includer.php';
include '../include/jdf.php';

if(isset($do) && $do =='project')
{
    if(!empty($_GET['number']))
    {
        $projectCode = sqi($_GET['number']);
        $result = Query("SELECT * From tbl_contract where tbl_contract_code ='$projectCode' ");
        if(mysqli_num_rows($result)> 0 ) {
            header("Content-Type: application/json");
            $read = mysqli_fetch_array($result);

            $admin = $read['tbl_contract_project'];
            $resultAdmin = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$admin'  ");
            $readAdmin = mysqli_fetch_array($resultAdmin);

            $contractId = $read['tbl_contract_id'];
            $contractIdStr = '['.$contractId.'],';
            $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' and tbl_member_delete='0' ");
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
            $date = jdate("Y/m/d : a:h:s");
            $myObj = array();
            $response = array();

            $Projectmanager=(mysqli_num_rows($resultAdmin) > 0 ? $readAdmin['tbl_user_name'] : '');
            $Status = (!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]);
            $BedonSoal = abs($allFaragir-$allFaragirHaveQuestion);
            $ArzyabiTaeednashode= abs($allFaragir-$allFaragirHaveQuestionCheck);
            $Plannotcomplate = abs($allFaragir-$endPlan);
            $Progress = $persent.'%';
            $Allfaragir = (mysqli_num_rows($result_member) > '0' ? mysqli_num_rows($result_member) : '0');
            
            $response = array(
                'Code' => "$read[tbl_contract_code]",
                'Karfarma' => "$read[tbl_contract_customer]",
                'Startdate' => "$read[tbl_contract_startdate]",
                'Enddate' => "$read[tbl_contract_enddate]",
                'Projectmanager' => (mysqli_num_rows($resultAdmin) > 0 ? $readAdmin['tbl_user_name'] : ''),
                'Skill' => "$read[tbl_contract_namesection]",
                'Status' => (!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]),
                'BedonSoal' => abs($allFaragir-$allFaragirHaveQuestion),
                'ArzyabiTaeednashode'=> abs($allFaragir-$allFaragirHaveQuestionCheck),
                'Withoutplan' => $eblaghNashode,
                'Plannotcomplate' => abs($allFaragir-$endPlan),
                'Progress' => $persent.'%',
                'Allfaragir' => (mysqli_num_rows($result_member) > '0' ? mysqli_num_rows($result_member) : '0')
            );

            $resultCheck = Query("select * from tbl_json where tbl_json_Code='$projectCode' and tbl_json_model='project' ");
            if(mysqli_num_rows($resultCheck) > 0)
            {
                Query("update tbl_json set 
                tbl_json_Karfarma='$read[tbl_contract_customer]',
                tbl_json_Startdate='$read[tbl_contract_startdate]',
                tbl_json_Enddate='$read[tbl_contract_enddate]',
                tbl_json_Projectmanager='$Projectmanager',
                tbl_json_Skill='$read[tbl_contract_namesection]',
                tbl_json_Status='$Status',
                tbl_json_BedonSoal='$BedonSoal',
                tbl_json_ArzyabiTaeednashode='$ArzyabiTaeednashode',
                tbl_json_Withoutplan='$eblaghNashode',
                tbl_json_Plannotcomplate='$Plannotcomplate',
                tbl_json_Progress='$Progress',
                tbl_json_Allfaragir='$Allfaragir',
                tbl_json_dateupdate='$date'
                where tbl_json_Code='$projectCode' and tbl_json_model='project' ");
            }
            else {
                Query("insert into tbl_json 
                (tbl_json_Code,
                 tbl_json_Karfarma,
                tbl_json_Startdate,
                tbl_json_Enddate,
                tbl_json_Projectmanager,
                tbl_json_Skill,
                tbl_json_Status,
                tbl_json_BedonSoal,
                tbl_json_ArzyabiTaeednashode,
                tbl_json_Withoutplan,
                tbl_json_Plannotcomplate,
                tbl_json_Progress,
                tbl_json_Allfaragir,
                tbl_json_dateupdate,
                tbl_json_model)
                values('$projectCode',
                '$read[tbl_contract_customer]',
                '$read[tbl_contract_startdate]',
                '$read[tbl_contract_enddate]',
                '$Projectmanager',
                '$read[tbl_contract_namesection]',
                '$Status',
                '$BedonSoal',
                '$ArzyabiTaeednashode',
                '$eblaghNashode',
                '$Plannotcomplate',
                '$Progress',
                '$Allfaragir',
                '$date',
                'project') 
                ");
            }

            echo json_encode($response);
            // $myObj['Code'] = "$read[tbl_contract_code]";
            // $myObj['Karfarma'] = "$read[tbl_contract_customer]";
            // $myObj['Startdate'] = "$read[tbl_contract_startdate]";
            // $myObj['Enddate'] = "$read[tbl_contract_enddate]";
            // $myObj['Projectmanager'] = "$readAdmin[tbl_user_name]";
            // $myObj['Skill'] = "$read[tbl_contract_namesection]";
            // $myObj['Status'] = (!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]);
            // $myObj['BedonSoal'] = abs($allFaragir-$allFaragirHaveQuestion);
            // $myObj['ArzyabiTaeednashode'] = abs($allFaragir-$allFaragirHaveQuestionCheck);
            // $myObj['Withoutplan'] = $eblaghNashode;
            // $myObj['Plannotcomplate'] = abs($allFaragir-$endPlan);
            // $myObj['Progress'] = toPersianNum($persent).'%';
            // $myObj['Allfaragir'] = (mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0');
            // //var_dump($myObj);
            // $myJSON = json_encode($myObj,JSON_UNESCAPED_UNICODE );
             //var_dump($response);
            //var_dump($myJSON);
            //file_put_contents('project.json', $myJSON);
        }
    }
}

if(isset($do) && $do =='listfaragir')
{
    if(!empty($_GET['number']))
    {
        $projectCode = sqi($_GET['number']);
        $resultContract = Query("SELECT * From tbl_contract where tbl_contract_code !='$projectCode' ");
        if(mysqli_num_rows($resultContract)> 0 ) {
            $readContract = mysqli_fetch_array($resultContract);
            $getcontract =$readContract['tbl_contract_id'];

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
            $response = array();
            $i=0;
            if(mysqli_num_rows($result) > 0 ){
                while ($read = mysqli_fetch_array($result)) {
                   
                
                

                $mentor = $read['tbl_projectmember_mentor'];
                $resultMentor = Query("select tbl_user_name from tbl_user where tbl_user_usercode='$mentor'");
                $readMentor= mysqli_fetch_array($resultMentor);
                $result_m = Query("SELECT * From tbl_member  where tbl_member_delete='0' and tbl_member_id='$read[tbl_projectmember_user]' ");
                $read_m = mysqli_fetch_array($result_m);
                header("Content-Type: application/json");
                
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
                                        $maharat .= $readSkill['tbl_categories_title'].',';
                                        $result_for_count = Query("SELECT * From tbl_plan where tbl_plan_skill='$value' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                                        $countPlan +=mysqli_num_rows($result_for_count);
                                        $result_for_confirm = Query("SELECT * From tbl_plan where tbl_plan_status='1' and tbl_plan_skill='$value' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_lng='$language_default' and tbl_plan_id !='' and tbl_plan_delete='0'  ");
                                        $countPlanConfirm +=mysqli_num_rows($result_for_confirm);


                                    }
                                }
                            }
                            $resultCer = Query("SELECT tbl_number_id From tbl_number Where tbl_number_user ='$read_m[tbl_member_id]' and tbl_number_contract='$getcontract' and  tbl_number_skill='$value' ");
                            $resultCheck = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_startdate !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                            $resultPlan = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
                            $resultPlanStatus = Query("SELECT tbl_plan_id From tbl_plan where tbl_plan_status ='1' and tbl_plan_contract='$getcontract' and tbl_plan_menti='$read_m[tbl_member_id]' and tbl_plan_delete='0'");
                            $resultCheckFinal = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_datereport !='' and tbl_memberskill_user='$read_m[tbl_member_id]' and tbl_memberskill_contract='$getcontract' ");
                                    
                            $status = '';
                            if (mysqli_num_rows($resultPlan) == '0') {
                                $status= $lang[1595];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                $status= $lang[1596];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) == '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                $status= $lang[1597];
                            } else if (mysqli_num_rows($resultPlan) != '0' && mysqli_num_rows($resultCheck) != '0' && mysqli_num_rows($resultPlanStatus) != '0' && mysqli_num_rows($resultCheckFinal) <= 0) {
                                $status= $lang[1598];
                            }
                            else if (mysqli_num_rows($resultCheckFinal) > '0') {
                                $status= $lang[1599];
                            }
                        $date = jdate("Y/m/d : a:h:s");
                        $Certificate = (mysqli_num_rows($resultCer) > 0 ? $lang[2045] : $lang[2046]);
                        $Progress = ($countPlanConfirm == 0 ? $countPlanConfirm : round(abs(((($countPlan - $countPlanConfirm) / $countPlan) * 100)-100))).'%';
                        $response[$i] = array(
                            'Firstname' => "$read_m[tbl_member_fname]",
                            'Lastname' => "$read_m[tbl_member_lname]",
                            'Nationalcode' => "$read_m[tbl_member_nationalcode]",
                            'Mobile' => "$read_m[tbl_member_mobile]",
                            'Skill' => "$maharat",
                            'Mentor' => "$readMentor[tbl_user_name]",
                            'Status' => "$status",
                            'Certificate' => (mysqli_num_rows($resultCer) > 0 ? $lang[2045] : $lang[2046]),
                            'Progress' => ($countPlanConfirm == 0 ? $countPlanConfirm : round(abs(((($countPlan - $countPlanConfirm) / $countPlan) * 100)-100))).'%'
                        );
                        
                        $resultCheck = Query("select * from tbl_json where tbl_json_Nationalcode='$read_m[tbl_member_nationalcode]' and tbl_json_Code='$projectCode' and tbl_json_model='listfaragir' ");
                        if(mysqli_num_rows($resultCheck) > 0)
                        {
                            Query("update tbl_json set 
                            tbl_json_Firstname='$read_m[tbl_member_fname]',
                            tbl_json_Lastname='$read_m[tbl_member_lname]',
                            tbl_json_Nationalcode='$read_m[tbl_member_nationalcode]',
                            tbl_json_Mobile='$read_m[tbl_member_mobile]',
                            tbl_json_Skill='$maharat',
                            tbl_json_Status='$status',
                            tbl_json_Mentor='$readMentor[tbl_user_name]',
                            tbl_json_Certificate='$Certificate',
                            tbl_json_Progress='$Progress',
                            tbl_json_dateupdate='$date'
                            where tbl_json_Code='$projectCode' and tbl_json_model='listfaragir' ");
                        }
                        else {
                            Query("insert into tbl_json 
                            (tbl_json_Code,
                             tbl_json_Firstname,
                            tbl_json_Lastname,
                            tbl_json_Nationalcode,
                            tbl_json_Mobile,
                            tbl_json_Skill,
                            tbl_json_Status,
                            tbl_json_Mentor,
                            tbl_json_Certificate,
                            tbl_json_Progress,
                            tbl_json_dateupdate,
                            tbl_json_model)
                            values('$projectCode',
                            '$read_m[tbl_member_fname]',
                            '$read_m[tbl_member_lname]',
                            '$read_m[tbl_member_nationalcode]',
                            '$read_m[tbl_member_mobile]',
                            '$maharat',
                            '$status',
                            '$readMentor[tbl_user_name]',
                            '$Certificate',
                            '$Progress',
                            '$date',
                            'listfaragir') 
                            ");
                        }
                        
                       
                        // $myObj = new stdClass();
                        // $myObj->Firstname = "$read_m[tbl_member_fname]";
                        // $myObj->Lastname = "$read_m[tbl_member_lname]";
                        // $myObj->Nationalcode = "$read_m[tbl_member_nationalcode]";
                        // $myObj->Mobile = "$read_m[tbl_member_mobile]";
                        // $myObj->Skill = "$maharat";
                        // $myObj->Mentor = "$readMentor[tbl_user_name]";
                        // $myObj->Status = $status;
                        // $myObj->Certificate = (mysqli_num_rows($resultCer) > 0 ? $lang[2045] : $lang[2046]);
                        // $myObj->Progress = ($countPlanConfirm == 0 ? $countPlanConfirm : round(abs(((($countPlan - $countPlanConfirm) / $countPlan) * 100)-100))).'%';
                        // $myJSON = json_encode($myObj,JSON_UNESCAPED_UNICODE );
                        //file_put_contents('project.json', $myJSON);
                        $i++;
                }
            }
        }
        echo json_encode($response);
        }
    }
}