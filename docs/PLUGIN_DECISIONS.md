# PLUGIN_DECISIONS.md — Nia Nutrition

Every plugin in the stack, kept, dropped, or added, with the reasoning. Default posture is "no plugin unless it earns its place" — see `ARCHITECTURE.md` for the underlying "hooks/custom code over plugins" principle.

Current site state (verified via `wp-cli`, updated 2026-07-07 after Phase 2 theme foundation work): WooCommerce installed and active with HPOS enabled; Rank Math, Wordfence, UpdraftPlus, Fluent Forms, FluentSMTP, the category-ordering plugin (`taxonomy-terms-order`), CookieYes (`cookie-law-info`), and ShortPixel all installed and active; Elementor (free) remains installed, scoped as described below, with its Global Colors/Fonts now synced to `DESIGN_SYSTEM.md` (`ARCHITECTURE.md` §2a); the custom **Nia Theme is scaffolded and active**, replacing `twentytwentyfive` (compiled Tailwind, self-hosted fonts, base templates — see `PROJECT_PLAN.md` Phase 2). **WPML is not yet installed — blocked on a client-purchased license, see `RISKS.md` R20.** This section otherwise remains the plan/reasoning record, not a live inventory — check `wp plugin list`/`wp theme list` for the current authoritative state.

---

## Ecommerce core

### WooCommerce — **Kept**
No real alternative for WordPress-native ecommerce at this scale with this much hook/extension ecosystem maturity. Enable **HPOS (High-Performance Order Storage)** on install — it's WooCommerce's own recommended default for new stores.

### Category ordering (e.g. "Category Order and Taxonomy Terms Order," free) — **Added (2026-07-08, following the categories-not-products correction)**
The catalog is 4 product **categories** at launch (not 4 products), each holding a variable, admin-managed number of products — and both axes are expected to grow/shrink over time (see `ARCHITECTURE.md` §1/§9). Product ordering *within* a category is native to WooCommerce (drag-and-drop custom sorting on the shop/category archive) — confirmed, no plugin needed there. But **ordering the categories themselves has no native WordPress/WooCommerce equivalent** (`product_cat` terms have no built-in drag-reorder), and the admin explicitly needs this flexibility, not as a nice-to-have but as a stated requirement. A small, long-established, free taxonomy-ordering plugin closes this narrow, real gap — this is the same "well-solved problem, don't build it custom" logic applied elsewhere in this document (e.g. the currency switcher), not a new precedent.

### Currency switcher — **Superseded by WPML's WooCommerce Multilingual & Multicurrency add-on (see Multilingual section below)**
Originally planned as a standalone plugin (e.g. WOOCS) when TZS/USD switching was first requested (2026-07-07). Now that multilingual support has also been confirmed (2026-07-08), currency switching is handled by WPML's WooCommerce Multilingual & Multicurrency add-on instead — see below for why consolidating the two into one plugin is the better call. Do not install a separate standalone currency-switcher plugin unless that add-on's multicurrency module proves inadequate during Phase 5 verification.

---

## Multilingual

### WPML + WooCommerce Multilingual & Multicurrency — **Added (client-requested 2026-07-08: English default + Kiswahili)**
Not in the original stack list. WordPress core has no content-translation system, so a dedicated plugin is unavoidable here — this is the same "narrow, well-solved problem, don't reinvent it" case as the currency switcher was. **WPML** is the most mature choice specifically because of its **WooCommerce Multilingual & Multicurrency** add-on, which does two things at once:
1. Translates products, categories, attributes, and subscription-tier copy alongside ordinary pages/posts — a generic multilingual plugin without dedicated WooCommerce support would leave the shop half-translated.
2. **Handles the TZS/USD currency switcher role** that was previously scoped to a standalone plugin (WOOCS) — see the superseded entry above. One plugin covering both language and currency is preferable to two separate plugins each independently touching price/display logic on every page, which is a realistic source of subtle bugs (e.g. currency selection not surviving a language switch).

**Alternatives considered:** Polylang (free core, but WooCommerce compatibility needs a separate paid "Polylang for WooCommerce" add-on, and it has no equivalent built-in multicurrency module — currency would still need a second, separate plugin) and TranslatePress (good visual/on-page translation UX, weaker WooCommerce-specific tooling at the product/attribute level). WPML's combined language+currency WooCommerce extension is the best fit for this site's specific combination of requirements (a category-based catalog needing translated commerce data as it grows, plus a currency switcher) rather than a generically "best" multilingual plugin in isolation.

**Verify in Phase 5:** confirm the WooCommerce Multilingual & Multicurrency add-on's multicurrency module is actually compatible with the block-based Cart/Checkout and the custom Selcom gateway before relying on it — if it isn't, fall back to WPML for language + a separate currency plugin, rather than assuming compatibility. See `ARCHITECTURE.md` §11, `RISKS.md`.

---

## Theme / Page building

