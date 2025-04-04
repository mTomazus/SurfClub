<?php 
    echo "<section>";
    echo "<div class='mod-head'><h2>Heat Score - " . out($heat_info->round) . " Heat " . out($heat_info->heat_number) . "</h2>";
    
    date_default_timezone_set('Europe/Vilnius');

    $future_time = new DateTime($heat_info->end_time); // Convert string to DateTime
    $current_time = new DateTime(); // Get current time

    if ($future_time > $current_time) {
        $interval = $current_time->diff($future_time);
        echo '<h2>' . $interval->format('%i:%s') . '</h2></div>';  // Outputs MM:SS
    } else {
        echo "<h2>00:00</h2></div>"; // Timer has expired
    }
    
    echo '<span mx-get="competitions/heat_time" mx-trigger="load" mx-target="every 1s"></span>';
    echo "<table id='heat-scores'>";
    foreach ($participants as $participant) {
        ?>
        <tr>
                <td class="<?= out($participant['jersey_color']) ?>"><?= out(strtoupper($participant['jersey_color'])) ?></td>
                <td><?= out($participant['first_name']) ?> <?= out($participant['last_name']) ?></td>

            <?php if (!empty($participant['scores'])) {
                    foreach ($participant['scores'] as $score) {
                        ?>
                        <td><?= out($score['avg_score']) ?></td>
            <?php } 
        } else {
            echo "<td colspan='2'>No Scores Available</td></tr>";
        }
        echo "</tr>";
    }
    echo "</section";
?>
