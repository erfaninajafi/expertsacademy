# File and directory guide

**New Phase 2 files:** use PHASE2_FILES.tsv for exact created/modified paths and PHASE2_DESIGN_STUDIO.md for responsibilities. New backend resides in include_site/studio, editor in site_admin/studio/src/design, public design assets in asset_site/studio and additional CMS entry points alongside page_studio.php. The retained guide below covers earlier trees.

This guide explains the application-owned files and the large generated/vendor trees. The **complete path-by-path inventory** is in `FILE_MANIFEST.tsv` (7,565 readable rows as of 2026-11-01), and **every PHP file** has one metadata row in `PHP_CODE_INDEX.tsv` (453 rows). Those indexes include copied libraries and media that are impractical to describe one by one in prose. The current full traversal had no errors; `asset/img/`, previously unreadable, is now inventoried. `DUCS/` and `.git/` are excluded from the generated source counts.

## Top-level files (all 34)

| File(s) | Role |
| --- | --- |
| `.htaccess` | Apache rewrite rules and directory index. See [Routes and APIs](ROUTES_AND_APIS.md). |
| `web.config`, `web.config-bk` | IIS PHP/default-document configuration and backup variant; neither mirrors Apache friendly-URL rules. |
| `index.php` | Current database-driven public page composer. |
| `index.php.bk` | Alternate/backup copy of the public index; not the Apache `DirectoryIndex`. |
| `includer_site.php` | Current public bootstrap for session, MySQL helpers, settings, request parameters, and translation include. |
| `header_site.inc.php`, `footer_site.inc.php` | Public layout, navigation, social links, scripts, and footer. |
| `cat_site.php`, `class_site.php`, `course_site.php` | Public category, class detail, and course/listing views. |
| `lib_site.php`, `lib_view.php`, `page_site.php` | Public content/library listing, content detail, and CMS page view. |
| `ajax_site.php` | Current public form/product/video/load-more and member-registration AJAX actions. |
| `chat-api.php`, `chat.html` | JSON chat proxy and browser chat UI. |
| `includer.php` | Legacy root member bootstrap requiring missing `include/`. |
| `register.php`, `panel.php`, `resume.php` | Member registration/login, portal router, and resume page; incomplete without missing legacy directories. |
| `ajax.php` | Legacy member/assessment AJAX actions. |
| `header_panel.inc.php`, `footer_panel.inc.php` | Legacy member layout. |
| `section-one.php`, `section-two.php`, `section-three.php`, `section-four.php`, `section-five.php` | Older standalone section templates; reference missing root `header.inc.php`/`footer.inc.php`. |
| `404.php` | Active missing-page handler used by public entrypoints. |
| `75981759.txt` | Empty verification/placeholder file retained. |
| `not-found.php`, `noscript.php` | Standalone not-found design and JavaScript-required notice. |
| `site.sql` | Full MySQL data dump with 25 DDL tables and INSERT rows. Schema only is documented in `DATABASE_SCHEMA.md`. |

## Public page templates and bootstrap

`components_site/` contains 37 PHP files. `index.php` delegates to `include_site/page_renderer.php`; Registry definitions select allowlisted templates for Legacy markers and Builder UUID instances. There are 32 catalog types plus four hidden companion headings; title variants use the common title template. `job copy.php` and `middle-box copy.php` are retained alternate copies. Template families are:

| Family | Files |
| --- | --- |
| Sliders and visual blocks | `slider.php`, `image-slider.php`, `slider-content.php`, `circle-slider.php`, `slider-box.php`, `slider-box-link.php`, `top-image.php`, `map.php`, `logo.php`, `logo-tooltip.php`. |
| Titles and content layout | `title-model-one.php` through `title-model-four.php`, `single-content.php`, `left-right-content.php`, `left-right-content2.php`, `tab-content.php`, `page.php`, `section-side.php`, `list-zire-ham.php`, `list-kenar-ham.php`. |
| Custom/middle blocks | `custom-one.php` through `custom-four.php`, `middle-box.php`, `middle-box2.php`, `middle-box3.php`, `footer-title.php`. |
| Interaction/forms | `faq.php`, `aca.php`, `faq_form.php`, `job.php`, `contact.php`. |
| Alternate copies | `job copy.php`, `middle-box copy.php`. |

