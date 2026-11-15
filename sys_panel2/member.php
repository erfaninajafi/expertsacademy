<?php
// include
include './includer.php';
$show_per_page = '20';
$pagetitle = $lang[1553];
$Tabpage = '20';
$thisaccess = '1';
include './header.inc.php';
$status = '';
if (isset($_GET['status'])) {
    $stt = $_GET['status'];
    $status = "tbl_member_status='$stt' and";
    $link = '?status=' . $stt . '&';
} elseif (isset($_GET['hobby'])) {
    $hobby = $_GET['hobby'];
    $status .= " tbl_member_hobby like '%[$hobby]%' and";
} else {
    $status = '';
    $link = '?';
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
                        <form action="?do=search" method="POST" class="search-form">
                            <input type="text" class="form-control" value="<?= $keyword ?>" name="keyword">
                            <button type="submit" name="go" class="btn btn-primary"><span class="fas fa-search"></span></button>
                        </form>
                    </td>
                    </tr>
                </table>
                <?php

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_member Where tbl_member_systemcode !='' and tbl_member_delete ='0' and $status (tbl_member_lname LIKE '%$keyword%'  or  tbl_member_fname LIKE '%$keyword%'  or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_member Where tbl_member_systemcode !='' and  tbl_member_delete ='0' and $status (tbl_member_lname LIKE '%$keyword%'  or  tbl_member_fname LIKE '%$keyword%' or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[53] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>' . $lang[95] . '</th>
                                    <th>'.$lang[378].'</th>
                                    <th>'.$lang[1554].'</th>
                                    <th>'.$lang[1555].'</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_member_status'] == 0) {
                                $st = $lang[1556];
                                $style = 'style="color:red;"';
                            } else {
                                $st = $lang[1557];
                                $style = 'style="color:green;"';
                            }
                            $thisid = $read['tbl_member_id'];
                            $result_document = Query("select * from tbl_document where tbl_document_status='0' and tbl_document_userid ='$thisid' ");
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">
                                <div class="avatar avatar-2xl border border-3 border-light rounded-circle">
                                            <img class="rounded-circle" src="'.(!empty($read['tbl_member_profile']) ? '../upload/profile/' . $read['tbl_member_profile'] : 'assets/img/no-image.jpg').'" alt="'.$read['tbl_member_fname'].' '.$read['tbl_member_lname'].'">
                                            </div></div>
                                            <td class="align-middle p-2">
                                ' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_email'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_mobile'] . '</td>
                                <td class="align-middle p-2" >' . $read['tbl_member_logincode'] . '</td>
                                <td class="align-middle p-2" >' . mysqli_num_rows($result_document) . '</td>
                                
                                <td class="align-middle p-2">
                                <a href="?do=edit&id=' . $read['tbl_member_systemcode'] . '" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '" ><span class="bi-pencil-square"></span></a>
                                <a href="?do=delete&id=' . $read['tbl_member_systemcode'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                <a href="archive.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-light" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1804] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>
                                <a href="archiveorder.php?user=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-default" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[2033] . '"><span class="bi-file-earmark-spreadsheet-fill"></span></a>
                                <a href="document.php?history=' . $read['tbl_member_systemcode'] . '" class="btn me-1 mb-1 btn-sm btn-info" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1312] . '" target="_blank" style="display:none;"><span class="bi-file-arrow-down-fill"></span></a> 
                                <a href="cer.php?history=' . $read['tbl_member_id'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1810] . '" target="_blank" ><span class="bi-file-arrow-down-fill"></span></a> 
                                <a href="../fa/resume/' . $read['tbl_member_id'] . '/" class="btn me-1 mb-1 btn-sm btn-primary" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1588]. '" target="_blank"><span class="bi-file-earmark-person"></span></a>
                                </td>
                                </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
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
                                <li><a class="page-link" href="<?= $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '')  ?>"><?= $lang[75] ?></a>
                                </li>
                                <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                </li>
                                <li><a class="page-link" href="<?= $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '') ?>"><?= $lang[74] ?></a>
                                </li>
                            </ul>
                        </td>
                        <td class="col-lg-3">
                            <form action="member.php?do=gotopage" class="search-form" method="POST">
                                <div class="form-group">
                                    <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
                                    <input type="text" class="form-control go-fld" name="page_number">
                                </div>
                            </form>
                        </td>
                    </tr>
                </table>
                <?php
                        }
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: member.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: member.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_member  Where tbl_member_systemcode = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . ' </h3><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger mt-3"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];
                            $result = Query("Select * From tbl_member where tbl_member_systemcode = '$id' ");
                            $read = mysqli_fetch_array($result);
                            $img = $read['tbl_member_profile'];
                            if (isset($img)) {
                                unlink($directory . $img);
                            }
                            Query("update tbl_member set tbl_member_delete='1' Where tbl_member_systemcode = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: member.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: member.php");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($do) && $do == 'edit') {
                        $result = Query("Select * from tbl_member Where tbl_member_delete='0' and tbl_member_systemcode = '$id' ");
                        $read = mysqli_fetch_array($result);
                        $check = $read['tbl_member_status'];
                        if ($check == 1) {
                            $style_status = 'checked';
                        } else {
                            $style_status = '';
                        }
                        $birthday = $read['tbl_member_birthday'];
                        $birthday = (!empty($birthday) ? explode("/", $birthday) : '');

                        echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">';
                        ?>
                <div class="form-group">
                    <label><?=$lang[127]?></label>
                    <input class="form-control" name="fname" value="<?= $read['tbl_member_fname'] ?>" type="text">
                </div>
                <div class="form-group">
                    <label><?=$lang[128]?></label>
                    <input class="form-control" name="lname" value="<?= $read['tbl_member_lname'] ?>" type="text">
                </div>
                <div class="form-group">
                    <label><?= $lang[132] ?></label>
                    <input class="form-control" name="nationalcode" value="<?= $read['tbl_member_nationalcode'] ?>" maxlength="10" type="text">
                    <input name="oldnationalcode" value="<?= $read['tbl_member_nationalcode'] ?>" maxlength="10" type="hidden">
                </div>

                <div class="form-group">
                    <label><?=$lang[131]?></label>
                    <input class="form-control" name="fathername" value="<?= $read['tbl_member_fathername'] ?>" type="text">
                </div>

                <div class="form-group">
                    <label><?= $lang[1811] ?></label>
                    <select class="form-select" name="bcity">
                        <option value=""><?=$lang[1558]?></option>
                        <?php
                                    for ($i = 764; $i <= 796; $i++) {
                                        echo '<option value="' . $lang[$i] . '" ' . ($read['tbl_member_bcity'] == $lang[$i] ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                                    }
                                    ?>
                    </select>
                </div>




                <div class="col-lg-12 padding-clear">
                    <div class="form-group">
                        <label><?= $lang[134] ?></label>
                    </div>
                </div>
                <div class="row padding-clear">
                    <div class="col-lg-4 padding-clear">
                        <div class="form-group">
                            <select class="form-select" name="day">
                                <option value=""><?= $lang[162] ?></option>
                                <?php
                                            for ($i = 1; $i <= 31; $i++) {
                                                if ($i < 10) {
                                                    $ch = 0;
                                                } else {
                                                    $ch = '';
                                                }
                                                echo '<option value="' . $ch . $i . '" ' . ($birthday[2] == $ch . $i ? 'selected' : '') . '>' . $i . '</option>';
                                            }
                                            ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-4 padding-clear">
                        <div class="form-group">
                            <select class="form-select" name="month">
                                <option value=""><?= $lang[163] ?></option>
                                <?php
                                            for ($i = 1; $i <= 12; $i++) {
                                                echo '<option value="' . $ch . $i . '" ' . ($birthday[1] == $ch . $i ? 'selected' : '') . '>' . $lang[149 + $i] . '</option>';
                                            }
                                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-4 padding-clear">
                        <div class="form-group">
                            <select class="form-select" name="year">
                                <option value=""><?= $lang[164] ?></option>
                                <?php
                                            //if ($read['tbl_member_lang'] == 'fa') {
                                            for ($i = 1310; $i <= 1395; $i++) {
                                                echo '<option value="' . $i . '" ' . ($birthday[0] == $i ? 'selected' : '') . '>' . $i . '</option>';
                                            }
                                            // } else {
                                            //     for ($i = 1930; $i <= 2010; $i++) {
                                            //         echo '<option value="' . $i . '" ' . ($birthday[0] == $i ? 'selected' : '') . '>' . $i . '</option>';
                                            //     }
                                            // }

                                            ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label><?= $lang[133] ?></label>
                    <label>
                        <input type="radio" name="sex" value="1" <?= ($read['tbl_member_sex'] == 1 ? 'checked' : '') ?>><?= $lang[148] ?>
                    </label>
                    <label>
                        <input type="radio" name="sex" value="2" <?= ($read['tbl_member_sex'] == 2 ? 'checked' : '') ?>><?= $lang[149] ?>
                    </label>
                </div>

            </div>
            <div class="col-lg-10 col-12 p-3">
                <div class="form-group">
                    <label><?= $lang[141] ?></label>
                    <select class="form-select" name="state">
                        <option value=""><?=$lang[1558]?></option>
                        <?php
                                    for ($i = 764; $i <= 796; $i++) {
                                        echo '<option value="' . $lang[$i] . '" ' . ($read['tbl_member_state'] == $lang[$i] ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                                    }
                                    ?>
                    </select>
                </div>
                <div class="form-group">
                    <label><?= $lang[147] ?></label>
                    <input class="form-control" name="address" value="<?= $read['tbl_member_address'] ?>" maxlength="200" type="text">
                </div>
                <div class="form-group">
                    <label><?= $lang[139] ?></label>
                    <input class="form-control" name="postcode" value="<?= $read['tbl_member_postcode'] ?>" maxlength="10" type="text">
                </div>
                <div class="form-group">
                    <label><?= $lang[137] ?></label>
                    <input class="form-control" name="phone" value="<?= $read['tbl_member_phone'] ?>" maxlength="30" type="text">
                </div>
                <div class="form-group">
                    <label><?= $lang[136] ?></label>
                    <input class="form-control" name="mobile" value="<?= $read['tbl_member_mobile'] ?>" maxlength="11" type="text">
                    <input name="oldmobile" value="<?= $read['tbl_member_mobile'] ?>" type="hidden">
                </div>


                <div class="form-group">
                    <label><?= $lang[135] ?></label>
                    <input class="form-control" name="email" value="<?= $read['tbl_member_email'] ?>" type="text">
                    <input name="oldemail" value="<?= $read['tbl_member_email'] ?>" type="hidden">
                </div>

                <div class="form-group">
                    <label><?= $lang[146] ?></label>
                    <input class="form-control" name="password" type="password">
                    <input name="oldpassword" value="<?= $read['tbl_member_password'] ?>" type="hidden">
                </div>


                <div class="form-group">
                    <label><?= $lang[365] ?></label>
                    <input class="form-control" name="profile" data-parsley-group="block-1" type="file" value="">
                </div>

                <?php if (!empty($read['tbl_member_profile'])) { ?>
                <a href="../upload/profile/<?= $read['tbl_member_profile'] ?>" target="_blank"><img width="80" height="80" src="../upload/profile/<?= $read['tbl_member_profile'] ?>" /></a>
                <?php } else { ?>
                <a href="../upload/profile/<?= $read['tbl_member_profile'] ?>" target="_blank"><img width="80" height="80" src="../asset/images/no-image.jpg" /></a>
                <?php } ?>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="showimage" <?= ($read['tbl_member_showimage'] == 1 ? 'checked' : '') ?> value="1">
                        <?=$lang[1561]?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?= ($read['tbl_member_status'] == 1 ? 'checked' : '') ?> value="1">
                        <?=$lang[1562]?>
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <?=$lang[1563]?> : <?= $read['tbl_member_regdate'] ?>
                    </label>
                </div>
                <hr>
                <?php
                        echo '<div class="form-group">
                            <input type="hidden" value = "' . $id . '" name="id" >
                            <input type="hidden" value = "' . $read['tbl_member_lang'] . '" name="user_lang" >
                            <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                            </div>
                        </form></div>';
                    }
                    // end = edit item
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $fname = protect($_POST['fname']);
                            $lname = protect($_POST['lname']);
                            $nationalcode = protect($_POST['nationalcode']);
                            $fathername = protect($_POST['fathername']);
                            $bcity = protect($_POST['bcity']);
                            $year = protect($_POST['year']);
                            $month = protect($_POST['month']);
                            $day = protect($_POST['day']);
                            $mobile = protect($_POST['mobile']);
                            $oldmobile = protect($_POST['oldmobile']);
                            $postcode = protect($_POST['postcode']);
                            $state = protect($_POST['state']);
                            $profile = $_FILES['profile'];
                            $email = strtolower(protect($_POST['email']));
                            $status = (!empty($_POST['status']) ? $_POST['status'] : '0');
                            $showimage = (!empty($_POST['showimage']) ? $_POST['showimage'] : '0');
                            $password = $_POST['password'];
                            $oldpassword = $_POST['oldpassword'];
                            $phone = protect($_POST['phone']);
                            $address = protect($_POST['address']);
                            $lang_user = protect($_POST['user_lang']);
                            $oldnationalcode = $_POST['oldnationalcode'];
                            $oldemail = $_POST['oldemail'];
                            $birthday = $year . "/" . $month . "/" . $day;
                            $sex = (!empty($_POST['sex']) ? $_POST['sex'] : '0');

                            if (empty($mobile)) {
                                $_SESSION['error'] = $lang[371];
                                header("Location: member.php");
                                exit();
                            }
                            $result = Query("select * from tbl_member where tbl_member_email = '$email' and tbl_member_systemcode != '$id' ");
                            $row = mysqli_num_rows($result);

                            if ($oldemail != $email && $row == 1) {
                                $_SESSION['error'] = $lang[389];
                                header("Location: member.php");
                                exit();
                            }

                            $result = Query("select * from tbl_member where tbl_member_mobile = '$mobile' and tbl_member_systemcode != '$id' ");
                            $row = mysqli_num_rows($result);

                            if ($oldmobile != $mobile && $row == 1) {
                                $_SESSION['error'] = $lang[1564];
                                header("Location: member.php");
                                exit();
                            }
                            if ($lang_user == 'fa') {
                                $result = Query("select * from tbl_member where tbl_member_nationalcode = '$nationalcode' and tbl_member_systemcode != '$id' ");
                                $row = mysqli_num_rows($result);

                                if ($oldnationalcode != $nationalcode && $row == 1) {
                                    $_SESSION['error'] = $lang[390];
                                    header("Location: member.php");
                                    exit();
                                }
                            }



                            $result = Query("select * from tbl_member where tbl_member_systemcode = '$id' ");
                            $read = mysqli_fetch_array($result);

                            if (empty($_FILES['profile']['name'])) {
                                $profile = $read['tbl_member_profile'];
                            } else {
                                if ($_FILES['profile']['name']) {
                                    $profile = $_FILES['profile']['name'];
                                    $read_file_name = explode(".", $profile);
                                    $extn = end($read_file_name);
                                    $profile = 'profile_' . time() . '.' . $extn;
                                    if ($extn == 'jpg' || $extn == 'JPG'  || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                        move_uploaded_file($_FILES['profile']["tmp_name"], "../upload/profile/" . $profile);
                                    }
                                }
                            }

                            if (!empty($password)) {
                                $npass = password_hash("$password", PASSWORD_BCRYPT, array('cost' => 12));
                            } else {
                                $npass = $oldpassword;
                            }

                            Query("update tbl_member SET  
                            tbl_member_delete='0',
                            tbl_member_adminverify='0',
                            tbl_member_verify='0',
                            tbl_member_view='0',
                            tbl_member_sath='0',
                            tbl_member_showimage = '$showimage' ,
                            tbl_member_postcode='$postcode',
                            tbl_member_address='$address',
                            tbl_member_phone='$phone',
                            tbl_member_fname='$fname',
                            tbl_member_lname='$lname',
                            tbl_member_zipcode='0',
                            tbl_member_nationalcode='$nationalcode',
                            tbl_member_sex='$sex',
                            tbl_member_birthday='$birthday',
                            tbl_member_mobile='$mobile',
                            tbl_member_state='$state',
                            tbl_member_email='$email',
                            tbl_member_profile='$profile',
                            tbl_member_status = '$status',
                            tbl_member_password='$npass',
                            tbl_member_fathername='$fathername',
                            tbl_member_bcity='$bcity'
                            where tbl_member_systemcode = '$id' ");
                            $_SESSION['error'] = $lang[391];
                            header("Location: member.php");
                            exit();
                        }
                        // end = update item
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