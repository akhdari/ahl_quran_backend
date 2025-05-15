<?php
include_once '../controllers/guardian_controller.php';
include_once '../cors.php';
include_once '../helpers/db_helpers.php';
include_once '../db.php';

$db = new DB(
    'localhost',
    'root',
    '',
    'quran'
);
$conn = $db->getConnection();
$controller = new GuardianController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'get_guardian_by_id':
                    $response = $controller->handleGetById();
                    break;
                case 'get_guardian_accounts':
                    $response = $controller->handleGetAccounts();
                    break;
                case 'get_guardian_info':
                    $response = $controller->handleGetInfo();
                    break;
                default:
                    throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        case 'POST':
            switch ($action) {
                case 'create_guardian':
                    $response = $controller->handleCreate();
                    http_response_code(201); // Created
                    break;
                case 'delete_guardian':
                    $response = $controller->handleDelete();
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