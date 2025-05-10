<?php
include_once './connect.php';
include_once './query.php';
include_once './cors.php';


function get_data()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);
    if (!$data) {
        return null;
    }
    return $data;
}
function get_latest_acheivements($conn, $lecture_id, $student_id) {
    $sql = "WITH ranked_content AS (
        SELECT 
        lc.type,
        lc.from_surah,
        lc.from_ayah,
        lc.to_surah,
        lc.to_ayah,
        lc.observation,
               ROW_NUMBER() OVER (PARTITION BY lc.type ORDER BY ls.lecture_date) as rn
        FROM lecture_content lc
        INNER JOIN lecture_student ls 
            ON lc.lecture_id = ls.lecture_id 
            AND lc.student_id = ls.student_id
        WHERE lc.type IN ('hifd', 'quickRev', 'majorRev') 
          AND lc.lecture_id = ?
          AND lc.student_id = ?
    )
    SELECT *
    FROM ranked_content
    WHERE rn = 1;";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $lecture_id, $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows == 0) {
        echo json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $stmt->close();
        return;
    }
    while ($row = $result->fetch_assoc()) {
        $type = $row['type'];
        $surah_ayah = [
            'from_surah' => $row['from_surah'],
            'from_ayah' => $row['from_ayah'],
            'to_surah' => $row['to_surah'],
            'to_ayah' => $row['to_ayah'],
            'observation' => $row['observation'],
        ];
        $results[]= [$type => $surah_ayah];
    }
    echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    mysqli_free_result($result);
    $stmt->close();   
}

$data = get_data();
if ($data) {
    $lecture_id = $data['lecture_id']??null;
    $student_id = $data['student_id']??null;
    if (empty($lecture_id) || empty($student_id)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Lecture ID or Student ID is missing']);
        exit();
    }
    get_latest_acheivements($conn, $lecture_id, $student_id);
}else{
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid or missing data format']);
}

?>