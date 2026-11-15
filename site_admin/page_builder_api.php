<?php
/** JSON API for the opt-in page builder. Legacy CMS endpoints remain untouched. */
declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if (empty($_SESSION['siteadmin_login'])) {
    http_response_code(403);
    echo json_encode(['ok' => false, 'error' => 'ورود به پنل لازم است.'], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once __DIR__ . '/../include_site/config.php';
require_once __DIR__ . '/../include_site/component_registry.php';

final class BuilderApiError extends RuntimeException
{
    public int $status;
    public function __construct(string $message, int $status = 400)
    {
        parent::__construct($message);
        $this->status = $status;
    }
}

function builderFail(string $message, int $status = 400): void
{
    throw new BuilderApiError($message, $status);
}

function builderDb(): mysqli
{
    global $dbserver, $dbname, $dbusername, $dbpassword;
    $db = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
    $db->set_charset('utf8mb4');
    return $db;
}

function builderRows(mysqli $db, string $sql, array $params = []): array
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();
    return $rows;
}

function builderExecute(mysqli $db, string $sql, array $params = []): int
{
    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $count = $stmt->affected_rows;
    $stmt->close();
    return $count;
}

function builderCode($value): string
{
    if (!is_string($value) || !preg_match('/^[A-Za-z0-9_-]{1,64}$/D', $value)) {
        builderFail('کد صفحه نامعتبر است.');
    }
    return $value;
}

function builderLocale($value): string
{
    if ($value !== 'fa' && $value !== 'en') {
        builderFail('زبان نامعتبر است.');
    }
    return $value;
}

function builderUuid($value): string
{
    if (!is_string($value) || !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/D', $value)) {
        builderFail('شناسه نمونه نامعتبر است.');
    }
    return $value;
}

function builderNewUuid(): string
{
    $bytes = random_bytes(16);
    $bytes[6] = chr((ord($bytes[6]) & 0x0f) | 0x40);
    $bytes[8] = chr((ord($bytes[8]) & 0x3f) | 0x80);
    $hex = bin2hex($bytes);
    return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20);
}

function builderNewContentCode(): string
{
    return strtoupper(bin2hex(random_bytes(7)));
}

function builderPages(mysqli $db, string $code): array
{
    $rows = builderRows($db, 'SELECT tbl_categories_code, tbl_categories_title, tbl_categories_url, tbl_categories_design, tbl_categories_lng FROM tbl_categories WHERE tbl_categories_type=4 AND tbl_categories_code=? AND tbl_categories_lng IN (\'fa\',\'en\')', [$code]);
    $pages = [];
    foreach ($rows as $row) {
        $locale = $row['tbl_categories_lng'];
        if (isset($pages[$locale])) {
            builderFail('رکوردهای زبان صفحه تکراری هستند؛ ابتدا آن‌ها را بررسی کنید.', 409);
        }
        $pages[$locale] = $row;
    }
    if (!$pages) {
        builderFail('صفحه پیدا نشد.', 404);
    }
    return $pages;
}

function builderLayouts(mysqli $db, string $code): array
{
    $layouts = ['fa' => ['mode' => 'legacy', 'revision' => 0, 'structure_mode' => 'independent'], 'en' => ['mode' => 'legacy', 'revision' => 0, 'structure_mode' => 'independent']];
    foreach (builderRows($db, 'SELECT locale, mode, revision, structure_mode FROM tbl_page_builder_layouts WHERE page_code=?', [$code]) as $row) {
        if (isset($layouts[$row['locale']])) {
            $layouts[$row['locale']] = ['mode' => $row['mode'], 'revision' => (int) $row['revision'], 'structure_mode' => $row['structure_mode']];
        }
    }
    return $layouts;
}

function builderRequireSchema(mysqli $db): void
{
    $tables = builderRows($db, "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME IN ('tbl_page_builder_layouts','tbl_page_component_instances')");
    if (count($tables) !== 2) builderFail('ابتدا Schema صفحه‌ساز را با Migration نصب کنید.', 503);
    $columns = builderRows($db, "SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tbl_page_builder_layouts' AND COLUMN_NAME='structure_mode'");
    if (!$columns) builderFail('نسخه Schema صفحه‌ساز قدیمی است و باید به‌روزرسانی شود.', 503);
}

