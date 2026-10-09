<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/Course.php';

$courseRepo = new Course($db);

$message = '';
$error = '';
$editCourse = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create' || $action === 'update') {
            $code = trim($_POST['course_code'] ?? '');
            $name = trim($_POST['course_name'] ?? '');
            $description = trim($_POST['description'] ?? '');

            if ($code === '' || $name === '') {
                throw new RuntimeException('Course code and course name are required.');
            }

            if ($action === 'create') {
                $courseRepo->create(
                    $code,
                    $name,
                    $description !== '' ? $description : null
                );

                $message = 'Course created successfully.';
            } else {
                $id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);

                if (!$id || $id < 1) {
                    throw new RuntimeException('Invalid course ID.');
                }

                $courseRepo->update(
                    $id,
                    $code,
                    $name,
                    $description !== '' ? $description : null
                );

                $message = 'Course updated successfully.';
            }
        } elseif ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);

            if (!$id || $id < 1) {
                throw new RuntimeException('Invalid course ID.');
            }

            $courseRepo->delete($id);
            $message = 'Course deleted successfully.';
        }
    } catch (Throwable $e) {
        if ($e instanceof PDOException && $e->getCode() === '23000') {
            $error = 'Unable to save or delete this course. The course code may already exist, or the course may be used by existing classes.';
        } else {
            $error = $e->getMessage();
        }
    }
}

if (isset($_GET['edit'])) {
    $id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

    if ($id && $id > 0) {
        $editCourse = $courseRepo->find($id);
    }
}

$courses = $courseRepo->all();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-heading">
    <h1>Course Management</h1>
    <p>Add, update, and manage the courses offered by your institution.</p>
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
            <div class="card-header">
                <?= $editCourse ? 'Edit Course' : 'Add New Course' ?>
            </div>

            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="action"
                           value="<?= $editCourse ? 'update' : 'create' ?>">

                    <?php if ($editCourse): ?>
                        <input type="hidden" name="course_id"
                               value="<?= (int) $editCourse['course_id'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label for="course_code" class="form-label">Course Code</label>
                        <input type="text" id="course_code" name="course_code"
                               class="form-control" maxlength="20" required
                               placeholder="e.g. BSIT"
                               value="<?= htmlspecialchars($editCourse['course_code'] ?? '') ?>">
                    </div>

                    <div class="mb-3">
                        <label for="course_name" class="form-label">Course Name</label>
                        <input type="text" id="course_name" name="course_name"
                               class="form-control" maxlength="100" required
                               placeholder="e.g. Bachelor of Science in Information Technology"
                               value="<?= htmlspecialchars($editCourse['course_name'] ?? '') ?>">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea id="description" name="description"
                                  class="form-control" rows="4"
                                  placeholder="Brief course description"><?= htmlspecialchars($editCourse['description'] ?? '') ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <?= $editCourse ? 'Save Changes' : 'Add Course' ?>
                    </button>

                    <?php if ($editCourse): ?>
                        <a href="courses.php"
                           class="btn btn-outline-primary w-100 mt-2">
                            Cancel Edit
                        </a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Course List</span>
                <span class="badge text-bg-primary">
                    <?= count($courses) ?> courses
                </span>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($courses)): ?>
                                <tr>
                                    <td colspan="5">
                                        <div class="empty-state">No courses found.</div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($courses as $course): ?>
                                    <tr>
                                        <td><?= (int) $course['course_id'] ?></td>
                                        <td>
                                            <span class="fw-semibold">
                                                <?= htmlspecialchars($course['course_code']) ?>
                                            </span>
                                        </td>
                                        <td><?= htmlspecialchars($course['course_name']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($course['description'] ?? '—') ?>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1">
                                                <a href="courses.php?edit=<?= (int) $course['course_id'] ?>"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Edit
                                                </a>

                                                <form method="post"
                                                      onsubmit="return confirm('Delete this course?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="course_id"
                                                           value="<?= (int) $course['course_id'] ?>">
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
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
