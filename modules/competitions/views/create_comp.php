<div id="form-table" class="comp_dash">
    <div id="comp-create-form" mx-get="competitions/create_comp" mx-target="#form-container" mx-select="#form-table" mx-trigger="load" style="color:white;font-weight:900">
        <h2 class="mb-0">Create Competition</h2>
        <p class="text-right mb-2">Enter details and chose divisions for the competition.</p>
        <div id="response"></div>
        <?php
            $form_attr = [
                'mx-post' => 'competitions/submit_create_comp',
                # 'mx-on-success' => '#comp-create-form',
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
            echo '<label class="lg" style="font-weight:normal;grid-column: 1 / -1;text-align: left;">Chose Divisions:</label>';
            // Add division checkboxes
            echo '<div class="mb-2" style="display: grid;grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));grid-column: 1 / -1;margin-left: 1rem;gap: 0.3rem 0.7rem;">';
            foreach ($divisions as $division) {
                echo '<input id="' . $division->id . '" class="division" style="display:none;" type="checkbox" name="divisions[]" value="' . $division->id . '">';
                echo '<label for ="' . $division->id . '" style="width:100%;font-weight:normal;text-align:center;padding: 0.2rem;margin:auto;"> <span style="color:var(--secondary-txt-clr)">' . out($division->name) . '</span>';
                echo '</label>';
            }
            echo '</div>';

            echo form_submit('submit', 'Create');
            echo form_close();
        ?>
    </div>
</div>
<div id="comp-table">
    <div id="comp-show-table" mx-get="competitions/create_comp" mx-trigger="load" mx-select="#comp-table" mx-target="#form-container">
        <div id="response2"></div>
        <h2 class="mb-0">Available Competition</h2>
        <?php
        if (empty($rows)) {
            echo '<p class="text-right mb-2">No new competitions available</p>';
        } else {
            echo '<p class="text-right mb-2">Click on the competition to view heats or edit / delete competition</p><div style="display: grid;grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));gap: 1rem;">';
            for ($i = 0; $i < $num_rows; $i++) {
                $status = $rows[$i]->status;
                switch ($status) {
                    case 'open':
                        $bgColor = 'skyblue';
                        break;
                    case 'closed':
                        $bgColor = 'orange';
                        break;
                    default:
                        $bgColor = 'lawngreen'; // fallback color
                }
                echo '<a href="competitions-heats/show_heats_draw/' . $rows[$i]->id . '" target="_blank" style="text-decoration: none;color: var(--secondary-color);text-transform: uppercase;">';
                echo '<div class="card d-flex justify-center;" style="display: grid;grid-template-columns:1fr;border: 1px solid var(--secondary-color);box-shadow: 0 0 10px var(--secondary-color);">';
                echo '<h3 style="text-transform: uppercase;text-align: center;background: ' . $bgColor . ';color: var(--black-color);margin: 1rem 1rem 0;">' . $rows[$i]->status . '</h3>';
                echo '<p class="mb-0">' . $rows[$i]->name . ' ' . $rows[$i]->year . '</p><p class="mt-0" style="text-transform:lowercase;font-size:1em;">' . $rows[$i]->location . '</p>
                <div style="display: flex;margin: 0 1rem 1rem;justify-content: space-evenly;gap: 1rem;">';

                if ($rows[$i]->status === 'closed') {
                    $btn_attr = [
                        'mx-post' => 'competitions-heats/generate_modal/' . $rows[$i]->id,
                        'mx-build-modal' => 'comp-generate-modal',
                        'mx-select' => '.modal-genetate-body',
                        'class' => 'modal-generate btn-success',
                        'style' => 'margin: 0;color:lawngreen;width:100%;'
                    ];
                    echo form_button('edit_button', '<i class="fa fa-tasks" aria-hidden="true"></i>', $btn_attr);
                }

                $del_attr = [
                    'mx-get' => 'competitions/delete_modal/' . $rows[$i]->id,
                    'mx-build-modal' => 'comp-delete-modal',
                    'class' => 'modal-delete btn-danger',
                    'style' => 'margin: 0;color:red;width:100%;'
                ];
                echo form_button('del_button', '<i class="fa fa-trash" aria-hidden="true"></i>', $del_attr);
                $btn_attr = [
                    'mx-get' => 'competitions/edit_comp/' . $rows[$i]->id,
                    'mx-build-modal' => 'comp-edit-modal',
                    'style' => 'margin: 0;width:100%;'
                ];
                echo form_button('edit_button', '<i class="fa fa-edit" aria-hidden="true"></i>', $btn_attr);
                echo '</div></div>';
                echo '</a>';
            }
            echo '</div>';
        }
        ?>
    </div>
</div>