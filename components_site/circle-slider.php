<section class="section-margin-top full-width-section content-model-cyrcle-bg">
    <div class="container">

        <div class="index-slider">

            <?php
            while ($readSlider = mysqli_fetch_array($resultContent)) {
                $file = $readSlider['tbl_content_file'];
                $ext = explode(".", $file);
                $img = explode(",", $readSlider['tbl_content_img']);
                echo '<div><div class="row justify-content-center">';

            ?>
                <div class="col-12 col-lg-6">
                    <div class="img-cyrcle" style="background:url(<?= $site_url . 'upload_site/' . $img[0] ?>);"></div>
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