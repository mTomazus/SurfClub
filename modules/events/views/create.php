<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Event Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('title');
        echo form_input('title', $title, array("placeholder" => "Enter title"));
        echo form_label('description');
        echo form_textarea('description', $description, array("placeholder" => "Enter description"));
        echo form_label('email');
        echo form_email('email', $email, array("placeholder" => "Enter email"));
        echo form_label('phone');
        echo form_input('phone', $phone, array("placeholder" => "Enter phone"));
        echo form_label('start_time');
        $attr = array("class"=>"datetime-picker", "autocomplete"=>"off", "placeholder"=>"Select start_time");
        echo form_input('start_time', $start_time, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>