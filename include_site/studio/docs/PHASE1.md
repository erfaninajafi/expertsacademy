# Phase 1 — CMS Architecture & Page Builder Foundation

Status: complete and verified in the development database (`expertsacademy_dev`).
Phase 2 has not been started.

## 1. Architecture

```
Public request
  Apache/PHP ─ index.php?section=<tbl_categories_url>&lang=<fa|en>
      │
      ├─ PublicGateway::find(code, lang)
      │     └─ published revision for (legacy_code, locale)
      │           │
      │           ├─ found  → Renderer::fragment(document) → PHP component templates
      │           └─ none   → existing legacy renderer (unchanged)
      │
      └─ legacy page chrome (header/footer/sidebar) is preserved around both paths

Editor request
  site_admin/page_studio.php (shared CMS session, capability check)
      └─ React 19 + TypeScript SPA (compiled; no Node server needed at runtime)
            ├─ Zustand store (document, selection, history, dirty state)
            ├─ dnd-kit canvas + keyboard reordering
            ├─ Radix dialogs, Tailwind UI, Motion/GSAP micro-animation
            └─ fetch → studio_api.php (JSON)
                          ├─ Auth: session + X-CSRF-Token + same-origin + caps
                          ├─ Validator (schema v1, per-component props)
                          ├─ PageRepository (optimistic lock, revisions, publish)
                          ├─ AuditRepository (append-only events)
                          └─ MySQL: tbl_studio_*

Preview
  studio_preview.php  → same PHP renderer, standalone HTML document + CSP
  studio_component_preview.php → single component/variant preview
```

Key properties:

- Published pages are plain PHP + MySQL. No Vite/Node process is required, and the
  editor bundle is a static build under `site_admin/asset/page-studio/`.
- Content is stored as a versioned JSON document. Every save creates an immutable
  revision; publishing points the page at one revision and records a publication row.
- The renderer is shared by the public page, the editor preview and sample renders,
  so previews cannot drift from production output.
- Legacy page storage, routing, media, authentication and business code are untouched.
  The new path only activates for a page that has a published Studio revision bound to
  an existing category code.

## 2. Files created

Backend module `include_site/studio/` (namespace `ExpertsAcademy\Studio`):

| Path | Purpose |
| --- | --- |
| `bootstrap.php` | autoloader, config load, error handling |
| `src/Env.php`, `src/Db.php` | environment/secrets and PDO access |
| `src/Problem.php`, `src/Uuid.php`, `src/Json.php`, `src/Html.php` | primitives |
| `src/Sanitizer.php` | allow-list HTML sanitizer for rich text |
| `src/DesignTokens.php` | token set → CSS custom properties |
| `src/Registry/ComponentRegistry.php` | 12 types / 24 variants, defaults, schema fingerprint |
| `src/Contract/PageDocument.php` | schema v1 document (normalize/migrate) |
| `src/Contract/Validator.php` | structural + per-component validation |
| `src/Contract/Samples.php` | starter documents (en/fa) |
| `src/Render/Renderer.php`, `Partial.php`, `MediaResolver.php`, `NullMediaResolver.php`, `MapMediaResolver.php` | deterministic PHP rendering |
| `src/Render/templates/*.php` | 12 component templates |
| `src/Repo/PageRepository.php` | create/save/publish/lookup, optimistic locking |
| `src/Repo/AuditRepository.php`, `TokenRepository.php`, `MediaRepository.php` | audit, themes/tokens, media registry |
| `src/Auth/Auth.php` | shared CMS session, dev login, CSRF, same-origin, capabilities |
| `src/Http/Http.php` | JSON responses, body limits, method guards |
| `src/PublicGateway.php` | resolves `(legacy_code, locale)` to a published page |
| `src/Database/Migrator.php`, `Seeder.php` | forward-only migrations + seeding |
| `database/migrations/001..004` (`up`/`down`) | additive schema |
| `bin/migrate.php` | CLI migration runner (`--dry-run` supported) |
| `tests/*` | PHPUnit unit + DB integration tests |
| `phpunit.xml`, `composer.json` | test/runtime config |
| `docs/PHASE1.md` | this document |

Admin entry points:

- `site_admin/page_studio.php` — editor shell inside the current CMS chrome.
- `site_admin/studio_api.php` — JSON API.
- `site_admin/studio_preview.php` — full-page preview.
- `site_admin/studio_component_preview.php` — component preview.

