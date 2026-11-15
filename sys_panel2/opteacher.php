<?php
// include
include './includer.php';
$show_per_page = '20';

    $pagetitle = $lang[1816];

$Tabpage = '91';
$img_item = 10;
$thisaccess = '1,5';
include './header.inc.php';
if (!empty($_GET['orderby'])) {
    if ($_GET['orderby'] == 'order') {
        $orderby = ' ORDER BY tbl_courseparent_order ASC ';
    }
    if ($_GET['orderby'] == 'title') {
        $orderby = ' ORDER BY tbl_courseparent_title ASC ';
    }
} else {
    $orderby = ' ORDER BY tbl_courseparent_order ASC ';
}


 if (!empty($_GET['class']))
{
    $result_class = Query("Select tbl_class_title from tbl_class where tbl_class_code = '$_GET[class]' and tbl_class_lng='$language_default'");
    $read_class = mysqli_fetch_array($result_class);


    $resultMember = Query("Select tbl_member_fname,tbl_member_lname from tbl_member where tbl_member_id = '$_GET[user]' ");
    $readMember = mysqli_fetch_array($resultMember);

    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[710].'  : ' . $read_class['tbl_class_title'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[1780].'  : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</h5>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>                    
    </div>
    </div>';    
}

if (!empty($do) && !empty($_GET['user']))
{
    $result_class = Query("Select tbl_courseparent_title from tbl_courseparent where tbl_courseparent_code = '$_GET[code]' and tbl_courseparent_lng='$language_default'");
    $read_class = mysqli_fetch_array($result_class);


    $resultMember = Query("Select tbl_member_fname,tbl_member_lname from tbl_member where tbl_member_id = '$_GET[user]' ");
    $readMember = mysqli_fetch_array($resultMember);

    echo '<div class="col-md-12 mb-3">
    <div class="box box-body ">
        <div class="card mb-3">
            <div class="card-body custom-h5">
            <div class="row justify-content-between align-items-center">
                <div class="col">
                <div class="d-flex">
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[1520].'  : ' . $read_class['tbl_courseparent_title'] . '</h5>
                    </div>
                    <div class="flex-1 fs--1">
                    <h5 class="fs-0">'.$lang[1780].'  : ' . $readMember['tbl_member_fname'] . ' ' . $readMember['tbl_member_lname'] . '</h5>
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
            <div class="card-body p-0 position-relative">
                  <?php
                    $result_exam = Query("select * from tbl_opinionend where tbl_opinionend_userid='$_GET[user]' and tbl_opinionend_class='$_GET[class]' and tbl_opinionend_contract='$_GET[contract]' and tbl_opinionend_model='3' and tbl_opinionend_delete='0' ");
                    if(mysqli_num_rows($result_exam) <= 0)
                    {
                        echo '<div class="alert alert-danger" role="alert">'.$lang[1817].'</div>';
                    }
                    else {
                        echo ' <div class="p-3">';
                        $result = 0;
                           $allQuestion = 0;
                                
                                    $read_select = mysqli_fetch_array($result_exam);
                                    $data = json_decode($read_select['tbl_opinionend_data'], true);
                                    
                                    $k=1;
                                    foreach ($data as $keyexam => $valueexam) {
                                        if ($keyexam != 'submit' && $keyexam != 'code') {
                                            $finalId = explode("-",$keyexam);
                                            $questionId = $finalId[1];
                                            $result_question = Query("select * from tbl_questionpublic Where tbl_questionpublic_disable='0' and tbl_questionpublic_lng='$language_default' and tbl_questionpublic_code = '$questionId' ");
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_questionpublic_answer'], true);
                                            echo '<div class="form-group exam p-3"><h2>'.toPersianNum($k) .' . ' . $read_question['tbl_questionpublic_title'] . '</h2>';
                                            $allQuestion++;
                                            foreach ($all_value as $key => $value) {
                                                $answervalue = $value["answervalue"];
                                                if ($answervalue == 1 && $valueexam == $key) {
                                                    $result++;
                                                }
                                                echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                                    <input  id="radio' . $read_question['tbl_questionpublic_id'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                          <label for="radio' . $read_question['tbl_questionpublic_id'] . $key . '"></span>' . $value["answerfa"] . '</label></div>';
                                            }
                                            echo '</div><hr>';
                                            $k++;
                                        }
                                    }
                                        echo '<div class="resultexam m-3">
                                        <span>'.$lang[1604].' : ' . $allQuestion . '</span>
                                        <span>'.$lang[1605].' : ' . $result . '</span>
                                                </div><br>';
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