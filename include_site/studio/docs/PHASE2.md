# Phase 2 — Design System Manager, Component Studio & Premium Library

Status: **Phase 2a delivered; the 60-type / 200-variant catalogue target is not yet met.**
No Phase 3 work has started.

This document reports what exists, how it was verified, and what remains. It does not
claim completion of the full Phase 2 brief.

## 1. Design System Manager

### Token model

`src/DesignTokens.php` compiles a validated token document into CSS custom properties.
Schema version 2 is retained; Phase 2 adds optional groups so Phase 1 documents and
themes keep validating unchanged.

| Group | Fields |
| --- | --- |
| `colors.light` / `colors.dark` | brand, onBrand, surface, canvas, text, muted, border, accent, success, danger, focus, info, warning |
| `typography` | latin/RTL family, base, heading, display, scale, line height, weights (regular–bold), display/body tracking, fluid min/max |
| `spacing` | unit, section, card, gap, 10-step numeric scale → `--cms-space-0…9` |
| `layout` | container, wide, narrow, reading, columns, grid gutter, three breakpoints |
| `radius` | small, medium, large, xl, pill |
| `shadow` | preset plus sm/md/lg box-shadow strings |
| `button` | height, weight, style, radius, horizontal padding |
| `background` | pattern (none/grid/grain), gradient preset, overlay strength |
| `gradients` | dawn, midnight, sand, forest (from/to/angle) |
| `motion` | preset, enabled, duration, distance, easing, four named preset timings |
| `rtl` | font scale, letter spacing |

Every value is validated server-side: hex colours, numeric ranges, enum whitelists and
CSS-injection rejection for shadow strings. The compiler only emits chosen values.

### Controlled publication

- Editing happens in `tbl_studio_theme_drafts` (one working draft per locale).
- `PUT ?resource=design&id=…` saves with an optimistic `expectedVersion`; stale writes get 409.
- `POST ?resource=design_publish&id=…` validates again, then in one transaction:
  appends an immutable `tbl_studio_design_tokens` version, deactivates the previous
  theme for that locale, inserts the new `tbl_studio_themes` row, records the
  publication in `tbl_studio_theme_publications`, and audits the action.
- Pages keep the tokens stored in their revisions; publishing a theme never rewrites
  existing pages. New documents take the active theme for their locale.

### UI

New “Design system” tab in the studio shell:

- Locale switcher (en/fa), draft version/status, unsaved indicator.
- Editors for every token group above, including both colour palettes.
- Live WCAG contrast calculations for text/canvas, text/surface, on-brand/brand and
  muted/surface, with a pass/fail threshold at 4.5:1.
- Server-rendered live preview in an iframe (mobile/tablet/desktop widths) using the
  sample page and the current draft tokens.
- Validation errors mapped to token paths; Save draft / Publish / Discard.
- Publication history with version and timestamp.

## 2. Component Studio

New “Component Studio” tab:

- Search by label/type/tag, category filter and lifecycle filter.
- Full registry list with variant counts.
- Variant selector, LTR/RTL toggle, mobile/tablet/desktop previews.
- Preview iframes call `studio_component_preview.php` — the same PHP renderer and
  stylesheet as published pages, not a separate React rendering.
- Contract panel: renderer, required stylesheet, responsive breakpoints, RTL support,
  version, status, and the property schema.
- Editable metadata stored in `tbl_studio_component_meta`: lifecycle status
  (stable/experimental/deprecated), tags, documentation, accessibility notes,
  animation preset. Component code/schema remain code-owned and cannot be uploaded.
- Presets stored in `tbl_studio_component_presets`, created from JSON props and
  validated through the page validator before insertion; delete supported.
- Version history read from `tbl_studio_component_versions` (version, label, notes,
  release timestamp).
- Link to the full gallery.

## 3. Component inventory (actual)

| Type | Category | Variants |
| --- | --- | --- |
| hero | hero/landing | editorial, split, centered (3) |
| hero-cinematic | hero/landing | overlay, split, minimal (3) |
| bento-grid | bento | mosaic, editorial, metrics (3) |
| feature-grid | editorial/features | cards, compact (2) |
| rich-text | editorial/features | prose, two-column (2) |
| media-text | media | image-start, image-end (2) |
| gallery | media | grid, strip (2) |
| quote | testimonials | statement, testimonial (2) |
| stats | statistics | inline, cards (2) |
| logos | logos | row (1) |
| timeline | timeline/process | vertical, alternating, horizontal (3) |
| pricing | comparison/pricing | tiers, comparison, spotlight (3) |
| accordion | FAQ | faq, plain (2) |
| cta | conversion | band, card (2) |
| course-list | courses | grid, list (2) |
| divider | utility/layout | line, space (2) |

