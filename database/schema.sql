-- Smart Girl Empowerment Platform Database
-- Created for Joy Meshi - Final Year Project
-- Run this in phpMyAdmin or MySQL CLI

CREATE DATABASE IF NOT EXISTS smart_girl_empowerment
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE smart_girl_empowerment;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    location VARCHAR(100) DEFAULT NULL,
    role ENUM('user', 'admin', 'mentor') DEFAULT 'user',
    bio TEXT DEFAULT NULL,
    profile_image VARCHAR(255) DEFAULT 'default-avatar.png',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    category VARCHAR(100) NOT NULL,
    duration VARCHAR(50) NOT NULL,
    level ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    instructor VARCHAR(150) NOT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Course enrollments
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    enrolled_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'completed', 'dropped') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (user_id, course_id)
);

-- Mentors table
CREATE TABLE IF NOT EXISTS mentors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    full_name VARCHAR(150) NOT NULL,
    title VARCHAR(150) NOT NULL,
    expertise VARCHAR(255) NOT NULL,
    bio TEXT NOT NULL,
    email VARCHAR(150) NOT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    image_url VARCHAR(255) DEFAULT NULL,
    years_experience INT DEFAULT 0,
    is_available TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Mentor requests
CREATE TABLE IF NOT EXISTS mentor_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    mentor_id INT NOT NULL,
    message TEXT NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_id) REFERENCES mentors(id) ON DELETE CASCADE
);

-- Scholarships table
CREATE TABLE IF NOT EXISTS scholarships (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    provider VARCHAR(150) NOT NULL,
    amount VARCHAR(100) NOT NULL,
    deadline DATE NOT NULL,
    eligibility TEXT NOT NULL,
    application_link VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    event_date DATETIME NOT NULL,
    location VARCHAR(200) NOT NULL,
    event_type VARCHAR(100) NOT NULL,
    max_participants INT DEFAULT 100,
    image_url VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Event registrations
CREATE TABLE IF NOT EXISTS event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_reg (user_id, event_id)
);

-- Forum categories
CREATE TABLE IF NOT EXISTS forum_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    icon VARCHAR(50) DEFAULT 'fa-comments'
);

