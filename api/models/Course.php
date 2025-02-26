<?php

namespace Models;

use Config\Database;

class Course {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Fetch all courses or filter by category ID
    public function getCourses($categoryId = null) {
        $sql = "
            SELECT c.id, c.name, c.description, cat.name AS main_category_name
            FROM courses c
            JOIN categories cat ON c.category_id = cat.id
        ";
        if ($categoryId !== null) {
            $sql .= " WHERE c.category_id = :category_id";
        }
        $stmt = $this->db->prepare($sql);
        if ($categoryId !== null) {
            $stmt->bindParam(':category_id', $categoryId, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Fetch a single course by ID
    public function getCourseById($id) {
        $stmt = $this->db->prepare("
            SELECT c.id, c.name, c.description, cat.name AS main_category_name
            FROM courses c
            JOIN categories cat ON c.category_id = cat.id
            WHERE c.id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}