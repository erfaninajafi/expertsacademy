# Phase 2 developer setup and extension guide

Run from `/opt/lampp/htdocs/expertsacademy`. This is part of the existing PHP CMS. Do not start another website or point tests at the owner’s development content. RTK prefixes follow this workspace’s instructions.

## Dependencies and build

Verified runtime: XAMPP PHP 8.2.12, MariaDB 10.4.32, Node 24.22.1, npm 11.19.1. MySQL 8.4.11 is also integration-tested. Composer resolves Opis JSON Schema 2.6.0 and PHPUnit 11.5.57. The editor lockfile resolves React 19.3.1, TypeScript 7.0.2, Vite 8.3.5, Zustand 5.0.16, dnd-kit core 6.3.1/sortable 10.0.0, Radix Dialog 1.3.1, Tailwind 4.3.3, Motion 14.9.0, GSAP 3.15.0, Vitest 5.0.5, Playwright 1.64.1 and axe 4.14.0.

```bash
cd /opt/lampp/htdocs/expertsacademy/include_site/studio
rtk proxy /opt/lampp/bin/php /path/to/composer.phar install
cd /opt/lampp/htdocs/expertsacademy/site_admin/studio
rtk proxy npm ci
rtk proxy npm run build
rtk proxy npm test
```

Vite builds both editor `src/main.tsx` and progressive runtime `src/design/runtime.ts` into `site_admin/asset/page-studio/`. PHP reads the generated manifest. Deploy the manifest and all referenced chunks together; do not remove older chunks while cached pages may reference them. Editor fonts are bundled with portable relative URLs; the PHP design stylesheet uses relative font paths too. Both subfolder and root installation layouts are browser-tested.

After a verified private backup, `rtk proxy /opt/lampp/bin/php include_site/studio/bin/studio.php migrate` applies additive migrations to the configured development DB. It refuses the protected original schema. Production promotion and destructive restores require separate review; no credentials belong in this documentation or in the public root.

## CMS usage

Sign in using the existing CMS login. Role status `0` can read/edit/publish and manage themes/lifecycle/reviews/inbox; status `2` can read/edit and submit briefs; status `1` is read-only. Other or inactive accounts are rejected. Client controls do not replace server authorization.

1. Open Design System, choose a preset or change typed controls, review light/dark contrast and PHP preview, then save a new version.
2. Open Component Studio, choose a category/type/version/variant and edit content or use a preset. Raw JSON must satisfy the displayed property schema. Use desktop/tablet/mobile, Persian/English and light/dark preview controls.
3. Open Page Studio, create a v2 page, insert reviewed components and optionally apply a saved theme version. Save a draft. Publication remains an explicit publisher action.
4. Use the demonstration preview, or `rtk proxy /opt/lampp/bin/php include_site/studio/bin/studio.php design-sample` to create a separate unbound draft. The command creates a new page each invocation, so use the recorded deployment ID to reopen the delivered sample.

## API additions

All paths below are `site_admin/studio_api.php?resource=...`. GET requires CMS authentication/read access. Every POST/PUT requires the session’s `X-CSRF-Token`; obtain it from GET `session`. JSON envelopes reject unexpected properties. Preserve numeric `expectedVersion` and handle 409 by reloading, not overwriting.

| Resource / method | Payload or response | Capability |
| --- | --- | --- |
| `design-catalog` GET | Categories, exact versioned definitions, default token snapshot, quota counts. | read |
| `design-quality` GET | Source fingerprint and measured quality report, pending/stale when appropriate. | read |
| `tokens` GET | Immutable stored theme versions. | read |
| `tokens` POST | `{id, expectedVersion, name, document}`; new UUID starts at version 0. | publish |
| `component-proposals` GET / POST | Submit `{type,label,category,description,fields,variants}`; bounded text/kind metadata only. | read / edit |
| `component-review` POST | `{id,decision,reference}`; explicit review ref; approval requires an implemented code-owned type. | publish |
| `component-lifecycle` POST | `{type,version,status,replacement,note}`; stable/deprecated, optional known replacement. | publish |
| `migrate-component` POST | `{component}`; validated dry-run replacement + review notes. | edit |
| `leads` GET | Latest 100 records; never public. | publish |
| `pages` POST | Existing contract plus optional `schemaVersion:2`; supplied full documents remain supported. | edit |

Existing page GET/PUT/publish/validate, media and template resources retain Phase 1 semantics. `studio_component_preview.php` accepts authenticated GET or JSON POST with type/variant/version, locale, mode, optional props/tokens, or `demo=curated|all`. It renders through the same validator and PHP renderer. It never submits preview forms.

Public `studio_lead.php` has a separate GET token contract and POST submission contract. Allowed intent is code-owned; native HTML fallback obtains the same public CSRF. Responses enforce consent/lengths/email/honeypot and three requests per ten minutes per session. Test only against an isolated schema; this endpoint does not send messages.

## Registering reviewed components

