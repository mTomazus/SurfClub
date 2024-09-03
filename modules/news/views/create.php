<h1><?= $headline ?></h1>
<?= validation_errors() ?>
<div class="card">
    <div class="card-heading">
        News Details
    </div>
    <div class="card-body">
        <?php
        echo form_open($form_location);
        echo form_label('Date And Time');
        $attr = array("class"=>"datetime-picker", "autocomplete"=>"off", "placeholder"=>"Select Date And Time", "autocomplete" => "off");
        echo form_input('date_and_time', $date_and_time, $attr);
        echo form_label('Article Headline');
        echo form_input('article_headline', $article_headline, array("placeholder" => "Enter Article Headline", "autocomplete" => "off"));
        echo form_label('Article Body');
        echo form_textarea('article_body', $article_body, array("placeholder" => "Enter Article Body"));
        echo '<div>';
        echo 'Published ';
        echo form_checkbox('published', 1, $checked=$published);
        echo '</div>';
        echo form_submit('submit', 'Submit');
        echo anchor($cancel_url, 'Cancel', array('class' => 'button alt'));
        echo form_close();
        ?>
    </div>
</div>