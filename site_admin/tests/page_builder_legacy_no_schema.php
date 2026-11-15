<?php
/** Legacy rendering must remain available before the additive tables are installed. */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit(2);
$database = getenv('EXACD_BUILDER_TEST_DATABASE') ?: 'expertsacademy_builder_test_noschema';
$socket = getenv('EXACD_BUILDER_TEST_SOCKET') ?: '/tmp/expertsacademy-builder.sock';
if (!preg_match('/^expertsacademy_builder_test_[a-z0-9_]+$/D', $database) || !str_starts_with($socket, '/tmp/')) exit(2);
ini_set('error_log', sys_get_temp_dir() . '/expertsacademy-builder-legacy-test.log');
$testDb = new mysqli('localhost', 'root', '', $database, 0, $socket);
$testDb->set_charset('utf8mb4');
function Query($sql)
{
    global $testDb;
    return $testDb->query($sql);
}
require_once __DIR__ . '/../../include_site/page_renderer.php';
$tables = $testDb->query("SHOW TABLES LIKE 'tbl_page_builder_layouts'");
if ($tables->num_rows !== 0) {
    fwrite(STDERR, "This test requires a fixture without builder tables.\n");
    exit(2);
}
$page = $testDb->query("SELECT * FROM tbl_categories WHERE tbl_categories_type=4 AND tbl_categories_lng='fa' AND tbl_categories_design<>'' ORDER BY tbl_categories_id LIMIT 1")->fetch_assoc();
$lang = array_fill(0, 200, 'Test');
$lang['faq_form_heading'] = 'Test form';
$site_url = 'http://localhost/expertsacademy/';
ob_start();
exacdCmsRenderPage($page, 'fa');
$html = ob_get_clean();
if (strlen($html) < 100 || exacdCmsBuilderIsActive($page['tbl_categories_code'], 'fa')) {
    fwrite(STDERR, "FAIL: legacy renderer without additive schema.\n");
    exit(1);
}
echo "PASS: legacy page renders without builder schema.\n";

$_SERVER['HTTP_HOST'] = 'localhost';
ini_set('session.save_path', sys_get_temp_dir());
session_start();
$_SESSION['siteadmin_login'] = 1;
define('EXACD_BUILDER_API_NO_DISPATCH', true);
require_once __DIR__ . '/../page_builder_api.php';
$missingSchemaRejected = false;
try {
    builderRequireSchema($testDb);
} catch (BuilderApiError $error) {
    $missingSchemaRejected = $error->status === 503;
}
if (!$missingSchemaRejected) {
    fwrite(STDERR, "FAIL: builder API did not explain the missing schema.\n");
    exit(1);
}
echo "PASS: builder API reports that its schema is not installed.\n";
