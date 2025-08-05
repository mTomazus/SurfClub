<div class="modal-delete-body ">
    <div id="response"></div>
        <h2>Delete competition</h2>
    <form mx-delete="competitions/submit_delete_comp/<?= $update_id ?>" mx-close-on-success="true" mx-target="#response2" mx-on-success="#comp-show-table" mx-animate-success="true">
        <p>Are you sure?</p>
        <p>You are about to delete a competition.  This cannot be undone.  Do you really want to do this?</p> 
        <?php
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'modal-delete'));
        echo form_button('close', 'cancel', ['class' => 'close', 'onclick' => 'closeModal()']);
    echo form_close();
    ?>
</div>