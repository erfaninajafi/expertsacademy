<?php
/** Documentation export: loads only the pure registry definitions, not a bootstrap. */
if (PHP_SAPI !== 'cli') exit(2);
require dirname(__DIR__, 2) . '/include_site/component_registry.php';
$file = fopen(dirname(__DIR__) . '/COMPONENT_REGISTRY.tsv', 'wb');
fputcsv($file, ['type_key','legacy_marker','template','storage','variant','hidden','schema_version','legacy_editor','companion_marker','fields','locale_behavior','migration_status'], "\t");
$count = 0;
foreach (exacdComponentRegistry() as $definition) {
    $fields = [];
    foreach ($definition['fields'] ?? [] as $key => $field) {
        $fields[] = $key . ':' . $field['type'] . (!empty($field['required']) ? ':required' : '');
    }
    foreach ($definition['variants'] as $variant => $marker) {
        fputcsv($file, [$definition['key'],$marker,$definition['template'] ?? '',$definition['storage'],$variant,
            !empty($definition['hidden']) ? 'yes' : 'no',$definition['schema_version'],$definition['legacy_editor'],
            $definition['companion_marker'] ?? '',implode(', ', $fields),'fa/en content independent; no cross-language fallback',
            !empty($definition['hidden']) ? 'composite companion only' : 'supported; per-page dry run required'], "\t");
        $count++;
    }
}
fclose($file);
echo json_encode(['definitions' => count(exacdComponentRegistry()), 'marker_rows' => $count]), PHP_EOL;
