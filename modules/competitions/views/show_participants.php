<div id="participantsTable">
    <section class="align-center">
        <h2 class="mb-0">Participants</h2>
        <?php if (!empty($rows)) { ?>
        <p class="text-right mt-1" style="margin:0;">Competition: <strong style="color:lawngreen">"<?= out($rows[0]->name) ?> <?= out($rows[0]->year) ?>"</strong></p>
        <p class="text-right xs mt-0 mb-1">Total Participants: <?= count($rows) ?></p>
        <div style="color: orange;gap: 1rem;align-items: center;">
            <?php } else { ?>
            <p class="mb-0">No participants found.</p>
            <p class="mb-0">Please check the competition status.</p>
            <?php } ?> 
        </div>
            <!-- Filters -->
        <div class="selector">
        <?php
            $options = [
                '' => 'All',
                'male_u12' => 'Male Under 12',
                'male_u15' => 'Male Under 15',
                'male_u18' => 'Male Under 18',
                'female_u12' => 'Female Under 12',
                'female_u15' => 'Female Under 15',
                'female_u18' => 'Female Under 18'
            ];
            $attributes = [
                'id' => 'category-selector-1',
                'mx-get' => 'competitions/show_participants/${this.value}',
                'mx-target' => '#participants-list',
                'mx-push-url' => 'true',
                'mx-select'=> '#participants-list',
                'mx-trigger' => 'change'
            ];
            echo form_label('By Division');
            echo form_dropdown('age_group', $options, '', $attributes);
        ?>
        </div>
        </section>
    <div id="participants-list" class="mt-1" mx-get="competitions/show_participants" mx-target="#participants-list" mx-select="#participants-list" mx-trigger="load">
        <?php foreach ($rows as $row) { ?>
                <ul>
                    <li><?= out($row->first_name) ?></li>
                    <li><?= out($row->last_name) ?></li>
                    <li><?= out($row->gender_age) ?></li>
                    <?php $user_info = Modules::run("competitions/_get_judge_info");
                    if ($user_info->role === 'admin') { ?>
                    <button mx-build-modal='{"id": "participant-modal"}' mx-get="competitions/edit_participant/<?= $row->id ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                    <button class="danger" style="color:red" mx-delete="competitions/submit_delete_participant/<?= $row->id ?>" mx-on-success="#participants-list"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                    <?php } ?>
                </ul>
        <?php } ?>
    </div>
</div>
