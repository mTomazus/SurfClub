<div class="modal-delete-body ">
        <h2 class="mb-0">Delete Judge</h2>
    <form mx-post="competitions/submit_delete_judge/<?= $update_id ?>" mx-close-on-success="true" mx-target="#response" mx-on-success=".show-judge" mx-animate-success="true">
        <div class="d-grid" style="grid-column:1 / -1;color: var(--secondary-color);">
            <p>Are you sure?</p>
            <p class="bm-0">You are about to delete a judge:</p>
            <p class="bt-0 lg"><?= out($name) ?></p>
            <p>This cannot be undone.  <strong>Do you really want to do this?</strong></p> 
        </div>
    <?php
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'modal-delete'));
        echo form_button('close', 'back', ['class' => 'close', 'onclick' => 'closeModal()']);
        echo form_close();
    ?>
</div>