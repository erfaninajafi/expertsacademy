<?php

// include
include './includer.php';
$show_per_page = '1000';
$Tabpage = '90';
if(!isset($do)) {
$pagetitle = $lang[1451];
}
if(isset($do) && $do=='answer') {
    $pagetitle = $lang[1608];
}
else {
    header("Location: index.php");
    exit(); 
}
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


if (isset($do) && ($do == 'question' || $do == 'answer' )) {

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
                    <h5 class="fs-0">'.$lang[1614].'  : ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[385].'  : ' . toPersianNum($read['tbl_member_nationalcode']) . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[378].'  : ' . toPersianNum($read['tbl_member_mobile']) . '</h5>
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
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?=(!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">
                    <?php
                    

                    if (isset($do) && $do == 'answer') {

                        $result = 0;
                        $allQuestion = 0;
                        $resultSelectQuestion = Query("SELECT * From tbl_memberquestion where tbl_memberquestion_userid='$id' and tbl_memberquestion_contract='$_GET[contract]' ");
                        $read_select = mysqli_fetch_array($resultSelectQuestion);
                        echo '<div class="p-3">';
                        if ($read_select['tbl_memberquestion_check'] == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">'.$lang[1603].'</div>';
                        } else {
                            $data = json_decode($read_select['tbl_memberquestion_dataanswer'], true);
                            $k=1;
                            foreach ($data as $keyexam => $valueexam) {
                                if ($keyexam != 'submit' && $keyexam != 'code') {
                                    $questionId = $keyexam;
                                    $result_question = Query("select * from tbl_question Where tbl_question_disable='0' and tbl_question_lng='$language_default' and tbl_question_code = '$questionId' ");
                                    $read_question = mysqli_fetch_array($result_question);
                                    $all_value = json_decode($read_question['tbl_question_answer'], true);
                                    echo '<div class="form-group exam"><h2>'.toPersianNum($k) .' . ' . $read_question['tbl_question_title'] . '</h2>';
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
                            echo '<div class="resultexam">
                            <span>'.$lang[1604].' : ' . $allQuestion . '</span>
                            <span>'.$lang[1605].' : ' . $result . '</span>
                                    </div>';
                            
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