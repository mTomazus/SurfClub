<?php
/**
 * Shared render helpers for the Staff Calendar.
 *
 * Defined as plain functions (not $this->view partials) so they work in BOTH
 * contexts: the template-rendered calendar.php (where $this is NOT the
 * controller) and the controller's MX response methods. Guarded against
 * double-definition because this file is required from several places.
 */

if (!function_exists('sc_status_meta')) {
    function sc_status_meta(): array {
        return [
            'colors' => ['working' => '#27ae60', 'halfday' => '#e67e22', 'dayoff' => '#3498db', 'sick' => '#e74c3c'],
            'icons'  => ['working' => '✓',        'halfday' => '½',        'dayoff' => '✕',        'sick' => '!'],
            'labels' => ['working' => 'Working',  'halfday' => 'Half Day', 'dayoff' => 'Day Off',  'sick' => 'Sick'],
        ];
    }
}

if (!function_exists('sc_render_cell')) {
    /**
     * Render a single calendar cell (<td>).
     * @param array $d Keys: member_id, date_str, status, notes, is_wkend
     */
    function sc_render_cell(array $d): string {
        $meta   = sc_status_meta();
        $colors = $meta['colors'];
        $icons  = $meta['icons'];
        $labels = $meta['labels'];

        $member_id = (int) $d['member_id'];
        $date_str  = $d['date_str'];
        $status    = $d['status'] ?? '';
        $notes     = $d['notes']  ?? '';
        $is_wkend  = !empty($d['is_wkend']);

        $cell_id   = 'sc-cell-' . $member_id . '-' . $date_str;
        $rowtot_id = 'sc-rowtotal-' . $member_id;

        // OOB: row total replaced outright; stat panel inner-swapped (keeps #stat-panel wrapper)
        $oob = htmlspecialchars(json_encode([
            ['select' => '#' . $rowtot_id, 'target' => '#' . $rowtot_id, 'swap' => 'outerHTML'],
            ['select' => '#stat-panel',    'target' => '#stat-panel',    'swap' => 'innerHTML'],
        ]), ENT_QUOTES);

        // Shared MX attrs for the quick-action buttons (only mx-vals differs)
        $mx = 'mx-post="staff_calendar/set_status/' . $member_id . '/' . $date_str . '" '
            . 'mx-target="#' . $cell_id . '" mx-swap="outerHTML" mx-select="#' . $cell_id . '" '
            . "mx-select-oob='" . $oob . "'";

        $modal_cfg = htmlspecialchars(json_encode([
            'id'              => 'sc-modal',
            'width'          => '360px',
            'modalHeading'    => 'Set Status',
            'showCloseButton' => 'true',
        ]), ENT_QUOTES);
        $modal = 'mx-get="staff_calendar/cell_form/' . $member_id . '/' . $date_str . '" '
               . "mx-build-modal='" . $modal_cfg . "'";

        $tip = $date_str . ($status ? ' — ' . $labels[$status] : '') . ($notes ? ': ' . $notes : '');

        ob_start();
        ?>
<td id="<?= $cell_id ?>" class="sc-cell<?= $is_wkend ? ' sc-weekend-cell' : '' ?>" title="<?= htmlspecialchars($tip) ?>">
    <div class="sc-quick">
        <?php if ($status): ?>
            <button type="button" class="sc-sq-btn" <?= $modal ?> title="Edit / add notes">
                <span class="sc-status-sq" style="background:<?= $colors[$status] ?>"><?= $icons[$status] ?></span>
            </button>
            <button type="button" class="sc-q-btn sc-q-clear" title="Clear" <?= $mx ?> mx-vals='{"status":""}'>&#215;</button>
        <?php else: ?>
            <div class="sc-q-row">
                <button type="button" class="sc-q-btn sc-q-work" title="Mark working" <?= $mx ?> mx-vals='{"status":"working"}'>&#10003;</button>
                <button type="button" class="sc-q-btn sc-q-off" title="Mark day off" <?= $mx ?> mx-vals='{"status":"dayoff"}'>&#10007;</button>
            </div>
            <button type="button" class="sc-q-more" title="More options" <?= $modal ?>>&#8943;</button>
        <?php endif; ?>
    </div>
</td>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('sc_render_row_total')) {
    function sc_render_row_total(int $member_id, float $total): string {
        $label = $total > 0
            ? ($total == floor($total) ? (string) (int) $total : number_format($total, 1))
            : '—';
        return '<td id="sc-rowtotal-' . $member_id . '" class="sc-total-col">' . $label . '</td>';
    }
}

if (!function_exists('sc_render_stat_panel')) {
    /** @param array $s Keys: total_staff, working, halfday, dayoff, sick, coverage */
    function sc_render_stat_panel(array $s): string {
        ob_start();
        ?>
<div id="stat-panel">
    <div class="stat-card"><span class="stat-count" style="color:var(--sc-text)"><?= (int) $s['total_staff'] ?></span><span class="stat-label">Staff</span></div>
    <div class="stat-card"><span class="stat-count" style="color:var(--clr-working)"><?= (int) $s['working'] ?></span><span class="stat-label">Working</span></div>
    <div class="stat-card"><span class="stat-count" style="color:var(--clr-halfday)"><?= (int) $s['halfday'] ?></span><span class="stat-label">Half Days</span></div>
    <div class="stat-card"><span class="stat-count" style="color:var(--clr-dayoff)"><?= (int) $s['dayoff'] ?></span><span class="stat-label">Days Off</span></div>
    <div class="stat-card"><span class="stat-count" style="color:var(--clr-sick)"><?= (int) $s['sick'] ?></span><span class="stat-label">Sick</span></div>
    <div class="stat-card"><span class="stat-count" style="color:var(--sc-text)"><?= (int) $s['coverage'] ?>%</span><span class="stat-label">Coverage</span></div>
</div>
        <?php
        return ob_get_clean();
    }
}
