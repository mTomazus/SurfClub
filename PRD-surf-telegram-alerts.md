# PRD: Melnragė Surf Forecast Telegram Alerts

**Status:** Draft
**Author:** Tomas Ūksas
**Date:** 2026-06-07
**Version:** 1.0

---

## 1. Product Definition

A "notify me" feature on the public surf forecast page (`test/port`) that lets any visitor subscribe — via a **Telegram bot** — to a personalised heads-up the **night before tomorrow's conditions match their skill level**. It solves the problem that casual and regular surfers don't know when Melnragė is worth the trip, so they either miss good days or check the page obsessively. Targeted at the surfclub.lt audience (Lithuanian, iPhone-heavy recreational and intermediate surfers).

| Field | Detail |
|---|---|
| Product name | Melnragė Surf Forecast Telegram Alerts |
| Stakeholders / team | surfclub.lt owner (Tomas), single-dev build |
| Target release | v1, no fixed deadline (small scope, ~1 focused build) |
| Primary user persona | Lithuanian recreational/intermediate surfer who checks surfclub.lt forecast and uses Telegram |

---

## 2. Goals & KPIs

| Goal | KPI / Success Metric | Target | Timeframe |
|---|---|---|---|
| Get visitors to opt in | Active (linked) Telegram subscribers | ≥ 30 | 60 days post-launch |
| Complete the subscribe flow reliably | % of modal "Connect Telegram" taps that become an active subscription | ≥ 60% | Ongoing |
| Send only relevant, accurate alerts | Alerts where tomorrow genuinely matched the profile (no false sends) | 100% (zero false alerts) | Every run |
| Avoid annoyance / churn | `/stop` unsubscribes ÷ total subscribers | < 15% | 60 days post-launch |
| Drive surf trips on good days | Self-reported "alert brought me to the beach" (informal, ask in club) | Qualitative positive | 60 days |

---

## 3. Assumptions & Constraints

**Assumptions**
- Target users have Telegram installed and are willing to start a bot (chosen over web push specifically for iPhone reach).
- A night-before (~18:00) alert gives enough lead time to plan a morning/next-day session.
- The hardcoded beginner/advanced thresholds are "good enough" to be useful without per-user tuning at launch.
- Open-Meteo marine + weather forecast data is reliable enough that a pure-numeric match produces trustworthy alerts.
- "Tomorrow only" is the right window; users don't need multi-day lead time in v1.

**Constraints**
- **Hosting:** Hostinger shared (`surfclub.lt`). Cron available via hPanel; no long-running processes. Sender must be a short cron-triggered script.
- **Channel mechanics:** Telegram bot only — requires a BotFather **bot token** (user-provided) and a one-time `setWebhook` registration.
- **Stack:** Trongate PHP MVC, plain CSS/vanilla JS, Trongate MX for the modal. No new heavy dependencies — all Telegram calls via cURL (existing codebase idiom).
- **Language:** All user-facing copy and bot messages in Lithuanian (matches the site + existing AI prompts).
- **Data source:** Reuse the existing `surf_forecast` numeric fields; do not add new forecast providers.
- **Match logic:** Pure numeric (AND across set stats). The existing AI rating word is decoration only and must not gate alerts.

---

## 4. Scope

### In scope
- Bell/CTA button on `test/port` opening a Trongate MX modal.
- Three profiles: **beginner** / **advanced** (hardcoded presets) / **custom** (4 user inputs).
- Anonymous subscription storage (new table) with a one-time link token.
- Telegram deep-link onboarding (`t.me/<bot>?start=<token>`) + webhook that links chat → profile and confirms.
- Bot `/stop` command → deactivate.
- Evening (~18:00 Europe/Vilnius) Hostinger cron that evaluates **tomorrow** against each active subscriber's profile and sends a Telegram alert on match.
- Per-subscriber, per-date dedup (no double-send).
- Lithuanian alert message: tomorrow's wave range + wind speed/direction + `/stop` hint.

### Out of scope (v1)
- Web push, email, SMS channels.
- Current/live-conditions alerts (forecast-only).
- Multi-day window (anything beyond *tomorrow*).
- Wind-direction quality gradient ("closer to S = great") — documented as **v2**.
- Admin UI: editing presets, subscriber list, or test-send button (presets hardcoded).
- In-bot profile editing / in-bot custom number entry.
- Linking subscriptions to `members` accounts.
- "good vs great" message labelling.