Editor `site_admin/studio/`:

- `src/main.tsx`, `App.tsx`, `types.ts`, `api.ts`, `store.ts`, `styles.css`
- `src/components/{Canvas,Inspector,Fields,Library,Preview,Dialogs,ErrorBoundary}.tsx`
- `src/store.test.ts` (Vitest), `e2e/studio.spec.ts` (Playwright), `playwright.config.ts`
- `vite.config.ts` builds to `site_admin/asset/page-studio/` with a manifest.

Public assets:

- `asset_site/css/cms-pages.css` — token-driven component styles.
- `asset_site/js/cms-runtime.js` — progressive reveal only; content works without it.
- `upload_site/cms/.htaccess` — denies script execution in the media directory.

## 3. Files modified

- `index.php` — Studio lookup with legacy fallback (only when a published page exists).
- `site_admin/header.inc.php` — removed dead links to the obsolete builder screens.
- `site_admin/page_studio.php`, `studio_api.php`, `studio_preview.php`,
  `studio_component_preview.php` — replaced prior prototypes with the new module.

Nothing in `include_site/component_registry.php`, `include_site/page_renderer.php`,
`components_site/*` or any business page was changed. The old builder files were
quarantined, not reused.

## 4. Database changes (additive only)

Applied to `expertsacademy_dev` via `bin/migrate.php` (4 migrations):

| Table | Role |
| --- | --- |
| `tbl_studio_migrations` | applied version + checksum |
| `tbl_studio_pages` | page identity, locale, direction, legacy binding, lock version, draft/published pointers, status |
| `tbl_studio_revisions` | immutable JSON document per save, schema version + checksum |
| `tbl_studio_publications` | publication history (revision, locale, path, checksum, publish/unpublish times) |
| `tbl_studio_events` | append-only audit (entity, action, payload, actor, IP, UA) |
| `tbl_studio_components`, `tbl_studio_component_variants`, `tbl_studio_component_versions` | registry snapshot (12 / 24 / 12) |
| `tbl_studio_component_lifecycle`, `tbl_studio_component_proposals` | Phase 3 lifecycle placeholders |
| `tbl_studio_themes`, `tbl_studio_design_tokens` | token sets (2 themes seeded) |
| `tbl_studio_media`, `tbl_studio_media_usages` | media registry + usage index |
| `tbl_studio_templates`, `tbl_studio_leads` | framework tables |

No legacy table, column or row was altered. Rollback scripts exist for every migration.

## 5. API endpoints (`site_admin/studio_api.php`)

All responses are JSON. Mutations require a valid session, `X-CSRF-Token` and a
same-origin `Origin`/`Referer`; capabilities come from `tbl_user_status`
(0 → read/edit/publish, 1 → read/edit, others → read).

| Method | Resource | Notes |
| --- | --- | --- |
| GET | `?resource=session` | actor + CSRF token |
| GET | `?resource=pages[&id=…]` | listing (`limit`, `offset`) or one page |
| GET | `?resource=registry` | components, categories, schema version, fingerprint |
| GET | `?resource=themes` / `tokens` | token sets and versions |
| GET | `?resource=media` | media registry |
| GET | `?resource=templates` | templates |
| GET | `?resource=audit&id=…` | page audit trail |
| GET | `?resource=sample&locale=fa\|en` | starter document |
| POST | `?resource=media` | multipart upload (edit) |
| POST | `?resource=validate` | dry-run document validation |
| POST | `?resource=pages` | create draft (edit) → 201 |
| PUT | `?resource=pages&id=…` | save revision with `expectedVersion` → 409 on conflict |
| POST | `?resource=publish&id=…` | publish revision (publish cap) |
| POST | `?resource=themes` | create theme (publish cap) → 201 |

## 6. Tests and results

| Suite | Command | Result |
| --- | --- | --- |
| PHPUnit | `EA_STUDIO_DB_TESTS=1 php ./vendor/bin/phpunit` | **37 tests, 147 assertions — OK** |
| Vitest | `npm test` | **12 tests — OK** |
| Playwright | `npx playwright test` | **6 specs passed** (system Chrome) |
| Build | `npm run build` (tsc + Vite) | OK, 492.85 kB JS / 21.4 kB CSS |
| HTTP smoke | temporary `storage/tmp/smoke.php` (removed) | 16/16 checks passed |

