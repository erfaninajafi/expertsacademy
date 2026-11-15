<?php
include './includer.php';


$Tabpage = '101';
$thisaccess = '1';
include './header.inc.php';
$pagetitle = $lang[1825];

?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle?></h4>
                            <?=(!isset($do) && $access == '1' ? '<div class="plus">
                                <a href="?do=add"><button class="btn btn-primary me-1 mb-1"><span class="far fa-plus-square"></span></button></a>
                            </div>' : '') ?>
                        </div>

                    </div>
                </div>
            </div>
            <?=(!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?=(!isset($do) ? '<div class="bg-holder d-none d-lg-block bg-card" style="background-image:url(assets/img/icons/spot-illustrations/corner-4.png);">
            </div>' : '') ?>
            <div class="card-body p-0 position-relative">
                <?php
                    if (!isset($do) && $access == '1') {

                        
                        $result = Query("SELECT * From tbl_user where tbl_user_contentcreator='1' ");
                        echo '<div class="table-responsive scrollbar">
                        <table class="table table-sm mb-0 fs--1 table-view-tickets">
                            <thead class="text-800 bg-light">
                                <tr>
                                    <th>'.$lang[60].'</th>
                                    <th>'.$lang[92].'</th>
                                    <th>'.$lang[1547].'</th>
                                    <th>'.$lang[378].'</th>
                                    <th>'.$lang[1393].'</th>
                                    <th>'.$lang[63].'</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<tr>
                                <td class="align-middle p-2">' . $i . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_name'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_company'] . '</td>
                                <td class="align-middle p-2">' . $read['tbl_user_mobile'] . '</td>
                                <td class="align-middle p-2">' . ($read['tbl_user_bakhsh'] =='1' ? $lang[1828] : $lang[1829]) . '</td>
                                <td class="align-middle p-2">
                                <a href="?id=' . $read['tbl_user_id'] . '&do=edit" class="btn me-1 mb-1 btn-sm btn-success" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[23] . '"><span class="bi-pencil-square"></span></a>
                                <a href="?id=' . $read['tbl_user_id'] . '&do=delete" class="btn me-1 mb-1 btn-sm btn-danger" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[24] . '"><span class="bi-trash"></span></a>
                                '.($read['tbl_user_access'] == 5 || $read['tbl_user_access'] == 6 ? '<a href="?do='.($read['tbl_user_access'] == 5 ? 'mentor' : 'coach').'&id=' . $read['tbl_user_id'] . '" class="btn me-1 mb-1 btn-sm btn-warning" data-bs-toggle="tooltip" data-bs-placement="top" title="' . $lang[1815] . '"><span class="bi-bar-chart-line-fill"></span></a>' : '' ).'
                                </td>
                                </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
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
                                header("Location: contentcreator.php");
                                exit();
                            }
                            Query("Delete From tbl_user Where tbl_user_id ='$id_user'  ");
                            $_SESSION['error'] = $lang[28];
                            header("Location: contentcreator.php");
                            exit();
                        }
                        if (isset($_POST['nodelete'])) {
                            header("Location: contentcreator.php");
                        }
                    }
                    // edit user
                    if (isset($do) && $do == 'edit' ) {
                        $result = Query("SELECT * From tbl_user Where tbl_user_id = '$id' and tbl_user_contentcreator='1' ");
                        $read = mysqli_fetch_array($result);
                        echo '<div class="col-lg-6 col-12 p-3">
                        <form action="" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_user" value="' . $id . '">
                        <input type="hidden" name="old_img" value="' . $read['tbl_user_img'] . '" >

                        <div class="form-group">
                                <label>' . $lang[92] . '</label>
                                <input type="text" name="name"  class="form-control" value="' . $read['tbl_user_name'] . '">
                            </div>
                            <div class="form-group">
                                <label>' . $lang[1547] . '</label>
                                <input type="text" name="company"  class="form-control" value="' . $read['tbl_user_company'] . '">
                            </div>
                            <div class="form-group">
                                <label>' . $lang[496] . '</label>
                                <input type="text" name="semat"  class="form-control" value="' . $read['tbl_user_semat'] . '">
                            </div>
                            <div class="form-group">
                            <label>' . $lang[30] . '</label>
                            <input type="text" name="email" class="form-control" value="' . $read['tbl_user_email'] . '">
                        </div>
                        <div class="form-group">
                            <label>'.$lang[378].'</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11" value="' . $read['tbl_user_mobile'] . '">
                        </div>';

                    $img = $read['tbl_user_img'];

                    if ($img) {
                        echo '<div class="form-group file-margin"><table ><tr>
                                  <td><label class="col-sm-12 control-label">' . $lang[1827] . '</label></td>
                                  <td><input type="file" name="img" class="form-control"></td><td><a href="../upload/' . $img . '" target="_blank" ><span class="btn btn-success me-1 mb-1 btn-sm bi-eye-fill"></span></a>
                                  </td></tr></table></div>';
                    } else {
                        echo '<div class="form-group file-margin">
                                  <label>' . $lang[1827] . '</label>
                                  <input type="file" name="img" class="form-control">
                                  </div>';
                    }

                            echo '<hr><div class="form-group check-style">
                            <label><input type="radio" name="bakhsh"  value="1" required="" ' . ($read['tbl_user_bakhsh'] == 1 ? 'checked' : '') . '> '.$lang[1828].' </label>
                            <label><input type="radio" name="bakhsh"  value="2" required="" ' . ($read['tbl_user_bakhsh'] == 2 ? 'checked' : '') . '> '.$lang[1829].'  </label>
                        </div>
                        <div class="form-group">
                        <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary mt-3">
                    </div></form></div>';
                        if (isset($_POST['submit'])) {

                            $old_img = $_POST['old_img'];
                            $img = $_FILES['img']['name'];
                            $read_file_name = explode(".", $img);
                            $extn = end($read_file_name);
                            if (!empty($img)) {

                                if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'doc' || $extn == 'DOC' || $extn == 'png' || $extn == 'PNG' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'DOCX' || $extn == 'docx') {
                                    $img_name = 'profile_' . time() . '.' . $extn;
                                    move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                                } 
                            } else {
                                $img_name = $old_img;
                            }

                            $id_user = $_POST['id_user'];
                            $email = $_POST['email'];
                            $name = $_POST['name'];
                            $mobile = $_POST['mobile'];
                            $company = $_POST['company'];
                            $semat = $_POST['semat'];
                            $bakhsh = $_POST['bakhsh'];
                            Query("Update tbl_user SET 
                            tbl_user_name='$name' ,
                            tbl_user_email = '$email' ,
                            tbl_user_semat = '$semat' ,
                            tbl_user_company = '$company',
                            tbl_user_mobile = '$mobile' ,
                            tbl_user_img = '$img_name',
                            tbl_user_bakhsh='$bakhsh'
                            Where tbl_user_id = '$id_user' ");
                                $_SESSION['error'] = $lang[21];
                                header("Location: contentcreator.php");
                                exit();
                        }
                    }
                    // add user
                    if (isset($do) && $do == 'add' && ( $access == '1' || $access == '5' )) {
                        echo '<div class="col-lg-6 col-12 p-3">
                        <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                                <label>' . $lang[92] . '</label>
                                <input type="text" name="name"  class="form-control">
                            </div>
                            <div class="form-group">
                                <label>' . $lang[1547] . '</label>
                                <input type="text" name="company"  class="form-control">
                            </div>
                            <div class="form-group">
                                <label>' . $lang[496] . '</label>
                                <input type="text" name="semat"  class="form-control">
                            </div>
                            <div class="form-group">
                            <label>' . $lang[30] . '</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>'.$lang[378].'</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11">
                        </div>
                        <div class="form-group file-margin">
                        <label>' . $lang[1827] . '</label>
                        <input type="file" name="img" class="form-control">
                        </div>
                        <hr><div class="form-group check-style">
                                <label><input type="radio" name="bakhsh"  value="1" required="" > '.$lang[1828].' </label>
                                <label><input type="radio" name="bakhsh"  value="2" required="" > '.$lang[1829].'  </label>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="submit" value="' . $lang[31] . '" class="btn btn-primary mt-3">
                            </div></form></div>';
                        if (isset($_POST['submit'])) {
                            $email = $_POST['email'];
                            $name = $_POST['name'];
                            $mobile = $_POST['mobile'];
                            $company = $_POST['company'];
                            $semat = $_POST['semat'];
                            $bakhsh = $_POST['bakhsh'];
                            $img = $_FILES['img']['name'];
                            $read_file_name = explode(".", $img);
                            $extn = end($read_file_name);
                            // upload images after rename 
                            if ($extn == 'JPG' || $extn == 'jpg' || $extn == 'jpeg' || $extn == 'JPEG' || $extn == 'doc' || $extn == 'DOC' || $extn == 'png' || $extn == 'PNG' || $extn == 'pdf' || $extn == 'PDF' || $extn == 'DOCX' || $extn == 'docx') {
                                $img_name = 'profile_' . time() . '.' . $extn;
                                move_uploaded_file($_FILES['img']["tmp_name"], "$directory" . $img_name);
                            } else {
                                $img_name ='';
                            }

                                $password = hash("md5", $password);
                                Query("Insert into tbl_user (
                                    tbl_user_name,
                                    tbl_user_email,
                                    tbl_user_mobile,
                                    tbl_user_img,
                                    tbl_user_company,
                                    tbl_user_semat,
                                    tbl_user_contentcreator,
                                    tbl_user_bakhsh) 
                                    values(
                                        '$name',
                                        '$email',
                                        '$mobile',
                                        '$img_name',
                                        '$company',
                                        '$semat',
                                        '1',
                                        '$bakhsh') ");
                                $_SESSION['error'] = $lang[32];
                                header("Location: contentcreator.php");
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