# Spec: Camp — "Email Next Week's Registrants" Button

**Date:** 2026-06-09
**Status:** Ready

## Problem
Each summer-camp shift (`pamaina`) runs Monday–Friday. Before a shift starts, staff
want to send every registered participant the same pre-arrival email (what to bring,
arrival time, location, etc.). Today there is no way to do this from the admin panel —
the only automated email is the registration/payment confirmation sent once per signup
in `Camps::curl_mail()` (Brevo `templateId` 32). Reminding the upcoming week's group
means exporting contacts and emailing them by hand, which is slow and error-prone.

Who is affected: camp administrators (the people viewing `camps/index` /
`camps/manage`) and the registered participants who should receive timely information.

## Goal
An admin can open the camp registrations screen and, with one button + confirmation,
send a pre-selected Brevo email template to **all registrants of the upcoming shift**.
After sending, the admin sees a clear result: how many emails were sent and how many
failed. Success = the right recipients receive the email, the admin gets accurate
feedback, and the same group cannot be accidentally emailed twice in one click.

## Scope

### In Scope
- A "Send next week's email" button on the camp registrations admin screen
  (`reservations.php`, rendered by `Camps::index()`), gated by `trongate_security`.
- A confirmation step before sending (recipient count shown, e.g. "Send to 9 people?").
- A backend controller method that:
  - determines which shift(s) count as "next week",
  - collects matching registrants from the `camps` table,
  - sends a chosen Brevo **transactional template** to each recipient via the existing
    Brevo API pattern (`https://api.brevo.com/v3/smtp/email`, `constant('BREVO_API')`),
  - returns a per-send summary (sent / failed counts).
- Admin-visible result feedback (sent count, failed count, optional list of failures).
- A guard against duplicate sends within the same action (and ideally across reloads —
  see Data Requirements).

### Out of Scope
- Designing or editing the email **content/template** itself — that lives in Brevo and
  is referenced only by `templateId`.
- Scheduling / cron / automatic sending. This is a manual, button-triggered action only.
- SMS, push, or any non-email channel.
- Manually hand-picking individual recipients or editing the recipient list in the UI.
- Bulk-marketing / Brevo "campaign" API. We reuse the transactional email endpoint
  already in use for registration mail.
- Changes to the public registration form or payment flow.

## Functional Requirements
1. The registrations admin screen MUST show a clearly labelled button to email the
   upcoming shift's registrants. The button MUST display, or reveal on click, the
   target shift's dates and the recipient count (e.g. "Email Pamaina 2 · 2026-06-15 –
   06-19 · 9 recipients").
2. The action MUST be restricted to authenticated admins via
   `trongate_security->_make_sure_allowed()`, consistent with other admin methods in
   `Camps.php`.
3. Clicking the button MUST require an explicit confirmation before any email is sent
   (no send on a single accidental click).
4. The admin MUST choose the target shift manually via a shift (`pamaina`) selector —
   reusing the dropdown already present in `reservations.php` (the values are the full
   `pamaina` strings, e.g. `"2. 2026-06-15 - 2026-06-19"`). The send action targets the
   currently selected shift. No automatic "next week" date detection is performed.
5. The system MUST select **all** `camps` rows whose `pamaina` matches the selected
   shift and send to every one of them, regardless of payment `status` (`completed` or
   `pending`).
6. For each recipient the system MUST send one Brevo transactional email using
   `templateId` **2**, passing at least `name` and `pamaina` as template `params`
   (matching the existing `curl_mail()` convention), to the recipient's `email`.
7. The system MUST handle each send independently: one failed/rejected address MUST NOT
   abort the remaining sends.
8. After processing, the system MUST report a summary to the admin: number sent and
   number failed (and, where practical, which addresses failed).
9. If there are no registrants for the upcoming shift, the system MUST tell the admin
   "no recipients" and send nothing.
10. The action MUST be idempotent against accidental repeats: re-triggering for an
    already-emailed shift MUST either be blocked or require a deliberate "send again"
    confirmation (see Data Requirements for the tracking mechanism).

## Non-Functional Requirements
- **Security:** admin-only (`trongate_security`); CSRF/token handling consistent with
  existing admin POST actions; the Brevo API key MUST stay server-side via
  `constant('BREVO_API')` and never reach the browser.
- **Reliability under Brevo limits:** sends are per-recipient API calls. The
  implementation SHOULD tolerate Brevo rate limiting / transient errors (e.g. capture
  non-2xx HTTP codes per call) rather than assuming success. For realistic shift sizes
  (≈12 participants/shift per the stats panel `max = 12`) a simple sequential loop is
  acceptable; no async/queue is required at this volume.
