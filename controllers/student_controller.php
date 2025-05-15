<?php
include_once '../models/student_model.php';
include_once '../helpers/db_helpers.php';
include_once '../db.php';

class StudentController {
    private $model;
    
    public function __construct($conn) {
        $this->model = new StudentModel($conn);
    }
    
    public function handleDeleteStudent() {
        $data = get_data();
        if (!$data || empty($data['id'])) {
            throw new InvalidArgumentException('Student ID is required', 400);
        }
        
        if (!$this->model->deleteStudent($data['id'])) {
            throw new RuntimeException('Student not found or could not be deleted', 404);
        }
        
        return ['success' => true, 'message' => 'Student deleted successfully'];
    }
    
    public function handleCreateStudent() {
        $data = get_data();
        if (!$data) {
            throw new InvalidArgumentException('Invalid or missing data format', 400);
        }
        
        $studentData = $this->validateStudentData($data);
        $result = $this->model->addStudent($studentData);
        
        if (!$result['success']) {
            throw new RuntimeException($result['message'], 500);
        }
        
        return $result;
    }
    
    public function handleGetStudentInfo() {
        $data = $this->model->getStudentInfo();
        return ['success' => true, 'data' => $data];
    }
    
    private function validateStudentData($data) {
        $student = $data['student'] ?? [];
        $guardian = $data['guardian'] ?? [];
        
        // Required fields validation
        $requiredFields = [
            'student.firstNameAR' => $student['personalInfo']['firstNameAR'] ?? null,
            'student.lastNameAR' => $student['personalInfo']['lastNameAR'] ?? null,
            'student.sex' => $student['personalInfo']['sex'] ?? null,
            'student.username' => $student['account']['username'] ?? null,
            'student.password' => $student['account']['password'] ?? null,
            'student.phoneNumber' => $student['contactInfo']['phoneNumber'] ?? null,
            'student.emailAddress' => $student['contactInfo']['emailAddress'] ?? null
        ];
        
        foreach ($requiredFields as $field => $value) {
            if (empty($value)) {
                throw new InvalidArgumentException("Required field '$field' is missing or empty", 400);
            }
        }
        
        // Prepare structured data for model
        return [
            'student' => [
                'personalInfo' => [
                    'firstNameAR' => $student['personalInfo']['firstNameAR'],
                    'lastNameAR' => $student['personalInfo']['lastNameAR'],
                    'sex' => $student['personalInfo']['sex'],
                    'firstNameEN' => $student['personalInfo']['firstNameEN'] ?? null,
                    'lastNameEN' => $student['personalInfo']['lastNameEN'] ?? null,
                    'nationality' => $student['personalInfo']['nationality'] ?? null,
                    'dateOfBirth' => $student['personalInfo']['dateOfBirth'] ?? null,
                    'placeOfBirth' => $student['personalInfo']['placeOfBirth'] ?? null,
                    'address' => $student['personalInfo']['address'] ?? null,
                    'motherStatus' => $student['personalInfo']['motherStatus'] ?? null,
                    'fatherStatus' => $student['personalInfo']['fatherStatus'] ?? null
                ],
                'medicalInfo' => [
                    'bloodType' => $student['medicalInfo']['bloodType'] ?? null,
                    'hasDisease' => $student['medicalInfo']['hasDisease'] ?? null,
                    'allergies' => $student['medicalInfo']['allergies'] ?? null,
                    'diseaseCauses' => $student['medicalInfo']['diseaseCauses'] ?? null
                ],
                'account' => [
                    'username' => $student['account']['username'],
                    'password' => $student['account']['password']
                ],
                'contactInfo' => [
                    'phoneNumber' => $student['contactInfo']['phoneNumber'],
                    'emailAddress' => $student['contactInfo']['emailAddress']
                ],
                'subscriptionInfo' => [
                    'enrollmentDate' => $student['subscriptionInfo']['enrollmentDate'] ?? null,
                    'exitDate' => $student['subscriptionInfo']['exitDate'] ?? null,
                    'exitReason' => $student['subscriptionInfo']['exitReason'] ?? null,
                    'isExempt' => $student['subscriptionInfo']['isExempt'] ?? null,
                    'exemptionPercent' => $student['subscriptionInfo']['exemptionPercent'] ?? null,
                    'exemptionReason' => $student['subscriptionInfo']['exemptionReason'] ?? null
                ],
                'educationInfo' => [
                    'schoolName' => $student['educationInfo']['schoolName'] ?? null,
                    'schoolType' => $student['educationInfo']['schoolType'] ?? null,
                    'grade' => $student['educationInfo']['grade'] ?? null,
                    'academicLevel' => $student['educationInfo']['academicLevel'] ?? null
                ],
                'sessions' => $student['sessions'] ?? []
            ],
            'guardian' => [
                'personalInfo' => [
                    'guardianId' => $guardian['personalInfo']['guardianId'] ?? null,
                    'firstName' => $guardian['personalInfo']['firstName'] ?? null,
                    'lastName' => $guardian['personalInfo']['lastName'] ?? null,
                    'dateOfBirth' => $guardian['personalInfo']['dateOfBirth'] ?? null,
                    'relationship' => $guardian['personalInfo']['relationship'] ?? null
                ],
                'account' => [
                    'username' => $guardian['account']['username'] ?? null,
                    'password' => $guardian['account']['password'] ?? null,
                    'imagePath' => $guardian['contactInfo']['imagePath'] ?? null
                ]
            ]
        ];
    }
}