<?php
http_response_code(404);
header('X-Robots-Tag: noindex');

require_once __DIR__ . '/includes/functions.php';

$pageTitle       = 'Page Not Found';
$currentPage     = '';
$metaDescription = 'The page you were looking for could not be found.';

include __DIR__ . '/includes/header.php';
?>

<section class="section">
    <div class="container text-center" style="max-width:640px">
        <div class="icon-box"><i class="bi bi-compass"></i></div>
        <h1 class="display-4">404</h1>
        <h2 class="h3 mb-3">We could not find that page</h2>
        <p class="text-muted mb-4">
            The link may be old or mistyped. Here are some places to go instead.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-2">
            <a href="<?= url('index.php') ?>" class="btn btn-green">Home</a>
            <a href="<?= url('menu.php') ?>" class="btn btn-outline-green">Menu</a>
            <a href="<?= url('book.php') ?>" class="btn btn-brand">Request a Quote</a>
            <a href="<?= e(tel_link()) ?>" class="btn btn-outline-green">
                <i class="bi bi-telephone-fill me-1"></i> <?= e(setting('phone')) ?>
            </a>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>