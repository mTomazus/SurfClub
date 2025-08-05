<div id="form-container">

    <h2>Edit Judge Scores</h2>

    <select id="heat-selector" name="heat_id"
            mx-get="competitions/edit_scores/${this.value}"
            mx-select-oob="#form-table:#form-table, #heat-selector:#heat-selector"
            mx-push-url="true"
            mx-trigger="change">
        <?php foreach ($all_heats as $heat): ?>
            <option value="<?= $heat['id'] ?>" <?= $heat_id == $heat['id'] ? 'selected' : '' ?>>
                <?= out($heat['id']) ?> -=- <?= out($heat['status']) ?> -=- <?= out($heat['division']) ?> - <?= out($heat['round']) ?> - Heat <?= $heat['heat_number'] ?>
            </option>
        <?php endforeach; ?>
    </select>

    <div id="form-table" class="edit-scores-table">
        <p>Heat ID: <?= $heat_id ?></p>  
        <div id="response"></div>  
        <table cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>Judge ID</th>
                    <th>Participant</th>
                    <th>Jersey</th>
                    <th>Wave</th>
                    <th>Score</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($scores as $score): ?>

                    <tr style="text-align: center;background:var(--bg-color-1);color:white">
                        <td><span>Judge </span><?= $score['judge_id'] ?></td>
                        <td class="d-sm-none"><?= out($score['first_name']) . ' ' . out($score['last_name']) ?></td>
                        <td style="background-color: <?= out($score['jersey_color']) ?>; color: #000; text-transform: uppercase;"><?= out($score['jersey_color']) ?></td>
                        <td><span>Wave </span><?= $score['wave_number'] ?></td>
                        <td>
                            
                            <?php
                                $form_attr = [
                                    'mx-post' => 'competitions/update_score/' . $score['id'],
                                    'mx-target' => '#response',
                                    'style' => 'gap: 0.5rem; display: flex; align-items: center; justify-content: center;'
                                ];
                                echo form_open('#', $form_attr);
                                echo form_input('score', $score['score'], ['style' => 'width: 60px; text-align: center;']);
                                echo form_submit('submit', 'update');
                                echo form_close();
                            ?>

                        </td>
                    </tr>
                    
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>