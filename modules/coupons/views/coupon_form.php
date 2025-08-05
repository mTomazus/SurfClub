<div id="error-msg"></div>
<?php
    $form_attr = [
        'mx-post' => 'coupons/submit_coupon/' . $id ,
        'mx-on-success' => '#coupons-container',
        'mx-on-error' => '#error-msg',
        'mx-close-on-success' => 'true',
        'mx-target' => '#information',
        'class' => 'highlight-errors',
        'style' => 'grid-template-columns:1fr 1fr;',
        'id' => 'coupon-form'
    ];
    echo form_open('#', $form_attr);
    echo validation_errors();
?>
    <div class="input-group" style="grid-column:1 / span 2"><label for="">Name</label>
    <?= validation_errors('name') ?>
    <input type="text" name="name"  value="<?= $name ?>"placeholder="Enter Name here..." autocomplete="off"></div>
    <div class="input-group"><label for="">Type</label>
    <?= validation_errors('coupon_type') ?>
    <input type="text" name="coupon_type" value="<?= $coupon_type ?>" placeholder="Enter Coupon type here..." autocomplete="off"></div>
    <div class="input-group"><label for="">Price</label>
    <?= validation_errors('price') ?>
    <input type="number" name="price" value="<?= $price ?>" placeholder="Enter Coupon price here..." autocomplete="off"></div>
    <div class="input-group"><label for="">Phone</label>
    <?= validation_errors('phone') ?>
    <input type="tel" name="phone" value="<?= $phone ?>" placeholder="Enter phone number here..." autocomplete="off"></div>
    <div class="input-group"><label for="">Email</label>
    <?= validation_errors('email') ?>
    <input type="email" name="email" value="<?= $email ?>" placeholder="Enter Email here..." autocomplete="off"></div>
<?php
    echo form_hidden('status', 'active');
    echo form_submit('submit', 'Submit', array('class' => "success", "style" => "grid-column: 1 / -1;justify-self: right;margin-top: 1rem;"));
    echo form_close();
?>