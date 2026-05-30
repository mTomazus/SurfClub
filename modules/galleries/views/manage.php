<div id="title" style="display:none"><h1>Galleries</h1></div>

<div id="stat-panel">
    <a class="stat-card">
        <span class="stat-label">Galleries</span>
        <span class="stat-count"><?= $pagination_data['total_rows'] ?></span>
    </a>
</div>

<div id="galleries-container">

<div class="gal-toolbar" id="results-tbl">
    <div class="gal-toolbar__search">
        <?php
        echo form_open('galleries/manage/1/', array("method" => "get"));
        echo form_search('searchphrase', '', array("placeholder" => "Search galleries..."));
        echo form_submit('submit', 'Search');
        echo form_close();
        ?>
    </div>
    <div class="gal-toolbar__right">
        <label class="gal-per-page-label">
            Per page
            <?php
            $dropdown_attr['onchange'] = 'setPerPage()';
            echo form_dropdown('per_page', $per_page_options, $selected_per_page, $dropdown_attr);
            ?>
        </label>
        <?= anchor('galleries/create', '<i class="fa fa-plus"></i> New Gallery', array("class" => "button gal-create-btn")) ?>
    </div>
</div>

<?php flashdata(); ?>
<?= Pagination::display($pagination_data) ?>

<?php if (count($rows) > 0): ?>
    <?php
    $by_year = [];
    foreach ($rows as $row) {
        $by_year[$row->year][] = $row;
    }
    krsort($by_year);
    ?>
    <div class="gal-year-groups">
        <?php foreach ($by_year as $year => $sessions): ?>
        <section class="gal-year-section">
            <div class="gal-year-badge"><?= (int) $year ?></div>
            <div class="gal-session-grid">
                <?php foreach ($sessions as $row): ?>
                <a href="<?= BASE_URL ?>galleries/show/<?= $row->id ?>" class="gal-session-card">
                    <i class="fa fa-picture-o gal-session-icon"></i>
                    <span class="gal-session-label">Session <?= out($row->pamaina) ?></span>
                    <span class="gal-session-cta">View <i class="fa fa-arrow-right"></i></span>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endforeach; ?>
    </div>

    <?php if (count($rows) > 9):
        unset($pagination_data['include_showing_statement']);
        echo Pagination::display($pagination_data);
    endif; ?>

<?php else: ?>
    <p class="gal-empty">No galleries found.</p>
<?php endif; ?>

</div>
