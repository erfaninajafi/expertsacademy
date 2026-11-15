<?php
// General contact submissions have no model and are not shown by form3.php/form4.php.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
if (!isset($_SESSION['siteadmin_login'])) {
    header('Location: login.php');
    exit;
}

include_once './includer.php';
$Tabpage = '6';
$pagetitle = 'پیام‌های تماس عمومی';

if (empty($_SESSION['form_contact_csrf'])) {
    $_SESSION['form_contact_csrf'] = bin2hex(random_bytes(32));
}
$contactCsrfToken = $_SESSION['form_contact_csrf'];
$contactNotice = $_SESSION['form_contact_notice'] ?? '';
unset($_SESSION['form_contact_notice']);
$contactError = '';

function contactEscape($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function contactDatabase()
{
    global $dbserver, $dbname, $dbusername, $dbpassword;
    $database = new mysqli($dbserver, $dbusername, $dbpassword, $dbname);
    $database->set_charset('utf8mb4');
    return $database;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';
    $recordId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if (
        ($_POST['action'] ?? '') !== 'mark_reviewed'
        || !is_string($submittedToken)
        || !hash_equals($contactCsrfToken, $submittedToken)
        || $recordId === false
    ) {
        http_response_code(403);
        $contactError = 'درخواست معتبر نیست. صفحه را تازه‌سازی و دوباره تلاش کنید.';
    } else {
        try {
            $database = contactDatabase();
            $statement = $database->prepare(
                "UPDATE tbl_form SET tbl_form_status = 1
                 WHERE tbl_form_id = ? AND tbl_form_section = 'contact'
                 AND COALESCE(tbl_form_model, '') = '' AND tbl_form_status = 0"
            );
            $statement->bind_param('i', $recordId);
            $statement->execute();
            $updated = $statement->affected_rows;
            $statement->close();
            $database->close();
            $_SESSION['form_contact_notice'] = $updated
                ? 'پیام به‌عنوان بررسی‌شده ثبت شد.'
                : 'وضعیت این پیام قبلاً تغییر کرده است.';
            header('Location: form_contact.php?view=' . $recordId, true, 303);
            exit;
        } catch (Throwable $exception) {
            error_log('CMS general contact status update failed: ' . $exception->getMessage());
            $contactError = 'ثبت وضعیت انجام نشد. دوباره تلاش کنید.';
        }
    }
}

$search = isset($_GET['q']) && is_string($_GET['q']) ? trim($_GET['q']) : '';
$search = mb_substr($search, 0, 120, 'UTF-8');
$status = isset($_GET['status']) && is_string($_GET['status']) ? $_GET['status'] : 'all';
if (!in_array($status, ['all', 'pending', 'reviewed'], true)) {
    $status = 'all';
}
$statusValue = $status === 'reviewed' ? 1 : 0;
$pageNumber = filter_var($_GET['page'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
$viewId = filter_var($_GET['view'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$viewId = $viewId === false ? null : $viewId;
$perPage = 20;
$total = 0;
$pageCount = 1;
$contactRows = [];
$detail = null;
$dataAvailable = true;

try {
    $database = contactDatabase();
    if ($viewId !== null) {
        $detailStatement = $database->prepare(
            "SELECT * FROM tbl_form WHERE tbl_form_id = ? AND tbl_form_section = 'contact'
             AND COALESCE(tbl_form_model, '') = '' LIMIT 1"
        );
        $detailStatement->bind_param('i', $viewId);
        $detailStatement->execute();
        $detail = $detailStatement->get_result()->fetch_assoc() ?: null;
        $detailStatement->close();
    } else {
        $filter = "tbl_form_section = 'contact' AND COALESCE(tbl_form_model, '') = ''
            AND (? = '' OR CONCAT_WS(' ', tbl_form_id, tbl_form_fld1, tbl_form_fld2,
                tbl_form_fld3, tbl_form_fld4, tbl_form_fld5, tbl_form_fld6,
                tbl_form_fld7, tbl_form_fld8, tbl_form_fld9, tbl_form_fld10,
                tbl_form_fld11, tbl_form_fld12, tbl_form_fld13, tbl_form_date) LIKE ?)
            AND (? = 'all' OR tbl_form_status = ?)";
        $like = '%' . $search . '%';
        $countStatement = $database->prepare("SELECT COUNT(*) FROM tbl_form WHERE $filter");
        $countStatement->bind_param('sssi', $search, $like, $status, $statusValue);
        $countStatement->execute();
        $countStatement->bind_result($total);
        $countStatement->fetch();
        $countStatement->close();

        $total = (int) $total;
        $pageCount = max(1, (int) ceil($total / $perPage));
        $pageNumber = min($pageNumber, $pageCount);
        $offset = ($pageNumber - 1) * $perPage;
        $listStatement = $database->prepare(
            "SELECT tbl_form_id, tbl_form_fld1, tbl_form_fld2, tbl_form_fld3,
                    tbl_form_date, tbl_form_status
             FROM tbl_form WHERE $filter ORDER BY tbl_form_id DESC LIMIT ? OFFSET ?"
        );
        $listStatement->bind_param('sssiii', $search, $like, $status, $statusValue, $perPage, $offset);
        $listStatement->execute();
        $result = $listStatement->get_result();
        while ($row = $result->fetch_assoc()) {
            $contactRows[] = $row;
        }
        $listStatement->close();
    }
    $database->close();
} catch (Throwable $exception) {
    error_log('CMS general contact inbox failed: ' . $exception->getMessage());
    $dataAvailable = false;
}

// header.inc.php starts its own session and retains the existing CMS authentication.
session_write_close();
include './header.inc.php';

$backQuery = http_build_query(['q' => $search, 'status' => $status, 'page' => $pageNumber]);
$fieldLabels = [
    1 => 'نام و نام خانوادگی',
    2 => 'ایمیل',
    3 => 'شماره همراه',
    4 => 'فیلد ۴',
    5 => 'فیلد ۵',
    6 => 'فیلد ۶',
    7 => 'فیلد ۷',
    8 => 'فیلد ۸',
    9 => 'فیلد ۹',
    10 => 'فیلد ۱۰',
    11 => 'فیلد ۱۱',
    12 => 'فیلد ۱۲',
    13 => 'فیلد ۱۳',
];
?>

<div class="admin-list-page admin-contact-page">
    <div class="card">
        <div class="card-header">
            <h1 class="h5 mb-1"><?= contactEscape($pagetitle) ?></h1>
            <p class="admin-list-toolbar__note mb-0">پیام‌هایی که از فرم تماس ثبت شده‌اند و در فرم‌های تخصصی دسته‌بندی نشده‌اند.</p>
        </div>
        <div class="card-body">
            <?php if ($contactNotice !== '') : ?>
                <div class="error" role="status"><?= contactEscape($contactNotice) ?></div>
            <?php endif; ?>
            <?php if ($contactError !== '') : ?>
                <div class="alert alert-danger" role="alert"><?= contactEscape($contactError) ?></div>
            <?php endif; ?>
            <?php if (!$dataAvailable) : ?>
                <div class="alert alert-warning" role="alert">دریافت پیام‌ها در حال حاضر ممکن نیست.</div>
            <?php elseif ($viewId !== null) : ?>
                <div class="mb-3">
                    <a class="btn btn-outline-secondary" href="form_contact.php?<?= contactEscape($backQuery) ?>">بازگشت به فهرست</a>
                </div>
                <?php if ($detail === null) : ?>
                    <p class="admin-list-empty">پیام مورد نظر پیدا نشد.</p>
                <?php else : ?>
                    <div class="admin-contact-detail">
                        <div class="admin-contact-detail__header">
                            <div>
                                <span class="admin-list-toolbar__note">شمارهٔ پیام <?= contactEscape(toPersianNum($detail['tbl_form_id'])) ?></span>
                                <h2 class="h5 mt-2 mb-0"><?= contactEscape(trim((string) $detail['tbl_form_fld1']) ?: 'پیام بدون نام') ?></h2>
                            </div>
                            <span class="admin-list-badge <?= (int) $detail['tbl_form_status'] === 0 ? 'admin-list-badge--pending' : 'admin-list-badge--reviewed' ?>">
                                <?= (int) $detail['tbl_form_status'] === 0 ? 'بررسی‌نشده' : 'بررسی‌شده' ?>
                            </span>
                        </div>
                        <dl class="admin-contact-detail__fields">
                            <div><dt>تاریخ ثبت</dt><dd><?= contactEscape($detail['tbl_form_date'] ?: '—') ?></dd></div>
                            <div><dt>زبان</dt><dd><?= contactEscape($detail['tbl_form_lng'] ?: '—') ?></dd></div>
                            <?php $hasContent = false; ?>
                            <?php foreach ($fieldLabels as $number => $label) : ?>
                                <?php
                                $value = trim((string) ($detail['tbl_form_fld' . $number] ?? ''));
                                if ($value === '') {
                                    continue;
                                }
                                $hasContent = true;
                                ?>
                                <div><dt><?= contactEscape($label) ?></dt><dd><?= nl2br(contactEscape($value)) ?></dd></div>
                            <?php endforeach; ?>
                            <?php if (!empty($detail['tbl_form_file'])) : ?>
                                <div><dt>فایل پیوست</dt><dd><?= contactEscape($detail['tbl_form_file']) ?></dd></div>
                            <?php endif; ?>
                        </dl>
                        <?php if (!$hasContent) : ?>
                            <p class="admin-list-empty">متن یا اطلاعات تماس برای این پیام ذخیره نشده است.</p>
                        <?php endif; ?>
                        <?php if ((int) $detail['tbl_form_status'] === 0) : ?>
                            <form method="post" action="form_contact.php?view=<?= (int) $detail['tbl_form_id'] ?>">
                                <input type="hidden" name="action" value="mark_reviewed">
                                <input type="hidden" name="id" value="<?= (int) $detail['tbl_form_id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= contactEscape($contactCsrfToken) ?>">
                                <button type="submit" class="btn btn-primary">علامت‌گذاری به‌عنوان بررسی‌شده</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else : ?>
                <form method="get" action="form_contact.php" class="admin-list-toolbar" role="search">
                    <div class="admin-list-toolbar__field">
                        <label for="contact-search">جست‌وجو در پیام‌ها</label>
                        <input id="contact-search" name="q" type="search" class="form-control" value="<?= contactEscape($search) ?>" placeholder="نام، ایمیل، شماره یا تاریخ">
                    </div>
                    <div class="admin-list-toolbar__field admin-list-toolbar__field--status">
                        <label for="contact-status">وضعیت</label>
                        <select id="contact-status" name="status" class="form-select">
                            <option value="all" <?= $status === 'all' ? 'selected' : '' ?>>همه</option>
                            <option value="pending" <?= $status === 'pending' ? 'selected' : '' ?>>بررسی‌نشده</option>
                            <option value="reviewed" <?= $status === 'reviewed' ? 'selected' : '' ?>>بررسی‌شده</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">نمایش نتایج</button>
                    <p class="admin-list-count"><?= contactEscape(toPersianNum($total)) ?> پیام</p>
                </form>
                <?php if (empty($contactRows)) : ?>
                    <p class="admin-list-empty">پیامی با این شرایط پیدا نشد.</p>
                <?php else : ?>
                    <div class="admin-list-scroll">
                        <table class="table admin-list-table">
                            <thead><tr><th scope="col">شماره</th><th scope="col">نام</th><th scope="col">راه ارتباطی</th><th scope="col">تاریخ</th><th scope="col">وضعیت</th><th scope="col">عملیات</th></tr></thead>
                            <tbody>
                                <?php foreach ($contactRows as $row) : ?>
                                    <?php
                                    $contactText = trim((string) $row['tbl_form_fld2']);
                                    if ($contactText === '') {
                                        $contactText = trim((string) $row['tbl_form_fld3']);
                                    }
                                    ?>
                                    <tr>
                                        <td><?= contactEscape(toPersianNum($row['tbl_form_id'])) ?></td>
                                        <td><?= contactEscape(trim((string) $row['tbl_form_fld1']) ?: 'بدون نام') ?></td>
                                        <td><?= contactEscape($contactText ?: '—') ?></td>
                                        <td><?= contactEscape($row['tbl_form_date'] ?: '—') ?></td>
                                        <td><span class="admin-list-badge <?= (int) $row['tbl_form_status'] === 0 ? 'admin-list-badge--pending' : 'admin-list-badge--reviewed' ?>"><?= (int) $row['tbl_form_status'] === 0 ? 'بررسی‌نشده' : 'بررسی‌شده' ?></span></td>
                                        <td class="admin-list-actions"><a class="btn btn-sm btn-outline-primary" href="form_contact.php?view=<?= (int) $row['tbl_form_id'] ?>">مشاهده</a></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php if ($pageCount > 1) : ?>
                        <nav aria-label="صفحات پیام‌های تماس" class="mt-3">
                            <ul class="pagination">
                                <?php for ($number = max(1, $pageNumber - 2); $number <= min($pageCount, $pageNumber + 2); $number++) : ?>
                                    <?php $pageHref = 'form_contact.php?' . http_build_query(['q' => $search, 'status' => $status, 'page' => $number]); ?>
                                    <li class="page-item <?= $number === $pageNumber ? 'active' : '' ?>"><a class="page-link" href="<?= contactEscape($pageHref) ?>" <?= $number === $pageNumber ? 'aria-current="page"' : '' ?>><?= contactEscape(toPersianNum($number)) ?></a></li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include './footer.inc.php'; ?>
