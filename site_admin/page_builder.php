<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (empty($_SESSION['siteadmin_login'])) {
    header('Location: login.php');
    exit;
}
include './includer.php';

$Tabpage = '58';
$pagetitle = 'ویرایش ساختار صفحه';
$builderCode = isset($_GET['code']) ? trim((string) $_GET['code']) : '';
$builderPage = array();

if (preg_match('/\A[A-Za-z0-9_-]{1,64}\z/D', $builderCode)) {
    $builderResult = Query("SELECT tbl_categories_lng, tbl_categories_title, tbl_categories_url, tbl_categories_index
        FROM tbl_categories WHERE tbl_categories_type = 4
        AND tbl_categories_code = '$builderCode'
        AND tbl_categories_lng IN ('fa', 'en')");
    if ($builderResult) {
        while ($builderRow = mysqli_fetch_assoc($builderResult)) {
            $builderPage[$builderRow['tbl_categories_lng']] = $builderRow;
        }
    }
}

include './header.inc.php';

if (!$builderPage) {
    http_response_code(404);
    echo '<div class="alert alert-warning">صفحهٔ موردنظر پیدا نشد. <a href="category_manage.php?t=4">بازگشت به فهرست صفحات</a></div>';
    include './footer.inc.php';
    return;
}

function exacdBuilderPreviewUrl($row, $locale)
{
    if (!$row) {
        return '';
    }
    if ((string) $row['tbl_categories_index'] === '1') {
        return '../' . $locale . '/';
    }
    return '../' . $locale . '/' . rawurlencode((string) $row['tbl_categories_url']) . '/';
}

$builderTitle = isset($builderPage['fa']) ? $builderPage['fa']['tbl_categories_title'] : $builderPage['en']['tbl_categories_title'];
?>
<main class="cms-builder" data-page-code="<?= htmlspecialchars($builderCode, ENT_QUOTES, 'UTF-8') ?>" dir="rtl">
    <header class="cms-builder__toolbar">
        <div class="cms-builder__identity">
            <a class="cms-builder__back" href="category_manage.php?t=4"><i class="bx bx-right-arrow-alt" aria-hidden="true"></i> فهرست صفحات</a>
            <div class="cms-builder__title-row">
                <div>
                    <h1><?= htmlspecialchars((string) $builderTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                    <p>ساختار و محتوای کامپوننت‌های صفحه</p>
                </div>
                <span class="cms-builder__save-status" id="cms-builder-status" role="status" aria-live="polite">در حال بارگذاری…</span>
            </div>
        </div>
        <div class="cms-builder__toolbar-actions">
            <div class="cms-builder__locale-switch" role="group" aria-label="زبان ویرایش">
                <button type="button" data-locale="fa" class="is-active" aria-pressed="true">فارسی</button>
                <button type="button" data-locale="en" aria-pressed="false">English</button>
            </div>
            <a class="btn btn-outline-primary" href="category_manage.php?t=4&amp;id=<?= rawurlencode($builderCode) ?>&amp;do=edit">عنوان، آدرس و SEO</a>
            <?php foreach (array('fa' => 'نمایش فارسی', 'en' => 'English preview') as $builderLocale => $builderLabel) : ?>
                <?php $builderPreviewUrl = exacdBuilderPreviewUrl(isset($builderPage[$builderLocale]) ? $builderPage[$builderLocale] : null, $builderLocale); ?>
                <?php if ($builderPreviewUrl !== '') : ?>
                    <a class="btn btn-outline-secondary" href="<?= htmlspecialchars($builderPreviewUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= htmlspecialchars($builderLabel, ENT_QUOTES, 'UTF-8') ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </header>

    <div class="cms-builder__message" id="cms-builder-message" role="alert" hidden></div>

    <section class="cms-builder__legacy" id="cms-builder-legacy" hidden aria-labelledby="cms-builder-legacy-title">
        <div>
            <h2 id="cms-builder-legacy-title">این زبان هنوز از چیدمان قدیمی استفاده می‌کند</h2>
            <p>سکشن‌ها و محتوای فعلی بدون تغییر نمایش داده می‌شوند. برای استفاده از نمونه‌های مستقل، ساختار این زبان را به ویرایشگر جدید منتقل کنید.</p>
            <p class="cms-builder__legacy-note">انتقال، روش نمایش عمومی این زبان را تغییر می‌دهد. پیش از ادامه، پیش‌نمایش فعلی را بررسی کنید.</p>
        </div>
        <div class="cms-builder__legacy-actions">
            <a class="btn btn-outline-secondary" href="category_manage.php?t=4&amp;id=<?= rawurlencode($builderCode) ?>&amp;do=edit">ویرایش چیدمان قدیمی</a>
            <button type="button" class="btn btn-primary" id="cms-builder-activate">انتقال به ویرایشگر جدید</button>
        </div>
    </section>

    <div class="cms-builder__workspace" id="cms-builder-workspace" hidden>
        <aside class="cms-builder__catalog" aria-labelledby="cms-builder-catalog-title">
            <div class="cms-builder__panel-heading">
                <h2 id="cms-builder-catalog-title">کامپوننت‌ها</h2>
                <p>هر بار افزودن، یک نمونهٔ مستقل می‌سازد.</p>
            </div>
            <label class="cms-builder__search-label" for="cms-builder-search">جست‌وجوی کامپوننت</label>
            <input id="cms-builder-search" class="form-control" type="search" placeholder="نام یا کاربرد…" autocomplete="off">
            <div id="cms-builder-catalog-list" class="cms-builder__catalog-list"></div>
            <p id="cms-builder-catalog-empty" class="cms-builder__empty-small" hidden>کامپوننتی با این عبارت پیدا نشد.</p>
        </aside>

        <section class="cms-builder__canvas" aria-labelledby="cms-builder-canvas-title">
            <div class="cms-builder__panel-heading cms-builder__panel-heading--row">
                <div>
                    <h2 id="cms-builder-canvas-title">ساختار صفحه</h2>
                    <p>سکشن‌ها به ترتیب نمایش در سایت چیده شده‌اند.</p>
                </div>
                <span id="cms-builder-count" class="cms-builder__count"></span>
            </div>
            <div class="cms-builder__structure" id="cms-builder-structure" hidden>
                <span id="cms-builder-structure-status"></span>
                <button type="button" class="btn btn-outline-primary" id="cms-builder-share" hidden>یکی‌کردن ساختار دو زبان</button>
            </div>
            <ol id="cms-builder-instances" class="cms-builder__instances"></ol>
            <div id="cms-builder-canvas-empty" class="cms-builder__empty">
                <i class="bx bx-layout" aria-hidden="true"></i>
                <h3>هنوز کامپوننتی افزوده نشده است</h3>
                <p>از فهرست کامپوننت‌ها، نخستین سکشن صفحه را اضافه کنید.</p>
            </div>
        </section>

        <aside class="cms-builder__inspector" aria-labelledby="cms-builder-inspector-title">
            <div class="cms-builder__panel-heading">
                <h2 id="cms-builder-inspector-title">تنظیمات نمونه</h2>
                <p>فقط محتوای نمونهٔ انتخاب‌شده در اینجا ویرایش می‌شود.</p>
            </div>
            <div id="cms-builder-inspector-empty" class="cms-builder__empty-small">برای ویرایش، یک کامپوننت از ساختار صفحه انتخاب کنید.</div>
            <div id="cms-builder-inspector-content" hidden></div>
        </aside>
    </div>
</main>
<script src="asset/js/page-builder.js" defer></script>
<?php include './footer.inc.php'; ?>
