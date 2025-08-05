<!DOCTYPE html>
<html lang="en">

	<head><?= Template::partial('partials/judges_head') ?></head>

	<body>

		<header><?= Template::partial('partials/judges_header', $data) ?></header>
		
		<?= Template::partial('partials/judges_slide', $data) ?>
		
		<aside><?= Template::partial('partials/judges_aside', $data) ?></aside>
		
		<main class="judges_area"><?= Template::display($data) ?></main>
		
		<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
		<script src="<?= BASE_URL ?>js/app.js"></script>
		<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>

	</body>

</html>