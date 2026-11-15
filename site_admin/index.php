<?php
include_once './includer.php';
$Tabpage = '1';
$pagetitle = 'داشبورد';
include_once './header.inc.php';

/** Optional dashboard queries should not hide the rest of the CMS. */
function dashboardCount($query)
{
    try {
        $result = Query($query);
        if (!$result) {
            return null;
        }
        $row = mysqli_fetch_row($result);
        return $row ? (int) $row[0] : null;
    } catch (Throwable $exception) {
        error_log('CMS dashboard count failed: ' . $exception->getMessage());
        return null;
    }
}

$dashboardStats = [
    ['label' => 'صفحات سایت', 'count' => dashboardCount("SELECT COUNT(*) FROM tbl_categories WHERE tbl_categories_type = 4 AND tbl_categories_lng = 'fa'"), 'icon' => 'bx-layout', 'href' => 'category_manage.php?t=4', 'tone' => 'violet'],
    ['label' => 'محتوای فارسی', 'count' => dashboardCount("SELECT COUNT(*) FROM tbl_content WHERE tbl_content_fld1 = 'fa'"), 'icon' => 'bx-file', 'href' => null, 'tone' => 'blue'],
    ['label' => 'آیتم‌های منو', 'count' => dashboardCount("SELECT COUNT(*) FROM tbl_menu WHERE tbl_menu_lng = 'fa'"), 'icon' => 'bx-menu', 'href' => 'menu_manager.php', 'tone' => 'cyan'],
    ['label' => 'پیام‌های تماس بررسی‌نشده', 'count' => dashboardCount("SELECT COUNT(*) FROM tbl_form WHERE tbl_form_section = 'contact' AND COALESCE(tbl_form_model, '') = '' AND tbl_form_status = 0"), 'icon' => 'bx-envelope', 'href' => 'form_contact.php?status=pending', 'tone' => 'amber'],
];

$recentPages = [];
$recentPagesAvailable = true;
try {
    $recentPagesResult = Query("SELECT tbl_categories_code, tbl_categories_title FROM tbl_categories WHERE tbl_categories_type = 4 AND tbl_categories_lng = 'fa' ORDER BY tbl_categories_id DESC LIMIT 5");
    if ($recentPagesResult) {
        while ($recentPage = mysqli_fetch_assoc($recentPagesResult)) {
            $recentPages[] = $recentPage;
        }
    } else {
        $recentPagesAvailable = false;
    }
} catch (Throwable $exception) {
    error_log('CMS dashboard recent pages failed: ' . $exception->getMessage());
    $recentPagesAvailable = false;
}
?>

<div class="admin-dashboard">
    <div class="dashboard-hero">
        <div>
            <span class="dashboard-eyebrow">ExAcd CMS</span>
            <h1>داشبورد مدیریت</h1>
            <p>به صفحه‌ها، محتوا و پیام‌های سایت از یک جا دسترسی داشته باشید.</p>
        </div>
        <div class="dashboard-hero-actions">
            <a href="category_manage.php?t=4&do=add" class="btn btn-light"><i class="bx bx-plus" aria-hidden="true"></i> صفحهٔ جدید</a>
            <a href="../" class="btn dashboard-outline-button" target="_blank" rel="noopener"><i class="bx bx-link-external" aria-hidden="true"></i> مشاهدهٔ سایت</a>
        </div>
    </div>

    <?php if (!empty($error)) : ?>
        <div class="alert alert-warning" role="alert"><?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="dashboard-stats" aria-label="آمار سایت">
        <?php foreach ($dashboardStats as $stat) : ?>
            <div class="dashboard-stat dashboard-stat-<?= $stat['tone'] ?>">
                <div class="dashboard-stat-icon"><i class="bx <?= $stat['icon'] ?>" aria-hidden="true"></i></div>
                <div class="dashboard-stat-content">
                    <span class="dashboard-stat-label"><?= $stat['label'] ?></span>
                    <strong><?= $stat['count'] === null ? '—' : htmlspecialchars((string) toPersianNum($stat['count']), ENT_QUOTES, 'UTF-8') ?></strong>
                </div>
                <?php if ($stat['href'] !== null) : ?>
                    <a class="dashboard-stat-link" href="<?= $stat['href'] ?>" aria-label="مشاهدهٔ <?= $stat['label'] ?>"><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="dashboard-columns">
        <section class="card dashboard-card" aria-labelledby="recent-pages-title">
            <div class="dashboard-card-heading">
                <div><span class="dashboard-section-kicker">مدیریت محتوا</span><h2 id="recent-pages-title">صفحات اخیر</h2></div>
                <a href="category_manage.php?t=4" class="dashboard-text-link">همهٔ صفحات <i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
            </div>
            <?php if (!$recentPagesAvailable) : ?>
                <p class="dashboard-empty">اطلاعات صفحات در حال حاضر در دسترس نیست.</p>
            <?php elseif (empty($recentPages)) : ?>
                <p class="dashboard-empty">هنوز صفحه‌ای ثبت نشده است.</p>
            <?php else : ?>
                <ul class="dashboard-recent-list">
                    <?php foreach ($recentPages as $recentPage) : ?>
                        <li>
                            <span class="dashboard-recent-icon"><i class="bx bx-file-blank" aria-hidden="true"></i></span>
                            <span class="dashboard-recent-title"><?= htmlspecialchars((string) $recentPage['tbl_categories_title'], ENT_QUOTES, 'UTF-8') ?></span>
                            <a href="category_manage.php?t=4&amp;id=<?= rawurlencode((string) $recentPage['tbl_categories_code']) ?>&amp;do=edit" class="dashboard-text-link" aria-label="ویرایش <?= htmlspecialchars((string) $recentPage['tbl_categories_title'], ENT_QUOTES, 'UTF-8') ?>">ویرایش</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section class="card dashboard-card" aria-labelledby="quick-actions-title">
            <div class="dashboard-card-heading"><div><span class="dashboard-section-kicker">میانبرها</span><h2 id="quick-actions-title">کارهای پرکاربرد</h2></div></div>
            <div class="dashboard-shortcuts">
                <a href="misc.php?t=1" class="dashboard-shortcut"><span><i class="bx bx-edit-alt" aria-hidden="true"></i> ویرایش صفحات متنی</span><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
                <a href="menu_manager.php" class="dashboard-shortcut"><span><i class="bx bx-navigation" aria-hidden="true"></i> تنظیم منوی بالای سایت</span><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
                <a href="menu_footer.php" class="dashboard-shortcut"><span><i class="bx bx-list-ul" aria-hidden="true"></i> تنظیم منوی پایین سایت</span><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
                <a href="setting.php" class="dashboard-shortcut"><span><i class="bx bx-cog" aria-hidden="true"></i> تنظیمات عمومی</span><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
                <a href="help.php" class="dashboard-shortcut"><span><i class="bx bx-help-circle" aria-hidden="true"></i> راهنمای قسمت‌ها</span><i class="bx bx-left-arrow-alt" aria-hidden="true"></i></a>
            </div>
        </section>
    </div>
</div>

<?php include_once './footer.inc.php'; ?>
