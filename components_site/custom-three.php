<?php
$resultContentTitle = Query("select * from tbl_textsection where tbl_textsection_type='58' and tbl_textsection_lng='$lng' and tbl_textsection_section='مدل کاستومایز سوم عنوان' and tbl_textsection_parent='$thisCode' ");
$readContentTtitle = mysqli_fetch_array($resultContentTitle);
$contentImg = explode(",", $readContentTtitle['tbl_textsection_img']);
?>

<section class="section-content section-why">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="title-section-two">
                    <p><?= $readContentTtitle['tbl_textsection_title'] ?></p>
                    <p><?= $readContentTtitle['tbl_textsection_undertitle'] ?></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="job-list">
                    <?php
                    while ($readExcd = mysqli_fetch_array($resultContent)) {
                        $file = $readExcd['tbl_content_file'];
                    ?>
                        <a href="<?= $site_url .  $lng . '/page/' . $readExcd['tbl_content_url'] ?>/">
                            <div class="tooltipicon" data="<?= $readExcd['tbl_content_text'] ?>">
                                <p><?= $readExcd['tbl_content_title'] ?></p>
                                <span class="icon-arrow-left"></span>
                            </div>
                        </a>
                        <?php
                        ?>
                    <?php } ?>

                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="video-modelone">
                    <img src="<?= $site_url ?>upload_site/<?= $contentImg[0] ?>" class="img-fluid" style="left: 0px;top: 0px;">
                    <figure class="inverse_triangle_one">

                    </figure>
                </div>
                <!-- <a href="<?= $site_url ?>upload_site/<?= $contentImg[1] ?>">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $readContentTtitle['tbl_textsection_fld1'] ?></p>
                            <p><?= $readContentTtitle['tbl_textsection_fld3'] ?></p>
                        </div>
                    </div>
                </a>
                <a href="<?= $site_url ?>upload_site/<?= $contentImg[2] ?>">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $readContentTtitle['tbl_textsection_fld2'] ?></p>
                            <p><?= $readContentTtitle['tbl_textsection_fld4'] ?></p>
                        </div>
                    </div>
                </a> -->
            </div>
            </a>
        </div>
    </div>
    </div>
</section>