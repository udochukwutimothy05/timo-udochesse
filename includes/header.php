<?php
/**
 * Shared page top. Set these BEFORE including this file:
 *   $pageTitle        e.g. 'About Us'
 *   $currentPage      home | about | services | menu | gallery | book | contact
 *   $metaDescription  (optional) short description for Google
 */
require_once __DIR__ . '/functions.php';

if (!function_exists('tel_link')) {
    // 07033358179  ->  tel:+2347033358179
    function tel_link(): string
    {
        return 'tel:+234' . ltrim(preg_replace('/\D/', '', setting('phone', '07033358179')), '0');
    }
}

$pageTitle       = $pageTitle ?? 'Home';
$currentPage     = $currentPage ?? '';
$metaDescription = $metaDescription
    ?? 'Udochesse Food Network offers professional event catering, custom cakes and small chops in Port Harcourt, Rivers State and across the Niger Delta. Serving since 2019.';

$fullTitle = ($pageTitle === 'Home')
    ? SITE_NAME . ' | Event Catering in Port Harcourt'
    : $pageTitle . ' | ' . SITE_NAME;

$flash = get_flash();

// Structured data so Google understands the business
$schema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'FoodEstablishment',
    'name'        => SITE_NAME,
    'url'         => BASE_URL,
    'telephone'   => '+234' . ltrim(preg_replace('/\D/', '', setting('phone', '07033358179')), '0'),
    'foundingDate'=> setting('founded_year', '2019'),
    'founder'     => ['@type' => 'Person', 'name' => setting('owner_name', 'Ngozi Timothy')],
    'areaServed'  => 'Niger Delta, Nigeria',
    'address'     => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'No 54 Mini Orlu Road, off Ada George Road',
        'addressLocality' => 'Port Harcourt',
        'addressRegion'   => 'Rivers State',
        'addressCountry'  => 'NG',
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($fullTitle) ?></title>
    <meta name="description" content="<?= e($metaDescription) ?>">
    <meta name="theme-color" content="#14532d">

    <!-- Social sharing -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($fullTitle) ?>">
    <meta property="og:description" content="<?= e($metaDescription) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">

    <!-- Fonts, Bootstrap 5, Bootstrap Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <!-- Our styles -->
    <link href="<?= url('assets/css/style.css') ?>?v=<?= @filemtime(__DIR__ . '/../assets/css/style.css') ?>" rel="stylesheet">

    <script type="application/ld+json"><?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?></script>
</head>
<body>

<?php include __DIR__ . '/navbar.php'; ?>

<?php if ($flash): ?>
    <div class="container mt-3">
        <div class="alert alert-<?= e($flash['type']) ?> alert-dismissible fade show" role="alert">
            <?= e($flash['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<main id="main">