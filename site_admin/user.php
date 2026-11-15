<?php
include './includer.php';
$pagetitle = '- ' . $lang[12];
$Tabpage = '6';
$check_level = '0,0';
$pagetitle = $lang[12];
include './header.inc.php';

?>
<div class="col-md-12 mb-12 admin-list-page">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!isset($do)) {
                        echo  '<div class="form-group">
                        ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                </div><div class="admin-list-toolbar">
                    <a class="btn btn-primary" href="?do=add">' . $lang[22] . '</a>
                    <div class="admin-list-toolbar__field">
                        <label for="admin-list-search">جست‌وجو در کاربران</label>
                        <input type="search" id="admin-list-search" data-admin-list-search class="form-control" placeholder="نام کاربری یا نقش">
                    </div>
                    <p class="admin-list-count" data-admin-list-count aria-live="polite"></p>
                </div>
                <p class="admin-list-empty" data-admin-list-empty hidden>کاربری مطابق جست‌وجو پیدا نشد.</p>';
                        $result = Query("SELECT * From tbl_user");
                        // start show lis
                        echo '<table class="table table-bordered table-hover table-striped margin-top" data-admin-list="users" style="text-align:right;">
                        <thead class="table-dark">
                                <tr >
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[2] . '</th>
                                    <th>نقش</th>
                                    <th>' . $lang[63] . '</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $role_labels = array('0' => 'مدیر کل', '1' => 'دبیرخانه', '2' => 'روابط عمومی');
                            $role = $role_labels[(string) $read['tbl_user_status']] ?? 'نامشخص';
                            echo '<tr>
                                        <td style="width: 1%">' . $i . '</td>
                                        <td class="col-sm-4">' . htmlspecialchars((string) $read['tbl_user_username'], ENT_QUOTES, 'UTF-8') . '</td>
                                        <td><span class="admin-list-badge admin-list-badge--reviewed">' . $role . '</span></td>
                                        <td class="col-sm-2 admin-list-actions"><a href="?id=' . $read['tbl_user_id'] . '&do=edit" class="btn btn-xs btn-success" >' . $lang[23] . '</a>
                                        <a href="?id=' . $read['tbl_user_id'] . '&do=delete" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
                                        </tr>';

                            $i++;
                        }
                        echo '</tbody></table>';
                    }
                    // delete user
                    if (isset($do) && $do == 'delete') {
                        echo $lang[25];
                        echo '<div class="col-lg-6 col-lg-offset-6">
          <form action="#" method="POST">
              <input type="hidden" name="id_user" value="' . $id . '" >
              <div class="form-group margin-top">
              <input type="submit" name="submit" value="' . $lang[26] . '" class="btn btn-success">
              <input type="submit" name="nodelete" value="' . $lang[27] . '" class="btn btn-danger">
              </form></div>';

                        if (isset($_POST['submit'])) {
                            $id_user = $_POST['id_user'];

                            if ($id_user == $_SESSION['id_login_' . $get_domain]) {
                                $_SESSION['error'] = $lang[29];
                                header("Location: user.php");
                                exit();
                            }

                            Query("Delete From tbl_user Where tbl_user_id ='$id_user' ");
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
                        $result = Query("SELECT * From tbl_user Where tbl_user_id = '$id' ");
                        $read = mysqli_fetch_array($result);
                        $level = $read['tbl_user_status'];
                        if ($level == 0) {
                            $style_a = "checked";
                        }
                        if ($level == 1) {
                            $style_b = "checked";
                        }
                        if ($level == 2) {
                            $style_c = "checked";
                        }
                        echo '<div class="col-lg-6 col-lg-offset-6">
        <form action="" method="POST" >
            <div class="form-group">
                <label>' . $lang[2] . '</label>
                <input type="text" name="username"  value="' . $read['tbl_user_username'] . '" readonly="readonly" class="form-control">
            </div>
            <div class="form-group">
                <label>' . $lang[3] . '</label>
                <input type="hidden" name="oldpassword" value="' . $read['tbl_user_password'] . '">
                <input type="hidden" name="id_user" value="' . $id . '">
                <input type="password" name="npassword"  value="" class="form-control">
            </div>
            <div class="form-group">
                <label>' . $lang[30] . '</label>
                <input type="text" name="email"  value="' . $read['tbl_user_email'] . '" class="form-control">
            </div>  
            <div class="form-group" style="display:none;">
                مدیر کل<input type="radio" name="level" value="0" ' . @$style_a . '>
                دبیرخانه<input type="radio" name="level" value="1" ' . @$style_b . '>
                روابط عمومی<input type="radio" name="level" value="2" ' . @$style_c . '>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary">
            </div>
</form></div>';
                        if (isset($_POST['submit'])) {
                            $username = $_POST['username'];
                            $oldpassword = $_POST['oldpassword'];
                            $npassword = $_POST['npassword'];
                            $email = $_POST['email'];
                            $id_user = $_POST['id_user'];
                            $level = $_POST['level'];

                            if (empty($npassword)) {
                                Query("Update tbl_user SET tbl_user_status = '$level' , tbl_user_email = '$email' Where tbl_user_id = '$id_user' ");
                                $_SESSION['error'] = $lang[21];
                                header("Location: user.php");
                                exit();
                            } else {
                                $npassword = hash("md5", $npassword);
                                Query("Update tbl_user SET tbl_user_status = '$level' , tbl_user_password = '$npassword' , tbl_user_email = '$email' Where tbl_user_id = '$id_user' ");
                                $_SESSION['error'] = $lang[21];
                                header("Location: user.php");
                                exit();
                            }
                        }
                    }
                    // add user

                    if (isset($do) && $do == 'add') {
                        echo '<div class="col-lg-6 col-lg-offset-6">
        <form action="" method="POST" >
        <div class="form-group">
                <label>' . $lang[2] . '</label>
                <input type="text" name="username"  class="form-control">
            </div>
        <div class="form-group">
                <label>' . $lang[3] . '</label>
                <input type="password" name="password"  class="form-control">
            </div>
        <div class="form-group">
                <label>' . $lang[30] . '</label>
                <input type="text" name="email" class="form-control">
            </div>  
            <div class="form-group"  style="display:none;">
                  مدیر کل<input type="radio" name="level" value="0" checked>
               دبیرخانه<input type="radio" name="level" value="1">
               روابط عمومی<input type="radio" name="level" value="2" >
            </div>
        <div class="form-group">
                <input type="submit" name="submit" value="' . $lang[31] . '" class="btn btn-primary">
            </div>
              </form>
    </div>';
                        if (isset($_POST['submit'])) {
                            $username = $_POST['username'];
                            $password = $_POST['password'];
                            $email = $_POST['email'];
                            $level = $_POST['level'];

                            if (empty($password) || empty($username)) {
                                $_SESSION['error'] = $lang[34];
                                header("Location: user.php");
                                exit();
                            }

                            $result = Query("SELECT * from tbl_user WHERE tbl_user_username = '$username' ");
                            $count = mysqli_num_rows($result);

                            if ($count == '1') {
                                $_SESSION['error'] = $lang[33];
                                header("Location: user.php");
                                exit();
                            } else {
                                $password = hash("md5", $password);
                                Query("Insert into tbl_user (tbl_user_username,tbl_user_password,tbl_user_email,tbl_user_status,tbl_user_active,tbl_user_lng,tbl_user_lastlogin,tbl_user_name) values('$username','$password','$email','$level','1','fa','','') ");
                                $_SESSION['error'] = $lang[32];
                                header("Location: user.php");
                                exit();
                            }
                        }
                    }

                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
echo '<script src="asset/js/listing-ui.js"></script>';
include './footer.inc.php';

?>
