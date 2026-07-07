# ARCHITECTURE.md — Nia Nutrition

This document explains every major architectural decision for the build, and why. It supersedes the brief's suggested stack where a change is recommended — each change is justified below. Read alongside `DESIGN_SYSTEM.md` (the UI source of truth) and `PLUGIN_DECISIONS.md` (per-plugin rationale).

---

## 1. Guiding constraints

- **4 product categories at launch (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules), not 4 fixed products — corrected 2026-07-08.** Each category holds one or more products, and both the number of categories and the number of products within each are expected to change over time. The admin needs full, code-free flexibility to add/remove categories and add/remove products within them at any point — nothing in the build may assume a fixed count or hardcode a specific product/category identity. This is a smaller catalog today, not a small catalog forever.
- The real engineering surface area is **automation**, not the catalog: payments (Selcom, mobile-money-heavy), WhatsApp notifications, subscriptions, and reducing the owner's manual workload.
- Complete, pixel-specific UI designs already exist as static HTML/Tailwind mockups. The job is disciplined **implementation and systemization** of that design, not new visual design work.
- Long-lived, maintained-for-years codebase, single developer/small team maintaining it after launch — so boring, standard, well-documented WordPress patterns beat clever ones.

---

## 2. Theme strategy

**Decision: build one custom, lightweight, classic-style WordPress theme with block-editor (Gutenberg) support, as the primary build system for every page — no purchased/framework theme, no Elementor Pro. Elementor (free) is kept installed, scoped specifically to occasional content-only adjustments — see §2a. This reverses the original "remove Elementor" draft decision per client direction (2026-07-08).**

Why not a framework theme (GeneratePress/Kadence/Astra)?
The mockups define a fully custom, specific visual system (12-col editorial grid, `sunlight-shadow` card treatment, sharp-cornered marketing UI vs. rounded "app" UI on account pages, a bespoke Material-Design-3-flavored color-token set). A framework theme's own CSS/customizer options would have to be overridden almost entirely to match — that's paying for a framework and then fighting it. A from-scratch theme has zero CSS to unwind, which is *lighter* than a framework theme with everything switched off, and there's no annual license.

Why not a block (FSE) theme?
Full Site Editing is the right long-term direction for WordPress, but its current tooling (theme.json-driven, template-part editing in-editor) adds complexity for a design this custom without buying much — the business owner will not be restructuring page layouts, only editing text/images inside fixed sections and writing blog posts. A **classic PHP-templated theme with the block editor enabled for content areas** (post content, blog) gives full control over pixel-perfect marketing pages while still giving the owner a normal writing experience for the Wellness Journal.

Why not Elementor **Pro** specifically?
- Cost: recurring license for what is still, even with a growing catalog, a small and simple product range compared to what Elementor Pro/WooCommerce Builder is built to justify.
- Elementor's generated markup is verbose and diverges from the exact, already-approved mockup markup — every page would need to be re-approximated inside Elementor rather than transcribed.
- Pro's main draw over free — the Theme Builder (header/footer/archive templates) and WooCommerce Builder — is exactly the part of the site that must stay custom-coded and hook-driven (see §2a). There's nothing Pro adds that this build actually needs.

**What the business owner gets instead of building pages in a page builder:** a small library of **custom Gutenberg blocks** (see §4) that map 1:1 to the repeating sections identified in `DESIGN_SYSTEM.md` §14 (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter). This is the primary system every page is actually built with — pixel-precise, no page-builder markup bloat, forward-compatible with WordPress's direction (blocks work in classic and FSE themes alike).

**ACF (Advanced Custom Fields):** not required. Native custom blocks with `block.json` attributes cover every flexible-content need the mockups show (no complex repeater-heavy admin forms are needed for a category-based catalog and a dozen static pages — WooCommerce's own product/category screens already handle the catalog's variable size natively, see §9). Adding ACF Pro on top of a custom block library would be redundant — pick one system, not two. If a genuinely awkward one-off field need appears later (e.g. store hours in a footer widget), a single small custom settings page in the Nia Core plugin is cheaper than a new plugin dependency.

