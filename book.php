<?php
require_once __DIR__ . '/includes/forms.php';

$pageTitle       = 'Request a Quote';
$currentPage     = 'book';
$metaDescription = 'Request a catering quote from Udochesse Food Network. Tell us your event date, guest count and location in Port Harcourt or the Niger Delta.';

$pdo      = db();
$services = $pdo->query(
    'SELECT title, slug FROM services WHERE is_active = 1 ORDER BY sort_order'
)->fetchAll();

$old    = pull_old();
$errors = pull_errors();

// Success state (shown once after a booking is saved)
$success = $_SESSION['booking_success'] ?? null;
unset($_SESSION['booking_success']);

// Pre-select a service when arriving from the Services page (?service=cakes)
$selected = $old['services'] ?? [];
if (!$old && !empty($_GET['service'])) {
    foreach ($services as $s) {
        if ($s['slug'] === $_GET['service']) {
            $selected = [$s['title']];
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>Request a Quote</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Request a Quote</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section bg-cream">
    <div class="container">
        <div class="row g-5">

            <!-- ============ FORM / SUCCESS ============ -->
            <div class="col-lg-8">

                <?php if ($success): ?>
                    <div class="form-card text-center py-5">
                        <div class="icon-box"><i class="bi bi-check-lg"></i></div>
                        <h3>Thank you, <?= e($success['name']) ?>!</h3>
                        <p class="text-muted">We have received your request and will contact you shortly.</p>
                        <p class="mb-4">Your reference number:<br>
                            <strong class="fs-4 text-green"><?= e($success['ref']) ?></strong>
                        </p>
                        <p class="small text-muted">Need a faster response? Call us or message us on WhatsApp and quote your reference.</p>
                        <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, my quote request reference is ' . $success['ref'] . '.')) ?>"
                           target="_blank" rel="noopener" class="btn btn-green me-2 mb-2">
                            <i class="bi bi-whatsapp me-1"></i> WhatsApp Us
                        </a>
                        <a href="<?= e(tel_link()) ?>" class="btn btn-outline-green mb-2">
                            <i class="bi bi-telephone-fill me-1"></i> Call <?= e(setting('phone')) ?>
                        </a>
                    </div>

                <?php else: ?>
                    <div class="form-card">
                        <h3 class="mb-1">Tell us about your event</h3>
                        <p class="text-muted mb-4">Fields marked * are required. We will reply with a quote.</p>

                        <?php if ($errors): ?>
                            <div class="alert alert-danger">
                                <strong>Please fix the following:</strong>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $err): ?>
                                        <li><?= e($err) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="<?= url('process_booking.php') ?>" method="post" class="needs-validation" novalidate>
                            <?= csrf_field() ?>

                            <!-- Spam trap: real visitors never see or fill this -->
                            <div class="hp-field" aria-hidden="true">
                                <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="full_name" class="form-label">Full name *</label>
                                    <input type="text" id="full_name" name="full_name" class="form-control"
                                           maxlength="120" required value="<?= e($old['full_name'] ?? '') ?>">
                                    <div class="invalid-feedback">Please enter your name.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone number *</label>
                                    <input type="tel" id="phone" name="phone" class="form-control" data-phone
                                           maxlength="20" minlength="7" required placeholder="e.g. 0703 335 8179"
                                           value="<?= e($old['phone'] ?? '') ?>">
                                    <div class="invalid-feedback">Please enter a valid phone number.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email (optional)</label>
                                    <input type="email" id="email" name="email" class="form-control"
                                           maxlength="150" value="<?= e($old['email'] ?? '') ?>">
                                    <div class="invalid-feedback">Please enter a valid email address.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="event_type" class="form-label">Type of event *</label>
                                    <select id="event_type" name="event_type" class="form-select" required>
                                        <option value="">Choose...</option>
                                        <?php foreach (event_types() as $type): ?>
                                            <option value="<?= e($type) ?>" <?= ($old['event_type'] ?? '') === $type ? 'selected' : '' ?>>
                                                <?= e($type) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">Please choose an event type.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="event_date" class="form-label">Event date *</label>
                                    <input type="date" id="event_date" name="event_date" class="form-control"
                                           data-min-today required value="<?= e($old['event_date'] ?? '') ?>">
                                    <div class="invalid-feedback">Please choose a date that is today or later.</div>
                                </div>

                                <div class="col-md-6">
                                    <label for="guest_count" class="form-label">Number of guests *</label>
                                    <input type="number" id="guest_count" name="guest_count" class="form-control"
                                           min="1" max="100000" required value="<?= e($old['guest_count'] ?? '') ?>">
                                    <div class="invalid-feedback">Please enter the expected number of guests.</div>
                                </div>

                                <div class="col-12">
                                    <label for="event_location" class="form-label">Event location *</label>
                                    <input type="text" id="event_location" name="event_location" class="form-control"
                                           maxlength="255" required placeholder="Venue, area and town"
                                           value="<?= e($old['event_location'] ?? '') ?>">
                                    <div class="invalid-feedback">Please tell us where the event will hold.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label d-block">What do you need?</label>
                                    <?php foreach ($services as $i => $s): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" name="services[]"
                                                   id="svc<?= $i ?>" value="<?= e($s['title']) ?>"
                                                   <?= in_array($s['title'], $selected, true) ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="svc<?= $i ?>"><?= e($s['title']) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="col-12">
                                    <label for="message" class="form-label">Extra details (optional)</label>
                                    <textarea id="message" name="message" rows="4" class="form-control" maxlength="2000"
                                              placeholder="Menu ideas, cake design, budget, special requests..."><?= e($old['message'] ?? '') ?></textarea>
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-brand btn-lg w-100" data-loading>
                                        <i class="bi bi-send me-1"></i> Send Request
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- ============ SIDEBAR ============ -->
            <div class="col-lg-4">
                <div class="form-card mb-4">
                    <h5 class="mb-3">Prefer to talk?</h5>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-telephone-fill"></i></div>
                        <div><strong>Call us</strong><br><a href="<?= e(tel_link()) ?>"><?= e(setting('phone')) ?></a></div>
                    </div>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-whatsapp"></i></div>
                        <div><strong>WhatsApp</strong><br>
                            <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I would like to enquire about catering.')) ?>" target="_blank" rel="noopener">Chat with us</a>
                        </div>
                    </div>
                    <div class="contact-info-item mb-0">
                        <div class="icon-box"><i class="bi bi-clock-fill"></i></div>
                        <div><strong>Hours</strong><br><?= e(setting('business_hours')) ?></div>
                    </div>
                </div>

                <div class="form-card">
                    <h5 class="mb-3">What happens next?</h5>
                    <ol class="ps-3 mb-0 text-muted">
                        <li class="mb-2">You send your event details.</li>
                        <li class="mb-2">We contact you to confirm details and discuss your menu.</li>
                        <li>We send your quote.</li>
                    </ol>
                </div>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>