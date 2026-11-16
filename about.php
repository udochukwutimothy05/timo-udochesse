<?php
require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'About Us';
$currentPage     = 'about';
$metaDescription = 'Learn about Udochesse Food Network, an event catering business in Port Harcourt founded in 2019 and run by Ngozi Timothy.';

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>About Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">About</li>
            </ol>
        </nav>
    </div>
</section>

<!-- ================= STORY ================= -->
<section class="section">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal">
                <span class="text-uppercase fw-semibold small text-gold" style="letter-spacing:.2em">Our story</span>
                <h2 class="mt-2">Catering built on care, since <?= e(setting('founded_year', '2019')) ?></h2>
                <p class="text-muted mt-3">
                    Udochesse Food Network is an event catering business based in Port Harcourt, Rivers State.
                    Founded in <?= e(setting('founded_year', '2019')) ?> and led by
                    <strong><?= e(setting('owner_name', 'Ngozi Timothy')) ?></strong>, we have been active
                    ever since, serving clients who want good food at their celebrations.
                </p>
                <p class="text-muted">
                    We cover every part of event catering: the main meal, the cake and the small chops.
                    Instead of coordinating several vendors, you work with one team from planning to clean-up.
                </p>
                <p class="text-muted">
                    Our service is not limited to Port Harcourt. Wherever your event is in the Niger Delta
                    region, we are available to serve you.
                </p>
                <a href="<?= url('book.php') ?>" class="btn btn-brand mt-2">Request a Quote</a>
            </div>

            <div class="col-lg-6 reveal">
                <div class="form-card">
                    <h4 class="mb-4">At a glance</h4>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-person-badge"></i></div>
                        <div><strong>Led by</strong><br><?= e(setting('owner_name', 'Ngozi Timothy')) ?></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-calendar-event"></i></div>
                        <div><strong>Founded</strong><br><?= e(setting('founded_year', '2019')) ?></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-geo-alt"></i></div>
                        <div><strong>Based in</strong><br><?= e(setting('address')) ?></div>
                    </div>
                    <div class="contact-info-item mb-0">
                        <div class="icon-box"><i class="bi bi-map"></i></div>
                        <div><strong>Service area</strong><br><?= e(setting('service_area')) ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ================= WHAT WE COVER ================= -->
<section class="section bg-cream">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">What we cover</span>
            <h2>Complete event catering</h2>
        </div>

        <div class="row g-4">
            <?php
            $covers = [
                ['bi-egg-fried', 'Food',        'Full meals for your guests, from rice dishes to native soups.'],
                ['bi-cake2',     'Cakes',       'Cakes made to your design, flavour and size.'],
                ['bi-basket2',   'Small chops', 'Platters for cocktails, receptions and parties.'],
            ];
            foreach ($covers as [$icon, $title, $text]): ?>
                <div class="col-md-4 reveal">
                    <div class="card-service text-center">
                        <div class="icon-box"><i class="bi <?= e($icon) ?>"></i></div>
                        <h5><?= e($title) ?></h5>
                        <p class="text-muted mb-0"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= VALUES ================= -->
<section class="section">
    <div class="container">
        <div class="section-title reveal">
            <span class="eyebrow">How we work</span>
            <h2>What you can expect from us</h2>
        </div>

        <div class="row g-4">
            <?php
            $values = [
                ['bi-heart',       'Care in every plate',  'We treat your event like it is our own.'],
                ['bi-clock',       'Reliability',          'We plan around your date and your schedule.'],
                ['bi-sliders',     'Tailored to you',      'Menus and quantities shaped around your guests and budget.'],
                ['bi-chat-heart',  'Clear communication',  'Quick replies by phone, WhatsApp or the quote form.'],
            ];
            foreach ($values as [$icon, $title, $text]): ?>
                <div class="col-sm-6 col-lg-3 reveal">
                    <div class="text-center">
                        <div class="icon-box"><i class="bi <?= e($icon) ?>"></i></div>
                        <h6 class="fw-semibold"><?= e($title) ?></h6>
                        <p class="text-muted small"><?= e($text) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ================= CALL TO ACTION ================= -->
<section class="cta-band">
    <div class="container reveal">
        <h2>Let us cater your next event</h2>
        <p class="lead mb-4">Call <?= e(setting('phone')) ?> or send us your event details.</p>
        <a href="<?= url('book.php') ?>" class="btn btn-brand btn-lg me-2 mb-2">Request a Quote</a>
        <a href="<?= e(tel_link()) ?>" class="btn btn-outline-light btn-lg mb-2">
            <i class="bi bi-telephone-fill me-1"></i> Call Now
        </a>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>