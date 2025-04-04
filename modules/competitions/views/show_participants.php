<div id="participantsTable">
    <section class="align-center">
        <h2>Competition Participants</h2>
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
    <div id="participants-list">
        <?php foreach ($rows as $row) { ?>
                <ul>
                    <li><?= out($row->first_name) ?></li>
                    <li><?= out($row->last_name) ?></li>
                    <li><?= out($row->gender_age) ?></li>
                    <?php if ($user->role === 'admin') { ?>
                    <button mx-build-modal='{"id": "participant-modal"}' mx-get="competitions/edit_participant/<?= $row->id ?>">edit</button>
                    <?php } ?>
                </ul>
        <?php } ?>
    </div>
</div>
