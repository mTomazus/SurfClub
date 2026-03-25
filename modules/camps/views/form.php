<div id="response"></div>
   
    <?php

      $form_attr = [
        'mx-post' => $form_location,
        'mx-animate-success' => 'true',
        'class' => 'highlight-errors'
      ];

      $bytes = openssl_random_pseudo_bytes(8);
      $reference = bin2hex($bytes);

      echo form_open($form_location, $form_attr);
      echo form_label('Vardas Pavardė');
      echo form_input('name', '', array("placeholder" => "Vardas Pavarde"));
      echo form_label('Mobilus');
      echo form_input('phone', '', array("placeholder" => "Telefono numeris..."));
      echo form_label('Emailas');
      echo form_email('email', '', array("placeholder" => "Jūsų emailas..."));
      echo form_label('Pamaina');
      ?>
        <select name="pamaina">
            <?php foreach($rows as $row): 
                $pam = $row->pamaina.'.  '.$row->start.' - '.$row->end;
                $disabled = ($row->status !== 'active') ? 'disabled' : '';
                $label = $pam . ' ~ ' . $row->price . ' Eur.';
                if ($row->status === 'ended') {
                    $label .= ' (baigėsi)';
                } elseif ($row->status !== 'active') {
                    $label .= ' (nėra vietų)';
                }
            ?>
                <option value="<?= $pam ?>" <?= $disabled ?>>
                    <?= $label ?>
                </option>
            <?php endforeach; ?>
        </select>
      <?php
      echo form_label('Amžius');
      echo form_input('age', '', array("placeholder" => "Stovyklautojo amžius..."));
      echo form_hidden('status', 'initial');
      echo form_hidden('reference', $reference);
      $close_btn_attr = [
        'class' => 'alt',
        'onclick' => 'closeModal()'
      ];
      echo form_submit('submit', 'Pateikti');
      echo form_close();
    ?>