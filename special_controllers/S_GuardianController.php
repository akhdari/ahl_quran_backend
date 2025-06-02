<?php

require_once '../../special_controllers/Controller.php';

class S_GuardianController extends S_Controller
{
   
    public static function getAllGuardians()
    {
        $data = self::execQuery("SELECT
                        g.guardian_id AS id,
                        g.last_name AS lastName,
                        g.first_name AS firstName,
                        g.date_of_birth AS dateOfBirth,
                        g.relationship,
                        ci.phone_number,
                        ci.email,
                        COALESCE(ai.username, '') AS guardianAccount,
                        COALESCE(GROUP_CONCAT(CONCAT(COALESCE(pi.first_name_ar, ''), ' ', COALESCE(pi.last_name_ar, '')) ORDER BY pi.first_name_ar SEPARATOR ', '), '') AS children
                        FROM guardian g
                        LEFT JOIN contact_info ci ON g.guardian_contact_id = ci.contact_id
                        LEFT JOIN account_info ai ON g.guardian_account_id = ai.account_id
                        LEFT JOIN student s ON g.guardian_id = s.guardian_id
                        LEFT JOIN personal_info pi ON s.student_id = pi.student_id
                        GROUP BY
                        g.guardian_id, g.last_name, g.first_name, g.date_of_birth, g.relationship, ci.phone_number, ci.email, ai.username
                    ");           

        
        self::sendResponse(200, $data);
    }

    public static function saveNewGuardian()
    {
        $data = self::getRequestBody();
        if (!$data) {
            self::sendResponse(400, ['error' => 'Invalid JSON body']);
            return;
        }

        $conn = self::$dbconnection;

        try {
            $conn->connect()->begin_transaction();


            // 1. Insert account_info
            $account = $data['account_info'];
            $account = AccountInfo::create($conn, $account);

            // 5. Insert contact_info
            $contact = $data['contact_info'];
            $contact = ContactInfo::create($conn, $contact);

            $guardian = $data['info'];
            $guardian['guardian_account_id'] = $account->account_id;
            $guardian['guardian_contact_id'] = $contact->contact_id;
            $guardian = Guardian::create($conn, $guardian);


            // Commit all
            $conn->commit();
            self::sendResponse(201, ['message' => 'Guardian created successfully', 'guardian_id' =>  $guardian->guardian_id]);

        } catch (Exception $e) {
            $conn->rollback();
            self::sendResponse(500, ['error' => 'Transaction failed: ' . $e->getMessage()]);
        }
    }

}
