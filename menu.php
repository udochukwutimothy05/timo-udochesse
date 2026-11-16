<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Menu';
$currentPage     = 'menu';
$metaDescription = 'See what Udochesse Food Network serves: party jollof rice, native soups, small chops, wedding and birthday cakes in Port Harcourt.';

$pdo = db();

$services = $pdo->query(
    'SELECT id, title, icon FROM services WHERE is_active = 1 ORDER BY sort_order'
)->fetchAll();

$itemsByService = [];
$items = $pdo->query(
    'SELECT service_id, name, description, price_from, image
       FROM menu_items WHERE is_active = 1 ORDER BY id'
)->fetchAll();
foreach ($items as $item) {
    $itemsByService[$item['service_id']][] = $item;
}

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>Our Menu</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Menu</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ================= MENU ================= -->
<section class="section">
    <div class="container">

        <div class="section-title reveal">
            <span class="eyebrow">What we serve</span>
            <h2>Menu highlights</h2>
            <p>Prices depend on your guest count and the size of your order, so tell us about your event and we will send a quote.</p>
        </div>

        <?php foreach ($services as $s): ?>
            <?php if (empty($itemsByService[$s['id']])) continue; ?>

            <div class="mb-5" id="service-<?= (int) $s['id'] ?>">
                <h3 class="mb-4 reveal">
                    <i class="bi <?= e($s['icon'] ?: 'bi-star') ?> text-gold me-2"></i><?= e($s['title']) ?>
                </h3>

                <div class="row g-4">
                    <?php foreach ($itemsByService[$s['id']] as $item): ?>
                        <div class="col-md-6 col-lg-4 reveal">
                            <div class="menu-card">
                                <?php if ($item['image']): ?>
                                    <img src="<?= e(UPLOAD_URL . $item['image']) ?>" alt="<?= e($item['name']) ?>"
                                         class="img-fluid rounded-3 mb-3" loading="lazy">
                                <?php endif; ?>
                                <h5><?= e($item['name']) ?></h5>
                                <?php if ($item['description']): ?>
                                    <p><?= e($item['description']) ?></p>
                                <?php endif; ?>
                                <span class="price-tag">
                                    <?= $item['price_from'] !== null
                                        ? 'From ' . e(naira($item['price_from']))
                                        : 'Request a quote' ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (!$items): ?>
            <p class="text-center text-muted">Our menu is being updated. Please call us on <?= e(setting('phone')) ?>.</p>
        <?php endif; ?>

        <div class="text-center mt-5 reveal">
            <p class="text-muted">Looking for something that is not listed? Ask us, we may be able to prepare it.</p>
            <a href="<?= url('book.php') ?>" class="btn btn-brand me-2 mb-2">Request a Quote</a>
            <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I have a question about your menu.')) ?>"
               target="_blank" rel="noopener" class="btn btn-outline-green mb-2">
                <i class="bi bi-whatsapp me-1"></i> Ask on WhatsApp
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>