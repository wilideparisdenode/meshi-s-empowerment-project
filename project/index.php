<?php
$pageTitle = 'Home';
require_once __DIR__ . '/config/init.php';
$pdo = getDBConnection();
$stats = getStats($pdo);

$stmt = $pdo->query("SELECT * FROM courses WHERE is_active = 1 ORDER BY created_at DESC LIMIT 3");
$featuredCourses = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM events WHERE is_active = 1 AND event_date >= NOW() ORDER BY event_date ASC LIMIT 3");
$upcomingEvents = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<section class="hero hero-with-bg">
    <div class="hero-overlay"></div>
    <div class="container hero-container">
        <div class="hero-content">
            <h1>Designing a <span>Smarter Future</span> for Every Girl</h1>
            <p>Welcome to the Smart Girl Empowerment Platform — your gateway to education, mentorship, scholarships, and skill development. Built to help girls in Bamenda and beyond achieve their dreams.</p>
            
            <div class="hero-buttons">
                <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Get Started Free</a>
                <a href="landing.php" class="btn btn-outline"><i class="fas fa-play-circle"></i> Learn More</a>
            </div>
            
            <div class="hero-stats">
                <div class="hero-stat"><strong><?= $stats['users'] + 500 ?>+</strong><span>Girls Empowered</span></div>
                <div class="hero-stat"><strong><?= $stats['courses'] ?>+</strong><span>Online Courses</span></div>
                <div class="hero-stat"><strong><?= $stats['mentors'] ?>+</strong><span>Expert Mentors</span></div>
                <div class="hero-stat"><strong><?= $stats['scholarships'] ?>+</strong><span>Scholarships</span></div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="label">What We Offer</p>
            <h2>Everything You Need to Succeed</h2>
            <p>A complete empowerment ecosystem combining learning, mentorship, and community support in one accessible platform.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Online Courses</h3>
                <p>Access digital literacy, entrepreneurship, leadership, and STEM courses designed for girls.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-hands-helping"></i></div>
                <h3>Mentorship Program</h3>
                <p>Connect with experienced mentors for career guidance and professional development.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-award"></i></div>
                <h3>Scholarships</h3>
                <p>Discover financial aid and educational support opportunities across Cameroon.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-calendar-alt"></i></div>
                <h3>Events & Workshops</h3>
                <p>Participate in summits, bootcamps, and networking sessions in Bamenda and online.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-comments"></i></div>
                <h3>Community Forum</h3>
                <p>Share experiences, ideas, and opportunities with girls across the platform.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-book-open"></i></div>
                <h3>Career Resources</h3>
                <p>Access guides, toolkits, and materials to build confidence and leadership skills.</p>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <p class="label">Featured Courses</p>
            <h2>Start Learning Today</h2>
            <p>Enroll in our most popular training programs and develop skills for your future career.</p>
        </div>
        <div class="card-grid">
            <?php foreach ($featuredCourses as $course): ?>
            <div class="card">
                <div class="card-image"><i class="fas fa-book"></i></div>
                <div class="card-body">
                    <div class="card-meta">
                        <span><?= e($course['category']) ?></span>
                        <span><?= e($course['level']) ?></span>
                        <span><?= e($course['duration']) ?></span>
                    </div>
                    <h3><?= e($course['title']) ?></h3>
                    <p><?= e(truncate($course['description'], 100)) ?></p>
                </div>
                <div class="card-footer">
                    <a href="courses.php?id=<?= $course['id'] ?>" class="btn btn-blue btn-sm btn-block">View Course</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div style="text-align:center;margin-top:2rem;">
            <a href="courses.php" class="btn btn-blue">View All Courses</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <p class="label">Upcoming Events</p>
            <h2>Join Our Next Event</h2>
            <p>Connect, learn, and grow with other empowered girls at our workshops and summits.</p>
        </div>
        <div class="card-grid">
            <?php foreach ($upcomingEvents as $event): ?>
            <div class="card">
                <div class="event-date">
                    <span class="day"><?= date('d', strtotime($event['event_date'])) ?></span>
                    <span class="month"><?= date('M Y', strtotime($event['event_date'])) ?></span>
                </div>
                <div class="card-body">
                    <div class="card-meta"><span><?= e($event['event_type']) ?></span></div>
                    <h3><?= e($event['title']) ?></h3>
                    <p><i class="fas fa-map-marker-alt"></i> <?= e($event['location']) ?></p>
                </div>
                <div class="card-footer">
                    <a href="events.php" class="btn btn-blue btn-sm btn-block">Learn More</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <p class="label">Success Stories</p>
            <h2>What Our Girls Say</h2>
        </div>
        <div class="card-grid">
            <div class="testimonial-card">
                <p>"This platform changed my life. I enrolled in the web development course and now I am building websites for local businesses in Bamenda."</p>
                <div class="testimonial-author">
                    <div class="avatar">AN</div>
                    <div><strong>Amanda Nje</strong><br><small>Web Developer, Bamenda</small></div>
                </div>
            </div>
            <div class="testimonial-card">
                <p>"Through the mentorship program, I received guidance that helped me secure a scholarship to study Computer Science at the University of Bamenda."</p>
                <div class="testimonial-author">
                    <div class="avatar">FT</div>
                    <div><strong>Faith Tchinda</strong><br><small>CS Student, UBa</small></div>
                </div>
            </div>
            <div class="testimonial-card">
                <p>"The entrepreneurship bootcamp gave me the confidence to start my own tailoring business. I now employ three other young women."</p>
                <div class="testimonial-author">
                    <div class="avatar">MK</div>
                    <div><strong>Mary Kom</strong><br><small>Entrepreneur, Bamenda</small></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <h2>Ready to Start Your Empowerment Journey?</h2>
        <p>Join thousands of girls across Cameroon who are building brighter futures through education and mentorship.</p>
        <a href="register.php" class="btn btn-primary"><i class="fas fa-rocket"></i> Create Free Account</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
