<div class="container-xxl">
    <h1>Stovyklos Galerijos</h1>

    <?php if (empty($galleries_by_year)): ?>
        <p class="text-center">Galerijos netrukus bus pridėtos.</p>
    <?php else: ?>
        <?php foreach ($galleries_by_year as $year => $galleries): ?>
            <h2><?= out($year) ?></h2>
            <div class="gallery-index-grid">
                <?php foreach ($galleries as $gallery): ?>
                    <a class="gallery-index-card" href="galleries/pamaina/<?= out($year) ?>/<?= out($gallery->pamaina) ?>">
                        <span class="gallery-index-label">Pamaina <?= out($gallery->pamaina) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
    .container-xxl h1, .container-xxl h2 {
        color: antiquewhite;
        text-align: center;
    }
    .gallery-index-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: center;
        margin-bottom: 2rem;
    }
    .gallery-index-card {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 160px;
        height: 80px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.3);
        border-radius: 8px;
        color: antiquewhite;
        text-decoration: none;
        font-size: 1rem;
        font-family: Silom, monospace;
        transition: background 0.2s, transform 0.2s;
    }
    .gallery-index-card:hover {
        background: rgba(255,255,255,0.25);
        transform: translateY(-2px);
        color: antiquewhite;
    }
</style>
