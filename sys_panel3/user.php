<?php
include './includer.php';
$pagetitle = $lang[12];
$Tabpage = '6';
$thisaccess = '1,6';
include './header.inc.php';
?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    if (!isset($do) && $access == '1') {
                        echo  '<div class="form-group">
                        ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                        </div>
                        <a class="btn btn-primary" href="?do=add" >' . $lang[22] . '</a>';
                                        $result = Query("SELECT * From tbl_user");
                                        // start show lis
                        echo '  <table class="table table-bordered table-hover table-striped margin-top" style="text-align:right;">
                            <thead class="table-dark">
                                <tr>
                                    <th>' . $lang[60] . '</th>
                                    <th>' . $lang[2] . '</th>
                                    <th>نام و نام خانوادگی</th>
                                    <th>کدکاربر</th>
                                    <th>نقش</th>
                                </tr>
                            </thead>
                            <tbody>';
                        $i = 1;
                        while ($read = mysqli_fetch_array($result)) {
                            $bakhsh = $read['tbl_user_bakhsh'];
                            $result_bakhsh = Query("SELECT tbl_categories_title From tbl_categories Where tbl_categories_code = '$bakhsh' and tbl_categories_lng='$language_default'");
                            $read_bakhsh = mysqli_fetch_array($result_bakhsh);
                            echo '<tr>
            <td style="width: 1%">' . $i . '</td>
            <td class="col-sm-2">' . $read['tbl_user_username'] . '</td>
            <td class="col-sm-2">' . $read['tbl_user_name'] . '</td>
            <td class="col-sm-2">' . $read['tbl_user_usercode'] . '</td>
            <td class="col-sm-2">' . ($read['tbl_user_access'] == 1 ? 'مدیرکل' : ($read['tbl_user_access'] == 5 ? 'استاد راهنما ' : 'مربی ')) . ($read['tbl_user_access'] == 6 ? '<br>بخش : ' . $read_bakhsh['tbl_categories_title'] : '') . '</td>
            </tr><tr>
            <td class="col-sm-1 btn-class" colspan="5">
            <a href="?id=' . $read['tbl_user_id'] . '&do=edit" class="btn btn-xs btn-warning" >' . $lang[23] . '</a>
            <a href="?id=' . $read['tbl_user_id'] . '&do=delete" class="btn btn-xs btn-danger">' . $lang[24] . '</a></td>
            </tr>';
                            $i++;
                        }
                        echo '</tbody></table>';
                        // end show list
                    }
                    // delete user
                    if (isset($do) && $do == 'delete' && $access == '1') {
                        echo '<div class="col-lg-6">' . $lang[25] . '
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
                        echo '<div class="col-lg-6"><form action="" method="POST" >
                        <div class="form-group">
                            <label>' . $lang[92] . '</label>
                            <input type="text" name="name"  value="' . $read['tbl_user_name'] . '" class="form-control">
                        </div>';
                        if($access == '1'){
                        echo '<div class="form-group">
                            <label>' . $lang[2] . '</label>
                            <input type="text" name="username"  value="' . $read['tbl_user_username'] . '" readonly="readonly" class="form-control">
                        </div>';
                        }
                        echo '<div class="form-group">
                            <label>' . $lang[3] . '</label>
                            <input type="hidden" name="oldpassword" value="' . $read['tbl_user_username'] . '">
                            <input type="hidden" name="id_user" value="' . $id . '">
                            <input type="password" name="password"  value="" class="form-control">
                        </div>';
                        if($access == '1'){
                            echo '<div class="form-group">
                        <label>انتخاب بخش مخصوص مربی </label><select name="bakhsh" class="form-control select2">
                            <option value="0">یک مورد را انتخاب کنید</option>';
                                    $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                    while ($read_access = mysqli_fetch_array($result_access)) {
                                        echo '<option value="' . $read_access['tbl_categories_code'] . '" ' . ($read['tbl_user_bakhsh'] == $read_access['tbl_categories_code'] ? 'selected' : '') . '>' . $read_access['tbl_categories_title'] . '</option>';
                                    }
                                    echo '
                        </select>
                    </div>';
                    
                    echo '<div class="form-group">
                        <label>کد کاربر</label>
                        <input type="text" name="usercode"  class="form-control" value="' . $read['tbl_user_usercode'] . '">
                    </div>';
                    }
                    echo '<div class="form-group">
                        <label>' . $lang[30] . '</label>
                        <input type="text" name="email"  value="' . $read['tbl_user_email'] . '" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>شماره موبایل</label>
                        <input type="text" name="mobile"  value="' . $read['tbl_user_mobile'] . '" class="form-control" maxlength="11">
                    </div>';
                    if($access == '1'){
                                echo '<div class="form-group">
                    <label><input type="radio" name="access"  value="1" required="" ' . ($read['tbl_user_access'] == 1 ? 'checked' : '') . '> مدیر کل </label>
                    <label><input type="radio" name="access"  value="5" required="" ' . ($read['tbl_user_access'] == 5 ? 'checked' : '') . '> استاد راهنما  </label>
                    <label><input type="radio" name="access"  value="6" required="" ' . ($read['tbl_user_access'] == 6 ? 'checked' : '') . '> مربی  </label>
                </div><hr>
                <div class="form-group">
                    <label><input type="checkbox" name="question"  value="1"  ' . ($read['tbl_user_question'] == 1 ? 'checked' : '') . '> امکان افزودن سوال</label>
                    <label><input type="checkbox" name="file"  value="1"  ' . ($read['tbl_user_file'] == 1 ? 'checked' : '') . '> امکان افزودن فایل </label>
                </div>';
                    }
                echo '<div class="form-group">
                        <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary">
                    </div></form></div>';
                        if (isset($_POST['submit'])) {

                            if($access == '6')
                                {
                                    $name = $_POST['name'];
                            $oldpassword = $_POST['oldpassword'];
                            $npassword = $_POST['password'];
                            $email = $_POST['email'];
                            $id_user = $_POST['id_user'];
                            $mobile = $_POST['mobile'];
                            if (empty($npassword)) {
                                Query("Update tbl_user SET 
                                tbl_user_name='$name' ,
                                tbl_user_email = '$email' ,
                                tbl_user_mobile = '$mobile' 
                                Where tbl_user_id = '$id_user' ");
                                    header("Location: index.php");
                                    exit();
                            } else {
                                $npassword = hash("md5", $npassword);
                                Query("Update tbl_user SET 
                                tbl_user_name='$name' , 
                                tbl_user_password = '$npassword' , 
                                tbl_user_email = '$email',
                                tbl_user_mobile = '$mobile'
                                Where tbl_user_id = '$id_user' ");
                                    header("Location: index.php");
                                    exit();
                               
                            }

                                }
                                else {
                            $question = (!empty($_POST['question']) ? $_POST['question'] : '0');
                            $file = (!empty($_POST['file']) ? $_POST['file'] : '0');
                            $username = $_POST['username'];
                            $access = $_POST['access'];
                            $usercode = $_POST['usercode'];
                            $oldpassword = $_POST['oldpassword'];
                            $npassword = $_POST['password'];
                            $email = $_POST['email'];
                            $id_user = $_POST['id_user'];
                            $name = $_POST['name'];
                            $bakhsh = $_POST['bakhsh'];
                            $mobile = $_POST['mobile'];
                            if (empty($npassword)) {
                                Query("Update tbl_user SET 
                                tbl_user_usercode='$usercode' ,
                                tbl_user_name='$name' ,
                                tbl_user_access='$access' , 
                                tbl_user_email = '$email' ,
                                tbl_user_question = '$question' ,
                                tbl_user_file = '$file' ,
                                tbl_user_bakhsh = '$bakhsh',
                                tbl_user_mobile = '$mobile' 
                                Where tbl_user_id = '$id_user' ");
                            
                                    $_SESSION['error'] = $lang[21];
                                    header("Location: user.php");
                                    exit();

                                
                            } else {
                                $npassword = hash("md5", $npassword);
                                Query("Update tbl_user SET 
                                tbl_user_usercode='$usercode' ,
                                tbl_user_name='$name' , 
                                tbl_user_access='$access' , 
                                tbl_user_password = '$npassword' , 
                                tbl_user_email = '$email',
                                tbl_user_question = '$question' ,
                                tbl_user_file = '$file'  ,
                                tbl_user_bakhsh = '$bakhsh' ,
                                tbl_user_mobile = '$mobile'
                                Where tbl_user_id = '$id_user' ");

                                    $_SESSION['error'] = $lang[21];
                                    header("Location: user.php");
                                    exit();
                            }
                        }
                        }
                    }
                    // add user
                    if (isset($do) && $do == 'add' && $access == '1') {
                        echo '<div class="col-lg-6"><form action="" method="POST" >
                        <div class="form-group">
                                <label>' . $lang[92] . '</label>
                                <input type="text" name="name"  class="form-control">
                            </div>
                        <div class="form-group">
                                <label>' . $lang[2] . '</label>
                                <input type="text" name="username"  class="form-control">
                            </div>
                        <div class="form-group">
                                <label>' . $lang[3] . '</label>
                                <input type="password" name="password"  class="form-control">
                            </div>
                            <div class="form-group">
                            <label>انتخاب بخش مخصوص مربی </label><select name="bakhsh" class="form-control select2">
                                <option value="0">یک مورد را انتخاب کنید</option>';
                                        $result_access = Query("Select * from tbl_categories where tbl_categories_type='5' and tbl_categories_lng ='$language_default' order by tbl_categories_id DESC ");
                                        while ($read_access = mysqli_fetch_array($result_access)) {
                                            echo '<option value="' . $read_access['tbl_categories_code'] . '" >' . $read_access['tbl_categories_title'] . '</option>';
                                        }
                                        echo '
                            </select>
                    </div>
                        <div class="form-group">
                            <label>کد کاربر</label>
                            <input type="text" name="usercode"  class="form-control">
                        </div>
                    <div class="form-group">
                            <label>' . $lang[30] . '</label>
                            <input type="text" name="email" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>شماره موبایل</label>
                            <input type="text" name="mobile" class="form-control" maxlength="11">
                        </div>';
                        echo '<div class="form-group">
                        <label><input type="radio" name="access"  value="1" required=""> مدیر کل </label>
                        <label><input type="radio" name="access"  value="5" required=""> استاد راهنما  </label>
                        <label><input type="radio" name="access"  value="6" required=""> مربی  </label>
                       </div><hr>
                       <div class="form-group">
                        <label><input type="checkbox" name="question"  value="1" > امکان افزودن سوال</label>
                        <label><input type="checkbox" name="file"  value="1" > امکان افزودن فایل </label>
                       </div>';
                        echo '<div class="form-group">
                <input type="submit" name="submit" value="' . $lang[31] . '" class="btn btn-primary">
            </div>
              </form></div>';
                        if (isset($_POST['submit'])) {
                            $question = (!empty($_POST['question']) ? $_POST['question'] : '0');
                            $file = (!empty($_POST['file']) ? $_POST['file'] : '0');
                            $username = $_POST['username'];
                            $password = $_POST['password'];
                            $email = $_POST['email'];
                            $usercode = $_POST['usercode'];
                            $access = ($_POST['access']);
                            $name = $_POST['name'];
                            $bakhsh = $_POST['bakhsh'];
                            $mobile = $_POST['mobile'];

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
                                    tbl_user_mobile) 
                                    values(
                                        '$username',
                                        '$name',
                                        '$password',
                                        '$email',
                                        '$access',
                                        '$usercode',
                                        '$question',
                                        '$file',
                                        '$bakhsh',
                                        '$mobile') ");
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
include './footer.inc.php';
?>