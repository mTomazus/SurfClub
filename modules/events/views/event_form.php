<div id="event-form">
    <div id="error-msg"></div>
    <?php
    $form_attr = [
        'mx-post' => 'events/submit_event/' . ($id ?? ''),
        'mx-on-success' => '#events-container',
        'mx-close-on-success' => 'true',
        'mx-target' => '#information',
        'class' => 'highlight-errors',
        'style' => 'grid-template-columns:1fr;padding: 1rem 0;',
        'id' => 'event-submit-form'
    ];
    echo form_open('#', $form_attr);
    echo '<div style="display: flex;gap: 1rem;"><div style="flex-grow:1">';
    echo form_label('Event Title');
    echo validation_errors('title');
    echo form_input('title', $title, array("placeholder" => "Enter title"));
    echo '</div><div style="flex-grow:1">';
    echo form_label('Time');
    echo validation_errors('start_time');
    echo '<input type="datetime-local" name="start_time" value="' . $start_time . '">';
    echo '</div></div>';
    echo form_label('Short Description');
    echo validation_errors('description');
    echo form_textarea('description', $description, array("placeholder" => "Enter description"));
    echo '<div style="display: flex;gap: 1rem;"><div style="flex-grow:1">';
    echo form_label('Email');
    echo validation_errors('email');
    echo form_email('email', $email, array("placeholder" => "Enter email"));
    echo '</div><div style="flex-grow:1">';
    echo form_label('Phone');
    echo validation_errors('phone');
    echo form_input('phone', $phone, array("placeholder" => "Enter phone"));
    echo '</div></div>';
    echo form_submit('submit', 'Submit', array('class' => "success"));
    echo form_close();
    ?>
</div>

<style>
    input, textarea {
        width: 100%;
        margin: 0;
        height: 2rem;
    }
    label {
        font-weight: bold;
        margin: 0;
    }
    form div {
        flex-grow:1;
    }
</style>