<?php
$pageTitle = 'Mentors';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$mentorId = (int) ($_GET['id'] ?? 0);
$search = trim($_GET['search'] ?? '');

if ($mentorId) {
    $stmt = $pdo->prepare("SELECT * FROM mentors WHERE id = ? AND is_available = 1");
    $stmt->execute([$mentorId]);
    $mentor = $stmt->fetch();
    if (!$mentor) {
        setFlash('error', 'Mentor not found.');
        redirect(SITE_URL . '/mentors.php');
    }
}

$sql = "SELECT * FROM mentors WHERE is_available = 1";
$params = [];
if ($search) {
    $sql .= " AND (full_name LIKE ? OR expertise LIKE ? OR title LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= " ORDER BY full_name ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$mentors = $stmt->fetchAll();

$requestSent = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn() && $mentorId) {
    if (verifyCsrf($_POST['csrf_token'] ?? '')) {
        $stmt = $pdo->prepare("INSERT INTO mentor_requests (user_id, mentor_id, message) VALUES (?, ?, ?)");
        $stmt->execute([currentUser()['id'], $mentorId, trim($_POST['message'])]);
        setFlash('success', 'Your mentorship request was sent successfully!');
        redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?= $mentorId && isset($mentor) ? e($mentor['full_name']) : 'Our Mentors' ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span>
            <?php if ($mentorId && isset($mentor)): ?>
                <a href="mentors.php">Mentors</a> <span>/</span> <span>Profile</span>
            <?php else: ?>
                <span>Mentors</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if ($mentorId && isset($mentor)): ?>
        <div style="max-width:700px;margin:0 auto;">
            <div class="card mentor-card">
                <?= renderAvatar($mentor['image_url'] ?? null, $mentor['full_name'], 'mentors', 'mentor-avatar') ?>
                <div class="card-body" style="padding-top:3.5rem;text-align:center;">
                    <h2><?= e($mentor['full_name']) ?></h2>
                    <p class="mentor-title"><?= e($mentor['title']) ?></p>
                    <p style="color:var(--primary);font-weight:500;margin:1rem 0;"><i class="fas fa-star"></i> <?= e($mentor['expertise']) ?></p>
                    <p style="color:var(--gray-500);line-height:1.8;text-align:left;"><?= nl2br(e($mentor['bio'])) ?></p>
                    <div style="margin:1.5rem 0;display:flex;justify-content:center;gap:2rem;flex-wrap:wrap;">
                        <span><i class="fas fa-briefcase"></i> <?= (int)$mentor['years_experience'] ?>+ years experience</span>
                        <span><i class="fas fa-envelope"></i> <?= e($mentor['email']) ?></span>
                        <?php if ($mentor['phone']): ?><span><i class="fas fa-phone"></i> <?= e($mentor['phone']) ?></span><?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if (isLoggedIn()): ?>
            <div class="form-card" style="margin-top:2rem;">
                <h3 style="margin-bottom:1rem;color:var(--gray-900);">Request Mentorship</h3>
                <form method="POST">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label>Your Message</label>
                        <textarea name="message" required placeholder="Tell the mentor about your goals and what guidance you need..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-blue btn-block"><i class="fas fa-paper-plane"></i> Send Request</button>
                </form>
            </div>
            <?php else: ?>
            <div style="text-align:center;margin-top:2rem;">
                <a href="login.php" class="btn btn-blue">Login to Request Mentorship</a>
            </div>
            <?php endif; ?>
            <?php
                $canEdit = false;
                if (isLoggedIn()) {
                    if (isAdmin()) $canEdit = true;
                    $cu = currentUser();
                    if (!empty($mentor['user_id']) && isset($cu['id']) && $mentor['user_id'] === $cu['id']) $canEdit = true;
                    if (!$canEdit && isset($cu['email']) && $cu['email'] === ($mentor['email'] ?? '')) $canEdit = true;
                }
            ?>
            <?php if ($canEdit): ?>
            <div class="form-card" style="margin-top:2rem;">
                <h3 style="margin-bottom:1rem;color:var(--gray-900);">Update Profile Photo</h3>
                <form method="POST" action="actions/upload-mentor-profile.php" enctype="multipart/form-data">
                    <?= csrfField() ?>
                    <input type="hidden" name="mentor_id" value="<?= (int)$mentor['id'] ?>">
                    <div class="form-group">
                        <label>Upload Profile Photo</label>
                        <input type="file" name="mentor_photo" accept="image/jpeg,image/png,image/webp">
                        <p class="photo-upload-hint">JPG, PNG or WEBP — max 2MB. Appears on mentor profile.</p>
                    </div>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-upload"></i> Upload Photo</button>
                </form>
            </div>
            <?php endif; ?>

            <?php if ($canEdit): ?>
            <div style="margin-top:1rem;text-align:center;">
                <a href="mentor-edit.php?id=<?= (int)$mentor['id'] ?>" class="btn btn-outline">Edit Mentor Profile</a>
            </div>
            <?php endif; ?>
            <div style="text-align:center;margin-top:1.5rem;"><a href="mentors.php">&larr; Back to All Mentors</a></div>
        </div>
        <?php else: ?>
        <div class="section-header" style="margin-bottom:2rem;">
            <p>Connect with experienced professionals who are passionate about guiding the next generation of empowered women.</p>
        </div>
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search by name or expertise..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-blue btn-sm"><i class="fas fa-search"></i> Search</button>
        </form>
        <div class="card-grid">
            <?php foreach ($mentors as $m): ?>
            <div class="card mentor-card">
                <?= renderAvatar($m['image_url'] ?? null, $m['full_name'], 'mentors', 'mentor-avatar') ?>
                <div class="card-body">
                    <h3><?= e($m['full_name']) ?></h3>
                    <p class="mentor-title"><?= e($m['title']) ?></p>
                    <p style="font-size:0.85rem;color:var(--primary);margin:0.5rem 0;"><i class="fas fa-star"></i> <?= e($m['expertise']) ?></p>
                    <p style="font-size:0.9rem;color:var(--gray-500);"><?= e(truncate($m['bio'], 100)) ?></p>
                    <p style="font-size:0.8rem;color:var(--gray-500);margin-top:0.5rem;"><i class="fas fa-briefcase"></i> <?= (int)$m['years_experience'] ?> years experience</p>
                </div>
                <div class="card-footer">
                    <a href="mentors.php?id=<?= $m['id'] ?>" class="btn btn-blue btn-sm btn-block">View Profile</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
