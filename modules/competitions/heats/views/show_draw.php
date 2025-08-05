<div id="full-draw">
    <div id="heat-draw" style="margin: 0 0.5rem;">
        <h1 style="text-align: center;
                    background: linear-gradient(to right, red, rgb(152, 0, 104) 52.1%, purple);
                    background-clip: text;
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    font-family: Impact;
                    margin: 1rem;
                    margin-bottom: 0!important;"><?= out($comp_name) ?></h1>
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
                        <div class="heat-header">
                            <?php 
                                if ($heat['round'] === 'Final') {
                                    echo '<h2>' . out($heat['round']) . '</h2>';
                                } else {
                                    echo '<h2>' . out($heat['round']) . ' - Heat ' . out($heat['heat_number']) . '</h2>';
                                }

                                if ($heat['status'] === 'finished') {
                                    echo '<p style="color:red;margin: 0;padding: 0.1rem 0.5rem;">ended</p>';
                                    echo '<p>' . out($heat['division']) . '</p>';
                                } elseif ($heat['status'] === 'pending') { 
                                    echo '<p style="color:black;margin: 0;padding: 0.1rem 0.5rem;">soon</p>';
                                    echo '<p>' . out($heat['division']) . '</p>'; 
                                } elseif ($heat['status'] === 'scheduled') {
                                    echo '<h2 style="color:green">start at</h2>';
                                    echo '<p>' . out($heat['division']) . '</p>';
                                    $date=date_create($heat['start_time']);
                                    echo '<p style="text-align:center">' . date_format($date,"H:i") . '</p>';
                                } elseif ($heat['status'] === 'running') {
                                    date_default_timezone_set('Europe/Vilnius'); // Set your timezone
                                    $end_time_str = $heat['end_time']; // Example format from DB
                                    $end_time = new DateTime($end_time_str);
                                    $current_time = new DateTime();
                                    if ($end_time > $current_time) {
                                        $interval = $current_time->diff($end_time);
                                        $minutes = $interval->i + ($interval->h * 60) + ($interval->d * 1440); // convert hours/days to minutes
                                        $seconds = $interval->s;
                                        echo '<h2 style="color:greenyellow;text-align:center">live</h2>';
                                        echo '<p>' . out($heat['division']) . '</p>';
                                        echo '<p style="margin-inline:1rem">' . sprintf('%02d:%02d', $minutes, $seconds) . '</p>'; // Output like 12:34
                                    } else {
                                        echo '<p style="color:red;margin: 0;padding: 0.1rem 0.5rem;">ended</p>';
                                        echo '<p>' . out($heat['division']) . '</p>';

                                    }
                                }
                            ?>
                        </div>
                    <table>
                    <!--- <?php $counter = 1; // Initialize auto-numbering ?> -->
                        <?php foreach ($heat['participants'] as $participant): ?>
                            <tr>
                            <!---    <td><?= $counter++ ?></td> <!-- Auto-numbering --> 
                                <td><div class="jersey <?= out($participant['jersey_color']) ?>"></div></td>
                                <td><?= out($participant['first_name'] . ' ' . $participant['last_name']) ?></td>
                                <td><?= out($participant['result']['total_score']) ?></td>
                                <?php if ($heat['round'] === 'Final') { 
                                    if (!empty($participant['result']['total_score'])) { ?>
                                    <td style="font-size: 1rem;background:crimson;color:white;"><?= out($participant['result']['rank']) ?></td>
                                    <?php } else { ?>
                                    <td></td>
                                    <?php }
                                } else { if (!empty($participant['result']['total_score'])) { ?>
                                    <td style="font-size: 1rem;background:cornflowerblue;color:white;"><?= out($participant['result']['rank']) ?></td>
                                    <?php } else { ?>
                                    <td></td>
                                    <?php }
                                } ?>
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
        overflow: hidden;
        border-radius: 10px;
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
        margin-bottom: 2rem;
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
        background: radial-gradient(circle at 100px 100px, rgba(251, 80, 54, 0.8) 0.42%, rgb(255, 51, 51) 81.93%, rgb(152, 2, 2));
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px red;
    }
    .green {
        background: radial-gradient(circle at 100px 100px, #01d42c 0.42%, rgb(77, 144, 10) 78.99%, #044700);
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px green;
    }
    .blue {
        background: radial-gradient(circle at 100px 100px, #5cabff, rgb(10, 118, 233) 78.99%, #2216a6);
        color:white;
        border: 1px solid black;
        box-shadow: 0 0 5px blue;
    }
    .heat {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 5px;
        padding: 1rem;
        & div {
            display: grid;
            grid-template-columns: auto auto;
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
        gap: 3rem;
        margin: auto 2rem;
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
    /* Tooltip container */
    .tooltip {
    position: relative;
    display: inline-block;
    }

    /* Tooltip text */
    .tooltip .tooltiptext {
    visibility: hidden;
    width: 40px;
    background-color: grey;
    color: #fff;
    text-align: center;
    padding: 2px 10px;
    border-radius: 6px;
    
    /* Position the tooltip text - see examples below! */
    position: absolute;
    z-index: 1;
    }

    /* Show the tooltip text when you mouse over the tooltip container */
    .tooltip:hover .tooltiptext {
    visibility: visible;
    }
</style>