<?php
/**
 * Camps admin — registrations overview (Trongate MX powered).
 *
 * Follows the admin mx-oob nav convention: exposes three sibling regions so the
 * sidebar link can distribute them —
 *   #title      → #top-title  (header bar)
 *   #stat-panel → #stat-cards (dashboard chrome)
 *   #nav-table  → #form-container (main content)
 *
 * The shift filter pills live INSIDE .table-responsive (the in-page MX swap
 * region) so their active state, the email bar and the table always swap as
 * one consistent unit. The .table-responsive wrapper is rendered even when
 * there are no registrations, so the swap target never disappears.
 */
$current_shift = (string) segment(3);
$today         = date('Y-m-d');

$pamainos = [
    '' => 'Visos Pamainos', '2' => '2 Pamaina',
    '4' => '4 Pamaina',
    '5' => '5 Pamaina', '6' => '6 Pamaina', '7' => '7 Pamaina', '8' => '8 Pamaina',
    '9' => '9 Pamaina', '10' => '10 Pamaina', '11' => '11 Pamaina', '12' => '12 Pamaina'
];
?>

<div id="title" style="display:none"><h1>Summer Camp Registrations</h1></div>

<!-- ===== OVERVIEW (hoisted into #stat-cards by the sidebar's mx-select-oob;
     the <style> rides along inside the panel so it survives the hoist) ===== -->
<div id="stat-panel">
<style>
/* ============================================================
   CAMPS ADMIN — charcoal base, single cyan accent
   (same design language as the staff calendar)
   ============================================================ */
@import url('https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&family=Geist+Mono:wght@500;600;700&display=swap');

:root {
  --cp-bg:        #15141a;
  --cp-surface:   #1c1b23;
  --cp-surface-2: #211f2a;
  --cp-line:      rgba(255,255,255,0.06);
  --cp-line-2:    rgba(255,255,255,0.11);
  --cp-text:      #f4f3f8;
  --cp-muted:     #a09db1;
  --cp-faint:     #6c6979;

  --cp-accent:    #45c4d6;
  --cp-paid:      #2ec27e;
  --cp-wait:      #f2a33c;
  --cp-full:      #e5484d;

  --cp-font: 'Geist', 'Segoe UI', system-ui, -apple-system, sans-serif;
  --cp-mono: 'Geist Mono', 'SF Mono', ui-monospace, Menlo, monospace;

  --cp-ease:   cubic-bezier(0.16, 1, 0.3, 1);
  --cp-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* When hoisted into the dashboard chrome, #stat-cards is a card grid —
   force it back to block so the strip + shift grid span full width. */
#stat-cards { display: block !important; }

@keyframes cp-fade  { from { opacity: 0; } }
@keyframes cp-grow  { from { transform: scaleX(0); } }
@keyframes cp-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.35; } }
@keyframes cp-spin  { to { transform: rotate(360deg); } }

/* ── Summary strip ───────────────────────────────────────── */
#stat-panel { margin: 0.9rem 0.9rem 1.1rem; font-family: var(--cp-font); color: var(--cp-text); }
.cp-stats {
  display: grid;
  grid-template-columns: 1.5fr repeat(3, 1fr);
  gap: 1px;
  background: var(--cp-line-2);
  border: 1px solid var(--cp-line-2);
  border-radius: 14px;
  overflow: hidden;
  margin-bottom: 0.75rem;
  animation: cp-fade 0.4s var(--cp-ease) both;
}
.cp-stat {
  display: flex;
  flex-direction: column;
  justify-content: center;
  gap: 0.3rem;
  padding: 0.95rem 1.15rem;
  background: var(--cp-surface);
}
.cp-stat__num {
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
}
.cp-stat__num small { font-size: 0.6em; font-weight: 600; color: var(--cp-muted); margin-left: 1px; }
.cp-stat__label {
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--cp-muted);
}
.cp-stat--lead { background: linear-gradient(135deg, rgba(69,196,214,0.09), rgba(69,196,214,0.02) 65%), var(--cp-surface); }
.cp-stat--lead .cp-stat__num { color: var(--cp-accent); }
.cp-stat--paid .cp-stat__num { color: var(--cp-paid); }
.cp-stat--wait .cp-stat__num { color: var(--cp-wait); }
.cp-stat__bar {
  height: 3px;
  margin-top: 0.4rem;
  border-radius: 99px;
  background: rgba(255,255,255,0.08);
  overflow: hidden;
}
.cp-stat__bar i {
  display: block;
  height: 100%;
  background: var(--cp-accent);
  border-radius: inherit;
  transform-origin: left;
  animation: cp-grow 0.8s var(--cp-ease) both;
}

