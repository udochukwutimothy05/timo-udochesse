<?php
$adminTitle = 'Dashboard';
$adminPage  = 'dashboard';
require_once __DIR__ . '/includes/auth.php';

$pdo = db();

$stats = [
    'new'      => (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'new'")->fetchColumn(),
    'total'    => (int) $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'upcoming' => (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed' AND event_date >= CURDATE()")->fetchColumn(),
    'unread'   => (int) $pdo->query('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0')->fetchColumn(),
];

$recentBookings = $pdo->query(
    'SELECT id, reference, full_name, event_type, event_date, status FROM bookings ORDER BY created_at DESC LIMIT 6'
)->fetchAll();

$upcomingEvents = $pdo->query(
    "SELECT id, full_name, event_type, event_date, guest_count, event_location
       FROM bookings WHERE status = 'confirmed' AND event_date >= CURDATE()
      ORDER BY event_date ASC LIMIT 5"
)->fetchAll();

$recentMessages = $pdo->query(
    'SELECT id, name, subject, is_read, created_at FROM contact_messages ORDER BY created_at DESC LIMIT 5'
)->fetchAll();

include __DIR__ . '/includes/header.php';

$cards = [
    ['New requests',     $stats['new'],      'bi-bell',            'bookings.php?status=new'],
    ['Total bookings',   $stats['total'],    'bi-calendar-check',  'bookings.php'],
    ['Upcoming events',  $stats['upcoming'], 'bi-calendar-event',  'bookings.php?status=confirmed'],
    ['Unread messages',  $stats['unread'],   'bi-envelope',        'messages.php?filter=unread'],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0">Welcome, <?= e($_SESSION['admin_name'] ?? 'Admin') ?></h3>
        <small class="text-muted"><?= date('l, j F Y') ?></small>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($cards as [$label, $value, $icon, $link]): ?>
        <div class="col-6 col-xl-3">
            <a href="<?= admin_url($link) ?>" class="text-decoration-none text-dark">
                <div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3">
                    <div class="stat-icon"><i class="bi <?= e($icon) ?>"></i></div>
                    <div><h3><?= $value ?></h3><small class="text-muted"><?= e($label) ?></small></div>
                </div></div>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4">
    <div class="col-xl-7">
        <div class="card admin-card h-100">
            <div class="card-header d-flex justify-content-between">
                Latest booking requests <a href="<?= admin_url('bookings.php') ?>" class="small">View all</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light"><tr><th>Client</th><th>Event</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td><a href="<?= admin_url('bookings.php?id=' . (int) $b['id']) ?>"><?= e($b['full_name']) ?></a>
                                <div class="small text-muted"><?= e($b['reference']) ?></div></td>
                            <td><?= e($b['event_type']) ?></td>
                            <td><?= e(format_date($b['event_date'])) ?></td>
                            <td><?= status_badge($b['status']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (!$recentBookings): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4">No booking requests yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="card admin-card mb-4">
            <div class="card-header">Upcoming confirmed events</div>
            <ul class="list-group list-group-flush">
                <?php foreach ($upcomingEvents as $u): ?>
                    <li class="list-group-item">
                        <a href="<?= admin_url('bookings.php?id=' . (int) $u['id']) ?>" class="fw-semibold"><?= e($u['full_name']) ?></a>
                        <div class="small text-muted">
                            <?= e($u['event_type']) ?> &middot; <?= e(format_date($u['event_date'])) ?>
                            &middot; <?= (int) $u['guest_count'] ?> guests<br><?= e($u['event_location']) ?>
                        </div>
                    </li>
                <?php endforeach; ?>
                <?php if (!$upcomingEvents): ?>
                    <li class="list-group-item text-muted text-center py-4">No confirmed upcoming events.</li>
                <?php endif; ?>
            </ul>
        </div>

        <div class="card admin-card">
            <div class="card-header d-flex justify-content-between">
                Latest messages <a href="<?= admin_url('messages.php') ?>" class="small">View all</a>
            </div>
            <ul class="list-group list-group-flush">
                <?php foreach ($recentMessages as $m): ?>
                    <li class="list-group-item <?= $m['is_read'] ? '' : 'row-unread' ?>">
                        <a href="<?= admin_url('messages.php?id=' . (int) $m['id']) ?>"><?= e($m['name']) ?></a>
                        <div class="small text-muted"><?= e($m['subject'] ?: '(no subject)') ?> &middot; <?= e(format_datetime($m['created_at'])) ?></div>
                    </li>
                <?php endforeach; ?>
                <?php if (!$recentMessages): ?>
                    <li class="list-group-item text-muted text-center py-4">No messages yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>