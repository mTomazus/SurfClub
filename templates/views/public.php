<!DOCTYPE html>
<html lang="en">

<head><?= Template::partial('partials/head') ?></head>

<body>
	<div class="wrapper bg-grad">

		<header class=""><?= Template::partial('partials/header') ?></header> 
		<?= Template::partial('partials/meganav') ?>
		<main class='container-xl'><?= Template::display($data) ?></main>

	</div>
	
	<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
	<script src="<?= BASE_URL ?>js/app.js"></script>

	<style>
		body {
			overflow-x:hidden;
		}
		.bg-grad {
			background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
			background-size: 400% 400%;
			animation: gradient 15s ease infinite;
		}
		@keyframes gradient {
			0% {
				background-position: 0% 50%;
			}
			50% {
				background-position: 100% 50%;
			}
			100% {
				background-position: 0% 50%;
			}
		}
		header {
			width: 100%;
			box-sizing: border-box;
			position: sticky;
			max-height: 65px;
			top: 0;
			align-items:center;
			box-shadow: black 0px 1px 10px;
			background-image: linear-gradient(to right, #2f78a8, #114161);
			font-family: "Anton", sans-serif;
			z-index: 5;
		}
		header a {
			color: lightskyblue;
			transition: color 0.5s ease;
		}
		header a:hover {
			color: white;
		}
		.logo {
			color: white;
			font-family: serif;
			text-transform: uppercase;
			letter-spacing: 5px;
			display: flex;
			align-items: center;
			justify-content: space-between;
		}
		.logo-pic img {
			height: max(4vh, 36px);
			filter: drop-shadow(0 0.2rem 0.2rem #00000075);
			vertical-align: auto;
		}
		.logo-pic:hover {
			filter: brightness(50%) sepia(100) saturate(100) hue-rotate(25deg) drop-shadow(0 0.2rem 0.2rem #00000075);
		}
		.logo a {
			font-size:2.5vw;
			text-align:center;
			letter-spacing: 2px;
		}
		#top-nav {
			padding: 0;
		}
		#top-nav a {
			letter-spacing: 2px;
			text-decoration: none;
			font-size: 1.5vw;
		}
		#slide-nav {
			background-color: var(--primary-darker);
		}
		#slide-nav ul {
			margin-top:5rem;
		}
		#slide-nav li {
			margin:1rem;
		}
		.burger {
			z-index: 5;
		}
		.burger div {
			width: 30px;
			height: 3px;
			background-color: lightskyblue;
			margin: 7px auto;
			border-radius: 15px;
			transition: all 0.3s ease;
			z-index: 5;
		}
		.show-burger .line1 {
			transform: rotate(-45deg) translate(-7px, 7px);
		}
		.show-burger .line2 {
			opacity: 0;
		}
		.show-burger .line3 {
			transform: rotate(45deg) translate(-7px, -7px);
		}
		.meganav-links {
			display: flex;
			position: fixed;
			transform: translateX(100%);
			opacity: 0;
			filter: blur(8px);
			transition: transform ease-in-out 1s, opacity ease-in-out 1s, filter ease-in-out 1s;
			width: 100%;
			top: 65px;
			background: #296c99bf;
			height: calc(100vh - 65px);
			padding: 1rem;
			text-decoration: none;
			z-index: 4;
		}
		.meganav-links a{
			color: white;
			font-family: serif;
			text-decoration: none;
		}
		.meganav-links li{
			width: 33%;
			margin:0 auto;
			list-style-type:none;
			text-align: center;
		}
		.meganav-links ul{
			padding: 0;
		}
		.meganav-links span {
			font-size: x-large;
			border-bottom: 1px solid;
		}
		
		.mega-active {
			transform: translateX(0%);
			opacity: 1;
			filter: blur(0);
		}
		.d-flex {
			display: flex;
		}
		.justify-content-around {
			justify-content: space-around;
		}
		.justify-content-evenly {
			justify-content: space-evenly;
		}
		.flex-column {
			flex-direction:column;
		}
		.blur {
			filter: blur(8px);
		}
	</style>
</body>
</html>