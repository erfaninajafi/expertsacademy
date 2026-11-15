<?php
$readContent = mysqli_fetch_array($resultContent);
?>
<section class="section-job section-content">
    <div class="title-section"><?= $readContent['tbl_textsection_title'] ?> </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="job-text-title">
                    <?= $readContent['tbl_textsection_undertitle'] ?>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <p>
                    <?= $readContent['tbl_textsection_fld1'] ?>
                </p>
                <a href="<?= $readContent['tbl_textsection_fld3'] ?>"><?= $readContent['tbl_textsection_fld2'] ?></a>
            </div>
            <div class=" col-12 col-lg-6">
                <div class="map-job">
                    <img src="<?= $site_url ?>asset_site/images/map.jpg" class="img-fluid" alt="<?= $lang[7] ?>">
                </div>
            </div>
        </div>
    </div>
</section>