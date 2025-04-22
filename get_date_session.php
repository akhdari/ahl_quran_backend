<?php
include './connect.php';
include_once './cors.php';
include_once './last_session_info.php';

function get_date_session($conn) {
    $json_data = file_get_contents("php://input");
    $data = json_decode($json_data, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No JSON data received or format is invalid']);
        exit;
    }

    return $data;
}

$data = get_date_session($conn);

try {
    $requiredFields = [
        'session_date' => $data['session_date'] ?? null,
        'session_name' => $data['session_name'] ?? null,
    ];

    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            throw new Exception("Required field '$field' is missing or empty.");
        }
    }

    $last_session_info = get_last_session_info($conn, $data['session_date'], $data['session_name']);
    if (empty($last_session_info)) {
        throw new Exception("No session found for the provided date and name.");
    }

    echo json_encode([
        'success' => true,
        'data' => $last_session_info
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
