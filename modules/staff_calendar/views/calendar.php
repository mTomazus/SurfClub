<?php
/**
 * Staff Calendar — monthly grid view.
 * Loaded inside admin_area template via $data['view_file'] = 'calendar'.
 *
 * Available variables (set by Staff_calendar::index()):
 *   $staff           — array of member objects (id, username, full_name, role)
 *   $schedule_index  — assoc array keyed "member_id|Y-m-d" => schedule row object
 *   $month           — int (6,7,8)
 *   $year            — int
 *   $days_in_month   — int
 *   $month_name      — string
 *   $prev_month      — int|null
 *   $next_month      — int|null
 */

// Local helper — used in the month nav to avoid passing an extra var from the controller
if (!function_exists('month_name_local')) {
    function month_name_local(int $m): string {
        return [6 => 'June', 7 => 'July', 8 => 'August'][$m] ?? '';
    }
}

$status_colors = [
    'working' => '#2ecc71',
    'halfday' => '#f39c12',
    'dayoff'  => '#95a5a6',
    'sick'    => '#e74c3c',
];

$status_labels = [
    'working' => 'Working',
    'halfday' => 'Half Day',
    'dayoff'  => 'Day Off',
    'sick'    => 'Sick',
];

$total_staff   = count($staff);
$working_count = 0;
$halfday_count = 0;
$dayoff_count  = 0;
$sick_count    = 0;
foreach ($schedule_index as $entry) {
    if ($entry->status === 'working')     $working_count++;
    elseif ($entry->status === 'halfday') $halfday_count++;
    elseif ($entry->status === 'dayoff')  $dayoff_count++;
    elseif ($entry->status === 'sick')    $sick_count++;
}
$filled      = $working_count + $halfday_count + $dayoff_count + $sick_count;
$total_slots = $total_staff * $days_in_month;
$coverage    = $total_slots > 0 ? round($filled / $total_slots * 100) : 0;
?>

<div id="form-container">

<div id="title" style="display:none"><h1>Staff Calendar</h1></div>

<div id="stat-panel">
    <a class="stat-card">
        <span class="stat-label">Staff</span>
        <span class="stat-count"><?= $total_staff ?></span>
    </a>
    <a class="stat-card">
        <span class="stat-label">Working</span>
        <span class="stat-count" style="color:#2ecc71"><?= $working_count ?></span>
    </a>
    <a class="stat-card">
        <span class="stat-label">Half Days</span>
        <span class="stat-count" style="color:#f39c12"><?= $halfday_count ?></span>
    </a>
    <a class="stat-card">
        <span class="stat-label">Days Off</span>
        <span class="stat-count" style="color:#95a5a6"><?= $dayoff_count ?></span>
    </a>
    <a class="stat-card">
        <span class="stat-label">Sick</span>
        <span class="stat-count" style="color:#e74c3c"><?= $sick_count ?></span>
    </a>
    <a class="stat-card">
        <span class="stat-label">Coverage</span>
        <span class="stat-count"><?= $coverage ?>%</span>
    </a>
</div>

<div id="sc-wrapper">

<style>
/* ============================================================
   STAFF CALENDAR — Premium styles
   ============================================================ */
:root {
  --sc-purple:        hsl(276 100% 43%);   /* #6a0dad — matches admin sidebar */
  --sc-purple-dark:   hsl(276 100% 32%);
  --sc-purple-dim:    hsl(276 60% 20%);
  --sc-purple-glow:   hsl(276 100% 43% / .18);
  --sc-surface:       hsl(0 0% 100%);
  --sc-border:        hsl(220 14% 88%);
  --sc-text:          hsl(220 18% 14%);
  --sc-muted:         hsl(220 9% 52%);
  --sc-weekend-bg:    hsl(276 30% 97%);
  --sc-weekend-head:  hsl(276 40% 30%);
  --sc-total-bg:      hsl(276 30% 96%);
  --sc-name-bg:       hsl(220 20% 97%);
  --sc-name-head:     hsl(276 100% 38%);

  --ease-out:   cubic-bezier(0.22, 1, 0.36, 1);
  --ease-in-out:cubic-bezier(0.65, 0, 0.35, 1);
  --dur-micro:  150ms;
  --dur-std:    250ms;

  --shadow-sm:  0 1px 3px hsl(220 20% 10% / .08), 0 1px 2px hsl(220 20% 10% / .06);
  --shadow-md:  0 4px 12px hsl(220 20% 10% / .10), 0 2px 4px hsl(220 20% 10% / .07);
  --shadow-lg:  0 12px 28px hsl(220 20% 10% / .13), 0 4px 8px hsl(220 20% 10% / .08);
  --shadow-xl:  0 24px 48px hsl(220 20% 10% / .18), 0 8px 16px hsl(220 20% 10% / .10);
}

