DROP DATABASE IF EXISTS `quran`;
CREATE DATABASE `quran`;
USE `quran`;


CREATE TABLE `account_info` (
  `account_id` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT '',
  `passcode` varchar(200) DEFAULT '',
  `account_type` enum('guardian','student','teacher','superviser') DEFAULT "student",
  PRIMARY KEY (`account_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `contact_info` (
  `contact_id` INT NOT NULL AUTO_INCREMENT,
  `email` varchar(50) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`contact_id`),
  UNIQUE KEY `contact_info_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `team_accomplishment` (
  `team_accomplishment_id` INT NOT NULL AUTO_INCREMENT,
  `from_surah` varchar(50) DEFAULT '',
  `from_ayah` INT NOT NULL,
  `to_surah` varchar(50) DEFAULT '',
  `to_ayah` INT NOT NULL,
  `accompanying_curriculum_subject` varchar(50) DEFAULT '',
  `accompanying_curriculum_lesson` varchar(50) DEFAULT '',
  `tajweed_lesson` varchar(50) DEFAULT '',
  PRIMARY KEY (`team_accomplishment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `supervisor` (
  `supervisor_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `supervisor_account_id` INT DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`supervisor_id`),
  CONSTRAINT `fk_supervisor_account_id` FOREIGN KEY (`supervisor_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `guardian` (
  `guardian_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `relationship` enum('father','mother','brother','sister','uncle','aunt','grandfather','grandmother','other') DEFAULT "father",
  `guardian_contact_id` INT DEFAULT NULL,
  `guardian_account_id` INT DEFAULT NULL,
  `home_address` varchar(100) DEFAULT NULL,
  `job` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`guardian_id`),
UNIQUE KEY `fk_guardian_contact_id` (`guardian_contact_id`),
UNIQUE KEY `fk_guardian_account_id` (`guardian_account_id`),
  CONSTRAINT `fk_guardian_contact_id` FOREIGN KEY (`guardian_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_guardian_account_id` FOREIGN KEY (`guardian_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `teacher` (
  `teacher_id` INT NOT NULL AUTO_INCREMENT,
  `work_hours` INT DEFAULT 0,
  `teacher_contact_id` INT DEFAULT NULL,
  `teacher_account_id` INT DEFAULT NULL,
  `first_name` varchar(50) DEFAULT '',
  `last_name` varchar(50) DEFAULT '',
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`teacher_id`),
  UNIQUE KEY `fk_teacher_contact_id` (`teacher_contact_id`),
UNIQUE KEY `fk_teacher_account_id` (`teacher_account_id`),
  CONSTRAINT `fk_teacher_account_id` FOREIGN KEY (`teacher_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_teacher_contact_id` FOREIGN KEY (`teacher_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `lecture` (
  `lecture_id` INT NOT NULL AUTO_INCREMENT,
  `team_accomplishment_id` INT DEFAULT NULL,
  `lecture_name_ar` varchar(50) DEFAULT '',
  `lecture_name_en` varchar(50) DEFAULT '',
  `shown_on_website` BOOLEAN DEFAULT NULL,
  `circle_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`lecture_id`),
  KEY `team_accomplishment_id` (`team_accomplishment_id`),
  CONSTRAINT `lecture_ibfk_2` FOREIGN KEY (`team_accomplishment_id`) REFERENCES `team_accomplishment` (`team_accomplishment_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `student` (
  `student_id` INT NOT NULL AUTO_INCREMENT,
  `guardian_id` INT DEFAULT NULL,
  `student_contact_id` INT DEFAULT NULL,
  `student_account_id` INT DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `guardian_id` (`guardian_id`),
  UNIQUE KEY `fk_contact_id` (`student_contact_id`),
  UNIQUE KEY `fk_account_info` (`student_account_id`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`guardian_id`) REFERENCES `guardian` (`guardian_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_contact_id` FOREIGN KEY (`student_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_account_info` FOREIGN KEY (`student_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `golden_record` (
  `golden_record_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `record_type` enum('seal','figurative') DEFAULT NULL,
  `riwayah` enum('Hafs an Asim','Shubah an Asim','Warsh an Nafi','Qalun an Nafi','Al-Duri an Abi Amr','As-Susi an Abi Amr','Hisham an Ibn Amir','Ibn Dhakwan an Ibn Amir','Khalaf an Hamzah','Khallad an Hamzah','Al-Duri an Al-Kisai','Abu Al-Harith an Al-Kisai','Isa ibn Mina (Abu Jaafar)','Ibn Wardan an Abu Jaafar','Ibn Jammaz an Abu Jaafar','Ruways an Yaqoub','Rawh an Yaqoub','Ishaq an Khalaf','Idris an Khalaf') DEFAULT 'Hafs an Asim',
  `date_of_completion` date NOT NULL,
  `school_name` varchar(50) DEFAULT '',
  PRIMARY KEY (`golden_record_id`),
  CONSTRAINT `fk_golden_record_student_id` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `formal_education_info` (
  `student_id` INT NOT NULL,
  `school_name` varchar(50) DEFAULT NULL,
  `school_type` enum('Public','Private','International') DEFAULT 'Public',
  `grade` varchar(50) DEFAULT NULL,
  `academic_level` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `formal_education_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medical_info` (
  `student_id` INT NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') DEFAULT NULL,
  `allergies` varchar(255) DEFAULT 'No',
  `diseases` varchar(255) DEFAULT 'No',
  `diseases_causes` text DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `medical_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `personal_info` (
  `student_id` INT NOT NULL,
  `first_name_ar` varchar(50) DEFAULT '',
  `last_name_ar` varchar(50) DEFAULT '',
  `first_name_en` varchar(50) DEFAULT NULL,
  `last_name_en` varchar(50) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `sex` enum('male','female') DEFAULT 'male',
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(50) DEFAULT NULL,
  `home_address` varchar(100) DEFAULT NULL,
  `father_status` varchar(20) DEFAULT NULL,
  `mother_status` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`student_id`),
  CONSTRAINT `personal_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subscription_info` (
  `subscription_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `enrollment_date` date NOT NULL,
  `exit_date` date DEFAULT NULL,
  `exit_reason` text DEFAULT NULL,
  `is_exempt_from_payment` BOOLEAN NOT NULL DEFAULT 0,
  `exemption_percentage` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`subscription_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `subscription_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `team_accomplishment_student` (
  `team_accomplishment_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  PRIMARY KEY (`team_accomplishment_id`,`student_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `team_accomplishment_student_ibfk_1` FOREIGN KEY (`team_accomplishment_id`) REFERENCES `team_accomplishment` (`team_accomplishment_id`) ON DELETE CASCADE,
  CONSTRAINT `team_accomplishment_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lecture_student` (
  `lecture_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `attendance_status` enum('present','absent with excuse','late','absent without excuse') DEFAULT 'present',
  `lecture_date` date DEFAULT NULL,
  PRIMARY KEY (`lecture_id`,`student_id`),
  UNIQUE KEY `lecture_id` (`lecture_id`,`student_id`,`lecture_date`),
  KEY `lecture_student_ibfk_2` (`student_id`),
  CONSTRAINT `lecture_student_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `lecture_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lecture_content` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `from_surah` varchar(50) DEFAULT NULL,
  `from_ayah` INT DEFAULT NULL,
  `to_surah` varchar(50) DEFAULT NULL,
  `to_ayah` INT DEFAULT NULL,
  `observation` varchar(255) DEFAULT NULL,
  `student_id` INT DEFAULT NULL,
  `lecture_id` INT DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
UNIQUE KEY `fk_student_lecture` (`lecture_id`,`student_id`),
  KEY `type_2` (`type`),
  CONSTRAINT `fk_student_lecture` FOREIGN KEY (`lecture_id`,`student_id`) REFERENCES `lecture_student` (`lecture_id`, `student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lecture_teacher` (
  `teacher_id` INT NOT NULL,
  `lecture_id` INT NOT NULL,
  `lecture_date` date DEFAULT (CURRENT_DATE),
  `attendance_status` varchar(20) DEFAULT '',
  PRIMARY KEY (`teacher_id`,`lecture_id`,`lecture_date`),
  FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `weekly_schedule` (
  `weekly_schedule_id` INT NOT NULL AUTO_INCREMENT,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `lecture_id` INT NOT NULL,
  PRIMARY KEY (`weekly_schedule_id`),
UNIQUE KEY `fk_lecture_id` (`lecture_id`),
  CONSTRAINT `fk_lecture_id` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `exam_level` (
  `exam_level_id` INT NOT NULL AUTO_INCREMENT,
  `level` varchar(70) DEFAULT '',
  `from_surah` varchar(50) DEFAULT NULL,
  `from_ayah` INT DEFAULT NULL,
  `to_surah` varchar(50) DEFAULT NULL,
  `to_ayah` INT DEFAULT NULL,

  
  PRIMARY KEY (`exam_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `exam` (
  `exam_id` INT NOT NULL AUTO_INCREMENT,
  `exam_level_id` INT NOT NULL,
  `exam_name_ar` varchar(50) DEFAULT '',
  `exam_name_en` varchar(50) DEFAULT '',
  `exam_type` enum('ajzaa','all') DEFAULT NULL,
  `exam_sucess_min_point` INT NOT NULL DEFAULT 0,
  `exam_max_point` INT NOT NULL DEFAULT 0,
  `exam_memo_point` INT NOT NULL DEFAULT 0,
  `exam_tjwid_app_point` INT NOT NULL DEFAULT 0,
  `exam_tjwid_tho_point` INT NOT NULL DEFAULT 0,
  `exam_performance_point` INT NOT NULL DEFAULT 0,

  
  PRIMARY KEY (`exam_id`),
  CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`exam_level_id`) REFERENCES `exam_level` (`exam_level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `appreciation` (
  `appreciation_id` INT NOT NULL AUTO_INCREMENT,

  `point_min` INT NOT NULL DEFAULT 0,
  `point_max` INT NOT NULL DEFAULT 0,
  `note` enum("didn’t pass", 'fair', 'satisfactory', 'good', 'very good', 'excellent') DEFAULT NULL,
  PRIMARY KEY (`appreciation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



CREATE TABLE `exam_student` (
  `exam_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `appreciation_id` INT NOT NULL,

  `point_hifd` INT NOT NULL DEFAULT 0,
  `point_tajwid_applicative` INT NOT NULL DEFAULT 0,
  `point_tajwid_theoric` INT NOT NULL DEFAULT 0,
  `point_performance` INT NOT NULL DEFAULT 0,
  `point_deduction_tal9ini` INT NOT NULL DEFAULT 0,
  `point_deduction_tanbihi` INT NOT NULL DEFAULT 0,
  `point_deduction_tajwidi` INT NOT NULL DEFAULT 0,

  `date_take_exam` DATE DEFAULT NULL,

  PRIMARY KEY (`exam_id`, `student_id`, `appreciation_id`),
  
  CONSTRAINT `exam_student_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `exam_student_ibfk_3` FOREIGN KEY (`appreciation_id`) REFERENCES `appreciation` (`appreciation_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `exam_teacher` (
  `exam_id` INT NOT NULL,
  `teacher_id` INT NOT NULL,
  `date` DATE,

  
  PRIMARY KEY (`exam_id`,`teacher_id` ),
  FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`exam_id`) REFERENCES `exam` (`exam_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `student_lecture_achievements` (
  `lecture_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `achievement_type` enum('memorization','minor-review','major-review') DEFAULT NULL,
  `lecture_date` date DEFAULT NULL,
  `from_surah` varchar(50) DEFAULT '',
  `from_ayah` INT NOT NULL,
  `to_surah` varchar(50) DEFAULT '',
  `to_ayah` INT NOT NULL,
  `teacher_note` varchar(255) DEFAULT '',


  PRIMARY KEY (`lecture_id`,`student_id`),
  UNIQUE KEY `lecture_id` (`lecture_id`,`student_id`,`lecture_date`),
  KEY `student_lecture_achievements_ibfk_2` (`student_id`),
  CONSTRAINT `student_lecture_achievements_ibfk_1` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `student_lecture_achievements_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


-- insert dommy data
-- 1. account_info
INSERT INTO account_info (username, passcode, account_type) VALUES
('guardian1', 'pass1', 'guardian'),
('student1', 'pass2', 'student'),
('teacher1', 'pass3', 'teacher'),
('guardian2', 'pass4', 'guardian'),
('student2', 'pass5', 'student'),
('teacher2', 'pass6', 'teacher'),
('guardian3', 'pass7', 'guardian'),
('student3', 'pass8', 'student'),
('teacher3', 'pass9', 'teacher');

-- 2. contact_info
INSERT INTO contact_info (email, phone_number) VALUES
('g1@email.com', '1111111111'),
('s1@email.com', '2222222222'),
('t1@email.com', '3333333333'),
('g2@email.com', '4444444444'),
('s2@email.com', '5555555555'),
('t2@email.com', '6666666666'),
('g3@email.com', '7777777777'),
('s3@email.com', '8888888888'),
('t3@email.com', '9999999999');

-- 3. guardian (references account_info and contact_info)
INSERT INTO guardian (first_name, last_name, date_of_birth, relationship, guardian_contact_id, guardian_account_id, home_address, job, profile_image)
VALUES
('Ali', 'Ahmad', '1970-01-01', 'father', 1, 1, 'Address 1', 'Engineer', NULL),
('Omar', 'Saeed', '1975-02-02', 'father', 4, 4, 'Address 2', 'Doctor', NULL),
('Fatima', 'Hassan', '1980-03-03', 'mother', 7, 7, 'Address 3', 'Teacher', NULL);

-- 4. teacher (references account_info and contact_info)
INSERT INTO teacher (work_hours, teacher_contact_id, teacher_account_id, first_name, last_name, profile_image)
VALUES
(40, 3, 3, 'Mohamed', 'Ali', NULL),
(35, 6, 6, 'Sara', 'Youssef', NULL),
(30, 9, 9, 'Khaled', 'Mahmoud', NULL);

-- 5. student (references guardian, contact_info, account_info)
INSERT INTO student (guardian_id, student_contact_id, student_account_id)
VALUES
(1, 2, 2),
(2, 5, 5),
(3, 8, 8);

-- 6. personal_info (references student)
INSERT INTO personal_info (student_id, first_name_ar, last_name_ar, first_name_en, last_name_en, nationality, sex, date_of_birth, place_of_birth, home_address, father_status, mother_status, profile_image)
VALUES
(1, 'أحمد', 'علي', 'Ahmed', 'Ali', 'Egyptian', 'male', '2010-05-10', 'Cairo', 'Cairo Address', 'alive', 'alive', NULL),
(2, 'سارة', 'محمد', 'Sara', 'Mohamed', 'Egyptian', 'female', '2011-06-15', 'Giza', 'Giza Address', 'alive', 'alive', NULL),
(3, 'خالد', 'محمود', 'Khaled', 'Mahmoud', 'Egyptian', 'male', '2012-07-20', 'Alexandria', 'Alex Address', 'alive', 'alive', NULL);

-- 7. medical_info (references student)
INSERT INTO medical_info (student_id, blood_type, allergies, diseases, diseases_causes)
VALUES
(1, 'A+', 'None', 'None', NULL),
(2, 'B-', 'Peanuts', 'Asthma', 'Dust'),
(3, 'O+', 'None', 'None', NULL);

-- 8. formal_education_info (references student)
INSERT INTO formal_education_info (student_id, school_name, school_type, grade, academic_level)
VALUES
(1, 'Al-Azhar', 'Public', '5', 'Primary'),
(2, 'Al-Nour', 'Private', '6', 'Primary'),
(3, 'Al-Huda', 'International', '7', 'Secondary');

-- 9. subscription_info (references student)
INSERT INTO subscription_info (student_id, enrollment_date, exit_date, exit_reason, is_exempt_from_payment, exemption_percentage)
VALUES
(1, '2022-09-01', NULL, NULL, 0, 0.00),
(2, '2022-09-01', NULL, NULL, 1, 50.00),
(3, '2022-09-01', NULL, NULL, 0, 0.00);

-- 10. team_accomplishment
INSERT INTO team_accomplishment (from_surah, from_ayah, to_surah, to_ayah, accompanying_curriculum_subject, accompanying_curriculum_lesson, tajweed_lesson)
VALUES
('Al-Fatiha', 1, 'Al-Baqara', 5, 'Math', 'Lesson 1', 'Tajweed 1'),
('Al-Baqara', 6, 'Al-Imran', 10, 'Science', 'Lesson 2', 'Tajweed 2'),
('Al-Imran', 11, 'An-Nisa', 15, 'Arabic', 'Lesson 3', 'Tajweed 3');

-- 11. lecture (references team_accomplishment)
INSERT INTO lecture (team_accomplishment_id, lecture_name_ar, lecture_name_en, shown_on_website, circle_type)
VALUES
(1, 'دائرة 1', 'Circle 1', 1, 'boys'),
(2, 'دائرة 2', 'Circle 2', 0, 'girls'),
(3, 'دائرة 3', 'Circle 3', 1, 'mixed');

-- 12. lecture_student (references lecture, student)
INSERT INTO lecture_student (lecture_id, student_id, attendance_status, lecture_date)
VALUES
(1, 1, 'present', '2023-01-01'),
(2, 2, 'late', '2023-01-02'),
(3, 3, 'present', '2023-01-03');

-- 13. lecture_teacher (references teacher, lecture)
INSERT INTO lecture_teacher (teacher_id, lecture_id, lecture_date, attendance_status)
VALUES
(1, 1, '2023-01-01', 'present'),
(2, 2, '2023-01-02', 'present'),
(3, 3, '2023-01-03', 'present');

-- 14. weekly_schedule (references lecture)
INSERT INTO weekly_schedule (day_of_week, start_time, end_time, lecture_id)
VALUES
('Monday', '08:00:00', '10:00:00', 1),
('Tuesday', '10:00:00', '12:00:00', 2),
('Wednesday', '12:00:00', '14:00:00', 3);

-- 15. exam_level
INSERT INTO exam_level (level, from_surah, from_ayah, to_surah, to_ayah)
VALUES
('Level 1', 'Al-Fatiha', 1, 'Al-Baqara', 5),
('Level 2', 'Al-Baqara', 6, 'Al-Imran', 10),
('Level 3', 'Al-Imran', 11, 'An-Nisa', 15);

-- 16. exam (references exam_level)
INSERT INTO exam (exam_level_id, exam_name_ar, exam_name_en, exam_type, exam_sucess_min_point, exam_max_point, exam_memo_point, exam_tjwid_app_point, exam_tjwid_tho_point, exam_performance_point)
VALUES
(1, 'امتحان 1', 'Exam 1', 'ajzaa', 50, 100, 20, 20, 20, 40),
(2, 'امتحان 2', 'Exam 2', 'all', 60, 120, 30, 30, 30, 30),
(3, 'امتحان 3', 'Exam 3', 'ajzaa', 70, 140, 40, 40, 40, 20);

-- 17. appreciation
INSERT INTO appreciation (point_min, point_max, note)
VALUES
(0, 49, "didn’t pass"),
(50, 79, "good"),
(80, 100, "excellent");

-- 18. exam_student (references exam, student, appreciation)
INSERT INTO exam_student (exam_id, student_id, appreciation_id, point_hifd, point_tajwid_applicative, point_tajwid_theoric, point_performance, point_deduction_tal9ini, point_deduction_tanbihi, point_deduction_tajwidi, date_take_exam)
VALUES
(1, 1, 1, 40, 10, 10, 10, 0, 0, 0, '2023-02-01'),
(2, 2, 2, 60, 10, 10, 10, 0, 0, 0, '2023-02-02'),
(3, 3, 3, 90, 10, 10, 10, 0, 0, 0, '2023-02-03');

-- 19. exam_teacher (references exam, teacher)
INSERT INTO exam_teacher (exam_id, teacher_id, date)
VALUES
(1, 1, '2023-02-01'),
(2, 2, '2023-02-02'),
(3, 3, '2023-02-03');

-- 20. team_accomplishment_student (references team_accomplishment, student)
INSERT INTO team_accomplishment_student (team_accomplishment_id, student_id)
VALUES
(1, 1),
(2, 2),
(3, 3);

-- 21. golden_record (references student)
INSERT INTO golden_record (student_id, record_type, riwayah, date_of_completion, school_name)
VALUES
(1, 'seal', 'Hafs an Asim', '2023-03-01', 'Al-Azhar'),
(2, 'figurative', 'Warsh an Nafi', '2023-03-02', 'Al-Nour'),
(3, 'seal', 'Qalun an Nafi', '2023-03-03', 'Al-Huda');

-- 22. lecture_content (references lecture_student)
INSERT INTO lecture_content (from_surah, from_ayah, to_surah, to_ayah, observation, student_id, lecture_id, type)
VALUES
('Al-Fatiha', 1, 'Al-Baqara', 5, 'Good', 1, 1, 'memorization'),
('Al-Baqara', 6, 'Al-Imran', 10, 'Average', 2, 2, 'minor-review'),
('Al-Imran', 11, 'An-Nisa', 15, 'Excellent', 3, 3, 'major-review');

-- 23. student_lecture_achievements (references lecture, student)
INSERT INTO student_lecture_achievements (lecture_id, student_id, achievement_type, lecture_date, from_surah, from_ayah, to_surah, to_ayah, teacher_note)
VALUES
(1, 1, 'memorization', '2023-01-01', 'Al-Fatiha', 1, 'Al-Baqara', 5, 'Well done'),
(2, 2, 'minor-review', '2023-01-02', 'Al-Baqara', 6, 'Al-Imran', 10, 'Needs improvement'),
(3, 3, 'major-review', '2023-01-03', 'Al-Imran', 11, 'An-Nisa', 15, 'Excellent progress');