<div id="information"></div>
<?php

$form_attr = [
    'mx-post' => 'lessons-schedules/submit/' . ($id ?? ''),
    'mx-on-success' => '#lessons-container',
    'mx-close-on-success' => 'true',
    'mx-animate-success' => 'true',
    'mx-on-error' => '#error-msg',
    'autocomplete' => 'off',
    'mx-target' => '#information',
    'class' => 'highlight-errors',
    'id' => 'lesson-form'
];

echo form_open('#', $form_attr);

$options = array_combine(
    array_column($lessons, 'id'),
    array_column($lessons, 'name')
);

if (!isset($id)) {
    $start_time = '18:00';
    $reserved_places = 0;
    $available_places = 1;
    $selected_lesson = '1';
} else {
    $selected_lesson = $lesson_id;
}
?>
<div class="ls-form-grid">
    <div class="ls-field ls-field--full">
        <label for="ls-lesson">Lesson</label>
        <?= form_dropdown('lesson_id', $options, $selected_lesson, ['id' => 'ls-lesson']) ?>
    </div>
    <div class="ls-field">
        <label for="ls-date">Date</label>
        <input type="date" id="ls-date" name="date" class="date-picker" value="<?= out($date ?? '') ?>" required />
    </div>
    <div class="ls-field">
        <label for="ls-time">Start time</label>
        <input type="time" id="ls-time" name="start_time" value="<?= out($start_time) ?>" class="time-picker" required />
    </div>
    <div class="ls-field">
        <label for="ls-available">Available places</label>
        <input type="number" id="ls-available" name="available_places" min="1" value="<?= out($available_places) ?>" required />
    </div>
    <div class="ls-field">
        <label for="ls-reserved">Reserved places</label>
        <input type="number" id="ls-reserved" name="reserved_places" min="0" value="<?= out($reserved_places) ?>" required />
    </div>
</div>
<?php
echo '<div class="ls-modal-btns">';
echo form_button('close_btn', 'Cancel', ['class' => 'ls-btn-cancel', 'onclick' => 'closeModal()']);
echo form_submit('submit', 'Save schedule', ['class' => 'ls-btn-save']);
echo '</div>';
echo form_close();
?>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