/* ── Wrapper ─────────────────────────────────────────────── */
#sc-wrapper {
    padding: 1.5rem 1.75rem 2.5rem;
    font-family: inherit;
}

/* ── Header bar ──────────────────────────────────────────── */
#sc-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1.5rem;
}
#sc-header h1 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: -0.02em;
    color: hsl(0 0% 98%);
}

/* ── Month navigation ────────────────────────────────────── */
.sc-nav {
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.sc-nav a,
.sc-nav span {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.9rem;
    border-radius: 6px;
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.01em;
}
.sc-nav a {
    background: hsl(276 100% 43% / .22);
    color: hsl(276 80% 90%);
    border: 1px solid hsl(276 60% 60% / .3);
    transition: background var(--dur-micro) var(--ease-out),
                transform  var(--dur-micro) var(--ease-out),
                box-shadow var(--dur-micro) var(--ease-out);
}
.sc-nav a:hover {
    background: hsl(276 100% 43% / .38);
    border-color: hsl(276 60% 70% / .5);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}
.sc-nav a:active {
    transform: translateY(0) scale(0.98);
    transition-duration: 80ms;
}
.sc-nav .sc-current-month {
    background: hsl(276 100% 43% / .32);
    border: 1px solid hsl(276 60% 65% / .45);
    color: hsl(276 80% 93%);
    font-weight: 700;
    min-width: 100px;
    text-align: center;
    letter-spacing: 0.02em;
}

/* ── Action buttons ──────────────────────────────────────── */
.sc-actions { display: flex; gap: 0.5rem; }

.sc-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.42rem 1rem;
    border-radius: 6px;
    font-size: 0.83rem;
    font-weight: 600;
    cursor: pointer;
    border: 1px solid hsl(220 14% 80% / .25);
    background: hsl(0 0% 100% / .1);
    color: hsl(0 0% 98%);
    text-decoration: none;
    transition: background var(--dur-micro) var(--ease-out),
                transform  var(--dur-micro) var(--ease-out),
                box-shadow var(--dur-micro) var(--ease-out);
}
.sc-btn:hover {
    background: hsl(0 0% 100% / .18);
    transform: translateY(-1px);
    box-shadow: var(--shadow-sm);
}
.sc-btn:active { transform: translateY(0) scale(0.98); transition-duration: 80ms; }
.sc-btn-print {
    background: hsl(210 85% 55% / .22);
    border-color: hsl(210 70% 65% / .35);
    color: hsl(210 90% 88%);
}
.sc-btn-print:hover { background: hsl(210 85% 55% / .38); }

/* ── Legend ──────────────────────────────────────────────── */
.sc-legend {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
}
.sc-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.28rem 0.7rem;
    border-radius: 20px;
    background: hsl(0 0% 100% / .08);
    border: 1px solid hsl(0 0% 100% / .12);
    font-size: 0.76rem;
    font-weight: 600;
    color: hsl(0 0% 90%);
    letter-spacing: 0.01em;
}
.sc-legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    box-shadow: 0 0 0 2px hsl(0 0% 100% / .15);
}

/* ── Table wrapper ───────────────────────────────────────── */
.sc-table-wrap {
    overflow-x: auto;
    border-radius: 10px;
    box-shadow: var(--shadow-md);
}

