<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/dashboard.php?tab=profile');
}

$pdo = getDBConnection();
$userId = currentUser()['id'];
$stmt = $pdo->prepare("SELECT profile_image FROM users WHERE id = ?");
$stmt->execute([$userId]);
$row = $stmt->fetch();

$upload = uploadPhoto($_FILES['profile_photo'] ?? [], 'users', $row['profile_image'] ?? null);
if (!$upload['success']) {
    setFlash('error', $upload['error']);
    redirect(SITE_URL . '/dashboard.php?tab=profile');
}

if (!empty($upload['filename'])) {
    $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?")->execute([$upload['filename'], $userId]);
    refreshUserSession($pdo, $userId);
    setFlash('success', 'Your profile photo was uploaded successfully.');
} else {
    setFlash('warning', 'No photo was selected.');
}

redirect(SITE_URL . '/dashboard.php?tab=profile');