/* ── Shift occupancy grid ────────────────────────────────── */
.cp-shifts {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 0.5rem;
}
.cp-shift {
  padding: 0.65rem 0.75rem;
  background: var(--cp-surface);
  border: 1px solid var(--cp-line-2);
  border-radius: 10px;
  animation: cp-fade 0.4s var(--cp-ease) backwards;
  animation-delay: min(calc(var(--i) * 30ms), 360ms);
}
.cp-shift--live { border-color: rgba(69,196,214,0.45); }
.cp-shift--full { border-color: rgba(229,72,77,0.4); }
.cp-shift__head { display: flex; align-items: baseline; gap: 0.45rem; margin-bottom: 0.45rem; }
.cp-shift__num {
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--cp-text);
}
.cp-shift__dates {
  font-family: var(--cp-mono);
  font-size: 0.6rem;
  font-weight: 500;
  color: var(--cp-faint);
  letter-spacing: 0.02em;
}
.cp-shift__live-dot {
  width: 7px;
  height: 7px;
  border-radius: 50%;
  background: var(--cp-accent);
  margin-left: auto;
  align-self: center;
  animation: cp-pulse 2.2s ease-in-out infinite;
}
.cp-shift__bar {
  height: 4px;
  border-radius: 99px;
  background: rgba(255,255,255,0.08);
  overflow: hidden;
  margin-bottom: 0.45rem;
}
.cp-shift__bar i {
  display: block;
  height: 100%;
  background: var(--cp-paid);
  border-radius: inherit;
  transform-origin: left;
  animation: cp-grow 0.8s var(--cp-ease) both;
}
.cp-shift--full .cp-shift__bar i { background: var(--cp-full); }
.cp-shift__foot {
  display: flex;
  justify-content: space-between;
  gap: 0.4rem;
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.62rem;
  font-weight: 500;
  color: var(--cp-muted);
}
.cp-shift__paid { color: var(--cp-paid); }
.cp-shift__free { color: var(--cp-accent); }
.cp-shift__full-label { color: var(--cp-full); font-weight: 700; }

/* ── Main content ────────────────────────────────────────── */
#nav-table {
  margin: 0 0.9rem 1.5rem;
  padding: clamp(1.25rem, 2vw, 1.85rem);
  background: var(--cp-bg);
  border: 1px solid var(--cp-line);
  border-radius: 16px;
  color: var(--cp-text);
  font-family: var(--cp-font);
}
.cp-head { margin-bottom: 1.1rem; }
.cp-eyebrow {
  margin: 0 0 0.45rem;
  font-family: var(--cp-mono);
  font-size: 0.62rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.16em;
  color: var(--cp-accent);
}
.cp-title {
  margin: 0;
  font-size: clamp(1.4rem, 2.5vw, 1.8rem);
  font-weight: 700;
  line-height: 1;
  letter-spacing: -0.03em;
  color: var(--cp-text);
  text-transform: none;
}

#nav-table .table-responsive { margin: 0; overflow: visible; }

