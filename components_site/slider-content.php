<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="section-three">
                    <?php
                    while ($readCustomer = mysqli_fetch_array($resultContent)) {
                        $img = explode(",", $readCustomer['tbl_content_img']);
                        echo '<div class="open-modal" data-id="' . $readCustomer['tbl_content_code'] . '">' . (!empty($readCustomer['tbl_content_pageurl']) ? '<a href="' . $readCustomer['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload_site/' . $img[0] . '" alt="' . $readCustomer['tbl_content_title'] . '" class="img-fluid">' . (!empty($readCustomer['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>