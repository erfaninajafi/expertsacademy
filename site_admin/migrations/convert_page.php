<?php
/**
 * CLI-only Page Builder migration.
 *
 * Examples:
 *   /opt/lampp/bin/php convert_page.php --page-code=ABC1234 --locale=fa
 *   /opt/lampp/bin/php convert_page.php --page-code=ABC1234 --locale=fa --apply
 *   /opt/lampp/bin/php convert_page.php --all
 *   /opt/lampp/bin/php convert_page.php --install-schema
 *
 * The default page conversion is a read-only dry run. Apply is per page+locale.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_HOST'] ?? 'localhost';
require dirname(__DIR__, 2) . '/include_site/config.php';
require dirname(__DIR__) . '/builder_migration.php';

$options = getopt('', ['page-code:', 'locale:', 'apply', 'install-schema', 'database:', 'all']);
$database = $options['database'] ?? $dbname;
if ($dbname !== 'expertsacademy_dev'
    || ($database !== 'expertsacademy_dev'
        && !preg_match('/^expertsacademy_builder_test_[a-z0-9_]+$/', $database))) {
    file_put_contents('php://stderr', "Only the development or isolated builder test database is allowed.\n");
    exit(2);
}
$installSchema = isset($options['install-schema']);
$apply = isset($options['apply']);
$pageCode = $options['page-code'] ?? null;
$locale = $options['locale'] ?? null;
$all = isset($options['all']);
if (($pageCode === null) !== ($locale === null) || ($pageCode === null && !$installSchema && !$all)
    || ($all && ($apply || $pageCode !== null || $installSchema))) {
    file_put_contents('php://stderr', "Provide a page and locale, --install-schema, or read-only --all.\n");
    exit(2);
}

try {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    if ($database !== 'expertsacademy_dev') {
        $socket = getenv('EXACD_BUILDER_TEST_SOCKET') ?: '';
        if (!str_starts_with($socket, '/tmp/') || !file_exists($socket)) {
            throw new RuntimeException('An isolated test socket under /tmp is required.');
        }
        $db = new mysqli('localhost', 'root', '', $database, 0, $socket);
    } else {
        $db = new mysqli($dbserver, $dbusername, $dbpassword, $database);
    }
    $db->set_charset('utf8mb4');
    if ($installSchema) {
        $sql = file_get_contents(__DIR__ . '/20261027_page_builder.sql');
        if ($sql === false) throw new RuntimeException('Schema file missing.');
        $statements = array_filter(array_map('trim', explode(';', $sql)));
        foreach ($statements as $statement) $db->query($statement);
        echo json_encode(['database' => $database, 'schema_installed' => true], JSON_UNESCAPED_SLASHES), PHP_EOL;
    }
    if ($pageCode !== null) {
        $result = exacdBuilderConvertLocale($db, (string)$pageCode, (string)$locale, $apply);
        $result['database'] = $database;
        $result['page_code'] = $pageCode;
        $result['locale'] = $locale;
        $result['operation'] = $apply ? 'apply' : 'dry_run';
        echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), PHP_EOL;
        if (!$result['ok'] && !$result['already_active']) exit(1);
    }
    if ($all) {
        $query = $db->query("SELECT tbl_categories_code AS code,tbl_categories_lng AS locale FROM tbl_categories WHERE tbl_categories_type=4 AND tbl_categories_lng IN ('fa','en') ORDER BY tbl_categories_code,tbl_categories_lng");
        $summary = ['database' => $database, 'operation' => 'dry_run_all', 'page_locales' => 0,
            'convertible' => 0, 'already_active' => 0, 'needs_review' => 0,
            'source_markers' => 0, 'planned_instances' => 0, 'issues' => []];
        while ($page = $query->fetch_assoc()) {
            $report = exacdBuilderConvertLocale($db, $page['code'], $page['locale'], false);
            $summary['page_locales']++;
            $summary['source_markers'] += $report['source_markers'];
            $summary['planned_instances'] += $report['planned_instances'];
            if ($report['ok']) $summary['convertible']++;
            elseif ($report['already_active']) $summary['already_active']++;
            else {
                $summary['needs_review']++;
                $summary['issues'][] = ['page_code' => $page['code'], 'locale' => $page['locale'], 'codes' => array_column($report['issues'], 'code')];
            }
        }
        echo json_encode($summary, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), PHP_EOL;
    }
} catch (Throwable $e) {
    file_put_contents('php://stderr', "Migration command failed (" . (int)$e->getCode() . ").\n");
    exit(1);
}
