<?php
$pageTitle = 'Manage Scholarships';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare("UPDATE scholarships SET is_active = 0 WHERE id = ?")->execute([$_POST['delete_id']]);
        setFlash('success', 'Scholarship was removed successfully.');
    } else {
        $data = [trim($_POST['title']), trim($_POST['description']), trim($_POST['provider']),
            trim($_POST['amount']), $_POST['deadline'], trim($_POST['eligibility'])];
        if ($id) {
            $pdo->prepare("UPDATE scholarships SET title=?, description=?, provider=?, amount=?, deadline=?, eligibility=? WHERE id=?")->execute([...$data, $id]);
            setFlash('success', 'Scholarship was updated successfully.');
        } else {
            $pdo->prepare("INSERT INTO scholarships (title, description, provider, amount, deadline, eligibility) VALUES (?,?,?,?,?,?)")->execute($data);
            setFlash('success', 'Scholarship was added successfully.');
        }
        redirect(SITE_URL . '/admin/scholarships.php');
    }
}

$scholarship = null;
if ($id && $action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM scholarships WHERE id = ?");
    $stmt->execute([$id]);
    $scholarship = $stmt->fetch();
}
$scholarships = $pdo->query("SELECT * FROM scholarships ORDER BY deadline ASC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Manage Scholarships</h1>
    <?php if ($action === 'list'): ?><a href="?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add Scholarship</a><?php endif; ?>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="dashboard-content" style="max-width:600px;">
    <form method="POST">
        <?= csrfField() ?>
        <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($scholarship['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" required><?= e($scholarship['description'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Provider</label><input type="text" name="provider" required value="<?= e($scholarship['provider'] ?? '') ?>"></div>
            <div class="form-group"><label>Amount</label><input type="text" name="amount" required value="<?= e($scholarship['amount'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>Deadline</label><input type="date" name="deadline" required value="<?= e($scholarship['deadline'] ?? '') ?>"></div>
        <div class="form-group"><label>Eligibility</label><textarea name="eligibility" required><?= e($scholarship['eligibility'] ?? '') ?></textarea></div>
        <button type="submit" class="btn btn-blue">Save</button>
        <a href="scholarships.php">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="dashboard-content">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Provider</th><th>Amount</th><th>Deadline</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($scholarships as $s): ?>
        <tr>
            <td><strong><?= e($s['title']) ?></strong></td>
            <td><?= e($s['provider']) ?></td>
            <td><?= e($s['amount']) ?></td>
            <td><?= formatDate($s['deadline']) ?></td>
            <td class="table-actions">
                <a href="?action=edit&id=<?= $s['id'] ?>" class="btn-edit">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
                    <?= csrfField() ?><input type="hidden" name="delete_id" value="<?= $s['id'] ?>">
                    <button type="submit" class="btn-delete">Delete</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