`include_site/` contains `config.php`, `function.php`, `jdf.php`, `sms.class.php`, `component_registry.php`, `page_renderer.php`, and `lang/en.php`, `en_lng.php`, `fa.php`, `fa_lng.php`. The `_lng` files feed the public site; the shorter language files feed CMS-related code. `jdf.php` supplies Jalali/Persian date functions. `sms.class.php` is a bundled SMS adapter. `lib/` contains 12 NuSOAP PHP classes/files for SOAP clients/server/schema/transport, not the public library catalog despite its folder name.

## CMS area: `site_admin/`

There are 39 top-level PHP files and 969 readable files total, mostly under its `asset/` bundle. The CMS uses `include_site/` and controls the public site. Its pages group as follows:

| Function | Files |
| --- | --- |
| Entry/auth/layout | `index.php`, `login.php`, `includer.php`, `inc.php`, `header.inc.php`, `footer.inc.php`, `ajax.php`. |
| Public page composition and localization | `section.php`, `section copy.php`, `pages.php`, `category_manage.php`, `menu_manager.php`, `menu_footer.php`, `lng_sitefa.php`, `lng_siteen.php`, `setting.php`, `logo.php`, `ads.php`. |
| Catalog and learning content | `course.php`, `parentcourse.php`, `class.php`, `classlist.php`, `workshop.php`, `teacher.php`, `question.php`, `questionclass.php`, `product.php`. |
| Forms, users, and other | `form1.php`, `form2.php`, `form3.php`, `form4.php`, `form_contact.php`, `user.php`, `help.php`, `misc.php`, `misctwo.php`. |

Additional current Builder files:

| File / directory | Responsibility |
| --- | --- |
| `page_builder.php` / `asset/js/page-builder.js` / `asset/css/page-builder.css` | Authenticated Builder shell, locale/catalog/ordered list/inspector UI and behavior. |
| `page_builder_api.php` | JSON state and mutations, revision/CSRF/ownership, sanitization, prepared SQL and image upload. |
| `builder_migration.php` | Read-only conversion planning and additive per-locale transactional conversion. |
| `migrations/20261027_page_builder.sql` | Additive layout/instance schema. |
| `migrations/backup_dev.php` | Guarded private development dump. |
| `migrations/convert_page.php` | CLI schema installation, per-locale dry-run/apply and read-only all-page audit. |
| `migrations/repair_clone_order.php` | Payload-checked transactional order repair for untouched revision-1 conversions. |
| `tests/page_builder_unit.php`, `page_builder_integration.php`, `page_builder_legacy_no_schema.php` | Unit and disposable-database checks; never target the original database. |
| `asset/css/admin-dashboard-nav.css`, `cms-editor-ui.css`, `listing-ui.css`; matching editor/list JS | Earlier dashboard, editor and list enhancements. |

The asset tree contains Bootstrap/admin layout resources, TinyMCE and a PHP file manager. These executable asset endpoints are detailed in [Routes and APIs](ROUTES_AND_APIS.md). `site_admin/section copy.php` is a separate alternate file, not selected by normal filename routing.

## Operations dashboard: `sys_panel2/`

This is the largest application-owned module tree: 107 top-level PHP files and 1,608 readable files in total. It has a Falcon-style `assets/` tree, vendor resources, and role-specific navigation. All 63 top-level PHP names from `sys_panel3/` also appear here, but only 19 same-name files are byte-identical; treat panel 2 as a distinct implementation.