### Elementor (free, currently installed) — **Kept, scoped to occasional content-only edits (client direction, 2026-07-08 — reverses the earlier "Removed" draft decision)**
Not the primary build tool — every page is still built with the custom theme + custom Gutenberg blocks (see `ARCHITECTURE.md` §2). Kept specifically so the client can make occasional hands-on adjustments to already-built marketing-page content. This is workable at low risk because free Elementor has no Theme Builder — it is structurally unable to touch the header, footer, or WooCommerce templates, so the boundary that matters (never let a page builder near checkout/cart/product logic) is enforced by the plugin's own limitations, not just by policy. Full integration approach (Global Colors/Fonts sync, porting Tailwind utility classes into Elementor via CSS Classes, a Saved Template library mirroring the custom blocks) is in `ARCHITECTURE.md` §2a; governance rule (which pages/areas it may touch) is in `CUSTOMIZATIONS.md`.

### Elementor Pro — **Not adopted**
The brief listed this as "only if necessary," and the client has only requested the free tier. Pro's main additions — Theme Builder and WooCommerce Builder — are exactly the areas that must stay custom-coded (header/footer/WooCommerce templates), so Pro wouldn't be used even if licensed. Revisit only if the client later wants to build entirely new page structures (not just edit existing content) without developer involvement.

### ACF / ACF Pro — **Not adopted**
Custom Gutenberg blocks (`block.json` + `render.php`, living in the Nia Core plugin) cover every flexible-content need identified in the mockups (hero, benefit grid, testimonial, CTA banner, newsletter). Running both ACF and a custom block library would be two systems doing the same job. Revisit only if a specific field need appears that blocks genuinely can't cover (rare at this scope).

### Custom theme ("Nia Theme") — **Added (net-new, built in-house)**
Not a plugin, but recorded here for completeness: replaces `twentytwentyfive`. See `ARCHITECTURE.md` §2–3 for the build approach (compiled Tailwind, self-hosted fonts, classic PHP templates + block editor for content).

---

## Payments

