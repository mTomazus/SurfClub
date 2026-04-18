<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Gallery Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('year');
        echo form_input('year', $year ?? date('Y'), array("placeholder" => "e.g. 2025", "type" => "number"));
        echo form_label('pamaina');
        echo form_input('pamaina', $pamaina ?? '', array("placeholder" => "Session number e.g. 1", "type" => "number"));
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>
