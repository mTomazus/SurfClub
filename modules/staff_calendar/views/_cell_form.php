<?php
/**
 * Modal body for setting a cell's status + notes (MX-built modal).
 * Expects: $member_id, $date_str, $member (object|false), $status, $notes
 *
 * Status is chosen via styled radio inputs (zero JS — :checked drives the
 * highlight). "Save" submits the form; "Clear" is its own MX trigger that
 * posts an empty status. Both close the modal and update the cell + stats.
 * All styling lives in calendar.php's stylesheet (.sc-opt, .sc-field, …).
 */
require_once APPPATH . 'modules/staff_calendar/views/_sc_helpers.php';

$sc_labels = sc_status_meta()['labels'];

$cell_id   = 'sc-cell-' . $member_id . '-' . $date_str;
$rowtot_id = 'sc-rowtotal-' . $member_id;

$oob = json_encode([
    ['select' => '#' . $rowtot_id, 'target' => '#' . $rowtot_id, 'swap' => 'outerHTML'],
    ['select' => '#stat-panel',    'target' => '#stat-panel',    'swap' => 'innerHTML'],
]);

$member_name = 'Staff';
if ($member) {
    $member_name = !empty($member->full_name) ? $member->full_name : ($member->username ?? 'Staff');
}
$date_label = date('l, M j', strtotime($date_str));

$form_attr = [
    'mx-post'             => 'staff_calendar/set_status/' . $member_id . '/' . $date_str,
    'mx-target'           => '#' . $cell_id,
    'mx-swap'             => 'outerHTML',
    'mx-select'           => '#' . $cell_id,
    'mx-select-oob'       => $oob,
    'mx-close-on-success' => 'true',
];
?>
<div class="sc-modal-body">
    <div class="sc-modal-who">
        <span class="sc-ava sc-ava--lg" aria-hidden="true"><?= htmlspecialchars(sc_initials($member_name)) ?></span>
        <div>
            <strong><?= htmlspecialchars($member_name) ?></strong>
            <span><?= $date_label ?></span>
        </div>
    </div>

    <?= form_open('#', $form_attr) ?>
        <div class="sc-status-grid" role="radiogroup" aria-label="Status">
            <?php foreach ($sc_labels as $slug => $label): ?>
            <label class="sc-opt sc-opt--<?= $slug ?>">
                <input type="radio" name="status" value="<?= $slug ?>" <?= $status === $slug ? 'checked' : '' ?>>
                <span class="sc-opt__card"><i></i><?= $label ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="sc-field">
            <label for="sc-modal-notes">Notes (optional)</label>
            <textarea id="sc-modal-notes" name="notes" placeholder="e.g. morning shift only, covering for Egle&hellip;"><?= htmlspecialchars($notes) ?></textarea>
        </div>

        <div class="sc-modal-btns">
            <button type="button" class="sc-btn-clear"
                    mx-post="staff_calendar/set_status/<?= $member_id ?>/<?= $date_str ?>"
                    mx-vals='{"status":""}'
                    mx-target="#<?= $cell_id ?>" mx-swap="outerHTML" mx-select="#<?= $cell_id ?>"
                    mx-select-oob='<?= htmlspecialchars($oob, ENT_QUOTES) ?>'
                    mx-close-on-success="true">Clear day</button>
            <button type="button" class="sc-btn-cancel" onclick="closeModal()">Cancel</button>
            <?= form_submit('submit', 'Save', ['class' => 'sc-btn-save']) ?>
        </div>
    <?= form_close() ?>
</div>
