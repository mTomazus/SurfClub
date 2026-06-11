<?php
/**
 * Staff Calendar — monthly grid view (Trongate MX powered, no custom JS).
 * Loaded inside admin_area template via $data['view_file'] = 'calendar'.
 *
 * Follows the admin mx-oob nav convention: exposes three sibling regions so the
 * sidebar link can distribute them —
 *   #title      → #top-title  (header bar)
 *   #stat-panel → #stat-cards (dashboard chrome)
 *   #sc-wrapper → #form-container (main content)
 *
 * NOTE: this file is included by Template (static scope) — $this is NOT the
 * controller here, so rendering goes through sc_render_* helper functions.
 */
require_once APPPATH . 'modules/staff_calendar/views/_sc_helpers.php';

$sc_meta       = sc_status_meta();
$status_labels = $sc_meta['labels'];
$season_months = [6 => 'June', 7 => 'July', 8 => 'August'];
$today_str     = date('Y-m-d');
?>

<div id="title" style="display:none"><h1>Staff Calendar</h1></div>

<!-- ===== OVERVIEW (top-level sibling — hoisted into #stat-cards by the
     sidebar link's mx-select-oob, exactly like every other admin module) ===== -->
<?= sc_render_stat_panel($stats) ?>

<div id="sc-wrapper">

<style>
/* ============================================================
   STAFF CALENDAR — charcoal base, single cyan accent
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --sc-bg:        #15141a;
  --sc-surface:   #1c1b23;
  --sc-surface-2: #211f2a;
  --sc-line:      rgba(255,255,255,0.06);
  --sc-line-2:    rgba(255,255,255,0.11);
  --sc-text:      #f4f3f8;
  --sc-muted:     #a09db1;
  --sc-faint:     #6c6979;

  --sc-accent:    #45c4d6;
  --sc-working:   #2ec27e;
  --sc-halfday:   #f2a33c;
  --sc-dayoff:    #76849e;
  --sc-sick:      #e5484d;

  --sc-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --sc-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --sc-ease:   cubic-bezier(0.16, 1, 0.3, 1);
  --sc-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* ── Overview stat strip (hoisted to #stat-cards on the dashboard) ── */
/* The dashboard styles #stat-cards as a card grid — force block so the
   hoisted strip spans full width. */
#stat-cards { display: block !important; }
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; }
.sc-stats {
  display: grid;
  grid-template-columns: 1.5fr repeat(5, 1fr);
  gap: 1px;
  background: var(--sc-line-2);
  border: 1px solid var(--sc-line-2);
  border-radius: 14px;
  overflow: hidden;
  font-family: var(--sc-font);
  color: var(--sc-text);
  animation: sc-fade 0.4s var(--sc-ease) both;
}
.sc-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--sc-surface);
}
.sc-stat__num {
  font-family: var(--sc-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
}
.sc-stat__num small { font-size: 0.6em; font-weight: 600; color: var(--sc-muted); margin-left: 1px; }
.sc-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--sc-muted);
}
.sc-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--sc-surface); }
.sc-stat--lead .sc-stat__num { color: var(--sc-accent); }
.sc-stat__bar {
  height: 3px;
  margin-top: 0.4rem;
  border-radius: 99px;
  background: rgba(255,255,255,0.08);
  overflow: hidden;
}
.sc-stat__bar i {
  display: block;
  height: 100%;
  background: var(--sc-accent);
  border-radius: inherit;
  transform-origin: left;
  animation: sc-grow 0.8s var(--sc-ease) both;
}
.sc-stat--working .sc-stat__num { color: var(--sc-working); }
.sc-stat--halfday .sc-stat__num { color: var(--sc-halfday); }
.sc-stat--dayoff  .sc-stat__num { color: var(--sc-dayoff); }
.sc-stat--sick    .sc-stat__num { color: var(--sc-sick); }

@keyframes sc-grow { from { transform: scaleX(0); } }
@keyframes sc-fade { from { opacity: 0; } }

/* ── Wrapper ─────────────────────────────────────────────── */
#sc-wrapper {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--sc-bg);
  border: 1px solid var(--sc-line);
  border-radius: 16px;
  color: var(--sc-text);
  font-family: var(--sc-font);
}

