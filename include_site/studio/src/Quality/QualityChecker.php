<?php
declare(strict_types=1);

namespace ExpertsAcademy\Studio\Quality;

use ExpertsAcademy\Studio\Contract\PageDocument;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use Throwable;

/**
 * Pre-publication quality gate.
 *
 * Blocking errors stop publication; warnings are shown to the editor and do
 * not block. Checks run against the exact revision that would be published.
 */
final class QualityChecker
{
    /** @param array<string,mixed> $page @return array<string,mixed> */
    public function check(array $page, array $document): array
    {
        $errors = [];
        $warnings = [];
        $this->metadata($page, $document, $errors, $warnings);
        $this->components($document, $errors, $warnings);
        $this->headings($document, $warnings);
        $this->links($document, $warnings);
        $this->duplicates($page, $document, $warnings);

        return [
            'ok' => $errors === [],
            'errorCount' => count($errors),
            'warningCount' => count($warnings),
            'errors' => $errors,
            'warnings' => $warnings,
            'checkedAt' => gmdate('c'),
        ];
    }

    /** @param list<array<string,string>> $errors @param list<array<string,string>> $warnings */
    private function metadata(array $page, array $document, array &$errors, array &$warnings): void
    {
        $title = trim((string) ($document['title'] ?? ''));
        if ($title === '') {
            $errors[] = ['code' => 'title.missing', 'message' => 'Page title is required.', 'path' => 'title'];
        }
        if (
            trim((string) ($page['legacyCode'] ?? $page['legacy_code'] ?? '')) === ''
            && trim((string) ($page['fullPath'] ?? $page['full_path'] ?? '')) === ''
        ) {
            $warnings[] = [
                'code' => 'route.missing',
                'message' => 'No public route is assigned. Set a Full Path or legacy CMS code.',
                'path' => 'route',
            ];
        }
        $seo = is_array($document['seo'] ?? null) ? $document['seo'] : [];
        $seoTitle = trim((string) ($seo['title'] ?? ''));
        $description = trim((string) ($seo['description'] ?? ''));
        if ($seoTitle === '') {
            $warnings[] = ['code' => 'seo.title.missing', 'message' => 'SEO title is empty; the page title will be used.', 'path' => 'seo.title'];
        } elseif (mb_strlen($seoTitle) > 60) {
            $warnings[] = ['code' => 'seo.title.long', 'message' => 'SEO title is longer than 60 characters and may be truncated.', 'path' => 'seo.title'];
        }
        if ($description === '') {
            $warnings[] = ['code' => 'seo.description.missing', 'message' => 'Meta description is empty.', 'path' => 'seo.description'];
        } elseif (mb_strlen($description) < 50 || mb_strlen($description) > 160) {
            $warnings[] = ['code' => 'seo.description.length', 'message' => 'Meta description should be 50-160 characters.', 'path' => 'seo.description'];
        }
        $canonical = (string) ($seo['canonical'] ?? '');
        if ($canonical !== '' && filter_var($canonical, FILTER_VALIDATE_URL) === false) {
            $warnings[] = ['code' => 'seo.canonical.invalid', 'message' => 'Canonical URL is not absolute.', 'path' => 'seo.canonical'];
        }
        if (isset($seo['ogImage']['id'])) {
            $image = $this->media((string) $seo['ogImage']['id']);
            if ($image === null) {
                $warnings[] = ['code' => 'seo.image.missing', 'message' => 'The social sharing image no longer exists.', 'path' => 'seo.ogImage'];
            } elseif (trim((string) ($seo['ogImage']['alt'] ?? '')) === '') {
                $warnings[] = ['code' => 'seo.image.alt', 'message' => 'Social image has no alt text.', 'path' => 'seo.ogImage.alt'];
            }
        }
        if (($document['schemaVersion'] ?? null) !== PageDocument::SCHEMA_VERSION) {
            $errors[] = ['code' => 'schema.unsupported', 'message' => 'Unsupported document schema version.', 'path' => 'schemaVersion'];
        }
        $components = 0;
        foreach ($document['sections'] ?? [] as $section) {
            $components += is_array($section['components'] ?? null) ? count($section['components']) : 0;
        }
        if ($components === 0) {
            $errors[] = ['code' => 'content.empty', 'message' => 'A page with no components cannot be published.', 'path' => 'sections'];
        }
    }

