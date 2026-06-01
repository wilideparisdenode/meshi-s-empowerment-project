<?php
$pageTitle = 'Events';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$filter = $_GET['filter'] ?? 'upcoming';
$sql = "SELECT e.*, (SELECT COUNT(*) FROM event_registrations er WHERE er.event_id = e.id) as registered_count FROM events e WHERE e.is_active = 1";
if ($filter === 'upcoming') $sql .= " AND e.event_date >= NOW()";
elseif ($filter === 'past') $sql .= " AND e.event_date < NOW()";
$sql .= " ORDER BY e.event_date " . ($filter === 'past' ? 'DESC' : 'ASC');
$events = $pdo->query($sql)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Events & Workshops</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Events</span></div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="filter-bar">
            <a href="?filter=upcoming" class="btn btn-sm <?= $filter === 'upcoming' ? 'btn-blue' : '' ?>" style="<?= $filter !== 'upcoming' ? 'background:var(--gray-100);color:var(--gray-700);' : '' ?>">Upcoming Events</a>
            <a href="?filter=past" class="btn btn-sm <?= $filter === 'past' ? 'btn-blue' : '' ?>" style="<?= $filter !== 'past' ? 'background:var(--gray-100);color:var(--gray-700);' : '' ?>">Past Events</a>
            <a href="?filter=all" class="btn btn-sm <?= $filter === 'all' ? 'btn-blue' : '' ?>" style="<?= $filter !== 'all' ? 'background:var(--gray-100);color:var(--gray-700);' : '' ?>">All Events</a>
        </div>

        <?php if (empty($events)): ?>
        <div class="empty-state"><i class="fas fa-calendar"></i><h3>No events found</h3></div>
        <?php else: ?>
        <div class="card-grid">
            <?php foreach ($events as $event):
                $isPast = strtotime($event['event_date']) < time();
                $registered = false;
                if (isLoggedIn()) {
                    $stmt = $pdo->prepare("SELECT id FROM event_registrations WHERE user_id = ? AND event_id = ?");
                    $stmt->execute([currentUser()['id'], $event['id']]);
                    $registered = (bool) $stmt->fetch();
                }
                $spotsLeft = $event['max_participants'] - $event['registered_count'];
            ?>
            <div class="card">
                <div class="event-date">
                    <span class="day"><?= date('d', strtotime($event['event_date'])) ?></span>
                    <span class="month"><?= date('M Y', strtotime($event['event_date'])) ?></span>
                </div>
                <div class="card-body">
                    <div class="card-meta"><span><?= e($event['event_type']) ?></span></div>
                    <h3><?= e($event['title']) ?></h3>
                    <p><?= e(truncate($event['description'], 130)) ?></p>
                    <p style="font-size:0.85rem;color:var(--gray-500);margin-top:0.75rem;">
                        <i class="fas fa-clock"></i> <?= formatDateTime($event['event_date']) ?><br>
                        <i class="fas fa-map-marker-alt"></i> <?= e($event['location']) ?><br>
                        <i class="fas fa-users"></i> <?= (int)$event['registered_count'] ?>/<?= (int)$event['max_participants'] ?> registered
                    </p>
                </div>
                <div class="card-footer">
                    <?php if (!$isPast && isLoggedIn()): ?>
                        <?php if ($registered): ?>
                        <span class="badge badge-success btn-block" style="display:block;text-align:center;padding:0.6rem;">Successfully Registered</span>
                        <?php elseif ($spotsLeft > 0): ?>
                        <form method="POST" action="actions/register-event.php">
                            <?= csrfField() ?>
                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                            <button type="submit" class="btn btn-blue btn-sm btn-block"><i class="fas fa-ticket-alt"></i> Register Successfully</button>
                        </form>
                        <?php else: ?>
                        <span class="badge badge-warning btn-block" style="display:block;text-align:center;padding:0.6rem;">Full</span>
                        <?php endif; ?>
                    <?php elseif (!$isPast): ?>
                        <a href="login.php" class="btn btn-blue btn-sm btn-block">Login to Register</a>
                    <?php else: ?>
                        <span class="badge badge-info btn-block" style="display:block;text-align:center;padding:0.6rem;">Completed</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
