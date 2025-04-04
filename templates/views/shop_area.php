<!DOCTYPE html>
<html lang="en">

	<head><?= Template::partial('partials/shop_head') ?></head>

	<body>

		<header><?= Template::partial('partials/shop_header', $data) ?></header> 
		
		<main class="shop_area">
			<?= Template::display($data) ?>
			<?= Template::partial('partials/shop_rules') ?>
		</main>

		<?= Template::partial('partials/meganav') ?>

		<footer><?= Template::partial('partials/shop_footer') ?></footer>

		<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
		<script src="<?= BASE_URL ?>js/app.js"></script>
		<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>

	</body>

</html>