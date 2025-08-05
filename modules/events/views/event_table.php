<?php
		if (empty($rows)) {
			return;
            echo '<p>no events</p>';
		}
		?>

		<table>
			<thead>
				<tr>
                    <th>Date</th>
					<th>Title / Description</th>
					<th>Phone / Email</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($rows as $row) { 
					?>
				<tr>
                    <td><?= date('m/d H:i', strtotime($row->start_time)) ?></td>
					<td><p class="lg"><?= out($row->title) ?></p><br><?= out($row->description) ?></td>
					<td><a href="tel:<?= out($row->phone) ?>"><?= out($row->phone) ?></a><br><?= out($row->email) ?></td>
					<td>
						<button class="danger sm" style="margin-bottom:0.5rem;" mx-post="events/delete_modal/<?= $row->id ?>" mx-build-modal='{
																			"id": "event-delete-modal",
																			"modalHeading": "Delete Event Schedule"}' 
																					mx-on-success="table" 
																					mx-target="#information">Delete</button>
						<button class="inverse sm" mx-get="events/event_form/<?= $row->id ?>" mx-build-modal='{
																			"id": "event-modal",
																			"modalHeading": "Edit Event Schedule"}'>Edit</button>
					</td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>