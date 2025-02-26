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
            SELECT c.id, c.name, c.parent_id, c.depth,
                   (SELECT COUNT(*) 
                    FROM courses 
                    WHERE category_id IN (
                        SELECT id FROM categories WHERE id = c.id OR parent_id = id
                    )
                   ) AS count_of_courses
            FROM categories c
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Fetch a single category by ID
    public function getCategoryById($id) {
        $stmt = $this->db->prepare("
            SELECT id, name, parent_id, depth,
                   (SELECT COUNT(*) 
                    FROM courses 
                    WHERE category_id IN (
                        SELECT id FROM categories WHERE id = :id OR parent_id = :id
                    )
                   ) AS count_of_courses
            FROM categories
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Insert a new category
    public function insertCategory($name, $parentId = null) {
        // Validate maximum depth
        if ($parentId !== null) {
            $parentDepth = $this->getCategoryDepth($parentId);
            if ($parentDepth === false) {
                throw new \Exception("Parent category does not exist.");
            }
            if ($parentDepth >= 4) {
                throw new \Exception("Maximum depth of 4 exceeded.");
            }
        }

        // Insert the new category
        $stmt = $this->db->prepare("
            INSERT INTO categories (id, name, parent_id, depth)
            VALUES (:id, :name, :parent_id, :depth)
        ");
        $id = uniqid(); // Generate a unique ID
        $depth = $parentId ? $this->getCategoryDepth($parentId) + 1 : 0;
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':parent_id', $parentId);
        $stmt->bindParam(':depth', $depth);
        $stmt->execute();

        return $id;
    }

    // Update an existing category
    public function updateCategory($id, $parentId = null) {
        // Validate maximum depth
        if ($parentId !== null) {
            $parentDepth = $this->getCategoryDepth($parentId);
            if ($parentDepth === false) {
                throw new \Exception("Parent category does not exist.");
            }
            if ($parentDepth >= 4) {
                throw new \Exception("Maximum depth of 4 exceeded.");
            }
        }

        // Update the category
        $stmt = $this->db->prepare("
            UPDATE categories
            SET parent_id = :parent_id, depth = :depth
            WHERE id = :id
        ");
        $depth = $parentId ? $this->getCategoryDepth($parentId) + 1 : 0;
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':parent_id', $parentId);
        $stmt->bindParam(':depth', $depth);
        $stmt->execute();
    }

    // Get the depth of a category
    private function getCategoryDepth($categoryId) {
        $stmt = $this->db->prepare("
            SELECT depth FROM categories WHERE id = :id
        ");
        $stmt->bindParam(':id', $categoryId);
        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $result ? $result['depth'] : false;
    }
}