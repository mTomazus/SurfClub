<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?= BASE_URL ?>">
    <title>Trongate</title>
    <style>
        body {
            min-height:100vh;
            font-size: 2em;
            background: #222222;
            color: #ddd;
            text-align: center;
            font-family: Avenir;
            background:url("images/inquisition.jpeg") center;
            background-repeat: no-repeat;
            background-size: cover
        }

        h1 {
            margin-top: 2em;
        }

        h1, h2 {
            text-transform: uppercase;
        }

        a {
            color: white;
        }
    </style>
</head>
<body>
    <h1>404 Error : Page Not Found</h1>
    <p>We can't find the page you're looking for.</p>
    <p>You can start over from the main <a href="<?= BASE_URL ?>">page</a>.</p>
    <p>If you feel you need our help, don't hesitate to contact us! <a href="<?= BASE_URL ?>kontaktai"><?= BASE_URL ?>kontaktai/</a></p>
</body>
</html>