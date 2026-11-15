<?php
declare(strict_types=1);

/** Server-rendered preview of a draft or saved page. HTML output, same renderer as publication. */

require dirname(__DIR__) . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Auth\Auth;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Http\Http;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Render\NullMediaResolver;
use ExpertsAcademy\Studio\Render\Renderer;
use ExpertsAcademy\Studio\Repo\MediaRepository;
use ExpertsAcademy\Studio\Repo\PageRepository;
use ExpertsAcademy\Studio\Uuid;

try {
    $actor = Auth::actor();
    $method = Http::requireMethod('GET', 'POST');
    $document = null;

    if ($method === 'GET') {
        $page = (new PageRepository())->get(Uuid::require($_GET['id'] ?? null));
        $document = $page['document'];
    } else {
        Auth::csrf(isset($_SERVER['HTTP_X_CSRF_TOKEN']) ? (string) $_SERVER['HTTP_X_CSRF_TOKEN'] : null);
        $body = Http::body();
        Http::only($body, ['document']);
        $document = Http::document($body);
    }
    if (!is_array($document)) {
        throw new Problem(422, 'This page has no draft document.');
    }
    (new Validator())->assert($document);

    $media = (new MediaRepository())->resolverFor($document);
    header('Content-Type: text/html; charset=utf-8');
    header('Cache-Control: no-store');
    header('Content-Security-Policy: ' . Renderer::policy());
    $base = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))), '/');
    echo (new Renderer($media))->document(
        $document,
        $base . '/asset_site/css/cms-pages.css',
        $base . '/asset_site/js/cms-runtime.js'
    );
} catch (Throwable $error) {
    if ($error instanceof Problem) {
        http_response_code($error->status);
        header('Content-Type: text/plain; charset=utf-8');
        echo $error->getMessage();
        foreach ($error->errors as $field => $message) {
            echo "\n", $field, ': ', $message;
        }
        exit;
    }
    \ExpertsAcademy\Studio\Db::log('preview failed: ' . $error->getMessage());
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'The preview could not be rendered.';
}
