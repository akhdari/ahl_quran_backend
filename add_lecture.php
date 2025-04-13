<?php
include_once './connect.php';

function add_lecture($conn, $schedule_info, $teachers_names, $lecture_name_ar, $lecture_name_en, $circle_type, $shown_on_website)
{
    $conn->begin_transaction();

    try {
        // Insert the lecture
        $stmt = $conn->prepare("INSERT INTO lecture (lecture_id, lecture_name_ar, lecture_name_en, circle_type, shown_on_website) VALUES (NULL, ?, ?, ?, ?)");
        $stmt->bind_param("sssi", $lecture_name_ar, $lecture_name_en, $circle_type, $shown_on_website);
        $stmt->execute();
        $lecture_id = $conn->insert_id;
        $stmt->close();

        // Insert weekly schedule
        foreach ($schedule_info as $day => $time) {
            $start_time = $time['from'];
            $end_time = $time['to'];

            $stmt = $conn->prepare("INSERT INTO weekly_schedule (day_of_week, start_time, end_time, lecture_id) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $day, $start_time, $end_time, $lecture_id);
            $stmt->execute();
            $stmt->close();
        }

        // Get teacher IDs from names
        $teacher_ids = [];
        foreach ($teachers_names as $teacher_name) {
            $stmt = $conn->prepare("SELECT teacher_id FROM teacher WHERE teacher_name = ?");
            $stmt->bind_param("s", $teacher_name);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $teacher_ids[] = $row['teacher_id'];
            } else {
                throw new Exception("Teacher name '$teacher_name' not found.");
            }
            $stmt->close();
        }

        // Insert into lecture_teacher
        $stmt = $conn->prepare("INSERT INTO lecture_teacher (teacher_id, lecture_id) VALUES (?, ?)");
        foreach ($teacher_ids as $teacher_id) {
            $stmt->bind_param("ii", $teacher_id, $lecture_id);
            $stmt->execute();
        }
        $stmt->close();

        $conn->commit();

        return [
            'success' => true,
            'message' => 'Lecture added successfully',
            'lecture_id' => $lecture_id
        ];
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => 'Transaction failed: ' . $e->getMessage()
        ];
    } finally {
        mysqli_close($conn);
    }
}
?>
