<?php
/**
 * UDOCHESSE FOOD NETWORK - BATCH 8 BUILDER
 * Turns the home page hero into a slideshow fed by the gallery photos.
 * Patches: index.php, assets/css/style.css
 * Run once:  php build_batch8.php
 */

$indexPath = 'index.php';
$cssPath   = 'assets/css/style.css';

if (!is_file($indexPath) || !is_file($cssPath)) {
    exit("Run this from the project root (index.php and assets/css/style.css must exist).\n");
}

$index = file_get_contents($indexPath);

if (strpos($index, 'hero-slides') !== false) {
    exit("Already patched. Nothing to do.\n");
}

/* ---------- 1. Add the query that fetches slides ---------- */
$query = <<<'FILE_END'
// Hero slideshow: the 6 newest visible gallery photos (falls back to the plain banner if none)
$heroSlides = $pdo->query(
    'SELECT title, image FROM gallery WHERE is_active = 1 ORDER BY created_at DESC, id DESC LIMIT 6'
)->fetchAll();

FILE_END;

$needle = "include __DIR__ . '/includes/header.php';";
if (substr_count($index, $needle) !== 1) {
    exit("Could not find the header include in index.php. Send me index.php and I will patch it.\n");
}
$index = str_replace($needle, $query . $needle, $index);

/* ---------- 2. Replace the hero section ---------- */
$start = strpos($index, '<!-- ================= HERO ================= -->');
$end   = strpos($index, '<!-- ================= SERVICES ================= -->');

if ($start === false || $end === false || $end < $start) {
    exit("Could not find the hero section in index.php. Send me index.php and I will patch it.\n");
}

$newHero = <<<'FILE_END'
<!-- ================= HERO ================= -->
<section class="hero">

    <?php if ($heroSlides): ?>
        <div id="heroCarousel" class="carousel slide carousel-fade hero-slides"
             data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false" aria-hidden="true">
            <div class="carousel-inner">
                <?php foreach ($heroSlides as $i => $slide): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= e(UPLOAD_URL . $slide['image']) ?>" alt=""
                             <?= $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="hero-overlay"></div>
    <?php endif; ?>

    <div class="container hero-content">
        <div class="row">
            <div class="col-lg-8">
                <span class="badge-since">Serving since <?= e(setting('founded_year', '2019')) ?></span>
                <h1>Great food for every <span class="text-gold">celebration</span></h1>
                <p class="lead mt-3">
                    Event catering, custom cakes and small chops, prepared with care and delivered
                    anywhere in Port Harcourt and the Niger Delta.
                </p>
                <div class="d-flex flex-wrap gap-2 mt-4">
                    <a href="<?= url('book.php') ?>" class="btn btn-brand btn-lg">
                        <i class="bi bi-calendar-check me-1"></i> Request a Quote
                    </a>
                    <a href="<?= e(tel_link()) ?>" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-telephone-fill me-1"></i> Call Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

FILE_END;

$index = substr($index, 0, $start) . $newHero . substr($index, $end);
file_put_contents($indexPath, $index);
echo "Patched: $indexPath\n";

/* ---------- 3. Add the slideshow styles ---------- */
$css = <<<'FILE_END'


/* ---------- Hero slideshow (Batch 8) ---------- */
.hero { position: relative; overflow: hidden; }

.hero-slides,
.hero-slides .carousel-inner,
.hero-slides .carousel-item { height: 100%; }

.hero-slides { position: absolute; inset: 0; z-index: 0; }

.hero-slides .carousel-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Dark green overlay keeps the white text readable on any photo */
.hero-overlay {
    position: absolute;
    inset: 0;
    z-index: 1;
    background: linear-gradient(90deg, rgba(13, 59, 32, .88) 0%, rgba(13, 59, 32, .62) 55%, rgba(13, 59, 32, .35) 100%);
}

.hero-content { position: relative; z-index: 2; }

@media (max-width: 767.98px) {
    .hero-overlay { background: rgba(13, 59, 32, .75); }
}
FILE_END;

file_put_contents($cssPath, $css, FILE_APPEND);
echo "Patched: $cssPath\n";

echo "\nBATCH 8 DONE.\n";
echo "Upload photos in Admin > Gallery, then refresh the home page with Ctrl + F5.\n";