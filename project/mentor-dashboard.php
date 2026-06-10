<?php
$pageTitle = 'Mentor Dashboard';
require_once __DIR__ . '/config/init.php';
requireLogin();
$pdo = getDBConnection();
$user = currentUser();

// Find mentor profile for this user by user_id or email
$stmt = $pdo->prepare("SELECT * FROM mentors WHERE user_id = ? OR email = ? LIMIT 1");
$stmt->execute([$user['id'], $user['email']]);
$mentor = $stmt->fetch();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Mentor Dashboard</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Mentor</span></div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if (!$mentor): ?>
            <div class="card">
                <h2>No Mentor Profile Found</h2>
                <p>We couldn't find a mentor profile linked to your account. If you are a mentor, ask an administrator to link your account or create a mentor profile for you.</p>
            </div>
        <?php else: ?>
            <h2 style="margin-bottom:1rem;">Requests for <?= e($mentor['full_name']) ?></h2>
            <?php
                $reqStmt = $pdo->prepare("SELECT mr.*, u.full_name as user_name, u.email as user_email FROM mentor_requests mr JOIN users u ON mr.user_id = u.id WHERE mr.mentor_id = ? ORDER BY mr.created_at DESC");
                $reqStmt->execute([$mentor['id']]);
                $requests = $reqStmt->fetchAll();
                $pendingCount = 0;
                foreach ($requests as $rr) if ($rr['status'] === 'pending') $pendingCount++;
            ?>

            <div style="display:flex;gap:1rem;align-items:center;margin-bottom:1rem;">
                <div class="stat-card" style="min-width:180px;"><h3><?= (int)$pendingCount ?></h3><p>Pending Requests</p></div>
                <div style="flex:1;color:var(--gray-700);">Below are mentorship requests sent by users. Click "View" to read the full message, then Accept or Decline.</div>
            </div>

            <?php if (empty($requests)): ?>
                <div class="empty-state"><i class="fas fa-inbox"></i><p>No mentorship requests yet.</p></div>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>User</th><th>Message</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
                    <tbody>
                    <?php foreach ($requests as $r): ?>
                    <tr>
                        <td style="vertical-align:top;"><strong><?= e($r['user_name']) ?></strong><br><small><?= e($r['user_email']) ?></small></td>
                        <td style="vertical-align:top;">
                            <div id="summary-<?= (int)$r['id'] ?>"><?= e(truncate($r['message'], 120)) ?></div>
                            <div id="full-<?= (int)$r['id'] ?>" style="display:none;margin-top:.5rem;background:#f9f9f9;padding:.5rem;border-radius:4px;white-space:pre-wrap;"><?= e($r['message']) ?></div>
                            <button class="btn btn-sm" onclick="toggleMessage(<?= (int)$r['id'] ?>)">View</button>
                        </td>
                        <td style="vertical-align:top;"><span class="badge badge-<?= $r['status'] === 'accepted' ? 'success' : ($r['status'] === 'declined' ? 'danger' : 'warning') ?>"><?= e($r['status']) ?></span></td>
                        <td style="vertical-align:top;"><?= formatDate($r['created_at']) ?></td>
                        <td style="vertical-align:top;">
                            <?php if ($r['status'] === 'pending'): ?>
                                <form method="POST" action="actions/mentor-request-action.php" style="display:inline;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="status" value="accepted">
                                    <button class="btn btn-green btn-sm" type="submit">Accept</button>
                                </form>
                                <form method="POST" action="actions/mentor-request-action.php" style="display:inline;margin-left:.5rem;">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="request_id" value="<?= (int)$r['id'] ?>">
                                    <input type="hidden" name="status" value="declined">
                                    <button class="btn btn-red btn-sm" type="submit">Decline</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <script>
                function toggleMessage(id) {
                    var full = document.getElementById('full-' + id);
                    var btn = event.currentTarget;
                    if (full.style.display === 'none' || full.style.display === '') {
                        full.style.display = 'block';
                        btn.textContent = 'Hide';
                    } else {
                        full.style.display = 'none';
                        btn.textContent = 'View';
                    }
                }
                </script>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
