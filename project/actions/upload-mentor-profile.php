<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/mentors.php');
}

$mentorId = (int) ($_POST['mentor_id'] ?? 0);
if (!$mentorId) {
    setFlash('error', 'Invalid mentor.');
    redirect(SITE_URL . '/mentors.php');
}

$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT image_url, email FROM mentors WHERE id = ?");
$stmt->execute([$mentorId]);
$mentor = $stmt->fetch();
if (!$mentor) {
    setFlash('error', 'Mentor not found.');
    redirect(SITE_URL . '/mentors.php');
}

// Only admins or the mentor (matching user_id or email) can update the mentor photo
$user = currentUser();
$canUpdate = false;
if (isAdmin()) {
    $canUpdate = true;
} else {
    if (!empty($mentor['user_id']) && $mentor['user_id'] === ($user['id'] ?? 0)) $canUpdate = true;
    if (!$canUpdate && ($user['email'] ?? '') === ($mentor['email'] ?? '')) $canUpdate = true;
}
if (!$canUpdate) {
    setFlash('error', 'Access denied.');
    redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
}

$upload = uploadPhoto($_FILES['mentor_photo'] ?? [], 'mentors', $mentor['image_url'] ?? null);
if (!$upload['success']) {
    setFlash('error', $upload['error']);
    redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
}

if (!empty($upload['filename'])) {
    $pdo->prepare("UPDATE mentors SET image_url = ? WHERE id = ?")->execute([$upload['filename'], $mentorId]);
    setFlash('success', 'Mentor profile photo updated successfully.');
} else {
    setFlash('warning', 'No photo was selected.');
}

redirect(SITE_URL . '/mentors.php?id=' . $mentorId);
