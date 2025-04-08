<?php
include './connect.php';
include_once './cors.php';
include_once './add_student.php';

function get_student() {
    $jsonData = file_get_contents("php://input");
    $data = json_decode($jsonData, true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'No data received']);
        return null;
    }
    return $data;
}

$data = get_student();
if ($data) {
    try {
        // Extract student data from the nested structure
        $studentData = $data['student'];
        $guardianData = $data['guardian'] ?? [];

        // Required fields (non-nullable from class 1)
        $requiredFields = [
            'firstNameAR' => $studentData['personalInfo']['firstNameAR'],
            'lastNameAR' => $studentData['personalInfo']['lastNameAR'],
            'sex' => $studentData['personalInfo']['sex'],
            'username' => $studentData['account']['username'],
            'password' => $studentData['account']['password'],
            'phoneNumber' => $studentData['contactInfo']['phoneNumber'],
            'emailAddress' => $studentData['contactInfo']['emailAddress']
        ];

        // Validate required fields
        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                throw new Exception("Required field $field is missing or empty");
            }
        }

        // Optional fields
        $optionalFields = [
            // Personal info (including parent status)
            'firstNameEN' => $studentData['personalInfo']['firstNameEN'] ?? null,
            'lastNameEN' => $studentData['personalInfo']['lastNameEN'] ?? null,
            'nationality' => $studentData['personalInfo']['nationality'] ?? null,
            'dateOfBirth' => $studentData['personalInfo']['dateOfBirth'] ?? null,
            'placeOfBirth' => $studentData['personalInfo']['placeOfBirth'] ?? null,
            'address' => $studentData['personalInfo']['address'] ?? null,
            'motherStatus' => $studentData['personalInfo']['motherStatus'] ?? null,
            'fatherStatus' => $studentData['personalInfo']['fatherStatus'] ?? null,
            
            // Medical info
            'bloodType' => $studentData['medicalInfo']['bloodType'] ?? null,
            'hasDisease' => $studentData['medicalInfo']['hasDisease'] ?? null,
            'allergies' => $studentData['medicalInfo']['allergies'] ?? null,
            'diseaseCauses' => $studentData['medicalInfo']['diseaseCauses'] ?? null,
            
            // Guardian info
            'guardianFirstName' => $guardianData['personalInfo']['firstName'] ?? null,
            'guardianLastName' => $guardianData['personalInfo']['lastName'] ?? null,
            'guardianDob' => $guardianData['personalInfo']['dateOfBirth'] ?? null,
            'relationship' => $guardianData['personalInfo']['relationship'] ?? null,
            
            // Guardian account
            'guardianUsername' => $guardianData['account']['username'] ?? null,
            'guardianPassword' => $guardianData['account']['password'] ?? null,
            'guardianImage' => $guardianData['contactInfo']['imagePath'] ?? null,
            
            // Subscription info
            'enrollmentDate' => $studentData['subscriptionInfo']['enrollmentDate'] ?? null,
            'exitDate' => $studentData['subscriptionInfo']['exitDate'] ?? null,
            'exitReason' => $studentData['subscriptionInfo']['exitReason'] ?? null,
            'isExempt' => $studentData['subscriptionInfo']['isExempt'] ?? null,
            'exemptionPercent' => $studentData['subscriptionInfo']['exemptionPercent'] ?? null,
            'exemptionReason' => $studentData['subscriptionInfo']['exemptionReason'] ?? null,
            
            // Education info
            'schoolName' => $studentData['educationInfo']['schoolName'] ?? null,
            'schoolType' => $studentData['educationInfo']['schoolType'] ?? null,
            'grade' => $studentData['educationInfo']['grade'] ?? null,
            'academicLevel' => $studentData['educationInfo']['academicLevel'] ?? null
        ];

        // Call add_student with all fields
        add_student(
            $conn,
            // Required fields
            $requiredFields['firstNameAR'],
            $requiredFields['lastNameAR'],
            $requiredFields['sex'],
            $requiredFields['username'],
            $requiredFields['password'],
            $requiredFields['phoneNumber'],
            $requiredFields['emailAddress'],
            
            // Optional personal info (including parent status)
            $optionalFields['firstNameEN'],
            $optionalFields['lastNameEN'],
            $optionalFields['nationality'],
            $optionalFields['dateOfBirth'],
            $optionalFields['placeOfBirth'],
            $optionalFields['address'],
            $optionalFields['motherStatus'],
            $optionalFields['fatherStatus'],
            
            // Medical info
            $optionalFields['bloodType'],
            $optionalFields['hasDisease'],
            $optionalFields['allergies'],
            $optionalFields['diseaseCauses'],
            
            // Guardian info
            $optionalFields['guardianFirstName'],
            $optionalFields['guardianLastName'],
            $optionalFields['guardianDob'],
            $optionalFields['relationship'],
            
            // Guardian account
            $optionalFields['guardianUsername'],
            $optionalFields['guardianPassword'],
            $optionalFields['guardianImage'],
            
            // Subscription info
            $optionalFields['enrollmentDate'],
            $optionalFields['exitDate'],
            $optionalFields['exitReason'],
            $optionalFields['isExempt'],
            $optionalFields['exemptionPercent'],
            $optionalFields['exemptionReason'],
            
            // Education info
            $optionalFields['schoolName'],
            $optionalFields['schoolType'],
            $optionalFields['grade'],
            $optionalFields['academicLevel']
        );

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data format']);
}
?>