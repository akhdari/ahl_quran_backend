<?php
include_once '../models/teacher_model.php';
include_once '../helpers/db_helpers.php';
include_once '../db.php';

class TeacherController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new TeacherModel($conn);
    }
    
    public function handleGetTeachers() {
        try {
            $teachers = $this->model->getTeachers();
            return [
                'success' => true,
                'data' => $teachers
            ];
        } catch (Exception $e) {
            throw new RuntimeException('Failed to fetch teachers: ' . $e->getMessage(), 500);
        }
    }
    
}