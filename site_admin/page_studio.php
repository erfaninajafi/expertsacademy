<?php
declare(strict_types=1);

/**
 * Page Studio shell, embedded in the existing CMS chrome.
 * The editor is a compiled React application; no Vite server is required.
 */

require dirname(__DIR__) . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Auth\Auth;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Problem;

try {
    $actor = Auth::actor();
} catch (Problem $error) {
    if ($error->status === 401) {
        header('Location: login.php');
        exit;
    }
    http_response_code($error->status);
    header('Content-Type: text/plain; charset=utf-8');
    echo $error->getMessage();
    exit;
}

include './includer.php';
$Tabpage = '58';
$pagetitle = 'Page Studio';
include './header.inc.php';

$manifestPath = __DIR__ . '/asset/page-studio/.vite/manifest.json';
$manifest = is_file($manifestPath)
    ? json_decode((string) file_get_contents($manifestPath), true)
    : null;
$entry = is_array($manifest) ? ($manifest['src/main.tsx'] ?? null) : null;
$devServer = getenv('EA_STUDIO_VITE_URL') ?: '';
$devAllowed = preg_match('~^http://(?:127\.0\.0\.1|localhost):5173$~D', $devServer) === 1;

$asset = static function (string $path): string {
    if (preg_match('~^assets/[A-Za-z0-9_.-]+$~D', $path) !== 1) {
        throw new RuntimeException('Invalid editor manifest entry.');
    }
    return 'asset/page-studio/' . $path;
};

$config = [
    'mode' => 'pages',
    'api' => 'studio_api.php',
    'preview' => 'studio_preview.php',
    'componentPreview' => 'studio_component_preview.php',
    'csrf' => Auth::token(),
    'actor' => [
        'id' => $actor['id'],
        'name' => $actor['name'],
        'role' => $actor['role'] ?? '',
        'permissions' => $actor['permissions'] ?? [],
    ],
    'capabilities' => $actor['capabilities'],
    'role' => $actor['role'] ?? '',
    'permissions' => $actor['permissions'] ?? [],
    'schemaVersion' => \ExpertsAcademy\Studio\Contract\PageDocument::SCHEMA_VERSION,
    'registryVersion' => \ExpertsAcademy\Studio\Registry\ComponentRegistry::SCHEMA_VERSION,
];
?>
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex align-items-start justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Page Studio</h4>
                <p class="text-muted mb-0">Structured drafts, versioned revisions and one PHP renderer.</p>
            </div>
            <div class="text-end small">
                <div><?= htmlspecialchars($actor['name'], ENT_QUOTES, 'UTF-8') ?></div>
                <div class="text-muted"><?= htmlspecialchars(($actor['role'] ?? '') . ' · ' . implode(' · ', $actor['capabilities']), ENT_QUOTES, 'UTF-8') ?></div>
            </div>
        </div>

        <?php if (!$devAllowed && !is_array($entry)): ?>
            <div class="alert alert-warning" role="alert">
                The editor bundle is not built yet. Run
                <code>cd site_admin/studio &amp;&amp; npm install &amp;&amp; npm run build</code>
                and reload this page.
            </div>
        <?php else: ?>
            <div id="ea-studio-root" dir="ltr"></div>
            <script type="application/json" id="ea-studio-config"><?= json_encode(
                $config,
                JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
            ) ?></script>
            <?php if ($devAllowed): ?>
                <script type="module">
                    import RefreshRuntime from '<?= $devServer ?>/@react-refresh';
                    RefreshRuntime.injectIntoGlobalHook(window);
                    window.$RefreshReg$ = () => {};
                    window.$RefreshSig$ = () => (type) => type;
                    window.__vite_plugin_react_preamble_installed__ = true;
                </script>
                <script type="module" src="<?= $devServer ?>/@vite/client"></script>
                <script type="module" src="<?= $devServer ?>/src/main.tsx"></script>
            <?php else:
                foreach (($entry['css'] ?? []) as $css): ?>
                    <link rel="stylesheet" href="<?= htmlspecialchars($asset((string) $css), ENT_QUOTES, 'UTF-8') ?>">
                <?php endforeach; ?>
                <script type="module" src="<?= htmlspecialchars($asset((string) $entry['file']), ENT_QUOTES, 'UTF-8') ?>"></script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
<?php include './footer.inc.php';
