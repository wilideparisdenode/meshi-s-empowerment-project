<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
<h1>User Dashboard</h1>
</header>

<section>

<h2>
Welcome
<?php echo $_SESSION['email']; ?>
</h2>

<ul>

<li>
<a href="courses.html">
View Courses
</a>
</li>

<li>
<a href="mentors.html">
View Mentors
</a>
</li>

<li>
<a href="events.html">
View Events
</a>
</li>

<li>
<a href="scholarships.html">
Scholarships
</a>
</li>

<li>
<a href="forum.php">
Discussion Forum
</a>
</li>

<li>
<a href="logout.php">
Logout
</a>
</li>

</ul>

</section>

</body>
</html>