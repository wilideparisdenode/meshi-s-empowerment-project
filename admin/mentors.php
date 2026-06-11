<?php
$pageTitle = 'Manage Mentors';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare("DELETE FROM mentors WHERE id = ?")->execute([$_POST['delete_id']]);
        setFlash('success', 'Mentor was removed successfully.');
    } elseif (isset($_POST['request_id'], $_POST['status'])) {
        $pdo->prepare("UPDATE mentor_requests SET status = ? WHERE id = ?")->execute([$_POST['status'], $_POST['request_id']]);
        setFlash('success', 'Mentorship request was updated successfully.');
    } else {
        $data = [trim($_POST['full_name']), trim($_POST['title']), trim($_POST['expertise']), trim($_POST['bio']),
            trim($_POST['email']), trim($_POST['phone']), (int)$_POST['years_experience']];
        $oldImage = null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT image_url FROM mentors WHERE id = ?");
            $stmt->execute([$id]);
            $oldImage = $stmt->fetchColumn();
            $pdo->prepare("UPDATE mentors SET full_name=?, title=?, expertise=?, bio=?, email=?, phone=?, years_experience=? WHERE id=?")->execute([...$data, $id]);
            $mentorId = $id;
            setFlash('success', 'Mentor was updated successfully.');
        } else {
            $pdo->prepare("INSERT INTO mentors (full_name, title, expertise, bio, email, phone, years_experience) VALUES (?,?,?,?,?,?,?)")->execute($data);
            $mentorId = (int) $pdo->lastInsertId();
            setFlash('success', 'Mentor was added successfully.');
        }
        $upload = uploadPhoto($_FILES['mentor_photo'] ?? [], 'mentors', $oldImage ?: null);
        if (!$upload['success']) {
            setFlash('error', $upload['error']);
            redirect(SITE_URL . '/admin/mentors.php?action=edit&id=' . $mentorId);
        }
        if (!empty($upload['filename'])) {
            $pdo->prepare("UPDATE mentors SET image_url = ? WHERE id = ?")->execute([$upload['filename'], $mentorId]);
        }
        redirect(SITE_URL . '/admin/mentors.php');
    }
}

$mentor = null;
if ($id && $action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM mentors WHERE id = ?");
    $stmt->execute([$id]);
    $mentor = $stmt->fetch();
}
$mentors = $pdo->query("SELECT * FROM mentors ORDER BY full_name")->fetchAll();
$requests = $pdo->query("SELECT mr.*, u.full_name as user_name, m.full_name as mentor_name FROM mentor_requests mr JOIN users u ON mr.user_id=u.id JOIN mentors m ON mr.mentor_id=m.id ORDER BY mr.created_at DESC LIMIT 20")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Manage Mentors</h1>
    <?php if ($action === 'list'): ?><a href="?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add Mentor</a><?php endif; ?>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="dashboard-content" style="max-width:600px;">
    <form method="POST" enctype="multipart/form-data">
        <?= csrfField() ?>
        <?php if (!empty($mentor['image_url']) && photoUrl($mentor['image_url'], 'mentors')): ?>
        <img src="<?= e(photoUrl($mentor['image_url'], 'mentors')) ?>" alt="" class="profile-upload-preview">
        <?php endif; ?>
        <div class="form-group">
            <label>Profile Photo</label>
            <input type="file" name="mentor_photo" accept="image/jpeg,image/png,image/webp">
            <p class="photo-upload-hint">JPG, PNG or WEBP — max 2MB</p>
        </div>
        <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required value="<?= e($mentor['full_name'] ?? '') ?>"></div>
        <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($mentor['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Expertise</label><input type="text" name="expertise" required value="<?= e($mentor['expertise'] ?? '') ?>"></div>
        <div class="form-group"><label>Bio</label><textarea name="bio" required><?= e($mentor['bio'] ?? '') ?></textarea></div>
        <div class="form-row">
            <div class="form-group"><label>Email</label><input type="email" name="email" required value="<?= e($mentor['email'] ?? '') ?>"></div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= e($mentor['phone'] ?? '') ?>"></div>
        </div>
        <div class="form-group"><label>Years Experience</label><input type="number" name="years_experience" value="<?= (int)($mentor['years_experience'] ?? 0) ?>"></div>
        <button type="submit" class="btn btn-blue">Save</button>
        <a href="mentors.php">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="dashboard-content" style="margin-bottom:2rem;">
    <h3 style="margin-bottom:1rem;">Mentorship Requests</h3>
    <table class="data-table">
        <thead><tr><th>User</th><th>Mentor</th><th>Message</th><th>Status</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($requests as $r): ?>
        <tr>
            <td><?= e($r['user_name']) ?></td>
            <td><?= e($r['mentor_name']) ?></td>
            <td><?= e(truncate($r['message'], 50)) ?></td>
            <td><span class="badge badge-warning"><?= e($r['status']) ?></span></td>
            <td>
                <?php if ($r['status'] === 'pending'): ?>
                <form method="POST" style="display:inline-flex;gap:0.25rem;">
                    <?= csrfField() ?>
                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                    <button name="status" value="accepted" class="btn-edit">Accept</button>
                    <button name="status" value="declined" class="btn-delete">Decline</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="dashboard-content">
    <h3 style="margin-bottom:1rem;">All Mentors</h3>
    <table class="data-table">
        <thead><tr><th>Name</th><th>Title</th><th>Expertise</th><th>Experience</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($mentors as $m): ?>
        <tr>
            <td><strong><?= e($m['full_name']) ?></strong></td>
            <td><?= e($m['title']) ?></td>
            <td><?= e(truncate($m['expertise'], 40)) ?></td>
            <td><?= (int)$m['years_experience'] ?> yrs</td>
            <td class="table-actions">
                <a href="?action=edit&id=<?= $m['id'] ?>" class="btn-edit">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Remove mentor?')">
                    <?= csrfField() ?><input type="hidden" name="delete_id" value="<?= $m['id'] ?>">
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
