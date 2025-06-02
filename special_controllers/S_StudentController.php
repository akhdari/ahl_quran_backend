<?php

require_once '../../special_controllers/Controller.php';

class S_StudentController extends S_Controller
{
   
    public static function getAllStudents()
    {
        $data = self::execQuery("SELECT
                                    s.student_id AS id,
                                    pi.first_name_ar AS firstNameAr,
                                    pi.last_name_ar AS lastNameAr,
                                    pi.sex AS sex,
                                    pi.date_of_birth AS dateOfBirth,
                                    pi.place_of_birth AS placeOfBirth,
                                    pi.nationality AS nationality,
                                    ai.username AS username,
                                    GROUP_CONCAT(DISTINCT l.lecture_name_ar ORDER BY l.lecture_name_ar SEPARATOR ', ') AS lectures
                                FROM student s
                                LEFT JOIN personal_info pi ON s.student_id = pi.student_id
                                LEFT JOIN lecture_student ls ON s.student_id = ls.student_id
                                LEFT JOIN lecture l ON ls.lecture_id = l.lecture_id
                                LEFT JOIN account_info ai ON s.student_account_id = ai.account_id
                                GROUP BY
                                    s.student_id, pi.first_name_ar, pi.last_name_ar, pi.sex, pi.date_of_birth,
                                    pi.place_of_birth, pi.nationality, ai.username
                                ");

        
        self::sendResponse(200, $data);
    }

    public static function saveNewStudent()
    {
        $data = self::getRequestBody();
        if (!$data) {
            self::sendResponse(400, ['error' => 'Invalid JSON body']);
            return;
        }

        $conn = self::$dbconnection;

        try {
            $conn->connect()->begin_transaction();

            // 6. Insert guardian
            $guardian = $data['guardian'];


            // 1. Insert account_info
            $account = $data['accountInfo'];
            $account = AccountInfo::create($conn, $account);

            // 5. Insert contact_info
            $contact = $data['contactInfo'];
            $contact = ContactInfo::create($conn, $contact);


            // 2. Insert student (with student_account_id)
            $student = $data['student'];
            $student['guardian_id'] = $guardian['guardian_id'] ?? null; // Optional guardian_id
            $student['student_contact_id'] = $account->account_id; // Set the account_id from AccountInfo
            $student['student_account_id'] = $contact->contact_id; // Set the contact_id from ContactInfo
            $student = Student::create($conn, $student);

            // 3. Insert personal_info
            $personal = $data['personalInfo'];
            $personal['student_id'] = $student->student_id; // Set the student_id from Student
            PersonalInfo::create($conn, $personal);

            // 4. Insert medical_info
            $medical = $data['medicalInfo'];
            $medical['student_id'] = $student->student_id; // Set the student_id from Student
            MedicalInfo::create($conn, $medical);

            

            
            

            // 7. Insert formal_education_info
            $formalEdu = $data['formalEducationInfo'];
            $formalEdu['student_id'] = $student->student_id; // Set the student_id from Student
            FormalEducationInfo::create($conn, $formalEdu);

            // 8. Insert subscription_info
            $sub = $data['subscriptionInfo'];
            $sub['student_id'] = $student->student_id; // Set the student_id from Student
            $sub['is_exempt_from_payment'] = (int)$sub['is_exempt_from_payment'];
            SubscriptionInfo::create($conn, $sub);

            // 9. Insert lecture_student mapping
            foreach ($data['lectures'] as $lecture) {
               LectureStudent::create($conn, [
                    'student_id' => $student->student_id,
                    'lecture_id' => $lecture['lecture_id']
                ]);
            }

            // Commit all
            $conn->commit();
            self::sendResponse(201, ['message' => 'Student created successfully', 'student_id' =>  $student->student_id]);

        } catch (Exception $e) {
            $conn->rollback();
            self::sendResponse(500, ['error' => 'Transaction failed: ' . $e->getMessage()]);
        }
    }

}
