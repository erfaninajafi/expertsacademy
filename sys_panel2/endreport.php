<?php

// include
include './includer.php';
$show_per_page = '30';
$Tabpage = '90';
$pagetitle = $lang[1947];
$thisaccess = '9,7';
include './header.inc.php';



    $thiscontract = "Where tbl_attach_contract = '$getcontract'";
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$getcontract' ");
    $read_contract = mysqli_fetch_array($result_contract);
    $thisProject = $read_contract['tbl_contract_project'];
    $result_p = Query("select tbl_user_name from tbl_user where tbl_user_usercode !='' and tbl_user_usercode = '$thisProject'  ");
    $read_p = mysqli_fetch_array($result_p);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">'.$lang[1440].'  : ' . $read_contract['tbl_contract_code'] . '</h5>
              <h5 class="fs-0">'.$lang[1567].'  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">'.$lang[1611].'  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">'.$lang[1612].'  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1613].'  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">'.$lang[1566].'  : ' . $read_p['tbl_user_name'] . '</h5>
            <h5 class="fs-0">'.$lang[1571].'  : ' . toPersianNum($read_contract['tbl_contract_startdate']) . '</h5>
            <h5 class="fs-0">'.$lang[1572].'  : ' . toPersianNum($read_contract['tbl_contract_enddate']) . '</h5>
            </div>

            <div class="flex-1 fs--1">
            <h5 class="fs-0">'.$lang[1582].'  : <a href="contract.php">' . $read_contract['tbl_contract_code'] . '</a></h5>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
    </div>
    </div>';
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
                    if (!isset($do)) { 
                        $result = Query("SELECT * from tbl_contract Where tbl_contract_id = '$getcontract' ");
                        $read = mysqli_fetch_array($result);
                        if($access =='7')
                        {
                            echo '<div class="col-lg-12 col-12 p-3">';
                            echo '<form action="?do=reportkarfarma" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';

                            echo '<div class="form-group">
                            <label>' . $lang[1216] . '</label>
                            <p>' . $read['tbl_contract_reporttext'] . '</p>
                            </div>';

                        echo '<div class="form-group">
                            <label>' . $lang[1499] . '</label>
                            <p>' . $read['tbl_contract_reportdate'] . '</p>
                            </div>';
                            
                        $reportfile = @$read['tbl_contract_reportattach'];
                        if (!empty($reportfile)) {
                            echo '<div class="form-group"><label class="col-sm-12 control-label">' . $lang[1153] . '</label><table><tr>
                                <td>
                                <a href="../upload/report/' . $reportfile . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                </td></tr></table></div>';
                        } 

                        echo '<div class="form-group">
                            <label>' . $lang[1956] . '</label>
                            '.($read['tbl_contract_reportconfirmdate'] !='' ? '<p>'.$read['tbl_contract_reportconfirmdate'].'</p>' : '<input type="text" name="reportconfirmdate"  class="form-control datepicker" value="' . $read['tbl_contract_reportconfirmdate'] . '">' ).'
                            </div>';
                            
                        echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form>';
                        echo '</div>';
                        }
                        else {
                            echo '<div class="col-lg-12 col-12 p-3">';
                            echo '<form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';

                            echo '<div class="form-group">
                            <label>' . $lang[1216] . '</label>
                            <textarea type="text" name="comment"  class="form-control" rows="6">' . $read['tbl_contract_reporttext'] . '</textarea>
                            </div>';

                        echo '<div class="form-group">
                            <label>' . $lang[1499] . '</label>
                            <input type="text" name="reportdate"  class="form-control datepicker" value="' . $read['tbl_contract_reportdate'] . '">
                            </div>';
                            if($read['tbl_contract_reportconfirmdate'] !=''){
                                echo '<hr><div class="form-group">
                                    <label>' . $lang[1957] . '</label>
                                    <p>'.$read['tbl_contract_reportconfirmdate'].'</p>
                                    </div><hr>';
                                }
                        $reportfile = @$read['tbl_contract_reportattach'];
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
                        
}
if (isset($do) && $do == 'reportkarfarma' && $access =='7') {
    if (isset($_POST['updateitem'])) {
        $contract = $_POST['contract'];
        $reportconfirmdate = $_POST['reportconfirmdate'];

            Query("Update tbl_contract SET 
            tbl_contract_reportconfirmdate='$reportconfirmdate'
            Where  tbl_contract_id= '$contract'  ");

            $_SESSION['error'] = $lang[21];
            header("Location: endreport.php?contract=$contract");
            exit();
        }

   

    }

if (isset($do) && $do == 'reportupdate' && $access =='9') {

    if (isset($_POST['updateitem'])) {

        $contract = $_POST['contract'];
        $comment = $_POST['comment'];
        $reportdate = $_POST['reportdate'];
        $date = jdate("Y/m/d : a:h:s");

            if ($_FILES['attach']['size'] == 0 && $_FILES['attach']['error'] == 0) {
                $attachfinal = 'empty';
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

            if($attachfinal == 'empty')
            {
                Query("Update tbl_contract SET 
                tbl_contract_reporttext='$comment',
                tbl_contract_reportdate='$reportdate',
                tbl_contract_reportupdate='$date'
                Where  tbl_contract_project= '$userCode' and tbl_contract_id= '$contract'  ");
            }
            else {
                Query("Update tbl_contract SET 
                tbl_contract_reporttext='$comment',
                tbl_contract_reportattach='$attachfinal',
                tbl_contract_reportdate='$reportdate',
                tbl_contract_reportupdate='$date'
                Where  tbl_contract_project= '$userCode' and tbl_contract_id= '$contract'  ");
            }

        $_SESSION['error'] = $lang[21];
        header("Location: endreport.php?contract=$contract");
        exit();
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