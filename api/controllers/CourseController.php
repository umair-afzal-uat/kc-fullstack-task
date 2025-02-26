<?php

namespace Controllers;

use Models\Course;

class CourseController {
    private $courseModel;

    public function __construct() {
        $this->courseModel = new \Models\Course();
    }

    // GET /courses
    public function getCourses() {
        try {
            $categoryId = $_GET['category_id'] ?? null;

            $courses = $this->courseModel->getCourses($categoryId);

            echo json_encode([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // GET /courses/{id}
    public function getCourseById($id) {
        try {
            $course = $this->courseModel->getCourseById($id);

            if ($course) {
                echo json_encode([
                    'status' => 'success',
                    'data' => $course
                ]);
            } else {
                http_response_code(404);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Course not found'
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
}