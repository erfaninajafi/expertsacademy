<?php
$readContent = mysqli_fetch_array($resultContent);
?>

<section class="section-content ">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-them-two">
                    <span class="icon-certificate1"></span>
                    <div class="title-section-two">
                        <p><?= $readContent['tbl_textsection_title'] ?></p>
                        <p><?= $readContent['tbl_textsection_fld1'] ?></p>
                    </div>
                    <a href="<?= $readContent['tbl_textsection_fld3'] ?>"><?= $readContent['tbl_textsection_fld2'] ?></a>
                </div>
            </div>
        </div>
    </div>
</section>