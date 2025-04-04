<div id="just-heats">
    <h2>All Competition Heats</h2>
    <div class="legend">      
        <h4>Legend:</h4>
        <h4>finished</h4>
        <h4>running</h4>
        <h4>scheduled</h4>
        <h4>pending</h4>
    </div>
    
    <div class="wrapper">
        <?php foreach ($heats as $heat): ?>
            <div class="heat <?= out($heat['status']) ?>">
                <h4><?= out($heat['division']) ?> | <?= out($heat['round']) ?> | Heat <?= out($heat['heat_number']) ?></h4>
                <hr>
            </div>
        <?php endforeach; ?>
    </div>
</div>
