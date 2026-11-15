<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Seo;

use ExpertsAcademy\Studio\Html;
use ExpertsAcademy\Studio\Render\MediaResolver;

/** Context-specific head tags for a published page. */
final class SeoRenderer
{
    /** @param array<string,mixed> $document */
    public static function head(array $document, MediaResolver $media, string $canonicalUrl): string
    {
        $seo = is_array($document['seo'] ?? null) ? $document['seo'] : [];
        $title = trim((string) ($seo['title'] ?? '')) ?: (string) ($document['title'] ?? '');
        $description = trim((string) ($seo['description'] ?? ''));
        $canonical = trim((string) ($seo['canonical'] ?? '')) ?: $canonicalUrl;
        $robots = (string) ($seo['robots'] ?? 'index,follow');
        if (!in_array($robots, ['index,follow', 'noindex,follow', 'index,nofollow', 'noindex,nofollow'], true)) {
            $robots = 'index,follow';
        }
        $locale = (string) ($document['locale'] ?? 'fa');
        $ogImage = null;
        if (isset($seo['ogImage']['id'])) {
            $resolved = $media->resolve((string) $seo['ogImage']['id']);
            if ($resolved !== null) {
                $ogImage = self::absolute($resolved['url'], $canonicalUrl);
            }
        }

        $tags = '<link rel="canonical" href="' . Html::e($canonical) . '">'
            . '<meta name="robots" content="' . Html::e($robots) . '">'
            . '<meta property="og:type" content="website">'
            . '<meta property="og:title" content="' . Html::e($title) . '">'
            . '<meta property="og:description" content="' . Html::e($description) . '">'
            . '<meta property="og:url" content="' . Html::e($canonical) . '">'
            . '<meta property="og:locale" content="' . ($locale === 'fa' ? 'fa_IR' : 'en_US') . '">'
            . '<meta name="twitter:card" content="' . ($ogImage !== null ? 'summary_large_image' : 'summary') . '">'
            . '<meta name="twitter:title" content="' . Html::e($title) . '">'
            . '<meta name="twitter:description" content="' . Html::e($description) . '">';
        if ($ogImage !== null) {
            $tags .= '<meta property="og:image" content="' . Html::e($ogImage) . '">'
                . '<meta name="twitter:image" content="' . Html::e($ogImage) . '">';
        }

        $structured = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $title,
            'description' => $description,
            'url' => $canonical,
            'inLanguage' => $locale,
            'isPartOf' => [
                '@type' => 'WebSite',
                'name' => 'Experts Academy',
                'url' => self::origin($canonicalUrl),
            ],
        ];
        $json = json_encode(
            $structured,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
        );
        if (is_string($json)) {
            $tags .= '<script type="application/ld+json">' . $json . '</script>';
        }
        return $tags;
    }

    public static function canonicalFromRequest(string $siteUrl): string
    {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
        if ($host === '') {
            return rtrim($siteUrl, '/');
        }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        return ($https ? 'https' : 'http') . '://' . $host . $uri;
    }

    private static function origin(string $url): string
    {
        $parts = parse_url($url);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return $url;
        }
        return $parts['scheme'] . '://' . $parts['host'] . (isset($parts['port']) ? ':' . $parts['port'] : '');
    }

    private static function absolute(string $url, string $base): string
    {
        if (preg_match('#\Ahttps?://#i', $url) === 1) {
            return $url;
        }
        return rtrim(self::origin($base), '/') . '/' . ltrim($url, '/');
    }
}
