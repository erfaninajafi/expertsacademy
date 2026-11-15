<?php
include './includer.php';
$pagetitle = $lang[14];
$Tabpage = '6';
$thisaccess = '1';
include './header.inc.php';
$result = Query("SELECT * From tbl_setting");
$read = mysqli_fetch_array($result);
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
                    <?php
                    echo '<div class="col-lg-8 col-12 p-4">
            <form action="?do=update" method="POST">
                <div class="form-group">
                  ' . (!empty($error) ? '<div class="error"><span>' . $error . '</span></div>' : '') . '
                </div>
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
            <div class="form-group">
                <label>' . $lang[18] . '</label>
                <input type="text" name="email" value="' . $read['tbl_setting_email'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>'.$lang[48].'</label>
                <input type="text" name="url" value="' . $read['tbl_setting_url'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>'.$lang[1387].'</label>
                <input type="text" name="toptext" value="' . $read['tbl_setting_toptext'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>'.$lang[1388].'</label>
                <input type="text" name="linktitle" value="' . $read['tbl_setting_linktitle'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>'.$lang[1211].'</label>
                <input type="text" name="link" value="' . $read['tbl_setting_link'] . '" class="form-control">
            </div>
            <div class="form-group">
                <label>'.$lang[62].'</label>
                <input type="text" name="date" value="' . $read['tbl_setting_date'] . '" class="form-control datepicker">
            </div>
            
            <div class="form-group">
                <label>'.$lang[1389].'</label>
                <input type="text" name="title_index" value="' . $read['tbl_setting_title_index'] . '" class="form-control">
            </div>
            <div class="form-group" style="display:none;">
                <label>'.$lang[1390].'</label>
                <textarea rows="4" name="text_index" class="form-control">' . $read['tbl_setting_text_index'] . '</textarea>
            </div>

            <div class="form-group">
                <label>'.$lang[1391].'</label>
                <textarea rows="4" name="text_indexb" class="form-control">' . $read['tbl_setting_text_indexb'] . '</textarea>
            </div>

            
            <div class="form-group">
                <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary mt-3">
            </div>
            </form>
        
    </div>';

                    if (isset($do) && $do == 'update') {
                        $title_fa = '';
                        $keywords = '';
                        $description = '';
                        $email = '';
                        $title_en = '';
                        $url = '';
                        $link = '';
                        $linktitle = '';
                        $toptext = '';
                        $title_index = '';
                        $text_index = '';
                        $text_indexb = '';
                        $date = '';

                        if (isset($_POST['submit'])) {
                            $title_fa = $_POST['title_fa'];
                            $keywords = $_POST['keywords'];
                            $description = $_POST['discreption'];
                            $email = $_POST['email'];
                            $title_en = $_POST['title_en'];
                            $url = $_POST['url'];
                            $toptext = $_POST['toptext'];
                            $linktitle = $_POST['linktitle'];
                            $link = $_POST['link'];
                            $title_index = $_POST['title_index'];
                            $text_index = $_POST['text_index'];
                            $text_indexb = $_POST['text_indexb'];
                            $date = $_POST['date'];

                            Query("Update tbl_setting SET tbl_setting_date= '$date' ,tbl_setting_text_indexb = '$text_indexb' ,tbl_setting_text_index = '$text_index' ,tbl_setting_title_index= '$title_index' ,tbl_setting_link = '$link' ,tbl_setting_linktitle = '$linktitle' ,tbl_setting_toptext = '$toptext' ,tbl_setting_title_fa = '$title_fa' , tbl_setting_keywords = '$keywords' , tbl_setting_des ='$description' , tbl_setting_email = '$email', tbl_setting_title_en ='$title_en', tbl_setting_url ='$url' ");
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