<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Our Services';
$currentPage     = 'services';
$metaDescription = 'Event catering, custom cakes, small chops and full event packages in Port Harcourt, Rivers State and across the Niger Delta.';

$pdo = db();

$services = $pdo->query(
    'SELECT id, title, slug, icon, short_desc, description, image
       FROM services WHERE is_active = 1 ORDER BY sort_order'
)->fetchAll();

// Group menu items by service so each service can list its items
$itemsByService = [];
$items = $pdo->query(
    'SELECT service_id, name FROM menu_items WHERE is_active = 1 ORDER BY name'
)->fetchAll();
foreach ($items as $item) {
    $itemsByService[$item['service_id']][] = $item['name'];
}

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>Our Services</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Services</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ================= SERVICES LIST ================= -->
<?php foreach ($services as $index => $s): ?>
    <section class="section <?= $index % 2 === 0 ? '' : 'bg-cream' ?>" id="<?= e($s['slug']) ?>">
        <div class="container">
            <div class="row align-items-center g-5 <?= $index % 2 === 0 ? '' : 'flex-lg-row-reverse' ?>">

                <div class="col-lg-7 reveal">
                    <div class="icon-box"><i class="bi <?= e($s['icon'] ?: 'bi-star') ?>"></i></div>
                    <h2><?= e($s['title']) ?></h2>
                    <p class="lead text-muted"><?= e($s['short_desc']) ?></p>
                    <?php if ($s['description']): ?>
                        <p class="text-muted"><?= e($s['description']) ?></p>
                    <?php endif; ?>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <a href="<?= url('book.php?service=' . urlencode($s['slug'])) ?>" class="btn btn-brand">
                            <i class="bi bi-calendar-check me-1"></i> Request a Quote
                        </a>
                        <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I would like to enquire about ' . $s['title'] . '.')) ?>"
                           target="_blank" rel="noopener" class="btn btn-outline-green">
                            <i class="bi bi-whatsapp me-1"></i> Ask on WhatsApp
                        </a>
                    </div>
                </div>

                <div class="col-lg-5 reveal">
                    <?php if ($s['image']): ?>
                        <img src="<?= e(UPLOAD_URL . $s['image']) ?>" alt="<?= e($s['title']) ?>"
                             class="img-fluid rounded-4 shadow mb-3" loading="lazy">
                    <?php endif; ?>

                    <?php if (!empty($itemsByService[$s['id']])): ?>
                        <div class="form-card">
                            <h5 class="mb-3">What we offer</h5>
                            <ul class="list-unstyled mb-3">
                                <?php foreach ($itemsByService[$s['id']] as $name): ?>
                                    <li class="mb-2">
                                        <i class="bi bi-check-circle-fill text-gold me-2"></i><?= e($name) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <a href="<?= url('menu.php#service-' . $s['id']) ?>" class="fw-medium">
                                See the full menu <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </section>
<?php endforeach; ?>

<!-- ================= CALL TO ACTION ================= -->
<section class="cta-band">
    <div class="container reveal">
        <h2>Not sure what you need?</h2>
        <p class="lead mb-4">Tell us about your event and we will recommend the right combination.</p>
        <a href="<?= url('book.php') ?>" class="btn btn-brand btn-lg me-2 mb-2">Request a Quote</a>
        <a href="<?= e(tel_link()) ?>" class="btn btn-outline-light btn-lg mb-2">
            <i class="bi bi-telephone-fill me-1"></i> Call <?= e(setting('phone')) ?>
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>