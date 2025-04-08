<?php
include_once './connect.php';
function add_lecture($conn)
{

    $student_id = 1;
    $day_of_week = "Monday";
    $start_time = "11:00:00";
    $end_time = "12:00:00";
    $lecture_id = 1;
    $teachers_id = [1, 2];
    $student_id = 18;
    $student_attendance_status = "present";
    $teacher_attendance_status = "present";
    $conn->begin_transaction();
    //set db connection to transaction state 
    //should not be inside the try bloc , bcs if so the transaction will not rolle back if anything accurs in the try bloc 

    try {
        //weekly_schedule 
        $stmt = $conn->prepare('INSERT INTO weekly_schedule (day_of_week, start_time, end_time) VALUES (?, ?, ?)');
        $stmt->bind_param("sss", $day_of_week, $start_time, $end_time);
        $stmt->execute();
        $weekly_schedule_id = $conn->insert_id;
        $stmt->close();
        
        //insert new lecture if it doesn't exist
        /*$stmt = $conn->prepare("INSERT INTO lecture (lecture_id, lecture_name_ar, lecture_name_en, circle_type, shown_on_website, lecture_start_time, lecture_end_time) VALUES (NULL)");
        $stmt->execute();
        $lecture_id = $conn->insert_id; 
        $stmt->close();*/
        //lecture_teacher
        $stmt = $conn->prepare("UPDATE lecture_teacher SET teacher_id=?,  teacher_attendance_status=? WHERE lecture_id=?");
        $stmt->bind_param("iis", $lecture_id, $teachers_id, $teacher_attendance_status);
        foreach ($teachers_id as $teacher_id) {
            $stmt->execute();
        }
        $stmt->close();
        //lecture_student
        $stmt = $conn->prepare("INSERT INTO lecture_student (lecture_id, student_id, attendance_status) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $lecture_id, $student_id, $student_attendance_status);
        $stmt->execute();
        $stmt->close();//close the statement
        $conn->commit();
        echo "Transaction successful!";
    } catch (Exception $e) {
        $conn->rollBack();
        echo "error msg:" . $conn->error;
        echo "error code:" . $conn->errno;

        throw $e;
    }
    mysqli_close($conn);



}
add_lecture($conn);
?>