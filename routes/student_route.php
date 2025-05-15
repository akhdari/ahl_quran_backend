<?php
include_once '../controllers/student_controller.php';
include_once '../cors.php';

$db = new DB(
    'localhost',
    'root',
    '',
    'quran'
);
$conn = $db->getConnection();
$controller = new StudentController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action == 'get_student_info') {
                $response = $controller->handleGetStudentInfo();
            } else {
                throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'create_student':
                    $response = $controller->handleCreateStudent();
                    http_response_code(201); // Created
                    break;
                case 'delete_student':
                    $response = $controller->handleDeleteStudent();
                    break;
                default:
                    throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        default:
            throw new RuntimeException('Method not allowed', 405);
    }
    
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (InvalidArgumentException $e) {
    http_response_code($e->getCode() ?: 400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (RuntimeException $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error']);
} finally {
    $conn->close();
}