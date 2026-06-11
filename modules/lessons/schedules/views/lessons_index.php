<?php
/**
 * Lessons admin — schedule panel (Trongate MX powered).
 *
 * Follows the admin mx-oob nav convention: exposes three sibling regions so
 * the sidebar link can distribute them —
 *   #title            → #top-title  (header bar)
 *   #stat-panel       → #stat-cards (dashboard chrome)
 *   #lesson-container → #form-container (main content)
 *
 * #lessons-container loads lessons-schedules/fetch_lessons on page load and
 * after every add/delete (mx-on-success re-trigger). The fetch response
 * carries a fresh #stat-panel which is OOB-swapped in, keeping stats live.
 */
require_once APPPATH . 'modules/lessons/schedules/views/_ls_helpers.php';
?>

<div id="title" style="display:none"><h1>Lessons</h1></div>

<!-- ===== OVERVIEW (hoisted into #stat-cards by the sidebar) ===== -->
<?= ls_render_stat_panel($rows) ?>

<div id="lesson-container">

<style>
/* ============================================================
   LESSONS ADMIN — charcoal base, single cyan accent
   (same design language as staff calendar / camps / coupons)
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --ls-bg:        #15141a;
  --ls-surface:   #1c1b23;
  --ls-surface-2: #211f2a;
  --ls-line:      rgba(255,255,255,0.06);
  --ls-line-2:    rgba(255,255,255,0.11);
  --ls-text:      #f4f3f8;
  --ls-muted:     #a09db1;
  --ls-faint:     #6c6979;

  --ls-accent:    #45c4d6;
  --ls-green:     #2ec27e;
  --ls-amber:     #f2a33c;
  --ls-red:       #e5484d;

  --ls-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --ls-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --ls-ease: cubic-bezier(0.16, 1, 0.3, 1);
}

#stat-cards { display: block !important; }

@keyframes ls-fade    { from { opacity: 0; } }
@keyframes ls-grow    { from { transform: scaleX(0); } }
@keyframes ls-shimmer { from { background-position: 200% 0; } to { background-position: -200% 0; } }

/* ── Stat strip ──────────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--ls-font); color: var(--ls-text); }
.ls-stats {
  display: grid;
  grid-template-columns: 1.5fr repeat(4, 1fr);
  gap: 1px;
  background: var(--ls-line-2);
  border: 1px solid var(--ls-line-2);
  border-radius: 14px;
  overflow: hidden;
  font-family: var(--ls-font);
  color: var(--ls-text);
  animation: ls-fade 0.4s var(--ls-ease) both;
}
.ls-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--ls-surface);
}
.ls-stat__num {
  font-family: var(--ls-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
}
.ls-stat__num small { font-size: 0.6em; font-weight: 600; color: var(--ls-muted); margin-left: 1px; }
.ls-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ls-muted);
}
.ls-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--ls-surface); }
.ls-stat--lead .ls-stat__num { color: var(--ls-accent); }
.ls-stat--today .ls-stat__num { color: var(--ls-amber); }
.ls-stat--free  .ls-stat__num { color: var(--ls-green); }
.ls-stat--res   .ls-stat__num { color: var(--ls-text); }
.ls-stat__bar {
  height: 3px;
  margin-top: 0.4rem;
  border-radius: 99px;
  background: rgba(255,255,255,0.08);
  overflow: hidden;
}
.ls-stat__bar i {
  display: block;
  height: 100%;
  background: var(--ls-accent);
  border-radius: inherit;
  transform-origin: left;
  animation: ls-grow 0.8s var(--ls-ease) both;
}

/* ── Shell ───────────────────────────────────────────────── */
.ls-shell {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--ls-bg);
  border: 1px solid var(--ls-line);
  border-radius: 16px;
  color: var(--ls-text);
  font-family: var(--ls-font);
}
.ls-head {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 1rem;
  margin-bottom: 1.1rem;
}
.ls-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--ls-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--ls-accent);
}
.ls-title {
  margin: 0;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--ls-text);
  text-transform: none;
}
.ls-tools { display: flex; gap: 0.6rem; flex-wrap: wrap; }
.ls-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.55rem 1.05rem;
  border-radius: 9px;
  font-family: var(--ls-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  margin: 0;
  transition: filter 0.15s var(--ls-ease), background 0.15s var(--ls-ease),
              color 0.15s var(--ls-ease), transform 0.12s var(--ls-ease);
}
.ls-btn:active { transform: translateY(1px) scale(0.985); }
.ls-btn:focus-visible { outline: 2px solid var(--ls-accent); outline-offset: 2px; }
.ls-btn--accent { background: var(--ls-accent); border: 1px solid var(--ls-accent); color: #08252a; font-weight: 700; }
.ls-btn--accent:hover { filter: brightness(1.08); background: var(--ls-accent); border-color: var(--ls-accent); }
.ls-btn--ghost { background: transparent; border: 1px solid var(--ls-line-2); color: var(--ls-muted); }
.ls-btn--ghost:hover { background: rgba(255,255,255,0.05); border-color: var(--ls-line-2); color: var(--ls-text); }

/* ── Toast / message area ────────────────────────────────── */
#lesson-container #information { margin: 0; padding: 0; background: transparent; }
#lesson-container #information p {
  margin: 0 0 0.9rem;
  padding: 0.55rem 0.8rem;
  background: rgba(46,194,126,0.12);
  border: 1px solid rgba(46,194,126,0.35);
  border-radius: 8px;
  color: var(--ls-green);
  font-size: 0.78rem;
}

