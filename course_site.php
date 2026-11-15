<?php
include 'header_site.inc.php';
?>

<section class="section-margin-top">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-3 col-md-12 padding-left-clear padding-right-clear">
                <div id="sliderRight">
                    <div class="frameOne">
                        <?php
                        $result_ads = Query("SELECT * From tbl_ads Where tbl_ads_section='a' order by tbl_ads_id DESC , tbl_ads_order DESC ");
                        $kk = 1;
                        while ($read_ads = mysqli_fetch_array($result_ads)) {
                            echo '<img class="' . ($kk == 1 ? 'bottom' : 'top') . ' img-fluid" src="' . $site_url . 'upload_site/' . $read_ads['tbl_ads_img'] . '" alt="' . $read_ads['tbl_ads_title'] . '">';
                            $kk++;
                        }
                        ?>
                    </div>
                    <div class="frameTwo">
                        <?php
                        $result_ads = Query("SELECT * From tbl_ads Where tbl_ads_section='b' order by tbl_ads_id DESC , tbl_ads_order DESC ");
                        $kk = 1;
                        while ($read_ads = mysqli_fetch_array($result_ads)) {
                            echo '<img class="' . ($kk == 1 ? 'bottom' : 'top') . ' img-fluid" src="' . $site_url . 'upload_site/' . $read_ads['tbl_ads_img'] . '" alt="' . $read_ads['tbl_ads_title'] . '">';
                            $kk++;
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5 col-md-12 padding-right-clear">
                <div class="searchMain">
                    <h2>یادگیری بدون مرز</h2>
                    <h3>مهارت های خود را با منابع ما ارتقاء و توسعه دهید</h3>
                    <form action="<?= $site_url ?>search.php" method="GET" style="margin: 20px 0px;">
                        <input type="text" name="keyword" placeholder="نام کلمه مورد جستجو خود را بنویسید ...">
                        <button type="submit">جستجو</button>
                    </form>
                    <div class="tools">
                        <a href="<?= $site_url . $lng ?>/cat/free/"><span class="icon-world"></span>
                            <p>مهارت های خودخوان</p>
                        </a>
                        <a href="<?= $site_url . $lng ?>/cat/workshop/"><span class="icon-certificate1"></span>
                            <p>مهارت های رایگان</p>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-4 col-md-12 ">

                <div id="sliderLeft">
                    <div class="frameOne">
                        <?php
                        $result_ads = Query("SELECT * From tbl_ads Where tbl_ads_section='c' order by tbl_ads_id DESC , tbl_ads_order DESC ");
                        $kk = 1;
                        while ($read_ads = mysqli_fetch_array($result_ads)) {
                            echo '<img class="' . ($kk == 1 ? 'bottom' : 'top') . ' img-fluid" src="' . $site_url . 'upload_site/' . $read_ads['tbl_ads_img'] . '" alt="' . $read_ads['tbl_ads_title'] . '">';
                            $kk++;
                        }
                        ?>
                    </div>
                    <div class="frameTwo">
                        <?php
                        $result_ads = Query("SELECT * From tbl_ads Where tbl_ads_section='d' order by tbl_ads_id DESC , tbl_ads_order DESC ");
                        $kk = 1;
                        while ($read_ads = mysqli_fetch_array($result_ads)) {
                            echo '<img class="' . ($kk == 1 ? 'bottom' : 'top') . ' img-fluid" src="' . $site_url . 'upload_site/' . $read_ads['tbl_ads_img'] . '" alt="' . $read_ads['tbl_ads_title'] . '">';
                            $kk++;
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<section class="section-content ">
    <div class="container">
        <div class="row justify-content-center">

            <div class="col-12 col-lg-3 col-md-12 classmenu">
                <div class="bg-right">
                    <h1>
                        منابع آکادمی</h1>
                    <ul>
                        <?php
                        $result = Query("SELECT * from tbl_categories Where tbl_categories_type = '3'  order by tbl_categories_order DESC , tbl_categories_id DESC ");
                        while ($read = mysqli_fetch_array($result)) {
                            echo '<li><a href="' . $site_url . $lng . '/cat/' . $read['tbl_categories_id'] . '/">' . $read['tbl_categories_title'] . '</a></li>';
                        }
                        ?>

                    </ul>
                    <div class="form-collection">
                        <h3>جستجو پیشرفته</h3>
                        <div class="form-section-course">
                            <h4>اهداف</h4>
                            <label><input type="radio" name="fld1" value="دوره با گواهینامه">دوره با گواهینامه</label>
                            <label><input type="radio" name="fld1" value="محتوی هم راستا با مسیر شغلی">محتوی هم راستا با مسیر شغلی</label>
                        </div>

                        <div class="form-section-course">
                            <h4>محتوی مشارکتی</h4>
                            <label><input type="radio" name="fld2" value="انجمن بنادر ایران">انجمن بنادر ایران</label>
                            <label><input type="radio" name="fld2" value="انجمن مهندسی حمل و نقل ریلی">انجمن مهندسی حمل و نقل ریلی</label>
                            <label><input type="radio" name="fld2" value="سازمان بنادر و کشتیرانی">سازمان بنادر و کشتیرانی</label>
                        </div>

                        <div class="form-section-course">
                            <h4>دسته محتوی</h4>
                            <label><input type="radio" name="fld3" value="دوره آموزشی">دوره آموزشی</label>
                            <label><input type="radio" name="fld3" value="ویدیو آموزشی">ویدیو آموزشی</label>
                            <label><input type="radio" name="fld3" value="فایل متنی">فایل متنی</label>
                            <label><input type="radio" name="fld3" value="کتاب">کتاب</label>
                            <label><input type="radio" name="fld3" value="کتب مرجع">کتب مرجع</label>
                            <label><input type="radio" name="fld3" value="مقالات">مقالات</label>
                            <label><input type="radio" name="fld3" value="وبینار">وبینار</label>
                        </div>
                        <div class="form-section-course">
                            <h4>نوع محتوی</h4>
                            <label><input type="radio" name="fld4" value="رایگان">رایگان</label>
                            <label><input type="radio" name="fld4" value="همه">همه</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-9 col-md-12 classmenu row">
                <div class="col-12 col-lg-12 title-custom mb-4">

                    <h3 style="text-align: right;"><?= $lang[115] ?>
                    </h3>
                </div>
                <?php
                if (!empty($do) && ($do == 'free' || $do == 'class')) {
                    if ($do == 'free') {
                        $select = " tbl_class_special ='1' and ";
                    } else if ($do == 'class') {
                        $select = "";
                    } else {
                        $select = "";
                    }
                    $result = Query("SELECT * From tbl_class where $select tbl_class_status='1' ORDER BY tbl_class_order DESC , tbl_class_id DESC ");
                    $row = mysqli_num_rows($result);
                    $result = Query("SELECT * From tbl_class where $select tbl_class_status='1' ORDER BY tbl_class_order DESC , tbl_class_id DESC  LIMIT 0,9");
                    while ($read = mysqli_fetch_array($result)) {
                        $result_cat = Query("Select tbl_categories_title from tbl_categories where tbl_categories_id='$read[tbl_class_cat]' ");
                        $read_cat = mysqli_fetch_array($result_cat);
                        $logo = $read['tbl_class_logo'];

                        $result_logo = Query("Select * from tbl_logo where tbl_logo_id ='$logo' ");
                        $read_logo = mysqli_fetch_array($result_logo);

                        $img = explode(",", $read['tbl_class_img']);
                        $text = explode("[short]", $read['tbl_class_content']);
                        $teacher = explode(",", $read['tbl_class_teacher']);
                        $teacher_all = '';
                        foreach ($teacher as $key => $value) {
                            if (!empty($value)) {
                                $value = str_replace("[", "", str_replace("]", "", $value));
                                $result_teacher = Query("select tbl_teacher_fname,tbl_teacher_lname,tbl_teacher_id from tbl_teacher where tbl_teacher_id = '$value' ");
                                $read_teacher = mysqli_fetch_array($result_teacher);
                                $teacher_all .= '<a href="#">' . $read_teacher['tbl_teacher_fname'] . '&nbsp;' . $read_teacher['tbl_teacher_lname'] . '</a>, ';
                            }
                        }
                        $teacher_all = rtrim($teacher_all, ", ");
                        echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                    <div class="itemclass">
                        <div class="img" style="background: url(' . $site_url . 'upload_site/' . $img[0] . ');">
                            <div class="cat">' . $read_cat['tbl_categories_title'] . '</div>
                            ' . ($read['tbl_class_special'] == 1 ? '<div class="free">دوره رایگان</div>' : '') . '
                        </div>
                        <div class="content">
                            <h1><a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/">' . $read['tbl_class_title'] . '</a></h1>
                                <elm>مدرس : ' . $teacher_all . '</elm>
                            <p>' . strip_tags($text[0]) . ' </p>
                            <div class="tools">
                                <span class="icon-tag"></span>
                                <p class="translate">' . ($read['tbl_class_price'] != '0' ? number_format($read['tbl_class_price']) . $lang[134] : $lang[156]) . ' </p>
                                
                            </div>
                            <a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/"><button>شرکت در این دوره</button></a>
                            <div class="logo_sponser"><img src="' . $site_url . 'upload_site/logo/' . $read_logo['tbl_logo_img'] . '" alt="' . $read_logo['tbl_logo_title'] . '"></div>
                        </div>
                    </div>
                </div>';
                    }

                    if ($row > 8) {
                        echo '<div class="col-12 col-lg-12 col-md-12 allLink">
                        <a href="' . $site_url . $lng . '/cat/' . (!empty($do) ? $do : 'all') . '/" alt="مشاهده تمام دوره ها">مشاهده تمام دوره ها</a>
                    </div>';
                    }
                } else if (!empty($do) && $do == 'workshop') {
                    $result = Query("SELECT * From tbl_workshop where tbl_workshop_status='1' ORDER BY tbl_workshop_order DESC , tbl_workshop_id DESC");
                    $row = mysqli_num_rows($result);
                    $result = Query("SELECT * From tbl_workshop where tbl_workshop_status='1' ORDER BY tbl_workshop_order DESC , tbl_workshop_id DESC LIMIT 0,9");
                    while ($read = mysqli_fetch_array($result)) {

                        $start = $read['tbl_workshop_start'];
                        $end = $read['tbl_workshop_end'];
                        $now = jdate("Y/m/d");
                        if ($start <= $now && $end > $now) {
                            $logo = $read['tbl_workshop_logo'];

                            $result_logo = Query("Select * from tbl_logo where tbl_logo_id ='$logo' ");
                            $read_logo = mysqli_fetch_array($result_logo);

                            $result_cat = Query("Select tbl_categories_title from tbl_categories where tbl_categories_id='$read[tbl_workshop_cat]' ");
                            $read_cat = mysqli_fetch_array($result_cat);
                            $img = explode(",", $read['tbl_workshop_img']);
                            $text = explode("[short]", $read['tbl_workshop_content']);
                            $teacher = json_decode($read['tbl_workshop_teacher'], true);
                            $teacher_all = '';
                            foreach ($teacher as $key => $value) {
                                if (!empty($value)) {
                                    $result_teacher = Query("select tbl_teacher_fname,tbl_teacher_lname,tbl_teacher_id from tbl_teacher where tbl_teacher_id = '$value' ");
                                    $read_teacher = mysqli_fetch_array($result_teacher);
                                    $teacher_all .= '<a href="#">' . $read_teacher['tbl_teacher_fname'] . '&nbsp;' . $read_teacher['tbl_teacher_lname'] . '</a>, ';
                                }
                            }
                            $teacher_all = rtrim($teacher_all, ", ");
                            echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                    <div class="itemclass">
                        <div class="img" style="background: url(' . $site_url . 'upload_site/' . $img[0] . ');">
                            <div class="cat">' . $read_cat['tbl_categories_title'] . '</div>
                            ' . ($read['tbl_workshop_free'] == 1 ? '<div class="free">کارگاه رایگان</div>' : '') . '
                        </div>
                        <div class="content">
                            <h1><a href="' . $site_url . $lng . '/workshop/' . $read['tbl_workshop_code'] . '/' . title($read['tbl_workshop_title']) . '/">' . $read['tbl_workshop_title'] . '</a></h1>
                                <elm>مدرس : ' . $teacher_all . '</elm>
                            <p>' . strip_tags($text[0]) . ' </p>
                            <div class="tools">
                                <span class="icon-tag"></span>
                                <p class="translate">' . ($read['tbl_workshop_price'] != '0' ? number_format($read['tbl_workshop_price']) . $lang[134] : $lang[156]) . ' </p>
                                <span class="icon-calendar-o"></span>
                                <p class="translate">تاریخ برگزاری : ' . $read['tbl_workshop_update'] . ' </p>
                                
                            </div>
                            <a href="' . $site_url . $lng . '/workshop/' . $read['tbl_workshop_code'] . '/' . title($read['tbl_workshop_title']) . '/"><button>شرکت در این دوره</button></a>
                            <div class="logo_sponser"><img src="' . $site_url . 'upload_site/logo/' . $read_logo['tbl_logo_img'] . '" alt="' . $read_logo['tbl_logo_title'] . '"></div>
                        </div>
                    </div>
                </div>';
                        }
                    }

                    if ($row > 8) {
                        echo '<div class="col-12 col-lg-12 col-md-12 allLink">
                        <a href="' . $site_url . $lng . '/cat/workshop/" alt="مشاهده تمام دوره ها">مشاهده تمام دوره ها</a>
                    </div>';
                    }
                } else {
                    $result = Query("SELECT * From tbl_class where  tbl_class_status='1' ");
                    $row = mysqli_num_rows($result);
                    $result = Query("SELECT * From tbl_class where  tbl_class_status='1' ORDER BY tbl_class_order DESC , tbl_class_id DESC  LIMIT 0,9");
                    while ($read = mysqli_fetch_array($result)) {
                        $result_cat = Query("Select tbl_categories_title from tbl_categories where tbl_categories_id='$read[tbl_class_cat]' ");
                        $read_cat = mysqli_fetch_array($result_cat);
                        $logo = $read['tbl_class_logo'];

                        $result_logo = Query("Select * from tbl_logo where tbl_logo_id ='$logo' ");
                        $read_logo = mysqli_fetch_array($result_logo);

                        $img = explode(",", $read['tbl_class_img']);
                        $text = explode("[short]", $read['tbl_class_content']);
                        $teacher = explode(",", $read['tbl_class_teacher']);
                        $teacher_all = '';
                        foreach ($teacher as $key => $value) {
                            if (!empty($value)) {
                                $value = str_replace("[", "", str_replace("]", "", $value));
                                $result_teacher = Query("select tbl_teacher_fname,tbl_teacher_lname,tbl_teacher_id from tbl_teacher where tbl_teacher_id = '$value' ");
                                $read_teacher = mysqli_fetch_array($result_teacher);
                                $teacher_all .= '<a href="#">' . $read_teacher['tbl_teacher_fname'] . '&nbsp;' . $read_teacher['tbl_teacher_lname'] . '</a>, ';
                            }
                        }
                        $teacher_all = rtrim($teacher_all, ", ");
                        echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                    <div class="itemclass">
                        <div class="img" style="background: url(' . $site_url . 'upload_site/' . $img[0] . ');">
                            <div class="cat">' . @$read_cat['tbl_categories_title'] . '</div>
                            ' . ($read['tbl_class_special'] == 1 ? '<div class="free">دوره رایگان</div>' : '') . '
                        </div>
                        <div class="content">
                            <h1><a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/">' . $read['tbl_class_title'] . '</a></h1>
                                <elm>مدرس : ' . $teacher_all . '</elm>
                            <p>' . strip_tags($text[0]) . ' </p>
                            <div class="tools">
                                <span class="icon-tag"></span>
                                <p class="translate">' . ($read['tbl_class_price'] != '0' ? number_format($read['tbl_class_price']) . $lang[134] : $lang[156]) . ' </p>
                                
                            </div>
                            <a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/"><button>شرکت در این دوره</button></a>
                            <div class="logo_sponser"><img src="' . $site_url . 'upload_site/logo/' . $read_logo['tbl_logo_img'] . '" alt="' . $read_logo['tbl_logo_title'] . '"></div>
                        </div>
                    </div>
                </div>';
                    }

                    if ($row > 8) {
                        echo '<div class="col-12 col-lg-12 col-md-12 allLink">
                        <a href="' . $site_url . $lng . '/cat/all/" alt="مشاهده تمام دوره ها">مشاهده تمام دوره ها</a>
                    </div>';
                    }
                }

                ?>

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
                    <?= $lang[7] ?>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <p>
                    <?= $lang[116] ?>
                </p>
                <a href="<?= $site_url . $lng  ?>/section-five/"><?= $lang[9] ?></a>
            </div>
            <div class="col-12 col-lg-6">
                <div class="map-job">
                    <img src="<?= $site_url ?>asset/images/map.jpg" class="img-fluid" alt="<?= $lang[7] ?>">
                </div>
            </div>
        </div>
    </div>
</section>


<section class="mt-5">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='32' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
            $key = 0;
            while ($readTab = mysqli_fetch_array($resultTab)) {
                $img = explode(",", $readTab['tbl_content_img']);
                $file = $readTab['tbl_content_file'];
                $ext = explode(".", $readTab['tbl_content_file']);
            ?>
                <div class="col-12 col-lg-4 mb-5">
                    <div class="course-content-section">
                        <img src="<?= $site_url ?>upload_site/<?= $img[0] ?>" alt="<?= $readTab['tbl_content_title'] ?>">
                        <h2 class="mb-4"><?= $readTab['tbl_content_title'] ?></h2>
                        <?= $readTab['tbl_content_text'] ?>
                    </div>
                </div>
            <?php
            }
            ?>

        </div>
    </div>
</section>

<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12 title-custom mb-4">
                <h3 class="btn-gray">منابع رایگان</h3>
            </div>
            <div class="col-12 col-lg-12">
                <div class="section-three">
                    <?php

                    $result = Query("SELECT * From tbl_class where tbl_class_status='1' ORDER BY tbl_class_order DESC , tbl_class_id DESC  LIMIT 0,9");
                    while ($read = mysqli_fetch_array($result)) {
                        $result_cat = Query("Select tbl_categories_title from tbl_categories where tbl_categories_id='$read[tbl_class_cat]' ");
                        $read_cat = mysqli_fetch_array($result_cat);
                        $logo = $read['tbl_class_logo'];

                        $result_logo = Query("Select * from tbl_logo where tbl_logo_id ='$logo' ");
                        $read_logo = mysqli_fetch_array($result_logo);

                        $img = explode(",", $read['tbl_class_img']);
                        $text = explode("[short]", $read['tbl_class_content']);
                        $teacher = explode(",", $read['tbl_class_teacher']);
                        $teacher_all = '';
                        foreach ($teacher as $key => $value) {
                            if (!empty($value)) {
                                $value = str_replace("[", "", str_replace("]", "", $value));
                                $result_teacher = Query("select tbl_teacher_fname,tbl_teacher_lname,tbl_teacher_id from tbl_teacher where tbl_teacher_id = '$value' ");
                                $read_teacher = mysqli_fetch_array($result_teacher);
                                $teacher_all .= '<a href="#">' . $read_teacher['tbl_teacher_fname'] . '&nbsp;' . $read_teacher['tbl_teacher_lname'] . '</a>, ';
                            }
                        }
                        $teacher_all = rtrim($teacher_all, ", ");
                        echo '<div>
                        <div class="itemclass">
                            <div class="img" style="background: url(' . $site_url . 'upload_site/' . $img[0] . ');">
                                <div class="cat">' . $read_cat['tbl_categories_title'] . '</div>
                                ' . ($read['tbl_class_special'] == 1 ? '<div class="free">دوره رایگان</div>' : '') . '
                            </div>
                            <div class="content">
                                <h1><a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/">' . $read['tbl_class_title'] . '</a></h1>
                                    <elm>مدرس : ' . $teacher_all . '</elm>
                                <p>' . strip_tags($text[0]) . ' </p>
                                <div class="tools">
                                    <span class="icon-tag"></span>
                                    <p class="translate">' . ($read['tbl_class_price'] != '0' ? number_format($read['tbl_class_price']) . $lang[134] : $lang[156]) . ' </p>
                                    
                                </div>
                                <a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/"><button>شرکت در این دوره</button></a>
                                <div class="logo_sponser"><img src="' . $site_url . 'upload_site/logo/' . $read_logo['tbl_logo_img'] . '" alt="' . $read_logo['tbl_logo_title'] . '"></div>
                            </div>
                        </div>
                    </div>';
                    }


                    ?>

                </div>
            </div>
        </div>
    </div>
</section>

<?php
include 'footer_site.inc.php';
?>