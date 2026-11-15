# Experts Academy (ExAcd): AI project handoff

**Current work: Phase 2 Design Studio.** Read [PHASE2_DESIGN_STUDIO.md](PHASE2_DESIGN_STUDIO.md), final test/deployment JSON and PHASE2_FILES.tsv before continuing. New components are independent of the legacy implementation. Never edit deployed migration checksums. The unrelated Firefox edit and preservation exception are documented in PHASE2_PREEXISTING_EDIT.json. The prior handoff below records earlier states.

## Latest continuation — Phase 1 foundation, 2026-11-01

The new independent CMS Page Studio is implemented. Read [the architecture/delivery report](PHASE1_CMS_FOUNDATION_2026-11-01.md), [developer/API guide](PHASE1_DEVELOPER_SETUP.md), [backup/rollback](PHASE1_BACKUP_ROLLBACK.md), [test results](PHASE1_TEST_RESULTS.json) and [file manifest](PHASE1_FILES.tsv) before using the older handoff below. Development has 35 tables; all 27 original tables match pre-change hashes. No existing public page is switched; the sample is unbound/unpublished. Studio must not import the old Builder or `components_site` implementations. Migration 001 is applied and checksum guarded; add a new migration for future seeded schema/registry changes. The user's original source/database remain protected. Temporary test processes were stopped after verification; XAMPP was left running.

**Last verified: 2026-11-01, local XAMPP workspace.** Start with current source and this document. Historical October change notes remain useful evidence for those dates. The current review updated documentation and performed read-only checks; it did not resume database migration or modify application logic.

## Workspace and owner constraints

| Item | Current value |
| --- | --- |
| Active improved source | `/opt/lampp/htdocs/expertsacademy` |
| Protected original source | `/opt/lampp/htdocs/expertsacademy-original` |
| Public / CMS URL | `http://localhost/expertsacademy/` / `site_admin/` |
| Active configured database | `expertsacademy_dev` |
| Protected original database | `expertsacademy` |
| XAMPP socket | `/opt/lampp/var/mysql/mysql.sock` |

Local command instructions are in `/home/igx7/.codex/RTK.md`; shell commands in this workspace use the `rtk` prefix. Read applicable agent instructions before work.

Work on the active source and development or disposable test database. Keep the original source/database intact. Back up development data outside the document root before writes. Do not print credentials, personal records, or dump INSERT payloads. The owner requires review before optional files/data are deleted or repurposed. No optional cleanup has been approved.

The improved project was first created on the Desktop, then moved into the active XAMPP path. The two source copies and databases do not synchronize. Uploads are separate files and also do not synchronize. Git tracks zero project files, so `git diff` is not a reliable change log. A file snapshot made before the initial code changes was lost when `/tmp` was reset; do not claim a bit-for-bit source rollback is available. The protected original is not proven identical to that pre-change state.

## Current checked state

- Source inventory: 7,565 readable files, including 453 PHP files. `asset/img/` is readable now; no traversal gap was observed. Static SQL names 72 tables.
- Development DB: MariaDB 10.4.32, 27 tables (26 InnoDB, one MyISAM). The two added tables are `tbl_page_builder_layouts` and `tbl_page_component_instances`. The original 25 table/column name sets match the historical dump; this does not prove identical types, indexes, or data.
- CMS pages: 52 locale rows (26 FA, 26 EN), 259 Legacy marker occurrences. **28 Builder layouts are active, all independent; 24 page/locales remain Legacy.** There are 193 instance records, 191 non-deleted/enabled.
- The owner deliberately edited test page `D5392E3/fa` after conversion: the original two instances were soft-deleted and a logo instance added. Preserve those changes; that page's difference from the October baseline is intentional.
- Current evidence: 453 PHP syntax passes, six application JS syntax passes, nine Builder unit passes, and 48 HTTP checks with expected statuses/no PHP error markers. These include 41 public CMS routes, one unknown route, selected authentication gates, and Builder assets. Eleven blank-slug English rows have no direct route.

The read-only [development snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json) contains current columns, engines, counts, layout metadata, and inactive conversion issues without record content. The [review report](PROJECT_REVIEW_2026-11-01.md) separates source facts from runtime evidence.

## Service issue resolved during review

The system `mysql` service again occupied port 3306. XAMPP printed `Starting MySQL...ok`, but the project socket failed (2002), TCP returned 1698, and Apache served 500. The owner stopped the system service and started XAMPP MySQL; the configured development connection and public routes then worked. An unprivileged `lampp status` can also misreport MySQL because it cannot read the mysql-owned PID file. Verify an actual configured connection. Do not switch the project to system MySQL or the original database to mask a connection problem. The system service may return after reboot; see [operations](SETUP_AND_OPERATIONS.md).

## Public site and Builder

`index.php` loads `includer_site.php`, restricts locale to FA/EN, selects a type-4 category by slug/home flag, and renders common header/footer. Missing pages return 404. `include_site/page_renderer.php` chooses Legacy markers or UUID instances using the exact `(page_code,locale)` layout row. `component_registry.php` allowlists templates and fields. There are 36 definitions (32 catalog choices plus four hidden headings), with 39 marker/variant mappings.

Builder content is copied into `tbl_textsection` and `tbl_content` with UUID parents. Legacy page-code rows and `tbl_categories_design` are retained. Missing locale content is skipped, not replaced from another language. FAQ accordion and `[فرم همکاری سوالات]` remain independent. Shared structure is an explicit action for exactly matching FA/EN instance sequences; locale content remains separate.

