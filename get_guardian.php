<?php
include './connect.php';
include_once './cors.php';
include_once './add_gradian.php';

function get_guardian()
{
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'No JSON data received or format is invalid']);
        return null;
    }
    return $data;
}

$data = get_guardian();
if ($data) {
    try {
        $info = $data['info'] ?? [];
        $account_info = $data['account_info'] ?? [];
        $contact_info = $data['contact_info'] ?? [];

        $requiredFields = [
            'first_name'   => $info['first_name'] ?? null,
            'last_name'    => $info['last_name'] ?? null,
            'relationship' => $info['relationship'] ?? null,
            'username'     => $account_info['username'] ?? null,
            'passcode'     => $account_info['passcode'] ?? null,
            'phone_number' => $contact_info['phone_number'] ?? null,
            'email'        => $contact_info['email'] ?? null
        ];

        // Check all required fields
        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                throw new Exception("Required field '$field' is missing or empty.");
            }
        }

        $optionalFields = [
            'profile_image' => $account_info['profile_image'] ?? null,
            'date_of_birth' => $info['date_of_birth'] ?? null,
            'home_address'  => $info['home_address'] ?? null,
            'job'           => $info['job'] ?? null
        ];

        add_guardian(
            $conn,
            $requiredFields['username'],
            $requiredFields['email'],
            $requiredFields['phone_number'],
            $requiredFields['passcode'],
            $optionalFields['profile_image'],
            $optionalFields['job'],
            $optionalFields['home_address'],
            $requiredFields['first_name'],
            $requiredFields['last_name'],
            $optionalFields['date_of_birth'],
            $requiredFields['relationship']
        );

        echo json_encode(['success' => true, 'message' => 'Guardian added successfully']);
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
