<?php
include_once '../helpers/db_helpers.php';
include_once __DIR__ . '/../DuplicateCheckerTrait.php';


class TeacherModel
{
    use DuplicateCheckerTrait;

    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function addTeacher($data)
    {
        $this->conn->begin_transaction();

        try {
            // Check for duplicates
            $this->checkForDuplicates(
                $data['account']['username'],
                $data['contactInfo']['email']
            );

            // Insert account info
            $accountId = $this->insertAccount(
                $data['account']['username'],
                $data['account']['password']
            );

            // Insert contact info
            $contactId = $this->insertContactInfo(
                $data['contactInfo']['email'],
                $data['contactInfo']['phone']
            );

            // Insert teacher
            $stmt = $this->conn->prepare("
                INSERT INTO teacher (
                    first_name, 
                    last_name, 
                    date_of_birth,
                    specialization,
                    teacher_account_id,
                    teacher_contact_id
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssssii",
                $data['personalInfo']['firstName'],
                $data['personalInfo']['lastName'],
                $data['personalInfo']['dateOfBirth'],
                $data['personalInfo']['specialization'],
                $accountId,
                $contactId
            );
            $stmt->execute();
            $teacherId = $this->conn->insert_id;
            $stmt->close();

            $this->conn->commit();

            return [
                'success' => true,
                'message' => 'Teacher added successfully',
                'teacher_id' => $teacherId
            ];
        } catch (Exception $e) {
            $this->conn->rollback();
            return [
                'success' => false,
                'message' => 'Failed to add teacher: ' . $e->getMessage()
            ];
        }
    }
    
    public function getTeachers() {
        $query = "SELECT 
                    teacher_id AS id, 
                    CONCAT(first_name, ' ', last_name) AS name 
                  FROM teacher";
        
        $result = $this->conn->query($query);
        if (!$result) {
            throw new RuntimeException("Database query failed: " . $this->conn->error);
        }
        
        $data = convertResultToArray($result);
        $result->free();
        return $data;
    }

    public function deleteTeacher($teacherId) {
        try {
            $this->conn->begin_transaction();

            $stmt = $this->conn->prepare("SELECT teacher_account_id, teacher_contact_id FROM teacher WHERE teacher_id = ?");
            $stmt->bind_param("i", $teacherId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                $this->conn->rollback();
                return false;
            }
            $teacher = $result->fetch_assoc();
            $accountId = $teacher['teacher_account_id'];
            $contactId = $teacher['teacher_contact_id'];
            $stmt->close();

            // Delete teacher
            $stmt = $this->conn->prepare("DELETE FROM teacher WHERE teacher_id = ?");
            $stmt->bind_param("i", $teacherId);
            $stmt->execute();
            $stmt->close();

            // Delete orphaned account_info
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM teacher WHERE teacher_account_id = ?");
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

            // Delete orphaned contact_info
            $stmt = $this->conn->prepare("SELECT COUNT(*) AS count FROM teacher WHERE teacher_contact_id = ?");
            $stmt->bind_param("i", $contactId);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if ($result['count'] == 0) {
                $stmt = $this->conn->prepare("DELETE FROM contact_info WHERE contact_id = ?");
                $stmt->bind_param("i", $contactId);
                $stmt->execute();
                $stmt->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error deleting teacher: " . $e->getMessage());
            throw $e;
        }
    }

    public function getTeacherInfo()
    {
        $query = "SELECT 
                    t.teacher_id,
                    t.first_name,
                    t.last_name,
                    t.date_of_birth,
                    t.specialization,
                    ci.email,
                    ci.phone_number,
                    ai.username
                  FROM teacher t
                  LEFT JOIN contact_info ci ON t.teacher_contact_id = ci.contact_id
                  LEFT JOIN account_info ai ON t.teacher_account_id = ai.account_id";
        
        $result = $this->conn->query($query);
        if (!$result) {
            throw new RuntimeException("Database query failed: " . $this->conn->error);
        }
        
        $data = convertResultToArray($result);
        $result->free();
        return $data;
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
}