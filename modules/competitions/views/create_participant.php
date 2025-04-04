<div id="form-table" class="container">
    <div style="width:100%">
        <h2>Register Participant</h2>
        <div id="response"></div>

        <?php
            $form_attr = [
                'mx-post' => 'competitions/submit_create_participant',
                'mx-target' => '#response'
            ];
            echo form_open('#', $form_attr);
            $options = array(); // Initialize empty array
            foreach ($rows as $row) { 
                $options[$row->id] = $row->name . ' ' . $row->year;
            }
            echo form_label('Choose Competition:');
            echo form_dropdown('comp_id', $options);
            echo form_label('Name');
            $attr['placeholder'] = 'Enter your name here...';
            $attr['autocomplete'] = 'off';
            echo form_input('first_name', '', $attr);
            echo form_label('Surname');
            $attr['placeholder'] = 'Enter your surname here...';
            $attr['autocomplete'] = 'off';
            echo form_input('last_name', '', $attr);
            echo form_label('Email');
            $attr['placeholder'] = 'Enter your email here...';
            $attr['autocomplete'] = 'off';
            echo form_input('email', '', $attr);
            echo form_label('Gender');
            $genders = ['Male' => 'Male', 'Female' => 'Female'];
            echo form_dropdown('gender', $genders);
            echo form_label('Group');
            $groups = ['U12' => 'Under 12', 'U15' => 'Under 15', 'U18' => 'Under 18', 'ADT' => 'Adult', 'VET' => 'Veteran'];
            echo form_dropdown('age_group', $groups);
            echo form_hidden('comp_id', '1'); // -- CHANGE FOR EACH COMP
            echo form_submit('submit', 'Register');
            echo form_close();
        ?>
    </div>
</div>