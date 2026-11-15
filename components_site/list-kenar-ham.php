<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            while ($readContent = mysqli_fetch_array($resultContent)) {
            ?>
                <div class="col-12 col-lg-3">
                    <div class="box-content-position">
                        <div class="box-content">
                            <h4><?= $readContent['tbl_content_title'] ?></h4>
                            <p><?= $readContent['tbl_content_text'] ?></p>
                            <?= (!empty($readContent['tbl_content_pageurl']) ? '<a href="' . $readContent['tbl_content_pageurl'] . '">' . $lang[12] . '</a>' : '') ?>
                        </div>
                        <figure class="inverse_triangle_three"></figure>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</section>