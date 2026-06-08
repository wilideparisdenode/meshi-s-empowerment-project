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

// Only admins or the mentor (matching email) can update the mentor photo
if (!isAdmin() && (currentUser()['email'] ?? '') !== ($mentor['email'] ?? '')) {
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
