<?php
$adminTitle = 'Messages';
$adminPage  = 'messages';
require_once __DIR__ . '/includes/auth.php';

$pdo = db();

/* ---------- Handle actions ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        set_flash('danger', 'Security check failed. Please try again.');
        redirect('admin/messages.php');
    }

    $id     = (int) ($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'unread') {
        $pdo->prepare('UPDATE contact_messages SET is_read = 0 WHERE id = ?')->execute([$id]);
        set_flash('success', 'Marked as unread.');
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM contact_messages WHERE id = ?')->execute([$id]);
        set_flash('success', 'Message deleted.');
    } elseif ($action === 'mark_all_read') {
        $pdo->exec('UPDATE contact_messages SET is_read = 1');
        set_flash('success', 'All messages marked as read.');
    }
    redirect('admin/messages.php');
}

/* ---------- Reading view ---------- */
$viewId = (int) ($_GET['id'] ?? 0);
if ($viewId > 0) {
    $stmt = $pdo->prepare('SELECT * FROM contact_messages WHERE id = ?');
    $stmt->execute([$viewId]);
    $m = $stmt->fetch();

    if (!$m) {
        set_flash('warning', 'That message was not found.');
        redirect('admin/messages.php');
    }

    $pdo->prepare('UPDATE contact_messages SET is_read = 1 WHERE id = ?')->execute([$viewId]);

    include __DIR__ . '/includes/header.php';
    ?>
    <div class="mb-3"><a href="<?= admin_url('messages.php') ?>"><i class="bi bi-arrow-left"></i> Inbox</a></div>

    <div class="card admin-card">
        <div class="card-header"><?= e($m['subject'] ?: '(no subject)') ?></div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-sm-4"><p class="detail-label">From</p><?= e($m['name']) ?></div>
                <div class="col-sm-4"><p class="detail-label">Phone</p>
                    <?= $m['phone'] ? '<a href="tel:' . e(preg_replace('/[^\d+]/', '', $m['phone'])) . '">' . e($m['phone']) . '</a>' : '<span class="text-muted">Not given</span>' ?></div>
                <div class="col-sm-4"><p class="detail-label">Email</p>
                    <?= $m['email'] ? '<a href="mailto:' . e($m['email']) . '">' . e($m['email']) . '</a>' : '<span class="text-muted">Not given</span>' ?></div>
                <div class="col-12"><p class="detail-label">Received</p><?= e(format_datetime($m['created_at'])) ?></div>
            </div>

            <p class="detail-label">Message</p>
            <div class="msg-body p-3 bg-light rounded mb-4"><?= e($m['message']) ?></div>

            <div class="d-flex flex-wrap gap-2">
                <?php if ($m['phone']): ?>
                    <a class="btn btn-success btn-sm" target="_blank" rel="noopener"
                       href="https://wa.me/<?= e(wa_number($m['phone'])) ?>?text=<?= rawurlencode('Hello ' . $m['name'] . ', this is Udochesse Food Network replying to your message.') ?>">
                        <i class="bi bi-whatsapp"></i> WhatsApp</a>
                <?php endif; ?>
                <?php if ($m['email']): ?>
                    <a class="btn btn-outline-dark btn-sm"
                       href="mailto:<?= e($m['email']) ?>?subject=<?= rawurlencode('Re: ' . ($m['subject'] ?: 'Your message')) ?>">
                        <i class="bi bi-envelope"></i> Reply by email</a>
                <?php endif; ?>

                <form method="post" class="d-inline">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button name="action" value="unread" class="btn btn-outline-secondary btn-sm">Mark unread</button>
                </form>
                <form method="post" class="d-inline" data-confirm="Delete this message permanently?">
                    <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
                    <button name="action" value="delete" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Delete</button>
                </form>
            </div>
        </div>
    </div>
    <?php
    include __DIR__ . '/includes/footer.php';
    exit;
}

/* ---------- Inbox ---------- */
$filter  = ($_GET['filter'] ?? '') === 'unread' ? 'unread' : 'all';
$page    = max(1, (int) ($_GET['page'] ?? 1));
$perPage = 20;

$whereSql = $filter === 'unread' ? ' WHERE is_read = 0' : '';
$total    = (int) $pdo->query('SELECT COUNT(*) FROM contact_messages' . $whereSql)->fetchColumn();
$pages    = max(1, (int) ceil($total / $perPage));
$page     = min($page, $pages);
$offset   = ($page - 1) * $perPage;

$messages = $pdo->query(
    'SELECT id, name, phone, email, subject, message, is_read, created_at
       FROM contact_messages' . $whereSql . " ORDER BY created_at DESC LIMIT $perPage OFFSET $offset"
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h3 class="mb-0">Messages</h3>
    <form method="post">
        <?= csrf_field() ?>
        <button name="action" value="mark_all_read" class="btn btn-sm btn-outline-dark">Mark all as read</button>
    </form>
</div>

<div class="card admin-card">
    <div class="card-body">
        <div class="d-flex gap-2 mb-3">
            <a href="<?= admin_url('messages.php') ?>" class="btn btn-sm <?= $filter === 'all' ? 'btn-success' : 'btn-outline-success' ?>">All</a>
            <a href="<?= admin_url('messages.php?filter=unread') ?>" class="btn btn-sm <?= $filter === 'unread' ? 'btn-success' : 'btn-outline-success' ?>">Unread</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>From</th><th>Subject</th><th>Preview</th><th>Received</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($messages as $m): ?>
                    <tr class="<?= $m['is_read'] ? '' : 'row-unread' ?>">
                        <td><?= e($m['name']) ?><div class="small text-muted"><?= e($m['phone'] ?: $m['email']) ?></div></td>
                        <td><?= e($m['subject'] ?: '(no subject)') ?></td>
                        <td class="small text-muted"><?= e(mb_strimwidth($m['message'], 0, 70, '...')) ?></td>
                        <td class="small"><?= e(format_datetime($m['created_at'])) ?></td>
                        <td class="text-end"><a class="btn btn-sm btn-outline-dark" href="<?= admin_url('messages.php?id=' . (int) $m['id']) ?>">Read</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (!$messages): ?>
                    <tr><td colspan="5" class="text-center text-muted py-5">No messages here.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?= paginate_links($page, $pages, ['filter' => $filter === 'unread' ? 'unread' : '']) ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>