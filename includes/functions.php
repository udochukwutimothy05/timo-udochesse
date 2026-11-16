<?php
/**
 * Helper functions used across the whole site.
 * Every page should start with:  require_once __DIR__ . '/includes/functions.php';
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

/* ---------- Output safety ---------- */

// Escape text before printing it in HTML (prevents XSS)
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Clean text coming from a form
function clean($value): string
{
    return trim(strip_tags((string) $value));
}

/* ---------- URLs ---------- */

function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

function redirect(string $path): void
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

/* ---------- Site settings (from the settings table) ---------- */

function settings(): array
{
    static $cache = null;

    if ($cache === null) {
        $cache = [];
        try {
            $rows = db()->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
            foreach ($rows as $row) {
                $cache[$row['setting_key']] = $row['setting_value'];
            }
        } catch (PDOException $e) {
            // settings table missing: fall back to defaults
        }
    }

    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings();
    return (isset($all[$key]) && $all[$key] !== '') ? $all[$key] : $default;
}

function whatsapp_link(string $message = ''): string
{
    $link = 'https://wa.me/' . setting('whatsapp', '2347033358179');
    return $message !== '' ? $link . '?text=' . rawurlencode($message) : $link;
}

/* ---------- CSRF protection ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/* ---------- Flash messages (one-time alerts) ---------- */

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function get_flash(): ?array
{
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/* ---------- Admin authentication ---------- */

function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('admin/login.php');
    }
}

/* ---------- Formatting ---------- */

function naira($amount): string
{
    return '₦' . number_format((float) $amount, 0);
}

function booking_reference(): string
{
    return 'UFN-' . date('Y') . '-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 5));
}