<?php
include_once './connect.php';
include_once './cors.php';

function acheivement_student_list($conn,$lecture_id){
    $query = "SELECT student.student_id, CONCAT(personal_info.first_name_ar, ' ', personal_info.last_name_ar) AS full_name FROM student INNER JOIN personal_info ON student.student_id = personal_info.student_id INNER JOIN lecture_student ON student.student_id = lecture_student.student_id WHERE lecture_student.lecture_id = ?;";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $lecture_id);
$stmt->execute();
$result = $stmt->get_result();
$data = fetch_data_table_1($result);
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
mysqli_free_result($result);
closet_db($conn); 
}

if (isset($_GET['session_id'])) {
    acheivement_student_list($conn,$_GET['session_id']);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data format']);
}

?>