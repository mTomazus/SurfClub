<div id="title" style="display:none"><h1>Coupons</h1></div>

<div id="stat-panel">
    <a class="stat-card">
        <span class="stat-label">Module</span>
        <span class="stat-count" style="font-size:1.1rem">Coupons</span>
    </a>
</div>

<div id="coupons" class="container">
    <h2 class="mt-1">Surf Coupons</h2>
    <h3 class="mb-1">Add Coupons Here</h3>
    <button id="form" mx-get="coupons/coupon_form" mx-build-modal='{"id": "coupon-modal","modalHeading": "Create New Coupon"}'>Add New Coupon</button>
    <h1 class="mt-1 mb-1">All Your Coupons Here</h1>
    <div id="information"></div>
	<div id="loading-coupons" class="mt-3 mx-indicator"></div>
    <div id="coupons-container" mx-get="coupons/fetch_table" mx-trigger="load" mx-indicator="#loading-coupons">

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
                            <td style="display:flex;border:none;gap:1rem;flex-direction:row;justify-content:space-around;">
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
    </div>

    <div id="load"></div>
    <div id="result" mx-get="coupons/show_all" mx-trigger="load" mx-target="#load" class="mb-3"></div>
