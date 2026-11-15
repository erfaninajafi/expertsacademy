# HTTP routes and API handlers

**Phase 2 routes:** integrated component_studio.php, design_system.php and studio_leads.php under site_admin; authenticated studio_component_preview.php; additive resources on studio_api.php; public studio_lead.php. See PHASE2_DEVELOPER_SETUP.md for methods, contracts and capabilities. Existing public routing and SEO URLs are retained.

**Reviewed 2026-11-01.** Current dedicated Builder API details are in [Page Builder API](PAGE_BUILDER_API.md); the tables below describe the other source handlers and later access corrections. Current automated GET-only checks cover 41 CMS page routes and selected gates/assets. Other handlers are source contracts, not claims of authenticated runtime success.

A `do` value is commonly read from GET even when data is POSTed; unless a handler checks `REQUEST_METHOD`, that does not enforce a method. Responses remain a mix of HTML/text/JSON/redirects. Historical source line offsets have been removed here; use the regenerated [dispatch index](DISPATCH_ACTION_INDEX.tsv) and [PHP index](PHP_CODE_INDEX.tsv) for current navigation. Dynamic includes, missing dependencies and action-local authorization still require tracing.

## Apache friendly URLs

Rules are in `.htaccess`, in this order. `{lng}` and `{code}` are route segments; public code commonly expects `fa` or `en`.

| URL shape | Target and query | Purpose / caveat |
| --- | --- | --- |
| `/{lng}/` | `index.php?lng={lng}` | CMS-selected home page. |
| `/{lng}/{section}/` | `index.php?lng={lng}&section={section}` | CMS page by `tbl_categories_url`; appears after special routes. |
| `/{lng}/register/` and `/{lng}/register/{do}/` | `register.php?lng=...&do=...` | Legacy member registration/login/code confirmation. |
| `/{lng}/mypanel/...` | `panel.php?lng=...&do=...&id=...&code=...` | Member portal, with variants for 0–3 extra segments (`.htaccess`). Missing `include/` and `components/` block full verification. |
| `/{lng}/resume/{id}/` | `resume.php?lng=...&id=...` | Public/member resume display. |
| `/{lng}/course/` | `course_site.php?lng=...` | Existing target; course/secondary-database behavior still needs its dependencies. |
| `/{lng}/page/{code}/` | `page_site.php?lng=...&code=...` | Content slug lookup, then Legacy `tbl_pages` code fallback; an optional decorative slug is supported. |
| `/{lng}/class/{code}/{slug}/` | `class_site.php?lng=...&code=...` | Class detail; slug is decorative in the rewrite. |
| `/{lng}/lib/{code}/{slug}/` | `lib_view.php?lng=...&code=...` | Library/content detail. |
| `/{lng}/cat/...` | `cat_site.php` with `code`, `do`, and/or `page` | Category listing variants (`.htaccess`). Duplicate three-segment patterns in the matching category rules mean the first matching variant wins; at least one later `do` mapping is shadowed. |
| `/site_admin/` | `site_admin/index.php` | CMS home. Direct `/site_admin/*.php` paths also exist. |

`web.config` configures IIS FastCGI PHP/default documents but has no equivalent rewrite rules. Direct `.php` endpoints below do not require `.htaccess` rewrites. Some root references also target absent `search.php` (`course_site.php`) and `upload/` media.

## Public API and form handlers

### `chat-api.php`

`POST /chat-api.php` requires a JSON body with a nonempty `message`. It returns JSON `405` for another method, JSON `400` for a missing message, otherwise forwards a chat-completions request to an external service and echoes the upstream body (`chat-api.php`). The file does not add application authentication/rate limiting, contains a bearer credential, disables TLS peer verification, and does not visibly propagate cURL error/status handling. No token value is reproduced here.

### `ajax_site.php?do=...`

`ajax_site.php` uses the public bootstrap and emits `Access-Control-Allow-Origin: *`. It does not have a single global login gate. Branches in the source are:

