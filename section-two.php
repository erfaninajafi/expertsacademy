<?php
include 'header.inc.php';
?>

<section class="section-margin-top bg-section-two">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='228C331' and tbl_content_fld1='$lng' ");
            $readPage = mysqli_fetch_array($resultPage);
            $img = explode(",", $readPage['tbl_content_img']);
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one padding-top"><h1 class="mb-3 orange">' . $readPage['tbl_content_title'] . '</h1>' . $readPage['tbl_content_text'] . '</div></div>';
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid"></div>';
            ?>
        </div>
    </div>
    </div>
</section>


<section class="section-margin-top ">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='C18D1E2' and tbl_content_fld1='$lng' ");
            $readPage = mysqli_fetch_array($resultPage);
            $file = $readPage['tbl_content_file'];
            $ext = explode(".", $file);
            $img = explode(",", $readPage['tbl_content_img']);

            ?>
            <div class="col-12 col-lg-6">
                <div class="content-model-one">
                    <?php
                    echo '<h1 class="mb-3">' . $readPage['tbl_content_title'] . '</h1>';
                    echo $readPage['tbl_content_text'];
                    ?>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="video-modelthree">
                    <?php if (end($ext) == 'mp4' || end($ext) == 'MP4') { ?>
                        <video id="player" playsinline controls data-poster="<?= $site_url ?>upload/<?= $img[0] ?>">
                            <source src="<?= $site_url ?>upload/<?= $file ?>" type="video/mp4" />
                        </video>
                    <?php } else { ?>
                        <img src="<?= $site_url ?>upload/<?= $img[0] ?>" alt="<?= $readPage['tbl_content_title'] ?>" class="img-fluid">
                    <?php } ?>
                    <figure class="inverse_triangle_one">
                    </figure>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section-margin-top ">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='20' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
            $key = 0;
            $tabHtml = '<ul class="nav nav-tabs" id="myTab" role="tablist">';
            $tabContentHtml = '<div class="tab-content" id="myTabContent">';
            while ($readTab = mysqli_fetch_array($resultTab)) {

                $img = explode(",", $readTab['tbl_content_img']);
                $active = $key === 0 ? 'active' : '';

                $tabHtml .= "<li class='nav-item' role='presentation'><button class='nav-link $active' id='tab$key-tab' data-bs-toggle='tab' data-bs-target='#tab$key' type='button' role='tab' aria-controls='tab$key' aria-selected='" . ($key === 0 ? 'true' : 'false') . "'>{$readTab['tbl_content_title']}</button></li>";
                $tabContentHtml .= "<div class=' tab-pane fade $active show' id='tab$key' role='tabpanel' aria-labelledby='tab$key-tab'><div class='row'><div class='col-12 col-lg-6'><h2 class='mb-3'>{$readTab['tbl_content_title']}</h2>{$readTab['tbl_content_text']}</div><div class='col-12 col-lg-6'><img src='$site_url/upload/{$img[0]}' class='img-fluid' alt='{$readTab['tbl_content_title']}'></div></div></div>";

                $key++;
            }
            $tabHtml .= '</ul>';
            $tabContentHtml .= '</div>';
            echo "<div class='tabsetion'>{$tabHtml}{$tabContentHtml}</div>";
            ?>

        </div>
    </div>
</section>


<section class="section-margin-top ">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='21' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
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


<section class="section-content mb-0">
    <div class="container-fulid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <div class="box-them-two border-radius-clear">
                    <span class="icon-certificate1"></span>
                    <div class="title-section-two">
                        <p><?= $lang[79] ?></p>
                        <p><?= $lang[80] ?></p>
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