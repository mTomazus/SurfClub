<?php
/**
 * Coupons admin — main panel (Trongate MX powered).
 *
 * Follows the admin mx-oob nav convention: exposes three sibling regions so
 * the sidebar link can distribute them —
 *   #title      → #top-title  (header bar)
 *   #stat-panel → #stat-cards (dashboard chrome)
 *   #coupons    → #form-container (main content)
 *
 * #coupons-container loads coupons/fetch_table on page load and after every
 * add/delete (mx-on-success re-trigger). The fetch response carries a fresh
 * #stat-panel which is OOB-swapped in, so the stats stay live.
 */
require_once APPPATH . 'modules/coupons/views/_cu_helpers.php';
?>

<div id="title" style="display:none"><h1>Coupons</h1></div>

<!-- ===== OVERVIEW (hoisted into #stat-cards by the sidebar) ===== -->
<?= cu_render_stat_panel($rows) ?>

<div id="coupons">

<style>
/* ============================================================
   COUPONS ADMIN — charcoal base, single cyan accent
   (same design language as staff calendar / camps)
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --cu-bg:        #15141a;
  --cu-surface:   #1c1b23;
  --cu-surface-2: #211f2a;
  --cu-line:      rgba(255,255,255,0.06);
  --cu-line-2:    rgba(255,255,255,0.11);
  --cu-text:      #f4f3f8;
  --cu-muted:     #a09db1;
  --cu-faint:     #6c6979;

  --cu-accent:    #45c4d6;
  --cu-green:     #2ec27e;
  --cu-amber:     #f2a33c;
  --cu-red:       #e5484d;

  --cu-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --cu-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --cu-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

#stat-cards { display: block !important; }

@keyframes cu-fade    { from { opacity: 0; } }
@keyframes cu-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }

/* ── Stat strip ──────────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--cu-font); color: var(--cu-text); }
.cu-stats {
  display: grid;
  grid-template-columns: 1.5fr repeat(3, 1fr);
  gap: 1px;
  background: var(--cu-line-2);
  border: 1px solid var(--cu-line-2);
  border-radius: 14px;
  overflow: hidden;
  font-family: var(--cu-font);
  color: var(--cu-text);
  animation: cu-fade 0.4s var(--cu-ease) both;
}
.cu-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--cu-surface);
}
.cu-stat__num {
  font-family: var(--cu-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
}
.cu-stat__num small { font-size: 0.6em; font-weight: 600; color: var(--cu-muted); margin-left: 2px; }
.cu-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--cu-muted);
}
.cu-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--cu-surface); }
.cu-stat--lead .cu-stat__num { color: var(--cu-accent); }
.cu-stat--active .cu-stat__num { color: var(--cu-green); }
.cu-stat--other  .cu-stat__num { color: var(--cu-amber); }

/* ── Shell ───────────────────────────────────────────────── */
.cu-shell {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--cu-bg);
  border: 1px solid var(--cu-line);
  border-radius: 16px;
  color: var(--cu-text);
  font-family: var(--cu-font);
}
.cu-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.cu-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--cu-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--cu-accent);
}
.cu-title {
  margin: 0;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--cu-text);
  text-transform: none;
}
.cu-btn-add {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.05rem;
  border: 1px solid var(--cu-accent);
  border-radius: 9px;
  background: var(--cu-accent);
  color: #08252a;
  font-family: var(--cu-font);
  font-size: 0.82rem;
  font-weight: 700;
  cursor: pointer;
  margin: 0;
  transition: filter 0.15s var(--cu-ease), transform 0.12s var(--cu-ease);
}
.cu-btn-add:hover { filter: brightness(1.08); background: var(--cu-accent); border-color: var(--cu-accent); }
.cu-btn-add:active { transform: translateY(1px) scale(0.985); }
.cu-btn-add:focus-visible { outline: 2px solid var(--cu-accent); outline-offset: 2px; }

/* ── Toast / message area ────────────────────────────────── */
#coupons #information { margin: 0; }
#coupons #information p {
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--cu-green);
  font-size: 0.78rem;
}

