<section class="container contact-us">
    <h1>Get In Touch</h1>
    <?php
    $form_attr['id'] = 'contact-form';
    $form_attr['class'] = 'highlight-errors container';
    echo form_open($form_location, $form_attr);

    $input_attr['placeholder'] = 'Įveskite savo vardą';
    $input_attr['autocomplete'] = 'off';
    validation_errors('name');
    echo form_input('name', $name, $input_attr);

    $input_attr['placeholder'] = 'Įveskite savo tel. numerį';
    validation_errors('phone');
    echo form_input('phone', $phone, $input_attr);

    $input_attr['placeholder'] = 'Įveskite savo el. paštą';
    validation_errors('email_address');
    echo form_email('email_address', $email_address, $input_attr);

    $input_attr['placeholder'] = 'Čia rašykite savo užklausimą ar žinutę';
    $input_attr['rows'] = 3;
    echo form_textarea('message', $message, $input_attr);

    echo form_submit('submit', 'Submit');
    flashdata();
    echo form_close();
    ?>
</section>

<style>
    .contact-us {
        padding-bottom: 120px;
    }
</style>