<?php
include 'header.inc.php';
?>

<section class="section-margin-top bg-section-two">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultPage = Query("SELECT tbl_content_text,tbl_content_file,tbl_content_img,tbl_content_title From tbl_content Where tbl_content_code='362C321' and tbl_content_fld1='$lng' ");
            $readPage = mysqli_fetch_array($resultPage);
            $img = explode(",", $readPage['tbl_content_img']);
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><div class="content-model-one padding-top"><h1 class="mb-3 orang">' . $readPage['tbl_content_title'] . '</h1>' . $readPage['tbl_content_text'] . '</div></div>';
            echo '<div class="col-12 col-lg-6 pt-3 pb-3"><img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readPage['tbl_content_title'] . '" class="img-fluid"></div>';
            ?>
        </div>
    </div>
    </div>
</section>

<section class="mt-3">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-12">
                <p class="title-index">
                    <?= $lang[89] ?>
                </p>
            </div>
            <div class="col-12 col-lg-12">
                <div class="customer">
                    <?php
                    $resultCustomer = Query("select * from tbl_content where tbl_content_type='26' and tbl_content_fld1='$lng' order by tbl_content_order ASC , tbl_content_id DESC ");
                    while ($readCustomer = mysqli_fetch_array($resultCustomer)) {
                        $img = explode(",", $readCustomer['tbl_content_img']);
                        echo '<div>' . (!empty($readCustomer['tbl_content_pageurl']) ? '<a href="' . $readCustomer['tbl_content_pageurl'] . '" target="_blank">' : '') . '<img src="' . $site_url . 'upload/' . $img[0] . '" alt="' . $readCustomer['tbl_content_title'] . '">' . (!empty($readCustomer['tbl_content_pageurl']) ? '</a>' : '') . '</div>';
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="row justify-content-center">
            <?php
            $resultTab = Query("select * from tbl_content where tbl_content_type='27' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
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
<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-6">
                <form class="form-style">
                    <div class="form-group">
                        <input type="text" name="fld1" placeholder="<?= $lang[91] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld2" placeholder="<?= $lang[92] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld3" placeholder="<?= $lang[93] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld4" placeholder="<?= $lang[94] ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <input type="text" name="fld5" placeholder="<?= $lang[95] ?>" class="form-control">
                    </div>
                    <input type="submit" name="submit" value="<?= $lang[96] ?>" class="btn btn-success">
                </form>
            </div>
            <div class="col-12 col-lg-6">
                <h3><?= $lang[90] ?></h3>
                <?php
                $resultTab = Query("select * from tbl_content where tbl_content_type='28' and tbl_content_fld1='$lng' order by tbl_content_order ASC ");
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
<?php
include 'footer.inc.php';
?>