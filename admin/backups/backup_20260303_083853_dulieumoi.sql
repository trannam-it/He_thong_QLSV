-- ==============================================
-- Database Backup: database_qlsv
-- Generated: 2026-03-03 08:38:53
-- ==============================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
SET NAMES utf8mb4;

-- ----------------------------------------
-- Table: `attendance`
-- ----------------------------------------

DROP TABLE IF EXISTS `attendance`;
CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') DEFAULT 'Present',
  `note` text DEFAULT NULL,
  PRIMARY KEY (`attendance_id`),
  KEY `fk_att_enrollment` (`enrollment_id`),
  CONSTRAINT `fk_att_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------
-- Table: `audit_logs`
-- ----------------------------------------

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`audit_id`),
  KEY `idx_audit_user` (`user_id`),
  KEY `idx_audit_action` (`action`),
  KEY `idx_audit_created` (`created_at`),
  KEY `idx_audit_table` (`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('1', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:02:37');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('2', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:06:06');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('3', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:07:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('4', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:12:21');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('5', '26', 'sv06', 'LOGIN_FAIL', 'users', '26', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:29:35');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('6', '21', 'sv01', 'LOGIN_FAIL', 'users', '21', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:33:16');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('7', '24', 'sv04', 'LOGIN_SUCCESS', 'users', '24', NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:33:52');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('8', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:34:59');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('9', '1', 'admin', 'ASSIGN_PERMISSIONS', 'roles', '5', NULL, '{\"permission_ids\":[28,63,64,67,71,72],\"count\":6}', '::1', '2026-02-28 22:36:32');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('10', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:36:48');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('11', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:40:05');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('12', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:40:31');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('13', '1', 'admin', 'ASSIGN_PERMISSIONS', 'roles', '5', NULL, '{\"permission_ids\":[28,63,64,67],\"count\":4}', '::1', '2026-02-28 22:41:59');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('14', '24', 'sv04', 'LOGIN_SUCCESS', 'users', '24', NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:43:11');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('15', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-28 22:44:20');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('16', '26', 'sv06', 'LOGIN_SUCCESS', 'users', '26', NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:44:58');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('17', '201', 'student_admin1', 'LOGIN_SUCCESS', 'users', '201', NULL, '{\"role\":\"student_admin\"}', '::1', '2026-02-28 22:46:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('18', '201', 'student_admin1', 'LOGIN_SUCCESS', 'users', '201', NULL, '{\"role\":\"student_admin\"}', '::1', '2026-02-28 22:46:58');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('19', '203', 'librarian1', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"librarian\"}', '::1', '2026-02-28 22:47:19');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('20', '200', 'academic1', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-02-28 22:47:50');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('21', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-01 16:19:05');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('22', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 00:37:29');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('23', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 00:38:57');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('24', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:17:20');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('25', '21', 'sv01', 'LOGIN_FAIL', 'users', '21', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-03-02 01:17:36');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('26', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 01:17:47');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('27', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:25:43');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('28', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 01:27:04');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('29', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:28:35');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('30', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:54:15');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('31', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-02 01:54:59');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('32', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 07:05:20');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('33', '203', 'librarian1', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 07:16:41');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('34', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 18:12:12');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('35', '21', 'sv01', 'LOGIN_SUCCESS', 'users', '21', NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 18:17:56');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('36', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-02 18:20:38');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('37', '200', 'academic1', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-02 18:36:49');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('38', '203', 'librarian1', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 18:38:09');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('39', '203', 'thuvien', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 18:46:21');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('40', '200', 'daotao', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-02 18:51:11');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('41', '1', 'admin', 'ASSIGN_PERMISSIONS', 'roles', '6', NULL, '{\"permission_ids\":[82],\"count\":1}', '::1', '2026-03-02 20:40:13');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('42', '1', 'admin', 'ASSIGN_PERMISSIONS', 'roles', '6', NULL, '{\"permission_ids\":[68,69,82],\"count\":3}', '::1', '2026-03-02 20:42:42');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('43', '1', 'admin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-03 13:56:21');

-- ----------------------------------------
-- Table: `base_classes`
-- ----------------------------------------

DROP TABLE IF EXISTS `base_classes`;
CREATE TABLE `base_classes` (
  `base_class_id` int(11) NOT NULL AUTO_INCREMENT,
  `base_class_code` varchar(20) NOT NULL,
  `base_class_name` varchar(100) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL,
  PRIMARY KEY (`base_class_id`),
  UNIQUE KEY `base_class_code` (`base_class_code`),
  KEY `fk_bc_faculty` (`faculty_id`),
  KEY `fk_bc_lecturer` (`lecturer_id`),
  CONSTRAINT `fk_bc_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `fk_bc_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('1', 'CNTT2022A', 'Công nghệ thông tin K22A', '1', '1', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('2', 'CNTT2022B', 'Công nghệ thông tin K22B', '1', '2', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('3', 'QTKD2022A', 'Quản trị kinh doanh K22A', '2', '4', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('4', 'QTKD2022B', 'Quản trị kinh doanh K22B', '2', '5', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('5', 'NN2022A', 'Ngôn ngữ Anh K22A', '3', '7', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('6', 'NN2022B', 'Ngôn ngữ Anh K22B', '3', '8', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('7', 'KT2022A', 'Kế toán K22A', '4', '10', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('8', 'KT2022B', 'Kế toán K22B', '4', '11', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('9', 'TCNH2022A', 'Tài chính - Ngân hàng K22A', '5', '13', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('10', 'TCNH2022B', 'Tài chính - Ngân hàng K22B', '5', '14', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('11', 'SP2022A', 'Sư phạm K22A', '6', '16', '2022', '2026');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('12', 'SP2022B', 'Sư phạm K22B', '6', '17', '2022', '2026');

-- ----------------------------------------
-- Table: `class_schedules`
-- ----------------------------------------

DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '2=Mon 3=Tue 4=Wed 5=Thu 6=Fri 7=Sat',
  `start_period` tinyint(4) NOT NULL,
  `end_period` tinyint(4) NOT NULL,
  `room` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `fk_cs_class` (`class_id`),
  CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('1', '1', '2', '1', '3', 'A101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('2', '1', '4', '1', '3', 'A101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('3', '2', '2', '4', '6', 'A102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('4', '2', '5', '4', '6', 'A102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('5', '3', '3', '1', '3', 'A103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('6', '3', '6', '1', '3', 'A103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('7', '4', '2', '7', '9', 'B201');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('8', '4', '4', '7', '9', 'B201');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('9', '5', '3', '4', '6', 'B202');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('10', '5', '5', '4', '6', 'B202');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('11', '6', '4', '1', '3', 'B203');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('12', '6', '6', '1', '3', 'B203');

-- ----------------------------------------
-- Table: `classes`
-- ----------------------------------------

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `max_students` int(11) DEFAULT 40,
  `status` enum('Active','Closed','Cancelled') DEFAULT 'Active',
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `fk_cls_subject` (`subject_id`),
  KEY `fk_cls_lecturer` (`lecturer_id`),
  KEY `fk_class_semester` (`semester_id`),
  CONSTRAINT `fk_class_semester` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  CONSTRAINT `fk_cls_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`),
  CONSTRAINT `fk_cls_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('1', 'CNTT101-01', '1', '1', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('2', 'CNTT201-01', '2', '2', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('3', 'CNTT301-01', '3', '3', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('4', 'QTKD101-01', '4', '4', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('5', 'QTKD201-01', '5', '5', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('6', 'QTKD301-01', '6', '6', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('7', 'ENGL101-01', '7', '7', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('8', 'ENGL201-01', '8', '8', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('9', 'ENGL301-01', '9', '9', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('10', 'KT101-01', '10', '10', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('11', 'KT201-01', '11', '11', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('12', 'KT301-01', '12', '12', '3', '40', 'Active');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES ('13', 'QTKD301-02', '6', '1', '3', '40', 'Active');

-- ----------------------------------------
-- Table: `dormitory_registrations`
-- ----------------------------------------

DROP TABLE IF EXISTS `dormitory_registrations`;
CREATE TABLE `dormitory_registrations` (
  `registration_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Pending','Active','Ended','Cancelled') DEFAULT 'Pending',
  `note` text DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`registration_id`),
  KEY `fk_dr_student` (`student_id`),
  KEY `fk_dr_room` (`room_id`),
  CONSTRAINT `fk_dr_room` FOREIGN KEY (`room_id`) REFERENCES `dormitory_rooms` (`room_id`),
  CONSTRAINT `fk_dr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dormitory_registrations` (`registration_id`, `student_id`, `room_id`, `start_date`, `end_date`, `status`, `note`, `registered_at`, `updated_at`) VALUES ('1', '2', '1', '2026-03-06', '2026-04-04', 'Pending', NULL, '2026-02-28 22:37:29', '2026-02-28 22:37:29');

-- ----------------------------------------
-- Table: `dormitory_rooms`
-- ----------------------------------------

DROP TABLE IF EXISTS `dormitory_rooms`;
CREATE TABLE `dormitory_rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `building` varchar(50) NOT NULL DEFAULT 'Tòa A',
  `room_type` enum('Single','Double','Triple','Quad') NOT NULL DEFAULT 'Double',
  `price_per_month` decimal(10,0) NOT NULL DEFAULT 500000,
  `total_beds` int(11) NOT NULL DEFAULT 2,
  `available_beds` int(11) NOT NULL DEFAULT 2,
  `floor` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`room_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('1', 'A101', 'Tòa A', 'Double', '400000', '2', '0', '1', 'Phòng đôi tầng 1, có điều hòa', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('2', 'A102', 'Tòa A', 'Double', '400000', '2', '2', '1', 'Phòng đôi tầng 1, có điều hòa', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('3', 'A201', 'Tòa A', 'Quad', '300000', '4', '2', '2', 'Phòng 4 người tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('4', 'B101', 'Tòa B', 'Double', '380000', '2', '2', '1', 'Phòng đôi tòa B tầng 1', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('5', 'B201', 'Tòa B', 'Triple', '350000', '3', '3', '2', 'Phòng 3 người tòa B tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('6', 'C101', 'Tòa C', 'Quad', '280000', '4', '4', '1', 'Phòng 4 người tòa C giá rẻ', '1');

-- ----------------------------------------
-- Table: `enrollments`
-- ----------------------------------------

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `status` enum('Enrolled','Withdrawn','Completed') DEFAULT 'Enrolled',
  PRIMARY KEY (`enrollment_id`),
  UNIQUE KEY `uq_enroll` (`student_id`,`class_id`),
  KEY `fk_enr_class` (`class_id`),
  KEY `idx_enrollments_student` (`student_id`),
  KEY `idx_enrollments_class` (`class_id`),
  CONSTRAINT `fk_enr_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  CONSTRAINT `fk_enr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('1', '1', '1', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('2', '1', '2', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('3', '1', '3', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('4', '2', '1', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('5', '2', '2', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('6', '3', '1', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('7', '3', '7', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('8', '4', '4', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('9', '4', '5', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('10', '5', '4', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('11', '5', '6', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('12', '8', '4', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('13', '8', '5', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('14', '9', '4', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('15', '9', '6', '2026-02-28 21:58:01', 'Enrolled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES ('16', '10', '4', '2026-02-28 21:58:01', 'Enrolled');

-- ----------------------------------------
-- Table: `faculties`
-- ----------------------------------------

DROP TABLE IF EXISTS `faculties`;
CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_code` varchar(20) NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`faculty_id`),
  UNIQUE KEY `faculty_code` (`faculty_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('1', 'CNTT', 'Công nghệ thông tin', NULL, '2026-02-28 21:58:01');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('2', 'QTKD', 'Quản trị kinh doanh', NULL, '2026-02-28 21:58:01');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('3', 'NN', 'Ngôn ngữ Anh', NULL, '2026-02-28 21:58:01');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('4', 'KT', 'Kế toán', NULL, '2026-02-28 21:58:01');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('5', 'TCNH', 'Tài chính - Ngân hàng', NULL, '2026-02-28 21:58:01');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES ('6', 'SP', 'Sư phạm', NULL, '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `grades`
-- ----------------------------------------

DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `attendance_score` decimal(4,2) DEFAULT NULL,
  `midterm_score` decimal(5,2) DEFAULT NULL,
  `final_score` decimal(5,2) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm tổng kết',
  `grade_letter` varchar(5) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `enrollment_id` (`enrollment_id`),
  KEY `fk_gr_enrollment` (`enrollment_id`),
  KEY `idx_grades_enrollment` (`enrollment_id`),
  CONSTRAINT `fk_gr_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('1', '1', NULL, '8.50', '8.00', '8.20', 'B+', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('2', '2', NULL, '7.00', '7.50', '7.30', 'B', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('3', '3', NULL, '9.00', '9.50', '9.30', 'A', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('4', '4', NULL, '6.50', '7.00', '6.80', 'C+', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('5', '5', NULL, '8.00', '8.50', '8.30', 'B+', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('6', '8', NULL, '7.50', '8.00', '7.80', 'B+', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('7', '10', NULL, '9.00', '9.00', '9.00', 'A', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('8', '12', NULL, '6.00', '6.50', '6.30', 'C+', NULL, '2026-02-28 21:58:01');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES ('9', '14', NULL, '8.00', '7.50', '7.70', 'B+', NULL, '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `lecturers`
-- ----------------------------------------

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `lecturer_code` varchar(20) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') DEFAULT 'Male',
  `birth_date` date DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `degree` enum('Bachelor','Master','PhD','Professor') DEFAULT 'Master',
  `faculty_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`lecturer_id`),
  UNIQUE KEY `lecturer_code` (`lecturer_code`),
  KEY `fk_lec_user` (`user_id`),
  KEY `fk_lec_faculty` (`faculty_id`),
  KEY `idx_lecturers_faculty` (`faculty_id`),
  CONSTRAINT `fk_lec_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `fk_lec_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('1', '2', 'GV001', 'Minh', 'Nguyen', 'Male', NULL, 'gv01@university.edu', NULL, 'PhD', '1', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('2', '3', 'GV002', 'Hoa', 'Tran', 'Male', NULL, 'gv02@university.edu', NULL, 'Master', '1', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('3', '4', 'GV003', 'An', 'Le', 'Male', NULL, 'gv03@university.edu', NULL, 'Master', '1', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('4', '5', 'GV004', 'Binh', 'Pham', 'Male', NULL, 'gv04@university.edu', NULL, 'PhD', '2', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('5', '6', 'GV005', 'Chi', 'Vu', 'Male', NULL, 'gv05@university.edu', NULL, 'Master', '2', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('6', '7', 'GV006', 'Dung', 'Do', 'Male', NULL, 'gv06@university.edu', NULL, 'Master', '2', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('7', '8', 'GV007', 'Hieu', 'Hoang', 'Male', NULL, 'gv07@university.edu', NULL, 'PhD', '3', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('8', '9', 'GV008', 'Khanh', 'Nguyen', 'Male', NULL, 'gv08@university.edu', NULL, 'Master', '3', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('9', '10', 'GV009', 'Linh', 'Tran', 'Male', NULL, 'gv09@university.edu', NULL, 'Master', '3', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('10', '11', 'GV010', 'Manh', 'Le', 'Male', NULL, 'gv10@university.edu', NULL, 'PhD', '4', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('11', '12', 'GV011', 'Nga', 'Pham', 'Male', NULL, 'gv11@university.edu', NULL, 'Master', '4', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('12', '13', 'GV012', 'Phong', 'Vu', 'Male', NULL, 'gv12@university.edu', NULL, 'Master', '4', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('13', '14', 'GV013', 'Quang', 'Do', 'Male', NULL, 'gv13@university.edu', NULL, 'PhD', '5', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('14', '15', 'GV014', 'Son', 'Hoang', 'Male', NULL, 'gv14@university.edu', NULL, 'Master', '5', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('15', '16', 'GV015', 'Thao', 'Nguyen', 'Male', NULL, 'gv15@university.edu', NULL, 'Master', '5', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('16', '17', 'GV016', 'Uyen', 'Tran', 'Male', NULL, 'gv16@university.edu', NULL, 'PhD', '6', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('17', '18', 'GV017', 'Van', 'Le', 'Male', NULL, 'gv17@university.edu', NULL, 'Master', '6', '2026-02-28 21:58:01');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES ('18', '19', 'GV018', 'Xuan', 'Pham', 'Male', NULL, 'gv18@university.edu', NULL, 'Professor', '6', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `library_books`
-- ----------------------------------------

DROP TABLE IF EXISTS `library_books`;
CREATE TABLE `library_books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `author` varchar(200) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `total_copies` int(11) DEFAULT 1,
  `available_copies` int(11) DEFAULT 1,
  `published_year` year(4) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES ('1', NULL, 'Lập trình PHP căn bản', 'Nguyễn Văn A', 'Công nghệ thông tin', '5', '3', '2020', '1');
INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES ('2', NULL, 'Cơ sở dữ liệu MySQL', 'Trần Thị B', 'Công nghệ thông tin', '3', '1', '2019', '1');
INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES ('3', NULL, 'Quản trị kinh doanh hiện đại', 'Lê Văn C', 'Kinh tế', '4', '3', '2021', '1');
INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES ('4', NULL, 'Tiếng Anh thương mại', 'Phạm Thị D', 'Ngoại ngữ', '6', '5', '2022', '1');
INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES ('5', NULL, 'Kế toán tài chính doanh nghiệp', 'Vũ Văn E', 'Kế toán', '4', '3', '2020', '1');

-- ----------------------------------------
-- Table: `library_borrows`
-- ----------------------------------------

DROP TABLE IF EXISTS `library_borrows`;
CREATE TABLE `library_borrows` (
  `borrow_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Borrowed','Returned','Overdue') DEFAULT 'Borrowed',
  `created_at` datetime DEFAULT current_timestamp(),
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`borrow_id`),
  KEY `fk_lb_student` (`student_id`),
  KEY `fk_lb_book` (`book_id`),
  CONSTRAINT `fk_lb_book` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`book_id`),
  CONSTRAINT `fk_lb_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `library_borrows` (`borrow_id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `fine_amount`, `note`) VALUES ('1', '2', '2', '2026-02-28', '2026-03-14', NULL, 'Borrowed', '2026-03-02 18:43:26', '0.00', NULL);
INSERT INTO `library_borrows` (`borrow_id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `fine_amount`, `note`) VALUES ('2', '2', '5', '0000-00-00', '2026-03-15', NULL, 'Borrowed', '2026-03-02 18:43:26', '0.00', NULL);
INSERT INTO `library_borrows` (`borrow_id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `fine_amount`, `note`) VALUES ('3', '2', '1', '0000-00-00', '2026-03-15', NULL, 'Borrowed', '2026-03-02 18:43:26', '0.00', NULL);

-- ----------------------------------------
-- Table: `password_resets`
-- ----------------------------------------

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pr_user` (`user_id`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------
-- Table: `permission_groups`
-- ----------------------------------------

DROP TABLE IF EXISTS `permission_groups`;
CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(60) NOT NULL COMMENT 'Mã nhóm: users_management, training_management...',
  `name` varchar(150) NOT NULL COMMENT 'Tên hiển thị: Quản lý Người dùng',
  `icon` varchar(60) DEFAULT 'bi-shield-lock' COMMENT 'Bootstrap icon class',
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `uq_pg_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('1', 'user_management', 'Quản lý Người dùng', 'bi-people-fill', '1', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('2', 'role_management', 'Quản lý Vai trò & Quyền', 'bi-shield-lock-fill', '2', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('3', 'student_management', 'Quản lý Sinh viên', 'bi-mortarboard-fill', '3', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('4', 'lecturer_management', 'Quản lý Giảng viên', 'bi-person-badge-fill', '4', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('5', 'faculty_management', 'Quản lý Khoa / Ngành', 'bi-building-fill', '5', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('6', 'training_management', 'Quản lý Đào tạo', 'bi-journal-bookmark-fill', '6', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('7', 'grade_management', 'Quản lý Điểm số', 'bi-graph-up-arrow', '7', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('8', 'enrollment_mgmt', 'Quản lý Đăng ký môn học', 'bi-clipboard-check-fill', '8', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('9', 'finance_management', 'Quản lý Tài chính', 'bi-cash-stack', '9', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('10', 'dormitory_mgmt', 'Quản lý Ký túc xá', 'bi-house-fill', '10', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('11', 'library_management', 'Quản lý Thư viện', 'bi-book-fill', '11', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('12', 'scholarship_mgmt', 'Quản lý Học bổng', 'bi-award-fill', '12', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('13', 'report_analytics', 'Báo cáo & Thống kê', 'bi-bar-chart-fill', '13', '2026-02-28 21:58:01');
INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES ('14', 'system_admin', 'Quản trị Hệ thống', 'bi-gear-fill', '14', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `permissions`
-- ----------------------------------------

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL COMMENT 'Thuộc nhóm quyền nào',
  `code` varchar(100) NOT NULL COMMENT 'Mã quyền: students.view, students.create...',
  `name` varchar(200) NOT NULL COMMENT 'Tên hiển thị: Xem danh sách sinh viên',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết quyền này làm gì',
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1 = quyền hệ thống, không thể xóa',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `uq_perm_code` (`code`),
  KEY `fk_perm_group` (`group_id`),
  CONSTRAINT `fk_perm_group` FOREIGN KEY (`group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('1', '1', 'users.view', 'Xem danh sách tài khoản', 'Xem được danh sách tất cả tài khoản người dùng', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('2', '1', 'users.create', 'Tạo tài khoản mới', 'Tạo tài khoản người dùng mới', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('3', '1', 'users.edit', 'Sửa thông tin tài khoản', 'Chỉnh sửa thông tin tài khoản người dùng', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('4', '1', 'users.delete', 'Xóa tài khoản', 'Xóa tài khoản người dùng khỏi hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('5', '1', 'users.toggle_status', 'Kích hoạt/Khóa tài khoản', 'Bật/tắt trạng thái hoạt động của tài khoản', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('6', '1', 'users.reset_password', 'Đặt lại mật khẩu', 'Reset mật khẩu cho người dùng bất kỳ', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('7', '1', 'users.unlock', 'Mở khóa tài khoản bị khoá', 'Mở khóa tài khoản bị chặn do nhập sai mật khẩu', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('8', '1', 'users.assign_role', 'Gán/Đổi vai trò cho tài khoản', 'Thay đổi vai trò (role) của người dùng', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('9', '2', 'roles.view', 'Xem danh sách vai trò', 'Xem được danh sách các vai trò hiện có', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('10', '2', 'roles.create', 'Tạo vai trò mới', 'Thêm vai trò mới vào hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('11', '2', 'roles.edit', 'Sửa thông tin vai trò', 'Chỉnh sửa tên/mô tả vai trò', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('12', '2', 'roles.delete', 'Xóa vai trò', 'Xóa vai trò không còn dùng', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('13', '2', 'roles.assign_perm', 'Gán quyền cho vai trò', 'Cấp hoặc thu hồi quyền của một vai trò', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('14', '2', 'permissions.view', 'Xem danh sách quyền hạn', 'Xem toàn bộ danh sách quyền trong hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('15', '2', 'permissions.create', 'Tạo quyền hạn mới', 'Thêm quyền hạn mới vào hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('16', '2', 'permissions.edit', 'Sửa thông tin quyền hạn', 'Chỉnh sửa mô tả quyền hạn', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('17', '2', 'permissions.delete', 'Xóa quyền hạn', 'Xóa quyền hạn khỏi hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('18', '3', 'students.view', 'Xem danh sách sinh viên', 'Xem được danh sách sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('19', '3', 'students.view_detail', 'Xem chi tiết hồ sơ sinh viên', 'Xem thông tin chi tiết từng sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('20', '3', 'students.create', 'Thêm sinh viên mới', 'Nhập thông tin sinh viên mới vào hệ thống', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('21', '3', 'students.edit', 'Sửa thông tin sinh viên', 'Cập nhật thông tin hồ sơ sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('22', '3', 'students.delete', 'Xóa sinh viên', 'Xóa hồ sơ sinh viên khỏi hệ thống', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('23', '3', 'students.change_status', 'Đổi trạng thái học tập', 'Thay đổi trạng thái: Đang học, Bảo lưu, Thôi học, Tốt nghiệp', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('24', '3', 'students.create_account', 'Tạo tài khoản cho sinh viên', 'Khởi tạo tài khoản đăng nhập cho sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('25', '3', 'students.reset_password', 'Reset mật khẩu sinh viên', 'Đặt lại mật khẩu tài khoản sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('26', '3', 'students.export', 'Xuất danh sách sinh viên', 'Xuất file Excel/PDF danh sách sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('27', '3', 'students.import', 'Nhập danh sách sinh viên', 'Import sinh viên từ file Excel', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('28', '3', 'students.view_transcript', 'Xem bảng điểm sinh viên', 'Xem bảng điểm học tập của sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('29', '4', 'lecturers.view', 'Xem danh sách giảng viên', 'Xem danh sách tất cả giảng viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('30', '4', 'lecturers.view_detail', 'Xem chi tiết hồ sơ giảng viên', 'Xem thông tin chi tiết giảng viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('31', '4', 'lecturers.create', 'Thêm giảng viên mới', 'Thêm hồ sơ giảng viên mới', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('32', '4', 'lecturers.edit', 'Sửa thông tin giảng viên', 'Cập nhật thông tin hồ sơ giảng viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('33', '4', 'lecturers.delete', 'Xóa giảng viên', 'Xóa hồ sơ giảng viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('34', '4', 'lecturers.assign_class', 'Phân công lớp học', 'Phân công giảng viên cho lớp học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('35', '4', 'lecturers.view_schedule', 'Xem lịch giảng dạy', 'Xem lịch dạy của giảng viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('36', '5', 'faculties.view', 'Xem danh sách khoa', 'Xem danh sách các khoa/ngành', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('37', '5', 'faculties.create', 'Thêm khoa mới', 'Tạo mới khoa/ngành đào tạo', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('38', '5', 'faculties.edit', 'Sửa thông tin khoa', 'Cập nhật thông tin khoa/ngành', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('39', '5', 'faculties.delete', 'Xóa khoa', 'Xóa khoa/ngành khỏi hệ thống', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('40', '5', 'faculties.view_stats', 'Xem thống kê theo khoa', 'Xem báo cáo thống kê theo từng khoa', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('41', '6', 'subjects.view', 'Xem danh sách môn học', 'Xem danh sách tất cả môn học', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('42', '6', 'subjects.create', 'Thêm môn học mới', 'Thêm môn học mới vào chương trình', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('43', '6', 'subjects.edit', 'Sửa thông tin môn học', 'Cập nhật thông tin môn học', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('44', '6', 'subjects.delete', 'Xóa môn học', 'Xóa môn học khỏi hệ thống', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('45', '6', 'classes.view', 'Xem danh sách lớp học phần', 'Xem danh sách các lớp học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('46', '6', 'classes.create', 'Mở lớp học phần mới', 'Tạo lớp học phần mới trong học kỳ', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('47', '6', 'classes.edit', 'Sửa thông tin lớp học phần', 'Cập nhật thông tin lớp học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('48', '6', 'classes.delete', 'Xóa lớp học phần', 'Xóa lớp học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('49', '6', 'classes.view_schedule', 'Xem thời khóa biểu lớp', 'Xem lịch học của lớp học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('50', '6', 'base_classes.view', 'Xem lớp cơ sở', 'Xem danh sách lớp hành chính', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('51', '6', 'base_classes.manage', 'Quản lý lớp cơ sở', 'Tạo, sửa, xóa lớp hành chính', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('52', '6', 'semesters.view', 'Xem học kỳ', 'Xem danh sách học kỳ', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('53', '6', 'semesters.manage', 'Quản lý học kỳ', 'Tạo, cập nhật học kỳ', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('54', '7', 'grades.view', 'Xem điểm', 'Xem điểm số của sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('55', '7', 'grades.enter', 'Nhập điểm', 'Nhập/cập nhật điểm số cho sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('56', '7', 'grades.edit', 'Sửa điểm đã nhập', 'Chỉnh sửa điểm đã nhập (có log)', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('57', '7', 'grades.approve', 'Duyệt/Khóa điểm', 'Phê duyệt và khóa điểm không cho sửa', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('58', '7', 'grades.view_all', 'Xem điểm tất cả các lớp', 'Xem điểm của toàn trường, không giới hạn', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('59', '7', 'grades.export', 'Xuất bảng điểm', 'Xuất bảng điểm ra file Excel/PDF', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('60', '7', 'attendance.view', 'Xem điểm danh', 'Xem kết quả điểm danh', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('61', '7', 'attendance.enter', 'Nhập điểm danh', 'Điểm danh sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('62', '8', 'enrollment.view', 'Xem đăng ký học phần', 'Xem danh sách đăng ký học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('63', '8', 'enrollment.register', 'Đăng ký học phần', 'Sinh viên tự đăng ký học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('64', '8', 'enrollment.cancel', 'Hủy đăng ký học phần', 'Hủy đăng ký học phần đã đăng ký', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('65', '8', 'enrollment.manage', 'Quản lý đăng ký học phần', 'Admin/Giáo vụ quản lý đăng ký của sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('66', '8', 'enrollment.approve', 'Duyệt đăng ký học phần', 'Phê duyệt yêu cầu đăng ký học phần', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('67', '9', 'tuition.view', 'Xem học phí của mình', 'Sinh viên xem học phí của bản thân', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('68', '9', 'tuition.view_all', 'Xem học phí tất cả sinh viên', 'Admin/Kế toán xem học phí toàn trường', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('69', '9', 'tuition.manage', 'Quản lý học phí', 'Tạo, cập nhật thông tin học phí', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('70', '9', 'tuition.record_payment', 'Ghi nhận thanh toán', 'Ghi nhận SV đã nộp học phí', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('71', '10', 'dormitory.view', 'Xem thông tin ký túc xá', 'Xem thông tin phòng ký túc xá', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('72', '10', 'dormitory.register', 'Đăng ký ký túc xá', 'Sinh viên đăng ký phòng ký túc xá', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('73', '10', 'dormitory.manage', 'Quản lý ký túc xá', 'Admin quản lý phòng và đăng ký KTX', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('74', '10', 'dormitory.approve', 'Duyệt đăng ký ký túc xá', 'Phê duyệt yêu cầu đăng ký KTX', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('75', '11', 'library.view', 'Xem thư viện', 'Tìm kiếm và xem sách trong thư viện', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('76', '11', 'library.borrow', 'Mượn sách', 'Đăng ký mượn sách thư viện', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('77', '11', 'library.manage', 'Quản lý thư viện', 'Quản lý sách, mượn trả trong thư viện', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('78', '12', 'scholarship.view', 'Xem học bổng', 'Xem danh sách học bổng đang mở', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('79', '12', 'scholarship.apply', 'Nộp đơn học bổng', 'Sinh viên nộp đơn xin học bổng', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('80', '12', 'scholarship.manage', 'Quản lý học bổng', 'Tạo, sửa, xóa thông tin học bổng', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('81', '12', 'scholarship.approve', 'Xét duyệt học bổng', 'Phê duyệt/từ chối đơn học bổng', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('82', '13', 'reports.view', 'Xem báo cáo tổng quan', 'Xem báo cáo thống kê tổng quan hệ thống', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('83', '13', 'reports.student', 'Xem báo cáo sinh viên', 'Báo cáo thống kê sinh viên', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('84', '13', 'reports.grade', 'Xem báo cáo điểm', 'Báo cáo phân phối điểm, kết quả học tập', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('85', '13', 'reports.finance', 'Xem báo cáo tài chính', 'Báo cáo thu học phí, nợ học phí', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('86', '13', 'reports.export', 'Xuất báo cáo', 'Xuất báo cáo ra file Excel/PDF', '0', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('87', '14', 'system.audit_logs', 'Xem nhật ký hệ thống', 'Xem lịch sử mọi thao tác trong hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('88', '14', 'system.backup', 'Sao lưu & khôi phục CSDL', 'Tạo bản sao lưu và khôi phục dữ liệu', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('89', '14', 'system.settings', 'Cài đặt hệ thống', 'Thay đổi cấu hình hệ thống', '1', '2026-02-28 21:58:01');
INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES ('90', '14', 'system.dashboard', 'Xem Dashboard quản trị', 'Xem trang tổng quan quản trị', '1', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `role_permissions`
-- ----------------------------------------

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` datetime DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL COMMENT 'Admin nào đã gán quyền này',
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_rp_perm` (`permission_id`),
  CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '18', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '19', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '20', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '21', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '23', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '29', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '30', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '31', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '32', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '34', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '36', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '37', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '38', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '40', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '41', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '42', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '43', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '45', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '46', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '47', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '49', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '50', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '51', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '52', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '53', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '54', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '55', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '56', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '57', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '58', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '59', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '62', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '65', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '66', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '82', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '83', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '84', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('2', '86', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '18', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '19', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '20', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '21', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '22', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '23', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '24', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '25', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '26', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '28', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '62', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '65', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '71', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '73', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '74', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '78', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '80', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '81', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '82', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '83', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('3', '86', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '18', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '19', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '45', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '49', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '54', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '55', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '56', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '59', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '60', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '61', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '62', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '82', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('4', '84', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('5', '28', '2026-02-28 22:41:59', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('5', '63', '2026-02-28 22:41:59', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('5', '64', '2026-02-28 22:41:59', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('5', '67', '2026-02-28 22:41:59', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('6', '68', '2026-03-02 20:42:42', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('6', '69', '2026-03-02 20:42:42', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('6', '82', '2026-03-02 20:42:42', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('7', '75', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('7', '76', '2026-02-28 21:58:01', NULL);
INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES ('7', '77', '2026-02-28 21:58:01', NULL);

-- ----------------------------------------
-- Table: `roles`
-- ----------------------------------------

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(10) DEFAULT '#6c757d' COMMENT 'Màu badge hiển thị',
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1 = role hệ thống, không thể xóa',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('1', 'super_admin', 'Super Admin', 'Quản trị viên tối cao - toàn quyền hệ thống', '#dc3545', '1', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('2', 'academic_admin', 'Quản lý Đào tạo', 'Quản lý chuyên trách về đào tạo, chương trình học', '#0d6efd', '0', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('3', 'student_admin', 'Quản lý Sinh viên', 'Phòng công tác sinh viên', '#198754', '0', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('4', 'teacher', 'Giảng viên', 'Giảng viên giảng dạy', '#0dcaf0', '0', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('5', 'student', 'Sinh viên', 'Sinh viên theo học', '#6f42c1', '0', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('6', 'accountant', 'Kế toán', 'Bộ phận kế toán, tài chính', '#fd7e14', '0', '2026-02-28 21:58:01');
INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES ('7', 'librarian', 'Thủ thư', 'Quản lý thư viện', '#20c997', '0', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `scholarship_applications`
-- ----------------------------------------

DROP TABLE IF EXISTS `scholarship_applications`;
CREATE TABLE `scholarship_applications` (
  `application_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  KEY `fk_sa_student` (`student_id`),
  KEY `fk_sa_scholarship` (`scholarship_id`),
  CONSTRAINT `fk_sa_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`),
  CONSTRAINT `fk_sa_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `scholarship_applications` (`application_id`, `student_id`, `scholarship_id`, `status`, `applied_at`, `reviewed_at`, `note`) VALUES ('1', '2', '3', 'Pending', '2026-02-28 22:41:07', NULL, NULL);

-- ----------------------------------------
-- Table: `scholarships`
-- ----------------------------------------

DROP TABLE IF EXISTS `scholarships`;
CREATE TABLE `scholarships` (
  `scholarship_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `value` decimal(12,0) NOT NULL DEFAULT 0,
  `min_gpa` decimal(5,2) DEFAULT NULL,
  `max_gpa` decimal(5,2) DEFAULT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `quantity` int(11) DEFAULT NULL,
  `deadline` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`scholarship_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('1', 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 8.5 trở lên', '5000000', '8.50', NULL, 'Spring', '2026', '20', '2026-03-15', '1', '2026-02-28 21:58:01');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('2', 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 7.5 đến dưới 8.5', '2000000', '7.50', '8.49', 'Spring', '2026', '50', '2026-03-15', '1', '2026-02-28 21:58:01');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('3', 'Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', '1500000', NULL, NULL, 'Spring', '2026', '30', '2026-03-20', '1', '2026-02-28 21:58:01');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('4', 'Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', '10000000', '8.00', NULL, 'Spring', '2026', '5', '2026-03-10', '1', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `semesters`
-- ----------------------------------------

DROP TABLE IF EXISTS `semesters`;
CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL AUTO_INCREMENT,
  `semester_code` varchar(20) NOT NULL,
  `semester_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`semester_id`),
  UNIQUE KEY `semester_code` (`semester_code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `semesters` (`semester_id`, `semester_code`, `semester_name`, `start_date`, `end_date`, `is_active`, `created_at`, `is_current`) VALUES ('1', 'HK1-2025', 'Học kỳ 1 năm 2025', '2025-01-06', '2025-05-30', '0', '2026-02-28 21:58:01', '0');
INSERT INTO `semesters` (`semester_id`, `semester_code`, `semester_name`, `start_date`, `end_date`, `is_active`, `created_at`, `is_current`) VALUES ('2', 'HK2-2025', 'Học kỳ 2 năm 2025', '2025-06-02', '2025-09-30', '0', '2026-02-28 21:58:01', '1');
INSERT INTO `semesters` (`semester_id`, `semester_code`, `semester_name`, `start_date`, `end_date`, `is_active`, `created_at`, `is_current`) VALUES ('3', 'HK1-2026', 'Học kỳ 1 năm 2026', '2026-01-05', '2026-05-30', '1', '2026-02-28 21:58:01', '0');

-- ----------------------------------------
-- Table: `students`
-- ----------------------------------------

DROP TABLE IF EXISTS `students`;
CREATE TABLE `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `student_code` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `birth_date` date NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `faculty_id` int(11) NOT NULL,
  `base_class_id` int(11) DEFAULT NULL,
  `status` enum('Studying','Graduated','Suspended','Dropped') DEFAULT 'Studying',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `student_code` (`student_code`),
  KEY `fk_stu_user` (`user_id`),
  KEY `fk_stu_faculty` (`faculty_id`),
  KEY `fk_stu_class` (`base_class_id`),
  KEY `idx_students_faculty` (`faculty_id`),
  KEY `idx_students_status` (`status`),
  CONSTRAINT `fk_stu_class` FOREIGN KEY (`base_class_id`) REFERENCES `base_classes` (`base_class_id`) ON DELETE SET NULL,
  CONSTRAINT `fk_stu_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `fk_stu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('1', '20', 'SV000', 'Nam', 'Tran', 'Male', '2004-03-15', 'student00@university.edu', '0123456789', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('2', '21', 'SV001', 'An', 'Nguyen', 'Male', '2004-01-15', 'student01@university.edu', '0901000001', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('3', '22', 'SV002', 'Binh', 'Tran', 'Female', '2004-02-20', 'student02@university.edu', '0901000002', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('4', '23', 'SV003', 'Chi', 'Le', 'Male', '2004-03-10', 'student03@university.edu', '0901000003', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('5', '24', 'SV004', 'Dung', 'Pham', 'Female', '2004-04-05', 'student04@university.edu', '0901000004', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('6', '25', 'SV005', 'Hoa', 'Vu', 'Male', '2004-05-25', 'student05@university.edu', '0901000005', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('7', '26', 'SV006', 'Khanh', 'Do', 'Female', '2004-06-12', 'student06@university.edu', '0901000006', '1', '1', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('8', '27', 'SV007', 'Lan', 'Hoang', 'Male', '2004-07-08', 'student07@university.edu', '0901000007', '1', '2', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('9', '28', 'SV008', 'Minh', 'Nguyen', 'Female', '2004-08-18', 'student08@university.edu', '0901000008', '2', '3', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('10', '29', 'SV009', 'Nam', 'Tran', 'Male', '2004-09-22', 'student09@university.edu', '0901000009', '2', '3', 'Studying', '2026-02-28 21:58:01');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('11', '30', 'SV010', 'Oanh', 'Le', 'Female', '2004-10-30', 'student10@university.edu', '0901000010', '2', '4', 'Studying', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `subjects`
-- ----------------------------------------

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `credit_hours` int(11) NOT NULL DEFAULT 3,
  `faculty_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prerequisite_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_code` (`subject_code`),
  KEY `fk_sub_faculty` (`faculty_id`),
  KEY `prerequisite_id` (`prerequisite_id`),
  CONSTRAINT `fk_sub_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE SET NULL,
  CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`prerequisite_id`) REFERENCES `subjects` (`subject_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('1', 'CNTT101', 'Lập trình cơ bản', '3', '1', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('2', 'CNTT201', 'Cấu trúc dữ liệu và giải thuật', '3', '1', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('3', 'CNTT301', 'Cơ sở dữ liệu', '3', '1', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('4', 'QTKD101', 'Quản trị kinh doanh đại cương', '3', '2', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('5', 'QTKD201', 'Marketing căn bản', '3', '2', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('6', 'QTKD301', 'Quản trị chiến lược', '3', '2', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('7', 'ENGL101', 'Tiếng Anh cơ bản 1', '4', '3', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('8', 'ENGL201', 'Tiếng Anh cơ bản 2', '4', '3', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('9', 'ENGL301', 'Tiếng Anh nâng cao', '4', '3', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('10', 'KT101', 'Kế toán đại cương', '3', '4', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('11', 'KT201', 'Kế toán tài chính', '3', '4', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('12', 'KT301', 'Kiểm toán', '3', '4', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('13', 'TCNH101', 'Tài chính tiền tệ', '3', '5', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('14', 'TCNH201', 'Nghiệp vụ ngân hàng', '3', '5', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('15', 'TCNH301', 'Thị trường chứng khoán', '3', '5', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('16', 'SP101', 'Tâm lý học giáo dục', '3', '6', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('17', 'SP201', 'Lý luận dạy học', '3', '6', NULL, NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES ('18', 'SP301', 'Thực hành giảng dạy', '3', '6', NULL, NULL);

-- ----------------------------------------
-- Table: `system_settings`
-- ----------------------------------------

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('allow_register', '0', 'boolean', 'Cho phép tự đăng ký', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('lock_duration', '15', 'integer', 'Thời gian khóa tài khoản (phút)', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('max_login_fail', '5', 'integer', 'Số lần nhập sai tối đa trước khi khóa', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('rbac_cache_ttl', '300', 'integer', 'Thời gian cache permission (giây)', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('site_name', 'Hệ thống Quản lý Đào tạo', 'string', 'Tên hệ thống', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('site_url', 'http://localhost/web_QLSV', 'string', 'URL gốc', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('smtp_host', '', 'string', 'SMTP server', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('smtp_pass', '', 'string', 'SMTP password', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('smtp_port', '587', 'integer', 'SMTP port', '2026-02-28 21:58:01');
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES ('smtp_user', '', 'string', 'SMTP email', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `tuition_fees`
-- ----------------------------------------

DROP TABLE IF EXISTS `tuition_fees`;
CREATE TABLE `tuition_fees` (
  `fee_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `amount` decimal(12,0) NOT NULL DEFAULT 0,
  `paid_amount` decimal(12,0) DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `status` enum('Unpaid','PartialPaid','Paid') DEFAULT 'Unpaid',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`fee_id`),
  KEY `fk_tf_student` (`student_id`),
  CONSTRAINT `fk_tf_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES ('1', '1', 'Spring', '2026', '8000000', '8000000', '2026-02-15', 'Paid', '2026-02-28 21:58:01');
INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES ('2', '2', 'Spring', '2026', '8000000', '8000000', '2026-02-15', 'Paid', '2026-02-28 21:58:01');
INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES ('3', '3', 'Spring', '2026', '8000000', '4000000', '2026-02-15', 'PartialPaid', '2026-02-28 21:58:01');
INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES ('4', '4', 'Spring', '2026', '8000000', '0', '2026-02-15', 'Unpaid', '2026-02-28 21:58:01');
INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES ('5', '5', 'Spring', '2026', '8000000', '8000000', '2026-02-15', 'Paid', '2026-02-28 21:58:01');

-- ----------------------------------------
-- Table: `user_permission_cache`
-- ----------------------------------------

DROP TABLE IF EXISTS `user_permission_cache`;
CREATE TABLE `user_permission_cache` (
  `user_id` int(11) NOT NULL,
  `permission_code` varchar(100) NOT NULL,
  `cached_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`,`permission_code`),
  CONSTRAINT `fk_upc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------
-- Table: `user_roles`
-- ----------------------------------------

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_ur_role` (`role_id`),
  CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('1', '1', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('2', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('3', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('4', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('5', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('6', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('7', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('8', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('9', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('10', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('11', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('12', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('13', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('14', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('15', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('16', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('17', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('18', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('19', '4', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('20', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('21', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('22', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('23', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('24', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('25', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('26', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('27', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('28', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('29', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('30', '5', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('200', '2', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('201', '3', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('202', '6', '2026-02-28 21:58:01', NULL);
INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES ('203', '7', '2026-02-28 21:58:01', NULL);

-- ----------------------------------------
-- Table: `users`
-- ----------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=204 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('1', 'admin', 'superadmin@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-03-03 13:56:21', '2026-02-28 21:58:01', '2026-03-03 13:56:21');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('2', 'gv01', 'gv01@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-03-02 18:20:38', '2026-02-28 21:58:01', '2026-03-02 18:20:38');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('3', 'gv02', 'gv02@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('4', 'gv03', 'gv03@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('5', 'gv04', 'gv04@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('6', 'gv05', 'gv05@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('7', 'gv06', 'gv06@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('8', 'gv07', 'gv07@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('9', 'gv08', 'gv08@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('10', 'gv09', 'gv09@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('11', 'gv10', 'gv10@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('12', 'gv11', 'gv11@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('13', 'gv12', 'gv12@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('14', 'gv13', 'gv13@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('15', 'gv14', 'gv14@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('16', 'gv15', 'gv15@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('17', 'gv16', 'gv16@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('18', 'gv17', 'gv17@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('19', 'gv18', 'gv18@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('20', 'sv00', 'student00@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('21', 'sv01', 'student01@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-03-02 18:17:56', '2026-02-28 21:58:01', '2026-03-02 18:17:56');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('22', 'sv02', 'student02@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('23', 'sv03', 'student03@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('24', 'sv04', 'student04@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-02-28 22:43:11', '2026-02-28 21:58:01', '2026-02-28 22:43:11');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('25', 'sv05', 'student05@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('26', 'sv06', 'student06@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-02-28 22:44:58', '2026-02-28 21:58:01', '2026-02-28 22:44:58');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('27', 'sv07', 'student07@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('28', 'sv08', 'student08@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('29', 'sv09', 'student09@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('30', 'sv10', 'student10@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('200', 'daotao', 'academic1@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-03-02 18:51:11', '2026-02-28 21:58:01', '2026-03-02 18:51:11');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('201', 'student_admin1', 'studentadmin@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-02-28 22:46:58', '2026-02-28 21:58:01', '2026-02-28 22:46:58');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('202', 'accountant1', 'accountant@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46');
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES ('203', 'thuvien', 'librarian@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', '1', '0', NULL, '2026-03-02 18:46:21', '2026-02-28 21:58:01', '2026-03-02 18:46:21');

SET FOREIGN_KEY_CHECKS = 1;
