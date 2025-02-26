<?php

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/models/Category.php';
require_once __DIR__ . '/models/Course.php';
require_once __DIR__ . '/controllers/CategoryController.php';
require_once __DIR__ . '/controllers/CourseController.php';

header('Content-Type: application/json');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriParts = explode('/', trim($uri, '/'));

// Route requests
switch ($uriParts[0]) {
    case 'categories':
        $controller = new \Controllers\CategoryController();
        if ($requestMethod === 'GET') {
            if (isset($uriParts[1])) {
                $controller->getCategoryById($uriParts[1]);
            } else {
                $controller->getAllCategories();
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
        break;

    case 'courses':
        $controller = new \Controllers\CourseController();
        if ($requestMethod === 'GET') {
            if (isset($uriParts[1])) {
                $controller->getCourseById($uriParts[1]);
            } else {
                $controller->getCourses();
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
}