/* ── Grid table ──────────────────────────────────────────── */
.sc-table {
    border-collapse: separate; /* separate required for position:sticky on cells */
    border-spacing: 0;
    width: 100%;
    padding: 0;
    border: 0;
    min-width: 700px;
    font-size: 0.76rem;
    background: var(--sc-surface);
    color: var(--sc-text);
    border-radius: 10px;
    /* overflow:hidden removed — it blocks position:sticky on child cells */
}
.sc-table th,
.sc-table td {
    border-right: 1px solid var(--sc-border);
    border-bottom: 1px solid var(--sc-border);
    text-align: center;
    vertical-align: middle;
    padding: 0;
}
.sc-table th:first-child,
.sc-table td:first-child {
    border-left: 1px solid var(--sc-border);
}
.sc-table thead tr:first-child th {
    border-top: 1px solid var(--sc-border);
}
.sc-table thead tr:first-child th:first-child {
    border-top-left-radius: 10px;
}
.sc-table thead tr:first-child th:last-child {
    border-top-right-radius: 10px;
}

/* ── Sticky name column ──────────────────────────────────── */
.sc-table th:first-child,
.sc-table td:first-child {
    position: sticky;
    left: 0px;
    z-index: 2;
    background: var(--sc-name-bg);
    font-weight: 700;
    padding: 0 0.65rem;
    min-width: 140px;
    max-width: 170px;
    text-align: left;
    white-space: nowrap;
    border-right: 2px solid hsl(276 30% 82%);
}
.sc-table td:first-child {
    background: hsl(0 0% 100%);
    font-size: 0.8rem;
    color: var(--sc-text);
}

/* ── Header row ──────────────────────────────────────────── */
.sc-table thead th {
    background: hsl(276 100% 38%);
    color: hsl(276 80% 95%);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.55rem 0.1rem;
    line-height: 1.3;
    min-width: 40px;
    letter-spacing: 0.01em;
}
.sc-table thead th:first-child {
    background: var(--sc-name-head);
    color: hsl(0 0% 98%);
    font-size: 0.75rem;
    letter-spacing: 0.03em;
    text-transform: uppercase;
}
.sc-table thead th.sc-weekend {
    background: var(--sc-weekend-head);
}

/* Weekend data cells — background set via inline style in PHP/JS */

/* ── Totals column ───────────────────────────────────────── */
.sc-table th.sc-total-col,
.sc-table td.sc-total-col {
    background: var(--sc-total-bg);
    font-weight: 800;
    min-width: 56px;
    font-size: 0.85rem;
    color: var(--sc-purple-dark);
    border-left: 2px solid hsl(276 30% 82%);
}
.sc-table thead th.sc-total-col {
    background: hsl(276 100% 30%);
    color: hsl(48 100% 70%);
    font-size: 0.72rem;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

/* ── Data cells ──────────────────────────────────────────── */
.sc-cell {
    position: relative;
    height: 44px;
    min-width: 40px;
    cursor: pointer;
    transition: box-shadow var(--dur-micro) var(--ease-out),
                filter     var(--dur-micro) var(--ease-out);
}
.sc-cell:hover {
    filter: brightness(0.90);
    box-shadow: inset 0 0 0 1px hsl(276 100% 43% / .3);
    z-index: 1;
}

/* ── Quick-action buttons ────────────────────────────────── */
.sc-quick {
    display: grid;
    justify-content: center;
    align-items: center;
    gap: 4px;
    height: 100%;
    padding: 4px 3px;
}
.sc-q-btn {
    width: 22px;
    height: 22px;
    border-radius: 4px;
    font-size: 0.72rem;
    line-height: 1;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    transition: transform  var(--dur-micro) var(--ease-out),
                box-shadow var(--dur-micro) var(--ease-out),
                opacity    var(--dur-micro) var(--ease-out);
    flex-shrink: 0;
    box-shadow: 0 1px 2px hsl(0 0% 0% / .18);
}
.sc-q-btn:hover {
    transform: translateY(-1px) scale(1.08);
    box-shadow: 0 3px 6px hsl(0 0% 0% / .22);
}
.sc-q-btn:active {
    transform: scale(0.95);
    transition-duration: 60ms;
}
.sc-q-work { background: hsl(145 63% 49%); color: #fff; }
.sc-q-off  { background: hsl(210 10% 62%); color: #fff; }

/* ── Status dot (set cells) ──────────────────────────────── */
.sc-status-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid hsl(0 0% 100% / .45);
    box-shadow: 0 1px 3px hsl(0 0% 0% / .2);
    flex-shrink: 0;
}

/* ============================================================
   MODAL
   ============================================================ */
#sc-modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: hsl(220 20% 5% / .6);
    z-index: 9998;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(2px);
}
#sc-modal-overlay.active {
    display: flex;
    animation: sc-fade-in var(--dur-std) var(--ease-out) both;
}
@keyframes sc-fade-in {
    from { opacity: 0; }
    to   { opacity: 1; }
}
#sc-modal {
    background: var(--sc-surface);
    color: var(--sc-text);
    border-radius: 12px;
    padding: 1.75rem;
    width: 360px;
    max-width: 95vw;
    box-shadow: var(--shadow-xl);
    position: relative;
    border: 1px solid var(--sc-border);
    animation: sc-slide-up var(--dur-std) var(--ease-out) both;
}
@keyframes sc-slide-up {
    from { opacity: 0; transform: translateY(12px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)    scale(1);    }
}
#sc-modal h3 {
    margin: 0 0 0.25rem;
    font-size: 1.05rem;
    font-weight: 700;
    letter-spacing: -0.01em;
    color: var(--sc-text);
}
#sc-modal .sc-modal-meta {
    font-size: 0.78rem;
    color: var(--sc-muted);
    margin-bottom: 1.25rem;
    font-weight: 500;
}

