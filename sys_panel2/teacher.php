<?php
// include
include './includer.php';
$show_per_page = '30';
$pagetitle = 'لیست اساتید';
$Tabpage = '25';
$thisaccess = '1,4';
include './header.inc.php';
if (isset($_GET['status'])) {
    $stt = $_GET['status'];
    $status = "tbl_teacher_status='$stt' and";
    $link = '?status=' . $stt . '&';
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
                            <?=(!isset($do) ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
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
                        $result_for_count = Query("SELECT * From tbl_teacher Where $status (tbl_teacher_fname LIKE '%$keyword%' or tbl_teacher_lname LIKE '%$keyword%' or tbl_teacher_lnameen LIKE '%$keyword%' or tbl_teacher_fnameen LIKE '%$keyword%' or tbl_teacher_email LIKE '%$keyword%' or tbl_teacher_mobile LIKE '%$keyword%' or tbl_teacher_nationalcode LIKE '%$keyword%' or tbl_teacher_username LIKE '%$keyword%' or (tbl_teacher_lname LIKE '%$keyword%' and tbl_teacher_fname LIKE '%$keyword%')) ");
                        $count_result = mysqli_num_rows($result_for_count);
                        $start_contnet =  ($page - 1) * $show_per_page;
                        $page_number = ceil($count_result / $show_per_page);
                        // end count db result for pagination               
                        $result = Query("SELECT * From tbl_teacher Where $status (tbl_teacher_fname LIKE '%$keyword%' or tbl_teacher_lname LIKE '%$keyword%' or tbl_teacher_lnameen LIKE '%$keyword%' or tbl_teacher_fnameen LIKE '%$keyword%' or tbl_teacher_email LIKE '%$keyword%' or tbl_teacher_mobile LIKE '%$keyword%' or tbl_teacher_nationalcode LIKE '%$keyword%' or tbl_teacher_username LIKE '%$keyword%' or (tbl_teacher_lname LIKE '%$keyword%' and tbl_teacher_fname LIKE '%$keyword%')) ORDER BY tbl_teacher_id DESC LIMIT $start_contnet,$show_per_page ");
                        // error : if not find record show no item for view
                        $row = mysqli_num_rows($result);
                        if (!isset($do) && $row == '0') {
                            echo '<div class="alert alert-warning m-3" role="alert">' . $lang[57] . '</div>';
                        }
                        // start show list
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>'.$lang[1339].'</th>
                                    <th>' . $lang[95] . '</th>
                                    <th>'.$lang[378].'</th>
                                    <th>'.$lang[385].'</th>
                                    <th>'.$lang[395].'</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $thisId = $read['tbl_teacher_id'];
                            $thisbakhsh = explode(",", $read['tbl_teacher_bakhsh']);
                            $all_bakhsh = '';
                            foreach ($thisbakhsh as $key => $value) {
                                if (!empty($value)) {
                                    $result_skill = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default'");
                                    $read_skill = mysqli_fetch_array($result_skill);
                                    $all_bakhsh .= $read_skill['tbl_categories_title'] . '<br>';
                                }
                            }

                            $result_class = Query("SELECT * From tbl_class where tbl_class_lng='$language_default' and tbl_class_teacher like '%[$thisId]%' ");
                            echo '<tr>
                            <td class="align-middle p-2">' . $i . '</td>
                            <td class="align-middle p-2"><a href="?do=edit&id=' . $read['tbl_teacher_id'] . '">' . $read['tbl_teacher_fname'] . ' ' . $read['tbl_teacher_lname'] . '</a></td>
                            <td class="align-middle p-2">' . $all_bakhsh . '</td>
                            <td class="align-middle p-2">' . $read['tbl_teacher_email'] . '</td>
                            <td class="align-middle p-2">' . $read['tbl_teacher_mobile'] . '</td>
                            <td class="align-middle p-2">' . $read['tbl_teacher_nationalcode'] . '</td>
                            <td class="align-middle p-2"><a href="class.php?teacher=' . $read['tbl_teacher_id'] . '" target="_blank">' . toPersianNum(mysqli_num_rows($result_class)) . '</a></td>
                            <td class="align-middle p-2">
                            <a href="?do=edit&id=' . $read['tbl_teacher_id'] . '"  class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '" ><span class="bi-pencil-square"></span></a>
                            <a href="?do=delete&id=' . $read['tbl_teacher_id'] . '" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                            <a href="misc.php?t=20&teacher=' . $read['tbl_teacher_id'] . '" class="btn me-1 mb-1 btn-sm btn-warning" target="_blank" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[41] . '"><span class="bi-newspaper"></span></a>
                            </td></tr>';
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
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[75] ?></a>
                                    </li>
                                    <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                    </li>
                                    <li><a class="page-link" href="<?= $link . 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword ?>"><?= $lang[74] ?></a>
                                    </li>
                                </ul>
                            </td>
                            <td class="col-lg-3">
                                <form action="teacher.php?do=gotopage" class="search-form" method="POST">
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
                        echo '<div class="col-lg-6 col-12 p-3"><p>' . $lang[25] . '</p><h3>' . $read['tbl_teacher_fname'] . ' ' . $read['tbl_teacher_lname'] . '</h3><br />
                         <form action=" " method="POST">
                         <div class="form-group margin-top">
                         <input type="hidden" value="' . $id . '" name="id" >
                         <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                         <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3"></form></div>';
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
                        echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=additem" method="POST" enctype="multipart/form-data">';
                        ?>
                        <div class="form-group">
                            <label><?=$lang[1339]?></label><select name="bakhsh[]" style="width: 100%;" class="select2" multiple="multiple">
                                <option value="0"><?=$lang[70]?></option>
                                <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option ' . (!empty($_GET['skill']) && $_GET['skill'] == $read_access['tbl_categories_code'] ? 'selected' : '') . ' value="' . $read_access['tbl_categories_code'] . '">' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="lastname"><?= $lang[127] ?></label>
                            <input class="form-control" name="fname" type="text" required="">
                        </div>
                        <div class="form-group">
                            <label for="lastname"><?= $lang[128] ?></label>
                            <input class="form-control" name="lname" type="text" required="">
                        </div>
                        <div class="form-group">
                            <label for="lastname"><?=$lang[1473]?></label>
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

                    
                        <div class="form-group">
                            <label><?= $lang[147] ?></label>
                            <input class="form-control" name="address" maxlength="200" type="text">
                        </div>
                        <div class="form-group">
                            <label><?= $lang[139] ?></label>
                            <input class="form-control" name="postcode" maxlength="10" type="text">
                        </div>
                        <div class="form-group">
                            <label><?=$lang[132]?></label>
                            <input class="form-control" name="nationalcode" maxlength="10" type="text">
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
                            <label for="lastname"><?=$lang[718]?></label>
                            <input class="form-control" name="password" type="password">
                        </div>
                        <div class="form-group">
                            <label for="lastname"><?= $lang[365] ?></label>
                            <input class="form-control" name="profile" data-parsley-group="block-1" type="file" value="">
                        </div>

                    <?php
                        echo '<div class="form-group">
                <input type="submit" name="additem" value="' . $lang[22] . '" class="btn btn-primary mt-3">
                </div></div>
              </form></div>';
                    }
                    if (isset($do) && $do == 'additem') {
                        if (isset($_POST['additem'])) {
                            $id = $_POST['id'];
                            $password = $_POST['password'];

                            $bakhsh_orginal = $_POST['bakhsh'];
                            $bakhsh = '';
                            foreach ($bakhsh_orginal as $key => $value) {
                                if (!empty($value)) {
                                    $bakhsh .= $value . ',';
                                }
                            }

                            if (!empty($password)) {
                                $npass = md5("$password");
                            } else {
                                $npass = '';
                            }

                            $fname = protect($_POST['fname']);
                            $lname = protect($_POST['lname']);
                            $nationalcode = protect($_POST['nationalcode']);
                            $sex = protect($_POST['sex']);
                            $year = protect($_POST['year']);
                            $month = protect($_POST['month']);
                            $linkdin = $_POST['linkdin'];
                            $twitter = $_POST['twitter'];
                            $insta = $_POST['insta'];

                            $day = protect($_POST['day']);
                            $country = protect($_POST['country']);
                            $mobile = protect($_POST['mobile']);
                            $postcode = protect($_POST['postcode']);
                            $state = protect($_POST['state']);
                            $profile = protect($_FILES['profile']);
                            $email = strtolower(protect($_POST['email']));
                            $status = $_POST['status'];
                            $showimage = $_POST['showimage'];
                            $phone = protect($_POST['phone']);
                            $address = protect($_POST['address']);
                            $lang_user = protect($_POST['user_lang']);
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
                                    tbl_teacher_text,
                                    tbl_teacher_bakhsh) 
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
                                    '$content',
                                    '$bakhsh') ");

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

                        echo '<div class="col-lg-10 col-12 p-3">
                          <form action="?do=edititem" method="POST" enctype="multipart/form-data">';
                    ?>
                        <div class="form-group">
                            <label><?=$lang[1339]?></label><select name="bakhsh[]" style="width:100%;" class="select2" multiple="multiple">
                                <option value="0"><?=$lang[70]?></option>
                                <?php
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        $skill_code = $read_access['tbl_categories_code'];
                                        echo '<option value="' . $read_access['tbl_categories_code'] . '" ' . (stripos($read['tbl_teacher_bakhsh'], "$skill_code") !== false ? 'selected' : '') . '>' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    ?>
                            </select>
                        </div>

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
                            <label for="lastname"><?=$lang[1473]?></label>
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

                        <div class="form-group">
                            <label><?= $lang[147] ?></label>
                            <input class="form-control" name="address" value="<?= $read['tbl_teacher_address'] ?>" maxlength="200" type="text">
                        </div>
                        <div class="form-group">
                            <label><?= $lang[139] ?></label>
                            <input class="form-control" name="postcode" value="<?= $read['tbl_teacher_postcode'] ?>" maxlength="10" type="text">
                        </div>
                        <div class="form-group">
                            <label><?=$lang[132]?></label>
                            <input class="form-control" name="nationalcode" maxlength="10" type="text" value="<?= $read['tbl_teacher_nationalcode'] ?>">
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
                    <hr>
                    <?php
                        echo '<div class="form-group">
                <input type="hidden" value = "' . $id . '" name="id" >
                <input type="hidden" value = "' . $read['tbl_teacher_lang'] . '" name="user_lang" >
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
                            $lnameen = protect($_POST['lnameen']);
                            $fnameen = protect($_POST['fnameen']);

                            $bakhsh_orginal = $_POST['bakhsh'];
                            $bakhsh = '';
                            foreach ($bakhsh_orginal as $key => $value) {
                                if (!empty($value)) {
                                    $bakhsh .= $value . ',';
                                }
                            }


                            $nationalcode = protect($_POST['nationalcode']);
                            $sex = protect($_POST['sex']);
                            $linkdin = $_POST['linkdin'];
                            $twitter = $_POST['twitter'];
                            $insta = $_POST['insta'];
                            $year = protect($_POST['year']);
                            $month = protect($_POST['month']);
                            $day = protect($_POST['day']);
                            $country = protect($_POST['country']);
                            $mobile = protect($_POST['mobile']);
                            $oldmobile = protect($_POST['oldmobile']);
                            $postcode = protect($_POST['postcode']);
                            $state = protect($_POST['state']);
                            $profile = protect($_FILES['profile']);
                            $email = strtolower(protect($_POST['email']));
                            $status = $_POST['status'];
                            $showimage = $_POST['showimage'];
                            $password = $_POST['password'];
                            $oldpassword = $_POST['oldpassword'];
                            $phone = protect($_POST['phone']);
                            $address = protect($_POST['address']);
                            $lang_user = protect($_POST['user_lang']);
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

                            Query("update tbl_teacher SET  
                            tbl_teacher_linkdin='$linkdin',
                            tbl_teacher_twitter='$twitter',
                            tbl_teacher_insta='$insta',
                            tbl_teacher_text='$content',
                            tbl_teacher_info='$info',
                            tbl_teacher_postcode='$postcode',
                            tbl_teacher_address='$address',
                            tbl_teacher_phone='$phone',
                            tbl_teacher_lname='$lname',
                            tbl_teacher_fname='$fname',
                            tbl_teacher_lnameen='$lnameen',
                            tbl_teacher_fnameen='$fnameen',
                            tbl_teacher_nationalcode='$nationalcode',
                            tbl_teacher_sex='$sex',
                            tbl_teacher_birthday='$birthday',
                            tbl_teacher_country='$country',
                            tbl_teacher_mobile='$mobile',
                            tbl_teacher_state='$state',
                            tbl_teacher_email='$email',
                            tbl_teacher_profile='$profile',
                            tbl_teacher_status = '$status',
                            tbl_teacher_password='$npass',
                            tbl_teacher_bakhsh='$bakhsh' 
                            where tbl_teacher_id = '$id' ");
                            $_SESSION['error'] = $lang[391];
                            header("Location: teacher.php");
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