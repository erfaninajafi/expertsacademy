<?php
// include
include './includer.php';
$show_per_page = '10';
$Tabpage = '901';
$pagetitle = $lang[2044];
$thisaccess = '1';
include './header.inc.php';
// end include 
if (!empty($_GET['contract'])) {
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$getcontract]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$getcontract' ");
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
            <h5 class="fs-0">'.$lang[1566].'  : ' . @$read_project['tbl_user_name'] . '</h5>
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

                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>

            <div class="card-body p-0 position-relative">
                <?php
                    if (!isset($do)) { 
                        ?>
                <table>
                    <tr>
                        <td class="col-sm-2 p-3">
                            <a href="csv.php?do=report6&contract=<?=$getcontract?>"><button type="submit" name="go" class="btn btn-success"><?=$lang[2019]?></button></a>
                        </td>
                    </tr>
                </table>
                <?php
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

                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }

                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[1337] . '</th>
                                    <th>' . $lang[1447] . '</th>
                                    <th>' . $lang[2054] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
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
                                                echo '<tr>
                                                    <td class="align-middle p-2">' . $i . '</td>
                                                    <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '') . '</td>
                                                    <td class="align-middle p-2">'.$read_m['tbl_member_nationalcode'].'</td>
                                                    <td class="align-middle p-2">'.$read_m['tbl_member_mobile'].'</td>
                                                    <td class="align-middle p-2">'.$readSkill['tbl_categories_title'].'</td>
                                                    <td class="align-middle p-2">'.$readMentor['tbl_user_name'].'</td>
                                                    <td class="align-middle p-2">'.$readPlan['tbl_plan_coach'].'</td>
                                                    </tr>';
                                                    $i++;

                                            }                                       
                                        }
                                        else {
                                            echo '<tr>
                                                    <td class="align-middle p-2">' . $i . '</td>
                                                    <td class="align-middle p-2">' . (!empty($read_m['tbl_member_id']) ? $read_m['tbl_member_fname'] . ' ' . $read_m['tbl_member_lname'] : '') . '</td>
                                                    <td class="align-middle p-2">'.$read_m['tbl_member_nationalcode'].'</td>
                                                    <td class="align-middle p-2">'.$read_m['tbl_member_mobile'].'</td>
                                                    <td class="align-middle p-2">'.$readSkill['tbl_categories_title'].'</td>
                                                    <td class="align-middle p-2">'.$readMentor['tbl_user_name'].'</td>
                                                    <td class="align-middle p-2"></td>
                                                    </tr>';
                                                    $i++;
                                        }

                                        }
                                    }
                                }
                               
                                
                            }
                        }
                        echo '</tbody></table>';
                        // start pagination                
                    }
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: report1.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result            
                    ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>