-- Forum topics
CREATE TABLE IF NOT EXISTS forum_topics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    views INT DEFAULT 0,
    is_pinned TINYINT(1) DEFAULT 0,
    is_locked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES forum_categories(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Forum replies
CREATE TABLE IF NOT EXISTS forum_replies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    topic_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (topic_id) REFERENCES forum_topics(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Contact messages
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Empowerment resources
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    resource_type ENUM('article', 'video', 'guide', 'toolkit') DEFAULT 'article',
    file_url VARCHAR(255) DEFAULT NULL,
    external_link VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin user (password: Admin@123)
INSERT INTO users (full_name, email, password, role, location) VALUES
('Platform Administrator', 'admin@smartgirl.cm', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Bamenda, Cameroon');

-- Sample courses
INSERT INTO courses (title, description, category, duration, level, instructor) VALUES
('Digital Literacy for Girls', 'Learn essential computer skills, internet safety, and digital communication tools to thrive in the modern world.', 'Technology', '6 weeks', 'Beginner', 'Dr. Sarah Ngu'),
('Entrepreneurship Fundamentals', 'Discover how to start and grow your own business with practical strategies for young women entrepreneurs.', 'Business', '8 weeks', 'Intermediate', 'Grace Fon'),
('Leadership & Confidence Building', 'Develop leadership skills, public speaking, and self-confidence to become a change-maker in your community.', 'Personal Development', '4 weeks', 'Beginner', 'Marie Tchinda'),
('Web Development Basics', 'Introduction to HTML, CSS, and JavaScript for girls interested in technology careers.', 'Technology', '10 weeks', 'Beginner', 'Pauline Akwa'),
('Financial Literacy for Young Women', 'Learn budgeting, saving, investing, and managing personal finances effectively.', 'Finance', '5 weeks', 'Beginner', 'Esther Mbaku'),
('STEM Career Pathways', 'Explore science, technology, engineering, and mathematics careers available to girls in Cameroon and beyond.', 'STEM', '6 weeks', 'Intermediate', 'Dr. Faith Nje');

-- Sample mentors
INSERT INTO mentors (full_name, title, expertise, bio, email, phone, years_experience) VALUES
('Dr. Sarah Ngu', 'Technology Educator', 'Digital Skills, STEM Education', 'Dr. Sarah has over 15 years of experience empowering girls through technology education across Cameroon. She holds a PhD in Computer Science and has trained over 2,000 young women.', 'sarah.ngu@smartgirl.cm', '+237 677 123 456', 15),
('Grace Fon', 'Social Entrepreneur', 'Business Development, Entrepreneurship', 'Grace founded two successful social enterprises focused on women empowerment. She mentors young girls on starting sustainable businesses.', 'grace.fon@smartgirl.cm', '+237 678 234 567', 12),
('Marie Tchinda', 'Leadership Coach', 'Personal Development, Public Speaking', 'Marie is a certified leadership coach who has helped hundreds of girls build confidence and take on leadership roles in their schools and communities.', 'marie.tchinda@smartgirl.cm', '+237 679 345 678', 10),
('Pauline Akwa', 'Software Engineer', 'Web Development, Programming', 'Pauline works at a leading tech company and volunteers her time mentoring girls interested in coding and software development careers.', 'pauline.akwa@smartgirl.cm', '+237 680 456 789', 8),
('Esther Mbaku', 'Financial Advisor', 'Financial Literacy, Investment', 'Esther specializes in helping young women understand personal finance, savings, and investment strategies for long-term wealth building.', 'esther.mbaku@smartgirl.cm', '+237 681 567 890', 11);

-- Sample scholarships
INSERT INTO scholarships (title, description, provider, amount, deadline, eligibility) VALUES
('Girls in STEM Scholarship 2026', 'Full tuition scholarship for girls pursuing degrees in Science, Technology, Engineering, or Mathematics at accredited universities in Cameroon.', 'Ministry of Higher Education', 'Up to 2,000,000 FCFA/year', '2026-08-31', 'Female students aged 17-25, enrolled in STEM programs, minimum GPA 3.0'),
('Young Women Entrepreneurs Grant', 'Seed funding for young women with innovative business ideas that address community challenges.', 'African Development Foundation', '500,000 - 1,500,000 FCFA', '2026-07-15', 'Girls aged 18-30 with a viable business plan and community impact focus'),
('Digital Skills Training Bursary', 'Covers full cost of digital literacy and web development courses on this platform plus certification.', 'Smart Girl Empowerment Platform', 'Full course coverage', '2026-12-31', 'Registered platform members from rural areas with demonstrated financial need'),
('Mastercard Foundation Scholars Program', 'Comprehensive scholarship including tuition, accommodation, books, and mentorship for outstanding young women.', 'Mastercard Foundation', 'Full scholarship package', '2026-06-30', 'Outstanding academic record, leadership potential, commitment to community service'),
('Cameroon Girls Education Fund', 'Supports girls completing secondary education and transitioning to higher education.', 'UNICEF Cameroon', '300,000 FCFA/year', '2026-09-30', 'Girls in final year of secondary school, family income below poverty line');

-- Sample events
INSERT INTO events (title, description, event_date, location, event_type, max_participants) VALUES
('Girls Tech Summit 2026', 'Annual technology summit bringing together girls, mentors, and industry leaders for workshops, networking, and career inspiration.', '2026-06-15 09:00:00', 'University of Bamenda, Main Hall', 'Conference', 200),
('Leadership Workshop: Finding Your Voice', 'Interactive workshop on public speaking, confidence building, and leadership skills for young women.', '2026-05-20 14:00:00', 'Smart Girl Hub, Commercial Avenue, Bamenda', 'Workshop', 50),
('Entrepreneurship Bootcamp', 'Three-day intensive bootcamp covering business planning, marketing, and funding strategies for young women entrepreneurs.', '2026-07-01 08:00:00', 'Bamenda Conference Center', 'Bootcamp', 75),
('Mentorship Connect Day', 'Meet and connect with professional mentors from various fields. One-on-one mentoring sessions available.', '2026-05-25 10:00:00', 'Online via Zoom', 'Networking', 100),
('STEM Career Fair', 'Explore career opportunities in science and technology with representatives from leading companies and universities.', '2026-08-10 09:00:00', 'Yaoundé International Conference Center', 'Career Fair', 300),
('Girls Coding Hackathon', '24-hour coding challenge for teams of girls to build solutions for community problems.', '2026-09-05 08:00:00', 'Tech Hub Bamenda', 'Hackathon', 60);

-- Forum categories
INSERT INTO forum_categories (name, description, icon) VALUES
('Career Guidance', 'Discuss career paths, job opportunities, and professional development', 'fa-briefcase'),
('Education & Scholarships', 'Share scholarship opportunities and educational resources', 'fa-graduation-cap'),
('Entrepreneurship', 'Business ideas, startup tips, and entrepreneurial journeys', 'fa-lightbulb'),
('Personal Development', 'Self-confidence, leadership, and life skills discussions', 'fa-heart'),
('Technology & STEM', 'Coding, tech careers, and STEM-related topics', 'fa-laptop-code'),
('General Discussion', 'Open discussions about empowerment and community', 'fa-comments');

-- Sample forum topics
INSERT INTO forum_topics (category_id, user_id, title, content, views) VALUES
(1, 1, 'How to choose a career in technology as a girl?', 'I am in Form 5 and very interested in technology. What steps should I take to prepare for a tech career? Any advice from mentors would be appreciated!', 45),
(2, 1, 'New scholarship opportunities for 2026', 'I found several new scholarships for girls in Cameroon. Let us share information and help each other apply successfully.', 78),
(4, 1, 'Building confidence in public speaking', 'Many girls struggle with public speaking. What techniques have helped you overcome fear and speak confidently?', 32);

-- Sample resources
INSERT INTO resources (title, description, resource_type, external_link) VALUES
('Career Planning Guide for Young Women', 'A comprehensive guide to exploring careers, setting goals, and creating action plans.', 'guide', '#'),
('Building Self-Confidence Toolkit', 'Practical exercises and strategies to boost self-esteem and confidence.', 'toolkit', '#'),
('Understanding Your Rights as a Girl', 'Educational material on girls rights, education access, and legal protections in Cameroon.', 'article', '#'),
('Interview Skills Masterclass', 'Video series covering resume writing, interview preparation, and professional presentation.', 'video', '#'),
('Leadership Skills Workbook', 'Interactive workbook for developing leadership capabilities in school and community.', 'guide', '#');
