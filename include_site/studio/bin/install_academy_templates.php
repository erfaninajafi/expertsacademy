<?php
declare(strict_types=1);

/**
 * ExpertsAcademy Page Studio: 15 Persian starter templates.
 * Install only after a DB backup and explicit --install (staging first).
 * Default is --dry-run. Existing template IDs are NEVER overwritten.
 * Run from project root: php include_site/studio/bin/install_academy_templates.php --dry-run
 * Then: php include_site/studio/bin/install_academy_templates.php --install
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only.');
}

foreach (array_slice($argv, 1) as $arg) {
    if (!in_array($arg, ['--help', '--dry-run', '--install'], true)) {
        fwrite(STDERR, "Unknown argument: {$arg}\n");
        exit(2);
    }
}
$mode = in_array('--install', $argv, true) ? 'install' : 'dry-run';
if (in_array('--help', $argv, true)) {
    echo "Usage: php include_site/studio/bin/install_academy_templates.php [--dry-run|--install]\\n";
    exit(0);
}
if (in_array('--install', $argv, true) && in_array('--dry-run', $argv, true)) {
    fwrite(STDERR, "Choose either --dry-run or --install.\\n");
    exit(2);
}
$root = dirname(__DIR__, 3);
$bootstrap = $root . '/include_site/studio/bootstrap.php';
if (!is_file($bootstrap) || !is_file($root . '/site_admin/studio_api.php')) {
    fwrite(STDERR, "ERROR: ExpertsAcademy project bootstrap not found.\\n");
    exit(2);
}
require_once $bootstrap;

use ExpertsAcademy\Studio\Contract\PageDocument;
use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Db;
use ExpertsAcademy\Studio\Json;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Repo\TokenRepository;

$catalogPath = dirname(__DIR__) . '/data/academy_templates_fa.json';
if (!is_file($catalogPath)) {
    fwrite(STDERR, "ERROR: Template catalogue not found at {$catalogPath}.\n");
    exit(2);
}
$catalogData = file_get_contents($catalogPath);
if ($catalogData === false) {
    fwrite(STDERR, "ERROR: Cannot read the template catalogue.\n");
    exit(2);
}
$catalog = json_decode($catalogData, true, 512, JSON_THROW_ON_ERROR);
if (!is_array($catalog) || count($catalog) !== 15) {
    throw new RuntimeException('Expected exactly 15 template definitions.');
}

/** @param array<string,mixed> $spec @param array<string,mixed>|null $activeTheme */
function eaBuildTemplate(array $spec, ?array $activeTheme): array
{
    $title = (string) $spec['name'];
    $tokens = is_array($activeTheme['tokens'] ?? null) ? $activeTheme['tokens'] : null;
    $document = PageDocument::blank($title, 'fa', $tokens);
    if ($activeTheme !== null && $tokens !== null) {
        $document['theme']['id'] = $activeTheme['id'];
        $document['theme']['version'] = (int) $activeTheme['version'];
    }
    $document['seo']['description'] = (string) $spec['description'];
    $document['sections'] = [];
    foreach ($spec['sections'] as $sectionSpec) {
        $blocks = [];
        foreach ($sectionSpec['blocks'] as $blockSpec) {
            $type = (string) $blockSpec['type'];
            $definition = ComponentRegistry::get($type);
            if ($definition === null) {
                throw new RuntimeException("Unknown registry component: {$type}");
            }
            $variant = (string) $blockSpec['variant'];
            if (!in_array($variant, ComponentRegistry::variantIds($type), true)) {
                throw new RuntimeException("Unsupported variant: {$type}/{$variant}");
            }
            $overrides = (array) $blockSpec['props'];
            $allowed = array_keys((array) $definition['props']);
            foreach (array_keys($overrides) as $key) {
                if (!in_array($key, $allowed, true)) {
                    throw new RuntimeException("Unsupported property {$type}.{$key}");
                }
            }
            // Replace top-level list fields atomically: no demo English items remain.
            $props = array_replace((array) $definition['defaults'], $overrides);
            if ($type === 'landing' && !array_key_exists('secondaryCta', $overrides)) {
                unset($props['secondaryCta']); // Do not preserve the registry's unrelated /programmes link.
            }
            foreach ($props as $key => $val) {
                if ($val === null || $val === '') unset($props[$key]);
            }
            $component = PageDocument::component($definition, $props);
            $component['variant'] = $variant;
            $component['animation'] = ['preset' => 'rise', 'duration' => 420, 'delay' => 0, 'easing' => 'calm'];
            $blocks[] = $component;
        }
        $section = Samples::section((string) $sectionSpec['label'], $blocks);
        $section['layout']['width'] = ($sectionSpec['width'] ?? '') === 'full' ? 'full' : 'contained';
        $section['layout']['background'] = in_array(($sectionSpec['background'] ?? 'none'), ['none','surface','canvas','accent'], true) ? $sectionSpec['background'] : 'none';
        $section['layout']['paddingY'] = 56;
        $document['sections'][] = $section;
    }
    (new Validator())->assert($document);
    return $document;
}

$activeTheme = null;
if ($mode === 'install') {
    // Snapshot the active Persian design system so templates match current identity.
    $activeTheme = (new TokenRepository())->defaultFor('fa');
}

$prepared = [];
foreach ($catalog as $spec) {
    $id = (string) $spec['id'];
    $document = eaBuildTemplate($spec, $activeTheme);
    $prepared[] = [$id, (string) $spec['name'], (string) $spec['category'], (string) $spec['description'], $document];
    printf("READY %-2d | %-42s | %d sections / %d blocks\n",
        count($prepared),
        $spec['name'],
        count($document['sections']),
        array_sum(array_map(static fn (array $s): int => count($s['components']), $document['sections']))
    );
}

if ($mode === 'dry-run') {
    echo "DRY-RUN SUCCESS: all 15 documents satisfy the current Page Studio contract. Database untouched.\n";
    echo "Then, after a database backup, run the same command with --install.\n";
    exit(0);
}

$summary = Db::transaction(static function () use ($prepared): array {
    $inserted = 0;
    $skipped = 0;
    foreach ($prepared as [$id, $name, $category, $description, $document]) {
        $existing = Db::one('SELECT id, name FROM tbl_studio_templates WHERE id = ? LIMIT 1', [$id]);
        if ($existing !== null) {
            if ((string) $existing['name'] !== $name) {
                throw new RuntimeException("Template ID conflict for {$id}; nothing installed.");
            }
            $skipped++;
            continue;
        }
        Db::run(
            'INSERT INTO tbl_studio_templates (id, version, name, category, description, page_schema_version, status, document)
             VALUES (?, 1, ?, ?, ?, ?, ?, ?)',
            [$id, $name, $category, $description, PageDocument::SCHEMA_VERSION, 'active', Json::encode($document)]
        );
        $inserted++;
    }
    return ['inserted' => $inserted, 'skipped' => $skipped];
});
printf("SUCCESS: added %d templates; skipped %d already installed.\n", $summary['inserted'], $summary['skipped']);
echo "No existing pages, publications, media or templates were modified. Refresh Page Studio > Templates.\n";
