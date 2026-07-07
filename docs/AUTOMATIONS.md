# AUTOMATIONS.md — Nia Nutrition

Every email, WhatsApp, payment, and subscription automation. This is the operational heart of the project — the brief's actual objective is minimizing the owner's manual work, and this file is where that objective becomes concrete, testable triggers.

Status values: **Not Started** · **In Progress** · **Testing** · **Live**

Channel key: **Email** (WooCommerce email system) · **WhatsApp** (Notify Africa API via Nia Core) · **Admin UI** (dashboard/wp-admin notice only, no external message)

---

## Customer-facing automations

| # | Trigger | Channel(s) | Recipient | Content summary | Implementation | Status |
|---|---|---|---|---|---|---|
| C1 | Order placed | Email + WhatsApp | Customer | Order confirmation: items, total, delivery estimate | WC core `woocommerce_order_status_pending`/`processing` email, override template; WhatsApp via `class-nia-whatsapp.php` on same hook | Not Started |
| C2 | Payment confirmed (Selcom webhook success) | Email + WhatsApp | Customer | Payment received, order now processing | Selcom webhook handler → `woocommerce_order_status_changed` to `processing` | Not Started |
| C3 | Order shipped | Email + WhatsApp | Customer | Shipping update, tracking info if available | Hook: `woocommerce_order_status_completed` or a custom "shipped" status if fulfillment has a distinct shipped-vs-completed step (confirm with client's fulfillment process) | Not Started |
| C4 | Subscription renewal due (T-3 days configurable) | WhatsApp + Email | Customer | "Your ritual is ready to renew" + Selcom payment link for the generated renewal order | Daily cron in `subscription-renewals.php` (see `ARCHITECTURE.md` §5) | Not Started |
| C5 | Subscription renewed successfully | Email + WhatsApp | Customer | Confirmation, next renewal date | Selcom webhook success on a renewal order | Not Started |
| C6 | Review request | Email (+ WhatsApp optional) | Customer | Sent N days after delivery, links to product review form | Cron sweep checking `date_completed` + configurable delay | Not Started |
| C7 | Abandoned cart | Email (+ WhatsApp optional, only if opted in) | Customer | Reminder of items left in cart, optional incentive | Cron sweep: `class-nia-abandoned-cart.php` detects carts with no completed order after a configurable window (e.g. 1 hour), matched to a known customer (logged-in or email captured at checkout start) | Not Started |
| C8 | Newsletter (ongoing) | Email | Subscribers | Editorial content, promotions | Newsletter signup block (Phase 3) feeds an email list; sending mechanism TBD — confirm with client whether this needs a dedicated list/campaign tool or can run through the transactional SMTP relay for low volume (see `RISKS.md`) | Not Started |
| C9 | Promotional campaigns (ad hoc) | Email + WhatsApp | Segmented customers | Sale announcements, seasonal campaigns | Manual trigger by owner via Nia Core admin UI (pick segment + template), not a standing cron | Not Started |
| C10 | Failed payment (customer-facing) | Email + WhatsApp | Customer | Payment did not go through, retry link | Selcom webhook failure / `woocommerce_order_status_failed` | Not Started |

## Owner-facing automations

| # | Trigger | Channel(s) | Recipient | Content summary | Implementation | Status |
|---|---|---|---|---|---|---|
| O1 | New order placed | Email + WhatsApp | Owner | Order details, customer info | Hook: `woocommerce_new_order` | Not Started |
| O2 | Payment confirmed | Email + WhatsApp | Owner | Payment received for order #X | Selcom webhook success | Not Started |
| O3 | Low inventory alert | Email + WhatsApp | Owner | Product X below threshold (configurable per product) | Hook: `woocommerce_low_stock` | Not Started |
| O4 | Subscription created / renewed / paused | Admin UI (+ WhatsApp for paused, since it needs action) | Owner | Subscription lifecycle events, especially paused-for-non-payment (needs owner follow-up) | Hooked into subscription engine (`ARCHITECTURE.md` §5) | Not Started |
| O5 | Failed payment | Email + WhatsApp | Owner | Order #X payment failed | `woocommerce_order_status_failed` | Not Started |
| O6 | Daily/weekly sales summary | Email | Owner | Orders count, revenue, low-stock flags, new subscriptions | Cron: `sales-summary.php`, configurable cadence | Not Started |

---

## Implementation notes

- **All WhatsApp sends route through `class-nia-whatsapp.php`** (single client class) — never call the Notify Africa API directly from a hook callback. See `ARCHITECTURE.md` §7.
- **All message templates are editable** via the Nia Core settings page (owner or future dev can change copy without a code deploy).
- **All sends are logged** (recipient, template, timestamp, success/failure) for troubleshooting delivery issues — a WhatsApp message that silently fails must be visible somewhere in wp-admin, not invisible.
- **Test every trigger with real staging numbers/inboxes** before enabling in production (Phase 8 exit criteria, re-verified in Phase 11).
- **Owner notification channel preference:** confirm with the client whether the owner wants every event via WhatsApp (can become noisy) or a curated subset — likely: WhatsApp for anything time-sensitive/needing action (new order, failed payment, low stock, paused subscription) and email for anything digest-style (sales summary). Reflected in the table above; adjust after client input.
- **Currency in notifications (client confirmed TZS + USD, 2026-07-07):** order/payment messages must always state the amount actually charged — per `ARCHITECTURE.md` §11's default settlement model, that's TZS, even if the customer was browsing in USD. Never surface a USD figure in a payment-confirmation message if TZS is what was actually charged.

## Open questions (resolve before Phase 8)

- [ ] Does "shipped" need to be a distinct WooCommerce order status from "completed," or does the client fulfill same-day such that processing→completed is sufficient? (Affects C3.)
- [ ] What review-request delay (days post-delivery) does the client want? (C6)
- [ ] Abandoned-cart window and whether WhatsApp is appropriate for it (more intrusive than email — confirm client's comfort level). (C7)
- [ ] Newsletter (C8): confirm sending mechanism/volume expectations — a transactional SMTP relay is not a bulk marketing sender; if list size grows, a proper ESP (Mailchimp/Brevo/etc.) may be needed. Flagged in `RISKS.md`.
- [ ] Sales summary cadence (daily vs. weekly) and preferred send time. (O6)
