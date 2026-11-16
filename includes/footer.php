</main>

<?php
$footerServices = [];
try {
    $footerServices = db()
        ->query('SELECT title, slug FROM services WHERE is_active = 1 ORDER BY sort_order')
        ->fetchAll();
} catch (PDOException $e) {
    // ignore: footer still renders without the list
}
?>

<footer class="site-footer">
    <div class="container">
        <div class="row gy-4">

            <div class="col-lg-4">
                <h4 class="footer-brand">Udochesse <small>Food Network</small></h4>
                <p class="mt-3">
                    Professional event catering, cakes and small chops in Port Harcourt and across the
                    Niger Delta. Proudly serving since <?= e(setting('founded_year', '2019')) ?>.
                </p>
                <div class="social-links mt-3">
                    <?php if (setting('facebook')):  ?><a href="<?= e(setting('facebook')) ?>"  target="_blank" rel="noopener" aria-label="Facebook"><i class="bi bi-facebook"></i></a><?php endif; ?>
                    <?php if (setting('instagram')): ?><a href="<?= e(setting('instagram')) ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="bi bi-instagram"></i></a><?php endif; ?>
                    <?php if (setting('tiktok')):    ?><a href="<?= e(setting('tiktok')) ?>"    target="_blank" rel="noopener" aria-label="TikTok"><i class="bi bi-tiktok"></i></a><?php endif; ?>
                    <a href="<?= e(whatsapp_link()) ?>" target="_blank" rel="noopener" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="footer-heading">Quick Links</h6>
                <ul class="list-unstyled footer-links">
                    <li><a href="<?= url('index.php') ?>">Home</a></li>
                    <li><a href="<?= url('about.php') ?>">About Us</a></li>
                    <li><a href="<?= url('menu.php') ?>">Menu</a></li>
                    <li><a href="<?= url('gallery.php') ?>">Gallery</a></li>
                    <li><a href="<?= url('book.php') ?>">Request a Quote</a></li>
                    <li><a href="<?= url('contact.php') ?>">Contact</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="footer-heading">Our Services</h6>
                <ul class="list-unstyled footer-links">
                    <?php foreach ($footerServices as $s): ?>
                        <li><a href="<?= url('services.php#' . $s['slug']) ?>"><?= e($s['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="footer-heading">Get in Touch</h6>
                <ul class="list-unstyled footer-contact">
                    <li><i class="bi bi-geo-alt-fill"></i> <?= e(setting('address')) ?></li>
                    <li><i class="bi bi-telephone-fill"></i> <a href="<?= e(tel_link()) ?>"><?= e(setting('phone')) ?></a></li>
                    <?php if (setting('email')): ?>
                        <li><i class="bi bi-envelope-fill"></i> <a href="mailto:<?= e(setting('email')) ?>"><?= e(setting('email')) ?></a></li>
                    <?php endif; ?>
                    <li><i class="bi bi-clock-fill"></i> <?= e(setting('business_hours')) ?></li>
                </ul>
            </div>

        </div>

        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. All rights reserved.</span>
            <span>Serving the Niger Delta since <?= e(setting('founded_year', '2019')) ?></span>
        </div>
    </div>
</footer>

<!-- Floating buttons -->
<a href="<?= e(whatsapp_link('Hello Udochesse Food Network, I would like to enquire about catering.')) ?>"
   class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp">
    <i class="bi bi-whatsapp"></i>
</a>
<button type="button" class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="bi bi-arrow-up"></i>
</button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= url('assets/js/main.js') ?>?v=<?= @filemtime(__DIR__ . '/../assets/js/main.js') ?>"></script>
</body>
</html>