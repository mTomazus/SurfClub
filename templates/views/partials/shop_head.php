<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="abstract" content="Klaipėdos banglenčių mokykla Melnragėje prie pat Baltijos jūros. Išmokite plaukti banglente Baltijos vandenyse. Išsinomuokite banglentes ir pirmyn.">
<meta name="google-site-verification" content="vI4-jaGremmKST9DX1EVLfkCVGlQsapPPlLqwrz7hJ4" />
<meta name="author" content="VšĮ Banglentė">
<meta name="description" content="Banglenčių sporto klubas - Molas surf club.">
<meta http-equiv="Cache-control" content="no-cache">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&display=swap" rel="stylesheet"><base href="<?= BASE_URL ?>">
<link rel="icon" type="image/x-icon" href="images/favicon.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/trongate.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/app.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/shop.css">
<?php
    $segment = segment(2);
    if (strlen($segment) !== 0) {
        $title = ucfirst(segment(2));
    } else {
        $title = "Parduotuvė";
    }
?>
<title>Molas Surf Club - <?= $title ?></title>
