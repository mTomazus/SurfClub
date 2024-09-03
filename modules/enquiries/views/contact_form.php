<section class="container contact-us">
    <h1>Get In Touch</h1>
    <?php
    validation_errors();
    echo form_open($form_location);
    echo form_label('Your Name');
    $input_attr['placeholder'] = 'Enter your name here';
    $input_attr['autocomplete'] = 'off';
    echo form_input('name', $name, $input_attr);

    echo form_label('Your Email Address');
    $input_attr['placeholder'] = 'Enter your email address here';
    echo form_email('email_address', $email_address, $input_attr);

    echo form_label('Your Message');
    $input_attr['placeholder'] = 'Use this space to enter your message';
    $input_attr['rows'] = 5;
    echo form_textarea('message', $message, $input_attr);

    echo '<p>Prove you\'re human by answering the question below!</p>';
    echo form_label($question);
    echo form_dropdown('answer', $options, $answer);

    echo form_submit('submit', 'Submit');
    echo anchor(BASE_URL, 'Cancel', array('class' => 'button alt'));

    echo form_close();
    ?>
</section>

<style>
    .contact-us {
        padding-bottom: 120px;
    }
</style>