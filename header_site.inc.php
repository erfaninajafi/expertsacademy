<?php
ob_start();
include './includer_site.php';
?>
<!DOCTYPE html>
<html <?= ($lng == 'fa' ? 'lang="fa" dir="rtl"' : 'lang="en" dir="ltr"') ?>>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <?php if (empty($studioSeoHead)): ?>
    <meta name="keywords" content="<?= (!empty($keywords) ? $keywords : '') ?>">
    <meta name="description" content="<?= (!empty($discription) ? $discription : '') ?>">
    <?php endif; ?>
    <?= $studioSeoHead ?? '' ?>
    <link rel="icon" type="image/png" href="<?= $site_url ?>asset_site/images/fav.png">
    <noscript>
        <META HTTP-EQUIV="Refresh" CONTENT="0; URL=noscript.php">
    </noscript>
    <title><?= (!empty($siteTitlePage) ? $siteTitlePage : $siteTitle) ?></title>
    <link rel="stylesheet" href="<?= $site_url ?>asset_site/css/bootstrap.min.css">
    <?php if ($lng == 'en') { ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cal+Sans&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <?php } else { ?>
        <link rel="stylesheet" href="<?= $site_url ?>asset_site/css/bootstrap.rtl.min.css">
    <?php } ?>
    <link rel="stylesheet" href="<?= $site_url ?>asset_site/css/jquery.lightbox.css">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" />

    <link rel="stylesheet" href="<?= $site_url ?>asset_site/css/style_<?= $lng ?>.css?version=<?= rand(10, 1000) ?>">
    <?php if (isset($studioDocument) && $studioDocument !== null) : ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($site_url . 'asset_site/css/page-studio.css', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
    <?php endif; ?>
</head>

<body>
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">پخش ویدیو</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="بستن"></button>
                </div>
                <div class="modal-body text-center">
                    <!-- ویدیو در اینجا بارگذاری می‌شود -->
                    <div id="videoContainer"></div>
                </div>
            </div>
        </div>
    </div>
    <header>
        <div class="top-language">
            <a href="<?= $site_url ?><?= ($lng == 'fa' ? 'en/' : 'fa/') ?>"><?= ($lng == 'fa' ? 'en' : 'fa') ?></a>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-3 col-lg-4">
                    <div class="logo">
                        <?php if ($index == 'index.php') {
                            echo '<h1>';
                        } ?>
                        <a href="<?= $site_url . $lng ?>/">
                            <img src="<?= $site_url ?>asset_site/images/logo.svg" class="img-fluid" alt="Experts Academy">
                        </a>
                        <?php if ($index == 'index.php') {
                            echo '</h1>';
                        } ?>
                    </div>
                </div>
                <div class="col-9 col-lg-8 relative">

                    <div class="top-tools">
                        <div class="login-top">
                            <span class="icon-user-o"></span>
                            <a href="https://exacd.igx.ir/?_lang=fa#login"><?= $lang[2] ?></a>
                        </div>
                        <div class="search-top">
                            <form method="GET" action="https://app.expertsacademy.ir/lms/courses">
                                <input type="text" name="title" placeholder="<?= $lang[1] ?>">
                                <input type="hidden" name="do" value="search">
                                <button>
                                    <span class="icon-search"></span>
                                </button>
                            </form>
                        </div>
                        <?php if ($site_url == 'none@') { ?>
                            <div class="cat-top">
                                <div class="main-item">
                                    <a href="#"><?= $lang[3] ?></a>
                                    <span class="icon-arrow_drop_down"></span>
                                    <div class="more-item">
                                        <ul>
                                            <?php
                                            $resultGanj = QueryExpert("SELECT * From tbl_categories Where tbl_categories_type = '5' and tbl_categories_parentid = '0' and tbl_categories_lng='$lng' ORDER BY tbl_categories_order ASC");
                                            while ($readGanj = mysqli_fetch_array($resultGanj)) {
                                                echo '<li><a href="' . $site_url . 'cat_site.php?lng=' . $lng . '&do=cat&code=' . $readGanj['tbl_categories_code'] . '">' . $readGanj['tbl_categories_title'] . '</a></li>';
                                            }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>


                    </div>
                </div>

                <div class="col-12 col-lg-12">
                    <nav class="main-menu">
                        <ul>
                            <?php
                            $resultMenu = Query("select * from tbl_menu where tbl_menu_parentid='0' and tbl_menu_lng='$lng' order by tbl_menu_order ASC");
                            while ($readMenu = mysqli_fetch_array($resultMenu)) {
                                $parentId = $readMenu['tbl_menu_code'];
                                $parentColor = $readMenu['tbl_menu_color'];
                                $parentColorText = $readMenu['tbl_menu_textcolor'];
                                echo '<li ' . (!empty($readMenu['tbl_menu_color']) ? 'style="border-color:' . $readMenu['tbl_menu_color'] . '"' : '') . '><a href="' . $readMenu['tbl_menu_link'] . '">' . $readMenu['tbl_menu_title'] . '</a><span class="icon-arrow_drop_down"></span>';
                                $resultMenuChild = Query("select * from tbl_menu where tbl_menu_parentid='$parentId' and tbl_menu_lng='$lng' order by tbl_menu_order ASC");
                                if (mysqli_num_rows($resultMenuChild) > 0) {
                                    echo '<div class="mega" ' . (!empty($parentColor) ? 'style="background-color:' . $parentColor . ';"' : '') . '><ul>';
                                    while ($readMenuChild = mysqli_fetch_array($resultMenuChild)) {
                                        echo '<li><a href="' . $readMenuChild['tbl_menu_link'] . '" ' . (!empty($parentColor) ? 'style="color:' . $parentColorText . ';"' : '') . '>' . $readMenuChild['tbl_menu_title'] . '</a></li>';
                                    }

                                    echo '</ul></div>';
                                }

                                echo '</li>';
                            }
                            ?>

                        </ul>
                    </nav>


                    <div class="othermenubgmobile">
                        <div class="sideMenu">
                            <div class="menuIcon">
                                <span></span>
                            </div>


                            <div class="sidebar-navigation">
                                <ul>
                                    <li><a href="https://exacd.igx.ir/?_lang=fa#login"><?= $lang[2] ?></a></li>
                                    <?php
                                    $resultMenu = Query("select * from tbl_menu where tbl_menu_parentid='0' and tbl_menu_lng='$lng' order by tbl_menu_order ASC");
                                    while ($readMenu = mysqli_fetch_array($resultMenu)) {
                                        $parentId = $readMenu['tbl_menu_code'];
                                        $parentColor = $readMenu['tbl_menu_color'];
                                        $parentColorText = $readMenu['tbl_menu_textcolor'];
                                        echo '<li><a href="' . $readMenu['tbl_menu_link'] . '">' . $readMenu['tbl_menu_title'] . '</a>';
                                        $resultMenuChild = Query("select * from tbl_menu where tbl_menu_parentid='$parentId' and tbl_menu_lng='$lng' order by tbl_menu_order ASC");
                                        if (mysqli_num_rows($resultMenuChild) > 0) {
                                            echo '<ul>';
                                            while ($readMenuChild = mysqli_fetch_array($resultMenuChild)) {
                                                echo '<li><a href="' . $readMenuChild['tbl_menu_link'] . '" >' . $readMenuChild['tbl_menu_title'] . '</a></li>';
                                            }

                                            echo '</ul>';
                                        }

                                        echo '</li>';
                                    }
                                    ?>



                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </header>
