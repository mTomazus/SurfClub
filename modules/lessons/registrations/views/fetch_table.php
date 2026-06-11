<table class="ls-reg-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Contact</th>
        </tr>
    </thead>
    <tbody>
        <?php $i = 1; foreach ($rows as $row): ?>
            <tr>
                <td class="ls-reg-num"><?= $i++ ?></td>
                <td><?= out($row['name']) ?></td>
                <td><?= out($row['email']) ?><span class="ls-reg-sub"><?= out($row['phone']) ?></span></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
