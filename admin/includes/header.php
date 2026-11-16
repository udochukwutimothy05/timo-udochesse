<?php
/**
 * Set before including:  $adminTitle  and  $adminPage (nav key)
 */
require_once __DIR__ . '/auth.php';

$adminTitle = $adminTitle ?? 'Admin';
$adminPage  = $adminPage ?? '';

$pdo         = db();
$newBookings = (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn();
$unreadMsgs  = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn();
$flash       = get_flash();

// key => [label, file, icon, badge count]. A link appears only when its page exists.
$adminNav = [
    'dashboard'    => ['Dashboard',    'dashboard.php',    'bi-speedometer2', 0],
    'bookings'     => ['Bookings',     'bookings.php',     'bi-calendar-check', $newBookings],
    'messages'     => ['Messages',     'messages.php',     'bi-envelope', $unreadMsgs],
    'gallery'      => ['Gallery',      'gallery.php',      'bi-images', 0],
    'services'     => ['Services',     'services.php',     'bi-grid', 0],
    'menu'         => ['Menu Items',   'menu.php',         'bi-egg-fried', 0],
    'testimonials' => ['Testimonials', 'testimonials.php', 'bi-chat-quote', 0],
    'settings'     => ['Settings',     'settings.php',     'bi-gear', 0],
];

// Build the menu once, print it in both the desktop sidebar and the phone drawer
ob_start();
?>
<div class="admin-brand">Udochesse <small>Admin</small></div>
<ul class="nav flex-column admin-nav">
    <?php foreach ($adminNav as $key => [$label, $file, $icon, $count]): ?>
        <?php if (!file_exists(__DIR__ . '/../' . $file)) continue; ?>
        <li class="nav-item">
            <a class="nav-link <?= $adminPage === $key ? 'active' : '' ?>" href="<?= admin_url($file) ?>">
                <i class="bi <?= e($icon) ?>"></i> <?= e($label) ?>
                <?php if ($count > 0): ?><span class="badge rounded-pill bg-warning text-dark ms-auto"><?= $count ?></span><?php endif; ?>
            </a>
        </li>
    <?php endforeach; ?>
    <li class="nav-item mt-3">
        <a class="nav-link" href="<?= url('index.php') ?>" target="_blank" rel="noopener">
            <i class="bi bi-box-arrow-up-right"></i> View website
        </a>
    </li>
    <li class="nav-item">
        <form method="post" action="<?= admin_url('logout.php') ?>">
            <?= csrf_field() ?>
            <button type="submit" class="nav-link btn btn-link w-100 text-start">
                <i class="bi bi-box-arrow-left"></i> Log out
            </button>
        </form>
    </li>
</ul>
<?php $sidebarHtml = ob_get_clean(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($adminTitle) ?> | Admin | <?= e(SITE_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= url('assets/css/admin.css') ?>?v=<?= @filemtime(__DIR__ . '/../../assets/css/admin.css') ?>" rel="stylesheet">
</head>
<body class="admin-body">

<!-- Phone top bar -->
<header class="admin-topbar d-lg-none">
    <button class="btn btn-sm btn-outline-light" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#adminDrawer" aria-controls="adminDrawer" aria-label="Open menu">
        <i class="bi bi-list fs-5"></i>
    </button>
    <strong>Udochesse Admin</strong>
</header>

<!-- Phone drawer -->
<div class="offcanvas offcanvas-start admin-drawer d-lg-none" tabindex="-1" id="adminDrawer">
    <div class="offcanvas-header justify-content-end">
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body pt-0"><?= $sidebarHtml ?></div>
</div>

<!-- Desktop sidebar -->
<aside class="admin-sidebar d-none d-lg-block"><?= $sidebarHtml ?></aside>

<main class="admin-main">
    <div class="p-3 p-lg-4">
        <?php if ($flash): ?>
            <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>