    /** @param list<array<string,string>> $errors @param list<array<string,string>> $warnings */
    private function components(array $document, array &$errors, array &$warnings): void
    {
        $seen = [];
        foreach ($document['sections'] ?? [] as $sectionIndex => $section) {
            foreach ($section['components'] ?? [] as $componentIndex => $component) {
                $path = "sections[$sectionIndex].components[$componentIndex]";
                $type = (string) ($component['type'] ?? '');
                $definition = ComponentRegistry::get($type);
                if ($definition === null) {
                    $errors[] = ['code' => 'component.unsupported', 'message' => "Unknown component type: {$type}.", 'path' => $path];
                    continue;
                }
                if ((int) ($component['version'] ?? 0) !== (int) $definition['version']) {
                    $errors[] = ['code' => 'component.version', 'message' => "Component {$type} needs version {$definition['version']}.", 'path' => $path . '.version'];
                }
                if (!in_array((string) ($component['variant'] ?? ''), ComponentRegistry::variantIds($type), true)) {
                    $errors[] = ['code' => 'component.variant', 'message' => "Unknown variant for {$type}.", 'path' => $path . '.variant'];
                }
                $id = (string) ($component['id'] ?? '');
                if ($id !== '' && isset($seen[$id])) {
                    $errors[] = ['code' => 'component.duplicate', 'message' => 'Duplicate component id.', 'path' => $path . '.id'];
                }
                $seen[$id] = true;
                $this->walkProps($component['props'] ?? [], $path . '.props', $errors, $warnings);
                if (empty($component['visibility']['devices'])) {
                    $warnings[] = ['code' => 'component.invisible', 'message' => 'Component is hidden on every device.', 'path' => $path . '.visibility'];
                }
            }
        }
    }

    /** @param list<array<string,string>> $errors @param list<array<string,string>> $warnings */
    private function walkProps(mixed $value, string $path, array &$errors, array &$warnings): void
    {
        if (!is_array($value)) {
            return;
        }
        if (isset($value['id'], $value['alt']) && is_string($value['id'])) {
            $image = $this->media($value['id']);
            if ($image === null) {
                $warnings[] = ['code' => 'media.missing', 'message' => 'A referenced image is unavailable and renders as a placeholder.', 'path' => $path];
            } else {
                if (trim((string) $value['alt']) === '') {
                    $warnings[] = ['code' => 'media.alt.missing', 'message' => 'Image has no alt text.', 'path' => $path . '.alt'];
                }
                if ((int) $image['size_bytes'] > 2 * 1024 * 1024) {
                    $warnings[] = ['code' => 'media.oversized', 'message' => 'Image is larger than 2 MB and should be compressed.', 'path' => $path];
                }
            }
        }
        foreach ($value as $key => $child) {
            $this->walkProps($child, $path . '.' . $key, $errors, $warnings);
        }
    }

    /** @param list<array<string,string>> $warnings */
    private function headings(array $document, array &$warnings): void
    {
        $previous = 1;
        $index = 0;
        foreach ($document['sections'] ?? [] as $section) {
            foreach ($section['components'] ?? [] as $component) {
                $level = (int) ($component['a11y']['headingLevel'] ?? 2);
                if ($index > 0 && $level > $previous + 1) {
                    $warnings[] = [
                        'code' => 'heading.hierarchy',
                        'message' => "Heading jumps from h{$previous} to h{$level}; use a sequential outline.",
                        'path' => 'component.' . $index,
                    ];
                }
                $previous = $level;
                $index++;
            }
        }
    }

    /** @param list<array<string,string>> $warnings */
    private function links(array $document, array &$warnings): void
    {
        $routes = $this->routes();
        $walk = function (mixed $value) use (&$walk, $routes, &$warnings): void {
            if (!is_array($value)) {
                return;
            }
            foreach ($value as $key => $child) {
                if (is_string($child) && $key === 'href') {
                    $this->link($child, 'link', $warnings, $routes);
                }
                $walk($child);
            }
        };
        $walk($document);
    }

