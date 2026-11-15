# CMS interface and FAQ updates (2026-10-26)

> Historical change record. Its counts and tests describe the date in this title. For the verified 2026-11-01 source, database, migration and test state, use [the current review](PROJECT_REVIEW_2026-11-01.md) and [handoff](AI_PROJECT_HANDOFF.md).

## Running copy and scope

These changes are in `/opt/lampp/htdocs/expertsacademy`, the improved copy that the site owner moved into XAMPP's `htdocs`. The earlier source is backed up at `/opt/lampp/htdocs/expertsacademy-original`. The running copy uses the separate `expertsacademy_dev` database through `include_site/config.php`. This update does not require a schema migration or change records in the original database.

The original documentation inventory is a snapshot from 2026-10-25. This file records subsequent CMS interface and FAQ behavior changes; its new files may be absent from the older TSV inventory.

## CMS dashboard and navigation

- `site_admin/index.php` now shows live counts for Persian pages, Persian content, Persian menu items, and pending general contact messages. It also shows recently created pages and shortcuts to frequent tasks. Counts are optional: a failed count query does not prevent the rest of the dashboard from rendering.
- `site_admin/header.inc.php` groups the existing visible CMS links into pages/content, menus, forms, and settings. It highlights the active section and keeps the formerly hidden skills group hidden. The site preview link uses the active local host.
- `site_admin/asset/css/admin-dashboard-nav.css` contains scoped dashboard and navigation styles, including smaller-screen layouts. The header loads this stylesheet together with the editor and listing stylesheets.

## Editing and lists

- `site_admin/asset/css/cms-editor-ui.css` and `site_admin/asset/js/cms-editor-ui.js` provide wider editing layouts, field-group headings, a sticky save bar, and a rich-text preview for supported editor forms. The preview uses a sandboxed iframe and removes active or external content. Existing form names and save actions remain in place.
- The editing presentation is applied to `pages.php`, `misc.php`, `section.php`, `category_manage.php`, and `setting.php`. The settings form now has a matching closing `</form>` tag.
- `site_admin/asset/css/listing-ui.css` and `site_admin/asset/js/listing-ui.js` provide readable, horizontally scrollable tables and current-page filters. Server-side search and pagination remain in use on screens that already had them. The content, menu, form, user, question, and page lists are updated where those modules are used.
- Category order updates now match each row by `tbl_categories_code`, which is the value submitted by the reorder form. Previously the update used `tbl_categories_id`, while stored codes and IDs differ. Content-section search and pagination keep their `catcode` and `section` parameters.
- `site_admin/pages.php` now previews legacy CMS pages by their existing `tbl_pages_code`. Root `page_site.php` falls back to `tbl_pages` by that code when no `tbl_content` page uses the requested slug. This fixes a preview link that referenced a nonexistent `tbl_pages_url` column; existing `tbl_content` routes retain priority.
- `site_admin/form_contact.php` is a new CMS inbox for general contact records with `tbl_form_section='contact'` and an empty `tbl_form_model`. Such records were previously absent from the existing specialized contact lists. It provides server-side search, status filtering, pagination, escaped details, and a CSRF-protected, POST-only reviewed-status action. It does not delete messages.

## FAQ and collaboration form

`[سوالات متداول]` now renders the FAQ accordion alone through `components_site/faq.php`. It no longer adds the collaboration form automatically. To place that form on a page intentionally, add the separate `[فرم همکاری سوالات]` block in the page design editor. The new block is mapped in root `index.php` to `components_site/faq_form.php`; it is listed in both add and edit design controls in `site_admin/category_manage.php` and described in `site_admin/help.php`. Existing FAQ content, submission field names, and the `ajax_site.php?do=faq` action remain the same.

The public contact and FAQ handlers now reject submissions missing fields that their HTML forms already mark as required. This prevents empty requests from creating new rows. Historical empty rows are retained and visible in the general contact inbox.

## Verification and limits

- PHP syntax checks passed for the changed PHP files; JavaScript syntax checks passed for the new CMS scripts.
- Read-only authenticated CLI renders covered the dashboard, editor forms, list pages, and the new inbox. The public FAQ route returned an accordion without an automatic collaboration form. The optional form template rendered its own five fields.
- Empty contact and FAQ HTTP POSTs returned validation messages and did not change the `expertsacademy_dev.tbl_form` row count.
- An existing legacy CMS page preview returned HTTP 200 with its title; an unknown page code returned HTTP 404. The repaired `pages.php` list rendered without warnings. CMS stylesheet and script URLs returned HTTP 200.
- CMS visual inspection with a real logged-in browser session and valid form submissions has not been completed. The original database and backed-up source were not used for write tests.

## Optional content cleanup for owner review

The isolated database contains 18 historical general contact rows with empty name and contact fields. They remain available in the new inbox. Six English FAQ answer rows contain pasted ChatGPT conversation wrapper HTML, which adds excess spacing inside the accordion. Neither the historical rows nor the stored FAQ HTML was deleted or rewritten; the owner requested to review optional removals first.
