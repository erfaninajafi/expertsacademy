# Database and data model

**Phase 2 schema:** migration 002 adds tbl_studio_component_proposals, tbl_studio_component_lifecycle and tbl_studio_leads (38 total development tables after migration), plus immutable metadata/theme/template versions. See PHASE2_DEPLOYMENT.json and PHASE2_PRESERVATION.json for measured deployment counts and row preservation. Actual XAMPP is MariaDB 10.4.32; MySQL 8.4.11 is a separately tested target. Historical snapshots below predate this migration.

## Phase 1 database update — 2026-11-01

The configured development database remains MariaDB 10.4.32. Eight additive `tbl_studio_*` tables are now installed, bringing the total to 35; all 27 original tables match their verified pre-change content hashes. Studio migrations/revisions/tokens/templates/media metadata and MySQL 8.4 compatibility testing are described in [the Phase 1 report](PHASE1_CMS_FOUNDATION_2026-11-01.md) and [SQL migration](../include_site/studio/database/001_foundation.up.sql). The schema snapshots and counts below predate this addition. See [backup/rollback](PHASE1_BACKUP_ROLLBACK.md) before any schema reversal.

**Current read-only review: 2026-11-01.** The configured `expertsacademy_dev` has **27 tables**: the 25 historical tables below plus `tbl_page_builder_layouts` and `tbl_page_component_instances`. There are 52 CMS page/locale rows, 28 active Builder layouts (all independent), and 24 Legacy locales. Current columns/engines/aggregate counts are in [the development snapshot](DEVELOPMENT_SCHEMA_2026-11-01.json); inactive migration issues are in the [handoff](AI_PROJECT_HANDOFF.md). The original database was not queried or changed for this review.

The live review found the same column-name sets for the 25 shared tables as `site.sql`. That is not evidence that types, indexes, or data are identical. The two Builder tables are additive and have their own executable SQL. The historical dump reference below must not be mistaken for the full current schema.

## What is actually available

`site.sql:1-5,948` is a MySQL 8.0.31 dump labelled `expertnewsite`, completed on 2025-11-30. It contains **25 `CREATE TABLE` statements and an `INSERT` statement for every table**. It is a data-bearing snapshot, not a set of migrations. The [schema reference](DATABASE_SCHEMA.md) records every dumped column, SQL type, default, and declared key without repeating its data rows. The dump declares **no foreign keys**. `tbl_menu` uses MyISAM; the other dumped tables use InnoDB. `tbl_seo` and `tbl_setting` have no declared primary key.

Current static SQL inspection of all 453 PHP files finds **72 distinct table names**: the 25 dumped tables, two Builder tables defined by migration SQL, and 45 names without DDL in this repository or tables in the current development schema. The `TABLE_REFERENCES.tsv` file maps those names to PHP files. It detects literal SQL table tokens after `FROM`, `JOIN`, `UPDATE`, `INTO`, and `TABLE`; dynamically constructed names may not be listed.

## Connection and query model

| Area | Source | Connection behavior |
| --- | --- | --- |
| Public site and CMS | `include_site/config.php:3-6`, `include_site/function.php:3-13` | Hard-coded primary MySQL settings. `Query($sql)` opens `mysqli`, sets charset to `utf8`, runs the SQL string, and returns the result. |
| Public course/library and registration | `include_site/function.php:15-25` | `QueryExpert($sql)` expects four `$db*Expert` globals. No assignments were found in this snapshot, so connection details cannot be established. |
| Legacy member/operations/company areas | `includer.php`, `sys_panel2/includer.php`, `sys_panel3/includer.php`, `company/includer.php` | Include `include/config.php` and `include/function.php`, which are absent. Local `inc.php` files define additional MySQL helpers/settings, but source entrypoints examined do not load them in place of the missing dependencies. |
| Legacy contract import | `sys_panel2/contractold.php:43-90`, `sys_panel2/contract2.php:38-86` | Uses `sqlsrv_connect` and reads SQL Server contract cases into local `tbl_contract` fields. Credentials are embedded in source and deliberately omitted here. |

