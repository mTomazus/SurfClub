<form id="score-submit" style="grid-template-columns: auto;" mx-post="competitions/score_submit" mx-animate-success="true" mx-on-success="#load-on">
    <h2>Judging Heat</h2>
    <input type="hidden" name="heat_id" required value="<?= $heat_id ?>">
    <input type="hidden" name="judge_id" required value="<?= $user->id ?>">
    
    <div class="heat_info">
        <div style="align-content:center">
            <?php if ($heat->round === 'Final') {
                echo '<h1 class="text-center m-0">' . $heat->round . '</h1>';
            } else {
                echo '<h1 class="text-center m-0">' . $heat->round . ' Heat ' . $heat->heat_number . '</h1>';
            } ?>
            <h3 class="text-center m-0">Division: <?= $heat->division ?></h3>
        </div>
        <div class="time_clock">
            <h2 style="margin: 0;border: none;color: springgreen;">
                <?php
                date_default_timezone_set('Europe/Vilnius');
                $future_time = new DateTime($heat->end_time); // Convert string to DateTime
                $current_time = new DateTime(); // Get current time
                if ($future_time > $current_time) {
                    $interval = $current_time->diff($future_time);
                    echo $interval->format('%i:%s'); // Outputs MM:SS
                } else {
                    echo "00:00"; // Timer has expired
                }
                ?>
            </h2>
        </div>
    </div>

    <!-- Display Wave Numbers for Each Participant -->
    <div class="wave_numbers">
        <?php foreach ($wave_numbers as $participant_id => $wave_number): ?>
            <div class="participant_wave">
                <strong> Next Wave No: <?= $wave_number ?></strong>
            </div>
        <?php endforeach; ?>
    </div>


    <div class="radio">
        <?php 
            $i = 0;
            $colors = ['white', 'red', 'green', 'blue'];
            foreach ($wave_numbers as $wave): ?>
                <input type="radio" class="radio_input" name="jersey_color" value="<?= $colors[$i] ?>" id="jersey_<?= $colors[$i] ?>">
                <label for="jersey_<?= $colors[$i] ?>" class="radio_label" style="--var:<?= $colors[$i] ?>">
                    <?= strtoupper($colors[$i]) ?>
                </label>
        <?php $i++;
        endforeach; ?>
    </div>

    <div class="radio_score">
        <?php
            for ($i = 1; $i <= 10; $i += 0.5) {
                $id = "score_" . str_replace('.', '_', $i); // Replace '.' with '_' for a valid ID
                echo '<input type="radio" class="score_input" name="score" value="' . $i . '" id="' . $id . '">';
                echo '<label for="' . $id . '" class="score_label">' . $i . '</label>';
            }
        ?>
        <button type="submit" class="score_label submit_button">Submit</button>
    </div>

    <div mx-get="competitions/judge_scores" mx-trigger="load"></div>

<?php
    echo form_close();
    ?>

<style>
    main {
        color: white;
    }
    .radio_input {
        display:none;
    }
    .score_input {
        display:none;
    }

</style>
