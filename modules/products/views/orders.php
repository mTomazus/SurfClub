<h1><?= out($headline) ?></h1>
<?php flashdata(); ?>
<p>
    <?= anchor('products/manage', '← Products', ['class' => 'button alt']) ?>
</p>
<div class="filter-bar" style="margin-bottom:1em;">
    <?php
    $all_url   = BASE_URL . 'products/orders';
    $statuses  = ['pending' => 'Pending', 'paid' => 'Paid', 'failed' => 'Failed'];
    $active    = 'button';
    $inactive  = 'button alt';
    echo anchor($all_url, 'All', ['class' => ($status_filter === '' ? $active : $inactive)]);
    foreach ($statuses as $key => $label) {
        $url = $all_url . '?status=' . $key;
        echo ' ';
        echo anchor($url, $label, ['class' => ($status_filter === $key ? $active : $inactive)]);
    }
    ?>
</div>
<?php echo Pagination::display($pagination_data); ?>
<?php if (count($rows) > 0): ?>
<table id="results-tbl">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Email</th>
            <th>Delivery</th>
            <th>Status</th>
            <th>Date</th>
            <th style="width:80px;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= out($row->id) ?></td>
            <td><?= out($row->customer_name) ?></td>
            <td><?= out($row->email) ?></td>
            <td><?= out($row->delivery) ?></td>
            <td>
                <?php
                $status_class = ['paid' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
                $cls = $status_class[$row->status] ?? '';
                echo '<span class="badge ' . $cls . '">' . out($row->status) . '</span>';
                ?>
            </td>
            <td><?= out($row->created_at ?? ($row->date ?? '—')) ?></td>
            <td><?= anchor('products/show_order/' . $row->id, 'View', ['class' => 'button alt']) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php
if (count($rows) > 9) {
    unset($pagination_data['include_showing_statement']);
    echo Pagination::display($pagination_data);
}
?>
<?php else: ?>
<p>No orders found<?= $status_filter ? ' with status <strong>' . out($status_filter) . '</strong>' : '' ?>.</p>
<?php endif; ?>