/* Status grid in modal */
.sc-status-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 1.1rem;
}
.sc-status-opt {
    padding: 0.65rem 0.5rem;
    border-radius: 8px;
    border: 2.5px solid transparent;
    text-align: center;
    cursor: pointer;
    font-size: 0.82rem;
    font-weight: 700;
    color: #fff;
    letter-spacing: 0.01em;
    transition: transform  var(--dur-micro) var(--ease-out),
                box-shadow var(--dur-micro) var(--ease-out),
                filter     var(--dur-micro) var(--ease-out);
    box-shadow: var(--shadow-sm);
}
.sc-status-opt:hover {
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
    filter: brightness(1.07);
}
.sc-status-opt:active { transform: scale(0.97); transition-duration: 60ms; }
.sc-status-opt.selected {
    border-color: var(--sc-text);
    box-shadow: 0 0 0 3px hsl(220 18% 14% / .18), var(--shadow-sm);
    transform: translateY(-1px);
}

/* Notes field */
.sc-notes-label {
    font-size: 0.78rem;
    font-weight: 700;
    color: var(--sc-muted);
    margin-bottom: 0.35rem;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
#sc-modal-notes {
    width: 100%;
    height: 72px;
    border: 1.5px solid var(--sc-border);
    border-radius: 7px;
    padding: 0.5rem 0.6rem;
    font-size: 0.83rem;
    font-family: inherit;
    resize: vertical;
    box-sizing: border-box;
    margin-bottom: 1.25rem;
    color: var(--sc-text);
    transition: border-color var(--dur-micro) var(--ease-out),
                box-shadow   var(--dur-micro) var(--ease-out);
}
#sc-modal-notes:hover { border-color: hsl(220 14% 72%); }
#sc-modal-notes:focus {
    outline: none;
    border-color: var(--sc-purple);
    box-shadow: 0 0 0 3px var(--sc-purple-glow);
}

/* Modal buttons */
.sc-modal-btns {
    display: flex;
    gap: 0.45rem;
    justify-content: flex-end;
    align-items: center;
    flex-wrap: wrap;
}
.sc-modal-btns button {
    padding: 0.48rem 1.1rem;
    border-radius: 7px;
    border: none;
    font-size: 0.83rem;
    font-weight: 700;
    font-family: inherit;
    cursor: pointer;
    transition: background    var(--dur-micro) var(--ease-out),
                transform     var(--dur-micro) var(--ease-out),
                box-shadow    var(--dur-micro) var(--ease-out);
    box-shadow: var(--shadow-sm);
}
.sc-modal-btns button:hover    { transform: translateY(-1px); box-shadow: var(--shadow-md); }
.sc-modal-btns button:active   { transform: scale(0.97); transition-duration: 60ms; }

