<?php
include 'header.inc.php';
?>

<section class="section-margin-top full-width-section content-model-cyrcle-bg">
    <div class="container">

        <div class="index-slider">

            <?php
            $resultSlider = Query("select * from tbl_content where tbl_content_type='22' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
            while ($readSlider = mysqli_fetch_array($resultSlider)) {
                $file = $readSlider['tbl_content_file'];
                $ext = explode(".", $file);
                $img = explode(",", $readSlider['tbl_content_img']);
                echo '<div><div class="row justify-content-center">';

            ?>
                <div class="col-12 col-lg-6">
                    <div class="img-cyrcle" style="background:url(<?= $site_url . 'upload/' . $img[0] ?>);"></div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="content-model-cyrcle">
                        <?php
                        echo $readSlider['tbl_content_text'];
                        ?>
                    </div>
                </div>

            <?php echo '</div></div>';
            } ?>
        </div>
    </div>
    </div>
</section>



<section class="section-content section-why-two">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="title-section-three">
                    <p><?= $lang[82] ?></p>
                    <p><?= $lang[83] ?></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="other-item-list">
                    <?php
                    $resultExcd = Query("select * from tbl_content where tbl_content_type='23' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readExcd = mysqli_fetch_array($resultExcd)) {
                        $file = $readExcd['tbl_content_file'];
                    ?>
                        <a href="<?= $readExcd['tbl_content_pageurl'] ?>">
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
                        <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                            <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                        </video>
                    <?php } else { ?>
                        <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readPage['tbl_content_title'] ?>" class="img-fluid">
                    <?php } ?>
                    <figure class="inverse_triangle_one">

                    </figure>
                </div>
                <a href="<?= $site_url ?>upload/Resume.pdf">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $lang[84] ?></p>
                            <p><?= $lang[86] ?></p>
                        </div>
                    </div>
                </a>
                <a href="<?= $site_url ?>upload/Resume.pdf">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $lang[85] ?></p>
                            <p><?= $lang[87] ?></p>
                        </div>
                    </div>
                </a>
            </div>
            </a>
        </div>
    </div>
    </div>
</section>

<section class="section-content section-three-main ">
    <div class="container">
        <div class="row justify-content-center">

            <?php
            $resultPage = Query("SELECT* From tbl_content Where tbl_content_code='EF2B31C' and tbl_content_fld1='$lng' ");
            $readPage = mysqli_fetch_array($resultPage);
            $file = $readPage['tbl_content_file'];
            $ext = explode(".", $file);
            $img = explode(",", $readPage['tbl_content_img']);
            ?>
            <div class="col-12 col-lg-12">
                <h2><?= $readPage['tbl_content_title'] ?></h2>
            </div>
            <div class="col-12 col-lg-3">
                <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readPage['tbl_content_title'] ?>" class="img-fluid">
            </div>
            <div class="col-12 col-lg-9">
                <div class="content-section">
                    <?= $readPage['tbl_content_text'] ?>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-them-three border-radius-clear">
                    <span class="icon-certificate1"></span>
                    <h3><?= $lang[88] ?></h3>
                </div>
            </div>
            <div class="col-12 col-lg-12">
                <div class="section-three">
                    <?php
                    $resultCustomer = Query("select * from tbl_content where tbl_content_type='24' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readCustomer = mysqli_fetch_array($resultCustomer)) {
                        $img = explode(",", $readCustomer['tbl_content_img']);
                        echo '<div class="open-modal" data-id="' . $readCustomer['tbl_content_code'] . '">' . (!empty($readCustomer['tbl_content_pageurl']) ? '<a href="' . $readCustomer['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readCustomer['tbl_content_title'] . '" class="img-fluid">' . (!empty($readCustomer['tbl_content_pageurl']) ? '</a>' : '') . '<h2>' . $readCustomer['tbl_content_title'] . '</h2></div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>


<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12 title-custom">
                <h3><?= $lang[114] ?></h3>
            </div>
        </div>
    </div>
</section>

<section class="">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='25' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
            $key = 0;
            while ($readTab = mysqli_fetch_array($resultTab)) {
                $img = explode(",", $readTab['tbl_content_img']);
                $file = $readTab['tbl_content_file'];
                $ext = explode(".", $readTab['tbl_content_file']);
                if ($key % 2 == 0) {
            ?>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section link-btn">
                            <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                            <?= $readTab['tbl_content_text'] ?>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="video-modeltwo">
                            <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>">
                            <?php } ?>
                            <figure class="inverse_triangle_two" style="<?= (!empty($readTab['tbl_content_color']) ? 'border-bottom-color:' . $readTab['tbl_content_color'] . ';' : '') ?>">

                            </figure>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="video-modelfour">
                            <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>">
                            <?php } ?>
                            <figure class="inverse_triangle_six" style="<?= (!empty($readTab['tbl_content_color']) ? 'border-bottom-color:' . $readTab['tbl_content_color'] . ';' : '') ?>">

                            </figure>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section link-btn">
                            <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                            <?= $readTab['tbl_content_text'] ?>
                        </div>
                    </div>
            <?php
                }
                $key++;
            }
            ?>

        </div>
    </div>
</section>
<?php
include 'footer.inc.php';
?>