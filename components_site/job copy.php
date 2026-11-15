<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="box-text-middle row">
                    <?php
                    $readPage = mysqli_fetch_array($resultContent);
                    $img = (!empty($readPage['tbl_content_img']) ? explode(",", $readPage['tbl_content_img']) : '');
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one"><h1 class="mb-3 orang">' . (!empty($readPage['tbl_content_title']) ? $readPage['tbl_content_title'] : '') . '</h1>' . (!empty($readPage['tbl_content_text']) ? $readPage['tbl_content_text'] : '') . '</div></div>';
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . (!empty($img[0]) ? $site_url . 'upload_site/' . $img[0] : '') . '" alt="' . (!empty($readPage['tbl_content_title']) ? $readPage['tbl_content_title'] : '') . '" class="img-fluid"></div>';
                    ?>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <form class="form-style" action="#" method="post" id="job" enctype="multipart/form-data">
                    <input type="hidden" name="lng" value="<?= $lng ?>">
                    <h3 class="mb-3"><?= $lang[97] ?></h3>
                    <div class="form-group">
                        <input type="text" name="fld1" placeholder="<?= $lang[91] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld2" placeholder="<?= $lang[98] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld3" placeholder="<?= $lang[99] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld4" placeholder="<?= $lang[100] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld5" placeholder="<?= $lang[101] ?>" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <input type="file" name="file" placeholder="<?= $lang[102] ?>" class="form-control">
                    </div>
                    <input type="submit" name="send" value="<?= $lang[103] ?>" class="btn btn-success">
                    <div class="msgform">
                </form>
            </div>
        </div>
    </div>
</section>