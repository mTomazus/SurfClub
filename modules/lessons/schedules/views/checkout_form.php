<div id="form" class="container">
    <div class="d-flex flex-column mb-1">
        <h2 class="text-center"><?= out($lessons->name) ?></h2>
        <div class="text-center" style="color:#555; font-weight:900; border-bottom:1px solid"><?= date('Y-m-d',  strtotime($date)) ?>  <?= date('H:i', strtotime($start_time)) ?></div>
    </div>
    <div class="error-message"><?= flashdata() ?></div>
    <?php
        $form_attr = [
            'mx-post' => 'lessons-registrations/process_order',
            'mx-target' => 'form',
            'mx-redirect-on-success' => 'true',
            'mx-on-error' => '.error-message',
            'class' => 'highlight-errors'
        ];
        $cancel_url = $_SERVER['HTTP_REFERER'];
        echo form_open('#', $form_attr);
        echo '<div class="progress-bar"><div class="steps active" data-title="informacija"></div>
        <div class="steps" data-title="apmokėjimas"></div></div>';

        echo form_hidden('lesson_id', $id);
        echo form_label('Vardas Pavardė*');
        echo validation_errors();
        $first_name_attr = ['placeholder' => 'Įveskite savo Vardą ir Pavardę...'];
        echo form_input('customer_name', '', $first_name_attr);
        echo form_label('El. paštas*');
        $email_attr = ['placeholder' => 'Įveskite savo el.paštą čia...'];
        echo form_input('email', '', $email_attr);
        echo form_label('Telefono numeris*');
        $phone_attr = ['placeholder' => 'Na ir telefono numerį...'];
        echo form_input('phone', '', $phone_attr);
        echo '<div class="d-flex justify-around mt-1 mb-1">';
        echo anchor($cancel_url, 'Atgal', array('class' => 'button alt'));
        echo form_submit('submit', 'Registruoti', array('class' => 'button'));
        echo '</div>';
        echo form_close();
    ?>
    <p>*SVARBU* Kad registracija būtų patvirtinta, reikės apmokėti pamoką.</p>
</div>
<style>
    .validation-error-report {
        display: none;
        position: absolute;
        top: 11px;
    }
    .modal .form-field-validation-error, .form-field-validation-error {
        border: 1px #ff0000 solid !important;
    }
</style>
