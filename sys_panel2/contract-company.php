<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = $lang[1307];

$thisaccess = '1,7';
include './header.inc.php';

            if (isset($do) && $do == 'view') {
                $result = Query("SELECT * FROM tbl_contract Where tbl_contract_id = '$id'");
                $read = mysqli_fetch_array($result);
                $contractIdStr = '['.$id.'],';
                $result_member = Query("select * from tbl_member where tbl_member_lastcontract like '%$contractIdStr%' ");

                    $thisMentor = $read['tbl_contract_project'];
                    $result_m = Query("select tbl_user_name from tbl_user where tbl_user_usercode = '$thisMentor'  ");
                    $read_m = mysqli_fetch_array($result_m);

                //find end plan
                $resultPlan =Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_contract='$id' and tbl_memberskill_datereport !='' ");  
                // added plan
                $resultPlanAdded =Query("select tbl_memberskill_id from tbl_memberskill where tbl_memberskill_contract='$id' ");  

                $allFaragir = mysqli_num_rows($result_member);
                $faragirHavePlan = mysqli_num_rows($resultPlanAdded);
                $faragirFinishd = mysqli_num_rows($resultPlan);

                ?>

<div class="card mb-3">
            <div class="card-body">
              <div class="row justify-content-between align-items-center">
                <div class="col">
                  <div class="d-flex">
                    <div class="flex-1 fs--1">
                      <h5 class="fs-0"><?= $lang[1565].' : '.$read['tbl_contract_code'] ?></h5>
                      <h5 class="fs-0"><?=$lang[1566].' : '.$read_m['tbl_user_name']?></h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0"><?=$lang[1571].' : '.$read['tbl_contract_startdate']?></h5>
                    <h5 class="fs-0"><?=$lang[1572].' : '.$read['tbl_contract_enddate']?></h5>
                      <?=(!empty($read['tbl_contract_editdate']) ? '<h5 class="fs-0">'.$lang[1573].' : '. $read['tbl_contract_editdate'] .'</h5>' : '' ) ?>
                    </div>
                  </div>
                </div>
                <div class="col-md-auto mt-4 mt-md-0">
    
                </div>
              </div>
            </div>
          </div>

          <div class="row g-0">
            <div class="col-lg-9 pe-lg-2">
              <div class="card mb-3 mb-lg-0">
                <div class="card-body">
                  <h4 class="mb-3"><?=$lang[1570]?></h4>
                  <p><?= $read['tbl_contract_text'] ?></p>
                  <?php if (!empty($read["tbl_contract_file"])) { ?>

                        <h4><?=$lang[52]?> :</h4>
                        <ul>
                        <?php
                            $file = explode(",", $read['tbl_contract_file']);
                            foreach ($file as $key => $value) {
                                if (!empty($value)) {
                                    echo '<li><a href="' . $value . '">'.$lang[52].' [' . $key . ']</a></li>';
                                }
                            } ?>
                        </ul>
                    </div>
                    <?php } ?>
               </div>
              </div>
              
<?php 

$resultAttach = Query("Select * from tbl_attach Where tbl_attach_contract ='$id' order by tbl_attach_id DESC");

?>



        <div class="card mt-3">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $lang[1448]?></h4>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0 position-relative">
                <?php
                $i=1;
    ?>
                <div class="scrollbar">
                    <table class="table table-sm mb-0 fs--1 table-view-tickets">
                        <thead class="text-800 bg-light">
                            <tr>
                            <th><?= $lang[60] ?></th>
                                    <th><?=$lang[1580]?></th>
                                    <th><?=$lang[1581]?></th>
                                    <th><?=$lang[1582]?></th>
                                    <th><?=$lang[63] ?></th>
                            </tr>
                        </thead>
                        <tbody class="list">
                            <?php
                                    while ($readAttach=mysqli_fetch_array($resultAttach)) {
                                        $resultAttachContact = Query("select * from tbl_contract where tbl_contract_id = '$id'  ");
                                        $readContact = mysqli_fetch_array($resultAttachContact);
                                        echo '<tr>
                                        <td class="align-middle p-2">' . $i . '</td>
                                        <td class="align-middle p-2">' . $readContact['tbl_contract_number'] . '</td>
                                        <td class="align-middle p-2">' . $readAttach['tbl_attach_number'] . '</td>
                                        <td class="align-middle p-2">' . $readAttach['tbl_attach_title'] . '</td>
                                        <td class="align-middle p-2"><a href="?do=attach&id=' . $readAttach['tbl_attach_id'] . '&contract=' . $id . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[87] . '" ><span class="bi-eye-fill"></span></a>                                
                                        </td>
                                        </tr>';
                                    $i++;
                                    }
                            ?>
                        </tbody>
                    </table>
                </div>
                <?php
?>
            </div>
        </div>
            </div>
            <div class="col-lg-3 ps-lg-2">
         
              <div class="sticky-sidebar">
                <div class="card mb-3 fs--1">
                  <div class="card-body">

                    <div class="col-lg-12 col-xxl-12">
                          <div class="row mx-0">
                            <div class="col-6 border-end border-bottom py-3">
                              <h5 class="fw-normal text-600"><?=$allFaragir.' '.$lang[1575]?></h5>
                              <h6 class="text-500 mb-0"><?=$lang[714]?></h6>
                            </div>
                            <div class="col-6 border-bottom py-3">
                              <h5 class="fw-normal text-600"><?=$faragirFinishd.' '.$lang[1575]?></h5>
                              <h6 class="text-500 mb-0"><?=$lang[1576]?></h6>
                            </div>
                            <div class="col-6 border-end py-3">
                              <h5 class="fw-normal text-600"><?=$faragirHavePlan.' '.$lang[1575]?></h5>
                              <h6 class="text-500 mb-0"><?=$lang[1577]?></h6>
                            </div>
                            <div class="col-6 py-3">
                              <h5 class="fw-normal text-600"><?=$allFaragir-$faragirHavePlan.' '.$lang[1575]?></h5>
                              <h6 class="text-500 mb-0"><?=$lang[1578]?></h6>
                            </div>
                          </div>
                        </div>
                  </div>
                </div>
              </div>

              <div class="sticky-sidebar">
                <div class="card mb-3 fs--1">
                  <div class="card-body">
                    <h4><?=$lang[1574]?></h4>
                    <div class=" h-100">
                        <div class="card-body d-flex flex-center flex-column">
                        <div class="circle_percent" data-percent="<?=($allFaragir !='0' ? abs(((($allFaragir - $faragirFinishd) / $allFaragir) * 100)-100) : '0')?>"> 
                        <div class="circle_inner">
                            <div class="round_per"></div>
                        </div>
                    </div>
                            <div class="text-center mt-3">
                                <h6 class="fs-0 mb-1">
                                    <span class="fas fa-check text-primary me-1" data-fa-transform="shrink-2"></span><?=($allFaragir !='0' ? abs(((($allFaragir - $faragirFinishd) / $allFaragir) * 100)-100) : '0')?> 
                                </h6>
                                <p class="fs--1 mb-0"><?=$lang[1579]?></p>
                            </div>
                        </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
                





            <?php
            }

            if (isset($do) && $do == 'attach') {
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
                $result = Query("SELECT * FROM tbl_attach Where tbl_attach_id = '$id'");
                $read = mysqli_fetch_array($result);

                $contractId = $read['tbl_attach_contract'];
                $resultAttach = Query("select * from tbl_contract where tbl_contract_id = '$contractId'  ");
                $readContact = mysqli_fetch_array($resultAttach);

                ?>
                <div class="col-12 p-3">
                    <div class="form-group">
                        <p><?=$lang[1580]?> : 
                        <?= toPersianNum($readContact['tbl_contract_number']) ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[1581]?> : 
                        <?= toPersianNum($read['tbl_attach_number']) ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[61]?> : 
                        <?= $read['tbl_attach_title'] ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[1458]?> : 
                        <?= toPersianNum($read['tbl_attach_startdate']) ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[1459]?> : 
                        <?= toPersianNum($read['tbl_attach_enddate']) ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[1573]?> : 
                        <?= toPersianNum($read['tbl_attach_editdate']) ?></p>
                    </div>
                    <div class="form-group">
                        <p><?=$lang[1527]?> : 
                        <?= $read['tbl_attach_text'] ?></p>
                    </div>
                    <div class="form-group">
                        <label><?=$lang[52]?></label>
                        <p><?php
                                $file = explode(",", $read['tbl_attach_file']);
                                foreach ($file as $key => $value) {
                                    if (!empty($value)) {
                                        echo '<a href="' . $value . '">'.$lang[52].'  [' . $key . ']</a>';
                                    }
                                } ?>
                        </p>
                    </div>
                </div>
                </div>
                </div>
            </div>
            <?php
            }
include './footer.inc.php';

?>