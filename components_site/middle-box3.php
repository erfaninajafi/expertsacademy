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
                <h2><?= strip_tags($readPage['tbl_content_text']) ?></h2>
            </div>
        </div>
    </div>
</section>