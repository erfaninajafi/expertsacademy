<?php
/** Run only against a disposable MariaDB database restored from site.sql or a dev backup. */
declare(strict_types=1);

if (PHP_SAPI !== 'cli') exit(2);
$socket = getenv('EXACD_BUILDER_TEST_SOCKET') ?: '/tmp/expertsacademy-builder.sock';
$database = getenv('EXACD_BUILDER_TEST_DATABASE') ?: 'expertsacademy_builder_test_local';
if (!preg_match('/^expertsacademy_builder_test_[a-z0-9_]+$/D', $database) || !str_starts_with($socket, '/tmp/')) {
    fwrite(STDERR, "Refusing to test outside an isolated builder database.\n");
    exit(2);
}
$_SERVER['HTTP_HOST'] = 'localhost';
ini_set('session.save_path', sys_get_temp_dir());
session_start();
$_SESSION['siteadmin_login'] = 1;
define('EXACD_BUILDER_API_NO_DISPATCH', true);
require_once __DIR__ . '/../page_builder_api.php';
require_once __DIR__ . '/../builder_migration.php';
require_once __DIR__ . '/../../include_site/page_renderer.php';

function integrationAssert(bool $condition, string $name): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: $name\n");
        exit(1);
    }
    echo "PASS: $name\n";
}

function integrationContentValues(array $data): array
{
    unset($data['id']);
    foreach ($data as $key => $value) if (is_array($value)) $data[$key] = integrationContentValues($value);
    return $data;
}

$testDb = new mysqli('localhost', 'root', '', $database, 0, $socket);
$testDb->set_charset('utf8mb4');
function Query($sql)
{
    global $testDb;
    return $testDb->query($sql);
}

$candidates = builderRows($testDb, "SELECT tbl_categories_code AS code, tbl_categories_design AS design FROM tbl_categories WHERE tbl_categories_type=4 AND tbl_categories_lng='fa' ORDER BY tbl_categories_code");
$code = null;
$dry = null;
foreach ($candidates as $candidate) {
    $report = exacdBuilderConvertLocale($testDb, $candidate['code'], 'fa', false);
    if ($report['ok'] && $report['planned_instances'] > 0 && preg_match('/^[A-Za-z0-9]{1,64}$/D', $candidate['code'])) {
        $code = $candidate['code'];
        $dry = $report;
        $originalDesign = $candidate['design'];
        break;
    }
}
integrationAssert($code !== null && $dry['schema_ready'], 'isolated fixture has a convertible page and schema');
integrationAssert(builderLayouts($testDb, $code)['fa']['mode'] === 'legacy', 'dry run did not activate public rendering');

$beforeContent = (int) builderRows($testDb, 'SELECT COUNT(*) AS n FROM tbl_content WHERE tbl_content_parent=?', [$code])[0]['n'];
$beforeText = (int) builderRows($testDb, 'SELECT COUNT(*) AS n FROM tbl_textsection WHERE tbl_textsection_parent=?', [$code])[0]['n'];
$converted = exacdBuilderConvertLocale($testDb, $code, 'fa', true);
integrationAssert($converted['ok'] && $converted['applied'], 'explicit conversion commits');
integrationAssert($converted['planned_instances'] === $dry['planned_instances'], 'conversion preserves marker count and order');
integrationAssert(builderLayouts($testDb, $code)['fa']['mode'] === 'builder' && builderLayouts($testDb, $code)['en']['mode'] === 'legacy', 'only requested language changes renderer');
integrationAssert((int) builderRows($testDb, 'SELECT COUNT(*) AS n FROM tbl_content WHERE tbl_content_parent=?', [$code])[0]['n'] === $beforeContent
    && (int) builderRows($testDb, 'SELECT COUNT(*) AS n FROM tbl_textsection WHERE tbl_textsection_parent=?', [$code])[0]['n'] === $beforeText,
    'legacy source content remains unchanged');
integrationAssert(builderRows($testDb, "SELECT tbl_categories_design AS design FROM tbl_categories WHERE tbl_categories_code=? AND tbl_categories_lng='fa'", [$code])[0]['design'] === $originalDesign, 'legacy design and URL metadata remain unchanged');
$repeat = exacdBuilderConvertLocale($testDb, $code, 'fa', true);
integrationAssert(!$repeat['applied'] && $repeat['already_active'], 'conversion is idempotent');

$state = builderState($testDb, $code);
integrationAssert(count($state['instances']['fa']) === $dry['planned_instances'], 'API returns converted instances');
$legacyEnCount = count($state['instances']['en']);
integrationAssert($legacyEnCount === 0, 'unconverted language has no new instances');

