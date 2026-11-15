<?php
include './includer.php';
$pagetitle = '- Language Edit';
$Tabpage = '6';
$thisaccess = '1';
include './header.inc.php';
$fname = "../include/lang/site_fa.php";
$fhandle = fopen($fname, "r");

?>
<div class="col-md-12 mb-12">
    <div class="card">
        <h5 class="card-header"> تغییرات زبان
        </h5>
        <div class="card-body">
            <div class="divider">
                <div class="text-nowrap">
                    <?php

                    echo '
    </section>
        <section class="content">
<div class="box box-body">
        <div class="col-lg-12">
            <form action="?do=update" method="POST">
                <div class="form-group">
                  <div class="error"><span>' . $error . '</span></div>
                </div>
            <div class="form-group">
                <textarea class="form-control" rows="20" name="content" style="height:400px;text-align: left;direction: ltr;">';
                    while (!feof($fhandle)) {
                        echo fgets($fhandle);
                    }
                    echo '</textarea>
            </div>
            <div class="form-group">
                <input type="submit" name="submit" value="' . $lang[20] . '" class="btn btn-primary">
            </div>
        </div>
    </div>';
                    if (isset($do) && $do == 'update') {
                        if (isset($_POST['submit'])) {
                            $fname = "../include/lang/site_fa.php";
                            $content = $_POST['content'];
                            $fhandle = fopen($fname, "w");
                            fwrite($fhandle, $content);
                            fclose($fhandle);
                            $_SESSION['error'] = $lang[21];
                        }
                        header('Location: lng_editorfa.php');
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