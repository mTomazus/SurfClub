<div id="form-table" class="comp_dash">
    <div>
        <h2>Create Competition</h2>
        <div id="response"></div>
        <?php
            $form_attr = [
                'mx-post' => 'competitions/submit_create_comp',
                'mx-target' => '#response'
            ];
            echo form_open('#', $form_attr);
            echo form_label('Competition');
            $attr['placeholder'] = 'Competion name here...';
            $attr['autocomplete'] = 'off';
            echo form_input('name', '', $attr);
            echo form_label('Year');
            $attr['placeholder'] = 'Enter competition year ...';
            $attr['autocomplete'] = 'off';
            echo form_input('year', '', $attr);
            echo form_label('Location');
            $attr['placeholder'] = 'Enter location here...';
            $attr['autocomplete'] = 'off';
            echo form_input('location', '', $attr);
            echo form_submit('submit', 'Create');
            echo form_close();
        ?>
    </div>
    <div class="cards-container">
        <h2>Available Competition</h2>
        <?php
            for ($i = 0; $i < $num_rows; $i++) {
                echo '<div class="card d-flex justify-center" style="align-items:center">';
                echo '<p class="mr-1">' . $rows[$i]->name . ' ' . $rows[$i]->year . '</p>';
                echo anchor('competitions-heats/show_heats_draw/' . $rows[$i]->id, 'Show draw');
                echo '</div>';
            }
        ?>
    </div>
</div>