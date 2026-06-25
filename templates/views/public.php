<!DOCTYPE html>
<html lang="lt">

<head><?= Template::partial('partials/head', $data) ?></head>
<body>
	
	<header>
		<?= Template::partial('partials/header') ?>
	</header>

	<div id="slide-nav-overlay" onclick="toggleSlideNav()"></div>

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