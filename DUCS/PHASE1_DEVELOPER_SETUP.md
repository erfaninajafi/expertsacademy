# Page Studio — setup, extension and API

Read [architecture](PHASE1_CMS_FOUNDATION_2026-11-01.md) and [backup/rollback](PHASE1_BACKUP_ROLLBACK.md) first. Paths below are relative to `/opt/lampp/htdocs/expertsacademy` unless stated otherwise. Workspace commands use the required `rtk` prefix; it is a command wrapper, not an application dependency.

## Install and open

Requirements: PHP 8.2+ with PDO/MySQL and JSON; Composer 2; Node 24+ and npm; Apache with rewrite/authorization overrides; verified development database. PHPUnit also needs DOM/XML/mbstring. Do not change the configured database to the protected original.

From `include_site/studio/`:

```bash
rtk proxy composer install --no-interaction
```

Composer is not globally installed on this workstation. The verified private PHAR is at `/home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/phase1_20261101/tools/composer.phar`; run it with `/opt/lampp/bin/php` in place of `composer`. No Composer plugins are allowed. Use `install` with the lockfile; do not run an unreviewed dependency update.

From `site_admin/studio/`:

```bash
rtk proxy npm ci
rtk proxy npm run build
rtk proxy npm test
```

From the application root:

```bash
rtk proxy /opt/lampp/bin/php include_site/studio/bin/studio.php migrate
rtk proxy /opt/lampp/bin/php include_site/studio/bin/studio.php validate include_site/studio/examples/page.v1.json
```

The migration is already installed locally. Rerunning reports `applied:false`; it never converts legacy pages. `sample` creates another unbound unpublished sample each time, so use it deliberately. `render-sample` writes standalone rendered HTML to stdout; save it outside the web root when inspecting it.

