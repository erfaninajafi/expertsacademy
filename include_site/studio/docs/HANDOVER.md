# Experts Academy Studio — Phase 3 & 4 handover

## What was delivered

| Stage | Scope |
| --- | --- |
| 3A | Revision history, structural diff, append-only restore with optimistic locking |
| 3B | Template library: 6 starter pages, categories, favourites, create-from-template |
| 3C | Layer tree with reorder/device visibility and in-place content editing |
| 3D | Media library/picker with uploads and a responsive canvas with 3 widths |
| 3E | Visual baseline, CSP/preview security checks, backup drill, benchmark, handover |

The Phase 2 catalogue remains 60 component types and 200 meaningful variants.

Phase 4 adds publishing states/scheduling, backend roles, SEO/sitemap/redirects,
quality gates, preview tokens, image derivatives/focal points, rate limiting and
the performance/security hardening. See `docs/PHASE4.md` for the full report,
permission matrix, measured results and remaining issues.

## Running it

The editor is a compiled React bundle served by the existing CMS. No Vite server
is required in production.

```bash
cd site_admin/studio
npm install
npm run build                       # writes site_admin/asset/page-studio

cd ../../include_site/studio
php bin/migrate.php status          # migrations 001..008
php bin/migrate.php up
php bin/migrate.php seed            # 60 types / 200 variants / 6 templates
```

Database configuration comes from the studio environment (`Env::db()`); this
checkout uses `expertsacademy_dev` on `127.0.0.1:3306`.

## Verification

```bash
(cd include_site/studio && EA_STUDIO_DB_TESTS=1 php ./vendor/bin/phpunit)
(cd site_admin/studio && npx vitest run)
(cd site_admin/studio && npx playwright test)   # needs the PHP dev server on :8097
(cd site_admin/studio && npx tsc --noEmit && npm run build)
```

Latest results: PHPUnit **56 tests / 2,433 assertions**, Vitest **26**, Playwright
**18/18**, TypeScript clean, build clean. Playwright requires the session
directory (`php -d session.save_path=/tmp/ea-sessions ...`) to exist and the
`e2e_login=1` development login to be enabled.

Visual baseline: `site_admin/studio/e2e/visual.spec.ts-snapshots/`. It is tied to
Chrome on Linux. After an intentional visual change, run
`npx playwright test e2e/visual.spec.ts --update-snapshots` and review the diff.

## Backup and restore drill

```bash
cd include_site/studio
php bin/backup.php create
php bin/backup.php drill storage/backups/studio-YYYYmmdd-HHMMSS.sql
```

`create` writes a logical dump of every `tbl_studio_*` table, a JSON manifest
with row counts and SHA-256, and a `*.media.tar.gz` archive of media files.
`drill` verifies the dump checksum, restores it into `ea_drill_*` tables inside
the same database with renamed constraints, compares row counts and the media
archive checksum, and drops the scratch tables. It does not need
`CREATE DATABASE` and never writes to live tables.

Keep `storage/backups/` private (it is self-ignored). The dump covers the studio
schema and media files, not the legacy CMS tables.

## Security posture

- Component Studio never executes admin-submitted PHP/JS; previews use the PHP
  renderer with validation and sanitization.
- Preview responses send `script-src 'self'` without `unsafe-inline`; rich text
  is sanitized at render time and plain props are escaped.
- Mutations require a session, CSRF token and same-origin request.
- Uploads are MIME-checked, size-limited, renamed and stored outside executable
  paths.
- The E2E preview test asserts CSP and escaping with a script payload.

## Known limitations

- Focal-point and generated responsive image variants are not implemented;
  media references stay `id` + `alt`.
- The visual baseline is Chrome/Linux specific and must be regenerated on a
  different host.
- The backup drill restores into scratch tables; a full production restore is an
  operator runbook step, not an automated command.
- The Desktop mirror of the project has not been synchronised with this XAMPP
  checkout.
- Publishing still requires binding a draft to an existing CMS page code before
  the public route changes.