#sc-btn-save   { background: hsl(145 63% 42%); color: #fff; }
#sc-btn-save:hover   { background: hsl(145 63% 36%); }
#sc-btn-clear  { background: hsl(4 76% 54%); color: #fff; }
#sc-btn-clear:hover  { background: hsl(4 76% 46%); }
#sc-btn-cancel { background: hsl(220 14% 91%); color: hsl(220 18% 30%); box-shadow: none; }
#sc-btn-cancel:hover { background: hsl(220 14% 84%); }

#sc-modal-close {
    position: absolute;
    top: 0.75rem;
    right: 0.9rem;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: hsl(220 14% 94%);
    border: none;
    font-size: 1.1rem;
    cursor: pointer;
    color: var(--sc-muted);
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background var(--dur-micro) var(--ease-out),
                color     var(--dur-micro) var(--ease-out);
}
#sc-modal-close:hover { background: hsl(220 14% 86%); color: var(--sc-text); }

/* Saving indicator */
#sc-saving {
    display: none;
    font-size: 0.78rem;
    color: var(--sc-muted);
    margin-right: auto;
    align-self: center;
    font-style: italic;
}

/* ============================================================
   PRINT
   ============================================================ */
@media print {
    body > * { display: none !important; }
    #sc-print-area { display: block !important; }
    #sc-modal-overlay { display: none !important; }
    .sc-table { font-size: 0.65rem; }
    .sc-cell { height: 32px !important; }
}

/* ============================================================
   TOAST
   ============================================================ */
#sc-toast {
    position: fixed;
    bottom: 1.75rem;
    right: 1.75rem;
    padding: 0.65rem 1.2rem;
    background: hsl(145 63% 38%);
    color: #fff;
    border-radius: 8px;
    font-size: 0.83rem;
    font-weight: 700;
    opacity: 0;
    transform: translateY(6px);
    transition: opacity var(--dur-std) var(--ease-out),
                transform var(--dur-std) var(--ease-out);
    z-index: 9999;
    pointer-events: none;
    box-shadow: var(--shadow-md);
    letter-spacing: 0.01em;
}
#sc-toast.error { background: hsl(4 76% 50%); }
#sc-toast.show  { opacity: 1; transform: translateY(0); }

/* ── Stat panel (visible when calendar loaded directly) ─── */
#stat-panel {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
    gap: 0.75rem;
    padding: 1rem 1.5rem 0;
}
#stat-panel .stat-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 0.3rem;
    padding: 1rem 0.75rem;
    background: rgba(255,255,255,0.12);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 10px;
    color: floralwhite;
    text-decoration: none;
    font-family: Silom, monospace;
    cursor: default;
}
#stat-panel .stat-label {
    font-size: 0.68rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    opacity: 0.75;
}
#stat-panel .stat-count {
    font-size: 1.9rem;
    font-weight: bold;
    line-height: 1;
}

