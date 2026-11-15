<?php
declare(strict_types=1);

/**
 * Secure draft preview by opaque, expiring token. No CMS session is needed;
 * the token is validated server-side and the response is never indexable.
 */

require __DIR__ . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Render\Renderer;
use ExpertsAcademy\Studio\Repo\MediaRepository;
use ExpertsAcademy\Studio\Repo\PreviewTokenRepository;

header('X-Robots-Tag: noindex, nofollow, noarchive');
header('Cache-Control: private, no-store, max-age=0');
header('Referrer-Policy: no-referrer');
header('Content-Security-Policy: ' . Renderer::policy());

$token = isset($_GET['token']) && is_string($_GET['token']) ? $_GET['token'] : '';
$preview = $token !== '' ? (new PreviewTokenRepository())->resolve($token) : null;
if ($preview === null) {
    http_response_code(404);
    header('Content-Type: text/html; charset=utf-8');
    echo '<!doctype html><meta charset="utf-8"><title>Preview unavailable</title>'
        . '<p style="font-family:system-ui;padding:2rem">This preview link is invalid or has expired.</p>';
    exit;
}

$document = $preview['document'];
$media = (new MediaRepository())->resolverFor($document);
$renderer = new Renderer($media);
if ($renderer->needsRuntime($document)) {
    $runtime = 'asset_site/js/cms-runtime.js';
} else {
    $runtime = '';
}
echo $renderer->document($document, 'asset_site/css/cms-pages.css', $runtime);
