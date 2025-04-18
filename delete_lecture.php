<?php
include './connect.php';
include_once './cors.php';
include_once './add_lecture.php';

function get_lecture()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No JSON data received or format is invalid']);
        return null;
    }
    return $data;
}

function delete_lecture($lecture_id, $conn )
{
    $stmt = $conn->prepare("DELETE FROM lecture WHERE lecture_id = ?");
    $stmt->bind_param("s", $lecture_id);
    $stmt->execute();
    $stmt->close();
}

$data = get_lecture();
if ($data) {
    try {
        $lecture_id = $data['id'] ?? null;
        if (empty($lecture_id)) {
            throw new Exception("Lecture ID is required.");
        }
        delete_lecture($lecture_id, $conn);
        echo json_encode(['success' => true, 'message' => 'Lecture deleted successfully']);


    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data format']);
}
?>