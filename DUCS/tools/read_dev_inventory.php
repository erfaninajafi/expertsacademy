<?php
/** Read-only documentation audit. Never queries the original database. No content values are exported. */
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit(2);
$_SERVER['HTTP_HOST'] = 'localhost';
require dirname(__DIR__, 2) . '/include_site/config.php';
require dirname(__DIR__, 2) . '/site_admin/builder_migration.php';
if ($dbname !== 'expertsacademy_dev') exit(2);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
$db->set_charset('utf8mb4');
$db->query('SET TRANSACTION READ ONLY');
$db->begin_transaction();
$out = ['database' => $dbname, 'server_version' => $db->server_version, 'tables' => [], 'pages' => []];
foreach ($db->query('SHOW TABLE STATUS')->fetch_all(MYSQLI_ASSOC) as $table) {
    $name = $table['Name'];
    if (!preg_match('/^[A-Za-z0-9_]+$/D', $name)) throw new RuntimeException('Unexpected table name.');
    $columns = [];
    foreach ($db->query("SHOW COLUMNS FROM `$name`")->fetch_all(MYSQLI_ASSOC) as $column) {
        $columns[] = array_intersect_key($column, array_flip(['Field','Type','Null','Key','Extra']));
    }
    $out['tables'][$name] = ['engine' => $table['Engine'], 'collation' => $table['Collation'],
        'rows' => (int)$db->query("SELECT COUNT(*) n FROM `$name`")->fetch_assoc()['n'], 'columns' => $columns];
}
$pages = $db->query("SELECT tbl_categories_code code,tbl_categories_lng locale,tbl_categories_design design,
    tbl_categories_url url,tbl_categories_index home FROM tbl_categories WHERE tbl_categories_type=4
    AND tbl_categories_lng IN ('fa','en') ORDER BY tbl_categories_code,tbl_categories_lng");
while ($page = $pages->fetch_assoc()) {
    $report = exacdBuilderConvertLocale($db, $page['code'], $page['locale'], false);
    $out['pages'][] = ['code' => $page['code'], 'locale' => $page['locale'],
        'has_direct_route' => $page['url'] !== '' || (string)$page['home'] === '1',
        'home' => (string)$page['home'] === '1',
        'legacy_marker_count' => count(array_filter(preg_split('/\r\n|\r|\n/', $page['design']), 'strlen')),
        'builder_active' => $report['already_active'], 'convertible' => $report['ok'],
        'planned_instances' => $report['planned_instances'],
        'issues' => array_column($report['issues'], 'code'), 'warnings' => array_column($report['warnings'], 'code')];
}
if (isset($out['tables']['tbl_page_builder_layouts'])) {
    $out['layouts'] = $db->query('SELECT page_code,locale,mode,structure_mode,revision FROM tbl_page_builder_layouts ORDER BY page_code,locale')->fetch_all(MYSQLI_ASSOC);
    $out['instance_counts'] = $db->query('SELECT locale_scope,type_key,enabled,(deleted_at IS NOT NULL) deleted,COUNT(*) n
        FROM tbl_page_component_instances GROUP BY locale_scope,type_key,enabled,(deleted_at IS NOT NULL)
        ORDER BY locale_scope,type_key,enabled,deleted')->fetch_all(MYSQLI_ASSOC);
}
$db->rollback();
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR), PHP_EOL;
