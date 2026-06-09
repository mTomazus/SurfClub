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

if (!function_exists('month_name_local')) {
    function month_name_local(int $m): string {
        return [6 => 'June', 7 => 'July', 8 => 'August'][$m] ?? '';
    }
}

$sc_meta       = sc_status_meta();
$status_colors = $sc_meta['colors'];
$status_labels = $sc_meta['labels'];
?>

<div id="title" style="display:none"><h1>Staff Calendar</h1></div>

<!-- ===== OVERVIEW (top-level sibling — hoisted into #stat-cards by the
     sidebar link's mx-select-oob, exactly like every other admin module) ===== -->
<?= sc_render_stat_panel($stats) ?>

<div id="sc-wrapper">

<style>
/* ============================================================
   STAFF CALENDAR — dark theme
   ============================================================ */
:root {
  --sc-bg:          #1a1528;
  --sc-surface:     #241e3a;
  --sc-surface2:    #201b35;
  --sc-border:      rgba(120,100,180,0.18);
  --sc-border-hard: rgba(120,100,180,0.28);
  --sc-thead:       #2a2045;
  --sc-weekend:     #261f3e;
  --sc-text:        #f0ecff;
  --sc-muted:       #9188b0;
  --sc-name-bg:     #201b35;

  --clr-working:  #27ae60;
  --clr-halfday:  #e67e22;
  --clr-dayoff:   #3498db;
  --clr-sick:     #e74c3c;

  --ease-out: cubic-bezier(0.22, 1, 0.36, 1);
  --dur:       150ms;
  --shadow:    0 4px 16px rgba(0,0,0,0.4);
}

/* ── Overview stat panel (lives inside #sc-wrapper) ──────── */
#sc-overview-title {
  font-size: 1.05rem; font-weight: 700; margin: 0 0 0.85rem;
  letter-spacing: -0.01em; color: var(--sc-text);
}
#stat-panel {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 0.6rem;
  margin-bottom: 1.75rem;
}
.stat-card {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 0.4rem; padding: 1.1rem 0.75rem;
  background: var(--sc-surface); border: 1px solid var(--sc-border-hard); border-radius: 10px;
}
.stat-count { font-size: 2rem; font-weight: 700; line-height: 1; letter-spacing: -0.02em; }
.stat-label {
  font-size: 0.62rem; font-weight: 700; text-transform: uppercase;
  letter-spacing: 0.07em; color: var(--sc-muted);
}

/* ── Wrapper ─────────────────────────────────────────────── */
#sc-wrapper {
  padding: 1.25rem 1.5rem 2rem;
  background: var(--sc-bg);
  border-radius: 12px;
  color: var(--sc-text);
  font-family: inherit;
}

/* ── Calendar header ─────────────────────────────────────── */
#sc-cal-header {
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 0.75rem; margin-bottom: 1.1rem;
}
#sc-cal-header h2 { margin: 0; font-size: 1.1rem; font-weight: 700; letter-spacing: -0.01em; color: var(--sc-text); }

.sc-nav { display: flex; align-items: center; gap: 0.35rem; }
.sc-nav a {
  display: inline-flex; align-items: center; padding: 0.35rem 0.75rem;
  border-radius: 6px; text-decoration: none; font-size: 0.82rem;
  font-weight: 600; color: var(--sc-muted); transition: color var(--dur) var(--ease-out);
}
.sc-nav a:hover { color: var(--sc-text); }
.sc-current-month {
  display: inline-flex; align-items: center; padding: 0.35rem 0.9rem;
  font-size: 0.85rem; font-weight: 700; letter-spacing: 0.01em; color: var(--sc-text);
}

.sc-btn-print {
  display: inline-flex; align-items: center; gap: 0.4rem;
  padding: 0.38rem 0.9rem; border-radius: 7px;
  border: 1px solid var(--sc-border-hard); background: transparent;
  color: var(--sc-text); font-size: 0.8rem; font-weight: 600;
  cursor: pointer; font-family: inherit; transition: background var(--dur) var(--ease-out);
}
.sc-btn-print:hover { background: rgba(255,255,255,0.07); }

