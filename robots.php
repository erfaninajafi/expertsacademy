<?php
declare(strict_types=1);

header('Content-Type: text/plain; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
$base = rtrim(str_replace('\\', '/', dirname((string) ($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');

echo "User-agent: *\n";
echo "Allow: /\n";
echo "Disallow: /site_admin/\n";
echo "Disallow: /*?token=\n";
echo "Sitemap: {$scheme}://{$host}{$base}/sitemap.php\n";
