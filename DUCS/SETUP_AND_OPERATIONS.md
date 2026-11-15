# Setup, configuration, and operations

**Phase 2 operations:** use [developer setup](PHASE2_DEVELOPER_SETUP.md) and [verified backup/rollback](PHASE2_BACKUP_ROLLBACK.md) for new Studio builds, isolated tests and migration boundaries. No automated destructive development rollback is provided.

## Phase 1 setup update — 2026-11-01

For the independent React/PHP Studio, use [developer setup/API/testing](PHASE1_DEVELOPER_SETUP.md) and [verified backup/rollback](PHASE1_BACKUP_ROLLBACK.md). The new editor mounts at `site_admin/page_studio.php`; migration 001 is already applied on development. Composer/npm manifests are scoped to the new module. The procedures below concern the retained application and earlier Builder, not Studio migrations.

**Verified 2026-11-01.** Active source is `/opt/lampp/htdocs/expertsacademy`; its configuration selects `expertsacademy_dev`. Protect the separate original source and `expertsacademy` database. The project needs Apache rewrites, PHP, mysqli, and a compatible MySQL/MariaDB schema; copied vendor assets require no npm/Composer build.

## Runtime and configuration

XAMPP PHP is 8.2.12 and the current development MariaDB connection reports 10.4.32. The public/CMS bootstrap loads `include_site/config.php`, `function.php`, translations, and `tbl_setting`. Configuration and embedded integration credentials must not be copied into reports. Builder also uses DOM/libxml and fileinfo for rich text/image validation. The earlier environment exposed mysqli, PDO MySQL, cURL, GD, mbstring, JSON, and SOAP; no `sqlsrv` driver was observed. Re-check modules on any new server.

The repository has no central framework router, Composer/npm manifest, Docker setup, or environment template. It now has dedicated Builder SQL migrations, guarded CLI commands, and tests under `site_admin/migrations/` and `site_admin/tests/`. They are not a general application migration framework. IIS `web.config` does not implement the Apache routes.

| Configuration | Purpose / limit |
| --- | --- |
| `include_site/config.php` | Active primary DB and local path/language settings. Never switch to the original DB to work around an error. |
| `include_site/function.php` | Legacy `Query()` opens a connection per call using `utf8`; secondary `QueryExpert()` globals are not configured. |
| `site_admin/page_builder_api.php` | Dedicated prepared-SQL `utf8mb4` connection for Builder. Existing content table charsets still govern storage. |
| Legacy `includer.php` files | Require missing root `include/` dependencies. |
| TinyMCE file-manager configs | CMS uses `uploadfile_site/`; other copies refer to absent `uploadfile/`. CMS auth was improved; canonical path/upload handling still needs review. |
| `.htaccess` | Friendly routes, including existing `course_site.php`; duplicate member/category rules remain. |

## XAMPP says MySQL started, but the site fails

This exact failure recurred on 2026-11-01: Apache returned 500, the XAMPP socket connection failed with 2002, TCP connected to another server and returned 1698, and `systemctl is-active mysql` reported active. The owner stopped system MySQL and started XAMPP MySQL; a connection through the project's actual configuration then succeeded.

Read-only diagnosis:

```bash
rtk proxy systemctl is-active mysql
rtk proxy ss -ltn '( sport = :3306 or sport = :80 )'
```

On this workstation, the owner resolved that conflict using:

```bash
rtk proxy sudo systemctl stop mysql
rtk proxy sudo /opt/lampp/lampp startmysql
```

These service commands affect the workstation. First confirm that its system MySQL can be stopped; do not change database settings or point the project at that separate server. The system service is enabled and can return after reboot. Automatic disabling was not performed. A socket pathname can be stale, and an unprivileged `lampp status` can misreport the PID file. A successful connection to the configured `expertsacademy_dev` is the useful check. Do not paste passwords into commands or chat; Linux sudo authentication belongs in the user's terminal.

## Database and backups

`site.sql` is a historical, data-bearing 25-table dump. Current development has 27 tables; PHP statically references 72 names. Importing the dump does not supply the absent legacy/secondary schemas. [The development snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json) records column names/types/keys, engines, aggregate counts, and migration state without application record content.

Before any future DB write, make a new development backup outside the web tree:

```bash
rtk proxy /opt/lampp/bin/php site_admin/migrations/backup_dev.php --output=/ABSOLUTE/PRIVATE/PATH/expertsacademy_dev_YYYYMMDD_HHMM.sql
```

The parent directory must already exist. The script accepts only the configured `expertsacademy_dev`, refuses an existing/public output path, creates mode-600 output, removes failed/short dumps, and prints file size/hash. It uses a logical table/data dump with a single-transaction option; nontransactional tables can still change concurrently. It does not capture uploads, application files, external services, or stored routines/events explicitly. Do not describe it as a full workstation backup.

The verified October pre-Builder dump is 2,810,411 bytes, SHA-256 `209835d2c8b279b7e3010b001260f19cb5f34040b39d7dbe86336de7a8585c6a`, in the private visualization directory recorded in the handoff. It predates later Builder and owner edits. Restoring it now would discard those later edits. The earlier pre-change source backup in `/tmp` is gone; the available source tar is a later snapshot.

## Builder migration

Run commands from the project root. Installation and conversion are writes and require the backup/review sequence. The following examples use placeholders; do not paste `PAGE_CODE` as real data.