`site_admin/page_builder.php` provides catalog, instance order/actions, locale switch, TinyMCE inspector, image uploads, and external public-preview links. `category_manage.php` retains title/URL/SEO and Legacy access. New API actions use CMS session, CSRF, prepared statements, revision checks, and transactions. See [the API contract](PAGE_BUILDER_API.md).

Saving applies directly to the public page. There is no draft/publish state, undo/history, live embedded rendering canvas, per-user page ACL, reusable global-content library, or unshare action. Rich-text sanitization intentionally keeps a small tag set and strips attributes/styles; do not promise arbitrary HTML, media embeds, or preserved text alignment. The `complete` badge is a nonempty-data heuristic, not a full validation certificate.

### Ordering correction

Early conversion reversed some equal-order rows on `BEC40D0/fa`. The source and clone payloads matched; only order differed. Converter reads now preserve the observed Legacy query sequence and Builder content uses an explicit ID tie-breaker. `custom_two`/`custom_four` require source-ID order for their own descending-ID query. Legacy query ordering must stay unchanged. A guarded repair rewrote only 13 new clone rows on two instances in one transaction; a second dry run reported zero changes. That page's visible text again matched the October baseline. Duplication uses a separate helper without explicit source order; equal-order duplication/shared-copy fidelity remains a regression-test gap, not a claimed proven fix.

## Partial migration and continuation

The verified 2026-10-27 backup is outside the web tree:

`/home/igx7/.codex/visualizations/2026/10/27/01a17c31-7846-7222-8dcc-72ce7dfc5b2a/.private/expertsacademy_dev_pre_builder_20261027.sql`

Size: 2,810,411 bytes. SHA-256: `209835d2c8b279b7e3010b001260f19cb5f34040b39d7dbe86336de7a8585c6a`. It predates Builder schema/data changes and the owner's later test edit; restoring it now would discard those later edits. It does not back up media files. Take a fresh backup before future writes.

Remaining Legacy rows:

| Group | Count / identifiers |
| --- | --- |
| Public routes with a clean dry run | 9: `0DB1FA2/fa`, `45619A4/fa`, `45619A4/en`, `A4E328F/fa`, `A732C13/fa`, `C17FD92/fa`, `DC62BDC/fa`, `ECD8FCB/fa`, `FE5E126/fa` |
| Empty-data warnings | `31DFEA3/fa` (`slider_box`) and `38A3272/en` (`raw_page`) |
| Ambiguous conversion | `6D6AAB4/fa` (`ambiguous_companion_data`), `8BA88DC/fa` (`orphan_companion_marker`) |
| English rows with blank slug and zero design markers | 11: `0DB1FA2`, `31DFEA3`, `6D6AAB4`, `A4E328F`, `A732C13`, `AEB115B`, `C17FD92`, `D5392E3`, `DC62BDC`, `ECD8FCB`, `FE5E126` (all `/en`) |

Do not infer that all pages were migrated. The last October batch was applied, but its subsequent baseline comparison was interrupted. The November read-only checks establish current HTTP health, not bit-for-bit equivalence to the missing October baseline files. Those `/tmp` baseline/comparison files no longer exist. A fresh hash/count baseline is now stored in `PUBLIC_HTTP_2026-11-01.tsv`.

`convert_page.php --all` is read-only. `--install-schema` adds schema; `--page-code=... --locale=...` defaults to dry run; `--apply` converts one locale transactionally. Warnings do not automatically block application, so the operator must review them. `repair_clone_order.php` defaults to dry run and refuses edited/shared layouts or changed payloads. Never run it as a general repair on user edits. See [operations](SETUP_AND_OPERATIONS.md) and the updated [Builder report](CMS_PAGE_BUILDER_2026-10-27.md).

## Remaining project areas

Public `cat_site.php`, `class_site.php`, `course_site.php`, `lib_site.php`, `lib_view.php`, and registration paths can use `QueryExpert()`; no assignments for its secondary connection globals exist. Root member pages and `sys_panel2`, `sys_panel3`, and `company` need missing `include/`; member views also need missing `components/`. Contract spreadsheet imports need `include/SimpleXLSX.php`. Do not claim these flows work because syntax passes.

`sys_panel3/csv.php` **exists**; earlier documentation incorrectly called it absent. It exports work review/referee data rather than matching every link/action from `custom_export.php`, and has no local login gate. Other remaining source risks include interpolated SQL, MD5 login flows, unauthenticated full SQL exports/JSON/CSV paths, embedded secrets, legacy uploads/file-manager paths, member login bypass, and chat TLS/auth behavior. The CMS AJAX/session gates and exit-after-redirect fixes are present; distinguish those improvements from unresolved endpoints.

SMS, mail, chat, SQL Server, external login/LMS links, and CDN/widget resources can contact real services. Local DB isolation does not isolate these effects. No valid external submission was tested in November. Preserve optional backup/prototype files, empty historical contact records, and English FAQ wrapper HTML until the owner approves cleanup.

## Documentation tooling

`DUCS/tools/rebuild_indexes.py` rebuilds the file, PHP, action, table, and literal-include indexes. `export_component_registry.php` exports pure Registry definitions. `read_dev_inventory.php` queries only the configured development DB in a read-only transaction. `check_public_routes.py` reads page routes and performs GET-only checks, storing status/hash/count metadata rather than content. Commands and limits are in [setup and operations](SETUP_AND_OPERATIONS.md). No new application dependency or build system was added.
