<?php
$query1 =
"SELECT personal_info.first_name_ar, 
       personal_info.last_name_ar, 
       personal_info.sex, 
       personal_info.date_of_birth,
       personal_info.place_of_birth,
       personal_info.nationality,
       lecture.lecture_name_ar, 
       account_info.username
FROM student 
INNER JOIN personal_info ON student.student_id = personal_info.student_id
INNER JOIN account_info ON student.student_id = account_info.account_id
LEFT JOIN lecture_student ON student.student_id = lecture_student.student_id  
LEFT JOIN lecture ON lecture_student.lecture_id = lecture.lecture_id;";
$query2 = 
"SELECT 
    lecture.lecture_name_ar, 
    lecture.circle_type, 
    GROUP_CONCAT(DISTINCT lecture_teacher.teacher_id SEPARATOR ', ') AS teacher_ids,
    COUNT(DISTINCT lecture_student.student_id) AS student_count
FROM lecture
LEFT JOIN lecture_teacher ON lecture.lecture_id = lecture_teacher.lecture_id
LEFT JOIN lecture_student ON lecture.lecture_id = lecture_student.lecture_id 
GROUP BY lecture.lecture_name_ar, lecture.circle_type;
";

$query3=
"SELECT 
    guardian.last_name, 
    guardian.first_name,  
    guardian.date_of_birth, 
    guardian.relationship,
    contact_info.phone_number, 
    contact_info.email, 
    guardian_account.username AS guardian_account, 
    student_account.username AS student_account,
    GROUP_CONCAT(student_account.username SEPARATOR ', ') AS children
FROM guardian
LEFT JOIN contact_info 
    ON guardian.guardian_contact_id = contact_info.contact_id
LEFT JOIN account_info AS guardian_account 
    ON guardian.guardian_account_id = guardian_account.account_id
LEFT JOIN student 
    ON student.guardian_id = guardian.guardian_id
LEFT JOIN account_info AS student_account 
    ON student.student_account_id = student_account.account_id
GROUP BY guardian.guardian_id;";
$query4="SELECT lecture_name_ar FROM lecture;";
$query5 = "SELECT username FROM account_info INNER JOIN guardian WHERE guardian_account_id = account_id;";
$query6 = "SELECT teacher_id, CONCAT(first_name, ' ', last_name) AS full_name FROM teacher;";

?>