| `do` | Input read | Result / source-observed access |
| --- | --- | --- |
| `registeritem` | GET `code`, `lng` | Uses `QueryExpert()` and registration tables to enroll the session member, then redirects to a course view. Checks `$_SESSION['userid']`; the secondary DB connection is unconfigured in this snapshot. |
| `registerlib` | GET `code`, `lng` | Registers member for content and redirects. Checks member session; also uses unconfigured `QueryExpert()`. |
| `contact` | POST `lng`, `section`, `kind`, `fld1`–`fld12`; optional multipart `file` | Rejects missing required fields, otherwise inserts into `tbl_form` and returns localized text. Optional upload goes to `upload_site/`. |
| `faq` | POST `lng`, `fld1`–`fld5` | Rejects missing required fields, otherwise inserts `tbl_form`; localized text. FAQ accordion and collaboration-form component remain separate. |
| `job` | POST `lng`, `fld1`–`fld5`; optional multipart `file` | Inserts `tbl_form`, returns localized text; optional upload to `upload_site/`. |
| `modal-video` | POST `id` | Returns video HTML or error text. |
| `product` | POST `val` | Returns product-specification HTML. |
| `getdata` | POST `cat`, `offset` | Returns HTML product cards for load-more UI. |

`contact`/`job` check filename extensions `jpg`, `jpeg`, `pdf`, `doc`, `docx`; no file-size or MIME validation was identified in those branches. Active AJAX requests in `asset_site/js/script_fa.js` now resolve on the current host/project path; external navigation/service links still exist elsewhere.

### Legacy root `ajax.php?do=...`

The root `ajax.php` loads missing `includer.php` dependencies. Its global login redirect is commented out (`ajax.php`); access checks vary by action.

| `do` | Input read | Result / behavior |
| --- | --- | --- |
| `libraryfilter` | POST `price`, `lng`, `cat[]` | Redirect to member library filter. |
| `rating` | POST `ratingPoints`, `postID` | Validates inputs and prior vote, writes `tbl_rateuser`/`tbl_rating` using the corrected aggregate flow; plain `ok`/error text. Concurrent-vote consistency remains unverified. |
| `libraryfiltersite` | POST `price`, `lng`, `cat[]` | Redirect to public library filter. |
| `mohtava` | GET `code`, `lng` | Attempts content registration, then redirect; reads user ID without a branch-level login check. |
| `interview` | POST `contract`, `code`, `lng`, dynamic answer fields | Assessment/interview HTML fragment. |
| `skill` | POST `contract`, `title`, `lng`, `skill` | Competency/plan HTML fragment. |
| `descriptionindex` | POST `val` | Plan detail HTML. |
| `timeline` | POST `contract`, `skill`, `lng` | Timeline HTML. |
| `description` | POST `val` | Description HTML. |
| `lesson` | POST `code` | Registration/course mutation and redirect; checks member session. |
| `setting` | POST `color`, `fluid`, `navbarStyle` | Updates member UI preferences; redirects when no member session. |
| `exam` | POST `submit` plus dynamic answer fields including `code` | Stores answer JSON in `tbl_exam` then redirects; source can write an empty user ID when unauthenticated. |
| `updateresume` | Session member ID | Invokes resume update and returns text; no explicit guard in branch. |

### `register.php` and member pages

The friendly `/{lng}/register/{do}/` route covers `register` and `login` views, `reg` form POST (`register.php`), `sendcode` form POST, `checkcode` view, and `checklogin` form POST. The flow uses `tbl_member` and session keys including `mobile`, `email`, `userid`, and `username`; a successful code check clears the stored code and redirects into the member portal. The source contains a hard-coded mobile bypass in part of this flow; its value is intentionally omitted. `panel.php` requires `userid` and contains many `do` views, but finally delegates to absent `components/$do.php` (`panel.php`). The `PHP_CODE_INDEX.tsv` and `DISPATCH_ACTION_INDEX.tsv` list all static page action names without claiming that missing component bodies exist.

## CMS AJAX endpoint

`site_admin/ajax.php` now checks `siteadmin_login` itself, returns 403 and exits before its configuration/handler work when unauthenticated. Rendered CMS pages also have their own header guard. The endpoint branches are:

| `do` | Inputs | Response / effect |
| --- | --- | --- |
| `select2ajax` | GET `section`, `searchTerm` (minimum two chars) | JSON tag `id`/`text` suggestions. |
| `itemUpdate` | POST `item` encoded as ID and `hot`, `limit`, or `hide` flag | Toggles `tbl_product` field; text response. |
| `checkname` | POST `title`, `code`, `lng`, `section` | Product URL-name uniqueness HTML. |
| `checknamecat` | Same | Category URL-name uniqueness HTML. |
| `state` | POST `code` | HTML `<option>` elements. |
| `city` | POST `code` | HTML `<option>` elements. |

An independent POST `type=cat_title|user_name|agahi_title` with `keyword` returns JSON suggestion arrays (`site_admin/ajax.php`) without `do`.

## Operations AJAX endpoints

