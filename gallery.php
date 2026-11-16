<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Gallery';
$currentPage     = 'gallery';
$metaDescription = 'Photos of food, cakes, small chops and events catered by Udochesse Food Network in Port Harcourt and the Niger Delta.';

$categoryLabels = [
    'food'        => 'Food',
    'cakes'       => 'Cakes',
    'small-chops' => 'Small Chops',
    'events'      => 'Events',
];

$photos = db()->query(
    'SELECT title, category, image FROM gallery WHERE is_active = 1 ORDER BY created_at DESC'
)->fetchAll();

// Only show filter buttons for categories that actually have photos
$presentCategories = array_unique(array_column($photos, 'category'));

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>Gallery</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Gallery</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ================= GALLERY ================= -->
<section class="section">
    <div class="container">

        <?php if ($photos): ?>

            <div class="text-center mb-4 reveal">
                <button type="button" class="filter-btn active" data-filter="all">All</button>
                <?php foreach ($categoryLabels as $key => $label): ?>
                    <?php if (in_array($key, $presentCategories, true)): ?>
                        <button type="button" class="filter-btn" data-filter="<?= e($key) ?>"><?= e($label) ?></button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="row g-3">
                <?php foreach ($photos as $p): ?>
                    <div class="col-6 col-md-4 col-lg-3" data-category="<?= e($p['category']) ?>">
                        <div class="gallery-item"
                             data-img="<?= e(UPLOAD_URL . $p['image']) ?>"
                             data-title="<?= e($p['title']) ?>">
                            <img src="<?= e(UPLOAD_URL . $p['image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy">
                            <div class="overlay"><?= e($p['title']) ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>

            <div class="text-center py-5">
                <div class="icon-box"><i class="bi bi-images"></i></div>
                <h4>Photos coming soon</h4>
                <p class="text-muted">We are putting our gallery together. In the meantime, get in touch to talk about your event.</p>
                <a href="<?= url('contact.php') ?>" class="btn btn-brand">Contact Us</a>
            </div>

        <?php endif; ?>

    </div>
</section>

<!-- ================= LIGHTBOX ================= -->
<div class="modal fade" id="lightboxModal" tabindex="-1" aria-label="Photo viewer" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0">
            <div class="text-end">
                <button type="button" class="btn-close btn-close-white mb-2" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <img src="" alt="" class="img-fluid rounded-3">
            <p class="lb-caption text-white text-center mt-2 mb-0"></p>
        </div>
    </div>
</div>

<!-- ================= CALL TO ACTION ================= -->
<section class="cta-band">
    <div class="container reveal">
        <h2>Like what you see?</h2>
        <p class="lead mb-4">Let us do the same for your event.</p>
        <a href="<?= url('book.php') ?>" class="btn btn-brand btn-lg">Request a Quote</a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>