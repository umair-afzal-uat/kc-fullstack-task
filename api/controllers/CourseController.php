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
        $categoryId = $_GET['category_id'] ?? null;
        $courses = $this->courseModel->getCourses($categoryId);
        echo json_encode($courses);
    }

    // GET /courses/{id}
    public function getCourseById($id) {
        $course = $this->courseModel->getCourseById($id);
        if ($course) {
            echo json_encode($course);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Course not found']);
        }
    }
}