<?php
// include
include './includer.php';
$show_per_page = '10';
$Tabpage = '901';
$pagetitle = $lang[1979];
$thisaccess = '1';
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

                    <form action="?do=search" method="GET" class="search-form">
                        <td class="col-sm-1 p-3">
                            <input type="text" class="form-control box-shadow-none" value="<?= (!empty($_GET['project']) ? $_GET['project'] : '') ?>" name="project" placeholder="<?=$lang[1980]?>">
                        </td>
                        <td class="col-sm-1 p-3 custom-select2">
                            <select class="select2" placeholder="<?=$lang[1980]?>" name="status">
                                <option value="0"><?=$lang[1997]?></option>
                                <option value="1" <?=(!empty($_GET['status']) && $_GET['status']=='1' ? 'selected' : '')?>><?=$lang[1995]?></option>
                                <option value="2" <?=(!empty($_GET['status']) && $_GET['status']=='2' ? 'selected' : '')?>><?=$lang[1996]?></option>
                            </select>
                        </td>
                        <td class="col-sm-2 p-3">
                            <input type="text" class="form-control box-shadow-none" value="<?= (!empty($_GET['karfarma']) ? $_GET['karfarma'] : '') ?>" name="karfarma" placeholder="<?=$lang[1981]?>">
                        </td>
                        <td class="col-sm-2 p-3">
                            <input type="text" class="form-control box-shadow-none datepicker" value="<?= (!empty($_GET['startdate']) ? $_GET['startdate'] : '') ?>" name="startdate" placeholder="<?=$lang[1982]?>">
                        </td>
                        <td class="col-sm-2 p-3">
                            <input type="text" class="form-control box-shadow-none datepicker" value="<?= (!empty($_GET['enddate']) ? $_GET['enddate'] : '') ?>" name="enddate" placeholder="<?=$lang[1983]?>">
                        </td>
                        <td class="col-sm-2 p-3">
                            <input type="text" class="form-control box-shadow-none" value="<?= (!empty($_GET['maharat']) ? $_GET['maharat'] : '') ?>" name="maharat" placeholder="<?=$lang[1985]?>">
                        </td>
                        <td class="col-sm-2 p-3 custom-select2">
                            <select class="select2" placeholder="<?=$lang[1984]?>" name="admin">
                                <option value="0"><?=$lang[1997]?></option>
                                <?php
                                    $resultAdmin = Query("select tbl_user_usercode,tbl_user_name from tbl_user where tbl_user_access ='9'  order by tbl_user_id DESC ");
                                    while ($readAdmin = mysqli_fetch_array($resultAdmin)) {
                                        echo '<option value="'.$readAdmin['tbl_user_usercode'].'" '.(!empty($_GET['admin']) && $_GET['admin']==$readAdmin['tbl_user_usercode'] ? 'selected' : '').'>'.$readAdmin['tbl_user_name'].'</option>';
                                    }
                                ?>
                            </select>
                        </td>
                        <td class="col-sm-2 p-3">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </td>
                    </form>

                    </tr>
                    <tr>
                        <td class="col-sm-2 p-3">
                            <a href="csv.php?do=report1<?=(!empty($_GET['project']) ? '&project='.$_GET['project'] : '')?><?=(!empty($_GET['status']) ? '&status='.$_GET['status'] : '')?><?=(!empty($_GET['startdate']) ? '&startdate='.$_GET['startdate'] : '')?><?=(!empty($_GET['enddate']) ? '&enddate='.$_GET['enddate'] : '')?><?=(!empty($_GET['karfarma']) ? '&karfarma='.$_GET['karfarma'] : '')?><?=(!empty($_GET['maharat']) ? '&maharat='.$_GET['maharat'] : '')?><?=(!empty($_GET['admin']) ? '&admin='.$_GET['admin'] : '')?>"><button type="submit" name="go" class="btn btn-success"><?=$lang[2019]?></button></a>
                        </td>
                    </tr>
                </table>
                <?php
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
                        
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_contract where $select tbl_contract_id !=''   ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_contract where $select tbl_contract_id !='' ORDER BY tbl_contract_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1980] . '</th>
                                    <th>' . $lang[1981] . '</th>
                                    <th>' . $lang[1982] . '</th>
                                    <th>' . $lang[1983] . '</th>
                                    <th>' . $lang[1984] . '</th>
                                    <th>' . $lang[1985] . '</th>
                                    <th>' . $lang[1986] . '</th>
                                    <th>' . $lang[2014] . '</th>
                                    <th>' . $lang[2015] . '</th>
                                    <th>' . $lang[2016] . '</th>
                                    <th>' . $lang[2017] . '</th>
                                    <th>' . $lang[2018] . '</th>
                                    <th>' . $lang[1993] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
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

                            //if($allFaragir > 0) {

                            echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2">'.$read['tbl_contract_code'].'</td>
                            <td class="align-middle p-2">'.$read['tbl_contract_customer'].'</td>
                            <td class="align-middle p-2">'.$read['tbl_contract_startdate'].'</td>
                            <td class="align-middle p-2">'.$read['tbl_contract_enddate'].'</td>
                            <td class="align-middle p-2">'.@$readAdmin['tbl_user_name'].'</td>
                            <td class="align-middle p-2">'.$read['tbl_contract_namesection'].'</td>
                            <td class="align-middle p-2">'.(!empty($read['tbl_contract_reportdate']) ? $lang[1996] : $lang[1995]).'</td>
                            <td class="align-middle p-2">'.abs($allFaragir-$allFaragirHaveQuestion).'</td>
                            <td class="align-middle p-2">'.abs($allFaragir-$allFaragirHaveQuestionCheck).'</td>
                            <td class="align-middle p-2">'.$eblaghNashode.'</td>
                            <td class="align-middle p-2">'.abs($allFaragir-$endPlan).'</td>
                            <td>'.toPersianNum($persent).'%</td>
                            <td>'.(mysqli_num_rows($result_member) > '0' ? toPersianNum(mysqli_num_rows($result_member)) : '0').'</td>
                            <td>
                            <a href="report4.php?contract='.$read['tbl_contract_id'].'" target="_blank" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2044] . '"><span class="fas fa-id-badge"></span></a>
                            <a href="report5.php?contract='.$read['tbl_contract_id'].'" target="_blank" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2047] . '"><span class="fas fa-receipt"></span></a>
                            <a href="report6.php?contract='.$read['tbl_contract_id'].'" target="_blank" class="btn me-1 mb-1 btn-sm btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2050] . '"><span class="fas fa-receipt"></span></a>
                            <a href="json.php?do=project&number='.$read['tbl_contract_code'].'" target="_blank" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="JSON Project"><span class="fas fa-receipt"></span></a>
                            <a href="json.php?do=listfaragir&number='.$read['tbl_contract_code'].'" target="_blank" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="JSON List User"><span class="fas fa-receipt"></span></a>
                                </td>
                            </tr>';
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
                                <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="?do=gotopage" class="search-form" method="POST">
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