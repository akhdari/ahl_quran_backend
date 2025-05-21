DROP DATABASE IF EXISTS `quran`;
CREATE DATABASE `quran`;
USE `quran`;


CREATE TABLE `account_info` (
  `account_id` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `passcode` varchar(200) NOT NULL,
  `account_type` enum('guardian','student','teacher','superviser') DEFAULT NULL,
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
  `from_surah` varchar(50) NOT NULL,
  `from_ayah` INT NOT NULL,
  `to_surah` varchar(50) NOT NULL,
  `to_ayah` INT NOT NULL,
  `accompanying_curriculum_subject` varchar(50) NOT NULL,
  `accompanying_curriculum_lesson` varchar(50) NOT NULL,
  `tajweed_lesson` varchar(50) NOT NULL,
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
  `relationship` enum('father','mother','brother','sister','uncle','aunt','grandfather','grandmother','other') DEFAULT NULL,
  `guardian_contact_id` INT DEFAULT NULL,
  `guardian_account_id` INT DEFAULT NULL,
  `home_address` varchar(100) DEFAULT NULL,
  `job` varchar(100) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`guardian_id`),
  KEY `fk_guardian_contact_id` (`guardian_contact_id`),
  KEY `fk_guardian_account_id` (`guardian_account_id`),
  CONSTRAINT `fk_guardian_contact_id` FOREIGN KEY (`guardian_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_guardian_account_id` FOREIGN KEY (`guardian_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `teacher` (
  `teacher_id` INT NOT NULL AUTO_INCREMENT,
  `work_hours` INT DEFAULT 0,
  `teacher_contact_id` INT DEFAULT NULL,
  `teacher_account_id` INT DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`teacher_id`),
  KEY `fk_teacher_contact_id` (`teacher_contact_id`),
  KEY `fk_teacher_account_id` (`teacher_account_id`),
  CONSTRAINT `fk_teacher_account_id` FOREIGN KEY (`teacher_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_teacher_contact_id` FOREIGN KEY (`teacher_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `lecture` (
  `lecture_id` INT NOT NULL AUTO_INCREMENT,
  `team_accomplishment_id` INT DEFAULT NULL,
  `lecture_name_ar` varchar(50) NOT NULL,
  `lecture_name_en` varchar(50) NOT NULL,
  `shown_on_website` BOOLEAN DEFAULT NULL,
  `circle_type` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`lecture_id`),
  KEY `team_accomplishment_id` (`team_accomplishment_id`),
  CONSTRAINT `lecture_ibfk_2` FOREIGN KEY (`team_accomplishment_id`) REFERENCES `team_accomplishment` (`team_accomplishment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `student` (
  `student_id` INT NOT NULL AUTO_INCREMENT,
  `guardian_id` INT DEFAULT NULL,
  `student_contact_id` INT DEFAULT NULL,
  `student_account_id` INT DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `guardian_id` (`guardian_id`),
  KEY `fk_contact_id` (`student_contact_id`),
  KEY `fk_account_info` (`student_account_id`),
  CONSTRAINT `student_ibfk_1` FOREIGN KEY (`guardian_id`) REFERENCES `guardian` (`guardian_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_contact_id` FOREIGN KEY (`student_contact_id`) REFERENCES `contact_info` (`contact_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_account_info` FOREIGN KEY (`student_account_id`) REFERENCES `account_info` (`account_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `golden_record` (
  `golden_record_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `record_type` enum('seal','figurative') DEFAULT NULL,
  `riwayah` enum('Hafs an Asim','Shubah an Asim','Warsh an Nafi','Qalun an Nafi','Al-Duri an Abi Amr','As-Susi an Abi Amr','Hisham an Ibn Amir','Ibn Dhakwan an Ibn Amir','Khalaf an Hamzah','Khallad an Hamzah','Al-Duri an Al-Kisai','Abu Al-Harith an Al-Kisai','Isa ibn Mina (Abu Jaafar)','Ibn Wardan an Abu Jaafar','Ibn Jammaz an Abu Jaafar','Ruways an Yaqoub','Rawh an Yaqoub','Ishaq an Khalaf','Idris an Khalaf') NOT NULL,
  `date_of_completion` date NOT NULL,
  `school_name` varchar(50) NOT NULL,
  PRIMARY KEY (`golden_record_id`),
  CONSTRAINT `fk_golden_record_student_id` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


CREATE TABLE `formal_education_info` (
  `student_id` INT NOT NULL,
  `school_name` varchar(50) DEFAULT NULL,
  `school_type` enum('Public','Private','International') DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `academic_level` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `formal_education_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `medical_info` (
  `student_id` INT NOT NULL,
  `blood_type` enum('A+','A-','B+','B-','O+','O-','AB+','AB-') DEFAULT NULL,
  `allergies` varchar(255) DEFAULT 'None',
  `diseases` varchar(255) DEFAULT 'None',
  `diseases_causes` text DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  CONSTRAINT `medical_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `personal_info` (
  `student_id` INT NOT NULL,
  `first_name_ar` varchar(50) NOT NULL,
  `last_name_ar` varchar(50) NOT NULL,
  `first_name_en` varchar(50) DEFAULT NULL,
  `last_name_en` varchar(50) DEFAULT NULL,
  `nationality` varchar(50) DEFAULT NULL,
  `sex` enum('male','female') NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `place_of_birth` varchar(50) DEFAULT NULL,
  `home_address` varchar(100) DEFAULT NULL,
  `father_status` varchar(20) DEFAULT NULL,
  `mother_status` varchar(20) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,

  PRIMARY KEY (`student_id`),
  CONSTRAINT `personal_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `subscription_info` (
  `subscription_id` INT NOT NULL AUTO_INCREMENT,
  `student_id` INT NOT NULL,
  `enrollment_date` date NOT NULL,
  `exit_date` date DEFAULT NULL,
  `exit_reason` text DEFAULT NULL,
  `is_exempt_from_payment` tinyINT NOT NULL DEFAULT 0,
  `exemption_percentage` decimal(5,2) DEFAULT 0.00,
  PRIMARY KEY (`subscription_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `subscription_info_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `team_accomplishment_student` (
  `team_accomplishment_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  PRIMARY KEY (`team_accomplishment_id`,`student_id`),
  KEY `student_id` (`student_id`),
  CONSTRAINT `team_accomplishment_student_ibfk_1` FOREIGN KEY (`team_accomplishment_id`) REFERENCES `team_accomplishment` (`team_accomplishment_id`) ON DELETE CASCADE,
  CONSTRAINT `team_accomplishment_student_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lecture_student` (
  `lecture_id` INT NOT NULL,
  `student_id` INT NOT NULL,
  `attendance_status` enum('present','absent with excuse','late','absent without excuse') NOT NULL,
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
  KEY `fk_student_lecture` (`lecture_id`,`student_id`),
  KEY `type_2` (`type`),
  CONSTRAINT `fk_student_lecture` FOREIGN KEY (`lecture_id`,`student_id`) REFERENCES `lecture_student` (`lecture_id`, `student_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `lecture_teacher` (
  `teacher_id` INT NOT NULL,
  `lecture_id` INT NOT NULL,
  `lecture_date` date DEFAULT NULL,
  `attendance_status` varchar(20) NOT NULL,
  PRIMARY KEY (`teacher_id`,`lecture_id`,`lecture_date`),
  FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`teacher_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `weekly_schedule` (
  `weekly_schedule_id` INT NOT NULL AUTO_INCREMENT,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `lecture_id` INT NOT NULL,
  PRIMARY KEY (`weekly_schedule_id`),
  KEY `fk_lecture_id` (`lecture_id`),
  CONSTRAINT `fk_lecture_id` FOREIGN KEY (`lecture_id`) REFERENCES `lecture` (`lecture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `exam_level` (
  `exam_level_id` INT NOT NULL AUTO_INCREMENT,
  `level` varchar(70) NOT NULL,
  `from_surah` varchar(50) DEFAULT NULL,
  `from_ayah` INT DEFAULT NULL,
  `to_surah` varchar(50) DEFAULT NULL,
  `to_ayah` INT DEFAULT NULL,

  
  PRIMARY KEY (`exam_level_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `exam` (
  `exam_id` INT NOT NULL AUTO_INCREMENT,
  `exam_level_id` INT NOT NULL,
  `exam_name_ar` varchar(50) NOT NULL,
  `exam_name_en` varchar(50) NOT NULL,
  `exam_type` enum('ajzaa','all') DEFAULT NULL,
  `exam_sucess_min_point` INT NOT NULL DEFAULT 0,
  `exam_max_point` INT NOT NULL DEFAULT 0,
  
  PRIMARY KEY (`exam_id`),
  CONSTRAINT `exam_ibfk_1` FOREIGN KEY (`exam_level_id`) REFERENCES `exam_level` (`exam_level_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `appreciation` (
  `appreciation_id` INT NOT NULL,

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


CREATE TABLE `request_copy` (
  `request_copy_id` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `phone_number` varchar(50) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,


  PRIMARY KEY `request_copy` (`request_copy_id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

