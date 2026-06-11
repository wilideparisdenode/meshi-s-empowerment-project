<?php
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../../config/init.php';
}
requireAdmin();
$adminPage = basename($_SERVER['PHP_SELF'], '.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> | Admin Panel</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
    <style>
        .admin-layout { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
        .admin-sidebar { background: var(--primary-dark); color: white; padding: 1.5rem 0; }
        .admin-sidebar .logo { color: white; padding: 0 1.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 1rem; }
        .admin-nav a { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1.5rem; color: rgba(255,255,255,0.8); }
        .admin-nav a:hover, .admin-nav a.active { background: rgba(255,255,255,0.1); color: white; }
        .admin-main { background: var(--gray-50); padding: 2rem; }
        .admin-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        @media (max-width: 768px) { .admin-layout { grid-template-columns: 1fr; } .admin-sidebar { display: none; } }
    </style>
</head>
<body>
<?php if ($flash): ?>
<div class="flash flash-<?= e($flash['type']) ?>"><div class="container"><?= e($flash['message']) ?></div></div>
<?php endif; ?>
<div class="admin-layout">
    <aside class="admin-sidebar">
        <div class="logo"><i class="fas fa-star"></i> Smart<strong>Girl</strong> Admin</div>
        <nav class="admin-nav">
            <a href="index.php" class="<?= $adminPage === 'index' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="courses.php" class="<?= $adminPage === 'courses' ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> Courses</a>
            <a href="mentors.php" class="<?= $adminPage === 'mentors' ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Mentors</a>
            <a href="events.php" class="<?= $adminPage === 'events' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Events</a>
            <a href="scholarships.php" class="<?= $adminPage === 'scholarships' ? 'active' : '' ?>"><i class="fas fa-award"></i> Scholarships</a>
            <a href="forum.php" class="<?= $adminPage === 'forum' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Forum</a>
            <a href="users.php" class="<?= $adminPage === 'users' ? 'active' : '' ?>"><i class="fas fa-users"></i> Users</a>
            <a href="messages.php" class="<?= $adminPage === 'messages' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Messages</a>
            <a href="developer.php" class="<?= $adminPage === 'developer' ? 'active' : '' ?>"><i class="fas fa-camera"></i> Developer Photo</a>
            <a href="<?= SITE_URL ?>/index.php" style="margin-top:1rem;border-top:1px solid rgba(255,255,255,0.1);padding-top:1rem;"><i class="fas fa-globe"></i> View Site</a>
            <a href="<?= SITE_URL ?>/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>
    <main class="admin-main">
