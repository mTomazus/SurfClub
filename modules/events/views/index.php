<?php
/**
 * Events admin — main panel (Trongate MX powered).
 *
 * Follows the admin mx-oob nav convention: exposes three sibling regions so
 * the sidebar link can distribute them —
 *   #title           → #top-title  (header bar)
 *   #stat-panel      → #stat-cards (dashboard chrome)
 *   #event-container → #form-container (main content)
 *
 * #events-container loads events/fetch_table on page load and after every
 * add/delete (mx-on-success re-trigger). The fetch response carries a fresh
 * #stat-panel which is OOB-swapped in, keeping stats live.
 */
require_once APPPATH . 'modules/events/views/_ev_helpers.php';
?>

<div id="title" style="display:none"><h1>Events</h1></div>

<!-- ===== OVERVIEW (hoisted into #stat-cards by the sidebar) ===== -->
<?= ev_render_stat_panel($rows) ?>

<div id="event-container">

<style>
/* ============================================================
   EVENTS ADMIN — charcoal base, single cyan accent
   (same design language as the other admin panels)
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --ev-bg:        #15141a;
  --ev-surface:   #1c1b23;
  --ev-surface-2: #211f2a;
  --ev-line:      rgba(255,255,255,0.06);
  --ev-line-2:    rgba(255,255,255,0.11);
  --ev-text:      #f4f3f8;
  --ev-muted:     #a09db1;
  --ev-faint:     #6c6979;

  --ev-accent:    #45c4d6;
  --ev-green:     #2ec27e;
  --ev-amber:     #f2a33c;
  --ev-red:       #e5484d;

  --ev-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --ev-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --ev-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

#stat-cards { display: block !important; }

@keyframes ev-fade    { from { opacity: 0; } }
@keyframes ev-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }

/* ── Stat strip ──────────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--ev-font); color: var(--ev-text); }
.ev-stats {
  display: grid;
  grid-template-columns: 2fr repeat(3, 1fr);
  gap: 1px;
  background: var(--ev-line-2);
  border: 1px solid var(--ev-line-2);
  border-radius: 14px;
  overflow: hidden;
  font-family: var(--ev-font);
  color: var(--ev-text);
  animation: ev-fade 0.4s var(--ev-ease) both;
}
.ev-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--ev-surface);
}
.ev-stat__num {
  font-family: var(--ev-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
}
.ev-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ev-muted);
}
.ev-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--ev-surface); }
.ev-stat__next {
  font-size: 1.05rem;
  font-weight: 700;
  letter-spacing: -0.02em;
  line-height: 1.2;
  color: var(--ev-text);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}
.ev-stat__next--none { color: var(--ev-faint); font-weight: 600; }
.ev-stat__when {
  font-family: var(--ev-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.7rem;
  font-weight: 500;
  color: var(--ev-accent);
}
.ev-stat--up   .ev-stat__num { color: var(--ev-green); }
.ev-stat--past .ev-stat__num { color: var(--ev-faint); }

/* ── Shell ───────────────────────────────────────────────── */
.ev-shell {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--ev-bg);
  border: 1px solid var(--ev-line);
  border-radius: 16px;
  color: var(--ev-text);
  font-family: var(--ev-font);
}
.ev-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.ev-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--ev-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--ev-accent);
}
.ev-title-h {
  margin: 0;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--ev-text);
  text-transform: none;
}
.ev-btn-add {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.05rem;
  border: 1px solid var(--ev-accent);
  border-radius: 9px;
  background: var(--ev-accent);
  color: #08252a;
  font-family: var(--ev-font);
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  margin: 0;
  transition: filter 0.15s var(--ev-ease), transform 0.12s var(--ev-ease);
}
.ev-btn-add:hover { filter: brightness(1.08); background: var(--ev-accent); border-color: var(--ev-accent); }
.ev-btn-add:active { transform: translateY(1px) scale(0.985); }
.ev-btn-add:focus-visible { outline: 2px solid var(--ev-accent); outline-offset: 2px; }

/* ── Toast / message area ────────────────────────────────── */
#event-container #information { margin: 0; }
#event-container #information p {
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--ev-green);
  font-size: 0.78rem;
}

