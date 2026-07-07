# RISKS.md — Nia Nutrition

Risk register. Status values: **Open** · **Mitigated** · **Accepted** · **Closed**. Severity is impact if unmitigated, not likelihood.

---

## Technical risks

### R1 — Selcom mobile-money recurring billing may not support silent auto-charge
**Severity:** High. **Status:** Open.
The subscription model's cost/complexity hinges on whether Selcom can tokenize and silently re-charge mobile money accounts (M-Pesa/Tigo Pesa/Airtel Money) or whether every renewal genuinely requires active customer approval. This must be confirmed directly with Selcom's API documentation/support **before Phase 9**, ideally before Phase 7 starts, since it determines whether the custom "renewal request" engine (`ARCHITECTURE.md` §5) is the permanent design or a stopgap.
**Mitigation:** built the v1 subscription engine assuming manual approval is required (safe default); confirm with Selcom early; upgrade path exists without re-architecting the data model if auto-debit is available.

### R2 — Design mockups contain a stale color palette on 2 of 13 pages
**Severity:** Medium. **Status:** Closed (client-confirmed 2026-07-07).
`checkout.html` and `faqs.html` use an old olive-gold (`#735c00`) palette that predates the "golden orange" rebrand visible in the other 10 pages. If a developer copies these pages' colors literally, Checkout — the single most revenue-critical page — ships off-brand.
**Resolution:** client confirmed both pages must inherit the canonical orange-gold palette (Set A), not their current stale palette — documented in `DESIGN_SYSTEM.md` §0 and `PROJECT_PLAN.md` Phase 4/6 tasks and `CUSTOMIZATIONS.md`. Re-theme during transcription, not after.

### R3 — Mobile navigation pattern does not exist in any mockup
**Severity:** Medium. **Status:** Closed (client-confirmed 2026-07-07).
Every mockup hides the center nav links below the `md` breakpoint with no replacement (hamburger, drawer, or otherwise). The one file with a menu icon (`about.html`) is itself excluded from the build (abandoned concept) and its icon has no wired behavior anyway. Same gap exists for the dashboard's sidebar on mobile.
**Resolution:** client approved a standard hamburger + full-screen overlay drawer pattern, consistent with common WordPress site conventions and the site's existing visual language — full spec in `DESIGN_SYSTEM.md` §7. The dashboard's mobile sidebar uses the same mechanism. Build directly in Phase 3, no further sign-off needed.

### R4 — Two mockup pages (`ritual.html`, `subscription.html`) are near-duplicates
**Severity:** Low. **Status:** Mitigated.
Building both as separate WordPress templates would double-maintain identical content. Resolved: build one template; documented in `DESIGN_SYSTEM.md`, `PROJECT_PLAN.md`, `PAGE_STATUS.md`.

### R5 — Hosting/caching dependency not yet locked in
**Severity:** Medium. **Status:** Closed (client-confirmed 2026-07-08).
LiteSpeed Cache (from the original stack list) only functions on LiteSpeed-based hosting. If the eventual production host runs Nginx/Apache, installing it does nothing. This must be resolved in Phase 1, not discovered at deployment.
**Resolution:** production host confirmed as **Hostinger Business/Cloud (WooCommerce plan)** — runs LiteSpeed Enterprise natively with WooCommerce pre-configured, NVMe SSD, and a built-in CDN, so the originally-planned LiteSpeed Cache plugin is used as-is; the WP Rocket/WP Super Cache fallback is no longer needed. See `PLUGIN_DECISIONS.md`, `ARCHITECTURE.md` §10/§12.

