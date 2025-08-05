<h1 id="title">Scheduled Events</h1>
<div id="event-container" class="container">	
	<h2 class="mt-1">Scheduled Surf Events</h2>
	<h3 class="mb-1">Add New Event Here</h3>
	<button mx-get="events/event_form" mx-select="#event-form" mx-build-modal='{
		"id": "event-modal",
		"modalHeading": "Create Event Schedule"}'>Create New Event</button>
	<h1 class="mt-2 text-center mb-1">All Scheduled Events</h1>	
	<div id="information"></div>
	<div id="loading-events" class="mt-3 mx-indicator"></div>
	<div id="events-container" mx-get="events/fetch_table" mx-trigger="load" mx-indicator="#loading-events">

		<?php
		if (empty($rows)) {
			echo '<p class="blink xl">no events</p>';
			return;
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
                    <td><?= date('m/d - H:i', strtotime($row->start_time)) ?></td>
					<td><p class="lg"><?= out($row->title) ?></p><br><?= out($row->description) ?></td>
					<td><a href="tel:<?= out($row->phone) ?>"><?= out($row->phone) ?></a><br><?= out($row->email) ?></td>
					<td>
						<button class="danger sm" style="margin-bottom:0.5rem;" mx-post="events/delete_modal/<?= $row->id ?>" mx-build-modal='{
																			"id": "event-delete-modal",
																			"modalHeading": "Delete Event Schedule"}' 
																					mx-on-success="#event-container" 
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

	</div>
</div>
<style>
    input, textarea {
        width: 100%;
        margin: 0;
        height: 2rem;
    }
    label {
        font-weight: bold;
        margin: 0;
    }
    form div {
        flex-grow:1;
    }
</style>