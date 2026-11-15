<?php
$resultContentTitle = Query("select * from tbl_textsection where tbl_textsection_type='58' and tbl_textsection_lng='$lng' and tbl_textsection_section='مدل کاستومایز دوم عنوان' and tbl_textsection_parent='$thisCode' ");
$readContentTtitle = mysqli_fetch_array($resultContentTitle);
$contentImg = explode(",", $readContentTtitle['tbl_textsection_img']);

?>

<section class="section-content section-why-two">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="title-section-three">
                    <p><?= $readContentTtitle['tbl_textsection_title'] ?></p>
                    <p><?= $readContentTtitle['tbl_textsection_undertitle'] ?></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="other-item-list">
                    <?php
                    $resultExcd = Query("select * from tbl_content where tbl_content_section='مدل کاستومایز دوم لیست' and tbl_content_parent='$thisCode'  and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readExcd = mysqli_fetch_array($resultExcd)) {
                        $file = $readExcd['tbl_content_file'];
                    ?>
                        <a href="<?= $site_url .  $lng . '/page/' . $readExcd['tbl_content_url'] ?>/">
                            <div>
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
                <?php

                $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='E3C37F9' and tbl_content_fld1='$lng' ");
                $readPage = mysqli_fetch_array($resultPage);
                $file = $readPage['tbl_content_file'];
                $ext = explode(".", $file);
                $img = explode(",", $readPage['tbl_content_img']);
                ?>
                <div class="video-modelone">
                    <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                        <video playsinline controls data-poster="<?= $site_url ?>upload_site/<?= $img[0] ?>">
                            <source src="<?= $site_url ?>upload_site/<?= $file ?>" type="video/mp4" />
                        </video>
                    <?php } else { ?>
                        <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readPage['tbl_content_title'] ?>" class="img-fluid">
                    <?php } ?>
                    <figure class="inverse_triangle_one">

                    </figure>
                </div>
                <a href="<?= $site_url ?>upload_site/<?= $contentImg[1] ?>">
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
                </a>
            </div>
            </a>
        </div>
    </div>
    </div>
</section>