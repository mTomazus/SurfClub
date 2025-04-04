<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Product Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('name');
        echo form_input('name', $name, array("placeholder" => "Enter name"));
        echo form_label('description');
        echo form_textarea('description', $description, array("placeholder" => "Enter description"));
        echo form_label('price');
        echo form_input('price', $price, array("placeholder" => "Enter price"));
        echo form_label('stock');
        echo form_number('stock', $stock, array("placeholder" => "Enter stock"));
        echo form_label('status');
        echo form_input('status', $status, array("placeholder" => "Enter status"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>