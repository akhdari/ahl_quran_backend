<?php
include_once '../controllers/lecture_controller.php';
include_once '../cors.php';

$db = new DB(
    'localhost',
    'root',
    '',
    'quran'
);
$conn = $db->getConnection();
$controller = new LectureController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'get_lecture_info':
                    $response = $controller->handleGetLectureInfo();
                    break;
                case 'get_lecture_id_name':
                    $response = $controller->handleGetLectureIdName();
                    break;
                case 'get_lecture_by_id':
                    $response = $controller->handleGetLectureById();
                    break;
                default:
                    throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'create_lecture':
                    $response = $controller->handleCreateLecture();
                    http_response_code(201);
                    break;
                case 'update_lecture':
                    $response = $controller->handleUpdateLecture();
                    break;
                case 'delete_lecture':
                    $response = $controller->handleDeleteLecture();
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