The Legacy `Query()` helpers execute raw SQL strings; many callers interpolate request values. The public helper opens a new connection per call and uses `utf8`. In contrast, `page_builder_api.php`, the converter, clone-order repair, and general contact inbox use prepared statements; Builder mutations use transactions and revision locking. The Builder connection uses `utf8mb4`, but retained content table charsets and Legacy reads still limit end-to-end character support. Do not apply the old "no prepared statements/transactions" observation to these new paths.

## Dumped tables

| Area | Tables and column counts | Purpose visible in source |
| --- | --- | --- |
| CMS content and navigation | `tbl_ads` (11), `tbl_categories` (17), `tbl_content` (28), `tbl_logo` (4), `tbl_menu` (12), `tbl_menufooter` (7), `tbl_pages` (6), `tbl_section` (13), `tbl_textsection` (13), `tbl_setting` (19), `tbl_seo` (2) | Page structure, text/list components, navigation, site settings, ads, and metadata. |
| Learning and enrollment | `tbl_certificate` (4), `tbl_class` (29), `tbl_comment` (7), `tbl_course` (22), `tbl_courseparent` (22), `tbl_question` (11), `tbl_rateuser` (4), `tbl_rating` (7), `tbl_register` (31), `tbl_teacher` (34), `tbl_view` (5), `tbl_workshop` (29) | Courses/classes, units, teachers, questions, registrations, ratings, views, and certificates. |
| Administration and forms | `tbl_form` (21), `tbl_user` (15) | Submitted contact/job/FAQ form records and CMS/legacy user records. |

DDL start locations in `site.sql`: `tbl_ads:25`, `tbl_categories:58`, `tbl_certificate:97`, `tbl_class:123`, `tbl_comment:174`, `tbl_content:203`, `tbl_course:253`, `tbl_courseparent:297`, `tbl_form:341`, `tbl_logo:384`, `tbl_menu:410`, `tbl_menufooter:444`, `tbl_pages:473`, `tbl_question:501`, `tbl_rateuser:534`, `tbl_rating:560`, `tbl_register:589`, `tbl_section:642`, `tbl_seo:677`, `tbl_setting:700`, `tbl_teacher:740`, `tbl_textsection:796`, `tbl_user:831`, `tbl_view:868`, `tbl_workshop:895`.

### CMS page composition

`index.php` selects the type-4 category by locale and slug/home flag. `include_site/page_renderer.php` then reads the exact page/locale layout. Legacy mode uses newline-delimited `tbl_categories_design` markers and page-code content parents. Builder mode uses ordered enabled/non-deleted UUID instances and content parents equal to those UUIDs. Both paths resolve only Registry-owned templates. Text locale is `tbl_textsection_lng`; content locale is `tbl_content_fld1`. These remain string relations, not foreign keys.

### Additive Builder tables

