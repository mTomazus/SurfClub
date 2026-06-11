<?php
/**
 * MX fragment for #lessons-container (mx-select="#ls-payload").
 * Also carries a fresh #stat-panel which the container OOB-swaps in,
 * so the stats stay live after every add/delete.
 */
require_once APPPATH . 'modules/lessons/schedules/views/_ls_helpers.php';

echo ls_render_stat_panel($rows);
?>
<div id="ls-payload">
    <?= ls_render_board($rows) ?>
</div>