    /** @param list<array<string,string>> $warnings @param array<string,bool>|null $routes */
    private function link(string $href, string $path, array &$warnings, ?array $routes = null): void
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, 'mailto:') || str_starts_with($href, 'tel:')) {
            return;
        }
        if (preg_match('#\Ahttps?://#i', $href) === 1 || str_starts_with($href, '//')) {
            return;
        }
        if (str_contains($href, '{') || str_contains($href, '}')) {
            return;
        }
        $routes ??= $this->routes();
        $pathOnly = parse_url($href, PHP_URL_PATH);
        if (!is_string($pathOnly)) {
            return;
        }
        $normalized = trim($pathOnly, '/');
        $normalized = preg_replace('#^(fa|en)/#', '', $normalized) ?? $normalized;
        if ($normalized === '' || $normalized === 'index.php' || isset($routes[$normalized])) {
            return;
        }
        if (preg_match('/\.(pdf|jpe?g|png|webp|avif|svg|zip|mp4)$/i', $normalized) === 1) {
            return;
        }
        $warnings[] = [
            'code' => 'link.unresolved',
            'message' => "Internal link does not match a published route: {$href}",
            'path' => $path,
        ];
    }

    /** @return array<string,bool> */
    private function routes(): array
    {
        $routes = [];
        try {
            foreach (Db::all("SELECT slug, legacy_code FROM tbl_studio_pages WHERE status = 'published'") as $row) {
                if ($row['slug'] !== null && $row['slug'] !== '') {
                    $routes[(string) $row['slug']] = true;
                }
                if ($row['legacy_code'] !== null && $row['legacy_code'] !== '') {
                    $routes[(string) $row['legacy_code']] = true;
                }
            }
            if (in_array('tbl_studio_redirects', $this->tables(), true)) {
                foreach (Db::all('SELECT from_path FROM tbl_studio_redirects WHERE active = 1') as $row) {
                    $routes[trim((string) $row['from_path'], '/')] = true;
                }
            }
        } catch (Throwable) {
            // Quality checks must not fail the publish dialog if route lookup is unavailable.
        }
        return $routes;
    }

    /** @return list<string> */
    private function tables(): array
    {
        $rows = Db::all('SHOW TABLES');
        $tables = [];
        foreach ($rows as $row) {
            $tables[] = (string) array_values($row)[0];
        }
        return $tables;
    }

    /** @return array<string,mixed>|null */
    private function media(string $id): ?array
    {
        try {
            return Db::one('SELECT id, size_bytes, width, height FROM tbl_studio_media WHERE id = ? AND deleted_at IS NULL', [$id]);
        } catch (Throwable) {
            return null;
        }
    }

    /** @param list<array<string,string>> $warnings */
    private function duplicates(array $page, array $document, array &$warnings): void
    {
        $seo = is_array($document['seo'] ?? null) ? $document['seo'] : [];
        $title = trim((string) ($seo['title'] ?? ''));
        $description = trim((string) ($seo['description'] ?? ''));
        if ($title === '' && $description === '') {
            return;
        }
        try {
            $rows = Db::all(
                "SELECT p.id, p.title,
                        JSON_UNQUOTE(JSON_EXTRACT(r.document, '$.seo.title')) AS seo_title,
                        JSON_UNQUOTE(JSON_EXTRACT(r.document, '$.seo.description')) AS seo_description
                 FROM tbl_studio_pages p
                 JOIN tbl_studio_revisions r ON r.id = p.draft_revision_id
                 WHERE p.id <> ? AND p.locale = ?
                 LIMIT 200",
                [(string) ($page['id'] ?? ''), (string) ($page['locale'] ?? '')]
            );
        } catch (Throwable) {
            return;
        }
        foreach ($rows as $row) {
            if ($title !== '' && hash_equals($title, (string) ($row['seo_title'] ?? ''))) {
                $warnings[] = ['code' => 'seo.title.duplicate', 'message' => 'Another page uses the same SEO title: ' . $row['title'], 'path' => 'seo.title'];
            }
            if ($description !== '' && hash_equals($description, (string) ($row['seo_description'] ?? ''))) {
                $warnings[] = ['code' => 'seo.description.duplicate', 'message' => 'Another page uses the same meta description: ' . $row['title'], 'path' => 'seo.description'];
            }
        }
    }
}
