<?php
define('NO_SESSION', true);
require_once __DIR__ . '/config/config.php';

header('Content-Type: text/plain; charset=utf-8');

echo "User-agent: *\n";
echo "Disallow: /admin/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /config/\n";
echo "Disallow: /process_booking.php\n";
echo "Disallow: /process_contact.php\n";
echo "\n";
echo 'Sitemap: ' . BASE_URL . "/sitemap.xml\n";