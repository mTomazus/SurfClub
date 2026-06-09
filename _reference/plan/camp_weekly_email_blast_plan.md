# Plan: Camp — "Email Shift's Registrants" Button

**Date:** 2026-06-09
**Spec:** `_reference/specs/camp_weekly_email_blast.md`
**Status:** In Progress

## Summary
Add a button to the camp registrations admin screen that sends Brevo transactional
template `2` to every registrant of a manually selected shift (`pamaina`). The admin
picks the shift from the dropdown already present in `reservations.php`; the button then
shows the recipient count, asks for confirmation, sends one email per registrant via the
existing Brevo API pattern, records `reminder_sent_at` per row so a shift can't be
accidentally double-blasted, and swaps in a sent/failed summary. No content/template
authoring (that lives in Brevo), no scheduling, no payment-status filtering — all
registrants of the chosen shift receive the email.

## Affected Files
- `camps` table (DB) — add `reminder_sent_at DATETIME NULL DEFAULT NULL`.
- `modules/camps/controllers/Camps.php` — extend `index()` to pass per-shift email data;
  add `send_shift_email()` action and two private helpers (`get_shift_recipients()`,
  `send_brevo_template()`).
- `modules/camps/views/reservations.php` — render the email button + result container
  inside the `.table-responsive` block (so it swaps with the shift selection).
- `modules/camps/views/_send_result.php` — **new** partial: sent/failed summary returned
  to the `mx-target`.

## Tasks

### Phase 1: Database
- [ ] Run, on local and production, via phpMyAdmin (no migration system in this project):
      `ALTER TABLE camps ADD reminder_sent_at DATETIME NULL DEFAULT NULL;`
- [ ] Verify the column exists and is NULL for all existing rows → `DESCRIBE camps;`

### Phase 2: Controller (`Camps.php`)
- [ ] Add private `get_shift_recipients(int $num, bool $unsent_only = true): array`
      that returns `camps` rows for the shift, matched by
      `WHERE pamaina LIKE :prefix` with `:prefix = $num . '. %'` (the `". "` makes
      `2. %` match only shift 2, not `12. %`/`20…`). When `$unsent_only`, also
      `AND reminder_sent_at IS NULL`. Order by `id`.
- [ ] Add private `send_brevo_template(string $email, array $params, int $template_id): bool`
      — a thin clone of the cURL block in `curl_mail()` (lines ~196–211): POST to
      `https://api.brevo.com/v3/smtp/email`, header `api-key: ` . `constant('BREVO_API')`,
      sender `VšĮ Banglentė / sales@banglente.com`, `to => [["email" => $email]]`,
      `templateId`, `params`. Return `true` only on HTTP 2xx. Do **not** modify the
      existing `curl_mail()` (it stays bound to registration template 32).
- [ ] In `index()`: after computing `$show_only`, derive the selected shift number from
      `segment(3)`. When a single shift is selected, build
      `$data['email_shift'] = ['num' => N, 'label' => <pamaina string of first matched
      row, contains dates>, 'recipient_count' => <count of matched rows>, 'sent_count' =>
      <rows with reminder_sent_at NOT NULL>, 'last_sent' => <MAX(reminder_sent_at) or
      null>];` Otherwise `$data['email_shift'] = null`. Capture the token returned by
      `_make_sure_allowed()` into `$data['token']` (consistent with `show()`).
- [ ] Add public `send_shift_email(): void`:
      - `$this->module('trongate_security'); $this->trongate_security->_make_sure_allowed();`
      - `$num = (int) segment(3);` reject if `$num < 1 || $num > 12` (http 400 + message).
      - `$resend = (post('resend') === '1');` recipients =
        `get_shift_recipients($num, !$resend)` (normal = unsent only; resend = all matched).
      - If empty → return `_send_result` partial with a "no recipients" / "already all
        sent" message.
      - Loop recipients: `$ok = send_brevo_template($r->email, ['name'=>$r->name,
        'pamaina'=>$r->pamaina], 2);` on success
        `$this->model->update($r->id, ['reminder_sent_at' => date('Y-m-d H:i:s')], 'camps');`
        and `$sent++`; else `$failed[] = $r->email;`
      - Render `views/_send_result.php` with `$sent`, `$failed`, `$num` (echo it, not
        `template()` — this is an MX fragment response).

