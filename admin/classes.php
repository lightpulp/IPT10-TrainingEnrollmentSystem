<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/ClassSection.php';
require_once __DIR__ . '/../classes/Course.php';

$classRepo = new ClassSection($db);
$courseRepo = new Course($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    $code = trim($_POST['class_code'] ?? '');
    $schedule = trim($_POST['schedule'] ?? '');
    $instructor = trim($_POST['instructor'] ?? '');
    $slots = filter_input(INPUT_POST, 'slots', FILTER_VALIDATE_INT);

    try {
        if (!$course_id || $course_id < 1) {
            throw new RuntimeException('Please select a valid course.');
        }

        if ($code === '') {
            throw new RuntimeException('Class code is required.');
        }

        if ($slots === false || $slots === null || $slots < 0) {
            throw new RuntimeException('Slots must be a non-negative whole number.');
        }

        if (!$courseRepo->find($course_id)) {
            throw new RuntimeException('Course not found.');
        }

        $classRepo->create(
            $course_id,
            $code,
            $schedule !== '' ? $schedule : null,
            $instructor !== '' ? $instructor : null,
            $slots
        );

        $message = 'Class section created successfully.';
        $_POST = [];
    } catch (Throwable $e) {
        if ($e instanceof PDOException && $e->getCode() === '23000') {
            $error = 'Unable to create the class. Check whether the class code already exists for this course.';
        } else {
            $error = $e->getMessage();
        }
    }
}

$courses = $courseRepo->all();
$classList = $classRepo->allWithCourse();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-heading">
    <h1>Class Management</h1>
    <p>Create class sections and manage class schedules.</p>
</div>

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

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">Add New Class Section</div>
            <div class="card-body">

                <?php if (empty($courses)): ?>
                    <div class="empty-state">
                        <p>No courses are available yet.</p>
                        <a href="courses.php" class="btn btn-primary">
                            Create a Course
                        </a>
                    </div>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select name="course_id" id="course_id"
                                    class="form-select" required>
                                <option value="">Select a course</option>
                                <?php foreach ($courses as $course): ?>
                                    <option
                                        value="<?= (int) $course['course_id'] ?>"
                                        <?= (string) ($_POST['course_id'] ?? '') ===
                                            (string) $course['course_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars(
                                            $course['course_code'] . ' - ' .
                                            $course['course_name']
                                        ) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="class_code" class="form-label">Class Code</label>
                            <input type="text" id="class_code" name="class_code"
                                   class="form-control" maxlength="20" required
                                   placeholder="e.g. BSIT-1A"
                                   value="<?= htmlspecialchars($_POST['class_code'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="schedule" class="form-label">Schedule</label>
                            <input type="text" id="schedule" name="schedule"
                                   class="form-control" maxlength="100"
                                   placeholder="e.g. Monday, 9 AM - 12 PM"
                                   value="<?= htmlspecialchars($_POST['schedule'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="instructor" class="form-label">Instructor</label>
                            <input type="text" id="instructor" name="instructor"
                                   class="form-control" maxlength="100"
                                   placeholder="Instructor name"
                                   value="<?= htmlspecialchars($_POST['instructor'] ?? '') ?>">
                        </div>

                        <div class="mb-4">
                            <label for="slots" class="form-label">Available Slots</label>
                            <input type="number" id="slots" name="slots"
                                   class="form-control" min="0" required
                                   value="<?= htmlspecialchars($_POST['slots'] ?? '20') ?>">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Create Class
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Class Schedule List</span>
                <span class="badge text-bg-primary">
                    <?= count($classList) ?> classes
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course</th>
                                <th>Class Code</th>
                                <th>Schedule</th>
                                <th>Instructor</th>
                                <th>Slots</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($classList)): ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">No class sections found.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($classList as $class): ?>
                                    <tr>
                                        <td><?= (int) $class['class_id'] ?></td>
                                        <td><?= htmlspecialchars($class['course_name']) ?></td>
                                        <td>
                                            <span class="fw-semibold">
                                                <?= htmlspecialchars($class['class_code']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($class['schedule'] ?? 'Not set') ?></td>
                                        <td><?= htmlspecialchars($class['instructor'] ?? 'Not assigned') ?></td>
                                        <td>
                                            <span class="badge text-bg-light">
                                                <?= (int) $class['slots'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>