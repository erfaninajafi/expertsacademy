<?php

// include
include './includer.php';
$show_per_page = '1000';
$Tabpage = '90';
if (!isset($do)) {
    $pagetitle = $lang[1451];
} else if (isset($do) && $do == 'question') {
    $pagetitle = $lang[1589];
} else if (isset($do) && $do == 'answer') {
    $pagetitle = $lang[1608];
} else {
    $pagetitle = $lang[1451];
}
$thisaccess = '1,7';
include './header.inc.php';

$select = '';
if (!empty($_GET['user'])) {
    $select = " and tbl_member_id= '$_GET[user]'  ";
}

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
    $thiscontract = "Where tbl_member_delete='0' and tbl_member_lastcontract like '%[$_GET[contract]]%' ";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
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
    </div>';
} else {
    $thiscontract = " where tbl_member_delete='0'";
}

if (isset($do) && ($do == 'question' || $do == 'answer')) {

    $result = Query("SELECT * From tbl_member where tbl_member_id='$id'");
    $read = mysqli_fetch_array($result);
    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[1614] . '  : ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[385] . '  : ' . toPersianNum($read['tbl_member_nationalcode']) . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">' . $lang[378] . '  : ' . toPersianNum($read['tbl_member_mobile']) . '</h5>
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
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?= (!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
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
                    $result_for_count = Query("SELECT * From tbl_member  $thiscontract  and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%' ) $select ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination         
                    $result = Query("SELECT * From tbl_member $thiscontract and (tbl_member_lname like '%$keyword%' or tbl_member_nationalcode like '%$keyword%') $select ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                    // error : if not find record show no item for view
                    $row = mysqli_num_rows($result);
                    if (!isset($do) && $row == '0') {
                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                    }
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . ' </th>
                                    <th>' . $lang[127] . ' </th>
                                    <th>' . $lang[385] . '</th>
                                    <th>' . $lang[378] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $memberContract = $_GET['contract'];
                        //find contract bakhsh
                        $result_contract = Query("SELECT tbl_contract_codesection From tbl_contract where tbl_contract_id='$memberContract' ");
                        $read_contract = mysqli_fetch_array($result_contract);
                        $codeSection = $read_contract['tbl_contract_codesection'];
                        // find bakhsh info
                        $result_bakhsh = Query("SELECT tbl_categories_code From tbl_categories Where tbl_categories_catcode = '$codeSection' and tbl_categories_lng='$language_default'");
                        $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                        $resultSelectQuestion = Query("SELECT tbl_memberquestion_admincheck,tbl_memberquestion_data,tbl_memberquestion_check From tbl_memberquestion where tbl_memberquestion_userid='$read[tbl_member_id]' and tbl_memberquestion_contract='$_GET[contract]' ");
                        if (mysqli_num_rows($resultSelectQuestion) > 0) {
                            $readSelectQuestion = mysqli_fetch_array($resultSelectQuestion);
                            $arzyabi = json_decode($readSelectQuestion['tbl_memberquestion_data'], True);
                            $arzyabi = count($arzyabi);
                            $check = $readSelectQuestion['tbl_memberquestion_check'];
                            $adminCheck = $readSelectQuestion['tbl_memberquestion_admincheck'];
                        } else {
                            $arzyabi = 0;
                            $check = 0;
                            $adminCheck = 0;
                        }
                        echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">
                                <div class="notification-avatar">
                                <div class="avatar avatar-xl me-3">
                                    <img class="rounded-circle" src="' . (!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg') . '" alt="">
                                </div>
                                </div>
                                </td>
                                <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_nationalcode']) . '</td>
                                <td class="align-middle p-2">' . toPersianNum($read['tbl_member_mobile']) . '</td>
                                <td class="align-middle p-2">
                                <a href="?do=answer&id=' . $read['tbl_member_id'] . '&contract=' . $getcontract . '" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1766] . '" ><span class="bi-card-checklist"></span></a></td>
                                    </tr>';
                        $i++;
                    }
                    // end show status   
                    echo '</tbody></table>';
                    // start pagination                

                }

                if (isset($do) && $do == 'answer') {

                    $result = 0;
                    $allQuestion = 0;

                    $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$_GET[contract]' ");
                    $read_select = mysqli_fetch_array($resultSelectQuestion);
                    echo '<div class="p-3">';
                    if ($read_select['tbl_memberquestion_check'] == '0') {

                        echo '<div class="alert alert-warning m-3" role="alert">' . $lang[1603] . '</div>';
                    } else {
                        if (!empty($read_select['tbl_memberquestion_dataanswer'])) {
                            $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                            $k = 1;
                            if (!empty($read_select['tbl_memberquestion_dataanswer'])) {
                                //echo '<h4 class="mt-3">' . $lang[1440] . '</h4>';
                                foreach ($data as $keyexam => $valueexam) {
                                    if ($keyexam != 'submit' && $keyexam != 'code') {
                                        $questionId = $keyexam;
                                        $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                        if (mysqli_num_rows($result_question) > 0) {
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_question_answer'], true);
                                            echo '<div class="form-group exam"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_question_title'] . '</h2>';
                                            $allQuestion++;
                                            foreach ($all_value as $key => $value) {
                                                $answervalue = $value["answervalue"];
                                                //var_dump($answervalue);
                                                if ($answervalue == 1 && $valueexam == $key) {
                                                    $result++;
                                                }

                                                echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_question_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                                            }
                                            echo '</div><hr>';
                                            $k++;
                                        }
                                    }
                                }
                                if ($allQuestion != 0) {
                                    echo '<div class="resultexam">
                            <span>' . $lang[1438] . ' : ' . $allQuestion . '</span>
                            <span>' . $lang[1439] . ' : ' . $result . '</span>
                                    </div><br>';
                                }
                            }
                        }
                        if (!empty($read_select['tbl_memberquestion_dataanswertext'])) {
                            $k = 1;
                            if (!empty($read_select['tbl_memberquestion_dataanswertext'])) {
                                //echo '<h4 class="mt-3">' . $lang[1441] . '</h4>';

                                $data = json_decode($read_select['tbl_memberquestion_dataanswertext'], true);
                                foreach ($data as $keyexam => $valueexam) {
                                    if ($keyexam != 'submit' && $keyexam != 'code') {
                                        $questionId = $keyexam;
                                        $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                        if (mysqli_num_rows($result_question) > 0) {
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_question_answer'], true);
                                            echo '<div class="form-group exam"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_question_title'] . '</h2><p>' . $valueexam . '</p>';
                                            echo '</div><hr>';
                                            $k++;
                                        }
                                    }
                                }
                            }
                        }

                        // $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                        // var_dump($data);
                        // $k = 1;
                        // foreach ($data as $keyexam => $valueexam) {

                        //     if ($keyexam != 'submit' && $keyexam != 'code') {
                        //         $questionId = $keyexam;
                        //         $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");

                        //         $read_question = mysqli_fetch_array($result_question);
                        //         $all_value = json_decode($read_question['tbl_question_answer'], true);
                        //         echo '<div class="form-group exam"><h2>' . toPersianNum($k) . ' . ' . $read_question['tbl_question_title'] . '</h2>';
                        //         $allQuestion++;
                        //         foreach ($all_value as $key => $value) {
                        //             $answervalue = $value["answervalue"];
                        //             //var_dump($answervalue);
                        //             if ($answervalue == 1 && $valueexam == $key) {
                        //                 $result++;
                        //             }

                        //             echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                        //                     <input  id="radio' . $read_question['tbl_question_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                        //                           <label for="radio' . $read_question['tbl_question_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                        //         }
                        //         echo '</div><hr>';
                        //         $k++;
                        //     }
                        // }
                        // echo '<div class="resultexam">
                        //     <span>' . $lang[1604] . ' : ' . $allQuestion . '</span>
                        //     <span>' . $lang[1605] . ' : ' . $result . '</span>
                        //             </div>';
                    }
                    echo '</div>';
                }


                ?>
            </div>
        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>