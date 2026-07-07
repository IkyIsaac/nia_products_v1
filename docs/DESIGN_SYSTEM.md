# DESIGN_SYSTEM.md — Nia Nutrition

**Source of truth:** `/nia-products/*.html` (13 static HTML mockups, git repo `IkyIsaac/nia_products`, last commit `2026-06-12`, "Modify Color to golden orange").

This document is **extracted**, not designed. Every token below was read directly out of the mockups' inline Tailwind config blocks and `<style>` tags. Where the mockups disagree with themselves, that is called out explicitly as a **Drift** so engineering doesn't silently pick one at random — these are decided once, here, and then treated as final.

Do not hand-roll new colors, spacing, or type sizes during build. If a value isn't in this document, it doesn't belong on the site — go back to the mockup, find it, and add it here first.

---

## 0. Critical finding: three token sets exist in the source files

The 13 mockups are **not** internally consistent. There are three distinct design languages present:

| Set | Primary color | Files | Verdict |
|---|---|---|---|
| **A — Orange-Gold (canonical)** | `#E07A10` | index, collection, product, about-nia, journal, ritual, subscription, my-cart, contact, dashboard (10/13) | **Use this. This is the brand.** |
| **B — Olive-Gold (stale/legacy)** | `#735c00` | checkout, faqs (2/13) | Never rebranded after the orange-gold pivot. **Must be re-themed to Set A before build** — do not copy their color values. |
| **C — "Aureate Wellness" (abandoned)** | `#735c00`, different fonts (EB Garamond + Hanken Grotesk) | about.html only | **Discarded concept.** Not linked from any page's nav. `about-nia.html` is the real, in-use About page. **Exclude about.html from the build entirely.** |

Two more structural findings that change how Phase 4–6 are scoped:

- **`ritual.html` and `subscription.html` are near-duplicate pages** (same hero, tiers, testimonials, footer). Build **one** WordPress template for "The Ritual / Subscription" — do not build two.
- **Mobile navigation is not solved in any mockup.** Center nav links are simply `hidden md:flex` with no hamburger/drawer anywhere (one exception, `about.html`, has a dead menu icon with no behavior — and that file is excluded per above). **Resolved:** client-approved standard hamburger + full-screen overlay drawer pattern, matching the existing visual language (glassmorphic header, Material Symbols icons, Playfair/Montserrat type) — see §7 for the full spec. The same mechanism is used for the dashboard sidebar on mobile.

---

## 1. Color Palette

All colors are implemented as Material Design 3–style token names inside each page's Tailwind config. Use these **exact token names** in `theme.json` / the compiled Tailwind config so class names stay identical to the mockups (`bg-primary`, `text-on-surface-variant`, etc.) — this lets us port markup near-verbatim from HTML to PHP templates.

### Primary — Orange-Gold (the brand color)
| Token | Hex | Usage |
|---|---|---|
| `primary` | `#E07A10` | Hover state on primary buttons, active nav links, price highlights, "Recommended" tags |
| `on-primary` | `#2E1A00` | Text on primary surfaces |
| `primary-container` | `#FFD8A8` | Badge backgrounds, CTA panel backgrounds |
| `on-primary-container` | `#241400` | Text on primary-container |
| `primary-fixed` | `#FFE8CC` | Pale accents, decorative blobs, bullet dots |
| `on-primary-fixed` | `#241400` | — |
| `primary-fixed-dim` | `#CC6A08` | Deeper accent, blurred decorative circles |
| `inverse-primary` | `#F5A03A` | Dark-mode primary |
| `surface-tint` | `#E07A10` | Tint overlay reference |

### Secondary — Classic Gold
| Token | Hex |
|---|---|
| `secondary` | `#D4AF37` |
| `on-secondary` | `#241A00` |
| `secondary-container` | `#FCE28A` |
| `on-secondary-container` | `#241A00` |
| `secondary-fixed` | `#FFF0B5` |
| `on-secondary-fixed` | `#1F1500` |
| `secondary-fixed-dim` | `#E0BA40` |
| `on-secondary-fixed-variant` | `#4A3A00` |