---

## 5. Features

| Feature | Priority | Description | User interaction |
|---|---|---|---|
| Notify bell + modal | P0 | CTA on `test/port` opens a modal to choose a profile | Tap bell → modal opens |
| Profile selection | P0 | Beginner / Advanced presets + Custom with 4 inputs | Pick one; Custom reveals wave min/max, wind max, period min, dirs |
| Telegram handoff | P0 | Store pending sub + one-time token, deep-link to bot | Tap "Connect Telegram" → opens `t.me/<bot>?start=<token>` |
| Webhook link & confirm | P0 | Bot receives `/start <token>`, links chat_id → profile, confirms | User taps **Start** in Telegram; bot replies confirmation (LT) |
| `/stop` unsubscribe | P0 | Bot deactivates the subscriber | User sends `/stop`; bot confirms opt-out |
| Evening evaluation cron | P0 | Match tomorrow vs each active profile, send alerts, dedup | None (automated ~18:00) |
| Alert message | P0 | LT message: tomorrow wave range + wind + `/stop` hint | User receives Telegram message |
| Change profile / re-subscribe | P1 | Reopen modal & reconnect; dedup by chat_id updates the row | Reopen modal, pick new profile, reconnect |

### Notify bell + modal
- **What it does:** Adds a subscribe entry point to the existing forecast page without cluttering the glass-card layout.
- **User flow:** Tap bell → modal → pick profile → (Custom: fill 4 fields) → "Connect Telegram".
- **Edge cases:** Custom inputs need sane min/max/step + validation (reject empty/illogical ranges, e.g. wave_min > wave_max). Modal must work on mobile width (page is mobile-first).

### Profile selection (presets)
- **Beginner:** wave 0.4–1.0 m · wind ≤ 6 m/s · period any · dir SW/S/W/NW
- **Advanced:** wave ≥ 1.2 m · wind ≤ 12 m/s · period ≥ 4 s · dir SW/S/W/NW
- **Custom:** user sets wave min/max, wind max, period min; dirs default to the favourable set.
- **Edge cases:** Presets hardcoded in the controller — change requires code edit + deploy.

### Telegram handoff + webhook
- **What it does:** Bridges the anonymous web session to a Telegram chat_id.
- **User flow:** Server creates `pending` row + single-use `link_token` (TTL ~30 min) → deep link → user taps Start → webhook matches token → attaches chat_id, status `active`, dedups by chat_id (update existing row if the same chat re-subscribes) → bot confirms in Lithuanian.
- **Edge cases:** Expired/unknown/used token → bot replies "link expired, please subscribe again". Same chat re-subscribing → update profile, don't duplicate. Webhook must verify requests are from Telegram (secret token in URL/header).

### Evening evaluation cron
- **What it does:** The only sending path.
- **User flow:** Refresh `surf_forecast` → locate tomorrow (date = today+1) → for each `active` sub, test wave/wind/dir/period (AND) → if match AND `last_notified_date` ≠ tomorrow → `sendMessage` → set `last_notified_date = tomorrow`.
- **Edge cases:** Forecast API failure or missing tomorrow → skip run entirely (never send a false/empty alert). Telegram send failure (e.g. user blocked bot) → mark inactive / log, don't crash the loop.

### Alert message
- **What it does:** Tells the subscriber tomorrow matched, with the numbers that mattered.
- **Copy (example):** `🏄 Rytoj Melnragėje geros sąlygos! Bangos 1.0–1.4 m, vėjas 8 m/s iš PV. Parašyk /stop, kad atsisakytum.`
- **Edge cases:** Use tomorrow's `wave_min`–`wave_max`, `wind_avg` (or `wind_max`), `wind_dir`. No link, no period/temp/AI advice.

---

## 6. Release Criteria

**Functionality**
- [ ] All P0 features implemented and tested
- [ ] No P0 or P1 bugs open
- [ ] Pure-numeric match verified against known forecast data (beginner & advanced + one custom)
- [ ] Dedup confirmed (running the cron twice in one evening sends at most one message per sub)
- [ ] Forecast-failure path sends nothing

