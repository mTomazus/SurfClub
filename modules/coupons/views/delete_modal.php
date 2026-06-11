<?php
$form_attr = [
    'mx-post' => 'coupons/submit_delete_coupon/'.$update_id,
    'mx-on-success' => '#coupons-container',
    'mx-close-on-success' => 'true',
    'mx-target' => '#information',
    'id' => 'coupon-delete'
];
echo form_open('#', $form_attr) ?>
<p class="cu-confirm-title">Delete this coupon?</p>
<p class="cu-confirm-text">Coupon <strong><?= date('Y') . '-' . (int) $update_id ?></strong> will be permanently removed. This cannot be undone.</p>
<div class="cu-modal-btns">
<?php
echo form_button('close', 'Cancel', ['class' => 'cu-btn-cancel', 'onclick' => 'closeModal()']);
echo form_submit('submit', 'Yes - Delete Now', ['class' => 'cu-btn-delete']);
echo '</div>';
echo form_close();
?>
