<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-12 col-lg-12">
                <div class="section-two">
                    <?php
                    while ($readContent = mysqli_fetch_array($resultContent)) {
                        $img = explode(",", $readContent['tbl_content_img']);
                        echo '<div>' . (!empty($readContent['tbl_content_pageurl']) ? '<a href="' . $readContent['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload_site/' . $img[0] . '" alt="' . $readContent['tbl_content_title'] . '" class="img-fluid">' . (!empty($readContent['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>