`sys_panel2/ajax.php`, `sys_panel3/ajax.php`, and `company/ajax.php` now exit after the missing-`id_login` login redirect. Their missing `include/` dependencies are loaded earlier, so a clean unauthenticated response is not established for these incomplete legacy applications. Action-specific authorization still needs review; the header/sidebar gate is not a substitute.

### `sys_panel2/ajax.php?do=...`

| `do` | Main POST input | Response / effect |
| --- | --- | --- |
| `checkcodefile`  | `title`, `code` | Uniqueness HTML. |
| `planmodel`  | `val` encoded model/user/contract/skill | Plan form HTML. |
| `checkmentor`  | `mentor`, `contract` | Mentor/list HTML. |
| `selectcontent`  | `title`, `ghaleb`, `department`, `creator`, `producer` | Content table HTML. |
| `shayestegi`  | `contract`, `skill`, `lng`, `user` | Competency HTML. |
| `timeline`  | Same | Timeline HTML. |
| `description`  | `val` | Plan detail HTML. |
| `desinterview`  | `val` | Interview detail/attachment HTML. |
| `ajaxchecklist`  | `contract`, `lng` | Checklist HTML. |
| `contract`  | `contract`, `lng` | Contract summary HTML. |
| `ajaxkarfarmamenti`  | `contract`, `lng`, `skill` | Member table HTML. |
| `karfarma`  | `contract`, `lng` | Employer summary HTML. |
| `setting`  | `color`, `fluid`, `navbarStyle` | Updates user UI preferences. |
| `planstatus`  | `val` encoded status/ID | Updates plan status; text. |
| `contractstatus`  | `val` encoded status/ID | Updates contract status; text. |
| `documentstatus`  | `val` encoded status/ID | Updates document status; text. |
| `subcat`  | `code` | Category `<option>` elements. |
| `checkbox`  | `dataName=register\|checklist`, `dataId` | Toggles confirmation; text. |
| `registerupdate`  | `id` | Updates registration/class count; `1`. |
| `mentor`  | `status` JSON array, `userid`, `contract` | Upserts/deletes member-skill assignments; text. |
| `addmentor`  | `mentor`, `userid`, `contract` | Upserts project mentor assignment; text. |
| `specialmohtava`  | `val` encoded prefix/code | Toggles special content; text. |
| `changebox`  | `val` | Toggles work box; `0`/`1`. |
| `userupdate`  | `id` | Toggles member image flag; `0`/`1`. |

Two independent operations are outside the `do` table: GET `role` switches the session role and redirects (`sys_panel2/ajax.php`), including a member impersonation branch; POST `type=cat_title` plus `keyword` returns JSON suggestions.

### `company/ajax.php` and `sys_panel3/ajax.php`

These endpoints have parallel action sets in separate source files and page styles. Use the generated action index to locate each current branch.

| `do` | Company / panel 3 source | Input and effect |
| --- | --- | --- |
| `planmodel` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `val` encoded model/user/contract/skill; form HTML. |
| `planstatus` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `val` status/ID; update and text. |
| `contractstatus` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `val`; update and text. |
| `documentstatus` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `val`; update and text. |
| `subcat` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `code`; HTML options. |
| `checkbox` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `dataName=register`, `dataId`; toggle and `0`/`1`. |
| `registerupdate` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `id`; registration/class count update and `1`. |
| `mentor` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `status` JSON array, `userid`, `contract`; member-skill upsert and text. |
| `changebox` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `val`; work box toggle and `0`/`1`. |
| `userupdate` | `company/ajax.php` / `sys_panel3/ajax.php` | POST `id`; member image toggle and `0`/`1`. |

Both have independent POST `type=cat_title`, `keyword` JSON suggestion paths (`company/ajax.php`, `sys_panel3/ajax.php`). `company/footer.inc.php` and `sys_panel3/footer.inc.php` call local `do=select2ajax`, but neither local handler implements that action.

## JSON, CSV, and full-database exports