```bash
rtk proxy /opt/lampp/bin/php site_admin/migrations/convert_page.php --install-schema
rtk proxy /opt/lampp/bin/php site_admin/migrations/convert_page.php --all
rtk proxy /opt/lampp/bin/php site_admin/migrations/convert_page.php --page-code=PAGE_CODE --locale=fa
rtk proxy /opt/lampp/bin/php site_admin/migrations/convert_page.php --page-code=PAGE_CODE --locale=fa --apply
```

`--all` cannot be combined with `--apply`; it audits every page/locale without activating it. Single-page conversion is transactional, copies Legacy content to UUID parents, and leaves original rows/design intact. Reapplying an active Builder locale does not duplicate it. `ok=true` can still include warnings; review empty-data warnings before activation. The default configured target must be `expertsacademy_dev`; a disposable `expertsacademy_builder_test_*` target additionally requires a `/tmp/` test socket through `EXACD_BUILDER_TEST_SOCKET`.

As of this review, 28 locales are Builder and 24 Legacy. Two ambiguous rows, two empty-data warnings, eleven blank English routes, and nine clean public candidates remain. Their exact codes are in the handoff and JSON snapshot. The current documentation review applied no conversions.

### Clone-order repair

```bash
rtk proxy /opt/lampp/bin/php site_admin/migrations/repair_clone_order.php --page-code=PAGE_CODE --locale=fa
```

Default is dry run. `--apply` replaces only new clone rows whose payloads match the Legacy source but whose sequence differs. It requires an independent Builder layout at revision 1 with enabled/non-deleted instances; edited/shared layouts and changed payloads are refused. The operation is transactional and a repeated dry run can confirm zero pending changes. It regenerates clone content codes for repaired rows. It does not restore user edits or repair the original database. In October it corrected two instances / 13 clone rows of `BEC40D0/fa`.

## Tests and review tools

No-DB unit check:

```bash
rtk proxy /opt/lampp/bin/php site_admin/tests/page_builder_unit.php
```

Integration tests deliberately mutate data and must run only on a disposable `expertsacademy_builder_test_*` database served through a separate socket under `/tmp`. The fixture needs the historical tables; the integration fixture also needs the Builder schema installed. The Legacy-without-schema test needs a separate fixture without those two tables. Neither test provisions or starts MariaDB itself, and they must not be pointed at development/original databases. Example after provisioning the separate fixture:

```bash
rtk proxy env EXACD_BUILDER_TEST_SOCKET=/tmp/expertsacademy-builder.sock EXACD_BUILDER_TEST_DATABASE=expertsacademy_builder_test_local /opt/lampp/bin/php site_admin/tests/page_builder_integration.php
rtk proxy env EXACD_BUILDER_TEST_SOCKET=/tmp/expertsacademy-builder.sock EXACD_BUILDER_TEST_DATABASE=expertsacademy_builder_test_noschema /opt/lampp/bin/php site_admin/tests/page_builder_legacy_no_schema.php
```

The October isolated runs passed 38 integration and two no-schema checks. They were not rerun against a new disposable server in this documentation review. November checks passed all 453 application/vendor PHP syntax checks, six selected application JS checks, nine unit checks, and 48 GET-only HTTP probes. These do not verify all valid form submissions or authenticated panel workflows.

Documentation regeneration:

```bash
rtk proxy python3 DUCS/tools/rebuild_indexes.py --date 2026-11-01
rtk proxy /opt/lampp/bin/php DUCS/tools/export_component_registry.php
rtk proxy /opt/lampp/bin/php DUCS/tools/read_dev_inventory.php > DUCS/DEVELOPMENT_SCHEMA_2026-11-01.json
rtk proxy python3 DUCS/tools/check_public_routes.py --output DUCS/PUBLIC_HTTP_2026-11-01.tsv
```

Use a fresh review date for later snapshots. The schema reader enforces the configured development database and a read-only transaction. The HTTP tool uses only local GET requests, skips blank non-home slugs, follows no redirects, and stores hashes/counts rather than response content. It does not validate the visual result or compare against a lost historical baseline.

## Known missing dependencies and operational boundaries

Root `include/`, member `components/`, `include/SimpleXLSX.php`, `search.php`, standalone-section header/footer includes, and old `assets/`, `upload/`, `uploadfile/` paths are absent. `course.php` is absent but is no longer the course rewrite target. `sys_panel3/csv.php` exists and has a different review/referee export contract; older documentation calling it missing was incorrect.

Keep uploads writable only where needed. Builder accepts MIME-checked JPEG/PNG/WebP/GIF up to 8 MiB and writes randomized filenames; uploading does not itself attach/save the filename to a component and abandoned uploads have no cleanup job. Legacy contact/job and file-manager upload controls differ.

CMS/public form flows may invoke real mail/SMS/chat services or external links. Do not test them as though DB cloning isolated those effects. Full authenticated panel, payment, external API, upload persistence, every responsive template, and form delivery were not exercised in November.

## Rollback

For a page/locale, changing the layout `mode` to `legacy` in a reviewed transaction returns public rendering to retained Legacy design/content. This stops showing UUID edits but does not erase them. A shared layout needs additional planning before restoring independent editing; there is no unshare UI. Restoring a whole DB dump is a separate destructive operation that must account for all edits since that dump. Never copy the protected original over the active source as an automatic rollback.
