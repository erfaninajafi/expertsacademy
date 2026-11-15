<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            while ($readOstad = mysqli_fetch_array($resultContent)) {
            ?>
                <div class="col-12 col-lg-4">
                    <div class="box-content-position">
                        <div class="box-content">
                            <h4><?= $readOstad['tbl_content_title'] ?></h4>
                            <p><?= $readOstad['tbl_content_text'] ?></p>
                            <?= (!empty($readOstad['tbl_content_pageurl']) ? '<a href="' . $readOstad['tbl_content_pageurl'] . '">' . $lang[12] . '</a>' : '') ?>
                        </div>
                        <figure class="inverse_triangle_four"></figure>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</section>