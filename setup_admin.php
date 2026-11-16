<?php
/**
 * ONE-TIME admin account creator.
 * Works only while the admins table is empty. DELETE THIS FILE afterwards.
 */
require_once __DIR__ . '/includes/functions.php';

$pdo     = db();
$hasAdmin = (int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
$errors  = [];
$done    = false;

if (!$hasAdmin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        $errors[] = 'Security check failed. Refresh and try again.';
    } else {
        $name     = clean($_POST['name'] ?? '');
        $email    = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm'] ?? '';

        if ($name === '')            $errors[] = 'Name is required.';
        if (!$email)                 $errors[] = 'Enter a valid email address.';
        if (strlen($password) < 8)   $errors[] = 'Password must be at least 8 characters.';
        if ($password !== $confirm)  $errors[] = 'Passwords do not match.';

        if (!$errors) {
            $stmt = $pdo->prepare('INSERT INTO admins (name, email, password_hash) VALUES (?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);
            $done = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:480px">
    <h3 class="mb-4">Create Admin Account</h3>

    <?php if ($hasAdmin && !$done): ?>
        <div class="alert alert-warning">An admin already exists. Delete <code>setup_admin.php</code> now.</div>

    <?php elseif ($done): ?>
        <div class="alert alert-success">
            Admin created. <strong>Delete setup_admin.php immediately.</strong>
        </div>

    <?php else: ?>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-danger py-2"><?= e($err) ?></div>
        <?php endforeach; ?>

        <form method="post" class="card card-body">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label">Full name</label>
                <input type="text" name="name" class="form-control" value="Ngozi Timothy" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password (min 8 characters)</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirm password</label>
                <input type="password" name="confirm" class="form-control" required>
            </div>
            <button class="btn btn-dark w-100">Create Admin</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>