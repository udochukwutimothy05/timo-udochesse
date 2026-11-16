<?php
require_once __DIR__ . '/../includes/functions.php';

// Logout is a POST with a CSRF token so another website cannot log you out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verify_csrf()) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    session_start();
    set_flash('success', 'You have been logged out.');
    redirect('admin/login.php');
}

redirect(is_admin() ? 'admin/dashboard.php' : 'admin/login.php');