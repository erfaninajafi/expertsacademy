<section class="section-content">
    <div class="container">
        <div class="row justify-content-center">
            <?php
            // A page may include more than one tabs instance, including legacy repeats.
            $GLOBALS['exacdTabSectionIndex'] = ($GLOBALS['exacdTabSectionIndex'] ?? 0) + 1;
            $tabPrefix = 'cms-tabs-' . $GLOBALS['exacdTabSectionIndex'];
            $key = 0;
            $tabHtml = '<ul class="nav nav-tabs" id="' . $tabPrefix . '-list" role="tablist">';
            $tabContentHtml = '<div class="tab-content" id="' . $tabPrefix . '-content">';
            while ($readTab = mysqli_fetch_array($resultContent)) {
                $img = explode(',', (string) $readTab['tbl_content_img']);
                $active = $key === 0 ? ' active' : '';
                $show = $key === 0 ? ' show' : '';
                $tabId = $tabPrefix . '-' . $key;
                $title = htmlspecialchars((string) $readTab['tbl_content_title'], ENT_QUOTES, 'UTF-8');
                $tabHtml .= '<li class="nav-item" role="presentation"><button class="nav-link' . $active . '" id="' . $tabId . '-tab" data-bs-toggle="tab" data-bs-target="#' . $tabId . '" type="button" role="tab" aria-controls="' . $tabId . '" aria-selected="' . ($key === 0 ? 'true' : 'false') . '">' . $title . '</button></li>';
                $tabContentHtml .= '<div class="tab-pane fade' . $active . $show . '" id="' . $tabId . '" role="tabpanel" aria-labelledby="' . $tabId . '-tab"><div class="row"><div class="col-12 col-lg-6"><h2 class="mb-3">' . $title . '</h2>' . $readTab['tbl_content_text'] . '</div><div class="col-12 col-lg-6"><img src="' . $site_url . 'upload_site/' . $img[0] . '" class="img-fluid" alt="' . $title . '"></div></div></div>';
                $key++;
            }
            echo '<div class="tabsetion">' . $tabHtml . '</ul>' . $tabContentHtml . '</div></div>';
            ?>
        </div>
    </div>
</section>
