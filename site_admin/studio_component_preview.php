<?php
declare(strict_types=1);

/** Renders a single registry component in a minimal document (editor preview iframe). */

require dirname(__DIR__) . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Auth\Auth;
use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Http\Http;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Render\Renderer;
use ExpertsAcademy\Studio\Repo\MediaRepository;

try {
    Auth::actor();
    $method = Http::requireMethod('GET', 'POST');
    $input = $method === 'GET' ? $_GET : Http::body();
    $locale = in_array($input['locale'] ?? null, ['fa', 'en'], true) ? $input['locale'] : 'en';

    if (($input['demo'] ?? '') === '1' || ($input['demo'] ?? false) === true) {
        $document = Samples::demo($locale);
    } else {
        $type = is_string($input['type'] ?? null) ? $input['type'] : '';
        $definition = ComponentRegistry::get($type);
        if ($definition === null) {
            throw new Problem(400, 'Unknown component type.');
        }
        $variant = is_string($input['variant'] ?? null) ? $input['variant'] : '';
        if (!in_array($variant, ComponentRegistry::variantIds($type), true)) {
            $variant = $definition['variants'][0]['id'];
        }
        $document = Samples::single($type, $variant, $locale);
    }

    (new Validator())->assert($document);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('Content-Security-Policy: ' . Renderer::policy());
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
    echo (new Renderer((new MediaRepository())->resolverFor($document)))->document(
        $document,
        $base . '/asset_site/css/cms-pages.css',
        $base . '/asset_site/js/cms-runtime.js'
    );
} catch (Throwable $error) {
    if ($error instanceof Problem) {
        http_response_code($error->status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $error->getMessage();
        exit;
    }
    \ExpertsAcademy\Studio\Db::log('component preview failed: ' . $error->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'The component preview could not be rendered.';
}
