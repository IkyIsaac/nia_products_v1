# PAGE_STATUS.md — Nia Nutrition

Status values: **Not Started** · **In Progress** · **Review** · **Completed**

Update this file the moment a page's status changes — do not batch updates at phase end. This is the single place anyone on the project can check "is X page done yet."

**Language rule (client-confirmed 2026-07-08):** every page below must have both an English (default) and a Kiswahili version completed via WPML before it is marked **Completed** — a page with only its English version done should stay at **In Progress**/**Review**, not Completed. **Currently on hold:** WPML is not yet installed (client decision 2026-07-07, see `RISKS.md` R20) — no page can reach Completed until it's unblocked.

**Phase 2 (Theme Foundation) — done 2026-07-07:** the Nia Theme is scaffolded and active (`wp-content/themes/nia-theme`) — `style.css`, `functions.php`, `theme.json`, base templates (`index.php`, `page.php`, `single.php`, `archive.php`, `404.php`), minimal `header.php`/`footer.php` stubs (real markup is Phase 3). Full token set compiled via Tailwind (`tailwind.config.js`) and mirrored in `theme.json`'s block-editor palette/font sizes. Fonts self-hosted (Playfair Display, Montserrat, Material Symbols Outlined — latin/latin-ext subsets only, no CDN requests). Elementor's Global Colors/Fonts configured to mirror the same tokens (curated subset — see `ARCHITECTURE.md` §2a). Verified: blank pages render correctly (home, single post, static page, WooCommerce shop archive, 404), no PHP errors/warnings, WPCS clean.

---

## Static Marketing Pages (Phase 4)

| Page | Source mockup | Status | Notes |
|---|---|---|---|
| Homepage | `index.html` | Not Started | |
| Our Heritage / About | `about-nia.html` | Not Started | Do NOT use `about.html` — abandoned concept, see `DESIGN_SYSTEM.md` §0 |
| Contact | `contact.html` | Not Started | Form via Fluent Forms |
| FAQ | `faqs.html` | Not Started | Re-theme from stale olive-gold palette to canonical orange-gold |
| Wellness Journal — index | `journal.html` | Not Started | |
| Wellness Journal — single post template | `journal.html` (article layout) | Not Started | |
| The Ritual / Subscription info page | `ritual.html` / `subscription.html` | Not Started | Build ONE template — pages are near-duplicates, see `DESIGN_SYSTEM.md` §0 |
| Shipping Policy | none (new content) | Not Started | Style per system, no direct mockup |
| Refund Policy | none (new content) | Not Started | Style per system, no direct mockup |
| Privacy Policy | none (new content) | Not Started | Style per system, no direct mockup |
| Terms of Service | none (new content) | Not Started | Style per system, no direct mockup |

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
