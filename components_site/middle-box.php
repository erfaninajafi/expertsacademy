<section class="section-content section-three-main ">
    <div class="container">
        <div class="row justify-content-center">

            <?php
            $readPage = mysqli_fetch_array($resultContent);
            $file = $readPage['tbl_content_file'];
            $ext = explode(".", $file);
            $img = explode(",", $readPage['tbl_content_img']);
            ?>
            <div class="col-12 col-lg-12">
                <h2><?= $readPage['tbl_content_title'] ?></h2>
            </div>
            <div class="col-12 col-lg-3">
                <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readPage['tbl_content_title'] ?>" class="img-fluid">
            </div>
            <div class="col-12 col-lg-9">
                <div class="content-section">
                    <?= $readPage['tbl_content_text'] ?>
                </div>
            </div>
        </div>
    </div>
</section>