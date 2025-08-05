<div class="container-xxl d-grid">
    <h1>Stovyklos galerija</h1>
    <h2>Pamaina <?= out($pamaina) ?></h2>

    <div class="gallery">
    <?php foreach ($pictures as $picture): ?>
        <a class="gallery-item" href="galleries_module/galleries_pictures/<?= out($update_id) ?>/<?= out($picture) ?>" download>
            <img src="galleries_module/galleries_pictures/<?= out($update_id) ?>/<?= out($picture) ?>" alt="Camp Photo">
    </a>
    <?php endforeach; ?>
    </div>
    <?php Pagination::display($pagination_data); ?>
    <p class="lg">Visos nuotraukos yra atsisiunčiamos. Spustelėkite nuotrauką, kad ją atsisiųstumėte
</div>

<style>
    .container-xxl {
       & h1, h2, p {
            color: antiquewhite;
            text-align: center;
        }
    }
    .pagination {
        margin:auto;
    }
    .gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        justify-content: center;
        padding: 16px;
    }

    .gallery-item {
        flex: 1 1 calc(33.333% - 24px);
        box-sizing: border-box;
        max-width: calc(33.333% - 24px);
    }

    .gallery-item img {
        width: 100%;
        height: auto;
        border-radius: 8px;
        object-fit: cover;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .gallery-item img:hover {
        transform: scale(1.03);
    }

    @media (max-width: 768px) {
        .gallery-item {
            flex: 1 1 calc(50% - 24px);
            max-width: calc(50% - 24px);
        }
    }

    @media (max-width: 480px) {
        .gallery-item {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
</style>