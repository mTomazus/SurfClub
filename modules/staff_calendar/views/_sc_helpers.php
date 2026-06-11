<?php
/**
 * Shared render helpers for the Staff Calendar.
 *
 * Defined as plain functions (not $this->view partials) so they work in BOTH
 * contexts: the template-rendered calendar.php (where $this is NOT the
 * controller) and the controller's MX response methods. Guarded against
 * double-definition because this file is required from several places.
 *
 * All colors live in the stylesheet (calendar.php) — helpers emit semantic
 * classes only (.sc-chip--working etc.), never inline color values.
 */

if (!function_exists('sc_status_meta')) {
    function sc_status_meta(): array {
        return [
            'icons'  => ['working' => '&#10003;', 'halfday' => '&#189;',   'dayoff' => '&#10005;', 'sick' => '!'],
            'labels' => ['working' => 'Working',  'halfday' => 'Half Day', 'dayoff' => 'Day Off',  'sick' => 'Sick'],
        ];
    }
}

if (!function_exists('sc_initials')) {
    /** Monogram initials: first letter of first + last word of the name. */
    function sc_initials(string $name): string {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $ini   = mb_strtoupper(mb_substr($parts[0] ?? '', 0, 1));
        if (count($parts) > 1) {
            $ini .= mb_strtoupper(mb_substr(end($parts), 0, 1));
        }
        return $ini !== '' ? $ini : '?';
    }
}

if (!function_exists('sc_render_cell')) {
    /**
     * Render a single calendar cell (<td>).
     * @param array $d Keys: member_id, date_str, status, notes, is_wkend
     */
    function sc_render_cell(array $d): string {
        $meta   = sc_status_meta();
        $icons  = $meta['icons'];
        $labels = $meta['labels'];

        $member_id = (int) $d['member_id'];
        $date_str  = $d['date_str'];
        $status    = $d['status'] ?? '';
        $notes     = $d['notes']  ?? '';
        $is_wkend  = !empty($d['is_wkend']);
        $is_today  = ($date_str === date('Y-m-d'));

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
            'width'           => '360px',
            'modalHeading'    => 'Set Status',
            'showCloseButton' => 'true',
        ]), ENT_QUOTES);
        $modal = 'mx-get="staff_calendar/cell_form/' . $member_id . '/' . $date_str . '" '
               . "mx-build-modal='" . $modal_cfg . "'";

        $tip = $date_str . ($status ? ' — ' . $labels[$status] : '') . ($notes ? ': ' . $notes : '');

        $td_class = 'sc-cell'
                  . ($is_wkend ? ' sc-wkend-cell' : '')
                  . ($is_today ? ' sc-today-cell' : '');

        ob_start();
        ?>
<td id="<?= $cell_id ?>" class="<?= $td_class ?>" title="<?= htmlspecialchars($tip) ?>">
    <div class="sc-quick">
        <?php if ($status): ?>
            <button type="button" class="sc-chip-btn" <?= $modal ?>
                    title="Edit / add notes" aria-label="Edit <?= $labels[$status] ?> on <?= $date_str ?>">
                <span class="sc-chip sc-chip--<?= $status ?>"><?= $icons[$status] ?><?php if ($notes !== ''): ?><i class="sc-note-dot"></i><?php endif; ?></span>
            </button>
            <button type="button" class="sc-x" title="Clear" aria-label="Clear status on <?= $date_str ?>"
                    <?= $mx ?> mx-vals='{"status":""}'>&#215;</button>
        <?php else: ?>
            <span class="sc-dot" aria-hidden="true"></span>
            <div class="sc-acts">
                <div class="sc-acts__row">
                    <button type="button" class="sc-act sc-act--work" title="Mark working"
                            aria-label="Mark working on <?= $date_str ?>" <?= $mx ?> mx-vals='{"status":"working"}'>&#10003;</button>
                    <button type="button" class="sc-act sc-act--off" title="Mark day off"
                            aria-label="Mark day off on <?= $date_str ?>" <?= $mx ?> mx-vals='{"status":"dayoff"}'>&#10005;</button>
                </div>
                <button type="button" class="sc-act sc-act--more" title="More options"
                        aria-label="More options for <?= $date_str ?>" <?= $modal ?>>&#8943;</button>
            </div>
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
            : '&mdash;';
        return '<td id="sc-rowtotal-' . $member_id . '" class="sc-total-col'
             . ($total > 0 ? '' : ' sc-total-col--zero') . '">' . $label . '</td>';
    }
}

if (!function_exists('sc_render_stat_panel')) {
    /** @param array $s Keys: total_staff, working, halfday, dayoff, sick, coverage */
    function sc_render_stat_panel(array $s): string {
        ob_start();
        ?>
<div id="stat-panel">
    <div class="sc-stats">
        <div class="sc-stat sc-stat--lead">
            <span class="sc-stat__label">Month coverage</span>
            <span class="sc-stat__num"><?= (int) $s['coverage'] ?><small>%</small></span>
            <span class="sc-stat__bar"><i style="width:<?= max(0, min(100, (int) $s['coverage'])) ?>%"></i></span>
        </div>
        <div class="sc-stat"><span class="sc-stat__num"><?= (int) $s['total_staff'] ?></span><span class="sc-stat__label">Staff</span></div>
        <div class="sc-stat sc-stat--working"><span class="sc-stat__num"><?= (int) $s['working'] ?></span><span class="sc-stat__label">Working</span></div>
        <div class="sc-stat sc-stat--halfday"><span class="sc-stat__num"><?= (int) $s['halfday'] ?></span><span class="sc-stat__label">Half days</span></div>
        <div class="sc-stat sc-stat--dayoff"><span class="sc-stat__num"><?= (int) $s['dayoff'] ?></span><span class="sc-stat__label">Days off</span></div>
        <div class="sc-stat sc-stat--sick"><span class="sc-stat__num"><?= (int) $s['sick'] ?></span><span class="sc-stat__label">Sick</span></div>
    </div>
</div>
        <?php
        return ob_get_clean();
    }
}
