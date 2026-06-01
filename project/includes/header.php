<?php
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/init.php';
}
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = currentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Girl Empowerment Platform - Empowering girls through education, mentorship, and skill development in Cameroon.">
    <meta name="author" content="Joy Meshi">
    <title><?= e($pageTitle ?? 'Home') ?> | <?= e(SITE_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/style.css">
</head>
<body>
    <!-- Top bar -->
    <div class="top-bar">
        <div class="container top-bar-inner">
            <div class="top-bar-left">
                <span><i class="fas fa-envelope"></i> <?= e(SITE_EMAIL) ?></span>
                <span><i class="fas fa-phone"></i> <?= e(SITE_PHONE) ?></span>
            </div>
            <div class="top-bar-right">
                <span><i class="fas fa-map-marker-alt"></i> Bamenda, Cameroon</span>
                <?php if ($user): ?>
                    <a href="<?= SITE_URL ?>/dashboard.php"><i class="fas fa-user"></i> <?= e($user['full_name']) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container nav-container">
            <a href="<?= SITE_URL ?>/index.php" class="logo">
                <i class="fas fa-star"></i>
                <span>Smart<strong>Girl</strong></span>
            </a>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu">
                <span></span><span></span><span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="<?= SITE_URL ?>/index.php" class="<?= $currentPage === 'index' ? 'active' : '' ?>">Home</a></li>
                <li><a href="<?= SITE_URL ?>/landing.php" class="<?= $currentPage === 'landing' ? 'active' : '' ?>">About</a></li>
                <li><a href="<?= SITE_URL ?>/courses.php" class="<?= $currentPage === 'courses' ? 'active' : '' ?>">Courses</a></li>
                <li><a href="<?= SITE_URL ?>/mentors.php" class="<?= $currentPage === 'mentors' ? 'active' : '' ?>">Mentors</a></li>
                <li><a href="<?= SITE_URL ?>/scholarships.php" class="<?= $currentPage === 'scholarships' ? 'active' : '' ?>">Scholarships</a></li>
                <li><a href="<?= SITE_URL ?>/events.php" class="<?= $currentPage === 'events' ? 'active' : '' ?>">Events</a></li>
                <li><a href="<?= SITE_URL ?>/forum.php" class="<?= $currentPage === 'forum' ? 'active' : '' ?>">Forum</a></li>
                <li><a href="<?= SITE_URL ?>/contact.php" class="<?= $currentPage === 'contact' ? 'active' : '' ?>">Contact</a></li>
                <?php if ($user): ?>
                    <?php if (isAdmin()): ?>
                        <li><a href="<?= SITE_URL ?>/admin/index.php" class="nav-admin"><i class="fas fa-cog"></i> Admin</a></li>
                    <?php endif; ?>
                    <li><a href="<?= SITE_URL ?>/dashboard.php" class="nav-btn-outline">Dashboard</a></li>
                    <li><a href="<?= SITE_URL ?>/logout.php" class="nav-btn">Logout</a></li>
                <?php else: ?>
                    <li><a href="<?= SITE_URL ?>/login.php" class="nav-btn-outline">Login</a></li>
                    <li><a href="<?= SITE_URL ?>/register.php" class="nav-btn">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <?php if ($flash): ?>
    <div class="flash flash-<?= e($flash['type']) ?>">
        <div class="container">
            <i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : ($flash['type'] === 'error' ? 'exclamation-circle' : 'info-circle') ?>"></i>
            <?= e($flash['message']) ?>
            <button class="flash-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    </div>
    <?php endif; ?>

    <main>
