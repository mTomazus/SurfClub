<form style="grid-template-columns: 1fr;color:white"><h2><?= $error ?></h2>
<section>
    <?php 
        if (!empty($heat[0])) { 
    ?>
    <h4>Next scheduled heat:</h4>
    <h3 class="text-center"><?= $heat[0]->division ?> | <?= $heat[0]->round ?> | Heat <?= $heat[0]->heat_number ?></h3>
    <h3 class="text-center">Scheduled to start at <?= date("H:i", strtotime($heat[0]->start_time)); ?></h3>
    <h3 id="time-now" class="text-center" mx-get="competitions/current_time" mx-trigger="load">00:00</h3>
    <?php 
        } else {
            echo '<h4>No scheduled heats available</h4>';
            echo '<h3>Come back later</h3>';
        }
    ?>
</section>
</form>
    
