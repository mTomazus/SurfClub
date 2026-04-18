<!DOCTYPE html>
<html lang="en">

<head><?= Template::partial('partials/head') ?></head>
<body>
	
	<header>
		<?= Template::partial('partials/header') ?>
	</header> 
	
	<main class="bg-grad">
		<?= Template::display($data) ?>
	</main>
	
	<?= Template::partial('partials/meganav') ?>
	
	<footer>
		<?= Template::partial('partials/footer') ?>
	</footer>
	
	<?= Template::partial('partials/chat_widget') ?>

	<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
	<script src="<?= BASE_URL ?>js/app.js"></script>
	<script src="<?= BASE_URL ?>js/carousel.js"></script>

</body>

</html>