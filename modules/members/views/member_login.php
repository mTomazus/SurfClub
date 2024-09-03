<div class="container-xs">
    <h1>Member Login</h1>
    
    <?= validation_errors() ?>

    <p>Please enter your username and password then click 'Submit'</p>
    <?php
    echo form_open('members/submit_login');
    
    echo form_label('Username');
    $attr['placeholder'] = 'Enter your username here...';
    $attr['autocomplete'] = 'off';
    echo form_input('username', $username, $attr);

    echo form_label('Password');
    $attr['placeholder'] = 'Enter your password here...';
    echo form_password('password', '', $attr);

    echo '<div>';
    echo form_checkbox('remember');
    echo 'remember me';
    echo '</div>';

    echo form_submit('submit', 'Submit');
    echo anchor(BASE_URL, 'Cancel', array('class' => 'button alt'));
    echo anchor('members/create_account', 'Create', array('class' => 'button success'));

    echo form_close();
    ?>
</div>