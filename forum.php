<?php
$pageTitle = 'Community Forum';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$categoryId = (int) ($_GET['category'] ?? 0);
$topicId = (int) ($_GET['topic'] ?? 0);

// Handle new topic
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    requireLogin();
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
    } elseif ($_POST['action'] === 'new_topic') {
        $stmt = $pdo->prepare("INSERT INTO forum_topics (category_id, user_id, title, content) VALUES (?, ?, ?, ?)");
        $stmt->execute([$_POST['category_id'], currentUser()['id'], trim($_POST['title']), trim($_POST['content'])]);
        setFlash('success', 'Your topic was created successfully!');
        redirect(SITE_URL . '/forum.php?topic=' . $pdo->lastInsertId());
    } elseif ($_POST['action'] === 'reply') {
        $stmt = $pdo->prepare("INSERT INTO forum_replies (topic_id, user_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$_POST['topic_id'], currentUser()['id'], trim($_POST['content'])]);
        setFlash('success', 'Your reply was posted successfully!');
        redirect(SITE_URL . '/forum.php?topic=' . $_POST['topic_id']);
    }
}

$categories = $pdo->query("SELECT fc.*, (SELECT COUNT(*) FROM forum_topics ft WHERE ft.category_id = fc.id) as topic_count FROM forum_categories fc ORDER BY fc.name")->fetchAll();

// Single topic view
if ($topicId) {
    $pdo->prepare("UPDATE forum_topics SET views = views + 1 WHERE id = ?")->execute([$topicId]);
    $stmt = $pdo->prepare("SELECT ft.*, u.full_name, fc.name as category_name FROM forum_topics ft JOIN users u ON ft.user_id = u.id JOIN forum_categories fc ON ft.category_id = fc.id WHERE ft.id = ?");
    $stmt->execute([$topicId]);
    $topic = $stmt->fetch();
    if (!$topic) { setFlash('error', 'Topic not found.'); redirect(SITE_URL . '/forum.php'); }

    $stmt = $pdo->prepare("SELECT fr.*, u.full_name FROM forum_replies fr JOIN users u ON fr.user_id = u.id WHERE fr.topic_id = ? ORDER BY fr.created_at ASC");
    $stmt->execute([$topicId]);
    $replies = $stmt->fetchAll();
}

// Topics list
$topicSql = "SELECT ft.*, u.full_name, fc.name as category_name,
    (SELECT COUNT(*) FROM forum_replies fr WHERE fr.topic_id = ft.id) as reply_count
    FROM forum_topics ft JOIN users u ON ft.user_id = u.id JOIN forum_categories fc ON ft.category_id = fc.id";
$topicParams = [];
if ($categoryId) { $topicSql .= " WHERE ft.category_id = ?"; $topicParams[] = $categoryId; }
$topicSql .= " ORDER BY ft.is_pinned DESC, ft.updated_at DESC";
$stmt = $pdo->prepare($topicSql);
$stmt->execute($topicParams);
$topics = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Community Forum</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Forum</span></div>
    </div>
</div>