/* ── Header ──────────────────────────────────────────────── */
.sc-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.35rem;
}
.sc-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--sc-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--sc-accent);
}
.sc-month {
  margin: 0;
  font-size: clamp(1.55rem, 3vw, 2rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--sc-text);
  text-transform: none;
}
.sc-month span { color: var(--sc-faint); font-weight: 500; }

.sc-tools { display: flex; align-items: center; gap: 0.6rem; flex-wrap: wrap; }

.sc-seg {
  display: inline-flex;
  padding: 3px;
  background: var(--sc-surface);
  border: 1px solid var(--sc-line-2);
  border-radius: 10px;
}
.sc-seg a {
  padding: 0.42rem 0.95rem;
  border-radius: 7px;
  font-size: 0.78rem;
  font-weight: 600;
  color: var(--sc-muted);
  text-decoration: none;
  transition: color 0.15s var(--sc-ease), background 0.15s var(--sc-ease);
}
.sc-seg a:hover { color: var(--sc-text); }
.sc-seg a.is-active {
  background: rgba(255,255,255,0.09);
  color: var(--sc-text);
  box-shadow: inset 0 1px 0 rgba(255,255,255,0.07), 0 1px 3px rgba(0,0,0,0.35);
}
.sc-seg a:focus-visible { outline: 2px solid var(--sc-accent); outline-offset: 2px; }

.sc-print {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.5rem 0.95rem;
  border: 1px solid var(--sc-line-2);
  border-radius: 10px;
  background: transparent;
  color: var(--sc-muted);
  font-size: 0.78rem;
  font-weight: 600;
  text-decoration: none;
  transition: color 0.15s var(--sc-ease), background 0.15s var(--sc-ease), transform 0.12s var(--sc-ease);
}
.sc-print:hover { background: rgba(255,255,255,0.05); color: var(--sc-text); }
.sc-print:active { transform: translateY(1px) scale(0.985); }
.sc-print svg { flex-shrink: 0; }

/* ── Legend + hint row ───────────────────────────────────── */
.sc-meta {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.5rem 1.25rem;
  margin-bottom: 0.85rem;
}
.sc-legend { display: flex; align-items: center; gap: 1rem; flex-wrap: wrap; }
.sc-key {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-size: 0.72rem;
  font-weight: 500;
  color: var(--sc-muted);
}
.sc-key i { width: 9px; height: 9px; border-radius: 3px; flex-shrink: 0; }
.sc-key--working i { background: var(--sc-working); }
.sc-key--halfday i { background: var(--sc-halfday); }
.sc-key--dayoff  i { background: var(--sc-dayoff); }
.sc-key--sick    i { background: var(--sc-sick); }
.sc-key--unset   i { background: rgba(255,255,255,0.12); }

.sc-hint { margin: 0; font-size: 0.7rem; color: var(--sc-faint); }
.sc-hint b { color: var(--sc-muted); font-weight: 600; }
@media (hover: none) { .sc-hint { display: none; } }

/* ── Board / table ───────────────────────────────────────── */
.sc-board {
  border: 1px solid var(--sc-line-2);
  border-radius: 12px;
  background: var(--sc-surface);
  overflow: auto;
}
.sc-board::-webkit-scrollbar { height: 8px; }
.sc-board::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.13); border-radius: 99px; }
.sc-board::-webkit-scrollbar-track { background: transparent; }

.sc-table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0;
  font-size: 0.75rem;
  font-family: var(--sc-font);
  color: var(--sc-text);
  background: transparent;
}
.sc-table thead { background: transparent; }
.sc-table th, .sc-table td {
  border: 0;
  border-bottom: 1px solid var(--sc-line);
  border-right: 1px solid rgba(255,255,255,0.035);
  background: transparent;
  padding: 0;
  text-align: center;
  vertical-align: middle;
  font-family: inherit;
}
.sc-table th:last-child, .sc-table td:last-child { border-right: 0; }
.sc-table tbody tr:last-child td { border-bottom: 0; }
.sc-table tbody tr {
  animation: sc-fade 0.4s var(--sc-ease) backwards;
  animation-delay: min(calc(var(--i) * 35ms), 420ms);
}
.sc-table tbody tr:hover td {
  background-image: linear-gradient(rgba(255,255,255,0.03), rgba(255,255,255,0.03));
}

