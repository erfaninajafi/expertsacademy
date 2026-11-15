<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = $lang[1745];

$thisaccess = '1,7,6';
include './header.inc.php';

foreach ($myContract as $key => $value) {
    if(!empty($value)) {
    $thisId = str_replace("[","",str_replace("]","",$value));
$result = Query("SELECT * From tbl_contract where tbl_contract_id='$thisId' and tbl_contract_status='1' ORDER BY tbl_contract_id DESC ");
if(mysqli_num_rows($result) > 0 ) {
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
                                <th><?=$lang[1567]?></th>
                                <th><?= $lang[1440] ?></th>
                                <th><?= $lang[1352] ?></th>
                                <th><?= $lang[1611] ?></th>
                                <th><?= $lang[1458] ?></th>
                                <th><?= $lang[1459] ?></th>
                                <th><?=$lang[1748]?></th>
                                <th><?= $lang[1709] ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php
                                 $read =  mysqli_fetch_array($result);

                                        $contractId = $read['tbl_contract_id'];
                                        $result_attach = Query("select * from tbl_attach where tbl_attach_contract = '$contractId'  ");
                                        $contractIdStr = '['.$contractId.'],';
                                        $result_member = Query("select tbl_member_id from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");
                                        
                                        ?>
                            <tr>
                                <td class="align-middle pe-3 py-2"><?=$read['tbl_contract_number']?></td>
                                <td class="align-middle pe-3 py-2  cat-code"><?=$read['tbl_contract_code']?></td>
                                <td class="align-middle pe-3 py-2"><?=strtoupper($read['tbl_contract_codesection'])?></td>
                                <td class="align-middle pe-3 py-2 ">
                                    <?= $read['tbl_contract_customer']?>
                                </td>
                                <td class="align-middle subject py-2 pe-2"><?= $read['tbl_contract_startdate'] ?></td>
                                <td class="align-middle status py-2 pe-2"><?= $read['tbl_contract_enddate'] ?></td>
                                <td class="align-middle status py-2 pe-2">

                                    <?php 
                                if(mysqli_num_rows($result_member) > '0') { 
                                    $h=0;
                                    echo '<div class="avatar-group avatar-group-dense mb-3">';
                                    $resultMember = Query("select * from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' order by tbl_member_id DESC ");
                                    while ($readMember = mysqli_fetch_array($resultMember)) {
                                        if($h <= 3)
                                        {
                                            echo '<div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                            <img class="rounded-circle" src="'.(!empty($readMember['tbl_member_profile']) ? '../upload/profile/' . $readMember['tbl_member_profile'] : 'assets/img/no-image.jpg').'" alt="'.$readMember['tbl_member_fname'].' '.$readMember['tbl_member_lname'].'">
                                            </div>';
                                            $h++;
                                            
                                        }
                                        else 
                                        {
                                            break;
                                        }
                                    }
                                    if(mysqli_num_rows($result_member) > $h)
                                    {
                                        echo '<div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                        <div class="avatar-name rounded-circle "><span>+'.abs(mysqli_num_rows($result_member)-4).'</span></div>
                                        </div>';
                                    }
                                    echo '</div>';
                                    ?>


                                    <?php } else { echo toPersianNum('0'); } ?>

                                </td>
                                <td class="align-middle subject py-2 pe-2">
                                    <a href="arzyabi-list.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm  btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1751]?>"><span class="bi-pie-chart-fill"></span></a>
                                    <a href="#" class="btn me-1 mb-1 btn-sm  btn-dark" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=$lang[1750]?>"><span class="bi-bar-chart-fill"></span></a>
                                    <a href="faragir-list.php?contract=<?= $read['tbl_contract_id'] ?>" class="btn me-1 mb-1 btn-sm btn-success " data-contract="<?= $read['tbl_contract_id'] ?>" data-lng="<?= $language_default ?>" data-bs-toggle="tooltip" data-bs-placement="top" title="<?= $lang[1451] ?>"><span class="bi-people-fill"></span></a>

                                </td>
                            </tr>

                            <?php
                            
                                    echo '</tr>';
                            ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
<?php } }else {
    ?>
<div class="row gx-3 mb-2">
    <div class="col-xxl-12 col-xl-12 p-0">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $lang[1745] ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(<?=$site_url?>assets/img/icons/spot-illustrations/corner-4.png);">
            </div>
            <div class="card-body p-0 position-relative">
                <div class="alert alert-warning m-3" role="alert"><?= $lang[57] ?></div>
            </div>
        </div>
    </div>
    <?php
}}
include './footer.inc.php';

?>