**Total: 16 types, 36 variants.** The brief asks for 60 types and 200 variants, so
44 types and 164 variants remain. They are *not* registered as placeholders.

The four Phase 2 additions were chosen for structural range: cinematic hero
(overlay/split/minimal), bento (mosaic/editorial/metrics), pricing
(tiers/comparison/spotlight) and timeline (vertical/alternating/horizontal). Their
variants differ in DOM structure, hierarchy and interaction, not only colour/radius.

## 4. Preview gallery

`site_admin/studio_gallery.php` (authenticated CMS session) renders every registered
variant in a live iframe through `studio_component_preview.php`, grouped by type.
Filters: type and LTR/RTL. The gallery is linked from Component Studio and from this
document (source path).

## 5. Versioning

- **Page documents:** schema 1, immutable revisions, optimistic page version.
- **Themes:** immutable token rows plus theme rows; every publication is recorded with
  checksum, locale, publisher and timestamp.
- **Components:** registry `version` is part of the page contract (a saved revision
  pins it); lifecycle and release notes live in metadata/version tables. Changing a
  template requires bumping the registry version and adding a schema-compatible
  migration path before old documents are edited.
- **Presets:** append-only with creator and timestamp.

## 6. Tests and results

| Suite | Command | Result |
| --- | --- | --- |
| PHPUnit | `EA_STUDIO_DB_TESTS=1 php ./vendor/bin/phpunit` | **45 tests, 564 assertions — OK** |
| Vitest | `npm test` | **12 tests — OK** |
| Playwright | `npx playwright test` | **9 specs passed** |
| Editor build | `npm run build` | OK (tsc + Vite; 522.73 kB JS / 23.55 kB CSS) |
| Migration | `php bin/migrate.php status` | 001–005 applied |
| Seeder | `php bin/migrate.php seed` | components 16, variants 36 |

PHPUnit now includes:

- registry inventory floor (≥16 types, ≥36 variants), unique variants, description,
- every variant renders non-empty markup for its type and variant class,
- every variant renders with `dir="rtl"` and `lang="fa"`,
- structural markers for the four new premium components,
- the gallery document contains every registered type,
- design-token contrast values (WCAG reference), Phase 2 CSS variables, invalid-token
  rejection (including CSS injection),
- a DB-gated draft → save → stale-409 → publish lifecycle that restores the previously
  active theme and deletes its own rows.

Playwright covers the original page editor plus: Design System tab live preview,
Component Studio variant preview and RTL toggle, and the gallery counting all 36
variants.

## 7. Visual regression

**Not implemented.** There are no screenshot baselines or pixel-diff gates yet. The
current coverage is DOM/behavioural (Playwright) and render coverage (PHPUnit), not
visual. A baseline suite should be added with the remaining catalogue, using fixed
fixtures and deterministic fonts before it can be trusted.

## 8. Remaining limitations

1. **Catalogue gap:** 16 types / 36 variants delivered against 60 / 200 required.
2. No visual-regression baselines.
3. No scrollytelling/sticky-story GSAP components yet; Motion presets exist as tokens
   and page animation settings, but the Phase 2 brief’s advanced scroll components are
   part of the remaining catalogue.
4. Navigation, footer, forms, tabs, team/expert profiles, case studies, projects,
   services, learning pathways, video and comparison-table family are not yet built.
5. Presets are stored and manageable, but the page editor does not yet offer “apply
   preset” while inserting a component.
6. Component version history is seeded/read-only; the release workflow (bump version,
   write notes from the UI) is not implemented.
7. Media resolution still uses the Phase 1 map/null resolver for seeded previews;
   upload-backed media is Phase 2b work.
8. Gallery renders 36 iframes in one page; acceptable for the current catalogue, but
   it needs pagination/virtualisation before a 200-variant library.
9. Draft theme preview uses the sample page, not the page currently being edited.
10. PHP built-in server and Apache/litespeed differences remain untested here.
11. Restore drill still blocked by the missing `CREATE DATABASE` privilege.

## 9. Next tranche (Phase 2b)

