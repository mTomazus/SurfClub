<div class="send-result-box">
    <?php if ($sent === 0 && empty($failed)): ?>
        <p class="sr-none">Nėra naujų gavėjų (jau išsiųsta arba nėra registracijų).</p>
    <?php else: ?>
        <?php if ($sent > 0): ?>
            <p class="sr-ok">Išsiųsta: <strong><?= $sent ?></strong></p>
        <?php endif; ?>
        <?php if (!empty($failed)): ?>
            <p class="sr-fail">Nepavyko: <strong><?= count($failed) ?></strong>
                <small>(<?= out(implode(', ', $failed)) ?>)</small>
            </p>
        <?php endif; ?>
    <?php endif; ?>
</div>