Sign into the existing CMS and open [Page Studio](http://127.0.0.1/expertsacademy/site_admin/page_studio.php). Open **Phase 1 foundation sample**. The existing categories, old Builder and legacy content remain accessible through their existing links.

Vite is a build tool for the CMS mount, not a separate website. Compiled assets and `.vite/manifest.json` are written only to `site_admin/asset/page-studio/`. Output cleanup is disabled to avoid deleting earlier assets. `page_studio.php` reads the manifest server-side. For optional local HMR, run `npm run dev` and configure the **PHP process's** `EA_STUDIO_VITE_URL=http://127.0.0.1:5173`; only loopback port 5173 is accepted. An export in an unrelated shell does not configure Apache. The normal built editor needs no running Node service.

## Authoring and extending

1. Create an unbound draft, apply component-property changes, validate, preview and save. Applying properties changes the browser draft; saving creates a revision. The inspector is structured JSON, not an executable code editor.
2. The optional existing category code binds one exact locale at creation. Binding alone does not alter the public site. Confirm publication only when replacing that existing page body is intended.
3. To add a new type/version, add a local schema, an explicit `Registry` definition and an escaped renderer branch; update TypeScript types/defaults/catalog and meaningful tests. Add a new database migration for metadata. Preserve support for previously published versions.
4. Do not import legacy templates, old Builder functions or request-supplied include paths. Keep user values out of SQL identifiers and CSS/HTML construction unless explicitly validated and escaped.

## API version 1

All requests use same-origin CMS session cookies. Responses carry `apiVersion:1`, `X-Studio-API-Version:1`, `Cache-Control:no-store` and `X-Content-Type-Options:nosniff`. GET a session to obtain `csrf`; send it as `X-CSRF-Token` on every POST/PUT, including validation and preview. Bodies require `Content-Type: application/json` and are limited to 1 MiB. Browser console examples assume an already signed-in CMS session; do not embed real tokens into source files.

Base: `site_admin/studio_api.php`.

| Method / resource | Input | Result / permission |
| --- | --- | --- |
| GET `?resource=session` | None | Actor capabilities and CSRF token; read. |
| GET `?resource=registry` | None | Supported component versions and complete property schemas; read. |
| GET `?resource=pages&limit=30&offset=0` | Optional bounded pagination; max 100 | Summaries, total, limit, offset; read. |
| GET `?resource=pages&id=UUID` | Page ID | Draft document, lock version, pointers and latest 50 revision metadata entries; read. |
| POST `?resource=pages` | `{title,locale,legacyCode?}` or `{document,legacyCode?}` | 201 with new draft/revision; edit. No publication. |
| PUT `?resource=pages&id=UUID` | `{document,expectedVersion}` | New immutable draft revision and incremented lock; edit. |
| POST `?resource=validate` | `{document}` | `{ok:true,valid,errors}`; validation failures remain 200 with `valid:false`; read. |
| POST `?resource=publish&id=UUID` | `{expectedVersion}` | Moves published pointer to saved draft and increments lock; publish. |
| GET `?resource=templates` | None | Up to 100 versioned templates; read. |
| GET `?resource=tokens` | None | Up to 100 versioned token sets; read. |
| GET `?resource=media` | None | Up to 100 media metadata records, initially empty; read. |

Page response fields: `id`, `version`, `legacyCode`, `draftRevision`, `publishedRevision`, `document`, `revisions`. The ETag on a single GET is informative; **`expectedVersion` is the concurrency precondition**, not `If-Match`. Reload/resolve a 409 rather than silently overwriting. `version` tracks save/publish operations; revision ordinals track document snapshots.

Errors: `{apiVersion:1,ok:false,error,details?}`; unexpected errors add a safe `requestId`. Statuses: 400 malformed JSON/envelope/ID, 401 missing/revoked login, 403 role/CSRF, 404 missing resource/page, 405 unsupported method, 409 collision/stale lock, 413 size limit, 415 content type, 422 invalid document/binding, 503 missing schema/write target, 500 internal failure. Schema errors are indexed by JSON paths. APIs expose no delete, bulk conversion, arbitrary SQL, file upload or executable template action.

Preview: `site_admin/studio_preview.php?id=UUID` (saved draft GET) or POST `{document}` with CSRF (unsaved draft). It returns validated HTML using the exact public renderer, with no-store/CSP. POST validation failure returns the JSON error envelope. Preview never publishes or saves.

## Automated tests in isolation

**Do not point tests at development/original data.** The suite intentionally drops/recreates only new Studio tables in schemas named `expertsacademy_studio_test_*`. It inserts synthetic role accounts into the isolated copy. Integration requires a restored private database backup because it also verifies original table preservation and real category bindings.

The verified run used `/tmp/ea-studio-mysql8/mysql.sock`, schema `expertsacademy_studio_test_mysql8`, and a second isolated XAMPP schema `expertsacademy_studio_test_backup` through `/tmp/ea-studio-mariadb.sock`. Temporary processes are stopped after verification; recreate them before rerunning tests. No network listener is necessary for the test MySQL instance.

Example fresh MySQL 8 setup, with no existing directory/database at these paths (run in separate terminals as appropriate):

```bash
rtk proxy install -d -m 700 /tmp/ea-studio-mysql8/data
rtk proxy /usr/sbin/mysqld --no-defaults --initialize-insecure --datadir=/tmp/ea-studio-mysql8/data
rtk proxy /usr/sbin/mysqld --no-defaults --datadir=/tmp/ea-studio-mysql8/data --socket=/tmp/ea-studio-mysql8/mysql.sock --pid-file=/tmp/ea-studio-mysql8/mysql.pid --log-error=/tmp/ea-studio-mysql8/mysql.log --skip-networking --mysqlx=0
rtk proxy /usr/bin/mysql --socket=/tmp/ea-studio-mysql8/mysql.sock -u root -e 'CREATE DATABASE expertsacademy_studio_test_mysql8 CHARACTER SET utf8mb4 COLLATE utf8mb4_bin'
```

Import the private `database.sql` from the backup directory into that **new isolated schema** with the socket-only MySQL client; keep stdin/dump paths outside the web root. `initialize-insecure` is appropriate only for this private local socket fixture with directory mode 0700; `Connection` test mode assumes its empty-password root. A restored backup contains real records, so keep its directory private and never expose this fixture server to the network.

Run from the application root after the import:

```bash
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_mysql8 EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mysql8/mysql.sock /opt/lampp/bin/php include_site/studio/vendor/bin/phpunit --configuration include_site/studio/phpunit.xml
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_mysql8 EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mysql8/mysql.sock /opt/lampp/bin/php include_site/studio/tests/create-sessions.php
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_mysql8 EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mysql8/mysql.sock /opt/lampp/bin/php -d session.save_path=/tmp/ea-studio-sessions -S 127.0.0.1:8097 -t /opt/lampp/htdocs /opt/lampp/htdocs/expertsacademy/include_site/studio/tests/http-router.php
```

The last command stays running in a separate terminal. Sessions/storage state are generated privately under `/tmp/ea-studio-sessions`; never copy them into the application. The router is test-only and protects source trees normally denied by Apache. Browser tests block external requests. Existing common PHP CMS helpers still read their configured development DB; Studio uses the isolated override, so browser publication changes only the isolated Studio records.

From `site_admin/studio/`, with the test server running:

```bash
rtk proxy env EA_STUDIO_BROWSER_PATH=/opt/google/chrome/chrome npm run test:e2e
```

Use an installed compatible browser path, or install Playwright's browser through its normal installer when downloads are available. No download restriction was bypassed in this run. Stop the PHP server with Ctrl-C, then stop only the private test MySQL with `mysqladmin --socket=/tmp/ea-studio-mysql8/mysql.sock -u root shutdown`. Leave the user's XAMPP services alone. Do not run PHPUnit's table-reset test while browser tests are running.

## Operational checks

Verify Apache refuses `include_site/studio/composer.json`, `site_admin/studio/package.json`, `.git/config` and `site.sql`, while the built JS/CSS and authorized CMS entry load. On a different web server, reproduce these deny rules before serving the tree; `.htaccess` is Apache-specific. Real HTTPS deployments should configure secure session transport, least-privilege DB credentials, off-web-root backup retention and centralized error logs under their normal deployment process. No remote deployment was performed here.

Official dependency references: [Vite](https://vite.dev/guide/), [Opis JSON Schema](https://opis.io/json-schema/2.x/), [Tailwind Vite integration](https://tailwindcss.com/docs/installation/using-vite), [PHPUnit version requirements](https://phpunit.de/supported-versions.html). Lockfiles, runtime commands and the recorded test results establish the versions actually used.
