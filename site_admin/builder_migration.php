<?php
/**
 * Explicit, additive conversion of one legacy CMS page locale to builder rows.
 * The public renderer keeps using legacy design until a layout row is committed.
 */
require_once dirname(__DIR__) . '/include_site/component_registry.php';

function exacdBuilderMigrationRows(mysqli $db, string $sql, string $types = '', array $values = []): array
{
    $stmt = $db->prepare($sql);
    if ($types !== '') {
        $stmt->bind_param($types, ...$values);
    }
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $rows;
}

function exacdBuilderMigrationUuid(): string
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    $hex = bin2hex($bytes);
    return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-'
        . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-'
        . substr($hex, 20);
}

function exacdBuilderMigrationFreshCode(mysqli $db, string $table, string $column): string
{
    if (!(($table === 'tbl_content' && $column === 'tbl_content_code')
        || ($table === 'tbl_textsection' && $column === 'tbl_textsection_code'))) {
        throw new InvalidArgumentException('Invalid content code column.');
    }
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $code = strtoupper(bin2hex(random_bytes(10))); // Fits tbl_content_code varchar(20).
        $rows = exacdBuilderMigrationRows($db, "SELECT 1 FROM `$table` WHERE `$column`=? LIMIT 1", 's', [$code]);
        if (!$rows) {
            return $code;
        }
    }
    throw new RuntimeException('Could not allocate a unique content code.');
}

function exacdBuilderMigrationInsertClone(mysqli $db, string $table, array $source, string $id): void
{
    if ($table === 'tbl_content') {
        unset($source['tbl_content_id']);
        $source['tbl_content_parent'] = $id;
        $source['tbl_content_code'] = exacdBuilderMigrationFreshCode($db, $table, 'tbl_content_code');
    } elseif ($table === 'tbl_textsection') {
        unset($source['tbl_textsection_id']);
        $source['tbl_textsection_parent'] = $id;
        $source['tbl_textsection_code'] = exacdBuilderMigrationFreshCode($db, $table, 'tbl_textsection_code');
    } else {
        throw new InvalidArgumentException('Invalid source table.');
    }
    foreach (array_keys($source) as $column) {
        if (!preg_match('/^[a-z0-9_]+$/', $column)) {
            throw new RuntimeException('Invalid source column.');
        }
    }
    $names = array_map(static fn($column) => '`' . $column . '`', array_keys($source));
    $sql = 'INSERT INTO `' . $table . '` (' . implode(',', $names) . ') VALUES ('
        . implode(',', array_fill(0, count($source), '?')) . ')';
    $stmt = $db->prepare($sql);
    $values = array_values($source);
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $stmt->close();
}

function exacdBuilderMigrationMarkerRows(mysqli $db, string $table, string $code, string $marker, string $locale, bool $lock): array
{
    if ($table === 'tbl_textsection') {
        $sql = "SELECT * FROM tbl_textsection WHERE tbl_textsection_type='58' AND tbl_textsection_parent=? AND tbl_textsection_section=? AND tbl_textsection_lng=? ORDER BY tbl_textsection_id ASC";
    } elseif ($table === 'tbl_content') {
        $sql = "SELECT * FROM tbl_content WHERE tbl_content_type=58 AND tbl_content_parent=? AND tbl_content_section=? AND tbl_content_fld1=? ORDER BY tbl_content_order ASC, tbl_content_id DESC";
    } else {
        throw new InvalidArgumentException('Invalid source table.');
    }
    return exacdBuilderMigrationRows($db, $sql . ($lock ? ' FOR UPDATE' : ''), 'sss', [$code, $marker, $locale]);
}

/**
 * @return array{ok:bool,applied:bool,already_active:bool,schema_ready:bool,source_markers:int,planned_instances:int,planned_text_rows:int,planned_content_rows:int,skipped_hidden_markers:int,issues:array,warnings:array,instances:array}
 */
