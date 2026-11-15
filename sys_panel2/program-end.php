<?php

// include
include './includer.php';
$show_per_page = '100';
$Tabpage = '90';
$pagetitle = $lang[1783];

$thisaccess = '1,6';
include './header.inc.php';
echo '<div class="modal fade" id="result" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">'.$lang[1760].'</button>
        </div>
      </div>
    </div>
  </div>';
    ?>

<div class="row gx-3 mb-2">
    <div class="col-xxl-12 col-xl-12 p-0">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $lang[1708] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?=$site_url?>assets/img/icons/spot-illustrations/corner-4.png);">
            </div>
            <div class="card-body p-0 position-relative">
                <div class="table-responsive scrollbar">
                    <?php
                    ?>
                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                        <thead class="text-800 bg-light">
                            <tr>
                            <th><?=$lang[53]?></th>
                                <th><?= $lang[92] ?></th>
                                <th><?= $lang[385] ?></th>
                                <th><?= $lang[1770] ?></th>
                                <th><?= $lang[1774] ?></th>
                                <th><?= $lang[1775] ?></th>
                                <th><?=$lang[1447]?></th>
                                <th><?=$lang[1772]?></th>
                                <th><?= $lang[1709] ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                        <?php
                            
                            $result = Query("SELECT * From tbl_plan where tbl_plan_coach='$this_user_id' and tbl_plan_lng='$language_default' and tbl_plan_delete='0' and tbl_plan_status='1' ORDER BY tbl_plan_order DESC , tbl_plan_id DESC ");
                            if(mysqli_num_rows($result) > 0 ) {
                           while ($read =  mysqli_fetch_array($result)) {
                            $contractId = $read['tbl_plan_contract'];
                            $faragir = $read['tbl_plan_menti'];
                            $skill = $read['tbl_plan_skill'];
                            $planCode = $read['tbl_plan_code'];
                            $mentor = $read['tbl_plan_mentor'];

                            $resultSkill= Query("select * from tbl_categories where tbl_categories_code = '$skill'  ");
                            $readSkill =  mysqli_fetch_array($resultSkill);

                            $resultContract= Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                            $readContract =  mysqli_fetch_array($resultContract);

                            $resultMember = Query("select * from tbl_member where tbl_member_id ='$faragir' ");
                            $readMember =  mysqli_fetch_array($resultMember);

                            $resultUser = Query("select tbl_user_name from tbl_user where tbl_user_id ='$mentor' ");
                            $readUser =  mysqli_fetch_array($resultUser);

                            $resultMessage = Query("SELECT * From tbl_coachmessage Where tbl_coachmessage_reply='0' and tbl_coachmessage_plan='$planCode' and tbl_coachmessage_contract='$contractId' and tbl_coachmessage_skill='$skill' and tbl_coachmessage_user='$faragir' and tbl_coachmessage_delete ='0'");

                                        ?>
                            <tr>
                                <td class="align-middle pe-3 py-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="<?= (!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg') ?>" alt="">
                                </div>
                                </div>
                            </td>
                                <td class="align-middle pe-3 py-2"><?=$readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] ?></td>
                                <td class="align-middle pe-3 py-2"><?=toPersianNum($readMember['tbl_member_nationalcode'])?></td>
                                <td class="align-middle pe-3 py-2 "><?= $readSkill['tbl_categories_title']?></td>
                                <td class="align-middle subject py-2 pe-2"><?= strtoupper($readSkill['tbl_categories_catcode']) ?></td>
                                <td class="align-middle status py-2 pe-2"><?= $readContract['tbl_contract_customer'] ?></td>
                                <td class="align-middle status py-2 pe-2"><?= $readUser['tbl_user_name'] ?></td>
                                <?php
                                                    $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$faragir' and tbl_plan_contract ='$contractId' and tbl_plan_skill='$skill'");
                                                    $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$faragir' and tbl_plan_contract ='$contractId' and tbl_plan_skill='$skill'");
                                                    $count= mysqli_num_rows($resultPlan);
                                                    $confirm= mysqli_num_rows($resultPlanConfirm);
                                                    $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100)-100)));
                                                echo '<td class="align-middle priority pe-4">
                                                <div class="d-flex align-items-center gap-2">
                                                  <div style="--falcon-circle-progress-bar: '.$persent.'">
                                                    <svg
                                                      class="circle-progress-svg"
                                                      width="26"
                                                      height="26"
                                                      viewBox="0 0 120 120"
                                                    >
                                                      <circle
                                                        class="progress-bar-rail"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke-width="12"
                                                      ></circle>
                                                      <circle
                                                        class="progress-bar-top"
                                                        cx="60"
                                                        cy="60"
                                                        r="54"
                                                        fill="none"
                                                        stroke-linecap="round"
                                                        stroke="'.($persent <= 30 ? '#e63757' : ($persent <= 50 ? '#F68F57' : ($persent <= 75 ? '#2A7BE4' : '#00D27B') )).'"
                                                        stroke-width="12"
                                                      ></circle>
                                                    </svg>
                                                  </div>
                                                  <h6 class="mb-0 text-700">'.toPersianNum($persent).'%</h6>
                                                </div>
                                              </td>';
                                              ?>

                                </td>
                                <td class="align-middle subject py-2 pe-2">
                                <a href="interviewcoach.php?user=<?=$faragir?>&skill=<?=$skill?>&contract=<?= $contractId ?>&plan=<?=$planCode?>" class="btn me-1 mb-1 btn-sm  btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1777]?>"><span class="bi-book"></span></a>
                                <a href="arzyabi.php?do=answer&id=<?= $faragir ?>&contract=<?= $contractId ?>" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1751]?>"><span class="bi-pie-chart-fill"></span></a>
                                <a href="#" data-user="<?=$faragir?>" data-contract="<?=$contractId?>" data-skill="<?=$skill?>" data-lng="<?=$language_default?>" class=" timelinecontent btn me-1 mb-1 btn-sm  btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1708]?>"><span class="bi-bar-chart-steps"></span></a>
                                <a href="coachmessage.php?user=<?=$faragir?>&skill=<?=$skill?>&contract=<?= $contractId ?>&plan=<?=$planCode?>" class="btn me-1 mb-1 btn-sm  btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[822] .'('.toPersianNum(mysqli_num_rows($resultMessage)).')'?>"><span class="bi-chat-dots-fill"></span></a>

                                </td>
                            </tr>

                            <?php
                            
                                    echo '</tr>';
                                }
                                   } 
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>