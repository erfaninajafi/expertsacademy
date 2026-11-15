<?php
declare(strict_types=1);

/** Component gallery: every registered variant, rendered by the production preview.

 */

require dirname(__DIR__) . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Auth\Auth;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;

try {
    $actor = Auth::actor();
} catch (Problem $error) {
    if ($error->status === 401) {
        header('Location: login.php');
        exit;
    }
    http_response_code($error->status);
    echo 'Authentication failed.';
    exit;
}

$locale = in_array($_GET['locale'] ?? null, ['fa', 'en'], true) ? (string) $_GET['locale'] : 'en';
$only = is_string($_GET['type'] ?? null) ? (string) $_GET['type'] : '';
$catalogue = ComponentRegistry::catalog();
$types = array_values(array_filter($catalogue, static fn (array $item): bool => $only === '' || $item['type'] === $only));
$total = 0;
foreach ($catalogue as $item) {
    $total += count($item['variants']);
}
$preview = 'studio_component_preview.php';
$dir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$base = dirname($dir);
?>
<!doctype html>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Component gallery · Experts Academy</title>
<style>
:root{color-scheme:light;--ink:#172b26;--muted:#59675f;--line:#d5d9ce;--canvas:#f5f3ed;--surface:#fff;--brand:#824822}
*{box-sizing:border-box}
body{margin:0;background:var(--canvas);color:var(--ink);font:15px/1.55 system-ui,-apple-system,"Segoe UI",sans-serif}
header{position:sticky;top:0;z-index:5;display:flex;flex-wrap:wrap;gap:12px;align-items:center;padding:14px 22px;background:rgba(255,255,255,.94);border-bottom:1px solid var(--line);backdrop-filter:blur(8px)}
h1{font-size:18px;margin:0 auto 0 0;font-weight:650;letter-spacing:-.01em}
h1 span{font-weight:400;color:var(--muted);font-size:13px}
a{color:inherit}
nav{display:flex;gap:6px;flex-wrap:wrap;padding:12px 22px 0}
nav a{padding:5px 10px;border:1px solid var(--line);border-radius:999px;background:var(--surface);font-size:12px;text-decoration:none}
nav a[aria-current="true"]{background:var(--ink);color:#fff;border-color:var(--ink)}
main{padding:16px 22px 60px;display:grid;gap:38px}
section>header{position:static;padding:0 0 10px;background:none;border:0;backdrop-filter:none}
h2{font-size:16px;margin:0}
h2 code{font-size:12px;color:var(--muted);font-weight:400}
p.note{margin:2px 0 0;font-size:12.5px;color:var(--muted)}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(360px,1fr));gap:16px}
article{background:var(--surface);border:1px solid var(--line);border-radius:14px;overflow:hidden;box-shadow:0 18px 40px -34px rgba(23,43,38,.5)}
article header{position:static;display:flex;gap:8px;align-items:center;padding:9px 12px;background:#fff;border-bottom:1px solid var(--line);backdrop-filter:none}
article header strong{font-size:12.5px}
article header span{margin-left:auto;font-size:11px;color:var(--muted)}
iframe{display:block;width:100%;height:420px;border:0;background:#fff}
@media(max-width:560px){.grid{grid-template-columns:1fr}iframe{height:360px}}
</style>
</head>
<body>
<header>
    <h1>Component gallery <span>· <?= count($catalogue) ?> types · <?= $total ?> variants · actor <?= htmlspecialchars($actor['name'], ENT_QUOTES, 'UTF-8') ?></span></h1>
    <a href="page_studio.php">Page Studio</a>
    <a href="?locale=<?= $locale === 'en' ? 'fa' : 'en' ?><?= $only !== '' ? '&type=' . rawurlencode($only) : '' ?>"><?= $locale === 'en' ? 'RTL preview' : 'LTR preview' ?></a>
</header>
<nav aria-label="Filter by component">
    <a href="?locale=<?= $locale ?>"<?= $only === '' ? ' aria-current="true"' : '' ?>>All types</a>
    <?php foreach ($catalogue as $item): ?>
        <a href="?locale=<?= $locale ?>&type=<?= rawurlencode($item['type']) ?>"<?= $only === $item['type'] ? ' aria-current="true"' : '' ?>><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?></a>
    <?php endforeach; ?>
</nav>
<main>
<?php foreach ($types as $item): ?>
    <section aria-labelledby="type-<?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?>">
        <header>
            <h2 id="type-<?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8') ?> <code><?= htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8') ?></code></h2>
            <p class="note"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?> · <?= count($item['variants']) ?> variants</p>
        </header>
        <div class="grid">
            <?php foreach ($item['variants'] as $variant): ?>
                <article>
                    <header>
                        <strong><?= htmlspecialchars($variant['label'], ENT_QUOTES, 'UTF-8') ?></strong>
                        <span><?= htmlspecialchars($variant['id'], ENT_QUOTES, 'UTF-8') ?> · <?= strtoupper($locale) ?></span>
                    </header>
                    <iframe
                        title="<?= htmlspecialchars($item['label'] . ' — ' . $variant['label'], ENT_QUOTES, 'UTF-8') ?>"
                        loading="lazy"
                        src="<?= htmlspecialchars($preview . '?type=' . rawurlencode($item['type']) . '&variant=' . rawurlencode($variant['id']) . '&locale=' . $locale, ENT_QUOTES, 'UTF-8') ?>"
                    ></iframe>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
<?php endforeach; ?>
</main>
</body>
</html>