### 2a. Elementor (free) — kept, scoped to occasional content edits

**Decision: keep Elementor free installed, for the client's own occasional hands-on adjustments to already-built marketing pages. It is not the primary build tool — the custom block library above is — and it never touches WooCommerce templates or the header/footer.**

Why this is workable rather than a maintenance liability:
- **Free Elementor has no Theme Builder.** It can only edit the content area of an individual page or post — it is *structurally incapable* of reaching the header, footer, archive templates, or WooCommerce's Cart/Checkout/Shop templates. That's not a policy we have to enforce by discipline alone; it's a hard technical boundary of the free tier, which happens to match exactly what should and shouldn't be hand-editable on this site.
- **Governance rule (enforced by the boundary above, and documented in `CUSTOMIZATIONS.md`): Elementor is for static/marketing page content only** (e.g. touching up a homepage section, editing a Journal post's layout, adjusting an About-page block). **Never** for Shop, Product, Cart, Checkout, My Account, or the header/footer template parts — those remain fully custom-coded and hook-driven, full stop.
- **Staying on-brand:** Elementor's Site Settings → Global Colors and Global Fonts will be configured once (Phase 2/3, after `tailwind.config.js` is finalized) to mirror the exact token set in `DESIGN_SYSTEM.md` §1–2 (same hex values, same Playfair Display/Montserrat pairing), so anything built in Elementor picks the same palette/type scale by default rather than Elementor's own defaults.
- **Porting the existing Tailwind styling into Elementor's format:** the compiled Tailwind stylesheet (see §3) is enqueued site-wide regardless of which pages use Elementor, so the exact utility classes already validated in the mockups (`bg-primary`, `sunlight-shadow`, `px-10 py-5 uppercase tracking-widest`, etc.) can be applied directly to any Elementor widget via its "Advanced → CSS Classes" field — this is the literal mechanism for "converting the Tailwind styling into that format": not a rewrite into Elementor's own styling system, but making our existing classes reachable from within Elementor's UI.
- **On top of that, build a small library of Elementor Saved Templates** mirroring the custom Gutenberg block set (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter, promo tile) — see `PROJECT_PLAN.md` Phase 3. This means a content edit in Elementor usually starts from an already-on-brand block, rather than free-styling from Elementor's raw widgets.
- **Ongoing cost of this decision:** two places now hold design tokens (the Tailwind config and Elementor's Global Colors/Fonts) that must be kept in sync if a brand color or font ever changes. This is a real, recurring maintenance obligation, not a one-time setup task — flagged in `RISKS.md`.
- **Performance:** Elementor's own frontend CSS/JS only loads on pages where Elementor is actually used to edit content, not sitewide — so pages built and left alone in the block editor carry no Elementor overhead.

---

## 3. CSS / front-end build pipeline

**Decision: compile Tailwind CSS via a build step (Tailwind CLI + PostCSS), do not use the Tailwind CDN script the mockups currently load.**

The mockups load `cdn.tailwindcss.com` with an inline `tailwind.config` script per page — this is explicitly called out by Tailwind's own docs as **not for production** (no purging, ships the entire framework, recompiles utility classes client-side on every page load via JIT-in-browser, and inline `<script>` config blocks mean every page duplicates and can drift from the others — which is exactly why `checkout.html`/`faqs.html` ended up on a stale color palette; see `DESIGN_SYSTEM.md` §0).

Build approach:
- One canonical `tailwind.config.js` in the theme, containing the token set decided in `DESIGN_SYSTEM.md` (colors, `fontFamily`, `fontSize`, `spacing`, `borderRadius`) — a single source, impossible for one template to drift from another.
- Tailwind CLI (or Vite + the Tailwind plugin) compiles to one purged, minified `style.css`, enqueued once via `wp_enqueue_style`.
- This preserves the exact utility-class markup already validated in the mockups (fast to port HTML → PHP almost verbatim) while being genuinely production-safe and fast.
- Fonts self-hosted (see `DESIGN_SYSTEM.md` §2) instead of Google Fonts CDN — removes a render-blocking cross-origin request and a GDPR third-party data transfer for EU customers.

**JS:** no SPA framework. The mockups' interactivity (quantity stepper, radio-driven price swap, mobile menu, scroll-reveal, quick-add overlay, accordion) is all small, declarative, per-component behavior. **Recommendation: Alpine.js** (~15 KB, no build step required, works well with server-rendered PHP/WooCommerce markup) for anything needing reactive state (quantity, accordions, tabs), plus small vanilla JS modules for one-off effects (IntersectionObserver scroll-reveal, smooth-scroll). Avoid React/Vue — there is no SPA-shaped problem here, and WooCommerce's own checkout/cart blocks are React-based already where that's genuinely needed (see §6).

---

## 4. Custom plugin: "Nia Core"

**Decision: one custom plugin holds all business logic. The theme holds only presentation.**

Rationale: themes can be switched, previewed, or reset from wp-admin; a theme should never be the only place order-processing, payment, or notification logic lives. Separating presentation from business logic (an explicit project principle) means:
- Deactivating/switching the theme never breaks payments, subscriptions, or WhatsApp.
- The plugin is independently testable and versionable.
- Custom WooCommerce hooks live in one discoverable place instead of scattered across `functions.php`.

**Proposed plugin structure:**
```
nia-core/
  nia-core.php                 # bootstrap, activation/deactivation hooks
  includes/
    class-nia-selcom-gateway.php     # extends WC_Payment_Gateway
    class-nia-subscriptions.php      # recurring-order engine (see §5)
    class-nia-whatsapp.php           # Notify Africa API client + WC hook bindings
    class-nia-emails.php             # extra transactional email triggers beyond WC defaults
    class-nia-inventory-alerts.php   # low-stock owner notifications
    class-nia-abandoned-cart.php     # cart-abandonment detection + reminder trigger
    class-nia-blocks.php             # registers custom Gutenberg blocks
    class-nia-settings.php           # admin settings page (API keys, thresholds, templates)
  blocks/
    hero/ benefit-grid/ testimonial/ cta-banner/ newsletter/   # block.json + render.php per block
  admin/
    settings-page.php
  cron/
    subscription-renewals.php
    abandoned-cart-sweep.php
    sales-summary.php
```
All third-party API keys (Selcom, Notify Africa, SMTP) live in the plugin's settings page (stored via the Options API, sensitive values not hardcoded), never in the theme.

**Hooks before template overrides:** for every WooCommerce customization, the default approach is a hook (`woocommerce_*` action/filter) in Nia Core. Template overrides (copying a WC template into the theme's `woocommerce/` folder) are reserved for cases where no hook achieves the needed markup change — each such override must be logged in `CUSTOMIZATIONS.md` with the reason a hook wasn't sufficient, so future maintainers don't "fix" it back to core and silently break the site.

---

## 5. Subscriptions

**Decision: build a lightweight custom recurring-order engine in Nia Core rather than adopting the official WooCommerce Subscriptions plugin, at least for v1.**

Why this needs a real decision (not just "install the plugin"):
- WooCommerce Subscriptions ($) is built around **automatic recurring card charges** — it assumes a stored payment token the gateway can charge unattended on each renewal date.
- The mockup's own payment methods (M-Pesa, Tigo Pesa, Airtel Money — mobile money) generally **cannot be silently re-charged** the way a saved card can; mobile money confirmation typically requires the customer to actively approve a push/USSD prompt at time of charge. This is a real technical constraint, not a design choice, and it must be validated against Selcom's actual API capabilities before Phase 9 (flagged in `RISKS.md`).
- Paying for a plugin whose core value proposition (silent auto-rebilling) likely doesn't work for the majority of this customer base's payment methods is poor value, and "avoid unnecessary plugins" applies directly.

**v1 model — "renewal request," not silent auto-charge:**
1. Customer subscribes to a tier (Weekly/Bi-weekly/Monthly — see `DESIGN_SYSTEM.md` §10) at checkout; Nia Core stores the subscription (product, tier, cadence, next-renewal-date) as custom post data tied to the customer.
2. A daily cron job (`subscription-renewals.php`) finds subscriptions due for renewal, creates a new WooCommerce order in `pending payment` status, and triggers a WhatsApp + email "Your ritual is ready to renew — pay now" notification with a direct payment link (Selcom checkout URL for that order).
3. Customer approves payment as a normal one-off Selcom transaction; standard `woocommerce_order_status_changed` hooks handle fulfillment identically to a regular order.
4. If unpaid after a configurable grace window, a reminder is sent once more, then the subscription is marked "paused" and the owner is notified.

This is intentionally simple, fully hook-driven, and requires no third-party subscription plugin. **Upgrade path:** if Selcom confirms true tokenized recurring debit is available and the business wants zero-touch renewal, WooCommerce Subscriptions (or an extension of this same custom engine to auto-charge via that token) can be layered in later without re-architecting the storefront — the recurring-order *data model* stays the same either way.

---

## 6. Payments — Selcom

**Decision: build a custom WooCommerce payment gateway (`WC_Payment_Gateway` subclass) in Nia Core integrating Selcom's checkout/order API. No generic "Selcom for WooCommerce" plugin is assumed to exist/be maintained — verify availability first (see `PLUGIN_DECISIONS.md`), but plan for custom integration regardless since Selcom's mobile-money flows need to match the exact UI in `checkout.html` (M-Pesa/Tigo Pesa/Airtel Money tiles), not a generic redirect button.**

- Cart/Checkout: use **WooCommerce's block-based Cart and Checkout** (the modern, actively-developed, React-powered core blocks) rather than the legacy shortcode checkout, then restyle them to match the mockup via the block editor + custom CSS/theme.json rather than fighting the legacy template system. This is more future-proof (legacy shortcode checkout is in maintenance mode upstream) and gives cleaner extension points for adding the Selcom payment-method UI.
- Gateway responsibilities: create Selcom order, redirect/embed their payment flow, handle callback/webhook to mark the WooCommerce order paid, reconcile failed/pending states, expose the "pay this renewal" link used by the subscription engine (§5).
- All webhook endpoints registered via the WordPress REST API (`register_rest_route`) under a namespaced route (e.g. `nia/v1/selcom-callback`), with signature/HMAC verification per Selcom's API docs — never trust an unauthenticated callback to mark an order paid.
- **Currency interaction:** the gateway must always submit the amount in whatever currency Selcom actually settles (see §11) — if the customer was browsing in USD, checkout must clearly display the converted TZS amount *before* the Selcom payment step runs, never silently pass a mismatched currency through to the gateway.

---

## 7. WhatsApp notifications

**Decision: a thin API client class in Nia Core wraps Notify Africa's WhatsApp API; every WooCommerce/subscription event that should notify a customer or the owner calls through this one class, never the HTTP API directly from scattered hook callbacks.**

- Centralizing the client means: one place to handle auth/token refresh, one place to log delivery failures, one place to rate-limit, and one place to swap providers later if needed without touching every hook.
- Message templates (order confirmed, shipped, subscription renewal due, low stock alert, etc.) are stored as editable strings via the Nia Core settings page, not hardcoded — the owner or a future dev can tweak copy without touching PHP.
- Every send is logged (timestamp, recipient, template, success/failure) to a custom table or post type for troubleshooting — WhatsApp delivery failures must be visible somewhere, not silent.
- Full trigger list lives in `AUTOMATIONS.md`.

---

## 8. Emails

- Use WooCommerce's own transactional email system as the backbone (order confirmation, processing, completed, refunded, etc.) — customized via **template overrides** in `theme/woocommerce/emails/` (this is the one area where overriding templates, not hooking, is WooCommerce's own recommended extension point) to match the brand's typography/color system.
- Additional automation-driven emails (subscription renewal reminder, abandoned cart, review request, sales summary to owner) are custom emails sent through `WC_Emails`-compatible custom email classes registered in Nia Core, so they inherit the same HTML header/footer wrapper as core WooCommerce emails.
- **Transport:** SMTP via a dedicated business mailbox, delivered through a dedicated SMTP plugin (see `PLUGIN_DECISIONS.md`) rather than PHP `mail()`, for deliverability (SPF/DKIM) and logging.

---

## 9. Data model summary

No custom database tables are required for v1 beyond what a WhatsApp delivery log needs (§7) — everything else rides on WordPress/WooCommerce's existing structures:
- **Product categories:** the standard WooCommerce `product_cat` taxonomy — 4 terms at launch (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules), each with a category thumbnail/description (native term meta). **Not a custom taxonomy or CPT** — this is deliberately just WooCommerce used as intended, specifically so the admin can add, rename, or delete categories at any time via Products → Categories with zero code changes.
- **Products:** standard WooCommerce simple products (variable products too, if the client later wants size/flavor variants within one listing — either way, no architecture change, since both product types are native), each assigned to one category. **The count per category is not fixed** — a category may hold 1 product today and 6 next quarter; the admin manages this entirely through Products → Add New / Edit, no developer involvement required. Nothing in the theme, blocks, or Nia Core plugin may hardcode a specific product ID, category ID, or assume a fixed total count — every catalog-facing template (homepage featured section, shop archive, category navigation) must query dynamically. See `DESIGN_SYSTEM.md` §10/§14 and `CUSTOMIZATIONS.md` for what that means concretely for each affected component.
- **Category/product ordering:** product ordering *within* a category is native WooCommerce (drag-and-drop custom ordering via "Default sorting" on the shop/category archive). Ordering the *categories themselves* (e.g., which appears first in navigation) is **not** natively supported by WordPress/WooCommerce taxonomies — see `PLUGIN_DECISIONS.md` for the small plugin added to cover this narrow, genuine gap.
- **Subscriptions:** custom post type (`nia_subscription`) storing product, tier, cadence, next-renewal date, linked customer ID, linked order history — not WooCommerce Subscriptions' data model, since we're not using that plugin (§5). References a product ID generically (any product in any category can be subscribed to), not a fixed list of 4.
- **Orders:** standard `HPOS` (WooCommerce's High-Performance Order Storage) — enable this rather than legacy post-based orders for a new build; it's the WooCommerce-recommended default going forward and scales better.
- **Customers:** standard WordPress users + WooCommerce customer meta.
- **Journal/blog:** standard WordPress posts + categories.
- **Reviews:** standard WooCommerce product reviews (built on WP comments) — no separate reviews plugin needed unless the client wants photo reviews or a curated on-site testimonial wall distinct from product reviews (flagged as an open question).

### 9a. Building the catalog-facing UI to actually be count-agnostic

The mockups were designed against a flat, known set of exactly 4 products (`index.html`'s "Core Collection," `collection.html`'s product grid). Now that the real model is N categories × M products each, both growing/shrinking over time, three specific places in the build need to be genuinely dynamic rather than a literal transcription of the mockup:

1. **Homepage "Featured Products" section:** query WooCommerce's native **Featured** product flag (`meta_query` on `_featured`, or `wc_get_featured_product_ids()`), not a hardcoded list of product IDs/slugs. The admin marks/unmarks products as Featured from the ordinary product edit screen — no code change needed to swap what the homepage shows. Cap the query at a fixed display count (e.g. 4, matching the mockup's grid) so the layout doesn't break if the admin marks 15 products Featured — display count is a query `limit`, not a data-model constraint.
2. **Shop/Collection archive:** build as a true WooCommerce category archive (`taxonomy-product_cat.php` / the block-based Products block), with pagination for when a category grows past one page, and a category filter/tab row so a customer can move between categories — see `DESIGN_SYSTEM.md` §10 for reusing the existing journal-page tab pattern for this rather than inventing new UI.
3. **Navigation / category exposure:** "Shop" in the header should not hardcode 4 category links. Two viable approaches: (a) a WordPress nav menu populated with "Product categories" menu items, which the admin can add/remove/reorder from Appearance → Menus without a developer, or (b) the Shop archive itself as the single entry point with the category filter/tabs from #2 handling category-level navigation. Recommend (b) as primary (matches the existing single "Shop" nav link in the mockups) with (a) as an option if the client wants categories directly in the header dropdown once there are more of them.

---

## 10. Performance & security architecture

- **Caching:** page cache via LiteSpeed Cache. **Production host confirmed 2026-07-08: Hostinger Business/Cloud (WooCommerce plan)**, which runs LiteSpeed Enterprise natively with WooCommerce pre-configured, NVMe SSD, and a built-in CDN — this is a direct, no-fallback-needed match for the plugin already planned (see `RISKS.md` R5, closed, and `PLUGIN_DECISIONS.md`).
- **Images:** serve WebP/AVIF via an image-optimization plugin (see `PLUGIN_DECISIONS.md`), lazy-load below-the-fold images, and replace every mockup placeholder image with correctly-sized real photography (no un-optimized full-resolution AI-placeholder-sized assets in production).
- **Security:** Wordfence (firewall + malware scan) plus standard WordPress hardening (disable file editing in wp-admin, enforce strong admin passwords/2FA, limit login attempts, keep core/plugins patched). REST endpoints (Selcom webhook) must validate signatures, not rely on obscurity.
- **Backups:** UpdraftPlus scheduled off-site (not just to the same server), tested restore at least once before launch (see `DEPLOYMENT.md`).

---

## 11. Internationalization / localization

### 11a. Language — English (default) + Kiswahili

**Decision (client-confirmed 2026-07-08, reverses the original "English only" assumption): the site supports English and Kiswahili, defaulting to English for new visitors.** WordPress core has no content-translation management system, so this requires a dedicated multilingual plugin — it cannot be built with `__()`/`_e()` string functions alone, since those translate static theme/plugin UI strings, not the client's actual page/product/blog content.

**Decision: WPML, with the official WooCommerce Multilingual & Multicurrency add-on.**
- WPML is the most mature option for a WooCommerce site specifically — its WooCommerce extension handles product, category, and attribute translation natively, which matters more, not less, now that the catalog is category-based and expected to grow: every category and every product added later needs a Swahili version too, not just the initial set, so translation has to be part of the ordinary content workflow (see Phase 5), not a one-time task.
- **This also subsumes the currency-switcher requirement** (§11b): WPML's WooCommerce Multilingual & **Multicurrency** add-on handles language and currency switching as one coherent, officially-supported system. Running WPML for language and a separate, unrelated plugin (e.g. WOOCS) for currency would mean two independently-authored plugins both touching price/display logic on every page — a plausible source of subtle bugs (e.g. a currency selection not persisting correctly across a language switch). **This replaces the standalone currency-switcher plugin decided previously in `PLUGIN_DECISIONS.md`** — verify at the start of Phase 5 that the WooCommerce Multilingual & Multicurrency add-on's multicurrency module meets the same needs (compatibility with the block-based Checkout and the custom Selcom gateway) that were being evaluated for WOOCS; fall back to a standalone currency plugin only if it doesn't.
- **URL structure:** language-prefixed subdirectories (`/en/...`, `/sw/...`) — WPML's recommended default, and best practice for SEO (distinct, crawlable URLs per language with proper `hreflang` tags, rather than cookie-based or parameter-based switching that search engines handle poorly). Confirm compatibility with Rank Math (WPML publishes an official Rank Math integration).
- **Translation workflow:** recommend human/professional translation for customer-facing brand copy (product descriptions, marketing pages, subscription copy) rather than machine translation — a premium wellness brand's voice does not survive literal MT well. WPML supports assigning translation work to a human translator per string/page through its own translation-management screen; its automatic MT integration (if used at all) should be a first-draft aid, not the shipped copy. **Open question:** who performs the Swahili translation and on what timeline relative to each page's build — flagged in `RISKS.md`, needs client input before Phase 4 content population.
- **Switcher UI:** a small header control, text-label based (`EN` / `SW`), sitting alongside the currency switcher in the icon row (see `DESIGN_SYSTEM.md` §7/§10) — **not** flag icons, since Kiswahili is spoken across multiple countries (Tanzania, Kenya, Uganda, DRC, etc.) and a single national flag would misrepresent it.
- Every page in `PAGE_STATUS.md` must have both an English and a Kiswahili version completed before it is marked Completed — this is now a hard exit criterion across Phases 4–6, not an afterthought bolted on post-launch.

### 11b. Currency — TZS + USD

**Decision (client-confirmed 2026-07-07): TZS and USD, with a customer-facing switcher**, now implemented via **WPML's WooCommerce Multilingual & Multicurrency add-on** (§11a) rather than a standalone currency plugin. See `DESIGN_SYSTEM.md` §10 for the switcher's UI spec.

- **Settlement-currency question (open — resolve in Phase 7):** switching the *displayed* price is straightforward; what actually gets *charged* through Selcom is the harder question. Selcom is a Tanzania-focused aggregator and its mobile-money rails (M-Pesa/Tigo Pesa/Airtel Money) almost certainly settle only in TZS regardless of what currency the customer was browsing in. Two viable models:
  1. **Display in USD, charge in TZS** (recommended default): the switcher is a browsing convenience; at checkout the order total is converted to TZS at the prevailing rate and the customer is clearly shown the exact TZS amount they're being charged before confirming. Works regardless of what Selcom's card processing supports, and carries no FX settlement risk for the business.
  2. **True dual settlement:** only viable if Selcom's card processing can genuinely settle a transaction in USD — unconfirmed. If the client wants real USD settlement and Selcom can't provide it, a secondary international gateway (Stripe/PayPal) may be needed for non-Tanzanian card payments.
  Build model 1 first; only move to model 2 if Phase 7 confirms Selcom supports it.
- **Exchange rate source:** needs a defined, auditable source (an automatic daily-rate feed from WPML's multicurrency module, or an owner-controlled manual rate in wp-admin) and a rounding rule — do not leave this undocumented. Decide during Phase 5 (see `RISKS.md` R14).

---

## 12. Environments

- **Local:** current Local by Flywheel install (`nia-seamoss-products.local`), PHP 8.2, WP-CLI available — this is the active dev environment.
- **Staging:** a clone of production, used for QA before each phase's sign-off and for client review of static pages (Phase 4) before WooCommerce work begins.
- **Production:** **Hostinger Business/Cloud (WooCommerce plan) — confirmed 2026-07-08** (see `PLUGIN_DECISIONS.md`). LiteSpeed Enterprise, WooCommerce pre-configured, NVMe SSD, built-in CDN; staging should be provisioned on the same host/stack where possible so QA reflects production caching/server behavior accurately.

Every environment runs the same theme + Nia Core plugin from version control; environment-specific values (API keys, URLs) are kept out of code via `wp-config.php` constants or environment variables, never committed.