/* Head: day columns */
.sc-table thead th { padding: 0.5rem 0.15rem 0.45rem; }
.sc-day__num {
  display: block;
  width: 22px;
  height: 22px;
  line-height: 22px;
  margin-inline: auto;
  border-radius: 7px;
  font-family: var(--sc-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.74rem;
  font-weight: 600;
  color: var(--sc-text);
}
.sc-day__dow {
  display: block;
  margin-top: 2px;
  font-size: 0.54rem;
  font-weight: 600;
  letter-spacing: 0.09em;
  color: var(--sc-faint);
}
th.sc-wkend { background: rgba(255,255,255,0.025); }
th.sc-today .sc-day__num { background: var(--sc-accent); color: #0b2227; }
th.sc-today .sc-day__dow { color: var(--sc-accent); }

td.sc-wkend-cell { background-color: rgba(255,255,255,0.02); }
td.sc-today-cell { background-color: rgba(69,196,214,0.055); }

/* Sticky name column */
.sc-table th.sc-name-col, .sc-table td.sc-name-cell {
  position: sticky;
  left: 0;
  z-index: 2;
  background: var(--sc-surface-2);
  border-right: 1px solid var(--sc-line-2);
  text-align: left;
  min-width: 168px;
  white-space: nowrap;
}
.sc-table th.sc-name-col {
  padding: 0 0.85rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--sc-faint);
}
.sc-table td.sc-name-cell { padding: 0.4rem 0.85rem 0.4rem 0.7rem; }

.sc-person { display: flex; align-items: center; gap: 0.6rem; }
.sc-ava {
  display: grid;
  place-items: center;
  width: 30px;
  height: 30px;
  border-radius: 9px;
  background: rgba(255,255,255,0.06);
  border: 1px solid var(--sc-line-2);
  font-family: var(--sc-mono);
  font-size: 0.66rem;
  font-weight: 600;
  letter-spacing: 0.02em;
  color: var(--sc-muted);
  flex-shrink: 0;
}
.sc-person__name { display: block; font-size: 0.8rem; font-weight: 600; letter-spacing: -0.01em; line-height: 1.25; color: var(--sc-text); }
.sc-person__role { display: block; font-size: 0.56rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; color: var(--sc-faint); }

/* Sticky total column */
.sc-table th.sc-total-col, .sc-table td.sc-total-col {
  position: sticky;
  right: 0;
  z-index: 2;
  background: var(--sc-surface-2);
  border-left: 1px solid var(--sc-line-2);
  min-width: 50px;
  font-family: var(--sc-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--sc-text);
}
.sc-table thead th.sc-total-col {
  font-size: 0.56rem;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--sc-faint);
}
td.sc-total-col--zero { color: var(--sc-faint); }

/* ── Data cells ──────────────────────────────────────────── */
td.sc-cell { height: 46px; min-width: 40px; position: relative; }
.sc-quick { position: absolute; inset: 0; display: grid; place-items: center; }

