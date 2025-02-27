<?php

namespace Controllers;

use Models\Category;

class CategoryController {
    private Category $categoryModel;

    public function __construct() {
        $this->categoryModel = new Category();
    }

    /**
     * Get all categories.
     *
     * @return void Outputs JSON response.
     */
    public function getAllCategories(): void {
        try {
            $categories = $this->categoryModel->getAllCategories();
            echo json_encode([
                'status' => 'success',
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a category by ID.
     *
     * @param string $id The ID of the category.
     * @return void Outputs JSON response.
     */
    public function getCategoryById(string $id): void {
        try {
            $category = $this->categoryModel->getCategoryById($id);
            if ($category) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $category
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Category not found'
                ]);
            }
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a new category.
     *
     * @return void Outputs JSON response.
     */
    public function createCategory(): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $name = $input['name'] ?? null;
        $parentId = $input['parent_id'] ?? null;

        if (!$name) {
            http_response_code(400);
            echo json_encode([
                'status' => 'error',
                'message' => 'Name is required'
            ]);
            return;
        }

        try {
            $newCategoryId = $this->categoryModel->insertCategory($name, $parentId);
            echo json_encode([
                'status' => 'success',
                'message' => 'Category created successfully',
                'data' => ['id' => $newCategoryId]
            ]);
        } catch (\Exception $e) {
            http_response_code(400); 
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Update an existing category.
     *
     * @param string $id The ID of the category.
     * @return void Outputs JSON response.
     */
    public function updateCategory(string $id): void {
        $input = json_decode(file_get_contents('php://input'), true);

        $parentId = $input['parent_id'] ?? null;

        try {
            $this->categoryModel->updateCategory($id, $parentId);
            echo json_encode([
                'status' => 'success',
                'message' => 'Category updated successfully'
            ]);
        } catch (\Exception $e) {
            http_response_code(400); 
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}