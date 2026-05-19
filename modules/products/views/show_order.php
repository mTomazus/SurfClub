<h1><?= out($headline) ?></h1>
<?php flashdata(); ?>
<p><?= anchor('products/orders', '← All Orders', ['class' => 'button alt']) ?></p>

<div class="two-col">
    <div class="card">
        <div class="card-heading">Customer Details</div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Name</div>
                    <div><?= out($order->customer_name) ?></div>
                </div>
                <div class="row">
                    <div>Email</div>
                    <div><?= out($order->email) ?></div>
                </div>
                <div class="row">
                    <div>Phone</div>
                    <div><?= out($order->phone) ?></div>
                </div>
                <div class="row">
                    <div>Delivery</div>
                    <div><?= out($order->delivery) ?></div>
                </div>
                <?php if (!empty($order->address)): ?>
                <div class="row">
                    <div>Address</div>
                    <div><?= out($order->address) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-heading">Order Status</div>
        <div class="card-body">
            <div class="record-details">
                <div class="row">
                    <div>Status</div>
                    <div><?= out($order->status) ?></div>
                </div>
                <?php if (!empty($order->payment_reference)): ?>
                <div class="row">
                    <div>Payment Ref</div>
                    <div><?= out($order->payment_reference) ?></div>
                </div>
                <?php endif; ?>
                <?php if (!empty($order->created_at ?? $order->date ?? '')): ?>
                <div class="row">
                    <div>Date</div>
                    <div><?= out($order->created_at ?? $order->date) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-heading">Order Items</div>
    <div class="card-body">
        <?php if (!empty($items)): ?>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th style="text-align:right;">Price</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($items as $item):
                    $subtotal = $item->price * $item->quantity;
                    $total += $subtotal;
                ?>
                <tr>
                    <td><?= out($item->name) ?></td>
                    <td style="text-align:right;">&euro;<?= number_format($item->price, 2) ?></td>
                    <td style="text-align:right;"><?= (int)$item->quantity ?></td>
                    <td style="text-align:right;">&euro;<?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" style="text-align:right;">Total</th>
                    <th style="text-align:right;">&euro;<?= number_format($total, 2) ?></th>
                </tr>
            </tfoot>
        </table>
        <?php else: ?>
        <p>No items found for this order.</p>
        <?php endif; ?>
    </div>
</div>
