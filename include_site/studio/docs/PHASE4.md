# Experts Academy Studio — Phase 4: Publishing, Security, SEO & Performance

Phase 4 builds on Phases 1–3 without changing the public legacy renderer for
pages that have no published CMS revision. Everything runs on PHP 8.2 + MySQL;
the React editor remains a build-time tool and is served as compiled assets.

## 1. Publishing workflow

### States

| State | Meaning | Public output |
| --- | --- | --- |
| `draft` | Editable working revision, never public | No |
| `scheduled` | A revision and UTC publish time are queued | No (until the scheduler publishes) |
| `published` | A specific revision is the live document | Yes, when bound to a legacy code |
| `unpublished` | Was public, deliberately taken offline; history retained | No |
| `archived` | Retired page; schedules cancelled and history retained | No |

`preview` is not a stored state: any draft revision can be rendered through the
authenticated preview endpoint or a short-lived preview token.

### Transitions

- **Save** validates the JSON contract, appends a revision and increments
  `lock_version`. Editing a scheduled page cancels its pending schedule and
  returns it to `draft`, so a stale revision cannot be published silently.
- **Publish** locks the page row, validates the exact revision, writes the
  publication row and updates the page in one transaction. Pending schedules are
  cancelled. Passing a revision id publishes that revision (rollback) without
  rewriting history.
- **Schedule** validates the revision and stores `publish_at` in UTC plus the
  editor's timezone. One pending schedule per page; a new schedule supersedes
  the old one.
- **Unpublish** updates the page to `unpublished` and marks published
  publication rows `unpublished`; the public gateway requires page status
  `published`, so the legacy body is used again or the route 404s.
- **Archive** cancels schedules, unpublishes and sets `archived_at`.
- **Restore** appends an old revision as a new draft; **rollback** publishes an
  old revision directly.

All state changes use `SELECT ... FOR UPDATE`, `lock_version` optimistic checks
and a transaction; callers receive **409** on a stale version rather than
overwriting another editor.

### Routes

- `legacy_code` binds a page to an existing `tbl_categories_code` URL, preserving
  existing public URLs (`?section=code`).
- `slug` is an optional lowercase route segment, unique per locale. It is used by
  the sitemap and future slug routing; it never replaces a legacy URL
  automatically.
- Both are managed through `PUT ?resource=route&id=...` and validated server-side.
- `tbl_studio_redirects` provides managed 301/302/307/308 redirects, checked at
  the top of `index.php` before the legacy query.

### Publication log

`tbl_studio_publications` stores one row per page/revision with status, path,
checksum, actor and timestamps. Every transition is also appended to
`tbl_studio_events` (`page.published`, `page.unpublished`, `page.scheduled`,
`page.archived`, `page.route`, …). History is append-only.

## 2. Scheduled publishing

`bin/publish_scheduled.php` is a server-side worker; no browser is involved.

```bash
cd /path/to/expertsacademy/include_site/studio
/usr/bin/php bin/publish_scheduled.php --limit=25
```

Cron every minute:

```cron
* * * * * cd /path/to/expertsacademy/include_site/studio && /usr/bin/php bin/publish_scheduled.php >> /path/to/expertsacademy/storage/logs/scheduler.log 2>&1
```

Behaviour:

- `publish_at` is stored in UTC; the editor timezone is stored for display.
- A schedule is claimed atomically (`pending -> processing`) with `attempts + 1`,
  so overlapping cron runs cannot publish it twice.
- If the page was already published with the same revision, the job completes
  idempotently.
- Failures retry with a short backoff (2/4 minutes) up to **3 attempts**, then
  become `failed` with `last_error` and the page returns to `draft`.
- Stale `processing` rows older than 15 minutes are recovered.
- A file lock (`storage/cache/scheduler.lock`) prevents duplicate local workers.
- Failed jobs can be requeued by a publisher through
  `POST ?resource=schedule_retry` with `scheduleId`; the queue is visible through
  `GET ?resource=schedules`.
- Timezone: `EA_PUBLISH_TIMEZONE` (or `publishing.timezone` in the env file),
  default `UTC`. `bin/publish_scheduled.php` also sets PHP's default timezone.

## 3. Permission matrix

Roles are stored in `tbl_studio_user_roles`; users without a row fall back to
the legacy `tbl_user_status` mapping (0 administrator, 1 editor, other reviewer).
`Auth::requirePermission()` runs in the PHP API for every mutation.

