# Desktop copy improvements (2026-10-26)

> Historical change record. Its counts and tests describe the date in this title. For the verified 2026-11-01 source, database, migration and test state, use [the current review](PROJECT_REVIEW_2026-11-01.md) and [handoff](AI_PROJECT_HANDOFF.md).

> Location update: the owner subsequently moved the improved Desktop copy to `/opt/lampp/htdocs/expertsacademy`; the earlier original is now `/opt/lampp/htdocs/expertsacademy-original`. See [CMS interface and FAQ updates](CMS_UI_UPDATES_2026-10-26.md) for later changes. The scope paragraph below describes where the first improvement pass was performed.

## Scope

This file describes changes made to `/home/igx7/Desktop/expertsacademy-improved-20261025`. The original checkout at `/opt/lampp/htdocs/expertsacademy` was not edited for this work. The copy is owned by the `igx7` account so it can be edited in VS Code without the root-owned source tree's permission prompts. The existing DUCS documents remain a source snapshot; this file records the later improvements.

## Implemented changes

| Area | Files | Effect |
| --- | --- | --- |
| Home page readability and missing sections | `index.php`, `404.php` | Replaced the long section dispatch chain with explicit text/content template maps. The 34 original component selections and 10 text-section markers were checked against the old code. Unknown or malformed sections now return HTTP 404 instead of rendering an empty page or dereferencing a missing row. |
| Routes and public page errors | `.htaccess`, `page_site.php`, `course_site.php` | Course URLs target the existing `course_site.php`; page URLs with an optional slug reach `page_site.php`; missing page records return a meaningful 404. Two links use the defined language variable. |
| Category conditions | `cat_site.php`, `lib_site.php`, `site_admin/misc.php`, `site_admin/misctwo.php`, `sys_panel2/misc.php`, `sys_panel3/misc.php` | Corrected assignment inside conditions to comparison. Category-manager checks no longer change their own setting. |
| FAQ presentation and behavior | `components_site/faq.php`, `components_site/aca.php`, `asset_site/css/style_fa.css`, `asset_site/css/style_en.css`, `asset_site/js/script_fa.js`, `include_site/lang/fa_lng.php`, `include_site/lang/en_lng.php` | Scoped card/accordion styling, readable question text, visible labels, responsive layout, accessible status feedback, and unique collapse IDs. FAQ submission now serializes only its own form, updates only its own status/button, and restores the button after completion. The API action and form field names remain the same; active AJAX requests now resolve on the current host and project path. Corrected two Persian FAQ heading typos and added a localized form heading. |
| Rich text alignment | `site_admin/footer.inc.php`, `sys_panel2/assets/js/custom_fa.js`, `sys_panel3/footer.inc.php`, `company/footer.inc.php` | Moved the existing left/center/right/justify controls to a dedicated second TinyMCE toolbar row in all four application editor configurations. The available controls and storage format were preserved. |
| Form language and input safety | `includer_site.php`, `ajax_site.php` | Limited language-file selection to `fa` and `en`, with a Persian fallback. Registration handlers now reject missing/malformed course codes and use the validated language. |
| Admin and panel access | `site_admin/ajax.php`, `site_admin/header.inc.php`, `site_admin/asset/editor/tinymce/plugins/filemanager/config.php`, `company/ajax.php`, `company/header.inc.php`, `sys_panel2/ajax.php`, `sys_panel2/header.inc.php`, `sys_panel2/export.php`, `sys_panel3/ajax.php`, `sys_panel3/header.inc.php`, `sys_panel3/export.php` | Added exit after unauthorized redirects, blocked unauthenticated CMS AJAX and file-manager requests, and corrected the panel 2 role fallback and role-switch checks. Redirects no longer continue into protected handlers. |
| Ratings | `ajax.php` | Validates rating inputs while accepting alphanumeric member IDs, avoids a duplicate vote when a prior vote is found, increments existing aggregates instead of resetting them, and uses columns present in the supplied SQL schema. |
| Local database isolation | `include_site/config.php`, XAMPP MySQL `expertsacademy_dev`, `DUCS/LOCAL_XAMPP_SWITCHING.md` | Cloned the local 25-table database, changed only the clone's base URL to `http://localhost/expertsacademy/`, and connected the Desktop copy to the clone. The source project and its database were not changed. |
| Local AJAX routing | `asset_site/js/script_fa.js`, `asset_site/js/script_fa.min.js`, `sys_panel2/assets/js/custom_fa.js`, `sys_panel2/ajax.php`, `ajax_site.php` | Routes active public and panel AJAX requests and login redirects to the active host and project path, so local forms do not call the hard-coded production AJAX endpoints. |

## Verification performed

- PHP syntax checks passed for every modified PHP file.
- JavaScript syntax checks passed for both modified JavaScript files using the bundled Node runtime.
- Local HTTP smoke checks returned 404 for missing public pages and 403 for unauthenticated CMS AJAX/file-manager requests. These checks did not use the live database.
- A local Chrome preview of representative FAQ content was inspected at desktop (1280 × 900) and mobile (390 × 844) sizes. The preview used sample data and did not submit forms.
- Ownership checks found no root-owned files in the Desktop copy.

## Verification limits and remaining work

The supplied snapshot lacks the legacy `include/` directory and root `components/` directory used by several member/panel flows. `site.sql` defines 25 tables while application SQL references 70 distinct table names. No full database-backed end-to-end run was possible. The existing `DUCS/DATABASE.md` and `DUCS/SETUP_AND_OPERATIONS.md` explain those gaps. The rating and role-switch flows therefore have static/syntax verification, not integration verification against a working deployment.

The project still interpolates request data into many SQL strings through its `Query()` helpers. File-upload validation and cross-request rating consistency also need a broader review with a working schema and test database. Active public and panel AJAX endpoints now resolve on the current host and project path. Some inactive code, navigation links, verification-email text, and static assets still reference production domains. Registration can send real SMS; do not treat the database clone as isolation from external services. The bundled TinyMCE editor is version 4.1.3; dependency upgrades require a separate compatibility pass.

## Optional cleanup proposed for approval

No optional files were deleted or repurposed. If these are no longer needed, review the backup/prototype files `index.php.bk`, `web.config-bk`, `components_site/job copy.php`, `components_site/middle-box copy.php`, `site_admin/section copy.php`, `sys_panel2/questionclass copy.php`, and `sys_panel3/questionclass copy.php`. The separate `chat.html` and `chat-api.php` should be retained if chat remains a planned site feature. Confirm each group before removing it.
