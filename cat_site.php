<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include_once './includer_site.php';

if (isset($do) && $do == 'cat' && !empty($code)) {
    $show_per_page = '12';
    $result_cat = QueryExpert("Select tbl_categories_title from tbl_categories where tbl_categories_code='$code' ");
    if (mysqli_num_rows($result_cat) <= 0) {
        header("Location: $site_url/404.php");
        exit();
    }
    $read_cat = mysqli_fetch_array($result_cat);
    $title = $read_cat['tbl_categories_title'];

    $navigation = '<span><a href="' . $site_url .  '">' . $lang[13] . '</a></span><span><a href="' . $site_url . 'cat/">' . $lang[129] . '</a></span>' . $title;
    @$pagetitle .= ' - ' . $read_cat['tbl_categories_title'] . ' - ' . $title;
} elseif (isset($do) && $do == 'filter') {
    $show_per_page = '12';
    $title = $lang[3];
    $navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
    @$pagetitle .= ' - ' . $title;
} elseif (isset($do) && $do == 'search') {
    $show_per_page = '12';
    $title = $lang[3];
    $navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
    @$pagetitle .= ' - ' . $title;
} else {
    $show_per_page = '12';
    $title = $lang[3];
    $navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
    @$pagetitle .= ' - ' . $title;
}
include_once './header_site.inc.php';
$keyword = (!empty($keyword) ? $keyword : '');
?>
<section class="section-margin-top" style="margin-top: 200px;">
    <div class="container ">
        <div class="row justify-content-start content_class">
            <div class="col-12 col-lg-12 title-custom mb-4">
                <h3><?= $title ?>
                </h3>
            </div>
            <?php
            if (isset($do) && $do == 'cat' && !empty($code)) {


            ?>
                <div class="col-12 col-lg-3">
                    <form method="POST" action="<?= $site_url ?>cat_site.php?do=filter">
                        <div class="accordion-item">

                            <div id="panelsStayOpen" class="accordion-collapse collapse in show" aria-labelledby="panelsStayOpen-heading">
                                <div class="accordion-body">
                                    <input type="text" name="keyword" placeholder="<?= $lang[136] ?>" value="<?= (!empty($keyword) ? $keyword : '') ?>">
                                </div>
                                <div class="accordion-body">
                                    <?php
                                    $result = QueryExpert("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$lng' ORDER BY tbl_categories_id DESC ");
                                    while ($read = mysqli_fetch_array($result)) {

                                        echo '<label><input type="radio" name="cat" value="' . $read['tbl_categories_code'] . '" ' . ($code == $read['tbl_categories_code'] ? 'checked' : '') . '> <span></span>' . $read['tbl_categories_title'] . '</label>';
                                    }
                                    ?>
                                </div>
                                <div class="accordion-body">
                                    <label><input type="radio" name="price" value="free" <?= (!empty($_GET['price']) && $_GET['price'] == 'free' ? 'checked' : '') ?>> <span></span><?= $lang[156] ?></label>
                                    <label><input type="radio" name="price" value="notfree" <?= (!empty($_GET['price']) &&  $_GET['price'] == 'notfree' ? 'checked' : '') ?>> <span></span><?= $lang[158] ?></label>
                                    <label><input type="radio" name="price" value="all" <?= (empty($_GET['price']) || $_GET['price'] == 'all'   ? 'checked' : '') ?>> <span></span><?= $lang[159] ?></label>
                                </div>

                                <div class="accordion-body">
                                    <label><input type="radio" name="section" value="course" <?= (!empty($_GET['section']) && $_GET['section'] == 'course' ? 'checked' : '') ?>> <span></span><?= $lang[166] ?></label>
                                    <label><input type="radio" name="section" value="lib" <?= (!empty($_GET['section']) &&  $_GET['section'] == 'lib' ? 'checked' : '') ?>> <span></span><?= $lang[167] ?></label>
                                    <label><input type="radio" name="section" value="all" <?= (empty($_GET['section']) || $_GET['section'] == 'all'   ? 'checked' : '') ?>> <span></span><?= $lang[168] ?></label>
                                </div>

                            </div>
                            <button type="submit" class="clear-btn shadow"><?= $lang[132] ?></button>
                        </div>
                    </form>

                </div>
                <?php
                echo '<div class=" col-12 col-lg-9">
                                    <div class="row">';
                if (!empty($_GET['section']) && $_GET['section'] == 'all') {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {
                        $result_for_count = QueryExpert("SELECT 
                            tbl_class.tbl_class_id AS item_id, 
                            tbl_class.tbl_class_title AS title, 
                            'class' AS source
                            FROM tbl_class
                            WHERE tbl_class_lng='$lng' 
                            AND tbl_class_price='0' 
                            AND tbl_class_status='1' 
                            AND tbl_class_department='$code'
                            AND tbl_class_title LIKE '%$keyword%'
                            UNION ALL
                            SELECT 
                                tbl_mohtava.tbl_mohtava_id AS item_id, 
                                tbl_mohtava.tbl_mohtava_title AS title, 
                                'mohtava' AS source
                            FROM tbl_mohtava
                            WHERE tbl_mohtava_sellprice='0' 
                            AND tbl_mohtava_statustext='4' 
                            AND tbl_mohtava_department LIKE '%$code%' 
                            AND tbl_mohtava_title LIKE '%$keyword%'
                            AND tbl_mohtava_lng='$lng';
                            ");
                    } else if (!empty($_GET['price']) && $_GET['price'] == 'notfree') {

                        $result_for_count = QueryExpert("SELECT 
                            tbl_class.tbl_class_id AS item_id, 
                            tbl_class.tbl_class_title AS title, 
                            'class' AS source
                            FROM tbl_class
                            WHERE tbl_class_lng='$lng' 
                            AND tbl_class_price !='0' 
                            AND tbl_class_status='1' 
                            AND tbl_class_department='$code'
                            AND tbl_class_title LIKE '%$keyword%'
                            UNION ALL
                            SELECT 
                                tbl_mohtava.tbl_mohtava_id AS item_id, 
                                tbl_mohtava.tbl_mohtava_title AS title, 
                                'mohtava' AS source
                            FROM tbl_mohtava
                            WHERE tbl_mohtava_sellprice !='0' 
                            AND tbl_mohtava_statustext='4' 
                            AND tbl_mohtava_department LIKE '%$code%' 
                            AND tbl_mohtava_title LIKE '%$keyword%'
                            AND tbl_mohtava_lng='$lng';
                            ");
                    } else {
                        $result_for_count = QueryExpert("SELECT 
                            tbl_class.tbl_class_id AS item_id, 
                            tbl_class.tbl_class_title AS title, 
                            'class' AS source
                            FROM tbl_class
                            WHERE tbl_class_lng='$lng' 
                            AND tbl_class_status='1' 
                            AND tbl_class_department='$code'
                            AND tbl_class_title LIKE '%$keyword%'
                            UNION ALL
                            SELECT 
                                tbl_mohtava.tbl_mohtava_id AS item_id, 
                                tbl_mohtava.tbl_mohtava_title AS title, 
                                'mohtava' AS source
                            FROM tbl_mohtava
                            WHERE  tbl_mohtava_statustext='4' 
                            AND tbl_mohtava_department LIKE '%$code%' 
                             AND tbl_mohtava_title LIKE '%$keyword%'
                            AND tbl_mohtava_lng='$lng'
                        ");
                    }
                } elseif (!empty($_GET['section']) && $_GET['section'] == 'course') {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {
                        $result_for_count = QueryExpert("SELECT * From tbl_class where tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_price='0' and tbl_class_status='1' and tbl_class_department='$code' ");
                    } elseif (!empty($_GET['price']) && $_GET['price'] == 'notfree') {
                        $result_for_count = QueryExpert("SELECT * From tbl_class where tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_price !='0' and tbl_class_status='1' and tbl_class_department='$code' ");
                    } else {
                        $result_for_count = QueryExpert("SELECT * From tbl_class where tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_status='1' and tbl_class_department='$code' ");
                    }
                } else {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {
                        $result_for_count = QueryExpert("SELECT * From tbl_mohtava Where  tbl_mohtava_title LIKE '%$keyword%' and tbl_mohtava_sellprice='0' and tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ");
                    } elseif (!empty($_GET['price']) && $_GET['price'] == 'notfree') {
                        $result_for_count = QueryExpert("SELECT * From tbl_mohtava Where  tbl_mohtava_title LIKE '%$keyword%' and tbl_mohtava_sellprice !='0' and tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ");
                    } else {
                        $result_for_count = QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_title LIKE '%$keyword%' and tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ");
                    }
                }


                $count_result = mysqli_num_rows($result_for_count);
                if ($count_result == 0) {
                    echo '<div class="no_result text-right mb-5">' . $lang[130] . '</div>';
                }

                $start_contnet =  (($page - 1) * $show_per_page);
                $page_number = ceil($count_result / $show_per_page);
                if (!empty($_GET['section']) && $_GET['section'] == 'all') {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {
                        $result = QueryExpert("
                        SELECT item_id, item_code, item_price, item_shortcontent, item_img, title, source 
                        FROM (
                            SELECT 
                                tbl_class.tbl_class_id AS item_id, 
                                tbl_class.tbl_class_code AS item_code, 
                                tbl_class.tbl_class_price AS item_price,
                                tbl_class.tbl_class_shortcontent AS item_shortcontent,
                                tbl_class.tbl_class_img AS item_img,
                                tbl_class.tbl_class_title AS title, 
                                'class' AS source,
                                tbl_class.tbl_class_order AS order_field,
                                tbl_class.tbl_class_id AS order_id
                            FROM tbl_class
                            WHERE tbl_class_lng='$lng' 
                            AND tbl_class_status='1' 
                            AND tbl_class_department='$code' 
                            AND tbl_class_title like '%$keyword%' 
                            AND tbl_class_price='0'

                            UNION ALL

                            SELECT 
                                tbl_mohtava.tbl_mohtava_id AS item_id, 
                                tbl_mohtava.tbl_mohtava_code AS item_code, 
                                tbl_mohtava.tbl_mohtava_sellprice AS item_price,
                                tbl_mohtava.tbl_mohtava_shortcontent AS item_shortcontent,
                                tbl_mohtava.tbl_mohtava_file AS item_img,
                                tbl_mohtava.tbl_mohtava_title AS title, 
                                'mohtava' AS source,
                                NULL AS order_field,
                                tbl_mohtava.tbl_mohtava_id AS order_id
                            FROM tbl_mohtava
                            WHERE tbl_mohtava_statustext='4' 
                            AND tbl_mohtava_department LIKE '%$code%' 
                            AND tbl_mohtava_title LIKE '%$keyword%' 
                            AND tbl_mohtava_lng='$lng' 
                            AND tbl_mohtava_sellprice='0'
                        ) t
                        ORDER BY order_field DESC, order_id DESC
                        LIMIT $start_contnet,$show_per_page
                    ");
                    } else if (!empty($_GET['price']) && $_GET['price'] == 'notfree') {
                        $result = QueryExpert("
                                SELECT item_id, item_code, item_price, item_shortcontent, item_img, title, source 
                                FROM (
                                    SELECT 
                                        tbl_class.tbl_class_id AS item_id, 
                                        tbl_class.tbl_class_code AS item_code, 
                                        tbl_class.tbl_class_price AS item_price,
                                        tbl_class.tbl_class_shortcontent AS item_shortcontent,
                                        tbl_class.tbl_class_img AS item_img,
                                        tbl_class.tbl_class_title AS title, 
                                        'class' AS source,
                                        tbl_class.tbl_class_order AS order_field,
                                        tbl_class.tbl_class_id AS order_id
                                    FROM tbl_class
                                    WHERE tbl_class_lng='$lng' 
                                    AND tbl_class_status='1' 
                                    AND tbl_class_department='$code' 
                                    AND tbl_class_title like '%$keyword%' 
                                    AND tbl_class_price!='0'

                                    UNION ALL

                                    SELECT 
                                        tbl_mohtava.tbl_mohtava_id AS item_id, 
                                        tbl_mohtava.tbl_mohtava_code AS item_code, 
                                        tbl_mohtava.tbl_mohtava_sellprice AS item_price,
                                        tbl_mohtava.tbl_mohtava_shortcontent AS item_shortcontent,
                                        tbl_mohtava.tbl_mohtava_file AS item_img,
                                        tbl_mohtava.tbl_mohtava_title AS title, 
                                        'mohtava' AS source,
                                        NULL AS order_field,
                                        tbl_mohtava.tbl_mohtava_id AS order_id
                                    FROM tbl_mohtava
                                    WHERE tbl_mohtava_statustext='4' 
                                    AND tbl_mohtava_department LIKE '%$code%' 
                                    AND tbl_mohtava_title LIKE '%$keyword%' 
                                    AND tbl_mohtava_lng='$lng' 
                                    AND tbl_mohtava_sellprice!='0'
                                ) t
                                ORDER BY order_field DESC, order_id DESC
                                LIMIT $start_contnet,$show_per_page
                            ");
                    } else {
                        $result = QueryExpert("
                                SELECT item_id, item_code, item_price, item_shortcontent, item_img, title, source 
                                FROM (
                                    SELECT 
                                        tbl_class.tbl_class_id AS item_id, 
                                        tbl_class.tbl_class_code AS item_code, 
                                        tbl_class.tbl_class_price AS item_price,
                                        tbl_class.tbl_class_shortcontent AS item_shortcontent,
                                        tbl_class.tbl_class_img AS item_img,
                                        tbl_class.tbl_class_title AS title, 
                                        'class' AS source,
                                        tbl_class.tbl_class_order AS order_field,
                                        tbl_class.tbl_class_id AS order_id
                                    FROM tbl_class
                                    WHERE tbl_class_lng='$lng' 
                                    AND tbl_class_status='1' 
                                    AND tbl_class_department='$code'
                                    AND tbl_class_title like '%$keyword%' 

                                    UNION ALL

                                    SELECT 
                                        tbl_mohtava.tbl_mohtava_id AS item_id, 
                                        tbl_mohtava.tbl_mohtava_code AS item_code, 
                                        tbl_mohtava.tbl_mohtava_sellprice AS item_price,
                                        tbl_mohtava.tbl_mohtava_shortcontent AS item_shortcontent,
                                        tbl_mohtava.tbl_mohtava_file AS item_img,
                                        tbl_mohtava.tbl_mohtava_title AS title, 
                                        'mohtava' AS source,
                                        NULL AS order_field,
                                        tbl_mohtava.tbl_mohtava_id AS order_id
                                    FROM tbl_mohtava
                                    WHERE tbl_mohtava_statustext='4' 
                                    AND tbl_mohtava_department LIKE '%$code%' 
                                    AND tbl_mohtava_title LIKE '%$keyword%' 
                                    AND tbl_mohtava_lng='$lng'
                                ) t
                                ORDER BY order_field DESC, order_id DESC
                                LIMIT $start_contnet,$show_per_page
                            ");
                    }
                } else if (!empty($_GET['section']) && $_GET['section'] == 'course') {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {
                        $result = QueryExpert("SELECT * From tbl_class where  tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_price='0' and tbl_class_status='1' and tbl_class_department='$code' ORDER BY tbl_class_order DESC , tbl_class_id DESC  limit $start_contnet,$show_per_page");
                    } else if (!empty($_GET['price']) && $_GET['price'] == 'notfree') {
                        $result = QueryExpert("SELECT * From tbl_class where  tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_price !='0' and tbl_class_status='1' and tbl_class_department='$code' ORDER BY tbl_class_order DESC , tbl_class_id DESC  limit $start_contnet,$show_per_page");
                    } else {
                        $result = QueryExpert("SELECT * From tbl_class where  tbl_class_title LIKE '%$keyword%' and tbl_class_lng='$lng' and tbl_class_status='1' and tbl_class_department='$code' ORDER BY tbl_class_order DESC , tbl_class_id DESC  limit $start_contnet,$show_per_page");
                    }
                } else {
                    if (!empty($_GET['price']) && $_GET['price'] == 'free') {

                        $result =  QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_title LIKE '%$keyword%' and tbl_mohtava_sellprice='0' and tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ORDER BY tbl_mohtava_id DESC   limit $start_contnet,$show_per_page ");
                    } else if (!empty($_GET['price']) && $_GET['price'] == 'notfree') {
                        $result =  QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_title LIKE '%$keyword%' and  tbl_mohtava_sellprice !='0' and tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ORDER BY tbl_mohtava_id DESC   limit $start_contnet,$show_per_page ");
                    } else {
                        $result =  QueryExpert("SELECT * From tbl_mohtava Where tbl_mohtava_title LIKE '%$keyword%' and  tbl_mohtava_statustext='4' and tbl_mohtava_department like '%$code%' and tbl_mohtava_lng='$lng' ORDER BY tbl_mohtava_id DESC   limit $start_contnet,$show_per_page ");
                    }
                }



                while ($read = mysqli_fetch_array($result)) {
                    if (!empty($_GET['section']) && $_GET['section'] == 'all') {
                        if ($read['source'] === 'class') {
                            $img = explode(",", $read['item_img']);
                            $text = $read['item_shortcontent'];
                            echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                            <div class="itemclass">
                            <div class="img" style="background: url(' . $site_url . 'upload/' . $img[0] . ');">
                        </div>
                                <div class="content">
                                    <h1><a href="' . $site_url . $lng . '/class/' . $read['item_code'] . '/' . title($read['title']) . '/">' . $read['title'] . '</a></h1>
                                    <p>' . (!empty($text) ? strip_tags($text) : '') . ' </p>
                                    <div class="tools">
                                        <span class="icon-tag"></span>
                                         <p class="translate">' . ($read['item_price'] != '0' && !empty($read['item_price']) ? number_format($read['item_price']) : '') . ' </p>
                                             <p>' . ($read['item_price'] != '0' && !empty($read['item_price']) ?  $lang[134] : $lang[156]) . ' </p>
                                        
                                    </div>
                                    <a href="' . $site_url . $lng . '/class/' . $read['item_code'] . '/' . title($read['title']) . '/"><button>' . $lang[160] . '</button></a>
                                </div>
                            </div>
                        </div>';
                        } else {

                            $img = explode(",", $read['item_img']);
                            $text = $read['item_shortcontent'];
                            echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                                <div class="itemclass">
                                <div class="img" style="background: url(' . $site_url . 'upload/' . $img[2] . ');">
                            </div>
                                    <div class="content">
                                        <h1><a href="' . $site_url . $lng . '/lib/' . $read['item_code'] . '/' . title($read['title']) . '/">' . $read['title'] . '</a></h1>
                                        <p>' .  (!empty($text) ? strip_tags($text) : '') . ' </p>
                                        <div class="tools">
                                            <span class="icon-tag"></span>
                                             <p class="translate">' . ($read['item_price'] != '0' && !empty($read['item_price']) ? number_format($read['item_price']) : '') . ' </p>
                                             <p>' . ($read['item_price'] != '0' && !empty($read['item_price']) ?  $lang[134] : $lang[156]) . ' </p>
                                            
                                        </div>
                                        <a href="' . $site_url . $lng . '/lib/' . $read['item_code'] . '/' . title($read['title']) . '/"><button>' . $lang[161] . '</button></a>
                                    </div>
                                </div>
                            </div>';
                        }
                    } else if (!empty($_GET['section']) && $_GET['section'] == 'course') {
                        $img = explode(",", $read['tbl_class_img']);
                        $text = $read['tbl_class_shortcontent'];
                        echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                            <div class="itemclass">
                            <div class="img" style="background: url(' . $site_url . 'upload/' . $img[0] . ');">
                        </div>
                                <div class="content">
                                    <h1><a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/">' . $read['tbl_class_title'] . '</a></h1>
                                    <p>' . (!empty($text) ? strip_tags($text) : '') . ' </p>
                                    <div class="tools">
                                        <span class="icon-tag"></span>
                                        <p class="translate">' . (!empty($read['item_price']) && $read['item_price'] != '0' ? @number_format($read['item_price']) : '') . ' </p>
                                             <p>' . (!empty($read['item_price']) && $read['item_price'] != '0' ?  $lang[134] : $lang[156]) . ' </p>
                                        
                                    </div>
                                    <a href="' . $site_url . $lng . '/class/' . $read['tbl_class_code'] . '/' . title($read['tbl_class_title']) . '/"><button>' . $lang[160] . '</button></a>
                                </div>
                            </div>
                        </div>';
                    } else {
                        $img = explode(",", $read['tbl_mohtava_file']);
                        $text = $read['tbl_mohtava_shortcontent'];
                        echo '<div class="col-12 col-lg-4 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                                <div class="itemclass">
                                <div class="img" style="background: url(' . $site_url . 'upload/' . $img[2] . ');">
                            </div>
                                    <div class="content">
                                        <h1><a href="' . $site_url . $lng . '/lib/' . $read['tbl_mohtava_code'] . '/' . title($read['tbl_mohtava_title']) . '/">' . $read['tbl_mohtava_title'] . '</a></h1>
                                        <p>' . (!empty($text) ? strip_tags($text) : '') . ' </p>
                                        <div class="tools">
                                            <p class="translate">' . ($read['tbl_mohtava_sellprice'] != '0' && !empty($read['tbl_mohtava_sellprice']) ? (!empty($read['tbl_mohtava_sellprice']) ? number_format($read['tbl_mohtava_sellprice']) : '0') . $lang[134] : $lang[156]) . ' </p>
                                            
                                        </div>
                                        <a href="' . $site_url . $lng . '/lib/' . $read['tbl_mohtava_code'] . '/' . title($read['tbl_mohtava_title']) . '/"><button>' . $lang[161] . '</button></a>
                                    </div>
                                </div>
                            </div>';
                    }
                }



                if ($count_result > $show_per_page) {
                    if ($page == 1) {
                        $back_page_number = 2;
                    } else {
                        $back_page_number = $page;
                    }

                    echo '<div class="col-12 col-md-12 ">
                            <div class="pagination-style">
                                <div class="page-item"><a class="page_back" href="' . $site_url . 'cat_site.php?lng=' . $lng . '&do=cat&code=' . $code . '&page=' . ($back_page_number - 1) . (!empty($keyword) ? '&keyword=' . $keyword : '') . '">' . $lang[142] . '</a></div>
                                <div class="page-item">' . $lang[144] . '&nbsp;' . $page . '&nbsp;' . $lang[145] . '&nbsp;' . $page_number . '</div>
                                <div class="page-item"><a class="page_next" href="' . $site_url . 'cat_site.php?lng=' . $lng . '&do=cat&code=' . $code . '&page=' . ($page + 1)  . (!empty($keyword) ? '&keyword=' . $keyword : '') . '">' . $lang[143] . '</a></div>
                            </div>
                          </div></div>';
                }
                echo '</div>';
            } else if (isset($do) && $do == 'filter') {
                if (isset($_POST['cat'])) {
                    $code = sqi($_POST['cat']);
                    $price = sqi($_POST['price']);
                    $section = sqi($_POST['section']);
                    $keyword = sqi($_POST['keyword']);
                    header("location: $site_url/cat_site.php?lng=$lng&do=cat&code=$code&price=$price&section=$section&keyword=$keyword");
                    exit();
                }
            } else if (isset($do) && $do == 'search') {
                ?>
                <div class="col-12 col-lg-3">
                    <form method="POST" action="<?= $site_url ?>cat_site.php?do=filter">
                        <div class="accordion-item">

                            <div id="panelsStayOpen" class="accordion-collapse collapse in show" aria-labelledby="panelsStayOpen-heading">
                                <div class="accordion-body">
                                    <input type="text" name="keyword" placeholder="<?= $lang[136] ?>" value="<?= (!empty($keyword) ? $keyword : '') ?>">
                                </div>
                                <div class="accordion-body">
                                    <?php
                                    $result = QueryExpert("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$lng' ORDER BY tbl_categories_id DESC ");
                                    while ($read = mysqli_fetch_array($result)) {

                                        echo '<label><input type="radio" name="cat" value="' . $read['tbl_categories_code'] . '" ' . (!empty($code) && $code == $read['tbl_categories_code'] ? 'checked' : '') . '> <span></span>' . $read['tbl_categories_title'] . '</label>';
                                    }
                                    ?>
                                </div>
                                <div class="accordion-body">
                                    <label><input type="radio" name="price" value="free" <?= (!empty($_GET['price']) && $_GET['price'] == 'free' ? 'checked' : '') ?>> <span></span><?= $lang[156] ?></label>
                                    <label><input type="radio" name="price" value="notfree" <?= (!empty($_GET['price']) &&  $_GET['price'] == 'notfree' ? 'checked' : '') ?>> <span></span><?= $lang[158] ?></label>
                                    <label><input type="radio" name="price" value="all" <?= (empty($_GET['price']) || $_GET['price'] == 'all'   ? 'checked' : '') ?>> <span></span><?= $lang[159] ?></label>
                                </div>

                                <div class="accordion-body">
                                    <label><input type="radio" name="section" value="course" <?= (!empty($_GET['section']) && $_GET['section'] == 'course' ? 'checked' : '') ?>> <span></span><?= $lang[166] ?></label>
                                    <label><input type="radio" name="section" value="lib" <?= (!empty($_GET['section']) &&  $_GET['section'] == 'lib' ? 'checked' : '') ?>> <span></span><?= $lang[167] ?></label>
                                    <label><input type="radio" name="section" value="all" <?= (empty($_GET['section']) || $_GET['section'] == 'all'   ? 'checked' : '') ?>> <span></span><?= $lang[168] ?></label>
                                </div>

                            </div>
                            <button type="submit" class="clear-btn shadow"><?= $lang[132] ?></button>
                        </div>
                    </form>

                </div>
            <?php

                echo '<div class=" col-12 col-lg-9">
                                    <div class="row">';
                $result_for_count = QueryExpert("SELECT 
                            tbl_class.tbl_class_id AS item_id, 
                            tbl_class.tbl_class_title AS title, 
                            'class' AS source
                            FROM tbl_class
                            WHERE tbl_class_lng='$lng' 
                            AND tbl_class_status='1' 
                            AND tbl_class_title LIKE '%$keyword%' 
                            UNION ALL
                            SELECT 
                                tbl_mohtava.tbl_mohtava_id AS item_id, 
                                tbl_mohtava.tbl_mohtava_title AS title, 
                                'mohtava' AS source
                            FROM tbl_mohtava
                            WHERE  tbl_mohtava_statustext='4' 
                            AND tbl_mohtava_title LIKE '%$keyword%' 
                            AND tbl_mohtava_lng='$lng'
                        ");

                $count_result = mysqli_num_rows($result_for_count);

                if ($count_result == 0) {
                    echo '<div class="no_result text-right mb-4">' . $lang[130] . '</div>';
                }

                $start_contnet =  (($page - 1) * $show_per_page);
                $page_number = ceil($count_result / $show_per_page);

                $result = QueryExpert("
                SELECT item_id, item_code, item_price, item_shortcontent, item_img, title, source 
                FROM (
                    SELECT 
                        tbl_class.tbl_class_id AS item_id, 
                        tbl_class.tbl_class_code AS item_code, 
                        tbl_class.tbl_class_price AS item_price,
                        tbl_class.tbl_class_shortcontent AS item_shortcontent,
                        tbl_class.tbl_class_img AS item_img,
                        tbl_class.tbl_class_title AS title, 
                        'class' AS source,
                        tbl_class.tbl_class_order AS order_field,
                        tbl_class.tbl_class_id AS order_id
                    FROM tbl_class
                    WHERE tbl_class_lng='$lng' 
                    AND tbl_class_status='1' 
                    AND tbl_class_title like '%$keyword%'

                    UNION ALL

                    SELECT 
                        tbl_mohtava.tbl_mohtava_id AS item_id, 
                        tbl_mohtava.tbl_mohtava_code AS item_code, 
                        tbl_mohtava.tbl_mohtava_sellprice AS item_price,
                        tbl_mohtava.tbl_mohtava_shortcontent AS item_shortcontent,
                        tbl_mohtava.tbl_mohtava_file AS item_img,
                        tbl_mohtava.tbl_mohtava_title AS title, 
                        'mohtava' AS source,
                        NULL AS order_field,
                        tbl_mohtava.tbl_mohtava_id AS order_id
                    FROM tbl_mohtava
                    WHERE tbl_mohtava_statustext='4' 
                    AND tbl_mohtava_title LIKE '%$keyword%' 
                    AND tbl_mohtava_lng='$lng'
                ) t
                ORDER BY order_field DESC, order_id DESC
                LIMIT $start_contnet,$show_per_page
            ");

                while ($read = mysqli_fetch_array($result)) {
                    if ($read['source'] === 'class') {
                        $img = explode(",", $read['item_img']);
                        $text = $read['item_shortcontent'];
                        echo '<div class="col-12 col-lg-3 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                            <div class="itemclass">
                            <div class="img" style="background: url(' . $site_url . 'upload/' . $img[0] . ');">
                        </div>
                                <div class="content">
                                    <h1><a href="' . $site_url . $lng . '/class/' . $read['item_code'] . '/' . title($read['title']) . '/">' . $read['title'] . '</a></h1>
                                    <p>' . (!empty($text) ? strip_tags($text) : '') . ' </p>
                                    <div class="tools">
                                        <p class="translate">' . ($read['item_price'] != '0' && !empty($read['item_price']) ? number_format($read['item_price']) : '') . ' </p>
                                        <p>' . ($read['item_price'] != '0' && !empty($read['item_price']) ?  $lang[134] : $lang[156]) . ' </p>
                                    </div>
                                    <a href="' . $site_url . $lng . '/class/' . $read['item_code'] . '/' . title($read['title']) . '/"><button>' . $lang[160] . '</button></a>
                                </div>
                            </div>
                        </div>';
                    } else {

                        $img = explode(",", $read['item_img']);
                        $text = $read['item_shortcontent'];
                        echo '<div class="col-12 col-lg-3 col-md-12 padding-class " data-aos="fade-up" data-aos-duration="1000">
                                <div class="itemclass">
                                <div class="img" style="background: url(' . $site_url . 'upload/' . $img[2] . ');">
                            </div>
                                    <div class="content">
                                        <h1><a href="' . $site_url . $lng . '/lib/' . $read['item_code'] . '/' . title($read['title']) . '/">' . $read['title'] . '</a></h1>
                                        <p>' .  (!empty($text) ? strip_tags($text) : '') . ' </p>
                                        <div class="tools">
                                           <p class="translate">' . ($read['item_price'] != '0' && !empty($read['item_price']) ? number_format($read['item_price']) : '') . ' </p>
                                             <p>' . ($read['item_price'] != '0' && !empty($read['item_price']) ?  $lang[134] : $lang[156]) . ' </p>
                                        </div>
                                        <a href="' . $site_url . $lng . '/lib/' . $read['item_code'] . '/' . title($read['title']) . '/"><button>' . $lang[161] . '</button></a>
                                    </div>
                                </div>
                            </div>';
                    }
                }
                if ($count_result > $show_per_page) {
                    if ($page == 1) {
                        $back_page_number = 2;
                    } else {
                        $back_page_number = $page;
                    }

                    echo '<div class="col-12 col-md-12 ">
                            <div class="pagination-style">
                                <div class="page-item"><a class="page_back" href="' . $site_url . 'cat_site.php?lng=' . $lng . '&do=cat&code=' . $code . '&page=' . ($back_page_number - 1) . '">' . $lang[142] . '</a></div>
                                <div class="page-item">' . $lang[144] . '&nbsp;' . $page . '&nbsp;' . $lang[145] . '&nbsp;' . $page_number . '</div>
                                <div class="page-item"><a class="page_next" href="' . $site_url . 'cat_site.php?lng=' . $lng . '&do=cat&code=' . $code . '&page=' . ($page + 1)  . '">' . $lang[143] . '</a></div>
                            </div>
                          </div></div>';
                }
                echo '</div>';
            } else {
                $result_for_count = QueryExpert("SELECT * from tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$lng'  ");
                $count_result = mysqli_num_rows($result_for_count);
                $start_contnet =  ($page - 1) * $show_per_page;
                $page_number = ceil($count_result / $show_per_page);

                $result = QueryExpert("SELECT * from tbl_categories Where tbl_categories_type = '5' and tbl_categories_lng='$lng' order by tbl_categories_order DESC , tbl_categories_id DESC ");
                while ($read = mysqli_fetch_array($result)) {
                    echo '<div class="col-12 col-md-6 col-lg-3" data-aos="fade-up" data-aos-duration="1000">
                        <a href="' . $site_url .  'cat_site.php?lng=' . $lng . '&do=cat&code=' . $read['tbl_categories_code'] . '">'
                        . '<div class="cat_item">'
                        . '<div class="cat_img" style="background: url(' . $site_url . 'upload/' . $read['tbl_categories_img'] . ');"></div>'
                        . '<h2>' . $read['tbl_categories_title'] . '</h2></a>'
                        . '</div>'
                        . '</div>';
                }
                echo '</div>';
            }
            if ($count_result > $show_per_page) {
                if ($page == 1) {
                    $back_page_number = 2;
                } else {
                    $back_page_number = $page;
                }

                // echo '<div class="col-12 col-md-12 ">
                //     <div class="pagination-style">
                //         <div class="page-item"><a class="page_back" href="' . $site_url . 'cat/' . (!empty($do) ? $do . '/' : '') . $code . '/' . ($back_page_number - 1) . '/">' . $lang[75] . '</a></div>
                //         <div class="page-item">' . $lang[72] . '&nbsp;' . $page . '&nbsp;' . $lang[73] . '&nbsp;' . $page_number . '</div>
                //         <div class="page-item"><a class="page_next" href="' . ($page_number == $page ? '#' : $site_url . 'cat/' . (!empty($do) ? $do . '/' : '') . $code . '/' . ($page + 1))  . '/">' . $lang[74] . '</a></div>
                //     </div>
                //   </div></div>';
            }
            ?>
        </div>
    </div>
</section>
<?php
include './footer_site.inc.php';
?>