/* ── Reduced motion ──────────────────────────────────────── */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: .01ms !important;
    transition-duration: .01ms !important;
  }
}
</style>

    <!-- ===== PAGE HEADER ===== -->
    <div id="sc-header">
        <h1>Staff Calendar</h1>

        <!-- Month navigation -->
        <div class="sc-nav">
            <?php if ($prev_month): ?>
                <a href="<?= BASE_URL ?>staff_calendar/index/<?= $prev_month ?>">&#8592; <?= htmlspecialchars(month_name_local($prev_month)) ?></a>
            <?php endif; ?>
            <span class="sc-current-month"><?= $month_name ?> <?= $year ?></span>
            <?php if ($next_month): ?>
                <a href="<?= BASE_URL ?>staff_calendar/index/<?= $next_month ?>"><?= htmlspecialchars(month_name_local($next_month)) ?> &#8594;</a>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="sc-actions">
            <button class="sc-btn sc-btn-print" onclick="window.print()">Print / PDF</button>
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
            <div class="sc-legend-dot" style="background:#fff;border:1px solid #ccc"></div>
            <span>Not set</span>
        </div>
    </div>

    <!-- ===== CALENDAR GRID ===== -->
    <div class="sc-table-wrap" id="sc-print-area">
    <table class="sc-table">
        <thead>
            <tr>
                <th>Staff Member</th>
                <?php for ($d = 1; $d <= $days_in_month; $d++):
                    $dow = (int) date('N', mktime(0,0,0,$month,$d,$year)); // 1=Mon … 7=Sun
                    $is_weekend = $dow >= 6;
                    $day_label  = date('D', mktime(0,0,0,$month,$d,$year));
                ?>
                <th class="<?= $is_weekend ? 'sc-weekend' : '' ?>" title="<?= $day_label ?>">
                    <?= $d ?><br><span style="font-size:0.65rem;opacity:.7"><?= substr($day_label, 0, 2) ?></span>
                </th>
                <?php endfor; ?>
                <th class="sc-total-col">Days</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($staff as $member):
            $total = 0.0;
        ?>
            <tr>
                <td>
                    <?php
                    $display_name = !empty($member->full_name)
                        ? htmlspecialchars($member->full_name)
                        : htmlspecialchars($member->username);
                    $role_display = !empty($member->role)
                        ? '<br><small style="color:#888;font-weight:400">' . htmlspecialchars($member->role) . '</small>'
                        : '';
                    echo $display_name . $role_display;
                    ?>
                </td>

                <?php for ($d = 1; $d <= $days_in_month; $d++):
                    $date_str   = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                    $key        = $member->id . '|' . $date_str;
                    $entry      = $schedule_index[$key] ?? null;
                    $status     = $entry ? $entry->status : '';
                    $notes      = $entry ? $entry->notes  : '';
                    $bg_color   = $status ? $status_colors[$status] : ($is_wkend ? '#f5f0fc' : '#ffffff');
                    $dow_body   = (int) date('N', mktime(0,0,0,$month,$d,$year));
                    $is_wkend   = $dow_body >= 6;

                    // Accumulate totals
                    if ($status === 'working') $total += 1.0;
                    if ($status === 'halfday') $total += 0.5;
                ?>
                <td class="sc-cell<?= $is_wkend ? ' sc-weekend-cell' : '' ?>"
                    style="background:<?= $bg_color ?>"
                    data-weekend="<?= $is_wkend ? '1' : '0' ?>"
                    data-member-id="<?= $member->id ?>"
                    data-member-name="<?= htmlspecialchars($display_name) ?>"
                    data-date="<?= $date_str ?>"
                    data-status="<?= htmlspecialchars($status) ?>"
                    data-notes="<?= htmlspecialchars($notes) ?>"
                    onclick="scOpenModal(this)"
                    title="<?= $date_str ?><?= $status ? ' — ' . $status_labels[$status] : '' ?><?= $notes ? ': ' . htmlspecialchars($notes) : '' ?>">
                    <div class="sc-quick">
                        <?php if ($status): ?>
                            <div class="sc-status-dot" style="background:<?= $bg_color ?>;border:2px solid rgba(0,0,0,0.2)"></div>
                        <?php else: ?>
                            <button class="sc-q-btn sc-q-work"
                                title="Mark working"
                                onclick="event.stopPropagation(); scQuickSet(<?= $member->id ?>, '<?= $date_str ?>', 'working', this.closest('td'))">&#10003;</button>
                            <button class="sc-q-btn sc-q-off"
                                title="Mark day off"
                                onclick="event.stopPropagation(); scQuickSet(<?= $member->id ?>, '<?= $date_str ?>', 'dayoff', this.closest('td'))">&#10007;</button>
                        <?php endif; ?>
                    </div>
                </td>
                <?php endfor; ?>

                <td class="sc-total-col"><?= $total > 0 ? ($total == floor($total) ? (int)$total : number_format($total, 1)) : '—' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div><!-- /.sc-table-wrap -->