$revision = $state['layouts']['fa']['revision'];
$originalIds = array_column($state['instances']['fa'], 'id');
$state = builderMutate($testDb, ['action' => 'add', 'code' => $code, 'locale' => 'fa', 'revision' => $revision, 'key' => 'title']);
$firstId = array_values(array_diff(array_column($state['instances']['fa'], 'id'), $originalIds))[0] ?? null;
integrationAssert($firstId !== null, 'add creates a new title instance');
$revision = $state['layouts']['fa']['revision'];
$allIds = array_column($state['instances']['fa'], 'id');
$state = builderMutate($testDb, ['action' => 'add', 'code' => $code, 'locale' => 'fa', 'revision' => $revision, 'key' => 'title']);
$secondId = array_values(array_diff(array_column($state['instances']['fa'], 'id'), $allIds))[0] ?? null;
integrationAssert($secondId !== null && $firstId !== $secondId, 'same component type can be added repeatedly');

$state = builderMutate($testDb, ['action' => 'save_content', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $firstId,
    'data' => ['title' => '<strong>Test First</strong>', 'undertitle' => 'first subtitle']]);
$state = builderMutate($testDb, ['action' => 'save_content', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $secondId,
    'data' => ['title' => '<strong>Test Second</strong><script>bad()</script>', 'undertitle' => 'second subtitle']]);
$firstData = builderReadData($testDb, builderInstance($testDb, $code, 'fa', $firstId), 'fa', exacdComponentRegistry()['title']);
$secondData = builderReadData($testDb, builderInstance($testDb, $code, 'fa', $secondId), 'fa', exacdComponentRegistry()['title']);
integrationAssert(str_contains($firstData['title'], 'Test First') && str_contains($secondData['title'], 'Test Second') && !str_contains($secondData['title'], 'script'), 'repeated title data is independent and sanitized');

$beforeIds = array_column($state['instances']['fa'], 'id');
$state = builderMutate($testDb, ['action' => 'duplicate', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $firstId]);
$thirdId = array_values(array_diff(array_column($state['instances']['fa'], 'id'), $beforeIds))[0] ?? null;
integrationAssert($thirdId !== null && $thirdId !== $firstId, 'duplicate allocates a distinct instance ID');
$thirdData = builderReadData($testDb, builderInstance($testDb, $code, 'fa', $thirdId), 'fa', exacdComponentRegistry()['title']);
integrationAssert($thirdData['title'] === $firstData['title'], 'duplicate copies content');
$state = builderMutate($testDb, ['action' => 'save_content', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $thirdId,
    'data' => ['title' => 'Test Third', 'undertitle' => 'third subtitle']]);
$firstDataAfter = builderReadData($testDb, builderInstance($testDb, $code, 'fa', $firstId), 'fa', exacdComponentRegistry()['title']);
integrationAssert($firstDataAfter['title'] === $firstData['title'], 'editing duplicate does not mutate source');

$staleRejected = false;
try {
    builderMutate($testDb, ['action' => 'toggle', 'code' => $code, 'locale' => 'fa', 'revision' => 1, 'id' => $firstId]);
} catch (BuilderApiError $e) {
    $staleRejected = $e->status === 409;
}
integrationAssert($staleRejected, 'stale revision is rejected');
$crossPageRejected = false;
try {
    builderInstance($testDb, 'INVALID', 'fa', $firstId);
} catch (BuilderApiError $e) {
    $crossPageRejected = $e->status === 404;
}
integrationAssert($crossPageRejected, 'instance cannot be addressed from a different page');

$state = builderMutate($testDb, ['action' => 'move', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $secondId, 'direction' => 'up']);
integrationAssert($state['layouts']['fa']['revision'] > $revision, 'reorder persists and advances revision');
$state = builderMutate($testDb, ['action' => 'toggle', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $secondId]);
integrationAssert(builderInstance($testDb, $code, 'fa', $secondId)['enabled'] == 0, 'visibility toggle persists');
$state = builderMutate($testDb, ['action' => 'delete', 'code' => $code, 'locale' => 'fa', 'revision' => $state['layouts']['fa']['revision'], 'id' => $secondId]);
integrationAssert(!in_array($secondId, array_column($state['instances']['fa'], 'id'), true), 'delete hides only selected instance');

$row = builderRows($testDb, "SELECT * FROM tbl_categories WHERE tbl_categories_code=? AND tbl_categories_lng='fa' AND tbl_categories_type=4", [$code])[0];
$lang = array_fill(0, 200, 'Test');
$lang['faq_form_heading'] = 'Test form';
$site_url = 'http://localhost/expertsacademy/';
ob_start();
exacdCmsRenderPage($row, 'fa');
$html = ob_get_clean();
integrationAssert(substr_count($html, 'Test First') === 1 && substr_count($html, 'Test Third') === 1 && !str_contains($html, 'Test Second'), 'public builder renderer uses independent active instances once');

