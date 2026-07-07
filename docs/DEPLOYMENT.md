# DEPLOYMENT.md — Nia Nutrition

Launch checklist. This is the gate for Phase 12 in `PROJECT_PLAN.md`. Nothing here should be a surprise at launch time — every item traces back to a decision made in `ARCHITECTURE.md`, `RISKS.md`, or `PLUGIN_DECISIONS.md`.

---

## Pre-launch (staging, before DNS cutover)

### Content & design
- [ ] Every page in `PAGE_STATUS.md` is marked Completed, not just Review.
- [ ] All placeholder/AI-generated photography replaced with real, licensed photography (R6 in `RISKS.md`).
- [ ] All legal/policy pages (Privacy, Terms, Shipping, Refund) reviewed by the client (and legal counsel if the client wants that) — these are new content, not derived from mockups.
- [ ] Copyright line year updated (`© {current year} NIA . CRAFTED IN TANZANIA.`).
- [ ] Design sign-off items from `DESIGN_SYSTEM.md` ("Design Sign-off — Resolved") all correctly implemented as decided, not left as engineering assumptions.
- [ ] Every page's Kiswahili translation is complete, client-reviewed for brand voice/accuracy, and not just machine-translated placeholder text (R17 in `RISKS.md`).
- [ ] Language switcher (EN/SW) tested — defaults to English for new visitors, persists selection correctly across pages/cart/checkout.
- [ ] Elementor Global Colors/Fonts spot-checked against the current `DESIGN_SYSTEM.md` token set — confirm no drift has crept in since Phase 2/3 setup (R15 in `RISKS.md`).
- [ ] Confirm no WooCommerce template, header, or footer has been edited via Elementor (should be structurally impossible on the free tier, but verify).

### Commerce
- [ ] All 4 initial product categories live (Seamoss Powder, Seamoss Gel, Raw Seamoss, Seamoss Capsules) with correct thumbnails/descriptions, and every product within them live with correct pricing, descriptions, and images — the per-category count is whatever the client has ready, not assumed to be exactly one each.
- [ ] Category ordering plugin configured with the client's preferred category display order.
- [ ] Selcom gateway switched from sandbox to live credentials — confirmed with a real small-value transaction on every supported method (M-Pesa, Tigo Pesa, Airtel Money, card).
- [ ] Tax/VAT configuration confirmed correct (18% per the checkout mockup).
- [ ] Shipping rates/zones configured per client's actual delivery areas (Dar es Salaam same-day + wider Tanzania + international, per business requirements).
- [ ] Refund/failed-payment flow tested end-to-end, not just the happy path.
- [ ] Currency switcher (TZS/USD) tested in production — displayed conversion is accurate, and checkout clearly confirms the actual settlement-currency (TZS) amount before payment (`ARCHITECTURE.md` §11).
- [ ] Exchange rate source/update cadence live and functioning correctly in production (R14 in `RISKS.md`).
- [ ] R7 (Selcom settlement-currency behavior / international payment path) resolved or explicitly deferred with client agreement.

### Subscriptions
- [ ] Full subscription lifecycle tested in production-like conditions: subscribe → renewal reminder → pay → renewed; and subscribe → non-payment → paused → owner alerted.
- [ ] Grace-window and cancellation policy (R12) confirmed with client and configured, not left at a default guess.

### Automations
- [ ] Every trigger row in `AUTOMATIONS.md` tested and marked Live, not just Testing.
- [ ] WhatsApp sending verified against real production Notify Africa credentials (not a sandbox/test number).
- [ ] Business email verified sending/receiving correctly via production SMTP.
- [ ] Owner has been shown where WhatsApp/email automation failures would be logged/visible (so silent failures don't go unnoticed post-launch).

### SEO & analytics
- [ ] Rank Math fully configured: sitemap generated, schema verified (Organization + Product + Article), meta title/description on every page, in both languages.
- [ ] `hreflang` tags verified across the site (WPML + Rank Math integration) and multilingual sitemaps submitted.
- [ ] Google Search Console property verified, sitemap submitted.
- [ ] GA4 receiving data, verified with a real test session.
- [ ] Cookie consent banner live and functioning correctly for EU/international visitors.

### Performance & security
- [ ] LiteSpeed Cache active and tuned on production (Hostinger Business/Cloud — confirmed LiteSpeed-native, R5 in `RISKS.md` closed).
- [ ] Core Web Vitals checked against a real production-like build (not the raw mockups, not a cache-cold staging box).
- [ ] Images serving as WebP/AVIF where supported, properly sized (no oversized originals).
- [ ] Fonts self-hosted, no Google Fonts CDN or Tailwind CDN requests present (verify in network tab — see `ARCHITECTURE.md` §3).
- [ ] Wordfence scan clean, firewall active.
- [ ] File editing disabled in wp-admin (`DISALLOW_FILE_EDIT`), strong admin passwords/2FA enforced.
- [ ] Selcom webhook endpoint signature verification tested against a forged/invalid request to confirm it correctly rejects it.
- [ ] SSL certificate provisioned and auto-renewing for the production domain.

### Backups
- [ ] UpdraftPlus scheduled with an **off-site** destination (not same-server only).
- [ ] At least one full restore has actually been performed and verified successful (Phase 11 exit criterion) — a backup that's never been restored is unverified.

---

## Launch day

- [ ] Final content freeze communicated to client — no last-minute edits mid-cutover.
- [ ] DNS cutover to production host.
- [ ] SSL confirmed active on the live domain (not just staging).
- [ ] Production Selcom credentials confirmed live (double-check not still pointing at sandbox after cutover).
- [ ] Smoke test immediately after cutover: load homepage, browse shop, complete one real small-value purchase end-to-end, confirm order + payment + WhatsApp + email all fire correctly on the live domain.
- [ ] Search Console/GA4 property confirmed tracking the live domain (not the staging URL).
- [ ] Monitor error logs (PHP error log, Wordfence activity) for the first few hours post-cutover.

---

## Post-launch

- [ ] Client training session: walk the business owner through wp-admin, the Nia Core settings page (API keys, message templates, low-stock thresholds), My Account customer view, and how to read the automation logs.
- [ ] Confirm the client knows how to check: new orders, subscription status, low-stock alerts, and where to look if a customer reports a missing WhatsApp/email notification.
- [ ] Set a monitoring window (e.g. first 2 weeks) for closer-than-usual attention to error logs, failed payments, and automation delivery logs.
- [ ] Schedule the first sales-summary automation and confirm the owner actually receives it as expected.
- [ ] Archive/close out `RISKS.md` items resolved during launch; carry forward anything still open (e.g. R7 international payments if deferred, R8 newsletter scaling if list is still small).
- [ ] Confirm backup schedule is running in production (not just staging) and off-site destination is correctly configured for the live environment.
