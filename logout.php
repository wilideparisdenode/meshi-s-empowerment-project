<?php
require_once __DIR__ . '/config/init.php';
logoutUser();
session_start();
$_SESSION['flash'] = ['type' => 'success', 'message' => 'You have been logged out successfully.'];
redirect(SITE_URL . '/index.php');