/* Set state: status chip */
.sc-chip-btn, .sc-chip-btn:hover { background: none; border: none; padding: 0; margin: 0; cursor: pointer; line-height: 0; }
.sc-chip-btn:focus-visible { outline: 2px solid var(--sc-accent); outline-offset: 2px; border-radius: 8px; }
.sc-chip {
  position: relative;
  display: grid;
  place-items: center;
  width: 27px;
  height: 27px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 700;
  color: #fff;
  box-shadow: 0 2px 6px rgba(0,0,0,0.35), inset 0 1px 0 rgba(255,255,255,0.22);
  transition: transform 0.15s var(--sc-ease);
  animation: sc-pop 0.3s var(--sc-spring);
}
.sc-chip--working { background: var(--sc-working); }
.sc-chip--halfday { background: var(--sc-halfday); color: #231603; }
.sc-chip--dayoff  { background: var(--sc-dayoff); }
.sc-chip--sick    { background: var(--sc-sick); }
.sc-chip-btn:hover  .sc-chip { transform: scale(1.1); }
.sc-chip-btn:active .sc-chip { transform: scale(0.92); transition-duration: 60ms; }
@keyframes sc-pop { from { transform: scale(0.4); opacity: 0; } }

.sc-note-dot {
  position: absolute;
  top: -3px;
  right: -3px;
  width: 9px;
  height: 9px;
  border-radius: 50%;
  background: #fff;
  border: 2px solid var(--sc-surface);
}

/* Clear button — revealed on cell hover / focus */
.sc-x {
  position: absolute;
  top: 3px;
  right: 3px;
  width: 14px;
  height: 14px;
  display: grid;
  place-items: center;
  padding: 0;
  border: none;
  border-radius: 4px;
  background: rgba(0,0,0,0.5);
  color: rgba(255,255,255,0.85);
  font-size: 0.62rem;
  line-height: 1;
  cursor: pointer;
  opacity: 0;
  transform: scale(0.8);
  transition: opacity 0.15s var(--sc-ease), transform 0.15s var(--sc-ease), background 0.15s var(--sc-ease);
}
td.sc-cell:hover .sc-x, .sc-x:focus-visible { opacity: 1; transform: none; }
.sc-x:hover { background: var(--sc-sick); border: none; }
.sc-x:focus-visible { outline: 2px solid var(--sc-accent); outline-offset: 1px; }

/* Empty state: faint dot, quick actions on hover */
.sc-dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: rgba(255,255,255,0.1);
  transition: opacity 0.15s var(--sc-ease);
}
.sc-acts {
  position: absolute;
  inset: 0;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 3px;
  opacity: 0;
  transition: opacity 0.15s var(--sc-ease);
}
.sc-acts__row { display: flex; gap: 3px; }
td.sc-cell:hover .sc-acts, td.sc-cell:focus-within .sc-acts { opacity: 1; }
td.sc-cell:hover .sc-dot,  td.sc-cell:focus-within .sc-dot  { opacity: 0; }

.sc-act {
  display: grid;
  place-items: center;
  padding: 0;
  margin: 0;
  width: 18px;
  height: 18px;
  border: 1px solid var(--sc-line-2);
  border-radius: 6px;
  background: var(--sc-surface-2);
  color: var(--sc-muted);
  font-size: 0.62rem;
  font-weight: 700;
  font-family: inherit;
  line-height: 1;
  cursor: pointer;
  transition: background 0.12s var(--sc-ease), color 0.12s var(--sc-ease),
              border-color 0.12s var(--sc-ease), transform 0.12s var(--sc-ease);
}
.sc-act:hover {
  transform: translateY(-1px);
  color: #fff;
  background: var(--sc-surface-2);
  border: 1px solid var(--sc-line-2);
}
.sc-act:active { transform: scale(0.92); transition-duration: 60ms; }
.sc-act:focus-visible { outline: 2px solid var(--sc-accent); outline-offset: 1px; }
.sc-act--work:hover { background: var(--sc-working); border-color: transparent; }
.sc-act--off:hover  { background: var(--sc-dayoff); border-color: transparent; }
.sc-act--more { width: 39px; height: 12px; font-size: 0.66rem; border-radius: 4px; }
.sc-act--more:hover { background: rgba(255,255,255,0.12); border-color: var(--sc-line-2); color: var(--sc-text); }

@media (hover: none) {
  .sc-acts { opacity: 1; }
  .sc-dot { display: none; }
  .sc-x { opacity: 1; transform: none; }
}

/* ── Empty roster state ──────────────────────────────────── */
.sc-empty {
  padding: 3.5rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--sc-line-2);
  border-radius: 12px;
  color: var(--sc-muted);
}
.sc-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--sc-text); }
.sc-empty p { margin: 0; font-size: 0.8rem; }

/* ============================================================
   MODAL (built by mx-build-modal; framework supplies the shell)
   ============================================================ */
