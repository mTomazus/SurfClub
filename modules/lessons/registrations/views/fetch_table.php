<table style="background:transparent;">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email/Phone</th>
        </tr>
    </thead>
    <tbody class="text-center" style="font-weight: 900;">
        <?php $i = 1; foreach ($rows as $row): ?>
            <tr style="background:transparent;">
                <td><?= $i++ ?></td>
                <td><?= out($row['name']) ?></td>
                <td><?= out($row['email']) ?><br><?= out($row['phone']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>