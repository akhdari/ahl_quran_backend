<?php
include_once '../controllers/achievement_controller.php';
include_once '../cors.php';

$db = new DB(
    'localhost',
    'root',
    '',
    'quran'
);
$conn = $db->getConnection();
$controller = new AchievementController($conn);

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
    switch ($method) {
        case 'GET':
            switch ($action) {
                case 'get_latest_achievements':
                    $lectureId = $_GET['lecture_id'] ?? null;
                    $studentId = $_GET['student_id'] ?? null;
                    $response = $controller->handleGetLatestAchievements($lectureId, $studentId);
                    break;
                case 'get_students_by_lecture':
                    $response = $controller->handleGetStudentsByLecture();
                    break;
                default:
                    throw new RuntimeException('Invalid action', 400);
            }
            break;
            
        case 'POST':
            if ($action == 'create_achievement_record') {
                $response = $controller->handleCreateAchievementRecord();
                http_response_code(201); // Created
            } else {
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