### R6 — Placeholder photography must be fully replaced
**Severity:** Medium. **Status:** Open.
Every image across all 13 mockups is AI-generated placeholder photography (hosted on Google's `lh3.googleusercontent.com`), each with a *prompt description* rather than a real `alt` attribute. None of this is licensed or launch-ready. Real product photography, lifestyle photography, and the founder's portrait must be sourced/shot before Phase 4/6 pages can be marked Completed.
**Mitigation:** called out explicitly as an exit criterion in `PROJECT_PLAN.md` Phase 4 and Phase 6 — no page should move to Completed in `PAGE_STATUS.md` with placeholder imagery still in place. Client needs to commission/provide photography early, since this can become a schedule bottleneck if left until the end.

### R7 — Multi-currency settlement: Selcom may only settle in TZS regardless of display currency
**Severity:** Medium. **Status:** Open (scope escalated 2026-07-07 — client confirmed TZS + USD switching is a hard requirement, not an optional nice-to-have, which makes this risk more concrete than the original "may need a second payment path" framing).
The client has confirmed the site must support both TZS and USD with a customer-facing switcher. Selcom is a Tanzania-focused aggregator; its mobile-money rails almost certainly settle only in TZS regardless of what currency a customer was browsing in, and its card processing may or may not support true USD settlement. If this is assumed away, a customer could see a USD price and then be charged a mismatched or incorrect amount.
**Mitigation:** default to "display in USD, charge in TZS" — always show the converted TZS amount clearly before the customer confirms payment (`ARCHITECTURE.md` §11, model 1). Confirm Selcom's actual USD settlement capability directly during Phase 7; only move to true dual-currency settlement if confirmed available. A second, internationally-oriented gateway (Stripe/PayPal) remains a fallback if the client wants genuine USD settlement and Selcom can't provide it.

### R8 — Newsletter/bulk email is out of scope for a transactional SMTP relay
**Severity:** Low-Medium. **Status:** Open.
FluentSMTP (or any SMTP relay) is built for transactional volume/deliverability, not bulk marketing sends. If the newsletter list grows meaningfully, sending campaigns through the transactional relay risks deliverability problems (spam flagging) for *all* mail from that domain, including order confirmations.
**Mitigation:** flagged in `AUTOMATIONS.md` open questions; if list size warrants it, add a dedicated ESP (Mailchimp/Brevo/etc.) for campaign sends specifically, keeping transactional mail on the SMTP relay.

### R9 — Undefined design token found in source (`headline-sm`)
**Severity:** Low. **Status:** Closed (client-confirmed 2026-07-07).
Classes referencing a `headline-sm` type-scale token appear in the mockup markup but were never defined in any file's Tailwind config — an authoring bug in the source files themselves. **Resolution:** proposed value (24px/1.3/500) retained, see `DESIGN_SYSTEM.md` §2.

### R10 — Duplicate/legacy nav-target inconsistency
**Severity:** Low. **Status:** Open.
The "Wellness" nav link points to `journal.html` on some pages and `ritual.html`/`subscription.html` on others across the mockups — an authoring inconsistency, not an intentional IA decision.
**Mitigation:** decide final information architecture during Phase 3 header build (recommend: "Wellness" → Journal, with Subscription/Ritual reachable via its own nav item or a shop-page CTA, since conflating the two under one label is confusing) — get this into the nav structure once, not per-page.

### R14 — Exchange rate management for the TZS/USD switcher
**Severity:** Low-Medium. **Status:** Open.
A currency switcher (added 2026-07-07 per client request, now implemented via WPML's WooCommerce Multilingual & Multicurrency add-on — see `ARCHITECTURE.md` §11b, `PLUGIN_DECISIONS.md`) needs a defined, auditable exchange-rate source (automatic feed vs. owner-controlled manual rate), an update frequency, and a rounding rule. Left undefined, the store could display stale or inconsistent rates, eroding customer trust or creating small reconciliation discrepancies for the owner.
**Mitigation:** decide and document the rate source/update cadence/rounding rule during Phase 5; verify the WPML multicurrency module is compatible with the block-based Checkout and the custom Selcom gateway before committing to it (fall back to a standalone currency plugin alongside WPML if it isn't).

### R15 — Elementor and the Tailwind/theme.json token set are two systems that must stay in sync
**Severity:** Low-Medium. **Status:** Open.
Keeping Elementor free for occasional client-side content edits (client direction, 2026-07-08 — see `ARCHITECTURE.md` §2a) means design tokens now live in two places: the compiled `tailwind.config.js`/`theme.json` and Elementor's own Global Colors/Global Fonts settings. If the brand palette or type scale ever changes, updating only one of the two will cause Elementor-edited sections to silently drift from the rest of the site.
**Mitigation:** configure Elementor's Global Colors/Fonts directly from the finalized token set once, in Phase 2/3 (not before); document the sync obligation here and in `CUSTOMIZATIONS.md` so any future token change includes an explicit "update Elementor Global Colors/Fonts too" step; periodically spot-check an Elementor-edited page against `DESIGN_SYSTEM.md` during QA (Phase 11).

### R16 — Elementor scope creep beyond content-only editing
**Severity:** Low. **Status:** Open (technically constrained, but worth watching).
The intent is for Elementor to touch only static/marketing page content — never WooCommerce templates or header/footer. Free Elementor's lack of a Theme Builder makes the header/footer/WooCommerce boundary a hard technical wall, but nothing stops increasingly complex ad-hoc page structures accumulating in Elementor over time (e.g. a "quick landing page" growing into something that should have been a proper block/template), which would fragment where content actually lives.
**Mitigation:** the Elementor Saved Template library (mirroring the custom blocks, built in Phase 3) gives a bounded, on-brand starting point rather than a blank canvas, which reduces the temptation to freehand something novel. Revisit if usage patterns suggest the client wants more page-building freedom than "occasional adjustments."

### R17 — Kiswahili translation workflow and timeline undefined
**Severity:** Medium. **Status:** Open.
The client has confirmed English + Kiswahili support (2026-07-08), but who actually performs the Swahili translation (client-provided, professional translator, or WPML's machine-translation integration as a starting draft) and on what timeline relative to each page's build is not yet defined. Left unresolved, this risks either launching with incomplete Swahili content or MT-quality copy that doesn't match the premium brand voice.
**Mitigation:** confirm the translation workflow and responsible party with the client before Phase 4 content population begins (see `ARCHITECTURE.md` §11a); recommend professional/human translation for all customer-facing copy, with MT (if used at all) only as an internal first-draft aid.

### R18 — Mockups were designed against a fixed, flat 4-product catalog; the real catalog is category-based and will grow/shrink
**Severity:** Medium. **Status:** Mitigated (client-corrected 2026-07-08; documented, build not yet started).
`index.html`'s "Core Collection" and `collection.html`'s product grid were both designed assuming exactly 4 known, unchanging products. The actual model is 4 **categories** at launch, each holding a variable, admin-managed number of products, with both the category count and per-category product count expected to change over time. Building these pages as a literal transcription of the mockup (hardcoded product cards, no pagination, no category navigation) would work on day one and then require developer intervention every time the catalog changes — defeating the project's core automation/low-manual-work goal.
**Mitigation:** documented as a first-class architectural concern, not an afterthought — see `ARCHITECTURE.md` §9a for the three specific places this must be built dynamically (homepage Featured query, category-aware paginated Shop archive, category navigation), `DESIGN_SYSTEM.md` §10/§14 for the reused category-tab UI pattern, and `PROJECT_PLAN.md` Phase 5/6 for the concrete build tasks. A category-ordering plugin (`PLUGIN_DECISIONS.md`) was added specifically to give the admin control over category order, which WooCommerce doesn't provide natively.

---

## Business / process risks

### R11 — Owner's WhatsApp notification volume could become noise
**Severity:** Low-Medium. **Status:** Open.
If every automation in `AUTOMATIONS.md` sends the owner a WhatsApp message, the owner may start ignoring the channel entirely, defeating the automation's purpose.
**Mitigation:** curate which events go to WhatsApp (time-sensitive/actionable) vs. email (digest-style) — proposed split documented in `AUTOMATIONS.md`, needs client confirmation of their actual preference.

### R12 — Subscription "pause on non-payment" needs a clear owner workflow
**Severity:** Medium. **Status:** Open.
Automating the renewal *reminder* is straightforward; what happens when a customer simply doesn't pay is a business-process decision (how many reminders, how long a grace period, does the owner manually follow up, is the subscription auto-cancelled or just paused indefinitely) that needs the client's input, not an engineering default quietly chosen.
**Mitigation:** flagged as an open question in `AUTOMATIONS.md`/`ARCHITECTURE.md` §5; confirm exact grace-window and cancellation policy with the client before Phase 9 build.

### R13 — Single point of documentation/process knowledge
**Severity:** Low. **Status:** Accepted.
A small, single-developer-maintained custom build (custom gateway, custom subscription engine, custom WhatsApp integration) concentrates knowledge. This is why `ARCHITECTURE.md` and `CUSTOMIZATIONS.md` exist and must be kept current — the mitigation *is* the documentation discipline itself, not a separate action item.

### R19 — Hostinger account signup/payment cannot be done by the development side
**Severity:** Medium (schedule blocker). **Status:** Open (added 2026-07-07, Phase 1).
The host was confirmed (`PLUGIN_DECISIONS.md`), but actually creating the Hostinger Business/Cloud account is a real-world purchase requiring the client's payment method and business details — not something that can be provisioned from the development side. All other Phase 1 local-environment work (WordPress/WooCommerce install, HPOS, foundation plugins, build tooling, WPCS) has proceeded against the local dev environment in the meantime so nothing else is blocked.
**Mitigation:** client to sign up for the Hostinger Business/Cloud (WooCommerce plan) account and share access; staging can then be provisioned there per `PROJECT_PLAN.md` Phase 1.

### R20 — WPML requires a paid license, cannot be installed from the free plugin repository
**Severity:** Medium (schedule blocker). **Status:** Open (added 2026-07-07, Phase 1).
WPML (and its WooCommerce Multilingual & Multicurrency add-on) is a paid, licensed plugin distributed from wpml.org, not WordPress.org — it cannot be installed via the standard free-plugin workflow used for the rest of the Phase 1 plugin baseline. Multilingual/multicurrency work (header switchers' real logic, product/category translation, Phase 5 verification) is blocked until this is resolved.
**Mitigation:** client to purchase the WPML "Multilingual CMS" tier (includes the WooCommerce add-on) and provide the plugin zip + license key; install and configure once received.

### R21 — Business email + SMTP credentials not yet provided
**Severity:** Low-Medium (schedule blocker). **Status:** Open (added 2026-07-07, Phase 1).
FluentSMTP is installed, but cannot be configured or tested without a real business mailbox and its SMTP credentials (host, port, username, password/API key) from the client.
**Mitigation:** client to provide/create a business mailbox (Google Workspace, Hostinger mail, or other) and share SMTP credentials; configure and send a verified test email once received.

---

## Review cadence

This register should be reviewed at the end of every phase in `PROJECT_PLAN.md` — close out risks that are resolved, add any newly discovered ones. Do not let this file go stale; a risk register nobody reads is not a mitigation.
