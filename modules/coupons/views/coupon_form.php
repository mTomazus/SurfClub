<div id="error-msg"></div>
<?php
    $form_attr = [
        'mx-post' => 'coupons/submit_coupon/' . ($id ?? ''),
        'mx-on-success' => '#coupons-container',
        'mx-on-error' => '#error-msg',
        'mx-close-on-success' => 'true',
        'mx-target' => '#information',
        'class' => 'highlight-errors',
        'id' => 'coupon-form'
    ];
    echo form_open('#', $form_attr);
    echo validation_errors();
?>
    <div class="cu-form-grid">
        <div class="cu-field cu-field--full">
            <label for="cu-name">Name</label>
            <?= validation_errors('name') ?>
            <input type="text" id="cu-name" name="name" value="<?= out($name ?? '') ?>" placeholder="Recipient or buyer name" autocomplete="off">
        </div>
        <div class="cu-field">
            <label for="cu-type">Type</label>
            <?= validation_errors('coupon_type') ?>
            <input type="text" id="cu-type" name="coupon_type" value="<?= out($coupon_type ?? '') ?>" placeholder="e.g. 2 lessons" autocomplete="off">
        </div>
        <div class="cu-field">
            <label for="cu-price">Price (&euro;)</label>
            <?= validation_errors('price') ?>
            <input type="number" id="cu-price" name="price" value="<?= out($price ?? '') ?>" placeholder="e.g. 90" autocomplete="off">
        </div>
        <div class="cu-field">
            <label for="cu-phone">Phone</label>
            <?= validation_errors('phone') ?>
            <input type="tel" id="cu-phone" name="phone" value="<?= out($phone ?? '') ?>" placeholder="+370..." autocomplete="off">
        </div>
        <div class="cu-field">
            <label for="cu-email">Email</label>
            <?= validation_errors('email') ?>
            <input type="email" id="cu-email" name="email" value="<?= out($email ?? '') ?>" placeholder="name@example.com" autocomplete="off">
        </div>
    </div>
<?php
    echo form_hidden('status', $status ?? 'active');
    echo '<div class="cu-modal-btns">';
    echo form_button('close_btn', 'Cancel', ['class' => 'cu-btn-cancel', 'onclick' => 'closeModal()']);
    echo form_submit('submit', 'Save coupon', ['class' => 'cu-btn-save']);
    echo '</div>';
    echo form_close();
?>
