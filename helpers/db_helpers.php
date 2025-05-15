<?php
function fetch_all_assoc($result): array
{
    $data = [];
    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }
    return $data;
}


function convertResultToArray(mysqli_result $result): array
{
    $data = [];

    if ($result->num_rows > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
    }

    return $data;
}

function get_data()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!is_array($data)) {
        throw new InvalidArgumentException('Invalid JSON data', 400);
    }

    if (empty($data)) {
        throw new InvalidArgumentException('Request body is empty', 400);
    }

    return $data;
}


function validate_required($fields) {
    foreach ($fields as $key => $value) {
        if (empty($value)) {
            throw new Exception("Required field '$key' is missing or empty.");
        }
    }
}

function check_for_empty_field($value)
{
    return empty($value) && $value !== "0"; 
}

function validate_ayah_range($revisions)
{
    foreach ($revisions as $item) {
        if (
            check_for_empty_field($item['fromSurahName'] ?? null) ||
            check_for_empty_field($item['fromAyahNumber'] ?? null) ||
            check_for_empty_field($item['toSurahName'] ?? null) ||
            check_for_empty_field($item['toAyahNumber'] ?? null)
        ) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Missing data in revision fields']);
            exit;
        }
    }
    return true;
}

function checkForDuplicates($conn, $username, $email)
{
    // Check username in account_info
    $stmt = $conn->prepare("SELECT account_id FROM account_info WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        throw new RuntimeException("Username already exists", 409);
    }
    $stmt->close();

    // Check email in contact_info
    $stmt = $conn->prepare("SELECT contact_id FROM contact_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $stmt->close();
        throw new RuntimeException("Email already exists", 409);
    }
    $stmt->close();
}

?>
