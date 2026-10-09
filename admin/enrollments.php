<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = filter_input(
        INPUT_POST,
        'enrollment_id',
        FILTER_VALIDATE_INT
    );

    if (!$enrollment_id || $enrollment_id < 1) {
        $error = 'Invalid enrollment ID.';
    } else {
        try {
            $repo->cancel($enrollment_id);
            $message = 'Enrollment cancelled successfully.';
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}

$rows = $repo->allWithDetails();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-heading">
    <h1>Enrollment Report</h1>
    <p>View student enrollments and manage their current status.</p>
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

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Enrollment Records</span>
        <span class="badge text-bg-primary">
            <?= count($rows) ?> records
        </span>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Email</th>
                        <th>Course</th>
                        <th>Class</th>
                        <th>Schedule</th>
                        <th>Instructor</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="10">
                                <div class="empty-state">
                                    <p class="mb-1 fw-semibold">No enrollment records</p>
                                    <p class="mb-0">Student enrollments will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <td><?= (int) $row['enrollment_id'] ?></td>

                                <td>
                                    <span class="fw-semibold">
                                        <?= htmlspecialchars($row['full_name']) ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($row['email'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['course_name']) ?></td>
                                <td><?= htmlspecialchars($row['class_code']) ?></td>
                                <td><?= htmlspecialchars($row['schedule'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['instructor'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($row['enrollment_date']) ?></td>

                                <td>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <span class="badge text-bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge text-bg-secondary">
                                            <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if ($row['status'] === 'active'): ?>
                                        <form method="post"
                                              onsubmit="return confirm('Cancel this enrollment?');">
                                            <input type="hidden"
                                                   name="enrollment_id"
                                                   value="<?= (int) $row['enrollment_id'] ?>">

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted">No action</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
