<?php
    foreach($posts as $post) {
        echo '<div class="inst-post">';
            echo '<div class="inst-head">';
                echo '<p>id: ' . $post->post_id . '</p>';
                echo '<p>status: ' . $post->status . '</p>';
                echo '<p>prompt: ' . $post->prompt . '</p>';
                echo '<p>type: ' . $post->media_type . '</p>';
            echo '</div>';
            echo '<div class="inst-body">';
                echo '<h3>' . $post->caption . '</h3>';
            $file_urls = explode(',', $post->file_urls);
            echo '<div>';
            foreach ($file_urls as $url) {
                echo '<img src="' . $url . '">';
            }
            echo '</div>';
            echo '</div>'; 
            echo '<div style="justify-content: right; gap: 1rem; display: flex;">';
    
        if($post->status === "ready") {
            $edit_attributes = [
                'mx-get' => 'test/edit/' . $post->post_id,
                'mx-build-modal' => 'insta-edit-modal',
                'class' => 'edit'
            ];
            echo form_button('edit_btn', '<i class="fa fa-pencil"></i> Edit', $edit_attributes);
                    //----------------
            $publish_attributes = [
                'mx-post' => 'test/publish_make_webhook/' . $post->post_id,
                'mx-target' => '#show-all',
                'mx-on-success' => '#show-all'
                ];
            echo form_button('submit_btn', 'Publish', $publish_attributes);
        }
        
        $btn_attr = [
            'mx-post' => 'test/generate_make_webhook/' . $post->post_id,
            'mx-target' => '.card',
            'mx-on-success' => '#show-all'
        ];
        echo form_button('load_btn', '<i class="fa fa-bullseye"></i> Generate', $btn_attr);

        $delete_attributes = [
            'mx-delete' => 'test/submit_delete/' . $post->post_id,
            'mx-target' => '.card'
        ];
        echo form_button('delete_btn', '<i class="fa fa-trash"></i> Delete', $delete_attributes);
        echo '</div></div>';
    } ?>

<style>
    .inst-post {
        padding: 0.5rem;
        font-size:1rem;
        margin-bottom:1rem;
        border: 2px solid rgb(0, 251, 255);
    }
    .inst-body {
        display:flex;
        & div {
            position: relative;
            margin: auto;
            min-width: 100px;
            min-height: 125px;
            padding:0.5rem;
        }
        & h3 {
            margin:auto;
        }
        & img {
            box-shadow: 0 0 3px;
            width:100px;
            position: absolute;
        }
        & img:nth-child(2) {
            transform:rotate(3deg);
        }
        & img:nth-child(3) {
            transform:rotate(6deg);
        }
        & img:nth-child(4) {
            transform:rotate(9deg);
        }
        & img:nth-child(5) {
            transform:rotate(12deg);
        }

    }
    .inst-head {
        display: flex;
        color: white;
        justify-content: space-around;
        border-bottom: 2px solid rgb(0, 251, 255);
    }

</style>