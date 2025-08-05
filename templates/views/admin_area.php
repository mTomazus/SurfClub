<!DOCTYPE html>
<html lang="en">

<head><?= Template::partial('partials/admin_head') ?></head>

<?= Template::partial('partials/admin_slide') ?>

<body>

	<header>
		<?= Template::partial('partials/admin_header') ?>
	</header>
	
	<aside>
		<?= Template::partial('partials/admin_sidebar') ?>
	</aside>

	<main>
		<?= Template::display($data) ?>
	</main>

	<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
	<script src="<?= BASE_URL ?>js/app.js"></script>
	<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>

</body>

</html>