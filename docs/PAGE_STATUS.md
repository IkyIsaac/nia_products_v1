# PAGE_STATUS.md — Nia Nutrition

Status values: **Not Started** · **In Progress** · **Review** · **Completed**

Update this file the moment a page's status changes — do not batch updates at phase end. This is the single place anyone on the project can check "is X page done yet."

**Language rule (client-confirmed 2026-07-08):** every page below must have both an English (default) and a Kiswahili version completed via WPML before it is marked **Completed** — a page with only its English version done should stay at **In Progress**/**Review**, not Completed. **Currently on hold:** WPML is not yet installed (client decision 2026-07-07, see `RISKS.md` R20) — no page can reach Completed until it's unblocked.

**Phase 2 (Theme Foundation) — done 2026-07-07:** the Nia Theme is scaffolded and active (`wp-content/themes/nia-theme`) — `style.css`, `functions.php`, `theme.json`, base templates (`index.php`, `page.php`, `single.php`, `archive.php`, `404.php`), minimal `header.php`/`footer.php` stubs (real markup is Phase 3). Full token set compiled via Tailwind (`tailwind.config.js`) and mirrored in `theme.json`'s block-editor palette/font sizes. Fonts self-hosted (Playfair Display, Montserrat, Material Symbols Outlined — latin/latin-ext subsets only, no CDN requests). Elementor's Global Colors/Fonts configured to mirror the same tokens (curated subset — see `ARCHITECTURE.md` §2a). Verified: blank pages render correctly (home, single post, static page, WooCommerce shop archive, 404), no PHP errors/warnings, WPCS clean.

**Phase 4 (Static Marketing Pages) — English builds done 2026-07-07.** All 10 pages below are live, responsive, and match their mockups' design language using dedicated `page-*.php` templates (per-page bespoke layouts) that reuse the Phase 3 block library (`nia/hero`, `nia/benefit-grid`, `nia/testimonial`, `nia/cta-banner`, `nia/newsletter-signup`) via `render_block()` for repeating sections. **Every page is capped at Review, not Completed** — the language rule above requires a Kiswahili version via WPML, which is on hold (R20). Two further known gaps, both already tracked in `RISKS.md`: **(1)** all photography is the mockups' original AI-generated placeholder imagery, now self-hosted in `wp-content/themes/nia-theme/assets/images/placeholders/` (fixed page images) or the media library (blog post featured images) instead of hotlinked from Google — still needs replacing with real product/lifestyle photography before launch (R6). **(2)** the 4 policy pages contain original draft copy (no client-provided legal text existed to transcribe) and are visibly marked "pending final legal review" on-page — see R22 below. A real `wp_nav_menu` ("Primary Navigation") now backs the header, replacing the Phase 3 fallback; `Nia_Nav_Walker`'s active-state highlighting verified working.

---

## Static Marketing Pages (Phase 4)

| Page | Source mockup | Status | Notes |
|---|---|---|---|
| Homepage | `index.html` | Review | `page-homepage.php`, set as the static front page. Featured Products section is a static 4-card placeholder — Phase 5 wires a live WooCommerce query (`ARCHITECTURE.md` §9a) |
| Our Heritage / About | `about-nia.html` | Review | `page-about.php`, slug `/about/`. Does NOT use `about.html` — abandoned concept, see `DESIGN_SYSTEM.md` §0 |
| Contact | `contact.html` | Review | `page-contact.php`, slug `/contact/`. Form is a real Fluent Forms form (#5, "Contact Page Inquiry") with matching fields, not dead markup |
| FAQ | `faqs.html` | Review | `page-faq.php`, slug `/faq/`. Re-themed from stale olive-gold to canonical orange-gold; accordion + category tabs rebuilt with Alpine.js. Added a "Payments" FAQ block (the mockup's category grid links to one but no content block existed in source) |
| Wellness Journal — index | `journal.html` | Review | `page-journal.php`, slug `/journal/`. Featured article = most recent post; grid = next 3; category bar links to real WP categories (Recipes/Science/Lifestyle/Community/Tradition) styled via `archive.php` |
| Wellness Journal — single post template | `journal.html` (article layout) | Review | `single.php`. No dedicated single-post mockup exists in source (journal.html is index-only) — layout is a reasonable extrapolation using the same tokens (hero image, eyebrow, `.entry-content` prose styling, related articles) |
| The Ritual / Subscription info page | `ritual.html` / `subscription.html` | Review | `page-subscription.php`, slug `/subscription/`. Built from `subscription.html` (chosen over `ritual.html` — near-identical; "Subscription" is the nav label used sitewide, see `DESIGN_SYSTEM.md` §0) |
| Shipping Policy | none (new content) | Review | `/shipping-policy/`, using base `page.php`. Original draft copy, marked "pending final legal review" on-page |
| Refund Policy | none (new content) | Review | `/refund-policy/`, using base `page.php`. Original draft copy, marked "pending final legal review" on-page |
| Privacy Policy | none (new content) | Review | `/privacy-policy/`, using base `page.php`. Original draft copy, marked "pending final legal review" on-page |
| Terms of Service | none (new content) | Review | `/terms-of-service/`, using base `page.php`. Original draft copy, marked "pending final legal review" on-page |

## WooCommerce Pages (Phases 5–6)

| Page | Source mockup | Status | Notes |
|---|---|---|---|
| Shop / Collection archive (category-aware, paginated) | `collection.html` | Not Started | Product card + badge + quick-add overlay + category filter/tab row (corrected 2026-07-08: 4 categories, not 4 products, variable count each — see `ARCHITECTURE.md` §9a) |
| Product Detail Page (shared template for any product, any category) | `product.html` | Not Started | Purchase-type radio (one-time vs. subscription) |
| Product category setup (4 initial: Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules) | none — WooCommerce taxonomy setup | Not Started | Admin-manageable; count of categories and products per category expected to change |
| Cart | `my-cart.html` | Not Started | |
| Checkout | `checkout.html` | Not Started | Re-theme from stale olive-gold palette; wire real Selcom methods in Phase 7 |
| My Account / Customer Dashboard | `dashboard.html` | Not Started | Sidebar nav; mobile equivalent uses client-approved hamburger/drawer pattern (see `DESIGN_SYSTEM.md` §7) |
| My Account → My Rituals (subscriptions) | `dashboard.html` (subscriptions panel) | Not Started | Depends on Phase 9 |
| My Account → Order History | `dashboard.html` (order history panel) | Not Started | |

## Global Components (Phase 3 — prerequisite for all pages above)

**Phase 3 — done 2026-07-07.** Built in `wp-content/themes/nia-theme` (header/footer/CSS/forms) and the new `wp-content/plugins/nia-core` (Gutenberg blocks). Verified via a dev-only style-guide page (`template-style-guide.php`, page `/nia-style-guide/`, noindexed) showing every button/card/block variant, plus header/footer tested live on the homepage, shop archive, and a 404. All 8 theme files + 23 plugin files pass WPCS clean.

| Component | Status | Notes |
|---|---|---|
| Header / desktop nav | Completed (2026-07-07) | `header.php` — glassmorphic fixed nav, `wp_nav_menu` with `Nia_Nav_Walker` for active-state (falls back to static links until a 'primary' menu is assigned in Phase 4) |
| Mobile nav (hamburger/drawer) | Completed (2026-07-07) | Alpine.js-driven full-screen overlay drawer in `header.php`, per client-approved pattern — see `DESIGN_SYSTEM.md` §7 |
| Currency switcher (TZS/USD, header control) | Completed — UI only (2026-07-07) | Static `TZS ▾` control in header; real conversion logic via WPML Multicurrency lands in Phase 5, see `ARCHITECTURE.md` §11 |
| Language switcher (EN/SW, header control) | Completed — UI only (2026-07-07) | Static `EN`/`SW` text-label control in header; wired to WPML in Phase 5, see `DESIGN_SYSTEM.md` §10 |
| Elementor Saved Template library (on-brand components) | Completed (2026-07-07) | 6 templates (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter, Promo Tile) in Elementor's Template Library, built via `_elementor_data` + Global Colors/Fonts (Phase 2) + compiled Tailwind classes on widgets — see `ARCHITECTURE.md` §2a for the `css_classes`/`_css_classes` key distinction discovered while building these |
| Footer (full) | Completed (2026-07-07) | `footer.php` — 4-col grid (brand, Discover, Support, Newsletter), verbatim copyright line |
| Footer (minimal, checkout variant) | Completed (2026-07-07) | `footer-minimal.php`, auto-selected via `is_checkout()` (same mechanism as the header) |
| Button variants (8 total) | Completed (2026-07-07) | `.btn-primary`, `.btn-outline-light`, `.btn-outline-dark`, `.btn-inverse-surface`, `.btn-primary-filled`, `.btn-link`, `.btn-icon-circle`, `.btn-radio-card` in `src/css/input.css` — see `DESIGN_SYSTEM.md` §4 |
| Product card | CSS foundation only (2026-07-07) | `.card-product`/`.card-product-image` classes built; the actual WooCommerce template wiring (count-agnostic, any category) is Phase 5/6 work, see `ARCHITECTURE.md` §9a |
| Category filter/tab row (Shop archive) | Not Started | Shop-archive-specific — deferred to Phase 5/6 alongside the Shop archive itself; reuses the journal/FAQ tab pattern, see `DESIGN_SYSTEM.md` §14 item 12 |
| `.sunlight-shadow` card utility | Completed (Phase 2) | Already existed as of Phase 2; card variants built on top of it this phase (`.card-journal`, `.card-testimonial`, `.card-subscription-tier`, `.card-app`) |
| Testimonial block | Completed (2026-07-07) | Custom Gutenberg block `nia/testimonial` (nia-core plugin) — star rating, italic pull-quote, avatar/name/role, repeater |
| Benefit/Icon Grid block ("92 Minerals" pattern) | Completed (2026-07-07) | Custom Gutenberg block `nia/benefit-grid` — icon + title + body repeater, flexible item count |
| CTA Banner block | Completed (2026-07-07) | Custom Gutenberg block `nia/cta-banner` — 3 background variants, up to 2 CTAs |
| Newsletter Signup block | Completed — UI only (2026-07-07) | Custom Gutenberg block `nia/newsletter-signup` — email capture UI only, no backend wired yet |
| Dashboard sidebar (desktop) | Deferred to Phase 6 | No My Account page exists yet to attach it to (My Account is Phase 6, not Phase 4's priority); building it now would be unverifiable. Reuses the same hamburger/drawer mechanism already built for the main nav |
| Dashboard sidebar (mobile equivalent) | Deferred to Phase 6 | Same reasoning as above — see `DESIGN_SYSTEM.md` §7 for the approved pattern to apply when Phase 6 is reached |

## Excluded from build

| File | Reason |
|---|---|
| `about.html` | Abandoned "Aureate Wellness" rebrand concept — different brand name, fonts, colors. Not linked from any live page's nav. Superseded by `about-nia.html`. |
| `ritual.html` **or** `subscription.html` (whichever is not chosen) | Near-duplicate pages — only one should be built as a WordPress template. |

---

## Legend for future entries

When adding a new page not listed above, append it to the correct section with: source mockup (or "none — new content"), status, and any notes on design decisions/gaps that had to be made during build (cross-reference `DESIGN_SYSTEM.md`/`ARCHITECTURE.md` if a decision was logged there).
