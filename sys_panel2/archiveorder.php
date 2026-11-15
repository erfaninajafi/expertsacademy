<?php
// include
include './includer.php';
$show_per_page = '100';
$pagetitle = $lang[2033];
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
                        $result_for_count = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_user = '$_GET[user]'");       
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("Select * from tbl_mohtavaregister where tbl_mohtavaregister_user = '$_GET[user]'  order by tbl_mohtavaregister_id DESC ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1537] . '</th>
                                    <th>' . $lang[61] . '</th>
                                    <th>' . $lang[2034] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $mohtava = $read['tbl_mohtavaregister_mohtava'];
                            $resultMohtava = Query("SELECT tbl_mohtava_title From tbl_mohtava Where  tbl_mohtava_lng='$language_default' and tbl_mohtava_code ='$mohtava' ");
                            $readMohtava = mysqli_fetch_array($resultMohtava);
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">' . $read['tbl_mohtavaregister_code'] . '</td>
                                <td class="align-middle p-2" >' . $readMohtava['tbl_mohtava_title'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_mohtavaregister_dateregister'] . '</td>
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