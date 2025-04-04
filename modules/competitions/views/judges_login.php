<div class="container-xs" style="color:white;font-weight:900">

    <?= flashdata() ?>

    <h1 class="text-center">Judge's Login</h1>
    
    <?= validation_errors() ?>

    <p>Please enter your username and password then click 'Submit'</p>
    <?php
    echo form_open('competitions/submit_login');
    echo '<div class="flex-row">';
    echo form_label('Username');
    $attr['placeholder'] = 'Enter your username here...';
    $attr['autocomplete'] = 'off';
    echo form_input('username', $username, $attr);
    echo '</div>';
    echo '<div class="flex-row">';
    echo form_label('Password');
    $attr['placeholder'] = 'Enter your password here...';
    echo form_password('password', '', $attr);
    echo '</div>';
    echo '<div class="flex-row">';
    echo form_checkbox('remember');
    echo 'remember me';
    echo '</div>';
    echo '<div class="flex-row">';
    echo form_submit('submit', 'Submit');
    echo '</div>';
    echo form_close();
    ?>
</div>