### Selcom (custom integration) — **Kept, built as a custom gateway**
No mainstream, actively-maintained "Selcom for WooCommerce" plugin is assumed available off-the-shelf — **verify this explicitly at the start of Phase 7** (search WordPress.org and Selcom's own developer docs/GitHub before writing code; if a well-maintained official plugin exists, prefer it over custom code to reduce long-term maintenance surface). Absent that, build a custom `WC_Payment_Gateway` subclass in Nia Core, because the checkout UI needs specific mobile-money provider tiles (M-Pesa/Tigo Pesa/Airtel Money) matching the mockup exactly, not a generic redirect button a generic plugin would provide.

### WooCommerce Subscriptions (official, paid) — **Not adopted for v1**
See `ARCHITECTURE.md` §5: its core value (silent recurring card charges) doesn't map cleanly onto mobile-money payment methods, which typically need active customer approval per charge. A custom lightweight "renewal request" engine in Nia Core is cheaper and matches the real payment constraint. Revisit if Selcom confirms tokenized auto-debit is genuinely available and the client wants zero-touch renewals at scale.

---

## Messaging

### Notify Africa WhatsApp API (custom integration) — **Kept, built as a thin client class**
No off-the-shelf plugin needed — this is a straightforward REST API client. Centralized in one class in Nia Core (`class-nia-whatsapp.php`) rather than scattered `wp_remote_post` calls in hook callbacks, so auth, logging, and template management live in one place. See `ARCHITECTURE.md` §7.

---

## Email

### WooCommerce Email Templates (core) — **Kept**
Customized via template overrides in `theme/woocommerce/emails/` — the one place WooCommerce itself recommends overriding templates rather than hooking. See `ARCHITECTURE.md` §8.

### SMTP — **FluentSMTP recommended over WP Mail SMTP**
The brief listed generic "SMTP." Recommendation: **FluentSMTP** (free, no upsell nagging, from the same vendor as Fluent Forms — already in the stack, so one vendor relationship instead of two, and shared UI conventions for the admin). WP Mail SMTP's free tier is more aggressively upsold and functionally equivalent otherwise. Either works technically; FluentSMTP is the better default here specifically because Fluent Forms is already committed to.

### Business email — **Kept**
Client needs a real business mailbox (e.g. Google Workspace or the hosting provider's mail) for the SMTP "from" address and for the owner's own order-notification inbox — this is a hosting/account decision, not a plugin, tracked in `DEPLOYMENT.md`.

---

## SEO

### Rank Math — **Kept**
Strong free-tier feature set (schema/structured data including Product schema, sitemaps, redirects) fits a lean ecommerce build without needing the Pro tier at this scale. Good WooCommerce integration out of the box.

---

## Hosting

### Hostinger — Business/Cloud Hosting, WooCommerce plan — **Confirmed (client-confirmed 2026-07-08)**
Runs LiteSpeed Enterprise natively with WooCommerce pre-installed and pre-configured (Object Cache, NVMe SSD, AMD EPYC, built-in CDN), which is an exact match for the LiteSpeed Cache plugin already planned in the stack — no fallback caching plugin needed. Chosen over a Tanzania-local host (e.g. Tayo Host/Truehost Tanzania — better local support/latency but less proven at scale) and a Kenya-based regional host (HostAfrica/Hostpinnacle — similar trade-off), in favor of global CDN reach for the international-customer requirement plus a confirmed, out-of-the-box LiteSpeed/WooCommerce match. Also ruled out premium managed WP hosts (Kinsta/WP Engine/Cloudways) — Nginx-based, would have required the WP Rocket fallback instead, at meaningfully higher cost.

## Caching

### LiteSpeed Cache — **Kept, confirmed**
Production host (Hostinger Business/Cloud) runs the LiteSpeed web server natively, so this plugin is used as originally planned — no longer conditional. See `RISKS.md` R5 (closed) and `ARCHITECTURE.md` §10/§12.

---

## Security

### Wordfence — **Kept**
Solid free firewall + malware scanning tier, well-maintained, widely deployed (meaning good compatibility precedent with WooCommerce). Acceptable resource footprint at this site's scale.

---

## Backups

### UpdraftPlus — **Kept**
Standard, reliable, supports off-site storage destinations (not just local) — configure to ship backups off-server (e.g. cloud storage), not just to the same host, so a host-level incident doesn't take out backups too. See `DEPLOYMENT.md`.

---

## Forms

### Fluent Forms — **Kept**
Lighter and more modern than Contact Form 7, good synergy with FluentSMTP (§Email) and Fluent's CRM add-on if the client ever wants basic lead/contact tracking later. Used for the Contact page form (`contact.html`) and any future non-WooCommerce forms (e.g. wholesale inquiry).

---

## Analytics & Consent

### Google Analytics 4 + Google Search Console — **Kept**
Standard, free, no real alternative for this scope. Implement via Rank Math's GA integration or a minimal custom snippet — avoid adding a whole separate "Google integration" plugin just for this if Rank Math or a simple functions.php snippet covers it.

### Cookie consent — **Added: lightweight consent plugin (CookieYes or Complianz free tier)**
Not explicitly in the original stack list but required given GA4 + international (including EU-reachable) customers. A lightweight, free-tier banner plugin is lower-effort and more maintainable than hand-rolling consent-string logic, and this is exactly the kind of narrow, well-solved problem a small plugin is right for (unlike page-building or subscriptions, which the project already has bespoke needs for).

---

## Images & Performance

### Image optimization — **Added: ShortPixel (or Imagify) recommended**
Not named in the original brief but required to keep product/lifestyle photography performant (the mockups' placeholder images will be replaced with real photography — see `RISKS.md`). Either service's free/low tier is sufficient at the catalog's current small scale; re-check tier limits if the catalog grows substantially (see `ARCHITECTURE.md` §1/§9 — category/product counts are expected to change over time). Pick based on the chosen host's existing integrations (some hosts bundle one).

### General performance optimization — **No dedicated plugin; architectural**
Covered by the compiled-CSS build pipeline, self-hosted fonts, image optimization, and caching layer above — not by an additional "performance" plugin. Adding a performance plugin on top of a properly built theme is usually redundant load, not extra speed.

---

## Summary table

| Plugin | Status | Notes |
|---|---|---|
| WooCommerce | Kept | HPOS enabled |
| Category ordering (e.g. Category Order and Taxonomy Terms Order) | **Added** | Native gap: WooCommerce has no drag-reorder for `product_cat` terms; product-within-category ordering is already native |
| WPML + WooCommerce Multilingual & Multicurrency | **Added** | EN (default) + Swahili; also provides TZS/USD currency switching (supersedes standalone WOOCS plan) |
| Elementor (free) | **Kept — scoped** | Content-only edits on marketing pages; never WooCommerce/header/footer (structurally enforced by the free tier's lack of Theme Builder) |
| Elementor Pro | **Not adopted** | Custom blocks instead; Pro's Theme/WooCommerce Builder wouldn't be used anyway |
| ACF / ACF Pro | **Not adopted** | Custom blocks instead |
| Selcom integration | Kept (custom code) | Verify no official plugin exists first |
| WooCommerce Subscriptions | **Not adopted (v1)** | Custom renewal-request engine instead |
| Notify Africa WhatsApp | Kept (custom code) | Thin client class in Nia Core |
| WooCommerce Emails | Kept | Template overrides for branding |
| SMTP | Kept, **FluentSMTP** recommended over WP Mail SMTP | Vendor synergy with Fluent Forms |
| Rank Math | Kept | Free tier sufficient |
| Hosting: Hostinger Business/Cloud (WooCommerce plan) | **Confirmed** | LiteSpeed-native, WooCommerce pre-configured, NVMe SSD, built-in CDN |
| LiteSpeed Cache | **Confirmed** | Host runs LiteSpeed natively — no fallback needed |
| Wordfence | Kept | |
| UpdraftPlus | Kept | Off-site destination required |
| Fluent Forms | Kept | |
| GA4 / Search Console | Kept | |
| Cookie consent | **Added** | CookieYes/Complianz free tier |
| Image optimization | **Added** | ShortPixel/Imagify |
