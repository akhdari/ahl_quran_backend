<?php
include_once '../helpers/db_helpers.php';
include_once __DIR__ . '/../DuplicateCheckerTrait.php';
class GuardianModel
{
    use DuplicateCheckerTrait;
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getGuardianInfo()
    {
        $query = "SELECT 
                guardian.guardian_id,
                guardian.last_name, 
                guardian.first_name,  
                guardian.date_of_birth, 
                guardian.relationship,
                contact_info.phone_number, 
                contact_info.email, 
                guardian_account.username AS guardian_account, 
                GROUP_CONCAT(student_account.username SEPARATOR ', ') AS children
            FROM guardian
            LEFT JOIN contact_info ON guardian.guardian_contact_id = contact_info.contact_id
            LEFT JOIN account_info AS guardian_account ON guardian.guardian_account_id = guardian_account.account_id
            LEFT JOIN student ON student.guardian_id = guardian.guardian_id
            LEFT JOIN account_info AS student_account ON student.student_account_id = student_account.account_id
            GROUP BY guardian.guardian_id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new RuntimeException("Database query failed: " . $this->conn->error);
        }

        $data = convertResultToArray($result);
        $result->free();
        return $data;
    }

    public function getGuardianAccounts()
    {
        $query = "SELECT 
                username AS name, 
                guardian_id AS id 
            FROM account_info 
            INNER JOIN guardian ON guardian_account_id = account_id";

        $result = $this->conn->query($query);
        if (!$result) {
            throw new RuntimeException("Database query failed: " . $this->conn->error);
        }

        $data = convertResultToArray($result);
        $result->free();
        return $data;
    }

    public function deleteGuardian($guardian_id)
    {
        try {
            $this->conn->begin_transaction();

            // Step 1: Get guardian's account_id and contact_id
            $stmt = $this->conn->prepare("
            SELECT guardian_account_id, guardian_contact_id 
            FROM guardian 
            WHERE guardian_id = ?");
            if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
            $stmt->bind_param("i", $guardian_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 0) {
                $stmt->close();
                $this->conn->rollback();
                return false; // guardian not found
            }
            $row = $result->fetch_assoc();
            $account_id = $row['guardian_account_id'];
            $contact_id = $row['guardian_contact_id'];
            $stmt->close();

            // Step 2: Delete guardian
            $stmt = $this->conn->prepare("DELETE FROM guardian WHERE guardian_id = ?");
            if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
            $stmt->bind_param("i", $guardian_id);
            if (!$stmt->execute()) throw new RuntimeException("Execute failed: " . $stmt->error);
            if ($stmt->affected_rows === 0) {
                $stmt->close();
                $this->conn->rollback();
                return false; // guardian not found (should not happen here)
            }
            $stmt->close();

            // Step 3: Check if account_id is orphaned (not used by any guardian/student/teacher)
            $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS count FROM (
                SELECT guardian_account_id AS id FROM guardian WHERE guardian_account_id = ? 
                UNION ALL
                SELECT student_account_id AS id FROM student WHERE student_account_id = ? 
                UNION ALL
                SELECT teacher_account_id AS id FROM teacher WHERE teacher_account_id = ? 
            ) AS accounts WHERE id = ?");
            if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
            $stmt->bind_param("iiii", $account_id, $account_id, $account_id, $account_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();

            if ($count == 0) {
                // Delete account_info if orphaned
                $stmt = $this->conn->prepare("DELETE FROM account_info WHERE account_id = ?");
                if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
                $stmt->bind_param("i", $account_id);
                if (!$stmt->execute()) throw new RuntimeException("Execute failed: " . $stmt->error);
                $stmt->close();
            }

            // Step 4: Check if contact_id is orphaned (not used by any guardian/student/teacher)
            $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS count FROM (
                SELECT guardian_contact_id AS id FROM guardian WHERE guardian_contact_id = ? 
                UNION ALL
                SELECT student_contact_id AS id FROM student WHERE student_contact_id = ? 
                UNION ALL
                SELECT teacher_contact_id AS id FROM teacher WHERE teacher_contact_id = ? 
            ) AS contacts WHERE id = ?");
            if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
            $stmt->bind_param("iiii", $contact_id, $contact_id, $contact_id, $contact_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $count = $result->fetch_assoc()['count'];
            $stmt->close();

            if ($count == 0) {
                // Delete contact_info if orphaned
                $stmt = $this->conn->prepare("DELETE FROM contact_info WHERE contact_id = ?");
                if (!$stmt) throw new RuntimeException("Prepare failed: " . $this->conn->error);
                $stmt->bind_param("i", $contact_id);
                if (!$stmt->execute()) throw new RuntimeException("Execute failed: " . $stmt->error);
                $stmt->close();
            }

            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error deleting guardian: " . $e->getMessage());
            throw $e;
        }
    }



    public function addGuardian($data)
    {
        try {
            $this->conn->begin_transaction();

            // Check for duplicates
            $this->checkForDuplicates($data['username'], $data['email']);

            // Insert contact info
            $stmt = $this->conn->prepare("INSERT INTO contact_info (email, phone_number) VALUES (?, ?)");
            $stmt->bind_param("ss", $data['email'], $data['phone_number']);
            $stmt->execute();
            $contact_id = $this->conn->insert_id;
            $stmt->close();

            // Insert account info
            $stmt = $this->conn->prepare("INSERT INTO account_info (username, passcode, profile_image) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data['username'], $data['passcode'], $data['profile_image']);
            $stmt->execute();
            $account_id = $this->conn->insert_id;
            $stmt->close();

            // Insert guardian
            $stmt = $this->conn->prepare("INSERT INTO guardian (
                first_name, last_name, date_of_birth, relationship, 
                home_address, job, guardian_contact_id, guardian_account_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

            $stmt->bind_param(
                "ssssssii",
                $data['first_name'],
                $data['last_name'],
                $data['date_of_birth'],
                $data['relationship'],
                $data['home_address'],
                $data['job'],
                $contact_id,
                $account_id
            );

            $stmt->execute();
            $stmt->close();

            $this->conn->commit();
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }


}
