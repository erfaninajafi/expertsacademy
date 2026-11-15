<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $key = 0;
            while ($readTab = mysqli_fetch_array($resultContent)) {
                $img = explode(",", $readTab['tbl_content_img']);
                $file = $readTab['tbl_content_file'];
                $ext = explode(".", $readTab['tbl_content_file']);
                if ($key % 2 == 0) {
            ?>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section">
                            <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                            <?= $readTab['tbl_content_text'] ?>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="video-modeltwo">
                            <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                <video playsinline controls data-poster="<?= $site_url ?>upload_site/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload_site/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>" class="img-fluid">
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
                                <video playsinline controls data-poster="<?= $site_url ?>upload_site/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload_site/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>" class="img-fluid">
                            <?php } ?>
                            <figure class="inverse_triangle_six" style="<?= (!empty($readTab['tbl_content_color']) ? 'border-bottom-color:' . $readTab['tbl_content_color'] . ';' : '') ?>">

                            </figure>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section">
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