/* ── Loading skeleton ────────────────────────────────────── */
#loading-events { display: grid; gap: 0.5rem; margin-bottom: 0.5rem; }
#loading-events.mx-indicator-hidden { display: none; }
#loading-events .ev-skel {
  height: 44px;
  border-radius: 10px;
  background: linear-gradient(90deg, var(--ev-surface) 0%, var(--ev-surface-2) 50%, var(--ev-surface) 100%);
  background-size: 200% 100%;
  animation: ev-shimmer 1.4s linear infinite;
}

/* ── Table ───────────────────────────────────────────────── */
.ev-board {
  border: 1px solid var(--ev-line-2);
  border-radius: 12px;
  background: var(--ev-surface);
  overflow: auto;
}
.ev-table {
  width: 100%;
  min-width: 560px;
  border-collapse: separate;
  border-spacing: 0;
  font-family: var(--ev-font);
  font-size: 0.8rem;
  color: var(--ev-text);
  background: transparent;
}
.ev-table thead { background: transparent; }
.ev-table th, .ev-table td {
  border: 0;
  border-bottom: 1px solid var(--ev-line);
  background: transparent;
  padding: 0.6rem 0.85rem;
  text-align: left;
  vertical-align: middle;
  font-family: inherit;
  font-size: inherit;
}
.ev-table tbody tr:last-child td { border-bottom: 0; }
.ev-table thead th {
  padding: 0.55rem 0.85rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--ev-faint);
  background: var(--ev-surface-2);
  white-space: nowrap;
}
.ev-table thead th.ev-th-actions { text-align: right; }
.ev-table tbody tr {
  animation: ev-fade 0.4s var(--ev-ease) backwards;
  animation-delay: min(calc(var(--i) * 25ms), 350ms);
}
.ev-table tbody tr:hover td {
  background-image: linear-gradient(rgba(255,255,255,0.03), rgba(255,255,255,0.03));
}
.ev-row--past .ev-title, .ev-row--past .ev-when { opacity: 0.5; }
.ev-when {
  display: inline-flex;
  flex-direction: column;
  font-family: var(--ev-mono);
  font-feature-settings: 'tnum' 1;
  line-height: 1.3;
  white-space: nowrap;
}
.ev-when b { font-size: 0.8rem; font-weight: 700; }
.ev-when span { font-size: 0.7rem; color: var(--ev-muted); }
.ev-past-badge {
  display: inline-block;
  margin-left: 0.5rem;
  vertical-align: top;
  font-size: 0.56rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--ev-faint);
  border: 1px solid var(--ev-line-2);
  padding: 0.1rem 0.4rem;
  border-radius: 99px;
}
.ev-title { display: block; font-weight: 600; letter-spacing: -0.01em; }
.ev-sub { display: block; font-size: 0.7rem; color: var(--ev-muted); margin-top: 1px; max-width: 48ch; }
.ev-tel { color: var(--ev-text); text-decoration: none; border-bottom: 1px dotted var(--ev-faint); }
.ev-tel:hover { color: var(--ev-accent); border-bottom-color: var(--ev-accent); }
.ev-actions { display: flex; gap: 0.4rem; justify-content: flex-end; }
.ev-iconbtn {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  padding: 0;
  margin: 0;
  border: 1px solid var(--ev-line-2);
  border-radius: 8px;
  background: var(--ev-surface-2);
  color: var(--ev-muted);
  cursor: pointer;
  transition: background 0.12s var(--ev-ease), color 0.12s var(--ev-ease),
              border-color 0.12s var(--ev-ease), transform 0.12s var(--ev-ease);
}
.ev-iconbtn:hover { background: rgba(255,255,255,0.1); border-color: var(--ev-line-2); color: var(--ev-text); transform: translateY(-1px); }
.ev-iconbtn:active { transform: scale(0.94); }
.ev-iconbtn:focus-visible { outline: 2px solid var(--ev-accent); outline-offset: 1px; }
.ev-iconbtn--danger:hover { background: rgba(229,72,77,0.16); border-color: rgba(229,72,77,0.45); color: var(--ev-red); }

/* ── Empty state ─────────────────────────────────────────── */
.ev-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--ev-line-2);
  border-radius: 12px;
  color: var(--ev-muted);
}
.ev-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--ev-text); }
.ev-empty p { margin: 0; font-size: 0.8rem; }

