    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <div class="footer-logo">
                       
                        <span>Smart<strong>Girl</strong></span>
                    </div>
                    <p>Empowering girls through education, mentorship, and skill development. Building confident leaders for tomorrow's Cameroon.</p>
                    <div class="social-links">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/index.php">Home</a></li>
                        <li><a href="<?= SITE_URL ?>/landing.php">About Us</a></li>
                        <li><a href="<?= SITE_URL ?>/courses.php">Courses</a></li>
                        <li><a href="<?= SITE_URL ?>/mentors.php">Mentors</a></li>
                        <li><a href="<?= SITE_URL ?>/scholarships.php">Scholarships</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Community</h4>
                    <ul>
                        <li><a href="<?= SITE_URL ?>/events.php">Events</a></li>
                        <li><a href="<?= SITE_URL ?>/forum.php">Forum</a></li>
                        <li><a href="<?= SITE_URL ?>/register.php">Join Us</a></li>
                        <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
                        <li><a href="<?= SITE_URL ?>/dashboard.php">My Dashboard</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Contact Info</h4>
                    <ul class="contact-info">
                        <li><i class="fas fa-map-marker-alt"></i> <?= e(SITE_ADDRESS) ?></li>
                        <li><i class="fas fa-phone"></i> <?= e(SITE_PHONE) ?></li>
                        <li><i class="fas fa-envelope"></i> <?= e(SITE_EMAIL) ?></li>
                        <li><i class="fas fa-clock"></i> Mon - Fri: 8:00 AM - 5:00 PM</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= e(SITE_NAME) ?>. Designed & Developed by <strong>Joy Meshi</strong>. All Rights Reserved.</p>
                <p class="footer-tagline"><i class="fas fa-heart"></i> Empowering Girls, Transforming Communities</p>
            </div>
        </div>
    </footer>

    <script src="<?= SITE_URL ?>/assets/js/main.js"></script>
    <?php if (!empty($extraScripts)): ?>
        <?= $extraScripts ?>
    <?php endif; ?>
</body>
</html>
