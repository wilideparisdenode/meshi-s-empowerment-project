<?php
$pageTitle = 'Scholarships';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM scholarships WHERE is_active = 1";
$params = [];
if ($search) {
    $sql .= " AND (title LIKE ? OR provider LIKE ? OR description LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}
$sql .= " ORDER BY deadline ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$scholarships = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Scholarship Opportunities</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Scholarships</span></div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="section-header" style="margin-bottom:2rem;">
            <p>Discover financial and educational support opportunities for girls in Cameroon and beyond.</p>
        </div>
        <form method="GET" class="filter-bar">
            <input type="text" name="search" placeholder="Search scholarships..." value="<?= e($search) ?>">
            <button type="submit" class="btn btn-blue btn-sm"><i class="fas fa-search"></i> Search</button>
        </form>

        <?php if (empty($scholarships)): ?>
        <div class="empty-state"><i class="fas fa-award"></i><h3>No scholarships available</h3></div>
        <?php else: ?>
        <div class="card-grid">
            <?php foreach ($scholarships as $s):
                $daysLeft = (strtotime($s['deadline']) - time()) / 86400;
                $expired = $daysLeft < 0;
            ?>
            <div class="card">
                <div class="card-body">
                    <span class="scholarship-amount"><i class="fas fa-money-bill-wave"></i> <?= e($s['amount']) ?></span>
                    <h3><?= e($s['title']) ?></h3>
                    <p style="font-size:0.9rem;color:var(--primary);margin-bottom:0.5rem;"><i class="fas fa-building"></i> <?= e($s['provider']) ?></p>
                    <p><?= e(truncate($s['description'], 150)) ?></p>
                    <p style="margin:1rem 0;font-size:0.9rem;"><strong>Eligibility:</strong> <?= e(truncate($s['eligibility'], 100)) ?></p>
                    <span class="deadline-badge <?= $expired ? 'badge-danger' : '' ?>">
                        <i class="fas fa-calendar"></i>
                        Deadline: <?= formatDate($s['deadline']) ?>
                        <?= !$expired ? '(' . ceil($daysLeft) . ' days left)' : '(Expired)' ?>
                    </span>
                </div>
                <div class="card-footer">
                    <?php if (isLoggedIn() && !$expired): ?>
                    <a href="contact.php?subject=Scholarship: <?= urlencode($s['title']) ?>" class="btn btn-blue btn-sm btn-block">Apply / Inquire</a>
                    <?php elseif (!$expired): ?>
                    <a href="register.php" class="btn btn-blue btn-sm btn-block">Register to Apply</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
