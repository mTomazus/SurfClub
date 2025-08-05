<div id="form-table">
    <div id="judge-create" mx-get="competitions/create_judge" mx-trigger="load" mx-select="#form-table" style="color:white;font-weight:900"">

        <h2 class="mb-0">Create New Judge</h2>
        <p class="mb-2 text-right">Fill in the form below to create a new judge.</p>
        <div id="response"></div>
        <?= flashdata() ?>

    <?php
        $user_info = Modules::run("competitions/_get_judge_info");

        $form_attr = [
            'mx-post' => 'competitions/submit_create_judge',
            'mx-target' => '#response',
            'mx-on-success' => '#judge-create',
            'mx-animate-success' => 'true'
          ];
        echo form_open('#', $form_attr);
        echo form_label('Choose Role');
        echo '<div class="flex-row" style="gap:1rem;margin:auto;">';
        echo form_radio('role', 'Judge', true, ['id' => 'role-judge', 'class' => 'judge-role', 'style' => 'display: none;']);
        echo form_label('Judge', ['for' => 'role-judge', 'style' => 'padding: 0.5rem;border:2px solid;border-radius: 5px;']);
        echo form_radio('role', 'Admin', false, ['id' => 'role-admin', 'class' => 'judge-role', 'style' => 'display: none;']);
        echo form_label('Admin', ['for' => 'role-admin', 'style' => 'padding: 0.5rem;border:2px solid;border-radius: 5px;']);
        echo '</div>';

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

        echo form_hidden('organizer', $user_info->id);

        echo form_submit('submit', 'Submit');
        echo form_close();

    ?>
        
    </div>
</div>
<div id="judge-table">
    <div class="show-judge" mx-get="competitions/create_judge" mx-trigger="load" mx-select="#judge-table">
        <h2 class="mb-0">Available Judges</h2>
        <?= flashdata() ?>
        <?php
        if (empty($rows)) {
            echo '<p style="text-align:right">No judges available</p>';
        } else {
            echo '<p class="mb-2" style="text-align:right">Click on the judge to edit or delete</p><div style="display: grid;grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 1rem;">';
            foreach ($rows as $judge) {
                $role = $judge->role;
                switch ($role) {
                    case 'admin':
                        $bgColor = 'royalblue';
                        $txtColor = 'white';
                        break;
                    default:
                        $bgColor = 'skyblue'; // fallback color
                        $txtColor = 'black';
                }
                echo '<div class="justify-center text-center" style="display: grid;grid-template-columns:1fr;border: 1px solid white;padding: 1rem;box-shadow: 0 0 10px white;">';
                echo '<h3 style="text-transform: uppercase;text-align: center;background:' . $bgColor . ';color:' . $txtColor . ';margin: 0;">' . $judge->name . '</h3>';
                echo '<div style="display: flex;justify-content: space-between;margin: 1rem 0.5rem 0;"><span>Username:</span><span>' . $judge->username . '</span></div>';
                echo '<div style="display: flex;justify-content: space-between;margin: 0 0.5rem;"><span>Judge id:</span><span>' . $judge->id . '</span></div>';
                echo '<div style="display: flex;justify-content: space-between;margin: 0 0.5rem;"><span>Role:</span><span>' . $judge->role . '</span></div>';
                echo '<div style="display: flex;justify-content: space-between;margin: 0 0.5rem 1rem;"><span>Created:</span><span>' . date("Y-m-d", strtotime($judge->date_created)) . '</span></div>';
                echo '<div style="display: flex;margin: 0;justify-content: space-evenly;gap:1rem">';

                $edit_attr = [
                    'mx-get' => 'competitions/edit_judge_modal/' . $judge->id,
                    'mx-build-modal' => 'judge-edit-modal',
                    'class' => 'modal-edit btn-success',
                    'style' => 'margin: 0;color:lawngreen;width:100%;'
                ];
                echo form_button('edit_button', '<i class="fa fa-edit" aria-hidden="true"></i> edit', $edit_attr);
                $del_attr = [
                    'mx-get' => 'competitions/delete_judge_modal/' . $judge->id,
                    'mx-build-modal' => 'judge-delete-modal',
                    'class' => 'modal-delete btn-danger',
                    'style' => 'margin: 0;color:red;width:100%;'
                ];
                echo form_button('del_button', '<i class="fa fa-trash" aria-hidden="true"></i> delete', $del_attr);
                echo '</div></div>';
            }
        }
        ?>
</div>