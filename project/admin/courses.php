<?php
$pageTitle = 'Manage Courses';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare("UPDATE courses SET is_active = 0 WHERE id = ?")->execute([$_POST['delete_id']]);
        setFlash('success', 'Course was deleted successfully.');
    } else {
        $data = [
            trim($_POST['title']), trim($_POST['description']), trim($_POST['category']),
            trim($_POST['duration']), $_POST['level'], trim($_POST['instructor'])
        ];
        if ($id) {
            $pdo->prepare("UPDATE courses SET title=?, description=?, category=?, duration=?, level=?, instructor=? WHERE id=?")->execute([...$data, $id]);
            setFlash('success', 'Course was updated successfully.');
        } else {
            $pdo->prepare("INSERT INTO courses (title, description, category, duration, level, instructor) VALUES (?,?,?,?,?,?)")->execute($data);
            setFlash('success', 'Course was added successfully.');
        }
        redirect(SITE_URL . '/admin/courses.php');
    }
}

$course = null;
if ($id && in_array($action, ['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ?");
    $stmt->execute([$id]);
    $course = $stmt->fetch();
}
$courses = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Manage Courses</h1>
    <?php if ($action === 'list'): ?><a href="?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add Course</a><?php endif; ?>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="dashboard-content" style="max-width:600px;">
    <h3><?= $course ? 'Edit' : 'Add' ?> Course</h3>
    <form method="POST" style="margin-top:1.5rem;">
        <?= csrfField() ?>
        <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($course['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" required><?= e($course['description'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Category</label><input type="text" name="category" required value="<?= e($course['category'] ?? '') ?>"></div>
            <div class="form-group"><label>Duration</label><input type="text" name="duration" required value="<?= e($course['duration'] ?? '') ?>" placeholder="e.g. 6 weeks"></div>
        </div>
        <div class="form-row">
            <div class="form-group"><label>Level</label>
                <select name="level" required>
                    <?php foreach (['Beginner','Intermediate','Advanced'] as $l): ?>
                    <option value="<?= $l ?>" <?= ($course['level'] ?? '') === $l ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Instructor</label><input type="text" name="instructor" required value="<?= e($course['instructor'] ?? '') ?>"></div>
        </div>
        <button type="submit" class="btn btn-blue">Save Course</button>
        <a href="courses.php" class="btn" style="margin-left:0.5rem;background:var(--gray-200);color:var(--gray-700);padding:0.75rem 1.5rem;border-radius:30px;">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="dashboard-content">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Category</th><th>Level</th><th>Duration</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($courses as $c): ?>
        <tr>
            <td><strong><?= e($c['title']) ?></strong></td>
            <td><?= e($c['category']) ?></td>
            <td><?= e($c['level']) ?></td>
            <td><?= e($c['duration']) ?></td>
            <td><span class="badge badge-<?= $c['is_active'] ? 'success' : 'danger' ?>"><?= $c['is_active'] ? 'Active' : 'Inactive' ?></span></td>
            <td class="table-actions">
                <a href="?action=edit&id=<?= $c['id'] ?>" class="btn-edit">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this course?')">
                    <?= csrfField() ?>
                    <input type="hidden" name="delete_id" value="<?= $c['id'] ?>">
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
