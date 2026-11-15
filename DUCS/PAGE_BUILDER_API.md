# Page Builder API contract

**Source-checked 2026-11-01.** Endpoint: `site_admin/page_builder_api.php`. This is a local CMS API, distinct from the older `site_admin/ajax.php?do=...` handlers. Source: `builderState()`, `builderMutate()`, and the HTTP dispatch at the end of that file.

## Authentication and request rules

All requests require `$_SESSION['siteadmin_login']`; unauthenticated requests return JSON 403 before database access. The endpoint sets JSON UTF-8 and `Cache-Control: no-store`. Supported methods are GET and POST; other methods return 405 with `Allow: GET, POST` after successful authentication/connection. There is no independent per-user/page role model; instance ownership checks mean that an instance must belong to the supplied page and locale scope.

`code` must contain 1–64 ASCII letters/digits/underscore/hyphen. `locale` must be `fa` or `en`. Instance `id` is a lowercase UUID v4. All POST requests carry `csrf`, obtained from GET state, and require the same CMS session. Ordinary POST uses JSON; upload uses `multipart/form-data`. The API has no `do` parameter and does not accept arbitrary template paths.

## GET state

```text
GET site_admin/page_builder_api.php?code=PAGE_CODE
```

Success returns the current state, not a persisted draft:

| Key | Meaning |
| --- | --- |
| `ok` | `true` |
| `csrf` | Session token; do not log or commit its value. |
| `page` | `code`, `title_fa`, `title_en`, `url_fa`, `url_en`, `available_locales` |
| `layouts.fa`, `.en` | `mode` (`legacy`/`builder`), numeric `revision`, `structure_mode` (`independent`/`shared`) |
| `catalog` | Visible Registry definitions, fields, variants, templates, storage and schema version. There are currently 32 visible choices. |
| `instances.fa`, `.en` | Ordered non-deleted instances for that locale or shared scope; disabled entries remain available for editing. |

Each instance has `id`, `key`, `marker`, numeric `position`, boolean `enabled`, `scope`, `status`, and `data`. `status=complete` only means some content is present (or storage is `none`); it does not prove that every required field or translation is complete. A missing language is reported through available locales rather than guessed from matching titles/URLs.

## POST actions

Common fields: `action`, `code`, `locale`, `csrf`. Success normally returns the full updated state; `upload` returns a filename object instead. The canonical content action is **`save_content`**, not `save`.

| Action | Additional fields | Result and concurrency |
| --- | --- | --- |
| `activate` | None | Converts one Legacy locale transactionally using the converter. No revision is required. Existing/ambiguous layouts are refused; conversion warnings are not a separate approval barrier in the API. |
| `add` | integer `revision`, `key`, optional Registry `variant` | Adds a fresh UUID at the end. No content is implicitly translated. |
| `duplicate` | integer `revision`, instance `id` | Allocates a UUID after the source, copies its stored locale data, and shifts following positions. |
| `delete` | integer `revision`, instance `id` | Sets `deleted_at` and disables the instance. Content rows remain. |
| `toggle` | integer `revision`, instance `id` | Flips enabled state. |
| `move` | integer `revision`, instance `id`, `direction=up\|down` | Swaps positions with the adjacent instance; no drag/drop contract exists. |
| `save_content` | integer `revision`, instance `id`, `data` | Saves only the selected locale's content; shape depends on storage below. |
| `share_structure` | integer **`revision_fa`** and **`revision_en`** | Requires both active independent layouts and matching ordered `type_key`, `marker`, `enabled`, and `schema_version` sequences. Links structure explicitly; does not merge translated text. |
| `upload` | multipart `file` | Does not require a revision. Validates the page/locale and CSRF before saving an image. Returns `{"ok":true,"file":"cms_RANDOM.ext"}`. |

Normal mutations lock/check the selected layout revision and increment it on success. A stale revision returns 409; the caller must reload state before retrying. When structure is shared, structural mutations lock both locale layouts and increment both revisions. Content saves increment only the selected locale revision. These revisions detect concurrent edits; the API stores no content history or undo versions.

### Content shapes

Text-backed components use a field object. For a title instance, a request body has this shape (placeholder values only):

