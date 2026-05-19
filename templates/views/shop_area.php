<!DOCTYPE html>
<html lang="en">

	<head><?= Template::partial('partials/shop_head') ?></head>

	<body>

		<header><?= Template::partial('partials/shop_header', $data) ?></header> 
		
		<main class="shop_area">
			<?php flashdata('<p class="shop-flash">', '</p>'); ?>
			<?= Template::display($data) ?>
			<?= Template::partial('partials/shop_rules') ?>
		</main>

		<?= Template::partial('partials/meganav') ?>

		<footer><?= Template::partial('partials/shop_footer') ?></footer>

		<?= Template::partial('partials/chat_widget') ?>

		<div id="cart-drawer-overlay"></div>
		<aside id="cart-drawer" aria-label="Krepšelis">
			<div id="cart-panel-inner"></div>
		</aside>

		<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
		<script src="<?= BASE_URL ?>js/app.js"></script>
		<script src="<?= BASE_URL ?>js/trongate-datetime.js"></script>

	</body>

</html>