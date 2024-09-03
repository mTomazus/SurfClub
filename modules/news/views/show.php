<h1><?= $headline ?> <span class="smaller hide-sm">(Record ID: <?= $update_id ?>)</span></h1>
<?= flashdata() ?>
<div class="card">
    <div class="card-heading">
        Options
    </div>
    <div class="card-body">
        <?php 
        echo anchor('news/manage', 'View All News', array("class" => "button alt"));
        echo anchor('news/create/'.$update_id, 'Update Details', array("class" => "button"));
        $attr_delete = array( 
            "class" => "danger go-right",
            "id" => "btn-delete-modal",
            "onclick" => "openModal('delete-modal')"
        );
        echo form_button('delete', 'Delete', $attr_delete);
        ?>
    </div>
</div>
<div class="three-col">
    <div class="card record-details">
        <div class="card-heading">
            News Details
        </div>
        <div class="card-body">
            <div><span>Date And Time</span><span><?= date('l jS F Y \a\t H:i',  strtotime($date_and_time)) ?></span></div>
            <div><span>Published</span><span><?= $published ?></span></div>

            <div>
                <div><b>Article Headline</b></div>
                <div style="padding-top: 1em;"><?= $article_headline ?></div>
            </div>
            <div>
                <div><b>Article Body</b></div>
                <div style="padding-top: 1em;"><?= $article_body_preview ?></div>
                <div class="text-right"><button class="alt smaller" onclick="openModal('article-preview')">READ ARTICLE</button></div>
            </div>            
        </div>
    </div>
        <div class="card">
        <div class="card-heading">
            Picture
        </div>
        <div class="card-body picture-preview">
            <?php
            if ($draw_picture_uploader == true) {
                echo form_open_upload(segment(1).'/submit_upload_picture/'.$update_id);
                echo validation_errors();
                echo '<p>Please choose a picture from your computer and then press \'Upload\'.</p>';
                echo form_file_select('picture');
                echo form_submit('submit', 'Upload');
                echo form_close();
            } else {
                $picture_path = BASE_URL.segment(1).'_module/images/'.segment(1).'_pics/'.$update_id.'/'.$picture;
            ?>
                <p class="text-center">
                    <button class="danger" onclick="openModal('delete-picture-modal')"><i class="fa fa-trash"></i> Delete Picture</button>
                </p>
                <p class="text-center">
                    <img src="<?= $picture_path ?>" alt="picture preview">
                </p>

                <div class="modal" id="delete-picture-modal" style="display: none;">
                    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Picture</div>
                    <div class="modal-body">
                        <?= form_open(segment(1).'/ditch_picture/'.$update_id) ?>
                            <p>Are you sure?</p>
                            <p>You are about to delete the picture.  This cannot be undone. Do you really want to do this?</p>
                            <p>
                                <button type="button" name="close" value="Cancel" class="alt" onclick="closeModal()">Cancel</button>
                                <button type="submit" name="submit" value="Yes - Delete Now" class="danger">Yes - Delete Now</button>
                            </p>
                        <?= form_close() ?>
                    </div>
                </div>

            <?php 
            }
            ?>
        </div>
    </div>
    <div class="card">
        <div class="card-heading">
            Comments
        </div>
        <div class="card-body">
            <div class="text-center">
                <p><button class="alt" onclick="openModal('comment-modal')">Add New Comment</button></p>
                <div id="comments-block"><table></table></div>
            </div>
        </div>
    </div>
    <div class="modal" id="comment-modal" style="display: none;">
        <div class="modal-heading"><i class="fa fa-commenting-o"></i> Add New Comment</div>
        <div class="modal-body">
            <p><textarea placeholder="Enter comment here..."></textarea></p>
            <p><?php
                $attr_close = array( 
                    "class" => "alt",
                    "onclick" => "closeModal()"
                );
                echo form_button('close', 'Cancel', $attr_close);
                echo form_button('submit', 'Submit Comment', array("onclick" => "submitComment()"));
                ?>
            </p>
        </div>
    </div>
</div>

<div class="modal" id="article-preview">
    <div class="modal-heading">Article Preview <span class="float-right close-preview" onclick="closeModal()">&#10005;</span></div>
    <div class="modal-body">
        <div class="text-left">
            <h1><?= $article_headline ?></h1>
            <?= nl2br($article_body) ?>
        </div>
    </div>
</div>

<div class="modal" id="delete-modal" style="display: none;">
    <div class="modal-heading danger"><i class="fa fa-trash"></i> Delete Record</div>
    <div class="modal-body">
        <?= form_open('news/submit_delete/'.$update_id) ?>
        <p>Are you sure?</p>
        <p>You are about to delete a record.  This cannot be undone.  Do you really want to do this?</p> 
        <?php 
        echo '<p>'.form_button('close', 'Cancel', $attr_close);
        echo form_submit('submit', 'Yes - Delete Now', array("class" => 'danger')).'</p>';
        echo form_close();
        ?>
    </div>
</div>

<style>
#article-preview {
    max-width: 760px !important;
}

#article-preview .modal-body {
    max-height: 70vh;
    overflow: auto;
}

#article-preview .modal-heading .float-right {
    font-size: 1.2em;
    top: -4px;
    position: relative;
}

#article-preview h1 {
    font-size: 36px;
}

.close-preview {
    cursor: pointer;
}

#article-preview > div.modal-body > div {
    padding-bottom: 33px;
}
</style>

<script>
var token = '<?= $token ?>';
var baseUrl = '<?= BASE_URL ?>';
var segment1 = '<?= segment(1) ?>';
var updateId = '<?= $update_id ?>';
var drawComments = true;
</script>