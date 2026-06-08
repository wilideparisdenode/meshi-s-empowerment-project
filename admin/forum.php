<?php
$pageTitle = 'Moderate Forum';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $topicId = (int) ($_POST['topic_id'] ?? 0);
    if (isset($_POST['pin'])) {
        $pdo->prepare("UPDATE forum_topics SET is_pinned = 1 WHERE id = ?")->execute([$topicId]);
        setFlash('success', 'Topic was pinned successfully.');
    } elseif (isset($_POST['unpin'])) {
        $pdo->prepare("UPDATE forum_topics SET is_pinned = 0 WHERE id = ?")->execute([$topicId]);
        setFlash('success', 'Topic was unpinned successfully.');
    } elseif (isset($_POST['lock'])) {
        $pdo->prepare("UPDATE forum_topics SET is_locked = 1 WHERE id = ?")->execute([$topicId]);
        setFlash('success', 'Topic was locked successfully.');
    } elseif (isset($_POST['unlock'])) {
        $pdo->prepare("UPDATE forum_topics SET is_locked = 0 WHERE id = ?")->execute([$topicId]);
        setFlash('success', 'Topic was unlocked successfully.');
    } elseif (isset($_POST['delete_topic'])) {
        $pdo->prepare("DELETE FROM forum_topics WHERE id = ?")->execute([$topicId]);
        setFlash('success', 'Topic was deleted successfully.');
    } elseif (isset($_POST['delete_reply'])) {
        $pdo->prepare("DELETE FROM forum_replies WHERE id = ?")->execute([(int)$_POST['reply_id']]);
        setFlash('success', 'Reply was deleted successfully.');
    }
    redirect(SITE_URL . '/admin/forum.php');
}

$topics = $pdo->query("SELECT ft.*, u.full_name, fc.name as category_name,
    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.topic_id = ft.id) as reply_count
    FROM forum_topics ft JOIN users u ON ft.user_id = u.id JOIN forum_categories fc ON ft.category_id = fc.id
    ORDER BY ft.created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Forum Moderation</h1>
</div>

<div class="dashboard-content">
    <table class="data-table">
        <thead><tr><th>Topic</th><th>Author</th><th>Category</th><th>Replies</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($topics as $t): ?>
        <tr>
            <td><strong><?= e(truncate($t['title'], 40)) ?></strong></td>
            <td><?= e($t['full_name']) ?></td>
            <td><?= e($t['category_name']) ?></td>
            <td><?= (int)$t['reply_count'] ?></td>
            <td>
                <?php if ($t['is_pinned']): ?><span class="badge badge-info">Pinned</span><?php endif; ?>
                <?php if ($t['is_locked']): ?><span class="badge badge-warning">Locked</span><?php endif; ?>
            </td>
            <td>
                <form method="POST" style="display:inline-flex;gap:0.25rem;flex-wrap:wrap;">
                    <?= csrfField() ?>
                    <input type="hidden" name="topic_id" value="<?= $t['id'] ?>">
                    <?php if (!$t['is_pinned']): ?><button name="pin" value="1" class="btn-edit">Pin</button>
                    <?php else: ?><button name="unpin" value="1" class="btn-edit">Unpin</button><?php endif; ?>
                    <?php if (!$t['is_locked']): ?><button name="lock" value="1" class="btn-edit">Lock</button>
                    <?php else: ?><button name="unlock" value="1" class="btn-edit">Unlock</button><?php endif; ?>
                    <button name="delete_topic" value="1" class="btn-delete" data-confirm="Delete this topic?">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
