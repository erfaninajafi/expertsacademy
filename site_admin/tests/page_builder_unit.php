<?php
declare(strict_types=1);

$_SERVER['HTTP_HOST'] = 'localhost';
ini_set('session.save_path', sys_get_temp_dir());
session_start();
$_SESSION['siteadmin_login'] = 1;
define('EXACD_BUILDER_API_NO_DISPATCH', true);
require_once __DIR__ . '/../page_builder_api.php';

function assertBuilder(bool $condition, string $name): void
{
    if (!$condition) {
        fwrite(STDERR, "FAIL: $name\n");
        exit(1);
    }
    echo "PASS: $name\n";
}

$registry = exacdComponentRegistry();
assertBuilder(isset($registry['title'], $registry['faq'], $registry['faq_form']), 'registry contains title, FAQ, and separate collaboration form');
assertBuilder($registry['faq']['marker'] !== $registry['faq_form']['marker'], 'FAQ and collaboration form remain independent');
assertBuilder(exacdComponentDefinitionByTypeAndMarker('title', 'تایتل مدل چهار') !== null, 'title variants stay identifiable');
assertBuilder(exacdComponentDefinitionByTypeAndMarker('title', '../../evil.php') === null, 'template path cannot be selected as marker');

$first = builderNewUuid();
$second = builderNewUuid();
assertBuilder($first !== $second && builderUuid($first) === $first, 'instances get distinct valid IDs');

$safe = builderCleanHtml('<p onclick="bad()">Hello <strong>world</strong><script>alert(1)</script><a href="javascript:alert(1)">bad link</a></p>');
assertBuilder(str_contains($safe, '<strong>world</strong>') && !str_contains($safe, '<script') && !str_contains($safe, 'onclick') && !str_contains($safe, 'javascript:'), 'rich text keeps formatting and strips executable content');

$rejected = false;
try {
    builderValidateFields(['unexpected' => 'x'], $registry['title']['fields'], 'text');
} catch (BuilderApiError $e) {
    $rejected = true;
}
assertBuilder($rejected, 'unknown props are rejected');

$rejected = false;
try {
    builderStoredValue('javascript:alert(1)', ['label' => 'لینک', 'type' => 'url']);
} catch (BuilderApiError $e) {
    $rejected = true;
}
assertBuilder($rejected, 'unsafe links are rejected');

$rejected = false;
try {
    builderValidateFields(['img' => str_repeat('a', 130) . '.png,' . str_repeat('b', 130) . '.png'],
        ['img' => ['label' => 'تصویر', 'type' => 'image']], 'text');
} catch (BuilderApiError $e) {
    $rejected = true;
}
assertBuilder($rejected, 'values exceeding the legacy image column limit are rejected before SQL');
