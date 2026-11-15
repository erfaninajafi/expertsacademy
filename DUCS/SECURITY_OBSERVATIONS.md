# Current security observations

**Phase 2 scope:** new Studio code uses strict typed schemas, escaped PHP output, reviewed implementations, existing CMS capability adapters, CSRF and private backups. New public form records are publisher-only; no external messages are sent. See PHASE2_DESIGN_STUDIO.md for measured checks and limits. Legacy observations below remain applicable to their original code paths.

## Phase 1 security update — 2026-11-01

The new independent Studio uses fresh CMS account/role checks, CSRF, strict versioned schemas, prepared PDO queries and escaped PHP rendering. Its backend/editor source directories deny HTTP access. Apache checks found existing `.git/config` and `site.sql` returned 200; root rules now deny hidden metadata and SQL exports, verified as 403. New backups and test fixtures are outside the web root. These changes do not resolve the retained login/password/export/upload risks listed below. See [Phase 1 boundaries](PHASE1_CMS_FOUNDATION_2026-11-01.md).

**Source-reviewed 2026-11-01.** This document distinguishes improvements from remaining source risks. It is not an exploit report or a claim that every endpoint is reachable in a complete production deployment. Missing Legacy dependencies can stop handlers before their business code. Credential values, account/form records, and the member bypass value are omitted. No sensitive export, valid external submission, or exploit attempt was executed.

## Access-control state

| Area / endpoint | Current source observation | Runtime scope |
| --- | --- | --- |
| `site_admin/header.inc.php`, `page_builder.php` | CMS session gate and exit after login redirect. | Builder shell unauthenticated redirect verified. CMS directory/direct index also returned a login redirect in supplementary checks. |
| `site_admin/ajax.php` | Own `siteadmin_login` check, 403 and exit before handler includes. | Unauthenticated 403 verified. This fixes the older report's missing-gate observation. |
| `site_admin/page_builder_api.php` | Own CMS-session gate; POST CSRF; prepared SQL, revision locking, transactions, field/instance/page/locale validation. | Unauthenticated 403 and nine unit checks passed; authenticated action tests were historical isolated tests. All CMS admins share access; there is no separate per-user page ACL. |
| `site_admin/form_contact.php` | CMS session; prepared read/status queries, escaped details, CSRF-protected POST-only reviewed update. | Current source checked; a valid status write was not performed in November. |
| CMS TinyMCE file manager | Requires CMS login plus expected `verify` flag; denies with 403/exit. | Unauthenticated config 403 verified. This improves auth but does not prove safe canonical-path/upload handling. |
| `sys_panel2/ajax.php`, `sys_panel3/ajax.php`, `company/ajax.php`, panel 2/3 `export.php` | Redirect then exit when `id_login` is absent. Bootstraps requiring missing `include/` load earlier. | Source only; action/role-specific authorization is not established by the global gate. |
| `sys_panel2/json.php`, `sys_panel2/csv.php`, `sys_panel3/csv.php` | No global local login gate. A role-dependent use of session values inside a CSV branch is not a global denial. | Not requested; exports can expose personal data and JSON paths also write snapshots. |
| `sys_panel2/sql.php`, `sys_panel3/sql.php` | Standalone full-database export logic with embedded credentials, no visible session gate. | Not requested or exercised. Treat as sensitive server-side code. |
| Other three TinyMCE file-manager copies | Dialog self-grants `verify`; older `id_login`/redirect behavior remains. | Source only; flag alone is not authentication. |

## Remaining risks and limitations