| Function | Main files |
| --- | --- |
| Entry/auth/layout/config | `index.php`, `login.php`, `login-company.php`, `includer.php`, `inc.php`, `header.inc.php`, `footer.inc.php`, `register.php`, `user.php`, `access.php`, `setting.php`, `logo.php`, `code.php`, `misc.php`, `lng_panelfa.php`, `lng_editorfa.php`. |
| Contracts, members, and employer work | `contract.php`, `contract2.php`, `contractold.php`, `contract-company.php`, `contract-list.php`, `contract-end.php`, `archive.php`, `archiveorder.php`, `member.php`, `faragir.php`, `faragir-list.php`, `faragirfilter.php`, `mentorlist.php`, `coachlist.php`, `skill-list.php`, `registeruserlist.php`, `quser.php`, `shenasname.php`. |
| Plans, interviews, assessment, reporting | `plan.php`, `planview.php`, `plan-view.php`, `plandetail.php`, `program-end.php`, `endreport.php`, `interview.php`, `interviewcoach.php`, `arzyabi.php`, `arzyabi-list.php`, `checklist.php`, `checklistdefault.php`, `checklistreport.php`, `levelquestion.php`, `exam.php`, `result.php`, `result-coach.php`, `result-contract.php`, `report1.php`, `report2.php`, `report3.php`, `report4.php`, `report5.php`, `report6.php`, `report7.php`, `chart.php`, `print.php`. |
| Courses and content | `course.php`, `course-view.php`, `courseparent.php`, `parentcourse.php`, `class.php`, `class-uniq.php`, `classlist.php`, `section-class.php`, `workshop.php`, `teacher.php`, `opteacher.php`, `question.php`, `questionpublic.php`, `questionclass.php`, `questionclass copy.php`, `mohtava.php`, `contentcreator.php`, `film.php`, `video.php`, `cer.php`, `certificate.php`, `category_manage.php`, `slide.php`, `ads.php`, `pages.php`, `pageview.php`, `seo_setting.php`, `menu_manager.php`, `menu_footer.php`. |
| Communication, files, finance, data transfer | `message.php`, `allmessage.php`, `skillmessage.php`, `coachmessage.php`, `comment.php`, `newsletter.php`, `document.php`, `attach.php`, `bankfile.php`, `invoice.php`, `ajax.php`, `json.php`, `csv.php`, `export.php`, `custom_export.php`, `sql.php`, `test.php`. |

`sys_panel2/sql.php` and `test.php` are executable diagnostic/export scripts with significant exposure risk. `contractold.php`/`contract2.php` contain active legacy SQL Server import code. `questionclass copy.php` is an alternate source copy. The index and API docs identify direct endpoint behavior.

## Older operations and company areas

`sys_panel3/` has 63 top-level PHP files and 1,826 readable files total. Its names overlap panel 2, but its Sneat-style `asset/` tree and page implementations differ. Use `PHP_CODE_INDEX.tsv` to locate a particular file and its input/table usage; do not infer panel 3 behavior from panel 2 merely because filenames match. `sys_panel3/csv.php` exists and exports work review/referee data. Some `custom_export.php` actions do not match that contract; see the route reference. The older claim that the CSV file itself was missing was incorrect.

`company/` has nine top-level PHP files: `index.php`, `login.php`, `includer.php`, `inc.php`, `header.inc.php`, `footer.inc.php`, `ajax.php`, `planview.php`, and `film.php`. Its 1,749 readable files are mostly copied front-end/editor assets under `company/asset/`. Its `ajax.php` provides plan/status/member update fragments and mutations. Both this area and panel 3 depend on the absent legacy `include/` directory.

## Assets, uploads, and other trees

| Directory | Observed purpose and count |
| --- | --- |
| `asset_site/` | 254 current public-site styles/scripts/fonts/images. `asset_site/js/script_fa.js` contains custom calls to public AJAX handlers. |
| `vendors/` | 271 files from more than 40 bundled front-end library directories, including Bootstrap, jQuery, DataTables, Select2, TinyMCE, Leaflet, ECharts, Swiper, Dropzone, and related utilities. |
| `upload_site/` | 640 files, about 966 MB, including public images/videos and form-upload destinations. |
| `uploadfile_site/` | 85 files, about 268 MB; CMS file-manager upload destination and public media. |
| `asset/` | 69 readable image files under `img/` are now included; the former traversal gap is resolved for this audit. |
| `.well-known/pki-validation/` | One certificate/domain-validation text file. |

The manifest reports each accessible asset path and size. Do not treat the presence of a file in an upload or library directory as proof that it is safe to serve publicly. The four TinyMCE file-manager copies contain executable PHP under their asset paths.

## Documentation and review tools

`DUCS/tools/` contains index regeneration, pure Registry export, development-only read-only schema inventory, and local GET-only smoke tools. Their outputs and limits are linked from [README](README.md). `DUCS/` is excluded from the application manifest to avoid recursively indexing generated documentation. These tools are CLI utilities, not supported web endpoints; keep documentation and data-bearing source dumps appropriately restricted when deploying.
