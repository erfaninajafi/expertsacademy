<section class="<?= $key == '0' ? 'section-margin-top' : '' ?> full-width-section">
    <div class="container">
        <div class="index-slider">

            <?php
            while ($readContent = mysqli_fetch_array($resultContent)) {
                $file = $readContent['tbl_content_file'];
                $ext = explode(".", $file);
                $img = explode(",", $readContent['tbl_content_img']);
                echo '<div><div class="row justify-content-center">';

            ?>
                <div class="col-12 col-lg-6">
                    <div class="video-modelone">
                        <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                            <video playsinline controls data-poster="<?= $site_url ?>upload_site/<?= $img[0] ?>">
                                <source src="<?= $site_url ?>upload_site/<?= $file ?>" type="video/mp4" />
                            </video>
                        <?php } else { ?>
                            <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readContent['tbl_content_title'] ?>" class="img-fluid">
                        <?php } ?>
                        <figure class="inverse_triangle_one">
                        </figure>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="content-model-one">
                        <?php
                        echo $readContent['tbl_content_text'];

                        ?>
                    </div>
                </div>

            <?php echo '</div></div>';
            } ?>
        </div>
    </div>
    </div>
</section>