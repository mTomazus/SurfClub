<div class="text-right" style="margin: 1em 0;">
<?php
$delete_all_attr['onclick'] = 'openModal(\'delete-everything-modal\')';
$delete_all_attr['class'] = 'danger';
$warning_icon = '<i class=\'fa fa-warning\'></i> ';
echo form_button('delete_all', $warning_icon.'Delete All Articles', $delete_all_attr);
?>
</div>

<div class="modal" id="delete-everything-modal" style="display: none;">
    <div class="modal-heading danger">
        Delete Everything
    </div>
    <div class="modal-body">
        <?= form_open('news/submit_delete_everything') ?>
        <h3>WARNING: You are about to delete all of the articles.  This cannot be undone!</h3>
        <p>Are you sure?</p>
        <p>
            <button class="alt" onclick="closeModal()">Cancel</button>
            <?php
            echo form_submit('submit', '<i class=\'fa fa-trash\'></i> Yes - Delete Everything!', array("class" => "danger"));
            ?>
        </p>
        <?= form_close() ?>
    </div>
</div>