<section class="section">
    <div class="container">
        <?php if ($topicId && isset($topic)): ?>
        <div style="max-width:800px;margin:0 auto;">
            <div class="card" style="padding:1.5rem;margin-bottom:1rem;">
                <div class="card-meta" style="margin-bottom:0.75rem;">
                    <span><?= e($topic['category_name']) ?></span>
                    <?php if ($topic['is_pinned']): ?><span style="background:#fef3c7;color:#92400e;">Pinned</span><?php endif; ?>
                </div>
                <h2 style="color:var(--gray-900);margin-bottom:1rem;"><?= e($topic['title']) ?></h2>
                <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1rem;">
                    <div class="topic-avatar"><?= strtoupper(substr($topic['full_name'], 0, 1)) ?></div>
                    <div>
                        <strong><?= e($topic['full_name']) ?></strong><br>
                        <small style="color:var(--gray-500);"><?= timeAgo($topic['created_at']) ?> &bull; <?= (int)$topic['views'] ?> views</small>
                    </div>
                </div>
                <p style="line-height:1.8;color:var(--gray-700);"><?= nl2br(e($topic['content'])) ?></p>
            </div>

            <h3 style="margin:1.5rem 0 1rem;color:var(--gray-900);"><?= count($replies) ?> Replies</h3>
            <?php foreach ($replies as $reply): ?>
            <div class="card" style="padding:1.25rem;margin-bottom:0.75rem;">
                <div style="display:flex;gap:1rem;">
                    <div class="topic-avatar" style="width:40px;height:40px;font-size:0.9rem;"><?= strtoupper(substr($reply['full_name'], 0, 1)) ?></div>
                    <div>
                        <strong><?= e($reply['full_name']) ?></strong>
                        <small style="color:var(--gray-500);margin-left:0.5rem;"><?= timeAgo($reply['created_at']) ?></small>
                        <p style="margin-top:0.5rem;line-height:1.7;"><?= nl2br(e($reply['content'])) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

            <?php if (isLoggedIn() && !$topic['is_locked']): ?>
            <div class="form-card" style="margin-top:1.5rem;">
                <h3 style="margin-bottom:1rem;">Post a Reply</h3>
                <form method="POST">
                    <?= csrfField() ?>
                    <input type="hidden" name="action" value="reply">
                    <input type="hidden" name="topic_id" value="<?= $topicId ?>">
                    <div class="form-group">
                        <textarea name="content" required placeholder="Share your thoughts..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-blue"><i class="fas fa-reply"></i> Post Reply</button>
                </form>
            </div>
            <?php elseif ($topic['is_locked']): ?>
            <p style="text-align:center;color:var(--gray-500);margin-top:1rem;"><i class="fas fa-lock"></i> This topic is locked.</p>
            <?php else: ?>
            <p style="text-align:center;margin-top:1rem;"><a href="login.php" class="btn btn-blue">Login to Reply</a></p>
            <?php endif; ?>
            <div style="text-align:center;margin-top:1.5rem;"><a href="forum.php">&larr; Back to Forum</a></div>
        </div>

        <?php else: ?>
        <div class="forum-layout">
            <aside class="forum-sidebar">
                <h3>Categories</h3>
                <a href="forum.php" class="forum-cat <?= !$categoryId ? 'active' : '' ?>">
                    <i class="fas fa-comments"></i> All Topics
                </a>
                <?php foreach ($categories as $cat): ?>
                <a href="forum.php?category=<?= $cat['id'] ?>" class="forum-cat <?= $categoryId == $cat['id'] ? 'active' : '' ?>">
                    <i class="fas <?= e($cat['icon']) ?>"></i>
                    <?= e($cat['name']) ?>
                    <span style="margin-left:auto;font-size:0.8rem;color:var(--gray-500);"><?= (int)$cat['topic_count'] ?></span>
                </a>
                <?php endforeach; ?>

                <?php if (isLoggedIn()): ?>
                <button class="btn btn-blue btn-sm btn-block" style="margin-top:1.5rem;" data-modal="newTopicModal">
                    <i class="fas fa-plus"></i> New Topic
                </button>
                <?php endif; ?>
            </aside>

            <div>
                <?php if (empty($topics)): ?>
                <div class="empty-state"><i class="fas fa-comments"></i><h3>No topics yet</h3><p>Be the first to start a discussion!</p></div>
                <?php else: ?>
                <?php foreach ($topics as $t): ?>
                <a href="forum.php?topic=<?= $t['id'] ?>" class="topic-item" style="text-decoration:none;color:inherit;">
                    <div class="topic-avatar"><?= strtoupper(substr($t['full_name'], 0, 1)) ?></div>
                    <div class="topic-info">
                        <h4><?= e($t['title']) ?> <?= $t['is_pinned'] ? '<i class="fas fa-thumbtack" style="color:var(--warning);font-size:0.8rem;"></i>' : '' ?></h4>
                        <div class="topic-meta">
                            <?= e($t['full_name']) ?> &bull; <?= e($t['category_name']) ?> &bull; <?= timeAgo($t['created_at']) ?>
                        </div>
                    </div>
                    <div class="topic-stats">
                        <div><strong><?= (int)$t['reply_count'] ?></strong><br>replies</div>
                        <div style="margin-top:0.5rem;"><strong><?= (int)$t['views'] ?></strong><br>views</div>
                    </div>
                </a>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php if (isLoggedIn()): ?>
<div class="modal-overlay" id="newTopicModal">
    <div class="modal">
        <h3>Start New Discussion</h3>
        <form method="POST">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="new_topic">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $categoryId == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" required placeholder="Topic title">
            </div>
            <div class="form-group">
                <label>Content</label>
                <textarea name="content" required placeholder="Share your question or idea..."></textarea>
            </div>
            <button type="submit" class="btn btn-blue">Create Topic</button>
            <button type="button" class="btn modal-close" style="background:var(--gray-200);color:var(--gray-700);margin-left:0.5rem;">Cancel</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
