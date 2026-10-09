<?php


require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';
require_once __DIR__ . '/../classes/Student.php';
require_once __DIR__ . '/../classes/ClassSection.php';

$repo = new EnrollmentRepository($db);
$studentRepo = new Student($db);
$classRepo = new ClassSection($db);

$students = $studentRepo->all();
$classList = $classRepo->allWithCourse();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = filter_input(
        INPUT_POST, 'student_id', FILTER_VALIDATE_INT
    );
    $class_id = filter_input(
        INPUT_POST, 'class_id', FILTER_VALIDATE_INT
    );

    if (!$student_id || $student_id < 1 ||
        !$class_id || $class_id < 1) {
        $error = 'Please select a valid student and class.';
    } else {
        try {
            $repo->enroll($student_id, $class_id);
            $message = 'Student enrolled successfully.';
            $classList = $classRepo->allWithCourse();
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Enroll Existing Student</h1>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    <label>Student</label>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php foreach ($students as $student): ?>
            <option value="<?= (int)$student['student_id'] ?>">
                <?= htmlspecialchars(
                    $student['full_name']
                ) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Class</label>
    <select name="class_id" required>
        <option value="">-- Select Class --</option>
        <?php foreach ($classList as $class): ?>
            <option value="<?= (int)$class['class_id'] ?>">
                <?= htmlspecialchars(
                    $class['course_name'] . ' - ' .
                    $class['class_code'] . ' (' .
                    $class['slots'] . ' slots)'
                ) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <button type="submit">Enroll Student</button>
</form>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
?>