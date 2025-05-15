<?php
include_once '../helpers/db_helpers.php';
include_once __DIR__ . '/../DuplicateCheckerTrait.php';

class StudentModel
{
    use DuplicateCheckerTrait;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function addStudent($data)
    {
        $this->conn->begin_transaction();

        try {
            $this->checkForDuplicates(
                $data['student']['account']['username'],
                $data['student']['contactInfo']['emailAddress']
            );

            if (!empty($data['guardian']['account']['username'])) {
                $this->checkForDuplicates(
                    $data['guardian']['account']['username'],
                    $data['guardian']['contactInfo']['emailAddress']
                );
            }

            $studentId = $this->insertBasicStudentRecord();
            $this->insertPersonalInfo($studentId, $data['student']['personalInfo']);
            $this->insertMedicalInfo($studentId, $data['student']['medicalInfo']);

            $accountId = $this->insertAccount(
                $data['student']['account']['username'],
                $data['student']['account']['password']
            );

            $contactId = $this->insertContactInfo(
                $data['student']['contactInfo']['emailAddress'],
                $data['student']['contactInfo']['phoneNumber']
            );

            $this->updateStudentWithAccountAndContact($studentId, $accountId, $contactId);

            $guardianId = $this->handleGuardian($studentId, $data['guardian']);
            $this->handleEducationInfo($studentId, $data['student']['educationInfo']);
            $this->handleSubscriptionInfo($studentId, $data['student']['subscriptionInfo']);
            $this->handleSessions($studentId, $data['student']['sessions']);

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Student added successfully',
                'student_id' => $studentId,
                'guardian_id' => $guardianId
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Failed to add student: ' . $e->getMessage()
            ];
        }
    }
    public function deleteStudent($studentId)
    {
        try {
            $this->conn->begin_transaction();

            
            $stmt = $this->conn->prepare("SELECT student_account_id FROM student WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                $this->conn->rollback();
                return false;
            }
            $student = $result->fetch_assoc();
            $accountId = $student['student_account_id'];
            $stmt->close();

            
            $stmt = $this->conn->prepare("DELETE FROM student WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->close();

          
            $stmt = $this->conn->prepare("DELETE FROM personal_info WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->close();

            $stmt = $this->conn->prepare("DELETE FROM lecture_student WHERE student_id = ?");
            $stmt->bind_param("i", $studentId);
            $stmt->execute();
            $stmt->close();

            // Delete orphan account if not used by others
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM student WHERE student_account_id = ?");
            $stmt->bind_param("i", $accountId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result['count'] == 0) {
                $stmt = $this->conn->prepare("DELETE FROM account_info WHERE account_id = ?");
                $stmt->bind_param("i", $accountId);
                $stmt->execute();
                $stmt->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error deleting student: " . $e->getMessage());
            throw $e;
        }
    }


    public function getStudentInfo()
    {
        $query = "
            SELECT 
                student.student_id,
                personal_info.first_name_ar, 
                personal_info.last_name_ar, 
                personal_info.sex, 
                personal_info.date_of_birth,
                personal_info.place_of_birth,
                personal_info.nationality,
                GROUP_CONCAT(DISTINCT lecture.lecture_name_ar SEPARATOR ', ') AS lectures,
                account_info.username
            FROM student 
            LEFT JOIN personal_info ON student.student_id = personal_info.student_id
            LEFT JOIN account_info ON student.student_account_id = account_info.account_id
            LEFT JOIN lecture_student ON student.student_id = lecture_student.student_id  
            LEFT JOIN lecture ON lecture_student.lecture_id = lecture.lecture_id
            GROUP BY student.student_id
        ";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new RuntimeException("Query failed: " . $this->conn->error);
        }

        $data = convertResultToArray($result);
        $result->free();
        return $data;
    }

    private function insertBasicStudentRecord()
    {
        $stmt = $this->conn->prepare("INSERT INTO student (guardian_id) VALUES (NULL)");
        $stmt->execute();
        $studentId = $this->conn->insert_id;
        $stmt->close();
        return $studentId;
    }

    private function insertPersonalInfo($studentId, $personalInfo)
    {
        $fields = [
            'student_id' => $studentId,
            'first_name_ar' => $personalInfo['firstNameAR'],
            'last_name_ar' => $personalInfo['lastNameAR'],
            'sex' => $personalInfo['sex']
        ];

        $optionalFields = [
            'first_name_en' => $personalInfo['firstNameEN'],
            'last_name_en' => $personalInfo['lastNameEN'],
            'nationality' => $personalInfo['nationality'],
            'date_of_birth' => $personalInfo['dateOfBirth'],
            'place_of_birth' => $personalInfo['placeOfBirth'],
            'home_address' => $personalInfo['address'],
            'mother_status' => $personalInfo['motherStatus'],
            'father_status' => $personalInfo['fatherStatus']
        ];

        $sql = "INSERT INTO personal_info (";
        $sql .= implode(", ", array_keys($fields));
        $placeholders = "VALUES (?" . str_repeat(", ?", count($fields) - 1);
        $types = "isss"; // student_id (i), then strings (s)
        $values = array_values($fields);

        foreach ($optionalFields as $field => $value) {
            if ($value !== null) {
                $sql .= ", $field";
                $placeholders .= ", ?";
                $types .= "s";
                $values[] = $value;
            }
        }

        $sql .= ") $placeholders)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $stmt->close();
    }

    private function insertMedicalInfo($studentId, $medicalInfo)
    {
        if (
            $medicalInfo['bloodType'] !== null ||
            $medicalInfo['hasDisease'] !== null ||
            $medicalInfo['allergies'] !== null ||
            $medicalInfo['diseaseCauses'] !== null
        ) {

            $stmt = $this->conn->prepare("
                INSERT INTO medical_info 
                (student_id, blood_type, diseases, allergies, diseases_causes) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "issss",
                $studentId,
                $medicalInfo['bloodType'],
                $medicalInfo['hasDisease'],
                $medicalInfo['allergies'],
                $medicalInfo['diseaseCauses']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    private function insertAccount($username, $password)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO account_info 
            (username, passcode, profile_image) 
            VALUES (?, ?, NULL)
        ");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $accountId = $this->conn->insert_id;
        $stmt->close();
        return $accountId;
    }

    private function insertContactInfo($email, $phone)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO contact_info 
            (email, phone_number) 
            VALUES (?, ?)
        ");
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $contactId = $this->conn->insert_id;
        $stmt->close();
        return $contactId;
    }

    private function updateStudentWithAccountAndContact($studentId, $accountId, $contactId)
    {
        $stmt = $this->conn->prepare("
            UPDATE student SET 
                student_account_id = ?, 
                student_contact_id = ? 
            WHERE student_id = ?
        ");
        $stmt->bind_param("iii", $accountId, $contactId, $studentId);
        $stmt->execute();
        $stmt->close();
    }

    private function handleGuardian($studentId, $guardianData)
    {
        if (
            $guardianData['personalInfo']['firstName'] === null &&
            $guardianData['personalInfo']['lastName'] === null
        ) {
            return null;
        }

        // Insert guardian basic info
        $stmt = $this->conn->prepare("
            INSERT INTO guardian 
            (first_name, last_name, date_of_birth, relationship) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param(
            "ssss",
            $guardianData['personalInfo']['firstName'],
            $guardianData['personalInfo']['lastName'],
            $guardianData['personalInfo']['dateOfBirth'],
            $guardianData['personalInfo']['relationship']
        );
        $stmt->execute();
        $guardianId = $this->conn->insert_id;
        $stmt->close();

        // Handle guardian account if exists
        if ($guardianData['account']['username'] !== null) {
            $accountId = $this->insertAccount(
                $guardianData['account']['username'],
                $guardianData['account']['password']
            );

            $stmt = $this->conn->prepare("
                UPDATE guardian SET 
                    guardian_account_id = ? 
                WHERE guardian_id = ?
            ");
            $stmt->bind_param("ii", $accountId, $guardianId);
            $stmt->execute();
            $stmt->close();
        }

        // Update student with guardian ID
        $stmt = $this->conn->prepare("
            UPDATE student SET 
                guardian_id = ? 
            WHERE student_id = ?
        ");
        $stmt->bind_param("ii", $guardianId, $studentId);
        $stmt->execute();
        $stmt->close();

        return $guardianId;
    }

    private function handleEducationInfo($studentId, $educationInfo)
    {
        if ($educationInfo['schoolName'] !== null || $educationInfo['schoolType'] !== null) {
            $stmt = $this->conn->prepare("
                INSERT INTO formal_education_info 
                (student_id, school_name, school_type, grade, academic_level) 
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "issss",
                $studentId,
                $educationInfo['schoolName'],
                $educationInfo['schoolType'],
                $educationInfo['grade'],
                $educationInfo['academicLevel']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    private function handleSubscriptionInfo($studentId, $subscriptionInfo)
    {
        if ($subscriptionInfo['enrollmentDate'] !== null) {
            $stmt = $this->conn->prepare("
                INSERT INTO subscription_info 
                (student_id, enrollment_date, exit_date, exit_reason, 
                 is_exempt_from_payment, exemption_percentage, exemption_reason) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "isssidi",
                $studentId,
                $subscriptionInfo['enrollmentDate'],
                $subscriptionInfo['exitDate'],
                $subscriptionInfo['exitReason'],
                $subscriptionInfo['isExempt'],
                $subscriptionInfo['exemptionPercent'],
                $subscriptionInfo['exemptionReason']
            );
            $stmt->execute();
            $stmt->close();
        }
    }

    private function handleSessions($studentId, $sessions)
    {
        if (!empty($sessions)) {
            $stmt = $this->conn->prepare("
                INSERT INTO lecture_student 
                (student_id, lecture_id) 
                VALUES (?, ?)
            ");

            foreach ($sessions as $sessionId) {
                $stmt->bind_param("ii", $studentId, $sessionId);
                $stmt->execute();
            }

            $stmt->close();
        }
    }
}
