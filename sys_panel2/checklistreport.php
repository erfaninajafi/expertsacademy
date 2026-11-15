<?php
include './includer.php';
$show_per_page = '130';
$pagetitle = $lang[1927];
$Tabpage = '25';
$thisaccess = '1,9,5';
include './header.inc.php';
$img_item = '4';
$checklistQuerry='';

    if($access =='9') {
        $checklistQuerry = " tbl_checklistreport_contract ='$getcontract' and tbl_checklistreport_mentor='$getmentor' and tbl_checklistreport_listcode='$getchecklist' and ";
    }
    else {
        $checklistQuerry = " tbl_checklistreport_contract ='$getcontract' and  tbl_checklistreport_mentor='$userCode' and tbl_checklistreport_listcode='$getchecklist' and ";
    }

    if(!empty($getchecklist) )
{
    $result = Query("SELECT * From tbl_checklist where  tbl_checklist_code='$getchecklist' and tbl_checklist_delete='0' and tbl_checklist_lng='$language_default'   ");
    $read = mysqli_fetch_array($result);
    if($read['tbl_checklist_list'] !='0')
    {
        $result_checklistdefault = Query("SELECT tbl_checklistdefault_title From tbl_checklistdefault where tbl_checklistdefault_code='$read[tbl_checklist_list]' and tbl_checklistdefault_lng='$language_default'  ");
        $read_checklistdefault = mysqli_fetch_array($result_checklistdefault);
        $pagetitle .= '<hr><br> '.$read_checklistdefault['tbl_checklistdefault_title'];
    }
    else {
        $pagetitle .= '<hr><br> '.$read['tbl_checklist_title']; 
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
                            <h5 class="mb-0"><?= $pagetitle?></h5>
                            <?=(!isset($do) && $access =='5' ? '<div class="plus">
                                <a href="?do=add'.(!empty($getchecklist) ? '&checklist='.$getchecklist : '' ).(!empty($getcontract) ? '&contract='.$getcontract : '' ).'"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>
                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <div class="card-body p-0 position-relative">
                <?php
                    if (!isset($do)) { ?>
                <?php
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_checklistreport where $checklistQuerry  tbl_checklistreport_lng='$language_default' and tbl_checklistreport_delete='0'   ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination 
                                      
                        $result = Query("SELECT * From tbl_checklistreport where $checklistQuerry tbl_checklistreport_lng='$language_default' and tbl_checklistreport_delete='0'  order by tbl_checklistreport_id DESC LIMIT $start_contnet,$show_per_page ");
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
                                    <th>'.$lang[61].' </th>
                                    <th>'.$lang[865].' </th>
                                    <th>' . $lang[1340] . '</th>
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
                        <td class="align-middle p-2">' . ($read['tbl_checklistreport_status'] =='0' ? $lang[1556] : $lang[1557]) . '</td>
                        <td class="align-middle p-2">
                        '.($access =='9' || ($access =='5' && $read['tbl_checklistreport_status'] !='0') ? '<a href="checklistreport.php?do=view&code=' . $read['tbl_checklistreport_code'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-eye-fill"></span></a>' : '' ).'

                        '.($access =='5' && $read['tbl_checklistreport_status'] =='0' ? '<a href="checklistreport.php?do=edit&code=' . $read['tbl_checklistreport_code'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-pencil-square"></span></a>' : '' ).'
                        '.($access =='5' && $read['tbl_checklistreport_status'] =='0' ? '<a href="checklistreport.php?do=delete&code=' . $read['tbl_checklistreport_code'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1931] . '"><span class="bi-trash-fill"></span></a>' : '' ).'
                       </td></tr>';
                            $i++;
                        }

                        echo '</tbody></table>';
                    }
                  
              
                    if (isset($do) && $do == 'view' && ($access =='9' || $access =='5') ) {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_checklistreport_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_checklistreport Where tbl_checklistreport_code =  '$code'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = $read[$field];
                            return $read;
                        }
                        // form for edit
                        echo '<div class="col-lg-12 col-12 p-3">';

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[61] . '<span class="language">' . $language[$i] . '</span></label>
                            <p>' . read_fild($language[$i], 'tbl_checklistreport_title') . '</p>
                            </div><hr>';
                        }

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
                            
                        $reportfile = explode(",",read_fild(NULL, 'tbl_checklistreport_reportfile'));
                        foreach ($reportfile as $key => $value) {
                            if (!empty($value)) {
                                echo '<div class="form-group"><label>' . $lang[1153] . '</label>
                                      <a href="../upload/report/' . $value . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                      </div><hr>';
                            }
                        }
                        
                        if($access == '9') {

                        echo '<form action="?do=reportupdateadmin" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="code" value="' . $code . '" >
                        <input type="hidden" name="mentor" value="' . read_fild(Null, 'tbl_checklistreport_mentor') . '" >
                        <input type="hidden" name="checklist" value="' . read_fild(Null, 'tbl_checklistreport_listcode') . '" >
                        <input type="hidden" name="contract" value="' . read_fild(Null, 'tbl_checklistreport_contract') . '" >';
                        if(read_fild(null, 'tbl_checklistreport_status') == 0) {
                        echo '<div class="form-group">
                        <label>'.$lang[1933].' <input type="checkbox" value="1" name="status" '.(read_fild(null, 'tbl_checklistreport_status') == 1 ? 'checked' : '').'></label></div>';
                        }
                        echo '<div class="form-group">
                        <label>'.$lang[1941].' <input type="checkbox" value="1" name="karfarma" '.(read_fild(null, 'tbl_checklistreport_karfarma') == 1 ? 'checked' : '').'></label></div>
                        <div class="form-group">
                        <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                        </div>
                        </form>';
                    }
                    echo '</div>';
                      
                    }

                    if (isset($do) && $do == 'reportupdateadmin' && $access =='9') {

                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $contract = $_POST['contract'];
                            $mentor = $_POST['mentor'];
                            $checklist = $_POST['checklist'];
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $karfarma = (!empty($_POST['karfarma']) ? $_POST['karfarma'] : '0');
                            $date = jdate("Y/m/d : a:h:s");

                            $result = Query("SELECT * FROM tbl_checklistreport Where tbl_checklistreport_status ='1' and tbl_checklistreport_listcode='$checklist' and tbl_checklistreport_mentor='$mentor' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_code= '$code' ");
                            if(mysqli_num_rows($result) > 0)
                            {
                                Query("Update tbl_checklistreport SET 
                                tbl_checklistreport_karfarma='$karfarma',
                                tbl_checklistreport_confirmdate='$date'   
                                Where tbl_checklistreport_listcode='$checklist' and tbl_checklistreport_mentor='$mentor' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_code= '$code'  ");
                            }
                            else {
                                Query("Update tbl_checklistreport SET 
                                tbl_checklistreport_status='$status',
                                tbl_checklistreport_karfarma='$karfarma',
                                tbl_checklistreport_confirmdate='$date'   
                                Where tbl_checklistreport_listcode='$checklist' and tbl_checklistreport_mentor='$mentor' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_code= '$code'  ");
                            }

                           

                            header("Location: checklistreport.php?checklist=$checklist&contract=$contract&mentor=$mentor");

                            exit();
                    }

                    }

                    if (isset($do) && $do == 'edit' && $access =='5') {

                        function read_fild($language_id = NULL, $field = NULL)
                        {
                            global $userCode;
                            if ($language_id == NULL) {
                                $check_language_id = "";
                            } else {
                                $check_language_id = "and tbl_checklistreport_lng = '$language_id'";
                            }
                            $code = $_GET['code'];
                            $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_code =  '$code' and tbl_checklistreport_mentor= '$userCode'  $check_language_id ");
                            $read = mysqli_fetch_array($result);
                            $read = @$read[$field];
                            return $read;
                        }
                        // form for edit
                        echo '<div class="col-lg-12 col-12 p-3">';
                            echo '<form action="?do=reportupdate" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >';
                            echo '<input type="hidden" name="reportfile" value="' . @read_fild(NULL, 'tbl_checklistreport_reportfile') . '">';


                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                <label>' . $lang[61] . '<span class="language">' . $language[$i] . '</span></label>
                                <input type="text" name="title_' . $language[$i] . '"  class="form-control"  value="' . read_fild($language[$i], 'tbl_checklistreport_title') . '">
                                </div>';
                            }


                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group">
                            <label>' . $lang[1216] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea type="text" name="comment_' . $language[$i] . '"  class="form-control mb-3" rows="6">' . read_fild($language[$i], 'tbl_checklistreport_comment') . '</textarea>
                            </div>';
                        }
                        // echo '<div class="form-group">
                        //     <label>' . $lang[1499] . '</label>
                        //     <input type="text" name="reportdate"  class="form-control datepicker mb-3" value="' . @read_fild(Null, 'tbl_checklistreport_reportdate') . '">
                        //     </div>';
                           
                            
                            echo '<input type="hidden" name="allimages" value="' . read_fild(NULL, 'tbl_checklistreport_reportfile') . '">';
                            for ($i = 1; $i <= $img_item; $i++) {
    
                                $exp = explode(",", read_fild(NULL, 'tbl_checklistreport_reportfile'));
                                if ($exp[$i - 1]) {
                                    echo '<div class="form-group"><table><tr>
                                          <td><label class="col-sm-12 control-label">' . $lang[1153] . '&nbsp; ' . $i . '</label></td>
                                          <td><input type="file" name="img_' . $i . '" class="form-control"></td><td>
                                          <a href="../upload/report/' . $exp[$i - 1] . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                          <a href="?code=' . $code . '&do=deleteimage&img=' . $exp[$i - 1] . '" ><span class="btn me-1 mb-1 btn-sm btn-danger bi-trash-fill"></span></a>
                                          </td></tr></table></div>';
                                } else {
                                    echo '<div class="form-group"><table ><tr>
                                          <td><label class="col-sm-12 control-label">' . $lang[1153] . '&nbsp; ' . $i . '</label></td>
                                          <td><input type="file" name="img_' . $i . '" class="form-control">
                                          </td></tr></table></div>';
                                }
                            }



                        echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form>';
                        echo '</div>';
                    }



                    if (isset($do) && $do == 'reportupdate' && $access =='5') {

                        if (isset($_POST['updateitem'])) {

                            $code = $_POST['code'];

                            $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_status='0' and tbl_checklistreport_code =  '$code'  ");
                            if(mysqli_num_rows($result) > 0) {
                            $read = mysqli_fetch_array($result);
                            $listcode = $read['tbl_checklistreport_listcode'];
                            $contract = $read['tbl_checklistreport_contract'];
                            $reportdate = $_POST['reportdate'];
                            $reportfile = $_POST['reportfile'];
                            $date = jdate("Y/m/d : a:h:s");

                            $allimages = @$_POST['allimages'];
                            $read_allimages = explode(",", $allimages);

                        $images_name = '';
                        if (!$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                if ($img_[$i_image] == $read_allimages[$i_image - 1]) {
                                    $images_name .= $read_allimages[$i_image - 1] . ',';
                                } else {
                                    $read_file_name = explode(".", $img_[$i_image]);
                                    $extn = end($read_file_name);
                                    $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;
                                    if ($extn == 'xls' || $extn == 'xlsx' || $extn == 'DOCX' || $extn == 'docx' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attach']['size'] < 200000000) {
                                        $images_name .= $img_[$i_image] . ',';
                                        move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/report/" . $img_[$i_image]);
                                        unlink('../upload/report/' . $read_allimages[$i_image - 1]);
                                    } else {
                                        $images_name .= $read_allimages[$i_image - 1] . ',';
                                    }
                                }
                            }
                        }



                            
                            for ($i = 1; $i <= $language_count; $i++) {
                                $comment_[$i] = addslashes($_POST['comment_' . $language[$i]]);
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_record = $language[$i];
                              
                                Query("Update tbl_checklistreport SET 
                                tbl_checklistreport_dateadd='$date',
                                tbl_checklistreport_reportdate='$reportdate',
                                tbl_checklistreport_reportfile='$images_name',
                                tbl_checklistreport_comment='$comment_[$i]',
                                tbl_checklistreport_title='$title_[$i]'
                                Where tbl_checklistreport_mentor ='$userCode' and tbl_checklistreport_contract= '$contract' and tbl_checklistreport_delete='0' and tbl_checklistreport_status='0' and tbl_checklistreport_code= '$code' and tbl_checklistreport_lng='$language_record' ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: checklistreport.php?checklist=$listcode&contract=$contract");
                            exit();
                        }
                    }

                    }



                    // if (isset($do) && $do == 'delete' && $access =='5') {
                    //     $result = Query("SELECT * from tbl_checklistreport Where tbl_checklistreport_code = '$code' and tbl_checklistreport_lng='$language_default' ");
                    //     $read = mysqli_fetch_array($result);
                    //     echo '<div class="col-lg-8 col-12 p-2"><p>' . $lang[25] . '</p><h3>' . $read['tbl_checklistreport_title'] . '</h3>
                    //      <form action="" method="POST">
                    //      <div class="form-group margin-top">
                    //      <input type="hidden" value="' . $code . '" name="code" >
                    //      <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                    //      <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                    //     if (isset($_POST['submit'])) {
                    //         $code = $_POST['code'];
                    //         $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_status='0' and tbl_checklistreport_code =  '$code'  ");
                    //         if(mysqli_num_rows($result) > 0) {
                    //         $read = mysqli_fetch_array($result);
                    //         $listcode = $read['tbl_checklistreport_listcode'];
                    //         $contract = $read['tbl_checklistreport_contract'];
                    //         Query("update tbl_checklistreport set  tbl_checklistreport_delete='1' Where tbl_checklistreport_code= '$code' ");
                    //         $_SESSION['error'] = $lang[28];
                    //         header("Location: checklistreport.php?checklist=$listcode&contract=$contract");
                    //         exit();
                    //         }
                    //     }
                    //     // no delete
                    //     elseif (isset($_POST['nodelete'])) {
                    //         $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_status='0' and tbl_checklistreport_code =  '$code'  ");
                    //         if(mysqli_num_rows($result) > 0) {
                    //         $read = mysqli_fetch_array($result);
                    //         $listcode = $read['tbl_checklistreport_listcode'];
                    //         $contract = $read['tbl_checklistreport_contract'];
                    //         $contract = $_POST['contract'];
                    //         header("Location: checklistreport.php?checklist=$listcode&contract=$contract");
                    //         exit();
                    //         }
                    //     }
                    // }


                    if ( isset($do) && $do == 'deleteimage' && $access =='5') {
                        
                        echo '<div class="col-lg-8 col-12 p-3"><h3>' . $lang[25] . '</h3><img style="width:35%; height:35%;" src="../upload/report/' .$img . '" ><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" name="img" value="' . $img . '" >
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '"  class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                        if (isset($_POST['submit'])) {
                            $result = Query("SELECT * from tbl_checklistreport Where tbl_checklistreport_delete='0' and tbl_checklistreport_code = '$code' ");
                            if(mysqli_num_rows($result) > 0){
                            $read = mysqli_fetch_array($result);
                            $img = $_POST['img'];
                            $code = $_POST['code'];
                            $record = str_replace("$img", "", $read['tbl_checklistreport_reportfile']);
                            $external_file = '../upload/report/' . $img;
                            unlink("$external_file");
                            Query("Update tbl_checklistreport SET tbl_checklistreport_reportfile = '$record' Where tbl_checklistreport_delete='0' and tbl_checklistreport_code = '$code' ");
                            header("Location: checklistreport.php?do=edit&code=$code");
                        } 
                    }
                    elseif (isset($_POST['nodelete'])) {
                        $code = $_POST['code'];
                        header("Location: checklistreport.php?do=edit&code=$code");
                        exit();
                    }
                    }


                    if (isset($do) && $do == 'add' && $access =='5') {
                        echo '<div class="col-lg-12 col-12 p-3">';
                            echo '<form action="?do=addreport" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="checklist" value="' . $getchecklist . '" >
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';

                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                                <label>' . $lang[61] . '<span class="language">' . $language[$i] . '</span></label>
                                <input type="text" name="title_' . $language[$i] . '"  class="form-control" >
                                </div>';
                            }

                        for ($i = 1; $i <= $language_count; $i++) {
                            echo '<div class="form-group ">
                            <label>' . $lang[1216] . '<span class="language">' . $language[$i] . '</span></label>
                            <textarea type="text" name="comment_' . $language[$i] . '"  class="form-control mb-3" rows="6"></textarea>
                            </div>';
                        }
                        // echo '<div class="form-group">
                        //     <label>' . $lang[1499] . '</label>
                        //     <input type="text" name="reportdate"  class="form-control datepicker" >
                        //     </div>';


                            for ($i = 1; $i <= $img_item; $i++) {
                                echo '<div class="form-group"><table><tr>
                                      <td><label class="col-sm-12 control-label">' . $lang[1153] . '&nbsp; ' . $i . '</label></td>
                                      <td><input type="file" name="img_' . $i . '" class="form-control"></td>
                                      </tr></table></div>';
                            }

                        

                        echo '<div class="form-group">
                                <input type="submit" name="addreport" value="' . $lang[1952] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form>';
                        echo '</div>';
                    }

                    if (isset($do) && $do == 'addreport' && $access =='5') {

                        if (isset($_POST['addreport'])) {

                            $checklist = $_POST['checklist'];
                            $contract = $_POST['contract'];
                            $reportdate = jdate("Y/m/d : a:h:s");;
                            $date = jdate("Y/m/d : a:h:s");

                        $images_name = '';
                        if (!@$images_name) {
                            for ($i_image = 1; $i_image <= $img_item; $i_image++) {
                                $img_[$i_image] = $_FILES['img_' . $i_image]['name'];
                                $read_file_name = explode(".", $img_[$i_image]);
                                $extn = end($read_file_name);
                                $img_[$i_image] = 'img_' . time() . '_' . $i_image . '.' . $extn;

                                if ($extn == 'xls' || $extn == 'xlsx' || $extn == 'DOCX' || $extn == 'docx' || $extn == 'DOC' || $extn == 'doc' || $extn == 'zip' || $extn == 'ZIP' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' && $_FILES['attach']['size'] < 200000000) {
                                    $images_name .= $img_[$i_image] . ',';
                                    move_uploaded_file($_FILES['img_' . $i_image]["tmp_name"], "../upload/report/" . $img_[$i_image]);
                                } elseif (!empty($exp[$i_image])) {
                                    $images_name .= $exp[$i_image] . ',';
                                } else {
                                    $images_name .= ',';
                                }
                            }
                        }



                                $code = Code("", "no", 7);
                            for ($i = 1; $i <= $language_count; $i++) {
                                $comment_[$i] = addslashes($_POST['comment_' . $language[$i]]);
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_record = $language[$i];
                              
                                    Query("insert into 
                                    tbl_checklistreport 
                                    (tbl_checklistreport_title,
                                    tbl_checklistreport_dateadd,
                                    tbl_checklistreport_reportdate,
                                    tbl_checklistreport_reportfile,
                                    tbl_checklistreport_comment,
                                    tbl_checklistreport_mentor,
                                    tbl_checklistreport_contract,
                                    tbl_checklistreport_status,
                                    tbl_checklistreport_listcode,
                                    tbl_checklistreport_lng,
                                    tbl_checklistreport_code,
                                    tbl_checklistreport_delete)
                                    values('$title_[$i]','$date','$reportdate','$images_name','$comment_[$i]','$userCode','$contract','0','$checklist','$language_record','$code','0') ");
                              
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: checklistreport.php?checklist=$checklist&contract=$contract");
                            exit();
                    }

                    }

                    
                    if (isset($do) && $do == 'edit' && $access =='9') {

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
                            <form action="?do=edititem&contract='.$getcontract.'" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="code" value="' . $code . '" >
                            <input type="hidden" name="contract" value="' . $getcontract . '" >';

                            echo '<div class="form-group">
                        <label>'.$lang[1939].'</label><select name="question" style="width: 100%;" class="select2">
                            <option value="0">'.$lang[70].'</option>';
                            $result_checklistdefault = Query("SELECT * From tbl_checklistdefault where tbl_checklistdefault_admin='$userCode' and tbl_checklistdefault_delete='0' and tbl_checklistdefault_lng='$language_default' order by tbl_checklistdefault_id DESC ");
                            while ($read_checklistdefault = mysqli_fetch_array($result_checklistdefault)) {
                                echo '<option '.(read_fild(NULL, 'tbl_checklist_list') == $read_checklistdefault['tbl_checklistdefault_code'] ? 'selected' : '').' value="' . $read_checklistdefault['tbl_checklistdefault_code'] . '">' . $read_checklistdefault['tbl_checklistdefault_title'] . '</option>';
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
                        <label>' . $lang[1459] . '</label>
                        <input type="text" name="expire" class="form-control datepicker" value="' . read_fild(null, 'tbl_checklist_expire') . '" >
                        </div>';
                        echo '<div class="form-group">
                                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3" >
                                </div>
                        </form></div>';
                    }

                    if (isset($do) && $do == 'edititem' && $access =='9') {
                        if (isset($_POST['updateitem'])) {
                            $code = $_POST['code'];
                            $contract = $_POST['contract'];
                            $question = $_POST['question'];
                            $expire = $_POST['expire'];
                            $date = jdate("Y/m/d : a:h:s");
                            for ($i = 1; $i <= $language_count; $i++) {
                                $title_[$i] = addslashes($_POST['title_' . $language[$i]]);
                                $language_record = $language[$i];

                                Query("Update tbl_checklist SET 
                                tbl_checklist_dateedit='$date',
                                tbl_checklist_title='$title_[$i]',
                                tbl_checklist_list='$question',
                                tbl_checklist_expire='$expire'   
                                Where tbl_checklist_contract= '$contract' and tbl_checklist_code= '$code' and tbl_checklist_lng='$language_record' ");
                            }
                            $_SESSION['error'] = $lang[21];
                            header("Location: checklist.php?contract=$contract");
                        }
                    }

                    // start = delete record
                    if (isset($do) && $do == 'delete' && $access =='5') {
                        $result = Query("SELECT * from tbl_checklistreport Where tbl_checklistreport_code = '$code' and tbl_checklistreport_lng='$language_default' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-8 col-12 p-2"><p>' . $lang[25] . '</p><h3>' . $read['tbl_checklistreport_title'] . '</h3>
                         <form action="" method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $code . '" name="code" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
                        if (isset($_POST['submit'])) {
                            $code = $_POST['code'];
                            $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_status='0' and tbl_checklistreport_code =  '$code'  ");
                            if(mysqli_num_rows($result) > 0) {
                            $read = mysqli_fetch_array($result);
                            $listcode = $read['tbl_checklistreport_listcode'];
                            $contract = $read['tbl_checklistreport_contract'];
                            Query("update tbl_checklistreport set  tbl_checklistreport_delete='1' Where tbl_checklistreport_code= '$code' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: checklistreport.php?checklist=$listcode&contract=$contract");
                            exit();
                            }
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            $result = Query("SELECT * FROM tbl_checklistreport Where  tbl_checklistreport_status='0' and tbl_checklistreport_code =  '$code'  ");
                            if(mysqli_num_rows($result) > 0) {
                            $read = mysqli_fetch_array($result);
                            $listcode = $read['tbl_checklistreport_listcode'];
                            $contract = $read['tbl_checklistreport_contract'];
                            $contract = $_POST['contract'];
                            header("Location: checklistreport.php?checklist=$listcode&contract=$contract");
                            exit();
                            }
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