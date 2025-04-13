<?php
include './connect.php';
include_once './cors.php';
include_once './add_lecture.php';


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
        'lecture_name_ar'   => $info['lecture_name_ar'] ?? null,
        'lecture_name_en'   => $info['lecture_name_en'] ?? null,
        'circle_type'       => $info['circle_type'] ?? null,
        'category'          => $info['category'] ?? null,
        'teacher_names'     => $info['teacher_names'] ?? null,
        'show_on_website'   => $info['show_on_website'] ?? null,
        'schedule'          => $schedule_info ?? null,
    ];

    foreach ($requiredFields as $field => $value) {
        if (!isset($value)) {
            throw new Exception("Required field '$field' is missing or empty.");
        }
    }

    $response = add_lecture(
        $conn,
        $requiredFields['schedule'],
        $requiredFields['teacher_names'], 
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
?>
