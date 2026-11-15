<?php
include 'header.inc.php';
?>

<section class="section-margin-top bg-section-two">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='115F565' and tbl_content_fld1='$lng' ");
            $readPage = mysqli_fetch_array($resultPage);
            $img = explode(",", $readPage['tbl_content_img']);
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one padding-top"><h1 class="mb-3 orang">' . $readPage['tbl_content_title'] . '</h1>' . $readPage['tbl_content_text'] . '</div></div>';
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid"></div>';
            ?>
        </div>
    </div>
    </div>
</section>



<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="box-text-middle row">
                    <?php
                    $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='29AE089' and tbl_content_fld1='$lng' ");
                    $readPage = mysqli_fetch_array($resultPage);
                    $img = explode(",", $readPage['tbl_content_img']);
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one"><h1 class="mb-3 orang">' . $readPage['tbl_content_title'] . '</h1>' . $readPage['tbl_content_text'] . '</div></div>';
                    echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid"></div>';
                    ?>
                </div>
            </div>
            <div class="col-12 col-lg-4">
                <form class="form-style">
                    <h3 class="mb-3"><?= $lang[97] ?></h3>
                    <div class="form-group">
                        <input type="text" name="fld1" placeholder="<?= $lang[91] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld2" placeholder="<?= $lang[98] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld3" placeholder="<?= $lang[99] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld4" placeholder="<?= $lang[100] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld5" placeholder="<?= $lang[101] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="file" name="fld6" placeholder="<?= $lang[102] ?>" class="form-control">
                    </div>
                    <input type="submit" name="submit" value="<?= $lang[103] ?>" class="btn btn-success">
                </form>
            </div>
        </div>
    </div>
</section>



<section class="section-margin-top ">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='29' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
            $key = 0;
            while ($readTab = mysqli_fetch_array($resultTab)) {
                $img = explode(",", $readTab['tbl_content_img']);
                $file = $readTab['tbl_content_file'];
                $ext = explode(".", $readTab['tbl_content_file']);
                if ($key % 2 == 0) {
            ?>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section">
                            <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                            <?= $readTab['tbl_content_text'] ?>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="video-modeltwo">
                            <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>">
                            <?php } ?>
                            <figure class="inverse_triangle_two" style="<?= (!empty($readTab['tbl_content_color']) ? 'border-bottom-color:' . $readTab['tbl_content_color'] . ';' : '') ?>">

                            </figure>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="video-modelfour">
                            <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                                <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                                    <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                                </video>
                            <?php } else { ?>
                                <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>">
                            <?php } ?>
                            <figure class="inverse_triangle_six" style="<?= (!empty($readTab['tbl_content_color']) ? 'border-bottom-color:' . $readTab['tbl_content_color'] . ';' : '') ?>">

                            </figure>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6 mb-5">
                        <div class="content-section">
                            <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                            <?= $readTab['tbl_content_text'] ?>
                        </div>
                    </div>
            <?php
                }
                $key++;
            }
            ?>

        </div>
    </div>
</section>



<section class="section-content section-why">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="title-section-two">
                    <p><?= $lang[104] ?></p>
                    <p></p>
                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="job-list">
                    <?php
                    $resultExcd = Query("select * from tbl_content where tbl_content_type='30' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readExcd = mysqli_fetch_array($resultExcd)) {
                        $file = $readExcd['tbl_content_file'];
                    ?>
                        <a href="<?= $readExcd['tbl_content_pageurl'] ?>">
                            <div>
                                <p><?= $readExcd['tbl_content_title'] ?></p>
                                <span class="icon-arrow-left"></span>
                            </div>
                        </a>
                        <?php
                        ?>
                    <?php } ?>

                </div>
            </div>
            <div class="col-12 col-lg-6 mt-5">
                <div class="video-modelone">
                    <img src="<?= $site_url ?>upload/11.jpg" class="img-fluid" style="left: 0px;top: 0px;">
                    <!-- <video id="player" playsinline controls data-poster="/path/to/poster.jpg">
                        <source src="<?= $site_url ?>upload/file_1736079196_fa.mp4?ver=1" type="video/mp4" />
                    </video> -->
                    <figure class="inverse_triangle_one">

                    </figure>
                </div>
                <a href="<?= $site_url ?>upload/21-22.pdf">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $lang[16] ?></p>
                            <p><?= $lang[105] ?></p>
                        </div>
                    </div>
                </a>
                <a href="<?= $site_url ?>upload/21-22.pdf">
                    <div class="pdf-download">
                        <div>
                            <span class="icon-document-file-pdf"></span>
                        </div>
                        <div>
                            <p><?= $lang[107] ?></p>
                            <p><?= $lang[106] ?></p>
                        </div>
                    </div>
                </a>
            </div>
            </a>
        </div>
    </div>
    </div>
</section>

<section class="section-job">
    <div class="title-section"><?= $lang[6] ?></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="job-text-title">
                    <?= $lang[108] ?>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <p>
                    <?= $lang[109] ?>
                </p>
                <a href="#"><?= $lang[9] ?></a>
            </div>
            <div class="col-12 col-lg-6">
                <div class="map-job">
                    <img src="<?= $site_url ?>asset/images/map.jpg" class="img-fluid" alt="<?= $lang[7] ?>">
                </div>
            </div>
        </div>
    </div>
</section>
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <h3><?= $lang[110] ?></h3>
                <?php
                $resultTab = Query("select * from tbl_content where tbl_content_type='31' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
                $key = 0;
                $accordionHtml = '';
                while ($readTab = mysqli_fetch_array($resultTab)) {

                    $img = explode(",", $readTab['tbl_content_img']);
                    $show = $key === 0 ? 'show' : '';
                    $aria = $key === 0 ? 'true' : 'false';
                    $collapsed = $key === 0 ? '' : 'collapsed';

                    $accordionHtml .= "<div class='accordion-item'>";
                    $accordionHtml .= "<h2 class='accordion-header'><button class='accordion-button $collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#collapse$key' aria-expanded='$aria' aria-controls='collapse$key'>{$readTab['tbl_content_title']}</button></h2>";
                    $accordionHtml .= "<div id='collapse$key' class='accordion-collapse collapse $show' data-bs-parent='#accordion'><div class='accordion-body'>{$readTab['tbl_content_text']}</div></div>";
                    $accordionHtml .= "</div>";
                    $key++;
                }
                echo "<div class='accordion' id='accordion'>{$accordionHtml}</div>";
                ?>
            </div>
        </div>
    </div>
</section>
<section class="section-content ">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-them-two">
                    <span class="icon-certificate1"></span>
                    <div class="title-section-two">
                        <p><?= $lang[111] ?></p>
                        <p><?= $lang[112] ?></p>
                    </div>
                    <a href="#"><?= $lang[113] ?></a>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
include 'footer.inc.php';
?>