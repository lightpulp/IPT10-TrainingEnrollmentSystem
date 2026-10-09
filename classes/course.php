
<?php

class Course {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function all() {
        return $this->db->query(
            "SELECT * FROM courses ORDER BY course_name"
        )->fetchAll();
    }

    public function find($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM courses WHERE course_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($code, $name, $description) {
        $stmt = $this->db->prepare(
            "INSERT INTO courses
             (course_code, course_name, description)
             VALUES (?, ?, ?)"
        );

        return $stmt->execute([$code, $name, $description]);
    }

    public function update($id, $code, $name, $description) {
        $stmt = $this->db->prepare(
            "UPDATE courses
             SET course_code = ?, course_name = ?, description = ?
             WHERE course_id = ?"
        );

        return $stmt->execute([$code, $name, $description, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare(
            "DELETE FROM courses WHERE course_id = ?"
        );

        return $stmt->execute([$id]);
    }
}

?>