Playwright covers: registry listing, create/edit/save + reload, component add +
preview render, publish, anonymous/cross-origin API refusal, axe accessibility
(no serious/critical violations). DB tests create and delete their own pages; the
suite ends with `tbl_studio_pages/revisions/events/publications = 0`.

Manual HTTP checks: anonymous API → 401, missing CSRF → 419, foreign Origin → 403,
stale version → 409, preview → 200 with CSP, legacy page unchanged until publish,
legacy output restored after unpublishing the test page.

## 7. Backups, restore and rollback

Backup (verified, outside the docroot):
`/home/igx7/backups/expertsacademy/20261109-114619/`

- `expertsacademy_dev.sql` — 2.0 MB, 36 tables (pre-change dump)
- `expertsacademy-code.tar.gz` — 105 MB code snapshot
- `SHA256SUMS` — checksums (`sha256sum -c SHA256SUMS`)

Restore:

```bash
cd /home/igx7/backups/expertsacademy/20261109-114619
sha256sum -c SHA256SUMS
mysql -u <dba> -p expertsacademy_dev < expertsacademy_dev.sql
tar -xzf expertsacademy-code.tar.gz -C /opt/lampp/htdocs
```

Limitation: the app user `experts_admin` has no `CREATE DATABASE` privilege, so the
dump could not be drill-restored into a scratch database. A DBA-level restore test is
still required before production. The dump header/table count and checksums were verified.

Rollback of Phase 1 (no production deploy has happened):

1. Run `php include_site/studio/bin/migrate.php --down` (or execute
   `database/migrations/00{4,3,2,1}_*.down.sql` in reverse order) to drop only
   `tbl_studio_*` tables.
2. Restore `index.php` and `site_admin/header.inc.php` from the code snapshot.
3. Remove `site_admin/{page_studio,studio_api,studio_preview,studio_component_preview}.php`,
   `site_admin/studio/`, `site_admin/asset/page-studio/`, `include_site/studio/`,
   `asset_site/css/cms-pages.css`, `asset_site/js/cms-runtime.js`, `upload_site/cms/`.
4. Legacy pages were never modified, so they keep serving from the existing renderer.

## 8. Environment and configuration

- Secrets live outside the docroot: `/home/igx7/.config/expertsacademy/studio.php`
  (DB credentials, `asset_url=/expertsacademy`, `e2e_dev_login`).
- `e2e_dev_login=true` is development-only. Production must omit it; login then uses
  the existing CMS session (`siteadmin_login`), no separate session name.
- Editor build: `cd site_admin/studio && npm install && npm run build`.
- Migrations: `php include_site/studio/bin/migrate.php [--dry-run]`.

## 9. Remaining issues / known limitations

1. Restore drill blocked by missing `CREATE DATABASE` privilege (see above).
2. Apache is not running on this machine; verification used PHP's built-in server on
   `127.0.0.1:8097`. `.htaccess` protections and HTTP behaviour under Apache/LiteSpeed
   should be re-checked on staging.
3. Media library UI/endpoint exists, but public media resolution currently uses the
   map resolver for seeded assets; dynamic media URL resolution is Phase 2 work.
4. RTL/Farsi rendering carries `dir`/`lang` and logical CSS properties, but a full
   bilingual visual QA pass is pending.
5. No legacy-page import/migration tool — deliberately out of Phase 1 scope.
6. The React editor is desktop-first; small-screen editing is usable but not optimized.

## 10. Phase 2 readiness

The contracts Phase 2 depends on already exist and are versioned: document schema v1,
registry fingerprint, token sets, revision/publication tables, audit trail, migration
runner and the shared renderer. Adding routing by slug, media pipeline, page import
and richer templates does not require reshaping stored documents.

## 11. Ten actions before production

1. Have a DBA drill-restore the dump into a scratch database.
2. Run migrations and the full suite against a staging clone with Apache/LiteSpeed.
3. Remove `e2e_dev_login` from every non-development environment.
4. Set real filesystem permissions on `upload_site/cms/` and confirm `.htaccess` is honoured.
5. Rotate the DB password used by the Studio config and keep it outside the docroot.
6. Load-test the published-page path (one indexed lookup + one revision read).
7. Add monitoring for 5xx on `index.php` and `studio_api.php`.
8. Complete bilingual/RTL visual QA on the 12 components.
9. Document the editor workflow for content editors (create → edit → preview → publish).
10. Decide the legacy-page migration order and freeze the old builder screens.
