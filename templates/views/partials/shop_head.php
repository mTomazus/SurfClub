<?php
    $page_description = (isset($meta_description) && $meta_description !== '')
        ? $meta_description
        : 'Banglenčių sporto klubas Molas – banglenčių nuoma, pamokos ir surf parduotuvė Klaipėdoje, Melnragėje.';
    $page_title = (isset($meta_title) && $meta_title !== '')
        ? $meta_title . ' – Molas Surf Shop'
        : 'Molas Surf Shop';
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="abstract" content="Klaipėdos banglenčių mokykla Melnragėje prie pat Baltijos jūros. Išmokite plaukti banglente Baltijos vandenyse. Išsinomuokite banglentes ir pirmyn.">
<meta name="google-site-verification" content="vI4-jaGremmKST9DX1EVLfkCVGlQsapPPlLqwrz7hJ4" />
<meta name="author" content="VšĮ Banglentė">
<meta name="description" content="<?= out($page_description) ?>">
<?php if (!empty($robots)): ?><meta name="robots" content="<?= out($robots) ?>">
<?php endif; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Baskervville:ital@0;1&display=swap" rel="stylesheet"><base href="<?= BASE_URL ?>">
<link rel="icon" type="image/x-icon" href="images/favicon.ico">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/app.css">
<link rel="stylesheet" href="<?= BASE_URL ?>css/shop.css">
<title><?= out($page_title) ?></title>
<?= Template::partial('partials/seo_meta', [
    'page_title'       => $page_title,
    'page_description' => $page_description,
    'og_image'         => $og_image ?? null,
    'og_type'          => $og_type ?? 'website',
    'extra_json_ld'    => $json_ld ?? null,
]) ?>
