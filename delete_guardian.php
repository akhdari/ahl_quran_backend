<?php
include './connect.php';
include_once './cors.php';
include_once './add_guardian.php';

function get_guardian()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        http_response_code(response_code: 400);
        return null;
    }
    return $data;
}

function delete_guardian($guardian_id, $conn )
{
    $stmt = $conn->prepare("DELETE FROM guardian WHERE guardian_id = ?");
    $stmt->bind_param("s", $guardian_id);
    $stmt->execute();
    $stmt->close();
}

$data = get_guardian();
if ($data) {
    try {
        $guardian_id = $data['id'] ?? null;
        if (empty($guardian_id)) {
            throw new Exception("Guardian ID is required.");
        }
        delete_guardian($guardian_id, $conn);
        echo json_encode(['success' => true, 'message' => 'Guardian deleted successfully']);


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