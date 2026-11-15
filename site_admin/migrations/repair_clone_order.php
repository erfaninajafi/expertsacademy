<?php
/**
 * Repair content clone order for an untouched builder conversion made before
 * the legacy tie-order fix. Dry-run by default; --apply is transactional.
 */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit(2);
$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
require dirname(__DIR__, 2) . '/include_site/config.php';
require dirname(__DIR__) . '/builder_migration.php';

$options = getopt('', ['page-code:', 'locale:', 'apply']);
$pageCode = (string)($options['page-code'] ?? '');
$locale = (string)($options['locale'] ?? '');
$apply = isset($options['apply']);
if ($dbname !== 'expertsacademy_dev'
    || !preg_match('/\A[A-Za-z0-9_-]{1,255}\z/D', $pageCode)
    || !in_array($locale, ['fa', 'en'], true)) {
    fwrite(STDERR, "Only a page locale in the configured development database is allowed.\n");
    exit(2);
}

function exacdClonePayloadHash(array $row): string
{
    unset($row['tbl_content_id'], $row['tbl_content_code'], $row['tbl_content_parent']);
    ksort($row);
    return hash('sha256', json_encode($row, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR));
}

$db = null;
try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
    $db->set_charset('utf8mb4');
    $db->begin_transaction();

    $layout = exacdBuilderMigrationRows($db,
        'SELECT mode,structure_mode,revision FROM tbl_page_builder_layouts WHERE page_code=? AND locale=? FOR UPDATE',
        'ss', [$pageCode, $locale]);
    if (count($layout) !== 1 || $layout[0]['mode'] !== 'builder'
        || $layout[0]['structure_mode'] !== 'independent' || (int)$layout[0]['revision'] !== 1) {
        throw new RuntimeException('Expected one untouched independent builder layout at revision 1.');
    }

    $instances = exacdBuilderMigrationRows($db,
        'SELECT id,marker,type_key,enabled,deleted_at FROM tbl_page_component_instances
         WHERE page_code=? AND locale_scope=? ORDER BY sort_order,id FOR UPDATE',
        'ss', [$pageCode, $locale]);
    if (!$instances) throw new RuntimeException('No component instances found.');

    $changes = [];
    foreach ($instances as $instance) {
        if ((int)$instance['enabled'] !== 1 || $instance['deleted_at'] !== null) {
            throw new RuntimeException('The builder layout has been edited.');
        }
        $definition = exacdComponentDefinitionByTypeAndMarker($instance['type_key'], $instance['marker']);
        if ($definition === null) throw new RuntimeException('Unknown component instance.');
        if ($definition['storage'] !== 'content') continue;

        $marker = $instance['marker'];
        $id = $instance['id'];
        $source = exacdBuilderMigrationMarkerRows($db, 'tbl_content', $pageCode, $marker, $locale, true,
            in_array($definition['key'], ['custom_two', 'custom_four'], true));
        $clones = exacdBuilderMigrationRows($db,
            'SELECT * FROM tbl_content WHERE tbl_content_type=58 AND tbl_content_parent=?
             AND tbl_content_section=? AND tbl_content_fld1=? ORDER BY tbl_content_id ASC FOR UPDATE',
            'sss', [$id, $marker, $locale]);
        $sourceHashes = array_map('exacdClonePayloadHash', $source);
        $cloneHashes = array_map('exacdClonePayloadHash', $clones);
        $sourceSet = $sourceHashes;
        $cloneSet = $cloneHashes;
        sort($sourceSet);
        sort($cloneSet);
        if ($sourceSet !== $cloneSet) {
            throw new RuntimeException('Clone content differs from untouched legacy source.');
        }
        if ($sourceHashes === $cloneHashes) continue;

        $changes[] = ['instance_id' => $id, 'rows' => count($source)];
        if (!$apply) continue;
        $delete = $db->prepare('DELETE FROM tbl_content WHERE tbl_content_type=58
            AND tbl_content_parent=? AND tbl_content_section=? AND tbl_content_fld1=?');
        $delete->bind_param('sss', $id, $marker, $locale);
        $delete->execute();
        if ($delete->affected_rows !== count($clones)) {
            throw new RuntimeException('Clone delete count changed unexpectedly.');
        }
        $delete->close();
        foreach ($source as $row) exacdBuilderMigrationInsertClone($db, 'tbl_content', $row, $id);
    }

    if ($apply) $db->commit();
    else $db->rollback();
    echo json_encode(['database' => $dbname, 'page_code' => $pageCode, 'locale' => $locale,
        'operation' => $apply ? 'apply' : 'dry_run', 'reordered_instances' => count($changes),
        'reordered_rows' => array_sum(array_column($changes, 'rows'))], JSON_UNESCAPED_SLASHES), PHP_EOL;
} catch (Throwable $e) {
    if ($db instanceof mysqli) $db->rollback();
    fwrite(STDERR, "Clone-order repair refused or failed: " . $e->getMessage() . "\n");
    exit(1);
}
