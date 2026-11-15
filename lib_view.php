<?php
include './includer_site.php';
$result_class = QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_statustext='4' and tbl_mohtava_code = '$code' and tbl_mohtava_lng='$lng' ");
if (mysqli_num_rows($result_class) == 0) {
    header("location: $site_url/404.php");
    exit();
}
$read_class = mysqli_fetch_array($result_class);
$title = $read_class['tbl_mohtava_title'];
@$pagetitle .= ' - ' . $title;
$navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
include './header_site.inc.php';
$result = QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_statustext='4' and tbl_mohtava_code = '$code' and tbl_mohtava_lng='$lng' ");
$read = mysqli_fetch_array($result);
$img = explode(",", $read['tbl_mohtava_file']);
$text = $read['tbl_mohtava_shortcontent'];
$thisID = $read['tbl_mohtava_code'];
?>
<section>
    <div class="container">
        <div class="row justify-content-md-center content_class">
            <div class="col-12 col-lg-8 col-md-12 ">
                <div class="class_detail_info">
                    <h1><?= $read['tbl_mohtava_title'] ?></h1>
                    <?php
                    if (!empty($img[2])) {
                        echo '<video controls crossorigin playsinline poster="' . $site_url . 'upload/' . $img[1] . '" class="player">
                                            <source src="' . $site_url . 'upload/' . $img[2]  . '" type="video/mp4" size="576">                             
                                        </video>';
                    } else {
                        echo '<img src="' . $site_url . 'upload/' . $img[1] . '" alt="' . $read['tbl_mohtava_title'] . '">';
                    }
                    ?>

                    <?php echo $text; ?>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-md-12">
                <div class="class_detail_item">
                    <div class="item">
                        <elm>
                            <h3> <?= $lang[162] ?></h3>
                            <h3 class="translate"><?= $read['tbl_mohtava_codefile'] ?></h3>
                        </elm>
                        <?php if (!empty($read['tbl_mohtava_page'])) { ?>
                            <elm>
                                <h3> <?= $lang[163] ?></h3>
                                <h3 class="translate"><?= $read['tbl_mohtava_page'] ?></h3>
                            </elm>
                        <?php } ?>
                        <?php if (!empty($read['tbl_mohtava_time'])) { ?>
                            <elm>
                                <h3> <?= $lang[165] ?></h3>
                                <h3 class="translate"><?= $read['tbl_mohtava_time'] ?></h3>
                            </elm>
                        <?php } ?>
                        <elm>
                            <h3><span class="icon-bookmark"></span> <?= $lang[164] ?> </h3>
                            <h3 class="translate"><?php
                                                    $resultKey = QueryExpert("SELECT * From tbl_user Where tbl_user_id='$read[tbl_mohtava_creator]' and tbl_user_contentcreator='1' and tbl_user_bakhsh='1' ");
                                                    $readKey = mysqli_fetch_array($resultKey);
                                                    echo $readKey['tbl_user_name']
                                                    ?></h3>
                        </elm>
                        <elm>
                            <h3><span class="icon-paperclip"></span> <?= $lang[152] ?> </h3>
                            <h3 class="translate">
                                <?= ($read['tbl_mohtava_sellprice'] != '0' && !empty($read['tbl_mohtava_sellprice']) ? (!empty($read['tbl_mohtava_sellprice']) ? number_format($read['tbl_mohtava_sellprice']) : '0') . $lang[134] : $lang[156]) ?>
                            </h3>
                        </elm>

                        <?php if ($read['tbl_mohtava_sellprice'] == '0' || empty($read['tbl_mohtava_sellprice'])) { ?>
                            <a href="<?= $site_url ?>ajax_site.php?lng=<?= $lng ?>&do=registerlib&code=<?= $code ?>">
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