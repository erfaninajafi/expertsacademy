<?php
declare(strict_types=1);

require __DIR__ . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Seo\SeoRenderer;
use ExpertsAcademy\Studio\Seo\Sitemap;

$base = Env::string('base_url', '/');
if (preg_match('#\Ahttps?://#i', $base) !== 1) {
    $request = SeoRenderer::canonicalFromRequest('/');
    $origin = preg_replace('#(https?://[^/]+).*#', '$1', $request) ?? '';
    $base = rtrim($origin . rtrim(dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/')), '/'), '/') . '/';
}
$url = rtrim($base, '/') . '/';
$xml = Sitemap::xml($url);
$etag = '"' . hash('sha256', $xml) . '"';

header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=300, s-maxage=600');
header('ETag: ' . $etag);
if (trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
    http_response_code(304);
    exit;
}
echo $xml;
