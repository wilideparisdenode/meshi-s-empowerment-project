<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/courses.php');
}

$courseId = (int) ($_POST['course_id'] ?? 0);
$pdo = getDBConnection();
$userId = currentUser()['id'];

$stmt = $pdo->prepare("SELECT id FROM courses WHERE id = ? AND is_active = 1");
$stmt->execute([$courseId]);
if (!$stmt->fetch()) {
    setFlash('error', 'Course not found.');
    redirect(SITE_URL . '/courses.php');
}

$stmt = $pdo->prepare("SELECT id FROM enrollments WHERE user_id = ? AND course_id = ?");
$stmt->execute([$userId, $courseId]);
if ($stmt->fetch()) {
    setFlash('warning', 'You have already enrolled successfully in this course.');
} else {
    $stmt = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt->execute([$userId, $courseId]);
    setFlash('success', 'You have successfully enrolled in the course!');
}

redirect(SITE_URL . '/courses.php?id=' . $courseId);