### Tertiary — Royal Plum (decorative accent, used sparingly)
| Token | Hex |
|---|---|
| `tertiary` | `#6B3A6B` |
| `on-tertiary` | `#FFFFFF` |
| `tertiary-container` | `#F0D0F0` |
| `on-tertiary-container` | `#2A1A2A` |
| `tertiary-fixed` | `#F8E0F8` **(decided value — see Drift #1)** |
| `on-tertiary-fixed` | `#1C0E1C` |
| `tertiary-fixed-dim` | `#D0B0D0` |
| `on-tertiary-fixed-variant` | `#4A284A` |

> **Drift #1 — resolved (client-confirmed 2026-07-07):** `tertiary-fixed` is `#FFD8A8` in index.html but `#F8E0F8` in product.html/about-nia.html/journal.html — i.e. 3 of the 4 files that define it use `#F8E0F8`. Per client direction to follow whichever value is used most: **decision is `#F8E0F8`.** This also matches semantically better — `#FFD8A8` is identical to `primary-container`, so index.html's use of it here was almost certainly a copy-paste error rather than an intentional tertiary-family color; `#F8E0F8` is a pale tint consistent with `tertiary-container` (`#F0D0F0`).

### Surfaces — Warm Golden Ivory
| Token | Hex | Usage |
|---|---|---|
| `surface` / `surface-bright` | `#FFFCF5` | Section backgrounds, page background |
| `surface-dim` | `#E8DEC8` | — |
| `surface-container-lowest` | `#FFFFFF` | Card backgrounds |
| `surface-container-low` | `#FEF8EC` | Footer, newsletter panel |
| `surface-container` | `#F8F0E0` | Selected radio-card background |
| `surface-container-high` | `#F0E8D4` | — |
| `surface-container-highest` | `#E8DEC8` | Bento-grid tiles |
| `surface-variant` | `#F2ECE0` | — |
| `background` | `#FFFCF5` | `<body>` background |
| `on-background` | `#1E1A10` | Primary body text; **also the default button fill color** |
| `on-surface` | `#1E1A10` | Body text |
| `on-surface-variant` | `#4D4534` | Muted/secondary body text (most common text color after primary) |
| `inverse-surface` | `#2E2A1E` | Dark section backgrounds (e.g. product.html "Purely Sourced") |
| `inverse-on-surface` | `#F5EFE2` | Text on inverse-surface |

### Outline
| Token | Hex |
|---|---|
| `outline` | `#B89840` |
| `outline-variant` | `#D8C88A` |

### Error (stable across all files — no drift)
| Token | Hex | Usage |
|---|---|---|
| `error` | `#BA1A1A` | Cart remove icon hover, dashboard logout link |
| `on-error` | `#FFFFFF` | — |
| `error-container` | `#FFDAD6` | — |
| `on-error-container` | `#93000A` | — |

### Custom Royal Neutrals
| Token | Hex | Usage |
|---|---|---|
| `off-white` | `#FFFFFF` | Header bg (w/ opacity), card bg, button text |
| `warm-grey` | `#EAE0CC` | Placeholder image bg, dividers, borders |
| `warm-ivory` | `#FDF8EC` | Body background |

### Non-tokenized literals found in source (normalize these during build)
| Value | Where | Decision |
|---|---|---|
| `rgba(115,92,0,0.08)` @ `0 20px 40px -10px` | `.sunlight-shadow` box-shadow, used everywhere | **Canonical value — codify as a single CSS custom property `--shadow-sunlight`.** Some files use `-15px`/`0.05`/`0.06` — those are typos, not intentional variants. |
| `#1c1b1b` | Hardcoded button backgrounds (my-cart checkout CTA, product.html radio-checked state) | Replace with `on-background` token everywhere. |
| `#faf9f6`, `#d0c5af` | Scrollbar track/thumb (index, contact) | Fine to keep as a global scrollbar style, not a design token. |
| `#735c00` | contact.html scrollbar thumb | Leftover from Set B — replace with `on-background`/`outline` equivalents. |

### Semantic usage rules (apply consistently everywhere, including new pages not in the mockups)
- **Primary CTA button:** `on-background` fill by default → `primary` fill on hover. **Not** primary-colored by default.
- **Sale / "Subscribe & Save" badges:** `tertiary-container` bg + `on-tertiary-container` text (plum, not red/orange).
- **"Recommended" / "Most Popular" tags:** solid `primary` bg + `off-white` text.
- **Destructive actions:** `error` token.
- **No dedicated "success" color exists in source.** For order-status/active-subscription states, the mockups reuse `primary` (e.g. `bg-primary/10 text-primary` "Delivered" badge). Keep using `primary` for positive/active status — do not invent a green.

---

## 2. Typography

**Fonts (Google Fonts):**
```html
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,500&display=swap" rel="stylesheet" />
```
- **Display/headline:** Playfair Display (serif) — all headings, hero titles, italic pull-quotes/testimonials.
- **Body/label/nav/buttons:** Montserrat (sans).
- **Performance note:** production must **self-host both fonts** (subset to used weights/styles) instead of loading from Google's CDN — removes a render-blocking third-party request and a GDPR data-transfer concern for EU visitors. See ARCHITECTURE.md.

**Type scale** (from each page's `tailwind.config.theme.extend.fontSize`):

| Token | Size | Line-height | Tracking | Weight | Font |
|---|---|---|---|---|---|
| `display-lg` | 64px | 1.1 | -0.02em | 700 | Playfair Display |
| `display-lg-mobile` | 40px | 1.2 | normal | 700 | Playfair Display |
| `headline-lg` | 48px | 1.2 | normal | 600 | Playfair Display |
| `headline-lg-mobile` | 32px | 1.2 | normal | 600 | Playfair Display |
| `headline-md` | 32px | 1.3 | normal | 500 | Playfair Display |
| `headline-sm` | 24px | 1.3 | normal | 500 | Playfair Display |
| `body-lg` | 18px | 1.6 | normal | 400 | Montserrat |
| `body-md` | 16px | 1.6 | normal | 400 | Montserrat |
| `label-lg` | 14px | 1.2 | 0.1em | 600 | Montserrat |
| `label-md` | 12px | 1.2 | 0.05em | 500 | Montserrat |

> **Drift #2 — resolved (client-confirmed 2026-07-07):** classes like `font-headline-sm` appear in footers/cart markup but were never defined in *any* mockup's config — this isn't a case of conflicting values to pick between, it's an undefined token in the source (no file has a value at all). Per client direction: retaining the proposed value — `headline-sm` = 24px / 1.3 / 500 weight, sitting between `headline-md` (32px) and `body-lg` (18px), consistent with the ~1.33× step used elsewhere in the scale.

**Responsive type strategy (apply everywhere):** default to the `*-mobile` token, override at `md:` with the full desktop token, e.g. `class="font-display-lg text-display-lg-mobile md:text-display-lg"`. The `font-*` family class never changes across breakpoints — only size does.

**Conventions:**
- Uppercase + wide tracking (`tracking-widest` / `tracking-[0.2em]`) for all eyebrow labels and all button text.
- Italic Playfair Display for pull-quotes/testimonials.
- `on-surface-variant` (`#4D4534`) is the default color for secondary/muted body copy.

---

## 3. Spacing & Layout

From `tailwind.config.theme.extend.spacing` (identical across all canonical + legacy files):

| Token | Value | Usage |
|---|---|---|
| `container-max` | 1440px | Max-width wrapper, nearly every section |
| `section-gap` | 120px | Vertical padding between major sections (`py-section-gap`) |
| `margin-mobile` | 20px | Horizontal page padding, mobile |
| `margin-desktop` | 80px | Horizontal page padding, desktop (`md:`) |
| `gutter` | 24px | Grid/flex gap between cards |
| `base` | 8px | Small padding unit (e.g. header vertical padding) |

`margin-tablet: 40px` appears only in the abandoned `about.html` — **not used**, do not port it.

**Universal wrapper pattern:** `max-w-container-max mx-auto px-margin-mobile md:px-margin-desktop` — apply to every section on every new page.

**Grid systems:**
- 12-column `.editorial-grid` (`grid-template-columns: repeat(12,1fr); gap:24px`) drives asymmetric hero/story layouts via `col-span-*`/`col-start-*`. Used on index.html, product.html.
- Product/journal grids use plain Tailwind: `grid-cols-1` → `md:grid-cols-2` → `lg:grid-cols-4`.
- Checkout/cart split: `grid-cols-1 lg:grid-cols-12` (stacked mobile, side-by-side at `lg`).

**Border radius scale** (identical everywhere):
```
DEFAULT: 0.25rem   lg: 0.5rem   xl: 0.75rem   full: 9999px
```
**Rule:** marketing/shop surfaces (buttons, product cards, images) are **sharp — no radius** by design intent (editorial-luxury look). `rounded-lg`/`rounded-xl` is reserved for: pills/badges, avatars, circular icon-buttons, and **checkout/dashboard "app" cards**, which deliberately use a softer, rounded, app-like treatment distinct from the marketing pages. Preserve this distinction — it's intentional, not an inconsistency.

---

## 4. Buttons

No component classes exist in source — every button is hand-composed Tailwind utilities. Codify these as reusable classes/block styles during build (`.btn-primary`, `.btn-outline`, etc.) rather than repeating utility strings by hand in every template.

| Variant | Pattern | Notes |
|---|---|---|
| **Primary** | `px-10 py-5 bg-on-background text-off-white font-label-lg uppercase tracking-widest hover:bg-primary transition-all duration-300 active:scale-[0.98]` | Dominant CTA everywhere. No radius. |
| **Outline (light bg)** | `px-10 py-5 border border-on-background text-on-background hover:bg-on-background hover:text-off-white ...` | Inverts fill/text on hover. |
| **Outline (dark bg / on photo)** | `border border-off-white text-off-white hover:bg-off-white hover:text-on-background ...` | Used over hero photography. |
| **Inverse-surface** | `bg-inverse-surface text-off-white ... hover:bg-primary` | Ritual/subscription hero. |
| **Primary-filled** | `bg-primary text-on-primary-container hover:bg-primary-fixed-dim` | Subscription tier CTA. |
| **Text/underline link** | `border-b border-on-background pb-1 hover:text-primary hover:border-primary` | "View All Products," "Read More," newsletter submit. |
| **Icon-only circular** | `w-10 h-10 rounded-full border border-warm-grey hover:bg-primary hover:text-off-white` | Nav icons, footer socials. |
| **Payment-method radio card** | `p-4 border border-warm-grey rounded-lg hover:border-primary` | Checkout only — one of the few rounded button treatments. |

**Global rules:** rectangular/no-radius on marketing & shop; uppercase; wide tracking; `transition-all duration-300`; `active:scale-[0.98]` press feedback. Never use the hardcoded `bg-[#1c1b1b]` seen in `my-cart.html` — use the `on-background` token.

---

## 5. Cards

- **Product card:** `aspect-[4/5] bg-warm-grey` image (no radius), `group-hover:scale-105` zoom, optional pill badge top-left (`bg-tertiary-container/90`), eyebrow label (`text-primary`) + title (`headline-sm`/`headline-md`) + price (`label-lg` bold) below. No shadow — flat, editorial. `collection.html` adds a quick-add overlay (`opacity-0 translate-y-4` → hover reveal).
- **`.sunlight-shadow` card:** `box-shadow: 0 20px 40px -10px rgba(115,92,0,0.08)`, `bg-off-white`, generous padding (`p-8`–`p-12`), no border. The universal "elevated" card — benefit tiles, testimonials, subscription tiers, dashboard panels, cart/checkout summaries.
- **Journal/blog card:** `aspect-[4/5]` image + `sunlight-shadow`, category pill (`bg-off-white/90 backdrop-blur-md`), reading-time eyebrow, `headline-md` title, 3-line clamp excerpt. Featured article is a 2-col (7/5 split) large variant.
- **Testimonial card:** star row (filled Material Symbols in `text-primary`), large italic `display-lg`/`headline-md` pull-quote, avatar + name + role.
- **Subscription tier card:** `sunlight-shadow p-10`; "Most Popular" variant inverts to `bg-inverse-surface text-off-white` with a `bg-primary` ribbon badge.
- **Bento tile (About/Mission sections):** flat solid-color `md:col-span-*` grid tiles, no shadow, no radius.
- **Checkout/Dashboard "app" card:** `rounded-xl border border-warm-grey sunlight-shadow bg-white` — the one place rounding + border + shadow combine. Use this pattern for all account-area UI.

---

## 6. Icons & Imagery

- **Icon system:** Google **Material Symbols Outlined** exclusively — no SVG library, no Font Awesome, no Lucide/Feather. Loaded via:
```html
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
```
```css
.material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 300, "GRAD" 0, "opsz" 24; }
```
Filled variant (`'FILL' 1`) used inline for star ratings and emphasis icons. Icons referenced by name as text content, e.g. `search`, `shopping_bag`, `person`, `arrow_forward`, `check_circle`, `local_shipping`, `verified_user`, `lock`, `credit_card`, `smartphone`, `eco`, `biotech`, `spa`, `waves`, `star`, `close`, `expand_more`, `dashboard`, `history`, `settings`, `logout`.
- **Imagery:** 100% photographic (placeholder AI-generated lifestyle/product photography in the mockups) — warm ivory tones, soft golden light, minimalist studio settings. **All placeholder images must be replaced with real product/lifestyle photography before launch** (see RISKS.md). Recurring interaction: `grayscale` → `hover:grayscale-0` reveal on portrait/story imagery.
- **Decorative motifs:** subtle dot-grid texture on CTA panels (`radial-gradient(circle at 2px 2px,#000 1px,transparent 0); background-size:40px 40px; opacity:10%`); blurred color-blob accents (`rounded-full blur-3xl`); offset solid-color rectangles behind portrait photos (`-top-12 -left-12 bg-warm-grey -z-10`).

---

## 7. Header / Navigation

```html
<nav class="fixed top-0 left-0 w-full z-50 flex items-center justify-between
  px-margin-mobile md:px-margin-desktop py-base bg-off-white/80 backdrop-blur-xl">
```
- Fixed, glassmorphic (`bg-off-white/80 backdrop-blur-xl`).
- Logo: "NIA" wordmark, Playfair Display, `display-lg`, tight tracking.
- Center links (desktop only): Shop / About / Subscription / Wellness.
- Active link: `text-primary border-b-2 border-primary pb-1`.
- Right icons: search, **currency switcher (`TZS ▾`)**, **language switcher (`EN`/`SW`)**, account (`person`), cart (`shopping_bag`) with a small circular badge:
```html
<span class="absolute -top-1 -right-1 bg-primary text-off-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">0</span>
```
- **Checkout header variant:** stripped down — logo + tagline + "← Back to Shop" only, no nav/icons (intentional, reduces funnel exit points).
- **Dashboard adds a fixed left sidebar** (`w-72`, `hidden md:flex`, gradient `linear-gradient(180deg,#313030 0%,#1c1b1b 100%)`) with Overview / My Rituals / Order History / Wellness Profile / Settings / Logout.
- **Mobile nav — decided pattern (client-approved 2026-07-07):** a standard, widely-recognized pattern used across WordPress sites rather than a novel one. A `menu` (hamburger) Material Symbol icon replaces/joins the icon row on mobile; tapping it opens a full-screen overlay drawer (fade/slide transition) in `off-white`/`inverse-surface` matching the existing glassmorphic header treatment, with nav links stacked vertically in the same uppercase `label-lg` tracked-out style used on desktop, a `close` icon top-right to dismiss, and the account/cart icons remaining reachable in the collapsed header. This is final — build directly in Phase 3, no further sign-off needed.
- **Dashboard mobile sidebar — decided pattern (client-approved 2026-07-07):** use the **same hamburger/drawer mechanism** as the main site mobile nav, for one consistent interaction pattern site-wide rather than two different mobile behaviors. A `menu` icon in the dashboard's mobile header opens the same style of overlay drawer, listing the sidebar's own links (Overview / My Rituals / Order History / Wellness Profile / Settings / Logout) in place of the main nav links.

---

## 8. Footer

4-column grid: `grid-cols-1 md:grid-cols-4 gap-gutter bg-surface-container-low border-t border-warm-grey`.
1. Brand wordmark + one-line description.
2. "Discover" links (Shop All, Wholesale, Bundles, The Science).
3. "Support" links (FAQ, Contact, Shipping, Sustainability).
4. Newsletter signup (underline input + arrow/text submit) or, on index/collection, a Location block instead.

Verbatim copyright line on every page — reuse exactly:
```
© 2024 NIA . CRAFTED IN TANZANIA.
```
(Update the year at launch.) **Checkout uses a minimal single-row footer** (copyright + Privacy/Terms/Help only, `py-base`) — keep this distinction, it's intentional funnel design.

---

## 9. Forms & Inputs

**Underline input (dominant pattern, used everywhere — newsletter, contact form):**
```css
border: none; border-bottom: 1px solid var(--outline-or-warm-grey);
background: transparent; border-radius: 0; padding-left: 0;
transition: border-color .3s ease;
```
focus → `border-bottom-color: on-background`, no box-shadow/outline. Labels are separate small-caps elements above the field (`label-md`, `text-outline`), not floating placeholders.

- **Select:** native `<select appearance-none>` with a manually positioned `expand_more` icon on top.
- **Checkbox:** native, styled via Tailwind Forms plugin defaults + `text-primary focus:ring-primary`.
- **Radio (custom card selector — product purchase-type, checkout payment method):** **standardize on the CSS-only `sr-only peer` + `peer-checked:` pattern.** The source mockups use two different approaches (CSS `peer-checked` on product.html vs. a JS `addEventListener` toggle on checkout.html) — the JS approach is redundant and introduces FOUC risk. Build exclusively with `peer-checked:`.
- **Validation states:** not designed in any mockup. Use the existing `error` (`#BA1A1A`) / `error-container` (`#FFDAD6`) tokens for new invalid-state styling — border + helper text, consistent with the rest of the palette.

---

## 10. Product / Shop Patterns

- **Currency — multi-currency, client-confirmed (2026-07-07):** the site supports **both TZS and USD with a customer-facing currency switcher** — this is a new requirement layered on top of the mockups (no mockup shows a currency switcher at all). See `ARCHITECTURE.md` §11 for the settlement-currency implications and `RISKS.md` R7/R14. Display format for both currencies follows the mockups' dominant pattern — **amount, then code** (`"45,000 TZS"` / `"25 USD"`) — this format appears in 8 of the mockups' TZS occurrences vs. 4 for `"TZS 45,000"`.
- **Currency switcher UI (new — needs building, not in any mockup):** a small control in the header icon row, e.g. `TZS ▾` using the `expand_more` Material Symbol and `label-lg` Montserrat styling, sized consistently with the existing icon row. Keep it visually minimal — do not introduce a new visual language for it; it should look like it always belonged in the header.
- **Language switcher UI (new — client-confirmed 2026-07-08, needs building, not in any mockup):** English (default) + Kiswahili, via WPML (see `ARCHITECTURE.md` §11a). Placed in the header icon row alongside the currency switcher, same visual treatment: a text-label toggle (`EN` / `SW`) in `label-lg` Montserrat — **not flag icons** (Kiswahili is spoken across several countries, so a single national flag would misrepresent it). Default to English for all new visitors regardless of locale/geolocation unless the client later asks for geo-based defaulting.
- **Catalog is category-based, not a fixed 4 products (corrected 2026-07-08):** Seamoss Powder, Seamoss Gel, Raw Seamoss, and Seamoss Capsules are **categories**, each holding a variable, admin-managed number of products. `collection.html`'s flat product grid assumed a small, known, unchanging set — it must instead be built as a genuine, paginated category archive. See `ARCHITECTURE.md` §9a for the full technical implication.
- **Category filter/tab UI on the Shop archive (new — needed, but not actually new design work):** reuse the existing **category-filter pill/tab pattern already in the design system** (§4 item H — plain-text tabs, active = `text-primary`, inactive = `text-on-surface-variant hover:text-primary`, as seen on `journal.html`'s category filters and `faqs.html`'s category cards). Apply that same pattern to the Shop archive so customers can move between an arbitrary, growing number of categories without any new visual language being introduced.
- **Homepage "Featured Products" section is a live query, not 4 fixed cards:** sourced from WooCommerce's native Featured-product flag (admin-controlled per product), capped at a fixed display count (e.g. 4, matching the mockup's grid) — the *set* of what's shown is dynamic even though the *layout* stays a 4-card grid. See `ARCHITECTURE.md` §9a.
- **Subscribe & Save badge:** `bg-tertiary-container/90 text-on-tertiary-container` pill, e.g. "SUBSCRIBE & SAVE 10%".
- **Purchase-type selector (PDP):** two radio-cards, one-time vs. subscription; subscription option shows discounted price in `primary` + a small "Recommended" ribbon; JS swaps displayed unit price on selection.
- **Subscription tiers:**

| Tier | Cadence | Price | Discount | Note |
|---|---|---|---|---|
| The Enthusiast | Weekly | 45,000 TZS/delivery | 20% off | |
| The Balanced | Bi-weekly | 82,000 TZS/delivery | 15% off | "MOST POPULAR," inverted dark card |
| The Ritualist | Monthly | 155,000 TZS/delivery | 10% off | |

- **Quantity stepper:** bordered pill, plain-text `−`/`+`, min clamp at 1.
- **Add-to-cart:** full-width, `on-background` → `hover:primary`; on click, label swaps to "Added to Ritual" + `bg-primary` for 2s, then reverts.
- **WhatsApp inquiry button:** secondary outline button under Add-to-Bag on PDP — reflects the brand's WhatsApp-first support channel.
- **Gallery:** one hero image (`aspect-[4/5]`) + 3-up thumbnail strip (decorative only in source; must be wired to click-to-swap in build).
- **Sticky purchase panel:** `md:sticky md:top-32` on PDP's right column.

---

## 11. Checkout Patterns

- **Step framing:** no visual stepper bar — just text labels "Step 1 of 3" / "2 of 3" / "3 of 3" beside each section heading. Sections: **Contact Information → Shipping Details → Payment.**
- **Payment methods shown in mockup:** a "Mobile Money (TZ Only)" card cluster with M-Pesa / Tigo Pesa / Airtel Money radio tiles, plus a separate card-payment option with inline Card Number/MM-YY/CVC fields.
- **No Selcom branding appears anywhere in the mockups.** Selcom is the aggregator that will actually process these mobile-money + card payments in Phase 7 — the UI copy/logos need to be reconciled with what Selcom's checkout actually returns/supports (see RISKS.md and ARCHITECTURE.md §Payments).
- **Order summary (sticky, right column):** `sunlight-shadow rounded-xl border border-warm-grey p-8` — line items, Subtotal → Shipping → VAT (18%) → Promo code input → Total.
- **Trust badges:** "Tanzania Bureau of Standards" + "Ethically Sourced," tiny uppercase caption row.
- **CTA:** full-width "Complete Order," lock-icon reassurance microcopy below.
- **Uses the stale Set B (olive-gold) palette in source — must be re-themed to Set A before build.**

---

## 12. Cart Patterns

- 12-col layout: line items `lg:col-span-8`, summary `lg:col-span-4`.
- Line item: square thumbnail + content column (title/variant + remove icon top row; stepper + price bottom row), 1px `warm-grey` divider between items (no card borders).
- **"Complete Your Ritual" upsell:** 2-col grid of small horizontal suggestion cards (`sunlight-shadow`, hover `translate-y-[-4px]`), "Quick Add" underline button.
- **Summary sidebar:** sticky, Subtotal → Shipping estimate → Total → "Proceed to Checkout" CTA → same-day delivery microcopy.
- **Trust row:** SSL/lock icon, delivery icon, dimmed payment-method row ("M-PESA / TIGO PESA").
- **Gap:** no promo-code input on cart (only on checkout) — carry the same summary component to both pages for feature parity in the build, even though the mockup doesn't.

---

## 13. Responsive Rules

- Breakpoints: Tailwind defaults, `md:` is the dominant one; `lg:` mainly for 4-col grids and the checkout/cart 12-col split; `sm:` rare.
- Nav links: `hidden md:flex` on desktop; mobile replacement is the approved hamburger/drawer pattern (see §7).
- Product grids: `grid-cols-1` → `md:grid-cols-2` → `lg:grid-cols-4`.
- Hero/story sections: `grid-cols-1 md:grid-cols-12` with `order-1`/`order-2` swaps for image/text on mobile vs. desktop.
- Typography: mobile-size token by default, desktop token at `md:` (see §2).
- Padding: `px-margin-mobile md:px-margin-desktop` universally.
- Dashboard sidebar: `hidden md:flex` on desktop; mobile equivalent is the same hamburger/drawer pattern as the main nav (see §7).

---

## 14. Reusable Components → WordPress Template Parts / Blocks

Build these once, use everywhere — do not let any of these get re-implemented per page:

1. **Site header/nav** (`template-parts/header.php` or a block template part) — one component, active-state driven by current page, not hardcoded per file as in source.
2. **Site footer** — one component with a "minimal" variant prop/flag for checkout.
3. **Copyright line** — static string in the footer part.
4. **Newsletter signup block** — one custom Gutenberg block, reused in-footer and as standalone sections.
5. **`.sunlight-shadow` card** — a CSS utility class, not per-component styling.
6. **Testimonial block** — custom Gutenberg block (stars + quote + avatar).
7. **Benefit/icon-grid block** ("92 Minerals" pattern) — one flexible block reused with different copy on homepage, PDP, About.
8. **Product card** — WooCommerce template override, shared by homepage "Featured" and Shop archive (archive adds badge + quick-add overlay as a variant). Must render correctly for any product in any category — no assumptions about which 4 products exist (see `ARCHITECTURE.md` §9a).
9. **CTA banner block** — one block, background-color and copy as attributes.
10. **Ritual/Subscription page** — one WordPress page template, not two.
11. **Elementor Saved Template mirrors (added 2026-07-08):** for each of blocks 4, 6, 7, 9 above, build an equivalent Elementor Saved Template using Elementor's Global Colors/Fonts (synced to these same tokens) and the compiled Tailwind classes via CSS Classes — see `ARCHITECTURE.md` §2a. These are for the client's occasional Elementor-based content edits; the Gutenberg blocks remain the primary build system.
12. **Category filter/tab row (Shop archive, added 2026-07-08)** — reuses the existing journal/FAQ category-tab pattern (§4 item H) rather than new UI; scales to however many categories exist (see `ARCHITECTURE.md` §9a).

---

## Design Sign-off — Resolved (client-confirmed 2026-07-07)

All items below were open questions in the initial documentation pass and have since been resolved directly by the client. Recorded here for traceability — no further sign-off needed on any of these:

- [x] `headline-sm` = 24px/1.3/500 (Drift #2 — no conflicting source value existed; proposed value retained).
- [x] `tertiary-fixed` = `#F8E0F8` (Drift #1 — corrected to the value used in 3 of 4 defining files; supersedes the earlier `#FFD8A8` draft).
- [x] Mobile nav pattern approved — standard hamburger + full-screen overlay drawer, matching existing header visual language (see §7).
- [x] Dashboard mobile sidebar approved — same hamburger/drawer mechanism as the main nav, for one consistent pattern site-wide (see §7).
- [x] Currency: **multi-currency (TZS + USD) with a customer-facing switcher**, not single-currency TZS as originally assumed. Display format remains `"45,000 TZS"` / `"25 USD"` (amount, then code). See §10, `ARCHITECTURE.md` §11, and `RISKS.md` R7/R14 for the settlement-currency follow-up work this creates.
- [x] Checkout/FAQ pages confirmed to inherit Set A (canonical orange-gold) — the stale Set B (olive-gold) palette is never used in the build.



