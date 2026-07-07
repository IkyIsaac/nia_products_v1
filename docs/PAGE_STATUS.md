# PAGE_STATUS.md — Nia Nutrition

Status values: **Not Started** · **In Progress** · **Review** · **Completed**

Update this file the moment a page's status changes — do not batch updates at phase end. This is the single place anyone on the project can check "is X page done yet."

**Language rule (client-confirmed 2026-07-08):** every page below must have both an English (default) and a Kiswahili version completed via WPML before it is marked **Completed** — a page with only its English version done should stay at **In Progress**/**Review**, not Completed.

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

| Component | Status | Notes |
|---|---|---|
| Header / desktop nav | Not Started | |
| Mobile nav (hamburger/drawer) | Not Started | Client-approved pattern (2026-07-07) — see `DESIGN_SYSTEM.md` §7, build directly |
| Currency switcher (TZS/USD, header control) | Not Started | Client-requested (2026-07-07) — now provided via WPML's Multicurrency add-on, see `ARCHITECTURE.md` §11 |
| Language switcher (EN/SW, header control) | Not Started | Client-requested (2026-07-08) — text-label only, no flag icons, see `DESIGN_SYSTEM.md` §10 |
| Elementor Saved Template library (on-brand components) | Not Started | Client-requested (2026-07-08) — mirrors the Gutenberg block set, see `ARCHITECTURE.md` §2a |
| Footer (full) | Not Started | |
| Footer (minimal, checkout variant) | Not Started | |
| Button variants (8 total) | Not Started | See `DESIGN_SYSTEM.md` §4 |
| Product card | Not Started | Shared between Homepage featured + Shop archive; count-agnostic (any product, any category) |
| Category filter/tab row (Shop archive) | Not Started | Client-requested flexibility (2026-07-08) — reuses journal/FAQ tab pattern, see `DESIGN_SYSTEM.md` §14 item 12 |
| `.sunlight-shadow` card utility | Not Started | |
| Testimonial block | Not Started | |
| Benefit/Icon Grid block ("92 Minerals" pattern) | Not Started | |
| CTA Banner block | Not Started | |
| Newsletter Signup block | Not Started | |
| Dashboard sidebar (desktop) | Not Started | |
| Dashboard sidebar (mobile equivalent) | Not Started | Client-approved (2026-07-07) — same hamburger/drawer mechanism as main nav, see `DESIGN_SYSTEM.md` §7 |

## Excluded from build

| File | Reason |
|---|---|
| `about.html` | Abandoned "Aureate Wellness" rebrand concept — different brand name, fonts, colors. Not linked from any live page's nav. Superseded by `about-nia.html`. |
| `ritual.html` **or** `subscription.html` (whichever is not chosen) | Near-duplicate pages — only one should be built as a WordPress template. |

---

## Legend for future entries

When adding a new page not listed above, append it to the correct section with: source mockup (or "none — new content"), status, and any notes on design decisions/gaps that had to be made during build (cross-reference `DESIGN_SYSTEM.md`/`ARCHITECTURE.md` if a decision was logged there).
