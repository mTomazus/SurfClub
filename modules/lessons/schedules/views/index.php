<?php
$title = 'Pamokų tvarkaraštis';
function getWeekStart($date) {
    return new DateTime($date);
}

// Determine current week (from GET param or today)
$week_start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-d');
$week_start_dt = getWeekStart($week_start);


// Build days of week (3 days)
$days = [];
for ($i = 0; $i < 3; $i++) {
    $d = clone $week_start_dt;
    $d->modify("+$i day");
    $days[] = $d;
}

// Group lessons by date, only for today and future
$lessons_by_date = [];
foreach ($rows as $row) {
    if ($row->available_places !== $row->reserved_places && $row->date >= date('Y-m-d')) {
        $lessons_by_date[$row->date][] = $row;
    }
}

// Navigation URLs (move by 3 days)
$prev_start_dt = clone $week_start_dt;
$prev_start_dt->modify('-3 days');
$next_start_dt = clone $week_start_dt;
$next_start_dt->modify('+3 days');
$prev_start = $prev_start_dt->format('Y-m-d');
$next_start = $next_start_dt->format('Y-m-d');

// Lithuanian day names
$lithuanian_days = [
    'Monday'    => 'Pirmadienis',
    'Tuesday'   => 'Antradienis',
    'Wednesday' => 'Trečiadienis',
    'Thursday'  => 'Ketvirtadienis',
    'Friday'    => 'Penktadienis',
    'Saturday'  => 'Šeštadienis',
    'Sunday'    => 'Sekmadienis',
];
?>

<div class="container-xl">
    <div class="week-nav" style="text-align:center;margin-bottom:1em;">
        <?php if ($prev_start >= date('Y-m-d')): ?>
            <a href="lessons-schedules/?start=<?= $prev_start ?>" style="color:white;font-size:1.5rem;"><i class="fa fa-caret-left" aria-hidden="true"></i></a>
        <?php else: ?>
            <span style="opacity:0;font-size:1.5rem;"><i class="fa fa-caret-left" aria-hidden="true"></i></span>
        <?php endif; ?>
        <span style="margin: 0 2em;font-weight: 900;color: white;">
            <?= $days[0]->format('Y-m-d') ?> - <?= $days[2]->format('Y-m-d') ?>
        </span>
        <a href="lessons-schedules/?start=<?= $next_start ?>" style="color:white;font-size:1.5rem;"><i class="fa fa-caret-right" aria-hidden="true"></i></a>
    </div>
    <div class="week-grid">
        <?php foreach ($days as $day): ?>
            <div class="day-col">
                <div class="day-head">
                    <span><?= $lithuanian_days[$day->format('l')] . ' ' . $day->format('m-d') ?></span>
                </div>
                <?php
                $date_str = $day->format('Y-m-d');
                if (!empty($lessons_by_date[$date_str])) {
                    foreach ($lessons_by_date[$date_str] as $row) {
                        $places_left = $row->available_places - $row->reserved_places;
                        if ($row->name === 'Grupinė Pamoka') {
                            $color = 'green';
                        } else if ($row->name === 'Pamokų Paketas') {
                            $color = 'orange';
                        } else if ($row->name === 'Privati Pamoka') {
                            $color = 'blue';
                        } else {
                            $color = 'yellow';
                        }
                        if ($places_left <= 0) {
                            $color = 'red';
                        }
                        ?>
                        <div class="thumbhead <?= $color ?>" mx-get="lessons-schedules/checkout_form/<?= $row->id ?>" 
                            mx-select="#form" 
                            mx-build-modal='{"id": "checkout-modal","modalHeading": "Registracija į pamoką"}'>
                            <div>
                                <h4><?= date('H:i',strtotime($row->start_time)) ?></h4>
                            </div>
                            <div>
                                <h4><?= $row->name ?></h4>
                                <p>Laisvų vietų: <?= $places_left ?></p>
                            </div>
                            <div>
                                <h4><?= $row->price ?> € </h5>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="no-lessons">Nėra Bangų<br>Nėra pamokų</div>';
                }
                ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<style>
    .validation-error-report {
        display: none;
        position: absolute;
        top: 11px;
    }
    .error-message p {
        color: whitesmoke!important;
        background: orange;
        padding: 0.5rem;
        font-weight: 900;
    }
    .week-nav {
        text-align: center;
        margin-bottom: 1em;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .week-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1em;
    }
    .day-col {
        min-height: 180px;
        display: flex;
        flex-direction: column;
        gap:1rem;
        align-items: stretch;
        text-align: center;
    }
    .day-head {
        font-weight: bold;
        text-align: center;
        border-bottom: 2px solid;
        padding: 0.5em;
        color: white;
        font-size: 1.2rem;
    }
    .no-lessons {
        color: white;
        font-weight: bold;
        background: rgb(133,33,33);
        padding: 1em;
        border-radius: 8px;
        text-align: center;
        margin-top: 2em;
        opacity: 0.8;
    }
    .no-lessons:hover {
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        transform: scale(1.02);
        transition: transform 0.2s, box-shadow 0.2s;
        opacity: 1;
    }
    .green h4 {
        background: rgb(70, 179, 99);
    }
    .red h4 {
        background: rgb(255, 99, 71);
    }
    .orange h4 {
        background: rgb(255, 165, 0);
    }
    .blue h4 {
        background: rgb(70, 130, 180);
    }
    .yellow h4 {
        background: rgb(255, 255, 102);
    }
    .thumbhead {
        display: grid;
        grid-template-columns: 1fr 4fr 1fr;
        gap: 0.2rem;
        color: black;
        width:100%;
        overflow-x: hidden;
        border-radius: 8px;
        padding: 0;
        opacity: 0.85;
        & div {
            background: rgb(244, 244, 244);
        }
        & h4 {
            font-weight: 900;
            font-size: 1rem;
            margin: 0;
            padding: 0.5rem;
        }
        & p {   
            font-size: 1.5rem;
            margin: 0;
            padding: 1rem 0;
        }
    }
    .thumbhead:hover {
        box-shadow: 0 0 10px rgba(0,0,0,0.2);
        transform: scale(1.02);
        transition: transform 0.2s, box-shadow 0.2s;
        opacity: 1;
    }
    .modal .form-field-validation-error, .form-field-validation-error {
        border: 1px #ff0000 solid !important;
    }
</style>