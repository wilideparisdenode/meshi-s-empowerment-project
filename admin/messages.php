<?php
$pageTitle = 'Contact Messages';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    if (isset($_POST['mark_read'])) {
        $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?")->execute([(int)$_POST['message_id']]);
        setFlash('success', 'Message was marked as read successfully.');
    }
    redirect(SITE_URL . '/admin/messages.php');
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY is_read ASC, created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Contact Messages</h1>
</div>

<div class="dashboard-content">
    <?php if (empty($messages)): ?>
    <p>No messages yet.</p>
    <?php else: ?>
    <?php foreach ($messages as $m): ?>
    <div class="card" style="padding:1.5rem;margin-bottom:1rem;<?= !$m['is_read'] ? 'border-left:4px solid var(--primary);' : '' ?>">
        <div style="display:flex;justify-content:space-between;margin-bottom:0.75rem;">
            <div>
                <strong><?= e($m['name']) ?></strong> &lt;<?= e($m['email']) ?>&gt;
                <?php if (!$m['is_read']): ?><span class="badge badge-info">New</span><?php endif; ?>
            </div>
            <small style="color:var(--gray-500);"><?= formatDateTime($m['created_at']) ?></small>
        </div>
        <h4 style="color:var(--gray-900);margin-bottom:0.5rem;"><?= e($m['subject']) ?></h4>
        <p style="color:var(--gray-500);line-height:1.7;"><?= nl2br(e($m['message'])) ?></p>
        <?php if (!$m['is_read']): ?>
        <form method="POST" style="margin-top:1rem;">
            <?= csrfField() ?>
            <input type="hidden" name="message_id" value="<?= $m['id'] ?>">
            <button type="submit" name="mark_read" value="1" class="btn btn-blue btn-sm">Mark as Read</button>
        </form>
        <?php endif; ?>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
