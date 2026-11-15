<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-text-middle row">
                    <?php
                    $readPage = mysqli_fetch_array($resultContent);
                    $img = (!empty($readPage['tbl_content_img']) ? explode(",", $readPage['tbl_content_img']) : '');
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one"><h1 class="mb-3 orang">' . (!empty($readPage['tbl_content_title']) ? $readPage['tbl_content_title'] : '') . '</h1>' . (!empty($readPage['tbl_content_text']) ? $readPage['tbl_content_text'] : '') . '</div></div>';
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . (!empty($img[0]) ? $site_url . 'upload_site/' . $img[0] : '') . '" alt="' . (!empty($readPage['tbl_content_title']) ? $readPage['tbl_content_title'] : '') . '" class="img-fluid"></div>';
                    ?>
                </div>
            </div>

        </div>
    </div>
</section>