### Phase 3: Views
- [ ] `reservations.php`: inside the `.table-responsive` div, **above** the `<table>`,
      render an email control region (so it is included in the `mx-select=".table-responsive"`
      swap when the shift changes):
      - If `$email_shift === null` → small hint: "Pasirink pamainą laiškams siųsti."
      - Else a button labelled e.g.
        `✉ Siųsti laišką · Pamaina <num> · <recipient_count> gavėjų`, with
        `mx-post="camps/send_shift_email/<num>"`, `mx-target="#send-result"`,
        `mx-indicator="#send-spinner"`. Use `mx-build-modal` for a confirmation step
        (per the `trongate-mx` skill) showing the shift label + count before the POST
        fires. Follow the existing `competitions/confirm_participant` button pattern for
        token handling (Trongate MX attaches the admin token automatically).
      - If `last_sent` is set, show "Jau išsiųsta: <date> (<sent_count>/<recipient_count>)"
        and make the default button send only the not-yet-sent rows; add a separate
        "Siųsti visiems iš naujo" action that posts `resend=1` via `mx-vals`.
      - Add `<div id="send-result"></div>` and a hidden `#send-spinner` indicator inside
        the same `.table-responsive` block.
- [ ] Create `modules/camps/views/_send_result.php`: render
      "✅ Išsiųsta: <sent>" and, if `$failed`, "⚠️ Nepavyko: <count> (<emails>)".
      Plain markup matching the view's existing style; no `<style>` bloat.

### Phase 4: Testing
- [ ] Manually test happy path: select a shift with ≥2 registrants, confirm, verify each
      gets the Brevo template-2 email and each row's `reminder_sent_at` is set; summary
      shows correct sent count.
- [ ] Re-trigger the same shift → default send finds 0 unsent recipients and reports
      "already sent"; "Siųsti visiems iš naujo" (resend=1) re-sends to all matched rows.
- [ ] Edge cases from spec: no recipients for shift; one invalid email → counted as
      failed, others still sent and marked; "Visos Pamainos" selected → no button shown;
      malformed `pamaina` does not crash the `LIKE` match.
- [ ] Confirm no regressions: registration flow (`submit2` → `curl_mail`, template 32)
      still works; the shift dropdown still filters the table; `manage`/`show`/`create`
      admin screens unaffected.

## Notes
- **Reuse, don't refactor:** `send_brevo_template()` duplicates the small cURL block from
  `curl_mail()` rather than rewriting `curl_mail()`, per the project's surgical-changes
  rule. If later desired, `curl_mail()` could delegate to it — out of scope here.
- **Shift matching** relies on every `pamaina` string starting with `"<num>. "`
  (e.g. `"2. 2026-06-15 - 2026-06-19"`), which is how all current data and
  `get_show_only()` are structured. The `". "` in the LIKE prefix prevents `2` matching
  `12`/`20…`.
- **Volume is tiny** (`max` 12 per shift), so a synchronous per-recipient loop is fine;
  no queue/batch needed. Brevo non-2xx responses are treated as failures and surfaced.
- **Brevo API key** stays server-side via `constant('BREVO_API')`; only the recipient
  count and result summary reach the browser.
- **Open questions still carried from the spec** (don't block build, defaults applied):
  exact merge fields template 2 expects (sending `name` + `pamaina`); resend targets
  not-yet-sent rows by default; per-recipient API calls (not Brevo batch).
- DB change is manual SQL — remember to also apply it on the production DB before this
  ships (deploy is rsync-based per `deploy.sh`; it does not run migrations).
