<?php
/**
 * Modal body for setting a cell's status + notes (MX-built modal).
 * Expects: $member_id, $date_str, $member (object|false), $status, $notes
 *
 * Status is chosen via styled radio inputs (zero JS — :checked drives the
 * highlight). "Save" submits the form; "Clear" is its own MX trigger that
 * posts an empty status. Both close the modal and update the cell + stats.
 */
$sc_colors = ['working' => '#27ae60', 'halfday' => '#e67e22', 'dayoff' => '#3498db', 'sick' => '#e74c3c'];
$sc_labels = ['working' => 'Working', 'halfday' => 'Half Day', 'dayoff' => 'Day Off', 'sick' => 'Sick'];

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
    <p class="sc-modal-meta"><?= htmlspecialchars($member_name) ?> &middot; <?= htmlspecialchars($date_str) ?></p>

    <?= form_open('#', $form_attr) ?>
        <div class="sc-status-grid">
            <?php foreach ($sc_colors as $slug => $color): ?>
            <label class="sc-opt">
                <input type="radio" name="status" value="<?= $slug ?>" <?= $status === $slug ? 'checked' : '' ?>>
                <span style="background:<?= $color ?>"><?= $sc_labels[$slug] ?></span>
            </label>
            <?php endforeach; ?>
        </div>

        <label class="sc-notes-label" for="sc-modal-notes">Notes (optional)</label>
        <textarea id="sc-modal-notes" name="notes" placeholder="Add a note…"><?= htmlspecialchars($notes) ?></textarea>

        <div class="sc-modal-btns">
            <button type="button" class="sc-btn-clear"
                    mx-post="staff_calendar/set_status/<?= $member_id ?>/<?= $date_str ?>"
                    mx-vals='{"status":""}'
                    mx-target="#<?= $cell_id ?>" mx-swap="outerHTML" mx-select="#<?= $cell_id ?>"
                    mx-select-oob='<?= htmlspecialchars($oob, ENT_QUOTES) ?>'
                    mx-close-on-success="true">Clear</button>
            <button type="button" class="sc-btn-cancel" onclick="closeModal()">Cancel</button>
            <?= form_submit('submit', 'Save', ['class' => 'sc-btn-save']) ?>
        </div>
    <?= form_close() ?>
</div>