1. Submit a content/variant/property brief in Component Studio. This stores data only; it cannot create PHP, JS, HTML templates, executable schemas or CSS.
2. Implement a new exact type/version in reviewed application code: catalog entry, `ComponentCatalog` schema/defaults, `PremiumRenderer` implementation, scoped CSS, and reviewed runtime behavior when necessary. Never load callable paths or code from database rows.
3. Provide four meaningful compositions when extending this library, Academy and concise presets, translated defaults, semantic HTML, keyboard/reduced-motion behavior and local optimized media metadata.
4. Add validation/render tests and browser matrix cases. The matrix enumerates the registry automatically; update quota assertions deliberately if adding new types. Review desktop/mobile/RTL/dark screenshots and accessible names/reading order.
5. Add a **new** SQL/seed migration for deployed schema metadata. Do not edit migration 001 or 002 inputs after deployment. Deploy code and assets, then migrate and approve the registration brief with its code review reference.

To change an existing contract, retain its historical renderer/schema and register a new version. `hero-editorial` v1→v2 demonstrates this: v2 adds an annotation, the reviewed transformation preserves existing properties and IDs, and the editor previews the change before saving. Deprecation is metadata; it does not delete instances. Legacy hero/text/grid transformations are also available as explicit dry runs with review notes.

## Isolated tests

Create a disposable database whose name starts `expertsacademy_studio_test_`, using a private restored snapshot for routes/users needed by integration tests. `Connection` permits test overrides only for PHP CLI/CLI server with a `/tmp/` socket. Never use `expertsacademy_dev` with integration tests: their fixtures reset Studio tables.

```bash
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_phase2_backup EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mariadb.sock /opt/lampp/bin/php include_site/studio/vendor/bin/phpunit --configuration include_site/studio/phpunit.xml
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_phase2_backup EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mariadb.sock /opt/lampp/bin/php include_site/studio/tests/create-sessions.php
rtk proxy env EA_STUDIO_ENV=test EA_STUDIO_TEST_DATABASE=expertsacademy_studio_test_phase2_backup EA_STUDIO_TEST_SOCKET=/tmp/ea-studio-mariadb.sock /opt/lampp/bin/php -d session.save_path=/tmp/ea-studio-sessions -S 127.0.0.1:8097 -t /opt/lampp/htdocs include_site/studio/tests/http-router.php
```

Run the browser commands in `site_admin/studio` in another terminal. Generated role session cookies stay under private `/tmp/ea-studio-sessions`, never commit or publish them. Installed Chrome is used because the browser CDN was unavailable locally.

```bash
rtk proxy env EA_STUDIO_BROWSER_PATH=/opt/google/chrome/chrome npm run test:e2e -- e2e/foundation.spec.ts e2e/design-studio.spec.ts e2e/design-interactions.spec.ts
rtk proxy env EA_STUDIO_BROWSER_PATH=/opt/google/chrome/chrome EA_STUDIO_VISUAL=1 npm run test:e2e -- e2e/component-matrix.spec.ts --update-snapshots
rtk proxy env EA_STUDIO_BROWSER_PATH=/opt/google/chrome/chrome EA_STUDIO_VISUAL=1 npm run test:e2e -- e2e/component-matrix.spec.ts
```

Reset the isolated default Academy theme to factory values after functional UI tests and before baseline capture. Do not run DB-resetting integration tests or theme-saving UI tests between baseline and comparison. Baseline generation alone is not a passing regression comparison. `playwright.config.ts` currently stores evidence in the workspace’s private visualizations directory; adjust that artifact root for another machine. Baselines are intentionally outside the web root.

For faster exhaustive runs, `DUCS/tools/run_phase2_matrix.py --mode baseline|comparison --visual-root /PRIVATE/phase2-visual --workers 4` partitions the code-owned 60 types into disjoint groups. It isolates each output directory/log/JUnit report, verifies unchanged source fingerprints, rejects missing/duplicate tuples and merges exactly 1,464 renders / 488 audits. Run baseline and comparison sequentially. `design-typography.spec.ts` separately protects locale-navigation pixel stability. Gallery preview generation uses Python 3 with Pillow (`DUCS/tools/build_phase2_previews.py`); these are QA tools, not website runtime dependencies.

For root-vhost verification, start a second isolated server with the same test environment/session directory, `EA_STUDIO_TEST_ROOT=1`, port 8098 and document root `/opt/lampp/htdocs/expertsacademy`. Run `deployment-path.spec.ts` with `EA_STUDIO_BASE_URL=http://127.0.0.1:8098/site_admin/`. This checks fonts, CMS mounting, preview assets and HTTP-generated default links without using an owner account. CLI-generated sample links assume `/expertsacademy`, so open the delivered CLI sample using the documented localhost subfolder URL; pages created over HTTP derive their prefix from the actual installation.

## Quality evidence maintenance

The source fingerprint covers renderer, schemas, catalog, media metadata, responsive CSS, tokens and runtime. After reviewed changes, rerun affected tests and the full required matrix before regenerating `catalog/quality-results.json`; never hand-edit a stale result to “passed.” Screenshot updates require visual review, not blanket acceptance. The JSON/Markdown library exports and gallery thumbnails must be regenerated from the same reviewed catalog/snapshots. Keep unredacted data dumps and test cookies out of web-accessible directories.
