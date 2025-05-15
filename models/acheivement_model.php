<?php
include_once '../helpers/db_helpers.php';

class AchievementModel {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    public function getAchievementData($lectureId, $studentId) {
        $sql = "WITH ranked_content AS (
            SELECT 
                lc.type,
                lc.from_surah,
                lc.from_ayah,
                lc.to_surah,
                lc.to_ayah,
                lc.observation,
                ROW_NUMBER() OVER (PARTITION BY lc.type ORDER BY ls.lecture_date) as rn
            FROM lecture_content lc
            INNER JOIN lecture_student ls 
                ON lc.lecture_id = ls.lecture_id 
                AND lc.student_id = ls.student_id
            WHERE lc.type IN ('hifd', 'quickRev', 'majorRev') 
              AND lc.lecture_id = ?
              AND lc.student_id = ?
        )
        SELECT *
        FROM ranked_content
        WHERE rn = 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $lectureId, $studentId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $achievements = [];
        while ($row = $result->fetch_assoc()) {
            $type = $row['type'];
            $achievements[] = [
                'type' => $type,
                'from_surah' => $row['from_surah'],
                'from_ayah' => $row['from_ayah'],
                'to_surah' => $row['to_surah'],
                'to_ayah' => $row['to_ayah'],
                'observation' => $row['observation']
            ];
        }
        
        $stmt->close();
        return $achievements;
    }
    
    public function getStudentsForAchievement($lectureId) {
        $query = "SELECT 
                    student.student_id, 
                    CONCAT(personal_info.first_name_ar, ' ', personal_info.last_name_ar) AS full_name 
                  FROM student 
                  INNER JOIN personal_info ON student.student_id = personal_info.student_id 
                  INNER JOIN lecture_student ON student.student_id = lecture_student.student_id 
                  WHERE lecture_student.lecture_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $students = convertResultToArray($result);
        $stmt->close();
        
        return $students;
    }
    
    public function createAchievementRecord(
        $studentId,
        $lectureId,
        $date,
        $attendance,
        $hifd,
        $quickRev,
        $majorRev,
        $teacherNote
    ) {
        $this->conn->begin_transaction();
        
        try {
            // Insert student attendance
            $this->insertLectureStudentAttendance($lectureId, $studentId, $attendance, $date);
            
            // Insert teacher attendance
            $this->insertTeacherAttendance($lectureId, $date);
            
            // Insert revision content
            $this->insertRevisions($lectureId, $studentId, $hifd, 'hifd');
            $this->insertRevisions($lectureId, $studentId, $quickRev, 'quickRev');
            $this->insertRevisions($lectureId, $studentId, $majorRev, 'majorRev');
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'message' => 'Achievement record created successfully'
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Failed to create achievement record: ' . $e->getMessage()
            ];
        }
    }
    
    private function insertLectureStudentAttendance($lectureId, $studentId, $attendance, $date) {
        $stmt = $this->conn->prepare("
            INSERT INTO lecture_student 
            (lecture_id, student_id, attendance_status, lecture_date)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                attendance_status = VALUES(attendance_status),
                lecture_date = VALUES(lecture_date)
        ");
        $stmt->bind_param("iiss", $lectureId, $studentId, $attendance, $date);
        $stmt->execute();
        $stmt->close();
    }
    
    private function insertTeacherAttendance($lectureId, $date) {
        // Get teacher ID
        $stmt = $this->conn->prepare("SELECT teacher_id FROM lecture WHERE lecture_id = ?");
        $stmt->bind_param("i", $lectureId);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        if (!$row || !isset($row['teacher_id'])) {
            throw new RuntimeException("Teacher ID not found for lecture $lectureId");
        }
        
        $teacherId = $row['teacher_id'];
        $teacherAttendance = 'present';
        
        $stmt = $this->conn->prepare("
            INSERT INTO lecture_teacher 
            (teacher_id, lecture_id, attendance_status, lecture_date)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                attendance_status = VALUES(attendance_status),
                lecture_date = VALUES(lecture_date)
        ");
        $stmt->bind_param("iiss", $teacherId, $lectureId, $teacherAttendance, $date);
        $stmt->execute();
        $stmt->close();
    }
    
    private function insertRevisions($lectureId, $studentId, $revisions, $type) {
        if (empty($revisions)) {
            return;
        }
        
        $stmt = $this->conn->prepare("
            INSERT INTO lecture_content 
            (lecture_id, student_id, type, from_surah, from_ayah, to_surah, to_ayah, observation) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        foreach ($revisions as $revision) {
            $stmt->bind_param(
                "iissisis",
                $lectureId,
                $studentId,
                $type,
                $revision['fromSurahName'],
                $revision['fromAyahNumber'],
                $revision['toSurahName'],
                $revision['toAyahNumber'],
                $revision['observation'] ?? ''
            );
            $stmt->execute();
        }
        
        $stmt->close();
    }
}