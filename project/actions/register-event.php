<?php
require_once __DIR__ . '/../config/init.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/events.php');
}

$eventId = (int) ($_POST['event_id'] ?? 0);
$pdo = getDBConnection();
$userId = currentUser()['id'];

$stmt = $pdo->prepare("SELECT * FROM events WHERE id = ? AND is_active = 1 AND event_date >= NOW()");
$stmt->execute([$eventId]);
$event = $stmt->fetch();
if (!$event) {
    setFlash('error', 'Event not found or has already passed.');
    redirect(SITE_URL . '/events.php');
}

$count = $pdo->prepare("SELECT COUNT(*) FROM event_registrations WHERE event_id = ?");
$count->execute([$eventId]);
if ($count->fetchColumn() >= $event['max_participants']) {
    setFlash('error', 'This event is full.');
    redirect(SITE_URL . '/events.php');
}

$stmt = $pdo->prepare("SELECT id FROM event_registrations WHERE user_id = ? AND event_id = ?");
$stmt->execute([$userId, $eventId]);
if ($stmt->fetch()) {
    setFlash('warning', 'You have already registered successfully for this event.');
} else {
    $stmt = $pdo->prepare("INSERT INTO event_registrations (user_id, event_id) VALUES (?, ?)");
    $stmt->execute([$userId, $eventId]);
    setFlash('success', 'You have successfully registered for the event!');
}

redirect(SITE_URL . '/events.php');
