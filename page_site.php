<?php
if (!isset($_GET['code']) || !is_string($_GET['code']) || $_GET['code'] === '') {
    require __DIR__ . '/404.php';
    exit;
}
include './includer_site.php';
if (isset($code) && $code !== '') {

    $result_pages = Query("Select * from tbl_content Where tbl_content_fld1='$lng'  and tbl_content_url = '$code'");
    // Legacy pages in the CMS have a code, but no URL column. Render them by code
    // when no content-page slug matches, so their preview links are usable.
    if (!$result_pages || mysqli_num_rows($result_pages) === 0) {
        $result_pages = Query("SELECT tbl_pages_title AS tbl_content_title,
            tbl_pages_text AS tbl_content_text
            FROM tbl_pages WHERE tbl_pages_code = '$code' AND tbl_pages_lng = '$lng' LIMIT 1");
        if (!$result_pages || mysqli_num_rows($result_pages) === 0) {
            require __DIR__ . '/404.php';
            exit;
        }
    }
    $read_pages = mysqli_fetch_array($result_pages);
    $title = $read_pages['tbl_content_title'];
    $navigation = '<span><a href="' . $site_url . '">' . $lang[13] . '</a></span>' . $title;
    @$pagetitle .= ' - ' . $title;
    include './header_site.inc.php';
?>
<section class="margin-top content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <?php
                    echo '<h1>' . $read_pages['tbl_content_title'] . '</h1>';
                    echo $read_pages['tbl_content_text'];
                    ?>

            </div>
        </div>
</section>
<?php
} else {
    require __DIR__ . '/404.php';
    exit;
}
include './footer_site.inc.php';
?>
