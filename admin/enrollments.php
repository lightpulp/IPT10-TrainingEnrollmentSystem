<?php


require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../classes/EnrollmentRepository.php';

$repo = new EnrollmentRepository($db);
$message = '';
$error = '';

// Process cancellation
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

// Fetch report after any cancellation
$rows = $repo->allWithDetails();

require_once __DIR__ . '/../includes/header.php';
?>

<h1>Enrollment Report</h1>

<?php if ($message): ?>
    <p><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<?php if ($error): ?>
    <p><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0">
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
                <td colspan="10">No enrollments found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= (int)$row['enrollment_id'] ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['class_code']) ?></td>
                    <td><?= htmlspecialchars($row['schedule'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['instructor'] ?? '') ?></td>
                    <td><?= htmlspecialchars($row['enrollment_date']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <?php if ($row['status'] === 'active'): ?>
                            <form method="post">
                                <input type="hidden"
                                       name="enrollment_id"
                                       value="<?= (int)$row['enrollment_id'] ?>">
                                <button type="submit"
                                    onclick="return confirm(
                                        'Cancel this enrollment?'
                                    )">
                                    Cancel
                                </button>
                            </form>
                        <?php else: ?>
                            Cancelled
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

?>