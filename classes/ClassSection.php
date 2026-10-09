<?php 
class ClassSection {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function allWithCourse() {
        $sql = "SELECT c.*, co.course_name
                FROM classes c
                JOIN courses co ON c.course_id = co.course_id
                ORDER BY co.course_name, c.class_code";

        return $this->db->query($sql)->fetchAll();
    }

    public function create(
        $course_id,
        $code,
        $schedule,
        $instructor,
        $slots
    ) {
        $stmt = $this->db->prepare(
            "INSERT INTO classes
             (course_id, class_code, schedule, instructor, slots)
             VALUES (?, ?, ?, ?, ?)"
        );

        return $stmt->execute([
            $course_id,
            $code,
            $schedule,
            $instructor,
            $slots
        ]);
    }

    public function getSlots($class_id) {
        $stmt = $this->db->prepare(
            "SELECT slots FROM classes WHERE class_id = ?"
        );

        $stmt->execute([$class_id]);
        $row = $stmt->fetch();

        return $row ? (int) $row['slots'] : null;
    }
}
?>
