<?php
    echo form_open('test/submit_edit/'.$product->id );
    echo form_textarea('caption', $product->caption, ['id' => 'caption-textarea', 'rows' => '5']);
    echo form_submit('submit', 'SUBMIT');
    echo form_close();
?>
