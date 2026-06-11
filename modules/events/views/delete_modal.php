<?php
$form_attr = [
    'mx-post' => 'events/submit_delete_event/'.$update_id,
    'mx-on-success' => '#events-container',
    'mx-close-on-success' => 'true',
    'mx-target' => '#information',
    'id' => 'event-delete-form'
];
echo form_open('#', $form_attr) ?>
<p class="ev-confirm-title">Delete this event?</p>
<p class="ev-confirm-text">The event will be permanently removed. This cannot be undone.</p>
<div class="ev-modal-btns">
<?php
echo form_button('close', 'Cancel', ['class' => 'ev-btn-cancel', 'onclick' => 'closeModal()']);
echo form_submit('submit', 'Yes - Delete Now', ['class' => 'ev-btn-delete']);
echo '</div>';
echo form_close();
?>
