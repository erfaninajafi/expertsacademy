<?php
ob_start();
include './includer.php';
@$pagetitle = $lang[408];
$index = basename($_SERVER['PHP_SELF']);
$seo_code = Query("Select * from tbl_seo");
$read_seo = mysqli_fetch_array($seo_code);
$ip = myip();
$upload_profile_dir = "upload/profile/";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0; URL=<?= $site_url ?>noscript.php">
    </noscript>
    <link rel="icon" type="image/x-icon" href="<?= $site_url ?>asset/images/favicon.ico?ver=1" />
    <meta name="keywords" content="<?= $read['tbl_setting_keywords'] ?>">
    <meta name="description" content="<?= $read['tbl_setting_des'] ?>">
    <?php echo htmlspecialchars_decode($read_seo['tbl_seo_webmaster']); ?>
    <meta name="author" content="Hilano">
    <title><?= $read['tbl_setting_title_' . $lng] . @($pagetitle) ?></title>
    <link rel="stylesheet" href="<?= $site_url ?>assets/css/bootstrap.min.css">
    <?php if ($lng == 'fa' || $lng == 'ar') { ?>
    <link rel="stylesheet" href="<?= $site_url ?>assets/css/bootstrap.rtl.min.css">
    <?php } ?>
    <link rel="stylesheet" href="<?= $site_url ?>assets/css/resume_<?= $lng ?>.css?ver=<?= rand(10, 1000) ?>">
</head>

