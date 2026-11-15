<section class="section-margin-top ">
    <div class="content-model-one">
        <?php
        $readContent = mysqli_fetch_array($resultContent);
        $img = explode(",", $readContent['tbl_textsection_img']);
        echo '<a style="padding: 0px;margin: 0px;display: unset;border-radius: 0px;background: unset;" href="' . $readContent['tbl_textsection_title'] . '">
        <img src="' . $site_url . 'upload_site/' . $img[0] . '" class="img-fluid" alt="Expert Academy">
        </a>';
        ?>

    </div>
</section>