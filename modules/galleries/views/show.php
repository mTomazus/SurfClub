<?= flashdata() ?>

<div class="gal-show-header">
    <div class="gal-show-identity">
        <span class="gal-show-year"><?= out($year) ?></span>
        <span class="gal-show-sep">/</span>
        <span class="gal-show-session">Session <?= out($pamaina) ?></span>
        <span class="gal-show-id">ID&nbsp;<?= out($update_id) ?></span>
    </div>
    <div class="gal-show-actions">
        <?= anchor('galleries/manage', '<i class="fa fa-arrow-left"></i> All Galleries', array("class" => "button gal-btn-back")) ?>
        <?= anchor('galleries/create/' . $update_id, '<i class="fa fa-pencil"></i> Edit', array("class" => "button gal-btn-edit")) ?>
        <?php
        $attr_delete = array(
            "class" => "gal-btn-delete",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', '<i class="fa fa-trash"></i> Delete', $attr_delete);
        ?>
    </div>
</div>

<div class="gal-show-filezone">
    <?= Modules::run('trongate_filezone/_draw_summary_panel', $update_id, $filezone_settings) ?>
</div>

<div class="gal-show-secondary">
    <div class="card gal-details-card">
        <div class="card-heading">Gallery Details</div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Year</div>
                    <div><?= out($year) ?></div>
                </div>
                <div class="row">
                    <div>Session</div>
                    <div><?= out($pamaina) ?></div>
                </div>
                <div class="row">
                    <div>Record ID</div>
                    <div><?= out($update_id) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card gal-comments-card">
        <div class="card-heading">Comments</div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt gal-comment-btn" onclick="openModal('comment-modal')"><i class="fa fa-comment-o"></i> Add Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
</div>

<!-- Comment modal -->
<div class="modal" id="comment-modal" style="display: none;">
    <div class="modal-heading"><i class="fa fa-comment-o"></i> Add New Comment</div>
    <div class="modal-body">
        <p><textarea placeholder="Enter comment here..."></textarea></p>
        <p>
            <?php
            $attr_close = array("class" => "alt", "onclick" => "closeModal()");
            echo form_button('close', 'Cancel', $attr_close);
            echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
            ?>
        </p>
    </div>
</div>

<!-- Delete modal -->
<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Gallery</div>
    <div class="modal-body">
        <?= form_open('galleries/submit_delete/' . $update_id) ?>
        <p>Are you sure you want to delete this gallery record? This cannot be undone.</p>
        <p>
            <?php
            echo form_button('close', 'Cancel', $attr_close);
            echo form_submit('submit', 'Yes — Delete Now', array("class" => 'danger'));
            ?>
        </p>
        <?= form_close() ?>
    </div>
</div>

<script>
const token = '<?= $token ?>';
const baseUrl = '<?= BASE_URL ?>';
const segment1 = '<?= segment(1) ?>';
const updateId = '<?= $update_id ?>';
const drawComments = true;
</script>
