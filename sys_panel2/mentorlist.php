<?php
include './includer.php';
$show_per_page = '300';
$pagetitle = $lang[1927];
$Tabpage = '25';
$thisaccess = '7';
include './header.inc.php';
$checklistQuerry = '';

if ($access == '9') {
    if (empty($getcontract)) {
        header("Location: index.php");
        exit();
    } else {
        $checklistQuerry = " tbl_checklist_contract ='$getcontract' and  tbl_checklist_admin='$userCode' and";
    }
}
if ($access == '7') {
    $resultMentor = Query("select * from tbl_user where tbl_user_contract like '%[$getcontract]%' and tbl_user_id ='$this_user_id' ");
    if (mysqli_num_rows($resultMentor) > 0) {
        $checklistQuerry = " tbl_checklist_contract ='$getcontract' and  ";
    } else {
        $checklistQuerry = " tbl_checklist_contract ='$getcontract' and  tbl_checklist_admin='$userCode' and";
    }
}
if (isset($do) && ($do == 'showmentor' || $do == 'showreport')) {
    $result = Query("SELECT * From tbl_checklist where  tbl_checklist_code='$code' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'   ");
    $read = mysqli_fetch_array($result);
    if ($read['tbl_checklist_list'] != '0') {
        $result_checklistdefault = Query("SELECT tbl_checklistdefault_title From tbl_checklistdefault where tbl_checklistdefault_code='$read[tbl_checklist_list]' and tbl_checklistdefault_lng='$language_default'  ");
        $read_checklistdefault = mysqli_fetch_array($result_checklistdefault);
        $pagetitle .= '<hr><br>' . $read_checklistdefault['tbl_checklistdefault_title'];
    } else {
        $pagetitle .= '<hr><br>' . $read['tbl_checklist_title'];
    }
}
?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                            <?= (!isset($do) && $access == '9' ? '<div class="plus">
                                <a href="?do=add' . (!empty($getcontract) ? '&contract=' . $getcontract : '') . '"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php
                if (!isset($do)) { ?>
                    <table>
                        </tr>
                        <td class="col-sm-6 p-3">
                            <form action="?do=search" method="POST" class="search-form">
                                <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                                <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                            </form>
                        </td>
                        </tr>
                    </table>
                <?php
                    // start count db result for pagination               
                    $result_for_count = Query("SELECT * From tbl_checklist where $checklistQuerry tbl_checklist_title like '%$keyword%' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'   ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination               
                    $result = Query("SELECT * From tbl_checklist where $checklistQuerry  tbl_checklist_title like '%$keyword%' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  order by tbl_checklist_order ASC , tbl_checklist_order DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1944] . ' </th>
                                    <th>' . $lang[1459] . ' </th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $list = $read['tbl_checklist_list'];
                        if ($list == '0') {
                            $thisTitle = $read['tbl_checklist_title'];
                        } else {
                            $result_checklistdefault = Query("SELECT tbl_checklistdefault_title From tbl_checklistdefault where tbl_checklistdefault_code='$list' and tbl_checklistdefault_lng='$language_default'  ");
                            $read_checklistdefault = mysqli_fetch_array($result_checklistdefault);
                            $thisTitle = $read_checklistdefault['tbl_checklistdefault_title'];
                        }

                        $result_report_count = Query("select * from tbl_checklistreport where  tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");

                        $result_report = Query("select * from tbl_checklistreport where  tbl_checklistreport_mentor='$userCode' and tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");
                        $read_report = mysqli_fetch_array($result_report);
                        echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                        
                        <td class="align-middle p-2">' . $thisTitle . '</td>
                        <td class="align-middle p-2">' . $read['tbl_checklist_expire'] . '</td>
                        <td class="align-middle p-2">
                        ' . ($access == '9' ? '<a href="?do=edit&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                        <a href="?do=delete&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '') . '
                        <a href="?do=showmentor&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-eye-fill"></span></a>
                        </td>
                        </tr>';
                        $i++;
                    }

                    echo '</tbody></table>';
                    // start pagination                
                    if ($page > $page_number) {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                }

                if (isset($do) && $do == 'showmentor') {

                    $result = Query("SELECT * From tbl_contract where tbl_contract_id='$getcontract'  ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1447] . ' </th>
                                    <th>' . $lang[1930] . ' </th>
                                    <th>' . $lang[1945] . ' </th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $mentor = explode(",", $read['tbl_contract_mentor']);
                    foreach ($mentor as $key => $value) {
                        if (!empty($value)) {
                            $result_mentor = Query("select * from tbl_user where tbl_user_usercode='$value' ");
                            $read_mentor = mysqli_fetch_array($result_mentor);
                            $result_report = Query("select * from tbl_checklistreport where tbl_checklistreport_listcode='$code' and tbl_checklistreport_karfarma='1' and tbl_checklistreport_mentor='$value' and tbl_checklistreport_contract='$getcontract'");
                            $read_report = mysqli_fetch_array($result_report);
                            echo '<tr>
                                    <td class="align-middle p-2">' . ($key + 1) . '</td>
                                    <td class="align-middle p-2">' . $read_mentor['tbl_user_name'] . '</td>
                                    <td class="align-middle p-2">' . (mysqli_num_rows($result_report) <= 0 ? $lang[1495]  : $lang[1494]) . '</td>
                                    <td class="align-middle p-2">' . (mysqli_num_rows($result_report) <= 0 ? ''  : $read_report['tbl_checklistreport_reportdate']) . '</td>
                                    <td class="align-middle p-2">
                                    ' . (mysqli_num_rows($result_report) > 0 ? '<a href="?do=showreport&code=' . $code . '&contract=' . $getcontract . '&mentor=' . $value . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-eye-fill"></span></a>' : '') . '
                                    </td></tr>';
                        }
                    }

                    echo '</tbody></table>';
                }
                if (isset($do) && $do == 'showreportlist') {


                    $result = Query("SELECT * From tbl_checklistreport where tbl_checklistreport_karfarma='1' and tbl_checklistreport_contract ='$getcontract' and tbl_checklistreport_mentor='$getmentor' and tbl_checklistreport_lng='$language_default' order by tbl_checklistreport_id DESC  ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list

                    echo '<div class="table-responsive scrollbar">
                         <table class="table table-sm mb-0 fs--1 table-view-tickets">
                             <thead class="text-800 bg-light">
                                 <tr>
                                     <th>' . $lang[60] . '</th>
                                     <th>' . $lang[61] . ' </th>
                                     <th>' . $lang[865] . ' </th>
                                     <th>' . $lang[63] . '</th>
                                 </tr>
                             </thead>
                             <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {

                        echo '<tr>
                         <td class="align-middle p-2">' . $i . '</td>
                         <td class="align-middle p-2">' . $read['tbl_checklistreport_title'] . '</td>
                         <td class="align-middle p-2">' . $read['tbl_checklistreport_reportdate'] . '</td>
                         <td class="align-middle p-2">' . ($read['tbl_checklistreport_karfarma'] == '1' ? '<a href="?do=showreport&code=' . $read['tbl_checklistreport_listcode'] . '&reportcode=' . $read['tbl_checklistreport_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-pencil-square"></span></a>' : '') . '</td>
                         </tr>';
                        $i++;
                    }

                    echo '</tbody></table>';
                }
                if (isset($do) && $do == 'showreport') {

                    $result = Query("SELECT * FROM tbl_checklistreport Where tbl_checklistreport_karfarma='1' and tbl_checklistreport_listcode ='$code' ");
                    if (mysqli_num_rows($result) <= 0) {
                        header("location: 404.php");
                        exit();
                    }


                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_checklistreport_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_checklistreport Where tbl_checklistreport_karfarma='1' and tbl_checklistreport_listcode ='$code'   $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    // form for edit
                    echo '<div class="col-lg-12 col-12 p-3">';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                            <label>' . $lang[61] . '<span class="language">' . $language[$i] . '</span></label>
                            <p>' . @read_fild($language[$i], 'tbl_checklistreport_title') . '</p>
                            </div><hr>';
                    }
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                            <label>' . $lang[1216] . '<span class="language">' . $language[$i] . '</span></label>
                            <p>' . @read_fild($language[$i], 'tbl_checklistreport_comment') . '</p>
                            </div><hr>';
                    }
                    echo '<div class="form-group">
                            <label>' . $lang[1499] . '</label>
                            <p>' . read_fild(Null, 'tbl_checklistreport_reportdate') . '</p>
                            </div><hr>';

                    $reportfile = read_fild(NULL, 'tbl_checklistreport_reportfile');
                    if (!empty($reportfile)) {
                        echo '<div class="form-group"><label>' . $lang[1153] . '</label>
                                  <a href="../upload/report/' . $reportfile . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                  </div><hr>';
                    } else {
                        echo '<div class="form-group"><label>' . $lang[1932] . '</label></div><hr>';
                    }
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