// A matching, already paired code can share its component order while retaining two content sets.
$paired = builderRows($testDb, "SELECT fa.tbl_categories_code AS code FROM tbl_categories fa JOIN tbl_categories en ON en.tbl_categories_code=fa.tbl_categories_code AND en.tbl_categories_lng='en' AND en.tbl_categories_type=4 WHERE fa.tbl_categories_type=4 AND fa.tbl_categories_lng='fa' AND fa.tbl_categories_design=en.tbl_categories_design AND fa.tbl_categories_code<>? ORDER BY fa.tbl_categories_code", [$code]);
$sharedCode = null;
foreach ($paired as $candidate) {
    $faDry = exacdBuilderConvertLocale($testDb, $candidate['code'], 'fa', false);
    $enDry = exacdBuilderConvertLocale($testDb, $candidate['code'], 'en', false);
    if ($faDry['ok'] && $enDry['ok'] && $faDry['planned_instances'] > 0 && $faDry['planned_instances'] === $enDry['planned_instances']) {
        $sharedCode = $candidate['code'];
        break;
    }
}
integrationAssert($sharedCode !== null, 'fixture has a proven bilingual pair with matching layouts');
integrationAssert(exacdBuilderConvertLocale($testDb, $sharedCode, 'fa', true)['applied'], 'paired FA locale converts independently');
integrationAssert(exacdBuilderConvertLocale($testDb, $sharedCode, 'en', true)['applied'], 'paired EN locale converts independently');
$pairedState = builderState($testDb, $sharedCode);
$enOriginalId = $pairedState['instances']['en'][0]['id'];
$enOriginalData = $pairedState['instances']['en'][0]['data'];
$pairedState = builderMutate($testDb, ['action' => 'share_structure', 'code' => $sharedCode, 'locale' => 'fa',
    'revision_fa' => $pairedState['layouts']['fa']['revision'], 'revision_en' => $pairedState['layouts']['en']['revision']]);
integrationAssert($pairedState['layouts']['fa']['structure_mode'] === 'shared' && $pairedState['layouts']['en']['structure_mode'] === 'shared', 'explicit share sets both layout modes');
integrationAssert(array_column($pairedState['instances']['fa'], 'id') === array_column($pairedState['instances']['en'], 'id'), 'shared bilingual page uses one ordered instance sequence');
integrationAssert(integrationContentValues($pairedState['instances']['en'][0]['data']) === integrationContentValues($enOriginalData), 'English content survives sharing unchanged');
integrationAssert((int) builderRows($testDb, 'SELECT COUNT(*) AS n FROM tbl_page_component_instances WHERE id=? AND deleted_at IS NOT NULL', [$enOriginalId])[0]['n'] === 1, 'original English instance is retained as rollback archive');
$sharedBefore = array_column($pairedState['instances']['fa'], 'id');
$pairedState = builderMutate($testDb, ['action' => 'add', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'key' => 'title']);
$newSharedId = array_values(array_diff(array_column($pairedState['instances']['fa'], 'id'), $sharedBefore))[0] ?? null;
integrationAssert($newSharedId !== null && in_array($newSharedId, array_column($pairedState['instances']['en'], 'id'), true), 'adding to shared structure updates both languages');
$pairedState = builderMutate($testDb, ['action' => 'save_content', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'id' => $newSharedId,
    'data' => ['title' => 'فارسی مشترک', 'undertitle' => 'FA only']]);
$pairedState = builderMutate($testDb, ['action' => 'save_content', 'code' => $sharedCode, 'locale' => 'en', 'revision' => $pairedState['layouts']['en']['revision'], 'id' => $newSharedId,
    'data' => ['title' => 'English shared', 'undertitle' => 'EN only']]);
integrationAssert($pairedState['instances']['fa'][count($pairedState['instances']['fa']) - 1]['data']['title'] !==
    $pairedState['instances']['en'][count($pairedState['instances']['en']) - 1]['data']['title'], 'shared instance retains separate localized content');
$enRevision = $pairedState['layouts']['en']['revision'];
$pairedState = builderMutate($testDb, ['action' => 'move', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'id' => $newSharedId, 'direction' => 'up']);
integrationAssert($pairedState['layouts']['en']['revision'] === $enRevision + 1 &&
    array_column($pairedState['instances']['fa'], 'id') === array_column($pairedState['instances']['en'], 'id'), 'shared reorder persists once and invalidates both stale views');