#sc-modal.modal {
  background: var(--sc-surface);
  color: var(--sc-text);
  border: 1px solid var(--sc-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--sc-font);
}
#sc-modal .modal-heading {
  background: transparent;
  color: var(--sc-text);
  border: none;
  border-bottom: 1px solid var(--sc-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#sc-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#sc-modal form { display: block; width: auto; }

.sc-modal-who { display: flex; align-items: center; gap: 0.7rem; margin-bottom: 1.15rem; text-align: left; }
.sc-ava--lg { width: 38px; height: 38px; font-size: 0.78rem; border-radius: 11px; }
.sc-modal-who strong { display: block; font-size: 0.95rem; font-weight: 600; letter-spacing: -0.01em; line-height: 1.3; }
.sc-modal-who span {
  display: block;
  font-family: var(--sc-mono);
  font-size: 0.68rem;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.07em;
  color: var(--sc-muted);
}

.sc-status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.15rem; }
.sc-opt { display: block; cursor: pointer; position: relative; }
.sc-opt input { position: absolute; opacity: 0; pointer-events: none; }
.sc-opt--working { --c: var(--sc-working); }
.sc-opt--halfday { --c: var(--sc-halfday); }
.sc-opt--dayoff  { --c: var(--sc-dayoff); }
.sc-opt--sick    { --c: var(--sc-sick); }
.sc-opt__card {
  display: flex;
  align-items: center;
  gap: 0.55rem;
  padding: 0.65rem 0.8rem;
  border: 1px solid var(--sc-line-2);
  border-radius: 10px;
  background: var(--sc-surface-2);
  font-size: 0.82rem;
  font-weight: 600;
  color: var(--sc-muted);
  transition: border-color 0.15s var(--sc-ease), color 0.15s var(--sc-ease),
              background 0.15s var(--sc-ease), transform 0.12s var(--sc-ease);
}
.sc-opt__card i { width: 10px; height: 10px; border-radius: 4px; background: var(--c); flex-shrink: 0; }
.sc-opt:hover .sc-opt__card { border-color: rgba(255,255,255,0.24); color: var(--sc-text); }
.sc-opt:active .sc-opt__card { transform: scale(0.98); }
.sc-opt input:checked + .sc-opt__card {
  border-color: var(--c);
  color: var(--sc-text);
  background: color-mix(in srgb, var(--c) 12%, var(--sc-surface-2));
}
.sc-opt input:focus-visible + .sc-opt__card { outline: 2px solid var(--sc-accent); outline-offset: 2px; }

.sc-field { display: grid; gap: 0.4rem; margin-bottom: 1.25rem; text-align: left; }
.sc-field label {
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--sc-muted);
}
#sc-modal #sc-modal-notes {
  width: 100%;
  min-height: 70px;
  padding: 0.6rem 0.7rem;
  border: 1px solid var(--sc-line-2);
  border-radius: 10px;
  background: var(--sc-bg);
  color: var(--sc-text);
  font-family: var(--sc-font);
  font-size: 0.85rem;
  resize: vertical;
  box-sizing: border-box;
  transition: border-color 0.15s var(--sc-ease), box-shadow 0.15s var(--sc-ease);
}
#sc-modal #sc-modal-notes:focus {
  outline: none;
  border-color: var(--sc-accent);
  box-shadow: 0 0 0 3px rgba(69,196,214,0.18);
}

