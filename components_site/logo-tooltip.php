<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="customer">
                    <?php
                    while ($readContent = mysqli_fetch_array($resultContent)) {
                        $img = explode(",", $readContent['tbl_content_img']);
                        echo '<div class="tooltipicon" data="' . strip_tags($readContent['tbl_content_text']) . '">' . (!empty($readContent['tbl_content_pageurl']) ? '<a href="' . $readContent['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload_site/' . $img[0] . '" alt="' . $readContent['tbl_content_title'] . '">' . (!empty($readContent['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>