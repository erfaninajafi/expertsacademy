<?php
include './includer.php';
$pagetitle = 'مدیریت زبان انگلیسی';
$Tabpage = '6';
$thisaccess = '1';
include './header.inc.php';
$fname = "../include_site/lang/en_lng.php";
$fhandle = fopen($fname, "r");

?>
<div class="row gx-3 mb-3">
    <div class="col-xxl-12 col-xl-12">
        <div class="card">
            <div class="card-header border-bottom border-200 px-0">
                <div class="d-lg-flex justify-content-between">
                    <div class="row flex-between-center gy-2 px-x1">
                        <div class="col-auto pe-0">
                            <h4 class="mb-0"><?= $pagetitle ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <?= (!empty($error) ? '<div class="alert alert-success m-3" role="alert">' . $error . '</div>' : ''); ?>
            <?php

            echo '<form action="?do=update" method="POST" class="p-4">
            <div class="form-group">
                <textarea class="form-control" rows="20" name="content" style="height:400px;text-align: left;direction: ltr;">';
            while (!feof($fhandle)) {
                echo fgets($fhandle);
            }
            echo '</textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary mt-3">
            </div>
        </form>';
            if (isset($do) && $do == 'update') {
                if (isset($_POST['submit'])) {
                    $fname = "../include_site/lang/en_lng.php";
                    $content = $_POST['content'];
                    $fhandle = fopen($fname, "w");
                    fwrite($fhandle, $content);
                    fclose($fhandle);
                    $_SESSION['error'] = $lang[21];
                }
                header('Location: lng_siteen.php');
            }
            ?>

        </div>
    </div>
</div>
<?php
include './footer.inc.php';

?>