/* ── Filter pills ────────────────────────────────────────── */
.cp-filter {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
  margin-bottom: 1rem;
}
.cp-filter__label {
  font-size: 0.68rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--cp-faint);
  margin-right: 0.2rem;
}
.cp-pill {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-width: 34px;
  padding: 0.38rem 0.7rem;
  border: 1px solid var(--cp-line-2);
  border-radius: 99px;
  background: var(--cp-surface);
  color: var(--cp-muted);
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.76rem;
  font-weight: 600;
  text-decoration: none;
  cursor: pointer;
  transition: color 0.15s var(--cp-ease), background 0.15s var(--cp-ease),
              border-color 0.15s var(--cp-ease), transform 0.12s var(--cp-ease);
}
.cp-pill:hover { color: var(--cp-text); border-color: rgba(255,255,255,0.24); }
.cp-pill:active { transform: scale(0.96); }
.cp-pill:focus-visible { outline: 2px solid var(--cp-accent); outline-offset: 2px; }
.cp-pill.is-active {
  background: var(--cp-accent);
  border-color: var(--cp-accent);
  color: #08252a;
}

/* ── Email reminder bar ──────────────────────────────────── */
.cp-mailbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-wrap: wrap;
  gap: 0.6rem 1rem;
  padding: 0.85rem 1rem;
  margin-bottom: 1rem;
  background: var(--cp-surface);
  border: 1px solid var(--cp-line-2);
  border-radius: 12px;
}
.cp-mailbar--muted {
  font-size: 0.78rem;
  color: var(--cp-faint);
  justify-content: flex-start;
}
.cp-mailbar__info { display: flex; flex-direction: column; gap: 0.2rem; }
.cp-mailbar__title { font-size: 0.85rem; font-weight: 600; letter-spacing: -0.01em; }
.cp-mailbar__sub {
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.68rem;
  color: var(--cp-muted);
}
.cp-btn-mail {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  padding: 0.52rem 1rem;
  border: 1px solid var(--cp-accent);
  border-radius: 9px;
  background: var(--cp-accent);
  color: #08252a;
  font-family: var(--cp-font);
  font-size: 0.8rem;
  font-weight: 700;
  cursor: pointer;
  margin: 0;
  transition: filter 0.15s var(--cp-ease), transform 0.12s var(--cp-ease);
}
.cp-btn-mail:hover { filter: brightness(1.08); background: var(--cp-accent); border-color: var(--cp-accent); }
.cp-btn-mail:active { transform: translateY(1px) scale(0.985); }
.cp-btn-mail:focus-visible { outline: 2px solid var(--cp-accent); outline-offset: 2px; }
#send-result { flex-basis: 100%; }
.send-result-box p {
  margin: 0.2rem 0 0;
  padding: 0.5rem 0.75rem;
  border-radius: 8px;
  font-size: 0.78rem;
}
.send-result-box .sr-ok   { background: rgba(46,194,126,0.12); border: 1px solid rgba(46,194,126,0.35); color: var(--cp-paid); }
.send-result-box .sr-fail { background: rgba(229,72,77,0.12);  border: 1px solid rgba(229,72,77,0.35);  color: var(--cp-full); }
.send-result-box .sr-none { background: rgba(255,255,255,0.04); border: 1px solid var(--cp-line-2); color: var(--cp-muted); }

