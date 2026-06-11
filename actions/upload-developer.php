<?php
require_once __DIR__ . '/../config/init.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrf($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect(SITE_URL . '/admin/developer.php');
}

ensureUploadDir('profiles');
$upload = uploadPhoto($_FILES['developer_photo'] ?? [], 'profiles');

if (!$upload['success']) {
    setFlash('error', $upload['error']);
    redirect(SITE_URL . '/admin/developer.php');
}

if (!empty($upload['filename'])) {
    $dir = ensureUploadDir('profiles');
    $ext = pathinfo($upload['filename'], PATHINFO_EXTENSION);
    $target = $dir . '/developer.' . $ext;
    foreach (glob($dir . '/developer.*') as $old) {
        @unlink($old);
    }
    rename($dir . '/' . $upload['filename'], $target);
    setFlash('success', 'Developer profile photo was uploaded successfully.');
} else {
    setFlash('warning', 'No photo was selected.');
}

redirect(SITE_URL . '/admin/developer.php');