<body>
    <section>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-lg-12 col-md-12">
                    <?php
                    $result = Query("Select * from tbl_member where tbl_member_id = '$id' ");
                    if (mysqli_num_rows($result) <= 0) {
                        header("Location : $site_url/404.php");
                        exit();
                    }
                    $read = mysqli_fetch_array($result);
                    $profile = $read['tbl_member_profile'];
                    $fname = $read['tbl_member_fname'];
                    $lname = $read['tbl_member_lname'];
                    $mobile = $read['tbl_member_mobile'];
                    $state = $read['tbl_member_state'];
                    $birthday = $read['tbl_member_birthday'];
                    $reshteh = $read['tbl_member_reshteh'];
                    $madrak = $read['tbl_member_madrak'];
                    $email = $read['tbl_member_email'];


                    $resultResume = Query("select * from tbl_resume where tbl_resume_user = '$id' ");
                    $readResume = mysqli_fetch_array($resultResume);
                    ?>
                    <div class="resume">
                        <div class="resume_left">
                            <div class="resume_profile">
                                <img src="<?= (!empty($profile) ? $site_url . 'upload/profile/' . $profile : $site_url . 'asset/images/no-image.jpg') ?>" alt="<?= $fname . ' ' . $lname ?> ">
                            </div>
                            <div class="resume_content">
                                <div class="resume_item resume_info">
                                    <div class="title">
                                        <p class="bold"><?= $fname . ' ' . $lname ?></p>
                                    </div>
                                    <ul>
                                        <?php if (!empty($mobile)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-iphone"></i>
                                            </div>
                                            <div class="data">
                                                <?= toPersianNum($mobile) ?>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($email)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-mail-envelope-closed"></i>
                                            </div>
                                            <div class="data">
                                                <?= $email ?>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($state)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-location_city"></i>
                                            </div>
                                            <div class="data">
                                                <?= $state ?>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($birthday)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-calendar"></i>
                                            </div>
                                            <div class="data">
                                                <?= $birthday ?>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($reshteh)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-education"></i>
                                            </div>
                                            <div class="data">
                                                <?= $reshteh ?>
                                            </div>
                                        </li>
                                        <?php } ?>
                                        <?php if (!empty($madrak)) { ?>
                                        <li>
                                            <div class="icon">
                                                <i class="icon-document-certificate"></i>
                                            </div>
                                            <div class="data">
                                                <?= $madrak ?>
                                            </div>
                                        </li>
                                        <?php } ?>

                                    </ul>
                                </div>
                                <div class="resume_item resume_skills">
                                    <div class="title">
                                        <p class="bold"><?= $lang[410] ?></p>
                                    </div>
                                    <ul>
                                        <!-- <li>
                                                <div class="skill_name">
                                                    HTML
                                                </div>
                                                <div class="skill_progress">
                                                    <span style="width: 80%;"></span>
                                                </div>
                                                <div class="skill_per">80%</div>
                                            </li> -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php

                        ?>
                        <div class="resume_right">
                            <div class="resume_item resume_education">
                                <div class="title">
                                    <p class="bold"><?= $lang[412] ?></p>
                                </div>
                                <ul>
                                    <?php
                                    $resultEdu = Query("select * from tbl_resumedata where tbl_resumedata_model='2' and tbl_resumedata_user='$id' ");
                                    if (mysqli_num_rows($resultEdu) > 0) {

                                        while ($readEdu = mysqli_fetch_array($resultEdu)) {
                                    ?>

                                    <li>
                                        <div class="date"><?= $readEdu['tbl_resumedata_start'] ?><?= (!empty($readEdu['tbl_resumedata_end']) ? ' - ' . $readEdu['tbl_resumedata_end'] : '') ?></div>
                                        <div class="info">
                                            <p><label><?= $lang[413] ?></label><?= $readEdu['tbl_resumedata_name'] ?></p>
                                            <p><label><?= $lang[142] ?></label><?= $readEdu['tbl_resumedata_city'] ?></p>
                                            <p><label><?= $lang[414] ?></label><?= $readEdu['tbl_resumedata_reshte'] ?></p>
                                            <p><label><?= $lang[396] ?></label><?= $readEdu['tbl_resumedata_maghta'] ?></p>
                                            <p><label><?= $lang[525] ?></label><?= $readEdu['tbl_resumedata_comment'] ?></p>

                                            <?= (!empty($readEdu['tbl_resumedata_file']) ?  '<p><label>' . $lang[1183] . '</label><a href="' . $site_url . 'upload/attach/' . $readEdu['tbl_resumedata_file'] . '" target="_blank"> ' . $lang[98] . '</a></p>' : '') ?>
                                        </div>
                                    </li>
                                    <?php }
                                    } ?>

                                </ul>
                            </div>


                            <div class="resume_item resume_education">
                                <div class="title">
                                    <p class="bold"><?= $lang[720] ?></p>
                                </div>
                                <ul>
                                    <?php

                                    $resultSkill = Query("select * from tbl_resumedata where tbl_resumedata_model='3' and tbl_resumedata_user='$id' ");
                                    
                                    if (mysqli_num_rows($resultSkill) > 0) {

                                        while ($readSkill = mysqli_fetch_array($resultSkill)) {
                                    ?>

                                    <li>
                                        <div class="date"><?= $readSkill['tbl_resumedata_start'] ?></div>
                                        <div class="info">
                                            <p><label><?= $lang[532] ?></label><?= $readSkill['tbl_resumedata_name'] ?></p>
                                            <p><label><?= $lang[534] ?></label><?= $readSkill['tbl_resumedata_course'] ?></p>
                                            <p><label><?= $lang[536] ?></label><?= $readSkill['tbl_resumedata_start'] ?></p>
                                            <p><label><?= $lang[525] ?></label><?= $readSkill['tbl_resumedata_comment'] ?></p>
                                            <?= (!empty($readSkill['tbl_resumedata_file']) ?  '<p><label>' . $lang[1183] . '</label><a href="' . $site_url . 'upload/attach/' . $readSkill['tbl_resumedata_file'] . '" target="_blank"> ' . $lang[98] . '</a></p>' : '') ?>
                                        </div>
                                    </li>
                                    <?php }
                                    } ?>

                                </ul>
                            </div>


                            <div class="resume_item resume_education">
                                <div class="title">
                                    <p class="bold"><?= $lang[721] ?></p>
                                </div>
                                <ul>
                                    <?php
                                    $resultJob = Query("select * from tbl_resumedata where tbl_resumedata_model='1' and tbl_resumedata_user='$id' ");
                                    
                                    if (mysqli_num_rows($resultJob) > 0) {

                                        while ($readJob = mysqli_fetch_array($resultJob)) {
                                    ?>

                                    <li>
                                        <div class="date"><?= $readJob['tbl_resumedata_start'] ?><?= (!empty($readJob['tbl_resumedata_end']) ? ' - ' . $readJob['tbl_resumedata_end'] : '') ?></div>
                                        <div class="info">
                                            <p><label><?= $lang[552] ?></label><?= $readJob['tbl_resumedata_name'] ?></p>
                                            <p><label><?= $lang[554] ?></label><?= $readJob['tbl_resumedata_semat'] ?></p>
                                            <p><label><?= $lang[555] ?></label><?= $readJob['tbl_resumedata_coaddress'] ?></p>
                                            <p><label><?= $lang[558] ?></label><?= $readJob['tbl_resumedata_ostan'] ?></p>
                                            <p><label><?= $lang[525] ?></label><?= $readJob['tbl_resumedata_comment'] ?></p>
                                            <?= (!empty($readJob['tbl_resumedata_file']) ?  '<p><label>' . $lang[1183] . '</label><a href="' . $site_url . 'upload/attach/' . $readJob['tbl_resumedata_file'] . '" target="_blank"> ' . $lang[98] . '</a></p>' : '') ?>
                                        </div>
                                    </li>
                                    <?php }
                                    } ?>

                                </ul>
                            </div>

                        </div>

                    </div>


                </div>


            </div>
        </div>
    </section>
</body>

</html>