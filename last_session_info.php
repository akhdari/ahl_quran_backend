<?php
function get_last_session_info($conn, $session_date, $session_name) {
    $tables = ['quick_revision_data', 'major_revision_data', 'memorazation_data'];
    $results = [];

    foreach ($tables as $table) {
        $query = "SELECT from_surah, from_ayah, to_surah, to_ayah 
                  FROM $table 
                  WHERE session_date = :session_date AND session_name = :session_name 
                  ORDER BY session_id DESC LIMIT 1";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':session_date', $session_date);
        $stmt->bindParam(':session_name', $session_name);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $results[$table] = $row ?: null;
    }

    return $results;
}

?>