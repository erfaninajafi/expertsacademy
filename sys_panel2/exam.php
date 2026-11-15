<?php
// include
include './includer.php';
$show_per_page = '20';
if(empty($do))
{
    $pagetitle = $lang[1685];
}
else {
    $pagetitle = $lang[1367];
}
$Tabpage = '91';
$img_item = 10;
$thisaccess = '1,5,7';
include './header.inc.php';
checkValid($getcontract, $access, $this_user_id);
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
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
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
                    
                        if (!empty($_GET['class'])) {
                            $thiscalss = " tbl_courseparent_class = '$_GET[class]' and ";
                        } else {
                            header("Location: index.php");
                            exit();
                        }
                        
                        $result_for_count = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !=''  and tbl_courseparent_lng='$language_default' ");
                        // start count db result for pagination               
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        $result = Query("SELECT * From tbl_courseparent where $thiscalss tbl_courseparent_id !=''  and tbl_courseparent_lng='$language_default' $orderby  LIMIT $start_contnet,$show_per_page ");
                        $row = mysqli_num_rows($result);
                        // 0 result search error 
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        $allfield = '';
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                    <tr>
                                        <th>' . $lang[60] . '</th>
                                        <th>'.$lang[1680].'</th>
                                        <th>'.$lang[1681].' </th>
                                        <th>' . $lang[63] . '</th>
                                    </tr>
                                </thead>
                                <tbody>';
                        $i = 1;  // for record number
                        while ($read = mysqli_fetch_array($result)) {
                            $admin = $read['tbl_courseparent_admin'];
                            $result_cat = Query("SELECT * FROM tbl_exam Where tbl_exam_user='$_GET[user]' and tbl_exam_course =  '$read[tbl_courseparent_code]' and tbl_exam_contract='$_GET[contract]'");
                            // parent menu code
                            echo '<tr><td class="align-middle p-2">' . $i . '</td>
                                 <td class="align-middle p-2">' . $read['tbl_courseparent_classcode'] . '</td>
                                <td class="align-middle p-2"><span style="' . (mysqli_num_rows($result_cat) <=0 ? 'color:red;' : 'color:green;') . '">' . $read['tbl_courseparent_title'] . '' . ($read['tbl_courseparent_free'] == 1 ? $lang[1364] : '') . '</span></td>
                                <td class="align-middle p-2">
                                <a href="?do=view&code=' . $read['tbl_courseparent_code'] . (!empty($_GET['contract']) ? '&contract=' . $_GET['contract'] : '' ).(!empty($_GET['user']) ? '&user=' . $_GET['user'] : '' ).'" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="'.(mysqli_num_rows($result_cat) > 0 ? $lang[1492] : $lang[1493]).'"><span class="bi-list-ol"></span></a></td>'
                                . '</tr>';
                            $i++;
                        }
                        echo '</table>';

                    }

                    if (isset($do) && $do == 'view') {
                        echo ' <div class="p-3">';
                        $result = 0;
                           $allQuestion = 0;
                                $result_exam = Query("select * from tbl_exam Where tbl_exam_user = '$_GET[user]'  and tbl_exam_course ='$code' and tbl_exam_contract='$_GET[contract]' ");
                                if (mysqli_num_rows($result_exam) == 0) {
                                    echo '<div class="alert alert-danger" role="alert">'.$lang[1813].'</div>';
                                } else {
                                    $read_exam = mysqli_fetch_array($result_exam);
                                    $course = $read_exam['tbl_exam_course'];
                                    $data = json_decode($read_exam['tbl_exam_data'], true);
                                    foreach ($data as $keyexam => $valueexam) {
                                        if ($keyexam != 'submit' && $keyexam != 'code') {
                                            $allQuestion++;
                                            $question = explode("-", $keyexam);
                                            $questionId = $question[1];
                                            $result_question = Query("select * from tbl_question Where tbl_question_lng='$language_default' and tbl_question_disable='0' and tbl_question_code = '$questionId' and tbl_question_parent = '$course' ");
                                            $read_question = mysqli_fetch_array($result_question);
                                            $all_value = json_decode($read_question['tbl_question_answer']);
                                            echo '<div class="form-group exam"><h2>' . $read_question['tbl_question_title'] . '</h2>';
                                            foreach ($all_value as $key => $value) {
                                                $answervalue = $value->answervalue;
                                                if ($answervalue == 1 && $valueexam == $key) {
                                                    $result++;
                                                }
                                                echo '<div class="exam-item"><div class="translate ' . ($answervalue == 1 ? 'correct' : '') . ' ' . ($valueexam == $key ? 'notcorrect' : '') . '">' . ($key + 1) . '</div>
                                            <input  id="radio' . $read_question['tbl_question_code'] . $key . '" type="radio" ' . ($valueexam == $key ? 'checked' : '') . ' disabled>
                                                  <label for="radio' . $read_question['tbl_question_code'] . $key . '"><span></span>' . $value->answerfa . '</label></div>';
                                            }
                                            echo '</div>';
                                        }
                                    }

                                    echo '<div class="resultexam">
                                        <span>'.$lang[1378].' : ' . $allQuestion . '</span>
                                        <span>'.$lang[1379].' : ' . $result . '</span>
                                                </div>';
                                }
                                echo '</div>';
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