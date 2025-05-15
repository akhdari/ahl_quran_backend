<?php
include_once '../models/achievement_model.php';
include_once '../helpers/db_helpers.php';
include_once '../db.php';

class AchievementController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new AchievementModel($conn);
    }
    
    public function handleGetLatestAchievements($lectureId, $studentId) {
        if (empty($lectureId) || empty($studentId)) {
            throw new InvalidArgumentException('Lecture ID and Student ID are required', 400);
        }
        
        $achievements = $this->model->getAchievementData($lectureId, $studentId);
        return ['success' => true, 'data' => $achievements];
    }
    
    public function handleGetStudentsByLecture() {
        $lectureId = $_GET['session_id'] ?? null;
        
        if (empty($lectureId)) {
            throw new InvalidArgumentException('Lecture ID is required', 400);
        }
        
        $students = $this->model->getStudentsForAchievement($lectureId);
        return ['success' => true, 'data' => $students];
    }
    
    public function handleCreateAchievementRecord() {
        $data = get_data();
        
        if (!$data) {
            throw new InvalidArgumentException('Invalid or missing data format', 400);
        }
        
        $this->validateAchievementData($data);
        
        $result = $this->model->createAchievementRecord(
            $data['studentId'],
            $data['lectureId'],
            $data['date'] ?? date('Y-m-d'),
            $data['attendanceStatus'] ?? 'present',
            $data['hifd'] ?? [],
            $data['quickRev'] ?? [],
            $data['majorRev'] ?? [],
            $data['teacherNote'] ?? ''
        );
        
        if (!$result['success']) {
            throw new RuntimeException($result['message'], 500);
        }
        
        return $result;
    }
    
    private function validateAchievementData($data) {
        $requiredFields = [
            'studentId' => $data['studentId'] ?? null,
            'lectureId' => $data['lectureId'] ?? null
        ];
        
        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                throw new InvalidArgumentException("Required field '$field' is missing", 400);
            }
        }
        
        // Validate ayah ranges if provided
        $revisionTypes = [
            'hifd' => $data['hifd'] ?? [],
            'quickRev' => $data['quickRev'] ?? [],
            'majorRev' => $data['majorRev'] ?? []
        ];
        
        foreach ($revisionTypes as $type => $revisions) {
            if (!empty($revisions)) {
                foreach ($revisions as $revision) {
                    if (!isset($revision['fromSurahName'], $revision['fromAyahNumber'], 
                              $revision['toSurahName'], $revision['toAyahNumber'])) {
                        throw new InvalidArgumentException("Invalid $type revision format", 400);
                    }
                }
            }
        }
    }
}