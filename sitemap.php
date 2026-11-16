<?php
define('NO_SESSION', true);
require_once __DIR__ . '/config/config.php';

header('Content-Type: application/xml; charset=utf-8');

$pages = [
    ''             => '1.0',
    'about.php'    => '0.8',
    'services.php' => '0.9',
    'menu.php'     => '0.8',
    'gallery.php'  => '0.6',
    'book.php'     => '0.9',
    'contact.php'  => '0.7',
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($pages as $path => $priority) {
    $loc = htmlspecialchars(BASE_URL . '/' . $path, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    echo "  <url><loc>{$loc}</loc><priority>{$priority}</priority></url>\n";
}
echo "</urlset>\n";