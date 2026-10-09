<?php
// index.php
require_once __DIR__ . '/includes/header.php';
?>

<div class="welcome-panel">
    <div class="row align-items-center g-4">
        <div class="col-lg-8">
            <p class="text-uppercase fw-semibold mb-2">
                Administrator Dashboard
            </p>

            <h1>Training Enrollment System</h1>

            <p class="mb-4">
                Welcome to the Training Enrollment Management System.
                Manage training courses, class schedules, student
                registrations, and enrollments in one place.
            </p>

            <a href="admin/students.php" class="btn btn-primary">
                Record New Student
            </a>

            <a href="admin/enrollments.php"
               class="btn btn-outline-primary ms-2">
                View Enrollments
            </a>
        </div>

        <div class="col-lg-4 text-lg-end">
            <div class="bg-white rounded-4 p-4 d-inline-block text-start">
                <div class="text-muted small mb-1">
                    SYSTEM OVERVIEW
                </div>
                <h4 class="mb-2">Training Administration</h4>
                <p class="mb-0 text-muted">
                    Courses, classes, students, and enrollment records.
                </p>
            </div>
        </div>
    </div>
</div>

<div class="page-heading">
    <h2>Administrator Menu</h2>
    <p>Select a section to manage your training enrollment system.</p>
</div>

<div class="row g-4">

    <div class="col-sm-6 col-lg-4">
        <div class="card quick-card">
            <div class="card-body">
                <div class="rounded-3 p-3 d-inline-block"
                     style="background: var(--accent-light); color: var(--accent);">
                    <span class="fs-3 fw-bold">01</span>
                </div>

                <h3>Manage Courses</h3>

                <p>
                    Create new courses, update course details,
                    and manage your course list.
                </p>

                <a href="admin/courses.php"
                   class="btn btn-outline-primary">
                    Open Courses
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card quick-card">
            <div class="card-body">
                <div class="rounded-3 p-3 d-inline-block"
                     style="background: var(--accent-light); color: var(--accent);">
                    <span class="fs-3 fw-bold">02</span>
                </div>

                <h3>Manage Classes</h3>

                <p>
                    Set up class sections, assign instructors,
                    and maintain class schedules and available slots.
                </p>

                <a href="admin/classes.php"
                   class="btn btn-outline-primary">
                    Open Classes
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card quick-card">
            <div class="card-body">
                <div class="rounded-3 p-3 d-inline-block"
                     style="background: var(--accent-light); color: var(--accent);">
                    <span class="fs-3 fw-bold">03</span>
                </div>

                <h3>Record New Student</h3>

                <p>
                    Register a new student and assign them
                    to an available class section.
                </p>

                <a href="admin/students.php"
                   class="btn btn-outline-primary">
                    Register Student
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card quick-card">
            <div class="card-body">
                <div class="rounded-3 p-3 d-inline-block"
                     style="background: var(--accent-light); color: var(--accent);">
                    <span class="fs-3 fw-bold">04</span>
                </div>

                <h3>Enroll Existing Student</h3>

                <p>
                    Select a registered student and enroll
                    them in a class section.
                </p>

                <a href="admin/enroll.php"
                   class="btn btn-outline-primary">
                    Enroll Student
                </a>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-lg-4">
        <div class="card quick-card">
            <div class="card-body">
                <div class="rounded-3 p-3 d-inline-block"
                     style="background: var(--accent-light); color: var(--accent);">
                    <span class="fs-3 fw-bold">05</span>
                </div>

                <h3>Enrollment Reports</h3>

                <p>
                    Review enrollment records, check student
                    statuses, and cancel active enrollments.
                </p>

                <a href="admin/enrollments.php"
                   class="btn btn-outline-primary">
                    View Reports
                </a>
            </div>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
