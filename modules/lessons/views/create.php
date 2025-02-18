<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Lesson Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('name');
        echo form_input('name', $name, array("placeholder" => "Enter name"));
        echo form_label('description');
        echo form_textarea('description', $description, array("placeholder" => "Enter description"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>