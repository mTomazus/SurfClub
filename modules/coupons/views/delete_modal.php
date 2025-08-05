<?php
$form_attr = [
    'mx-post' => 'coupons/submit_delete_coupon/'.$update_id,
    'mx-on-success' => '#coupons-container',
    'mx-close-on-success' => 'true',
    'mx-target' => '#information',
    'style' => 'grid-template-columns: 1fr;padding: 1rem 0;',
    'id' => 'coupon-delete'
];
echo form_open('#', $form_attr) ?>
<p class="text-center xl">Are you sure?</p>
<p>You are about to delete a Coupon.  This cannot be undone.  Do you really want to do this?</p> 
<?php
$attr_close = array( 
    "class" => "alt",
    "onclick" => "closeModal()"
);
echo '<p>'.form_button('close', 'Cancel', $attr_close);
echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
echo form_close();
?>