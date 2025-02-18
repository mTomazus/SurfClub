<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Lesson Registration Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('user_id');
        echo form_number('user_id', $user_id, array("placeholder" => "Enter user_id"));
        echo form_label('schedule_id');
        echo form_number('schedule_id', $schedule_id, array("placeholder" => "Enter schedule_id"));
        echo form_label('registration_date');
        $attr = array("class"=>"datetime-picker", "autocomplete"=>"off", "placeholder"=>"Select registration_date");
        echo form_input('registration_date', $registration_date, $attr);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>