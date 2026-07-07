# CUSTOMIZATIONS.md — Nia Nutrition

Every planned WooCommerce customization, logged here **before or as** it's built. Per `ARCHITECTURE.md` §4: default to a hook (action/filter) in the Nia Core plugin; use a template override only when no hook achieves the required markup change, and state why in the "Why not a hook" column when that happens.

Status values: **Not Started** · **In Progress** · **Review** · **Completed**

---

## Product & Catalog

**Corrected 2026-07-08:** the catalog is 4 product **categories** (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules), each holding a variable, admin-managed number of products — not 4 fixed products. See `ARCHITECTURE.md` §1/§9/§9a. Every row below must work for any number of categories/products, not just the initial set.

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Category archive (Shop/Collection page) — paginated, count-agnostic | WooCommerce native `taxonomy-product_cat.php` / Products block, styled to match `collection.html` | — | Not Started |
| Category filter/tab row on Shop archive | Custom markup querying `get_terms('product_cat')`, styled per the existing journal/FAQ tab pattern (`DESIGN_SYSTEM.md` §4 item H) | — | Not Started |
| Homepage "Featured Products" section sourced from native Featured-product flag | `wc_get_featured_product_ids()` / `meta_query` on `_featured`, capped at a display limit | — | Not Started |
| Category display ordering (admin-controlled) | Category-ordering plugin (see `PLUGIN_DECISIONS.md`) — no native WooCommerce equivalent for reordering `product_cat` terms | — | Not Started |
| Product card badge ("Subscribe & Save", "Best Seller") | Hook: `woocommerce_before_shop_loop_item_title` | — | Not Started |
| Quick-add hover overlay on Shop archive | Hook + custom markup via `woocommerce_after_shop_loop_item` | — | Not Started |
| Currency display format standardized to `"45,000 TZS"` | Filter: `woocommerce_price_format` / `wc_price` args | — | Not Started |
| One-time vs. subscription purchase-type radio on PDP | Hook: `woocommerce_before_add_to_cart_button` + custom field/price logic | — | Not Started |
| PDP gallery + sticky purchase panel layout | Template override: `single-product.php`, `content-single-product.php` | Layout structure (sticky column, gallery position) is a page-level arrangement, not something the standard hooks expose cleanly | Not Started |
| WhatsApp inquiry button on PDP | Hook: `woocommerce_after_add_to_cart_button` | — | Not Started |
| "Added to Ritual" add-to-cart button micro-interaction | JS enhancement only, no PHP hook needed | — | Not Started |

## Cart

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Cart line-item layout (thumbnail + stepper + price row) | Template override: `cart/cart.php` or Cart block styling via CSS | Visual layout restructuring beyond what cart hooks expose; block-based cart may avoid needing a full override — attempt CSS-only styling of the Cart block first, fall back to override only if the block markup can't achieve it | Not Started |
| "Complete Your Ritual" upsell section | Hook: `woocommerce_cart_collaterals` (or Cart block cross-sell configuration) | — | Not Started |
| Promo code field parity between Cart and Checkout | Native WooCommerce coupon field, ensure visible on both (mockup only shows it on Checkout) | — | Not Started |
| Trust signal row (SSL, delivery, payment icons) | Hook: `woocommerce_after_cart_totals` | — | Not Started |

## Checkout

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Re-theme Checkout from stale olive-gold palette to canonical orange-gold | CSS only (compiled Tailwind using canonical tokens) | — | Not Started |
| "Step X of 3" section labels | Checkout block custom CSS/markup insertion via block editor | — | Not Started |
| Mobile Money payment method tiles (M-Pesa/Tigo Pesa/Airtel Money) | Selcom gateway's `payment_fields()` method | — | Not Started |
| Order summary sticky sidebar + trust badges | Checkout block layout + CSS (`position: sticky`) | — | Not Started |
| Minimal checkout header/footer (no nav, no icons) | Conditional template part: check `is_checkout()` in header/footer template part | — | Not Started |
| VAT (18%) line item | WooCommerce tax settings (Tanzania VAT rate configured as a standard tax class) | — | Not Started |

## My Account / Dashboard

| Customization | Mechanism | Why not a hook (if override) | Status |
|---|---|---|---|
| Sidebar navigation (Overview, My Rituals, Order History, Wellness Profile, Settings, Logout) | Hook: `woocommerce_account_menu_items` (reorder/add custom endpoints) | — | Not Started |
| Mobile sidebar equivalent (not in any mockup) | New: to be designed in Phase 3, implemented as a hook-driven template part | — | Not Started |
| "My Rituals" (subscriptions) endpoint | Custom endpoint via `woocommerce_account_menu_items` + `add_rewrite_endpoint`, rendering `nia_subscription` CPT data | Custom data source (not core WooCommerce), but registration itself is the standard hook-based endpoint pattern | Not Started |
| Order History styling (rounded "app card" treatment) | CSS only, template override only if markup structure blocks it | Attempt hook/CSS first | Not Started |
| Active subscription status badge (uses `primary` token, not a dedicated success color) | Hook: custom rendering in the My Rituals endpoint template | — | Not Started |

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
| Elementor Saved Template library (Hero, Benefit Grid, Testimonial, CTA Banner, Newsletter, promo tile) mirroring the custom Gutenberg block set | Elementor "Save as Template" (global), built in Phase 3 alongside the block library | — | Not Started |
| Compiled Tailwind utility classes made usable inside Elementor | No new mechanism — the compiled stylesheet (`ARCHITECTURE.md` §3) is already enqueued site-wide; classes are applied via each widget's Advanced → CSS Classes field | — | Not Started |
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
