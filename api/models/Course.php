<?php

namespace Models;

use Config\Database;
use PDO;

class Course {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Fetch all courses or filter by category ID.
     *
     * @param string|null $categoryId The ID of the category to filter courses (optional).
     * @return array An associative array of courses.
     */
    public function getCourses(?string $categoryId = null): array {
        $sql = "
            SELECT c.course_id, c.title, c.description, c.image_preview, cat.name AS main_category_name
            FROM courses c
            JOIN categories cat ON c.category_id = cat.id
        ";
        if ($categoryId !== null) {
            $sql .= " WHERE c.category_id = :category_id";
        }
        $stmt = $this->db->prepare($sql);
        if ($categoryId !== null) {
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_STR); 
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single course by ID.
     *
     * @param string $courseId The ID of the course.
     * @return array|false The course data or false if not found.
     */
    public function getCourseById(string $courseId): array|false {
        $stmt = $this->db->prepare("
            SELECT c.course_id, c.title, c.description, c.image_preview, cat.name AS main_category_name
            FROM courses c
            JOIN categories cat ON c.category_id = cat.id
            WHERE c.course_id = :course_id
        ");
        $stmt->bindParam(':course_id', $courseId, PDO::PARAM_STR); 
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}