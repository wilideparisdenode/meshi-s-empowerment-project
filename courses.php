<?php
$pageTitle = 'Courses';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$level = trim($_GET['level'] ?? '');
$courseId = (int) ($_GET['id'] ?? 0);

// Single course view
if ($courseId) {
    $stmt = $pdo->prepare("SELECT * FROM courses WHERE id = ? AND is_active = 1");
    $stmt->execute([$courseId]);
    $course = $stmt->fetch();
    if (!$course) {
        setFlash('error', 'Course not found.');
        redirect(SITE_URL . '/courses.php');
    }
    $enrolled = false;
    if (isLoggedIn()) {
        $stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmt->execute([currentUser()['id'], $courseId]);
        $enrolled = (bool) $stmt->fetch();
    }
}

// List courses
$sql = "SELECT * FROM courses WHERE is_active = 1";
$params = [];
if ($search) { $sql .= " AND (title LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
if ($category) { $sql .= " AND category = ?"; $params[] = $category; }
if ($level) { $sql .= " AND level = ?"; $params[] = $level; }
$sql .= " ORDER BY title ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$courses = $stmt->fetchAll();
$categories = getCourseCategories($pdo);

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1><?= $courseId && isset($course) ? e($course['title']) : 'Online Courses' ?></h1>
        <div class="breadcrumb">
            <a href="index.php">Home</a> <span>/</span>
            <?php if ($courseId && isset($course)): ?>
                <a href="courses.php">Courses</a> <span>/</span> <span><?= e(truncate($course['title'], 40)) ?></span>
            <?php else: ?>
                <span>Courses</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if ($courseId && isset($course)): ?>
        <div style="max-width:800px;margin:0 auto;">
            <div class="card" style="padding:2rem;">
                <div class="card-meta" style="margin-bottom:1.5rem;">
                    <span><?= e($course['category']) ?></span>
                    <span><?= e($course['level']) ?></span>
                    <span><i class="fas fa-clock"></i> <?= e($course['duration']) ?></span>
                    <span><i class="fas fa-user"></i> <?= e($course['instructor']) ?></span>
                </div>
                <h2 style="color:var(--gray-900);margin-bottom:1rem;"><?= e($course['title']) ?></h2>
                <p style="color:var(--gray-500);line-height:1.8;margin-bottom:2rem;"><?= nl2br(e($course['description'])) ?></p>
                <?php if (isLoggedIn()): ?>
                    <?php if ($enrolled): ?>
                        <span class="badge badge-success" style="font-size:1rem;padding:0.5rem 1rem;"><i class="fas fa-check"></i> You have successfully enrolled in this course</span>
                    <?php else: ?>
                        <form method="POST" action="actions/enroll.php" style="display:inline;">
                            <?= csrfField() ?>
                            <input type="hidden" name="course_id" value="<?= $course['id'] ?>">
                            <button type="submit" class="btn btn-blue"><i class="fas fa-graduation-cap"></i> Enroll Successfully</button>
                        </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="btn btn-blue"><i class="fas fa-sign-in-alt"></i> Login to Enroll</a>
                <?php endif; ?>
                <a href="courses.php" class="btn btn-outline" style="color:var(--primary);border-color:var(--primary);margin-left:0.5rem;">Back to Courses</a>
            </div>
        </div>
        <?php else: ?>
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search courses..." value="<?= e($search) ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="level">
                <option value="">All Levels</option>
                <?php foreach (['Beginner', 'Intermediate', 'Advanced'] as $lvl): ?>
                <option value="<?= $lvl ?>" <?= $level === $lvl ? 'selected' : '' ?>><?= $lvl ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-blue btn-sm"><i class="fas fa-search"></i> Filter</button>
        </form>

        <?php if (empty($courses)): ?>
        <div class="empty-state"><i class="fas fa-book"></i><h3>No courses found</h3><p>Try adjusting your search filters.</p></div>
        <?php else: ?>
        <div class="card-grid">
            <?php foreach ($courses as $c): ?>
            <div class="card">
                <div class="card-image"><i class="fas fa-graduation-cap"></i></div>
                <div class="card-body">
                    <div class="card-meta">
                        <span><?= e($c['category']) ?></span>
                        <span><?= e($c['level']) ?></span>
                    </div>
                    <h3><?= e($c['title']) ?></h3>
                    <p><?= e(truncate($c['description'], 120)) ?></p>
                    <p style="font-size:0.85rem;color:var(--gray-500);"><i class="fas fa-user"></i> <?= e($c['instructor']) ?> &bull; <?= e($c['duration']) ?></p>
                </div>
                <div class="card-footer">
                    <a href="courses.php?id=<?= $c['id'] ?>" class="btn btn-blue btn-sm btn-block">View & Enroll</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
