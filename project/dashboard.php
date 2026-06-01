<?php
$pageTitle = 'My Dashboard';
require_once __DIR__ . '/config/init.php';
requireLogin();
$pdo = getDBConnection();
$userId = currentUser()['id'];

$tab = $_GET['tab'] ?? 'overview';

// User enrollments
$enrollments = $pdo->prepare("SELECT e.*, c.title, c.category, c.duration FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.user_id = ? ORDER BY e.enrolled_at DESC");
$enrollments->execute([$userId]);
$myCourses = $enrollments->fetchAll();

// Event registrations
$events = $pdo->prepare("SELECT er.*, ev.title, ev.event_date, ev.location FROM event_registrations er JOIN events ev ON er.event_id = ev.id WHERE er.user_id = ? ORDER BY ev.event_date ASC");
$events->execute([$userId]);
$myEvents = $events->fetchAll();

// Mentor requests
$requests = $pdo->prepare("SELECT mr.*, m.full_name as mentor_name FROM mentor_requests mr JOIN mentors m ON mr.mentor_id = m.id WHERE mr.user_id = ? ORDER BY mr.created_at DESC");
$requests->execute([$userId]);
$myRequests = $requests->fetchAll();

// Resources
$resources = $pdo->query("SELECT * FROM resources WHERE is_active = 1 ORDER BY created_at DESC LIMIT 10")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>my Resource Center</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Dashboard</span></div>
    </div>
</div>

