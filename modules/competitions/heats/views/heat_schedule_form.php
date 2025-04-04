<div id="just-heats" mx-get="competitions-heats/heat_schedule_page" mx-trigger="activate" mx-target="#form-container">
    <h2>Edit Heats Times</h2>
    <div id="#result"></div>
    <div class="wrapper">
    <?php
        date_default_timezone_set('Europe/Vilnius');
        foreach($heats as $heat) {
                if (($heat->status === 'pending') || ($heat->status === 'scheduled')) {
                echo '<section class="heat_length">';
                echo '<div class="d-flex"><h4>' . $heat->division . ' | ' . $heat->round . ' | Heat ' . $heat->heat_number . ' -&nbsp</h4><h4 class="' . $heat->status . '">' . $heat->status . '</h4></div>';
                ?>
                <form mx-post="competitions-heats/update_heat_schedule" mx-on-success="#just-heats" mx-target="#result">  
                <?php
                if ($heat->start_time === '0000-00-00 00:00:00'){
                    $date_time = date('Y-m-d H:i');
                } else {
                    $date_time = date("Y-m-d H:i", strtotime($heat->start_time));
                }
                $attr['class'] = 'datetime-picker';
                $attr['type'] = 'text';
                echo form_input('start_time', $date_time, $attr);
            // echo form_input('start_time', $heat->start_time, $attr);
                $options = array(
                    '15'  => '15 mins.',
                    '20'    => '20 mins.',
                    '25'  => '25 mins.',
                    '30' => '30 mins.',
                );
                echo form_dropdown('heat_length', $options, '20');
                echo form_hidden('heat_id', $heat->id);
                echo form_submit('submit', 'edit');
                echo form_close();
                echo '</section>';
            }
        }
    ?>
    <script>
        function setNow(inputName) {
            let now = new Date().toISOString().slice(0, 16).replace("T", " "); // Format YYYY-MM-DD HH:MM
            document.querySelector(`[name="${inputName}"]`).value = now;
        }
    </script>
    <script src="http://localhost/banglente_v25/js/trongate-datetime.js"></script>
    </div>
</div>