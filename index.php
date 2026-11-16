<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Home';
$currentPage     = 'home';
$metaDescription = 'Udochesse Food Network: event catering, custom cakes and small chops in Port Harcourt, Rivers State and across the Niger Delta. Request a quote today.';

$pdo = db();

// Services (from database)
$services = $pdo->query(
    'SELECT title, slug, icon, short_desc FROM services WHERE is_active = 1 ORDER BY sort_order'
)->fetchAll();

// Approved testimonials only (the section hides itself if there are none)
$testimonials = $pdo->query(
    'SELECT client_name, event_type, message, rating
       FROM testimonials WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 3'
)->fetchAll();

// Latest gallery photos (the section hides itself if there are none)
$gallery = $pdo->query(
    'SELECT title, image FROM gallery WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6'
)->fetchAll();

$yearsActive = max(1, (int) date('Y') - (int) setting('founded_year', '2019'));

// Hero slideshow: the 6 newest visible gallery photos (falls back to the plain banner if none)
$heroSlides = $pdo->query(
    'SELECT title, image FROM gallery WHERE is_active = 1 ORDER BY created_at DESC, id DESC LIMIT 6'
)->fetchAll();

// Give every slide an image address
foreach ($heroSlides as &$slide) {
    $slide['src'] = UPLOAD_URL . $slide['image'];
}
unset($slide);

// No gallery photos yet: use the placeholder photos in assets/images/hero/
if (!$heroSlides) {
    $files = glob(__DIR__ . '/assets/images/hero/*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE) ?: [];
    sort($files);
    foreach (array_slice($files, 0, 6) as $file) {
        $heroSlides[] = ['title' => '', 'src' => url('assets/images/hero/' . rawurlencode(basename($file)))];
    }
}
include __DIR__ . '/includes/header.php';
?>

<!-- ================= HERO ================= -->
<section class="hero">

    <?php if ($heroSlides): ?>
        <div id="heroCarousel" class="carousel slide carousel-fade hero-slides"
             data-bs-ride="carousel" data-bs-interval="5000" data-bs-pause="false" aria-hidden="true">
            <div class="carousel-inner">
                <?php foreach ($heroSlides as $i => $slide): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <img src="<?= e($slide['src']) ?>" alt=""
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
<!-- ================= SERVICES ================= -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">What we do</span>
            <h2>Everything your event needs</h2>
            <p>From the main meal to the cake and the small chops, one team takes care of it all.</p>
        </div>

        <div class="row g-4">
            <?php foreach ($services as $s): ?>
                <div class="col-md-6 col-lg-3 reveal">
                    <div class="card-service text-center">
                        <div class="icon-box"><i class="bi <?= e($s['icon'] ?: 'bi-star') ?>"></i></div>
                        <h5><?= e($s['title']) ?></h5>
                        <p class="text-muted mb-3"><?= e($s['short_desc']) ?></p>
                        <a href="<?= url('services.php#' . $s['slug']) ?>" class="fw-medium">
                            Learn more <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= WHY CHOOSE US ================= -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5 reveal">
                <span class="eyebrow text-uppercase fw-semibold small text-gold" style="letter-spacing:.2em">Why Udochesse</span>
                <h2 class="mt-2">Food you can trust at your most important moments</h2>
                <p class="text-muted mt-3">
                    Since <?= e(setting('founded_year', '2019')) ?>, Udochesse Food Network has catered
                    events of all sizes. We handle the food so you can enjoy your guests.
                </p>
                <a href="<?= url('about.php') ?>" class="btn btn-outline-green mt-2">Our Story</a>
            </div>

            <div class="col-lg-7">
                <div class="row g-4">
                    <?php
                    $reasons = [
                        ['bi-collection',  'One team, whole event',  'Food, cake and small chops handled together, with one point of contact.'],
                        ['bi-people',      'Any event size',         'Intimate gatherings to large receptions, planned around your guest count.'],
                        ['bi-geo-alt',     'Across the Niger Delta', 'Based in Port Harcourt and available wherever your event takes place in the region.'],
                        ['bi-chat-dots',   'Easy to reach',          'Call, WhatsApp or send a quote request and we respond quickly.'],
                    ];
                    foreach ($reasons as [$icon, $title, $text]): ?>
                        <div class="col-sm-6 reveal">
                            <div class="d-flex gap-3">
                                <div class="icon-box flex-shrink-0"><i class="bi <?= e($icon) ?>"></i></div>
                                <div>
                                    <h6 class="mb-1 fw-semibold"><?= e($title) ?></h6>
                                    <p class="text-muted small mb-0"><?= e($text) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= STATS ================= -->
<section class="section-sm bg-green">
    <div class="container">
        <div class="row text-center g-4 stat-box">
            <div class="col-4 reveal">
                <h3><?= $yearsActive ?>+</h3>
                <span>Years serving</span>
            </div>
            <div class="col-4 reveal">
                <h3><?= count($services) ?></h3>
                <span>Service types</span>
            </div>
            <div class="col-4 reveal">
                <h3>1</h3>
                <span>Region covered: Niger Delta</span>
            </div>
        </div>
    </div>
</section>

<?php if ($gallery): ?>
<!-- ================= GALLERY TEASER ================= -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Our work</span>
            <h2>A taste of what we serve</h2>
        </div>

        <div class="row g-3">
            <?php foreach ($gallery as $g): ?>
                <div class="col-6 col-md-4 reveal">
                    <a href="<?= url('gallery.php') ?>" class="gallery-item d-block">
                        <img src="<?= e(UPLOAD_URL . $g['image']) ?>" alt="<?= e($g['title']) ?>" loading="lazy">
                        <div class="overlay"><?= e($g['title']) ?></div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <a href="<?= url('gallery.php') ?>" class="btn btn-green">View Full Gallery</a>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if ($testimonials): ?>
<!-- ================= TESTIMONIALS ================= -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">Testimonials</span>
            <h2>What our clients say</h2>
        </div>

        <div class="row g-4">
            <?php foreach ($testimonials as $t): ?>
                <div class="col-md-4 reveal">
                    <div class="testimonial-card">
                        <div class="stars mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="bi bi-star<?= $i <= (int) $t['rating'] ? '-fill' : '' ?>"></i>
                            <?php endfor; ?>
                        </div>
                        <p>"<?= e($t['message']) ?>"</p>
                        <strong><?= e($t['client_name']) ?></strong>
                        <?php if ($t['event_type']): ?>
                            <div class="small text-muted"><?= e($t['event_type']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ================= CALL TO ACTION ================= -->
<section class="cta-band">
    <div class="container reveal">
        <h2>Planning an event?</h2>
        <p class="lead mb-4">Tell us the date, the guest count and the location. We will take it from there.</p>
        <a href="<?= url('book.php') ?>" class="btn btn-brand btn-lg me-2 mb-2">Request a Quote</a>
        <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I would like to enquire about catering.')) ?>"
           target="_blank" rel="noopener" class="btn btn-outline-light btn-lg mb-2">
            <i class="bi bi-whatsapp me-1"></i> Chat on WhatsApp
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>