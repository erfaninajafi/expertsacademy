# Phase 3 — Visual composer, staged delivery

Phase 3 is delivered in slices so each one is independently testable.

## Stage 3A — Revision history, comparison and restore (delivered)

- `GET studio_api.php?resource=revision&page=<id>&id=<revisionId>` returns a
  stored revision document for a page.
- `POST studio_api.php?resource=restore&id=<pageId>` with
  `{revisionId, expectedVersion}` appends the old document as a new revision.
  History is never rewritten and the restore is audited as `page.restored`.
- Optimistic locking still applies: a stale restore returns 409.
- UI: toolbar **History** opens a dialog listing revisions, loading the selected
  document, showing a structural comparison against the current draft
  (sections, added/removed/changed components, page-level fields) and a restore
  action. Restored content is loaded into the editor as a clean draft.
- `src/diff.ts` computes the comparison; `src/diff.test.ts` covers added,
  removed, changed, moved and identical documents.

Validation: PHPUnit 45 tests / 2,348 assertions; Vitest 19; Playwright 10/10.

## Stage 3B — Template library (delivered)

- Six complete starter pages (course launch, academy landing, about, admissions,
  workshop detail, content hub) are seeded from registry components, plus the
  original editorial starter.
- `tbl_studio_templates` now carries `category` and `description`; migration
  `006_template_library` also adds per-admin favourites
  (`tbl_studio_template_favorites`).
- API: `templates` lists the latest active versions with section counts and
  favourite state, `template` returns a validated document, and
  `template-favorite` toggles a favourite.
- `POST pages` accepts `template` + `templateVersion`; the server copies the
  stored document, applies the requested title/locale/direction and audits the
  template id. Clients cannot submit a template document directly.
- UI: toolbar **Templates** opens a searchable library with category filters,
  favourites, template preview through the PHP renderer, and page creation from
  the selected template. Copied pages are ordinary drafts; later template
  changes never affect existing pages.
- Import/export of template JSON was not included in this stage.

Validation: PHPUnit 46 tests / 2,392 assertions; Playwright 11/11 (template
library search, preview data and page creation).

## Stage 3C — Layers and inline editing (delivered)

- The left sidebar switches between **Blocks** and **Layers**. The layer tree
  lists every section and block with selection, up/down reorder and per-device
  visibility (desktop/tablet/mobile). The last visible device cannot be removed,
  matching the contract that every layer stays visible somewhere.
- Selecting a block on the canvas expands an in-place editor for its heading,
  text, URL, media (id + alt) and flat CTA object fields. Edits write straight
  into the draft with undo/redo merging; the inspector stays in sync.
- Inline validation mirrors the PHP rules: required fields, string length,
  single-line strings and safe URLs. A required field emptied in place reverts
  on blur and announces the error. Rich text, lists and enum fields stay in the
  inspector because they need structured/sanitized editing.
- Hiding a block on desktop dims its canvas card and shows a "Hidden on desktop"
  badge; the renderer already emits the same device visibility classes.

Validation: Vitest 26 (inline field discovery, path writes, URL/required/max
rules); Playwright 12/12 including layer reorder, visibility toggling and inline
required-field recovery.

## Stage 3D — Media and responsive canvas (delivered)

- Media listing resolves a public URL, so the library shows thumbnails,
  dimensions and search by name/alt. A shared picker is available from both the
  inline image field and the inspector: choosing an image writes its id and alt
  into the draft, and uploads refresh the library in place.
- The canvas has a desktop/tablet/mobile switcher that constrains the editing
  surface to 834px or 390px. The server-rendered preview opens at the same
  width, so the renderer's media queries and responsive overrides are exercised.
- Layer visibility and `responsive[<device>].hide` now dim the block and show a
  "Hidden on <device>" badge for the active canvas device.
- Media references remain `id` + `alt`. Focal-point metadata and generated
  responsive image variants are not included; the renderer keeps one source per
  image and responsive layout is handled in CSS.

## Stage 3E — Hardening (delivered)

- **Visual baseline**: `site_admin/studio/e2e/visual.spec.ts` opens the Course
  launch starter, waits for fonts and images, emulates reduced motion and
  compares the server-rendered `.cms-page` against
  `e2e/visual.spec.ts-snapshots/course-launch-chromium-linux.png`. Regenerate it
  deliberately with `--update-snapshots`; it is Chrome/Linux specific.
- **Preview security**: an E2E test posts the sample document with a script
  payload and asserts the preview stays HTTP 200, escapes the payload and sends
  `script-src 'self'` with no `unsafe-inline` for scripts. `style-src` keeps
  `'unsafe-inline'` because the renderer emits inline CSS custom properties;
  script execution remains blocked.
- **Keyboard**: undo (`Ctrl/Cmd+Z`) after an inline heading edit is covered by
  E2E and restores the previous value.
- **Backup and restore drill**: `bin/backup.php create` dumps all 21
  `tbl_studio_*` tables with a checksum manifest and archives media files.
  `bin/backup.php drill <dump>` restores the dump into `ea_drill_*` scratch
  tables, verifies every row count and the media archive checksum, then removes
  the scratch tables. It never touches live tables and needs no CREATE DATABASE.
  Latest drill: 21 tables, 413 rows, media archive verified, 0 scratch tables
  left behind.
- **Performance**: `bin/benchmark.php` (PHP 8.2.12, 60 components / 200
  variants, 6 sections) measured 0.15 ms average / 0.17 ms p95 per rendered
  fragment and 0.17 ms / 0.18 ms per full document, with 4 MB peak memory.

## Phase 3 status

All five stages are delivered. Final verification: PHPUnit 46 tests / 2,392
assertions, Vitest 26 tests, Playwright 16/16, TypeScript clean and a production
build. Handover notes live in `docs/HANDOVER.md`.
