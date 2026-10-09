
<?php
// index.php
require_once __DIR__ . '/includes/header.php';
?>

<h1>Training Enrollment System</h1>

<p>
    Welcome to the Training Enrollment Management System.
    This system allows the administrator to manage training
    courses, class schedules, student registrations, and
    enrollments.
</p>

<h2>Administrator Menu</h2>

<ul>
    <li><a href="admin/courses.php">Manage Courses</a></li>
    <li><a href="admin/classes.php">Manage Classes</a></li>
    <li><a href="admin/students.php">Record a New Student</a></li>
    <li><a href="admin/enroll.php">Enroll an Existing Student</a></li>
    <li><a href="admin/enrollments.php">View Enrollment Reports</a></li>
</ul>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
?>