function exacdBuilderConvertLocale(mysqli $db, string $code, string $locale, bool $apply): array
{
    $result = [
        'ok' => false, 'applied' => false, 'already_active' => false,
        'schema_ready' => false, 'source_markers' => 0,
        'planned_instances' => 0, 'planned_text_rows' => 0,
        'planned_content_rows' => 0, 'skipped_hidden_markers' => 0,
        'issues' => [], 'warnings' => [], 'instances' => [],
    ];
    if (!in_array($locale, ['fa', 'en'], true) || $code === '' || strlen($code) > 255) {
        $result['issues'][] = ['code' => 'invalid_page_or_locale'];
        return $result;
    }

    $inTransaction = false;
    try {
        $db->set_charset('utf8mb4');
        $tables = exacdBuilderMigrationRows($db, "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('tbl_page_builder_layouts','tbl_page_component_instances')");
        $result['schema_ready'] = count($tables) === 2;
        if ($apply && !$result['schema_ready']) {
            $result['issues'][] = ['code' => 'builder_schema_missing'];
            return $result;
        }
        if ($apply) {
            $db->begin_transaction();
            $inTransaction = true;
        }

        $pageRows = exacdBuilderMigrationRows(
            $db,
            'SELECT tbl_categories_design FROM tbl_categories WHERE tbl_categories_type=4 AND tbl_categories_code=? AND tbl_categories_lng=?' . ($apply ? ' FOR UPDATE' : ''),
            'ss', [$code, $locale]
        );
        if (count($pageRows) !== 1) {
            $result['issues'][] = ['code' => 'page_locale_not_unique', 'rows' => count($pageRows)];
            if ($inTransaction) $db->rollback();
            return $result;
        }

        if ($result['schema_ready']) {
            $existing = exacdBuilderMigrationRows($db,
                'SELECT mode FROM tbl_page_builder_layouts WHERE page_code=? AND locale=?' . ($apply ? ' FOR UPDATE' : ''),
                'ss', [$code, $locale]);
            if ($existing) {
                $result['already_active'] = $existing[0]['mode'] === 'builder';
                $result['issues'][] = ['code' => $result['already_active'] ? 'already_builder' : 'existing_layout'];
                if ($inTransaction) $db->rollback();
                return $result;
            }
            $existingInstances = exacdBuilderMigrationRows($db,
                'SELECT id FROM tbl_page_component_instances WHERE page_code=? AND locale_scope=? LIMIT 1',
                'ss', [$code, $locale]);
            if ($existingInstances) {
                $result['issues'][] = ['code' => 'unlinked_builder_instances'];
                if ($inTransaction) $db->rollback();
                return $result;
            }
        }

        $design = (string)($pageRows[0]['tbl_categories_design'] ?? '');
        $markers = [];
        foreach (preg_split('/\r\n|\r|\n/', $design) as $line) {
            $line = trim($line);
            if ($line === '') continue;
            if (!preg_match('/^\[([^\[\]]+)\]$/u', $line, $match)) {
                $result['issues'][] = ['code' => 'malformed_marker'];
                continue;
            }
            $marker = trim($match[1]);
            if ($marker === '') {
                $result['issues'][] = ['code' => 'empty_marker'];
                continue;
            }
            $markers[] = $marker;
        }
        $result['source_markers'] = count($markers);
        $counts = array_count_values($markers);
        foreach ($counts as $marker => $count) {
            if ($count > 1) {
                $result['issues'][] = ['code' => 'duplicate_marker', 'marker' => $marker, 'count' => $count];
            }
        }

        $plan = [];
        foreach ($markers as $position => $marker) {
            $definition = exacdComponentDefinitionByMarker($marker);
            if ($definition === null) {
                $result['issues'][] = ['code' => 'unknown_marker', 'marker' => $marker];
                continue;
            }
            if (!empty($definition['hidden'])) {
                $listKey = preg_replace('/_heading$/', '', $definition['key']);
                $list = exacdComponentRegistry()[$listKey] ?? null;
                if (!$list || !in_array($list['marker'], $markers, true)) {
                    $result['issues'][] = ['code' => 'orphan_companion_marker', 'marker' => $marker];
                }
                $result['skipped_hidden_markers']++;
                continue;
            }
            $storage = $definition['storage'];
            $sourceRows = [];
            if ($storage === 'text' || $storage === 'content') {
                $table = $storage === 'text' ? 'tbl_textsection' : 'tbl_content';
                $sourceRows = exacdBuilderMigrationMarkerRows($db, $table, $code, $marker, $locale, $apply);
                if ($storage === 'text' && count($sourceRows) > 1) {
                    $result['issues'][] = ['code' => 'ambiguous_text_rows', 'marker' => $marker, 'rows' => count($sourceRows)];
                }
                if ($storage === 'content') {
                    $codes = [];
                    foreach ($sourceRows as $row) {
                        $oldCode = (string)($row['tbl_content_code'] ?? '');
                        if ($oldCode === '' || isset($codes[$oldCode])) {
                            $result['issues'][] = ['code' => 'ambiguous_content_code', 'marker' => $marker];
                            break;
                        }
                        $codes[$oldCode] = true;
                    }
                }
            }
            $companionRows = [];
            if (isset($definition['companion_marker'])) {
                $companion = $definition['companion_marker'];
                $companionRows = exacdBuilderMigrationMarkerRows($db, 'tbl_textsection', $code, $companion, $locale, $apply);
                if (count($companionRows) !== 1) {
                    $result['issues'][] = ['code' => 'ambiguous_companion_data', 'marker' => $companion, 'rows' => count($companionRows)];
                }
            }
            if (!$sourceRows && $storage !== 'none') {
                $result['warnings'][] = ['code' => 'empty_component_data', 'marker' => $marker];
            }
            $plan[] = [
                'marker' => $marker,
                'type_key' => $definition['key'],
                'sort_order' => $position + 1,
                'schema_version' => (int)($definition['schema_version'] ?? 1),
                'storage' => $storage,
                'source_rows' => $sourceRows,
                'companion_rows' => $companionRows,
            ];
            $result['instances'][] = ['marker' => $marker, 'type_key' => $definition['key'], 'sort_order' => $position + 1];
            $result['planned_instances']++;
            $result['planned_text_rows'] += ($storage === 'text' ? count($sourceRows) : 0) + count($companionRows);
            $result['planned_content_rows'] += $storage === 'content' ? count($sourceRows) : 0;
        }
        if ($result['issues']) {
            if ($inTransaction) $db->rollback();
            return $result;
        }
        if (!$apply) {
            $result['ok'] = true;
            return $result;
        }

        // The layout row activates the new renderer only when the transaction commits.
        $stmt = $db->prepare("INSERT INTO tbl_page_builder_layouts (page_code,locale,mode,revision) VALUES (?,?,'builder',1)");
        $stmt->bind_param('ss', $code, $locale);
        $stmt->execute();
        $stmt->close();
        $stmt = $db->prepare('INSERT INTO tbl_page_component_instances (id,page_code,marker,type_key,locale_scope,sort_order,enabled,schema_version) VALUES (?,?,?,?,?,?,1,?)');
        foreach ($plan as $instance) {
            $id = exacdBuilderMigrationUuid();
            $marker = $instance['marker'];
            $typeKey = $instance['type_key'];
            $order = $instance['sort_order'];
            $version = $instance['schema_version'];
            $stmt->bind_param('sssssii', $id, $code, $marker, $typeKey, $locale, $order, $version);
            $stmt->execute();
            if ($instance['storage'] === 'text') {
                foreach ($instance['source_rows'] as $row) exacdBuilderMigrationInsertClone($db, 'tbl_textsection', $row, $id);
            } elseif ($instance['storage'] === 'content') {
                foreach ($instance['source_rows'] as $row) exacdBuilderMigrationInsertClone($db, 'tbl_content', $row, $id);
            }
            foreach ($instance['companion_rows'] as $row) exacdBuilderMigrationInsertClone($db, 'tbl_textsection', $row, $id);
        }
        $stmt->close();
        $db->commit();
        $result['ok'] = true;
        $result['applied'] = true;
        return $result;
    } catch (Throwable $e) {
        if ($inTransaction) $db->rollback();
        $result['issues'][] = ['code' => 'conversion_failed', 'detail' => $e->getMessage()];
        return $result;
    }
}
