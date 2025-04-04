<div id="form-table" class="container-xs" style="color:white;font-weight:900">
    <div style="width:100%;">

        <h2>Create New Judge</h2>
        <div id="response"></div>

    <?php

        $form_attr = [
            'mx-post' => 'competitions/submit_create_judge',
            'mx-target' => '#response'
          ];
        echo form_open('#', $form_attr);

        echo form_label('Full Name');
        $attr['placeholder'] = 'Enter your full name here...';
        $attr['autocomplete'] = 'off';
        echo form_input('name', $name, $attr);

        echo form_label('Username');
        $attr['placeholder'] = 'Enter your username here...';
        $attr['autocomplete'] = 'off';
        echo form_input('username', $username, $attr);

        echo form_label('Password');
        $attr['placeholder'] = 'Enter your password here...';
        echo form_password('password', '', $attr);

        echo form_label('Repeat Password');
        $attr['placeholder'] = 'Repeat your password here...';
        echo form_password('repeat_password', '', $attr);

        echo form_submit('submit', 'Submit');
        echo form_close();

    ?>
        
    </div>
</div>