/* ── Legend ──────────────────────────────────────────────── */
.sc-legend { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 1.25rem; }
.sc-legend-item {
  display: inline-flex; align-items: center; gap: 0.35rem;
  padding: 0.25rem 0.65rem; border-radius: 20px;
  background: rgba(255,255,255,0.06); border: 1px solid var(--sc-border);
  font-size: 0.74rem; font-weight: 600; color: var(--sc-text);
}
.sc-legend-dot { width: 9px; height: 9px; border-radius: 50%; flex-shrink: 0; }

/* ── Table ───────────────────────────────────────────────── */
.sc-table-wrap { overflow-x: auto; border-radius: 10px; border: 1px solid var(--sc-border-hard); }
.sc-table {
  border-collapse: collapse; width: 100%; min-width: 700px;
  font-size: 0.76rem; background: var(--sc-surface2); color: var(--sc-text);
}
.sc-table th, .sc-table td {
  border-right: 1px solid var(--sc-border); border-bottom: 1px solid var(--sc-border);
  text-align: center; vertical-align: middle; padding: 0;
}
.sc-table th:last-child, .sc-table td:last-child { border-right: 0; }
.sc-table tbody tr:last-child td { border-bottom: 0; }

.sc-table th:first-child, .sc-table td:first-child {
  position: sticky; left: 0; z-index: 2; background: var(--sc-name-bg);
  text-align: left; padding: 0 0.75rem; min-width: 150px; white-space: nowrap;
  border-right: 1px solid var(--sc-border-hard);
}

