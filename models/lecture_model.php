<?php
include_once '../helpers/db_helpers.php';

class LectureModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getLectureInfo($lectureId = null) {
        $query = "SELECT 
                lecture.lecture_id,
                lecture.lecture_name_ar, 
                lecture.lecture_name_en,
                lecture.circle_type, 
                lecture.shown_on_website,
                GROUP_CONCAT(DISTINCT lecture_teacher.teacher_id SEPARATOR ', ') AS teacher_ids,
                COUNT(lecture_student.student_id) AS student_count
            FROM lecture
            LEFT JOIN lecture_teacher ON lecture.lecture_id = lecture_teacher.lecture_id
            LEFT JOIN lecture_student ON lecture.lecture_id = lecture_student.lecture_id";
        
        if ($lectureId) {
            $query .= " WHERE lecture.lecture_id = ?";
        }
        
        $query .= " GROUP BY lecture.lecture_id, lecture.lecture_name_ar, lecture.circle_type";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        
        if ($lectureId) {
            $stmt->bind_param("i", $lectureId);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $data = convertResultToArray($result);
        $stmt->close();
        
        return $lectureId ? ($data[0] ?? null) : $data;
    }
    
    public function deleteLecture($lectureId) {
        $stmt = $this->conn->prepare("DELETE FROM lecture WHERE lecture_id = ?");
        if (!$stmt) {
            throw new RuntimeException("Prepare failed: " . $this->conn->error);
        }
        
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();
        
        return $affected > 0;
    }
    
    public function addLecture($data) {
        $this->conn->begin_transaction();
        
        try {
            // Insert lecture
            $stmt = $this->conn->prepare("
                INSERT INTO lecture 
                (lecture_name_ar, lecture_name_en, circle_type, shown_on_website) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sssi", 
                $data['lecture_name_ar'], 
                $data['lecture_name_en'], 
                $data['circle_type'], 
                $data['show_on_website']
            );
            $stmt->execute();
            $lectureId = $this->conn->insert_id;
            $stmt->close();
            
            // Insert schedule
            $this->insertSchedule($lectureId, $data['schedule']);
            
            // Insert teachers
            $this->insertTeachers($lectureId, $data['teacher_ids']);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Lecture added successfully',
                'lecture_id' => $lectureId
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Failed to add lecture: ' . $e->getMessage()
            ];
        }
    }
    
    public function updateLecture($data) {
        $this->conn->begin_transaction();
        
        try {
            // Update lecture
            $stmt = $this->conn->prepare("
                UPDATE lecture SET 
                lecture_name_ar = ?, 
                lecture_name_en = ?, 
                circle_type = ?, 
                shown_on_website = ? 
                WHERE lecture_id = ?
            ");
            $stmt->bind_param(
                "sssii", 
                $data['lecture_name_ar'], 
                $data['lecture_name_en'], 
                $data['circle_type'], 
                $data['show_on_website'], 
                $data['lecture_id']
            );
            $stmt->execute();
            $stmt->close();
            
            // Update schedule
            $this->updateSchedule($data['lecture_id'], $data['schedule']);
            
            // Update teachers
            $this->updateTeachers($data['lecture_id'], $data['teacher_ids']);
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Lecture updated successfully',
                'lecture_id' => $data['lecture_id']
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Failed to update lecture: ' . $e->getMessage()
            ];
        }
    }
    
    public function getLectureIdName() {
        $query = "SELECT lecture_id AS id, lecture_name_ar AS name FROM lecture";
        $result = $this->conn->query($query);
        
        if (!$result) {
            throw new RuntimeException("Query failed: " . $this->conn->error);
        }
        
        $data = convertResultToArray($result);
        $result->free();
        return $data;
    }
    
    private function insertSchedule($lectureId, $schedule) {
        $stmt = $this->conn->prepare("
            INSERT INTO weekly_schedule 
            (day_of_week, start_time, end_time, lecture_id) 
            VALUES (?, ?, ?, ?)
        ");
        
        foreach ($schedule as $day => $time) {
            $stmt->bind_param(
                "sssi", 
                $day, 
                $time['from'], 
                $time['to'], 
                $lectureId
            );
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function updateSchedule($lectureId, $schedule) {
        // First delete existing schedule
        $stmt = $this->conn->prepare("
            DELETE FROM weekly_schedule WHERE lecture_id = ?
        ");
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $stmt->close();
        
        // Then insert new schedule
        $this->insertSchedule($lectureId, $schedule);
    }
    
    private function insertTeachers($lectureId, $teacherIds) {
        $stmt = $this->conn->prepare("
            INSERT INTO lecture_teacher (teacher_id, lecture_id) 
            VALUES (?, ?)
        ");
        
        foreach ($teacherIds as $teacherId) {
            $stmt->bind_param("ii", $teacherId, $lectureId);
            $stmt->execute();
        }
        
        $stmt->close();
    }
    
    private function updateTeachers($lectureId, $teacherIds) {
        // First delete existing teachers
        $stmt = $this->conn->prepare("
            DELETE FROM lecture_teacher WHERE lecture_id = ?
        ");
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $stmt->close();
        
        // Then insert new teachers
        $this->insertTeachers($lectureId, $teacherIds);
    }
}