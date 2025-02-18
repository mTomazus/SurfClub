<div id="form">
    <div class="d-flex space-between mb-1 blink">
        <h2 style="margin:auto;"><?= out($lessons->name) ?></h2>
     </div>
    <div class="d-flex space-between mb-1">
        <h3><?= out($lessons->description) ?></h3>
     </div>
    <div class="row d-flex space-between" style="color:red; font-weight:900; border-bottom:1px solid black">
        <div>Pamokos Data:</div>
        <div><?= date('Y-m-d',  strtotime($date)) ?></div>
    </div>
    <div class="row  d-flex space-between">
        <div>Pradžios laikas:</div>
        <div><?= out($start_time) ?></div>
    </div>
    <div class="error-message"></div>
    <?php
        echo form_open('lesson_schedules/checkout', array('id'=>'checkout-form'));
        echo form_label('Vardas Pavardė');
        echo form_input('name', $name, array("placeholder" => "Įveskite savo Vardą Pavardę..."));
        echo form_label('Emailas');
        echo form_email('email', $email, array("placeholder" => "Įveskite savo el. paštą..."));
        echo form_label('Tel Nr.');
        echo form_input('phone', $phone, array("placeholder" => "Įveskite savo tel numerį..."));
        echo form_submit('submit', 'Apmokėti');
        echo form_close();
    ?>
</div>