<?php
include './includer.php';
$pagetitle = $lang[14];
$Tabpage = '6';
$check_level = '0,0';
include './header.inc.php';
$result = Query("SELECT * From tbl_setting");
$read = mysqli_fetch_array($result);

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"><?= $pagetitle ?></h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php
                    echo '<div class="col-lg-6 col-lg-offset-6 cms-editor-wrap">
            <form action="?do=update" method="POST" class="cms-editor-form">
                <div class="form-group">
                ' . (!empty($error) ? '<div class="error" ><span>' . $error . '</span></div>' : '') . '
                </div>
            <h6 class="cms-section-heading">عنوان و معرفی سایت</h6>
            <div class="form-group">
                <label>' . $lang[15] . '</label>
                <input type="text" name="title_fa" value="' . $read['tbl_setting_title_fa'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>' . $lang[19] . '</label>
                <input type="text" name="title_en" value="' . $read['tbl_setting_title_en'] . '" class="form-control">
            </div>
             <div class="form-group">
                <label>' . $lang[16] . '</label>
                <input type="text" name="keywords" value="' . $read['tbl_setting_keywords'] . '" class="form-control">
            </div>
             <div class="form-group">
                <label>' . $lang[17] . '</label>
                <input type="text" name="discreption" value="' . $read['tbl_setting_des'] . '" class="form-control">
            </div>
            <h6 class="cms-section-heading">تماس و آدرس سایت</h6>
            <div class="form-group">
                <label>' . $lang[18] . '</label>
                <input type="text" name="email" value="' . $read['tbl_setting_email'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>' . $lang[48] . '</label>
                <input type="text" name="url" value="' . $read['tbl_setting_url'] . '" class="form-control">
            </div>
            <h6 class="cms-section-heading">شبکه‌های اجتماعی</h6>
            <div class="form-group">
                <label>Instagram</label>
                <input type="text" name="insta" value="' . $read['tbl_setting_insta'] . '" class="form-control">
            </div>
            
            <div class="form-group">
                <label>Whatsapp</label>
                <input type="text" name="whatsapp" value="' . $read['tbl_setting_whatsapp'] . '" class="form-control">
            </div>
        <div class="form-group">
            <label>Linkedin</label>
            <input type="text" name="linkedin" value="' . $read['tbl_setting_linkedin'] . '" class="form-control">
        </div>
        <div class="form-group">
            <label>Twitter</label>
            <input type="text" name="twitter" value="' . $read['tbl_setting_twitter'] . '" class="form-control">
        </div>
        <div class="form-group">
            <label>Facebook</label>
            <input type="text" name="facebook" value="' . $read['tbl_setting_facebook'] . '" class="form-control">
        </div>
        <div class="form-group">
            <label>Telegram</label>
            <input type="text" name="telegram" value="' . $read['tbl_setting_telegram'] . '" class="form-control">
        </div>
<div class="form-group">
            <label>Aparat</label>
            <input type="text" name="aparat" value="' . $read['tbl_setting_aparat'] . '" class="form-control">
        </div>
        <div class="form-group">
            <label>Youtube</label>
            <input type="text" name="youtube" value="' . $read['tbl_setting_youtube'] . '" class="form-control">
        </div>
            <div class="cms-save-bar">
                <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary">
                <span class="cms-save-hint">تغییرات پس از انتخاب این دکمه ذخیره می‌شوند.</span>
            </div>
        </form>
    </div>';

                    if (isset($do) && $do == 'update') {

                        if (isset($_POST['submit'])) {
                            $insta = (!empty($_POST['insta']) ? $_POST['insta'] : '');
                            $whatsapp = (!empty($_POST['whatsapp']) ? $_POST['whatsapp'] : '');
                            $linkedin = (!empty($_POST['linkedin']) ? $_POST['linkedin'] : '');
                            $aparat = (!empty($_POST['aparat']) ? $_POST['aparat'] : '');
                            $youtube = (!empty($_POST['youtube']) ? $_POST['youtube'] : '');

                            $title_fa = (!empty($_POST['title_fa']) ? $_POST['title_fa'] : '');
                            $keywords = (!empty($_POST['keywords']) ? $_POST['keywords'] : '');
                            $description = (!empty($_POST['discreption']) ? $_POST['discreption'] : '');
                            $email = (!empty($_POST['email']) ? $_POST['email'] : '');
                            $title_en = (!empty($_POST['title_en']) ? $_POST['title_en'] : '');
                            $url = (!empty($_POST['url']) ? $_POST['url'] : '');
                            $twitter = (!empty($_POST['twitter']) ? $_POST['twitter'] : '');
                            $facebook = (!empty($_POST['facebook']) ? $_POST['facebook'] : '');
                            $telegram = (!empty($_POST['telegram']) ? $_POST['telegram'] : '');

                            Query("Update tbl_setting SET 
                            tbl_setting_insta = '$insta',
                            tbl_setting_whatsapp = '$whatsapp',
                            tbl_setting_linkedin = '$linkedin',
                            tbl_setting_title_fa = '$title_fa' , 
                            tbl_setting_keywords = '$keywords' , 
                            tbl_setting_des ='$description' , 
                            tbl_setting_email = '$email', 
                            tbl_setting_title_en ='$title_en', 
                            tbl_setting_url ='$url',
                            tbl_setting_twitter ='$twitter',
                            tbl_setting_facebook ='$facebook',
                            tbl_setting_aparat ='$aparat',
                            tbl_setting_youtube ='$youtube',
                            tbl_setting_telegram ='$telegram' ");
                            $_SESSION['error'] = $lang[21];
                        }
                        header('Location: setting.php');
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