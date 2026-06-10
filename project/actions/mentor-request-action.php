<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();
$pdo = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(SITE_URL . '/mentor-dashboard.php');
}

$token = $_POST['csrf_token'] ?? null;
if (!verifyCsrf($token)) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/mentor-dashboard.php');
}

$requestId = (int) ($_POST['request_id'] ?? 0);
$status = $_POST['status'] ?? '';
if (!$requestId || !in_array($status, ['accepted', 'declined'])) {
    setFlash('error', 'Invalid parameters.');
    redirect(SITE_URL . '/mentor-dashboard.php');
}

// Ensure current user is allowed to update this request: admin or the mentor owner
$stmt = $pdo->prepare("SELECT mr.*, m.user_id as mentor_user_id, m.email as mentor_email FROM mentor_requests mr JOIN mentors m ON mr.mentor_id = m.id WHERE mr.id = ?");
$stmt->execute([$requestId]);
$req = $stmt->fetch();
if (!$req) {
    setFlash('error', 'Request not found.');
    redirect(SITE_URL . '/mentor-dashboard.php');
}

$user = currentUser();
$authorized = false;
if (isAdmin()) {
    $authorized = true;
} else {
    if (!empty($req['mentor_user_id']) && $req['mentor_user_id'] === $user['id']) $authorized = true;
    if (!$authorized && isset($user['email']) && $user['email'] === $req['mentor_email']) $authorized = true;
}

if (!$authorized) {
    setFlash('error', 'Access denied.');
    redirect(SITE_URL . '/mentor-dashboard.php');
}

$update = $pdo->prepare("UPDATE mentor_requests SET status = ? WHERE id = ?");
$update->execute([$status, $requestId]);
setFlash('success', 'Mentorship request was updated.');
redirect(SITE_URL . '/mentor-dashboard.php');
