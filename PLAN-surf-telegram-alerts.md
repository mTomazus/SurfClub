# Implementation Plan: Melnragė Surf Forecast Telegram Alerts

Companion to `PRD-surf-telegram-alerts.md` and `.grill-notify-decisions.md`.
Stack: Trongate PHP MVC · plain CSS/vanilla JS · Trongate MX · cURL · Hostinger shared + cron.

## 0. Architecture decision: extend the `test` module (don't make a new module)

All forecast logic already lives in `modules/test/controllers/Test.php` (`_build_forecast_days()`, `surf_forecast`) and the page is `test/port`. The evening cron must reuse `_build_forecast_days()` to know tomorrow's numbers. Putting subscribe/webhook/cron in `Test.php` avoids duplicating forecast logic or making HTTP calls to our own endpoint.
- **Trade-off:** `Test.php` is already large/grab-bag. Acceptable for v1.
- **Future refactor (not v1):** extract a `Surf_alerts` module + a shared forecast model.

URLs (test/* resolves directly; only `orai → test/port` is aliased in custom_routing):
- Subscribe (AJAX): `POST {BASE_URL}test/subscribe`
- Modal view: `GET {BASE_URL}test/notify_modal`
- Telegram webhook: `POST https://www.surfclub.lt/test/telegram_webhook`
- Cron endpoint: `GET https://www.surfclub.lt/test/run_alerts?key=<SURF_ALERT_CRON_KEY>`

---

## 1. Config — `config/config.php` (add near ANTHROPIC block)

```php
// ---------- TELEGRAM SURF ALERTS ----------
define('TELEGRAM_BOT_TOKEN',    '');           // from @BotFather  ← USER PROVIDES
define('TELEGRAM_BOT_USERNAME', '');           // e.g. melnrage_surf_bot (no @)  ← USER PROVIDES
define('TELEGRAM_WEBHOOK_SECRET', '');         // random 32+ chars; sent as Telegram secret_token
define('SURF_ALERT_CRON_KEY',     '');         // random 32+ chars; guards test/run_alerts
```
Note: never deploy real secrets via the public `config/` rsync if the repo is shared — these mirror the existing in-file convention (ANTHROPIC_API_KEY is already in-file), so follow the same handling.

---

## 2. Database — new table `surf_alerts` (run in phpMyAdmin; no migration convention here)

```sql
CREATE TABLE surf_alerts (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  chat_id         BIGINT NULL,                 -- Telegram chat id; NULL until linked
  profile_type    VARCHAR(16) NOT NULL,        -- 'beginner' | 'advanced' | 'custom'
  wave_min        DECIMAL(3,1) NULL,           -- metres
  wave_max        DECIMAL(3,1) NULL,           -- metres (NULL = no ceiling)
  wind_max        DECIMAL(4,1) NULL,           -- m/s   (NULL = no cap)
  period_min      DECIMAL(3,1) NULL,           -- seconds (NULL/0 = any)
  dirs            VARCHAR(40) NOT NULL DEFAULT 'PV,P,V,ŠV', -- favourable compass set
  link_token      VARCHAR(64) NULL,            -- single-use start token
  token_expires   DATETIME NULL,
  status          VARCHAR(16) NOT NULL DEFAULT 'pending', -- pending|active|stopped
  last_notified_date DATE NULL,                -- dedup guard
  created_at      DATETIME NOT NULL,
  linked_at       DATETIME NULL,
  INDEX (status),
  INDEX (chat_id),
  INDEX (link_token)
);
```

---

## 3. Controller methods — `modules/test/controllers/Test.php`

### 3.1 `notify_modal()` — render modal body (public)
Returns the modal view (loaded by `mx-build-modal`). No auth.
```php
public function notify_modal(): void {
    $this->view('notify_modal');
}
```

### 3.2 `subscribe()` — create pending sub, return deep link (public, AJAX)
- Read `post('profile')` ∈ {beginner, advanced, custom}.
- Resolve thresholds:
  - beginner/advanced → `_surf_alert_presets()`.
  - custom → read posted `wave_min, wave_max, wind_max, period_min` (+ optional `dirs[]`); **validate server-side**: numeric, sane ranges (wave 0–4, wind 0–25, period 0–12), `wave_min <= wave_max`. On fail → `echo json_encode(['ok'=>false,'error'=>'…']); die();`.
- Generate `link_token = _gen_token()` (32-byte hex), `token_expires = now + 30 min`.
- `insert` row with `status='pending'`, `created_at=now`.
- Respond JSON: `{"ok":true,"deep_link":"https://t.me/<BOT_USERNAME>?start=<token>"}`.
- (Opportunistic cleanup: delete pending rows older than 30 min.)

### 3.3 `telegram_webhook()` — link + /stop (public, secret-guarded)
- Verify header `X-Telegram-Bot-Api-Secret-Token` === `TELEGRAM_WEBHOOK_SECRET`; else `http_response_code(403); die();`.
- `$update = json_decode(file_get_contents('php://input'), true);`
- Extract `chat_id = $update['message']['chat']['id']`, `text = trim($update['message']['text'] ?? '')`.
- **`/start <token>`:**
  - Find pending row by `link_token`, not expired. If none → `_tg_send(chat_id, 'Nuoroda nebegalioja — užsiprenumeruok iš naujo svetainėje.')`.
  - Dedup by chat_id: if an existing row has this chat_id, update it with the new profile/thresholds and set the pending row aside (or update-in-place); ensure only one active row per chat_id.
  - Set `chat_id`, `status='active'`, `linked_at=now`, clear `link_token`/`token_expires`. `_tg_send` confirmation (LT).
- **`/stop`:** set all rows for chat_id `status='stopped'`; `_tg_send('Pranešimai išjungti. Parašyk svetainėje, jei norėsi vėl.')`.
- Always `http_response_code(200)` quickly.

### 3.4 `run_alerts()` — evening cron (public, key-guarded)
- Verify `get('key') === SURF_ALERT_CRON_KEY`; else 403.
- `$days = $this->_build_forecast_days();` → find `$tomorrow = date('Y-m-d', strtotime('+1 day'))`; pick day where `date === $tomorrow`. If none/empty → `die('no forecast')` (send nothing).
- Load active subs: `get_many_where('status','active','surf_alerts')` (or `query`).
- For each: if `_alert_matches($day, $sub)` AND `$sub->last_notified_date !== $tomorrow` →
  - `_tg_send($sub->chat_id, _format_alert_message($day))`.
  - On success: `update($sub->id, ['last_notified_date'=>$tomorrow], 'surf_alerts')`.
  - On Telegram 403 (user blocked bot) → set `status='stopped'`.
- Echo a tiny summary for the cron log (counts).

### 3.5 Private helpers
```php
private function _surf_alert_presets(): array {
    $dirs = 'PV,P,V,ŠV';
    return [
        'beginner' => ['wave_min'=>0.4,'wave_max'=>1.0,'wind_max'=>6.0,'period_min'=>null,'dirs'=>$dirs],
        'advanced' => ['wave_min'=>1.2,'wave_max'=>null,'wind_max'=>12.0,'period_min'=>4.0,'dirs'=>$dirs],
    ];
}

private function _alert_matches(array $day, $s): bool {
    // wave: representative = day wave_max ("biggest it gets")
    if ($s->wave_min !== null && $day['wave_max'] < $s->wave_min) return false;
    if ($s->wave_max !== null && $day['wave_max'] > $s->wave_max) return false;
    // wind: PRD default = wind_avg
    if ($s->wind_max !== null && $day['wind_avg'] !== null && $day['wind_avg'] > $s->wind_max) return false;
    // period
    if ($s->period_min !== null && $s->period_min > 0 && $day['wave_period'] < $s->period_min) return false;
    // direction
    $ok = explode(',', $s->dirs);
    if (!in_array($day['wind_dir'], $ok, true)) return false;
    return true;
}

private function _tg_send($chat_id, string $text): array {
    $ch = curl_init('https://api.telegram.org/bot'.constant('TELEGRAM_BOT_TOKEN').'/sendMessage');
    curl_setopt_array($ch, [
        CURLOPT_POST=>true, CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>10,
        CURLOPT_POSTFIELDS=>http_build_query(['chat_id'=>$chat_id,'text'=>$text]),
    ]);
    $raw = curl_exec($ch); $code = curl_getinfo($ch, CURLINFO_HTTP_CODE); curl_close($ch);
    return ['code'=>$code, 'body'=>json_decode($raw, true)];
}

private function _format_alert_message(array $d): string {
    return "🏄 Rytoj Melnragėje geros sąlygos! "
         . "Bangos {$d['wave_min']}–{$d['wave_max']} m, "
         . "vėjas {$d['wind_avg']} m/s iš {$d['wind_dir']}. "
         . "Parašyk /stop, kad atsisakytum.";
}

private function _gen_token(): string { return bin2hex(random_bytes(16)); }
```

**Match rule note (tunable):** wave match uses the day's `wave_max` as the representative height — beginner rejects days bigger than their ceiling, advanced requires the day to reach their floor. Documented as a deliberate v1 choice; easy to revisit.

---

## 4. Modal view — `modules/test/views/notify_modal.php`

- Heading (LT): "Gauk pranešimą, kai rytoj bus geros sąlygos".
- Profile chooser (3 buttons/radios): Pradedantysis (beginner) · Pažengęs (advanced) · Pasirinktinai (custom).
- Custom panel (hidden until custom): number inputs — Bangos min/max (m, step 0.1), Vėjas maks. (m/s), Periodas min. (s). Dirs default to favourable set (optional checkboxes; v1 may keep fixed).
- "Prisijungti per Telegram" button.
- Inline `<script>`: on click → `fetch(BASE_URL+'test/subscribe', {method:'POST', body: FormData})` → on `{ok:true}` open `deep_link` (`window.location.href = deep_link` on mobile so Telegram app opens) and show "Atidaryk Telegram ir paspausk Start". On `{ok:false}` show error.
- Style with the page's existing `--wx-*` glass tokens for visual consistency.

---

## 5. Bell button — `modules/test/views/port.php`

Add to `.wx-header` (or beside the NOW card):
```php
<button type="button" class="wx-bell"
        mx-get="test/notify_modal" mx-build-modal="surf-notify-modal"
        aria-label="Pranešimai">🔔 Pranešti</button>
```
Add a small `.wx-bell` rule in the page `<style>`. (trongate-mx.js is already loaded by the public template.)

---

## 6. One-time Telegram setup (manual, after token exists)

1. Create bot in **@BotFather** → copy token + username into config.
2. Register webhook (run once, e.g. from terminal):
```bash
curl "https://api.telegram.org/bot<TOKEN>/setWebhook?url=https://www.surfclub.lt/test/telegram_webhook&secret_token=<TELEGRAM_WEBHOOK_SECRET>"
```
3. Verify: `curl "https://api.telegram.org/bot<TOKEN>/getWebhookInfo"`.
4. (Optional) set bot commands via `setMyCommands`: `/stop` → "Atsisakyti pranešimų".

---

## 7. Hostinger cron (hPanel → Advanced → Cron Jobs)

- **Timezone gotcha:** Hostinger cron usually runs in UTC. 18:00 Europe/Vilnius = **15:00 UTC (summer, +3)** / 16:00 UTC (winter, +2). Confirm panel TZ; pick the hour accordingly (or run at a fixed UTC hour and accept ±1h seasonal drift).
- Command:
```
wget -q -O /dev/null "https://www.surfclub.lt/test/run_alerts?key=<SURF_ALERT_CRON_KEY>"
```
- Schedule: once daily at the chosen hour, minute 0.

---

## 8. Build order (each step independently testable)

1. **Config + DB table** — add constants, create `surf_alerts`.
2. **Web subscribe** — `subscribe()` + `notify_modal.php` + bell on `port.php`. Test: tapping bell → modal → returns a valid `t.me/...` deep link (token row appears as `pending`).
3. **BotFather + setWebhook** (manual). Needs deploy to a public HTTPS URL (surfclub.lt) — webhook can't hit localhost.
4. **`telegram_webhook()`** — deploy, then real-device test: open deep link → Start → row goes `active`, bot confirms; `/stop` → `stopped`.
5. **`run_alerts()` + helpers** — test locally by seeding rows and hitting `test/run_alerts?key=…`; verify match logic, dedup, and forecast-outage = nothing sent (temporarily stub `_build_forecast_days`).
6. **Hostinger cron** — schedule; confirm a live evening fire.
7. **Acceptance pass** — run the 7 PRD test cases.

## 9. Risks / watch-items
- Local webhook testing impossible without public HTTPS → use the deployed site (or ngrok with a test bot).
- Hostinger cron TZ (above).
- `random_bytes`/`bin2hex` available (standard PHP 7+) — fine.
- Public `subscribe()` writes rows → expired-pending cleanup keeps the table tidy; consider light rate-limiting if abused.
- Confirm `gmp`/`openssl` NOT needed (Telegram path uses neither — only cURL).
- Escape all output in `notify_modal.php`.

## 10. Files touched
- `config/config.php` — 4 constants.
- `modules/test/controllers/Test.php` — `notify_modal`, `subscribe`, `telegram_webhook`, `run_alerts` + 5 private helpers.
- `modules/test/views/notify_modal.php` — NEW.
- `modules/test/views/port.php` — bell button + `.wx-bell` style.
- DB: `surf_alerts` table.
- Hostinger: cron job + Telegram setWebhook (out-of-repo).
