<?php
require_once __DIR__ . '/../includes/functions.php';

if (is_admin()) {
    redirect('admin/dashboard.php');
}

$error = '';
$flash = get_flash();
$locked = isset($_SESSION['login_lock_until']) && time() < $_SESSION['login_lock_until'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($locked) {
        $error = 'Too many failed attempts. Please wait a few minutes and try again.';
    } elseif (!verify_csrf()) {
        $error = 'Security check failed. Please refresh the page and try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $stmt = db()->prepare('SELECT id, name, password_hash FROM admins WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // Always run password_verify so response time does not reveal whether the email exists
        $hash = $admin['password_hash'] ?? password_hash('not-a-real-password', PASSWORD_DEFAULT);
        $ok   = password_verify($password, $hash) && $admin;

        if ($ok) {
            session_regenerate_id(true);   // stops session fixation
            $_SESSION['admin_id']      = (int) $admin['id'];
            $_SESSION['admin_name']    = $admin['name'];
            $_SESSION['last_activity'] = time();
            unset($_SESSION['login_fails'], $_SESSION['login_lock_until']);

            db()->prepare('UPDATE admins SET last_login = NOW() WHERE id = ?')->execute([$admin['id']]);

            if (password_needs_rehash($hash, PASSWORD_DEFAULT)) {
                db()->prepare('UPDATE admins SET password_hash = ? WHERE id = ?')
                    ->execute([password_hash($password, PASSWORD_DEFAULT), $admin['id']]);
            }
            redirect('admin/dashboard.php');
        }

        $_SESSION['login_fails'] = ($_SESSION['login_fails'] ?? 0) + 1;
        if ($_SESSION['login_fails'] >= 5) {
            $_SESSION['login_lock_until'] = time() + 10 * 60;
            $_SESSION['login_fails'] = 0;
            $error = 'Too many failed attempts. Please wait 10 minutes.';
        } else {
            $error = 'Incorrect email or password.';
        }
        usleep(400000); // small delay slows down guessing
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Login | <?= e(SITE_NAME) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #14532d, #0d3b20); min-height: 100vh; display: flex; align-items: center; }
        .login-card { max-width: 410px; width: 100%; border: 0; border-radius: 16px; }
        .btn-login { background: #d99a1e; border-color: #d99a1e; color: #fff; }
        .btn-login:hover { background: #b97f10; border-color: #b97f10; color: #fff; }
    </style>
</head>
<body>
<div class="container">
    <div class="card login-card mx-auto shadow-lg">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <h3 class="mb-0" style="color:#14532d;font-weight:700">Udochesse</h3>
                <small class="text-uppercase text-muted" style="letter-spacing:.25em">Admin Login</small>
            </div>

            <?php if ($flash): ?>
                <div class="alert alert-<?= e($flash['type']) ?> py-2"><?= e($flash['message']) ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= e($error) ?></div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required autofocus
                           value="<?= e($_POST['email'] ?? '') ?>">
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-login w-100 py-2" <?= $locked ? 'disabled' : '' ?>>
                    <i class="bi bi-box-arrow-in-right me-1"></i> Log in
                </button>
            </form>

            <p class="text-center mt-4 mb-0 small"><a href="<?= url('index.php') ?>">&larr; Back to website</a></p>
        </div>
    </div>
</div>
</body>
</html>