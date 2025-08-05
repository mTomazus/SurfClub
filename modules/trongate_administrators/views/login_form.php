<?php
echo validation_errors('<div class="error">', "</div>");
$attr['placeholder'] = 'Enter your username here';
$attr['autocomplete'] = 'off';
$btn_attr['class'] = 'alt';
echo form_open($form_location);
echo '<h1>Admin Login</h1>';
echo form_label('username');
echo form_input('username', $username, $attr);
echo form_label('password');
$attr['placeholder'] = str_replace('username', 'password',  $attr['placeholder']);
echo form_password('password', '', $attr);
echo form_label(form_checkbox('remember', 1) . ' remember me');
echo form_submit('submit', 'Submit');
echo form_submit('submit', 'Cancel', $btn_attr);
?>
<?php
echo form_close();
?>
<style>
	body {
		display: flex;
		align-items: flex-start;
		justify-content: center;
		text-align: center;
	}

	form {
		width: auto;;
		font-family: Baskerville;
		text-transform: uppercase;
		color: color(srgb 0.1106 0 0.2518);
		font-weight: 900;
	}

	label {
		text-align: left;
	}

	body>div.container>form {
		color: white;
		text-shadow: 0 0 5px gray;
		max-width: 460px;
		margin: 0 auto;
		margin-top: 14vh;
		background: rgba(85, 16, 131, 0.2);
		-webkit-backdrop-filter: blur(15px);
		padding: 2rem;
		border: 3px solid rgba(113, 40, 193, 0.46);
		border-radius: 15px;
		box-shadow: 0px 0px 5px blueviolet;
		background-image: url('https://lumiere-a.akamaihd.net/v1/images/andor-favicon-sw-512x512_727d55b6.png');
		background-size: 100px 100px;
		background-repeat: no-repeat;
		background-position: 50% 10px;
	}
	input[type="text"],
	input[type="password"] {
		background: rgba(245, 9, 251, 0.22);
		border: 2px solid color(srgb 0.5009 0.2349 0.7641);
		border-radius: 20px;
		box-shadow: 0px 0px 5px blueviolet;
	}
	::placeholder {
		color: white;
		opacity: 0.8; 
		font-weight: 600;
		padding-left: 0.5rem;
	}

	::-ms-input-placeholder { /* Edge 12-18 */
		color: red;
	}

	body>div.container>form>button {
		width: 100%;
	}

	.go-left {
		text-align: left;
	}
	button {
		background: color(srgb 0.3901 0.1905 0.6571);
		border: 2px solid color(srgb 0.268 0.0833 0.5104);
		font-family: Baskerville;
		text-transform: uppercase;
		font-weight: 900;
		color: white;
		padding: 0.5rem;
	}
	button.alt {
		color: white;
		background: color(srgb 0.5009 0.2349 0.7641);
		border: 2px solid color(srgb 0.268 0.0833 0.5104);
	}
	button:hover {
		background: color(srgb 0.268 0.0833 0.5104);
		border: 2px solid color(srgb 0.5009 0.2349 0.7641);
	}
	button.alt:hover {
		background: color(srgb 0.3901 0.1905 0.6571);
		border: 2px solid color(srgb 0.268 0.0833 0.5104);
	}
	.error {
		color: red;
		font-weight: 600;
		margin: 0 auto;
		margin-top: 1rem;
		margin-bottom: 1rem;
	}
	.error::before {
		content: '⚠️ ';
	}
	.error::after {
		content: ' ⚠️';
	}
</style>