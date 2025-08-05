<h2>Edit competition</h2>
<div id="response"></div>
<?php
    $form_attr = [
        'mx-post' => 'competitions/submit_create_comp/' . $id,
        'mx-target' => '#response2',
        'mx-close-on-success' => 'true',
        'mx-on-success' => '#comp-show-table',
        'mx-animate-success' => 'true'
        ];
    echo form_open('#', $form_attr);
    echo form_label('Competition');
    echo form_input('name', $name);
    echo form_label('Year');
    echo form_input('year', $year);
    echo form_label('Location');
    echo form_input('location', $location);
    echo form_label('Status');
    $options = ['created' => 'created', 'open' => 'open', 'closed' => 'closed', 'finished' => 'finished'];
    echo form_dropdown('status', $options, $status);
    echo form_submit('submit', 'update', ['class' => 'modal-submit']);
    echo form_button('close', 'close', ['class' => 'close', 'onclick' => 'closeModal()']);
    echo form_close();
?>