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

	<script src="<?= BASE_URL ?>js/trongate-mx.js"></script>
	<script src="<?= BASE_URL ?>js/app.js"></script>
	<script src="<?= BASE_URL ?>js/carousel.js"></script>

	<style>
		@import url('https://fonts.googleapis.com/css2?family=Protest+Strike&family=Satisfy&display=swap');
		@import url('https://fonts.googleapis.com/css2?family=Protest+Strike&display=swap');
		:root {
		--primary: #4682b4;
		--primary-dark: #38678f;
		--primary-light: #87CEFA;
		--gradient: linear-gradient(to right, #2f78a8, #114161);
		--primary-darker: #294d6b;
		--primary-color: #fff;
		--secondary: #ff0000;
		--border: #c5c5c5;
		--alt: #fff;
		--modal-margin-top: 12vh;
		--clr-light: #f2f3f4f5;
		--clr-dark: #555;
		--clr-primary:#4682b4;
		--clr-light-15:#ffffff30;
		--clr-primary-50:#38678f80;
		}
		body {
			overflow-x:hidden;
			box-sizing:border-box;
			font-family: PT Serif;
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
			position: fixed;
			height: 65px;
			top: 0;
			padding-block:0;
			align-items:center;
			background: transparent;
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			z-index: 5;
			& a {
				color: white;
				transition: color 0.5s ease;
			}
			& a:hover {
				color: var(--primary-light);
			}
			& #header-sm {
				height:65px;
			}
			& #header-lg {
				height:65px;
			}
		}
		footer {
			display: block;
			position: fixed;
			bottom: 0px;
			height: 50px;
			background: transparent;
			backdrop-filter: blur(10px);
			-webkit-backdrop-filter: blur(10px);
			z-index: 2;
			& div {
				display: grid;
				grid-template-columns: repeat(5, 1fr);
			}
			& .fa {
				filter: drop-shadow(2px 2px 2px black);
			}
			& a {
				margin: auto;
				font-size: 35px;
			}
			& svg {
				height: 35px;
				fill: white;
				display: block;
				filter: drop-shadow(2px 2px 2px black);
			}
		}
		main {
			position: absolute;
			padding-top:65px;
			padding-bottom: 55px;
			min-height:calc(100vh - 65px);
		}
		.logo {
			color: white;
			display: flex;
			flex-direction:column;
			align-items: center;
			justify-content: space-between;
			& p {
				font-size: 0.7rem;
				font-weight: 900;
				text-shadow: 2px 2px 2px black;
				margin: 0;
			}
			& a {
				text-align:center;
				text-shadow: 2px 2px 2px black;
				font-weight: 900;
				font-size: 30px;
			}
		}
		.logo-pic img {
			height: 35px;
			filter: drop-shadow(2px 2px 2px black);
			vertical-align: auto;
		}
		.logo-pic:hover {
			filter: brightness(50%) sepia(100) saturate(100) hue-rotate(25deg) drop-shadow(0 0.2rem 0.2rem #00000075);
		}
		#top-nav {
			padding: 0;
			& a {
				text-decoration: none;
				font-size: 17px;
				font-family: PT Serif Caption;
				font-weight: 900;
				text-shadow: 1px 1px 5px black;
			}
		}
		
		#slide-nav {
			background: transparent;
			backdrop-filter: blur(100px);
			-webkit-backdrop-filter: blur(100px);
			display:none;
			top:65px;
			height: calc(100vh - 120px);
			-webkit-transition: all 1s ease-in;
			-moz-transition: all 1s ease-in;
			-o-transition: all 1s ease-in;
			transition: all 1s ease-in;
			& .logo-nav {
				padding:1rem;
			}
		}
		#slide-nav.isShown {
			display: grid;
			grid-template-columns: 1fr 1fr;
			text-align: center;
		}
		.burger {
			z-index: 5;
			filter: drop-shadow(2px 2px 2px black);
			& div {
				width: 30px;
				height: 3px;
				background-color: white;
				margin: 7px auto;
				border-radius: 15px;
				transition: all 0.3s ease;
				z-index: 5;
			}
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
			width: 50%;
			top: 65px;
			right:0;
			background: #296c99bf;
			height: calc(100vh - 65px);
			padding: 1rem;
			text-decoration: none;
			z-index: 4;
			& div {
				font-family: 'Alegreya';
				font-weight: 900;
				font-size: 1rem;
				color:var(--clr-light);
				display:grid;
				grid-template-rows: 2fr 2fr 1fr;
				gap: 2rem;
			}
			& section {
				display: flex;
				justify-content:space-around;
			}
			& section:not(:last-child){
				border-bottom:2px solid var(--clr-light);
			}
			& ul {
				flex-direction:column;
				padding:0;
			}
		}
		.meganav-links a{
			color: white;
			text-decoration: none;
		}
		.meganav-links li{
			width: 33%;
			margin:0 auto;
			list-style-type:none;
			text-align: center;
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
		.space-between {
			justify-content: space-between;
		}
        #checkout-form {
            display: grid;
            grid-template-columns: repeat(2,auto);
            gap: 1rem;
            margin-top: 1rem;
        }
		.blur {
			filter: blur(8px);
		}

		h1, h2, h3 {
			margin:0;
		}
		h2 {
			font-size:1.5em
		}
		h3 {
			font-size:1em
		}
		h4 {
			font-size:0.8em;
		}
		.br-10 {
			border-radius:10px;
		}
		.br-20 {
			border-radius:20px;
		}
	@media only screen and (min-width: 575px) {
		.logo a {
			font-size:35px;
		}
		footer {
			display:none;
		}
	}
    @media only screen and (min-width: 874px) {
		.logo a {
			font-size: 40px;
		}
		.logo-pic img {
			height: 40px;
		}
    }
    @media only screen and (min-width: 1198px) {

	}
	</style>
</body>
</html>