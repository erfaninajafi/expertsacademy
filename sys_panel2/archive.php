<?php
// include
include './includer.php';
$show_per_page = '100';
$pagetitle = $lang[1804];
$Tabpage = '20';
$thisaccess = '1,5,6';
include './header.inc.php';
$status = '';

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
            <div class="card-body p-0 position-relative">
                <?php
                    if (!isset($do)) { ?>
                <table>
                    </tr>
                    <td class="col-sm-6 p-3">

                    </td>
                    </tr>
                </table>
                <?php

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_memberskill Where tbl_memberskill_user ='$_GET[user]' ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_memberskill Where tbl_memberskill_user ='$_GET[user]' ORDER BY tbl_memberskill_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1659] . '</th>
                                    <th>' . $lang[1662] . '</th>
                                    <th>' . $lang[1666] . '</th>
                                    <th>'.$lang[1447].'</th>
                                    <th>'.$lang[1304].'</th>
                                    <th>'.$lang[1438].'</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mentor = $read['tbl_memberskill_mentor'];
                            $contract = $read['tbl_memberskill_contract'];
                            $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$contract' ");
                            $read_contract = mysqli_fetch_array($result_contract);
                            $resultMentor = Query("SELECT tbl_user_name From tbl_user Where tbl_user_id = '$mentor' ");
                            $readMentor = mysqli_fetch_array($resultMentor);

                            $data = explode(",",$read['tbl_memberskill_data']);
                            $skill = '';
                            $k=1;
                            foreach ($data as $key => $value) {
                                if(!empty($value)){
                                    $resultSkill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default' ");
                                    $readSkill = mysqli_fetch_array($resultSkill);
                                    $skill .=$k.' - '.$readSkill['tbl_categories_title'].'<br>';
                                    $k++;
                                }
                            }
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">' . $read['tbl_memberskill_planname'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_memberskill_startdate'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_memberskill_datereport'] . '</td>
                                <td class="align-middle p-2" >'.$readMentor['tbl_user_name'].'</td>
                                <td class="align-middle p-2" >'.$skill.'</td>
                                <td class="align-middle p-2" >'.$read_contract['tbl_contract_customer'].'</td>
                                <td class="align-middle p-2">
                                <a href="plandetail.php?user='.$_GET['user'].'&contract='.$read_contract['tbl_contract_id'].'" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1658] . '" ><span class="bi-eye-fill"></span></a>
                                 </td>
                                </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
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