/* ── Loading skeleton ────────────────────────────────────── */
#loading-lessons { display: grid; gap: 0.5rem; margin-bottom: 0.5rem; }
#loading-lessons.mx-indicator-hidden { display: none; }
#loading-lessons .ls-skel {
  height: 44px;
  border-radius: 10px;
  background: linear-gradient(90deg, var(--ls-surface) 0%, var(--ls-surface-2) 50%, var(--ls-surface) 100%);
  background-size: 200% 100%;
  animation: ls-shimmer 1.4s linear infinite;
}

/* ── Schedule table ──────────────────────────────────────── */
.ls-board {
  border: 1px solid var(--ls-line-2);
  border-radius: 12px;
  background: var(--ls-surface);
  overflow: auto;
}
.ls-table {
  width: 100%;
  min-width: 600px;
  border-collapse: separate;
  border-spacing: 0;
  font-family: var(--ls-font);
  font-size: 0.8rem;
  color: var(--ls-text);
  background: transparent;
}
.ls-table thead { background: transparent; }
.ls-table th, .ls-table td {
  border: 0;
  border-bottom: 1px solid var(--ls-line);
  background: transparent;
  padding: 0.55rem 0.85rem;
  text-align: left;
  vertical-align: middle;
  font-family: inherit;
  font-size: inherit;
}
.ls-table tbody tr:last-child td { border-bottom: 0; }
.ls-table thead th {
  padding: 0.55rem 0.85rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--ls-faint);
  background: var(--ls-surface-2);
  white-space: nowrap;
}
.ls-table thead th.ls-th-actions { text-align: right; }
.ls-table tbody tr {
  animation: ls-fade 0.4s var(--ls-ease) backwards;
  animation-delay: min(calc(var(--i) * 20ms), 320ms);
}
.ls-table tbody tr:hover td {
  background-image: linear-gradient(rgba(255,255,255,0.03), rgba(255,255,255,0.03));
}
.ls-table tbody tr.ls-row--newday td { border-top: 1px solid var(--ls-line-2); }
.ls-name { font-weight: 600; letter-spacing: -0.01em; }
.ls-date {
  font-family: var(--ls-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.74rem;
  color: var(--ls-muted);
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  white-space: nowrap;
}
.ls-date--today { color: var(--ls-text); }
.ls-date--today b {
  font-size: 0.58rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: #08252a;
  background: var(--ls-accent);
  padding: 0.1rem 0.4rem;
  border-radius: 99px;
}
.ls-time {
  font-family: var(--ls-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.78rem;
  font-weight: 600;
}
.ls-free {
  font-family: var(--ls-mono);
  font-feature-settings: 'tnum' 1;
  font-weight: 600;
  color: var(--ls-green);
}
.ls-full {
  font-size: 0.66rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  color: var(--ls-red);
}
.ls-res {
  display: inline-grid;
  place-items: center;
  min-width: 26px;
  height: 26px;
  padding: 0 6px;
  border-radius: 8px;
  background: rgba(255,255,255,0.05);
  border: 1px solid var(--ls-line-2);
  color: var(--ls-muted);
  font-family: var(--ls-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.76rem;
  font-weight: 700;
  text-decoration: none;
  cursor: pointer;
  transition: background 0.12s var(--ls-ease), color 0.12s var(--ls-ease),
              border-color 0.12s var(--ls-ease), transform 0.12s var(--ls-ease);
}
.ls-res:hover { transform: translateY(-1px); color: var(--ls-text); border-color: rgba(255,255,255,0.24); }
.ls-res--has { background: rgba(46,194,126,0.14); border-color: rgba(46,194,126,0.4); color: var(--ls-green); }
.ls-res--has:hover { background: rgba(46,194,126,0.24); color: var(--ls-green); }
.ls-actions { display: flex; gap: 0.4rem; justify-content: flex-end; }
.ls-iconbtn {
  display: grid;
  place-items: center;
  width: 28px;
  height: 28px;
  padding: 0;
  margin: 0;
  border: 1px solid var(--ls-line-2);
  border-radius: 8px;
  background: var(--ls-surface-2);
  color: var(--ls-muted);
  cursor: pointer;
  transition: background 0.12s var(--ls-ease), color 0.12s var(--ls-ease),
              border-color 0.12s var(--ls-ease), transform 0.12s var(--ls-ease);
}
.ls-iconbtn:hover { background: rgba(255,255,255,0.1); border-color: var(--ls-line-2); color: var(--ls-text); transform: translateY(-1px); }
.ls-iconbtn:active { transform: scale(0.94); }
.ls-iconbtn:focus-visible { outline: 2px solid var(--ls-accent); outline-offset: 1px; }
.ls-iconbtn--danger:hover { background: rgba(229,72,77,0.16); border-color: rgba(229,72,77,0.45); color: var(--ls-red); }

/* ── Empty state ─────────────────────────────────────────── */
.ls-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--ls-line-2);
  border-radius: 12px;
  color: var(--ls-muted);
}
.ls-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--ls-text); }
.ls-empty p { margin: 0; font-size: 0.8rem; }

