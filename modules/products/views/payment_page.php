<div class="payment-section">   
    <div style="pading-inline:2rem;">
        <h2>Order Summary</h2>

        <?php if (!empty($items)) { ?>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                <?php $total = 0; foreach ($items as $item): ?>
                    <?php $line_total = $item->price * $item->quantity; $total += $line_total; ?>
                    <tr>
                        <td><?= $item->name ?></td>
                        <td><?= $item->quantity ?></td>
                        <td>€<?= number_format($item->price, 2) ?></td>
                        <td>€<?= number_format($line_total, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="text-align:right"><strong>Total:</strong></td>
                        <td><strong>€<?= number_format($total, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
        <?php } ?>
    </div>
    <div class="payment-iframe">
        <?php if ($payment_link): ?>
            <iframe src="<?= $payment_link ?>" width="100%" height="600" style="border:1px solid #ccc;"></iframe>
        <?php else: ?>
            <p>Error: payment link not found.</p>
        <?php endif; ?>
    </div>
</div> 
<style>
    .order-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }

    .order-table th, .order-table td {
        padding: 12px;
        border: 1px solid #ddd;
        text-align: center;
    }
    th {
        background:none;
        color:var(--primary-darker)
    }
    iframe {
        border: none!important;
        background: white;
        height:100%;
    }
    .payment-section {
        display:flex;
        & div {
            background:#fdf6ee;
            flex-grow:1;
        }
        .framed {
            background:none
        }
        & body {
            background: white;
            border: none;
            box-shadow: none;
        }
    }
    .payment-iframe {
        height:750px;
    }
</style>