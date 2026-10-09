<?php
// classes/Student.php

class Student {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function find($id) {
        $stmt = $this->db->prepare(
            "SELECT * FROM students WHERE student_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function all() {
        $stmt = $this->db->query(
            "SELECT * FROM students ORDER BY full_name"
        );
        return $stmt->fetchAll();
    }
}