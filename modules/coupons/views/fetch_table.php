<?php
    if (empty($rows)) {
        echo '<p class="blink xl">no coupons</p>';
        return;
    }
?>
<table id="all-coupons">
    <thead>
        <tr>
            <th>Code</th>
            <th>Type</th>
            <th>Name</th>
            <th>Price</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ($rows as $row) { ?>
                <tr>
                    <td><?php echo date('Y') . '-' . $row->id; ?></td>
                    <td><?php echo $row->coupon_type; ?></td>
                    <td><?php echo $row->name; ?></td>
                    <td><?php echo $row->price; ?> €</td>
                    <td><?php echo $row->status; ?></td>
                    <td style="display:flex;border:none;gap:1rem;flex-direction:row;justify-content:space-around">
                        <button class="danger sm" mx-post="coupons/delete_modal/<?= $row->id ?>" mx-build-modal='{
                                                                    "id": "delete-modal",
                                                                    "modalHeading": "Delete Coupon"}' 
                                                                            mx-on-success="#coupons-container" 
                                                                            mx-target="#information"><i class="fa fa-trash"></i></button>
                        <button class="inverse sm" mx-get="coupons/coupon_form/<?= $row->id ?>" mx-build-modal='{
                                                                    "id": "event-modal",
                                                                    "modalHeading": "Edit Coupon"}'><i class="fa fa-pencil"></i></button>
                    </td>
                </tr>
            <?php } ?>
    </tbody>
</table>