/* ── Registrations table ─────────────────────────────────── */
.cp-board {
  border: 1px solid var(--cp-line-2);
  border-radius: 12px;
  background: var(--cp-surface);
  overflow: auto;
}
.cp-board::-webkit-scrollbar { height: 8px; }
.cp-board::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.13); border-radius: 99px; }
.cp-board::-webkit-scrollbar-track { background: transparent; }
.cp-table {
  width: 100%;
  min-width: 640px;
  border-collapse: separate;
  border-spacing: 0;
  font-family: var(--cp-font);
  font-size: 0.8rem;
  color: var(--cp-text);
  background: transparent;
}
.cp-table thead { background: transparent; }
.cp-table th, .cp-table td {
  border: 0;
  border-bottom: 1px solid var(--cp-line);
  background: transparent;
  padding: 0.6rem 0.85rem;
  text-align: left;
  vertical-align: middle;
  font-family: inherit;
  font-size: inherit;
}
.cp-table tbody tr:last-child td { border-bottom: 0; }
.cp-table thead th {
  padding: 0.55rem 0.85rem;
  font-size: 0.6rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.12em;
  color: var(--cp-faint);
  background: var(--cp-surface-2);
  white-space: nowrap;
}
.cp-table tbody tr {
  animation: cp-fade 0.4s var(--cp-ease) backwards;
  animation-delay: min(calc(var(--i) * 25ms), 350ms);
}
.cp-table tbody tr:hover td {
  background-image: linear-gradient(rgba(255,255,255,0.03), rgba(255,255,255,0.03));
}
.cp-num {
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.7rem;
  color: var(--cp-faint);
  width: 1%;
}
.cp-name { font-weight: 600; letter-spacing: -0.01em; }
.cp-sub {
  display: block;
  font-size: 0.68rem;
  font-weight: 400;
  color: var(--cp-muted);
  margin-top: 1px;
}
.cp-table a.cp-tel { color: var(--cp-text); text-decoration: none; border-bottom: 1px dotted var(--cp-faint); }
.cp-table a.cp-tel:hover { color: var(--cp-accent); border-bottom-color: var(--cp-accent); }
.cp-shift-chip {
  display: inline-flex;
  align-items: center;
  gap: 0.4rem;
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.74rem;
  font-weight: 600;
}
.cp-shift-chip b {
  display: grid;
  place-items: center;
  min-width: 22px;
  height: 22px;
  padding: 0 4px;
  border-radius: 7px;
  background: rgba(255,255,255,0.07);
  border: 1px solid var(--cp-line-2);
  font-weight: 700;
  color: var(--cp-text);
}
.cp-shift-chip span { color: var(--cp-faint); font-size: 0.66rem; }
.cp-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  padding: 0.22rem 0.6rem;
  border-radius: 99px;
  font-size: 0.68rem;
  font-weight: 600;
}
.cp-badge i { width: 6px; height: 6px; border-radius: 50%; background: currentColor; }
.cp-badge--done  { background: rgba(46,194,126,0.12); border: 1px solid rgba(46,194,126,0.35); color: var(--cp-paid); }
.cp-badge--wait  { background: rgba(242,163,60,0.12); border: 1px solid rgba(242,163,60,0.35); color: var(--cp-wait); }
.cp-badge--other { background: rgba(255,255,255,0.05); border: 1px solid var(--cp-line-2); color: var(--cp-muted); }
.cp-date {
  font-family: var(--cp-mono);
  font-feature-settings: 'tnum' 1;
  font-size: 0.66rem;
  color: var(--cp-faint);
}

/* ── Empty state ─────────────────────────────────────────── */
.cp-empty {
  padding: 3rem 1.5rem;
  text-align: center;
  border: 1px dashed var(--cp-line-2);
  border-radius: 12px;
  color: var(--cp-muted);
}
.cp-empty__title { margin: 0 0 0.4rem; font-size: 0.95rem; font-weight: 600; color: var(--cp-text); }
.cp-empty p { margin: 0; font-size: 0.8rem; }

