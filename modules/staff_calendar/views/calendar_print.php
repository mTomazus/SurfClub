<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Staff Calendar — <?= htmlspecialchars($month_name) ?> <?= $year ?></title>
<style>
/* ============================================================
   PRINT-ONLY calendar page
   window.print() is called automatically on page load.
   The admin nav, header, etc. are not included — this view is
   rendered without a template wrapper (Staff_calendar::export_pdf
   calls $this->view(...) directly).
   ============================================================ */
* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10px;
    color: #111;
    background: #fff;
}

h1 {
    text-align: center;
    font-size: 14px;
    margin: 10px 0 6px;
}

.print-meta {
    text-align: center;
    font-size: 9px;
    color: #555;
    margin-bottom: 10px;
}

table {
    width: 100%;
    border-collapse: collapse;
    page-break-inside: avoid;
}

th, td {
    border: 1px solid #bbb;
    text-align: center;
    vertical-align: middle;
    padding: 2px 1px;
    line-height: 1.2;
}

th {
    background: #2c3e50;
    color: #fff;
    font-size: 8px;
}

th:first-child {
    text-align: left;
    padding-left: 4px;
    min-width: 90px;
}

td:first-child {
    text-align: left;
    padding-left: 4px;
    background: #f9f9f9;
    font-weight: 600;
    font-size: 9px;
    white-space: nowrap;
}

.weekend { background: #3d556b; }

.cell-working { background: #2ecc71; }
.cell-halfday  { background: #f39c12; }
.cell-dayoff   { background: #95a5a6; }
.cell-sick     { background: #e74c3c; }

.total-col {
    background: #ecf0f1;
    font-weight: 700;
    font-size: 9px;
}
th.total-col {
    background: #2c3e50;
    color: #f1c40f;
}

.legend {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-top: 8px;
    font-size: 8px;
}
.legend-item {
    display: flex;
    align-items: center;
    gap: 3px;
}
.legend-dot {
    width: 10px;
    height: 10px;
    border-radius: 2px;
    border: 1px solid rgba(0,0,0,0.15);
    flex-shrink: 0;
}

@media screen {
    body { padding: 20px; }
    .no-print-btn {
        display: block;
        margin: 10px auto;
        padding: 8px 20px;
        background: #2c3e50;
        color: #fff;
        border: none;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
    }
}

@media print {
    .no-print-btn { display: none !important; }
    @page { margin: 10mm; size: landscape; }
}
</style>
</head>
<body>

<button class="no-print-btn" onclick="window.close()">Close</button>

<h1>Staff Calendar — <?= htmlspecialchars($month_name) ?> <?= $year ?></h1>
<div class="print-meta">Printed <?= date('Y-m-d H:i') ?></div>

<?php
$status_colors_print = [
    'working' => 'cell-working',
    'halfday' => 'cell-halfday',
    'dayoff'  => 'cell-dayoff',
    'sick'    => 'cell-sick',
];
?>

<table>
    <thead>
        <tr>
            <th>Staff Member</th>
            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $dow = (int) date('N', mktime(0,0,0,$month,$d,$year));
                $is_weekend = $dow >= 6;
                $day_abbr   = substr(date('D', mktime(0,0,0,$month,$d,$year)), 0, 2);
            ?>
            <th class="<?= $is_weekend ? 'weekend' : '' ?>"><?= $d ?><br><?= $day_abbr ?></th>
            <?php endfor; ?>
            <th class="total-col">Days</th>
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
                    ? ' <span style="font-weight:400;color:#666">(' . htmlspecialchars($member->role) . ')</span>'
                    : '';
                echo $display_name . $role_display;
                ?>
            </td>
            <?php for ($d = 1; $d <= $days_in_month; $d++):
                $date_str = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($d, 2, '0', STR_PAD_LEFT);
                $key      = $member->id . '|' . $date_str;
                $entry    = $schedule_index[$key] ?? null;
                $status   = $entry ? $entry->status : '';
                $css_class = $status ? $status_colors_print[$status] : '';

                if ($status === 'working') $total += 1.0;
                if ($status === 'halfday') $total += 0.5;
            ?>
            <td class="<?= $css_class ?>" title="<?= $entry && $entry->notes ? htmlspecialchars($entry->notes) : '' ?>"></td>
            <?php endfor; ?>
            <td class="total-col">
                <?= $total > 0 ? ($total == floor($total) ? (int)$total : number_format($total, 1)) : '' ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<!-- Legend -->
<div class="legend">
    <div class="legend-item"><div class="legend-dot" style="background:#2ecc71"></div> Working</div>
    <div class="legend-item"><div class="legend-dot" style="background:#f39c12"></div> Half Day</div>
    <div class="legend-item"><div class="legend-dot" style="background:#95a5a6"></div> Day Off</div>
    <div class="legend-item"><div class="legend-dot" style="background:#e74c3c"></div> Sick</div>
</div>

<script>
// Auto-trigger print dialog when this standalone page loads
window.addEventListener('load', function () {
    window.print();
});
</script>
</body>
</html>
