<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid request.';
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($name)) $errors[] = 'Name is required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
        if (empty($subject)) $errors[] = 'Subject is required.';
        if (empty($message)) $errors[] = 'Message is required.';

        if (empty($errors)) {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success = true;
            setFlash('success', 'Your message was sent successfully. We will respond within 24-48 hours.');
        }
    }
}

$defaultSubject = $_GET['subject'] ?? '';

require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header">
    <div class="container">
        <h1>Contact Us</h1>
        <div class="breadcrumb"><a href="index.php">Home</a> <span>/</span> <span>Contact</span></div>
    </div>
</div>

<section class="section">
    <div class="container">
        <div class="contact-grid">
            <div>
                <div class="contact-info-card">
                    <h2 style="margin-bottom:1.5rem;font-size:1.5rem;">Get In Touch</h2>
                    <p style="opacity:0.9;margin-bottom:2rem;">We are here to support your empowerment journey. Reach out for mentorship requests, technical support, partnership inquiries, or general information.</p>

                    <div class="contact-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <strong>Office Address</strong><br>
                            <?= e(SITE_ADDRESS) ?><br>
                            <small style="opacity:0.85;">Near Commercial Avenue, City Chemist Roundabout</small>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-phone"></i>
                        <div>
                            <strong>Phone Numbers</strong><br>
                            <?= e(SITE_PHONE) ?><br>
                            <small>+237 678 111 222 (WhatsApp)</small>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-envelope"></i>
                        <div>
                            <strong>Email Addresses</strong><br>
                            <?= e(SITE_EMAIL) ?><br>
                            <small>support@smartgirl.cm | mentorship@smartgirl.cm</small>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <i class="fas fa-clock"></i>
                        <div>
                            <strong>Office Hours</strong><br>
                            Monday - Friday: 8:00 AM - 5:00 PM<br>
                            Saturday: 9:00 AM - 1:00 PM<br>
                            <small>Sunday & Public Holidays: Closed</small>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <?php if ($errors): ?>
                <div class="flash flash-error" style="margin-bottom:1rem;border-radius:8px;padding:0.75rem;">
                    <ul style="margin:0;padding-left:1.25rem;"><?php foreach ($errors as $e): ?><li><?= e($e) ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>

                <div class="form-card">
                    <h3 style="margin-bottom:1.5rem;color:var(--gray-900);">Send Us a Message</h3>
                    <form method="POST">
                        <?= csrfField() ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Full Name *</label>
                                <input type="text" name="name" required value="<?= e($_POST['name'] ?? (currentUser()['full_name'] ?? '')) ?>">
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required value="<?= e($_POST['email'] ?? (currentUser()['email'] ?? '')) ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Subject *</label>
                            <input type="text" name="subject" required value="<?= e($_POST['subject'] ?? $defaultSubject) ?>" placeholder="e.g. Mentorship Request, Scholarship Inquiry">
                        </div>
                        <div class="form-group">
                            <label>Message *</label>
                            <textarea name="message" required placeholder="How can we help you?"><?= e($_POST['message'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-blue btn-block"><i class="fas fa-paper-plane"></i> Send Message</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="map-container">
            <h3 style="margin-bottom:1rem;color:var(--gray-900);text-align:center;"><i class="fas fa-map-marker-alt" style="color:var(--primary);"></i> Find Us in Bamenda, Cameroon</h3>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127584.2519487!2d10.1211!3d5.9631!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x105f34ff98c8f2c7%3A0x6b8e8e8e8e8e8e8e!2sCommercial%20Avenue%2C%20Bamenda%2C%20Cameroon!5e0!3m2!1sen!2s!4v1700000000000!5m2!1sen!2s"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                title="Smart Girl Empowerment Platform - Bamenda, Cameroon">
            </iframe>
            <p style="text-align:center;margin-top:1rem;color:var(--gray-500);font-size:0.9rem;">
                <i class="fas fa-info-circle"></i>
                Located in Bamenda, Northwest Region — Commercial Avenue area. Use Google Maps for directions.
            </p>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
