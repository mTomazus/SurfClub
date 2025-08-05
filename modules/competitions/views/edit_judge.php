<h2>Edit <?= out($username) ?></h2>
<div id="response"></div>
<form mx-post="competitions/submit_edit_judge/<?= $id ?>" mx-target="#response" mx-close-on-success="true" mx-animate-success="true" mx-on-success=".show-judge">
<?php
    echo '<div>';
    echo form_label('Full name:');
    echo form_input('name', $name);
    echo '</div><div>';
    echo form_label('Role:');
    $options = ['admin' => 'Admin', 'judge' => 'Judge'];
    echo form_dropdown('role', $options, $role, ['id' => 'role-select', 'class' => 'judge-role']);
    echo '</div>';
?>
    <div class="modal-footer">
        <button type="submit" class="modal-submit" name="update">update</button>
        <button class="close" onclick="closeModal()">close</button>
    </div>
<?php
    echo form_close();
?>