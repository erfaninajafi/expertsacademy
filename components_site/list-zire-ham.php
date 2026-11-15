<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <?php
                while ($readIndexBox = mysqli_fetch_array($resultContent)) {
                    $file = $readIndexBox['tbl_content_file'];
                    $ext = explode(".", $readIndexBox['tbl_content_file']);
                    $img = explode(",", $readIndexBox['tbl_content_img']);
                ?>
                    <div class="box-them-one row">
                        <div class="col-12 col-lg-7">
                            <h4 style="display:none;"><?= $readIndexBox['tbl_content_title'] ?></h4>
                            <?= $readIndexBox['tbl_content_text'] ?>
                            <?= (!empty($readIndexBox['tbl_content_pageurl']) ? '<a href="' . $readIndexBox['tbl_content_pageurl'] . '">' . $lang[50] . '</a>' : '') ?>
                        </div>
                        <div class="col-12 col-lg-5">
                            <div class="video-modeltwo">
                                <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                    <video playsinline controls data-poster="<?= $site_url ?>upload_site/<?= $img[0] ?>">
                                        <source src="<?= $site_url ?>upload_site/<?= $file ?>" type="video/mp4" />
                                    </video>
                                <?php } else { ?>
                                    <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readIndexBox['tbl_content_title'] ?>" class="img-fluid">
                                <?php } ?>
                                <figure class="inverse_triangle_two" style="<?= (!empty($readIndexBox['tbl_content_color']) ? 'border-bottom-color:' . $readIndexBox['tbl_content_color'] . ';' : '') ?>">

                                </figure>
                            </div>
                        </div>
                    </div>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</section>