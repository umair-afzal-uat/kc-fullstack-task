<?php

// Enable CORS (Cross-Origin Resource Sharing) for API requests
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Include necessary files
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/CourseController.php';

// Set response type to JSON
header('Content-Type: application/json');

try {
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uriParts = explode('/', trim($uri, '/'));

    // API routing
    switch ($uriParts[0]) {
        case 'categories':
            $controller = new \Controllers\CategoryController();
            if ($requestMethod === 'GET') {
                if (!empty($uriParts[1])) {
                    $controller->getCategoryById($uriParts[1]);
                } else {
                    $controller->getAllCategories();
                }
            } elseif ($requestMethod === 'POST') {
                $controller->createCategory();
            } elseif ($requestMethod === 'PUT' && !empty($uriParts[1])) {
                $controller->updateCategory($uriParts[1]);
            } else {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            }
            break;

        case 'courses':
            $controller = new \Controllers\CourseController();
            if ($requestMethod === 'GET') {
                if (!empty($uriParts[1])) {
                    $controller->getCourseById($uriParts[1]);
                } else {
                    $controller->getCourses();
                }
            } else {
                http_response_code(405);
                echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Not Found']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}