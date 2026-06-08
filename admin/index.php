<?php
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$pdo = getDBConnection();
$stats = getStats($pdo);
$unreadMessages = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
$pendingRequests = (int) $pdo->query("SELECT COUNT(*) FROM mentor_requests WHERE status = 'pending'")->fetchColumn();

require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Admin Dashboard</h1>
    <span>Welcome, <?= e(currentUser()['full_name']) ?></span>
</div>

<div class="stat-cards" style="margin-bottom:2rem;">
    <div class="stat-card"><h3><?= $stats['users'] ?></h3><p>Registered Users</p></div>
    <div class="stat-card"><h3><?= $stats['courses'] ?></h3><p>Active Courses</p></div>
    <div class="stat-card"><h3><?= $stats['mentors'] ?></h3><p>Available Mentors</p></div>
    <div class="stat-card accent"><h3><?= $stats['enrollments'] ?></h3><p>Total Enrollments</p></div>
    <div class="stat-card"><h3><?= $unreadMessages ?></h3><p>Unread Messages</p></div>
    <div class="stat-card"><h3><?= $pendingRequests ?></h3><p>Pending Mentorship</p></div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;">
    <div class="dashboard-content">
        <h3 style="margin-bottom:1rem;">Recent Contact Messages</h3>
        <?php
        $messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();
        if (empty($messages)): ?>
        <p style="color:var(--gray-500);">No messages yet.</p>
        <?php else: ?>
        <table class="data-table">
            <thead><tr><th>From</th><th>Subject</th><th>Date</th></tr></thead>
            <tbody>
            <?php foreach ($messages as $m): ?>
            <tr>
                <td><?= e($m['name']) ?></td>
                <td><?= e(truncate($m['subject'], 30)) ?></td>
                <td><?= timeAgo($m['created_at']) ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <a href="messages.php" style="display:inline-block;margin-top:1rem;">View all messages &rarr;</a>
        <?php endif; ?>
    </div>
    <div class="dashboard-content">
        <h3 style="margin-bottom:1rem;">Quick Management</h3>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <a href="courses.php?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add New Course</a>
            <a href="mentors.php?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add New Mentor</a>
            <a href="events.php?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add New Event</a>
            <a href="scholarships.php?action=add" class="btn btn-blue btn-sm"><i class="fas fa-plus"></i> Add Scholarship</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
