<div id="title">
    <h1>Summer Camp Registrations</h1>
</div>

<div id="stat-panel">
    <style>
        /* ---- Stats panel ---- */
        #stat-cards {
            display:block!important;
        }
        #stat-panel {
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
        <?php if (!empty($email_shift)): ?>
            <?php $unsent = $email_shift['recipient_count'] - $email_shift['sent_count']; ?>
            <div class="camp-email-bar" style="text-align: center;margin-block-end: 1rem;display:grid;">
                <button type="button" class="email-btn" onclick="openModal('camp-email-modal')" style="width: fit-content; margin-inline: auto;margin-block-end:1rem;">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i> Siųsti priminimą · Pamaina <?= $email_shift['num'] ?> · <?= $email_shift['recipient_count'] ?> gavėjams
                </button>
                <?php if ($email_shift['last_sent']): ?>
                    <span class="email-sent-note">Jau išsiųsta: <?= date('Y-m-d H:i', strtotime($email_shift['last_sent'])) ?> (<?= $email_shift['sent_count'] ?>/<?= $email_shift['recipient_count'] ?>)</span>
                <?php endif; ?>
                <div id="send-result"></div>
            </div>

            <div class="modal" id="camp-email-modal" style="display:none">
                <div class="modal-heading"><i class="fa fa-envelope"></i> Siųsti laišką pamainai <?= $email_shift['num'] ?></div>
                <div class="modal-body">
                    <p>Pamaina: <strong><?= out($email_shift['label']) ?></strong></p>
                    <?php if ($unsent > 0): ?>
                        <p>Bus išsiųsta <strong><?= $unsent ?></strong> dar negavusiems dalyviams.</p>
                        <button class="email-confirm" mx-post="camps/send_shift_email/<?= $email_shift['num'] ?>" mx-target="#send-result" mx-indicator="#send-spinner" mx-close-on-success="true" onclick="closeModal()">Siųsti</button>
                    <?php else: ?>
                        <p>Visiems šios pamainos dalyviams jau išsiųsta.</p>
                    <?php endif; ?>
                    <?php if ($email_shift['sent_count'] > 0): ?>
                        <button class="email-resend" mx-post="camps/send_shift_email/<?= $email_shift['num'] ?>" mx-vals='{"resend":"1"}' mx-target="#send-result" mx-indicator="#send-spinner" mx-close-on-success="true" onclick="closeModal()">Siųsti visiems iš naujo (<?= $email_shift['recipient_count'] ?>)</button>
                    <?php endif; ?>
                    <button type="button" class="email-cancel" onclick="closeModal()">Atšaukti</button>
                    <span id="send-spinner" style="display:none"><i class="fa fa-spinner fa-spin"></i> Siunčiama…</span>
                </div>
            </div>
        <?php else: ?>
            <div class="camp-email-bar muted">Pasirink pamainą laiškams siųsti.</div>
        <?php endif; ?>
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
    .camp-email-bar {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #eee;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem 1rem;
    }
    .camp-email-bar.muted {
        color: #888;
        font-style: italic;
    }
    .camp-email-bar .email-btn {
        background: #2e7d52;
        color: #fff;
        border: 0;
        border-radius: 5px;
        padding: 0.5rem 0.9rem;
        cursor: pointer;
        margin: 0;
    }
    .camp-email-bar .email-sent-note {
        font-size: 0.85rem;
        color: #777;
    }
    #send-result { flex-basis: 100%; }
    .send-result-box { font-size: 0.9rem; }
    .send-result-box .sr-ok { color: #2e7d52; margin: 0.25rem 0; }
    .send-result-box .sr-fail { color: #c0392b; margin: 0.25rem 0; }
    .send-result-box .sr-none { color: #777; margin: 0.25rem 0; }
    .modal .email-resend { background: #b9770e; color: #fff; border: 0; }
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

</div>