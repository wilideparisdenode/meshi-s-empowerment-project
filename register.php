<?php

$pageTitle = 'Register';

require_once __DIR__ . '/config/init.php';



if (isLoggedIn()) redirect(SITE_URL . '/dashboard.php');



$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $errors[] = 'Invalid request. Please try again.';

    } else {

        $pdo = getDBConnection();

        $result = registerUser($pdo, $_POST);

        if ($result['success']) {

            $userId = $result['user_id'];

            $upload = uploadPhoto($_FILES['profile_photo'] ?? [], 'users');

            if (!$upload['success']) {

                $errors[] = $upload['error'];

            } elseif (!empty($upload['filename'])) {

                $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?")->execute([$upload['filename'], $userId]);

            }

            if (empty($errors)) {

                authenticateUser($pdo, $_POST['email'], $_POST['password']);

                setFlash('success', 'Your account was registered successfully. Welcome!');

                redirect(SITE_URL . '/dashboard.php');

            }

        } else {

            $errors = $result['errors'];

        }

    }

}



require_once __DIR__ . '/includes/header.php';

?>



<div class="page-header">

    <div class="container">

        <h1>Create Your Account</h1>

        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Register</span></div>

    </div>

</div>



<section class="auth-section">

    <div class="container">

        <div class="form-container">

            <div class="auth-header">

                <h2>Join Smart Girl Platform</h2>

                <p>Start your empowerment journey — it's free!</p>

            </div>

            <div class="form-card">

                <?php if ($errors): ?>

                <div class="flash flash-error" style="margin-bottom:1rem;border-radius:8px;padding:0.75rem;">

                    <ul style="margin:0;padding-left:1.25rem;">

                        <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>

                    </ul>

                </div>

                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" data-validate>

                    <?= csrfField() ?>

                    <div class="form-group">

                        <label for="profile_photo">Profile Photo</label>

                        <input type="file" id="profile_photo" name="profile_photo" accept="image/jpeg,image/png,image/webp">

                        <p class="photo-upload-hint">Optional — JPG, PNG or WEBP (max 2MB)</p>

                    </div>

                    <div class="form-group">

                        <label for="full_name">Full Name *</label>

                        <input type="text" id="full_name" name="full_name" required placeholder="Enter your full name" value="<?= e($_POST['full_name'] ?? '') ?>">

                    </div>

                    <div class="form-group">

                        <label for="email">Email Address *</label>

                        <input type="email" id="email" name="email" required placeholder="your@email.com" value="<?= e($_POST['email'] ?? '') ?>">

                    </div>

                    <div class="form-row">

                        <div class="form-group">

                            <label for="phone">Phone Number</label>

                            <input type="tel" id="phone" name="phone" placeholder="+237 6XX XXX XXX" value="<?= e($_POST['phone'] ?? '') ?>">

                        </div>

                        <div class="form-group">

                            <label for="location">Location</label>

                            <input type="text" id="location" name="location" placeholder="e.g. Bamenda" value="<?= e($_POST['location'] ?? '') ?>">

                        </div>

                    </div>

                    <div class="form-group">

                        <label for="password">Password *</label>

                        <input type="password" id="password" name="password" required minlength="6" placeholder="Minimum 6 characters">

                    </div>

                    <div class="form-group">

                        <label for="confirm_password">Confirm Password *</label>

                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat your password">

                    </div>

                    <button type="submit" class="btn btn-blue btn-block"><i class="fas fa-user-plus"></i> Register Successfully</button>

                </form>

                <div class="form-footer">

                    Already have an account? <a href="login.php">Login here</a>

                </div>

            </div>

        </div>

    </div>

</section>



<?php require_once __DIR__ . '/includes/footer.php'; ?>