| Permission | Administrator | Developer | Designer | Editor | Reviewer | Publisher |
| --- | :-: | :-: | :-: | :-: | :-: | :-: |
| pages.create | ✓ | ✓ |  | ✓ |  |  |
| pages.edit | ✓ | ✓ | ✓ | ✓ |  |  |
| pages.restore | ✓ | ✓ |  | ✓ |  | ✓ |
| pages.route | ✓ | ✓ |  | ✓ |  |  |
| pages.publish | ✓ | ✓ |  |  |  | ✓ |
| pages.schedule | ✓ | ✓ |  |  |  | ✓ |
| pages.unpublish | ✓ | ✓ |  |  |  | ✓ |
| pages.archive | ✓ | ✓ |  |  |  | ✓ |
| design.edit | ✓ | ✓ | ✓ |  |  |  |
| components.manage | ✓ | ✓ | ✓ |  |  |  |
| templates.manage | ✓ | ✓ |  |  |  |  |
| media.upload | ✓ | ✓ | ✓ | ✓ |  |  |
| media.manage | ✓ | ✓ | ✓ |  |  |  |
| preview.draft | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ |
| seo.manage | ✓ | ✓ |  | ✓ |  |  |
| redirects.manage | ✓ | ✓ |  |  |  |  |
| users.manage | ✓ |  |  |  |  |  |
| audit.read | ✓ | ✓ |  |  | ✓ | ✓ |

The editor hides unavailable actions, but the API is the enforcement point:
direct calls without the permission return **403** and without CSRF/origin
validation **419/403**.

## 4. SEO

- Property-aware SEO panel: title, description, keywords, canonical, robots
  (`index,follow` / `noindex,follow` / `index,nofollow` / `noindex,nofollow`) and
  Open Graph image from the media library.
- Public head includes canonical, robots, Open Graph, Twitter card and JSON-LD
  `WebPage` structured data. Existing legacy meta is suppressed only when the CMS
  page supplies its own SEO head.
- `sitemap.php` emits XML for published, indexable pages. It prefers
  `seo.canonical`, then slug, then legacy `?section=` URL. ETag + 5-minute cache.
- `robots.php` disallows `/site_admin/`, token URLs, and points at the sitemap.
  Apache rewrites `robots.txt` and `sitemap.xml` to these endpoints.
- Redirect management is available at `GET/POST ?resource=redirect(s)`.
- Preflight checks (below) warn on short/long titles, missing descriptions,
  duplicate metadata, unresolved relative links and heading-level jumps.
- Existing URLs are preserved through `legacy_code`; slugs are opt-in.

## 5. Images

- Uploads are MIME-sniffed against JPEG/PNG/WebP/AVIF/SVG, size-limited to 8 MB,
  renamed randomly, stored under a non-executable media path, and originals are
  never overwritten or deleted.
- Resized derivatives (480/960/1440 px, never upscaled) are generated with GD
  when available and stored in `tbl_studio_media_variants`. This PHP build has
  **no WebP/AVIF encoder**, so derivatives stay in the source format; the
  optimizer automatically uses WebP/AVIF when the build supports it.
- `<img>` output includes `srcset`, `sizes`, intrinsic `width`/`height`,
  `loading="lazy"` (eager + `fetchpriority=high` for heroes), `decoding="async"`
  and per-asset focal point `object-position`.
- Alt text is stored and preflight warns when it is missing; safe upload
  validation and SVG handling are unchanged from Phase 3.

## 6. Performance

Measured on the local PHP 8.2 development server with Chrome, 3 runs, median,
1366×768, a full sample CMS page (preview token, `preview_link.php`):

| Metric | Measured | Target |
| --- | --- | --- |
| LCP | **540 ms** | ≤ 2500 ms |
| CLS | **0.0000** | ≤ 0.1 |
| Interaction latency (pointerdown→pointerup) | **0.1 ms** | — |
| Event Timing entries >16 ms | 0 in median run | INP ≤ 200 ms |
| TTFB | **7.3 ms** | — |
| FCP | **540 ms** | — |
| DOMContentLoaded / load | **16.4 / 20 ms** | — |
| Resources / transfer | **3 / 144.5 KB** | — |

These are lab numbers from a local development server, not field Core Web
Vitals. INP is not directly reported by the lab harness; no interaction exceeded
the 16 ms Event Timing threshold, which is consistent with the target but not a
substitute for field RUM. Re-run with:

```bash
node site_admin/studio/e2e/perf-phase4.mjs "http://127.0.0.1:8097/expertsacademy/preview_link.php?token=…"
```

Implemented levers: PHP SSR (no client render for content), one CSS request plus
inline token CSS, conditional motion runtime (skipped when no animation), `defer`
scripts, intrinsic image dimensions, lazy images, compiled assets with immutable
cache headers, ETag/cache on sitemap, indexed scheduler/media queries, and no
Node runtime in production.

## 7. Security

