# Smart Girl Empowerment Platform

**Project Title:** Designing and Development of a Smart Girl Empowerment Platform  
**Developer:** Joy Meshi — Final Year University Student  
**Location:** Bamenda, Cameroon

A complete web-based platform empowering girls through education, mentorship, scholarships, events, and community support.

---

## Features

| Feature | Description |
|---------|-------------|
| **Home & Landing** | Professional introduction, mission, objectives |
| **User Registration/Login** | Secure authentication with password hashing |
| **Courses** | Browse, filter, view details, enroll in courses |
| **Mentors** | View profiles, request mentorship |
| **Scholarships** | Browse opportunities with deadlines |
| **Events** | Register for workshops and summits |
| **Forum** | Community discussions with categories |
| **Contact** | Contact form + Google Maps (Bamenda) |
| **User Dashboard** | Courses, events, mentorship, resources |
| **Admin Panel** | Full CRUD for all content + moderation |

---

## Requirements

- XAMPP (Apache + MySQL + PHP 7.4+)
- Web browser (Chrome, Firefox, Edge)

---

## Installation

1. **Copy project** to `C:\xampp\htdocs\project`

2. **Start XAMPP** — Open XAMPP Control Panel → Start **Apache** AND **MySQL** (both must be green)

3. **Run diagnostics** (if anything fails):
   ```
   http://localhost/project/check.php
   ```

4. **Install database**:
   ```
   http://localhost/project/install.php
   ```
   Click **Install Database Now**

5. **Open the site:**
   ```
   http://localhost/project/
   ```

### Troubleshooting

| Problem | Solution |
|---------|----------|
| Blank page / nothing loads | Start Apache in XAMPP |
| "Database Connection Error" | Start MySQL in XAMPP, then run install.php |
| Opened file by double-clicking | Use browser URL `http://localhost/project/` NOT file:// |
| Port 80 in use | Use `http://localhost:8080/project/` if Apache uses port 8080 |
| MySQL password set | Copy `config/local.example.php` to `config/local.php` and set `DB_PASS` |

---

## Default Login Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@smartgirl.cm | Admin@123 |

Register new user accounts via the Register page.

---

## User Stories Implemented

### Girl/User Stories
- ✅ User Registration & Login
- ✅ Browse & Enroll in Courses
- ✅ Access Mentorship & View Profiles
- ✅ View Scholarship Opportunities
- ✅ Register for Events
- ✅ Participate in Community Forum
- ✅ Contact Administrators
- ✅ Career Guidance Resources (Dashboard)

### Admin Stories
- ✅ Manage Courses (Add/Edit/Delete)
- ✅ Manage Mentors & Mentorship Requests
- ✅ Manage Events & Scholarships
- ✅ Moderate Forum (Pin/Lock/Delete)
- ✅ Manage Registered Users
- ✅ View Contact Messages

### System Stories
- ✅ Secure Authentication (bcrypt, CSRF, sessions)
- ✅ Responsive Design (Mobile, Tablet, Desktop)

---

## Project Structure

```
project/
├── index.php          # Home page
├── landing.php        # About page
├── register.php       # User registration
├── login.php          # User login
├── courses.php        # Courses listing
├── mentors.php        # Mentors listing
├── scholarships.php   # Scholarships
├── events.php         # Events & registration
├── forum.php          # Community forum
├── contact.php        # Contact + Bamenda map
├── dashboard.php      # User dashboard
├── admin/             # Admin panel
├── actions/           # Form handlers
├── config/            # Database config
├── includes/          # Header, footer, auth
├── assets/css/        # Stylesheet
├── assets/js/         # JavaScript
└── database/schema.sql
```

---

## Technology Stack

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP 7.4+
- **Database:** MySQL
- **Server:** Apache (XAMPP)
- **Security:** password_hash(), PDO prepared statements, CSRF tokens

---

## For Project Defense

1. Demonstrate user registration and login
2. Enroll in a course from the Courses page
3. Request mentorship from a mentor profile
4. Register for an upcoming event
5. Post a topic in the Forum
6. Submit the Contact form
7. Login as admin and manage content
8. Show responsive design on mobile view

---

© 2026 Joy Meshi — Smart Girl Empowerment Platform
