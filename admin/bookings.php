<?php
$adminTitle = 'Bookings';
$adminPage  = 'bookings';
require_once __DIR__ . '/includes/auth.php';

$pdo = db();

/* ---------- Handle actions (before any HTML) ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('danger', 'Security check failed. Please try again.');
        redirect('admin/bookings.php');
    }

    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $status = $_POST['status'] ?? '';
        $notes  = trim(strip_tags($_POST['admin_notes'] ?? ''));
        if (in_array($status, booking_statuses(), true)) {
            $pdo->prepare('UPDATE bookings SET status = ?, admin_notes = ? WHERE id = ?')
                ->execute([$status, $notes !== '' ? $notes : null, $id]);
            set_flash('success', 'Booking updated.');
        }
        redirect('admin/bookings.php?id=' . $id);
    }

    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]);
        set_flash('success', 'Booking deleted.');
        redirect('admin/bookings.php');
    }

    redirect('admin/bookings.php');
}

/* ---------- Detail view ---------- */
$viewId = (int) ($_GET['id'] ?? 0);
if ($viewId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM bookings WHERE id = ?');
    $stmt->execute([$viewId]);
    $b = $stmt->fetch();

    if (!$b) {
        set_flash('warning', 'That booking was not found.');
        redirect('admin/bookings.php');
    }

    $waText = 'Hello ' . $b['full_name'] . ', this is Udochesse Food Network about your '
            . $b['event_type'] . ' on ' . format_date($b['event_date']) . ' (ref ' . $b['reference'] . ').';

    include __DIR__ . '/includes/header.php';
    ?>
    <div class="mb-3"><a href="<?= admin_url('bookings.php') ?>"><i class="bi bi-arrow-left"></i> All bookings</a></div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card admin-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?= e($b['reference']) ?></span> <?= status_badge($b['status']) ?>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-sm-6"><p class="detail-label">Client</p><?= e($b['full_name']) ?></div>
                        <div class="col-sm-6"><p class="detail-label">Phone</p>
                            <a href="tel:<?= e(preg_replace('/[^\d+]/', '', $b['phone'])) ?>"><?= e($b['phone']) ?></a></div>
                        <div class="col-sm-6"><p class="detail-label">Email</p>
                            <?= $b['email'] ? '<a href="mailto:' . e($b['email']) . '">' . e($b['email']) . '</a>' : '<span class="text-muted">Not given</span>' ?></div>
                        <div class="col-sm-6"><p class="detail-label">Event type</p><?= e($b['event_type']) ?></div>
                        <div class="col-sm-6"><p class="detail-label">Event date</p><?= e(format_date($b['event_date'])) ?></div>
                        <div class="col-sm-6"><p class="detail-label">Guests</p><?= (int) $b['guest_count'] ?></div>
                        <div class="col-12"><p class="detail-label">Location</p><?= e($b['event_location']) ?></div>
                        <div class="col-12"><p class="detail-label">Services needed</p>
                            <?= $b['services_needed'] ? e($b['services_needed']) : '<span class="text-muted">Not specified</span>' ?></div>
                        <div class="col-12"><p class="detail-label">Extra details</p>
                            <div class="msg-body"><?= $b['message'] ? e($b['message']) : '<span class="text-muted">None</span>' ?></div></div>
                        <div class="col-12"><p class="detail-label">Received</p><?= e(format_datetime($b['created_at'])) ?></div>
                    </div>

                    <hr>
                    <a class="btn btn-success btn-sm me-1 mb-1" target="_blank" rel="noopener"
                       href="https://wa.me/<?= e(wa_number($b['phone'])) ?>?text=<?= rawurlencode($waText) ?>">
                        <i class="bi bi-whatsapp"></i> WhatsApp client</a>
                    <a class="btn btn-outline-dark btn-sm mb-1" href="tel:<?= e(preg_replace('/[^\d+]/', '', $b['phone'])) ?>">
                        <i class="bi bi-telephone"></i> Call</a>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card admin-card mb-4">
                <div class="card-header">Update booking</div>
                <div class="card-body">
                    <form method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label" for="status">Status</label>
                            <select name="status" id="status" class="form-select">
                                <?php foreach (booking_statuses() as $st): ?>
                                    <option value="<?= e($st) ?>" <?= $b['status'] === $st ? 'selected' : '' ?>><?= e(ucfirst($st)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="admin_notes">Private notes</label>
                            <textarea name="admin_notes" id="admin_notes" rows="5" class="form-control"
                                      placeholder="Quote sent, deposit paid, menu agreed..."><?= e($b['admin_notes'] ?? '') ?></textarea>
                            <div class="form-text">Only you can see these. Clients never do.</div>
                        </div>
                        <button class="btn btn-success w-100">Save changes</button>
                    </form>
                </div>
            </div>

            <form method="post" data-confirm="Delete this booking permanently?">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
                <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete booking</button>
            </form>
        </div>
    </div>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

/* ---------- List view ---------- */
$status = $_GET['status'] ?? '';
$status = in_array($status, booking_statuses(), true) ? $status : '';
$q      = trim(strip_tags($_GET['q'] ?? ''));
$page   = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 15;

$where = [];
$args  = [];
if ($status !== '') {
    $where[] = 'status = ?';
    $args[]  = $status;
}
if ($q !== '') {
    $where[] = '(full_name LIKE ? OR phone LIKE ? OR reference LIKE ?)';
    $like = '%' . $q . '%';
    array_push($args, $like, $like, $like);
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$countStmt = $pdo->prepare('SELECT COUNT(*) FROM bookings' . $whereSql);
$countStmt->execute($args);
$total = (int) $countStmt->fetchColumn();
$pages = max(1, (int) ceil($total / $perPage));
$page  = min($page, $pages);
$offset = ($page - 1) * $perPage;

$listStmt = $pdo->prepare(
    'SELECT id, reference, full_name, phone, event_type, event_date, guest_count, status, created_at
       FROM bookings' . $whereSql . " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset"
);
$listStmt->execute($args);
$bookings = $listStmt->fetchAll();

$statusCounts = $pdo->query('SELECT status, COUNT(*) c FROM bookings GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);

include __DIR__ . '/includes/header.php';
?>

<h3 class="mb-3">Bookings</h3>

<div class="card admin-card">
    <div class="card-body">
        <div class="d-flex flex-wrap gap-2 mb-3">
            <a href="<?= admin_url('bookings.php') ?>" class="btn btn-sm <?= $status === '' ? 'btn-success' : 'btn-outline-success' ?>">
                All (<?= array_sum($statusCounts) ?>)</a>
            <?php foreach (booking_statuses() as $st): ?>
                <a href="<?= admin_url('bookings.php?status=' . $st) ?>"
                   class="btn btn-sm <?= $status === $st ? 'btn-success' : 'btn-outline-success' ?>">
                    <?= e(ucfirst($st)) ?> (<?= (int) ($statusCounts[$st] ?? 0) ?>)</a>
            <?php endforeach; ?>
        </div>

        <form method="get" class="row g-2 mb-3">
            <?php if ($status !== ''): ?><input type="hidden" name="status" value="<?= e($status) ?>"><?php endif; ?>
            <div class="col-sm-8 col-md-6">
                <input type="search" name="q" class="form-control" placeholder="Search name, phone or reference" value="<?= e($q) ?>">
            </div>
            <div class="col-auto"><button class="btn btn-dark">Search</button></div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr><th>Reference</th><th>Client</th><th>Event</th><th>Event date</th><th>Guests</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                <?php foreach ($bookings as $b): ?>
                    <tr>
                        <td class="small"><?= e($b['reference']) ?><div class="text-muted"><?= e(format_datetime($b['created_at'])) ?></div></td>
                        <td><?= e($b['full_name']) ?><div class="small text-muted"><?= e($b['phone']) ?></div></td>
                        <td><?= e($b['event_type']) ?></td>
                        <td><?= e(format_date($b['event_date'])) ?></td>
                        <td><?= (int) $b['guest_count'] ?></td>
                        <td><?= status_badge($b['status']) ?></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-dark" href="<?= admin_url('bookings.php?id=' . (int) $b['id']) ?>">Open</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$bookings): ?>
                    <tr><td colspan="7" class="text-center text-muted py-5">No bookings found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?= paginate_links($page, $pages, ['status' => $status, 'q' => $q]) ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>