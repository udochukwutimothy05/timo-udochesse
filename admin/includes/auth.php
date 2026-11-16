<?php
/**
 * Loaded by every admin page. Blocks anyone who is not logged in.
 */
require_once __DIR__ . '/../../includes/functions.php';

define('ADMIN_IDLE_LIMIT', 2 * 60 * 60); // log out after 2 hours of inactivity

require_admin();

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > ADMIN_IDLE_LIMIT) {
    $_SESSION = [];
    session_destroy();
    session_start();
    set_flash('warning', 'You were logged out because of inactivity.');
    redirect('admin/login.php');
}
$_SESSION['last_activity'] = time();

/* ---------- Helpers used by admin pages ---------- */

function admin_url(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function booking_statuses(): array
{
    return ['new', 'contacted', 'confirmed', 'completed', 'cancelled'];
}

function status_badge(string $status): string
{
    $colors = [
        'new' => 'primary', 'contacted' => 'info', 'confirmed' => 'success',
        'completed' => 'secondary', 'cancelled' => 'danger',
    ];
    $color = $colors[$status] ?? 'secondary';
    return '<span class="badge text-bg-' . $color . '">' . e(ucfirst($status)) . '</span>';
}

// 0703 335 8179 -> 2347033358179 (for wa.me links)
function wa_number(string $phone): string
{
    $digits = preg_replace('/\D/', '', $phone);
    if (str_starts_with($digits, '234')) {
        return $digits;
    }
    return '234' . ltrim($digits, '0');
}

function format_date(?string $date): string
{
    return $date ? date('D, j M Y', strtotime($date)) : '';
}

function format_datetime(?string $dt): string
{
    return $dt ? date('j M Y, g:i A', strtotime($dt)) : '';
}

function paginate_links(int $page, int $pages, array $params = []): string
{
    if ($pages <= 1) {
        return '';
    }
    unset($params['page']);
    $out = '<nav><ul class="pagination pagination-sm mb-0">';
    for ($i = 1; $i <= $pages; $i++) {
        $link = '?' . http_build_query($params + ['page' => $i]);
        $out .= '<li class="page-item' . ($i === $page ? ' active' : '') . '">'
              . '<a class="page-link" href="' . e($link) . '">' . $i . '</a></li>';
    }
    return $out . '</ul></nav>';
}