.sc-modal-btns { display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
.sc-modal-btns button {
  padding: 0.52rem 1.05rem;
  margin: 0;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--sc-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s var(--sc-ease), color 0.15s var(--sc-ease),
              border-color 0.15s var(--sc-ease), transform 0.12s var(--sc-ease);
}
.sc-modal-btns button:active { transform: translateY(1px) scale(0.985); transition-duration: 60ms; }
#sc-modal .sc-btn-clear { margin-right: auto; background: transparent; color: var(--sc-sick); }
#sc-modal .sc-btn-clear:hover { background: rgba(229,72,77,0.12); border-color: transparent; }
#sc-modal .sc-btn-cancel { background: transparent; border-color: var(--sc-line-2); color: var(--sc-muted); }
#sc-modal .sc-btn-cancel:hover { color: var(--sc-text); background: rgba(255,255,255,0.05); border-color: var(--sc-line-2); }
#sc-modal .sc-btn-save { background: var(--sc-accent); border-color: var(--sc-accent); color: #08252a; font-weight: 700; }
#sc-modal .sc-btn-save:hover { filter: brightness(1.08); background: var(--sc-accent); border-color: var(--sc-accent); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 56.25rem) {
  .sc-stats { grid-template-columns: repeat(3, 1fr); }
  .sc-stat--lead { grid-column: 1 / -1; }
}
@media (max-width: 33.75rem) {
  .sc-stats { grid-template-columns: repeat(2, 1fr); }
  #sc-wrapper { padding: 1rem; border-radius: 12px; }
  .sc-table th.sc-name-col, .sc-table td.sc-name-cell { min-width: 138px; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<!-- ===== HEADER ===== -->
<div class="sc-head">
    <div>
        <p class="sc-eyebrow">Staff schedule &middot; Season <?= $year ?></p>
        <h2 class="sc-month"><?= $month_name ?> <span><?= $year ?></span></h2>
    </div>
    <div class="sc-tools">
        <nav class="sc-seg" aria-label="Season month">
            <?php foreach ($season_months as $m => $m_name): ?>
                <?php if ($m === (int) $month): ?>
                    <a class="is-active" aria-current="page"><?= $m_name ?></a>
                <?php else: ?>
                    <a href=""
                       mx-get="staff_calendar/index/<?= $m ?>"
                       mx-target="#form-container" mx-select="#sc-wrapper"><?= $m_name ?></a>
                <?php endif; ?>
            <?php endforeach; ?>
        </nav>
        <a class="sc-print" href="staff_calendar/export_pdf/<?= $month ?>" target="_blank" rel="noopener">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 9V2h12v7"/>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                <rect x="6" y="14" width="12" height="8"/>
            </svg>
            Print
        </a>
    </div>
</div>

<?php if (empty($staff)): ?>

<!-- ===== EMPTY ROSTER ===== -->
<div class="sc-empty">
    <p class="sc-empty__title">No staff on the roster yet</p>
    <p>Add members with a full name in the Members module &mdash; they appear here automatically.</p>
</div>

<?php else: ?>

<!-- ===== LEGEND + HINT ===== -->
<div class="sc-meta">
    <div class="sc-legend">
        <?php foreach ($status_labels as $slug => $label): ?>
        <span class="sc-key sc-key--<?= $slug ?>"><i></i><?= $label ?></span>
        <?php endforeach; ?>
        <span class="sc-key sc-key--unset"><i></i>Not set</span>
    </div>
    <p class="sc-hint">Hover a day for quick actions &mdash; <b>&#10003;</b> working, <b>&#10005;</b> day off, <b>&#8943;</b> more &amp; notes</p>
</div>

<!-- ===== CALENDAR GRID ===== -->
<div class="sc-board">
<table class="sc-table">
    <thead>
        <tr>
            <th class="sc-name-col">Team</th>
            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $ts         = mktime(0, 0, 0, $month, $d, $year);
                $is_weekend = (int) date('N', $ts) >= 6;
                $date_str   = date('Y-m-d', $ts);
                $day_abbr   = strtoupper(substr(date('D', $ts), 0, 2));
                $th_class   = trim(($is_weekend ? 'sc-wkend ' : '') . ($date_str === $today_str ? 'sc-today' : ''));
            ?>
            <th class="<?= $th_class ?>">
                <span class="sc-day__num"><?= $d ?></span>
                <span class="sc-day__dow"><?= $day_abbr ?></span>
            </th>
            <?php endfor; ?>
            <th class="sc-total-col">Days</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach (array_values($staff) as $i => $member):
        $total = 0.0;
        $display_name = !empty($member->full_name) ? $member->full_name : $member->username;
        $role_str     = !empty($member->role) ? $member->role : 'Staff';
    ?>
        <tr style="--i:<?= $i ?>">
            <td class="sc-name-cell">
                <div class="sc-person">
                    <span class="sc-ava" aria-hidden="true"><?= htmlspecialchars(sc_initials($display_name)) ?></span>
                    <span>
                        <span class="sc-person__name"><?= htmlspecialchars($display_name) ?></span>
                        <span class="sc-person__role"><?= htmlspecialchars($role_str) ?></span>
                    </span>
                </div>
            </td>

            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $entry    = $schedule_index[$member->id . '|' . $date_str] ?? null;
                $status   = $entry ? $entry->status : '';
                $notes    = $entry ? $entry->notes  : '';
                $dow_body = (int) date('N', mktime(0, 0, 0, $month, $d, $year));

                if ($status === 'working') $total += 1.0;
                if ($status === 'halfday') $total += 0.5;

                echo sc_render_cell([
                    'member_id' => $member->id,
                    'date_str'  => $date_str,
                    'status'    => $status,
                    'notes'     => $notes,
                    'is_wkend'  => $dow_body >= 6,
                ]);
            endfor; ?>

            <?= sc_render_row_total((int) $member->id, $total) ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>

<?php endif; ?>

</div><!-- /#sc-wrapper -->
