<div id="title">
    <h1>Summer Camp Registrations</h1>
</div>

<div id="stats-panel">
    <style>
        /* ---- Stats panel ---- */
        #stat-cards {
            display:block!important;
        }
        #stats-panel {
            margin: 1rem 2rem 1.5rem;
        }
        .stats-summary {
            display: flex;
            gap: 1.5rem;
            justify-content: center;
            margin-bottom: 1rem;
            color: floralwhite;
            font-size: 0.95rem;
        }
        .stats-summary strong {
            font-size: 1.1rem;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 0.6rem;
        }
        .stat-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 6px;
            padding: 0.5rem 0.6rem;
            color: floralwhite;
        }
        .stat-card.stat-full {
            background: rgba(220,50,50,0.25);
            border-color: rgba(220,50,50,0.5);
        }
        .stat-header {
            display: flex;
            align-items: baseline;
            gap: 0.4rem;
            margin-bottom: 0.35rem;
        }
        .stat-num {
            font-size: 1.1rem;
            font-weight: 700;
            line-height: 1;
        }
        .stat-dates {
            font-size: 0.68rem;
            opacity: 0.75;
        }
        .stat-bar-wrap {
            background: rgba(255,255,255,0.15);
            border-radius: 3px;
            height: 6px;
            width: 100%;
            margin-bottom: 0.35rem;
            overflow: hidden;
        }
        .stat-bar {
            height: 100%;
            background: #4caf88;
            border-radius: 3px;
            transition: width 0.3s ease;
        }
        .stat-full .stat-bar {
            background: #e05555;
        }
        .stat-footer {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            opacity: 0.85;
        }
        .stat-paid  { color: #8de0b8; }
        .stat-free  { color: #a8d8ff; }
        .stat-full-label { color: #ff9090; font-weight: 600; }
    </style>
    <?php
    $total_all  = array_sum(array_column($stats, 'total'));
    $total_paid = array_sum(array_column($stats, 'paid'));
    ?>
    <div class="stats-summary">
        <span>Iš viso: <strong><?= $total_all ?></strong></span>
        <span>Apmokėta: <strong><?= $total_paid ?></strong></span>
        <span>Laukia: <strong><?= $total_all - $total_paid ?></strong></span>
    </div>
    <div class="stats-grid">
        <?php foreach ($stats as $num => $p):
            $pct   = $p['max'] > 0 ? round($p['total'] / $p['max'] * 100) : 0;
            $free  = $p['max'] - $p['total'];
            $full  = $p['total'] >= $p['max'];
        ?>
        <div class="stat-card <?= $full ? 'stat-full' : '' ?>">
            <div class="stat-header">
                <span class="stat-num"><?= $num ?></span>
                <span class="stat-dates"><?= $p['dates'] ?></span>
            </div>
            <div class="stat-bar-wrap">
                <div class="stat-bar" style="width:<?= $pct ?>%"></div>
            </div>
            <div class="stat-footer">
                <span><?= $p['total'] ?>/<?= $p['max'] ?> vietų</span>
                <span class="stat-paid"><?= $p['paid'] ?> apmok.</span>
                <?php if ($free > 0): ?>
                    <span class="stat-free"><?= $free ?> laisv.</span>
                <?php else: ?>
                    <span class="stat-full-label">Pilna</span>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<div id="nav-table">
    <h2 class="mt-1">Molas Surf Stovykla</h2>
    <h3 class="mb-1">Pasirink pamainą</h3>
    <div class="nav">
        <?php
        // Define the options for the dropdown menu
            $pamainos = [
                '' => 'Visos Pamainos', '2' => '2 Pamaina',
                '4' => '4 Pamaina',
                '5' => '5 Pamaina', '6' => '6 Pamaina', '7' => '7 Pamaina', '8' => '8 Pamaina',
                '9' => '9 Pamaina', '10' => '10 Pamaina', '11' => '11 Pamaina', '12' => '12 Pamaina'
            ];

            $attributes = [
                'mx-get' => 'camps/index/${this.value}',
                'mx-target' => '.table-responsive',
                'mx-select' => '.table-responsive',
                'mx-push-url' => 'true',
                'mx-trigger' => 'change'
            ];

            echo form_dropdown('pamaina', $pamainos, '', $attributes);
        ?>
    </div>
    <h1 class="mt-1 mb-1 text-center">Stovyklos Registracijos</h1>
<?php if (!empty($registrations)): ?>
    <div class="table-responsive d-sm">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name / Age</th>
                    <th>Ph. / Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php foreach ($registrations as $reg): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $reg->name ?><br><small><?= $reg->age ?> yrs</small></td>
                        <td><a href="tel:<?= $reg->phone ?>"><?= out($reg->phone) ?></a><br><small><?= $reg->email ?></small><br><small><?= $reg->pamaina ?></small></td>
                        <td><?= ucfirst($reg->status) ?><br><small><?= date('Y-m-d H:i', strtotime($reg->date_created)) ?></small></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p>No registrations found.</p>
<?php endif; ?>
</div>

<style>
    h1 {
        text-align: center;
        color: floralwhite;
    }
    .table-responsive {
        background: white;
        margin: 1rem 2rem;
    }
    button {
        margin: 0 0.5rem;
        padding: 0.5em 0.2rem;
    }
    .nav {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(75px, 1fr));
        margin-bottom:1rem;
    }
    th, td {
        text-align:center;
    }
    @media only screen and (min-width: 856px) {
        .d-lg {
            display:none;
        }
    }

    /* ---- Stats panel ---- */
    #stats-panel {
        margin: 1rem 2rem 1.5rem;
    }
    .stats-summary {
        display: flex;
        gap: 1.5rem;
        justify-content: center;
        margin-bottom: 1rem;
        color: floralwhite;
        font-size: 0.95rem;
    }
    .stats-summary strong {
        font-size: 1.1rem;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 0.6rem;
    }
    .stat-card {
        background: rgba(255,255,255,0.08);
        border: 1px solid rgba(255,255,255,0.15);
        border-radius: 6px;
        padding: 0.5rem 0.6rem;
        color: floralwhite;
    }
    .stat-card.stat-full {
        background: rgba(220,50,50,0.25);
        border-color: rgba(220,50,50,0.5);
    }
    .stat-header {
        display: flex;
        align-items: baseline;
        gap: 0.4rem;
        margin-bottom: 0.35rem;
    }
    .stat-num {
        font-size: 1.1rem;
        font-weight: 700;
        line-height: 1;
    }
    .stat-dates {
        font-size: 0.68rem;
        opacity: 0.75;
    }
    .stat-bar-wrap {
        background: rgba(255,255,255,0.15);
        border-radius: 3px;
        height: 6px;
        margin-bottom: 0.35rem;
        overflow: hidden;
    }
    .stat-bar {
        height: 100%;
        background: #4caf88;
        border-radius: 3px;
        transition: width 0.3s ease;
    }
    .stat-full .stat-bar {
        background: #e05555;
    }
    .stat-footer {
        display: flex;
        justify-content: space-between;
        font-size: 0.7rem;
        opacity: 0.85;
    }
    .stat-paid  { color: #8de0b8; }
    .stat-free  { color: #a8d8ff; }
    .stat-full-label { color: #ff9090; font-weight: 600; }
</style>