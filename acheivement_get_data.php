<?php
include_once './connect.php';
include_once './cors.php';
//TODO initialize teacher attendance to present

function get_data()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);
    if (!$data) {
        return null;
    }
    return $data;
}

function check_for_empty_field($value)
{
    return empty($value) && $value !== "0";  //TODO: Check for 0
}

function validate_revision_fields($revisions)
{
    foreach ($revisions as $item) {
        if (
            check_for_empty_field($item['fromSurahName'] ?? null) ||
            check_for_empty_field($item['fromAyahNumber'] ?? null) ||
            check_for_empty_field($item['toSurahName'] ?? null) ||
            check_for_empty_field($item['toAyahNumber'] ?? null)
        ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing data in revision fields']);
            exit;
        }
    }
    return true;
}

function insert_revisions($conn, $revision_list, $type, $lecture_id, $student_id)
{
    $stmt = $conn->prepare("
        INSERT INTO lecture_content 
        (lecture_id, student_id, type, from_surah, from_ayah, to_surah, to_ayah, observation) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($revision_list as $item) {
        $from_surah = $item['fromSurahName'];
        $from_ayah = $item['fromAyahNumber'];
        $to_surah = $item['toSurahName'];
        $to_ayah = $item['toAyahNumber'];
        $observation = $item['observation'] ?? '';

        $stmt->bind_param("iissisis", $lecture_id, $student_id, $type, $from_surah, $from_ayah, $to_surah, $to_ayah, $observation);
        $stmt->execute();
    }

    $stmt->close();
}

$data = get_data();


if ($data) {
    try {
        $student_id = $data['studentId'] ?? null;
        $lecture_id = $data['lectureId'] ?? null;
        $date = $data['date'] ?? date('Y-m-d');
        $attendance = $data['attendanceStatus'] ?? 'present';
        $hifd = $data['hifd'] ?? [];
        $quick_revision = $data['quickRev'] ?? [];
        $major_revision = $data['majorRev'] ?? [];
        $teacher_note = $data['teacherNote'] ?? '';

        if (
            check_for_empty_field($student_id) ||
            check_for_empty_field($lecture_id)

        ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Student ID, Lecture ID  is missing']);
            return;
        }

        if (!empty($hifd)) validate_revision_fields($hifd);
        if (!empty($quick_revision)) validate_revision_fields($quick_revision);
        if (!empty($major_revision)) validate_revision_fields($major_revision);

        // Insert student attendance
        $stmt1 = $conn->prepare("
            INSERT INTO lecture_student (lecture_id, student_id, attendance_status, lecture_date)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            attendance_status = VALUES(attendance_status),
            lecture_date = VALUES(lecture_date)

        ");
        $stmt1->bind_param("iiss", $lecture_id, $student_id, $attendance, $date);
        $stmt1->execute();
        $stmt1->close();

        // Get teacher ID
        $stmt = $conn->prepare("SELECT teacher_id FROM lecture WHERE lecture_id = ?");
        $stmt->bind_param("i", $lecture_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row && isset($row['teacher_id'])) { //isset is used for safe access to the row it checks of it exist and if null
            $teacher_id = $row['teacher_id'];

            // Insert teacher attendance
            $stmt2 = $conn->prepare("
        INSERT INTO lecture_teacher (teacher_id, lecture_id, attendance_status, lecture_date)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE 
            attendance_status = VALUES(attendance_status),
            lecture_date = VALUES(lecture_date)
           ");
            $teacher_attendance = 'present';
            $stmt2->bind_param("iiss", $teacher_id, $lecture_id, $teacher_attendance, $date);
            $stmt2->execute();
            $stmt2->close();
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Teacher ID not found for the given lecture ID']);
            exit;
        }




        // Insert revision content
        insert_revisions($conn, $hifd, 'hifd', $lecture_id, $student_id);
        insert_revisions($conn, $quick_revision, 'quickRev', $lecture_id, $student_id);
        insert_revisions($conn, $major_revision, 'majorRev', $lecture_id, $student_id);

        echo json_encode(['success' => true, 'message' => 'Record inserted successfully']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data format']);
}
