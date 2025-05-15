<?php
include_once '../controllers/teacher_controller.php';
include_once '../cors.php';

$db = new DB(
    'localhost',
    'root',
    '',
    'quran'
);
$conn = $db->getConnection();
$controller = new TeacherController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            if ($action == 'get_teachers') {
                $response = $controller->handleGetTeachers();
            } else {
                throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        // Add more methods 
            
        default:
            throw new RuntimeException('Method not allowed', 405);
    }
    
    header('Content-Type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (RuntimeException $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error'
    ]);
} finally {
    $conn->close();
}