<?php
$pageTitle = 'Manage Users';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $userId = (int) ($_POST['user_id'] ?? 0);
    if (isset($_POST['toggle_active'])) {
        $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'")->execute([$userId]);
        setFlash('success', 'User status was updated successfully.');
    }
    redirect(SITE_URL . '/admin/users.php');
}

$users = $pdo->query("SELECT u.*, 
    (SELECT COUNT(*) FROM enrollments e WHERE e.user_id = u.id) as course_count
    FROM users u WHERE u.role = 'user' ORDER BY u.created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Manage Users</h1>
</div>

<div class="dashboard-content">
    <table class="data-table">
        <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Location</th><th>Courses</th><th>Joined</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td><strong><?= e($u['full_name']) ?></strong></td>
            <td><?= e($u['email']) ?></td>
            <td><?= e($u['phone'] ?: '-') ?></td>
            <td><?= e($u['location'] ?: '-') ?></td>
            <td><?= (int)$u['course_count'] ?></td>
            <td><?= formatDate($u['created_at']) ?></td>
            <td><span class="badge badge-<?= $u['is_active'] ? 'success' : 'danger' ?>"><?= $u['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td>
                <form method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                    <button type="submit" name="toggle_active" value="1" class="btn-edit">
                        <?= $u['is_active'] ? 'Deactivate' : 'Activate' ?>
                    </button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