/* ── Modals (form / delete) ──────────────────────────────── */
#event-modal.modal, #event-delete-modal.modal {
  background: var(--ev-surface);
  color: var(--ev-text);
  border: 1px solid var(--ev-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--ev-font);
}
#event-modal .modal-heading, #event-delete-modal .modal-heading {
  background: transparent;
  color: var(--ev-text);
  border: none;
  border-bottom: 1px solid var(--ev-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#event-modal .modal-body, #event-delete-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#event-modal form, #event-delete-modal form { display: block; width: auto; }

.ev-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.1rem; }
.ev-field { display: grid; gap: 0.35rem; text-align: left; align-content: start; }
.ev-field--full { grid-column: 1 / -1; }
.ev-field > label {
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ev-muted);
  margin: 0;
}
.ev-field input, .ev-field textarea {
  width: 100% !important;
  margin: 0 !important;
  padding: 0 0.7rem;
  height: 38px;
  border: 1px solid var(--ev-line-2);
  border-radius: 9px;
  background: var(--ev-bg);
  color: var(--ev-text);
  font-family: var(--ev-font);
  font-size: 0.85rem;
  text-align: left;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--ev-ease), box-shadow 0.15s var(--ev-ease);
}
.ev-field textarea { height: auto; min-height: 70px; padding: 0.55rem 0.7rem; resize: vertical; }
.ev-field input:focus, .ev-field textarea:focus {
  border-color: var(--ev-accent);
  box-shadow: 0 0 0 3px rgba(69,196,214,0.18);
}
#event-modal .validation-error-report { color: var(--ev-red); font-size: 0.72rem; }
.ev-modal-btns { display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center; flex-wrap: wrap; }
.ev-modal-btns button, .ev-modal-btns input[type="submit"] {
  width: auto;
  padding: 0.52rem 1.05rem;
  margin: 0 !important;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--ev-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  height: auto;
  transition: background 0.15s var(--ev-ease), color 0.15s var(--ev-ease),
              border-color 0.15s var(--ev-ease), transform 0.12s var(--ev-ease),
              filter 0.15s var(--ev-ease);
}
.ev-modal-btns button:active, .ev-modal-btns input[type="submit"]:active { transform: translateY(1px) scale(0.985); }
.ev-btn-cancel { background: transparent !important; border-color: var(--ev-line-2) !important; color: var(--ev-muted) !important; }
.ev-btn-cancel:hover { background: rgba(255,255,255,0.05) !important; color: var(--ev-text) !important; }
.ev-btn-save { background: var(--ev-accent) !important; border-color: var(--ev-accent) !important; color: #08252a !important; font-weight: 700; }
.ev-btn-save:hover { filter: brightness(1.08); }
.ev-btn-delete { background: var(--ev-red) !important; border-color: var(--ev-red) !important; color: #fff !important; font-weight: 700; }
.ev-btn-delete:hover { filter: brightness(1.1); }
.ev-confirm-title { font-size: 1rem; font-weight: 700; color: var(--ev-text); margin: 0 0 0.4rem; text-align: left; }
.ev-confirm-text { font-size: 0.85rem; color: var(--ev-muted); margin: 0 0 1rem; text-align: left; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 56.25rem) {
  .ev-stats { grid-template-columns: repeat(3, 1fr); }
  .ev-stat--lead { grid-column: 1 / -1; }
}
@media (max-width: 33.75rem) {
  .ev-stats { grid-template-columns: repeat(2, 1fr); }
  .ev-shell { padding: 1rem; border-radius: 12px; }
  .ev-form-grid { grid-template-columns: 1fr; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="ev-shell">

    <div class="ev-head">
        <div>
            <p class="ev-eyebrow">Molas Surf Club &middot; Planned events</p>
            <h2 class="ev-title-h">Events</h2>
        </div>
        <button type="button" class="ev-btn-add"
                mx-get="events/event_form" mx-select="#event-form"
                mx-build-modal='{"id": "event-modal","modalHeading": "Create Event Schedule"}'>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            New event
        </button>
    </div>

    <div id="information"></div>

    <div id="loading-events" class="mx-indicator">
        <div class="ev-skel"></div>
        <div class="ev-skel"></div>
        <div class="ev-skel"></div>
    </div>

    <div id="events-container"
         mx-get="events/fetch_table"
         mx-trigger="load"
         mx-indicator="#loading-events"
         mx-select="#ev-payload"
         mx-select-oob='[{"select":"#stat-panel","target":"#stat-panel","swap":"innerHTML"}]'>
        <?= ev_render_board($rows) ?>
    </div>

</div><!-- /.ev-shell -->
</div><!-- /#event-container -->
