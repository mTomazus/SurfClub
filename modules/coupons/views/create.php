<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Coupon Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Coupon Type');
        echo form_input('coupon_type', $coupon_type, array("placeholder" => "Enter Coupon Type"));
        echo form_label('Price');
        echo form_input('price', $price, array("placeholder" => "Enter Price"));
        echo form_label('Phone');
        echo form_input('phone', $phone, array("placeholder" => "Enter Phone"));
        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name"));
        echo form_label('Date Formed');
        $attr = array("class"=>"in-the-future date-picker", "autocomplete"=>"off", "placeholder"=>"Select Date Formed");
        echo form_input('date_formed', $date_formed, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>