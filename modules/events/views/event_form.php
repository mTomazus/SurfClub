<div id="event-form">
    <div id="error-msg"></div>
    <?php
    $form_attr = [
        'mx-post' => 'events/submit_event/' . ($id ?? ''),
        'mx-on-success' => '#events-container',
        'mx-close-on-success' => 'true',
        'mx-target' => '#information',
        'class' => 'highlight-errors',
        'id' => 'event-submit-form'
    ];
    echo form_open('#', $form_attr);

    // datetime-local needs "Y-m-d\TH:i"; the DB stores "Y-m-d H:i:s"
    $start_val = '';
    if (!empty($start_time)) {
        $ts = strtotime($start_time);
        if ($ts !== false) {
            $start_val = date('Y-m-d\TH:i', $ts);
        }
    }
    ?>
    <div class="ev-form-grid">
        <div class="ev-field">
            <label for="ev-f-title">Event title</label>
            <?= validation_errors('title') ?>
            <input type="text" id="ev-f-title" name="title" value="<?= out($title) ?>" placeholder="e.g. Sunset session" autocomplete="off">
        </div>
        <div class="ev-field">
            <label for="ev-f-time">Date &amp; time</label>
            <?= validation_errors('start_time') ?>
            <input type="datetime-local" id="ev-f-time" name="start_time" value="<?= $start_val ?>">
        </div>
        <div class="ev-field ev-field--full">
            <label for="ev-f-desc">Short description</label>
            <?= validation_errors('description') ?>
            <textarea id="ev-f-desc" name="description" placeholder="What is happening, where to meet&hellip;"><?= out($description) ?></textarea>
        </div>
        <div class="ev-field">
            <label for="ev-f-email">Email</label>
            <?= validation_errors('email') ?>
            <input type="email" id="ev-f-email" name="email" value="<?= out($email) ?>" placeholder="name@example.com" autocomplete="off">
        </div>
        <div class="ev-field">
            <label for="ev-f-phone">Phone</label>
            <?= validation_errors('phone') ?>
            <input type="tel" id="ev-f-phone" name="phone" value="<?= out($phone) ?>" placeholder="+370..." autocomplete="off">
        </div>
    </div>
    <?php
    echo '<div class="ev-modal-btns">';
    echo form_button('close_btn', 'Cancel', ['class' => 'ev-btn-cancel', 'onclick' => 'closeModal()']);
    echo form_submit('submit', 'Save event', ['class' => 'ev-btn-save']);
    echo '</div>';
    echo form_close();
    ?>
</div>
