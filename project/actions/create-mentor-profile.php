<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/dashboard.php');
}

$token = $_POST['csrf_token'] ?? null;
if (!verifyCsrf($token)) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/dashboard.php');
}

$user = currentUser();
if (!$user) {
    setFlash('error', 'Not logged in.');
    redirect(SITE_URL . '/login.php');
}

// Only allow if user has mentor role or is admin
if (!isset($user['role']) || ($user['role'] !== 'mentor' && !isAdmin())) {
    setFlash('error', 'Only mentor accounts may create a mentor profile.');
    redirect(SITE_URL . '/dashboard.php');
}

$full_name = trim($_POST['full_name'] ?? $user['full_name']);
$title = trim($_POST['title'] ?? '');
$expertise = trim($_POST['expertise'] ?? '');
$bio = trim($_POST['bio'] ?? '');
$email = $user['email'];

// Ensure no existing mentor row for this user
$stmt = $pdo->prepare("SELECT id FROM mentors WHERE user_id = ? OR email = ? LIMIT 1");
$stmt->execute([$user['id'], $email]);
if ($stmt->fetch()) {
    setFlash('warning', 'A mentor profile already exists for your account.');
    redirect(SITE_URL . '/dashboard.php');
}

$ins = $pdo->prepare("INSERT INTO mentors (user_id, full_name, title, expertise, bio, email) VALUES (?, ?, ?, ?, ?, ?)");
$ok = $ins->execute([$user['id'], $full_name, $title, $expertise, $bio, $email]);
if ($ok) {
    setFlash('success', 'Mentor profile created. You can now receive requests.');
} else {
    setFlash('error', 'Failed to create mentor profile.');
}
redirect(SITE_URL . '/dashboard.php');
