<!DOCTYPE html>
<html lang="en">

	<head><?= Template::partial('partials/users/users_head') ?></head>

	<body>

		<header><?= Template::partial('partials/users/users_header') ?></header>
		
		<?= Template::partial('partials/users/users_slide', $data) ?>
		
		<aside><?= Template::partial('partials/users/users_aside', $data) ?></aside>
		
		<main class="users_area"><?= Template::display($data) ?></main>

		<footer><?= Template::partial('partials/users/users_footer') ?></footer>

	</body>

	<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
	<script src="<?= BASE_URL ?>js/app.js"></script>
	<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>

</html>