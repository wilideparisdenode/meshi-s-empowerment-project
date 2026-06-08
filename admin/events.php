<?php
$pageTitle = 'Manage Events';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();
$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
    } elseif (isset($_POST['delete_id'])) {
        $pdo->prepare("UPDATE events SET is_active = 0 WHERE id = ?")->execute([$_POST['delete_id']]);
        setFlash('success', 'Event was deleted successfully.');
    } else {
        $data = [trim($_POST['title']), trim($_POST['description']), $_POST['event_date'],
            trim($_POST['location']), trim($_POST['event_type']), (int)$_POST['max_participants']];
        if ($id) {
            $pdo->prepare("UPDATE events SET title=?, description=?, event_date=?, location=?, event_type=?, max_participants=? WHERE id=?")->execute([...$data, $id]);
            setFlash('success', 'Event was updated successfully.');
        } else {
            $pdo->prepare("INSERT INTO events (title, description, event_date, location, event_type, max_participants) VALUES (?,?,?,?,?,?)")->execute($data);
            setFlash('success', 'Event was added successfully.');
        }
        redirect(SITE_URL . '/admin/events.php');
    }
}

$event = null;
if ($id && $action === 'edit') {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    if ($event) $event['event_date'] = date('Y-m-d\TH:i', strtotime($event['event_date']));
}
$events = $pdo->query("SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id=e.id) as reg_count FROM events e ORDER BY event_date DESC")->fetchAll();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Manage Events</h1>
    <?php if ($action === 'list'): ?><a href="?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add Event</a><?php endif; ?>
</div>

<?php if ($action === 'add' || $action === 'edit'): ?>
<div class="dashboard-content" style="max-width:600px;">
    <form method="POST">
        <?= csrfField() ?>
        <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($event['title'] ?? '') ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" required><?= e($event['description'] ?? '') ?></textarea></div>
        <div class="form-group"><label>Date & Time</label><input type="datetime-local" name="event_date" required value="<?= e($event['event_date'] ?? '') ?>"></div>
        <div class="form-group"><label>Location</label><input type="text" name="location" required value="<?= e($event['location'] ?? '') ?>"></div>
        <div class="form-row">
            <div class="form-group"><label>Type</label><input type="text" name="event_type" required value="<?= e($event['event_type'] ?? '') ?>" placeholder="Workshop, Conference..."></div>
            <div class="form-group"><label>Max Participants</label><input type="number" name="max_participants" value="<?= (int)($event['max_participants'] ?? 100) ?>"></div>
        </div>
        <button type="submit" class="btn btn-blue">Save Event</button>
        <a href="events.php">Cancel</a>
    </form>
</div>
<?php else: ?>
<div class="dashboard-content">
    <table class="data-table">
        <thead><tr><th>Title</th><th>Date</th><th>Location</th><th>Registered</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($events as $ev): ?>
        <tr>
            <td><strong><?= e($ev['title']) ?></strong></td>
            <td><?= formatDateTime($ev['event_date']) ?></td>
            <td><?= e(truncate($ev['location'], 30)) ?></td>
            <td><?= (int)$ev['reg_count'] ?>/<?= (int)$ev['max_participants'] ?></td>
            <td class="table-actions">
                <a href="?action=edit&id=<?= $ev['id'] ?>" class="btn-edit">Edit</a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete?')">
                    <?= csrfField() ?><input type="hidden" name="delete_id" value="<?= $ev['id'] ?>">
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
