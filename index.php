<?php
// Resolve only canonical locale-prefixed Studio paths.
// Ordinary CMS requests continue through the legacy router.
$studioRoutePath = null;
$studioRouteLocale = null;

$uriPath = parse_url(
    (string) ($_SERVER['REQUEST_URI'] ?? ''),
    PHP_URL_PATH
);

$scriptBase = rtrim(
    str_replace('\\', '/', dirname(
        (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php')
    )),
    '/'
);

$relativePath = '';

if (is_string($uriPath)) {
    if ($scriptBase === '') {
        $relativePath = $uriPath;
    } elseif (str_starts_with($uriPath, $scriptBase . '/')) {
        $relativePath = substr($uriPath, strlen($scriptBase));
    }
}

$studioPattern =
    '~\A/(fa|en)/[a-z0-9]+(?:-[a-z0-9]+)*'
    . '(?:/[a-z0-9]+(?:-[a-z0-9]+)*)*\z~';

if (preg_match($studioPattern, $relativePath, $matches) === 1) {
    $studioRoutePath = $relativePath;
    $studioRouteLocale = $matches[1];

    // Set locale before the legacy bootstrap loads translations.
    $_GET['lng'] = $studioRouteLocale;
}

include './includer_site.php';

if ($studioRoutePath !== null) {
    require_once __DIR__ . '/include_site/studio/bootstrap.php';

    // Existing redirects retain their established precedence.
    try {
        $redirect = (
            new \ExpertsAcademy\Studio\Repo\RedirectRepository()
        )->match($studioRoutePath, $studioRouteLocale);

        if ($redirect !== null) {
            header(
                'Location: ' . (string) $redirect['to_path'],
                true,
                (int) $redirect['status_code']
            );
            exit;
        }
    } catch (\Throwable $error) {
        // Do not reveal internal exceptions in public responses.
    }

    // Existing CMS category paths take precedence if they collide.
    $legacySection = substr(
        $studioRoutePath,
        strlen('/' . $studioRouteLocale . '/')
    );

    $legacySectionSafe = sqi($legacySection);
    $legacyLocaleSafe = sqi($studioRouteLocale);

    $legacyResult = Query(
        "SELECT tbl_categories_code
         FROM tbl_categories
         WHERE tbl_categories_type = '4'
           AND tbl_categories_url = '$legacySectionSafe'
           AND tbl_categories_lng = '$legacyLocaleSafe'
         LIMIT 1"
    );

    if ($legacyResult && mysqli_num_rows($legacyResult) > 0) {
        // Let the original CMS controller handle this request.
        $_GET['section'] = $legacySection;
    } else {
        $studioPage = \ExpertsAcademy\Studio\PublicGateway::findByPath(
            $studioRoutePath,
            $studioRouteLocale
        );

        if ($studioPage === null) {
            require __DIR__ . '/404.php';
            exit;
        }

        $lng = $studioRouteLocale;

        $keywords = htmlspecialchars(
            implode(', ', $studioPage->keywords()),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $discription = htmlspecialchars(
            $studioPage->description(),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $siteTitlePage = htmlspecialchars(
            $studioPage->title(),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        $studioSeoHead = \ExpertsAcademy\Studio\Seo\SeoRenderer::head(
            $studioPage->document(),
            $studioPage->resolver(),
            \ExpertsAcademy\Studio\Seo\SeoRenderer::canonicalFromRequest(
                (string) $site_url
            )
        );

        // Studio-native layout:
        // Use the page's own navbar and footer when both exist.
        // Other pages keep the existing CMS layout.
        $studioDocument = $studioPage->document();
        $studioComponentTypes = [];

        foreach ($studioDocument['sections'] ?? [] as $section) {
            foreach ($section['components'] ?? [] as $component) {
                $type = $component['type'] ?? null;
                if (is_string($type)) {
                    $studioComponentTypes[$type] = true;
                }
            }
        }

        if (
            isset($studioComponentTypes['navbar']) &&
            isset($studioComponentTypes['footer'])
        ) {
            $nativeRenderer = new \ExpertsAcademy\Studio\Render\Renderer(
                $studioPage->resolver()
            );

            $assetBase = rtrim((string) $site_url, '/');

            echo $nativeRenderer->document(
                $studioDocument,
                $assetBase . '/asset_site/css/cms-pages.css',
                $assetBase . '/asset_site/js/cms-runtime.js',
                \ExpertsAcademy\Studio\Seo\SeoRenderer::canonicalFromRequest(
                    (string) $site_url
                )
            );

            exit;
        }

        include 'header_site.inc.php';

        $studioRenderer = new \ExpertsAcademy\Studio\Render\Renderer(
            $studioPage->resolver()
        );

        $cssUrl = rtrim($site_url, '/')
            . '/asset_site/css/cms-pages.css';

        echo '<link rel="stylesheet" href="'
            . htmlspecialchars($cssUrl, ENT_QUOTES, 'UTF-8')
            . '">';

        // Reserve space for the fixed legacy site header.
        // Scoped to independently routed Studio pages only.
        echo '<style>
            .studio-public-main {
                padding-top: 170px;
            }
            @media (max-width: 767px) {
                .studio-public-main {
                    padding-top: 110px;
                }
            }
        </style>';

        echo '<main id="studio-main-content" class="studio-public-main">';

        echo $studioRenderer->fragment($studioPage->document());

        echo '</main>';

        if ($studioRenderer->needsRuntime($studioPage->document())) {
            $jsUrl = rtrim($site_url, '/')
                . '/asset_site/js/cms-runtime.js';

            echo '<script src="'
                . htmlspecialchars($jsUrl, ENT_QUOTES, 'UTF-8')
                . '" defer></script>';
        }

        include 'footer_site.inc.php';
        exit;
    }
}
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