/* ── Modals (lesson / bulk / registrations) ──────────────── */
#lesson-modal.modal, #bulk-lesson-modal.modal, #registration-modal.modal {
  background: var(--ls-surface);
  color: var(--ls-text);
  border: 1px solid var(--ls-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--ls-font);
}
#lesson-modal .modal-heading, #bulk-lesson-modal .modal-heading, #registration-modal .modal-heading {
  background: transparent;
  color: var(--ls-text);
  border: none;
  border-bottom: 1px solid var(--ls-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#lesson-modal .modal-body, #bulk-lesson-modal .modal-body, #registration-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#lesson-modal form, #bulk-lesson-modal form { display: block; width: auto; }

.ls-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 1.1rem; }
.ls-field { display: grid; gap: 0.35rem; text-align: left; align-content: start; }
.ls-field--full { grid-column: 1 / -1; }
.ls-field > label {
  font-size: 0.66rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--ls-muted);
  margin: 0;
}
.ls-field input, .ls-field select {
  width: 100% !important;
  height: 38px;
  margin: 0 !important;
  padding: 0 0.7rem;
  border: 1px solid var(--ls-line-2);
  border-radius: 9px;
  background: var(--ls-bg);
  color: var(--ls-text);
  font-family: var(--ls-font);
  font-size: 0.85rem;
  text-align: left;
  box-sizing: border-box;
  box-shadow: none;
  outline: none;
  transition: border-color 0.15s var(--ls-ease), box-shadow 0.15s var(--ls-ease);
}
.ls-field input:focus, .ls-field select:focus {
  border-color: var(--ls-accent);
  box-shadow: 0 0 0 3px rgba(69,196,214,0.18);
}
.ls-field select option { background: var(--ls-surface); color: var(--ls-text); }

