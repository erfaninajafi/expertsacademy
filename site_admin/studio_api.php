<?php
declare(strict_types=1);

/** JSON API for the page editor, Design System Manager and Component Studio. */

require dirname(__DIR__) . '/include_site/studio/bootstrap.php';

use ExpertsAcademy\Studio\Auth\Auth;
use ExpertsAcademy\Studio\Contract\Samples;
use ExpertsAcademy\Studio\Contract\Validator;
use ExpertsAcademy\Studio\Auth\Roles;
use ExpertsAcademy\Studio\Env;
use ExpertsAcademy\Studio\Http\Http;
use ExpertsAcademy\Studio\Http\RateLimiter;
use ExpertsAcademy\Studio\Publish\PublishScheduler;
use ExpertsAcademy\Studio\Quality\QualityChecker;
use ExpertsAcademy\Studio\Repo\PreviewTokenRepository;
use ExpertsAcademy\Studio\Repo\RedirectRepository;
use ExpertsAcademy\Studio\Repo\RoleRepository;
use ExpertsAcademy\Studio\Problem;
use ExpertsAcademy\Studio\Registry\ComponentRegistry;
use ExpertsAcademy\Studio\Repo\AuditRepository;
use ExpertsAcademy\Studio\Repo\ComponentStudioRepository;
use ExpertsAcademy\Studio\Repo\DesignSystemRepository;
use ExpertsAcademy\Studio\Repo\MediaRepository;
use ExpertsAcademy\Studio\Repo\PageRepository;
use ExpertsAcademy\Studio\Repo\TemplateRepository;
use ExpertsAcademy\Studio\Repo\TokenRepository;
use ExpertsAcademy\Studio\Uuid;

Http::headers();

