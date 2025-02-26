<?php

namespace Config;

require_once __DIR__ . '/db.php';

use PDO;

class DatabaseSetup {
    private $db;

    public function __construct() {
        // Get the database connection instance
        $this->db = \Config\Database::getInstance();
    }

    /**
     * Run all migration files in the migrations directory.
     */
    public function runMigrations($migrationDir) {
        echo "Applying migrations...\n";

        // Ensure the migration directory exists
        if (!is_dir($migrationDir)) {
            die("Migration directory not found: $migrationDir\n");
        }

        // Get all migration files sorted by name
        $files = glob("$migrationDir/*.sql");
        sort($files);

        foreach ($files as $file) {
            echo "Running $file...\n";
            $sql = file_get_contents($file);
            try {
                $this->db->exec($sql);
            } catch (\PDOException $e) {
                die("Error applying migration $file: " . $e->getMessage() . "\n");
            }
        }

        echo "Migrations applied successfully.\n";
    }

    /**
     * Load categories data from a JSON file into the database.
     */
    public function loadCategoriesData($filePath) {
        echo "Loading categories data...\n";

        if (!file_exists($filePath)) {
            die("Categories data file not found: $filePath\n");
        }

        $categories = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Invalid JSON format in categories data file.\n");
        }

        foreach ($categories as $category) {
            $stmt = $this->db->prepare("
                INSERT INTO categories (id, name, parent_id, depth)
                VALUES (:id, :name, :parent_id, :depth)
            ");
            $stmt->bindValue(':id', $category['id']);
            $stmt->bindValue(':name', $category['name']);
            $stmt->bindValue(':parent_id', $category['parent'] ?? null);
            $stmt->bindValue(':depth', $category['depth'] ?? 0);
            try {
                $stmt->execute();
            } catch (\PDOException $e) {
                die("Error inserting category {$category['id']}: " . $e->getMessage() . "\n");
            }
        }

        echo "Categories data loaded successfully.\n";
    }

    /**
     * Load courses data from a JSON file into the database.
     */
    public function loadCoursesData($filePath) {
        echo "Loading courses data...\n";

        if (!file_exists($filePath)) {
            die("Courses data file not found: $filePath\n");
        }

        $courses = json_decode(file_get_contents($filePath), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            die("Invalid JSON format in courses data file.\n");
        }

        foreach ($courses as $course) {
            $stmt = $this->db->prepare("
                INSERT INTO courses (course_id, title, description, image_preview, category_id)
                VALUES (:course_id, :title, :description, :image_preview, :category_id)
            ");
            $stmt->bindValue(':course_id', $course['course_id']);
            $stmt->bindValue(':title', $course['title']);
            $stmt->bindValue(':description', $course['description']);
            $stmt->bindValue(':image_preview', $course['image_preview']);
            $stmt->bindValue(':category_id', $course['category_id']);
            try {
                $stmt->execute();
            } catch (\PDOException $e) {
                die("Error inserting course {$course['course_id']}: " . $e->getMessage() . "\n");
            }
        }

        echo "Courses data loaded successfully.\n";
    }
}

// Main execution
try {
    $setup = new DatabaseSetup();

    // Paths to directories/files
    $migrationDir = '/var/www/html/database/migrations';
    $categoriesDataFile = '/var/www/html/data/categories.json';
    $coursesDataFile = '/var/www/html/data/course_list.json';

    // Run migrations
    $setup->runMigrations($migrationDir);

    // Load categories data
    $setup->loadCategoriesData($categoriesDataFile);

    // Load courses data
    $setup->loadCoursesData($coursesDataFile);

    echo "Database setup complete.\n";
} catch (\Exception $e) {
    die("Error during database setup: " . $e->getMessage() . "\n");
}