.sc-table thead th {
  background: var(--sc-thead); color: var(--sc-text);
  font-size: 0.68rem; font-weight: 700; padding: 0.55rem 0.15rem;
  line-height: 1.3; min-width: 38px; letter-spacing: 0.01em;
}
.sc-table thead th:first-child { font-size: 0.7rem; letter-spacing: 0.04em; text-transform: uppercase; }
.sc-table thead th.sc-weekend { background: #2e2650; }
.sc-table td.sc-weekend-cell { background: var(--sc-weekend); }

.sc-table th.sc-total-col, .sc-table td.sc-total-col {
  font-weight: 800; font-size: 0.85rem; min-width: 52px;
  color: var(--sc-text); border-left: 1px solid var(--sc-border-hard);
}
.sc-table thead th.sc-total-col {
  background: #2e2650; font-size: 0.65rem; text-transform: uppercase;
  letter-spacing: 0.04em; color: var(--sc-muted);
}

.sc-member-name { font-weight: 700; font-size: 0.82rem; color: var(--sc-text); }
.sc-member-role {
  display: block; font-size: 0.65rem; font-weight: 600; text-transform: uppercase;
  letter-spacing: 0.04em; color: var(--sc-muted); margin-top: 1px;
}

/* ── Data cells ──────────────────────────────────────────── */
.sc-cell { position: relative; height: 50px; min-width: 38px; }
.sc-quick {
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  gap: 3px; height: 100%; padding: 4px 3px;
}
.sc-q-row { display: flex; gap: 3px; }

.sc-sq-btn { background: none; border: none; padding: 0; cursor: pointer; line-height: 0; }
.sc-status-sq {
  display: flex; align-items: center; justify-content: center;
  width: 26px; height: 26px; border-radius: 6px;
  font-size: 0.82rem; font-weight: 900; color: #fff; box-shadow: 0 1px 4px rgba(0,0,0,0.3);
  transition: transform var(--dur) var(--ease-out), box-shadow var(--dur) var(--ease-out);
}
.sc-sq-btn:hover .sc-status-sq { transform: scale(1.08); box-shadow: 0 2px 6px rgba(0,0,0,0.4); }
.sc-sq-btn:active .sc-status-sq { transform: scale(0.94); }

.sc-q-btn {
  width: 24px; height: 20px; border-radius: 4px;
  font-size: 0.72rem; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; border: none;
  transition: transform var(--dur) var(--ease-out), box-shadow var(--dur) var(--ease-out);
  flex-shrink: 0;
}
.sc-q-btn:hover  { transform: scale(1.1); box-shadow: 0 2px 5px rgba(0,0,0,0.35); }
.sc-q-btn:active { transform: scale(0.93); transition-duration: 60ms; }
.sc-q-work  { background: var(--clr-working); color: #fff; }
.sc-q-off   { background: rgba(255,255,255,0.1); color: var(--sc-muted); }
.sc-q-clear { background: rgba(255,255,255,0.08); color: var(--sc-muted); font-size: 0.7rem; }

.sc-q-more {
  width: 34px; height: 13px; border-radius: 3px; border: none; cursor: pointer;
  background: rgba(255,255,255,0.05); color: var(--sc-muted);
  font-size: 0.7rem; line-height: 1; display: flex; align-items: center; justify-content: center;
  transition: background var(--dur) var(--ease-out), color var(--dur) var(--ease-out);
}
.sc-q-more:hover { background: rgba(255,255,255,0.12); color: var(--sc-text); }

/* ============================================================
   MODAL (built by mx-build-modal; framework supplies the shell)
   ============================================================ */
#sc-modal.modal { background: var(--sc-surface); color: var(--sc-text); border-radius: 12px; }
#sc-modal .modal-heading {
  background: var(--sc-thead); color: var(--sc-text);
  border: none; border-radius: 12px 12px 0 0; letter-spacing: 0.03em;
}
#sc-modal .modal-body { border: none; background: var(--sc-surface); border-radius: 0 0 12px 12px; }

.sc-modal-meta { font-size: 0.82rem; color: var(--sc-muted); margin: 0 0 1.1rem; text-align: left; }

.sc-status-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.1rem; }
.sc-opt { display: block; cursor: pointer; position: relative; }
.sc-opt input { position: absolute; opacity: 0; pointer-events: none; }
.sc-opt span {
  display: block; padding: 0.65rem 0.5rem; border-radius: 8px;
  border: 2px solid transparent; text-align: center;
  font-size: 0.82rem; font-weight: 700; color: #fff;
  transition: transform var(--dur) var(--ease-out), filter var(--dur) var(--ease-out);
}
.sc-opt:hover span { filter: brightness(1.1); transform: translateY(-1px); }
.sc-opt input:checked + span { border-color: rgba(255,255,255,0.85); transform: translateY(-1px); }
.sc-opt input:focus-visible + span { outline: 2px solid #fff; outline-offset: 2px; }

.sc-notes-label {
  font-size: 0.72rem; font-weight: 700; color: var(--sc-muted);
  margin-bottom: 0.35rem; display: block; text-transform: uppercase; letter-spacing: 0.06em;
}
#sc-modal #sc-modal-notes {
  width: 100%; height: 68px; border: 1.5px solid var(--sc-border-hard);
  border-radius: 7px; padding: 0.5rem 0.6rem; font-size: 0.83rem;
  font-family: inherit; resize: vertical; box-sizing: border-box; margin-bottom: 1.25rem;
  background: var(--sc-bg); color: var(--sc-text);
  transition: border-color var(--dur) var(--ease-out), box-shadow var(--dur) var(--ease-out);
}
#sc-modal #sc-modal-notes:focus { outline: none; border-color: #6a3fc8; box-shadow: 0 0 0 3px rgba(106,63,200,0.2); }

