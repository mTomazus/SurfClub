<div id="bulk-information"></div>
<?php

$form_attr = [
    'mx-post' => 'lessons-schedules/submit_bulk',
    'mx-on-success' => '#lessons-container',
    'mx-close-on-success' => 'true',
    'mx-animate-success' => 'true',
    'mx-on-error' => '#bulk-information',
    'mx-target' => '#bulk-information',
    'autocomplete' => 'off',
    'class' => 'highlight-errors',
    'id' => 'bulk-lesson-form'
];

echo form_open('#', $form_attr);

$options = array_combine(
    array_column($lessons, 'id'),
    array_column($lessons, 'name')
);
?>
<div class="ls-form-grid">
    <div class="ls-field ls-field--full">
        <label for="ls-bulk-lesson">Lesson</label>
        <?= form_dropdown('lesson_id', $options, '1', ['id' => 'ls-bulk-lesson']) ?>
    </div>
    <div class="ls-field">
        <label for="ls-date-from">From date</label>
        <input type="date" id="ls-date-from" name="date_from" class="date-picker" required />
    </div>
    <div class="ls-field">
        <label for="ls-date-to">To date</label>
        <input type="date" id="ls-date-to" name="date_to" class="date-picker" required />
    </div>
    <div class="ls-field">
        <label for="ls-bulk-time">Start time</label>
        <input type="time" id="ls-bulk-time" name="start_time" value="18:00" class="time-picker" required />
    </div>
    <div class="ls-field ls-field--full">
        <label>Days of the week</label>
        <div class="ls-days">
            <?php
            $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
            foreach ($days as $day) {
                echo '<label><input type="checkbox" name="days[]" value="' . $day . '"><span>' . $day . '</span></label>';
            }
            ?>
        </div>
    </div>
    <div class="ls-field">
        <label for="ls-bulk-available">Available places</label>
        <input type="number" id="ls-bulk-available" name="available_places" min="1" value="1" required />
    </div>
    <div class="ls-field">
        <label for="ls-bulk-reserved">Reserved places</label>
        <input type="number" id="ls-bulk-reserved" name="reserved_places" min="0" value="0" required />
    </div>
</div>
<?php
echo '<div class="ls-modal-btns">';
echo form_button('close_btn', 'Cancel', ['class' => 'ls-btn-cancel', 'onclick' => 'closeModal()']);
echo form_submit('submit', 'Create lessons', ['class' => 'ls-btn-save']);
echo '</div>';
echo form_close();
?>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