/* Day-of-week chips (bulk form) */
.ls-days { display: flex; gap: 0.4rem; flex-wrap: wrap; }
#bulk-lesson-modal form .ls-days label {
  display: block;
  padding: 0;
  margin: 0;
  border: none;
  background: transparent;
  cursor: pointer;
  font-family: var(--ls-font);
}
.ls-days input { position: absolute; opacity: 0; pointer-events: none; }
.ls-days span {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 44px;
  padding: 0.4rem 0.65rem;
  border: 1px solid var(--ls-line-2);
  border-radius: 99px;
  background: var(--ls-surface-2);
  color: var(--ls-muted);
  font-size: 0.74rem;
  font-weight: 600;
  transition: background 0.12s var(--ls-ease), color 0.12s var(--ls-ease),
              border-color 0.12s var(--ls-ease);
}
.ls-days label:hover span { color: var(--ls-text); border-color: rgba(255,255,255,0.24); }
.ls-days input:checked + span { background: var(--ls-accent); border-color: var(--ls-accent); color: #08252a; }
.ls-days input:focus-visible + span { outline: 2px solid var(--ls-accent); outline-offset: 2px; }

.ls-modal-btns { display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center; flex-wrap: wrap; }
.ls-modal-btns button, .ls-modal-btns input[type="submit"] {
  width: auto;
  padding: 0.52rem 1.05rem;
  margin: 0 !important;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--ls-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  height: auto;
  transition: background 0.15s var(--ls-ease), color 0.15s var(--ls-ease),
              border-color 0.15s var(--ls-ease), transform 0.12s var(--ls-ease),
              filter 0.15s var(--ls-ease);
}
.ls-modal-btns button:active, .ls-modal-btns input[type="submit"]:active { transform: translateY(1px) scale(0.985); }
.ls-btn-cancel { background: transparent !important; border-color: var(--ls-line-2) !important; color: var(--ls-muted) !important; }
.ls-btn-cancel:hover { background: rgba(255,255,255,0.05) !important; color: var(--ls-text) !important; }
.ls-btn-save { background: var(--ls-accent) !important; border-color: var(--ls-accent) !important; color: #08252a !important; font-weight: 700; }
.ls-btn-save:hover { filter: brightness(1.08); }

/* Registrations modal table */
.ls-reg-table { width: 100%; border-collapse: separate; border-spacing: 0; font-size: 0.8rem; }
.ls-reg-table th, .ls-reg-table td {
  border: 0;
  border-bottom: 1px solid var(--ls-line);
  background: transparent;
  padding: 0.5rem 0.6rem;
  text-align: left;
  font-family: var(--ls-font);
}
.ls-reg-table thead { background: transparent; }
.ls-reg-table thead th {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--ls-faint);
}
.ls-reg-table tbody tr:last-child td { border-bottom: 0; }
.ls-reg-num { font-family: var(--ls-mono); font-size: 0.7rem; color: var(--ls-faint); }
.ls-reg-sub { display: block; font-size: 0.68rem; color: var(--ls-muted); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 56.25rem) {
  .ls-stats { grid-template-columns: repeat(2, 1fr); }
  .ls-stat--lead { grid-column: 1 / -1; }
}
@media (max-width: 33.75rem) {
  .ls-shell { padding: 1rem; border-radius: 12px; }
  .ls-form-grid { grid-template-columns: 1fr; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<div class="ls-shell">

    <div class="ls-head">
        <div>
            <p class="ls-eyebrow">Molas Surf Club &middot; Lessons</p>
            <h2 class="ls-title">Lesson Schedule</h2>
        </div>
        <div class="ls-tools">
            <button type="button" class="ls-btn ls-btn--ghost"
                    mx-get="lessons-schedules/bulk_lesson_form"
                    mx-build-modal='{"id": "bulk-lesson-modal","modalHeading": "Add Lessons for Multiple Days"}'>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                Bulk add
            </button>
            <button type="button" class="ls-btn ls-btn--accent"
                    mx-get="lessons-schedules/lesson_form"
                    mx-build-modal='{"id": "lesson-modal","modalHeading": "Create New Lesson Schedule"}'>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                New lesson
            </button>
        </div>
    </div>

    <div id="information"></div>

    <div id="loading-lessons" class="mx-indicator">
        <div class="ls-skel"></div>
        <div class="ls-skel"></div>
        <div class="ls-skel"></div>
    </div>

    <div id="lessons-container"
         mx-get="lessons-schedules/fetch_lessons"
         mx-trigger="load"
         mx-indicator="#loading-lessons"
         mx-select="#ls-payload"
         mx-select-oob='[{"select":"#stat-panel","target":"#stat-panel","swap":"innerHTML"}]'>
        <?= ls_render_board($rows) ?>
    </div>

</div><!-- /.ls-shell -->
</div><!-- /#lesson-container -->
