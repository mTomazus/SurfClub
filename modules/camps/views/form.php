
<div id="response"></div>

<?php

$options = array();

  foreach($rows as $row) {
    $pam_id = $row->pamaina;
    $pam_start = $row->start;
    $pam_end = $row->end;
    $pam = $row->pamaina.'.  '.$pam_start.' - '.$pam_end;
    $options["$pam_id"] = "$pam";
  }

$form_attr = [
  'mx-post' => $form_location,
  'mx-target' => '#response',
  'class' => 'highlight-errors'
];

$bytes = openssl_random_pseudo_bytes(8);
$reference = bin2hex($bytes);

echo form_open($form_location, $form_attr);
echo form_label('Vardas');
echo form_input('name', '', array("placeholder" => "Vardas Pavarde"));
echo form_label('Mobilus');
echo form_input('phone', '', array("placeholder" => "Enter Phone"));
echo form_label('Emailas');
echo form_email('email', '', array("placeholder" => "Enter Email"));
echo form_label('Pamaina');
//$options = ['apple' => 'Apple', 'banana' => 'Banana', 'cherry' => 'Cherry'];
echo form_dropdown('pamaina', $options);
echo form_label('Amžius');
echo form_input('age', '', array("placeholder" => "Enter Age"));
echo form_hidden('status', 'initial');
echo form_hidden('reference', $reference);
$close_btn_attr = [
   'class' => 'alt',
   'onclick' => 'closeModal()'
];
echo form_button('close_btn', 'Uždaryti', $close_btn_attr);
echo form_submit('submit', 'Submit');
echo form_close();
?>