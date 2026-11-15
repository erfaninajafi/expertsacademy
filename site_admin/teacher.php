<?php
// include
include './includer.php';
$show_per_page = '30';
$pagetitle = 'لیست اساتید';
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';
?>

<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (isset($_GET['status'])) {
                        $stt = $_GET['status'];
                        $status = "tbl_teacher_status='$stt' and";
                        $link = '?status=' . $stt . '&';
                    } else {
                        $status = '';
                        $link = '?';
                    }

                    function protect2($str)
                    {
                        $str = str_replace("'", "", $str);
                        $str = str_replace('"', "", $str);
                        return $str;
                    }
                    // end include 
                    echo '<section class="content-header">
         <h1>' . $pagetitle . '</h1>
      </section>
        <section class="content"><div class="col-md-12">
            <div class="box box-body">';
                    // list content
                    if (!isset($do)) {
                        // search and add button
                        echo  '<div class="form-group">
                       ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                       </div>
                       <table></tr><td class="col-sm-9"><a class="btn btn-primary margin-left" href="?do=add" >' . $lang[22] . '</a></td>
                       <td class="col-sm-6" style="padding-left: 0px;"> 
                       <form action="teacher.php?do=search" method="POST">
                       <input type="text" class="form-control" id="member_id" name="keyword"></td>
                       <td class="col-sm-2" style="padding-left: 0px;">
                       <button type="submit" name="go" class="btn btn-default" style="height: 33px; width:50px; padding:4px 8px;">' . $lang[77] . '</button>
                       </form></td></tr></table><div id="result"></div>';
                        // start count db result for pagination               
                        $result_for_count = Query("SELECT * From tbl_teacher Where $status (tbl_teacher_fname LIKE '%$keyword%' or tbl_teacher_lname LIKE '%$keyword%' or tbl_teacher_lnameen LIKE '%$keyword%' or tbl_teacher_fnameen LIKE '%$keyword%' or tbl_teacher_email LIKE '%$keyword%' or tbl_teacher_mobile LIKE '%$keyword%' or tbl_teacher_nationalcode LIKE '%$keyword%' or tbl_teacher_username LIKE '%$keyword%' or (tbl_teacher_lname LIKE '%$keyword%' and tbl_teacher_fname LIKE '%$keyword%')) ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_teacher Where $status (tbl_teacher_fname LIKE '%$keyword%' or tbl_teacher_lname LIKE '%$keyword%' or tbl_teacher_lnameen LIKE '%$keyword%' or tbl_teacher_fnameen LIKE '%$keyword%' or tbl_teacher_email LIKE '%$keyword%' or tbl_teacher_mobile LIKE '%$keyword%' or tbl_teacher_nationalcode LIKE '%$keyword%' or tbl_teacher_username LIKE '%$keyword%' or (tbl_teacher_lname LIKE '%$keyword%' and tbl_teacher_fname LIKE '%$keyword%')) ORDER BY tbl_teacher_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);

                        // start show list
                        echo '<table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                                <thead class="table-dark">
                <tr >
                    <th>' . $lang[60] . '</th>
                    <th>' . $lang[92] . '</th>
                    <th>' . $lang[95] . '</th>
                    <th>موبایل</th>
                    <th>' . $lang[63] . '</th>
                </tr>
            </thead>
            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            if ($read['tbl_teacher_status'] == 0) {
                                $st = 'تایید نشده';
                                $style = 'style="color:red;"';
                            } else {
                                $st = 'تایید شده';
                                $style = 'style="color:green;"';
                            }

                            echo '<tr>
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-2"><a href="?do=edit&id=' . $read['tbl_teacher_id'] . '">' . $read['tbl_teacher_fname'] . ' ' . $read['tbl_teacher_lname'] . '</a></td>
            <td class="col-sm-2">' . $read['tbl_teacher_email'] . '</td>
            <td class="col-sm-1">' . $read['tbl_teacher_mobile'] . '</td>
            <td class="col-sm-3">
            <a href="?do=edit&id=' . $read['tbl_teacher_id'] . '" class="btn btn-xs btn-success " >' . $lang[23] . '</a>
            <a href="misc.php?t=20&teacher=' . $read['tbl_teacher_id'] . '" class="btn btn-xs btn-warning" target="_blank">' . $lang[41] . '</a>
            <a href="?do=delete&id=' . $read['tbl_teacher_id'] . '" class="btn btn-xs btn-danger">' . $lang[24] . '</a>
            </td></tr>';
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
                            echo '<table class="table-striped margin-top"><tr><td class="col-sm-6"><ul class="pagination pagination-sm">
       <li><a href="' . $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword . '">' . $lang[75] . '</a></li>
       <li><a>' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</a></li>
       <li><a href="' . $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword . '">' . $lang[74] . '</a></li>
       </ul></td>
       <td class="col-sm-1">
       <form action="teacher.php?do=gotopage" class="navbar-form navbar-left" role="search" method="POST">
       <div class="form-group">
       <input type="text" class="form-control" name="page_number"  style="height: 28px; width:30px; padding:6px 8px;">
       </div><button type="submit" name="gotopage" class="btn btn-default" style="height: 28px; width:30px; padding:4px 8px;">' . $lang[76] . '</button>
       </form></td></tr></table>';
                        }
                    }
                    if (isset($do) && $do == 'gotopage') {
                        if (isset($_POST['gotopage'])) {
                            $page_number = $_POST['page_number'];
                            header("Location: teacher.php?page=$page_number");
                        }
                    }
                    // end = pagination
                    // start = search result               
                    if (isset($do) && $do == 'search') {
                        if (isset($_POST['go'])) {
                            $keyword_search = $_POST['keyword'];
                            header("Location: teacher.php?keyword=$keyword_search");
                        }
                    }
                    // end = search result  
                    // start = delete record
                    if (isset($do) && $do == 'delete') {
                        $result = Query("SELECT * from tbl_teacher  Where tbl_teacher_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6">' . $lang[25] . '<br /><h2>' . $read['tbl_teacher_fname'] . ' ' . $read['tbl_teacher_lname'] . '</h2><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger"></form></div>';
                        if (isset($_POST['submit'])) {
                            $id = $_POST['id'];

                            $result = Query("Select * From tbl_teacher where tbl_teacher_id = '$id' ");
                            $read = mysqli_fetch_array($result);
                            $img = $read['tbl_teacher_logo'];

                            if (isset($img)) {
                                unlink($directory . $img);
                            }
                            Query("DELETE from tbl_teacher Where tbl_teacher_id = '$id' ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: teacher.php");
                            exit();
                        }
                        // no delete
                        elseif (isset($_POST['nodelete'])) {
                            header("Location: teacher.php");
                            exit();
                        }
                    }

                    // end = delete item

                    // start = edit record
                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-12">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data">';
                    ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="lastname"><?= $lang[127] ?></label>
                                <input class="form-control" name="fname" type="text" required="">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[128] ?></label>
                                <input class="form-control" name="lname" type="text" required="">
                            </div>
                            <div class="form-group">
                                <label for="lastname">تخصص</label>
                                <input class="form-control" name="info" type="text">
                            </div>
                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                      <label>' . $lang[51] . '</label>
                      <textarea name="content" class="editor editor_' . $language[$i] . '"></textarea>
                      </div>';
                            }
                            ?>

                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label><?= $lang[147] ?></label>
                                <input class="form-control" name="address" maxlength="200" type="text">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[139] ?></label>
                                <input class="form-control" name="postcode" maxlength="10" type="text">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[137] ?></label>
                                <input class="form-control" name="phone" maxlength="30" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[136] ?></label>
                                <input class="form-control" name="mobile" maxlength="11" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">linkdin</label>
                                <input class="form-control" name="linkdin" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">insta</label>
                                <input class="form-control" name="insta" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">twitter</label>
                                <input class="form-control" name="twitter" type="text">
                            </div>

                            <div class="form-group">
                                <label for="lastname"><?= $lang[135] ?></label>
                                <input class="form-control" name="email" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">کلمه عبور</label>
                                <input class="form-control" name="password" type="password">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[365] ?></label>
                                <input class="form-control" name="profile" data-parsley-group="block-1" type="file" value="">
                            </div>
                        </div>

                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="submit" name="additem" value="' . $lang[22] . '" class="btn btn-primary">
                </div></div>
              </form></div>';
                    }
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $id = $_POST['id'];
                            $password = $_POST['password'];

                            if (!empty($password)) {
                                $npass = md5("$password");
                            } else {
                                $npass = '';
                            }

                            $fname = protect2($_POST['fname']);
                            $lname = protect2($_POST['lname']);
                            $nationalcode = protect2($_POST['nationalcode']);
                            $sex = protect2($_POST['sex']);
                            $year = protect2($_POST['year']);
                            $month = protect2($_POST['month']);
                            $linkdin = $_POST['linkdin'];
                            $twitter = $_POST['twitter'];
                            $insta = $_POST['insta'];

                            $day = protect2($_POST['day']);
                            $country = protect2($_POST['country']);
                            $mobile = protect2($_POST['mobile']);
                            $postcode = protect2($_POST['postcode']);
                            $state = protect2($_POST['state']);
                            $profile = protect2($_FILES['profile']);
                            $email = strtolower(protect2($_POST['email']));
                            $status = $_POST['status'];
                            $showimage = $_POST['showimage'];
                            $phone = protect2($_POST['phone']);
                            $address = protect2($_POST['address']);
                            $lang_user = protect2($_POST['user_lang']);
                            $oldemail = $_POST['oldemail'];
                            $info = $_POST['info'];
                            $birthday = $year . "/" . $month . "/" . $day;
                            if (empty($fname) || empty($lname)) {
                                $_SESSION['error'] = $lang[371];
                                header("Location: teacher.php");
                                exit();
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                $content = imjection(strip_tags($_POST['content']));
                            }
                            if ($_FILES['profile']['name']) {
                                $profile = $_FILES['profile']['name'];
                                $read_file_name = explode(".", $profile);
                                $extn = end($read_file_name);
                                $profile = 'profile_' . time() . '.' . $extn;
                                if ($extn == 'jpg' || $extn == 'JPG'  || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                    move_uploaded_file($_FILES['profile']["tmp_name"], "../upload/profile/" . $profile);
                                }
                            }
                            Query("insert into tbl_teacher  (
            tbl_teacher_postcode,
            tbl_teacher_address,
            tbl_teacher_phone,
            tbl_teacher_lname,
            tbl_teacher_fname,
            tbl_teacher_nationalcode,
            tbl_teacher_sex,
            tbl_teacher_birthday,
            tbl_teacher_mobile,
            tbl_teacher_state,
            tbl_teacher_email,
            tbl_teacher_profile,
            tbl_teacher_password,
            tbl_teacher_lang,
            tbl_teacher_info,
            tbl_teacher_linkdin,
            tbl_teacher_twitter,
            tbl_teacher_insta,
            tbl_teacher_text) 
            values(
            '$postcode',
            '$address',
            '$phone',
            '$lname',
            '$fname',
            '$nationalcode',
            '$sex',
            '$birthday',
            '$mobile',
            '$state',
            '$email',
            '$profile',
            '$npass',
            'fa',
            '$info',
            '$linkdin',
            '$twitter',
            '$insta',
            '$content') ");

                            $_SESSION['error'] = $lang[391];
                            header("Location: teacher.php");
                            exit();
                        }
                        // end = update item
                    }
                    // end = edit item
                    if (isset($do) && $do == 'edit') {
                        $result = Query("Select * from tbl_teacher Where tbl_teacher_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        $check = $read['tbl_teacher_status'];
                        if ($check == 1) {
                            $style_status = 'checked';
                        } else {
                            $style_status = '';
                        }
                        $birthday = $read['tbl_teacher_birthday'];
                        $birthday = explode("/", $birthday);

                        echo '<div class="col-lg-12">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">';
                    ?>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label for="lastname"><?= $lang[127] ?></label>
                                <input class="form-control" name="fname" lang='fa' value="<?= $read['tbl_teacher_fname'] ?>" type="text" required="">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[128] ?></label>
                                <input class="form-control" name="lname" lang='fa' value="<?= $read['tbl_teacher_lname'] ?>" type="text" required="">
                            </div>
                            <?php if ($read['tbl_teacher_lang'] == 'en') { ?>
                                <div class="form-group">
                                    <label for="lastname"><?= $lang[130] ?></label>
                                    <input class="form-control" name="lnameen" value="<?= $read['tbl_teacher_lnameen'] ?>" type="text">
                                </div>

                                <div class="form-group">
                                    <label for="lastname"><?= $lang[129] ?></label>
                                    <input class="form-control" name="fnameen" value="<?= $read['tbl_teacher_fnameen'] ?>" type="text">
                                </div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="lastname">تخصص</label>
                                <input class="form-control" name="info" value="<?= $read['tbl_teacher_info'] ?>" type="text">
                            </div>
                            <?php
                            for ($i = 1; $i <= $language_count; $i++) {
                                echo '<div class="form-group">
                      <label>' . $lang[51] . '</label>
                      <textarea name="content" class="editor editor_' . $language[$i] . '">' . exjection($read['tbl_teacher_text']) . '</textarea>
                      </div>';
                            }
                            ?>

                        </div>
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label><?= $lang[147] ?></label>
                                <input class="form-control" name="address" value="<?= $read['tbl_teacher_address'] ?>" maxlength="200" type="text">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[139] ?></label>
                                <input class="form-control" name="postcode" value="<?= $read['tbl_teacher_postcode'] ?>" maxlength="10" type="text">
                            </div>
                            <div class="form-group">
                                <label><?= $lang[137] ?></label>
                                <input class="form-control" name="phone" value="<?= $read['tbl_teacher_phone'] ?>" maxlength="30" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[136] ?></label>
                                <input class="form-control" name="mobile" value="<?= $read['tbl_teacher_mobile'] ?>" maxlength="11" type="text">
                                <input name="oldmobile" value="<?= $read['tbl_teacher_mobile'] ?>" type="hidden">
                            </div>
                            <div class="form-group">
                                <label for="lastname">linkdin</label>
                                <input class="form-control" name="linkdin" value="<?= $read['tbl_teacher_linkdin'] ?>" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">insta</label>
                                <input class="form-control" name="insta" value="<?= $read['tbl_teacher_insta'] ?>" type="text">
                            </div>
                            <div class="form-group">
                                <label for="lastname">twitter</label>
                                <input class="form-control" name="twitter" value="<?= $read['tbl_teacher_twitter'] ?>" type="text">
                            </div>

                            <div class="form-group">
                                <label for="lastname"><?= $lang[135] ?></label>
                                <input class="form-control" name="email" value="<?= $read['tbl_teacher_email'] ?>" type="text">
                                <input name="oldemail" value="<?= $read['tbl_teacher_email'] ?>" type="hidden">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[146] ?></label>
                                <input class="form-control" name="password" type="password">
                                <input name="oldpassword" value="<?= $read['tbl_teacher_password'] ?>" type="hidden">
                            </div>
                            <div class="form-group">
                                <label for="lastname"><?= $lang[365] ?></label>
                                <input class="form-control" name="profile" data-parsley-group="block-1" type="file" value="">
                            </div>

                            <?php if (!empty($read['tbl_teacher_profile'])) { ?>
                                <a href="../upload/profile/<?= $read['tbl_teacher_profile'] ?>" target="_blank"><img width="80" height="80" src="../upload/profile/<?= $read['tbl_teacher_profile'] ?>" /></a>
                            <?php } else { ?>
                                <a href="../upload/profile/<?= $read['tbl_teacher_profile'] ?>" target="_blank"><img width="80" height="80" src="../asset/images/no-image.jpg" /></a>
                            <?php } ?>
                        </div>



                        <hr>
                    <?php
                        echo '<div class="col-lg-12"><div class="form-group">
                <input type="hidden" value = "' . $id . '" name="id" >
                <input type="hidden" value = "' . $read['tbl_teacher_lang'] . '" name="user_lang" >
                <input type="submit" name="updateitem" value="' . $lang[20] . '" class="btn btn-primary">
                </div></div>
              </form></div>';
                    }
                    // end = edit item
                    // start = update items
                    if (isset($do) && $do == 'edititem') {
                        if (isset($_POST['updateitem'])) {
                            $id = $_POST['id'];
                            $fname = protect2($_POST['fname']);
                            $lname = protect2($_POST['lname']);
                            $lnameen = (!empty($_POST['lnameen']) ? protect2($_POST['lnameen']) : '');
                            $fnameen = (!empty($_POST['fnameen']) ? protect2($_POST['fnameen']) : '');
                            $nationalcode = (!empty($_POST['nationalcode']) ? protect2($_POST['nationalcode']) : '');
                            $sex = (!empty($_POST['sex']) ? protect2($_POST['sex']) : '');
                            $linkdin = $_POST['linkdin'];
                            $twitter = $_POST['twitter'];
                            $insta = $_POST['insta'];
                            $year = (!empty($_POST['year']) ? protect2($_POST['year']) : '');
                            $month = (!empty($_POST['month']) ? protect2($_POST['month']) : '');
                            $day = (!empty($_POST['day']) ? protect2($_POST['day']) : '');
                            $country = protect2($_POST['country']);
                            $mobile = protect2($_POST['mobile']);
                            $oldmobile = protect2($_POST['oldmobile']);
                            $postcode = protect2($_POST['postcode']);
                            $state = protect2($_POST['state']);
                            $profile = protect2($_FILES['profile']);
                            $email = strtolower(protect2($_POST['email']));
                            $status = $_POST['status'];
                            $showimage = $_POST['showimage'];
                            $password = $_POST['password'];
                            $oldpassword = $_POST['oldpassword'];
                            $phone = protect2($_POST['phone']);
                            $address = protect2($_POST['address']);
                            $lang_user = protect2($_POST['user_lang']);
                            $oldnationalcode = $_POST['oldnationalcode'];
                            $oldemail = $_POST['oldemail'];
                            $info = $_POST['info'];
                            $birthday = $year . "/" . $month . "/" . $day;

                            if ($lang_user == 'fa') {
                                if (empty($fname)  || empty($lname)) {
                                    $_SESSION['error'] = $lang[371];
                                    header("Location: teacher.php");
                                    exit();
                                }
                            } else {
                                if (empty($sex)  || empty($year) || empty($month) || empty($day)  || empty($country)  || empty($mobile) || empty($state)) {
                                    $_SESSION['error'] = $lang[371];
                                    header("Location: teacher.php");
                                    exit();
                                }
                            }
                            for ($i = 1; $i <= $language_count; $i++) {
                                $content = imjection($_POST['content']);
                            }
                            //                if(strlen($fname) > 150  || strlen($lname) > 150  || strlen($lnameen) > 150  || strlen($fnameen) > 150  || strlen($fathername) > 150  || strlen($nationalcode) > 150  || strlen($sex) > 150  || strlen($birthday) > 150  || strlen($education) > 150  || strlen($country) > 150  || strlen($mobile) > 150  || strlen($tel) > 150  || strlen($postcode) > 150  || strlen($state) > 150  || strlen($city) > 150  || strlen($address) > 150  || strlen($commenten) > 950  || strlen($commentfa) > 950 )
                            //                {
                            //                    $_SESSION['error'] = $lang[377];
                            //                    header("Location: teacher.php");
                            //                    exit();
                            //                }
                            $result = Query("select * from tbl_teacher where tbl_teacher_id = '$id' ");
                            $read = mysqli_fetch_array($result);

                            if (empty($_FILES['profile']['name'])) {
                                $profile = $read['tbl_teacher_profile'];
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
                                $npass = md5("$password");
                            } else {
                                $npass = $oldpassword;
                            }

                            Query("update tbl_teacher SET  tbl_teacher_linkdin='$linkdin',tbl_teacher_twitter='$twitter',tbl_teacher_insta='$insta',tbl_teacher_text='$content',tbl_teacher_info='$info',tbl_teacher_postcode='$postcode',tbl_teacher_address='$address',tbl_teacher_phone='$phone',tbl_teacher_lname='$lname',tbl_teacher_fname='$fname',tbl_teacher_lnameen='$lnameen',tbl_teacher_fnameen='$fnameen',tbl_teacher_nationalcode='$nationalcode',tbl_teacher_sex='$sex',tbl_teacher_birthday='$birthday',tbl_teacher_country='$country',tbl_teacher_mobile='$mobile',tbl_teacher_state='$state',tbl_teacher_email='$email',tbl_teacher_profile='$profile',tbl_teacher_status = '$status',tbl_teacher_password='$npass' where tbl_teacher_id = '$id' ");
                            $_SESSION['error'] = $lang[391];
                            header("Location: teacher.php");
                            exit();
                        }
                        // end = update item
                    }
                    echo '</div></div></div></section>';
                    include './footer.inc.php';
                    ?>