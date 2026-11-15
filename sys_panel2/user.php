<?php
include './includer.php';

$show_per_page = '30';
$Tabpage = '20';
$thisaccess = '1,6,5,7,9';
include './header.inc.php';
if ($_SESSION['user_access'] == 'mentor' && $do == 'edit') {
    $pagetitle = $lang[1701];
} else if ($_SESSION['user_access'] == 'mentor' && $do == 'add') {
    $pagetitle = $lang[1742];
} else if ($_SESSION['user_access'] == 'karfarma' && $do == 'edit') {
    $pagetitle = $lang[1767];
} else if (!empty($do) && $do == 'mentor') {
    $pagetitle = $lang[1815];
} else if (!empty($do) && $do == 'coach') {
    $pagetitle = $lang[1815];
} else {
    $pagetitle = $lang[1311];
}

if (!isset($do) && $access != '1') {
    header("Location: index.php");
    exit();
}

if (!empty($_GET['contract'])) {
    $result_contract = Query("SELECT * From tbl_contract where tbl_contract_id='$_GET[contract]' ");
    $read_contract = mysqli_fetch_array($result_contract);

    $result_project = Query("SELECT tbl_user_name From tbl_user where tbl_user_usercode='$read_contract[tbl_contract_project]'");
    $read_project = mysqli_fetch_array($result_project);

    echo '<div class="col-md-12">
    <div class="box box-body ">
<div class="card mb-3">
    <div class="card-body custom-h5">
      <div class="row justify-content-between align-items-center">
        <div class="col">
          <div class="d-flex">
            <div class="flex-1 fs--1">
              <h5 class="fs-0">' . $lang[1440] . '  : <a href="faragir.php?contract=' . $_GET['contract'] . '">' . $read_contract['tbl_contract_code'] . '</a></h5>
              <h5 class="fs-0">' . $lang[1567] . '  : ' . $read_contract['tbl_contract_number'] . '</h5>
              <h5 class="fs-0">' . $lang[1611] . '  : ' . $read_contract['tbl_contract_customer'] . '</h5>
              <h5 class="fs-0">' . $lang[1612] . '  : ' . $read_contract['tbl_contract_codesection'] . '</h5>
            </div>
            <div class="flex-1 fs--1">
            <h5 class="fs-0">' . $lang[1613] . '  : ' . $read_contract['tbl_contract_namesection'] . '</h5>
            <h5 class="fs-0">' . $lang[1566] . '  : ' . $read_project['tbl_user_name']  . '</h5>
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
                            <?= (!isset($do) && $access == '1' ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>

                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?php if ($access == '1' && empty($do)) { ?>
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
            <?php } ?>
            <div class="card-body p-0 position-relative">
                <?php
                if (!isset($do) && $access == '1') {

                    // start count db result for pagination               
                    $result_for_count = Query("SELECT * From  tbl_user where tbl_user_delete='0' and (tbl_user_usercode like '%$keyword%' or tbl_user_username like '%$keyword%') and tbl_user_contentcreator='0' ");
                    $count_result = mysqli_num_rows($result_for_count);
                    $start_contnet =  ($page - 1) * $show_per_page;
                    $page_number = ceil($count_result / $show_per_page);
                    // end count db result for pagination   

                    $result = Query("SELECT * From tbl_user where tbl_user_delete='0' and (tbl_user_usercode like '%$keyword%' or tbl_user_username like '%$keyword%') and tbl_user_contentcreator='0' order by tbl_user_id DESC LIMIT $start_contnet,$show_per_page");
                    echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[2] . '</th>
                                    <th>' . $lang[92] . '</th>
                                    <th>' . $lang[1392] . '</th>
                                    <th>' . $lang[1966] . '</th>
                                    <th>' . $lang[1967] . '</th>
                                    <th>' . $lang[1339] . '</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                    $i = 1;
                    while ($read = mysqli_fetch_array($result)) {
                        $bakhshall = '';
                        if (!empty($read['tbl_user_bakhsh'])) {
                            $bakhsh = explode(",", $read['tbl_user_bakhsh']);
                            foreach ($bakhsh as $key => $value) {
                                if (!empty($value)) {
                                    $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$value' and tbl_categories_lng='$language_default'");
                                    $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                                    $bakhshall .= @($key + 1) . '-' . $read_bakhsh['tbl_categories_title'] . '<br>';
                                }
                            }
                        }
                        if ($read['tbl_user_karfarma'] == '1') {
                            $thisContract = preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', $read['tbl_user_contract']);
                            $resultProject = Query("select tbl_contract_customer from tbl_contract where tbl_contract_id='$thisContract' ");
                            $readPorject = mysqli_fetch_array($resultProject);
                        }

                        echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_username'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_name'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_usercode'] . '</td>
                                <td class="align-middle p-2">' . ($read['tbl_user_karfarma'] == '1' ? '' : ($read['tbl_user_access'] == 1 ? $lang[1394] : ($read['tbl_user_access'] == 5 ? $lang[1447] : ($read['tbl_user_access'] == 6 ? $lang[1213] : ($read['tbl_user_access'] == 9 ? $lang[1566] : $lang[1826])))) . ($read['tbl_user_access'] == 6 ? '<br>' . $lang[1339] . ' : ' . @$read_bakhsh['tbl_categories_title'] : '')) . '</td>
                                <td class="align-middle p-2">' . role($read['tbl_user_role']) . ($read['tbl_user_karfarma'] == '1' ? 'نماینده کارفرما<br>' . (mysqli_num_rows($resultProject) > 0 ? $readPorject['tbl_contract_customer'] : '') : '') . '</td>
                                <td class="align-middle p-2">' . $bakhshall . '</td>
                                <td class="align-middle p-2">
                                <a href="?id=' . $read['tbl_user_id'] . '&do=edit" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?id=' . $read['tbl_user_id'] . '&do=delete" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                ' . ($read['tbl_user_access'] == 5 || $read['tbl_user_access'] == 6 ? '<a href="?do=' . ($read['tbl_user_access'] == 5 ? 'mentor' : 'coach') . '&id=' . $read['tbl_user_id'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1815] . '"><span class="bi-bar-chart-line-fill"></span></a>' : '') . '
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
                                        <li><a class="page-link" href="?<?= 'page=' . ($back_page_number - 1) . '' . $other_page_keyword . $keyword   ?>"><?= $lang[75] ?></a>
                                        </li>
                                        <li><a class="page-link"><?= $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number ?></a>
                                        </li>
                                        <li><a class="page-link" href="?<?= 'page=' . ($page + 1) . '' . $other_page_keyword . $keyword  ?>"><?= $lang[74] ?></a>
                                        </li>
                                    </ul>
                                </td>
                                <td class="col-lg-3">
                                    <form action="user.php?do=gotopage" class="search-form" method="POST">
                                        <div class="form-group">
                                            <input type="text" class="form-control go-fld" name="page_number">
                                            <button type="submit" name="gotopage" class="btn btn-success"><?= $lang[76] ?></button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    <?php
                    }
                }
                if (isset($do) && $do == 'search') {
                    if (isset($_POST['go'])) {
                        $keyword_search = $_POST['keyword'];
                        header("Location: user.php?keyword=$keyword_search");
                    }
                }

                // delete user
                if (isset($do) && $do == 'delete' && $access == '1') {
                    echo '<div class="col-lg-6 col-12 p-3">' . $lang[25] . '
                <form action="#" method="POST">
                <input type="hidden" name="id_user" value="' . $id . '" >
                <div class="form-group margin-top">
                <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success mt-3">
                <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger mt-3">
                </form></div>';
                    if (isset($_POST['submit'])) {
                        $id_user = $_POST['id_user'];
                        if ($id_user == $_SESSION['id_login_' . $get_domain]) {
                            $_SESSION['error'] = $lang[29];
                            header("Location: user.php");
                            exit();
                        }
                        Query("update tbl_user set tbl_user_delete='1' Where tbl_user_id ='$id_user' ");
                        $_SESSION['error'] = $lang[28];
                        header("Location: user.php");
                        exit();
                    }
                    if (isset($_POST['nodelete'])) {
                        header("Location: user.php");
                    }
                }
                // edit user
                if (isset($do) && $do == 'edit') {
                    $result = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_id = '$id' and tbl_user_contentcreator='0' ");
                    $read = mysqli_fetch_array($result);
                    $getcontract = $read['tbl_user_contract'];
                    $karfarma = $read['tbl_user_karfarma'];
                    echo '<div class="col-lg-6 col-12 p-3"><form action="?do=updateitem" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="old_img" value="' . $read['tbl_user_img'] . '" >
                        <input type="hidden" name="contract" value="' . (!empty($getcontract) ? $getcontract : '') . '">
                        <div class="form-group">
                            <label>' . $lang[92] . '</label>
                            <input type="text" name="name"  value="' . $read['tbl_user_name'] . '" class="form-control">
                        </div>';
                    if ($access == '1' && $karfarma == '0') {
                        echo '<div class="form-group">
                            <label>' . $lang[2] . '</label>
                            <input type="text" name="username"  value="' . $read['tbl_user_username'] . '" readonly="readonly" class="form-control">
                        </div>';

                        echo '<div class="form-group">
                            <label>' . $lang[132] . '</label>
                            <input type="text" name="nationalcode"  value="' . $read['tbl_user_nationalcode'] . '" class="form-control" required maxlength="10">
                        </div>';
                    }
                    echo '<div class="form-group ">
                            <label>' . $lang[3] . '</label>
                            <input type="hidden" name="oldpassword" value="' . $read['tbl_user_username'] . '">
                            <input type="hidden" name="id_user" value="' . $id . '">
                            
                            <input type="password" name="password"  value="" class="form-control">
                        </div>';
                    if ($access == '1' && $karfarma == '0') {
                        echo '<div class="form-group">
                        <label>' . $lang[1343] . ' </label><select name="bakhsh[]" class="form-control selectmulti" multiple>';
                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_categories_code'] . '" ' . (strpos($read['tbl_user_bakhsh'], $read_access['tbl_categories_code']) !== false ? 'selected' : '') . '>' . $read_access['tbl_categories_title'] . '</option>';
                        }
                        echo '</select>
                    </div>';

                        echo '<div class="form-group">
                        <label>' . $lang[1396] . '</label>
                        <input type="text" name="usercode"  class="form-control" value="' . $read['tbl_user_usercode'] . '">
                    </div>';
                    }
                    echo '<div class="form-group">
                        <label>' . $lang[30] . '</label>
                        <input type="text" name="email"  value="' . $read['tbl_user_email'] . '" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>' . $lang[378] . '</label>
                        <input type="text" name="mobile"  value="' . $read['tbl_user_mobile'] . '" class="form-control" maxlength="11">
                    </div>';

                    if ($karfarma == '0') {
                        $img = $read['tbl_user_img'];

                        if ($img) {
                            echo '<div class="form-group file-margin"><table ><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[53] . '</label></td>
                                  <td><input type="file" name="img" class="form-control"></td><td><a href="../upload/' . $img . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                  </td></tr></table></div>';
                        } else {
                            echo '<div class="form-group file-margin">
                                  <label>' . $lang[53] . '</label>
                                  <input type="file" name="img" class="form-control">
                                  </div>';
                        }

                        if ($karfarma == '0' && $access == '1') {
                            echo '<hr><div class="form-group check-style">
                                <label>' . $lang[1966] . '</label>
                    <label><input type="radio" name="access"  value="1" required="" ' . ($read['tbl_user_access'] == 1 ? 'checked' : '') . '> ' . $lang[384] . ' </label>
                    <label><input type="radio" name="access"  value="5" required="" ' . ($read['tbl_user_access'] == 5 ? 'checked' : '') . '> ' . $lang[1447] . '  </label>
                    <label><input type="radio" name="access"  value="6" required="" ' . ($read['tbl_user_access'] == 6 ? 'checked' : '') . '> ' . $lang[1213] . '  </label>
                    <label><input type="radio" name="access"  value="8" required="" ' . ($read['tbl_user_access'] == 8 ? 'checked' : '') . '> ' . $lang[1826] . '  </label>
                    <label><input type="radio" name="access"  value="9" required="" ' . ($read['tbl_user_access'] == 9 ? 'checked' : '') . '> ' . $lang[1566] . '  </label>
                </div>
                <hr><div class="form-group check-style">
                        <label>' . $lang[1967] . '</label>
                        <label><input type="checkbox" name="role[]"  value="1" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "1,") !== false ? 'checked' : '') . '> ' . $lang[384] . ' </label>
                        <label><input type="checkbox" name="role[]"  value="5" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "5,") !== false ? 'checked' : '') . '> ' . $lang[1447] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="6" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "6,") !== false ? 'checked' : '') . '> ' . $lang[1213] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="8" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "8,") !== false ? 'checked' : '') . '> ' . $lang[1826] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="9" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "9,") !== false ? 'checked' : '') . '> ' . $lang[1566] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="10" ' . (!empty($read['tbl_user_role']) && strpos($read['tbl_user_role'], "10,") !== false ? 'checked' : '') . '> ' . $lang[2029] . '  </label>                        
                       </div><hr>

                <div class="form-group check-style">
                    <label><input type="checkbox" name="question"  value="1"  ' . ($read['tbl_user_question'] == 1 ? 'checked' : '') . '> ' . $lang[1397] . '</label>
                    <label><input type="checkbox" name="file"  value="1"  ' . ($read['tbl_user_file'] == 1 ? 'checked' : '') . '> ' . $lang[1398] . ' </label>
                </div>';
                        }
                    }
                    echo '<div class="form-group">
                        <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                    </div></form></div>';
                }
                if (isset($do) && $do == 'updateitem') {
                    if (isset($_POST['submit'])) {
                        $id_user = $_POST['id_user'];
                        $result = Query("SELECT * From tbl_user Where tbl_user_delete='0' and tbl_user_id = '$id_user'  ");
                        $read = mysqli_fetch_array($result);
                        $karfarma = $read['tbl_user_karfarma'];

                        $old_img = $_POST['old_img'];
                        $img = $_FILES['img']['name'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);
                        $contract = (!empty($_POST['contract']) ? $_POST['contract'] : '');

                        if (!empty($img)) {

                            if ($extn == 'swf' || $extn == 'SWF' || $extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG') {
                                $img_name = 'profile_' . time() . '.' . $extn;
                                move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                            }
                        } else {
                            $img_name = $old_img;
                        }

                        if ($access == '6' || $access == '5') {
                            $name = $_POST['name'];
                            $oldpassword = $_POST['oldpassword'];
                            $npassword = $_POST['password'];
                            $email = $_POST['email'];

                            $mobile = $_POST['mobile'];

                            if (empty($npassword)) {
                                Query("Update tbl_user SET 
                                tbl_user_name='$name' ,
                                tbl_user_email = '$email' ,
                                tbl_user_mobile = '$mobile',
                                tbl_user_img = '$img_name' 
                                Where tbl_user_id = '$id_user' ");
                                if (!empty($contract) && $access == '5') {
                                    header("Location: contract.php?do=view&id=$contract");
                                    exit();
                                } else {
                                    header("Location: index.php");
                                    exit();
                                }
                            } else {
                                $npassword = hash("md5", $npassword);
                                Query("Update tbl_user SET 
                                tbl_user_name='$name' , 
                                tbl_user_password = '$npassword' , 
                                tbl_user_email = '$email',
                                tbl_user_mobile = '$mobile',
                                tbl_user_img = '$img_name'
                                Where tbl_user_delete='0' and tbl_user_id = '$id_user' ");
                                if (!empty($contract) && $access == '5') {
                                    header("Location: contract.php?do=view&id=$contract");
                                    exit();
                                } else {
                                    header("Location: index.php");
                                    exit();
                                }
                            }
                        } else {
                            $question = (!empty($_POST['question']) ? $_POST['question'] : '0');
                            $file = (!empty($_POST['file']) ? $_POST['file'] : '0');
                            @$username = $_POST['username'];
                            $thisaccess = ($karfarma == '1' ? '7' : (!empty($_POST['access']) ? $_POST['access'] : $access));


                            $faragirFlag = '0';

                            $roleall = '';
                            if (!empty($_POST['role'])) {
                                $role = $_POST['role'];
                                foreach ($role as $key => $value) {
                                    if (!empty($value) && $thisaccess != $value) {
                                        if ($value == '10') {
                                            $faragirFlag = '1';
                                        }
                                        $roleall .= $value . ',';
                                    }
                                }
                            }

                            $oldpassword = $_POST['oldpassword'];
                            $npassword = $_POST['password'];
                            $email = $_POST['email'];
                            $id_user = $_POST['id_user'];
                            $name = $_POST['name'];
                            $bakhshall = '';
                            if (!empty($_POST['bakhsh'])) {
                                $bakhsh = $_POST['bakhsh'];
                                foreach ($bakhsh as $key => $value) {
                                    if (!empty($value)) {
                                        $bakhshall .= $value . ',';
                                    }
                                }
                            }

                            if ($access != '1') {
                                $res = Query("select * from tbl_user Where tbl_user_id = '$id_user'");
                                $read = mysqli_fetch_array($res);
                                $usercode = $read['tbl_user_usercode'];
                            } else {
                                $usercode = $_POST['usercode'];
                            }
                            $nationalcode = $_POST['nationalcode'];

                            $result = Query("SELECT * from tbl_user WHERE tbl_user_id != '$id_user' and tbl_user_usercode = '$usercode' ");
                            $count = mysqli_num_rows($result);
                            if (!empty($usercode) && $count == '1') {
                                if ($access == '1') {
                                    $_SESSION['error'] = $lang[33];
                                    header("Location: user.php");
                                    exit();
                                }
                            }

                            $mobile = $_POST['mobile'];


                            if ($faragirFlag == '1' && !empty($nationalcode)) {
                                $resultCheck = Query("select * from tbl_member where tbl_member_nationalcode='$nationalcode' and tbl_member_mobile = '$mobile' ");
                                if (mysqli_num_rows($resultCheck) > 0) {
                                    Query("update tbl_member set tbl_member_delete='0' where tbl_member_nationalcode='$nationalcode' and tbl_member_mobile = '$mobile' ");
                                } else {
                                    $uniqid = strtolower(Code("", "no", 8));
                                    Query("Insert into tbl_member (
                                        tbl_member_mobile,
                                        tbl_member_lang,
                                        tbl_member_systemcode,
                                        tbl_member_lname,
                                        tbl_member_delete,
                                        tbl_member_model,
                                        tbl_member_nationalcode) 
                                        values(
                                            '$mobile',
                                            'fa',
                                            '$uniqid',
                                            '$name',
                                            '0',
                                            '0',
                                            '$nationalcode') ");
                                }
                            }

                            if (empty($npassword)) {

                                Query("Update tbl_user SET 
                                tbl_user_usercode='$usercode' ,
                                tbl_user_name='$name' ,
                                tbl_user_access='$thisaccess' , 
                                tbl_user_email = '$email' ,
                                tbl_user_question = '$question' ,
                                tbl_user_file = '$file' ,
                                tbl_user_bakhsh = '$bakhshall',
                                tbl_user_mobile = '$mobile' ,
                                tbl_user_img = '$img_name',
                                tbl_user_role = '$roleall',
                                tbl_user_nationalcode = '$nationalcode'
                                Where tbl_user_id = '$id_user' ");
                                $_SESSION['error'] = $lang[21];
                                header("Location: user.php");
                                exit();
                            } else {

                                $npassword = hash("md5", $npassword);
                                Query("Update tbl_user SET 
                                tbl_user_usercode='$usercode' ,
                                tbl_user_name='$name' , 
                                tbl_user_access='$thisaccess' , 
                                tbl_user_password = '$npassword' , 
                                tbl_user_email = '$email',
                                tbl_user_question = '$question' ,
                                tbl_user_file = '$file'  ,
                                tbl_user_bakhsh = '$bakhshall' ,
                                tbl_user_mobile = '$mobile',
                                tbl_user_img = '$img_name',
                                tbl_user_nationalcode = '$nationalcode'
                                Where tbl_user_id = '$id_user' ");
                                $_SESSION['error'] = $lang[21];
                                header("Location: user.php");
                                exit();
                            }
                        }
                    }
                }
                // add user
                if (isset($do) && $do == 'add' && ($access == '1' || $access == '5'  || $access == '9')) {
                    echo '<div class="col-lg-6 col-12 p-3"><form action="?do=additem" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="contract" value="' . (!empty($_GET['contract']) ? $_GET['contract'] : '') . '">
                        <div class="form-group">
                                <label>' . $lang[92] . '</label>
                                <input type="text" name="name"  class="form-control">
                            </div>
                        <div class="form-group">
                                <label>' . $lang[2] . '</label>
                                <input type="text" name="username"  class="form-control">
                            </div>

                        ' . ($access == '1' ? '<div class="form-group">
                            <label>' . $lang[132] . '</label>
                            <input type="text" name="nationalcode" class="form-control" required maxlength="10">
                        </div>' : '') . '
                        <div class="form-group">
                                <label>' . $lang[3] . '</label>
                                <input type="password" name="password"  class="form-control">
                            </div>';
                    if ($access == '1') {
                        echo '<div class="form-group check-style">
                            <label>' . $lang[1343] . '</label><select name="bakhsh[]" class="form-control selectmulti" multiple>';
                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                        while ($read_access = mysqli_fetch_array($result_access)) {
                            echo '<option value="' . $read_access['tbl_categories_code'] . '" >' . $read_access['tbl_categories_title'] . '</option>';
                        }
                        echo '
                            </select>
                    </div>
                        <div class="form-group">
                            <label>' . $lang[1396] . '</label>
                            <input type="text" name="usercode"  class="form-control">
                        </div>';
                    }
                    echo '<div class="form-group">
                            <label>' . $lang[30] . '</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>' . $lang[378] . '</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11">
                        </div>
                        <div class="form-group file-margin">
                        <label>' . $lang[53] . '</label>
                        <input type="file" name="img" class="form-control">
                        </div>';
                    if (empty($_GET['contract']) && $access != '5') {
                        echo '<hr><div class="form-group check-style">
                        <label>' . $lang[1966] . '</label>
                        <label><input type="radio" name="access"  value="1" required=""> ' . $lang[384] . ' </label>
                        <label><input type="radio" name="access"  value="5" required=""> ' . $lang[1447] . '  </label>
                        <label><input type="radio" name="access"  value="6" required=""> ' . $lang[1213] . '  </label>
                        <label><input type="radio" name="access"  value="8" required="" > ' . $lang[1826] . '  </label>
                        <label><input type="radio" name="access"  value="9" required="" > ' . $lang[1566] . '  </label>
                        
                       </div>
                       <hr><div class="form-group check-style">
                        <label>' . $lang[1967] . '</label>
                        <label><input type="checkbox" name="role[]"  value="1" > ' . $lang[384] . ' </label>
                        <label><input type="checkbox" name="role[]"  value="5" > ' . $lang[1447] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="6" > ' . $lang[1213] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="8" > ' . $lang[1826] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="9" > ' . $lang[1566] . '  </label>
                        <label><input type="checkbox" name="role[]"  value="10" > ' . $lang[2029] . '  </label>
                        
                       </div><hr>
                       <div class="form-group check-style">
                        <label><input type="checkbox" name="question"  value="1" > ' . $lang[1397] . '</label>
                        <label><input type="checkbox" name="file"  value="1" > ' . $lang[1398] . ' </label>
                       </div>';
                    }
                    echo '<div class="form-group">
                                <input type="submit" name="submit" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                            </div></form></div>';
                }
                if (isset($do) && $do == 'additem') {
                    if (isset($_POST['submit'])) {
                        $question = (!empty($_POST['question']) ? $_POST['question'] : '0');
                        $file = (!empty($_POST['file']) ? $_POST['file'] : '0');
                        $username = $_POST['username'];
                        $password = $_POST['password'];
                        $email = $_POST['email'];
                        $usercode = (!empty($_POST['usercode']) ? $_POST['usercode'] : '0');
                        $thisaccess = (!empty($_POST['access']) ? $_POST['access'] : '7');
                        $contract = (!empty($_POST['contract']) ? $_POST['contract'] : '0');

                        $name = $_POST['name'];
                        $mobile = $_POST['mobile'];
                        $nationalcode = $_POST['nationalcode'];

                        $bakhsh = $_POST['bakhsh'];
                        $bakhshall = '';
                        foreach ($bakhsh as $key => $value) {
                            if (!empty($value)) {
                                $bakhshall .= $value . ',';
                            }
                        }

                        $faragirFlag = '0';
                        $role = $_POST['role'];
                        $roleall = '';
                        foreach ($role as $key => $value) {
                            if (!empty($value) && $thisaccess != $value) {
                                if ($value == '10') {
                                    $faragirFlag = '1';
                                }
                                $roleall .= $value . ',';
                            }
                        }

                        if ($access == 5 || $access == 9) {
                            $karfarma = '1';
                            $result = Query("SELECT * from tbl_user WHERE tbl_user_username = '$username' ");
                            $read = mysqli_fetch_array($result);
                            $json = $read['tbl_user_contract'];
                            if (strpos($json, "[$contract],") !== false) {
                                $json =   $json;
                            } else {
                                $json .=   '[' . $contract . '],';
                            }
                            Query("update tbl_user set tbl_user_contract='$json' where  tbl_user_id = '$read[tbl_user_id]' ");
                        } else {
                            $json = '[' . $contract . '],';
                            $karfarma = '0';
                        }

                        $img = $_FILES['img']['name'];
                        $read_file_name = explode(".", $img);
                        $extn = end($read_file_name);
                        // upload images after rename 
                        if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'gif' || $extn == 'GIF' || $extn == 'png' || $extn == 'PNG' || $extn == 'swf' || $extn == 'SWF') {
                            $img_name = 'profile_' . time() . '.' . $extn;
                            move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                        } else {
                            $img_name = '';
                        }

                        if (empty($password) || empty($username)) {
                            $_SESSION['error'] = $lang[34];
                            header("Location: user.php");
                            exit();
                        }
                        $result = Query("SELECT * from tbl_user WHERE tbl_user_username = '$username' ");
                        $count = mysqli_num_rows($result);
                        if ($count == '1') {
                            if ($access == '1') {
                                $_SESSION['error'] = $lang[33];
                                header("Location: user.php");
                                exit();
                            } else {
                                header("Location: contract.php?do=view&id=$contract");
                                exit();
                            }
                        }
                        $result = Query("SELECT * from tbl_user WHERE tbl_user_usercode = '$usercode' ");
                        $count = mysqli_num_rows($result);
                        if (!empty($usercode) && $count == '1') {
                            if ($access == '1') {
                                $_SESSION['error'] = $lang[33];
                                header("Location: user.php");
                                exit();
                            }
                        }
                        $password = hash("md5", $password);
                        if ($faragirFlag == '1' && !empty($nationalcode)) {
                            $resultCheck = Query("select * from tbl_member where tbl_member_nationalcode='$nationalcode' and tbl_member_mobile = '$mobile' ");
                            if (mysqli_num_rows($resultCheck) > 0) {
                                Query("update tbl_member set tbl_member_delete='0' where tbl_member_nationalcode='$nationalcode' and tbl_member_mobile = '$mobile' ");
                            } else {
                                $uniqid = strtolower(Code("", "no", 8));
                                Query("Insert into tbl_member (
                                        tbl_member_mobile,
                                        tbl_member_lang,
                                        tbl_member_systemcode,
                                        tbl_member_lname,
                                        tbl_member_delete,
                                        tbl_member_model,
                                        tbl_member_nationalcode) 
                                        values(
                                            '$mobile',
                                            'fa',
                                            '$uniqid',
                                            '$name',
                                            '0',
                                            '0',
                                            '$nationalcode') ");
                            }
                        }
                        Query("Insert into tbl_user (
                                    tbl_user_username,
                                    tbl_user_name,
                                    tbl_user_password,
                                    tbl_user_email,
                                    tbl_user_access,
                                    tbl_user_usercode,
                                    tbl_user_question,
                                    tbl_user_file,
                                    tbl_user_bakhsh,
                                    tbl_user_mobile,
                                    tbl_user_img,
                                    tbl_user_karfarma,
                                    tbl_user_contract,
                                    tbl_user_contentcreator,
                                    tbl_user_role,
                                    tbl_user_nationalcode,
                                    tbl_user_delete) 
                                    values(
                                        '$username',
                                        '$name',
                                        '$password',
                                        '$email',
                                        '$thisaccess',
                                        '$usercode',
                                        '$question',
                                        '$file',
                                        '$bakhshall',
                                        '$mobile',
                                        '$img_name',
                                        '$karfarma',
                                        '$json',
                                        '0',
                                        '$roleall',
                                        '$nationalcode',
                                        '0') ");
                        $_SESSION['error'] = $lang[32];
                        if ($access == '5') {
                            header("Location: contract.php?do=view&id=$contract");
                            exit();
                        } else {
                            header("Location: user.php");
                            exit();
                        }
                    }
                }


                if (isset($do) && $do == 'mentor' && $access == '1') {
                    ?>

                    <div class="row justify-content-center gx-3 mb-3">
                        <div class="col-xxl-6 col-xl-6 ">
                            <div class=" chart-height">
                                <div class="card-body p-3 position-relative">
                                    <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-3437c979-0fa6-40a3-af4f-6e942a2b3868" id="dom-3437c979-0fa6-40a3-af4f-6e942a2b3868">
                                        <div class="echart-gauge-grade-chart-example" style="min-height: 300px;" data-echart-responsive="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <?php
                    $finalOp = 0;
                    $resultOpinion = Query("select tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1' and tbl_opinionend_mentor ='$id' ");
                    if (mysqli_num_rows($resultOpinion) > 0) {
                        $allOp = mysqli_num_rows($resultOpinion);

                        $resultOpinion = Query("select SUM(tbl_opinionend_result) AS tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='1' and tbl_opinionend_mentor ='$id' ");
                        $readOpinion = mysqli_fetch_array($resultOpinion);
                        $number = $readOpinion['tbl_opinionend_result'];

                        $finalOp = ($allOp == 0 ? 0 : round(abs($number / $allOp)) / 100);

                        $color = array("0" => "primary", "1" => "danger", "2" => "info", "3" => "success", "4" => "warning", "5" => "dark", "6" => "red", "7" => "orange");
                        $chartArr = array();

                        $resultSection = Query("select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng='$language_default' order by tbl_categories_order DESC , tbl_categories_id DESC ");
                        $s = 0;
                        while ($readSection = mysqli_fetch_array($resultSection)) {
                            $chartArr[$s]["itemStyle"] =   $color[$s];
                            $chartArr[$s]["code"] =   strtoupper($readSection['tbl_categories_catcode']);
                            $chartArr[$s]["name"] =   ' ' . $readSection['tbl_categories_title'] . ' ';
                            $resContract = Query("select * from tbl_contract where tbl_contract_codesection='$readSection[tbl_categories_catcode]' and tbl_contract_mentor='$userCode' ");
                            $chartArr[$s]["value"] =   mysqli_num_rows($resContract);
                            $s++;
                        }
                    }
                    ?>
                    <script>
                        // const canvas = document.getElementById("myCanvas");
                        // const ctx = canvas.getContext("2d");

                        // ctx.font = "30px Arial";


                        var echartsDoughnutChartInit = function echartsDoughnutChartInit() {
                            var $doughnutChartEl = document.querySelector('.echart-doughnut-chart-example');
                            if ($doughnutChartEl) {
                                // Get options from data attribute
                                var userOptions = utils.getData($doughnutChartEl, 'options');
                                var chart = window.echarts.init($doughnutChartEl);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        legend: {
                                            orient: 'vertical',
                                            left: 'left',
                                            textStyle: {
                                                color: utils.getGrays()['1000'],
                                                fontSize: '15px',
                                                Font: 'tahoma'
                                            }
                                        },
                                        series: [{
                                            type: 'pie',
                                            radius: ['50%', '70%'],
                                            center: ['70%', '50%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: false,
                                                position: 'center'
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: [
                                                <?php
                                                foreach ($chartArr as $key => $value) {
                                                    echo " {
                        value: $value[value],
                        name: '$value[name]',
                        itemStyle: {
                        color: utils.getColor('$value[itemStyle]')
                        }
                     }";
                                                    if ($key != 7) {
                                                        echo ",";
                                                    } else {
                                                    }
                                                }
                                                ?>

                                            ]
                                        }],
                                        tooltip: {
                                            trigger: 'item',
                                            padding: [7, 10],
                                            backgroundColor: utils.getGrays()['100'],
                                            borderColor: utils.getGrays()['300'],
                                            textStyle: {
                                                color: utils.getColors().dark,
                                                fontSize: '12px'
                                            },
                                            borderWidth: 1,
                                            transitionDuration: 0,
                                            axisPointer: {
                                                type: 'none'
                                            }
                                        }
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                            }
                        };


                        var linePaymentChartInit = function linePaymentChartInit() {
                            var $echartsLinePaymentChart = document.querySelector(".echart-line-payment");
                            var dataset = {
                                successful: [
                                    <?php
                                    for ($i = 1402; $i < 1414; $i++) {
                                        $resultMenti = Query("select tbl_memberskill_id from tbl_memberskill where (tbl_memberskill_shayestegi != Null or tbl_memberskill_shayestegi !='' ) and tbl_memberskill_startdate like '%$i%' and tbl_memberskill_mentor='$this_user_id' ");
                                        echo mysqli_num_rows($resultMenti) . ($i == '1414' ? '' : ',');
                                    }
                                    ?>
                                ],
                            };
                            var labels = [
                                <?php
                                for ($i = 1402; $i < 1414; $i++) {
                                    echo '"' . $i . '",';
                                }
                                ?>

                            ];
                            if ($echartsLinePaymentChart) {
                                var userOptions = utils.getData($echartsLinePaymentChart, "options");
                                var chart = window.echarts.init($echartsLinePaymentChart);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        tooltip: {
                                            trigger: "axis",
                                            axisPointer: {
                                                type: "none",
                                            },
                                            padding: [7, 10],
                                            backgroundColor: utils.getGrays()["100"],
                                            borderColor: utils.getGrays()["300"],
                                            borderWidth: 1,
                                            transitionDuration: 0,
                                            formatter: function formatter(params) {
                                                console.log(params);
                                                return ""
                                                    //.concat(params[0].axisValue, " - ")
                                                    .concat(params[0].value, "");
                                            },
                                            textStyle: {
                                                fontWeight: 500,
                                                fontSize: 12,
                                                color: utils.getColors().dark,
                                            },
                                        },
                                        xAxis: {
                                            type: "category",
                                            data: labels,
                                            splitLine: {
                                                show: true,
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                                interval: 0,
                                            },
                                            axisLine: {
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                            },
                                            axisTick: {
                                                show: true,
                                                length: 10,
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                            },
                                            axisLabel: {
                                                color: utils.getGrays()["400"],
                                                fontWeight: 600,
                                                formatter: function formatter(value) {
                                                    return value.substring(0, value.length);
                                                },
                                                fontSize: 12,
                                                interval: window.innerWidth < 768 ? "auto" : 0,
                                                margin: 15,
                                            },
                                            boundaryGap: false,
                                        },
                                        yAxis: {
                                            type: "value",
                                            axisPointer: {
                                                show: false,
                                            },
                                            splitLine: {
                                                show: false,
                                            },
                                            axisLabel: {
                                                show: false,
                                            },
                                            axisTick: {
                                                show: false,
                                            },
                                            axisLine: {
                                                show: false,
                                            },
                                        },
                                        series: [{
                                            type: "line",
                                            smooth: true,
                                            data: dataset.successful.map(function(d) {
                                                return (d);
                                            }),
                                            symbol: "emptyCircle",
                                            itemStyle: {
                                                color: localStorage.getItem("theme") === "light" ?
                                                    utils.getGrays().white : utils.getColors().primary,
                                            },
                                            lineStyle: {
                                                color: localStorage.getItem("theme") === "light" ?
                                                    utils.rgbaColor(utils.getGrays().white, 0.8) : utils.getColors().primary,
                                            },
                                            areaStyle: {
                                                color: {
                                                    type: "linear",
                                                    x: 0,
                                                    y: 0,
                                                    x2: 0,
                                                    y2: 1,
                                                    colorStops: [{
                                                            offset: 0,
                                                            color: localStorage.getItem("theme") === "light" ?
                                                                "rgba(255, 255, 255, 0.5)" : utils.rgbaColor(utils.getColors().primary, 0.5),
                                                        },
                                                        {
                                                            offset: 1,
                                                            color: localStorage.getItem("theme") === "light" ?
                                                                "rgba(255, 255, 255, 0)" : utils.rgbaColor(utils.getColors().primary, 0),
                                                        },
                                                    ],
                                                },
                                            },
                                            emphasis: {
                                                lineStyle: {
                                                    width: 2,
                                                },
                                            },
                                        }, ],
                                        grid: {
                                            right: 15,
                                            left: 15,
                                            bottom: "15%",
                                            top: 0,
                                        },
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                                utils.resize(function() {
                                    if (window.innerWidth < 768) {
                                        chart.setOption({
                                            xAxis: {
                                                axisLabel: {
                                                    interval: "auto",
                                                },
                                            },
                                        });
                                    }
                                });
                                var selectMenu = document.querySelector("#dashboard-chart-select");
                                if (selectMenu) {
                                    selectMenu.addEventListener("change", function(e) {
                                        var value = e.currentTarget.value;
                                        chart.setOption({
                                            series: [{
                                                data: dataset[value].map(function(d) {
                                                    return (d * 3.14).toFixed(2);
                                                }),
                                            }, ],
                                        });
                                    });
                                }
                            }
                        };

                        //tbl_opinionend_model  2



                        var echartsGaugeGradeChartInit = function echartsGaugeGradeChartInit() {
                            var $gaugeGradeChartEl = document.querySelector('.echart-gauge-grade-chart-example');
                            if ($gaugeGradeChartEl) {
                                // Get options from data attribute
                                var userOptions = utils.getData($gaugeGradeChartEl, 'options');
                                var chart = window.echarts.init($gaugeGradeChartEl);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        series: [{
                                            radius: '100%',
                                            type: 'gauge',
                                            center: ['50%', '70%'],
                                            startAngle: 180,
                                            endAngle: 0,
                                            min: 0,
                                            max: 1,
                                            splitNumber: 8,
                                            axisLine: {
                                                lineStyle: {
                                                    width: 6,
                                                    color: [
                                                        [0.25, utils.getColor('danger')],
                                                        [0.5, utils.getColor('warning')],
                                                        [0.75, utils.getColor('info')],
                                                        [1, utils.getColor('success')]
                                                    ]
                                                }
                                            },
                                            pointer: {
                                                icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
                                                length: '12%',
                                                width: 20,
                                                offsetCenter: [0, '-60%'],
                                                itemStyle: {
                                                    color: 'auto'
                                                }
                                            },
                                            axisTick: {
                                                length: 12,
                                                lineStyle: {
                                                    color: 'auto',
                                                    width: 2
                                                }
                                            },
                                            splitLine: {
                                                length: 20,
                                                lineStyle: {
                                                    color: 'auto',
                                                    width: 5
                                                }
                                            },
                                            axisLabel: {
                                                color: utils.getGrays()['600'],
                                                distance: -60,
                                                formatter: function formatter(value) {
                                                    if (value === 0.875) {
                                                        return 'Excellent';
                                                    }
                                                    if (value === 0.625) {
                                                        return 'Good';
                                                    }
                                                    if (value === 0.375) {
                                                        return 'Well';
                                                    }
                                                    if (value === 0.125) {
                                                        return 'Bad';
                                                    }
                                                    return '';
                                                }
                                            },
                                            title: {
                                                offsetCenter: [0, '-20%'],
                                                color: utils.getGrays()['600']
                                            },
                                            detail: {
                                                offsetCenter: [0, '0%'],
                                                valueAnimation: true,
                                                formatter: function formatter(value) {
                                                    return Math.round(value * 100);
                                                },
                                                color: 'auto'
                                            },
                                            data: [{
                                                value: <?= $finalOp ?>,
                                                name: 'Grade'
                                            }]
                                        }]
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                            }
                        };
                    </script>
                <?php
                }

                if (isset($do) && $do == 'coach' && $access == '1') {
                ?>

                    <div class="row justify-content-center gx-3 mb-3">
                        <div class="col-xxl-6 col-xl-6 ">
                            <div class=" chart-height">
                                <div class="card-body p-3 position-relative">
                                    <div class="tab-pane preview-tab-pane active" role="tabpanel" aria-labelledby="tab-dom-3437c979-0fa6-40a3-af4f-6e942a2b3868" id="dom-3437c979-0fa6-40a3-af4f-6e942a2b3868">
                                        <div class="echart-gauge-grade-chart-example" style="min-height: 300px;" data-echart-responsive="true"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <?php
                    $finalOp = 0;
                    $resultOpinion = Query("select tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='2' and tbl_opinionend_mentor ='$id' ");
                    $allOp = mysqli_num_rows($resultOpinion);

                    $resultOpinion = Query("select SUM(tbl_opinionend_result) AS tbl_opinionend_result from tbl_opinionend where tbl_opinionend_model='2' and tbl_opinionend_mentor ='$id' ");
                    $readOpinion = mysqli_fetch_array($resultOpinion);
                    $number = $readOpinion['tbl_opinionend_result'];

                    $finalOp = ($allOp == 0 ? 0 : round(abs($number / $allOp)) / 100);

                    $color = array("0" => "primary", "1" => "danger", "2" => "info", "3" => "success", "4" => "warning", "5" => "dark", "6" => "red", "7" => "orange");
                    $chartArr = array();

                    $resultSection = Query("select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng='$language_default' order by tbl_categories_order DESC , tbl_categories_id DESC ");
                    $s = 0;
                    while ($readSection = mysqli_fetch_array($resultSection)) {
                        $chartArr[$s]["itemStyle"] =   $color[$s];
                        $chartArr[$s]["code"] =   strtoupper($readSection['tbl_categories_catcode']);
                        $chartArr[$s]["name"] =   ' ' . $readSection['tbl_categories_title'] . ' ';
                        $resContract = Query("select * from tbl_contract where tbl_contract_codesection='$readSection[tbl_categories_catcode]' and tbl_contract_mentor='$userCode' ");
                        $chartArr[$s]["value"] =   mysqli_num_rows($resContract);
                        $s++;
                    }


                    ?>
                    <script>
                        // const canvas = document.getElementById("myCanvas");
                        // const ctx = canvas.getContext("2d");

                        // ctx.font = "30px Arial";


                        var echartsDoughnutChartInit = function echartsDoughnutChartInit() {
                            var $doughnutChartEl = document.querySelector('.echart-doughnut-chart-example');
                            if ($doughnutChartEl) {
                                // Get options from data attribute
                                var userOptions = utils.getData($doughnutChartEl, 'options');
                                var chart = window.echarts.init($doughnutChartEl);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        legend: {
                                            orient: 'vertical',
                                            left: 'left',
                                            textStyle: {
                                                color: utils.getGrays()['1000'],
                                                fontSize: '15px',
                                                Font: 'tahoma'
                                            }
                                        },
                                        series: [{
                                            type: 'pie',
                                            radius: ['50%', '70%'],
                                            center: ['70%', '50%'],
                                            avoidLabelOverlap: false,
                                            label: {
                                                show: false,
                                                position: 'center'
                                            },
                                            labelLine: {
                                                show: false
                                            },
                                            data: [
                                                <?php
                                                foreach ($chartArr as $key => $value) {
                                                    echo " {
                        value: $value[value],
                        name: '$value[name]',
                        itemStyle: {
                        color: utils.getColor('$value[itemStyle]')
                        }
                     }";
                                                    if ($key != 7) {
                                                        echo ",";
                                                    } else {
                                                    }
                                                }
                                                ?>

                                            ]
                                        }],
                                        tooltip: {
                                            trigger: 'item',
                                            padding: [7, 10],
                                            backgroundColor: utils.getGrays()['100'],
                                            borderColor: utils.getGrays()['300'],
                                            textStyle: {
                                                color: utils.getColors().dark,
                                                fontSize: '12px'
                                            },
                                            borderWidth: 1,
                                            transitionDuration: 0,
                                            axisPointer: {
                                                type: 'none'
                                            }
                                        }
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                            }
                        };


                        var linePaymentChartInit = function linePaymentChartInit() {
                            var $echartsLinePaymentChart = document.querySelector(".echart-line-payment");
                            var dataset = {
                                successful: [
                                    <?php
                                    for ($i = 1402; $i < 1414; $i++) {
                                        $resultMenti = Query("select tbl_memberskill_id from tbl_memberskill where (tbl_memberskill_shayestegi != Null or tbl_memberskill_shayestegi !='' ) and tbl_memberskill_startdate like '%$i%' and tbl_memberskill_mentor='$this_user_id' ");
                                        echo mysqli_num_rows($resultMenti) . ($i == '1414' ? '' : ',');
                                    }
                                    ?>
                                ],
                            };
                            var labels = [
                                <?php
                                for ($i = 1402; $i < 1414; $i++) {
                                    echo '"' . $i . '",';
                                }
                                ?>

                            ];
                            if ($echartsLinePaymentChart) {
                                var userOptions = utils.getData($echartsLinePaymentChart, "options");
                                var chart = window.echarts.init($echartsLinePaymentChart);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        tooltip: {
                                            trigger: "axis",
                                            axisPointer: {
                                                type: "none",
                                            },
                                            padding: [7, 10],
                                            backgroundColor: utils.getGrays()["100"],
                                            borderColor: utils.getGrays()["300"],
                                            borderWidth: 1,
                                            transitionDuration: 0,
                                            formatter: function formatter(params) {
                                                console.log(params);
                                                return ""
                                                    //.concat(params[0].axisValue, " - ")
                                                    .concat(params[0].value, "");
                                            },
                                            textStyle: {
                                                fontWeight: 500,
                                                fontSize: 12,
                                                color: utils.getColors().dark,
                                            },
                                        },
                                        xAxis: {
                                            type: "category",
                                            data: labels,
                                            splitLine: {
                                                show: true,
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                                interval: 0,
                                            },
                                            axisLine: {
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                            },
                                            axisTick: {
                                                show: true,
                                                length: 10,
                                                lineStyle: {
                                                    color: utils.rgbaColor("#fff", 0.1),
                                                },
                                            },
                                            axisLabel: {
                                                color: utils.getGrays()["400"],
                                                fontWeight: 600,
                                                formatter: function formatter(value) {
                                                    return value.substring(0, value.length);
                                                },
                                                fontSize: 12,
                                                interval: window.innerWidth < 768 ? "auto" : 0,
                                                margin: 15,
                                            },
                                            boundaryGap: false,
                                        },
                                        yAxis: {
                                            type: "value",
                                            axisPointer: {
                                                show: false,
                                            },
                                            splitLine: {
                                                show: false,
                                            },
                                            axisLabel: {
                                                show: false,
                                            },
                                            axisTick: {
                                                show: false,
                                            },
                                            axisLine: {
                                                show: false,
                                            },
                                        },
                                        series: [{
                                            type: "line",
                                            smooth: true,
                                            data: dataset.successful.map(function(d) {
                                                return (d);
                                            }),
                                            symbol: "emptyCircle",
                                            itemStyle: {
                                                color: localStorage.getItem("theme") === "light" ?
                                                    utils.getGrays().white : utils.getColors().primary,
                                            },
                                            lineStyle: {
                                                color: localStorage.getItem("theme") === "light" ?
                                                    utils.rgbaColor(utils.getGrays().white, 0.8) : utils.getColors().primary,
                                            },
                                            areaStyle: {
                                                color: {
                                                    type: "linear",
                                                    x: 0,
                                                    y: 0,
                                                    x2: 0,
                                                    y2: 1,
                                                    colorStops: [{
                                                            offset: 0,
                                                            color: localStorage.getItem("theme") === "light" ?
                                                                "rgba(255, 255, 255, 0.5)" : utils.rgbaColor(utils.getColors().primary, 0.5),
                                                        },
                                                        {
                                                            offset: 1,
                                                            color: localStorage.getItem("theme") === "light" ?
                                                                "rgba(255, 255, 255, 0)" : utils.rgbaColor(utils.getColors().primary, 0),
                                                        },
                                                    ],
                                                },
                                            },
                                            emphasis: {
                                                lineStyle: {
                                                    width: 2,
                                                },
                                            },
                                        }, ],
                                        grid: {
                                            right: 15,
                                            left: 15,
                                            bottom: "15%",
                                            top: 0,
                                        },
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                                utils.resize(function() {
                                    if (window.innerWidth < 768) {
                                        chart.setOption({
                                            xAxis: {
                                                axisLabel: {
                                                    interval: "auto",
                                                },
                                            },
                                        });
                                    }
                                });
                                var selectMenu = document.querySelector("#dashboard-chart-select");
                                if (selectMenu) {
                                    selectMenu.addEventListener("change", function(e) {
                                        var value = e.currentTarget.value;
                                        chart.setOption({
                                            series: [{
                                                data: dataset[value].map(function(d) {
                                                    return (d * 3.14).toFixed(2);
                                                }),
                                            }, ],
                                        });
                                    });
                                }
                            }
                        };

                        //tbl_opinionend_model  2



                        var echartsGaugeGradeChartInit = function echartsGaugeGradeChartInit() {
                            var $gaugeGradeChartEl = document.querySelector('.echart-gauge-grade-chart-example');
                            if ($gaugeGradeChartEl) {
                                // Get options from data attribute
                                var userOptions = utils.getData($gaugeGradeChartEl, 'options');
                                var chart = window.echarts.init($gaugeGradeChartEl);
                                var getDefaultOptions = function getDefaultOptions() {
                                    return {
                                        series: [{
                                            radius: '100%',
                                            type: 'gauge',
                                            center: ['50%', '70%'],
                                            startAngle: 180,
                                            endAngle: 0,
                                            min: 0,
                                            max: 1,
                                            splitNumber: 8,
                                            axisLine: {
                                                lineStyle: {
                                                    width: 6,
                                                    color: [
                                                        [0.25, utils.getColor('danger')],
                                                        [0.5, utils.getColor('warning')],
                                                        [0.75, utils.getColor('info')],
                                                        [1, utils.getColor('success')]
                                                    ]
                                                }
                                            },
                                            pointer: {
                                                icon: 'path://M12.8,0.7l12,40.1H0.7L12.8,0.7z',
                                                length: '12%',
                                                width: 20,
                                                offsetCenter: [0, '-60%'],
                                                itemStyle: {
                                                    color: 'auto'
                                                }
                                            },
                                            axisTick: {
                                                length: 12,
                                                lineStyle: {
                                                    color: 'auto',
                                                    width: 2
                                                }
                                            },
                                            splitLine: {
                                                length: 20,
                                                lineStyle: {
                                                    color: 'auto',
                                                    width: 5
                                                }
                                            },
                                            axisLabel: {
                                                color: utils.getGrays()['600'],
                                                distance: -60,
                                                formatter: function formatter(value) {
                                                    if (value === 0.875) {
                                                        return 'Excellent';
                                                    }
                                                    if (value === 0.625) {
                                                        return 'Good';
                                                    }
                                                    if (value === 0.375) {
                                                        return 'Well';
                                                    }
                                                    if (value === 0.125) {
                                                        return 'Bad';
                                                    }
                                                    return '';
                                                }
                                            },
                                            title: {
                                                offsetCenter: [0, '-20%'],
                                                color: utils.getGrays()['600']
                                            },
                                            detail: {
                                                offsetCenter: [0, '0%'],
                                                valueAnimation: true,
                                                formatter: function formatter(value) {
                                                    return Math.round(value * 100);
                                                },
                                                color: 'auto'
                                            },
                                            data: [{
                                                value: <?= $finalOp ?>,
                                                name: 'Grade'
                                            }]
                                        }]
                                    };
                                };
                                echartSetOption(chart, userOptions, getDefaultOptions);
                            }
                        };
                    </script>
                <?php
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