/* ── Loading skeleton ────────────────────────────────────── */
#loading-coupons { display: grid; gap: 0.5rem; margin-bottom: 0.5rem; }
#loading-coupons.mx-indicator-hidden { display: none; }
#loading-coupons .cu-skel {
  height: 44px;
  border-radius: 10px;
  background: linear-gradient(90deg, var(--cu-surface) 0%, var(--cu-surface-2) 50%, var(--cu-surface) 100%);
  background-size: 200% 100%;
  animation: cu-shimmer 1.4s linear infinite;
}

/* ── Table ───────────────────────────────────────────────── */
.cu-board {
  border: 1px solid var(--cu-line-2);
  border-radius: 12px;
  background: var(--cu-surface);
  overflow: auto;
}
.cu-table {
  width: 100%;
  min-width: 560px;
  border-collapse: separate;
  border-spacing: 0;
  font-family: var(--cu-font);
  font-size: 0.8rem;
  color: var(--cu-text);
  background: transparent;
}
.cu-table thead { background: transparent; }
.cu-table th, .cu-table td {
  border: 0;
  border-bottom: 1px solid var(--cu-line);
  background: transparent;
  padding: 0.6rem 0.85rem;
  text-align: left;
  vertical-align: middle;
  font-family: inherit;
  font-size: inherit;
}
.cu-table tbody tr:last-child td { border-bottom: 0; }
.cu-table thead th {
  padding: 0.55rem 0.85rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--cu-faint);
  background: var(--cu-surface-2);
  white-space: nowrap;
}
.cu-table thead th.cu-th-actions { text-align: right; }
.cu-table tbody tr {
  animation: cu-fade 0.4s var(--cu-ease) backwards;
  animation-delay: min(calc(var(--i) * 25ms), 350ms);
}
.cu-table tbody tr:hover td {
  background-image: linear-gradient(rgba(255,255,255,0.03), rgba(255,255,255,0.03));
}
.cu-code {
  font-family: var(--cu-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.74rem;
  font-weight: 600;
  color: var(--cu-text);
}
.cu-sub { display: block; font-size: 0.66rem; color: var(--cu-muted); margin-top: 1px; }
.cu-name { font-weight: 600; letter-spacing: -0.01em; }
.cu-price {
  font-family: var(--cu-mono);
  font-feature-settings: 'tnum' 1;
  font-weight: 600;
  white-space: nowrap;
}
.cu-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.22rem 0.6rem;
  border-radius: 99px;
  font-size: 0.68rem;
  font-weight: 600;
}
.cu-badge i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.cu-badge--active { background: rgba(46,194,126,0.12); border: 1px solid rgba(46,194,126,0.35); color: var(--cu-green); }
.cu-badge--other  { background: rgba(255,255,255,0.05); border: 1px solid var(--cu-line-2); color: var(--cu-muted); }
.cu-actions { display: flex; gap: 0.4rem; justify-content: flex-end; }
.cu-iconbtn {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  padding: 0;
  margin: 0;
  border: 1px solid var(--cu-line-2);
  border-radius: 8px;
  background: var(--cu-surface-2);
  color: var(--cu-muted);
  cursor: pointer;
  transition: background 0.12s var(--cu-ease), color 0.12s var(--cu-ease),
              border-color 0.12s var(--cu-ease), transform 0.12s var(--cu-ease);
}
.cu-iconbtn:hover { background: rgba(255,255,255,0.1); border-color: var(--cu-line-2); color: var(--cu-text); transform: translateY(-1px); }
.cu-iconbtn:active { transform: scale(0.94); }
.cu-iconbtn:focus-visible { outline: 2px solid var(--cu-accent); outline-offset: 1px; }
.cu-iconbtn--danger:hover { background: rgba(229,72,77,0.16); border-color: rgba(229,72,77,0.45); color: var(--cu-red); }

/* ── Empty state ─────────────────────────────────────────── */
.cu-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--cu-line-2);
  border-radius: 12px;
  color: var(--cu-muted);
}
.cu-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--cu-text); }
.cu-empty p { margin: 0; font-size: 0.8rem; }