/* ── Email modal ─────────────────────────────────────────── */
#camp-email-modal.modal {
  background: var(--cp-surface);
  color: var(--cp-text);
  border: 1px solid var(--cp-line-2);
  border-radius: 16px;
  box-shadow: 0 24px 64px -24px rgba(0,0,0,0.6);
  font-family: var(--cp-font);
  max-width: min(430px, 94vw);
}
#camp-email-modal .modal-heading {
  background: transparent;
  color: var(--cp-text);
  border: none;
  border-bottom: 1px solid var(--cp-line);
  border-radius: 16px 16px 0 0;
  font-weight: 600;
  letter-spacing: -0.01em;
  margin-bottom: 0;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#camp-email-modal .modal-body {
  background: transparent;
  border: none;
  border-radius: 0 0 16px 16px;
  -webkit-backdrop-filter: none;
  backdrop-filter: none;
}
#camp-email-modal .modal-body p { font-size: 0.85rem; color: var(--cp-muted); margin: 0 0 0.8rem; text-align: left; }
#camp-email-modal .modal-body p strong { color: var(--cp-text); }
.cp-modal-btns { display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.4rem; }
.cp-modal-btns button {
  width: 100%;
  padding: 0.55rem 1rem;
  margin: 0;
  border: 1px solid transparent;
  border-radius: 9px;
  font-family: var(--cp-font);
  font-size: 0.82rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s var(--cp-ease), color 0.15s var(--cp-ease),
              border-color 0.15s var(--cp-ease), transform 0.12s var(--cp-ease),
              filter 0.15s var(--cp-ease);
}
.cp-modal-btns button:active { transform: translateY(1px) scale(0.985); transition-duration: 60ms; }
#camp-email-modal .email-confirm { background: var(--cp-accent); border-color: var(--cp-accent); color: #08252a; font-weight: 700; }
#camp-email-modal .email-confirm:hover { filter: brightness(1.08); background: var(--cp-accent); border-color: var(--cp-accent); }
#camp-email-modal .email-resend { background: rgba(242,163,60,0.12); border-color: rgba(242,163,60,0.4); color: var(--cp-wait); }
#camp-email-modal .email-resend:hover { background: rgba(242,163,60,0.22); border-color: rgba(242,163,60,0.55); color: var(--cp-text); }
#camp-email-modal .email-cancel { background: transparent; border-color: var(--cp-line-2); color: var(--cp-muted); }
#camp-email-modal .email-cancel:hover { background: rgba(255,255,255,0.05); border-color: var(--cp-line-2); color: var(--cp-text); }
.cp-spinner {
  display: inline-flex;
  align-items: center;
  gap: 0.45rem;
  margin-top: 0.6rem;
  font-size: 0.78rem;
  color: var(--cp-muted);
}
.cp-spinner::before {
  content: '';
  width: 13px;
  height: 13px;
  border-radius: 50%;
  border: 2px solid var(--cp-line-2);
  border-top-color: var(--cp-accent);
  animation: cp-spin 0.7s linear infinite;
}

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 56.25rem) {
  .cp-stats { grid-template-columns: repeat(3, 1fr); }
  .cp-stat--lead { grid-column: 1 / -1; }
}
@media (max-width: 33.75rem) {
  .cp-stats { grid-template-columns: repeat(2, 1fr); }
  #nav-table { padding: 1rem; border-radius: 12px; }
}
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
}
</style>

<?php
$total_all  = array_sum(array_column($stats, 'total'));
$total_paid = array_sum(array_column($stats, 'paid'));
$pct_paid   = $total_all > 0 ? round($total_paid / $total_all * 100) : 0;
?>
    <div class="cp-stats">
        <div class="cp-stat cp-stat--lead">
            <span class="cp-stat__label">Apmokėta</span>
            <span class="cp-stat__num"><?= $pct_paid ?><small>%</small></span>
            <span class="cp-stat__bar"><i style="width:<?= $pct_paid ?>%"></i></span>
        </div>
        <div class="cp-stat"><span class="cp-stat__num"><?= $total_all ?></span><span class="cp-stat__label">Iš viso</span></div>
        <div class="cp-stat cp-stat--paid"><span class="cp-stat__num"><?= $total_paid ?></span><span class="cp-stat__label">Apmokėta</span></div>
        <div class="cp-stat cp-stat--wait"><span class="cp-stat__num"><?= $total_all - $total_paid ?></span><span class="cp-stat__label">Laukia</span></div>
    </div>

    <div class="cp-shifts">
        <?php $i = 0; foreach ($stats as $num => $p):
            $pct  = $p['max'] > 0 ? round($p['total'] / $p['max'] * 100) : 0;
            $free = $p['max'] - $p['total'];
            $full = $p['total'] >= $p['max'];

            // dates format: '2026-06-08 – 06-12' → live when today falls inside
            $is_live = false;
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2}).*?(\d{2})-(\d{2})$/u', $p['dates'], $m)) {
                $is_live = ($today >= "$m[1]-$m[2]-$m[3]" && $today <= "$m[1]-$m[4]-$m[5]");
            }
        ?>
        <div class="cp-shift<?= $full ? ' cp-shift--full' : '' ?><?= $is_live ? ' cp-shift--live' : '' ?>" style="--i:<?= $i++ ?>">
            <div class="cp-shift__head">
                <span class="cp-shift__num"><?= $num ?></span>
                <span class="cp-shift__dates"><?= substr($p['dates'], 5) ?></span>
                <?php if ($is_live): ?><span class="cp-shift__live-dot" title="Vyksta dabar"></span><?php endif; ?>
            </div>
            <div class="cp-shift__bar"><i style="width:<?= min(100, $pct) ?>%"></i></div>
            <div class="cp-shift__foot">
                <span><?= $p['total'] ?>/<?= $p['max'] ?> vietų</span>
                <span class="cp-shift__paid"><?= $p['paid'] ?> apmok.</span>
                <?php if ($free > 0): ?>
                    <span class="cp-shift__free"><?= $free ?> laisv.</span>
                <?php else: ?>
                    <span class="cp-shift__full-label">Pilna</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div><!-- /#stat-panel -->

