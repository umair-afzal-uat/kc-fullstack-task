<?php

namespace Models;

use Config\Database;

class Category {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    // Fetch all categories with course counts (including subcategories)
    public function getAllCategories() {
        $sql = "
            SELECT c.id, c.name, c.parent_id, 
                   (SELECT COUNT(*) FROM courses WHERE category_id = c.id) AS count_of_courses
            FROM categories c
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Fetch a single category by ID
    public function getCategoryById($id) {
        $stmt = $this->db->prepare("
            SELECT id, name, parent_id, 
                   (SELECT COUNT(*) FROM courses WHERE category_id = :id) AS count_of_courses
            FROM categories
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}