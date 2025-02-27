<?php

namespace Models;

use Config\Database;
use PDO;

class Category {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Fetch all categories along with the count of courses in each category (including subcategories).
     *
     * @return array An associative array of categories with course counts.
     */
    public function getAllCategories(): array {
        $sql = "
            WITH RECURSIVE category_tree AS (
                SELECT id, parent_id FROM categories
                WHERE parent_id IS NULL
                UNION ALL
                SELECT c.id, c.parent_id FROM categories c
                INNER JOIN category_tree ct ON c.parent_id = ct.id
            )
            SELECT c.id, c.name, c.parent_id, c.depth,
                   (SELECT COUNT(*) 
                    FROM courses 
                    WHERE category_id IN (SELECT id FROM category_tree WHERE id = c.id OR parent_id = c.id)
                   ) AS count_of_courses
            FROM categories c
        ";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    

    /**
     * Fetch a single category by ID, including the count of courses.
     *
     * @param string $id The ID of the category.
     * @return array|false The category data or false if not found.
     */
    public function getCategoryById(string $id): array|false {
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
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Insert a new category.
     *
     * @param string $name The name of the category.
     * @param string|null $parentId The ID of the parent category (optional).
     * @return string The newly generated category ID.
     * @throws \Exception If parent category doesn't exist or exceeds depth limit.
     */
    public function insertCategory(string $name, ?string $parentId = null): string {
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

    /**
     * Update an existing category.
     *
     * @param string $id The ID of the category.
     * @param string|null $parentId The ID of the new parent category (optional).
     * @throws \Exception If parent category doesn't exist or exceeds depth limit.
     */
    public function updateCategory(string $id, ?string $parentId = null): void {
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

    /**
     * Get the depth of a category.
     *
     * @param string $categoryId The ID of the category.
     * @return int|false The depth of the category or false if not found.
     */
    private function getCategoryDepth(string $categoryId): int|false {
        $stmt = $this->db->prepare("
            SELECT depth FROM categories WHERE id = :id
        ");
        $stmt->bindParam(':id', $categoryId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? (int)$result['depth'] : false;
    }
}
