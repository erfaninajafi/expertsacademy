<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Seo;

use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;

/** XML sitemap built from published pages and their canonical routes. */
final class Sitemap
{
    /** @return list<array{loc:string,lastmod:string,locale:string}> */
    public static function urls(string $siteUrl): array
    {
        $rows = Db::all(
            "SELECT p.slug, p.legacy_code, p.locale, p.published_at, r.document
             FROM tbl_studio_pages p
             JOIN tbl_studio_revisions r ON r.id = p.published_revision_id
             WHERE p.status = 'published'
             ORDER BY p.locale, p.slug"
        );
        $urls = [];
        foreach ($rows as $row) {
            $document = Json::decode((string) $row['document']);
            if (!is_array($document)) {
                continue;
            }
            $seo = is_array($document['seo'] ?? null) ? $document['seo'] : [];
            if (($seo['robots'] ?? 'index,follow') === 'noindex,follow' || ($seo['robots'] ?? '') === 'noindex,nofollow') {
                continue;
            }
            $locale = (string) $row['locale'];
            $canonical = trim((string) ($seo['canonical'] ?? ''));
            if ($canonical === '') {
                $prefix = rtrim($siteUrl, '/') . '/';
                $language = $locale === 'en' ? 'en/' : '';
                if (($row['slug'] ?? null) !== null && $row['slug'] !== '') {
                    $canonical = $prefix . $language . rawurlencode((string) $row['slug']);
                } elseif (($row['legacy_code'] ?? null) !== null && $row['legacy_code'] !== '') {
                    $canonical = $prefix . $language . '?section=' . rawurlencode((string) $row['legacy_code']);
                } else {
                    continue;
                }
            }
            $urls[] = [
                'loc' => $canonical,
                'lastmod' => gmdate('c', strtotime((string) $row['published_at']) ?: time()),
                'locale' => $locale,
            ];
        }
        return $urls;
    }

    public static function xml(string $siteUrl): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
            . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach (self::urls($siteUrl) as $url) {
            $xml .= '  <url><loc>' . htmlspecialchars($url['loc'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</loc>'
                . '<lastmod>' . htmlspecialchars($url['lastmod'], ENT_XML1 | ENT_QUOTES, 'UTF-8') . '</lastmod></url>' . "\n";
        }
        return $xml . '</urlset>' . "\n";
    }
}