<!-- ===== MODAL ===== -->
<div id="sc-modal-overlay" role="dialog" aria-modal="true" aria-labelledby="sc-modal-title">
    <div id="sc-modal">
        <button id="sc-modal-close" onclick="scCloseModal()" aria-label="Close">&times;</button>
        <h3 id="sc-modal-title">Set Status</h3>
        <div class="sc-modal-meta" id="sc-modal-meta"></div>

        <div class="sc-status-grid">
            <div class="sc-status-opt" style="background:#2ecc71" data-value="working"  onclick="scSelectStatus(this)">Working</div>
            <div class="sc-status-opt" style="background:#f39c12" data-value="halfday"  onclick="scSelectStatus(this)">Half Day</div>
            <div class="sc-status-opt" style="background:#95a5a6" data-value="dayoff"   onclick="scSelectStatus(this)">Day Off</div>
            <div class="sc-status-opt" style="background:#e74c3c" data-value="sick"     onclick="scSelectStatus(this)">Sick</div>
        </div>

        <label class="sc-notes-label" for="sc-modal-notes">Notes (optional)</label>
        <textarea id="sc-modal-notes" placeholder="Add a note…"></textarea>

        <div class="sc-modal-btns">
            <span id="sc-saving">Saving…</span>
            <button id="sc-btn-clear"  onclick="scSaveStatus('')">Clear</button>
            <button id="sc-btn-cancel" onclick="scCloseModal()">Cancel</button>
            <button id="sc-btn-save"   onclick="scSaveStatus(scActiveStatus)">Save</button>
        </div>
    </div>
</div>

<!-- ===== TOAST ===== -->
<div id="sc-toast"></div>

