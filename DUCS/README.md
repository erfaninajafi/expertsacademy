# Experts Academy project documentation

**Phase 2 verified and installed, 2026-11-01:** the integrated Design System, Component Studio and Page Studio now include 60 new component types and 240 composition variants. Start with [Phase 2 architecture](PHASE2_DESIGN_STUDIO.md), [developer setup](PHASE2_DEVELOPER_SETUP.md), [backup/rollback](PHASE2_BACKUP_ROLLBACK.md), [component docs](PHASE2_COMPONENTS.md), and final machine-readable test/deployment evidence. Earlier Phase 1 and legacy snapshots below are historical.

**Historical Phase 1 implementation, 2026-11-01.** Start with [the new CMS foundation report](PHASE1_CMS_FOUNDATION_2026-11-01.md), [developer/API setup](PHASE1_DEVELOPER_SETUP.md) and [verified backup/rollback](PHASE1_BACKUP_ROLLBACK.md). The new independent Studio is integrated in `site_admin/page_studio.php`; development now has 35 tables, including eight additive Studio tables. All 27 original tables and 48 measured routes are preserved. One unbound unpublished sample is available; no existing page has been switched to Studio.

The earlier [review report](PROJECT_REVIEW_2026-11-01.md), indexes and snapshots below describe the **pre-Phase-1** state. They remain useful for retained legacy code; use the [Phase 1 file manifest](PHASE1_FILES.tsv) and [test results](PHASE1_TEST_RESULTS.json) for the new implementation. Source facts, current checks and historical test results are identified separately.

## Documentation map

| Document | Purpose |
| --- | --- |
| [Project review](PROJECT_REVIEW_2026-11-01.md) | Gaps found in earlier documentation, current checks, partial migration, and remaining issues. |
| [AI project handoff](AI_PROJECT_HANDOFF.md) | Current source, database, owner constraints, working areas, and continuation instructions. |
| [Architecture](ARCHITECTURE.md) | Public renderer, component instances, CMS, member/operations/company applications, and integrations. |
| [Routes and APIs](ROUTES_AND_APIS.md) | Public rewrites, legacy action handlers, exports, and current access corrections. |
| [Page Builder API](PAGE_BUILDER_API.md) | GET state, POST actions, revision/CSRF rules, content formats, upload limits, and errors. |
| [Database](DATABASE.md) | Live development schema versus historical dump, data relationships, and schema gaps. |
| [SQL schema reference](DATABASE_SCHEMA.md) | Historical 25-table dump DDL plus the additive Builder schema reference. |
| [Development schema snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json) | Read-only column/engine/count metadata from the configured development database; no record content or credentials. |
| [Setup and operations](SETUP_AND_OPERATIONS.md) | XAMPP conflict diagnosis, migration/backup/repair commands, test requirements, and rollback boundaries. |
| [Local XAMPP switching](LOCAL_XAMPP_SWITCHING.md) | Active/original isolation and historical folder-switch procedure. |
| [Security observations](SECURITY_OBSERVATIONS.md) | Remaining source risks and the scope of later access-control improvements. |
| [File guide](FILE_GUIDE.md) | Responsibilities of application files and bundled trees. |
| [Inventory summary](INVENTORY_SUMMARY.md) | Current readable counts, extraction limits, and regeneration command. |
| [File manifest](FILE_MANIFEST.tsv) | Every readable project file outside `.git/` and `DUCS/`. |
| [PHP code index](PHP_CODE_INDEX.tsv) | All 453 PHP files: includes, inputs, sessions, SQL table tokens, actions, and named functions. |
| [Dispatch action index](DISPATCH_ACTION_INDEX.tsv) | Current lexical `do` comparisons/assignments with source lines. |
| [Table references](TABLE_REFERENCES.tsv) | Static SQL table-to-file references. |
| [Literal include audit](LITERAL_INCLUDE_AUDIT.tsv) | Complete quoted include paths resolved relative to the source file; missing entries are candidates requiring runtime tracing. |
| [Component registry export](COMPONENT_REGISTRY.tsv) | 36 definitions / 39 marker variants, storage, templates, fields, companions, and locale policy. |
| [PHP lint](PHP_LINT_2026-11-01.tsv), [HTTP checks](PUBLIC_HTTP_2026-11-01.tsv) | Current per-file and per-route evidence; HTTP output content is not stored. |
| [Review check summary](REVIEW_CHECKS_2026-11-01.json) | Machine-readable test scope, current results, historical results, and checks not rerun. |
| [CMS Builder implementation report](CMS_PAGE_BUILDER_2026-10-27.md) | A–J delivery report, updated with actual migration and order repair. |
| [CMS interface updates](CMS_UI_UPDATES_2026-10-26.md), [earlier improvements](IMPROVEMENTS_2026-10-26.md) | Historical change records; their verification statements describe those dates. |

## Verified local state before Phase 1

- Active source: `/opt/lampp/htdocs/expertsacademy`; configured database: `expertsacademy_dev`. The original source `/opt/lampp/htdocs/expertsacademy-original` and database `expertsacademy` are protected reference copies.
- Inventory: **7,565 files**, **453 PHP files**, **72 statically referenced table names**; no traversal error. The previously inaccessible `asset/img/` is now readable. Git tracks zero project files.
- Live development database: **27 tables**; 25 historical tables plus two Builder tables. There are **52 CMS page/locale rows**; **28 use Builder**, **24 remain Legacy**. Migration is partial.
- Current checks: all **453 PHP files** linted successfully, **6 application JavaScript files** passed syntax checks, **9 Builder unit checks** passed, and **48 read-only local HTTP checks** matched expected statuses without PHP error markers. This is not full authenticated testing of every panel.
- The separate system `mysql` service again occupied port 3306 on 2026-11-01. The owner stopped it and started XAMPP MySQL; the configured development connection then succeeded. See [operations](SETUP_AND_OPERATIONS.md).

## Reading the evidence

`site.sql` remains a data-bearing historical 25-table dump, not the current schema or a generic fixture. No credentials, account records, form content, or INSERT payloads are reproduced here. The new JSON snapshot contains schema metadata and aggregate counts only.

The TSV indexes are lexical navigation aids. Comments and literals can produce false positives; dynamic SQL/includes can be missed. The include audit assumes the file directory as its base, while actual PHP working directories can differ. Passing lint and HTTP smoke checks does not establish authorization, visual parity, or complete business behavior.

The missing legacy `include/` and root `components/` directories, unconfigured `QueryExpert()` connection, legacy SQL/schema gaps, and remaining export/upload/security risks still limit member, learning, operations, and company flows. Consult the relevant source before changing them.
