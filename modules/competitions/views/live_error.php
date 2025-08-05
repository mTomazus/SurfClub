<div id="form-container">
    <form style="grid-template-columns: 1fr;color:white">
        <h2 class="mb-0">Live Judging</h2>
        <?php 
            if (!empty($heat[0])) { 
        ?>
            <h4><?= $error ?></h4>
            <h4>Next scheduled heat:</h4>
            <h3 class="text-center"><?= $heat[0]->division ?> | <?= $heat[0]->round ?> | Heat <?= $heat[0]->heat_number ?></h3>
            <h3 class="text-center">Scheduled to start at <?= date("H:i", strtotime($heat[0]->start_time)); ?></h3>
            <h3 id="time-now" class="text-center" mx-get="competitions/current_time" mx-trigger="load">00:00</h3>
        <?php 
            } else {
                echo '<p class="mt-0 text-right">' . $error . '</p>';
                echo '<h4>There are no scheduled heats available</h4>';
                echo '<p class="blink lg text-center">Come back later</p>';
            }
        ?>
    </form>
</div>
    
