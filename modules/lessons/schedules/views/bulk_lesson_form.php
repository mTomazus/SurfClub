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
    'style' => 'grid-template-columns: 1fr; padding: 1rem 0;',
    'id' => 'bulk-lesson-form'
];

echo form_open('#', $form_attr);

$options = array_combine(
    array_column($lessons, 'id'),
    array_column($lessons, 'name')
);

echo form_dropdown('lesson_id', $options, '1');

?>

<div class="flex-row justify-evenly">
    <div>
        <?php echo form_label('From Date*'); ?>
        <input type="date" name="date_from" class="date-picker" style="width:auto" required />
    </div>
    <div>
        <?php echo form_label('To Date*'); ?>
        <input type="date" name="date_to" class="date-picker" style="width:auto" required />
    </div>
    <div>
        <?php echo form_label('Time*'); ?>
        <input type="time" name="start_time" style="width:auto" value="18:00" class="time-picker" required />
    </div>
</div>

<div style="margin: 0.75rem 0;border: 1px dashed;border-radius: 30px;padding: 0 1rem 1rem;">
    <?php echo form_label('Days of the Week*'); ?>
    <div class="flex-row" style="flex-wrap:wrap; gap:0.5rem; margin-top:0.4rem;justify-content: center;">
        <?php
        $days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        foreach ($days as $day) {
            echo '<label style="display:flex;align-items:center;gap:0.3rem;cursor:pointer;">';
            echo '<input type="checkbox" name="days[]" value="' . $day . '"> ' . $day;
            echo '</label>';
        }
        ?>
    </div>
</div>

<div class="flex-row justify-evenly mb-2">
    <div>
        <?php
        echo form_label('Available Places*');
        echo form_input('available_places', 1, [
            'type' => 'number',
            'min' => 1,
            'style' => 'margin:0 0 1rem;',
        ]);
        ?>
    </div>
    <div>
        <?php
        echo form_label('Reserved Places');
        echo form_input('reserved_places', 0, [
            'type' => 'number',
            'min' => 0,
            'style' => 'margin:0 0 1rem;',
        ]);
        ?>
    </div>
</div>

<?php
echo '<div class="d-flex justify-around mb-1">';
echo form_button('close_btn', 'Close', ['class' => 'alt', 'onclick' => 'closeModal()']);
echo form_submit('submit', 'Create Lessons', ['class' => 'success']);
echo '</div>';
echo form_close();
?>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>
