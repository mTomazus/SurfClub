<div id="form-container" style="color:white">
    <h2>Generate Possible Heats</h2>
    <h4 class="text-left">Select competition to seed</h4>
    <div class="container">
        <?php  
            $location = 'competitions-heats/generate_all_heats';
            $attributes = [
                'id' => 'generation-form',
                'mx-target' => '#result',
                'method' => 'get'
            ];
            echo form_open($location, $attributes);
            $options = array(); // Initialize empty array
            foreach ($rows as $row) { 
                $options[$row['id']] = $row['name'] . ' ' . $row['year'];
            }
            echo form_label('Choose Competition:');
            echo form_dropdown('comp_id', $options);
            echo form_submit('submit', 'Generate Heats');
            echo form_close();
        ?>
    </div>
    <div id="result"></div>
</div>