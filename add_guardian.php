<?php
include_once './connect.php';

function add_guardian(
    $conn,
    $username,
    $email,
    $phone_number,
    $passcode,
    $profile_image,
    $job,
    $home_address,
    $first_name,
    $last_name,
    $date_of_birth,
    $relationship
) {
    try {
        $conn->begin_transaction();

        // Contact Info
        $stmt = $conn->prepare("INSERT INTO contact_info (email, phone_number) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $phone_number);
        $stmt->execute();
        $contact_id = $conn->insert_id;
        $stmt->close();

        // Account Info
        $stmt = $conn->prepare("INSERT INTO account_info (username, passcode, profile_image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $passcode, $profile_image);
        $stmt->execute();
        $account_id = $conn->insert_id;
        $stmt->close();

        // Guardian Info
        $stmt = $conn->prepare("INSERT INTO guardian (first_name, last_name, date_of_birth, relationship, home_address, job, guardian_contact_id, guardian_account_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssii", $first_name, $last_name, $date_of_birth, $relationship, $home_address, $job, $contact_id, $account_id);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

    } catch (\Throwable $th) {
        $conn->rollback();
        throw $th; // let main file handle the error
    } finally {
        $conn->close();
    }
}
