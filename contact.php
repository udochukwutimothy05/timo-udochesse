<?php
require_once __DIR__ . '/includes/forms.php';

$pageTitle       = 'Contact Us';
$currentPage     = 'contact';
$metaDescription = 'Contact Udochesse Food Network in Port Harcourt. Call 07033358179, message us on WhatsApp or visit us at Mini Orlu Road, off Ada George Road.';

$old    = pull_old();
$errors = pull_errors();

$mapQuery = urlencode(setting('address', 'Mini Orlu Road, Ada George Road, Port Harcourt, Rivers State, Nigeria'));

include __DIR__ . '/includes/header.php';
?>

<!-- ================= BANNER ================= -->
<section class="page-banner">
    <div class="container">
        <h1>Contact Us</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="<?= url('index.php') ?>">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="row g-5">

            <!-- ============ DETAILS ============ -->
            <div class="col-lg-5 reveal">
                <span class="text-uppercase fw-semibold small text-gold" style="letter-spacing:.2em">Get in touch</span>
                <h2 class="mt-2 mb-4">We would love to hear from you</h2>

                <div class="contact-info-item">
                    <div class="icon-box"><i class="bi bi-telephone-fill"></i></div>
                    <div><strong>Phone</strong><br><a href="<?= e(tel_link()) ?>"><?= e(setting('phone')) ?></a></div>
                </div>
                <div class="contact-info-item">
                    <div class="icon-box"><i class="bi bi-whatsapp"></i></div>
                    <div><strong>WhatsApp</strong><br>
                        <a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I would like to enquire about catering.')) ?>" target="_blank" rel="noopener">Send us a message</a>
                    </div>
                </div>
                <div class="contact-info-item">
                    <div class="icon-box"><i class="bi bi-geo-alt-fill"></i></div>
                    <div><strong>Address</strong><br><?= e(setting('address')) ?></div>
                </div>
                <?php if (setting('email')): ?>
                    <div class="contact-info-item">
                        <div class="icon-box"><i class="bi bi-envelope-fill"></i></div>
                        <div><strong>Email</strong><br><a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></div>
                    </div>
                <?php endif; ?>
                <div class="contact-info-item">
                    <div class="icon-box"><i class="bi bi-clock-fill"></i></div>
                    <div><strong>Opening hours</strong><br><?= e(setting('business_hours')) ?></div>
                </div>
                <div class="contact-info-item">
                    <div class="icon-box"><i class="bi bi-map"></i></div>
                    <div><strong>Service area</strong><br><?= e(setting('service_area')) ?></div>
                </div>
            </div>

            <!-- ============ FORM ============ -->
            <div class="col-lg-7 reveal">
                <div class="form-card">
                    <h3 class="mb-1">Send us a message</h3>
                    <p class="text-muted mb-4">Planning an event? Use the <a href="<?= url('book.php') ?>">quote request form</a> instead.</p>

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

                    <form action="<?= url('process_contact.php') ?>" method="post" class="needs-validation" novalidate>
                        <?= csrf_field() ?>

                        <div class="hp-field" aria-hidden="true">
                            <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Your name *</label>
                                <input type="text" id="name" name="name" class="form-control" maxlength="120"
                                       required value="<?= e($old['name'] ?? '') ?>">
                                <div class="invalid-feedback">Please enter your name.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" data-phone
                                       maxlength="20" value="<?= e($old['phone'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-control" maxlength="150"
                                       value="<?= e($old['email'] ?? '') ?>">
                                <div class="invalid-feedback">Please enter a valid email address.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" maxlength="200"
                                       value="<?= e($old['subject'] ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message *</label>
                                <textarea id="message" name="message" rows="5" class="form-control" minlength="10"
                                          maxlength="3000" required><?= e($old['message'] ?? '') ?></textarea>
                                <div class="invalid-feedback">Please write a short message (at least 10 characters).</div>
                            </div>
                            <div class="col-12">
                                <p class="small text-muted mb-2">Please give us a phone number or an email so we can reply.</p>
                                <button type="submit" class="btn btn-brand btn-lg w-100" data-loading>
                                    <i class="bi bi-send me-1"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ================= MAP ================= -->
<section class="pb-0">
    <iframe title="Udochesse Food Network location"
            src="https://maps.google.com/maps?q=<?= $mapQuery ?>&output=embed"
            width="100%" height="380" style="border:0; display:block;"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>