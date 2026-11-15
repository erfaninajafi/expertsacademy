<?php
include './includer_site.php';
$sectionInput = $_GET['section'] ?? null;
if ($sectionInput !== null && !is_string($sectionInput)) {
    require __DIR__ . '/404.php';
    exit;
}
try {
    $requestedPath = $sectionInput ?? (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    $redirect = (new \ExpertsAcademy\Studio\Repo\RedirectRepository())->match($requestedPath, (string) $lng);
    if ($redirect !== null) {
        header('Location: ' . (string) $redirect['to_path'], true, (int) $redirect['status_code']);
        exit;
    }
} catch (\Throwable $redirectError) {
    // Redirects are optional during a rolling deployment.
}
if ($sectionInput !== null) {
    $section = sqi($sectionInput);
    $result = Query("select * from tbl_categories where tbl_categories_type='4' and tbl_categories_url='$section' and tbl_categories_lng='$lng' ");
} else {
    $result = Query("select * from tbl_categories where tbl_categories_type='4' and tbl_categories_index='1' and tbl_categories_lng='$lng' ");
}
$read = $result ? mysqli_fetch_array($result) : null;
if (!$read) {
    require __DIR__ . '/404.php';
    exit;
}
$keywords = $read['tbl_categories_keyword'];
$discription = $read['tbl_categories_discription'];
$pageRow = $read;

$studioPage = null;
if (is_file(__DIR__ . '/include_site/studio/vendor/autoload.php')) {
    require_once __DIR__ . '/include_site/studio/bootstrap.php';
    $studioPage = \ExpertsAcademy\Studio\PublicGateway::find((string) $pageRow['tbl_categories_code'], $lng);
}
if ($studioPage !== null) {
    $keywords = htmlspecialchars(implode(', ', $studioPage->keywords()), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $discription = htmlspecialchars($studioPage->description(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $siteTitlePage = htmlspecialchars($studioPage->title(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $studioSeoHead = \ExpertsAcademy\Studio\Seo\SeoRenderer::head(
        $studioPage->document(),
        $studioPage->resolver(),
        \ExpertsAcademy\Studio\Seo\SeoRenderer::canonicalFromRequest((string) $site_url)
    );
}
include 'header_site.inc.php';
if ($studioPage !== null) {
    $studioRenderer = new \ExpertsAcademy\Studio\Render\Renderer($studioPage->resolver());
    echo '<link rel="stylesheet" href="' . htmlspecialchars(rtrim($site_url, '/') . '/asset_site/css/cms-pages.css', ENT_QUOTES, 'UTF-8') . '">';
    echo $studioRenderer->fragment($studioPage->document());
    if ($studioRenderer->needsRuntime($studioPage->document())) {
        echo '<script src="' . htmlspecialchars(rtrim($site_url, '/') . '/asset_site/js/cms-runtime.js', ENT_QUOTES, 'UTF-8') . '" defer></script>';
    }
} else {
    require_once __DIR__ . '/include_site/page_renderer.php';
    exacdCmsRenderPage($pageRow, $lng);
}
include 'footer_site.inc.php';
