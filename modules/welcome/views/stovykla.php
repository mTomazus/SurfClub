<link rel="stylesheet" href="welcome_module/css/stovykla.css">
<div id="lang">
    <?php
    $current_lang = $_SESSION['language_code'] ?? 'lt';
    $langs = [
        'lt' => 'LT',
        'en' => 'EN',
        'ru' => 'РУ'
    ];
    $links = [];
    foreach ($langs as $code => $label) {
        if ($current_lang !== $code) {
            $links[] = '<a href="?lang='.$code.'">'.$label.'</a>';
        }
    }
    echo implode(' | ', $links);
    ?>
</div>
<!---------------             HERO 1             ------------------------>
<?= $translated_html ?>
<script src=<?= htmlspecialchars("welcome_module/js/scroll-animate.js") ?>></script>
<style>
    #lang {
        position: fixed;
        top: 65px;
        z-index: 2;
        left: 25px;
        font-size: 0.7rem;
    }
    .hero-3 a {
        box-shadow: 0 0 10px;
        display: block;
        width: 200px;
        margin: auto;
    }
    #lang a {
        color: white;
        text-decoration: none;
    }
    #lang a:hover {
        color: yellow;
    }
    .gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
        margin: 2rem;
    }
</style>