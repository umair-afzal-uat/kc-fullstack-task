<?php

namespace Controllers;

use Models\Category;

class CategoryController {
    private $categoryModel;

    public function __construct() {
        $this->categoryModel = new \Models\Category();
    }

    // GET /categories
    public function getAllCategories() {
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

    // GET /categories/{id}
    public function getCategoryById($id) {
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

    // POST /categories
    public function createCategory() {
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

    // PUT /categories/{id}
    public function updateCategory($id) {
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