| Endpoint | Inputs / output | Source access observation |
| --- | --- | --- |
| `sys_panel2/json.php?do=project&number=...` | Contract/project JSON with code, employer, dates, manager, skill, status and progress counters (`json.php`). Writes a `tbl_json` snapshot. | Includes `sys_panel2/includer.php`, which has no login check; no guard in endpoint. |
| `sys_panel2/json.php?do=listfaragir&number=...` | JSON member array with name, national code, mobile, skill, mentor, certificate and progress. Writes a `tbl_json` snapshot. | Same. The query compares contract code with `!=`, which may be unintended. |
| `sys_panel2/csv.php?do=report1\|report2\|report3\|report4\|report5\|report6\|mohtava` | Attachment headers and CSV report (`csv.php`). Some branches use GET `project`, `status`, dates, employer/skill/admin; content reports use content-title/creator/department/status fields. | No endpoint/session guard beyond the unguarded includer. See source for exact branch columns and filters. |
| `sys_panel2/export.php?do=register&historyclass=...` and panel 3 equivalent | Registration CSV (`export.php`). | Checks `id_login`, redirects and exits; its bootstrap still needs missing dependencies. |
| `sys_panel2/export.php?do=member` and panel 3 equivalent | Member CSV (`export.php`). | Same redirect/exit guard. |
| `sys_panel3/csv.php?do=review&section=...&lng=...` | CSV from `tbl_work`/`tbl_member`; review branch or referee-data fallback, not panel 2 report actions. | No local endpoint login gate; missing Legacy includes also apply. |
| `sys_panel2/sql.php`, `sys_panel3/sql.php` | Unconditional full SQL export built from `SHOW TABLES`, `SHOW CREATE TABLE`, and `SELECT *`. | Hard-coded DB credentials in files and no source login guard. Values deliberately omitted from this reference. |

`sys_panel2/custom_export.php` is an HTML form page that links to `csv.php`, rather than generating CSV itself. `sys_panel3/csv.php` exists but implements work review/referee export, so its `custom_export.php` links are not all supported by that contract. `contact_export` and `newsletter_export` links in both versions are not implemented by the panel 2 CSV file either.

## Bundled TinyMCE file-manager HTTP endpoints

Four copies exist beneath `company/asset/editor/tinymce/plugins/filemanager/`, `site_admin/asset/editor/tinymce/plugins/filemanager/`, `sys_panel2/assets/editor/tinymce/plugins/filemanager/`, and `sys_panel3/asset/editor/tinymce/plugins/filemanager/`. Each supplies `dialog.php` (browser UI), `upload.php` (multipart POST `file`, `path`, `path_thumb`), `create_folder.php`, `delete_file.php`, `delete_folder.php` (POST path inputs), and `force_download.php` (POST `path`, `name`, octet-stream response). Settings and allowed extensions are in each `config.php`.

The file-manager session `verify` flag is self-granted by the dialog, so it is not sufficient authorization. The CMS copy now requires both `siteadmin_login` and the expected flag and returns 403/exit on failure. The other three copies still use the older checks/redirect behavior. Canonical path and upload handling remain separate concerns even behind the improved CMS gate. The site-admin copy points to `/uploadfile_site/`; the other copies point to absent `/uploadfile/`. Upload code uses the submitted filename/path before extension checks, and download path checks do not canonically validate the path. See [Security observations](SECURITY_OBSERVATIONS.md).

## Coverage limits

Many other PHP page files accept `?do=` for CRUD forms and views. Their static action names, input keys, session keys, includes, and SQL table references are indexed per file in `PHP_CODE_INDEX.tsv` and `DISPATCH_ACTION_INDEX.tsv`. This document describes the dedicated network handlers and important form flows in detail; it does not invent request/response contracts for legacy pages whose dependencies or dynamically included bodies are absent. The current development database and selected local public/authentication responses were read during November review. This does not establish every handler's business behavior or the behavior of an external deployment.

## New CMS routes and current checks

- `GET site_admin/page_builder.php?code=...`: authenticated editor shell; unauthenticated response redirects to login.
- `GET/POST site_admin/page_builder_api.php`: JSON state/actions using `action`, CMS session and POST CSRF. See [the full API contract](PAGE_BUILDER_API.md).
- `GET site_admin/form_contact.php?q=...&status=all|pending|reviewed&page=...&view=...`: authenticated general-contact inbox. `POST action=mark_reviewed`, integer `id`, and `csrf_token` marks only matching general-contact records reviewed and redirects with 303. No delete action is provided.
- CLI migration, backup, repair, and test PHP files are not supported web APIs. Their guards and commands are documented in [operations](SETUP_AND_OPERATIONS.md).

November read-only HTTP results are in [PUBLIC_HTTP_2026-11-01.tsv](PUBLIC_HTTP_2026-11-01.tsv). No valid contact/job/FAQ submission, export, registration, payment, SMS/email/chat, or SQL Server call was sent in that review. An unauthenticated denial is evidence of that gate only, not full action authorization.