Priority order: tabs, team/expert profiles, case studies, project showcase, services,
learning pathways, video, testimonial wall, logo marquee, navigation, footer, contact
forms — each with 3–4 structurally distinct variants, plus apply-preset in the editor,
component release workflow, media-backed imagery, and Playwright visual baselines.

Target after that tranche: 32 types / ~100 variants, then complete the remaining
categories to reach 60 / 200.

## Catalogue tranche 2 (navigation, people, pathways, forms)

Added ten code-owned types with new structural variants, templates and CSS:

| Type | Variants | Structural range |
| --- | --- | --- |
| navbar | 4 | classic bar, centred two-row, split (links / brand / actions), minimal |
| breadcrumbs | 2 | inline trail, eyebrow with current-page title |
| footer | 3 | brand + columns, compact single bar, editorial wordmark |
| tabs | 3 | underline, pills, vertical rail (CSS-only, no JavaScript) |
| team | 4 | card grid, list rows, spotlight + compact roster, numbered editorial |
| services | 3 | cards, numbered rows, two-column list |
| case-studies | 3 | cards, featured lead + stack, text list |
| pathways | 3 | step cards, track lanes, roadmap rail |
| contact-form | 3 | stacked, split with contact aside, elevated card |
| faq | 3 | two-column Q&A, numbered divided rows, card grid |

Current totals: **26 types / 67 variants**. Remaining to the 60 / 200 target:
**34 types / 133 variants**.

Validation: PHPUnit 45 tests / 913 assertions (renders and RTL-checks every
registered variant), Vitest 16, Playwright 9/9. The page contract section limit
was raised from 60 to 120 so the all-components sample still validates.

The new tranche is not yet mirrored to the Desktop copy.

## Catalogue tranche 3 (media, proof and utilities)

Ten more types, each with a real template and structural variants:

| Type | Variants |
| --- | --- |
| video | cinematic poster, split copy |
| author | inline byline, card |
| certificates | grid, list, accent ribbon |
| reviews | aggregate summary, card grid, masonry wall |
| spec-table | table, definition rows |
| announcement | inline bar, stacked block |
| newsletter | band, split, compact card |
| checklist | inline list, card grid |
| related-content | cards, list rows, compact links |
| location | panel, split detail |

Current totals: **36 types / 91 variants**. Remaining to 60 / 200:
**24 types / 109 variants**.

Validation after this tranche: PHPUnit 45 tests / 1,199 assertions, Vitest 16,
Playwright 9/9, seeded catalogue `{"components":36,"variants":91}`.

## Catalogue tranche 4 (programme and editorial detail)

| Type | Variants |
| --- | --- |
| curriculum | outline, module cards, table |
| schedule | agenda rows, day cards, table |
| application-form | simple, split, stepped fieldsets |
| download-gate | inline, card |
| values | grid, list, manifesto |
| process | steps, numbered, cards |
| jobs | list, cards, table |
| awards | grid, list |
| resources | cards, list, rows |
| glossary | list, columns |
| comparison-matrix | table, option cards |
| campaign | spotlight, split, banner |

Current totals: **48 types / 123 variants**. Remaining to 60 / 200:
**12 types / 77 variants**.

Validation: PHPUnit 45 tests / 1,571 assertions, Vitest 16, Playwright 9/9,
seeded catalogue `{"components":48,"variants":123}`. Page contract section
limit is 160 so the full variant sample validates.

## Catalogue tranche 5 (final): 60 types / 200 variants

Added the remaining twelve types and then deepened the same tranche with
layout-level variants to reach the agreed target.

New types: landing (8), feature-showcase (8), instructor (6), booking-form (6),
team-request-form (6), mega-menu (6), press (6), partners (6), content-index (6),
section-header (7), social-links (6), cookie-consent (6).

Depth variants added to the tranche: minimal/overlay/form-first/proof landing
layouts, compact/list/cards/split feature layouts, numbered/label/actions
headers, compact and quote-card instructor, inline/minimal booking and team
forms, list/drawer mega menu, quotes/compact press, mosaic/badges partners,
compact/letter-bar index, inline/stacked social, banner/compact cookie notice.

**Final inventory: 60 types / 200 variants.** The registry test now asserts this
floor. PHPUnit 45 tests / 2,348 assertions, Vitest 16, Playwright 9/9, seeded
catalogue `{"components":60,"variants":200}`.

Limitations: the last depth variants are primarily layout/CSS structural
changes rather than entirely new PHP branch markup; there is still no
pixel-diff visual regression suite, and the Desktop mirror has not been
refreshed with tranches 3–5.
