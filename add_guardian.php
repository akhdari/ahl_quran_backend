<?php
include_once './connect.php';
$conn-> begin_transaction();
function add_guardian($conn){
    try {
        $username = "ahmedbr234";
        $email= "boRaS@gmail.com";
        $phone_number = "0665123456";
        $_passcode = "p5GDnb#3";
        $_profile_image = null;
        $job= "lawyer";
        $home_address = null;
        $first_name = "ahmed";
        $last_name = "bouras";
        $date_of_birth = "2000-01-01";
        $relationship = "uncle";
              //concact_info
             $stmt = $conn->prepare("INSERT INTO contact_info ( email, phone_number) VALUES ( ?, ?)");
             $stmt->bind_param("ss", $email, $phone_number);
             $stmt->execute();
             $contact_id = $conn->insert_id;
             $stmt->close();

       //account_info
       $stmt = $conn->prepare("INSERT INTO account_info (username, passcode, profile_image) VALUES (?, ?, ?)");
       $stmt->bind_param("sss", $username, $_passcode, $_profile_image);
       $stmt->execute();
       $account_id = $conn->insert_id;
       $stmt->close();

       //guardian_info
       $stmt= $conn->prepare('INSERT INTO guardian (first_name, last_name, date_of_birth, relationship, home_address, job,  guardian_contact_id, guardian_account_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
       $stmt->bind_param("ssssssii", $first_name, $last_name, $date_of_birth, $relationship,$home_address, $job, $contact_id, $account_id);  
       $stmt->execute();
       $stmt->close();
       mysqli_commit($conn);
       echo "Transaction successful!";
    } catch (\Throwable $th) {
        $conn->rollback();
        echo "error msg: ".$conn-> error;
        echo "error code: ".$conn-> errno;
        throw $th;
    }
    $conn->close();
}
add_guardian($conn);
?>