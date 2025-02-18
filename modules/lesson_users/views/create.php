<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Lesson_user Details
    </div>
    <div id="user-form" class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('name');
        echo form_input('name', $name, array("placeholder" => "Enter name"));
        echo form_label('email');
        echo form_email('email', $email, array("placeholder" => "Enter email"));
        echo form_label('phone');
        echo form_input('phone', $phone, array("placeholder" => "Enter phone"));
        echo form_label('registration_date');
        $attr = array("class"=>"datetime-picker", "autocomplete"=>"off", "placeholder"=>"Select registration_date");
        echo form_input('registration_date', $registration_date, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>