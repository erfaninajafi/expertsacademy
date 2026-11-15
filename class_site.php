<?php
include './includer_site.php';
$result_class = QueryExpert("SELECT * From tbl_class Where  tbl_class_lng='$lng' and tbl_class_status='1' and tbl_class_code='$code' ");
if (mysqli_num_rows($result_class) == 0) {
    header("location: $site_url/404.php");
    exit();
}
$read_class = mysqli_fetch_array($result_class);
$title = $read_class['tbl_class_title'];
@$pagetitle .= ' - ' . $title;
$navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
include './header_site.inc.php';
$result = QueryExpert("SELECT * From tbl_class Where  tbl_class_lng ='$lng' and tbl_class_status='1' and tbl_class_code='$code' ");
$read = mysqli_fetch_array($result);
$img = explode(",", $read['tbl_class_img']);
$text = $read['tbl_class_shortcontent'];
$thisID = $read['tbl_class_id'];
?>
<section>
    <div class="container">
        <div class="row justify-content-md-center content_class">
            <div class="col-12 col-lg-8 col-md-12 ">
                <div class="class_detail_info">
                    <h1><?= $read['tbl_class_title'] ?></h1>
                    <?php
                    if (!empty($img[2])) {
                        echo '<video controls crossorigin playsinline poster="' . $site_url . 'upload/' . $img[1] . '" class="player">
                                            <source src="' . $site_url . 'upload/' . $img[2]  . '" type="video/mp4" size="576">                             
                                        </video>';
                    } else {
                        echo '<img src="' . $site_url . 'upload/' . $img[1] . '" alt="' . $read['tbl_class_title'] . '">';
                    }
                    ?>

                    <?php echo @$text;
                    $resultcourse = QueryExpert("SELECT * From tbl_courseparent where tbl_courseparent_lng ='$lng' and tbl_courseparent_class='$code' and tbl_courseparent_status ='1'  ORDER BY tbl_courseparent_order DESC , tbl_courseparent_id DESC");
                    if (mysqli_num_rows($resultcourse) > 0) {
                    ?>
                        <div class="coursefree">

                            <hr>
                            <h2><?= $lang[146] ?></h2>
                            <div class="accordion" id="faq">
                                <?php
                                while ($readcourse = mysqli_fetch_array($resultcourse)) {
                                    echo '<div class="card">
                                <div class="card-header" >
                                    <a href="#" class="btn btn-header-link " >' . $readcourse['tbl_courseparent_title'] . '</a>
                                </div>
                            </div>';
                                }
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-md-12">
                <div class="class_detail_item">
                    <div class="item">
                        <elm>
                            <h3><span class="icon-clock"></span> <?= $lang[147] ?></h3>
                            <h3 class="translate"><?= $read['tbl_class_time'] ?></h3>
                        </elm>
                        <elm>
                            <h3><span class="icon-book"></span> <?= $lang[148] ?></h3>
                            <h3 class="translate"><?= $read['tbl_class_mortabet'] ?></h3>
                        </elm>
                        <elm>
                            <h3><span class="icon-bookmark"></span> <?= $lang[149] ?> </h3>
                            <h3 class="translate"><?= $read['tbl_class_sath'] ?></h3>
                        </elm>
                        <elm>
                            <h3><span class="icon-file-text2"></span> <?= $lang[150] ?> </h3>
                            <h3 class="translate"><?= $read['tbl_class_pish'] ?></h3>
                        </elm>
                        <elm>
                            <h3><span class="icon-clipboard"></span> <?= $lang[151] ?> </h3>
                            <h3 class="translate"><?= $read['tbl_class_modelclass'] ?></h3>
                        </elm>
                        <?php ?>
                        <elm>
                            <h3><span class="icon-paperclip"></span> <?= $lang[152] ?> </h3>
                            <h3 class="translate">
                                <?= ($read['tbl_class_price'] != '0' ? number_format($read['tbl_class_price']) . $lang[134] : $lang[156]) ?>
                            </h3>
                        </elm>

                        <?php if ($read['tbl_class_price'] == '0') { ?>
                            <a href="<?= $site_url ?>ajax_site.php?lng=<?= $lng ?>&do=registeritem&code=<?= $code ?>">
                                <button><?= $lang[157] ?></button>
                            </a>

                        <?php } else { ?>
                            <a href="#">
                                <button><?= $lang[153] ?></button>
                            </a>
                        <?php } ?>

                    </div>
                </div>
            </div>


        </div>
    </div>
</section>
<?php

include './footer_site.inc.php';
?>