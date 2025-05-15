<?php
include_once '../models/lecture_model.php';
include_once '../helpers/db_helpers.php';
include_once '../db.php';

class LectureController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new LectureModel($conn);
    }
    
    public function handleGetLectureInfo() {
        $data = $this->model->getLectureInfo();
        return ['success' => true, 'data' => $data];
    }
    
    public function handleDeleteLecture() {
        $data = get_data();
        if (!$data || empty($data['id'])) {
            throw new InvalidArgumentException('Lecture ID is required', 400);
        }
        
        if (!$this->model->deleteLecture($data['id'])) {
            throw new RuntimeException('Lecture not found or could not be deleted', 404);
        }
        
        return ['success' => true, 'message' => 'Lecture deleted successfully'];
    }
    
    public function handleCreateLecture() {
        $data = get_data();
        if (!$data) {
            throw new InvalidArgumentException('Invalid or missing data format', 400);
        }
        
        $lectureData = $this->validateLectureData($data);
        $result = $this->model->addLecture($lectureData);
        
        if (!$result['success']) {
            throw new RuntimeException($result['message'], 500);
        }
        
        return $result;
    }
    
    public function handleUpdateLecture() {
        $data = get_data();
        if (!$data) {
            throw new InvalidArgumentException('Invalid or missing data format', 400);
        }
        
        $lectureData = $this->validateLectureData($data, true);
        $result = $this->model->updateLecture($lectureData);
        
        if (!$result['success']) {
            throw new RuntimeException($result['message'], 500);
        }
        
        return $result;
    }
    
    public function handleGetLectureById() {
        $data = get_data();
        $lectureId = $data['id'] ?? null;
        
        if (!$lectureId) {
            throw new InvalidArgumentException('Lecture ID is required', 400);
        }
        
        $lectureInfo = $this->model->getLectureInfo($lectureId);
        
        if (!$lectureInfo) {
            throw new RuntimeException('Lecture not found', 404);
        }
        
        return ['success' => true, 'data' => $lectureInfo];
    }
    
    public function handleGetLectureIdName() {
        $data = $this->model->getLectureIdName();
        return ['success' => true, 'data' => $data];
    }
    
    private function validateLectureData($data, $isUpdate = false) {
        $info = $data['info'] ?? [];
        $schedule = $data['schedule'] ?? [];
        
        $requiredFields = [
            'lecture_name_ar' => $info['lecture_name_ar'] ?? null,
            'lecture_name_en' => $info['lecture_name_en'] ?? null,
            'circle_type' => $info['circle_type'] ?? null,
            'teacher_ids' => $info['teacher_ids'] ?? null,
            'show_on_website' => $info['show_on_website'] ?? null,
            'schedule' => $schedule
        ];
        
        if ($isUpdate) {
            $requiredFields['lecture_id'] = $info['lecture_id'] ?? null;
        }
        
        foreach ($requiredFields as $field => $value) {
            if ($value === null || $value === '') {
                throw new InvalidArgumentException("Required field '$field' is missing or empty", 400);
            }
        }
        
        return [
            'lecture_id' => $isUpdate ? $info['lecture_id'] : null,
            'lecture_name_ar' => $info['lecture_name_ar'],
            'lecture_name_en' => $info['lecture_name_en'],
            'circle_type' => $info['circle_type'],
            'show_on_website' => $info['show_on_website'],
            'teacher_ids' => $info['teacher_ids'],
            'schedule' => $schedule
        ];
    }
}