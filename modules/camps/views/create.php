<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        Camp Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Name');
        echo form_input('name', $name, array("placeholder" => "Enter Name"));
        echo form_label('Phone');
        echo form_input('phone', $phone, array("placeholder" => "Enter Phone"));
        echo form_label('Email');
        echo form_email('email', $email, array("placeholder" => "Enter Email"));
        echo form_label('Pamaina');
        echo form_input('pamaina', $pamaina, array("placeholder" => "Enter Pamaina"));
        echo form_label('Age');
        echo form_input('age', $age, array("placeholder" => "Enter Age"));
        echo form_hidden('status', 'initial');
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>