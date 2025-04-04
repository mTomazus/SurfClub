<h1>Summer Camp Registrations</h1>
<div class="nav">
    <?php
        $pamainos = ['visos' => '', '1 Pamaina' => '1',  '2 Pamaina' => '2', '3 Pamaina' => '3',
            '4 Pamaina' => '4', '5 Pamaina' => '5', '6 Pamaina' => '6', '7 Pamaina' => '7',
            '8 Pamaina' => '8', '9 Pamaina' => '9', '10 Pamaina' => '10', '11 Pamaina' => '11',
            '12 Pamaina' => '12',];

        foreach ($pamainos as $label => $value): ?>
            <button mx-get="camps/index/<?= $value ?>" 
                    mx-target=".table-responsive" mx-select=".table-responsive" 
                    mx-push-url="true">
                <?= out($label) ?>
            </button>
    <?php endforeach; ?>
</div>
<?php if (!empty($registrations)): ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name / Age</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Shift</th>
                    <th>Status</th>
                    <th>Date Created</th>
                </tr>
            </thead>
            <tbody>
                <?php $count = 1; ?>
                <?php foreach ($registrations as $reg): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $reg->name ?><br><small><?= $reg->age ?> yrs</small></td>
                        <td><?= $reg->phone ?></td>
                        <td><?= $reg->email ?></td>
                        <td><?= $reg->pamaina ?></td>
                        <td><?= ucfirst($reg->status) ?></td>
                        <td><?= date('Y-m-d H:i', strtotime($reg->date_created)) ?></td>
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
        margin-inline: 2rem;
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
    
</style>