```json
{
  "action": "save_content",
  "code": "PAGE_CODE",
  "locale": "fa",
  "csrf": "SESSION_TOKEN",
  "revision": 1,
  "id": "00000000-0000-4000-8000-000000000000",
  "data": {"title": "<strong>Heading</strong>", "undertitle": "Subtitle"}
}
```

Content-backed components use `data.items`, a JSON list with at most 100 entries. Existing items include their numeric row `id`; new entries omit it. Only IDs already belonging to that instance/marker/locale are accepted. The array order is stored as `tbl_content_order=1..n`. Existing rows omitted from the list are detached with `parent=removed:UUID`, rather than physically deleted. Custom list types also require `data.companion`, containing the Registry's heading fields. Storage `none` has no editable content fields and rejects a nonempty payload.

Field names/types come from [the Registry export](COMPONENT_REGISTRY.tsv). `tbl_textsection_lng` and `tbl_content_fld1` store locale; the parent is the instance UUID. New UUID content is independent from retained Legacy page-code content. Editing one representation does not synchronize the other.

### Shared structure

`share_structure` uses the common category code as the page pair, not title/URL similarity. It copies English content under the Persian UUIDs, soft-deletes the former English instances, and marks the Persian instances `shared`. Old English rows remain available for a planned rollback. Different component counts, order/types/markers/enabled flags/schema versions, absent locales, stale revisions, or existing shared state return 409.

There is no `unshare` action. Sharing propagates subsequent add/delete/toggle/move/duplicate structure changes to both locales, while stored text and list rows remain locale-specific. The live development review found no shared layouts; the shared flow was exercised on an isolated test database in October.

## Validation and rich text

Plain text is stripped of tags and escaped. Unknown fields and non-scalar field values are rejected. The general cap is 4,000 bytes, or 50,000 bytes for rich text, with additional actual column-length checks before SQL. Required fields, URL schemes, filename rules, colors, numeric/select values, and row ownership are validated separately.

The rich-text sanitizer permits `p`, `br`, `strong`, `b`, `em`, `i`, `ul`, `ol`, `li`, `a`, `h2`, `h3`, `h4`, `blockquote`, and `span`. It removes active media/form/script/style/SVG elements and strips attributes except allowed anchor links. Arbitrary styling, alignment attributes, images, iframes, and video embeds do not survive saving. Existing Legacy HTML copied by migration is preserved rather than passed through this sanitizer. Do not claim that the whole Legacy CMS shares the new API's protection.

Uploads accept detected MIME types JPEG, PNG, WebP, or GIF up to 8 MiB. Filenames are generated by the server and saved under `upload_site/`; PHP/document uploads are not accepted by this route. Uploading only returns a filename; the caller must still save the component field. There is no abandoned-upload cleanup job or file-history rollback.

## Errors

Error shape: `{"ok":false,"error":"message"}`. Server-side unexpected details are logged; clients receive a generic 500 message.

| HTTP status | Examples |
| --- | --- |
| 400 | Invalid body/field/locale/UUID/variant/action, required/length/upload validation. |
| 403 | Missing CMS session or invalid CSRF token. |
| 404 | Page/locale missing, or instance does not belong to that page/locale. |
| 409 | Stale revision, duplicate locale/text records, ownership conflict for a content item, ambiguous activation, or incompatible shared structure. |
| 405 | Unsupported HTTP method after authenticated connection. |
| 503 | Missing Builder tables or old layout schema without `structure_mode`. |
| 500 | Unexpected DB, encoding, storage, or server failure. |

## Known boundaries and verification

The converter's equal-order migration fix and the separate `builderCloneData()` used by duplicate/share are distinct code paths. The latter has no explicit source `ORDER BY`; fidelity for equal-order copies remains an unverified regression case. UI preview is an external link to saved public output; there is no draft/publish API, live embedded preview, unshare, or reusable global-content reference.

The 2026-11-01 HTTP review confirmed unauthenticated API 403, Builder page redirect, CMS AJAX/file-manager 403, and public/asset availability. Nine unit checks passed. The October isolated integration/browser tests covered authenticated CRUD, revisions, translations, sharing, and sanitization. Those historical results are not a claim that every action was rerun on the development database in November.
