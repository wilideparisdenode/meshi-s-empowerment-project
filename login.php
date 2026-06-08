<?php
$pageTitle = 'Login';
require_once __DIR__ . '/config/init.php';

if (isLoggedIn()) redirect(SITE_URL . '/dashboard.php');

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        $result = authenticateUser(getDBConnection(), $_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['success']) {
            setFlash('success', 'You have logged in successfully. Welcome back, ' . $result['user']['full_name'] . '!');
            if ($result['user']['role'] === 'admin') {
                redirect(SITE_URL . '/admin/index.php');
            }
            redirect(SITE_URL . '/dashboard.php');
        }
        $error = $result['error'];
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Welcome Back</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Login</span></div>
    </div>
</div>

<section class="auth-section">
    <div class="container">
        <div class="form-container">
            <div class="auth-header">
                <h2>Login to Your Account</h2>
                <p>Access your courses, mentors, and dashboard</p>
            </div>
            <div class="form-card">
                <?php if ($error): ?>
                <div class="flash flash-error" style="margin-bottom:1rem;border-radius:8px;padding:0.75rem;"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <?= csrfField() ?>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" required placeholder="your@email.com" value="<?= e($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="Enter your password">
                    </div>
                    <button type="submit" class="btn btn-blue btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
                <div class="form-footer">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
                <div style="margin-top:1.5rem;padding:1rem;background:var(--secondary);border-radius:8px;font-size:0.85rem;">
                    <strong>Demo Admin:</strong> admin@smartgirl.cm / Admin@123
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
