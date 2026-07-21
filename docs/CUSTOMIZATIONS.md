# CUSTOMIZATIONS.md — Nia Nutrition

Every planned WooCommerce customization, logged here **before or as** it's built. Per `ARCHITECTURE.md` §4: default to a hook (action/filter) in the Nia Core plugin; use a template override only when no hook achieves the required markup change, and state why in the "Why not a hook" column when that happens.

Status values: **Not Started** · **In Progress** · **Review** · **Completed**

---

## Product & Catalog

**Corrected 2026-07-08:** the catalog is 4 product **categories** (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules), each holding a variable, admin-managed number of products — not 4 fixed products. See `ARCHITECTURE.md` §1/§9/§9a. Every row below must work for any number of categories/products, not just the initial set.

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Category archive (Shop/Collection page) — paginated, count-agnostic | Template override: `woocommerce/archive-product.php`, wrapped in the theme's header/footer via a custom `woocommerce_before_main_content`/`woocommerce_after_main_content` wrapper (`functions.php`) | Editorial header + category tab row needed bespoke markup the default template + hooks don't expose | Completed (2026-07-08) |
| Category filter/tab row on Shop archive | Custom markup querying `get_terms('product_cat')` inside `archive-product.php`, styled per the existing journal/FAQ tab pattern (`DESIGN_SYSTEM.md` §4 item H) | — | Completed (2026-07-08) |
| Homepage "Featured Products" section sourced from native Featured-product flag | `wc_get_featured_product_ids()` capped at 4 via `WP_Query( 'post__in' )` in `page-homepage.php` | — | Completed (2026-07-08) |
| Category display ordering (admin-controlled) | Category-ordering plugin (`taxonomy-terms-order`, see `PLUGIN_DECISIONS.md`) — already installed/active (Phase 1); admin can drag-reorder the 4 categories from Products → Categories any time | — | Completed (2026-07-08) — plugin active, categories created; no reorder needed yet since only 4 exist |
| Product card badge (real sale % / "Best Seller" for Featured products) | Template override: `content-product.php`, badge text computed from `$product->is_on_sale()`/`is_featured()` — data-driven, not hardcoded copy | Needed to sit inside the same custom card markup as the quick-add overlay below | Completed (2026-07-08) |
| Quick-add hover overlay on Shop archive | `content-product.php` override wraps `woocommerce_template_loop_add_to_cart()` (real AJAX add-to-cart, kept) in the hover-reveal markup; text relabeled "Quick Add" via `woocommerce_product_add_to_cart_text` filter (`Nia_Woocommerce`) | Overlay positioning/reveal is bespoke markup | Completed (2026-07-08) |
| Currency display format | Native `wc_price()` output (store base currency corrected to TZS in Phase 5) — no custom format filter needed, WooCommerce's own TZS formatting matches the design intent closely enough | — | Completed (2026-07-08) |
| One-time vs. subscription purchase-type radio on PDP | `content-single-product.php` — Alpine `x-model`-driven radio + price preview (15% subscription discount computed client-side from the real regular price) | Custom Alpine-driven price swap isn't a hook-exposed behavior; the real add-to-cart button is still WooCommerce's own `woocommerce_template_single_add_to_cart()` | Completed (2026-07-08) — UI/price-preview only; actual subscription checkout is a Phase 9 dependency, stated explicitly in the panel copy |
| PDP gallery + sticky purchase panel layout | Template override: `content-single-product.php` | Layout structure (sticky column, gallery position, click-to-swap gallery) is a page-level arrangement the standard hooks don't expose | Completed (2026-07-08) |
| WhatsApp inquiry button on PDP | Bespoke markup in `content-single-product.php`, `wa.me` link | — | Completed (2026-07-08) — uses a placeholder phone number; needs the client's real WhatsApp Business number before launch |
| "Added to Ritual" add-to-cart button micro-interaction | Not built | The mockup's JS swap only makes sense with an AJAX (no-reload) single-product add-to-cart, which isn't wired up; the real button is a standard full-page-reload submit today (verified working in Phase 5's e2e test) | Deferred — revisit if/when single-product AJAX add-to-cart is added |
| Product Reviews & Ratings (client-requested, 2026-07-08 — not in the original mockups) | `Nia_Reviews` (nia-core) + a Reviews & Ratings section in `content-single-product.php`. Reviews are unmodified WordPress comments (`comment_type=review` + `rating` commentmeta) — WooCommerce's own submission/moderation/verified-owner-gating/average-rating-caching is reused as-is via `comment_form()`, not reimplemented. New: star-breakdown filter + sort links (plain query vars, no AJAX needed for read-only display), and a "Helpful" vote — real new state with no WP/WC equivalent, one vote per person (user meta if logged in, a cookie for guests), toggled via `wp_ajax_nia_toggle_helpful`/`wp_ajax_nopriv_nia_toggle_helpful` | The star-breakdown/sort links and the review-card layout are bespoke markup a hook can't produce; the Helpful vote is new state with no hook to attach to at all | Completed (2026-07-08) — admins see/moderate reviews the same way as any other comment, via wp-admin → Comments; no separate admin screen was built since none was needed |
| Related Products section restyled to match the rest of the PDP | Template override: `woocommerce/single-product/related.php` — same container/heading/grid classes as every other PDP section, reusing WC's own related-product query and `content-product.php`'s card per item | WC's default template is a bare, unstyled `<section>`/`<h2>`/`<ul>` with no hook exposing its wrapper markup | Completed (2026-07-08) |
| Removed default "Shop sidebar" widget area from Shop archive + PDP | `Nia_Woocommerce::remove_woocommerce_sidebar()` — unhooks `woocommerce_get_sidebar` from the `woocommerce_sidebar` action (hooked on `wp`, after WC registers it) | A hook fully solves this; no override needed | Completed (2026-07-08) — this theme has no sidebar concept anywhere; the default WordPress "sidebar-1" widget area (Search/Pages/Archives/Categories, still carrying fresh-install default widgets) was rendering unstyled after the product grid on Shop and after Related Products on the PDP, since both of WooCommerce's own templates call `do_action('woocommerce_sidebar')` |
| Disabled WooCommerce's own default frontend stylesheets | `add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' )` in `functions.php` — WooCommerce's own sanctioned mechanism for themes that fully re-theme the storefront | A hook fully solves this; no override needed | Completed (2026-07-08) — `woocommerce-layout.css`/`woocommerce.css`/`woocommerce-smallscreen.css` were still loading alongside our Tailwind stylesheet; `woocommerce-smallscreen.css`'s higher-specificity mobile-only rules were fighting our card markup (badges/quick-add overlays losing their absolute positioning and collapsing into normal document flow, stray list-style bullets on card titles). Doesn't affect `wc-blocks.css` (Cart/Checkout blocks' own separate handle, still needed and already re-themed) |
| Product grid columns (Shop archive + Related Products) | `ul.products { @apply grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter; }` in `input.css`, matching `collection.html`'s column counts exactly | Dequeuing WC's default stylesheet (above) removed the only source of `.products` grid CSS the site had; a hook can't add CSS, so this is a plain component-layer rule, not a template override | Completed (2026-07-08) — verified via real browser screenshots (Playwright, mobile + desktop) after the previous two fixes still showed a single-column stack on desktop. Scoped to `ul.products` specifically, not a bare `.products` class, since WooCommerce's related-products wrapper `<section>` also carries a literal `products` class token (`class="related products"`) that a bare selector would have turned into a grid too — caught the same way, via a real screenshot showing the heading and card squeezed into one grid cell. `single-product/related.php`'s wrapper was also changed from a bare `<div>` to a real `<ul>`, since `content-product.php`'s root `<li>` needs a list ancestor to inherit `list-style: none` from — without one the browser's default UA stylesheet gives `<li>` its bullet back |

## Cart

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Cart line-item layout (thumbnail + stepper + price row) | CSS-only re-theme of the Cart block's own stable class names (`.wc-block-cart-items__row`, `.wc-block-components-quantity-selector`, etc.) — no override needed, the block markup already achieved this | — | Completed (2026-07-08), class names corrected 2026-07-21 (see below) |
| "Complete Your Ritual" upsell section | WooCommerce's real native `woocommerce/product-collection` cross-sells block (already present in the Cart page's default content), relabeled and restyled via a block-content edit + CSS; cross-sell relationships set via real `_crosssell_ids` product meta | — | Completed (2026-07-08) — card layout is the block's native vertical grid (restyled on-brand), not a pixel-exact rebuild of the mockup's horizontal card layout — see note below |
| Promo code field parity between Cart and Checkout | Native WooCommerce Cart block already includes a coupon field by default | — | Completed (2026-07-08) — no work needed, already present |
| Trust signal row (SSL, delivery, payment icons) | Block-content edit: a `core/group` + `core/paragraph` blocks added as siblings after the Cart block's checkout button, styled via CSS (`nia-cart-trust-row` classes) | Cart block has no native "trust row" slot | Completed (2026-07-08) |

**Scope decision (2026-07-08):** `my-cart.html`'s upsell cards are horizontal (image left, content right). WooCommerce's native cross-sells block renders a vertical card grid with no supported way to flip its internal layout without overriding the Product Collection block's inner templates — disproportionate effort for a cross-sell widget. Restyled the native vertical grid on-brand (sunlight-shadow, hover lift, "Quick Add" text) instead of forcing a pixel-exact horizontal rebuild.

## Checkout

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Re-theme Checkout from stale olive-gold palette to canonical orange-gold | CSS only (compiled Tailwind using canonical tokens, targeting the Checkout block's stable class names) | — | Completed (2026-07-08) |
| "Step X of 3" section labels | Checkout block's own native `showFormStepNumbers` attribute, set via a block-content edit (`{"showFormStepNumbers":true}`) | Native option — no custom CSS/JS needed, avoided reimplementing something WooCommerce already ships | Completed (2026-07-08) |
| Mobile Money payment method tiles (M-Pesa/Tigo Pesa/Airtel Money) | Selcom gateway's `payment_fields()` method | Still genuinely Phase 7 work — no fake/non-functional gateway tiles were added in the meantime; a plain-text note next to the Payment step explains this instead | Not Started |
| Order summary sticky sidebar + trust badges | Checkout block layout + CSS (`.wc-block-checkout__sidebar`) for stickiness/card styling; trust badges added via a block-content edit (`core/group` + `core/paragraph` inside the order summary block) | — | Completed (2026-07-08) |
| Minimal checkout header/footer (no nav, no icons) | Conditional template part: `is_checkout()` check already built in Phase 3's `footer.php`/`header.php` | — | Completed (Phase 3), verified again this phase |
| VAT (18%) line item | WooCommerce tax settings — `woocommerce_calc_taxes` enabled, a Tanzania (TZ) standard-rate tax row added at 18% | — | Completed (2026-07-08) — verified against a real test order (correct tax + total, order deleted after) |

## My Account / Dashboard

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Sidebar navigation (Overview, My Rituals, Order History, Wellness Profile, Addresses, Settings, Logout) | Template override: `myaccount/navigation.php`, iterating `wc_get_account_menu_items()`; ordering/labels/icons set via `Nia_Woocommerce::add_and_reorder_dashboard_menu_items()` | Dark-gradient fixed-sidebar markup isn't something the default nav template/hooks produce | Completed (2026-07-08) — "Addresses" kept as its own real tab rather than folding into "Settings" like the mockup implies, since address management is genuinely necessary functionality |
| Downloads tab removed from My Account nav (catalog is 100% physical products, no downloadables planned) | Filter: `woocommerce_account_menu_items` in `Nia_Woocommerce` class (`nia-core/includes/class-nia-woocommerce.php`) | — | Completed (2026-07-08) |
| Mobile sidebar equivalent (not in any mockup) | Same `navigation.php` override — its own Alpine `x-data` scope (`dashboardMenuOpen`) + a `$dispatch`/`.window` event so the hamburger trigger and drawer don't need to share a DOM ancestor | Same hamburger/drawer visual pattern as the main nav, but a separate Alpine instance since header.php's drawer only covers the primary site menu | Completed (2026-07-08) |
| "My Rituals" (subscriptions) endpoint | Custom endpoint via `add_rewrite_endpoint()` + `woocommerce_account_my-rituals_endpoint` action (`Nia_Woocommerce`) | Custom endpoint (not core WooCommerce) | Completed (2026-07-08) — honest empty-state content (no `nia_subscription` CPT exists yet — that's Phase 9); explicitly states the dependency in the panel copy rather than showing fake data |
| "Wellness Profile" endpoint | Same mechanism as My Rituals, `woocommerce_account_wellness-profile_endpoint` action | Custom endpoint | Completed (2026-07-08) — no wellness-profile data model was ever scoped in `ARCHITECTURE.md`; this is a real landing spot (links to the Journal) rather than fabricated personalization |
| Order History styling (rounded "app card" treatment) | CSS only, targeting the native `.woocommerce-orders-table` class names | No override needed — the default template's markup was sufficient | Completed (2026-07-08) |
| Active subscription status badge (uses `primary` token, not a dedicated success color) | Not built | No subscription data exists yet (Phase 9 dependency) — nothing to badge | Deferred to Phase 9 |
| Overview welcome header + real order-based status indicator | Template override: `myaccount/dashboard.php` — status reflects whether the customer has any real orders, not a fabricated "Active Subscription" state | Bespoke welcome/status/My-Rituals-summary/Wellness-Journal-aside layout isn't the default dashboard.php's content | Completed (2026-07-08) |
| Overview → Wellness Journal recommendations | `WP_Query` for the 2 latest real Journal posts, same pattern as `page-journal.php` | — | Completed (2026-07-08) |
| My Account page container (wider layout than page.php's default) | New page template `page-myaccount.php`, assigned to the My Account page | page.php's `max-w-3xl` centered container doesn't fit a fixed-sidebar dashboard layout | Completed (2026-07-08) |

## Emails

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Branded header/footer wrapper on all WooCommerce core emails | Template override: `woocommerce/emails/email-header.php`, `email-footer.php` | WooCommerce's own documented/recommended extension point for email branding — this is the sanctioned exception to "hooks before overrides" | Not Started |
| Individual email body content (order confirmation, shipped, etc.) | Template override: `woocommerce/emails/*.php`, matched to per-template need | Same as above | Not Started |
| Custom automation emails (renewal reminder, abandoned cart, review request, sales summary) | New `WC_Email` subclasses registered via `woocommerce_email_classes` filter | — | Not Started |

## Payments

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Selcom payment gateway | New `WC_Payment_Gateway` subclass registered via `woocommerce_payment_gateways` filter | — | Not Started |
| **Temporary placeholder gateway** — WooCommerce core's built-in "Cash on Delivery" enabled so a full order can be placed in test mode ahead of Selcom (Phase 7) | Core `WC_Gateway_COD`, enabled via its own settings (`woocommerce_cod_settings`), no custom code | Not a hook/override — flagging here so it isn't mistaken for the real payment method; **must be disabled once Selcom ships in Phase 7** | Completed (2026-07-08), temporary |
| Selcom webhook → order status update | Custom REST route (`register_rest_route`) + `woocommerce_order_status_changed` | — | Not Started |
| Failed payment handling/messaging | Hook: `woocommerce_order_status_failed` | — | Not Started |

## Currency (added — client-requested TZS/USD switching, 2026-07-07)

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| TZS/USD currency switcher control in header | Provided by WPML's WooCommerce Multilingual & Multicurrency add-on (see `PLUGIN_DECISIONS.md`) + theme integration to match header styling | — | Not Started |
| Price display conversion (USD ↔ TZS) throughout shop/PDP/cart | WPML multicurrency filter on `woocommerce_price_html` / core price functions | — | Not Started |
| Checkout confirmation of actual TZS charge amount when browsing in USD | Hook into Checkout block rendering, or the WPML multicurrency add-on's own checkout integration if it exposes one | Plugin-dependent; may require block/template customization if the add-on doesn't expose this by default — evaluate once verified in Phase 5 | Not Started |
| Selcom gateway currency handling (always charge in the settlement currency, per `ARCHITECTURE.md` §11) | Custom logic in the Selcom gateway class, reading the order's base-currency total rather than trusting display currency | Payment-critical logic; must not rely solely on the currency plugin's assumptions without explicit verification | Not Started |

## Multilingual (added — client-requested EN/Swahili, 2026-07-08)

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| WPML install + language configuration (English default, Kiswahili added) | Plugin setup, no custom code | — | Not Started |
| Language switcher control in header (text-label `EN`/`SW`, no flag icons) | WPML's language switcher widget/shortcode + theme integration to match header styling | — | Not Started |
| Product/category/attribute translation | WooCommerce Multilingual & Multicurrency add-on's translation editor | — | Not Started |
| Static page + Journal post translation | WPML's Advanced Translation Editor, per page/post | — | Not Started |
| Transactional email translation (order confirmation, etc.) | WPML String Translation on the email templates from `ARCHITECTURE.md` §8 | — | Not Started |
| WhatsApp/email automation copy in Swahili | Nia Core settings page stores per-language template strings, keyed by WPML's active language | Automation templates are custom (not core WooCommerce), so this needs explicit language-aware handling rather than relying on WPML's automatic string translation | Not Started |
| `hreflang` tags / multilingual sitemap | WPML + Rank Math official integration | — | Not Started |

## Elementor (added — client-requested scoped inclusion, 2026-07-08)

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Elementor Global Colors/Fonts configured to mirror `DESIGN_SYSTEM.md` tokens | Elementor Site Settings (`_elementor_page_settings` on the active kit) — 4 system colors + 8 curated custom colors, 4 system typography styles + 3 curated custom styles | — | Completed (2026-07-07) |
| Elementor Saved Template library (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter, promo tile) mirroring the custom Gutenberg block set | Elementor `elementor_library` posts with hand-authored `_elementor_data`, provisioned via a one-time `wp eval-file` script | — | Completed (2026-07-07) |
| Compiled Tailwind utility classes made usable inside Elementor | The compiled stylesheet (`ARCHITECTURE.md` §3) is already enqueued site-wide; classes are applied via each widget's Advanced → CSS Classes field. **Discovered while building this:** the control key differs by element type — Widgets use `_css_classes` (leading underscore, from Elementor's `common-base.php`); Sections and Columns use `css_classes` (no underscore, their own native "Advanced" tab control). Using the wrong key silently does nothing (no error) — see `ARCHITECTURE.md` §2a. | — | Completed (2026-07-07) |
| **Governance rule (not a build task, but must be respected by every future edit):** Elementor may only edit content on static/marketing pages. **Never** on Shop, Product, Cart, Checkout, My Account, or header/footer — those remain fully custom-coded/hook-driven. | Enforced structurally: free Elementor has no Theme Builder, so it cannot reach header/footer/WooCommerce templates regardless of policy | — | N/A (standing rule) |

## Inventory

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Low-stock owner alert (WhatsApp + email) | Hook: `woocommerce_low_stock` / `woocommerce_no_stock` | — | Not Started |

## Subscriptions (custom engine — see `ARCHITECTURE.md` §5)

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| `nia_subscription` custom post type registration | `register_post_type` in Nia Core | — | Not Started |
| Subscription creation at checkout | Hook: `woocommerce_checkout_order_processed` | — | Not Started |
| Daily renewal cron | `wp_schedule_event` + custom cron callback | — | Not Started |
| Renewal order creation | `wc_create_order()` programmatically from cron callback | — | Not Started |
| Grace-window pause + owner alert | Cron callback checking days-overdue against setting | — | Not Started |

---

## Rule for adding new rows

Any WooCommerce customization not listed above must be added here **before** it's built, with its mechanism decided (hook vs. override) and justified if it's an override. If you find yourself about to copy a WooCommerce template file into the theme, stop and check this file first — if it's not listed, add it and justify the override before proceeding.
