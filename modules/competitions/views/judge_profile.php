<div id="form-container" class="container" style="color:white;font-weight:900">
    <h2 style="text-transform:capitalize"><?= $role ?> Profile</h2>
    <div id="response"></div>
    <?php
        $form_attr = [
            'mx-post' => 'competitions/submit_edit_judge/' . $id,
            'mx-target' => '#response'
        ];
        echo form_open('#', $form_attr);

        echo form_label('Full Name');
        $attr['placeholder'] = 'Enter your full name here...';
        $attr['autocomplete'] = 'off';
        echo form_input('name', $name, $attr);

        if ($role === 'organizer') {
            echo form_label('Organization');
            $attr['placeholder'] = 'Enter your organization name here...';
            $attr['autocomplete'] = 'off';
            echo form_input('organization', $organization, $attr);
            echo form_label('Email');
            $attr['placeholder'] = 'Enter your email here...';
            $attr['autocomplete'] = 'off';
            echo form_input('email', $email, $attr);
            echo form_label('Phone');
            $attr['placeholder'] = 'Enter your phone number here...';
            $attr['autocomplete'] = 'off';
            echo form_input('phone', $phone, $attr);
        }

        echo form_label('Username');
        $attr['placeholder'] = 'Enter your username here...';
        $attr['autocomplete'] = 'off';
        echo form_input('username', $username, $attr);

        echo '<div style="margin-top:2rem;display:flex;grid-column: 1 / -1;justify-content: center;gap: 2rem;">';
        echo form_submit('submit', 'Save Changes', ['class' => 'button']);
        echo '<button type="button" class="button alt" onclick="openChangePasswordModal()">Change Password</button>';
        echo '</div>';

        echo form_close();
    ?>
</div>

<!-- Change Password Modal (hidden by default) -->
<div id="change-password-modal" class="modal" style="display:none;color:black;">
    <div class="modal-heading">Change Password</div>
    <div class="modal-body">
        <?php
        $pw_form_attr = [
            'mx-post' => 'competitions/submit_change_password/' . $id,
            'mx-target' => '#response'
        ];
        echo form_open('#', $pw_form_attr);

        echo form_label('New Password');
        echo form_password('new_password', '', ['placeholder' => 'Enter new password']);

        echo form_label('Repeat New Password');
        echo form_password('repeat_password', '', ['placeholder' => 'Repeat new password']);

        echo form_submit('submit', 'Update Password', ['class' => 'button']);
        echo '<button type="button" class="button alt" onclick="closeChangePasswordModal()">Cancel</button>';

        echo form_close();
        ?>
    </div>
</div>

<script>
function openChangePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'block';
}
function closeChangePasswordModal() {
    document.getElementById('change-password-modal').style.display = 'none';
}
</script>
