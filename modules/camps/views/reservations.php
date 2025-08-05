<div id="title">
    <h1>Summer Camp Registrations</h1>
</div>
<div id="nav-table">
    <h2 class="mt-1">Molas Surf Stovykla</h2>
    <h3 class="mb-1">Pasirink pamainą</h3>
    <div class="nav">
        <?php
        // Define the options for the dropdown menu
            $pamainos = [
                '' => 'Visos Pamainos',
                '3' => '3 Pamaina', '4' => '4 Pamaina',
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
</style>