# PROJECT_PLAN.md — Nia Nutrition

Complete execution roadmap. Phases are sequential and gated — **each phase must be fully complete and approved before the next begins.** Every task completed updates `PAGE_STATUS.md`, `CUSTOMIZATIONS.md`, or `AUTOMATIONS.md` as applicable (see each phase's "Docs to update" line).

**Status: client-directed sequencing exception (2026-07-07).** Phase 1's three blocked items — Hostinger account, WPML license, business email/SMTP — are **on hold** at the client's request, prioritizing static marketing pages first. **Phase 2 (Theme Foundation) is complete** (2026-07-07) — none of it depended on the three held items. **Phase 3 (Global Components) is complete** (2026-07-07) — header/footer, 8 button variants, card system, forms, 5 custom Gutenberg blocks, and the 6-template Elementor Saved Template library are all built and verified; the two dashboard-sidebar rows are deliberately deferred to Phase 6 (see `PAGE_STATUS.md`). **Phase 4 (Static Marketing Pages) is English-complete** (2026-07-07) — all 10 pages are live and reviewed against their mockups; every page is capped at **Review**, not Completed, pending WPML (R20), real photography (R6), and legal review of the 4 policy pages' draft copy (R22). **Phase 5 (WooCommerce Integration) is functionally complete** (2026-07-08) — client directed proceeding with dummy/placeholder catalog data (4 categories, 8 products) rather than waiting on real product data; a full test purchase (browse → cart → checkout → order) was verified end-to-end using a placeholder Cash-on-Delivery gateway and a placeholder flat-rate shipping method, both to be replaced in Phase 7/by the client respectively. **Phase 6 (Custom WooCommerce Design) is complete** (2026-07-08) — Shop archive, Product Detail Page, Cart, Checkout, and My Account/Dashboard all now visually match their mockups (Checkout re-themed off its stale olive-gold source); VAT (18%, Tanzania) was configured and the store's base country corrected from the default `US:CA` to `TZ`. A client-requested Reviews & Ratings feature was added to the PDP the same day (see Phase 6 below). **Real-browser QA (2026-07-08, later same day)** on Shop + PDP (Playwright/Chromium) caught and fixed three bugs the earlier curl-only verification had missed — a leftover default WordPress sidebar widget rendering on WooCommerce pages, WooCommerce's own default stylesheets conflicting with this theme's Tailwind CSS (broken badge/quick-add positioning, stray list bullets, and a collapsed single-column product grid once the conflicting stylesheet was removed), and a CSS selector collision — all detailed in `PAGE_STATUS.md`. **Real-browser QA on Cart/Checkout (2026-07-21)** found and fixed four `input.css` selectors that didn't match WooCommerce Blocks' actual class names, plus a Tailwind content-purging bug that was silently dropping several component classes from the compiled stylesheet — all four WooCommerce-rendered pages (Shop, PDP, Cart, Checkout) are now real-browser verified. **Still outstanding:** 3 of the 8 dummy products are missing a featured image (content gap, see `PAGE_STATUS.md`). **Phase 9 (Subscriptions) core purchase flow built (2026-07-22)** ahead of its normal place in the sequence, client-requested directly — the client revised the v1 model to "pay for the whole term upfront in one checkout" instead of the original renewal-request-per-cycle design (`ARCHITECTURE.md` §5 updated accordingly); verified end-to-end (subscribe → real cart/checkout → order paid → subscription record + delivery schedule created → visible in wp-admin and My Account → My Rituals). Pause/cancel and delivery-date automations are explicitly deferred (see Phase 9 below).

---

## Phase 0 — Documentation (this phase)

- [x] Inspect `/nia-products/` design mockups end-to-end.
- [x] Extract design system → `DESIGN_SYSTEM.md`.
- [x] Decide and justify architecture → `ARCHITECTURE.md`.
- [x] Decide and justify plugin stack → `PLUGIN_DECISIONS.md`.
- [x] Produce this roadmap.
- [x] Produce `PAGE_STATUS.md`, `CUSTOMIZATIONS.md`, `AUTOMATIONS.md`, `RISKS.md`, `DEPLOYMENT.md`.
- [x] **Client sign-off received (2026-07-07)** on all open items — see `DESIGN_SYSTEM.md` ("Design Sign-off — Resolved") and `RISKS.md`. Cleared to begin Phase 1.

---

## Phase 1 — Environment Setup

**Goal:** a reliable, version-controlled foundation to build on.

- [x] Initialize git repository at the WordPress root — done 2026-07-07. `.gitignore` excludes `wp-content/uploads`, core WP files (tracked as a dependency, not source), cache, `wp-config.php`, `node_modules`, `vendor`.
- [ ] **Hosting confirmed (2026-07-08): Hostinger Business/Cloud, WooCommerce plan** — LiteSpeed Enterprise native, WooCommerce pre-configured, NVMe SSD, built-in CDN (see `PLUGIN_DECISIONS.md`, `ARCHITECTURE.md` §10/§12). **BLOCKED — client action required:** signing up and paying for the Hostinger account is a real-world purchase only the client can make; provide the account/access once created so staging can be provisioned there.
- [ ] Provision staging environment on the same host/stack, mirroring production. **Blocked on the Hostinger account above.** In the meantime, all Phase 1 local-environment work below has been done against the local dev install (Local by Flywheel, `nia-seamoss-products.local`) so build tooling and the plugin baseline are ready to move over once hosting exists.
- [x] Install WordPress + WooCommerce, enable HPOS — done 2026-07-07 (local dev site; WooCommerce 10.9.3, HPOS confirmed enabled via `wp wc hpos status`).
- [x] Install & configure: Rank Math, Wordfence, UpdraftPlus, Fluent Forms, FluentSMTP, cookie-consent plugin (CookieYes), image-optimization plugin (ShortPixel) — all installed and activated 2026-07-07. "Configure" (API keys, off-site backup destination, SMTP credentials, etc.) is still pending real credentials — see blocked items below.
- [x] **Keep Elementor (free) installed** — already present, left active, untouched (client direction 2026-07-08). Global Colors/Fonts sync happens in Phase 2/3 once tokens are finalized; see `ARCHITECTURE.md` §2a.
- [ ] Install and configure **WPML + WooCommerce Multilingual & Multicurrency** — **BLOCKED — client action required:** WPML is a paid plugin; it cannot be installed from the free WordPress.org repository. The client needs to purchase a license (the "Multilingual CMS" tier, which includes the WooCommerce Multilingual & Multicurrency add-on) and provide the plugin zip/license key. Once provided: set default language to English, add Kiswahili, choose subdirectory URL structure (`/en/`, `/sw/`).
- [x] Set up local dev tooling: Tailwind CLI/PostCSS build, npm scripts, PHP coding-standard linting (WPCS) — done 2026-07-07. Tailwind build pipeline scaffolded in `wp-content/themes/nia-theme/` (`package.json`, `tailwind.config.js`, `postcss.config.js`, `src/css/input.css`) and verified compiling (`npm run build`/`npm run watch`). WPCS installed via root `composer.json` + `phpcs.xml`, verified running clean against the theme directory (`vendor/bin/phpcs`).
- [ ] Set up business email + verify SMTP sending from staging. **BLOCKED — client action required:** the client needs to provide a real business mailbox (e.g. via Google Workspace or Hostinger's own mail) and its SMTP credentials before FluentSMTP (already installed) can be configured and tested.

**Exit criteria:** clean WordPress + WooCommerce install on staging, all foundation plugins active and configured, build tooling runs locally, test email delivers successfully. **Not yet met, and formally on hold (client decision 2026-07-07)** — the three blocked items above (hosting account, WPML license, business email/SMTP credentials) are deprioritized while marketing pages are built first. **Phase 2 has been given the go-ahead to start regardless**, since it doesn't depend on any of the three (see Phase 2 below).

**Docs to update:** none content-wise — hosting and its downstream plugin choice (LiteSpeed Cache) are already resolved and marked final in `PLUGIN_DECISIONS.md` and `RISKS.md` R5. This entry itself is the Phase 1 progress update.

---

## Phase 2 — Theme Foundation

**Started and completed 2026-07-07, ahead of Phase 1's formal close (client-directed sequencing exception — see `PROJECT_PLAN.md` top status and `RISKS.md` R19–R21).** None of this phase's tasks required Hostinger, WPML, or business email/SMTP — theme scaffolding, token compilation, and base templates are purely local build work.

**Goal:** the custom "Nia Theme" skeleton exists and compiles the full design-token set.

- [x] Scaffold custom theme (`style.css` header, `functions.php`, template hierarchy, `theme.json` for block-editor color/typography palettes matching `DESIGN_SYSTEM.md`).
- [x] Implement `tailwind.config.js` with the exact token set from `DESIGN_SYSTEM.md` §1–3 (colors, type scale, spacing, radius) — single source, no per-page drift.
- [x] Self-host Playfair Display + Montserrat + Material Symbols Outlined — downloaded and subset to latin/latin-ext only (sufficient for English + Kiswahili, both Latin-script), served from `assets/fonts/` via `assets/css/fonts.css`, zero Google Fonts CDN requests (verified in Phase 2 exit-criteria check).
- [x] Confirm the two open type-scale gaps (`headline-sm`, `tertiary-fixed`) are resolved per client sign-off from Phase 0 — both encoded in `tailwind.config.js` and `theme.json`.
- [x] Set up base template files: `index.php`, `page.php`, `single.php`, `archive.php`, `404.php`, minimal `header.php`/`footer.php` stubs (real nav/footer markup is Phase 3), print/reset styles (`src/css/input.css`).
- [x] Configure Elementor's Site Settings (Global Colors + Global Fonts) to mirror this same token set (see `ARCHITECTURE.md` §2a) — done via the kit's `_elementor_page_settings`: 4 system colors + 8 curated custom colors, 4 system typography styles + 3 curated custom styles (a curated brand subset, not all ~50 raw M3 tokens — appropriate for a content-editing tool, per the Elementor governance scoping already documented).
- [ ] Confirm `theme.json`/template strings are translation-ready (`__()`/`_e()`) in preparation for WPML string translation in later phases — done for the templates built so far (all strings wrapped); revisit as Phase 3/4 add more.

**Exit criteria:** blank pages render with correct fonts, colors, and spacing scale; no CDN Tailwind or Google Fonts CDN requests in network tab; Elementor's Global Colors/Fonts match the theme exactly when tested on a scratch page. **Met and verified 2026-07-07** — homepage, a single post, a static page, the WooCommerce shop archive, and a 404 all return correct status codes with the compiled stylesheet/fonts enqueued, zero CDN requests, zero PHP errors/warnings/notices, and WPCS runs clean against all 8 theme files.

**Docs to update:** `PAGE_STATUS.md` (theme scaffold done, still no pages — done, see Phase 2 note there).

---

## Phase 3 — Global Components

**Status: complete (2026-07-07).** Started immediately after Phase 2, per the client's sequencing exception (see top of file).

**Goal:** every component that appears on more than one page exists once, correctly, per `DESIGN_SYSTEM.md` §14.

- [x] Header/nav template part — desktop nav + icons, active-state logic driven by current page/menu location (not hardcoded per file). Built as `header.php` using `wp_nav_menu` + a custom `Nia_Nav_Walker` (falls back to static links until a 'primary' menu is assigned in Phase 4).
- [x] **Build the mobile nav pattern** (hamburger + full-screen overlay drawer) — client-approved pattern, matching existing visual language (see `DESIGN_SYSTEM.md` §7). Built with Alpine.js (self-hosted, `assets/js/alpine.min.js`). Dashboard's mobile sidebar equivalent is **deferred to Phase 6** (see below) — no My Account page exists yet to attach it to.
- [x] Currency switcher control in the header (TZS/USD) — UI only at this phase; wired to real conversion logic (via WPML's multicurrency module) in Phase 5 (see `ARCHITECTURE.md` §11).
- [x] Language switcher control in the header (EN/SW, text-label, no flag icons) — UI only at this phase; wired to WPML in Phase 5 alongside the currency switcher (see `DESIGN_SYSTEM.md` §7/§10).
- [x] Footer template part, with a "minimal" variant for checkout. Built as `footer.php` + `footer-minimal.php`, auto-selected via `is_checkout()`.
- [x] Buttons — codified the 8 variants from `DESIGN_SYSTEM.md` §4 as reusable CSS classes in `src/css/input.css`.
- [x] Cards — `.sunlight-shadow` utility (existed since Phase 2), plus `.card-product`, `.card-journal`, `.card-testimonial`, `.card-subscription-tier`, `.card-app`.
- [x] Custom Gutenberg blocks: Hero, Benefit/Icon Grid, Testimonial, CTA Banner, Newsletter Signup (see `ARCHITECTURE.md` §2, §4). Built in the new `wp-content/plugins/nia-core` plugin (`blocks/*`), zero-build (plain `wp.element`, manually-authored `index.asset.php` files), server-rendered via `render.php`.
- [x] **Build the Elementor Saved Template library** mirroring the custom blocks above (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter, promo tile) — see `ARCHITECTURE.md` §2a, `DESIGN_SYSTEM.md` §14. Built as 6 `elementor_library` posts with hand-authored `_elementor_data`. A key gotcha was discovered and documented while building these — see `ARCHITECTURE.md` §2a (the `css_classes` vs `_css_classes` control-key distinction between Widgets and Sections/Columns).
- [x] Forms base styling (underline inputs, custom radio-card `peer-checked` pattern, select styling).
- [x] Design tokens sanity pass: every component built this phase references `tailwind.config.js` tokens only — verified by reading back every class name used against `DESIGN_SYSTEM.md` §1–5 while writing this phase; nothing hardcoded.

**Exit criteria — met and verified 2026-07-07:** a dev-only style-guide page (`template-style-guide.php`, published at `/nia-style-guide/`, noindexed, not linked from any nav) demonstrates every button, badge, card, form, and block variant against real tokens — confirmed via direct HTTP fetch showing all expected classes/content present with zero PHP errors; header/footer verified functional on the homepage, shop archive, and a 404 (mobile drawer markup, both switchers, and cart badge all present in the rendered HTML); the Elementor Saved Template library (6 templates) was verified via Elementor's own `get_builder_content_for_display()` API, confirmed rendering each template's text content and custom CSS classes correctly and consistently across repeated calls. All 8 theme files + 23 nia-core files pass WPCS clean.

**Scope decision:** "Dashboard sidebar (desktop)" and "Dashboard sidebar (mobile equivalent)" from `PAGE_STATUS.md`'s Global Components list are **deferred to Phase 6** (My Account) rather than built now — there's no My Account page yet to attach or verify them against, and My Account isn't part of the client's stated Phase 4 priority (marketing pages). Building them now would be unverifiable scaffolding. The approved pattern (same hamburger/drawer mechanism as the main nav) is already documented in `DESIGN_SYSTEM.md` §7 and ready to apply when Phase 6 starts.

**Docs to update:** `DESIGN_SYSTEM.md` if any component needed a decision beyond what was documented (log it there, not just in code). No new design decisions were needed this phase beyond the Elementor control-key gotcha, which is a technical/implementation note (logged in `ARCHITECTURE.md` §2a and `CUSTOMIZATIONS.md`), not a design decision.

---

## Phase 4 — Static Marketing Pages

**Status: English builds complete (2026-07-07).** Started immediately after Phase 3, per the client's sequencing exception (see top of file).

**Goal:** every page that only displays information is fully built, matching its mockup, **before any WooCommerce engineering begins.** These pages require assembling already-built global components (Phase 3) plus page-specific content — minimal new engineering.

Pages (source mockup → WordPress page):
| Page | Source mockup | Status |
|---|---|---|
| Homepage | `index.html` | [x] `page-homepage.php`, static front page |
| Our Heritage / About | `about-nia.html` (**not** `about.html` — see `DESIGN_SYSTEM.md` §0) | [x] `page-about.php` |
| Contact | `contact.html` (Fluent Forms for the form itself) | [x] `page-contact.php` + Fluent Form #5 |
| FAQ | `faqs.html` (re-themed to canonical palette — see below) | [x] `page-faq.php`, re-themed, Alpine.js accordion |
| Wellness Journal (blog index + single) | `journal.html` | [x] `page-journal.php` + `single.php` + `archive.php` (categories) |
| The Ritual / Subscription info page | `ritual.html` **or** `subscription.html` — build once, not both (see `DESIGN_SYSTEM.md` §0) | [x] `page-subscription.php`, built from `subscription.html` |
| Shipping Policy | new content, styled per system, no mockup | [x] draft copy, base `page.php` |
| Refund Policy | new content, styled per system, no mockup | [x] draft copy, base `page.php` |
| Privacy Policy | new content, styled per system, no mockup | [x] draft copy, base `page.php` |
| Terms of Service | new content, styled per system, no mockup | [x] draft copy, base `page.php` |

- [x] Re-theme any page sourced from the stale olive-gold palette (FAQ) to the canonical orange-gold token set during transcription — do not port `#735c00`-based colors. Verified zero `#735c00` occurrences in FAQ's rendered output.
- [ ] Populate real copy (client-provided) and replace all AI-placeholder photography with real product/lifestyle photography (see `RISKS.md` R6) — **not done this pass**: real client copy was not available, so mockup copy was transcribed as-is (English) and the 4 policy pages used original draft text (R22); all photography is the mockups' own placeholder imagery, now self-hosted rather than hotlinked, but still not real photos. Do not launch without addressing both.
- [ ] SEO basics per page (title, meta description, Rank Math focus keyword) — **not done this pass**, deferred to the full SEO pass (Phase 10) or a follow-up within Phase 4 once real copy exists.
- [ ] **Build the Kiswahili translation for each page via WPML alongside the English version** — **blocked**, WPML is on hold (R20). English builds proceeded per the client's explicit sequencing exception; Kiswahili is the reason every page below is capped at Review.

**Exit criteria — partially met (2026-07-07):** every page above is live on staging in English, responsive, and matches its mockup's design language (verified via HTTP 200 + content-presence checks on all 10 pages, WPCS clean on all files touched). **Not yet met:** Kiswahili versions (blocked on WPML), real content/imagery (blocked on client assets), and client review/approval of this batch.

**Docs to update:** `PAGE_STATUS.md` (every static page moves Not Started → In Progress → Review → Completed as work proceeds) — done, all 10 pages now at Review.

---

## Phase 5 — WooCommerce Integration

**Status: functionally complete (2026-07-08).** Client directed proceeding with dummy/placeholder catalog data rather than waiting on the client's real product data, so real photography/copy/pricing can be swapped in later without any developer involvement (see the "where the admin edits this" note in `PAGE_STATUS.md`).

**Goal:** core commerce plumbing works, unstyled-to-brand is acceptable at this stage (styling is Phase 6).

- [x] **Create the 4 initial product categories** (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules) via Products → Categories, each with a thumbnail/description — **not 4 individual products** (corrected 2026-07-08; see `ARCHITECTURE.md` §1/§9).
- [x] Create the initial product(s) within each category with correct data — **dummy data**: 8 placeholder products (2 per category, varying size/count), TZS pricing, descriptions, reusing the existing placeholder product photography. Real client product data (pricing, descriptions, photos) still needs to replace this before launch — the count per category is not fixed and is expected to change either way.
- [x] Install/configure the category-ordering plugin (see `PLUGIN_DECISIONS.md`) — `taxonomy-terms-order` was already active (Phase 1); confirmed working with the 4 new categories.
- [ ] Confirm with the client whether any category needs variable products (size/flavor variants within one listing) vs. separate simple products per variant — **not resolved this pass**, dummy products were built as separate simple products per size; this is a data-entry decision the client can make/change at any time without developer involvement.
- [x] Cart & Checkout: implement using WooCommerce's block-based Cart/Checkout (see `ARCHITECTURE.md` §6) — both are WooCommerce's own default pages, verified working end-to-end.
- [x] My Account: enable and scope which default endpoints are needed (Orders, Downloads off, Addresses, Account details) — Downloads tab removed via `Nia_Woocommerce::remove_downloads_tab()` (`nia-core`). The custom "My Rituals"/subscriptions view still ties to Phase 9.
- [x] Shop archive / Collection page functional as a true category archive with pagination (unstyled, per this phase's goal) — **the category filter/tab row is deferred to Phase 6** alongside the rest of the Shop archive's visual design (see `ARCHITECTURE.md` §9a, `DESIGN_SYSTEM.md` §10).
- [x] Homepage "Featured Products" section wired to WooCommerce's native Featured-product flag, not hardcoded product IDs (see `ARCHITECTURE.md` §9a) — `page-homepage.php` now queries `wc_get_featured_product_ids()`.
- [ ] Product detail page functional with the one-time/subscription purchase-type radio wired to price swap logic (JS/Alpine) — **not done this pass**: WooCommerce's default single-product template renders correctly (verified all 8 dummy products return 200), but the purchase-type radio/price-swap logic is real Phase 6 build work, not plumbing.
- [ ] Verify WPML's WooCommerce Multilingual & Multicurrency add-on's multicurrency module — **blocked**, WPML remains on hold (R20). Store base currency was corrected from USD to **TZS** (matches Selcom's settlement currency, `ARCHITECTURE.md` §11b) so pricing displays correctly in the meantime.
- [ ] Translate the initial categories and products into Kiswahili — **blocked on WPML**, same as above.
- [x] **Not in the original checklist, but required to unblock everything above:** WooCommerce's storefront was in "Coming Soon" mode (`woocommerce_coming_soon` option, on by default for this install) — disabled so Shop/Product/Cart/Checkout actually render. Also added `add_theme_support('woocommerce')` plus a custom content-wrapper (`functions.php`) so WooCommerce's default templates render inside this theme's header/footer instead of WooCommerce's own generic wrapper markup.
- [x] **Also added:** since Selcom (Phase 7) and real shipping rates don't exist yet, a **temporary placeholder payment method (Cash on Delivery, WooCommerce core)** and a **temporary placeholder flat-rate shipping method** (5,000 TZS, applies everywhere) were enabled so the exit criteria's full purchase flow could actually be exercised. Both are flagged in `CUSTOMIZATIONS.md` as temporary — COD must be disabled once Selcom ships in Phase 7, and the client should confirm real shipping zones/rates before launch.

**Exit criteria — met (2026-07-08):** a full purchase was completed end-to-end in test mode via the real block-based Checkout and Store API (browse → add to cart → checkout with a real shipping method and Cash-on-Delivery payment → order #78 created correctly in HPOS, status "processing," correct line item and total) — functionally correct, visual polish pending (Phase 6). The test order was deleted after verification.

**Docs to update:** `CUSTOMIZATIONS.md` (log every hook/override added) — done. `PAGE_STATUS.md` — done, WooCommerce Pages table updated with an admin-editing note.

---

## Phase 6 — Custom WooCommerce Design

**Status: complete (2026-07-08).**

**Goal:** every WooCommerce-rendered page visually matches its mockup exactly.

- [x] Shop/Collection page: product card with badge + quick-add overlay (`DESIGN_SYSTEM.md` §10, §5), plus the category filter/tab row (reusing the journal/FAQ tab pattern) and pagination so it scales with however many categories/products exist. Built as `woocommerce/archive-product.php` + `content-product.php` theme overrides. Badge is data-driven (real sale % or Featured flag), not hardcoded copy. The mockup's "FORM" filter button was dropped (redundant with the real category tabs) — the native WooCommerce sort dropdown was kept and restyled instead of "SORT".
- [x] Product Detail Page: full layout per `product.html` — gallery, sticky purchase panel, benefit grid, testimonials, WhatsApp inquiry button. Built as `content-single-product.php` override, fully custom (not WC's default hooks, which don't map to this layout) — related products is the only default hook section kept, and its own markup (`woocommerce/single-product/related.php` override, added 2026-07-08) was restyled to match the container/heading/grid treatment of every other section, since WC's default template is an unstyled bare `<section>`/`<h2>`/`<ul>`. Purchase-type radio is a real Alpine-driven price preview; actual subscription checkout is explicitly gated on Phase 9 (the panel says so). WhatsApp number is a placeholder — needs the client's real number before launch.
- [x] Cart page: per `my-cart.html` — line items, upsell section, sticky summary, trust row. Kept WooCommerce's block-based Cart (native, not reimplemented) and re-themed via CSS targeting WC Blocks' own stable class names; the upsell section is WooCommerce's real native cross-sells block (relabeled "Complete Your Ritual"), driven by actual `_crosssell_ids` product relationships, not fake data. Trust row/microcopy added as sibling blocks in the page's block content.
- [x] Checkout: per `checkout.html`, **re-themed to canonical palette**, step-label framing, payment-method tiles (placeholder UI until Phase 7 wires real Selcom methods). Step numbers use the Checkout block's own native `showFormStepNumbers` option (no custom JS needed). A payment-method note explains Mobile Money/Card are Phase 7 work, Cash on Delivery is today's test method. **VAT (18%, Tanzania) configured** — also caught and fixed that the store's base country was still WooCommerce's `US:CA` default, corrected to `TZ`.
- [x] My Account / Dashboard: per `dashboard.html` — sidebar nav (+ mobile equivalent, per Phase 3 pattern), Overview, Order History, My Rituals, Wellness Profile, Settings. Built as `myaccount/my-account.php` + `navigation.php` + `dashboard.php` overrides, plus a new `page-myaccount.php` page template (page.php's narrow container didn't fit this layout). My Rituals and Wellness Profile are real custom endpoints with honest empty-state content (no fabricated subscription/personalization data) rather than dead links or fake data.
- [x] **Added (client request, 2026-07-08, not in the original mockups):** Product Reviews & Ratings on the PDP, moved to directly after the hero/purchase panel per a client follow-up request (originally built below Testimonials). Re-surfaces WooCommerce's native review system (unmodified — comments with `rating` commentmeta, real moderation/verified-owner gating via `comment_form()`) inside the new design, plus a genuinely new "Helpful" vote feature (commentmeta counter, one vote per person via user meta/cookie, toggled via a small AJAX endpoint in the new `Nia_Reviews` class, nia-core). Average-rating summary, clickable star-breakdown filter, and a sort control (Recent/Highest/Lowest/Most Helpful) round it out. Admins see/moderate reviews the same way as any other WordPress comment, via wp-admin → Comments — no new admin screen was needed.
- [x] **Real-browser QA pass (2026-07-08, later same day):** the client reported visibly broken layout from screenshots (misaligned Related Products, a stray sidebar-like block, misplaced badges/bullets on product cards) that the earlier curl-only verification hadn't caught. A headless browser (Playwright/Chromium — installed as a throwaway dev tool in the scratchpad, not part of the repo) was used to actually screenshot Shop + PDP at mobile/desktop, surfacing three real bugs, all now fixed: (1) a default WordPress sidebar widget area rendering on WooCommerce pages (`Nia_Woocommerce::remove_woocommerce_sidebar()`), (2) WooCommerce's own default stylesheets conflicting with this theme's Tailwind CSS — fixed via `woocommerce_enqueue_styles`, which then required adding the product grid's column CSS ourselves since WC's stylesheet had been its only source, and (3) a CSS selector collision (`.products` bare class also matching the related-products wrapper section) plus a stray list bullet from a `<li>` sitting outside a `<ul>`. Full detail in `PAGE_STATUS.md`.

**Exit criteria:** side-by-side comparison against each mockup shows matching layout, type, color, and spacing; responsive behavior verified at mobile/tablet/desktop. **Met for layout/type/color/spacing on Shop, PDP, Cart, and Checkout**, all four now real-browser verified (Playwright screenshots at 390px/1440px, not just curl). **Real-browser QA on Cart/Checkout (2026-07-21)** found and fixed four `input.css` selectors that didn't match WooCommerce Blocks' actual class names (see `PAGE_STATUS.md`/`CUSTOMIZATIONS.md` for the exact corrections) — a Tailwind content-purging issue was also found and fixed the same day: several component classes only ever appear inside `nia-core/includes/` (not previously scanned) or exclusively inside WooCommerce Blocks' own rendered markup (never in any scanned file at all), so Tailwind's JIT was silently dropping them from the compiled stylesheet; `tailwind.config.js` now scans the missing directory and safelists the WC-Blocks-only classes. **Still outstanding (content gap, not a build task):** 3 of the 8 dummy products ("Raw Seamoss — 200g," "Seamoss Powder — 250g," "Seamoss Powder — 500g") have no featured image assigned and show an empty placeholder box on the Shop grid.

**Docs to update:** `CUSTOMIZATIONS.md`, `PAGE_STATUS.md`.

---

## Phase 7 — Payments (Selcom)

**Goal:** real money moves correctly and safely.

- Verify whether an official/maintained Selcom WooCommerce plugin exists (see `PLUGIN_DECISIONS.md`) before writing custom code.
- Build/integrate the Selcom payment gateway (custom `WC_Payment_Gateway`, see `ARCHITECTURE.md` §6): M-Pesa, Tigo Pesa, Airtel Money, card.
- Webhook endpoint with signature verification.
- Sandbox/test transactions for every payment method before going live.
- Confirm with Selcom whether tokenized recurring debit exists (directly informs Phase 9's upgrade path — see `ARCHITECTURE.md` §5, `RISKS.md`).
- Confirm international customer payment path (Selcom is Tanzania-focused — is a second, international-card-friendly gateway needed as a fallback? Flagged in `RISKS.md`).
- Confirm Selcom's actual settlement-currency behavior (TZS-only vs. true USD settlement) and reconcile with the currency switcher added in Phase 5 — implement the "display in USD, charge in TZS with clear confirmation" model (`ARCHITECTURE.md` §11) unless Selcom confirms true USD settlement is available.

**Exit criteria:** live (small real-value) transaction succeeds end-to-end on each supported payment method in production before full launch; failure/decline states handled gracefully with clear customer messaging.

**Docs to update:** `AUTOMATIONS.md` (payment confirmation/failure triggers), `CUSTOMIZATIONS.md`, `RISKS.md` (resolve the recurring-debit and international-payment open questions).

---

## Phase 8 — Notifications (Emails + WhatsApp)

**Goal:** every automation in `AUTOMATIONS.md` fires correctly.

- Brand WooCommerce core emails via template overrides (`ARCHITECTURE.md` §8).
- Build Notify Africa WhatsApp client (`class-nia-whatsapp.php`) and wire every customer + owner trigger from `AUTOMATIONS.md`.
- Build owner-facing notifications (new order, payment, low stock, failed payment, subscription events, sales summary).
- Delivery logging + failure visibility (admin-facing log of WhatsApp send attempts).
- Write Kiswahili versions of every customer-facing automation template (WhatsApp + email) and select the correct language per the customer's site language (see `CUSTOMIZATIONS.md` Multilingual section) — owner-facing automations can stay English-only unless the owner prefers Swahili.
- Test every trigger in staging with real (non-production) numbers/inboxes before enabling in production, **in both languages**.

**Exit criteria:** every row in `AUTOMATIONS.md`'s trigger table has been manually tested and confirmed firing correctly, to the correct recipient, with correct content.

**Docs to update:** `AUTOMATIONS.md` (mark each automation Implemented + tested).

---

## Phase 9 — Subscriptions

**Status: core purchase flow built and verified end-to-end (2026-07-22), ahead of its original place in the sequence — client requested it directly. Pause/cancel and delivery-date automations remain future work (see below).**

**Goal:** the pay-upfront subscription model (`ARCHITECTURE.md` §5, revised 2026-07-22) is live.

- [x] Build `nia_subscription` custom post type + admin visibility (owner can see active subscriptions) — native list-table (Customer/Cadence/Products/Next Delivery/Status columns) + an edit-screen meta box showing the full per-cycle delivery schedule, no bespoke admin page needed.
- [x] Subscription selection (tier/cadence + one or more products + a first-delivery date, via a product picker on the Subscription page) → adds to the real WooCommerce cart at `quantity = per-delivery qty × the cadence's cycle count`, at the admin-set discounted price — checkout is the existing real Cart/Checkout, unchanged. The customer pays for the entire term in that one checkout (client decision, 2026-07-22) — there is no renewal charge to collect later, which is why the daily-cron/renewal-request items from the original checklist below no longer apply to v1.
- [x] Order-to-subscription: on `woocommerce_thankyou`, the paid order's tagged line items are grouped into one `nia_subscription` record per cadence+start-date, with a computed delivery schedule.
- [x] Customer-facing "My Rituals" view in My Account/Dashboard — real data (products, cadence, schedule, next delivery, status), replacing the earlier honest-empty-state placeholder.
- [x] Admin-editable discount settings (Settings → Nia Subscriptions), one percentage per cadence — dummy defaults (10/15/20%) until the client gives real numbers.
- [ ] **Deferred, not built:** pause/cancel (My Rituals shows status read-only; RISKS.md R12's grace-window/owner-workflow question is still open) and automated delivery-date reminders (WhatsApp/email — Phase 8 territory; the schedule data those automations need already exists in `_nia_schedule`).
- [ ] ~~Daily cron: find due renewals → create pending order → trigger WhatsApp/email renewal request with Selcom payment link.~~ — moot under the pay-upfront model; no renewal charge exists to request.
- [ ] ~~Grace-window handling → pause + owner notification if unpaid.~~ — moot for the same reason; the only "non-payment" case now is the original checkout itself, handled like any other order.
- [ ] Confirm final decision on WooCommerce Subscriptions vs. custom engine per Phase 7's Selcom recurring-debit finding — still relevant only if the business later wants true auto-rebilling instead of pay-per-term.

**Exit criteria (original, written before the 2026-07-22 model change):** a full subscription lifecycle (subscribe → renewal reminder → pay → renewed order → repeat; and subscribe → non-payment → paused → owner alerted) demonstrated on staging — superseded; see the pay-upfront flow above, verified end-to-end on local dev (subscribe → real cart/checkout → order paid → `nia_subscription` created with correct schedule → visible in wp-admin and My Rituals). Pause/cancel lifecycle testing applies once that follow-up work is built.

**Docs to update:** `ARCHITECTURE.md` §5 (done), `CUSTOMIZATIONS.md` (done). `AUTOMATIONS.md` — pending, once delivery-date reminders are built (Phase 8 territory).

---

## Phase 10 — SEO

- Rank Math full configuration: sitemaps, schema (Organization, Product, Article for Journal posts), meta titles/descriptions for every page, redirects map (if migrating from any existing URLs).
- Configure the WPML + Rank Math integration: `hreflang` tags on every page, per-language sitemaps, per-language meta titles/descriptions (see `ARCHITECTURE.md` §11a).
- Submit both language sitemaps to Google Search Console, verify property, connect GA4.
- Basic on-page SEO pass on all static + WooCommerce pages, in both languages (heading structure, alt text on real images, internal linking between Journal posts and products).

**Exit criteria:** Search Console verified with no critical crawl errors on either language; Rank Math shows no unresolved critical issues; `hreflang` tags validated; GA4 receiving data.

**Docs to update:** `PAGE_STATUS.md`.

---

## Phase 11 — Testing

- Cross-browser/device QA (the mockups' mobile-nav gap and dashboard-sidebar gap are the highest-risk areas — verify thoroughly).
- Full purchase-flow QA: one-time purchase, subscription purchase, each payment method, failed payment, refund — **repeat in both English and Kiswahili, and in both TZS and USD display modes**.
- Language + currency interaction QA: switching language mid-session doesn't reset/break currency selection and vice versa; language defaults correctly to English for a new visitor.
- Elementor governance QA: confirm no WooCommerce template, header, or footer has been edited via Elementor (should be structurally impossible, but verify) and that any Elementor-edited marketing page still matches `DESIGN_SYSTEM.md` tokens (Global Colors/Fonts still in sync).
- Automation QA: re-verify every `AUTOMATIONS.md` trigger in a production-like environment (not just staging defaults), including Swahili-language variants.
- Performance audit (Core Web Vitals) — confirm compiled CSS, self-hosted fonts, and image optimization are actually reducing load time vs. the raw mockups.
- Security review (Wordfence scan clean, REST webhook signature verification tested against forged requests).
- Accessibility pass (color contrast on the orange-gold-on-ivory combinations, keyboard navigation through the custom radio-card patterns, screen-reader labels on icon-only buttons).
- Backup/restore test (UpdraftPlus) — prove a restore actually works, not just that backups are being created.

**Exit criteria:** no critical/high bugs open; performance budget met; a documented backup restore has been successfully performed.

**Docs to update:** `RISKS.md` (close out resolved risks), `PAGE_STATUS.md`.

---

## Phase 12 — Deployment

See `DEPLOYMENT.md` for the full launch checklist. Summary: final content freeze, DNS/SSL cutover, production Selcom credentials swapped from sandbox to live, monitoring/alerts enabled, client training on the dashboard/admin, post-launch monitoring window.

**Docs to update:** `PAGE_STATUS.md` (all pages → Completed), `DEPLOYMENT.md` (checklist ticked off).

---

## Cross-cutting rule

Every task completed in any phase must update the relevant tracking doc **at the time of completion**, not retroactively at phase end:
- New/changed page → `PAGE_STATUS.md`
- New/changed architectural decision → `ARCHITECTURE.md`
- New WooCommerce hook/override → `CUSTOMIZATIONS.md`
- New/changed automation → `AUTOMATIONS.md`
