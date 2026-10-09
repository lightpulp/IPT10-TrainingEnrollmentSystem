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
    $student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
    $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);

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

<div class="page-heading">
    <h1>Enroll Existing Student</h1>
    <p>Assign a registered student to an available class section.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">

        <?php if ($message !== ''): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header">Enrollment Information</div>

            <div class="card-body">
                <?php if (empty($students)): ?>
                    <div class="alert alert-info">
                        No students have been registered yet.
                    </div>
                <?php elseif (empty($classList)): ?>
                    <div class="alert alert-info">
                        No class sections are available.
                    </div>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-4">
                            <label for="student_id" class="form-label">
                                Select Student
                            </label>

                            <select name="student_id" id="student_id"
                                    class="form-select" required>
                                <option value="">Choose a student</option>

                                <?php foreach ($students as $student): ?>
                                    <option value="<?= (int) $student['student_id'] ?>"
                                        <?= (string) ($_POST['student_id'] ?? '') ===
                                            (string) $student['student_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($student['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="class_id" class="form-label">
                                Select Class
                            </label>

                            <select name="class_id" id="class_id"
                                    class="form-select" required>
                                <option value="">Choose a class</option>

                                <?php foreach ($classList as $class): ?>
                                    <option value="<?= (int) $class['class_id'] ?>"
                                        <?= (string) ($_POST['class_id'] ?? '') ===
                                            (string) $class['class_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(
                                            $class['course_name'] . ' - ' .
                                            $class['class_code'] . ' (' .
                                            $class['slots'] . ' slots)'
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <div class="form-text">
                                Check class availability before enrolling.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Enroll Student
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