.sc-modal-btns { display: flex; gap: 0.45rem; justify-content: flex-end; align-items: center; flex-wrap: wrap; }
.sc-modal-btns button {
  padding: 0.48rem 1.1rem; border-radius: 7px; border: none;
  font-size: 0.83rem; font-weight: 700; font-family: inherit; cursor: pointer; margin: 0;
  transition: filter var(--dur) var(--ease-out), transform var(--dur) var(--ease-out);
}
.sc-modal-btns button:hover  { filter: brightness(1.1); transform: translateY(-1px); }
.sc-modal-btns button:active { transform: scale(0.97); transition-duration: 60ms; }
.sc-btn-save   { background: #27ae60; color: #fff; }
.sc-btn-clear  { background: #c0392b; color: #fff; }
.sc-btn-cancel { background: rgba(255,255,255,0.1); color: var(--sc-text); }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 900px) { #stat-panel { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 540px) { #stat-panel { grid-template-columns: repeat(2, 1fr); } }
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after { animation-duration:.01ms!important; transition-duration:.01ms!important; }
}
@media print {
  body > * { display: none !important; }
  #sc-wrapper { display: block !important; background: #fff !important; color: #000 !important; }
  .sc-table { font-size: 0.65rem; background: #fff !important; color: #000 !important; }
  .sc-cell  { height: 32px !important; }
}
</style>

<!-- ===== CALENDAR HEADER ===== -->
<div id="sc-cal-header">
    <h2>Staff Calendar</h2>
    <div style="display:flex;align-items:center;gap:0.5rem;flex-wrap:wrap">
        <div class="sc-nav">
            <?php if ($prev_month): ?>
                <a href=""
                   mx-get="staff_calendar/index/<?= $prev_month ?>"
                   mx-target="#form-container" mx-select="#sc-wrapper">&#8592; <?= out(month_name_local($prev_month)) ?></a>
            <?php endif; ?>
            <span class="sc-current-month"><?= $month_name ?> <?= $year ?></span>
            <?php if ($next_month): ?>
                <a href=""
                   mx-get="staff_calendar/index/<?= $next_month ?>"
                   mx-target="#form-container" mx-select="#sc-wrapper"><?= out(month_name_local($next_month)) ?> &#8594;</a>
            <?php endif; ?>
        </div>
        <button class="sc-btn-print" onclick="window.print()">&#128424; Print / PDF</button>
    </div>
</div>

<!-- ===== LEGEND ===== -->
<div class="sc-legend">
    <?php foreach ($status_colors as $slug => $color): ?>
    <div class="sc-legend-item">
        <div class="sc-legend-dot" style="background:<?= $color ?>"></div>
        <span><?= $status_labels[$slug] ?></span>
    </div>
    <?php endforeach; ?>
    <div class="sc-legend-item">
        <div class="sc-legend-dot" style="background:rgba(255,255,255,0.2);border:1px solid rgba(255,255,255,0.3)"></div>
        <span>Not set</span>
    </div>
</div>

<!-- ===== CALENDAR GRID ===== -->
<div class="sc-table-wrap">
<table class="sc-table">
    <thead>
        <tr>
            <th>Staff Member</th>
            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $dow        = (int) date('N', mktime(0,0,0,$month,$d,$year));
                $is_weekend = $dow >= 6;
                $day_abbr   = strtoupper(substr(date('D', mktime(0,0,0,$month,$d,$year)), 0, 2));
            ?>
            <th class="<?= $is_weekend ? 'sc-weekend' : '' ?>">
                <?= $d ?><br><span style="font-size:0.6rem;opacity:.55;font-weight:600"><?= $day_abbr ?></span>
            </th>
            <?php endfor; ?>
            <th class="sc-total-col">Days</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($staff as $member):
        $total = 0.0;
        $display_name = !empty($member->full_name)
            ? htmlspecialchars($member->full_name)
            : htmlspecialchars($member->username);
        $role_str = !empty($member->role) ? htmlspecialchars(strtoupper($member->role)) : 'STAFF';
    ?>
        <tr>
            <td>
                <span class="sc-member-name"><?= $display_name ?></span>
                <span class="sc-member-role"><?= $role_str ?></span>
            </td>

            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $entry    = $schedule_index[$member->id . '|' . $date_str] ?? null;
                $status   = $entry ? $entry->status : '';
                $notes    = $entry ? $entry->notes  : '';
                $dow_body = (int) date('N', mktime(0,0,0,$month,$d,$year));

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

</div><!-- /#sc-wrapper -->
