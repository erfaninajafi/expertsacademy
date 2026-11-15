<?php
$Tabpage = '16';
$thisaccess = '1,2,3,4,5,6,7';
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
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">' . $lang[1760] . '</button>
        </div>
      </div>
    </div>
  </div>';

if (!empty($_GET['contract'])) {
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    echo '<div class="row gx-3 ">
    <div class="col-xxl-12 col-xl-12 p-0">
    <div class="box box-body ">
<div class="card mb-2">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">' . $lang[1440] . '  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">' . $lang[1567] . '  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">' . $lang[1611] . '  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">' . $lang[1612] . '  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">' . $lang[1613] . '  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">' . $lang[1566] . '  : ' . $read_contract['tbl_contract_manager'] . '</h5>
            <h5 class="fs-0">' . $lang[1571] . '  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">' . $lang[1572] . '  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>
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

<?php

$allSkill = array();
$resultContract = Query("select tbl_memberskill_data from tbl_memberskill where tbl_memberskill_contract = '$getcontract'  ");
while ($readContract = mysqli_fetch_array($resultContract)) {
    $data = explode(",", $readContract['tbl_memberskill_data']);
    foreach ($data as $key => $value) {
        if (!empty($value)) {
            $allSkill[$value] = $value;
        }
    }
}
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
            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
            </div>
            <div class="card-body p-0 position-relative">
                <div class="table-responsive scrollbar">

                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                        <thead class="text-800 bg-light">
                            <tr>
                                <th><?= $lang[1347] ?></th>
                                <th><?= $lang[1770] ?></th>
                                <th><?= $lang[1772] ?></th>
                                <th><?= $lang[714] ?></th>
                                <th><?= $lang[1709] ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php
                            foreach ($allSkill as $key => $value) {
                                $result = Query("SELECT * From tbl_categories where tbl_categories_code='$value' and tbl_categories_lng='$language_default' ");
                                if (mysqli_num_rows($result) > 0) {
                                    $read = mysqli_fetch_array($result);

                                    $coutFaragir = Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_contract='$getcontract' and tbl_memberskill_data like '%$value%' ");
                                    $thisContract = sqi($_GET['contract']);
                                    $resultPlan = Query("SELECT tbl_plan_id FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_contract ='$thisContract' and tbl_plan_skill='$value'");
                                    $resultPlanConfirm = Query("SELECT tbl_plan_id FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_contract ='$thisContract' and tbl_plan_skill='$value'");
                                    $count = mysqli_num_rows($resultPlan);
                                    $confirm = mysqli_num_rows($resultPlanConfirm);

                            ?>
                                    <tr>
                                        <td class="align-middle pe-3 py-2"><?= strtoupper($read['tbl_categories_catcode']) ?></td>
                                        <td class="align-middle pe-3 py-2"><?= $read['tbl_categories_title'] ?></td>
                                        <td class="align-middle pe-3 py-2">
                                            <?php
                                            echo '<div class="skills padding-right-clear">
                                    <div class="d-flex align-items-center">
                                    <div class="progress me-3 rounded-3 bg-200" style="height: 5px; width:80px;" role="progressbar" aria-valuenow="39" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar rounded-pill" style="width: ' . ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100))) . '%;"></div>
                                    </div>
                                    <div class="fw-semi-bold ms-2">' . ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100))) . '%</div>
                                </div>
                                        </div>';
                                            ?>
                                        </td>
                                        <td class="align-middle pe-3 py-2"><?= toPersianNum(mysqli_num_rows($coutFaragir)) ?></td>
                                        <td class="align-middle subject py-2 pe-2">
                                            <a href="#" class="btn me-1 mb-1 btn-sm btn-warning ajaxkarfarmamenti" data-skill="<?= $value ?>" data-contract="<?= $getcontract ?>" data-lng="<?= $language_default ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1749] ?>"><span class="bi-card-heading"></span></a>
                                        </td>
                                    </tr>
                            <?php }
                            } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

<?php
if (!empty(reset($allSkill))) {

?>
    <div class="ajaxresult">

        <?php
        $firstValue = reset($allSkill);

        $resultSkill = Query("SELECT tbl_categories_title From tbl_categories where tbl_categories_code='$firstValue' and tbl_categories_lng='$language_default' ");
        $readSkill =  mysqli_fetch_array($resultSkill);
        $result = Query("select * from tbl_memberskill where tbl_memberskill_data like '%$firstValue%' and tbl_memberskill_contract = '$getcontract'  ");
        if (mysqli_num_rows($result) > 0) {
        ?>

            <div class="row gx-3 mb-2">
                <div class="col-xxl-12 col-xl-12 p-0">
                    <div class="card">
                        <div class="card-header border-bottom border-200 px-0">
                            <div class="d-lg-flex justify-content-between">
                                <div class="row flex-between-center gy-2 px-x1">
                                    <div class="col-auto pe-0">
                                        <h4 class="mb-0"><?= $readSkill['tbl_categories_title'] ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?= $site_url ?>assets/img/icons/spot-illustrations/corner-4.png);">
                        </div>
                        <div class="card-body p-0 position-relative">
                            <div class="table-responsive scrollbar">

                                <table class="table table-sm mb-0 fs--1 table-view-tickets">
                                    <thead class="text-800 bg-light">
                                        <tr>
                                            <th><?= $lang[53] ?></th>
                                            <th><?= $lang[127] ?></th>
                                            <th><?= $lang[385] ?></th>
                                            <th><?= $lang[1771] ?></th>
                                            <th><?= $lang[1340] ?></th>
                                            <th><?= $lang[1772] ?></th>
                                            <th><?= $lang[1709] ?></th>
                                        </tr>
                                    </thead>
                                    <tbody class="list">
                                        <?php
                                        while ($read = mysqli_fetch_array($result)) {
                                            $memberContract = $read['tbl_memberskill_contract'];
                                            $mentor = $read['tbl_memberskill_mentor'];
                                            $resultMentor = Query("select tbl_user_name from tbl_user where tbl_user_id = '$mentor' ");
                                            $readMentor = mysqli_fetch_array($resultMentor);
                                            $userId = $read['tbl_memberskill_user'];
                                            $resultMember = Query("SELECT * From tbl_member where tbl_member_id='$userId' ");
                                            if (mysqli_num_rows($resultMember) > 0) {
                                                $readMember = mysqli_fetch_array($resultMember);
                                                $resultNumber = Query("select * from tbl_number where tbl_number_skill='$firstValue' and tbl_number_user='$userId' and tbl_number_contract='$memberContract' ");
                                                echo '<tr>
                                    <td class="align-middle p-2">
                                    <div class="notification-avatar">
                                    <div class="avatar avatar-xl me-3">
                                        <img class="rounded-circle" src="' . (!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                    </div>
                                    </div>
                                    </td>
                                    <td class="align-middle p-2">
                                    ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</td>
                                    <td class="align-middle p-2">' . toPersianNum($readMember['tbl_member_nationalcode']) . '</td>
                                    <td class="align-middle p-2">' . $readMentor['tbl_user_name'] . '</td>
                                    <td class="align-middle p-2">' . (mysqli_num_rows($resultNumber) <= 0 ? '<small class="badge rounded badge-subtle-danger false" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="' . $lang[1556] . '" data-bs-original-title="' . $lang[1556] . '"><span class="bi-check-circle"></span></small>' : '<small class="badge rounded badge-subtle-success false" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="' . $lang[712] . '" data-bs-original-title="تایید شده"><span class="bi-check-circle-fill"></span></small>') . '</td>';

                                                $resultPlan = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$userId' and tbl_plan_contract ='$memberContract' and tbl_plan_skill='$firstValue'");
                                                $resultPlanConfirm = Query("SELECT tbl_plan_title FROM tbl_plan where tbl_plan_status='1' and tbl_plan_delete='0' and tbl_plan_lng='$language_default' and tbl_plan_menti = '$userId' and tbl_plan_contract ='$memberContract' and tbl_plan_skill='$firstValue'");
                                                $count = mysqli_num_rows($resultPlan);
                                                $confirm = mysqli_num_rows($resultPlanConfirm);
                                                $persent = ($confirm == 0 ? $confirm : round(abs(((($count - $confirm) / $count) * 100) - 100)));
                                                echo '<td class="align-middle priority pe-4">
                                                <div class="d-flex align-items-center gap-2">
                                                  <div style="--falcon-circle-progress-bar: ' . $persent . '">
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
                                                        stroke="' . ($persent <= 30 ? '#e63757' : ($persent <= 50 ? '#F68F57' : ($persent <= 75 ? '#2A7BE4' : '#00D27B'))) . '"
                                                        stroke-width="12"
                                                      ></circle>
                                                    </svg>
                                                  </div>
                                                  <h6 class="mb-0 text-700">' . toPersianNum($persent) . '%</h6>
                                                </div>
                                              </td>';


                                                echo '<td class="align-middle p-2">
                                    <a href="#" data-user="' . $userId . '" data-contract="' . $memberContract . '" data-skill="' . $firstValue . '" data-lng="' . $language_default . '" class=" timelinecontent btn me-1 mb-1 btn-sm  btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1708] . '"><span class="bi-bar-chart-steps"></span></a>
                                    <a href="arzyabi-list.php?do=answer&id=' . $userId . '&contract=' . $memberContract . '" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1751] . '"><span class="bi-pie-chart-fill"></span></a>
                                    <a href="#" class="shayestegi btn me-1 mb-1 btn-sm  btn-success" data-user="' . $userId . '" data-contract="' . $memberContract . '" data-skill="' . $firstValue . '" data-lng="' . $language_default . '" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1773] . '"><span class="bi-person-check-fill"></span></a>
                                    </td>
                                        </tr>';
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

    <?php }
    } ?>


    <?php
    include './footer.inc.php';
    ?>