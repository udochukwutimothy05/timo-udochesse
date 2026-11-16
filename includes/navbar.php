<?php
$navItems = [
    'home'     => ['Home',     'index.php'],
    'about'    => ['About',    'about.php'],
    'services' => ['Services', 'services.php'],
    'menu'     => ['Menu',     'menu.php'],
    'gallery'  => ['Gallery',  'gallery.php'],
    'contact'  => ['Contact',  'contact.php'],
];
?>
<!-- Top bar (hidden on small phones) -->
<div class="topbar d-none d-md-block">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <i class="bi bi-geo-alt-fill me-1"></i> Port Harcourt, Rivers State
            <span class="mx-2">|</span>
            <i class="bi bi-clock me-1"></i> <?= e(setting('business_hours', 'Mon - Sat: 8:00 AM - 7:00 PM')) ?>
        </div>
        <div>
            <a href="<?= e(tel_link()) ?>"><i class="bi bi-telephone-fill me-1"></i> <?= e(setting('phone', '07033358179')) ?></a>
        </div>
    </div>
</div>

<nav class="navbar navbar-expand-lg sticky-top main-nav">
    <div class="container">
        <a class="navbar-brand" href="<?= url('index.php') ?>">
            <span class="brand-name">Udochesse</span>
            <span class="brand-sub">Food Network</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
                data-bs-target="#navMenu" aria-controls="navMenu" aria-expanded="false" aria-label="Toggle menu">
            <i class="bi bi-list fs-1"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                <?php foreach ($navItems as $key => [$label, $file]): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === $key ? 'active' : '' ?>"
                           href="<?= url($file) ?>"><?= e($label) ?></a>
                    </li>
                <?php endforeach; ?>
                <li class="nav-item ms-lg-3 mt-2 mt-lg-0">
                    <a class="btn btn-brand" href="<?= url('book.php') ?>">
                        <i class="bi bi-calendar-check me-1"></i> Request a Quote
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>