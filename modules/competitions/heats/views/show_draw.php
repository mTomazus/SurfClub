<div id="full-draw">
    <div id="heat-draw" style="margin: 0 0.5rem;">
        <h1><?= out($comp_name) ?></h1>
        <div class="nav">
            <?php
                $divisions = ['ALL' => '', 'U-12 F' => 'female_u12', 'U-15 F' => 'female_u15', 'U-18 F' => 'female_u18', 
                            'U-12 M' => 'male_u12', 'U-15 M' => 'male_u15', 'U-18 M' => 'male_u18'];

                foreach ($divisions as $label => $value): ?>
                    <button mx-get="competitions-heats/show_heats_draw/<?= $comp_id ?>/<?= $value ?>" 
                            mx-target=".wrapper" mx-select=".wrapper" 
                            mx-push-url="true">
                        <?= out($label) ?>
                    </button>
            <?php endforeach; ?>
        </div>
        <div class="wrapper">
            <?php foreach ($heats as $heat):
                    if ($heat['status'] === 'running' || $heat['status'] === 'finished' ) {
                    echo '<div class="heat" mx-build-modal="heatScore" mx-get="competitions-heats/heat_scores/';
                    echo $heat['id'];
                    echo '" >'; }
                    else { echo '<div class="heat">';} ?>
                        <div>
                            <?php 
                                if ($heat['round'] === 'Final') {
                                    echo '<h2>' . out($heat['round']) . '</h2>';
                                } else {
                                    echo '<h2>' . out($heat['round']) . ' - Heat ' . out($heat['heat_number']) . '</h2>';
                                }

                                if ($heat['status'] === 'finished') {
                                    echo '<p style="color:red;margin: 0;padding: 0.1rem 0.5rem;">ended</p>';
                                } elseif ($heat['status'] === 'scheduled' || $heat['status'] === 'pending') { 
                                    echo '<p style="color:black;margin: 0;padding: 0.1rem 0.5rem;">soon</p>'; 
                                } else {
                                    echo '<p style="color:lawngreen">LIVE</p>';
                                } 
                            ?>
                        </div>
                    <p><?= out($heat['division']) ?></p>
                    <table>
                    <!--- <?php $counter = 1; // Initialize auto-numbering ?> -->
                        <?php foreach ($heat['participants'] as $participant): ?>
                            <tr>
                            <!---    <td><?= $counter++ ?></td> <!-- Auto-numbering --> 
                                <td><div class="jersey <?= out($participant['jersey_color']) ?>"></div></td>
                                <td><?= out($participant['first_name'] . ' ' . $participant['last_name']) ?></td>
                                <td><?= out($participant['result']['total_score']) ?></td>
                                <td><?= out($participant['result']['rank']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
    * {
        box-sizing: border-box;
    }
    h1 {
        margin-bottom:2rem!important;
    }
    h1, h2, h3, p {
        color: white;
    }
    p {
        margin-top:0;
    }
    table {
        background:white;
        box-shadow: 0 0 5px white;
        td {
            border:none;
            text-transform: uppercase;
            font-size: 0.7rem;
        }
    }
    tr td:first-child {
        font-size-adjust: 0.5;
    }
    .wrapper {
        display: grid;
        gap: 1rem;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    }
    .jersey {
        width: 30px;
        height: 30px;
        border-radius: 15px;
        margin:auto;
    }
    .white {
        background: white;
        border: 1px solid black;
        box-shadow: 0 0 5px;
    }
    .red {
        background: red;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px red;
    }
    .green {
        background: green;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px green;
    }
    .blue {
        background: blue;
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px blue;
    }
    .heat {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 5px;
        padding: 1rem;
        & div {
            display: flex;
            justify-content: space-between;
            align-items: center;
            & p {
                font-weight: 900;
                font-family: Silom;
            }
        }
    }
    tbody {
        font-weight: 900;
        font-family: Baskerville;
        text-align: center;
    }
    .nav {
        padding: 1rem 0;
        justify-content: center;
        display: grid;
        gap:1rem;
        grid-template-columns: repeat(auto-fit, minmax(55px, 1fr));
        & button {
            background: rgba(255, 255, 255, 0.30);
            box-shadow: 0 0 5px white;
            border: 0;
            border-radius: 0;
        }
        & button:hover{
            background: rgba(255, 255, 255, 0.60);
            box-shadow:0 0 5px skyblue;
        }
    }
</style>