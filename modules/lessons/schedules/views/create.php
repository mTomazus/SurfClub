<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Lesson Schedule Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Lesson');

        // Using array_column to extract 'id' and 'name' from $lessons array.
        $lessons = array_combine(array_column($lessons, 'id'), array_column($lessons, 'name'));
        $name = 'lesson_id';
        echo form_dropdown($name, $lessons);
        
        echo form_label('Date');
        $attr = array("class"=>"date-picker", "autocomplete"=>"off", "placeholder"=>"Select date");
        echo form_input('date', $date, $attr);
        echo form_label('Start Time');
        $attr = array("class"=>"time-picker", "autocomplete"=>"off", "placeholder"=>"Select start_time");
        echo form_input('start_time', $start_time, $attr);
        echo form_label('Available Places');
        echo form_number('available_places', $available_places, array("placeholder" => "Enter available_places"));
        echo form_label('Reserved Places');
        echo form_number('reserved_places', $reserved_places, array("placeholder" => "Enter reserved_places"));
        echo form_label('Associated Lesson');
        echo form_dropdown('lessons_id', $lessons_options, $lessons_id);
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>

<style>
    select {
        height: calc(1.2em + 20px);
    }
</style>