$sharedPage = builderRows($testDb, "SELECT * FROM tbl_categories WHERE tbl_categories_code=? AND tbl_categories_lng='fa' AND tbl_categories_type=4", [$sharedCode])[0];
ob_start();
exacdCmsRenderPage($sharedPage, 'fa');
$withoutFaqForm = ob_get_clean();
integrationAssert(!str_contains($withoutFaqForm, 'faq-form-section'), 'collaboration form is absent until its own component is added');
$beforeFormIds = array_column($pairedState['instances']['fa'], 'id');
$pairedState = builderMutate($testDb, ['action' => 'add', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'key' => 'faq_form']);
$formId = array_values(array_diff(array_column($pairedState['instances']['fa'], 'id'), $beforeFormIds))[0] ?? null;
ob_start();
exacdCmsRenderPage($sharedPage, 'fa');
$withFaqForm = ob_get_clean();
integrationAssert($formId !== null && str_contains($withFaqForm, 'faq-form-section') && !str_contains($withFaqForm, 'faq-accordion-'), 'collaboration form renders independently of FAQ answers');
$beforeFaqIds = array_column($pairedState['instances']['fa'], 'id');
$pairedState = builderMutate($testDb, ['action' => 'add', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'key' => 'faq']);
$faqId = array_values(array_diff(array_column($pairedState['instances']['fa'], 'id'), $beforeFaqIds))[0] ?? null;
$pairedState = builderMutate($testDb, ['action' => 'save_content', 'code' => $sharedCode, 'locale' => 'fa', 'revision' => $pairedState['layouts']['fa']['revision'], 'id' => $faqId,
    'data' => ['items' => [['title' => 'Test FAQ question', 'text' => '<p>Test FAQ answer</p>']]]]);
ob_start();
exacdCmsRenderPage($sharedPage, 'fa');
$withBoth = ob_get_clean();
integrationAssert(str_contains($withBoth, 'faq-form-section') && str_contains($withBoth, 'Test FAQ question') && str_contains($withBoth, 'Test FAQ answer'), 'FAQ content and collaboration form coexist as separate components');
$sharedPageEn = builderRows($testDb, "SELECT * FROM tbl_categories WHERE tbl_categories_code=? AND tbl_categories_lng='en' AND tbl_categories_type=4", [$sharedCode])[0];
ob_start();
exacdCmsRenderPage($sharedPageEn, 'en');
$englishWithoutFaqTranslation = ob_get_clean();
integrationAssert(str_contains($englishWithoutFaqTranslation, 'faq-form-section') && !str_contains($englishWithoutFaqTranslation, 'Test FAQ question'), 'missing EN FAQ translation does not leak FA content');

$different = builderRows($testDb, "SELECT fa.tbl_categories_code AS code FROM tbl_categories fa JOIN tbl_categories en ON en.tbl_categories_code=fa.tbl_categories_code AND en.tbl_categories_lng='en' AND en.tbl_categories_type=4 WHERE fa.tbl_categories_type=4 AND fa.tbl_categories_lng='fa' AND fa.tbl_categories_design<>en.tbl_categories_design ORDER BY fa.tbl_categories_code");
$differentCode = null;
foreach ($different as $candidate) {
    $faDry = exacdBuilderConvertLocale($testDb, $candidate['code'], 'fa', false);
    $enDry = exacdBuilderConvertLocale($testDb, $candidate['code'], 'en', false);
    $faSequence = array_map(fn($item) => [$item['type_key'], $item['marker']], $faDry['instances']);
    $enSequence = array_map(fn($item) => [$item['type_key'], $item['marker']], $enDry['instances']);
    if ($faDry['ok'] && $enDry['ok'] && $faSequence !== $enSequence) { $differentCode = $candidate['code']; break; }
}
integrationAssert($differentCode !== null, 'fixture has a bilingual pair with different legacy layouts');
exacdBuilderConvertLocale($testDb, $differentCode, 'fa', true);
exacdBuilderConvertLocale($testDb, $differentCode, 'en', true);
$differentState = builderState($testDb, $differentCode);
$differentRejected = false;
try {
    builderMutate($testDb, ['action' => 'share_structure', 'code' => $differentCode, 'locale' => 'fa',
        'revision_fa' => $differentState['layouts']['fa']['revision'], 'revision_en' => $differentState['layouts']['en']['revision']]);
} catch (BuilderApiError $e) { $differentRejected = $e->status === 409; }
integrationAssert($differentRejected && builderLayouts($testDb, $differentCode)['fa']['structure_mode'] === 'independent', 'different language structures cannot be implicitly merged');

echo "Integration checks completed on disposable database only.\n";
