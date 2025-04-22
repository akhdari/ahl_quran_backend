<?php
include './connect.php';
include_once './cors.php';
include_once './add_student.php'; 

function get_student()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        http_response_code(400);
        return null;
    }
    return $data;
}

function delete_student($student_id, $conn)
{
    $stmt = $conn->prepare("DELETE FROM student WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $stmt->close();
}

$data = get_student();
if ($data) {
    try {
        $student_id = $data['id'] ?? null;
        if (empty($student_id)) {
            throw new Exception("Student ID is required.");
        }
        delete_student($student_id, $conn);
        echo json_encode(['success' => true, 'message' => 'Student deleted successfully']);
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
