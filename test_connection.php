<?php
require_once __DIR__ . '/includes/functions.php';

$tables  = db()->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
$phone   = setting('phone');
$address = setting('address');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connection Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5" style="max-width:700px">
    <div class="alert alert-success"><strong>Connected!</strong> PHP is talking to MySQL.</div>

    <div class="card mb-3"><div class="card-body">
        <h5>Tables found (<?= count($tables) ?>)</h5>
        <p class="mb-0"><?= e(implode(', ', $tables)) ?></p>
    </div></div>

    <div class="card mb-3"><div class="card-body">
        <h5>Settings read from the database</h5>
        <p class="mb-1"><strong>Phone:</strong> <?= e($phone) ?></p>
        <p class="mb-0"><strong>Address:</strong> <?= e($address) ?></p>
    </div></div>

    <p class="text-muted small">Delete this file (test_connection.php) once you are done.</p>
</div>
</body>
</html>