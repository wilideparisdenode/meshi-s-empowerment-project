<?php
$pageTitle = 'Edit Mentor Profile';
require_once __DIR__ . '/config/init.php';
requireLogin();
$pdo = getDBConnection();

$mentorId = (int) ($_GET['id'] ?? ($_POST['mentor_id'] ?? 0));
if (!$mentorId) {
    setFlash('error', 'Invalid mentor.');
    redirect(SITE_URL . '/mentors.php');
}

$stmt = $pdo->prepare("SELECT * FROM mentors WHERE id = ? LIMIT 1");
$stmt->execute([$mentorId]);
$mentor = $stmt->fetch();
if (!$mentor) {
    setFlash('error', 'Mentor not found.');
    redirect(SITE_URL . '/mentors.php');
}

$user = currentUser();
$authorized = false;
if (isAdmin()) $authorized = true;
if (!$authorized && !empty($mentor['user_id']) && isset($user['id']) && $mentor['user_id'] === $user['id']) $authorized = true;
if (!$authorized && isset($user['email']) && $user['email'] === $mentor['email']) $authorized = true;
if (!$authorized) {
    setFlash('error', 'Access denied.');
    redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect(SITE_URL . '/mentor-edit.php?id=' . $mentorId);
    }

    $full_name = trim($_POST['full_name'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $years = (int) ($_POST['years_experience'] ?? 0);
    $is_available = isset($_POST['is_available']) ? 1 : 0;

    $errors = [];
    if ($full_name === '') $errors[] = 'Full name is required.';
    if ($title === '') $errors[] = 'Title is required.';
    if ($expertise === '') $errors[] = 'Expertise is required.';
    if ($bio === '') $errors[] = 'Bio is required.';
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';

    if (empty($errors)) {
        $update = $pdo->prepare("UPDATE mentors SET full_name=?, title=?, expertise=?, bio=?, email=?, phone=?, years_experience=?, is_available=? WHERE id=?");
        $update->execute([$full_name, $title, $expertise, $bio, $email, $phone, $years, $is_available, $mentorId]);
        setFlash('success', 'Mentor profile updated successfully.');
        redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
    } else {
        foreach ($errors as $err) setFlash('error', $err);
        redirect(SITE_URL . '/mentor-edit.php?id=' . $mentorId);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Edit Mentor Profile</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <a href="mentors.php">Mentors</a> <span>/</span> <span>Edit</span></div>
    </div>
</div>

<section class="section">
    <div class="container" style="max-width:800px;margin:0 auto;">
        <div class="card">
            <h2 style="margin-bottom:1rem;">Edit <?= e($mentor['full_name']) ?></h2>
            <form method="POST">
                <?= csrfField() ?>
                <input type="hidden" name="mentor_id" value="<?= (int)$mentor['id'] ?>">
                <div class="form-group"><label>Full Name</label><input type="text" name="full_name" required value="<?= e($mentor['full_name']) ?>"></div>
                <div class="form-group"><label>Title</label><input type="text" name="title" required value="<?= e($mentor['title']) ?>"></div>
                <div class="form-group"><label>Expertise</label><input type="text" name="expertise" required value="<?= e($mentor['expertise']) ?>"></div>
                <div class="form-group"><label>Bio</label><textarea name="bio" required><?= e($mentor['bio']) ?></textarea></div>
                <div class="form-group"><label>Email</label><input type="email" name="email" required value="<?= e($mentor['email']) ?>"></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= e($mentor['phone']) ?>"></div>
                <div class="form-group"><label>Years Experience</label><input type="number" name="years_experience" value="<?= (int)$mentor['years_experience'] ?>"></div>
                <div class="form-group"><label><input type="checkbox" name="is_available" <?= $mentor['is_available'] ? 'checked' : '' ?>> Accept Mentees (Available)</label></div>
                <div style="display:flex;gap:1rem;align-items:center;"><button class="btn btn-blue" type="submit">Save Changes</button><a href="mentors.php?id=<?= (int)$mentor['id'] ?>" class="btn btn-outline">Cancel</a></div>
            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
