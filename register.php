<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
include './includer.php';
$pagetitle = ' - ' . $lang[248];
if (isset($do) && $do == 'register') {
    $title = $lang[213];
} elseif (isset($do) && $do == 'login') {
    $title = $lang[214];
} elseif (isset($do) && $do == 'checklogin') {
    $title = $lang[214];
} elseif (isset($do) && $do == 'forgot') {
    $title = $lang[218];
} else {
    $title = $lang[213];
}
include './header_panel.inc.php';
?>
<div class="row min-vh-100 bg-100">


    <?php
    if (!isset($do) || (isset($do) && $do == 'register')) {
    ?>

        <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
            <div class="row justify-content-center g-0">
                <div class="col-lg-9 col-xl-8 col-xxl-6">
                    <div class="card">
                        <div class="card-header bg-circle-shape bg-shape text-center p-2"><a class="font-normal font-sans-serif fw-bolder fs-4 z-1 position-relative link-light" href="<?= $site_url . $lng ?>/" data-bs-theme="light">Experts Academy</a></div>
                        <div class="card-body p-4">
                            <div class="row flex-between-center">
                                <div class="col-auto">
                                    <h3><?= $lang[1151] ?></h3>
                                </div>
                                <div class="col-auto fs--1 text-600"><span class="mb-0 undefined"><?= $lang[1280] ?></span> <span><a href="<?= $site_url . $lng ?>/register/login/"><?= $lang[125] ?></a></span></div>
                            </div>

                            <?php echo (!empty($_SESSION['error']) ? '<div class="error">' . $_SESSION['error'] . '</div>' : '');
                            if (!empty($_SESSION['error'])) {
                                unset($_SESSION['error']);
                            }
                            ?>

                            <form action="<?= $site_url . $lng ?>/register/reg/" method="POST" class="register" data-parsley-validate="">
                                <div class="mb-3">
                                    <input class="form-control" type="text" name="fname" <?= (!empty($_SESSION['fname']) ? 'value="' . $_SESSION['fname'] . '"' : '') ?> autocomplete="on" required placeholder="<?= $lang[127] ?>" />
                                </div>
                                <div class="mb-3">
                                    <input class="form-control" type="text" name="lname" <?= (!empty($_SESSION['lname']) ? 'value="' . $_SESSION['lname'] . '"' : '') ?> autocomplete="on" required placeholder="<?= $lang[128] ?>" />
                                </div>

                                <?php if ($lng == 'fa') { ?>
                                    <div class="mb-3">
                                        <input class="form-control" type="number" name="mobile" <?= (!empty($_SESSION['mobile']) ? 'value="' . $_SESSION['mobile'] . '"' : '') ?> autocomplete="on" required oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" onKeyDown="if(this.value.length==11 && event.keyCode!=8) return false;" placeholder="<?= $lang[136] ?>" />
                                    </div>
                                <?php } else { ?>
                                    <div class="mb-3">
                                        <input class="form-control" type="number" name="email" <?= (!empty($_SESSION['email']) ? 'value="' . $_SESSION['email'] . '"' : '') ?> autocomplete="on" required placeholder="<?= $lang[30] ?>" />
                                    </div>
                                <?php } ?>



                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="fld_agree" required id="basic-register-checkbox" />
                                    <label class="form-label" for="basic-register-checkbox"><a href="<?= $site_url . $lng ?>/page/76CA4BE/قوانین/" target="_blank"><?= $lang[1155] ?></a></label>
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="add"><?= $lang[1154] ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <?php
    } else if (isset($do) && $do == 'reg') {
        unset($_SESSION['error']);
        $fname = strip_tags(sqi($_POST['fname']));
        $lname = strip_tags(sqi($_POST['lname']));
        if ($lng == 'fa') {
            $mobile = number_convert(strip_tags(sqi($_POST['mobile'])));
            $mobile = toEnglishNum($mobile);
            if (empty($fname) || empty($lname) || empty($mobile)) {
                $_SESSION['error'] = $lang[1167];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }
        } else {
            $email = strip_tags(strtolower(sqi($_POST['email'])));
            if (empty($fname) || empty($lname) || empty($email)) {
                $_SESSION['error'] = $lang[1167];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }
        }

        if ($lng == 'fa') {
            if (!is_numeric($mobile)) {
                $_SESSION['error'] = $lang[1168];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }
            if (strlen($mobile) > 11) {
                $_SESSION['error'] = $lang[1168];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }

            $resultm = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_mobile = '$mobile'  ");
            if (mysqli_num_rows($resultm) >= '1') {
                $_SESSION['error'] = $lang[1169];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }

            $date = jdate('Y/n/j');
            $codeSms = rand(100000, 999999);
            //$codeSms = '123456';
            $time = time();
            $uniqid = strtolower(Code("", "no", 8));
            Query("Insert Into tbl_member (
            tbl_member_lang,
            tbl_member_fname,
            tbl_member_lname,
            tbl_member_mobile,
            tbl_member_status,
            tbl_member_ip,
            tbl_member_regdate,
            tbl_member_logincode,
            tbl_member_time,
            tbl_member_code,
            tbl_member_delete,
            tbl_member_model,
            tbl_member_systemcode) 
            values('$lng','$fname','$lname','$mobile','1','$ip','$date','$codeSms','$time','$uniqid','0','1','$uniqid') ");
            $_SESSION['mobile'] = $mobile;
            //smsNew("$mobile", "$msg");

            Sms("$mobile", "716208", "CODE", "$codeSms");
            $_SESSION['error'] =  $lang[1170] . $mobile . $lang[1171];
        } else {
            if (!isValidEmail($email)) {
                $_SESSION['error'] = $lang[372];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }

            $resultm = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_email = '$email'  ");
            if (mysqli_num_rows($resultm) >= '1') {
                $_SESSION['error'] = $lang[389];
                header("Location: $site_url/$lng/register/register/");
                exit();
            }

            $date = jdate('Y/n/j');
            $codeSms = rand(100000, 999999);
            //$codeSms = '123456';
            $time = time();
            $uniqid = uniqid() . rand(10, 99);
            Query("Insert Into tbl_member (
            tbl_member_lang,
            tbl_member_fname,
            tbl_member_lname,
            tbl_member_email,
            tbl_member_status,
            tbl_member_ip,
            tbl_member_regdate,
            tbl_member_logincode,
            tbl_member_time,
            tbl_member_code,
            tbl_member_delete,
            tbl_member_model,tbl_member_systemcode) values('$lng','$fname','$lname','$email','1','$ip','$date','$codeSms','$time','$uniqid','0','1','$uniqid') ");


            $to = $email;

            $subject = "Login Code ( expertsacademy.ir )";
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: <no-reply@expertsacademy.ir>' . "\r\n";
            if ($lng == 'fa') {
                $direction = "rtl";
            } else {
                $direction = "ltr";
            }

            $message = '<div style="font-family:tahoma;font-size:12px;padding:10px;direction:' . $direction . ';background: #EBEBEB;line-height:25px;">
                    <b>' . $lang[510] . '</b><br>' . $lang[511] . ' : <p>
                    <span style="background:#fff;color:#d51d28;padding:2px 10px;">' . $codeSms . '</span></p>
                    <p> ' . $lang[512] . ' </p><p>https://expertsacademy.ir/' . $lng . '/register/checkcode//</p>
                    <div style="background:#d51d28;color:#fff;padding:8px;">© ' . date("Y") . ' Expertsacademy</div></div>';
            //mail($to, $subject, $message, $headers);
            $_SESSION['email'] = $email;
            $_SESSION['error'] =  $lang[1174] . $email . $lang[1175];
        }
        header("Location: $site_url/$lng/register/checkcode/");
        exit();
    } else if (isset($do) && $do == 'login') {
    ?>
        <?php //echo (!empty($_SESSION['error']) ? '<div class="error">' . $_SESSION['error'] . '</div>' : '');
        // if (isset($do) && $do == 'login') {
        //     unset($_SESSION['error']);
        // } 
        ?>
        <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
            <div class="row justify-content-center g-0 logo-login">
                <a href="#"><img src="<?= $site_url ?>assets/img/full-logo.png"></a>
                <h1 class="login-head">
                    سامانه جامع مهارت آموزی
                    <span>آکادمی کارشناسان</span>
                    <br>
                    "رویکردی برای تعیین هدف و راهبری فرایند یادگیری مهارت"
                </h1>
                <div class="col-lg-8 col-xl-6 col-xxl-5">

                    <div class="card none-shadow">
                        <div class="card-header text-center p-2">
                            <p>
                                کاربر گرامی پس از ارسال اطلاعات خود توسط
                                نماینده سازمان ، شماره همراه خود را وارد نمایید
                            </p>
                        </div>
                        <div class="card-body p-1">
                            <?php echo (!empty($_SESSION['error']) ? '<div class="error">' . $_SESSION['error'] . '</div>' : '');
                            if (!empty($_SESSION['error'])) {
                                unset($_SESSION['error']);
                            }
                            ?>

                            <form action="<?= $site_url . $lng ?>/register/sendcode/" method="POST" class="register" data-parsley-validate="">

                                <?php if ($lng == 'fa') { ?>
                                    <div class="mb-3">
                                        <input class="form-control" type="number" name="mobile" <?= (!empty($_SESSION['mobile']) ? 'value="' . $_SESSION['mobile'] . '"' : '') ?> autocomplete="on" required oninput="this.value = this.value.replace(/[^0-9.]/g, ''); this.value = this.value.replace(/(\..*)\./g, '$1');" onKeyDown="if(this.value.length==11 && event.keyCode!=8) return false;" placeholder="<?= $lang[136] ?>" />
                                    </div>
                                <?php } else { ?>
                                    <div class="mb-3">
                                        <input class="form-control" type="number" name="email" <?= (!empty($_SESSION['email']) ? 'value="' . $_SESSION['email'] . '"' : '') ?> autocomplete="on" required placeholder="<?= $lang[30] ?>" />
                                    </div>
                                <?php } ?>
                                <div class="mb-3">
                                    <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="login"><?= $lang[379] ?></button>
                                </div>
                                <div class="mb-3">
                                    <a href="<?= $site_url . $lng ?>/register/register/" class="btn btn-warning d-block w-100 mt-3"><?= $lang[1154] ?></a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } else if (isset($do) && $do == 'sendcode') {

        unset($_SESSION['error']);
        if (isset($_POST['login'])) {
            if (toEnglishNum(sqi($_POST['mobile'])) != '09123494614') {
                if ($lng == 'fa') {
                    $mobile = toEnglishNum(sqi($_POST['mobile']));
                    if (empty($mobile)) {
                        $_SESSION['error'] = $lang[1159];
                        header("Location: $site_url/$lng/register/login/");
                        exit();
                    }
                    if (!is_numeric($mobile)) {
                        $_SESSION['error'] = $lang[1160];
                        header("Location: $site_url/$lng/register/login/");
                        exit();
                    }
                    if (strlen($mobile) > 11) {
                        $_SESSION['error'] = $lang[1160];
                        header("Location: $site_url/$lng/register/login/");
                        exit();
                    }
                    $result = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_mobile = '$mobile' ");
                } else {
                    $email = strtolower(sqi($_POST['email']));
                    if (!isValidEmail($email)) {
                        $_SESSION['error'] = $lang[372];
                        header("Location: $site_url/$lng/register/login/");
                        exit();
                    }
                    $result = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_email = '$email' ");
                }
                if (mysqli_num_rows($result) == 1) {
                    $read = mysqli_fetch_array($result);
                    $status = $read['tbl_member_status'];
                    $timesend = (!empty($read['tbl_member_time']) ? $read['tbl_member_time'] : '0');
                    $time = time();
                    if (($time - $timesend) < 120) {
                        $_SESSION['error'] = $lang[1162];
                        header("Location: $site_url/$lng/register/checkcode/");
                        exit();
                    } else {
                        unset($_SESSION['error']);
                        $codeSms = rand(100000, 999999);
                        //$codeSms = '123456';
                        if ($lng == 'fa') {
                            Query("update tbl_member set  tbl_member_logincode ='$codeSms' , tbl_member_time='$time' where tbl_member_mobile = '$mobile' ");
                            $_SESSION['mobile'] = $mobile;

                            $msg = $codeSms . " کد ورود شما به پنل کاربری $site_url/$lng/checkcode/";
                            //smsNew("$mobile", "$msg");
                            Sms("$mobile", "716208", "CODE", "$codeSms");
                            $_SESSION['error'] =  $lang[1170] . $mobile . $lang[1171];
                        } else {
                            Query("update tbl_member set  tbl_member_logincode ='$codeSms' , tbl_member_time='$time' where tbl_member_email = '$email' ");
                            $_SESSION['email'] = $email;

                            $to = $email;
                            $subject = "Login Code ( expertsacademy.ir )";
                            $headers = "MIME-Version: 1.0" . "\r\n";
                            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                            $headers .= 'From: <no-reply@expertsacademy.ir>' . "\r\n";
                            if ($lng == 'fa') {
                                $direction = "rtl";
                            } else {
                                $direction = "ltr";
                            }
                            $message = '<div style="font-family:tahoma;font-size:12px;padding:10px;direction:' . $direction . ';background: #EBEBEB;line-height:25px;">
                    <b>' . $lang[510] . '</b><br>' . $lang[511] . ' : <p>
                    <span style="background:#fff;color:#d51d28;padding:2px 10px;">' . $codeSms . '</span></p>
                    <p> ' . $lang[512] . ' </p><p>https://expertsacademy.ir/' . $lng . '/register/checkcode//</p>
                    <div style="background:#d51d28;color:#fff;padding:8px;">© ' . date("Y") . ' Expertsacademy</div></div>';
                            //mail($to, $subject, $message, $headers);


                            $_SESSION['error'] =  $lang[1174] . $email . $lang[1175];
                        }
                        header("Location: $site_url/$lng/register/checkcode/");
                        exit();
                    }
                } else {
                    $_SESSION['error'] = $lang[1163];
                    header("Location: $site_url/$lng/register/login/");
                    exit();
                }
            } else {
                $mobile = toEnglishNum(sqi($_POST['mobile']));
                $result = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_mobile = '$mobile' ");
                $read = mysqli_fetch_array($result);
                $date = jdate("Y/m/d : a:h:s");
                Query("update tbl_member set tbl_member_lastlogin='$date' where tbl_member_delete = '0' and tbl_member_id = '$read[tbl_member_id]' ");
                $_SESSION['userid'] = $read['tbl_member_id'];
                $_SESSION['username'] = $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'];
                if (!empty($_SESSION['show-content'])) {
                    $showContent = $_SESSION['show-content'];
                    unset($_SESSION['show-content']);
                    header("Location: $site_url/$lng/mypanel/show-content/$showContent/");
                    exit();
                } else {
                    header("Location: $site_url/$lng/mypanel/");
                    exit();
                }
            }
        }
    } else if (isset($do) && $do == 'checkcode') {
    ?>
        <div class="col-sm-10 col-md-6 px-sm-0 align-self-center mx-auto py-5">
            <div class="row justify-content-center g-0 logo-login">
                <a href="#"><img src="<?= $site_url ?>assets/img/full-logo.png"></a>
                <h1 class="login-head">
                    سامانه جامع مهارت آموزی
                    <span>آکادمی کارشناسان</span>
                    <br>
                    "رویکردی برای تعیین هدف و راهبری فرایند یادگیری مهارت"
                </h1>
                <div class="col-lg-8 col-xl-6 col-xxl-5">

                    <div class="card none-shadow">
                        <div class="card-header text-center p-2">
                            <p>
                                کاربر گرامی لطفا کد پیامک شده به شماره همراه
                                خود را در کادر زیر وارد نمایید
                            </p>
                        </div>
                        <div class="card-body p-1">


                            <?php echo (!empty($_SESSION['error']) ? '<div class="error">' . $_SESSION['error'] . '</div>' : '');
                            if (!empty($_SESSION['error'])) {
                                unset($_SESSION['error']);
                            }
                            ?>

                            <form action="<?= $site_url . $lng ?>/register/checklogin/" method="POST" class="register" data-parsley-validate="">

                                <div class="mb-3">
                                    <input class="form-control" type="number" name="code" maxlength="6" autocomplete="on" required placeholder="<?= $lang[1281] ?>" />
                                </div>
                                <div class="mb-3">
                                    <button class="btn btn-primary d-block w-100 mt-3" type="submit" name="login"><?= $lang[379] ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php
    } else if (isset($do) && $do == 'checklogin' && (!empty($_SESSION['mobile']) || !empty($_SESSION['email']))) {
        unset($_SESSION['error']);
        if (isset($_POST['login'])) {
            unset($_SESSION['error']);
            $code = toEnglishNum(sqi($_POST['code']));
            //$code = '123456';
            if (empty($code)) {
                $_SESSION['error'] = $lang[1164];
                header("Location: $site_url/$lng/register/checkcode/");
                exit();
            }
            if (!is_numeric($code)) {
                $_SESSION['error'] = $lang[1165];
                header("Location: $site_url/$lng/register/checkcode/");
                exit();
            }
            if (strlen($code) < 6) {
                $_SESSION['error'] = $lang[1165];
                header("Location: $site_url/$lng/register/checkcode/");
                exit();
            }

            if ($lng == 'fa') {
                $mobile = $_SESSION['mobile'];
                $result = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_logincode = '$code' and tbl_member_mobile = '$mobile' ");
                $read = mysqli_fetch_array($result);
            } else {
                $email = $_SESSION['email'];
                $result = Query("select * from tbl_member where tbl_member_delete = '0' and tbl_member_logincode = '$code' and tbl_member_email = '$email' ");
                $read = mysqli_fetch_array($result);
            }
            if ($code == 0) {
                $_SESSION['error'] = $lang[1166];
                header("Location: $site_url/$lng/register/checkcode/");
                exit();
            } else if (mysqli_num_rows($result) == 1) {
                $_SESSION['userid'] = $read['tbl_member_id'];
                if ($lng == 'fa') {
                    $_SESSION['username'] = $read['tbl_member_fname'] . ' ' . $read['tbl_member_lname'];
                } else {
                    $_SESSION['username'] = $read['tbl_member_fnameen'] . ' ' . $read['tbl_member_lnameen'];
                }
                $_SESSION['sex'] = $read['tbl_member_sex'];
                if ($lng == 'fa') {
                    $_SESSION['state'] = $read['tbl_member_state'];
                } else {
                    $_SESSION['state'] = '0000';
                }

                if ($lng == 'fa') {
                    Query("update tbl_member set tbl_member_status='0',tbl_member_logincode='' where tbl_member_mobile = '$mobile'");
                } else {
                    Query("update tbl_member set tbl_member_status='0',tbl_member_logincode='' where tbl_member_email = '$email'");
                }
                unset($_SESSION['mobile']);
                unset($_SESSION['email']);
                unset($_SESSION['error']);

                $date = jdate("Y/m/d : a:h:s");
                Query("update tbl_member set tbl_member_lastlogin='$date' where tbl_member_delete = '0' and tbl_member_id = '$read[tbl_member_id]' ");


                if (!empty($_SESSION['show-content'])) {
                    $showContent = $_SESSION['show-content'];
                    unset($_SESSION['show-content']);
                    header("Location: $site_url/$lng/mypanel/show-content/$showContent/");
                    exit();
                } else {
                    header("Location: $site_url/$lng/mypanel/");
                    exit();
                }
            } else {
                $_SESSION['error'] = $lang[1166];
                header("Location: $site_url/$lng/register/checkcode/");
                exit();
            }
        } else {
            header("Location: $site_url/$lng/register/login/");
            exit();
        }
    }
    ?>
    <div class="col-6 d-none d-lg-block position-relative">
        <div class="bg-holder" style="background-image:url(<?= $site_url ?>assets/img/14.jpg);background-position: 50% 20%;">
        </div>
    </div>
</div>
<?php
include './footer_panel.inc.php';
?>