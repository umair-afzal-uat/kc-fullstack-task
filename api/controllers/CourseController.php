<?php

namespace Controllers;

use Models\Course;
use Exception;

class CourseController {
    private Course $courseModel;

    public function __construct() {
        $this->courseModel = new Course();
    }

    /**
     * Get all courses, optionally filtered by category ID.
     *
     * @return void Outputs JSON response.
     */
    public function getCourses(): void {
        try {
            $categoryId = $_GET['category_id'] ?? null;

            $courses = $this->courseModel->getCourses($categoryId);

            echo json_encode([
                'status' => 'success',
                'data' => $courses
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get a course by ID.
     *
     * @param string $id The ID of the course.
     * @return void Outputs JSON response.
     */
    public function getCourseById(string $id): void {
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
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}