- **No data leakage:** recipient email addresses MUST NOT be exposed to other recipients
  (one email per recipient — no shared `to`/CC list).
- **Feedback latency:** because sends are synchronous, the request may take a few
  seconds; the UI SHOULD show an in-progress indicator (the project already uses
  Trongate MX `mx-indicator` patterns) and avoid a double submission.
- **Maintainability:** reuse the existing Brevo call shape from `Camps::curl_mail()`
  rather than introducing a new HTTP/mail dependency (no Composer installs).

## Data Requirements
- **Source table:** existing `camps` table. Relevant columns: `name`, `email`,
  `pamaina`. No filtering by `status` (all registrants receive the email). No new source
  data needed to identify recipients.
- **Send tracking (required):** add a nullable timestamp column to `camps`:
  `reminder_sent_at DATETIME NULL`. On a successful send the row's `reminder_sent_at` is
  set. This satisfies FR-10 (the UI warns before re-sending an already-emailed shift)
  and survives partial failures, so a retry can target only rows that were not yet sent.
- **Brevo config:** reuse `constant('BREVO_API')` with `templateId` **2** (distinct from
  the registration confirmation template `32`).

## UI / UX Notes
- Place the button in the registrations admin view (`reservations.php`), next to the
  existing shift (`pamaina`) selector so it clearly acts on the chosen shift.
- Recommended flow:
  1. Admin selects a shift from the existing `pamaina` dropdown (the page already filters
     the table by this selection).
  2. The button shows the selected shift + recipient count (e.g. "Email Pamaina 2 ·
     9 recipients"). The button is disabled when no shift is selected ("Visos Pamainos").
  3. Click → confirmation ("Send the email to 9 registrants of Pamaina 2
     (2026-06-15 – 06-19)?"). Trongate MX `mx-build-modal` / confirm pattern fits.
  4. On confirm → POST to the send action with the admin token and the selected
     `pamaina`.
  5. Result swaps in below the button: "✅ Sent 8 · ⚠️ Failed 1 (bad@email)" using an
     `mx-target` content swap (consistent with the existing `mx-get`/`mx-select` usage
     in this view).
- If the selected shift was already emailed (`reminder_sent_at` present on any row in
  that shift), show the date it was last sent and require an explicit "Send again"
  confirmation.
- Copy is Lithuanian-facing in this view (e.g. "Siųsti laiškus", "Pamaina"); match the
  existing language/tone of `reservations.php`.

## Edge Cases
- **No registrants for the selected shift** → show "no recipients", send nothing, no error.
- **No shift selected** ("Visos Pamainos") → button is disabled; the action does not run
  against all shifts at once.
- **Duplicate email addresses** across two registrations in the same shift (e.g. a
  parent registering two children) → decide whether to dedupe by email or send one per
  registration. Default: send one per **registration row** (each child's `params` differ),
  accept that a shared parent inbox may receive multiple emails.
- **Invalid / bouncing email** → Brevo returns non-2xx; count as failed, continue.
- **Brevo rate limit / network error mid-batch** → already-sent recipients stay sent;
  surface the failure count so the admin can retry only failures (tracking column helps
  avoid re-sending the successful ones).
- **Double click / page refresh after send** → the duplicate-send guard (FR-10 /
  tracking column) must prevent a second blast.
- **Malformed `pamaina` string** (manual admin edit) that doesn't parse to a date → that
  row must not crash the matcher; skip and ideally log/flag it.

## Resolved Decisions
- **Brevo template ID:** `2`.
- **Recipient filter:** send to **all** registrants of the selected shift (no `status`
  filter).
- **Shift selection:** manual — admin picks the shift via the existing `pamaina`
  dropdown; no automatic "next week" detection.
- **Send tracking:** add `reminder_sent_at DATETIME NULL` to the `camps` table.

## Open Questions
- [ ] **Template params:** which merge fields does Brevo template `2` expect (just
      `name` / `pamaina`, or also dates, location, arrival time)?
- [ ] **Retry behaviour:** after a partial failure, should "send again" target only the
      not-yet-sent rows (those with `reminder_sent_at` still NULL), or everyone in the
      shift? Default: only not-yet-sent rows.
- [ ] **Send method:** one transactional API call per recipient (matches existing code)
      vs. a single Brevo batch/`messageVersions` call. Default: per-recipient loop.

## Related Specs or Plans
- Existing implementation reference: `modules/camps/controllers/Camps.php`
  — `curl_mail()` (Brevo transactional send), `get_show_only()` / `get_camp_stats()`
  (shift/`pamaina` date mapping), `index()` + `views/reservations.php` (admin list).
- Generate a plan from this spec with `/new-plan camp_weekly_email_blast.md` once the
  Open Questions are resolved.
