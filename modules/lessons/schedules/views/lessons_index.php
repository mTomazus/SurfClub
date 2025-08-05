<h1 id="title">Lessons</h1>
<div id="lesson-container" class="container">
	<h2 class="mt-1">Lesson Schedule</h2>
	<h3 class="mb-1">Add New Schedule</h3>
	<button mx-get="lessons-schedules/lesson_form" mx-build-modal='{
		"id": "lesson-modal",
		"modalHeading": "Create New Lesson Schedule"}'>Create New Lesson</button>
	<h2 class="mt-1 mb-1">Current Schedules</h2>	
	<div id="information"></div>
	<div id="loading-lessons" class="mt-3 mx-indicator"></div>
	<div id="lessons-container" mx-get="lessons-schedules/fetch_lessons" mx-trigger="load" mx-indicator="#loading-lessons">

		<?php
		if (empty($rows)) {
			return;
		}
		?>

		<table>
			<thead>
				<tr>
					<th>Lesson</th>
					<th>Time</th>
					<th>Available</th>
					<th>Reserved</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($rows as $row) { 
					?>
				<tr>
					<td><?php
						$lesson_types = [
							1 => 'Grupinė',
							2 => 'Privati',
							3 => 'Dviem',
							4 => 'Paketas'
							];
						echo $lesson_types[$row->lesson_id] ?? 'Nenurodyta'; 
					?></td>
					<td><?= out($row->date) ?><br><?= out(date('H:i', strtotime($row->start_time))) ?></td>
					<td><?= out($row->available_places - $row->reserved_places) ?></td>
					<td><a class="button <?php if($row->reserved_places > 0) { echo 'success';} ?> mt-0" mx-get="lessons-registrations/fetch/<?= $row->id ?>" mx-build-modal='{
																			"id": "registration-modal",
																			"modalHeading": "Lesson Registrations"}'><?= out($row->reserved_places) ?></a></td>
					<td class="justify-evenly">
						<button class="danger sm" mx-post="lessons-schedules/delete_lesson/<?= $row->id ?>" 
																					mx-on-success="#lessons-container" 
																					mx-target="#information">Delete</button>
						<button class="inverse sm" mx-get="lessons-schedules/lesson_form/<?= $row->id ?>" mx-build-modal='{
																			"id": "lesson-modal",
																			"modalHeading": "Edit Lesson Schedule"}'>Edit</button>
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
	h2, h1 {
        color: floralwhite;
        text-transform: capitalize;
        text-align: center;
        margin-bottom: 20px;
    }
	#information {
		padding: 0.5rem;
		background: white;
		color: seagreen;
		margin: 1rem;
	}
</style>