function builderDefinition(array $instance): array
{
    $registry = exacdComponentRegistry();
    $key = (string) $instance['type_key'];
    if (!isset($registry[$key])) {
        builderFail('نوع این نمونه در رجیستری وجود ندارد.', 409);
    }
    $definition = $registry[$key];
    $markers = array_values($definition['variants'] ?? []);
    $markers[] = $definition['marker'];
    if (!in_array($instance['marker'], $markers, true)) {
        builderFail('مارکر نمونه با نوع آن سازگار نیست.', 409);
    }
    return $definition;
}

function builderInstance(mysqli $db, string $code, string $locale, $id, bool $lock = false): array
{
    $id = builderUuid($id);
    $sql = 'SELECT * FROM tbl_page_component_instances WHERE id=? AND page_code=? AND deleted_at IS NULL AND locale_scope IN (?,\'shared\')';
    $rows = builderRows($db, $sql . ($lock ? ' FOR UPDATE' : ''), [$id, $code, $locale]);
    if (!$rows) {
        builderFail('این نمونه به صفحه یا زبان انتخاب‌شده تعلق ندارد.', 404);
    }
    builderDefinition($rows[0]);
    return $rows[0];
}

function builderStorageTable(string $storage): ?string
{
    if ($storage === 'text') return 'tbl_textsection';
    if ($storage === 'content') return 'tbl_content';
    return null;
}

function builderFieldMap(string $storage): array
{
    if ($storage === 'text') {
        return ['title' => 'tbl_textsection_title', 'undertitle' => 'tbl_textsection_undertitle', 'img' => 'tbl_textsection_img', 'fld1' => 'tbl_textsection_fld1', 'fld2' => 'tbl_textsection_fld2', 'fld3' => 'tbl_textsection_fld3', 'fld4' => 'tbl_textsection_fld4', 'text' => 'tbl_textsection_fld4'];
    }
    return ['title' => 'tbl_content_title', 'text' => 'tbl_content_text', 'img' => 'tbl_content_img', 'file' => 'tbl_content_file', 'pageurl' => 'tbl_content_pageurl', 'url' => 'tbl_content_url', 'color' => 'tbl_content_color', 'order' => 'tbl_content_order', 'fld2' => 'tbl_content_fld2', 'fld3' => 'tbl_content_fld3', 'fld4' => 'tbl_content_fld4', 'fld5' => 'tbl_content_fld5', 'keyword' => 'tbl_content_keyword', 'description' => 'tbl_content_discription', 'position' => 'tbl_content_position'];
}

