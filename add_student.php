
<?php
include_once './connect.php';
function add_student(
    $conn,
    // Required fields
    $first_name_ar, $last_name_ar, $sex, 
    $username, $password, $phone_number, $email_address,
    
    // Optional personal info (including parent status)
    $first_name_en = null, $last_name_en = null, $nationality = null,
    $date_of_birth = null, $place_of_birth = null, $address = null,
    $mother_status = null, $father_status = null,
    
    // Medical info
    $blood_type = null, $has_disease = null, $allergies = null, $disease_causes = null,
    
    // Guardian info
    $guardian_first_name = null, $guardian_last_name = null, 
    $guardian_date_of_birth = null, $relationship = null,
    
    // Guardian account
    $guardian_username = null, $guardian_password = null, $guardian_image = null,
    
    // Subscription info
    $enrollment_date = null, $exit_date = null, $exit_reason = null,
    $is_exempt = null, $exemption_percent = null, $exemption_reason = null,
    
    // Education info
    $school_name = null, $school_type = null, $grade = null, $academic_level = null
) {
    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Insert basic student record (always required)
        $stmt = $conn->prepare("INSERT INTO student (student_id) VALUES (NULL)");
        $stmt->execute();
        $student_id = $conn->insert_id;
        $stmt->close();

        // 2. Insert personal info (required + optional fields including parent status)
        $personal_fields = [
            'student_id' => $student_id,
            'first_name_ar' => $first_name_ar,
            'last_name_ar' => $last_name_ar,
            'sex' => $sex
        ];
        
        $optional_personal = [
            'first_name_en' => $first_name_en,
            'last_name_en' => $last_name_en,
            'nationality' => $nationality,
            'date_of_birth' => $date_of_birth,
            'place_of_birth' => $place_of_birth,
            'home_address' => $address,
            'mother_status' => $mother_status,
            'father_status' => $father_status
        ];

        $personal_sql = "INSERT INTO personal_info (";
        $personal_sql .= implode(", ", array_keys($personal_fields));
        
        $placeholders = "VALUES (?" . str_repeat(", ?", count($personal_fields) - 1);
        $types = "isss"; // student_id (i), then strings (s)

        // Add optional fields if they exist
        foreach ($optional_personal as $field => $value) {
            if ($value !== null) {
                $personal_sql .= ", $field";
                $placeholders .= ", ?";
                $types .= "s";
                $personal_fields[$field] = $value;
            }
        }

        $personal_sql .= ") $placeholders)";
        
        $stmt = $conn->prepare($personal_sql);
        $stmt->bind_param($types, ...array_values($personal_fields));
        $stmt->execute();
        $stmt->close();

        // 3. Insert medical info (only if provided)
        if ($blood_type !== null || $has_disease !== null || $allergies !== null || $disease_causes !== null) {
            $stmt = $conn->prepare("INSERT INTO medical_info 
                (student_id, blood_type, diseases, allergies, diseases_causes) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $student_id, $blood_type, $has_disease, $allergies, $disease_causes);
            $stmt->execute();
            $stmt->close();
        }

        // 4. Insert student account (required)
        $stmt = $conn->prepare("INSERT INTO account_info 
            (username, passcode, profile_image) 
            VALUES (?, ?, NULL)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $student_account_id = $conn->insert_id;
        $stmt->close();

        // 5. Insert student contact info (required)
        $stmt = $conn->prepare("INSERT INTO contact_info 
            (email, phone_number) 
            VALUES (?, ?)");
        $stmt->bind_param("ss", $email_address, $phone_number);
        $stmt->execute();
        $student_contact_id = $conn->insert_id;
        $stmt->close();

        // 6. Update student with account and contact info (required)
        $stmt = $conn->prepare("UPDATE student SET 
            student_account_id = ?, 
            student_contact_id = ? 
            WHERE student_id = ?");
        $stmt->bind_param("iii", $student_account_id, $student_contact_id, $student_id);
        $stmt->execute();
        $stmt->close();

        // 7. Handle guardian if provided
        $guardian_id = null;
        if ($guardian_first_name !== null || $guardian_last_name !== null) {
            // Insert guardian basic info
            $stmt = $conn->prepare("INSERT INTO guardian 
                (first_name, last_name, date_of_birth, relationship) 
                VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", 
                $guardian_first_name, $guardian_last_name, 
                $guardian_date_of_birth, $relationship);
            $stmt->execute();
            $guardian_id = $conn->insert_id;
            $stmt->close();

            // Insert guardian account if provided
            if ($guardian_username !== null || $guardian_password !== null) {
                $stmt = $conn->prepare("INSERT INTO account_info 
                    (username, passcode, profile_image) 
                    VALUES (?, ?, ?)");
                $stmt->bind_param("sss", 
                    $guardian_username, $guardian_password, $guardian_image);
                $stmt->execute();
                $guardian_account_id = $conn->insert_id;
                $stmt->close();
            }

            // Update guardian with account info if exists
            if (isset($guardian_account_id)) {
                $stmt = $conn->prepare("UPDATE guardian SET 
                    guardian_account_id = ? 
                    WHERE guardian_id = ?");
                $stmt->bind_param("ii", $guardian_account_id, $guardian_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        // 8. Handle education info if provided
        if ($school_name !== null || $school_type !== null) {
            $stmt = $conn->prepare("INSERT INTO education_info 
                (student_id, school_name, school_type, grade, academic_level) 
                VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", 
                $student_id, $school_name, $school_type, $grade, $academic_level);
            $stmt->execute();
            $stmt->close();
        }

        // 9. Handle subscription info if provided
        if ($enrollment_date !== null) {
            $stmt = $conn->prepare("INSERT INTO subscription_info 
                (student_id, enrollment_date, exit_date, exit_reason, 
                 is_exempt, exemption_percent, exemption_reason) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssidi", 
                $student_id, $enrollment_date, $exit_date, $exit_reason,
                $is_exempt, $exemption_percent, $exemption_reason);
            $stmt->execute();
            $stmt->close();
        }

        // Commit transaction
        $conn->commit();
        echo json_encode([
            'success' => true, 
            'message' => 'Student added successfully',
            'student_id' => $student_id
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
?>