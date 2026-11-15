<?php
include 'header.inc.php';
?>

<section class="section-margin-top ">
    <div class="container-fulid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12 padding-clear">
                <div class="content-model-one">
                    <?php
                    $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='7DA09B5' and tbl_content_fld1='$lng' ");
                    $readPage = mysqli_fetch_array($resultPage);
                    $img = explode(",", $readPage['tbl_content_img']);
                    echo '<img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid">';
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-12 col-lg-12">
                <div class="section-two">
                    <?php
                    $resultCustomer = Query("select * from tbl_content where tbl_content_type='16' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readCustomer = mysqli_fetch_array($resultCustomer)) {
                        $img = explode(",", $readCustomer['tbl_content_img']);
                        echo '<div>' . (!empty($readCustomer['tbl_content_pageurl']) ? '<a href="' . $readCustomer['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readCustomer['tbl_content_title'] . '" class="img-fluid">' . (!empty($readCustomer['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>


<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <p class="title-index">
                    <?= $lang[72] ?>
                </p>
                <p class="text-center"><?= $lang[73] ?></p>
            </div>
            <div class="col-12 col-lg-12">
                <div class="section-three">
                    <?php
                    $resultCustomer = Query("select * from tbl_content where tbl_content_type='17' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readCustomer = mysqli_fetch_array($resultCustomer)) {
                        $img = explode(",", $readCustomer['tbl_content_img']);
                        echo '<div class="open-modal" data-id="' . $readCustomer['tbl_content_code'] . '">' . (!empty($readCustomer['tbl_content_pageurl']) ? '<a href="' . $readCustomer['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readCustomer['tbl_content_title'] . '" class="img-fluid">' . (!empty($readCustomer['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="title-section-two">
                    <p><?= $lang[74] ?></p>
                    <p><?= $lang[75] ?></p>
                </div>
            </div>

            <?php
            $resultOstad = Query("select * from tbl_content where tbl_content_type='18' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
            while ($readOstad = mysqli_fetch_array($resultOstad)) {
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
<section class="section-content mb-0">
    <div class="container-fulid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-them-two border-radius-clear">
                    <span class="icon-certificate1"></span>
                    <div class="title-section-two">
                        <p><?= $lang[76] ?></p>
                        <p><?= $lang[77] ?></p>
                    </div>
                    <a href="#"><?= $lang[78] ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include 'footer.inc.php';
?>