<section class="welcome-banner" style="padding:2rem 0;">
    <div class="container">
        <h1 style="font-size:1.65rem;">Welcome to Smart Girl Empowerment Platform</h1>
        <p style="font-size:1rem;">Empowering girls and young women to learn, lead, innovate, and succeed.</p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container dashboard-layout">
        <aside class="dashboard-sidebar">
            <div class="user-info">
                <?= renderAvatar(currentUser()['profile_image'] ?? null, currentUser()['full_name'], 'users', 'user-avatar') ?>
                <h3 style="font-size:1rem;color:var(--gray-900);"><?= e(currentUser()['full_name']) ?></h3>
                <p style="font-size:0.85rem;color:var(--gray-500);"><?= e(currentUser()['email']) ?></p>
            </div>
            <nav class="dashboard-nav">
                <a href="?tab=overview" class="<?= $tab === 'overview' ? 'active' : '' ?>"><i class="fas fa-home"></i> Overview</a>
                <a href="?tab=profile" class="<?= $tab === 'profile' ? 'active' : '' ?>"><i class="fas fa-user-circle"></i> My Profile</a>
                <a href="?tab=courses" class="<?= $tab === 'courses' ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> My Courses</a>
                <a href="?tab=events" class="<?= $tab === 'events' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> My Events</a>
                <a href="?tab=mentorship" class="<?= $tab === 'mentorship' ? 'active' : '' ?>"><i class="fas fa-hands-helping"></i> Mentorship</a>
                <a href="?tab=resources" class="<?= $tab === 'resources' ? 'active' : '' ?>"><i class="fas fa-book"></i> Resources</a>
                <a href="courses.php"><i class="fas fa-search"></i> Browse Courses</a>
                <a href="scholarships.php"><i class="fas fa-award"></i> Scholarships</a>
                <a href="forum.php"><i class="fas fa-comments"></i> Forum</a>
            </nav>
        </aside>

        <div class="dashboard-content">
            <?php if ($tab === 'overview'): ?>
            <h2 style="margin-bottom:1.5rem;color:var(--gray-900);">Welcome, <?= e(explode(' ', currentUser()['full_name'])[0]) ?>!</h2>
            <div class="stat-cards">
                <div class="stat-card"><h3><?= count($myCourses) ?></h3><p>Enrolled Courses</p></div>
                <div class="stat-card accent"><h3><?= count($myEvents) ?></h3><p>Registered Events</p></div>
                <div class="stat-card"><h3><?= count($myRequests) ?></h3><p>Mentor Requests</p></div>
            </div>
            <h3 style="margin:1.5rem 0 1rem;color:var(--gray-900);">Quick Actions</h3>
            <div style="display:flex;flex-wrap:wrap;gap:1rem;">
                <a href="courses.php" class="btn btn-blue btn-sm"><i class="fas fa-graduation-cap"></i> Browse Courses</a>
                <a href="mentors.php" class="btn btn-blue btn-sm"><i class="fas fa-user-tie"></i> Find a Mentor</a>
                <a href="scholarships.php" class="btn btn-blue btn-sm"><i class="fas fa-award"></i> View Scholarships</a>
                <a href="events.php" class="btn btn-blue btn-sm"><i class="fas fa-calendar"></i> Upcoming Events</a>
            </div>

            <?php elseif ($tab === 'courses'): ?>
            <h2 style="margin-bottom:1.5rem;">My Enrolled Courses</h2>
            <?php if (empty($myCourses)): ?>
            <div class="empty-state"><i class="fas fa-book"></i><p>You haven't enrolled in any courses yet.</p><a href="courses.php" class="btn btn-blue" style="margin-top:1rem;">Browse Courses</a></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Course</th><th>Category</th><th>Duration</th><th>Status</th><th>Enrolled</th></tr></thead>
                <tbody>
                <?php foreach ($myCourses as $c): ?>
                <tr>
                    <td><strong><?= e($c['title']) ?></strong></td>
                    <td><?= e($c['category']) ?></td>
                    <td><?= e($c['duration']) ?></td>
                    <td><span class="badge badge-success"><?= e($c['status']) ?></span></td>
                    <td><?= formatDate($c['enrolled_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php elseif ($tab === 'events'): ?>
            <h2 style="margin-bottom:1.5rem;">My Registered Events</h2>
            <?php if (empty($myEvents)): ?>
            <div class="empty-state"><i class="fas fa-calendar"></i><p>No event registrations yet.</p><a href="events.php" class="btn btn-blue" style="margin-top:1rem;">View Events</a></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Event</th><th>Date</th><th>Location</th><th>Registered</th></tr></thead>
                <tbody>
                <?php foreach ($myEvents as $ev): ?>
                <tr>
                    <td><strong><?= e($ev['title']) ?></strong></td>
                    <td><?= formatDateTime($ev['event_date']) ?></td>
                    <td><?= e($ev['location']) ?></td>
                    <td><?= formatDate($ev['registered_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php elseif ($tab === 'mentorship'): ?>
            <h2 style="margin-bottom:1.5rem;">My Mentorship Requests</h2>
            <?php if (empty($myRequests)): ?>
            <div class="empty-state"><i class="fas fa-hands-helping"></i><p>No mentorship requests yet.</p><a href="mentors.php" class="btn btn-blue" style="margin-top:1rem;">Find a Mentor</a></div>
            <?php else: ?>
            <table class="data-table">
                <thead><tr><th>Mentor</th><th>Message</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($myRequests as $r): ?>
                <tr>
                    <td><strong><?= e($r['mentor_name']) ?></strong></td>
                    <td><?= e(truncate($r['message'], 80)) ?></td>
                    <td><span class="badge badge-<?= $r['status'] === 'accepted' ? 'success' : ($r['status'] === 'declined' ? 'danger' : 'warning') ?>"><?= e($r['status']) ?></span></td>
                    <td><?= formatDate($r['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

            <?php elseif ($tab === 'profile'): ?>
            <h2 style="margin-bottom:1.5rem;">My Profile Photo</h2>
            <div style="max-width:400px;">
                <?= renderAvatar(currentUser()['profile_image'] ?? null, currentUser()['full_name'], 'users', 'user-avatar') ?>
                <form method="POST" action="actions/upload-profile.php" enctype="multipart/form-data" style="margin-top:1.5rem;">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Upload Profile Photo</label>
                        <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/webp" required>
                        <p class="photo-upload-hint">JPG, PNG or WEBP — max 2MB. Your photo appears on your dashboard and forum posts.</p>
                    </div>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-upload"></i> Upload Profile Photo</button>
                </form>
            </div>

            <?php elseif ($tab === 'resources'): ?>
            <h2 style="margin-bottom:1.5rem;">Career & Empowerment Resources</h2>
            <div class="card-grid" style="grid-template-columns:1fr;">
                <?php foreach ($resources as $res): ?>
                <div class="card" style="display:flex;align-items:center;gap:1.5rem;padding:1.25rem;">
                    <div class="feature-icon" style="margin:0;flex-shrink:0;width:50px;height:50px;font-size:1.25rem;">
                        <i class="fas fa-<?= $res['resource_type'] === 'video' ? 'video' : ($res['resource_type'] === 'guide' ? 'book' : 'file-alt') ?>"></i>
                    </div>
                    <div style="flex:1;">
                        <h3 style="font-size:1rem;margin-bottom:0.25rem;"><?= e($res['title']) ?></h3>
                        <p style="font-size:0.9rem;color:var(--gray-500);"><?= e(truncate($res['description'], 120)) ?></p>
                        <span class="badge badge-info"><?= e(ucfirst($res['resource_type'])) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