<script>
(function () {
    'use strict';

    // ── State ──────────────────────────────────────────────
    var scActiveMemberId   = null;
    var scActiveMemberName = null;
    var scActiveDate       = null;
    var scActiveCell       = null;

    // Exposed on window so inline onclick attrs can reach it
    window.scActiveStatus = null;

    var STATUS_COLORS = {
        working: '#2ecc71',
        halfday: '#f39c12',
        dayoff:  '#95a5a6',
        sick:    '#e74c3c',
    };

    var STATUS_LABELS = {
        working: 'Working',
        halfday: 'Half Day',
        dayoff:  'Day Off',
        sick:    'Sick',
    };

    // ── Modal open ─────────────────────────────────────────
    window.scOpenModal = function (cell) {
        scActiveMemberId   = cell.dataset.memberId;
        scActiveMemberName = cell.dataset.memberName;
        scActiveDate       = cell.dataset.date;
        scActiveCell       = cell;
        window.scActiveStatus = cell.dataset.status || null;

        document.getElementById('sc-modal-meta').textContent =
            scActiveMemberName + '  ·  ' + scActiveDate;

        document.getElementById('sc-modal-notes').value = cell.dataset.notes || '';

        // Highlight the currently active status option
        document.querySelectorAll('.sc-status-opt').forEach(function (el) {
            el.classList.toggle('selected', el.dataset.value === window.scActiveStatus);
        });

        document.getElementById('sc-modal-overlay').classList.add('active');
        document.getElementById('sc-modal-notes').focus();
    };

    // ── Modal close ────────────────────────────────────────
    window.scCloseModal = function () {
        document.getElementById('sc-modal-overlay').classList.remove('active');
    };

    // Close on overlay click (but not on modal itself)
    document.getElementById('sc-modal-overlay').addEventListener('click', function (e) {
        if (e.target === this) scCloseModal();
    });

    // ── Status selection ───────────────────────────────────
    window.scSelectStatus = function (el) {
        document.querySelectorAll('.sc-status-opt').forEach(function (o) {
            o.classList.remove('selected');
        });
        el.classList.add('selected');
        window.scActiveStatus = el.dataset.value;
    };

    // ── Quick-set (✓ / ✗ buttons on empty cells) ──────────
    window.scQuickSet = function (memberId, date, status, cell) {
        sendUpdate(memberId, date, status, '', function (ok) {
            if (ok) {
                applyCellUI(cell, memberId, date, status, '');
                showToast(STATUS_LABELS[status] + ' saved');
            } else {
                showToast('Save failed', true);
            }
        });
    };

    // ── Save from modal ────────────────────────────────────
    window.scSaveStatus = function (status) {
        var notes = document.getElementById('sc-modal-notes').value;
        var saving = document.getElementById('sc-saving');
        saving.style.display = 'inline';

        sendUpdate(scActiveMemberId, scActiveDate, status, notes, function (ok) {
            saving.style.display = 'none';
            if (ok) {
                applyCellUI(scActiveCell, scActiveMemberId, scActiveDate, status, notes);
                scCloseModal();
                showToast(status ? STATUS_LABELS[status] + ' saved' : 'Cleared');
            } else {
                showToast('Save failed', true);
            }
        });
    };

    // ── AJAX helper ────────────────────────────────────────
    function sendUpdate(memberId, date, status, notes, callback) {
        var formData = new FormData();
        formData.append('member_id', memberId);
        formData.append('date',      date);
        formData.append('status',    status);
        formData.append('notes',     notes);

        fetch('<?= BASE_URL ?>staff_calendar/update_status', {
            method: 'POST',
            body:   formData,
        })
        .then(function (r) { return r.json(); })
        .then(function (json) { callback(json.success === true); })
        .catch(function ()   { callback(false); });
    }

    // ── Apply new status to cell DOM ───────────────────────
    function applyCellUI(cell, memberId, date, status, notes) {
        var emptyColor = cell.dataset.weekend === '1' ? '#f5f0fc' : '#ffffff';
        var color = status ? STATUS_COLORS[status] : emptyColor;
        cell.style.background = color;
        cell.dataset.status   = status;
        cell.dataset.notes    = notes;

        // Update tooltip
        var tip = date;
        if (status)  tip += ' — ' + (STATUS_LABELS[status] || status);
        if (notes)   tip += ': ' + notes;
        cell.title = tip;

        // Rebuild quick-action area
        var quick = cell.querySelector('.sc-quick');
        if (status) {
            quick.innerHTML = '<div class="sc-status-dot" style="background:' + color + ';border:2px solid rgba(0,0,0,0.2)"></div>';
        } else {
            quick.innerHTML =
                '<button class="sc-q-btn sc-q-work" title="Mark working" ' +
                'onclick="event.stopPropagation(); scQuickSet(' + memberId + ', \'' + date + '\', \'working\', this.closest(\'td\'))">&#10003;</button>' +
                '<button class="sc-q-btn sc-q-off" title="Mark day off" ' +
                'onclick="event.stopPropagation(); scQuickSet(' + memberId + ', \'' + date + '\', \'dayoff\', this.closest(\'td\'))">&#10007;</button>';
        }

        // Recompute that row's totals cell
        refreshRowTotal(cell.closest('tr'));
    }

    // ── Row total recalculation ────────────────────────────
    function refreshRowTotal(row) {
        var cells = row.querySelectorAll('td.sc-cell');
        var total = 0;
        cells.forEach(function (c) {
            if (c.dataset.status === 'working') total += 1;
            if (c.dataset.status === 'halfday') total += 0.5;
        });
        var totalCell = row.querySelector('td.sc-total-col');
        if (totalCell) {
            totalCell.textContent = total > 0
                ? (total === Math.floor(total) ? total.toString() : total.toFixed(1))
                : '—';
        }
    }

    // ── Toast notification ─────────────────────────────────
    function showToast(msg, isError) {
        var toast = document.getElementById('sc-toast');
        toast.textContent = msg;
        toast.className   = 'show' + (isError ? ' error' : '');
        clearTimeout(toast._timer);
        toast._timer = setTimeout(function () {
            toast.classList.remove('show');
        }, 2200);
    }

    // ── ESC closes modal ───────────────────────────────────
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') scCloseModal();
    });

})();
</script>

</div><!-- /#sc-wrapper -->
</div><!-- /#form-container -->

