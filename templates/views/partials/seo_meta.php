<?php
/**
 * Shared SEO block: canonical, Open Graph / Twitter cards, and JSON-LD.
 * Included from head.php and shop_head.php. Expects (all optional):
 *   $page_title, $page_description, $og_image, $og_type, $extra_json_ld
 */
$canonical   = strtok(current_url(), '?'); // drop query string
$og_image    = (!empty($og_image)) ? $og_image : BASE_URL . 'images/logo.png';
$og_type     = $og_type ?? 'website';
$page_title  = $page_title ?? 'Molas Surf Club';
$page_description = $page_description ?? '';
?>
<link rel="canonical" href="<?= out($canonical) ?>">
<meta property="og:type" content="<?= out($og_type) ?>">
<meta property="og:site_name" content="Molas Surf Club">
<meta property="og:locale" content="lt_LT">
<meta property="og:title" content="<?= out($page_title) ?>">
<meta property="og:description" content="<?= out($page_description) ?>">
<meta property="og:url" content="<?= out($canonical) ?>">
<meta property="og:image" content="<?= out($og_image) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= out($page_title) ?>">
<meta name="twitter:description" content="<?= out($page_description) ?>">
<meta name="twitter:image" content="<?= out($og_image) ?>">
<script type="application/ld+json">
<?= json_encode([
    '@context'  => 'https://schema.org',
    '@type'     => 'SportsActivityLocation',
    'name'      => 'Molas Surf Club',
    'legalName' => 'VšĮ Banglentė',
    'url'       => BASE_URL,
    'logo'      => BASE_URL . 'images/logo.png',
    'image'     => BASE_URL . 'images/logo.png',
    'telephone' => '+37068602356',
    'email'     => 'info@surfclub.lt',
    'address'   => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Vėtros g. 8',
        'addressLocality' => 'Klaipėda',
        'addressCountry'  => 'LT',
    ],
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => 55.731285,
        'longitude' => 21.065808,
    ],
    'sameAs' => ['https://m.me/banglente'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php if (!empty($extra_json_ld)): ?>
<script type="application/ld+json">
<?= json_encode($extra_json_ld, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?php endif; ?>
