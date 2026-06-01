<?php
$pageTitle = 'Developer Photo';
require_once __DIR__ . '/../config/init.php';
requireAdmin();
$devPhoto = developerPhotoUrl();
require_once __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-top">
    <h1 style="color:var(--gray-900);">Developer Profile Photo</h1>
</div>

<div class="dashboard-content" style="max-width:500px;">
    <p style="margin-bottom:1.5rem;color:var(--gray-500);">Upload Joy Meshi's photo for the About page "Meet the Creator" section.</p>
    <?php if ($devPhoto): ?>
    <img src="<?= e($devPhoto) ?>" alt="Developer" class="profile-upload-preview" style="width:120px;height:120px;">
    <?php endif; ?>
    <form method="POST" action="<?= SITE_URL ?>/actions/upload-developer.php" enctype="multipart/form-data">
        <?= csrfField() ?>
        <div class="form-group">
            <label>Profile Photo (JPG, PNG, WEBP — max 2MB)</label>
            <input type="file" name="developer_photo" accept="image/jpeg,image/png,image/webp" required>
            <p class="photo-upload-hint">This photo appears on the About page.</p>
        </div>
        <button type="submit" class="btn btn-blue">Upload Photo</button>
    </form>
</div>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