<!-- ===== MAIN CONTENT ===== -->
<div id="nav-table">

    <div class="cp-head">
        <p class="cp-eyebrow">Molas Surf Stovykla &middot; Sezonas 2026</p>
        <h2 class="cp-title">Stovyklos registracijos</h2>
    </div>

    <div class="table-responsive">

        <!-- Shift filter (inside the swap region so active state stays in sync) -->
        <nav class="cp-filter" aria-label="Pamainos filtras">
            <span class="cp-filter__label">Pamaina</span>
            <?php foreach ($pamainos as $val => $label):
                $val_str   = (string) $val;
                $is_active = ($current_shift === $val_str);
            ?>
            <a class="cp-pill<?= $is_active ? ' is-active' : '' ?>"
               <?= $is_active ? 'aria-current="true"' : '' ?>
               href=""
               title="<?= $label ?>"
               mx-get="camps/index/<?= $val_str ?>"
               mx-target=".table-responsive"
               mx-select=".table-responsive"
               mx-push-url="true"><?= $val_str === '' ? 'Visos' : $val_str ?></a>
            <?php endforeach; ?>
        </nav>

        <!-- Email reminder bar -->
        <?php if (!empty($email_shift)): ?>
            <?php $unsent = $email_shift['recipient_count'] - $email_shift['sent_count']; ?>
            <div class="cp-mailbar">
                <div class="cp-mailbar__info">
                    <span class="cp-mailbar__title">Priminimas &middot; Pamaina <?= $email_shift['num'] ?></span>
                    <span class="cp-mailbar__sub">
                        <?= $email_shift['recipient_count'] ?> gavėjai
                        <?php if ($email_shift['last_sent']): ?>
                            &middot; jau išsiųsta <?= date('Y-m-d H:i', strtotime($email_shift['last_sent'])) ?>
                            (<?= $email_shift['sent_count'] ?>/<?= $email_shift['recipient_count'] ?>)
                        <?php endif; ?>
                    </span>
                </div>
                <button type="button" class="cp-btn-mail" onclick="openModal('camp-email-modal')">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect x="2" y="4" width="20" height="16" rx="2"/>
                        <path d="m22 7-10 6L2 7"/>
                    </svg>
                    Siųsti priminimą
                </button>
                <div id="send-result"></div>
            </div>

            <div class="modal" id="camp-email-modal" style="display:none">
                <div class="modal-heading">Siųsti laišką pamainai <?= $email_shift['num'] ?></div>
                <div class="modal-body">
                    <p>Pamaina: <strong><?= out($email_shift['label']) ?></strong></p>
                    <?php if ($unsent > 0): ?>
                        <p>Bus išsiųsta <strong><?= $unsent ?></strong> dar negavusiems dalyviams.</p>
                    <?php else: ?>
                        <p>Visiems šios pamainos dalyviams jau išsiųsta.</p>
                    <?php endif; ?>
                    <div class="cp-modal-btns">
                        <?php if ($unsent > 0): ?>
                            <button class="email-confirm" mx-post="camps/send_shift_email/<?= $email_shift['num'] ?>" mx-target="#send-result" mx-indicator="#send-spinner" mx-close-on-success="true" onclick="closeModal()">Siųsti (<?= $unsent ?>)</button>
                        <?php endif; ?>
                        <?php if ($email_shift['sent_count'] > 0): ?>
                            <button class="email-resend" mx-post="camps/send_shift_email/<?= $email_shift['num'] ?>" mx-vals='{"resend":"1"}' mx-target="#send-result" mx-indicator="#send-spinner" mx-close-on-success="true" onclick="closeModal()">Siųsti visiems iš naujo (<?= $email_shift['recipient_count'] ?>)</button>
                        <?php endif; ?>
                        <button type="button" class="email-cancel" onclick="closeModal()">Atšaukti</button>
                    </div>
                    <span id="send-spinner" class="cp-spinner" style="display:none">Siunčiama&hellip;</span>
                </div>
            </div>
        <?php elseif ($current_shift !== ''): ?>
            <div class="cp-mailbar cp-mailbar--muted">Šioje pamainoje nėra registracijų, laiškų siųsti nėra kam.</div>
        <?php else: ?>
            <div class="cp-mailbar cp-mailbar--muted">Pasirink pamainą laiškams siųsti.</div>
        <?php endif; ?>

        <!-- Registrations -->
        <?php if (!empty($registrations)): ?>
        <div class="cp-board">
            <table class="cp-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Dalyvis</th>
                        <th>Kontaktai</th>
                        <th>Pamaina</th>
                        <th>Statusas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $count = 1; foreach ($registrations as $reg):
                        $shift_parts = explode('. ', (string) $reg->pamaina, 2);
                        $shift_num   = $shift_parts[0] ?? '';
                        $shift_dates = $shift_parts[1] ?? '';

                        $st = strtolower((string) $reg->status);
                        if ($st === 'completed') {
                            $badge_class = 'cp-badge--done';  $badge_label = 'Apmokėta';
                        } elseif ($st === 'pending') {
                            $badge_class = 'cp-badge--wait';  $badge_label = 'Laukiama';
                        } else {
                            $badge_class = 'cp-badge--other'; $badge_label = ucfirst(out((string) $reg->status));
                        }
                    ?>
                    <tr style="--i:<?= $count - 1 ?>">
                        <td class="cp-num"><?= $count++ ?></td>
                        <td>
                            <span class="cp-name"><?= out($reg->name) ?></span>
                            <span class="cp-sub"><?= out($reg->age) ?> m.</span>
                        </td>
                        <td>
                            <a class="cp-tel" href="tel:<?= out($reg->phone) ?>"><?= out($reg->phone) ?></a>
                            <span class="cp-sub"><?= out($reg->email) ?></span>
                        </td>
                        <td>
                            <span class="cp-shift-chip"><b><?= out($shift_num) ?></b><span><?= out($shift_dates) ?></span></span>
                        </td>
                        <td>
                            <span class="cp-badge <?= $badge_class ?>"><i></i><?= $badge_label ?></span>
                            <span class="cp-sub cp-date"><?= date('Y-m-d H:i', strtotime($reg->date_created)) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="cp-empty">
            <p class="cp-empty__title">Registracijų nerasta</p>
            <p>Šiai pamainai registracijų dar nėra &mdash; pasirink kitą pamainą arba &bdquo;Visos&ldquo;.</p>
        </div>
        <?php endif; ?>

    </div><!-- /.table-responsive -->

</div><!-- /#nav-table -->
