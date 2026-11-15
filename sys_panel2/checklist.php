<?php
include './includer.php';
$show_per_page = '30';
$pagetitle = $lang[1927];
$Tabpage = '25';
$thisaccess = '1,9,5';
include './header.inc.php';
$checklistQuerry = '';

if ($access == '5') {
    $resultMentor = Query("select * from tbl_contract where tbl_contract_mentor like '%$userCode%' and tbl_contract_id ='$getcontract' ");
    if (mysqli_num_rows($resultMentor) > 0) {
        $checklistQuerry = " tbl_checklist_contract ='$getcontract' and  ";
    } else {
        $checklistQuerry = " tbl_checklist_contract ='$getcontract' and  tbl_checklist_admin='$userCode' and";
    }
}
if ($access == '9') {
    if (empty($getcontract)) {
        header("Location: index.php");
        exit();
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
        $pagetitle = '<a href="checklist.php?contract=' . $getcontract . '">' . $pagetitle . '</a><hr><br> ' . $read_checklistdefault['tbl_checklistdefault_title'];
    } else {
        $pagetitle = '<a href="checklist.php?contract=' . $getcontract . '">' . $pagetitle . '</a><hr><br> ' . $read['tbl_checklist_title'];
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
                            <h5 class="mb-0"><?= $pagetitle ?></h5>
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

                    $result = Query("SELECT * From tbl_checklist where $checklistQuerry tbl_checklist_title like '%$keyword%' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'  order by tbl_checklist_order ASC , tbl_checklist_order DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    // start show list
                    echo ($access == '9' ? '<form action="?do=runupdate&contract=' . $getcontract . '" method="POST">
                        <input type="hidden" name="contract" value="' . $getcontract . '">' : '');
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    ' . ($access == '9' ? '<th><a href="?orderby=order&contract=' . $getcontract . '">' . $lang[1336] . ' <span class="' . (!empty($_GET['orderby']) && $_GET['orderby'] == 'order' ? 'icon-keyboard_arrow_up' : 'icon-keyboard_arrow_down') . '"></span></a></th>' : '') . '
                                    <th>' . $lang[1944] . ' </th>
                                    <th>' . $lang[1459] . ' </th>
                                    ' . ($access == '9' ? '<th>' . $lang[1340] . ' </th>' : '') . '
                                    ' . ($access == '9' ? '<th>' . $lang[2022] . ' </th>' : '') . '
                                    ' . ($access == '9' ? '<th>' . $lang[1340] . ' </th>' : '') . '
                                    ' . ($access == '9' ? '<th>' . $lang[1953] . ' </th>' : '') . '
                                    ' . ($access == '9' ? '<th>' . $lang[1954] . ' </th>' : '') . '
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


                        $result_report_confirm = Query("select * from tbl_checklistreport where tbl_checklistreport_status='1' and tbl_checklistreport_listcode ='$read[tbl_checklist_code]' and tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");
                        $result_report_notconfirm = Query("select * from tbl_checklistreport where  tbl_checklistreport_status='0' and tbl_checklistreport_listcode ='$read[tbl_checklist_code]' and tbl_checklistreport_contract='$getcontract' and  tbl_checklistreport_lng='$language_default'");
                        $count = mysqli_num_rows($result_report_confirm) + mysqli_num_rows($result_report_notconfirm);
                        $persent = (mysqli_num_rows($result_report_confirm) == 0 ? mysqli_num_rows($result_report_confirm) : round(abs(((($count - mysqli_num_rows($result_report_confirm)) / $count) * 100) - 100)));
                        echo '<tr>
                        <td class="align-middle p-2">' . $i . '</td>
                        ' . ($access == '9' ? '<td class="align-middle p-2">
                        <input type="hidden" name="code[]" value="' . $read['tbl_checklist_code'] . '">
                        <input type="text" name="o' . $read['tbl_checklist_code'] . '" value="' . $read['tbl_checklist_order'] . '" maxlength="5" style="width: 26px;text-align: center;height: 23px;margin-left: 2px;">
                        </td>' : '') . '
                        <td class="align-middle p-2">' . $thisTitle . '</td>
                        <td class="align-middle p-2">' . $read['tbl_checklist_expire'] . '</td>
                        ' . ($access == '9' ? '
                        <td class="align-middle status py-2 pe-2">
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
                        </td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . (mysqli_num_rows($result_report_confirm) + mysqli_num_rows($result_report_notconfirm)) . '</td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2"><input type="checkbox" value="1" data-name="checklist" data-id="' . $read['tbl_checklist_code'] . '" class="checkboxajax" ' . ($read['tbl_checklist_confirm'] == 1 ? 'checked' : '') . '></td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . mysqli_num_rows($result_report_confirm) . '</td>' : '') . '
                        ' . ($access == '9' ? '<td class="align-middle p-2">' . mysqli_num_rows($result_report_notconfirm) . '</td>' : '') . '
                        <td class="align-middle p-2">
                        ' . ($access == '9' ? '<a href="?do=edit&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                        <a href="?do=delete&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>' : '') . '
                        ' . ($access == '9' ? '<a href="?do=showmentor&code=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-eye-fill"></span></a>' : '') . '
                        ' . ($access == '5' ? '<a href="checklistreport.php?checklist=' . $read['tbl_checklist_code'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1929] . '"><span class="bi-pencil-square"></span></a>' : '') . '
                        </td>
                        </tr>';
                        $i++;
                    }

                    echo '</tbody></table>' . ($access == '9' ? '<input type="submit" name="runupdate" value="' . $lang[1342] . '" class="btn btn-warning update-btn m-3" ></form>' : '');
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
                                        <li><a class="page-link" href="<?= '?page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '')  ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="<?= '?page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['class']) ? '&class=' . $_GET['class'] : '') ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                            </tr>
                        </table>
                <?php
                    }
                }
                if (isset($do) && $do == 'runupdate' && $access == '9') {
                    $contract = $_POST['contract'];
                    $code = $_POST['code'];
                    foreach ($code as $key => $value) {
                        $val = $_POST['o' . $value];
                        Query("update tbl_checklist set tbl_checklist_order='$val' Where tbl_checklist_code = '$value' ");
                    }
                    header("Location: checklist.php?contract=$contract");
                }
                // end = pagination
                // start = do add
                if (isset($do) && $do == 'add' && $access == '9') {
                    echo '<div class="col-lg-12 col-12 p-3">
                        <form action="?do=additem&contract=' . $getcontract . '" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="contract" value="' . $getcontract . '">';
                    echo '<div class="form-group">
                        <label>' . $lang[1939] . '</label><select name="question" style="width: 100%;" class="select2">
                            <option value="0">' . $lang[70] . '</option>';
                    $result_checklistdefault = Query("SELECT * From tbl_checklistdefault where tbl_checklistdefault_admin='$userCode' and tbl_checklistdefault_delete='0' and tbl_checklistdefault_lng='$language_default' order by tbl_checklistdefault_id DESC ");
                    while ($read_checklistdefault = mysqli_fetch_array($result_checklistdefault)) {
                        echo '<option value="' . $read_checklistdefault['tbl_checklistdefault_code'] . '">' . $read_checklistdefault['tbl_checklistdefault_title'] . '</option>';
                    }
                    echo '
                        </select>
                    </div>';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                                  <label>' . $lang[1944] . '<span class="language">' . $language[$i] . '</span></label>
                                  <input type="text" name="title_' . $language[$i] . '" class="form-control">
                                  </div>';
                    }

                    echo '<div class="form-group">
                        <label>' . $lang[2023] . '</label>
                        <input type="text" name="persent" class="form-control">
                        </div>';

                    echo '<div class="form-group">
                        <label>' . $lang[1459] . '</label>
                        <input type="text" name="expire" class="form-control datepicker">
                        </div>';

                    echo '<div class="form-group">
                        <input type="submit" name="additem" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                        </div></form></div>';
                }
                // start = add record to db
                if (isset($do) && $do == 'additem' && $access == '9') {
                    if (isset($_POST['additem'])) {
                        $code = Code("", "no", 7);
                        $date = jdate("Y/m/d : a:h:s");
                        $contract = $_POST['contract'];
                        $question = $_POST['question'];
                        $persent = $_POST['persent'];
                        $expire = $_POST['expire'];
                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $language_name = $language[$i];
                            Query("insert into tbl_checklist (
                                tbl_checklist_title,
                                tbl_checklist_dateadd,
                                tbl_checklist_lng,
                                tbl_checklist_code,
                                tbl_checklist_delete,
                                tbl_checklist_admin,
                                tbl_checklist_order,
                                tbl_checklist_contract,
                                tbl_checklist_status,
                                tbl_checklist_list,
                                tbl_checklist_expire,
                                tbl_checklist_karfarma,
                                tbl_checklist_persent) values('$title_[$i]','$date','$language_name','$code','0','$userCode','0','$contract','0','$question','$expire','0','$persent')");
                        }
                        $_SESSION['error'] = $lang[32];
                        header("Location: checklist.php?contract=$contract");
                        exit();
                    }
                }
                // end  = add record  
                if (isset($do) && $do == 'showmentor' && $access == '9') {

                    $result = Query("SELECT * From tbl_contract where tbl_contract_id='$getcontract'  ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[1447] . ' </th>
                                    <th>' . $lang[1930] . ' </th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $mentor = explode(",", $read['tbl_contract_mentor']);
                    foreach ($mentor as $key => $value) {
                        if (!empty($value)) {
                            $result_mentor = Query("select * from tbl_user where tbl_user_usercode='$value' ");
                            $read_mentor = mysqli_fetch_array($result_mentor);
                            $result_report = Query("select * from tbl_checklistreport where  tbl_checklistreport_listcode ='$code' and tbl_checklistreport_mentor='$value' and tbl_checklistreport_contract='$getcontract'");
                            echo '<tr>
                                    <td class="align-middle p-2">' . ($key + 1) . '</td>
                                    <td class="align-middle p-2">' . $read_mentor['tbl_user_name'] . '</td>
                                    <td class="align-middle p-2">' . (mysqli_num_rows($result_report) <= 0 ? $lang[1495]  : $lang[1494]) . '</td>
                                    <td class="align-middle p-2">
                                    ' . (mysqli_num_rows($result_report) > 0 && $access == '9' ? '<a href="checklistreport.php?checklist=' . $code . '&contract=' . $getcontract . '&mentor=' . $value . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-eye-fill"></span></a>' : '') . '
                                    </td>
                                    </tr>';
                        }
                    }

                    echo '</tbody></table>';
                }
                if (isset($do) && $do == 'showreport' && $access == '9') {

                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_checklistreport_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_checklistreport Where tbl_checklistreport_mentor =  '$_GET[mentor]' and tbl_checklistreport_listcode =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    // form for edit
                    echo '<div class="col-lg-12 col-12 p-3">';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                            <label>' . $lang[1216] . '<span class="language">' . $language[$i] . '</span></label>
                            <p>' . read_fild($language[$i], 'tbl_checklistreport_comment') . '</p>
                            </div><hr>';
                    }
                    echo '<div class="form-group">
                            <label>' . $lang[1499] . '</label>
                            <p>' . read_fild(Null, 'tbl_checklistreport_reportdate') . '</p>
                            </div><hr>';

                    echo '<div class="form-group">
                            <label>' . $lang[1946] . '</label>
                            <p>' . read_fild(Null, 'tbl_checklistreport_dateadd') . '</p>
                            </div><hr>';

                    $reportfile = read_fild(NULL, 'tbl_checklistreport_reportfile');
                    if (!empty($reportfile)) {
                        echo '<div class="form-group"><label>' . $lang[1153] . '</label>
                                  <a href="../upload/report/' . $reportfile . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                  </div><hr>';
                    } else {
                        echo '<div class="form-group"><label>' . $lang[1932] . '</label></div><hr>';
                    }


                    if (read_fild(null, 'tbl_checklistreport_status') == 0) {
                        echo '<form action="?do=reportupdateadmin&contract=' . $getcontract . '" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="code" value="' . $code . '" >
                        <input type="hidden" name="mentor" value="' . $_GET['mentor'] . '" >
                        <input type="hidden" name="contract" value="' . $getcontract . '" >
                        <div class="form-group">
                        <label>' . $lang[1933] . ' <input type="checkbox" value="1" name="status" ' . (read_fild(null, 'tbl_checklistreport_status') == 1 ? 'checked' : '') . '></label></div>
                        <div class="form-group">
                        <label>' . $lang[1941] . ' <input type="checkbox" value="1" name="karfarma" ' . (read_fild(null, 'tbl_checklistreport_karfarma') == 1 ? 'checked' : '') . '></label></div>
                        <div class="form-group">
                        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                        </div>
                        </form></div>';
                    } else {

                        echo '<form action="?do=reportupdateadmin&contract=' . $getcontract . '" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="code" value="' . $code . '" >
                        <input type="hidden" name="mentor" value="' . $_GET['mentor'] . '" >
                        <input type="hidden" name="contract" value="' . $getcontract . '" >
                        <div class="form-group">
                            <label>' . $lang[1934] . '</label>
                            </div><hr>
                        <div class="form-group">
                        <label>' . $lang[1941] . ' <input type="checkbox" value="1" name="karfarma" ' . (read_fild(null, 'tbl_checklistreport_karfarma') == 1 ? 'checked' : '') . '></label></div>
                        <div class="form-group">
                        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                        </div>
                        </form></div>';
                    }
                }

                if (isset($do) && $do == 'reportupdateadmin' && $access == '9') {

                    if (isset($_POST['updateitem'])) {

                        $code = $_POST['code'];
                        $contract = $_POST['contract'];
                        $mentor = $_POST['mentor'];
                        $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                        $karfarma = (!empty($_POST['karfarma']) ? $_POST['karfarma'] : '0');
                        $date = jdate("Y/m/d : a:h:s");
                        echo
                        Query("Update tbl_checklistreport SET 
                                tbl_checklistreport_status='$status',
                                tbl_checklistreport_karfarma='$karfarma',
                                tbl_checklistreport_confirmdate='$date'   
                                Where tbl_checklistreport_mentor='$mentor' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_listcode= '$code'  ");
                        header("Location: checklist.php?do=showmentor&code=$code&contract=$contract");
                        exit();
                    }
                }


                if (isset($do) && $do == 'report' && $access == '5') {

                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        global $userCode;
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_checklistreport_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_listcode =  '$code' and tbl_checklistreport_mentor= '$userCode'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = @$read[$field];
                        return $read;
                    }
                    // form for edit
                    echo '<div class="col-lg-12 col-12 p-3">';
                    echo '<form action="?do=reportupdate&contract=' . $getcontract . '" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';
                    echo '<input type="hidden" name="reportfile" value="' . @read_fild(NULL, 'tbl_checklistreport_reportfile') . '">';
                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                            <label>' . $lang[1216] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea type="text" name="comment_' . $language[$i] . '"  class="form-control" rows="6">' . read_fild($language[$i], 'tbl_checklistreport_comment') . '</textarea>
                            </div>';
                    }
                    echo '<div class="form-group">
                            <label>' . $lang[1499] . '</label>
                            <input type="text" name="reportdate"  class="form-control datepicker" value="' . @read_fild(Null, 'tbl_checklistreport_reportdate') . '">
                            </div>';

                    $reportfile = @read_fild(NULL, 'tbl_checklistreport_reportfile');
                    if (!empty($reportfile)) {
                        echo '<div class="form-group"><label class="col-sm-12 control-label">' . $lang[1153] . '</label><table><tr>
                                  <td><input type="file" name="attach" class="form-control"></td><td>
                                  <a href="../upload/report/' . $reportfile . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                  </td></tr></table></div>';
                    } else {
                        echo '<div class="form-group"><label class="col-sm-12 control-label">' . $lang[1153] . '</label><table ><tr>
                                  <td><input type="file" name="attach" class="form-control">
                                  </td></tr></table></div>';
                    }

                    echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form>';
                    echo '</div>';
                }

                if (isset($do) && $do == 'reportupdate' && $access == '5') {

                    if (isset($_POST['updateitem'])) {

                        $code = $_POST['code'];
                        $contract = $_POST['contract'];
                        $reportdate = $_POST['reportdate'];
                        $reportfile = $_POST['reportfile'];
                        $date = jdate("Y/m/d : a:h:s");

                        if ($_FILES['attach']['size'] == 0 && $_FILES['attach']['error'] == 0) {
                            $attachfinal = $reportfile;
                        } else {
                            if ($_FILES['attach']['size'] > 20000000) {
                                $attachfinal = '';
                            }
                            if (isset($_FILES['attach']['name'])) {
                                $attach = $_FILES['attach']['name'];
                                $read_file_name = explode(".", $attach);
                                $extn = end($read_file_name);
                                $attach = 'attach_' . time() . '.' . $extn;
                                if ($extn == 'xls' || $extn == 'xlsx' || $extn == 'DOCX' || $extn == 'docx' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attach']['size'] < 200000000) {
                                    move_uploaded_file($_FILES['attach']["tmp_name"], "../upload/report/" . $attach);
                                    $attachfinal = $attach;
                                } else {
                                    $_SESSION['error'] = $lang[1193];
                                    $attachfinal = $reportfile;
                                }
                            }
                        }



                        for ($i = 1; $i <= $language_count; $i++) {
                            $comment_[$i] = addslashes($_POST['comment_' . $language[$i]]);
                            $language_record = $language[$i];
                            $result = Query("select * from tbl_checklistreport where tbl_checklistreport_mentor ='$userCode' and tbl_checklistreport_listcode = '$code' and tbl_checklistreport_lng='$language_record'");
                            if (mysqli_num_rows($result) <= 0) {
                                Query("insert into tbl_checklistreport 
                                    (tbl_checklistreport_dateadd,tbl_checklistreport_reportdate,tbl_checklistreport_reportfile,tbl_checklistreport_comment,tbl_checklistreport_mentor,tbl_checklistreport_contract,tbl_checklistreport_status,tbl_checklistreport_listcode,tbl_checklistreport_lng)
                                    values('$date','$reportdate','$attachfinal','$comment_[$i]','$userCode','$contract','0','$code','$language_record') ");
                            } else {
                                Query("Update tbl_checklistreport SET 
                                tbl_checklistreport_dateadd='$date',
                                tbl_checklistreport_reportdate='$reportdate',
                                tbl_checklistreport_reportfile='$attachfinal',
                                tbl_checklistreport_comment='$comment_[$i]'
                                Where tbl_checklistreport_mentor ='$userCode' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_status='0' and tbl_checklistreport_listcode= '$code' and tbl_checklistreport_lng='$language_record' ");
                            }
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: checklist.php?contract=$contract");
                        exit();
                    }
                }
                if (isset($do) && $do == 'edit' && $access == '9') {

                    function read_fild($language_id = NULL, $field = NULL)
                    {
                        if ($language_id == NULL) {
                            $check_language_id = "";
                        } else {
                            $check_language_id = "and tbl_checklist_lng = '$language_id'";
                        }
                        $code = $_GET['code'];
                        $result = Query("SELECT * FROM tbl_checklist Where tbl_checklist_code =  '$code'  $check_language_id ");
                        $read = mysqli_fetch_array($result);
                        $read = $read[$field];
                        return $read;
                    }
                    // form for edit
                    echo '<div class="col-lg-8 col-12 p-3">
                            <form action="?do=edititem&contract=' . $getcontract . '" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';

                    echo '<div class="form-group">
                        <label>' . $lang[1939] . '</label><select name="question" style="width: 100%;" class="select2">
                            <option value="0">' . $lang[70] . '</option>';
                    $result_checklistdefault = Query("SELECT * From tbl_checklistdefault where tbl_checklistdefault_admin='$userCode' and tbl_checklistdefault_delete='0' and tbl_checklistdefault_lng='$language_default' order by tbl_checklistdefault_id DESC ");
                    while ($read_checklistdefault = mysqli_fetch_array($result_checklistdefault)) {
                        echo '<option ' . (read_fild(NULL, 'tbl_checklist_list') == $read_checklistdefault['tbl_checklistdefault_code'] ? 'selected' : '') . ' value="' . $read_checklistdefault['tbl_checklistdefault_code'] . '">' . $read_checklistdefault['tbl_checklistdefault_title'] . '</option>';
                    }
                    echo '</select>
                    </div>';

                    for ($i = 1; $i <= $language_count; $i++) {
                        echo '<div class="form-group">
                            <label>' . $lang[1944] . '<span class="language">' . $language[$i] . '</span></label>
                            <input type="text" name="title_' . $language[$i] . '" value="' . exjection(read_fild($language[$i], 'tbl_checklist_title')) . '" class="form-control">
                            </div>';
                    }
                    echo '<div class="form-group">
                        <label>' . $lang[2023] . '</label>
                        <input type="text" name="persent" class="form-control" value="' . read_fild(null, 'tbl_checklist_persent') . '" >
                        </div>';

                    echo '<div class="form-group">
                        <label>' . $lang[1459] . '</label>
                        <input type="text" name="expire" class="form-control datepicker" value="' . read_fild(null, 'tbl_checklist_expire') . '" >
                        </div>';
                    echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form></div>';
                }

                if (isset($do) && $do == 'edititem' && $access == '9') {
                    if (isset($_POST['updateitem'])) {
                        $code = $_POST['code'];
                        $contract = $_POST['contract'];
                        $question = $_POST['question'];
                        $expire = $_POST['expire'];
                        $persent = $_POST['persent'];
                        $date = jdate("Y/m/d : a:h:s");
                        for ($i = 1; $i <= $language_count; $i++) {
                            $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                            $language_record = $language[$i];

                            Query("Update tbl_checklist SET 
                                tbl_checklist_dateedit='$date',
                                tbl_checklist_title='$title_[$i]',
                                tbl_checklist_list='$question',
                                tbl_checklist_expire='$expire',
                                tbl_checklist_persent='$persent'    
                                Where tbl_checklist_contract= '$contract' and tbl_checklist_code= '$code' and tbl_checklist_lng='$language_record' ");
                        }
                        $_SESSION['error'] = $lang[21];
                        header("Location: checklist.php?contract=$contract");
                    }
                }

                // start = delete record
                if (isset($do) && $do == 'delete' && $access == '9') {
                    $result = Query("SELECT * from tbl_checklist Where tbl_checklist_code = '$code' ");
                    $read = mysqli_fetch_array($result);
                    echo '<div class="col-lg-8 col-12 p-2"><p>' . $lang[25] . '</p><h3>' . $read['tbl_checklist_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="hidden" value="' . $getcontract . '" name="contract" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    if (isset($_POST['submit'])) {
                        $code = $_POST['code'];
                        $contract = $_POST['contract'];
                        Query("update tbl_checklist set  tbl_checklist_delete='1' Where tbl_checklist_contract= '$contract' and tbl_checklist_code = '$code' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: checklist.php?contract=$contract");
                        exit();
                    }
                    // no delete
                    elseif (isset($_POST['nodelete'])) {
                        $contract = $_POST['contract'];
                        header("Location: checklist.php?contract=$contract");
                        exit();
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        $contract = $_POST['contract'];
                        header("Location: ?keyword=$keyword_search&contract=$contract");
                    }
                }
                // end = delete item

                ?>
            </div>
        </div>
    </div>
</div>
</div>
<?php
include './footer.inc.php';

?>