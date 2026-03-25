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
    'style' => 'grid-template-columns: 1fr;padding: 1rem 0;',
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
    echo form_dropdown('lesson_id', $options, '1');
} else {
    echo form_dropdown('lesson_id', $options, $lesson_id);
}

?>

<div class="flex-row justify-evenly">
    <input type="date" name="date" class="date-picker" style="width:auto" value="<?= $date ?>" placeholder="Choose date here..." required />
    <input type="time" name="start_time" style="width:auto" value="<?= out($start_time) ?>" class="time-picker" required />
</div>
<div class="flex-row justify-evenly"><div> 

<?php

$places_attr = [
    'type' => 'number',
    'placeholder' => 'Enter available places...',
    'min' => 1,
    'value' => 1,
    'style' => 'margin:0 0 1rem;',
];
echo form_label('Available Places*');
echo form_input('available_places', $available_places, $places_attr);

?> </div><div> <?php

$reserved_places_attr = [
    'type' => 'number',
    'placeholder' => 'Enter reserved places...',
    'min' => 0,
    'value' => 0,
    'style' => 'margin:0 0 1rem;',
];
echo form_label('Reserved Places');
echo form_input('reserved_places', $reserved_places, $reserved_places_attr);

?> </div></div> <?php

echo '<div class="d-flex justify-between">';
$close_btn_attr = [
   'class' => 'alt',
   'onclick' => 'closeModal()'
];
echo form_button('close_btn', 'Close', $close_btn_attr);
echo form_submit('submit', 'Submit', ['class' => 'success']);
echo '</div>';
echo form_close();

?>
<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>