**Usability**
- [ ] A new user completes subscribe (modal → Start → confirmation) on a phone without help
- [ ] `/stop` reliably stops further messages

**Reliability**
- [ ] Cron tolerates API/Telegram errors without aborting the whole run
- [ ] Webhook rejects requests without the secret token

**Performance**
- [ ] Modal opens instantly; no push-permission or heavy assets on page load
- [ ] Cron run for the expected subscriber volume completes well within Hostinger limits

**Acceptance test cases**

```json
{
  "category": "functional",
  "description": "Beginner profile subscribe + link via Telegram",
  "steps": [
    "Open test/port, tap the notify bell",
    "Select 'Beginner', tap 'Connect Telegram'",
    "Open the t.me deep link and press Start in Telegram",
    "Verify bot replies with a Lithuanian confirmation",
    "Verify DB row status = active with chat_id set and beginner thresholds"
  ],
  "passes": false
}
```

```json
{
  "category": "functional",
  "description": "Custom profile validation",
  "steps": [
    "Open modal, select 'Custom'",
    "Enter wave_min greater than wave_max",
    "Attempt to connect",
    "Verify validation blocks submission with a clear message"
  ],
  "passes": false
}
```

```json
{
  "category": "functional",
  "description": "Evening cron sends alert only on match",
  "steps": [
    "Seed an active subscriber whose profile matches tomorrow's forecast",
    "Seed a second subscriber whose profile does NOT match",
    "Run the cron evaluation endpoint",
    "Verify only the matching subscriber receives a Telegram message",
    "Verify last_notified_date is set to tomorrow for the matched sub"
  ],
  "passes": false
}
```

```json
{
  "category": "reliability",
  "description": "Dedup prevents double-send",
  "steps": [
    "Run the evening cron once (subscriber matches, gets a message)",
    "Run the same cron again the same evening",
    "Verify no second message is sent to that subscriber"
  ],
  "passes": false
}
```

```json
{
  "category": "reliability",
  "description": "Forecast outage sends nothing",
  "steps": [
    "Simulate surf_forecast returning empty/error",
    "Run the cron",
    "Verify zero messages sent and the run exits cleanly"
  ],
  "passes": false
}
```

```json
{
  "category": "functional",
  "description": "/stop unsubscribes",
  "steps": [
    "As an active subscriber, send /stop to the bot",
    "Verify bot confirms opt-out",
    "Verify status = stopped and the next cron skips this subscriber"
  ],
  "passes": false
}
```

```json
{
  "category": "usability",
  "description": "Mobile subscribe flow unaided",
  "steps": [
    "On a phone, open test/port",
    "Complete bell → profile → Connect Telegram → Start without guidance",
    "Confirm the whole flow is understandable and the modal fits the screen"
  ],
  "passes": false
}
```

---

## 7. Success Metrics & Tracking

### During development
- Single checklist of P0 features (this PRD's §5/§6) ticked off as built.
- Manual run-throughs of each acceptance test case before deploy.

### Post-launch

| Metric | How measured | Owner | Review cadence |
|---|---|---|---|
| Active subscribers | `SELECT COUNT(*) WHERE status='active'` | Tomas | Weekly |
| Subscribe completion rate | pending rows created vs activated | Tomas | Bi-weekly |
| Alerts sent | count of sends per evening run (log) | Tomas | Per run / weekly |
| Unsubscribe rate | `/stop` count ÷ total | Tomas | Monthly |
| False/empty alerts | manual spot-check of sent vs actual conditions | Tomas | First 2 weeks |

---

## Open questions

| Question | Owner | Due |
|---|---|---|
| BotFather bot token + chosen bot username (needed before any send works) | Tomas | Before build of webhook/cron |
| Webhook secret/verification approach on Hostinger (URL secret param vs header) | Dev | During build |
| Confirm Hostinger cron minimum interval + exact 18:00 Europe/Vilnius scheduling | Tomas/Dev | Before cron setup |
| Custom-input ranges/steps (e.g. wave 0–4 m, wind 0–25 m/s, period 0–12 s) | Tomas | During modal build |
| Use `wind_avg` or `wind_max` for the wind-≤-max test (avg recommended) | Dev | During cron build |

---

Let me know if you want to adjust any section, add more features, or tighten the scope.
