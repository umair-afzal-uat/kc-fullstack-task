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
        $categories = $this->categoryModel->getAllCategories();
        echo json_encode($categories);
    }

    // GET /categories/{id}
    public function getCategoryById($id) {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Category not found']);
        }
    }
}