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
<!---------------             PAMAINOS             ------------------------>
<?php if (!empty($sessions)): ?>
<div class="hero hero-pamainos">
    <h2>Pamainos</h2>
    <div class="pamainos-grid">
        <?php foreach ($sessions as $s): ?>
        <?php
            $is_full   = ($s->status === 'full');
            $is_ended  = ($s->status === 'ended');
            $start_fmt = date('d.m', strtotime($s->start));
            $end_fmt   = date('d.m', strtotime($s->end));
        ?>
        <div class="pamaina-card <?= $is_full || $is_ended ? 'pamaina-unavailable' : '' ?>">
            <span class="pamaina-nr"><?= $s->pamaina ?></span>
            <span class="pamaina-dates"><?= $start_fmt ?> &ndash; <?= $end_fmt ?></span>
            <span class="pamaina-price"><?= $s->price ?> €</span>
            <span class="pamaina-status">
                <?php if ($is_ended): ?>Baigėsi
                <?php elseif ($is_full): ?>Pilna
                <?php else: ?>Laisva
                <?php endif; ?>
            </span>
        </div>
        <?php endforeach; ?>
    </div>
    <button class="btn-reg lg"
            mx-get="camps/forma"
            mx-select="form"
            mx-build-modal='{
                "id": "registracijos-forma",
                "modalHeading": "Stovyklos Registracija",
                "width": "460px"
            }'>
        REGISTRACIJA
    </button>
</div>
<?php endif; ?>
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