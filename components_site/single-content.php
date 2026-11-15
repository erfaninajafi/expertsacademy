<section class="section-margin-top bg-section-two">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $readPage = mysqli_fetch_array($resultContent);
            $img = explode(",", $readPage['tbl_content_img']);
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one padding-top"><h1 class="mb-3 orange">' . $readPage['tbl_content_title'] . '</h1>' . $readPage['tbl_content_text'] . '</div></div>';
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . $site_url . 'upload_site/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid"></div>';
            ?>
        </div>
    </div>
    </div>
</section>