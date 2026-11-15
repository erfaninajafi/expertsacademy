<?php
// include
include './includer.php';
$show_per_page = '10';
$pagetitle = 'لیست فراگیرها';
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
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-4"></td><td class="col-sm-4"></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="member.php?do=search" method="POST">
                       <input type="text" class="form-control" id="member_id" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-primary" >' . $lang[77] . '</button>
                       </form></td></tr></table>';

                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_member Where tbl_member_delete ='0' and $status (tbl_member_name LIKE '%$keyword%'  or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_member Where tbl_member_delete ='0' and $status (tbl_member_name LIKE '%$keyword%' or tbl_member_email LIKE '%$keyword%' or tbl_member_mobile LIKE '%$keyword%' or tbl_member_nationalcode LIKE '%$keyword%') ORDER BY tbl_member_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>' . $lang[95] . '</th>
                                    <th>موبایل</th>
                                    <th>کد ورود</th>
                                    <th>مدارک تایید نشده</th>
                                    <th>تصویر</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_member_status'] == 0) {
                                $st = 'تایید نشده';
                                $style = 'style="color:red;"';
                            } else {
                                $st = 'تایید شده';
                                $style = 'style="color:green;"';
                            }
                            $thisid = $read['tbl_member_id'];
                            $result_document = Query("select * from tbl_document where tbl_document_status='0' and tbl_document_userid ='$thisid' ");
                            echo '<tr>
                                <td style="width: 1%">' . $i . '</td>
                                <td class="col-sm-1"><a href="?do=edit&id=' . $read['tbl_member_systemcode'] . '" ' . $style . '>' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . '</a></td>
                                <td class="col-sm-1" >' . $read['tbl_member_email'] . '</td>
                                <td class="col-sm-1" >' . $read['tbl_member_mobile'] . '</td>
                                <td class="col-sm-1" >' . $read['tbl_member_logincode'] . '</td>
                                <td class="col-sm-1" >' . mysqli_num_rows($result_document) . '</td>
                                <td class="col-sm-1">' . (!empty($read['tbl_member_profile']) ? '<a href="../upload/profile/' . $read['tbl_member_profile'] . '" target="_blank"><img src="../upload/profile/' . $read['tbl_member_profile'] . '"></a>' : '<img src="../asset/images/no-image.jpg" style="width:100px;height:100px;margin:2px;">') . ''
                                . '</td>
                                </tr><tr>
                                <td class="col-sm-1 btn-class" colspan="7">
                                <a href="?do=edit&id=' . $read['tbl_member_systemcode'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
                                <a href="register.php?history=' . $read['tbl_member_systemcode'] . '" target="_blank" class="btn btn-xs btn-warning " > ثبت نام ها</a> 
                                <a href="document.php?history=' . $read['tbl_member_systemcode'] . '" class="btn btn-xs btn-warning " target="_blank">مدارک</a> 
                                <a href="?do=delete&id=' . $read['tbl_member_systemcode'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                                </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
                        // start pagination                
                        if ($page > $page_number) {
                            echo '<div class="margin-top">' . $lang[57] . '</div>';
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
                            <td class="col-lg-6">
                                <ul class="pagination">
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '')  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword . (!empty($_GET['hobby']) ? '&hobby=' . $_GET['hobby'] : '') ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-6">
                                <form action="member.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
                                    <div class="form-group">
                                        <button type="submit" name="gotopage" class="btn btn-success" style="float: left;"><?= $lang[76] ?></button>
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
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'] . ' </h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-xs btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-xs btn-danger"></form></div>';
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
                        $birthday = explode("/", $birthday);

                        echo '<div class="col-lg-12">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">';
                        ?>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label>نام</label>
                            <input class="form-control" name="fname" value="<?= $read['tbl_member_fname'] ?>" type="text">
                        </div>
                        <div class="form-group">
                            <label>نام خانوادگی</label>
                            <input class="form-control" name="lname" value="<?= $read['tbl_member_lname'] ?>" type="text">
                        </div>
                        <div class="form-group">
                            <label><?= $lang[132] ?></label>
                            <input class="form-control" name="nationalcode" value="<?= $read['tbl_member_nationalcode'] ?>" maxlength="10" type="text">
                            <input name="oldnationalcode" value="<?= $read['tbl_member_nationalcode'] ?>" maxlength="10" type="hidden">
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
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label><?= $lang[141] ?></label>
                            <select class="form-select" name="state">
                                <option value="">انتخاب استان</option>
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
                            <label>آخرین مدرک تحصیلی</label>
                            <select class="form-select" name="madrak">
                                <option value=""><?= $lang[70] ?></option>
                                <?php

                                    for ($i = 1262; $i <= 1266; $i++) {
                                        echo '<option value="' . $i . '" ' . ($read['tbl_member_madrak'] == $i ? 'selected' : '') . '>' . $lang[$i] . '</option>';
                                    }

                                    ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>رشته تحصیلی</label>
                            <input class="form-control" name="reshteh" value="<?= $read['tbl_member_reshteh'] ?>" data-parsley-length="[4, 100]" maxlength="20" data-parsley-group="block-1" type="text">
                        </div>

                    </div>
                    <div class="col-lg-6">
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
                    </div>
                    <div class="col-lg-6">

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
                                تایید عکس پروفایل و مدارک
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" name="status" <?= ($read['tbl_member_status'] == 1 ? 'checked' : '') ?> value="1">
                                تایید نهایی شده
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                تاریخ ثبت نام : <?= $read['tbl_member_regdate'] ?>
                            </label>
                        </div>
                    </div>



                    <hr>
                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="hidden" value = "' . $id . '" name="id" >
                <input type="hidden" value = "' . $read['tbl_member_lang'] . '" name="user_lang" >
                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                </div></div>
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
                            $year = protect($_POST['year']);
                            $month = protect($_POST['month']);
                            $day = protect($_POST['day']);
                            $mobile = protect($_POST['mobile']);
                            $oldmobile = protect($_POST['oldmobile']);
                            $postcode = protect($_POST['postcode']);
                            $state = protect($_POST['state']);
                            $madrak = protect($_POST['madrak']);
                            $reshteh = protect($_POST['reshteh']);
                            $profile = protect($_FILES['profile']);
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
                            $sex = $_POST['sex'];



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
                                $_SESSION['error'] = ' شماره موبایل تکراری میباشد';
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
                            tbl_member_reshteh='$reshteh',
                            tbl_member_madrak='$madrak',
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
                            tbl_member_password='$npass' 
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