function builderStoredValue($value, array $field): string
{
    if (!is_scalar($value) && $value !== null) builderFail('مقدار یکی از فیلدها نامعتبر است.');
    $value = trim((string) $value);
    $type = $field['type'] ?? 'text';
    if (strlen($value) > ($type === 'richtext' ? 50000 : 4000)) builderFail('مقدار فیلد بیش از حد طولانی است.');
    if (!empty($field['required']) && $value === '') builderFail('فیلد «' . ($field['label'] ?? '') . '» الزامی است.');
    if ($type === 'richtext') return builderCleanHtml($value);
    if ($type === 'url') {
        if ($value !== '' && !preg_match('~^(?:https?://[^\s<>"\']+|mailto:[^\s<>"\']+|tel:[+0-9-]+|/[A-Za-z0-9_./%?=&-]*)$~uiD', $value)) builderFail('نشانی پیوند نامعتبر است.');
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    if ($type === 'image' || $type === 'file') {
        foreach (explode(',', $value) as $filename) {
            if ($filename !== '' && (!preg_match('/^[\pL\pN_. -]{1,255}$/uD', trim($filename)) || str_contains($filename, '..'))) builderFail('نام فایل نامعتبر است.');
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    if ($type === 'color') {
        if ($value !== '' && !preg_match('/^#[0-9a-fA-F]{3}(?:[0-9a-fA-F]{3})?$/D', $value)) builderFail('رنگ نامعتبر است.');
        return $value;
    }
    if ($type === 'number') {
        if ($value !== '' && (!ctype_digit(ltrim($value, '-')) || (int) $value < 0 || (int) $value > 100000)) builderFail('عدد نامعتبر است.');
        return $value;
    }
    if ($type === 'select') {
        $choices = array_keys($field['options'] ?? []);
        if ($value !== '' && !in_array($value, $choices, true)) builderFail('گزینه انتخابی نامعتبر است.');
        return $value;
    }
    return htmlspecialchars(strip_tags($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function builderCleanHtml(string $html): string
{
    $source = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    $source->loadHTML('<?xml encoding="utf-8"?><html><body>' . $html . '</body></html>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $body = $source->getElementsByTagName('body')->item(0);
    if (!$body) return '';
    $target = new DOMDocument('1.0', 'UTF-8');
    $container = $target->createElement('div');
    $target->appendChild($container);
    $allowed = ['p','br','strong','b','em','i','ul','ol','li','a','h2','h3','h4','blockquote','span'];
    $discard = ['script','style','iframe','object','embed','form','svg','math','video','audio','meta','link'];
    $copy = function (DOMNode $node, DOMNode $parent) use (&$copy, $target, $allowed, $discard): void {
        if ($node instanceof DOMText) { $parent->appendChild($target->createTextNode($node->textContent)); return; }
        if (!($node instanceof DOMElement)) return;
        $tag = strtolower($node->tagName);
        if (in_array($tag, $discard, true)) return;
        $next = $parent;
        if (in_array($tag, $allowed, true)) {
            $next = $target->createElement($tag);
            if ($tag === 'a') {
                $href = trim($node->getAttribute('href'));
                if ($href !== '' && preg_match('~^(?:https?://|mailto:|tel:|/)~i', $href) && !preg_match('/[<>"\']/', $href)) $next->setAttribute('href', $href);
            }
            $parent->appendChild($next);
        }
        foreach ($node->childNodes as $child) $copy($child, $next);
    };
    foreach ($body->childNodes as $node) $copy($node, $container);
    $out = '';
    foreach ($container->childNodes as $node) $out .= $target->saveHTML($node);
    return $out;
}

function builderEditableRow(array $row, string $storage, array $fields): array
{
    $data = [];
    $map = builderFieldMap($storage);
    if ($storage === 'content') $data['id'] = (int) $row['tbl_content_id'];
    foreach ($fields as $key => $field) {
        if (!isset($map[$key])) continue;
        $value = (string) ($row[$map[$key]] ?? '');
        $data[$key] = ($field['type'] ?? 'text') === 'richtext' ? $value : html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    return $data;
}

function builderReadData(mysqli $db, array $instance, string $locale, array $definition): array
{
    $storage = $definition['storage'] ?? 'none';
    $id = $instance['id'];
    $marker = $instance['marker'];
    if ($storage === 'text') {
        $rows = builderRows($db, 'SELECT * FROM tbl_textsection WHERE tbl_textsection_parent=? AND tbl_textsection_section=? AND tbl_textsection_lng=? AND tbl_textsection_type=\'58\' ORDER BY tbl_textsection_id', [$id, $marker, $locale]);
        return $rows ? builderEditableRow($rows[0], 'text', $definition['fields'] ?? []) : [];
    }
    if ($storage === 'content') {
        $rows = builderRows($db, 'SELECT * FROM tbl_content WHERE tbl_content_parent=? AND tbl_content_section=? AND tbl_content_fld1=? AND tbl_content_type=58 ORDER BY tbl_content_order, tbl_content_id', [$id, $marker, $locale]);
        $data = ['items' => array_map(fn($row) => builderEditableRow($row, 'content', $definition['fields'] ?? []), $rows)];
        if (!empty($definition['companion_marker'])) {
            $titleRows = builderRows($db, 'SELECT * FROM tbl_textsection WHERE tbl_textsection_parent=? AND tbl_textsection_section=? AND tbl_textsection_lng=? AND tbl_textsection_type=\'58\' ORDER BY tbl_textsection_id', [$id, $definition['companion_marker'], $locale]);
            $data['companion'] = $titleRows ? builderEditableRow($titleRows[0], 'text', $definition['companion_fields'] ?? []) : [];
        }
        return $data;
    }
    return [];
}

function builderState(mysqli $db, string $code): array
{
    builderRequireSchema($db);
    if (empty($_SESSION['page_builder_csrf'])) $_SESSION['page_builder_csrf'] = bin2hex(random_bytes(32));
    $pages = builderPages($db, $code);
    $layouts = builderLayouts($db, $code);
    $catalog = [];
    foreach (exacdComponentRegistry() as $definition) {
        if (!empty($definition['hidden'])) continue;
        $catalog[] = $definition;
    }
    $state = [
        'ok' => true,
        'csrf' => $_SESSION['page_builder_csrf'],
        'page' => [
            'code' => $code,
            'title_fa' => $pages['fa']['tbl_categories_title'] ?? '',
            'title_en' => $pages['en']['tbl_categories_title'] ?? '',
            'url_fa' => $pages['fa']['tbl_categories_url'] ?? '',
            'url_en' => $pages['en']['tbl_categories_url'] ?? '',
            'available_locales' => array_keys($pages),
        ],
        'layouts' => $layouts,
        'catalog' => $catalog,
        'instances' => ['fa' => [], 'en' => []],
    ];
    foreach (['fa', 'en'] as $locale) {
        if (!isset($pages[$locale])) continue;
        $rows = builderRows($db, 'SELECT * FROM tbl_page_component_instances WHERE page_code=? AND locale_scope IN (?,\'shared\') AND deleted_at IS NULL ORDER BY sort_order, id', [$code, $locale]);
        foreach ($rows as $row) {
            $definition = builderDefinition($row);
            $data = builderReadData($db, $row, $locale, $definition);
            $hasContent = ($definition['storage'] ?? 'none') === 'none' || !empty($data['items']) || (bool) array_filter($data['companion'] ?? $data, fn($v) => $v !== '' && $v !== null);
            $state['instances'][$locale][] = [
                'id' => $row['id'], 'key' => $row['type_key'], 'marker' => $row['marker'],
                'position' => (int) $row['sort_order'], 'enabled' => (bool) $row['enabled'],
                'scope' => $row['locale_scope'], 'status' => $hasContent ? 'complete' : 'untranslated',
                'data' => $data,
            ];
        }
    }
    return $state;
}

function builderColumns(mysqli $db, string $table): array
{
    if ($table !== 'tbl_content' && $table !== 'tbl_textsection') builderFail('جدول نامعتبر است.');
    $result = $db->query('SHOW COLUMNS FROM `' . $table . '`');
    return array_column($result->fetch_all(MYSQLI_ASSOC), 'Field');
}

function builderInsertRow(mysqli $db, string $table, array $row): void
{
    $allowed = builderColumns($db, $table);
    foreach (array_keys($row) as $column) if (!in_array($column, $allowed, true)) builderFail('فیلد ذخیره‌سازی نامعتبر است.');
    $columns = array_keys($row);
    $quoted = array_map(fn($c) => '`' . $c . '`', $columns);
    builderExecute($db, 'INSERT INTO `' . $table . '` (' . implode(',', $quoted) . ') VALUES (' . implode(',', array_fill(0, count($columns), '?')) . ')', array_values($row));
}

function builderCloneData(mysqli $db, array $from, string $newId): void
{
    $definition = builderDefinition($from);
    $markers = [$from['marker']];
    if (!empty($definition['companion_marker'])) $markers[] = $definition['companion_marker'];
    $locales = $from['locale_scope'] === 'shared' ? ['fa', 'en'] : [$from['locale_scope']];
    foreach ($locales as $locale) foreach ($markers as $marker) {
        foreach (['tbl_content' => ['id' => 'tbl_content_id', 'code' => 'tbl_content_code', 'parent' => 'tbl_content_parent', 'section' => 'tbl_content_section', 'lng' => 'tbl_content_fld1'], 'tbl_textsection' => ['id' => 'tbl_textsection_id', 'code' => 'tbl_textsection_code', 'parent' => 'tbl_textsection_parent', 'section' => 'tbl_textsection_section', 'lng' => 'tbl_textsection_lng']] as $table => $names) {
            $rows = builderRows($db, 'SELECT * FROM `' . $table . '` WHERE `' . $names['parent'] . '`=? AND `' . $names['section'] . '`=? AND `' . $names['lng'] . '`=?', [$from['id'], $marker, $locale]);
            foreach ($rows as $row) {
                unset($row[$names['id']]);
                $row[$names['parent']] = $newId;
                $row[$names['code']] = builderNewContentCode();
                builderInsertRow($db, $table, $row);
            }
        }
    }
}

function builderValidateFields(array $input, array $fields, string $storage): array
{
    $mapped = [];
    $map = builderFieldMap($storage);
    foreach ($input as $key => $value) {
        if ($key === 'id' && $storage === 'content') continue;
        if (!isset($fields[$key], $map[$key])) builderFail('فیلد ناشناخته ارسال شده است: ' . (string) $key);
        $mapped[$map[$key]] = builderStoredValue($value, $fields[$key]);
    }
    foreach ($fields as $key => $field) {
        if (!empty($field['required']) && !array_key_exists($key, $input)) builderFail('فیلد «' . ($field['label'] ?? $key) . '» الزامی است.');
    }
    $limits = $storage === 'text'
        ? ['tbl_textsection_img' => 255]
        : ['tbl_content_title' => 65535, 'tbl_content_text' => 65535, 'tbl_content_img' => 65535,
           'tbl_content_file' => 65535, 'tbl_content_fld2' => 65535, 'tbl_content_fld3' => 65535,
           'tbl_content_fld4' => 65535, 'tbl_content_fld5' => 65535,
           'tbl_content_pageurl' => 255, 'tbl_content_position' => 255, 'tbl_content_color' => 45,
           'tbl_content_url' => 1000, 'tbl_content_keyword' => 1000, 'tbl_content_discription' => 1000];
    foreach ($mapped as $column => $value) {
        if (!isset($limits[$column])) continue;
        $size = str_contains($column, 'title') || in_array($column, ['tbl_content_text','tbl_content_img','tbl_content_file','tbl_content_fld2','tbl_content_fld3','tbl_content_fld4','tbl_content_fld5'], true)
            ? strlen($value) : mb_strlen($value, 'UTF-8');
        if ($size > $limits[$column]) builderFail('مقدار فیلد برای ذخیره در پایگاه داده بیش از حد طولانی است.');
    }
    return $mapped;
}

function builderSaveText(mysqli $db, string $parent, string $marker, string $locale, array $input, array $fields): void
{
    $mapped = builderValidateFields($input, $fields, 'text');
    $rows = builderRows($db, 'SELECT tbl_textsection_id FROM tbl_textsection WHERE tbl_textsection_parent=? AND tbl_textsection_section=? AND tbl_textsection_lng=? AND tbl_textsection_type=\'58\' FOR UPDATE', [$parent, $marker, $locale]);
    if (count($rows) > 1) builderFail('برای این نمونه محتوای متنی تکراری وجود دارد.', 409);
    if ($rows) {
        if ($mapped) {
            $assignments = implode(',', array_map(fn($c) => '`' . $c . '`=?', array_keys($mapped)));
            builderExecute($db, 'UPDATE tbl_textsection SET ' . $assignments . ' WHERE tbl_textsection_id=? AND tbl_textsection_parent=?', [...array_values($mapped), (int) $rows[0]['tbl_textsection_id'], $parent]);
        }
    } else {
        builderInsertRow($db, 'tbl_textsection', ['tbl_textsection_type' => '58', 'tbl_textsection_code' => builderNewContentCode(), 'tbl_textsection_section' => $marker, 'tbl_textsection_parent' => $parent, 'tbl_textsection_lng' => $locale] + $mapped);
    }
}

function builderSaveContent(mysqli $db, array $instance, string $locale, array $data, array $definition): void
{
    $items = $data['items'] ?? null;
    if (!is_array($items) || !array_is_list($items) || count($items) > 100) builderFail('فهرست آیتم‌ها نامعتبر است.');
    $id = $instance['id'];
    $marker = $instance['marker'];
    $existing = builderRows($db, 'SELECT tbl_content_id FROM tbl_content WHERE tbl_content_parent=? AND tbl_content_section=? AND tbl_content_fld1=? AND tbl_content_type=58 FOR UPDATE', [$id, $marker, $locale]);
    $existingIds = array_map('intval', array_column($existing, 'tbl_content_id'));
    $seen = [];
    foreach ($items as $index => $item) {
        if (!is_array($item)) builderFail('اطلاعات آیتم نامعتبر است.');
        $rowId = isset($item['id']) && $item['id'] !== '' ? (int) $item['id'] : null;
        if ($rowId !== null && (!in_array($rowId, $existingIds, true) || isset($seen[$rowId]))) builderFail('شناسه آیتم به این نمونه تعلق ندارد.', 409);
        if ($rowId !== null) $seen[$rowId] = true;
        $mapped = builderValidateFields($item, $definition['fields'] ?? [], 'content');
        $mapped['tbl_content_order'] = $index + 1;
        if ($rowId !== null) {
            $assignments = implode(',', array_map(fn($c) => '`' . $c . '`=?', array_keys($mapped)));
            builderExecute($db, 'UPDATE tbl_content SET ' . $assignments . ' WHERE tbl_content_id=? AND tbl_content_parent=? AND tbl_content_section=? AND tbl_content_fld1=?', [...array_values($mapped), $rowId, $id, $marker, $locale]);
        } else {
            builderInsertRow($db, 'tbl_content', ['tbl_content_type' => 58, 'tbl_content_code' => builderNewContentCode(), 'tbl_content_section' => $marker, 'tbl_content_parent' => $id, 'tbl_content_fld1' => $locale] + $mapped);
        }
    }
    // Archive removed builder rows rather than erasing content. The old parent is retained in the archive value.
    foreach ($existingIds as $rowId) {
        if (!isset($seen[$rowId])) builderExecute($db, 'UPDATE tbl_content SET tbl_content_parent=CONCAT(\'removed:\', tbl_content_parent) WHERE tbl_content_id=? AND tbl_content_parent=?', [$rowId, $id]);
    }
    if (!empty($definition['companion_marker'])) {
        if (!isset($data['companion']) || !is_array($data['companion'])) builderFail('اطلاعات عنوان همراه نامعتبر است.');
        builderSaveText($db, $id, $definition['companion_marker'], $locale, $data['companion'], $definition['companion_fields'] ?? []);
    }
}

function builderLockRevision(mysqli $db, string $code, string $locale, $revision): void
{
    $rows = builderRows($db, 'SELECT mode, revision FROM tbl_page_builder_layouts WHERE page_code=? AND locale=? FOR UPDATE', [$code, $locale]);
    if (!$rows || $rows[0]['mode'] !== 'builder') builderFail('ابتدا چیدمان این زبان را در صفحه‌ساز فعال کنید.', 409);
    if (!is_int($revision) || (int) $rows[0]['revision'] !== $revision) builderFail('صفحه در پنجره دیگری تغییر کرده است. دوباره بارگذاری کنید.', 409);
}

function builderBumpRevision(mysqli $db, string $code, string $locale): void
{
    builderExecute($db, 'UPDATE tbl_page_builder_layouts SET revision=revision+1 WHERE page_code=? AND locale=?', [$code, $locale]);
}

function builderStructureMode(mysqli $db, string $code, string $locale): string
{
    $rows = builderRows($db, 'SELECT structure_mode FROM tbl_page_builder_layouts WHERE page_code=? AND locale=?', [$code, $locale]);
    return $rows[0]['structure_mode'] ?? 'independent';
}

/** Pair only two already converted, code-identical pages with exactly matching instance sequences. */
function builderShareStructure(mysqli $db, string $code, array $request): void
{
    $pages = builderPages($db, $code);
    if (!isset($pages['fa'], $pages['en'])) builderFail('هر دو نسخه فارسی و انگلیسی باید موجود باشند.', 409);
    $revisions = ['fa' => $request['revision_fa'] ?? null, 'en' => $request['revision_en'] ?? null];
    foreach (['fa', 'en'] as $language) builderLockRevision($db, $code, $language, $revisions[$language]);
    foreach (['fa', 'en'] as $language) {
        if (builderStructureMode($db, $code, $language) !== 'independent') builderFail('ساختار صفحه از قبل مشترک شده است.', 409);
    }
    $existingShared = builderRows($db, 'SELECT id FROM tbl_page_component_instances WHERE page_code=? AND locale_scope=\'shared\' AND deleted_at IS NULL LIMIT 1 FOR UPDATE', [$code]);
    if ($existingShared) builderFail('نمونه مشترک پیشین نیازمند بررسی است.', 409);
    $fa = builderRows($db, 'SELECT * FROM tbl_page_component_instances WHERE page_code=? AND locale_scope=\'fa\' AND deleted_at IS NULL ORDER BY sort_order,id FOR UPDATE', [$code]);
    $en = builderRows($db, 'SELECT * FROM tbl_page_component_instances WHERE page_code=? AND locale_scope=\'en\' AND deleted_at IS NULL ORDER BY sort_order,id FOR UPDATE', [$code]);
    if (count($fa) !== count($en)) builderFail('تعداد کامپوننت‌های دو زبان متفاوت است؛ ساختار مستقل باقی می‌ماند.', 409);
    foreach ($fa as $index => $source) {
        $translated = $en[$index];
        foreach (['type_key', 'marker', 'enabled', 'schema_version'] as $field) {
            if ((string) $source[$field] !== (string) $translated[$field]) builderFail('ساختار کامپوننت‌های دو زبان با هم برابر نیست.', 409);
        }
        // Preserve the original EN instance and its rows for rollback; clone only its data.
        builderCloneData($db, $translated, $source['id']);
        builderExecute($db, 'UPDATE tbl_page_component_instances SET deleted_at=NOW() WHERE id=? AND page_code=? AND locale_scope=\'en\'', [$translated['id'], $code]);
        builderExecute($db, 'UPDATE tbl_page_component_instances SET locale_scope=\'shared\' WHERE id=? AND page_code=? AND locale_scope=\'fa\'', [$source['id'], $code]);
    }
    builderExecute($db, 'UPDATE tbl_page_builder_layouts SET structure_mode=\'shared\', revision=revision+1 WHERE page_code=? AND locale IN (\'fa\',\'en\')', [$code]);
}

function builderUpload(mysqli $db, array $request): array
{
    $code = builderCode($request['code'] ?? null);
    builderPages($db, $code);
    if (empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK || $_FILES['file']['size'] > 8 * 1024 * 1024) builderFail('بارگذاری فایل ناموفق بود یا حجم آن بیش از ۸ مگابایت است.');
    $tmp = $_FILES['file']['tmp_name'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($tmp);
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if (!isset($extensions[$mime])) builderFail('فقط تصویر JPEG، PNG، WebP یا GIF پذیرفته می‌شود.');
    $name = 'cms_' . bin2hex(random_bytes(12)) . '.' . $extensions[$mime];
    $destination = __DIR__ . '/../upload_site/' . $name;
    if (!move_uploaded_file($tmp, $destination)) builderFail('ذخیره فایل ناموفق بود.', 500);
    return ['ok' => true, 'file' => $name];
}

function builderMutate(mysqli $db, array $request): array
{
    builderRequireSchema($db);
    $action = $request['action'] ?? '';
    $code = builderCode($request['code'] ?? null);
    $pages = builderPages($db, $code);
    $locale = builderLocale($request['locale'] ?? null);
    if (!isset($pages[$locale])) builderFail('نسخه این زبان برای صفحه وجود ندارد.', 404);
    if ($action === 'upload') return builderUpload($db, $request);
    if ($action === 'activate') {
        require_once __DIR__ . '/builder_migration.php';
        $report = exacdBuilderConvertLocale($db, $code, $locale, true);
        if (!$report['ok']) {
            $issueCodes = array_map(fn($item) => (string) ($item['code'] ?? 'unknown'), $report['issues'] ?? []);
            builderFail('تبدیل این صفحه نیازمند بررسی است (' . implode(', ', $issueCodes) . ').', 409);
        }
        return builderState($db, $code);
    }
    if ($action === 'share_structure') {
        $db->begin_transaction();
        try {
            builderShareStructure($db, $code, $request);
            $db->commit();
            return builderState($db, $code);
        } catch (Throwable $e) {
            $db->rollback();
            throw $e;
        }
    }
    $db->begin_transaction();
    try {
        $structureChange = in_array($action, ['add', 'duplicate', 'delete', 'toggle', 'move'], true);
        if ($structureChange) {
            // Lock both locale layouts in one stable order before changing a shared sequence.
            builderRows($db, 'SELECT locale FROM tbl_page_builder_layouts WHERE page_code=? AND locale IN (\'fa\',\'en\') ORDER BY locale FOR UPDATE', [$code]);
        }
        builderLockRevision($db, $code, $locale, $request['revision'] ?? null);
        $sharedStructure = builderStructureMode($db, $code, $locale) === 'shared';
        if ($sharedStructure && $structureChange) {
            $other = $locale === 'fa' ? 'en' : 'fa';
            if (builderStructureMode($db, $code, $other) !== 'shared') builderFail('وضعیت ساختار دو زبان ناسازگار است.', 409);
        }
        if ($action === 'add') {
            $registry = exacdComponentRegistry();
            $key = $request['key'] ?? null;
            if (!is_string($key) || !isset($registry[$key]) || !empty($registry[$key]['hidden'])) builderFail('نوع کامپوننت نامعتبر است.');
            $definition = $registry[$key];
            $marker = $definition['marker'];
            if (!empty($request['variant'])) {
                $variant = $request['variant'];
                if (!is_string($variant) || !isset($definition['variants'][$variant])) builderFail('حالت نمایشی نامعتبر است.');
                $marker = $definition['variants'][$variant];
            }
            $position = builderRows($db, 'SELECT COALESCE(MAX(sort_order),0)+1 AS next_position FROM tbl_page_component_instances WHERE page_code=? AND locale_scope IN (?,\'shared\') AND deleted_at IS NULL', [$code, $locale])[0]['next_position'];
            builderExecute($db, 'INSERT INTO tbl_page_component_instances (id,page_code,type_key,marker,locale_scope,sort_order,enabled,schema_version) VALUES (?,?,?,?,?,?,1,?)', [builderNewUuid(), $code, $key, $marker, $sharedStructure ? 'shared' : $locale, (int) $position, (int) ($definition['schema_version'] ?? 1)]);
        } elseif ($action === 'duplicate') {
            $instance = builderInstance($db, $code, $locale, $request['id'] ?? null, true);
            $newId = builderNewUuid();
            builderExecute($db, 'UPDATE tbl_page_component_instances SET sort_order=sort_order+1 WHERE page_code=? AND locale_scope=? AND sort_order>? AND deleted_at IS NULL', [$code, $instance['locale_scope'], (int) $instance['sort_order']]);
            builderExecute($db, 'INSERT INTO tbl_page_component_instances (id,page_code,type_key,marker,locale_scope,sort_order,enabled,schema_version) VALUES (?,?,?,?,?,?,?,?)', [$newId, $code, $instance['type_key'], $instance['marker'], $instance['locale_scope'], (int) $instance['sort_order'] + 1, (int) $instance['enabled'], (int) $instance['schema_version']]);
            builderCloneData($db, $instance, $newId);
        } elseif ($action === 'delete') {
            $instance = builderInstance($db, $code, $locale, $request['id'] ?? null, true);
            builderExecute($db, 'UPDATE tbl_page_component_instances SET deleted_at=NOW(), enabled=0 WHERE id=? AND page_code=?', [$instance['id'], $code]);
        } elseif ($action === 'toggle') {
            $instance = builderInstance($db, $code, $locale, $request['id'] ?? null, true);
            builderExecute($db, 'UPDATE tbl_page_component_instances SET enabled=? WHERE id=? AND page_code=?', [empty($instance['enabled']) ? 1 : 0, $instance['id'], $code]);
        } elseif ($action === 'move') {
            $instance = builderInstance($db, $code, $locale, $request['id'] ?? null, true);
            $direction = $request['direction'] ?? null;
            if ($direction !== 'up' && $direction !== 'down') builderFail('جهت جابه‌جایی نامعتبر است.');
            $rows = builderRows($db, 'SELECT id, sort_order FROM tbl_page_component_instances WHERE page_code=? AND locale_scope IN (?,\'shared\') AND deleted_at IS NULL ORDER BY sort_order,id FOR UPDATE', [$code, $locale]);
            $index = array_search($instance['id'], array_column($rows, 'id'), true);
            $other = $direction === 'up' ? $index - 1 : $index + 1;
            if ($index !== false && isset($rows[$other])) {
                builderExecute($db, 'UPDATE tbl_page_component_instances SET sort_order=? WHERE id=?', [(int) $rows[$other]['sort_order'], $instance['id']]);
                builderExecute($db, 'UPDATE tbl_page_component_instances SET sort_order=? WHERE id=?', [(int) $instance['sort_order'], $rows[$other]['id']]);
            }
        } elseif ($action === 'save_content') {
            $instance = builderInstance($db, $code, $locale, $request['id'] ?? null, true);
            $definition = builderDefinition($instance);
            $data = $request['data'] ?? null;
            if (!is_array($data)) builderFail('داده کامپوننت نامعتبر است.');
            $storage = $definition['storage'] ?? 'none';
            if ($storage === 'text') builderSaveText($db, $instance['id'], $instance['marker'], $locale, $data, $definition['fields'] ?? []);
            elseif ($storage === 'content') builderSaveContent($db, $instance, $locale, $data, $definition);
            elseif ($data) builderFail('این کامپوننت فیلد محتوایی ندارد.');
        } else {
            builderFail('عملیات ناشناخته است.');
        }
        builderBumpRevision($db, $code, $locale);
        if ($sharedStructure && $structureChange) builderBumpRevision($db, $code, $locale === 'fa' ? 'en' : 'fa');
        $db->commit();
        return builderState($db, $code);
    } catch (Throwable $e) {
        $db->rollback();
        throw $e;
    }
}

if (!defined('EXACD_BUILDER_API_NO_DISPATCH')) {
    try {
        if (empty($_SESSION['page_builder_csrf'])) $_SESSION['page_builder_csrf'] = bin2hex(random_bytes(32));
        $db = builderDb();
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $state = builderState($db, builderCode($_GET['code'] ?? null));
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'multipart/form-data') ? $_POST : json_decode(file_get_contents('php://input'), true);
            if (!is_array($request)) builderFail('بدنه درخواست نامعتبر است.');
            if (!is_string($request['csrf'] ?? null) || !hash_equals($_SESSION['page_builder_csrf'], $request['csrf'])) builderFail('نشست امنیتی منقضی شده است؛ صفحه را دوباره بارگذاری کنید.', 403);
            $state = builderMutate($db, $request);
        } else {
            header('Allow: GET, POST');
            builderFail('روش درخواست نامعتبر است.', 405);
        }
        echo json_encode($state, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
    } catch (BuilderApiError $e) {
        http_response_code($e->status);
        echo json_encode(['ok' => false, 'error' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
    } catch (Throwable $e) {
        error_log('Page builder API: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['ok' => false, 'error' => 'خطای سرور در صفحه‌ساز.'], JSON_UNESCAPED_UNICODE);
    }
}
