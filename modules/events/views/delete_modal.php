<?php
$form_attr = [
    'mx-post' => 'events/submit_delete_event/'.$update_id,
    'mx-on-success' => '#events-container',
    'mx-close-on-success' => 'true',
    'mx-target' => '#information',
    'style' => 'grid-template-columns: 1fr;padding: 1rem 0;',
    'id' => 'event-delete-form'
];
echo form_open('#', $form_attr) ?>
<p class="text-center xl">Are you sure?</p>
<p>You are about to delete an Event.  This cannot be undone.  Do you really want to do this?</p> 
<?php
$attr_close = array( 
    "class" => "alt",
    "onclick" => "closeModal()"
);
echo '<p style="display: flex;justify-content: space-around;">'.form_button('close', 'Cancel', $attr_close);
echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
echo form_close();
?>