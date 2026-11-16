<?php
/**
 * Site-wide settings. Change these when you move to live hosting.
 */

// ----- Database -----
define('DB_HOST', 'localhost');
define('DB_NAME', 'udochesse_db');
define('DB_USER', 'root');   // XAMPP default
define('DB_PASS', '');       // XAMPP default is empty
define('DB_CHARSET', 'utf8mb4');

// ----- Site -----
// Local:  http://localhost/udochesse
// Live:   https://www.yourdomain.com   (no trailing slash)
define('BASE_URL', 'http://localhost/udochesse_db');
define('SITE_NAME', 'Udochesse Food Network');

// ----- Uploads -----
define('UPLOAD_DIR', __DIR__ . '/../uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 2 * 1024 * 1024); // 2MB

// ----- Environment -----
// Set to false on the live server so errors are never shown to visitors.
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

date_default_timezone_set('Africa/Lagos');

// ----- Sessions (safe defaults) -----
if (!defined('NO_SESSION') && session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}