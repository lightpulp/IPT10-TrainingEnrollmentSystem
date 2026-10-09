<?php
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
            $_POST = [];
            $classList = $classes->allWithCourse();
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-heading">
    <h1>Record New Student</h1>
    <p>Register a student and assign them to a class section.</p>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-7">

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
            <div class="card-header">Student Information</div>

            <div class="card-body">
                <?php if (empty($classList)): ?>
                    <div class="alert alert-info">
                        No class sections are available. Please create a course and class section first.
                        <a href="courses.php" class="alert-link">Manage courses</a>.
                    </div>
                <?php else: ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" id="full_name" name="full_name"
                                   class="form-control" required
                                   placeholder="Enter student's full name"
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email"
                                       class="form-control"
                                       placeholder="student@example.com"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="text" id="phone" name="phone"
                                       class="form-control"
                                       placeholder="Enter phone number"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="class_id" class="form-label">Class Section</label>

                            <select name="class_id" id="class_id"
                                    class="form-select" required>
                                <option value="">Select a class</option>

                                <?php foreach ($classList as $class): ?>
                                    <option
                                        value="<?= (int) $class['class_id'] ?>"
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
                                Select the class where the student will be enrolled.
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            Record and Enroll Student
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
