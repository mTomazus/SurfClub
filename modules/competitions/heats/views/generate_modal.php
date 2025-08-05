<div class="modal-genetate-body">
    <div id="response"></div>
    <h2>Generate heats</h2>

    <form mx-post="competitions-heats/generate_all_heats<?= $update_id ?>" mx-close-on-success="true" mx-target="#response" mx-on-success=".cards-container" style="display: grid;grid-template-columns: 1fr;color:white;">
        <p>Are you sure?</p>
        <p>You are about to generate heats for competition.</p>
        <?php
        echo form_submit('submit', 'Yes - Generate Now', array("class" => 'modal-generate'));
        echo form_button('close', 'cancel', ['class' => 'close', 'onclick' => 'closeModal()']);
    echo form_close();
    ?>

</div>