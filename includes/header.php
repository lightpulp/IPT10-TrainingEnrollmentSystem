<?php
$baseUrl = '/training-enrollment-system';

$currentPage = basename($_SERVER['PHP_SELF']);

$navigation = [
    ['label' => 'Home', 'file' => 'index.php', 'url' => '/index.php', 'icon' => 'bi-house-door'],
    ['label' => 'Courses', 'file' => 'courses.php', 'url' => '/admin/courses.php', 'icon' => 'bi-journal-bookmark'],
    ['label' => 'Classes', 'file' => 'classes.php', 'url' => '/admin/classes.php', 'icon' => 'bi-calendar3'],
    ['label' => 'Record Student', 'file' => 'students.php', 'url' => '/admin/students.php', 'icon' => 'bi-person-plus'],
    ['label' => 'Enroll', 'file' => 'enroll.php', 'url' => '/admin/enroll.php', 'icon' => 'bi-clipboard-check'],
    ['label' => 'Enrollments', 'file' => 'enrollments.php', 'url' => '/admin/enrollments.php', 'icon' => 'bi-list-check'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Training Enrollment System</title>

    <!-- Bootstrap CSS -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Bootstrap Icons -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
        rel="stylesheet"
    >

    <!-- Shared project stylesheet -->
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl) ?>/style.css">
</head>
<body>

<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-dark site-navbar">
        <div class="container-fluid px-0">

            <a class="navbar-brand brand"
               href="<?= htmlspecialchars($baseUrl) ?>/index.php">
                <img
                    src="<?= htmlspecialchars($baseUrl) ?>/auf.png"
                    alt="AUF Logo"
                    class="brand-logo"
                >
                <span>Training Enrollment System</span>
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavigation"
                aria-controls="mainNavigation"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavigation">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <?php foreach ($navigation as $item): ?>
                        <li class="nav-item">
                            <a
                                class="nav-link <?= $currentPage === $item['file'] ? 'active' : '' ?>"
                                href="<?= htmlspecialchars($baseUrl . $item['url']) ?>"
                                <?= $currentPage === $item['file'] ? 'aria-current="page"' : '' ?>
                            >
                                <i class="bi <?= htmlspecialchars($item['icon']) ?> me-1"></i>
                                <?= htmlspecialchars($item['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

        </div>
    </nav>
</header>

<main class="container-fluid page-container">