/* ── Modals (create / edit / delete) ─────────────────────── */
#coupon-modal.modal, #event-modal.modal, #delete-modal.modal {
  background: var(--cu-surface);
  color: var(--cu-text);
  border: 1px solid var(--cu-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--cu-font);
}
#coupon-modal .modal-heading, #event-modal .modal-heading, #delete-modal .modal-heading {
  background: transparent;
  color: var(--cu-text);
  border: none;
  border-bottom: 1px solid var(--cu-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#coupon-modal .modal-body, #event-modal .modal-body, #delete-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#coupon-modal form, #event-modal form, #delete-modal form { display: block; width: auto; }

.cu-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.1rem; }
.cu-field { display: grid; gap: 0.35rem; text-align: left; }
.cu-field--full { grid-column: 1 / -1; }
.cu-field > label {
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--cu-muted);
}
.cu-field input {
  width: 100%;
  height: 38px;
  margin: 0 !important;
  padding: 0 0.7rem;
  border: 1px solid var(--cu-line-2);
  border-radius: 9px;
  background: var(--cu-bg);
  color: var(--cu-text);
  font-family: var(--cu-font);
  font-size: 0.85rem;
  text-align: left;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--cu-ease), box-shadow 0.15s var(--cu-ease);
}
.cu-field input:focus {
  border-color: var(--cu-accent);
  box-shadow: 0 0 0 3px rgba(69,196,214,0.18);
}
.cu-field .validation-error-report,
#coupon-modal .validation-error-report, #event-modal .validation-error-report {
  color: var(--cu-red);
  font-size: 0.72rem;
}
.cu-modal-btns { display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center; flex-wrap: wrap; }
.cu-modal-btns button, .cu-modal-btns input[type="submit"] {
  width: auto;
  padding: 0.52rem 1.05rem;
  margin: 0 !important;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--cu-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s var(--cu-ease), color 0.15s var(--cu-ease),
              border-color 0.15s var(--cu-ease), transform 0.12s var(--cu-ease),
              filter 0.15s var(--cu-ease);
}
.cu-modal-btns button:active, .cu-modal-btns input[type="submit"]:active { transform: translateY(1px) scale(0.985); }
.cu-btn-cancel { background: transparent !important; border-color: var(--cu-line-2) !important; color: var(--cu-muted) !important; }
.cu-btn-cancel:hover { background: rgba(255,255,255,0.05) !important; color: var(--cu-text) !important; }
.cu-btn-save { background: var(--cu-accent) !important; border-color: var(--cu-accent) !important; color: #08252a !important; font-weight: 700; height: auto; }
.cu-btn-save:hover { filter: brightness(1.08); }
.cu-btn-delete { background: var(--cu-red) !important; border-color: var(--cu-red) !important; color: #fff !important; font-weight: 700; height: auto; }
.cu-btn-delete:hover { filter: brightness(1.1); }
.cu-confirm-text { font-size: 0.85rem; color: var(--cu-muted); margin: 0 0 1rem; text-align: left; }
.cu-confirm-title { font-size: 1rem; font-weight: 700; color: var(--cu-text); margin: 0 0 0.4rem; text-align: left; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 56.25rem) {
  .cu-stats { grid-template-columns: repeat(2, 1fr); }
  .cu-stat--lead { grid-column: 1 / -1; }
}
@media (max-width: 33.75rem) {
  .cu-shell { padding: 1rem; border-radius: 12px; }
  .cu-form-grid { grid-template-columns: 1fr; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="cu-shell">

    <div class="cu-head">
        <div>
            <p class="cu-eyebrow">Molas Surf Club &middot; Gift coupons</p>
            <h2 class="cu-title">Coupons</h2>
        </div>
        <button type="button" id="form" class="cu-btn-add"
                mx-get="coupons/coupon_form"
                mx-build-modal='{"id": "coupon-modal","modalHeading": "Create New Coupon"}'>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            New coupon
        </button>
    </div>

    <div id="information"></div>

    <div id="loading-coupons" class="mx-indicator">
        <div class="cu-skel"></div>
        <div class="cu-skel"></div>
        <div class="cu-skel"></div>
    </div>

    <div id="coupons-container"
         mx-get="coupons/fetch_table"
         mx-trigger="load"
         mx-indicator="#loading-coupons"
         mx-select="#cu-payload"
         mx-select-oob='[{"select":"#stat-panel","target":"#stat-panel","swap":"innerHTML"}]'>
        <?= cu_render_board($rows) ?>
    </div>

</div><!-- /.cu-shell -->
</div><!-- /#coupons -->