`site_admin/migrations/20261027_page_builder.sql` creates two InnoDB tables and repeatably adds `structure_mode` to an earlier draft layout table. [The schema reference](DATABASE_SCHEMA.md#builder-schema-added-on-2026-10-27) summarizes their columns; the SQL file is authoritative for exact defaults/collations/index definitions.

| Table | Purpose / keys |
| --- | --- |
| `tbl_page_builder_layouts` | Primary key `(page_code, locale)`; `mode`, `structure_mode`, revision and timestamps. Each locale switches rendering explicitly. |
| `tbl_page_component_instances` | UUID primary key; page, marker, type, locale scope, position, enabled/schema version, soft-delete and timestamps. Composite lookup index covers page/locale/deleted/order/ID. |

Content is copied into existing text/content tables rather than hidden in a JSON blob. Clone rows get new codes; Legacy rows/design are retained. Deleted instances retain their rows; removed list items use a `removed:UUID` parent. No foreign key, enum/check constraint for allowed types/modes/locales, or unique position constraint is added; API/Registry validation enforces these rules. `schema_version=1` is stored, but a generic future content upgrader is not implemented. Revision detects concurrency, not historical content versions.

Independent layouts can explicitly share structure only when their ordered definitions/states match. The shared instances use one UUID sequence with separate locale content. This is not a global reusable-content library and has no unshare action. Details are in [the API reference](PAGE_BUILDER_API.md).

## Tables referenced in PHP but absent from the dump

The following **45 names have no `CREATE TABLE` statement in `site.sql`**. Their columns and types cannot be defined from this repository; the PHP/table indexes locate table usage per file; inspect those sources for the observed column names. This list does not imply that the tables are absent from any live database.

| Domain inferred from use | Code-only table names |
| --- | --- |
| Members, mentoring, and plans | `tbl_member`, `tbl_memberskill`, `tbl_memberquestion`, `tbl_menti`, `tbl_projectmember`, `tbl_plan`, `tbl_interview`, `tbl_review`, `tbl_resume`, `tbl_resumedata`, `tbl_work`, `tbl_opinion`, `tbl_opinionend`, `tbl_evaluation` |
| Contracts, finance, and documents | `tbl_contract`, `tbl_attach`, `tbl_bankfile`, `tbl_document`, `tbl_invoice`, `tbl_number`, `tbl_code`, `tbl_json` |
| Learning/content extensions | `tbl_mohtava`, `tbl_mohtavaregister`, `tbl_exam`, `tbl_emtehan`, `tbl_questionpublic`, `tbl_qteacher`, `tbl_film`, `tbl_slide`, `tbl_checklist`, `tbl_checklistdefault`, `tbl_checklistreport`, `tbl_festcourse`, `tbl_festival`, `tbl_referee`, `tbl_registeruserlist` |
| Communication, catalog, and other | `tbl_agahi`, `tbl_coachmessage`, `tbl_skillmessage`, `tbl_message`, `tbl_newsletter`, `tbl_product`, `tbl_tag`, `tbl_menu_footer` |

`tbl_menu_footer` in code is spelled differently from dumped `tbl_menufooter`; do not assume they are interchangeable. The dump also lacks fields referenced in current PHP, such as `tbl_course_lng` (`panel.php:71`) and `tbl_user_role` (`sys_panel2/header.inc.php:14`). The dumped DDL is therefore a historical or partial schema for these code paths.

## Inferred relationships

These relationships are inferred from joins, filters, and assignment code. **None is enforced by a foreign key in the dump.**

| Relationship | Evidence and qualification |
| --- | --- |
| Class/workshop category | `tbl_class.tbl_class_cat` and workshop category values are compared to `tbl_categories.tbl_categories_id` (`course_site.php:150,212`). |
| Class → course group → unit | `tbl_courseparent.tbl_courseparent_class` stores a `tbl_class` code; `tbl_course.tbl_course_class` stores a course-parent code (`panel.php:71-82`, `sys_panel2/register.php:435-442`). |
| Enrollment | `tbl_register.tbl_register_user` identifies a member; `tbl_register.tbl_register_class` stores a class, course, or workshop code depending on workflow (`sys_panel2/class.php:162`, `workshop.php:76`, `register.php:224`). This is a polymorphic application reference. |
| Navigation tree | `tbl_menu.tbl_menu_parentid` is compared to `tbl_menu.tbl_menu_code` (`header_site.inc.php:112-118`); footer navigation uses a similar pattern (`footer_site.inc.php:6-10`). |
| Mentoring assignment | `tbl_projectmember_user` is compared to `tbl_member_id`, with contract/mentor filters (`sys_panel2/report4.php:74-89`, `faragir.php:620-649`). |
| Member competencies and plans | `tbl_memberskill_user` references a member; `tbl_memberskill_contract`, `tbl_plan_contract`, and `tbl_plan_menti` link assessments/plans to contracts and members (`sys_panel2/contract.php:494-506`, `faragir.php:480-503`). |
| CMS section content | `tbl_textsection_parent` stores the Legacy page/category code or Builder instance UUID; section marker and locale are separate fields. `tbl_content_parent` follows the same two-model convention. |
| Questions | `tbl_question_parent` is filtered by course/parent criteria (`site_admin/question.php:31,180`). |

`tbl_categories_parentid` is hierarchical, but source uses both numeric category IDs (`site_admin/category_manage.php:133`) and category codes (`sys_panel2/category_manage.php:309`) when comparing it. A single reliable key target cannot be asserted from this snapshot.

## Data and privacy handling

`site.sql` includes actual rows, including identity and password-related fields in user/teacher tables. The schema documentation intentionally omits INSERT payloads. The source also contains database credentials; this documentation gives only configuration key names and source locations. Treat database exports in `sys_panel2/sql.php` and `sys_panel3/sql.php` as sensitive server-side functionality; see [Security observations](SECURITY_OBSERVATIONS.md).
