<?php
// admin/students.php

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$classes = new ClassSection($db);
$classList = $classes->allWithCourse();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $class_id = filter_input(
        INPUT_POST,
        'class_id',
        FILTER_VALIDATE_INT
    );

    if ($full_name === '') {
        $error = 'Full name is required.';
    } elseif ($email !== '' &&
              !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$class_id || $class_id < 1) {
        $error = 'Please select a valid class.';
    } else {
        try {
            $repo->recordStudent(
                $full_name,
                $email !== '' ? $email : null,
                $phone !== '' ? $phone : null,
                $class_id
            );

            $message = 'Student recorded and enrolled successfully.';
            $classList = $classes->allWithCourse();

        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Record New Student</h1>

<?php if ($message !== ''): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($error !== ''): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Full Name</label>
    <input type="text" name="full_name" required
           value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">

    <label>Email</label>
    <input type="email" name="email"
           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

    <label>Phone</label>
    <input type="text" name="phone"
           value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">

    <label>Class</label>
    <select name="class_id" required>
        <option value="">-- Select Class --</option>
        <?php foreach ($classList as $class): ?>
            <option value="<?= (int) $class['class_id'] ?>"
                <?= (string)($_POST['class_id'] ?? '') ===
                    (string)$class['class_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars(
                    $class['course_name'] . ' - ' .
                    $class['class_code'] . ' (' .
                    $class['slots'] . ' slots)'
                ) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Record Student</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
?>