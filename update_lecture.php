<?php
include_once './connect.php';
include_once './cors.php';



function get_request_data()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No JSON data received or format is invalid']);
        exit;
    }
    return $data;
}

$data = get_request_data();

try {
    $info = $data['info'] ?? [];
    $schedule_info = $data['schedule'] ?? [];

    $requiredFields = [
        'lecture_id'        => $info['lecture_id'] ?? null,
        'lecture_name_ar'   => $info['lecture_name_ar'] ?? null,
        'lecture_name_en'   => $info['lecture_name_en'] ?? null,
        'circle_type'       => $info['circle_type'] ?? null,
        'category'          => $info['category'] ?? null,
        'teacher_ids'     => $info['teacher_ids'] ?? null,
        'show_on_website'   => $info['show_on_website'] ?? null,
        'schedule'          => $schedule_info ?? null,
    ];

    foreach ($requiredFields as $field => $value) {
        if (!isset($value)) {
            throw new Exception("Required field '$field' is missing or empty.");
        }
    }

    $response = update_lecture(
        $conn,
        $requiredFields['lecture_id'],
        $requiredFields['schedule'],
        $requiredFields['teacher_ids'],
        $requiredFields['lecture_name_ar'],
        $requiredFields['lecture_name_en'],
        $requiredFields['circle_type'],
        $requiredFields['show_on_website']

    );

    echo json_encode($response);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}



function update_lecture($conn, $lecture_id, $schedule_info, $teachers_ids, $lecture_name_ar, $lecture_name_en, $circle_type, $shown_on_website)
{
    $conn->begin_transaction();

    try {
        // Update the lecture
        $stmt = $conn->prepare("UPDATE lecture SET lecture_name_ar = ?, lecture_name_en = ?, circle_type = ?, shown_on_website = ? WHERE lecture_id = ?");
        $stmt->bind_param("sssii", $lecture_name_ar, $lecture_name_en, $circle_type, $shown_on_website, $lecture_id);
        $stmt->execute();
        $stmt->close();

        // Update weekly schedule
        foreach ($schedule_info as $schedule_id => $time) {
            $day = $time['day'];
            $start_time = $time['from'];
            $end_time = $time['to'];
            $stmt = $conn->prepare("UPDATE weekly_schedule SET day_of_week = ?, start_time = ?, end_time = ? WHERE weekly_schedule_id = ? AND lecture_id = ?");
            $stmt->bind_param("sssii", $day, $start_time, $end_time, $schedule_id, $lecture_id);
            $stmt->execute();
            $stmt->close();
        }

        $stmt->close();

        $conn->commit();

        return [
            'success' => true,
            'message' => 'Lecture updated successfully',
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
