<?php

class EnrollmentRepository {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    // Record a new student and enroll them in a class.
    public function recordStudent(
        $full_name,
        $email,
        $phone,
        $class_id
    ) {
        try {
            $this->db->beginTransaction();

            // Lock the class and check available slots.
            $stmt = $this->db->prepare(
                "SELECT slots FROM classes
                 WHERE class_id = ? FOR UPDATE"
            );
            $stmt->execute([$class_id]);
            $class = $stmt->fetch();

            if (!$class || (int) $class['slots'] <= 0) {
                throw new RuntimeException(
                    "No slots available or class not found."
                );
            }

            // Insert the student.
            $stmt = $this->db->prepare(
                "INSERT INTO students (full_name, email, phone)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$full_name, $email, $phone]);

            $student_id = $this->db->lastInsertId();

            // Insert the enrollment.
            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id)
                 VALUES (?, ?)"
            );
            $stmt->execute([$student_id, $class_id]);

            // Decrement available slots.
            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1
                 WHERE class_id = ? AND slots > 0"
            );
            $stmt->execute([$class_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    "Unable to reserve a slot."
                );
            }

            $this->db->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // Enroll an existing student.
    public function enroll($student_id, $class_id) {
        try {
            $this->db->beginTransaction();

            // Lock the class before checking capacity.
            $stmt = $this->db->prepare(
                "SELECT slots FROM classes
                 WHERE class_id = ? FOR UPDATE"
            );
            $stmt->execute([$class_id]);
            $class = $stmt->fetch();

            if (!$class || (int) $class['slots'] <= 0) {
                throw new RuntimeException(
                    "No slots available or class not found."
                );
            }

            // Verify that the student exists.
            $stmt = $this->db->prepare(
                "SELECT student_id FROM students
                 WHERE student_id = ?"
            );
            $stmt->execute([$student_id]);

            if (!$stmt->fetch()) {
                throw new RuntimeException("Student not found.");
            }

            // Insert enrollment; duplicate records are rejected
            // if the database has a UNIQUE(student_id, class_id) key.
            $stmt = $this->db->prepare(
                "INSERT INTO enrollments (student_id, class_id)
                 VALUES (?, ?)"
            );
            $stmt->execute([$student_id, $class_id]);

            // Decrement slots.
            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots - 1
                 WHERE class_id = ? AND slots > 0"
            );
            $stmt->execute([$class_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    "Unable to reserve a slot."
                );
            }

            $this->db->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // Cancel an active enrollment and restore its slot.
    public function cancel($enrollment_id) {
        try {
            $this->db->beginTransaction();

            // Lock the enrollment to prevent double cancellation.
            $stmt = $this->db->prepare(
                "SELECT class_id, status FROM enrollments
                 WHERE enrollment_id = ? FOR UPDATE"
            );
            $stmt->execute([$enrollment_id]);
            $enrollment = $stmt->fetch();

            if (
                !$enrollment ||
                $enrollment['status'] !== 'active'
            ) {
                throw new RuntimeException(
                    "Enrollment not found or already cancelled."
                );
            }

            // Cancel only an active enrollment.
            $stmt = $this->db->prepare(
                "UPDATE enrollments SET status = 'cancelled'
                 WHERE enrollment_id = ? AND status = 'active'"
            );
            $stmt->execute([$enrollment_id]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    "Unable to cancel enrollment."
                );
            }

            // Restore one available slot.
            $stmt = $this->db->prepare(
                "UPDATE classes SET slots = slots + 1
                 WHERE class_id = ?"
            );
            $stmt->execute([$enrollment['class_id']]);

            if ($stmt->rowCount() !== 1) {
                throw new RuntimeException(
                    "Unable to restore the class slot."
                );
            }

            $this->db->commit();
            return true;

        } catch (Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }

    // Retrieve enrollment details for reports.
    public function allWithDetails() {
        $sql = "SELECT
                    e.enrollment_id,
                    e.student_id,
                    e.class_id,
                    e.enrollment_date,
                    e.status,
                    s.full_name,
                    s.email,
                    s.phone,
                    c.class_code,
                    c.schedule,
                    c.instructor,
                    co.course_name,
                    co.course_code
                FROM enrollments e
                JOIN students s
                    ON e.student_id = s.student_id
                JOIN classes c
                    ON e.class_id = c.class_id
                JOIN courses co
                    ON c.course_id = co.course_id
                ORDER BY e.enrollment_date DESC";

        return $this->db->query($sql)->fetchAll();
    }
}
?>