try {
    $actor = Auth::actor();
    $method = Http::requireMethod('GET', 'POST', 'PUT');
    $resource = isset($_GET['resource']) && is_string($_GET['resource']) ? $_GET['resource'] : 'pages';

    $pages = new PageRepository();
    $tokens = new TokenRepository();
    $media = new MediaRepository();
    $audit = new AuditRepository();
    $design = new DesignSystemRepository();
    $studio = new ComponentStudioRepository();
    $templates = new TemplateRepository();
    $roles = new RoleRepository();
    $redirects = new RedirectRepository();
    $scheduler = new PublishScheduler();
    $quality = new QualityChecker();
    $validator = new Validator();
    $locale = in_array($_GET['locale'] ?? null, ['fa', 'en'], true) ? (string) $_GET['locale'] : 'en';

    if ($method === 'GET') {
        $response = match ($resource) {
            'session' => [
                'actor' => $actor,
                'csrf' => Auth::token(),
            ],
            'pages' => isset($_GET['id'])
                ? ['page' => $pages->get(Uuid::require($_GET['id']))]
                : $pages->listing((int) ($_GET['limit'] ?? 30), (int) ($_GET['offset'] ?? 0)),
            'revision' => ['revision' => $pages->revision(
                Uuid::require($_GET['page'] ?? null),
                Uuid::require($_GET['id'] ?? null)
            )],
            'registry' => [
                'components' => ComponentRegistry::catalog(),
                'categories' => ComponentRegistry::categories(),
                'schemaVersion' => ComponentRegistry::SCHEMA_VERSION,
                'fingerprint' => ComponentRegistry::fingerprint(),
            ],
            'themes' => ['items' => $tokens->themes()],
            'tokens' => ['items' => $tokens->versions()],
            'media' => ['items' => $media->listing()],
            'templates' => ['items' => $templates->listing((int) $actor['id'])],
            'template' => ['template' => $templates->get(
                Uuid::require($_GET['id'] ?? null),
                (int) $actor['id'],
                isset($_GET['version']) ? (int) $_GET['version'] : null
            )],
            'audit' => ['items' => $audit->forPage(Uuid::require($_GET['id'] ?? null))],
            'sample' => ['document' => Samples::page($locale)],
            'permissions' => [
                'matrix' => Roles::matrix(),
                'permissions' => Roles::PERMISSIONS,
                'roles' => Roles::ALL,
                'users' => Auth::can($actor, 'users.manage') ? $roles->users() : [],
            ],
            'redirects' => Auth::can($actor, 'redirects.manage')
                ? ['items' => $redirects->all()]
                : throw new Problem(403, 'Your role cannot manage redirects.'),
            'schedules' => Auth::can($actor, 'audit.read')
                ? ['items' => $scheduler->recent()]
                : throw new Problem(403, 'Your role cannot view the publishing queue.'),
            'quality' => (function () use ($pages, $quality): array {
                $page = $pages->get(Uuid::require($_GET['id'] ?? null));
                $checks = $quality->check($page, $page['document'] ?? []);
                return ['checks' => $checks];
            })(),
            'design' => [
                'drafts' => $design->drafts($locale),
                'working' => $design->workingDraft($locale, (string) $actor['id']),
                'history' => $design->history($locale),
                'current' => $tokens->defaultFor($locale),
                'locales' => ['en', 'fa'],
            ],
            'design_draft' => ['draft' => $design->draft(Uuid::require($_GET['id'] ?? null))],
            'components' => [
                'components' => $studio->catalogue(),
                'categories' => ComponentRegistry::categories(),
                'presets' => $studio->presets(),
            ],
            'component' => (function () use ($studio): array {
                $type = is_string($_GET['type'] ?? null) ? (string) $_GET['type'] : '';
                return [
                    'contract' => $studio->contract($type),
                    'presets' => $studio->presets($type),
                    'versions' => $studio->versions($type),
                ];
            })(),
            'versions' => ['items' => $studio->versions()],
            'presets' => ['items' => $studio->presets(isset($_GET['type']) ? (string) $_GET['type'] : null)],
            default => throw new Problem(404, 'Unknown resource.'),
        };
        Http::json(['ok' => true] + $response);
    }

    // All mutations require a CSRF token and a same-origin request.
    Auth::csrf(isset($_SERVER['HTTP_X_CSRF_TOKEN']) && is_string($_SERVER['HTTP_X_CSRF_TOKEN'])
        ? $_SERVER['HTTP_X_CSRF_TOKEN'] : null);

    if ($resource === 'media' && $method === 'POST') {
        Auth::requirePermission($actor, 'media.upload');
        RateLimiter::guard('media.upload', (string) $actor['id'], 30, 60);
        $file = $_FILES['file'] ?? null;
        if (!is_array($file)) {
            throw new Problem(400, 'A file field is required.');
        }
        $alt = isset($_POST['alt']) && is_string($_POST['alt']) ? $_POST['alt'] : '';
        Http::json(['ok' => true, 'media' => $media->register($file, $alt, $actor['id'])], 201);
    }

    $body = Http::body();

    if ($resource === 'validate' && $method === 'POST') {
        Http::only($body, ['document']);
        $errors = $validator->errors(Http::document($body));
        Http::json(['ok' => true, 'valid' => $errors === [], 'errors' => (object) $errors]);
    }

    if ($resource === 'media' && $method === 'PUT') {
        Auth::requirePermission($actor, 'media.manage');
        Http::only($body, ['alt', 'focalX', 'focalY']);
        Http::json(['ok' => true, 'media' => $media->updateMeta(
            Uuid::require($_GET['id'] ?? null),
            is_string($body['alt'] ?? null) ? $body['alt'] : '',
            isset($body['focalX']) ? (int) $body['focalX'] : 50,
            isset($body['focalY']) ? (int) $body['focalY'] : 50,
            (string) $actor['id']
        )]);
    }

    if ($resource === 'pages' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.create');
        Http::only($body, ['title', 'locale', 'legacyCode', 'template', 'templateVersion', 'fullPath']);
        $title = is_string($body['title'] ?? null) ? trim($body['title']) : '';
        $pageLocale = $body['locale'] ?? null;
        if ($title === '' || mb_strlen($title) > 200 || !in_array($pageLocale, ['fa', 'en'], true)) {
            throw new Problem(400, 'A title and a locale (fa or en) are required.');
        }
        $binding = $body['legacyCode'] ?? null;
        if ($binding !== null && (!is_string($binding) || trim($binding) === '')) {
            $binding = null;
        }
        $fullPath = $body['fullPath'] ?? null;
        if ($fullPath !== null && !is_string($fullPath)) {
            throw Problem::invalid([
                'fullPath' => 'Full path must be a string.'
            ]);
        }
        $fullPath = is_string($fullPath) && trim($fullPath) !== ''
            ? trim($fullPath)
            : null;

        $templateId = is_string($body['template'] ?? null) ? trim($body['template']) : '';
        if ($templateId !== '') {
            $template = $templates->get(
                Uuid::require($templateId),
                (int) $actor['id'],
                isset($body['templateVersion']) ? (int) $body['templateVersion'] : null
            );
            $document = $template['document'];
            $document['title'] = $title;
            $document['locale'] = $pageLocale;
            $document['direction'] = $pageLocale === 'fa' ? 'rtl' : 'ltr';
            $document['seo']['title'] = $title;
        } else {
            $document = Samples::page($pageLocale, $title);
        }
        Http::json(['ok' => true, 'page' => $pages->create(
            $document,
            $actor['id'],
            $binding !== null ? trim($binding) : null,
            $templateId !== '' ? $templateId : null,
            $fullPath
        )], 201);
    }

    if ($resource === 'pages' && $method === 'PUT') {
        Auth::requirePermission($actor, 'pages.edit');
        Http::only($body, ['document', 'expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->save(
            Uuid::require($_GET['id'] ?? null),
            Http::document($body),
            Http::version($body),
            $actor['id']
        )]);
    }

    if ($resource === 'template-favorite' && $method === 'POST') {
        Auth::require($actor, 'read');
        Http::json(['ok' => true] + $templates->toggleFavorite(
            Uuid::require($_GET['id'] ?? null),
            (int) $actor['id']
        ));
    }

    if ($resource === 'restore' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.restore');
        Http::only($body, ['revisionId', 'expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->restore(
            Uuid::require($_GET['id'] ?? null),
            Uuid::require($body['revisionId'] ?? null),
            Http::version($body),
            $actor['id']
        )]);
    }

    if (($resource === 'publish' || $resource === 'rollback') && $method === 'POST') {
        Auth::requirePermission($actor, $resource === 'rollback' ? 'pages.restore' : 'pages.publish');
        RateLimiter::guard('pages.publish', (string) $actor['id'], 20, 60);
        Http::only($body, ['expectedVersion', 'revisionId']);
        $pageId = Uuid::require($_GET['id'] ?? null);
        $page = $pages->get($pageId);
        $revisionId = $resource === 'rollback'
            ? Uuid::require($body['revisionId'] ?? null)
            : null;
        $document = $revisionId !== null
            ? $pages->revision($pageId, $revisionId)['document']
            : (array) ($page['document'] ?? []);
        $checks = $quality->check($page, $document);
        if (!$checks['ok']) {
            Http::json(['ok' => false, 'error' => 'Quality checks blocked publication.', 'checks' => $checks], 422);
        }
        Http::json(['ok' => true, 'checks' => $checks, 'page' => $pages->publish(
            $pageId,
            Http::version($body),
            $actor['id'],
            $revisionId
        )]);
    }

    if ($resource === 'schedule' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.schedule');
        RateLimiter::guard('pages.schedule', (string) $actor['id'], 20, 60);
        Http::only($body, ['expectedVersion', 'publishAt', 'timezone', 'revisionId']);
        $pageId = Uuid::require($_GET['id'] ?? null);
        $page = $pages->get($pageId);
        $checks = $quality->check($page, (array) ($page['document'] ?? []));
        if (!$checks['ok']) {
            Http::json(['ok' => false, 'error' => 'Quality checks blocked scheduling.', 'checks' => $checks], 422);
        }
        $publishAt = is_string($body['publishAt'] ?? null) ? trim($body['publishAt']) : '';
        $timezone = is_string($body['timezone'] ?? null) ? trim($body['timezone']) : Env::string('publishing.timezone', 'UTC');
        if ($publishAt === '') {
            throw Problem::invalid(['publishAt' => 'Choose a date and time.']);
        }
        Http::json(['ok' => true, 'checks' => $checks, 'page' => $pages->schedule(
            $pageId,
            $publishAt,
            $timezone,
            Http::version($body),
            $actor['id'],
            is_string($body['revisionId'] ?? null) ? Uuid::require($body['revisionId']) : null
        )]);
    }

    if ($resource === 'schedule_cancel' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.schedule');
        Http::only($body, ['expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->cancelSchedule(
            Uuid::require($_GET['id'] ?? null),
            Http::version($body),
            $actor['id']
        )]);
    }

    if ($resource === 'schedule_retry' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.schedule');
        Http::only($body, ['scheduleId']);
        $scheduler->retry(Uuid::require($body['scheduleId'] ?? null), (string) $actor['id']);
        Http::json(['ok' => true]);
    }

    if ($resource === 'unpublish' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.unpublish');
        Http::only($body, ['expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->unpublish(
            Uuid::require($_GET['id'] ?? null),
            Http::version($body),
            $actor['id']
        )]);
    }

    if ($resource === 'archive' && $method === 'POST') {
        Auth::requirePermission($actor, 'pages.archive');
        Http::only($body, ['expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->archive(
            Uuid::require($_GET['id'] ?? null),
            Http::version($body),
            $actor['id']
        )]);
    }

    if ($resource === 'public_path' && $method === 'PUT') {
        Auth::requirePermission($actor, 'pages.route');
        Http::only($body, ['fullPath', 'expectedVersion']);

        if (!array_key_exists('fullPath', $body)) {
            throw Problem::invalid([
                'fullPath' => 'Full path field is required.'
            ]);
        }

        if (
            $body['fullPath'] !== null &&
            !is_string($body['fullPath'])
        ) {
            throw Problem::invalid([
                'fullPath' => 'Full path must be a string or null.'
            ]);
        }

        Http::json([
            'ok' => true,
            'page' => $pages->updatePublicPath(
                Uuid::require($_GET['id'] ?? null),
                $body['fullPath'],
                Http::version($body),
                (string) $actor['id']
            )
        ]);
    }

    if ($resource === 'route' && $method === 'PUT') {
        Auth::requirePermission($actor, 'pages.route');
        Http::only($body, ['slug', 'legacyCode', 'expectedVersion']);
        Http::json(['ok' => true, 'page' => $pages->updateRoute(
            Uuid::require($_GET['id'] ?? null),
            is_string($body['slug'] ?? null) ? $body['slug'] : null,
            is_string($body['legacyCode'] ?? null) ? $body['legacyCode'] : null,
            Http::version($body),
            $actor['id']
        )]);
    }

    if ($resource === 'preview-token' && $method === 'POST') {
        Auth::requirePermission($actor, 'preview.draft');
        $preview = (new PreviewTokenRepository())->issue(
            Uuid::require($_GET['id'] ?? null),
            (string) $actor['id'],
            isset($body['ttl']) ? (int) $body['ttl'] : 3600
        );
        // Relative to site_admin/ so the editor resolves the public root correctly
        // regardless of the configured absolute base URL (localhost vs production).
        $preview['url'] = '../preview_link.php?token=' . $preview['token'];
        Http::json(['ok' => true] + $preview, 201);
    }

    if ($resource === 'role' && $method === 'POST') {
        Auth::requirePermission($actor, 'users.manage');
        RateLimiter::guard('roles.assign', (string) $actor['id'], 30, 60);
        Http::only($body, ['userId', 'role']);
        $roles->assign(
            is_string($body['userId'] ?? null) ? $body['userId'] : '',
            is_string($body['role'] ?? null) ? $body['role'] : '',
            (string) $actor['id']
        );
        Http::json(['ok' => true, 'users' => $roles->users()]);
    }

    if ($resource === 'redirect' && $method === 'POST') {
        Auth::requirePermission($actor, 'redirects.manage');
        Http::only($body, ['fromPath', 'toPath', 'locale', 'statusCode']);
        Http::json(['ok' => true, 'redirect' => $redirects->create(
            is_string($body['fromPath'] ?? null) ? $body['fromPath'] : '',
            is_string($body['toPath'] ?? null) ? $body['toPath'] : '',
            is_string($body['locale'] ?? null) ? $body['locale'] : $locale,
            isset($body['statusCode']) ? (int) $body['statusCode'] : 301,
            (string) $actor['id']
        )], 201);
    }

    if ($resource === 'redirect_delete' && $method === 'POST') {
        Auth::requirePermission($actor, 'redirects.manage');
        Http::only($body, ['id']);
        $redirects->delete(Uuid::require($body['id'] ?? null), (string) $actor['id']);
        Http::json(['ok' => true]);
    }

    if ($resource === 'themes' && $method === 'POST') {
        Auth::requirePermission($actor, 'design.edit');
        Http::only($body, ['name', 'locale', 'tokens']);
        $name = is_string($body['name'] ?? null) ? trim($body['name']) : '';
        $themeLocale = $body['locale'] ?? null;
        $document = $body['tokens'] ?? null;
        if ($name === '' || mb_strlen($name) > 200 || !in_array($themeLocale, ['fa', 'en'], true) || !is_array($document)) {
            throw new Problem(400, 'A theme name, locale and token document are required.');
        }
        Http::json(['ok' => true, 'theme' => $tokens->createTheme($name, $themeLocale, $document, $actor['id'])], 201);
    }

    if ($resource === 'design' && $method === 'POST') {
        Auth::requirePermission($actor, 'design.edit');
        Http::only($body, ['name', 'locale', 'tokens', 'baseThemeId']);
        $name = is_string($body['name'] ?? null) ? trim($body['name']) : '';
        $designLocale = $body['locale'] ?? null;
        $document = $body['tokens'] ?? null;
        if ($name === '' || mb_strlen($name) > 120 || !in_array($designLocale, ['fa', 'en'], true) || !is_array($document)) {
            throw new Problem(400, 'A name, locale and token document are required.');
        }
        Http::json(['ok' => true, 'draft' => $design->create($name, $designLocale, $document, (string) $actor['id'],
            is_string($body['baseThemeId'] ?? null) ? $body['baseThemeId'] : null)], 201);
    }

    if ($resource === 'design' && $method === 'PUT') {
        Auth::requirePermission($actor, 'design.edit');
        Http::only($body, ['tokens', 'expectedVersion']);
        $tokensBody = $body['tokens'] ?? null;
        if (!is_array($tokensBody)) {
            throw new Problem(400, 'A token document is required.');
        }
        Http::json(['ok' => true, 'draft' => $design->save(
            Uuid::require($_GET['id'] ?? null),
            $tokensBody,
            (string) $actor['id'],
            Http::version($body)
        )]);
    }

    if ($resource === 'design_publish' && $method === 'POST') {
        Auth::requirePermission($actor, 'design.edit');
        Http::only($body, ['expectedVersion']);
        Http::json(['ok' => true, 'publication' => $design->publish(
            Uuid::require($_GET['id'] ?? null),
            (string) $actor['id'],
            Http::version($body)
        )]);
    }

    if ($resource === 'component_meta' && $method === 'PUT') {
        Auth::requirePermission($actor, 'components.manage');
        Http::only($body, ['type', 'status', 'docs', 'a11y', 'tags', 'animation']);
        $type = is_string($body['type'] ?? null) ? $body['type'] : '';
        $meta = $studio->saveMeta($type, $body, (string) $actor['id']);
        $audit->record((string) $actor['id'], 'component.meta', 'component', $type, null, ['status' => $meta['meta']['status']]);
        Http::json(['ok' => true, 'component' => $meta]);
    }

    if ($resource === 'preset' && $method === 'POST') {
        Auth::requirePermission($actor, 'components.manage');
        Http::only($body, ['type', 'name', 'variant', 'props']);
        $props = $body['props'] ?? null;
        if (!is_array($props)) {
            throw new Problem(400, 'Preset props must be an object.');
        }
        Http::json(['ok' => true, 'preset' => $studio->createPreset(
            is_string($body['type'] ?? null) ? $body['type'] : '',
            is_string($body['name'] ?? null) ? $body['name'] : '',
            is_string($body['variant'] ?? null) ? $body['variant'] : '',
            $props,
            (string) $actor['id']
        )], 201);
    }

    if ($resource === 'preset_delete' && $method === 'POST') {
        Auth::requirePermission($actor, 'components.manage');
        Http::only($body, ['id']);
        $studio->deletePreset(is_string($body['id'] ?? null) ? $body['id'] : '');
        Http::json(['ok' => true]);
    }

    throw new Problem(405, 'Unsupported resource and method combination.');
} catch (Throwable $error) {
    Http::error($error);
}
