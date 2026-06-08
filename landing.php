<?php

$pageTitle = 'About Us';

require_once __DIR__ . '/config/init.php';

require_once __DIR__ . '/includes/header.php';

$devPhoto = developerPhotoUrl();

?>



<section class="welcome-banner">

    <div class="container">

        <h1>Welcome to Smart Girl Empowerment Platform</h1>

        <p>Empowering girls and young women to learn, lead, innovate, and succeed.</p>

    </div>

</section>



<div class="page-header">

    <div class="container">

        <h1>About Smart Girl Empowerment Platform</h1>

        <div class="breadcrumb">

            <a href="index.php">Home</a> <span>/</span> <span>About</span>

        </div>

    </div>

</div>



<section class="section">

    <div class="container">

        <div class="mission-vision-grid">

            <div class="mission-box">

                <i class="fas fa-bullseye"></i>

                <h3>Our Mission</h3>

                <p>To empower girls through technology by providing accessible learning, mentorship, scholarships, and community support — helping every girl build confidence, skills, and opportunities to achieve her goals in Bamenda and beyond.</p>

            </div>

            <div class="vision-box">

                <i class="fas fa-star"></i>

                <h3>Our Vision</h3>

                <p>A Cameroon where every girl has the confidence, skills, and opportunities to make decisions about her life and achieve her goals.</p>

            </div>

        </div>

        <div style="margin-top:2.5rem;color:var(--gray-500);line-height:1.8;">

            <p style="margin-bottom:1rem;">The Smart Girl Empowerment Platform is designed and developed by <strong> Awa Joy Meshi</strong> to address the lack of accessible empowerment resources for girls in developing countries, particularly in Cameroon.</p>

            <p>Our platform brings together learning materials, mentorship connections, scholarship opportunities, events, and community support in one simple, user-friendly digital space accessible from any smartphone or computer.</p>

        </div>

    </div>

</section>



<section class="section section-alt">

    <div class="container">

        <div class="section-header">

            <p class="label">The Problem We Solve</p>

            <h2>Why This Platform Matters</h2>

        </div>

        <div class="features-grid">

            <div class="feature-card">

                <div class="feature-icon"><i class="fas fa-exclamation-triangle"></i></div>

                <h3>Fragmented Resources</h3>

                <p>Most platforms focus only on education OR mentorship. We combine everything in one place.</p>

            </div>

            <div class="feature-card">

                <div class="feature-icon"><i class="fas fa-map-marked-alt"></i></div>

                <h3>Rural Accessibility</h3>

                <p>Programs in cities are unreachable for rural girls. Our digital platform is accessible anywhere with internet.</p>

            </div>

            <div class="feature-card">

                <div class="feature-icon"><i class="fas fa-mobile-alt"></i></div>

                <h3>Mobile-First Design</h3>

                <p>Designed for smartphones — the primary device for girls in Cameroon accessing the internet.</p>

            </div>

            <div class="feature-card">

                <div class="feature-icon"><i class="fas fa-shield-alt"></i></div>

                <h3>Safe Community</h3>

                <p>Moderated forum and verified mentors ensure a professional, safe environment for girls.</p>

            </div>

        </div>

    </div>

</section>



<section class="section">

    <div class="container">

        <div class="section-header">

            <p class="label">Project Objectives</p>

            <h2>Research & Development Goals</h2>

        </div>

        <div class="card-grid" style="grid-template-columns:repeat(auto-fill,minmax(280px,1fr));">

            <div class="card"><div class="card-body"><h3><i class="fas fa-search" style="color:var(--primary);"></i> Study Existing Platforms</h3><p>Identify weaknesses in current empowerment platforms and design improvements.</p></div></div>

            <div class="card"><div class="card-body"><h3><i class="fas fa-paint-brush" style="color:var(--primary);"></i> User-Friendly Design</h3><p>Create a simple, intuitive interface suitable for girls of all technical levels.</p></div></div>

            <div class="card"><div class="card-body"><h3><i class="fas fa-code" style="color:var(--primary);"></i> Core Features</h3><p>Develop registration, courses, mentorship, scholarships, events, and forum systems.</p></div></div>

            <div class="card"><div class="card-body"><h3><i class="fas fa-vial" style="color:var(--primary);"></i> Testing & Evaluation</h3><p>Test system functionality and evaluate how well it meets user needs.</p></div></div>

        </div>

    </div>

</section>



<section class="section section-alt">

    <div class="container" style="text-align:center;">

        <div class="section-header">

            <p class="label">Project Developer</p>

            <h2>Meet the Creator</h2>

        </div>

        <div style="max-width:500px;margin:0 auto;background:white;padding:2.5rem;border-radius:var(--radius);box-shadow:var(--shadow-lg);">

            <?php if ($devPhoto): ?>

                <img src="<?= e($devPhoto) ?>" alt="Joy Meshi" class="developer-photo">

            <?php else: ?>

                <div class="user-avatar" style="width:100px;height:100px;font-size:2.5rem;margin:0 auto 1rem;">JM</div>

            <?php endif; ?>

            <h3 style="color:var(--gray-900);margin-bottom:0.5rem;">Awa Joy Meshi</h3>

            <p style="color:var(--primary);font-weight:500;margin-bottom:1rem;">Software Engineering</p>

            <p style="color:var(--gray-500);">The Founder of: <em>Smart Girl Empowerment Platform</em></p>

            <p style="color:var(--gray-500);margin-top:1rem;">University of Bamenda, Cameroon</p>

            <?php if (isAdmin()): ?>

            <p style="margin-top:1rem;"><a href="admin/developer.php" class="btn btn-blue btn-sm">Upload Developer Photo</a></p>

            <?php endif; ?>

        </div>

    </div>

</section>



<section class="cta-section">

    <div class="container">

        <h2>Be Part of the Change</h2>

        <p>Whether you are a girl seeking empowerment, a mentor wanting to give back, or a supporter of girls education — join us today.</p>

        <a href="register.php" class="btn btn-primary">Join the Platform</a>

    </div>

</section>



<?php require_once __DIR__ . '/includes/footer.php'; ?>