| Priority | Observation | Source evidence / boundary | Follow-up |
| --- | --- | --- | --- |
| Critical | Full SQL exports lack a local guard; data-bearing `site.sql` and embedded integration credentials remain in the web tree. | Panel 2/3 `sql.php`; `include_site/config.php`; SQL Server import files; `chat-api.php`; `site.sql`. No values reproduced here. | Restrict deployment access, move sensitive artifacts/configuration outside the served tree, and plan secret rotation. Do not invoke exports to test this finding. |
| High | Legacy JSON/CSV endpoints lack a global local authorization gate. | Files in the access matrix. `sys_panel3/csv.php` exists and returns review/referee data; its absence was an old documentation error. | Require endpoint authentication and action-specific role/resource authorization. |
| High | Many Legacy handlers interpolate SQL strings. `sqi()` stripping and pass-through `SafeInput()` do not provide prepared-SQL guarantees. | `include_site/function.php`, root/panel/CMS old handlers. New Builder and general-contact prepared paths are exceptions. | Migrate affected actions deliberately with ownership/type checks; do not assume the entire project gained the new API's controls. |
| High | Member login contains a hard-coded mobile bypass and externally triggered SMS behavior. | `register.php`; the value is deliberately omitted. Missing member dependencies limit runtime reconstruction. | Remove/replace through a controlled authentication change and test data, preserving account access. |
| High | Login/password paths still use MD5; some member-writing paths use `password_hash()`, but no `password_verify()` call was found in the current application scan. | CMS/company/panel login and account files indexed in `PHP_CODE_INDEX.tsv`. | Reconcile login/storage contracts and plan a compatible migration/reset flow. This is source evidence, not proof of every stored account format. |
| High | File-manager upload/download paths and client filenames require review even behind the improved CMS gate. | Bundled `upload.php`, `force_download.php`, create/delete actions and configs. Other copies point at missing `uploadfile/`. | Canonically confine paths, validate content before moving, and use generated names with role checks. |
| Medium | Standalone diagnostics expose PHP/server information. | `sys_panel2/test.php` and **`sys_panel2/contract2.php` both actively call `phpinfo()`**; the latter does so before its bootstrap. | Restrict/remove diagnostic execution in deployment through a separate source change. No diagnostic page was requested in this review. |
| Medium | Chat proxy lacks application auth/rate limiting and disables TLS peer verification. | `chat-api.php`; embedded bearer value omitted. | Secure credentials, verify TLS, handle upstream status/errors, and define access/rate limits. No chat request was sent. |
| Medium | Public AJAX sets wildcard CORS; valid contact/job uploads use extension checks without the new Builder route's MIME/size policy. | `ajax_site.php`. Empty required contact/FAQ requests are now rejected; this does not equal a complete upload/abuse policy. | Define per-route origin, request and upload validation. |
| Medium | Historical content can contain arbitrary Legacy HTML; migration copies it unchanged. New Builder saves sanitize a limited tag set. | `builder_migration.php`, `builderCleanHtml()`, component templates. | Review Legacy content/output in context. Sanitizer removes style/alignment/media attributes; document compatibility before expanding it. |
| Medium | State changes in older CRUD pages do not share one global CSRF/authorization mechanism. | Dedicated new Builder/contact protections coexist with old page/`do` handlers. | Audit concrete mutations rather than inferring protection from sidebar visibility or one protected API. |
| Medium | Detailed error settings and incomplete dependencies can disclose paths/configuration context. | Legacy/public diagnostic settings, `web.config`, missing `include/`, secondary `QueryExpert()`. | Use protected logs and generic production errors after completing the relevant configuration. |

## Builder-specific boundaries

The API checks that a UUID/content row belongs to the page/locale, but it does not implement a separate per-user page permission model. Rich-text sanitation and allowed filename/URL rules apply to new Builder saves, not all existing CMS paths or copied historical HTML. Image upload is MIME-checked (JPEG/PNG/WebP/GIF), capped at 8 MiB, and uses randomized names, but abandoned files have no cleanup job.

Schema mode/type/locale values and positions are application-validated, with no added FK/check/enum/unique-position constraints. Revision is a concurrency token, not an audit trail or recoverable content history. Copy/duplicate order for equal-position content remains a test gap in the separate `builderCloneData()` helper. It is not a demonstrated authorization exploit.

The new documentation tools expose no credentials or record content and enforce CLI use for PHP exports. Their schema/count/index output is operational metadata; a deployment should decide whether to serve `DUCS/` and internal tools at all. No server access policy was changed during documentation review.

## Validation limits

Current read-only checks verify public responses and selected unauthenticated denials. They do not verify authenticated role escalation, SQL injection, path traversal, upload persistence, concurrent abuse, or business authorization across all applications. The original source/database, real exports, valid form delivery, payments and external providers were not used for test writes. Historical isolated Builder tests are documented separately in the delivery report.