- CSRF token + same-origin checks on every mutation (existing `Auth::csrf`).
- Prepared PDO statements everywhere; no interpolated user input in SQL.
- Context escaping via `Html::e()`; rich text sanitized through the allowlist.
- Strict JSON contract validation for documents, props, media refs and robots.
- Upload MIME/size validation, random keys, executable-path denial.
- Granular backend permissions plus legacy capability compatibility.
- Rate limiting (DB fixed windows) on publish/schedule/media/roles.
- Audit trail for publish/unpublish/archive/route/redirect/role events.
- CSP: `default-src 'none'`, scripts self only, frames self, no inline script.
- Preview links use 256-bit random tokens; only SHA-256 hashes are stored,
  expire after 1 hour by default, and responses are `no-store`/`noindex`.
- Scheduler runs as CLI only; no public HTTP trigger exists.

## 8. Pre-publication quality gate

`QualityChecker` runs against the exact revision being published:

**Blocking:** empty content, unknown component/version/variant, duplicate
component ids, unsupported schema, missing title, malformed document.

**Warnings:** missing/short/long SEO title or description, duplicate metadata,
missing image alt, unavailable media placeholder, oversized image (>2 MB),
unresolved internal links, heading hierarchy jumps, hidden-on-all-devices
components, missing public route binding, invalid canonical.

Publish and schedule return **422** with `checks.errors`/`checks.warnings` when
blocking issues exist. The editor shows the same result in the Publishing dialog.

## 9. Tests

```bash
(cd include_site/studio && EA_STUDIO_DB_TESTS=1 /opt/lampp/bin/php ./vendor/bin/phpunit)
(cd site_admin/studio && npx vitest run)
(cd site_admin/studio && npx tsc --noEmit && npm run build)
(cd site_admin/studio && npx playwright test)   # PHP server on :8097
```

Coverage added in Phase 4: `tests/Phase4PublishingTest.php` (schedule/claim/
server-side publication, cancel, unpublish, archive, rollback, slug conflicts,
quality gate, redirects, preview hashing, role matrix), SEO head tests,
robots validation tests, and Playwright flows for the Publishing dialog,
scheduling/unpublish, the preview-link path, and the permission matrix.

Final local results (2026-11-15):

| Suite | Result |
| --- | --- |
| PHPUnit (DB integration) | **56 tests / 2,433 assertions** — pass |
| Vitest | **26 tests** — pass |
| TypeScript `tsc --noEmit` | pass |
| Vite production build | pass |
| Playwright (Chrome/Linux) | **18/18** — pass |
| PHP lint (new/changed files) | pass |
| Scheduler CLI dry run | `{"claimed":0,"published":0,"failed":0}` |
| Sitemap / robots endpoints | 200 `application/xml` / `text/plain` |
| Public CMS page (temporary binding) | 200, canonical + OG + JSON-LD rendered; fixture removed afterwards |

## 10. Deployment requirements

- PHP 8.2+ with `pdo_mysql`, `json`, `mbstring`, `dom`, `fileinfo`, `gd`
  (GD is optional but required for resized derivatives).
- MySQL/MariaDB 10.4+; run `php bin/migrate.php up` (migrations 001–008).
- Apache with `mod_rewrite` + `mod_headers` (or the Nginx example) and HTTPS.
- Cron for `bin/publish_scheduled.php` every minute; writable
  `upload_site/cms`, `storage/cache`, `storage/logs`.
- Environment: `EA_BASE_URL`, `EA_ASSET_URL`, DB credentials,
  `EA_PUBLISH_TIMEZONE`; keep secrets outside the web root.
- Production serves `site_admin/asset/page-studio` compiled by `npm run build`;
  Node.js is not needed at runtime.
- Nginx/Apache examples: `include_site/studio/deploy/`.

## 11. Remaining issues

- WEBP/AVIF derivatives cannot be produced on this XAMPP PHP build (no encoders);
  source-format resizing is used. Install/enable GD WebP or Imagick to activate
  modern formats automatically.
- Slug routing is stored and exposed in the sitemap but the public legacy router
  still resolves by `legacy_code`; slug-only pages need a router integration.
- Quality checks for "mobile overflow" and full WCAG are not automated; the
  existing Playwright accessibility scan remains the basic check.
- Lab INP is a proxy (Event Timing + interaction marks), not field RUM; no RUM
  collector was added.
- Scheduler error alerts are log/DB only; no email/webhook notification.
- Redirects apply to the main `index.php` route only, not to arbitrary legacy
  sub-scripts.
- Apache/LiteSpeed-specific rewrite behaviour is configured but not tested on a
  real Apache vhost in this environment (the PHP built-in server was used).
- Backup still covers `tbl_studio_*` only, as documented in Phase 3.
