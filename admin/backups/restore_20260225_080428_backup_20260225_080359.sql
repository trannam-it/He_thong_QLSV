
-- ==============================================
-- Database Backup: qlsv
-- Generated: 2026-02-25 08:03:59
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
  `class_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') NOT NULL DEFAULT 'Present',
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uq_class_student_date` (`class_id`,`student_id`,`date`),
  KEY `idx_class_date` (`class_id`,`date`),
  KEY `idx_student_date` (`student_id`,`date`),
  CONSTRAINT `fk_att_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------
-- Table: `audit_logs`
-- ----------------------------------------

DROP TABLE IF EXISTS `audit_logs`;
CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
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
  KEY `idx_audit_table` (`table_name`),
  KEY `idx_audit_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('1', '0', 'admin', 'LOGIN_FAIL', 'users', NULL, NULL, '{\"reason\":\"USER_NOT_FOUND\"}', '::1', '2026-02-21 21:17:46');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('2', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-21 21:18:06');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('3', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":2,\"locked_until\":null}', '::1', '2026-02-21 21:18:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('4', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-21 21:26:24');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('5', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-21 21:26:39');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('6', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-21 21:32:03');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('7', '0', 'superadmin@university.edu', 'LOGIN_FAIL', 'users', NULL, NULL, '{\"reason\":\"USER_NOT_FOUND\"}', '::1', '2026-02-23 16:45:35');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('8', '0', 'superadmin@university.edu', 'LOGIN_FAIL', 'users', NULL, NULL, '{\"reason\":\"USER_NOT_FOUND\"}', '::1', '2026-02-23 16:45:47');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('9', '0', 'superadmin@university.edu', 'LOGIN_FAIL', 'users', NULL, NULL, '{\"reason\":\"USER_NOT_FOUND\"}', '::1', '2026-02-23 16:46:33');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('10', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-23 16:47:00');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('11', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-23 16:48:12');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('12', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-23 16:50:39');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('13', '24', 'sv05', 'LOGIN_SUCCESS', 'users', '24', NULL, '{\"role\":\"student\"}', '::1', '2026-02-23 16:58:46');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('14', '17', 'gv16', 'LOGIN_SUCCESS', 'users', '17', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-23 17:00:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('15', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 22:24:32');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('16', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 22:24:45');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('17', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 22:40:48');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('18', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 22:40:55');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('19', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 23:04:52');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('20', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-24 23:08:44');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('21', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 00:08:14');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('22', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 00:09:14');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('23', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 00:09:48');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('24', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 00:23:55');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('25', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 00:40:49');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('26', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 00:41:50');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('27', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 00:42:05');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('28', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 01:04:55');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('29', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 01:14:40');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('30', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 01:16:06');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('31', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 01:16:58');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('32', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 01:19:13');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('33', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 01:24:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('34', '5', 'gv04', 'LOGIN_SUCCESS', 'users', '5', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 01:32:21');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('35', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 01:53:35');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('36', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 02:03:32');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('37', '5', 'gv04', 'LOGIN_SUCCESS', 'users', '5', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 02:04:09');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('38', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 02:05:23');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('39', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 02:07:19');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('40', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 02:08:37');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('41', '5', 'gv04', 'LOGIN_SUCCESS', 'users', '5', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 02:09:53');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('42', '5', 'gv04', 'LOGIN_SUCCESS', 'users', '5', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 02:12:40');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('43', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 07:14:44');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('44', '1', 'superadmin', 'RESET_PASSWORD', 'users', '199', NULL, NULL, '::1', '2026-02-25 07:26:34');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('45', '1', 'superadmin', 'TOGGLE_STATUS', 'users', '198', '{\"is_active\":1}', '{\"is_active\":0}', '::1', '2026-02-25 07:27:15');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('46', '1', 'superadmin', 'TOGGLE_STATUS', 'users', '198', '{\"is_active\":0}', '{\"is_active\":1}', '::1', '2026-02-25 07:27:17');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('47', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 07:31:15');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('48', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 07:46:20');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('49', '23', 'sv04', 'LOGIN_SUCCESS', 'users', '23', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 07:53:13');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('50', '24', 'sv05', 'LOGIN_SUCCESS', 'users', '24', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 07:55:28');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('51', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 07:57:02');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('52', '1', 'superadmin', 'CREATE', 'users', '200', NULL, '{\"username\":\"svminh\",\"email\":\"minhka5k@gmail.com\"}', '::1', '2026-02-25 07:57:29');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('53', '200', 'svminh', 'LOGIN_FAIL', 'users', '200', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 07:57:36');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('54', '200', 'svminh', 'LOGIN_FAIL', 'users', '200', NULL, '{\"failed_attempts\":2,\"locked_until\":null}', '::1', '2026-02-25 07:57:40');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('55', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 07:57:46');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('56', '1', 'superadmin', 'RESET_PASSWORD', 'users', '200', NULL, NULL, '::1', '2026-02-25 07:57:53');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('57', '200', 'svminh', 'LOGIN_FAIL', 'users', '200', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 07:58:02');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('58', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 07:58:11');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('59', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 07:58:16');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('60', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:06:01');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('61', '1', 'superadmin', 'UPDATE', 'users', '200', NULL, '{\"username\":\"svminh\",\"email\":\"minhka5k@gmail.com\"}', '::1', '2026-02-25 08:06:19');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('62', '200', 'svminh', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:06:27');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('63', '27', 'sv08', 'LOGIN_SUCCESS', 'users', '27', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:06:47');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('64', '200', 'svminh', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:07:02');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('65', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:07:12');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('66', '1', 'superadmin', 'CREATE', 'users', '201', NULL, '{\"username\":\"minh\",\"email\":\"minhhvcb@gmail.com\"}', '::1', '2026-02-25 08:17:37');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('67', '1', 'superadmin', 'RESET_PASSWORD', 'users', '201', NULL, NULL, '::1', '2026-02-25 08:17:42');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('68', '201', 'minh', 'LOGIN_SUCCESS', 'users', '201', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:17:47');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('69', '200', 'svminh', 'LOGIN_SUCCESS', 'users', '200', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:19:14');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('70', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:19:28');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('71', '1', 'superadmin', 'DELETE', 'users', '200', NULL, NULL, '::1', '2026-02-25 08:19:39');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('72', '1', 'superadmin', 'CREATE', 'users', '202', NULL, '{\"username\":\"123456\",\"email\":\"minhka5k@gmail.com\"}', '::1', '2026-02-25 08:20:02');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('73', '202', '123456', 'LOGIN_SUCCESS', 'users', '202', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 08:20:12');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('74', '6', 'gv05', 'LOGIN_SUCCESS', 'users', '6', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 08:23:24');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('75', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 08:23:37');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('76', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:23:42');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('77', '1', 'superadmin', 'CREATE', 'users', '203', NULL, '{\"username\":\"gvminh\",\"email\":\"fdsfdfs@gmail.com\"}', '::1', '2026-02-25 08:24:12');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('78', '203', 'gvminh', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 08:26:09');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('79', '203', 'gvminh', 'LOGIN_SUCCESS', 'users', '203', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 08:26:24');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('80', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:31:39');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('81', '0', 'gvminh1', 'LOGIN_FAIL', 'users', NULL, NULL, '{\"reason\":\"USER_NOT_FOUND\"}', '::1', '2026-02-25 08:32:28');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('82', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 08:32:35');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('83', '1', 'superadmin', 'CREATE', 'users', '204', NULL, '{\"username\":\"gvminh1\",\"email\":\"sadasdaa5k@gmail.com\"}', '::1', '2026-02-25 08:32:54');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('84', '204', 'gvminh1', 'LOGIN_SUCCESS', 'users', '204', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 08:33:01');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('85', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 09:50:22');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('86', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 09:50:33');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('87', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 09:51:33');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('88', '20', 'sv01', 'LOGIN_SUCCESS', 'users', '20', NULL, '{\"role\":\"student\"}', '::1', '2026-02-25 09:53:13');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('89', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 09:54:52');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('90', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 10:03:08');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('91', '1', 'superadmin', 'CREATE', 'base_classes', '13', NULL, '{\"base_class_code\":\"CNTT12\",\"base_class_name\":\"C\\u00f4ng ngh\\u1ec7 th\\u00f4ng tin kh\\u00f3a 12\",\"faculty_id\":\"1\",\"lecturer_id\":\"10\",\"start_year\":\"2026\",\"end_year\":\"2028\"}', '::1', '2026-02-25 10:34:57');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('92', '1', 'superadmin', 'CREATE', 'base_classes', '14', NULL, '{\"base_class_code\":\"dsfdsf\",\"base_class_name\":\"dsfdsf\",\"faculty_id\":\"5\",\"lecturer_id\":\"12\",\"start_year\":\"2026\",\"end_year\":\"2029\"}', '::1', '2026-02-25 10:38:09');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('93', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 12:58:52');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('94', '2', 'gv01', 'LOGIN_SUCCESS', 'users', '2', NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-25 13:25:03');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('95', '1', 'superadmin', 'LOGIN_FAIL', 'users', '1', NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-25 14:00:56');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('96', '1', 'superadmin', 'LOGIN_SUCCESS', 'users', '1', NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-25 14:01:09');
INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES ('97', '1', 'superadmin', 'ASSIGN_PERMISSIONS', 'role_permissions', '2', NULL, '{\"count\":2}', '::1', '2026-02-25 14:01:51');

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
  KEY `idx_base_classes_faculty` (`faculty_id`),
  KEY `idx_base_classes_lecturer` (`lecturer_id`),
  KEY `idx_base_classes_year` (`start_year`,`end_year`),
  CONSTRAINT `base_classes_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `base_classes_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('13', 'CNTT12', 'Công nghệ thông tin khóa 12', '1', '10', '2026', '2028');
INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES ('14', 'dsfdsf', 'dsfdsf', '5', '12', '2026', '2029');

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
  KEY `class_id` (`class_id`),
  CONSTRAINT `class_schedules_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('13', '7', '2', '1', '2', 'C101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('14', '7', '4', '1', '2', 'C101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('15', '8', '3', '3', '5', 'C102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('16', '8', '5', '3', '5', 'C102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('17', '9', '4', '6', '8', 'C103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('18', '9', '6', '6', '8', 'C103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('19', '10', '2', '3', '5', 'D101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('20', '10', '5', '3', '5', 'D101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('21', '11', '3', '7', '9', 'D102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('22', '11', '6', '7', '9', 'D102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('23', '12', '4', '4', '6', 'D103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('24', '12', '7', '4', '6', 'D103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('25', '13', '2', '6', '8', 'E201');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('26', '13', '5', '6', '8', 'E201');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('27', '14', '3', '1', '3', 'E202');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('28', '14', '6', '1', '3', 'E202');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('29', '15', '4', '7', '9', 'E203');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('30', '15', '7', '7', '9', 'E203');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('31', '16', '2', '4', '6', 'F101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('32', '16', '4', '4', '6', 'F101');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('33', '17', '3', '6', '8', 'F102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('34', '17', '5', '6', '8', 'F102');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('35', '18', '5', '1', '3', 'F103');
INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES ('36', '18', '7', '1', '3', 'F103');

-- ----------------------------------------
-- Table: `classes`
-- ----------------------------------------

DROP TABLE IF EXISTS `classes`;
CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT,
  `class_code` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  PRIMARY KEY (`class_id`),
  UNIQUE KEY `class_code` (`class_code`),
  KEY `idx_classes_subject` (`subject_id`),
  KEY `idx_classes_lecturer` (`lecturer_id`),
  KEY `idx_classes_year_semester` (`year`,`semester`),
  CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('1', 'CNTT101-01', '1', '1', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('2', 'CNTT201-01', '2', '2', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('3', 'CNTT301-01', '3', '3', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('4', 'QTKD101-01', '4', '4', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('5', 'QTKD201-01', '5', '5', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('6', 'QTKD301-01', '6', '6', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('7', 'ENGL101-01', '7', '7', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('8', 'ENGL201-01', '8', '8', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('9', 'ENGL301-01', '9', '9', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('10', 'KT101-01', '10', '10', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('11', 'KT201-01', '11', '11', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('12', 'KT301-01', '12', '12', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('13', 'TCNH101-01', '13', '13', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('14', 'TCNH201-01', '14', '14', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('15', 'TCNH301-01', '15', '15', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('16', 'SP101-01', '16', '16', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('17', 'SP201-01', '17', '17', 'Summer', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('18', 'SP301-01', '18', '18', 'Fall', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('22', 'KT301-02', '12', '4', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('23', 'KT101-02', '10', '4', 'Spring', '2026');
INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES ('24', 'ENGL201-02', '8', '4', 'Spring', '2026');

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
  KEY `fk_dorm_student` (`student_id`),
  KEY `fk_dorm_room` (`room_id`),
  CONSTRAINT `fk_dorm_room` FOREIGN KEY (`room_id`) REFERENCES `dormitory_rooms` (`room_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dorm_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dormitory_registrations` (`registration_id`, `student_id`, `room_id`, `start_date`, `end_date`, `status`, `note`, `registered_at`, `updated_at`) VALUES ('1', '37', '6', '2026-02-25', '2026-05-02', 'Pending', NULL, '2026-02-25 07:56:34', '2026-02-25 07:56:34');

-- ----------------------------------------
-- Table: `dormitory_rooms`
-- ----------------------------------------

DROP TABLE IF EXISTS `dormitory_rooms`;
CREATE TABLE `dormitory_rooms` (
  `room_id` int(11) NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `building` varchar(50) NOT NULL DEFAULT 'T├▓a A',
  `room_type` enum('Single','Double','Triple','Quad') NOT NULL DEFAULT 'Double',
  `price_per_month` decimal(10,0) NOT NULL DEFAULT 500000,
  `total_beds` int(11) NOT NULL DEFAULT 2,
  `available_beds` int(11) NOT NULL DEFAULT 2,
  `floor` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `uq_room` (`building`,`room_number`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('1', 'A101', 'Tòa A', 'Double', '400000', '2', '1', '1', 'Phòng đôi tầng 1, có điều hòa', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('2', 'A102', 'Tòa A', 'Double', '400000', '2', '2', '1', 'Phòng đôi tầng 1, có điều hòa', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('3', 'A201', 'Tòa A', 'Quad', '300000', '4', '2', '2', 'Phòng 4 người tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('4', 'A202', 'Tòa A', 'Quad', '300000', '4', '4', '2', 'Phòng 4 người tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('5', 'A301', 'Tòa A', 'Single', '700000', '1', '0', '3', 'Phòng đơn tầng 3, tiện nghi cao', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('6', 'A302', 'Tòa A', 'Single', '700000', '1', '0', '3', 'Phòng đơn tầng 3, tiện nghi cao', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('7', 'B101', 'Tòa B', 'Double', '380000', '2', '2', '1', 'Phòng đôi tòa B tầng 1', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('8', 'B102', 'Tòa B', 'Double', '380000', '2', '1', '1', 'Phòng đôi tòa B tầng 1', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('9', 'B201', 'Tòa B', 'Triple', '350000', '3', '3', '2', 'Phòng 3 người tòa B tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('10', 'B202', 'Tòa B', 'Triple', '350000', '3', '0', '2', 'Phòng 3 người tòa B tầng 2', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('11', 'C101', 'Tòa C', 'Quad', '280000', '4', '4', '1', 'Phòng 4 người tòa C giá rẻ', '1');
INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES ('12', 'C102', 'Tòa C', 'Quad', '280000', '4', '2', '1', 'Phòng 4 người tòa C giá rẻ', '1');

-- ----------------------------------------
-- Table: `enrollments`
-- ----------------------------------------

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('Registered','Completed','Cancelled','Failed') DEFAULT 'Registered',
  PRIMARY KEY (`enrollment_id`),
  UNIQUE KEY `student_id` (`student_id`,`class_id`),
  KEY `idx_enrollments_student` (`student_id`),
  KEY `idx_enrollments_class` (`class_id`),
  KEY `idx_enrollments_status` (`status`),
  KEY `idx_view_results_enroll` (`enrollment_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=228 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('16', '33', '1', '2026-01-21 00:20:57', 'Cancelled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('17', '34', '2', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('18', '35', '3', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('19', '36', '1', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('20', '37', '2', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('21', '40', '3', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('22', '43', '1', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('23', '45', '2', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('24', '42', '3', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('25', '50', '1', '2026-01-21 00:20:57', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('26', '51', '2', '2026-01-21 00:20:57', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('27', '53', '3', '2026-01-21 00:20:57', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('28', '49', '1', '2026-01-21 00:20:57', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('29', '47', '2', '2026-01-21 00:20:57', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('30', '46', '3', '2026-01-21 00:20:57', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('125', '62', '6', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('126', '63', '4', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('127', '89', '5', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('128', '88', '6', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('129', '87', '4', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('130', '86', '5', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('131', '85', '6', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('132', '79', '4', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('133', '80', '5', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('134', '81', '6', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('135', '82', '4', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('136', '83', '5', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('137', '84', '6', '2026-01-21 00:30:18', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('138', '94', '5', '2026-01-21 00:30:18', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('139', '92', '6', '2026-01-21 00:31:13', 'Failed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('140', '94', '4', '2026-01-21 00:31:13', 'Failed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('141', '91', '5', '2026-01-21 00:31:13', 'Failed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('142', '96', '4', '2026-01-21 00:31:43', 'Cancelled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('143', '97', '5', '2026-01-21 00:31:43', 'Cancelled');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('144', '98', '5', '2026-01-21 00:32:49', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('145', '89', '6', '2026-01-21 00:32:49', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('146', '91', '4', '2026-01-21 00:32:49', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('182', '229', '16', '2026-01-21 00:50:51', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('183', '230', '17', '2026-01-21 00:50:51', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('184', '231', '18', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('185', '232', '16', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('186', '233', '17', '2026-01-21 00:50:51', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('187', '234', '18', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('188', '235', '16', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('189', '236', '17', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('190', '237', '18', '2026-01-21 00:50:51', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('191', '238', '16', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('192', '239', '16', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('193', '240', '17', '2026-01-21 00:50:51', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('194', '241', '18', '2026-01-21 00:50:51', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('195', '173', '16', '2026-01-21 00:55:34', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('196', '174', '17', '2026-01-21 00:55:34', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('197', '175', '18', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('198', '176', '16', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('199', '177', '17', '2026-01-21 00:55:34', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('200', '178', '18', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('201', '179', '16', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('202', '180', '17', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('203', '181', '18', '2026-01-21 00:55:34', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('204', '182', '16', '2026-01-21 00:55:34', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('205', '148', '10', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('206', '149', '11', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('207', '150', '12', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('208', '151', '10', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('209', '152', '11', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('210', '155', '11', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('211', '156', '12', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('212', '157', '10', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('213', '161', '11', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('214', '162', '12', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('215', '163', '10', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('216', '164', '11', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('217', '165', '12', '2026-01-21 01:00:02', 'Completed');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('218', '166', '10', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('219', '167', '11', '2026-01-21 01:00:02', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('220', '36', '10', '2026-02-25 00:45:31', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('221', '36', '13', '2026-02-25 00:45:40', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('222', '36', '4', '2026-02-25 02:03:36', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('223', '36', '18', '2026-02-25 02:03:38', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('224', '243', '10', '2026-02-25 08:18:29', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('225', '243', '22', '2026-02-25 08:18:33', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('226', '244', '11', '2026-02-25 08:20:21', 'Registered');
INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES ('227', '33', '22', '2026-02-25 09:53:29', 'Registered');

-- ----------------------------------------
-- Table: `faculties`
-- ----------------------------------------

DROP TABLE IF EXISTS `faculties`;
CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_code` varchar(20) NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`faculty_id`),
  UNIQUE KEY `faculty_code` (`faculty_code`),
  UNIQUE KEY `faculty_name` (`faculty_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('1', 'CNTT', 'Công nghệ thông tin', 'Khoa đào tạo các chuyên ngành về công nghệ thông tin');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('2', 'QTKD', 'Quản trị kinh doanh', 'Khoa đào tạo về quản trị và kinh doanh');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('3', 'NN', 'Ngôn ngữ Anh', 'Khoa đào tạo ngôn ngữ và văn hóa Anh');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('4', 'KT', 'Kế toán', 'Khoa đào tạo chuyên ngành kế toán và kiểm toán');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('5', 'TCNH', 'Tài chính - Ngân hàng', 'Khoa đào tạo chuyên ngành tài chính và ngân hàng');
INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES ('6', 'SP', 'Sư phạm', 'Khoa đào tạo giáo viên các bộ môn');

-- ----------------------------------------
-- Table: `faculty_addresses`
-- ----------------------------------------

DROP TABLE IF EXISTS `faculty_addresses`;
CREATE TABLE `faculty_addresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_code` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `idx_faculty_addresses_code` (`faculty_code`),
  CONSTRAINT `faculty_addresses_ibfk_1` FOREIGN KEY (`faculty_code`) REFERENCES `faculties` (`faculty_code`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('1', 'CNTT', 'Tòa nhà A1, Trường Đại học XYZ, Hà Nội', 'cntt@xyz.edu.vn');
INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('2', 'QTKD', 'Tòa nhà B2, Trường Đại học XYZ, Hà Nội', 'qtkd@xyz.edu.vn');
INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('3', 'NN', 'Tòa nhà C3, Trường Đại học XYZ, Hà Nội', 'nn@xyz.edu.vn');
INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('4', 'KT', 'Tòa nhà D4, Trường Đại học XYZ, Hà Nội', 'kt@xyz.edu.vn');
INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('5', 'TCNH', 'Tòa nhà E5, Trường Đại học XYZ, Hà Nội', 'tcnh@xyz.edu.vn');
INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES ('6', 'SP', 'Tòa nhà F6, Trường Đại học XYZ, Hà Nội', 'sp@xyz.edu.vn');

-- ----------------------------------------
-- Table: `grades`
-- ----------------------------------------

DROP TABLE IF EXISTS `grades`;
CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `grade_letter` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`grade_id`),
  UNIQUE KEY `enrollment_id` (`enrollment_id`),
  KEY `idx_grades_score` (`score`),
  KEY `idx_grades_letter` (`grade_letter`),
  CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('1', '26', '88.00', 'B');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('2', '182', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('3', '183', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('4', '186', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('5', '190', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('6', '194', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('9', '205', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('10', '206', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('11', '209', '86.00', 'B+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('12', '211', '75.00', 'C+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('13', '213', '75.00', 'C+');
INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES ('14', '217', '75.00', 'C+');

-- ----------------------------------------
-- Table: `lecturers`
-- ----------------------------------------

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `lecturer_code` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `faculty_id` int(11) NOT NULL,
  `degree` enum('Bachelor','Master','PhD','Professor') NOT NULL,
  PRIMARY KEY (`lecturer_id`),
  UNIQUE KEY `lecturer_code` (`lecturer_code`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_lecturers_faculty` (`faculty_id`),
  KEY `idx_lecturers_user` (`user_id`),
  CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `lecturers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('1', '2', 'GV001', 'Anh', 'Nguyen', 'teacher01@university.edu', '0911000001', '1', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('2', '3', 'GV002', 'Binh', 'Tran', 'teacher02@university.edu', '0911000002', '1', 'Master');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('3', '4', 'GV003', 'Chi', 'Le', 'teacher03@university.edu', '0911000003', '1', 'PhD');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('4', '5', 'GV004', 'Dung', 'Pham', 'teacher04@university.edu', '0911000004', '2', 'Professor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('5', '6', 'GV005', 'Hoa', 'Vu', 'teacher05@university.edu', '0911000005', '2', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('6', '7', 'GV006', 'Khanh', 'Do', 'teacher06@university.edu', '0911000006', '2', 'Master');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('7', '8', 'GV007', 'Lan', 'Hoang', 'teacher07@university.edu', '0911000007', '3', 'PhD');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('8', '9', 'GV008', 'Minh', 'Nguyen', 'teacher08@university.edu', '0911000008', '3', 'Professor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('9', '10', 'GV009', 'Nam', 'Tran', 'teacher09@university.edu', '0911000009', '3', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('10', '11', 'GV010', 'Oanh', 'Le', 'teacher10@university.edu', '0911000010', '4', 'Master');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('11', '12', 'GV011', 'Phong', 'Nguyen', 'teacher11@university.edu', '0911000011', '4', 'PhD');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('12', '13', 'GV012', 'Quang', 'Tran', 'teacher12@university.edu', '0911000012', '4', 'Professor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('13', '14', 'GV013', 'Son', 'Pham', 'teacher13@university.edu', '0911000013', '5', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('14', '15', 'GV014', 'Thao', 'Vu', 'teacher14@university.edu', '0911000014', '5', 'Master');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('15', '16', 'GV015', 'Uyen', 'Do', 'teacher15@university.edu', '0911000015', '5', 'PhD');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('16', '17', 'GV016', 'Van', 'Hoang', 'teacher16@university.edu', '0911000016', '6', 'Professor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('17', '18', 'GV017', 'Xuan', 'Nguyen', 'teacher17@university.edu', '0911000017', '6', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('18', '19', 'GV018', 'Yen', 'Tran', 'teacher18@university.edu', '0911000018', '6', 'Master');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('19', '203', 'GV19', 'Chưa cập nhật', '', 'fdsfdfs@gmail.com', NULL, '1', 'Bachelor');
INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES ('20', '204', 'GV20', 'dsadsad', 'sdadasdasd', 'sadasdaa5k@gmail.com', NULL, '1', 'Master');

-- ----------------------------------------
-- Table: `library_books`
-- ----------------------------------------

DROP TABLE IF EXISTS `library_books`;
CREATE TABLE `library_books` (
  `book_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(200) DEFAULT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `publisher` varchar(150) DEFAULT NULL,
  `published_year` year(4) DEFAULT NULL,
  `total_copies` int(11) NOT NULL DEFAULT 1,
  `available_copies` int(11) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`book_id`),
  UNIQUE KEY `isbn` (`isbn`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('1', 'Nhập môn lập trình', 'Nguyễn Văn An', '978-604-1-001', 'Công nghệ thông tin', 'NXB ĐHQG', '2020', '5', '4', 'Giáo trình nhập môn lập trình cho sinh viên năm nhất', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('2', 'Cấu trúc dữ liệu và giải thuật', 'Trần Thị Bình', '978-604-1-002', 'Công nghệ thông tin', 'NXB ĐHQG', '2019', '4', '3', 'Sách giáo trình về cấu trúc dữ liệu', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('3', 'Cơ sở dữ liệu', 'Lê Văn Chi', '978-604-1-003', 'Công nghệ thông tin', 'NXB Giáo dục', '2021', '6', '4', 'Nguyên lý cơ sở dữ liệu và SQL', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('4', 'Lập trình Java nâng cao', 'Phạm Minh Dũng', '978-604-1-004', 'Công nghệ thông tin', 'NXB ĐHQG', '2022', '3', '2', 'Java OOP và Design Patterns', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('5', 'Nguyên lý quản trị kinh doanh', 'Vũ Thị Hoa', '978-604-2-001', 'Quản trị kinh doanh', 'NXB Kinh tế', '2020', '5', '5', 'Giáo trình quản trị căn bản', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('6', 'Marketing hiện đại', 'Đỗ Quang Khánh', '978-604-2-002', 'Quản trị kinh doanh', 'NXB Kinh tế', '2021', '4', '3', 'Chiến lược và kế hoạch marketing', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('7', 'Tiếng Anh thương mại', 'Hoàng Thị Lan', '978-604-3-001', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2020', '8', '6', 'Tiếng Anh dùng trong kinh doanh', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('8', 'Ngữ pháp tiếng Anh B1-B2', 'Nguyễn Minh Thu', '978-604-3-002', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2019', '10', '8', 'Hệ thống ngữ pháp tiếng Anh từ căn bản đến nâng cao', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('9', 'Nguyên lý kế toán', 'Trần Văn Nam', '978-604-4-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2020', '6', '5', 'Giáo trình kế toán cơ bản', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('10', 'Kế toán tài chính doanh nghiệp', 'Lê Thị Oanh', '978-604-4-002', 'Kế toán - Tài chính', 'NXB Tài chính', '2021', '5', '4', 'Kế toán tài chính thực hành', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('11', 'Nguyên lý tài chính', 'Nguyễn Quang Phong', '978-604-5-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2022', '4', '4', 'Tài chính doanh nghiệp căn bản', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('12', 'Phương pháp nghiên cứu khoa học', 'Trần Thị Quyên', '978-604-6-001', 'Khoa học - Giáo dục', 'NXB ĐHQG', '2020', '3', '3', 'Hướng dẫn nghiên cứu khoa học', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('13', 'Tâm lý học giáo dục', 'Lê Văn Sơn', '978-604-7-001', 'Khoa học - Giáo dục', 'NXB Giáo dục', '2021', '5', '5', 'Tâm lý học ứng dụng trong giảng dạy', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('14', 'Lịch sử Việt Nam', 'Phạm Thị Thảo', '978-604-8-001', 'Lịch sử - Văn hóa', 'NXB Sử học', '2018', '4', '4', 'Lịch sử Việt Nam từ cổ đại đến hiện đại', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('15', 'Toán cao cấp tập 1', 'Vũ Minh Uyên', '978-604-9-001', 'Toán học', 'NXB ĐHQG', '2020', '8', '6', 'Giải tích và đại số tuyến tính', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('16', 'Toán cao cấp tập 2', 'Đỗ Thị Vân', '978-604-9-002', 'Toán học', 'NXB ĐHQG', '2020', '8', '7', 'Xác suất thống kê và phương trình vi phân', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('17', 'Vật lý đại cương', 'Hoàng Xuân Yên', '978-604-10-001', 'Khoa học tự nhiên', 'NXB ĐHQG', '2019', '6', '5', 'Vật lý cho sinh viên kỹ thuật', NULL, '2026-02-25 07:53:08');
INSERT INTO `library_books` (`book_id`, `title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`, `cover_image`, `created_at`) VALUES ('18', 'Triết học Mác – Lênin', 'Nguyễn Thị Bảo', '978-604-11-001', 'Lý luận chính trị', 'NXB Chính trị', '2021', '10', '9', 'Giáo trình triết học cho đại học', NULL, '2026-02-25 07:53:08');

-- ----------------------------------------
-- Table: `library_borrows`
-- ----------------------------------------

DROP TABLE IF EXISTS `library_borrows`;
CREATE TABLE `library_borrows` (
  `borrow_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL DEFAULT curdate(),
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Borrowed','Returned','Overdue','Lost') DEFAULT 'Borrowed',
  `fine_amount` decimal(10,0) NOT NULL DEFAULT 0,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`borrow_id`),
  KEY `fk_borrow_student` (`student_id`),
  KEY `fk_borrow_book` (`book_id`),
  CONSTRAINT `fk_borrow_book` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`book_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `library_borrows` (`borrow_id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `fine_amount`, `note`, `created_at`) VALUES ('1', '37', '3', '2026-02-25', '2026-03-11', NULL, 'Borrowed', '0', NULL, '2026-02-25 07:56:48');

-- ----------------------------------------
-- Table: `password_resets`
-- ----------------------------------------

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_password_resets_user` (`user_id`),
  KEY `idx_password_resets_token` (`token`),
  KEY `idx_password_resets_expires` (`expires_at`),
  CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------
-- Table: `permissions`
-- ----------------------------------------

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('1', 'manage_users', 'Quản lý người dùng');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('2', 'manage_roles', 'Quản lý vai trò');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('3', 'view_audit_logs', 'Xem nhật ký hệ thống');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('4', 'manage_students', 'Quản lý sinh viên');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('5', 'manage_lecturers', 'Quản lý giảng viên');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('6', 'manage_faculties', 'Quản lý khoa');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('7', 'manage_subjects', 'Quản lý môn học');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('8', 'manage_classes', 'Quản lý lớp học');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('9', 'manage_grades', 'Quản lý điểm');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('10', 'view_reports', 'Xem báo cáo');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('11', 'reset_passwords', 'Đặt lại mật khẩu người dùng');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('12', 'register_courses', 'Đăng ký môn học');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('13', 'export_reports', 'Xuất báo cáo');
INSERT INTO `permissions` (`id`, `code`, `description`) VALUES ('14', 'view_transcripts', 'Xem bảng điểm chi tiết');

-- ----------------------------------------
-- Table: `role_permissions`
-- ----------------------------------------

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `idx_role_permissions_role` (`role_id`),
  KEY `idx_role_permissions_permission` (`permission_id`),
  CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('1', '1');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '6');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('2', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '8');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '9');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('3', '14');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '10');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '11');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '12');
INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES ('4', '14');

-- ----------------------------------------
-- Table: `roles`
-- ----------------------------------------

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `roles` (`id`, `code`, `name`) VALUES ('1', 'super_admin', 'Admin quản trị hệ thống');
INSERT INTO `roles` (`id`, `code`, `name`) VALUES ('2', 'content_admin', 'Admin quản trị nội dung');
INSERT INTO `roles` (`id`, `code`, `name`) VALUES ('3', 'teacher', 'Giảng viên');
INSERT INTO `roles` (`id`, `code`, `name`) VALUES ('4', 'student', 'Sinh viên');

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
  UNIQUE KEY `uq_application` (`student_id`,`scholarship_id`),
  KEY `fk_app_student` (`student_id`),
  KEY `fk_app_scholarship` (`scholarship_id`),
  CONSTRAINT `fk_app_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------
-- Table: `scholarships`
-- ----------------------------------------

DROP TABLE IF EXISTS `scholarships`;
CREATE TABLE `scholarships` (
  `scholarship_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `value` decimal(12,0) NOT NULL DEFAULT 0 COMMENT 'VN─É',
  `min_gpa` decimal(5,2) DEFAULT NULL COMMENT '─æiß╗âm tß╗æi thiß╗âu (thang 100)',
  `max_gpa` decimal(5,2) DEFAULT NULL COMMENT '─æiß╗âm tß╗æi ─æa (thang 100) ─æß╗â ─æã░ß╗úc x├®t',
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `quantity` int(11) DEFAULT NULL COMMENT 'NULL = kh├┤ng giß╗øi hß║ín',
  `deadline` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`scholarship_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('1', 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', '5000000', '90.00', NULL, 'Spring', '2026', '20', '2026-03-15', '1', '2026-02-25 07:53:08');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('2', 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', '2000000', '80.00', '89.99', 'Spring', '2026', '50', '2026-03-15', '1', '2026-02-25 07:53:08');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('3', 'Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', '1500000', NULL, NULL, 'Spring', '2026', '30', '2026-03-20', '1', '2026-02-25 07:53:08');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('4', 'Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', '10000000', '85.00', NULL, 'Spring', '2026', '5', '2026-03-10', '1', '2026-02-25 07:53:08');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('5', 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', '5000000', '90.00', NULL, 'Fall', '2025', '20', '2025-09-15', '0', '2026-02-25 07:53:08');
INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES ('6', 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', '2000000', '80.00', '89.99', 'Fall', '2025', '50', '2025-09-15', '0', '2026-02-25 07:53:08');

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
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `idx_students_faculty` (`faculty_id`),
  KEY `idx_view_results_student` (`student_id`),
  KEY `idx_students_base_class` (`base_class_id`),
  KEY `idx_students_status` (`status`),
  CONSTRAINT `students_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  CONSTRAINT `students_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `students_ibfk_3` FOREIGN KEY (`base_class_id`) REFERENCES `base_classes` (`base_class_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=245 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('33', '20', 'SV000', 'Nam', 'Tran', 'Male', '2005-03-15', 'student00@university.edu', '0123456789', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('34', '21', 'SV001', 'An', 'Nguyen', 'Male', '2004-01-15', 'student01@university.edu', '0901000001', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('35', '22', 'SV002', 'Binh', 'Tran', 'Female', '2004-02-20', 'student02@university.edu', '0901000002', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('36', '23', 'SV003', 'Chi', 'Le', 'Male', '2004-03-10', 'student03@university.edu', '0901000003', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('37', '24', 'SV004', 'Dung', 'Pham', 'Female', '2004-04-05', 'student04@university.edu', '0901000004', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('38', '25', 'SV005', 'Hoa', 'Vu', 'Male', '2004-05-25', 'student05@university.edu', '0901000005', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('39', '26', 'SV006', 'Khanh', 'Do', 'Female', '2004-06-12', 'student06@university.edu', '0901000006', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('40', '27', 'SV007', 'Lan', 'Hoang', 'Male', '2004-07-08', 'student07@university.edu', '0901000007', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('41', '28', 'SV008', 'Minh', 'Nguyen', 'Female', '2004-08-18', 'student08@university.edu', '0901000008', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('42', '29', 'SV009', 'Nam', 'Tran', 'Male', '2004-09-22', 'student09@university.edu', '0901000009', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('43', '30', 'SV010', 'Oanh', 'Le', 'Female', '2004-10-30', 'student10@university.edu', '0901000010', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('44', '31', 'SV011', 'Phong', 'Nguyen', 'Male', '2004-11-15', 'student11@university.edu', '0901000011', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('45', '32', 'SV012', 'Quang', 'Tran', 'Female', '2004-12-05', 'student12@university.edu', '0901000012', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('46', '33', 'SV013', 'Son', 'Pham', 'Male', '2005-01-10', 'student13@university.edu', '0901000013', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('47', '34', 'SV014', 'Thao', 'Vu', 'Female', '2005-02-20', 'student14@university.edu', '0901000014', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('48', '35', 'SV015', 'Uyen', 'Do', 'Male', '2005-03-15', 'student15@university.edu', '0901000015', '1', NULL, 'Studying', '2026-01-21 00:07:14');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('49', '36', 'SV016', 'Van', 'Hoang', 'Female', '2005-04-25', 'student16@university.edu', '0901000016', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('50', '37', 'SV017', 'Xuan', 'Nguyen', 'Male', '2005-05-30', 'student17@university.edu', '0901000017', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('51', '38', 'SV018', 'Yen', 'Tran', 'Female', '2005-06-18', 'student18@university.edu', '0901000018', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('52', '39', 'SV019', 'Zung', 'Pham', 'Male', '2005-07-22', 'student19@university.edu', '0901000019', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('53', '40', 'SV020', 'Bao', 'Le', 'Female', '2005-08-10', 'student20@university.edu', '0901000020', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('54', '41', 'SV021', 'Cuong', 'Nguyen', 'Male', '2005-09-01', 'student21@university.edu', '0901000021', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('55', '42', 'SV022', 'Dao', 'Tran', 'Female', '2005-09-15', 'student22@university.edu', '0901000022', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('56', '43', 'SV023', 'Hieu', 'Le', 'Male', '2005-10-05', 'student23@university.edu', '0901000023', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('57', '44', 'SV024', 'Giang', 'Pham', 'Female', '2005-10-20', 'student24@university.edu', '0901000024', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('58', '45', 'SV025', 'Kien', 'Vu', 'Male', '2005-11-02', 'student25@university.edu', '0901000025', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('59', '46', 'SV026', 'Linh', 'Do', 'Female', '2005-11-18', 'student26@university.edu', '0901000026', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('60', '47', 'SV027', 'Manh', 'Hoang', 'Male', '2005-12-01', 'student27@university.edu', '0901000027', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('61', '48', 'SV028', 'Nga', 'Nguyen', 'Female', '2005-12-15', 'student28@university.edu', '0901000028', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('62', '49', 'SV029', 'Phuc', 'Tran', 'Male', '2006-01-05', 'student29@university.edu', '0901000029', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('63', '50', 'SV030', 'Phong', 'Nguyen', 'Male', '2004-11-15', 'student30@university.edu', '0901000030', '1', NULL, 'Studying', '2026-01-21 00:07:48');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('79', '51', 'SV031', 'Son', 'Pham', 'Male', '2006-02-02', 'student31@university.edu', '0901000031', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('80', '52', 'SV032', 'Thao', 'Vu', 'Female', '2006-02-18', 'student32@university.edu', '0901000032', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('81', '53', 'SV033', 'Uyen', 'Do', 'Male', '2006-03-05', 'student33@university.edu', '0901000033', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('82', '54', 'SV034', 'Van', 'Hoang', 'Female', '2006-03-20', 'student34@university.edu', '0901000034', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('83', '55', 'SV035', 'Xuan', 'Nguyen', 'Male', '2006-04-05', 'student35@university.edu', '0901000035', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('84', '56', 'SV036', 'An', 'Nguyen', 'Male', '2006-04-20', 'student36@university.edu', '0901000036', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('85', '57', 'SV037', 'Binh', 'Tran', 'Female', '2006-05-05', 'student37@university.edu', '0901000037', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('86', '58', 'SV038', 'Chi', 'Le', 'Male', '2006-05-18', 'student38@university.edu', '0901000038', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('87', '59', 'SV039', 'Dung', 'Pham', 'Female', '2006-06-02', 'student39@university.edu', '0901000039', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('88', '60', 'SV040', 'Hoa', 'Vu', 'Male', '2006-06-15', 'student40@university.edu', '0901000040', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('89', '61', 'SV041', 'Khanh', 'Do', 'Female', '2006-07-01', 'student41@university.edu', '0901000041', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('90', '62', 'SV042', 'Lan', 'Hoang', 'Male', '2006-07-18', 'student42@university.edu', '0901000042', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('91', '63', 'SV043', 'Minh', 'Nguyen', 'Female', '2006-08-05', 'student43@university.edu', '0901000043', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('92', '64', 'SV044', 'Nam', 'Tran', 'Male', '2006-08-20', 'student44@university.edu', '0901000044', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('93', '65', 'SV045', 'Oanh', 'Le', 'Female', '2006-09-02', 'student45@university.edu', '0901000045', '2', NULL, 'Studying', '2026-01-21 00:09:58');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('94', '66', 'SV046', 'Phong', 'Nguyen', 'Male', '2006-09-18', 'student46@university.edu', '0901000046', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('95', '67', 'SV047', 'Quang', 'Tran', 'Female', '2006-10-05', 'student47@university.edu', '0901000047', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('96', '68', 'SV048', 'Son', 'Pham', 'Male', '2006-10-20', 'student48@university.edu', '0901000048', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('97', '69', 'SV049', 'Thao', 'Vu', 'Female', '2006-11-02', 'student49@university.edu', '0901000049', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('98', '70', 'SV050', 'Uyen', 'Do', 'Male', '2006-11-18', 'student50@university.edu', '0901000050', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('99', '71', 'SV051', 'Van', 'Hoang', 'Female', '2006-12-05', 'student51@university.edu', '0901000051', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('100', '72', 'SV052', 'Xuan', 'Nguyen', 'Male', '2006-12-20', 'student52@university.edu', '0901000052', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('101', '73', 'SV053', 'Yen', 'Tran', 'Female', '2007-01-05', 'student53@university.edu', '0901000053', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('102', '74', 'SV054', 'Zung', 'Pham', 'Male', '2007-01-20', 'student54@university.edu', '0901000054', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('103', '75', 'SV055', 'Bao', 'Le', 'Female', '2007-02-05', 'student55@university.edu', '0901000055', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('104', '76', 'SV056', 'Bao', 'Nguyen', 'Male', '2007-02-20', 'student56@university.edu', '0912000056', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('105', '77', 'SV057', 'Cam', 'Tran', 'Female', '2007-03-05', 'student57@university.edu', '0912000057', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('106', '78', 'SV058', 'Diep', 'Le', 'Male', '2007-03-18', 'student58@university.edu', '0912000058', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('107', '79', 'SV059', 'Hanh', 'Pham', 'Female', '2007-04-02', 'student59@university.edu', '0912000059', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('108', '80', 'SV060', 'Khoa', 'Vu', 'Male', '2007-04-18', 'student60@university.edu', '0912000060', '2', NULL, 'Studying', '2026-01-21 00:10:11');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('109', '81', 'SV061', 'Luyen', 'Do', 'Female', '2007-05-05', 'student61@university.edu', '0912000061', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('110', '82', 'SV062', 'My', 'Hoang', 'Male', '2007-05-20', 'student62@university.edu', '0912000062', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('111', '83', 'SV063', 'Ngoc', 'Nguyen', 'Female', '2007-06-02', 'student63@university.edu', '0912000063', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('112', '84', 'SV064', 'Phuong', 'Tran', 'Male', '2007-06-18', 'student64@university.edu', '0912000064', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('113', '85', 'SV065', 'Quoc', 'Le', 'Female', '2007-07-05', 'student65@university.edu', '0912000065', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('114', '86', 'SV066', 'Sinh', 'Pham', 'Male', '2007-07-20', 'student66@university.edu', '0912000066', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('115', '87', 'SV067', 'Trang', 'Vu', 'Female', '2007-08-05', 'student67@university.edu', '0912000067', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('116', '88', 'SV068', 'Uy', 'Do', 'Male', '2007-08-20', 'student68@university.edu', '0912000068', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('117', '89', 'SV069', 'Vy', 'Hoang', 'Female', '2007-09-05', 'student69@university.edu', '0912000069', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('118', '90', 'SV070', 'Xuyen', 'Nguyen', 'Male', '2007-09-20', 'student70@university.edu', '0912000070', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('119', '91', 'SV071', 'Yen', 'Tran', 'Female', '2007-10-05', 'student71@university.edu', '0912000071', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('120', '92', 'SV072', 'An', 'Le', 'Male', '2007-10-20', 'student72@university.edu', '0912000072', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('121', '93', 'SV073', 'Bich', 'Pham', 'Female', '2007-11-05', 'student73@university.edu', '0912000073', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('122', '94', 'SV074', 'Cuong', 'Vu', 'Male', '2007-11-20', 'student74@university.edu', '0912000074', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('123', '95', 'SV075', 'Dao', 'Do', 'Female', '2007-12-05', 'student75@university.edu', '0912000075', '3', NULL, 'Studying', '2026-01-21 00:10:23');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('124', '96', 'SV076', 'AnhThu', 'Nguyen', 'Female', '2007-12-20', 'student76@university.edu', '0913000076', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('125', '97', 'SV077', 'BaoLong', 'Tran', 'Male', '2003-01-05', 'student77@university.edu', '0913000077', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('126', '98', 'SV078', 'CatTuong', 'Le', 'Female', '2003-02-20', 'student78@university.edu', '0913000078', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('127', '99', 'SV079', 'DangKhoa', 'Pham', 'Male', '2004-03-15', 'student79@university.edu', '0913000079', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('128', '100', 'SV080', 'HaVy', 'Vu', 'Female', '2004-04-10', 'student80@university.edu', '0913000080', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('129', '101', 'SV081', 'GiaBao', 'Do', 'Male', '2004-05-25', 'student81@university.edu', '0913000081', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('130', '102', 'SV082', 'HongAnh', 'Hoang', 'Female', '2004-06-18', 'student82@university.edu', '0913000082', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('131', '103', 'SV083', 'KhaiMinh', 'Nguyen', 'Male', '2005-07-05', 'student83@university.edu', '0913000083', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('132', '104', 'SV084', 'LamPhuong', 'Tran', 'Female', '2005-08-20', 'student84@university.edu', '0913000084', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('133', '105', 'SV085', 'ManhHung', 'Le', 'Male', '2005-09-12', 'student85@university.edu', '0913000085', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('134', '106', 'SV086', 'NguyenHa', 'Pham', 'Female', '2005-10-25', 'student86@university.edu', '0913000086', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('135', '107', 'SV087', 'PhuongNam', 'Vu', 'Male', '2006-01-05', 'student87@university.edu', '0913000087', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('136', '108', 'SV088', 'QuynhChi', 'Do', 'Female', '2006-02-20', 'student88@university.edu', '0913000088', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('137', '109', 'SV089', 'ThanhDat', 'Hoang', 'Male', '2006-03-15', 'student89@university.edu', '0913000089', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('138', '110', 'SV090', 'ThuyDuong', 'Nguyen', 'Female', '2006-04-10', 'student90@university.edu', '0913000090', '3', NULL, 'Studying', '2026-01-21 00:10:36');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('139', '111', 'SV091', 'VanKiet', 'Tran', 'Male', '2008-08-05', 'student91@university.edu', '0913000091', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('140', '112', 'SV092', 'YenNhi', 'Le', 'Female', '2008-08-20', 'student92@university.edu', '0913000092', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('141', '113', 'SV093', 'AnhQuan', 'Pham', 'Male', '2008-09-05', 'student93@university.edu', '0913000093', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('142', '114', 'SV094', 'BaoNgoc', 'Vu', 'Female', '2008-09-20', 'student94@university.edu', '0913000094', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('143', '115', 'SV095', 'ChiBao', 'Do', 'Male', '2008-10-05', 'student95@university.edu', '0913000095', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('144', '116', 'SV096', 'AnKhang', 'Nguyen', 'Male', '2008-10-20', 'student96@university.edu', '0914000096', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('145', '117', 'SV097', 'BaoHan', 'Tran', 'Female', '2008-11-05', 'student97@university.edu', '0914000097', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('146', '118', 'SV098', 'CamTu', 'Le', 'Male', '2008-11-20', 'student98@university.edu', '0914000098', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('147', '119', 'SV099', 'DangQuang', 'Pham', 'Female', '2008-12-05', 'student99@university.edu', '0914000099', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('148', '120', 'SV100', 'GiaHan', 'Vu', 'Male', '2008-12-20', 'student100@university.edu', '0914000100', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('149', '121', 'SV101', 'HoaiNam', 'Do', 'Female', '2009-01-05', 'student101@university.edu', '0914000101', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('150', '122', 'SV102', 'KieuAnh', 'Hoang', 'Male', '2009-01-20', 'student102@university.edu', '0914000102', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('151', '123', 'SV103', 'LamKhanh', 'Nguyen', 'Female', '2009-02-05', 'student103@university.edu', '0914000103', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('152', '124', 'SV104', 'MinhChau', 'Tran', 'Male', '2009-02-20', 'student104@university.edu', '0914000104', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('153', '125', 'SV105', 'NguyenPhuc', 'Le', 'Female', '2009-03-05', 'student105@university.edu', '0914000105', '4', NULL, 'Studying', '2026-01-21 00:10:47');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('154', '126', 'SV106', 'PhuongAnh', 'Pham', 'Male', '2009-03-20', 'student106@university.edu', '0914000106', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('155', '127', 'SV107', 'QuocHung', 'Vu', 'Female', '2009-04-05', 'student107@university.edu', '0914000107', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('156', '128', 'SV108', 'ThanhTung', 'Do', 'Male', '2009-04-20', 'student108@university.edu', '0914000108', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('157', '129', 'SV109', 'ThuyLinh', 'Hoang', 'Female', '2009-05-05', 'student109@university.edu', '0914000109', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('158', '130', 'SV110', 'VanAnh', 'Nguyen', 'Male', '2009-05-20', 'student110@university.edu', '0914000110', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('159', '131', 'SV111', 'XuanMai', 'Tran', 'Female', '2009-06-05', 'student111@university.edu', '0914000111', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('160', '132', 'SV112', 'YenVy', 'Le', 'Male', '2009-06-20', 'student112@university.edu', '0914000112', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('161', '133', 'SV113', 'AnhTuan', 'Pham', 'Female', '2009-07-05', 'student113@university.edu', '0914000113', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('162', '134', 'SV114', 'BaoTran', 'Vu', 'Male', '2009-07-20', 'student114@university.edu', '0914000114', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('163', '135', 'SV115', 'ChiLan', 'Do', 'Female', '2009-08-05', 'student115@university.edu', '0914000115', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('164', '136', 'SV116', 'AnHoa', 'Nguyen', 'Female', '2009-08-20', 'student116@university.edu', '0915000116', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('165', '137', 'SV117', 'BaoMinh', 'Tran', 'Male', '2009-09-05', 'student117@university.edu', '0915000117', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('166', '138', 'SV118', 'CamVan', 'Le', 'Female', '2009-09-20', 'student118@university.edu', '0915000118', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('167', '139', 'SV119', 'DangSon', 'Pham', 'Male', '2009-10-05', 'student119@university.edu', '0915000119', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('168', '140', 'SV120', 'GiaHuy', 'Vu', 'Female', '2009-10-20', 'student120@university.edu', '0915000120', '4', NULL, 'Studying', '2026-01-21 00:11:04');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('169', '141', 'SV121', 'HoangLam', 'Do', 'Male', '2006-11-05', 'student121@university.edu', '0915000121', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('170', '142', 'SV122', 'KieuMy', 'Hoang', 'Female', '2006-01-10', 'student122@university.edu', '0915000122', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('171', '143', 'SV123', 'LanHuong', 'Nguyen', 'Male', '2006-02-15', 'student123@university.edu', '0915000123', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('172', '144', 'SV124', 'MinhTri', 'Tran', 'Female', '2006-03-05', 'student124@university.edu', '0915000124', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('173', '145', 'SV125', 'NgocAnh', 'Le', 'Male', '2006-03-20', 'student125@university.edu', '0915000125', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('174', '146', 'SV126', 'PhuongThao', 'Pham', 'Female', '2006-04-10', 'student126@university.edu', '0915000126', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('175', '147', 'SV127', 'QuangVinh', 'Vu', 'Male', '2006-04-25', 'student127@university.edu', '0915000127', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('176', '148', 'SV128', 'ThanhHa', 'Do', 'Female', '2006-05-12', 'student128@university.edu', '0915000128', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('177', '149', 'SV129', 'ThuyTien', 'Hoang', 'Male', '2006-05-28', 'student129@university.edu', '0915000129', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('178', '150', 'SV130', 'VanLong', 'Nguyen', 'Female', '2006-06-15', 'student130@university.edu', '0915000130', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('179', '151', 'SV131', 'XuanPhuc', 'Tran', 'Male', '2006-07-02', 'student131@university.edu', '0915000131', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('180', '152', 'SV132', 'YenHoa', 'Le', 'Female', '2006-07-18', 'student132@university.edu', '0915000132', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('181', '153', 'SV133', 'AnhThu', 'Pham', 'Male', '2006-08-05', 'student133@university.edu', '0915000133', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('182', '154', 'SV134', 'BaoNguyen', 'Vu', 'Female', '2006-08-22', 'student134@university.edu', '0915000134', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('183', '155', 'SV135', 'ChiMai', 'Do', 'Male', '2006-09-10', 'student135@university.edu', '0915000135', '5', NULL, 'Studying', '2026-01-21 00:11:16');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('184', '156', 'SV136', 'AnVy', 'Nguyen', 'Female', '2006-06-20', 'student136@university.edu', '0916000136', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('185', '157', 'SV137', 'BaoLam', 'Tran', 'Male', '2006-01-05', 'student137@university.edu', '0916000137', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('186', '158', 'SV138', 'CamAnh', 'Le', 'Female', '2006-02-10', 'student138@university.edu', '0916000138', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('187', '159', 'SV139', 'DangHuy', 'Pham', 'Male', '2006-03-15', 'student139@university.edu', '0916000139', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('188', '160', 'SV140', 'GiaThinh', 'Vu', 'Female', '2006-04-20', 'student140@university.edu', '0916000140', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('189', '161', 'SV141', 'HoaiThu', 'Do', 'Male', '2006-05-05', 'student141@university.edu', '0916000141', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('190', '162', 'SV142', 'KhanhLinh', 'Hoang', 'Female', '2006-06-10', 'student142@university.edu', '0916000142', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('191', '163', 'SV143', 'LanAnh', 'Nguyen', 'Male', '2006-07-05', 'student143@university.edu', '0916000143', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('192', '164', 'SV144', 'MinhQuan', 'Tran', 'Female', '2006-08-20', 'student144@university.edu', '0916000144', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('193', '165', 'SV145', 'NgocHa', 'Le', 'Male', '2006-09-12', 'student145@university.edu', '0916000145', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('194', '166', 'SV146', 'PhuongVy', 'Pham', 'Female', '2006-10-25', 'student146@university.edu', '0916000146', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('195', '167', 'SV147', 'QuangAnh', 'Vu', 'Male', '2006-11-05', 'student147@university.edu', '0916000147', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('196', '168', 'SV148', 'ThanhHa', 'Do', 'Female', '2006-11-20', 'student148@university.edu', '0916000148', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('197', '169', 'SV149', 'ThuyTrang', 'Hoang', 'Male', '2006-12-05', 'student149@university.edu', '0916000149', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('198', '170', 'SV150', 'VanNhi', 'Nguyen', 'Female', '2006-12-20', 'student150@university.edu', '0916000150', '5', NULL, 'Studying', '2026-01-21 00:11:28');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('199', '171', 'SV151', 'XuanBach', 'Tran', 'Male', '2011-02-05', 'student151@university.edu', '0916000151', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('200', '172', 'SV152', 'YenNgoc', 'Le', 'Female', '2005-01-10', 'student152@university.edu', '0916000152', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('201', '173', 'SV153', 'AnhKiet', 'Pham', 'Male', '2005-02-15', 'student153@university.edu', '0916000153', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('202', '174', 'SV154', 'BaoChau', 'Vu', 'Female', '2005-03-05', 'student154@university.edu', '0916000154', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('203', '175', 'SV155', 'ChiThanh', 'Do', 'Male', '2005-03-20', 'student155@university.edu', '0916000155', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('204', '176', 'SV156', 'AnDuong', 'Nguyen', 'Male', '2005-04-05', 'student156@university.edu', '0917000156', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('205', '177', 'SV157', 'BaoAnh', 'Tran', 'Female', '2005-04-20', 'student157@university.edu', '0917000157', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('206', '178', 'SV158', 'CamLy', 'Le', 'Male', '2005-05-05', 'student158@university.edu', '0917000158', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('207', '179', 'SV159', 'DangKhanh', 'Pham', 'Female', '2005-05-20', 'student159@university.edu', '0917000159', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('208', '180', 'SV160', 'GiaBao', 'Vu', 'Male', '2005-06-05', 'student160@university.edu', '0917000160', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('209', '181', 'SV161', 'HoaiPhuong', 'Do', 'Female', '2005-06-20', 'student161@university.edu', '0917000161', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('210', '182', 'SV162', 'KhanhNgoc', 'Hoang', 'Male', '2005-07-05', 'student162@university.edu', '0917000162', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('211', '183', 'SV163', 'LanHuong', 'Nguyen', 'Female', '2005-07-20', 'student163@university.edu', '0917000163', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('212', '184', 'SV164', 'MinhTuan', 'Tran', 'Male', '2005-08-05', 'student164@university.edu', '0917000164', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('213', '185', 'SV165', 'NgocMai', 'Le', 'Female', '2005-08-20', 'student165@university.edu', '0917000165', '6', NULL, 'Studying', '2026-01-21 00:11:40');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('229', '186', 'SV166', 'PhuongLinh', 'Pham', 'Male', '2011-09-20', 'student166@university.edu', '0917000166', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('230', '187', 'SV167', 'QuangHuy', 'Vu', 'Female', '2005-01-05', 'student167@university.edu', '0917000167', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('231', '188', 'SV168', 'ThanhBinh', 'Do', 'Male', '2005-01-20', 'student168@university.edu', '0917000168', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('232', '189', 'SV169', 'ThuyDuong', 'Hoang', 'Female', '2005-02-05', 'student169@university.edu', '0917000169', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('233', '190', 'SV170', 'VanPhong', 'Nguyen', 'Male', '2005-02-20', 'student170@university.edu', '0917000170', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('234', '191', 'SV171', 'XuanThao', 'Tran', 'Female', '2005-03-05', 'student171@university.edu', '0917000171', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('235', '192', 'SV172', 'YenNhi', 'Le', 'Male', '2005-03-20', 'student172@university.edu', '0917000172', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('236', '193', 'SV173', 'AnhVu', 'Pham', 'Female', '2005-04-05', 'student173@university.edu', '0917000173', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('237', '194', 'SV174', 'BaoNgoc', 'Vu', 'Male', '2005-04-20', 'student174@university.edu', '0917000174', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('238', '195', 'SV175', 'ChiBao', 'Do', 'Female', '2005-05-05', 'student175@university.edu', '0917000175', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('239', '196', 'SV176', 'BaLuong', 'Nguyen', 'Female', '2005-05-20', 'student176@university.edu', '0917000176', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('240', '197', 'SV177', 'BaoTrung', 'Tran', 'Male', '2005-06-05', 'student177@university.edu', '0918000177', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('241', '198', 'SV178', 'CamNhi', 'Le', 'Female', '2005-06-20', 'student178@university.edu', '0918000178', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('242', '199', 'SV179', 'DangPhat', 'Pham', 'Male', '2005-07-05', 'student179@university.edu', '0918000179', '6', NULL, 'Studying', '2026-01-21 00:12:10');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('243', '201', 'SV180', 'Nguyễn', 'Minh', 'Male', '2006-01-01', 'minhhvcb@gmail.com', NULL, '1', NULL, 'Studying', '2026-02-25 08:17:37');
INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES ('244', '202', 'SV181', 'Nguyễn', 'Minh', 'Male', '2006-01-01', 'minhka5k@gmail.com', '1234567890', '1', NULL, 'Studying', '2026-02-25 08:20:02');

-- ----------------------------------------
-- Table: `subjects`
-- ----------------------------------------

DROP TABLE IF EXISTS `subjects`;
CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_code` varchar(10) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `credit_hours` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `prerequisite_code` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  UNIQUE KEY `subject_code` (`subject_code`),
  KEY `idx_subjects_prerequisite` (`prerequisite_code`),
  CONSTRAINT `fk_prerequisite` FOREIGN KEY (`prerequisite_code`) REFERENCES `subjects` (`subject_code`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('1', 'CNTT101', 'Nhập môn Công nghệ thông tin', '3', 'Giới thiệu tổng quan về CNTT', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('2', 'CNTT201', 'Lập trình Java', '4', 'Môn học về lập trình hướng đối tượng với Java', 'CNTT101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('3', 'CNTT301', 'Cơ sở dữ liệu', '3', 'Nguyên lý thiết kế và quản trị cơ sở dữ liệu', 'CNTT101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('4', 'QTKD101', 'Nguyên lý quản trị', '3', 'Khái niệm và nguyên lý cơ bản của quản trị', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('5', 'QTKD201', 'Marketing căn bản', '3', 'Các khái niệm và chiến lược marketing', 'QTKD101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('6', 'QTKD301', 'Quản trị nhân sự', '3', 'Nguyên lý và kỹ năng quản trị nguồn nhân lực', 'QTKD101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('7', 'ENGL101', 'Tiếng Anh cơ bản', '3', 'Môn học tiếng Anh dành cho người mới bắt đầu', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('8', 'ENGL201', 'Ngữ pháp tiếng Anh nâng cao', '3', 'Hệ thống ngữ pháp nâng cao', 'ENGL101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('9', 'ENGL301', 'Văn hóa Anh - Mỹ', '2', 'Giới thiệu văn hóa và xã hội Anh - Mỹ', 'ENGL101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('10', 'KT101', 'Nguyên lý kế toán', '3', 'Khái niệm và nguyên lý cơ bản của kế toán', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('11', 'KT201', 'Kế toán tài chính', '4', 'Nguyên lý kế toán tài chính doanh nghiệp', 'KT101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('12', 'KT301', 'Kiểm toán căn bản', '3', 'Nguyên lý và quy trình kiểm toán', 'KT201');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('13', 'TCNH101', 'Nguyên lý tài chính', '3', 'Khái niệm và nguyên lý cơ bản về tài chính', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('14', 'TCNH201', 'Ngân hàng thương mại', '3', 'Hoạt động và quản trị ngân hàng thương mại', 'TCNH101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('15', 'TCNH301', 'Đầu tư tài chính', '3', 'Nguyên lý và kỹ năng đầu tư tài chính', 'TCNH101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('16', 'SP101', 'Tâm lý học giáo dục', '3', 'Khái niệm và nguyên lý tâm lý học trong giáo dục', NULL);
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('17', 'SP201', 'Phương pháp giảng dạy', '3', 'Các phương pháp giảng dạy hiện đại', 'SP101');
INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES ('18', 'SP301', 'Quản lý lớp học', '2', 'Kỹ năng quản lý lớp học hiệu quả', 'SP101');

-- ----------------------------------------
-- Table: `tuition_invoices`
-- ----------------------------------------

DROP TABLE IF EXISTS `tuition_invoices`;
CREATE TABLE `tuition_invoices` (
  `invoice_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `total_credits` int(11) NOT NULL DEFAULT 0,
  `amount_due` decimal(12,0) NOT NULL DEFAULT 0,
  `amount_paid` decimal(12,0) NOT NULL DEFAULT 0,
  `status` enum('Unpaid','Partial','Paid','Overdue','Exempted') DEFAULT 'Unpaid',
  `due_date` date DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `uq_invoice` (`student_id`,`semester`,`year`),
  KEY `fk_invoice_student` (`student_id`),
  CONSTRAINT `fk_invoice_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('1', '36', 'Spring', '2026', '12', '6600000', '0', 'Unpaid', '2026-03-31', NULL, NULL, '2026-02-25 07:46:23');
INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('2', '36', 'Fall', '2026', '2', '1100000', '0', 'Unpaid', '2026-10-01', NULL, NULL, '2026-02-25 07:46:23');
INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('3', '37', 'Summer', '2026', '4', '2200000', '2200000', 'Paid', '2026-07-01', '2026-02-25 00:55:40', NULL, '2026-02-25 07:55:31');
INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('4', '243', 'Spring', '2026', '6', '3300000', '0', 'Unpaid', '2026-03-31', NULL, NULL, '2026-02-25 08:19:00');
INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('5', '244', 'Summer', '2026', '4', '2200000', '0', 'Unpaid', '2026-07-01', NULL, NULL, '2026-02-25 08:20:23');
INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES ('6', '33', 'Spring', '2026', '6', '3300000', '3300000', 'Paid', '2026-03-31', '2026-02-25 03:54:02', NULL, '2026-02-25 09:53:57');

-- ----------------------------------------
-- Table: `tuition_settings`
-- ----------------------------------------

DROP TABLE IF EXISTS `tuition_settings`;
CREATE TABLE `tuition_settings` (
  `setting_id` int(11) NOT NULL AUTO_INCREMENT,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `price_per_credit` decimal(12,0) NOT NULL DEFAULT 500000 COMMENT 'VN─É / t├¡n chß╗ë',
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `uq_tuition_semester` (`semester`,`year`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('13', 'Spring', '2026', '550000', 'Học kỳ 1 năm 2026', '2026-02-25 07:53:08');
INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('14', 'Summer', '2026', '550000', 'Học kỳ hè năm 2026', '2026-02-25 07:53:08');
INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('15', 'Fall', '2026', '550000', 'Học kỳ 2 năm 2026', '2026-02-25 07:53:08');
INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('16', 'Spring', '2025', '500000', 'Học kỳ 1 năm 2025', '2026-02-25 07:53:08');
INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('17', 'Summer', '2025', '500000', 'Học kỳ hè năm 2025', '2026-02-25 07:53:08');
INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES ('18', 'Fall', '2025', '500000', 'Học kỳ 2 năm 2025', '2026-02-25 07:53:08');

-- ----------------------------------------
-- Table: `two_factor_auth`
-- ----------------------------------------

DROP TABLE IF EXISTS `two_factor_auth`;
CREATE TABLE `two_factor_auth` (
  `user_id` int(11) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `enabled` tinyint(4) DEFAULT 0,
  PRIMARY KEY (`user_id`),
  KEY `idx_2fa_enabled` (`enabled`),
  CONSTRAINT `two_factor_auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------
-- Table: `user_roles`
-- ----------------------------------------

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `idx_user_roles_user` (`user_id`),
  KEY `idx_user_roles_role` (`role_id`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('1', '1');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('2', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('3', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('4', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('5', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('6', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('7', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('8', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('9', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('10', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('11', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('12', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('13', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('14', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('15', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('16', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('17', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('18', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('19', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('20', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('21', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('22', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('23', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('24', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('25', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('26', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('27', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('28', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('29', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('30', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('31', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('32', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('33', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('34', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('35', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('36', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('37', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('38', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('39', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('40', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('41', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('42', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('43', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('44', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('45', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('46', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('47', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('48', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('49', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('50', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('51', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('52', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('53', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('54', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('55', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('56', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('57', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('58', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('59', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('60', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('61', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('62', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('63', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('64', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('65', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('66', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('67', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('68', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('69', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('70', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('71', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('72', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('73', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('74', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('75', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('76', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('77', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('78', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('79', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('80', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('81', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('82', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('83', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('84', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('85', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('86', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('87', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('88', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('89', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('90', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('91', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('92', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('93', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('94', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('95', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('96', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('97', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('98', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('99', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('100', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('101', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('102', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('103', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('104', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('105', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('106', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('107', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('108', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('109', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('110', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('111', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('112', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('113', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('114', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('115', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('116', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('117', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('118', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('119', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('120', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('121', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('122', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('123', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('124', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('125', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('126', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('127', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('128', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('129', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('130', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('131', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('132', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('133', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('134', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('135', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('136', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('137', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('138', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('139', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('140', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('141', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('142', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('143', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('144', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('145', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('146', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('147', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('148', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('149', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('150', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('151', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('152', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('153', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('154', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('155', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('156', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('157', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('158', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('159', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('160', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('161', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('162', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('163', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('164', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('165', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('166', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('167', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('168', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('169', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('170', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('171', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('172', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('173', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('174', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('175', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('176', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('177', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('178', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('179', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('180', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('181', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('182', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('183', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('184', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('185', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('186', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('187', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('188', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('189', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('190', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('191', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('192', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('193', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('194', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('195', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('196', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('197', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('198', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('199', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('201', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('202', '4');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('203', '3');
INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES ('204', '3');

-- ----------------------------------------
-- Table: `users`
-- ----------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_users_is_active` (`is_active`),
  KEY `idx_users_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=205 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('1', 'superadmin', 'superadmin@university.edu', '$2y$10$qSz6Z5DjRT3S6u7F7YorTenBx.7MJoO70spf49sPk0sf9Es2GiNA.', '1', '2026-01-20 23:51:36', '2026-02-25 14:01:09', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('2', 'gv01', 'teacher01@university.edu', '$2y$10$L57XV/ooxhzU3bIySZotYu6o76qBwhjW3skzFMDI9oYz1pmYvN04i', '1', '2026-01-20 23:52:16', '2026-02-25 13:25:03', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('3', 'gv02', 'teacher02@university.edu', '$2y$10$HUyNvqus4MMliILFXswpfeUVU.xlWeHM/jA7BQzucjDJOpXYgDxiC', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('4', 'gv03', 'teacher03@university.edu', '$2y$10$hGlphZ4kX5UoCYWFdehzyurWxQEH/ZN.U38NPftMS1.amZSj8b46i', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('5', 'gv04', 'teacher04@university.edu', '$2y$10$2LF2MF41w90lv90pYxQGnuGUTHlH.tnU1CE/.HUXA/9lVW3Whh8qC', '1', '2026-01-20 23:52:16', '2026-02-25 02:12:40', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('6', 'gv05', 'teacher05@university.edu', '$2y$10$tQUJKgCn.0XyRIr7oW81DuciWCjmgivIJNUgSp.fPVlnm9sLg2de6', '1', '2026-01-20 23:52:16', '2026-02-25 08:23:24', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('7', 'gv06', 'teacher06@university.edu', '$2y$10$.krvdK/W2TWvvCLroTKgwOYrlHOhAIPG4utF5aCoU1CJ6Zfv9f8fG', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('8', 'gv07', 'teacher07@university.edu', '$2y$10$p0xYW/kZD8GNczbKpuZ1D.dyxVvWN0x6UxND0Poz5c0k3SI4Nw7rG', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('9', 'gv08', 'teacher08@university.edu', '$2y$10$kJfsw3V0U/JqN8mokroFveNVfx0bwbuOzosX4GT5T2Qn5HJVnHJq2', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('10', 'gv09', 'teacher09@university.edu', '$2y$10$1tVe8uAvd1ramiyZpqQrGOZ4Yvmk49FIoUw0dPNunjCYaDIIxN9Gm', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('11', 'gv10', 'teacher10@university.edu', '$2y$10$QgqQGBrEiayHiIx4MGx1R./7f9pnhtZt/TK4fFEfKoCDkjPrYRDwW', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('12', 'gv11', 'teacher11@university.edu', '$2y$10$b7Hmzuqj8Mi0U1dGy6xFSOF8LjfvASmpWKDsM4GrX5w2IjsA1SEse', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('13', 'gv12', 'teacher12@university.edu', '$2y$10$1ncE9Oy.R1a3WoprJc1PJuPZQhBH9LHSU0d9Z6FssMEJ0tGYscjIq', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('14', 'gv13', 'teacher13@university.edu', '$2y$10$faRUu.3iK1zMz7wzYaTKD.tsQGkG0mry75FNI6oL7PyfKNgZJuaQy', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('15', 'gv14', 'teacher14@university.edu', '$2y$10$WkboU0C/oMUA4jRT3hTRNOO13MCAhSXUSceLrn.gpLgRsCT2pd74u', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('16', 'gv15', 'teacher15@university.edu', '$2y$10$M0Qhj9gRUcAlD9hwhF4MhuNIju3SSucl5xd2KxziDbAdQjpfLIpuu', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('17', 'gv16', 'teacher16@university.edu', '$2y$10$OzxKYi8lq5yOQ9SUEWz7dewNzOo6rEG0GoFoA50mMZZP78XrNmcsC', '1', '2026-01-20 23:52:16', '2026-02-23 17:00:27', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('18', 'gv17', 'teacher17@university.edu', '$2y$10$djp5AqyfAJBWtKYk.ofJt.S6xWcyKDMD/jb.gmn9DbV8lh1HGSNE6', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('19', 'gv18', 'teacher18@university.edu', '$2y$10$dBEfrSGQHWuq34PzIg./muHedo/x1fB2ejXXOwOGFbZAjXykSlkZG', '1', '2026-01-20 23:52:16', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('20', 'sv01', 'student01@university.edu', '$2y$10$TlU.nIBSRf08dc7OkrZI6.9MMY2kAMvf5tBNY1VpQqFOt7YOaKpza', '1', '2026-01-20 23:53:03', '2026-02-25 09:53:13', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('21', 'sv02', 'student02@university.edu', '$2y$10$7F8i2VvZpA3wEQSkISRluOGWakNmbZ05wP7kNvM1sytFJa1XpV7mS', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('22', 'sv03', 'student03@university.edu', '$2y$10$FIQpfZYQerJzlVrzIN6pZ.2/dht4SFz/gL9x/scEpyjpyzWflH8uC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('23', 'sv04', 'student04@university.edu', '$2y$10$9PD5G7Jk9SYS8k6oKTfLi.hghqwwwmRbmMev7orad9KlGWxK7ny66', '1', '2026-01-20 23:53:03', '2026-02-25 07:53:13', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('24', 'sv05', 'student05@university.edu', '$2y$10$Bgb1RGDWlRYzkAIs0TCo7OjwYyVCMvDdjhmIP7G1EwtYQGfGEh/U2', '1', '2026-01-20 23:53:03', '2026-02-25 07:55:28', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('25', 'sv06', 'student06@university.edu', '$2y$10$drV6ssKMzy9KtoN73GsUo.NQO650aZ9AgJtoDKoEljOnp1PuEswI6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('26', 'sv07', 'student07@university.edu', '$2y$10$o1ISK1oqN/TKBvUK/mletuMaz9L3aWMDufPqiXZ9imLEPVHEPf6u.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('27', 'sv08', 'student08@university.edu', '$2y$10$x/grPYczw4zr4tPNQf/lZuB/9bcVKbudrk07E5UGXYxfRs9/cyYpu', '1', '2026-01-20 23:53:03', '2026-02-25 08:06:47', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('28', 'sv09', 'student09@university.edu', '$2y$10$zjT71AYiLC4shAxgHPdlXudHJ5sKvDP6WRIvGY9SI6pYVmMm2MBde', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('29', 'sv10', 'student10@university.edu', '$2y$10$IolU5Uo.iDU7nHYrGadsp.S/t46Igt.pbD7OL0NOonomsN81HLn2q', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('30', 'sv11', 'student11@university.edu', '$2y$10$aTPrjyG8YsjFiFZ.eWm1WuEMSZoIWiTnOpC4Q7kDS0P5dpMuuEcvq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('31', 'sv12', 'student12@university.edu', '$2y$10$CHpp4A8pWd2eRmcun81W0.WhrZZKxv6n9XBPHFPw6M6l7DfJg3Xuy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('32', 'sv13', 'student13@university.edu', '$2y$10$sDSO12c.5XroqF9OyHDK/u/L0a5d9Q1KQrUnfu1VGJznRWypuf3AC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('33', 'sv14', 'student14@university.edu', '$2y$10$SpFLB6BnWAgmVx/jX9lvIOCT3lQbP4qBfKXNEb2zEws4RtebVC8C2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('34', 'sv15', 'student15@university.edu', '$2y$10$TMgNBd.4p/CrqN6FxKrS/uwcKfg8zbnpbBOu.4oyYJmhD7BM7KsSm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('35', 'sv16', 'student16@university.edu', '$2y$10$7MIbgOLPbFf72Hz.yMjdCuRy5KQTX4vVn7ZddFaOGRrErz6MN7vsW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('36', 'sv17', 'student17@university.edu', '$2y$10$VIJhyBW.b8c2T1gYS/i5SestUbsqjUIzhcXGn3XM1GbFtrWpR2pL2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('37', 'sv18', 'student18@university.edu', '$2y$10$5w1cgwjHmFxbgOCZPFO0XOL31u2RFC4CiaAtAbiZf34.COWg7GEju', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('38', 'sv19', 'student19@university.edu', '$2y$10$vUtL/MaxfsUf3KIJf5SO7uzrIrXoNhfjBAWqNsDI3KBLgxgQsk0PC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('39', 'sv20', 'student20@university.edu', '$2y$10$v8rC5fTXsxDeOulLVAfKG.32njixFAsvvH4P1lmZesEBuqaf.kogq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('40', 'sv21', 'student21@university.edu', '$2y$10$gqS0BvUtMcHiWSMTTVdIHOeKIHWqIchq/gngaZwGf0.s1j4f9RZ5S', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('41', 'sv22', 'student22@university.edu', '$2y$10$LJLNU1BzRZLDXE5NNtsY2eCkACm9RG7Wr0.LmqiCullNoI52vqmmS', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('42', 'sv23', 'student23@university.edu', '$2y$10$yUFPaDwzLLj2UVHDgAKWh.HVO7Nr8/va3dsdLebyv7Fcyta5RS3Sm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('43', 'sv24', 'student24@university.edu', '$2y$10$GAHNw/eq4kFFv9zccvfQ8eQ016MkH7wxxlMwMT68NcHjynreAcXx2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('44', 'sv25', 'student25@university.edu', '$2y$10$Ars76cysPa2J2vjcjyLqReESYEcKW3ZPY9zQsJAHotgEc/DqZMp.C', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('45', 'sv26', 'student26@university.edu', '$2y$10$yP3PiWcpqTdndhZwMgQhhe6yAqEkfPp7EWOlino/tgXM2N9vt0oL2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('46', 'sv27', 'student27@university.edu', '$2y$10$xTaZftz8YYJpdA0GkJqitu3CkkWK.ThZNz8A1Tchn1oxDc4Pq1DRK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('47', 'sv28', 'student28@university.edu', '$2y$10$368bHF5mOti/g/ps1eQsN.2tCIiIFuoWtVU9Zk.ac3sp3mUbRySTe', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('48', 'sv29', 'student29@university.edu', '$2y$10$5OyNCW8o42adtx.0DQrrMe/UgTtbzUwrhtaMWHFFcde2t8tsKNy9e', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('49', 'sv30', 'student30@university.edu', '$2y$10$me67hWJ6O9cdFpGJUMG7ru/oObsWGwqQ0TepzcB2TT6lXusY92Oou', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('50', 'sv31', 'student31@university.edu', '$2y$10$zlj/Os9XefGKi0FvShJp.eSSDdYKqtF8RrpeSsoeOExW20wReDiDq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('51', 'sv32', 'student32@university.edu', '$2y$10$1T.hppInyOyZ.1/gey26D.78/IPD.Cr2c1suDzZr7byM7p6rO4WQa', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('52', 'sv33', 'student33@university.edu', '$2y$10$FoV9OIoio1/Qy8FEQ3MnFueQjk4NTCKwqYm2WXoBGL9d7IKscZrv6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('53', 'sv34', 'student34@university.edu', '$2y$10$wGuP./PYrBmTRGf3hqwnreBmVbHtEg6MxJ.7omizusBnrPG4z2BIW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('54', 'sv35', 'student35@university.edu', '$2y$10$oG7c6lmekX.Rgsxw764fSux5yENt2WgaCtN0xEEBAEujWVYf66Pxi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('55', 'sv36', 'student36@university.edu', '$2y$10$ECuUdVDYKR3MLbUdAUmgju3AesG6dakYwIdVbDD6COgOr49xLgZq.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('56', 'sv37', 'student37@university.edu', '$2y$10$AxGRvZgDzfb0ygmEgpwZEuNPett6Ex/QuWY6fqmufm2Vu3WxmOGu.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('57', 'sv38', 'student38@university.edu', '$2y$10$/HozEKd7/nQSdpGjhW1wzOt2QW7kMU.ptRjVbv1Tj5UIYCaRD71l6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('58', 'sv39', 'student39@university.edu', '$2y$10$A84zqp3R8XtIpNmqGJO4C.8xF7o29hKhSfC9XDLPNyEzm6FsT1pwO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('59', 'sv40', 'student40@university.edu', '$2y$10$CPddVqIHYYUxN2QuZCX8.Oyb1EjKTIBp4Zetelu.UJDNhmjJ12K7W', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('60', 'sv41', 'student41@university.edu', '$2y$10$KyPIzVFzajLu6vaZeWeJ4eSy4hJl88R.M7he0Iz2dT/0biPJsyAWW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('61', 'sv42', 'student42@university.edu', '$2y$10$yXGPWBx.JAxS1BR9PR.F/OLPKeS1NPUH/jCLmsXFTM4/QmnMJWHKe', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('62', 'sv43', 'student43@university.edu', '$2y$10$fPrIkWeMG0hKGNgKMk4gSOP6m22yfwo0eDNAT54eOmuznmgLhbkqi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('63', 'sv44', 'student44@university.edu', '$2y$10$AmDVx90rIGvZxC9cEgaKpOGMIkMdQztwenY04SgkqGusJzgR0UhvC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('64', 'sv45', 'student45@university.edu', '$2y$10$Ss3RM59BFxzoh90jML6.geIRkfZKFFfZKm91V4OCT0EbZcuD.d1re', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('65', 'sv46', 'student46@university.edu', '$2y$10$kgodZ.S4xvK29M8X7wGGqO5zffZwH5fAeQpV5tFu7x2iyE0sAyD1C', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('66', 'sv47', 'student47@university.edu', '$2y$10$66nabh6rPSQdBn85dudLbOB9eFu0RMh6xbOqK88Xjy59fcvbJhQHG', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('67', 'sv48', 'student48@university.edu', '$2y$10$S1m.yHVb9tijMn/NUeyHb.f7igxpUAzzKieV4zqH6Gk3.uqZv2HRO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('68', 'sv49', 'student49@university.edu', '$2y$10$OiUYX.RB3RBqIYlFSnZ4K.zokH0VegOK/YJiYNB.QRjyqFj6b8Wwq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('69', 'sv50', 'student50@university.edu', '$2y$10$iZh0ts6JD06x/sJ/RKre9uKYEY2THfwypTc.At6sWewm5lRsERIw.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('70', 'sv51', 'student51@university.edu', '$2y$10$Ya7J72fHGSAYK48zlvI3tuGhkeKb86B52qwX4OcDrXO/3y.Fqf6TW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('71', 'sv52', 'student52@university.edu', '$2y$10$xSRh2BkBjaN.u0gTSkki9.uX8k7z85Dmh5/wMXPr9c.XQ6O5wO36a', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('72', 'sv53', 'student53@university.edu', '$2y$10$X37nCP9tcNpqT/xdqGlfmu.Y1tz5ZL/v55Jfi9DicRn7/gRMUcINW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('73', 'sv54', 'student54@university.edu', '$2y$10$Z6rKUREtXon4dMZ/2fSw4.IDISZL8dD05JrW7iyyJ0..TFbjuyXq6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('74', 'sv55', 'student55@university.edu', '$2y$10$0Gs0BUGbLQufrQX1AJhTEOWAtxe9BSVt0I.sOR4ArR8XcIKU6AA1O', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('75', 'sv56', 'student56@university.edu', '$2y$10$9HZzsQCR1ehbmiqtAvcTv.N17on2idt1/aYUTL1E3trQy2asZzJnq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('76', 'sv57', 'student57@university.edu', '$2y$10$qlRSDr5gfrgs/KSg0Ya.IOEoF0XYf5qNgT8J.24F4s.0/aoCkbaw6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('77', 'sv58', 'student58@university.edu', '$2y$10$nC3zMH6imcN.kY5oPiJkb.MwMJvvmXOVdbt2WvHURLrPQ0eXMKnba', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('78', 'sv59', 'student59@university.edu', '$2y$10$9TK.36Q800h7EgJirz3l5uylPfgZWAu7mBrzFHSuF9pChKN5qNimC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('79', 'sv60', 'student60@university.edu', '$2y$10$BO.IAV4B7yMKQ3tYkJaXgePNMoYQNjrDutreFAwKs.PfIVcxdrSqK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('80', 'sv61', 'student61@university.edu', '$2y$10$F/W3mTAnUfpwDtN3SZv5SOt/7aDqADknT9Zz2T4H2QzB3VxYOpLc.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('81', 'sv62', 'student62@university.edu', '$2y$10$p7w08FTv1Trss06UhH3WB.vmONXHBmiiDaQPiS914OgNJhSXQ2ViK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('82', 'sv63', 'student63@university.edu', '$2y$10$XZOfGyntW7yU7DTCFHokEujHPzb.ZWbt.ZYJJ/iRFpl1ibbt47q7G', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('83', 'sv64', 'student64@university.edu', '$2y$10$7mu0gvGSMRS6fWaxeuPsQ.VVVgNku7aERpI7xB8/hQlAP9OfjK3Ye', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('84', 'sv65', 'student65@university.edu', '$2y$10$MMiHBj/VcjpPdK9Oi1.G8.U5YvBGexLFEco3mlcVwUc72b/g9EuAW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('85', 'sv66', 'student66@university.edu', '$2y$10$pLDIuU.X9zQttj9rDYiSWu9OZhQplsLZkJ1TvDVAmKFAf.gB7H.sq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('86', 'sv67', 'student67@university.edu', '$2y$10$CJrhnu3HsvwwotyaaASFBOA6EwP5rF714PfYZcjmKwSgWBWQl1/ny', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('87', 'sv68', 'student68@university.edu', '$2y$10$Jv/ib3T8I9AHszL3JzcCu.BC29bRBVTwTKRccZGjukxC1bWSIuQXy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('88', 'sv69', 'student69@university.edu', '$2y$10$ViSoklcGaRVqKKhumc4VZ.b.fVFD/n5KsJ2Un9OOa0kehl9WGMWwa', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('89', 'sv70', 'student70@university.edu', '$2y$10$zTiX0CzLO6y80W/EVH6tAef0EE4AoOXxqQhkfpMYwwLc/U6RNxM16', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('90', 'sv71', 'student71@university.edu', '$2y$10$J5XM1QUuRktVr7hSMP.1PeANiagZOjQ9qGuQOWjSfHWkuWdsng0Wu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('91', 'sv72', 'student72@university.edu', '$2y$10$GUmPoHCgZxf7tpPMRHu.r.t53sy6ET6QXtjYX019gMFb9UdEesy9q', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('92', 'sv73', 'student73@university.edu', '$2y$10$d/azNzDYSjdbpIgxntxnru8SAkQez2PSUrykuKrVjnG9NOT9ViY82', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('93', 'sv74', 'student74@university.edu', '$2y$10$nuypVwhkHsz4C67owqzDk.wADhg78hZDRpI9vNNHQ5WeBboAhH2B.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('94', 'sv75', 'student75@university.edu', '$2y$10$pD6m3uiHddANRW/IxHmVme/jfDSS1kfcVXb1fVilLcnQ3EnHQDRru', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('95', 'sv76', 'student76@university.edu', '$2y$10$zFD8gzVv0mCdQmZ4NetDY.koCXH51Txc7G.0dQGgHHveBNdHnYQNS', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('96', 'sv77', 'student77@university.edu', '$2y$10$9a8OVwpqDL281ob0Y9n52OZjt8tzt/GtUi7GoLiZ6GIuFCKhmpWwm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('97', 'sv78', 'student78@university.edu', '$2y$10$Tdb9GINHjSlDsSqlcD9N/.2wMLg5rJSsJWz0YKQMakTu/r7hSEGca', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('98', 'sv79', 'student79@university.edu', '$2y$10$qnDedrQfCEz8R.ehBKXP6.xnS640WBuXBqTSDSJ1T8Y9YvBfIRDIm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('99', 'sv80', 'student80@university.edu', '$2y$10$18CXfzeRnN7tWPsbxSTdOuSiS0vH.w4kHAnDjvX6V5moQTUze1iMC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('100', 'sv81', 'student81@university.edu', '$2y$10$1O4TvoqYmoBX0Wy4E3PU/.OZ9rJyljBWRYvN53EJ3zpttUpIRykrq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('101', 'sv82', 'student82@university.edu', '$2y$10$qI9mKRRDot4xRmwUpveJ0.apG.kuxZf2mCHEz2v.ykWPHaXIArhse', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('102', 'sv83', 'student83@university.edu', '$2y$10$o9MY/DCOVb6fVtgOtuNgN.4M0pf6FFPdCEVzMrsk9GClpOP1oPAwC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('103', 'sv84', 'student84@university.edu', '$2y$10$OrRv.Jw7qY9aBdVsszCLhu2fYCryhltY3e3fFXHh974L/iRubxYlm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('104', 'sv85', 'student85@university.edu', '$2y$10$F81MhcuLrYGiV3d8TBdYA.P6XhTZU7dsS5ErRXIZKJwEKLrYgHIVu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('105', 'sv86', 'student86@university.edu', '$2y$10$s15.7ZJTyTaFNqIZkD6Fc.jFf3MUWsW8dhhtYkwo.RcMsFgTmKcYu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('106', 'sv87', 'student87@university.edu', '$2y$10$HSnL4c8fZ/AD/e2l8fIYE.UJ9sh0jg6VAxVWiYeVsBxbEIGmdz5qi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('107', 'sv88', 'student88@university.edu', '$2y$10$/aGbBO1V3jh6vXhVLIxEFOAJJDHOp5iML7ezbscwzhlo/XDi/Ce9u', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('108', 'sv89', 'student89@university.edu', '$2y$10$kaj099wDpCW/GHeU7wyOneskY0fZ3O7xmlrmSBRyNcTVvSz88WKp6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('109', 'sv90', 'student90@university.edu', '$2y$10$cI60Y4qPm83SPuWhFA3mh.lXwXRGZ/qb8VuIw3yNyEUiBrdXOHJ1W', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('110', 'sv91', 'student91@university.edu', '$2y$10$Nu.SUiCstOq0Ybc3UmaOS.PONfiqLSSm/d8Gr1CHNNb5Wd6uMwOrS', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('111', 'sv92', 'student92@university.edu', '$2y$10$c2z2/g9tFxEwq0iE7sx1oOatErgegqBJq.7zVa9Ba.oRRY4jojDcy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('112', 'sv93', 'student93@university.edu', '$2y$10$YVd3GCnlGlBZkJ.Rg31wWeQHftUaRL0NquEAqLDfWOQf7z7cc/Unq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('113', 'sv94', 'student94@university.edu', '$2y$10$peh.IrUVjxP/0UUjIJ2uQOu9Zfcc5ZFvGLR6GTHooTAU5Tm/d18aG', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('114', 'sv95', 'student95@university.edu', '$2y$10$gTtVb9CHKnu4Ayc4e6TtfON5bPGY8CwgvHv8l5pbwphNkdxUHdCb6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('115', 'sv96', 'student96@university.edu', '$2y$10$sjBxJ8cNaUGLfApHDlXPcO.3XruK1K99KfcqcqdMQYeeIglVQj12S', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('116', 'sv97', 'student97@university.edu', '$2y$10$X61pHEsTFJjPfIs0jU3T8.DPGkiwoshkyzZ2ed2p/HMvCAJpk0.h2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('117', 'sv98', 'student98@university.edu', '$2y$10$6lPvvrpZElua98rXyoXB6OKbtBJNtp5PdxWxdvjddLQGkHroN6.p6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('118', 'sv99', 'student99@university.edu', '$2y$10$ofEw2EgQ0RSV6EQXYZeCjOsHmzIh3CnHF.uefaqQhQyjocwTJdg9i', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('119', 'sv100', 'student100@university.edu', '$2y$10$tx586uCA9KruqiUZ3zBm6usVfUpZHbWUdOWoFWKF7/7qSHRa0/gEq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('120', 'sv101', 'student101@university.edu', '$2y$10$UC2XONZED8JDqU4OZZCeWe3rWSe28XmMHhXT9hOF16M3KonuIR8Bm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('121', 'sv102', 'student102@university.edu', '$2y$10$8DkSVXYzerEvYsfbDNR/vuU9j2p4b/nfWocUHbGUfPeLM2TAQhaZC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('122', 'sv103', 'student103@university.edu', '$2y$10$RJaUXRzq1K9eNSLl0vaSROGNk/h/ZyIrklrWay7//.o/QEalFsPMy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('123', 'sv104', 'student104@university.edu', '$2y$10$.KN5c2UMc/ttdwd5G9gq3e3YvcZK8uNYHWc2plkzPiQVdc981QiWi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('124', 'sv105', 'student105@university.edu', '$2y$10$Gu4hcHAxiaXsCxfIGywlFemuLAcXxssdGt4qc6weER6ou7AToz336', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('125', 'sv106', 'student106@university.edu', '$2y$10$GKhq4RbhdZp3WE3QwA52V.HwGhG8hsp1BctDWytjpGpOJQqFqTfpG', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('126', 'sv107', 'student107@university.edu', '$2y$10$2DK7pbLjynlHrHEQfDWVg.sb5fC2ipVJVdotoj0ReN4rxJ4aHuBym', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('127', 'sv108', 'student108@university.edu', '$2y$10$hZEM/OEpl/vJkHwJQhiD8uOzp8jmobO3hLBBmVQN7kCbkJ47DHmHO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('128', 'sv109', 'student109@university.edu', '$2y$10$4HeTqtwAB8wud7BfNrKyd.pVFJXnqKSahtptBWoodbB9Q39wyyZJy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('129', 'sv110', 'student110@university.edu', '$2y$10$GyrZcuyF3kZBkyL.XM9/3Ozk0KMtXdDBoJvzUPgICgiwwP.GgARVa', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('130', 'sv111', 'student111@university.edu', '$2y$10$dxB19XQeIiEHFNVmlIqPM.aPfuZqWGbq8BYIjIwH9A2ZIi6K18LEa', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('131', 'sv112', 'student112@university.edu', '$2y$10$0R.ntWApzot3WzDqxCyvKu1hn3obll5rUJK.4RlkxsufNtC9Rteha', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('132', 'sv113', 'student113@university.edu', '$2y$10$Q57hP8bphv0eeFlvE8pP0.FCm0uLqw9lA.HBO0Lyv2perJ8vaZdfW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('133', 'sv114', 'student114@university.edu', '$2y$10$KTjO9bN5LcjoOlOXc0OL0eUEAU6KP5JlYLVyoZiKxmnP9r/8GOEiu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('134', 'sv115', 'student115@university.edu', '$2y$10$R2.B8T8D/uOFOPTG5dvmlOJGZ/iRqFH/TYU2IqwEkNUgQyb5F/Dai', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('135', 'sv116', 'student116@university.edu', '$2y$10$whjtufeiUwgqfa704BDqQu1m6LjiAZ1Db6P/ikXGGUMxAEGqsEo/.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('136', 'sv117', 'student117@university.edu', '$2y$10$dShFGLfPaS3W0nO3xIqf.ulyUnLAoe4mV6vbvZUIa494DHUwsMSCi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('137', 'sv118', 'student118@university.edu', '$2y$10$L2nFUL50MC/w0uYBWJYdw.pMlTj8vmZm52CleWeUHD7T/P19bQLYi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('138', 'sv119', 'student119@university.edu', '$2y$10$6N45VnHr8Qiio/MttvY0D..bNf563UjR6nbHMJ9.2ufU/fUeMuM/u', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('139', 'sv120', 'student120@university.edu', '$2y$10$UPLa1Bo8fTVfZKvgExwO6.xEyPWq2kacHtTXUFFwLqm4J8Zob73rG', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('140', 'sv121', 'student121@university.edu', '$2y$10$HxkxWoNpX9PJtk3qna0If.WvzE/iUHuIN6JFRCVRTnf9u2MeJwM8O', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('141', 'sv122', 'student122@university.edu', '$2y$10$xycj8.tBLG/lKt3Z1TD9Le5e6qF/hG289oMtLaF6xGItPzDpbF/Ua', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('142', 'sv123', 'student123@university.edu', '$2y$10$F06VCjNbVkvXtDGXdm884O/pW/0lwwItRHkJiN2Fr4/m3jiLAczqu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('143', 'sv124', 'student124@university.edu', '$2y$10$XWcqJKbSYxCzA0Tc1xM72OoJOF0VuT11mMDJSsRaDA29s88Y3jHpy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('144', 'sv125', 'student125@university.edu', '$2y$10$FQPOpS5UbqZcg1Z32QpU3ecLnB7tIsbvkKgfLIKDMC.A/KKrfbUIq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('145', 'sv126', 'student126@university.edu', '$2y$10$ei4CveE7B2RKJ1edXTwMme4lWGpylSde0tG81BpAnXsemYpg/gX1G', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('146', 'sv127', 'student127@university.edu', '$2y$10$GSNPG57fBvS.p6mlm5PKo.1nJDIfvT1d6Vi6VE8fU90O7i.ulhQ1i', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('147', 'sv128', 'student128@university.edu', '$2y$10$P4npid6o8myhl7DQeaanHe3n3iSWj7oKYUhyq5odufAe/OpLU6wUi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('148', 'sv129', 'student129@university.edu', '$2y$10$xar4VLWf7Ho3BuRKn.NBve83uCoVBwEWHxvYej1wcl44F/RYATCU2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('149', 'sv130', 'student130@university.edu', '$2y$10$R7hPtVs9YEODXGdaiRddN.IbieHjnHiOvbOebx./WpU2GXRURfv3.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('150', 'sv131', 'student131@university.edu', '$2y$10$vHEbayZvwjiePKVud9WyBO/gS0sXEv5vYUBNVw8UJ/dgu5/RoZUf6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('151', 'sv132', 'student132@university.edu', '$2y$10$cGVMVcU8ewgeiVGKluqxz.G.aXwZ2MS1.Ymiir7rfGxna4mrEp4TG', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('152', 'sv133', 'student133@university.edu', '$2y$10$MJQrmDy3AWN3ZAtUVN5qT.uJjRaMm5I3KMOJgRwmyUdvX3kC2VR8O', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('153', 'sv134', 'student134@university.edu', '$2y$10$S8pDgGEy9XBsmMoW2Kz3f.g72WuUTGWzbqpcE.Ro/AYz2q4KYzpia', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('154', 'sv135', 'student135@university.edu', '$2y$10$ZbR7pHrFpwP4gqR0S4ByYO9KyaHLKTL.9t9nMijnbXLHAkhT7LiX2', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('155', 'sv136', 'student136@university.edu', '$2y$10$xyeV/ivsXTrDPuCZcAylou8CNXGFF/uX8FYIcnb9hmq0Th3I5L44W', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('156', 'sv137', 'student137@university.edu', '$2y$10$Jx6NRF1TmVbMlbqqutoXQOctu9yxhJdj.4YSu75299X83SoMOzAJ.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('157', 'sv138', 'student138@university.edu', '$2y$10$QFI6evYxt5LkfvPYaJd.8eTgC2Bv4fUmTg2uQ1E9/j7B5fZh0qzsu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('158', 'sv139', 'student139@university.edu', '$2y$10$oRvM0DLJjI8J.f057Qu6jeCq8f6Vp5cigmlG0nCjQcTvkoN8tTj2O', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('159', 'sv140', 'student140@university.edu', '$2y$10$z5x3jBi0Vgt.TMVwhR3xROaHMoHQmgxgttqos2QLNr4S7qpiyaRAK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('160', 'sv141', 'student141@university.edu', '$2y$10$MRjRPCjELMWn.J.r6478zeqyOJrEr0km4xnFSEf1tFE7LROMzV0De', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('161', 'sv142', 'student142@university.edu', '$2y$10$qqsv7AtY3dooKY/k/wgau.RfwO03VupUmXygu2qvZlQefMAf2wlb.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('162', 'sv143', 'student143@university.edu', '$2y$10$U0sNo2BZXT7XKfPjAmPs7.9qWB54p/7VtADb1c4NLkDlIjRJHJ7fO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('163', 'sv144', 'student144@university.edu', '$2y$10$iW.W6MSCXXMQoY5BuSMwxOhPcAuTpF5JtbdwXDECTcLzORuesHnvK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('164', 'sv145', 'student145@university.edu', '$2y$10$sohNd4YYbYIj6h7Ac5kui.ZK.Mv5.SOPoxIlfbSoT24WJZ08pWBEq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('165', 'sv146', 'student146@university.edu', '$2y$10$O85qbzmyo5eau6hCmSvOkeKPIyygxpGnpqAduouFkNj6LcxnmoEdO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('166', 'sv147', 'student147@university.edu', '$2y$10$0Pt.FKswuNRGgVAguANDwOFWRGxWbrWuEew/7Jcp6j3Miz1t09sjq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('167', 'sv148', 'student148@university.edu', '$2y$10$dRl4hKdieH7MneuJ/tauCeIBlCPuUx/YtRUakS/c6m4wTk96iENse', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('168', 'sv149', 'student149@university.edu', '$2y$10$eOggOL6K.s4y2EVRcUwdPeyfgWN1hWkP/OhvaLWewAuo3BA6CDdjO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('169', 'sv150', 'student150@university.edu', '$2y$10$1FKpJBa9ioMYr6Kxah9zUeUkj7A8nCXmRTZG4bVV0ijF/ChdEyp26', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('170', 'sv151', 'student151@university.edu', '$2y$10$bMsLK2u0754TCK0YUb6dqewR8D4qbfm9UUBIM.7sI3cOj3mLCQE2m', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('171', 'sv152', 'student152@university.edu', '$2y$10$PTE8/dYhGQxRRDC1dihid.9V/KlzJEMqLGtAf1EvH1TVrcMnKBJBO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('172', 'sv153', 'student153@university.edu', '$2y$10$9h.dqOdorwnKtxKFXBwDLu.ShNcRg2u.6BplkkQYrzNKZDW51q/LC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('173', 'sv154', 'student154@university.edu', '$2y$10$GFcnPk9kN4mSEckx.Iz//O6t0smiX1UEkS0wmFAPbG2DitUrd838K', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('174', 'sv155', 'student155@university.edu', '$2y$10$ZkfmqdRD9qet6cTpL2R2aO1hER8df6G3MhUJZKYNq91rZgSzJRde6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('175', 'sv156', 'student156@university.edu', '$2y$10$HdLe.gX8k8V0BkfNEGMfFuI5n1BMTiBzhvaZs7XMMWddCQ8.k8jRm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('176', 'sv157', 'student157@university.edu', '$2y$10$4LeZGXuMpmjkV9oblfjhzONScZ/pqN7sWSQsHGmy7g8Oqg25pB90G', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('177', 'sv158', 'student158@university.edu', '$2y$10$FdD9zA4sgMsOHH7KdzCZnOE.aaSpfbo0oZKjjawe35D1xofwgV69a', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('178', 'sv159', 'student159@university.edu', '$2y$10$Rl.0lKqThZCDUEyjJLZzl.gxk9Rd.68kXaGYjwpSX/J96qO0mbR1m', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('179', 'sv160', 'student160@university.edu', '$2y$10$Y/ooUkxFdIrFBp16SRf.TOxv3NlqhT/YPMOVTYy70HAkNiv9GyGGW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('180', 'sv161', 'student161@university.edu', '$2y$10$hQdjQww4T5VtOTA9uMyqCONnf4AhWwrTrNWnhMqxqHfIlB.M6jfJm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('181', 'sv162', 'student162@university.edu', '$2y$10$7YkvTO6yqyX4wknuBt6kwuKH3.36r4cW6uyKkiUr6osyZUywZ0EUK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('182', 'sv163', 'student163@university.edu', '$2y$10$byKbZgQA3Z.obGI5eWVv1.nw5KU7soBfA3RwSO7zIosLOYu4nwsyi', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('183', 'sv164', 'student164@university.edu', '$2y$10$NO4pioPSm791VnMqJTJI0e8Y8b1on7/k/9MXAVB3/8XFoIOfYEcmW', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('184', 'sv165', 'student165@university.edu', '$2y$10$7G28tVB/hwX6cpCstYj9P.jmv7coLkTKp0/x.SmjDX6MDf8CHOxwm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('185', 'sv166', 'student166@university.edu', '$2y$10$BwbLAwIzZypEoXJm9/QYNuack/RcDsh8Pzy/90szRVUfdFT3rOyR.', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('186', 'sv167', 'student167@university.edu', '$2y$10$OyYHXqPZ2UhM03niehRU7uknxtRRrkL.cNQr8PAmhwJSjQCZDfctS', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('187', 'sv168', 'student168@university.edu', '$2y$10$aUnnd/n5j6RVpI74IXNtf.nYcyxzdeip.MMMrHCyCxG7mecEhLSky', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('188', 'sv169', 'student169@university.edu', '$2y$10$rTLhQ8yk74KuQ0j0FtYsEe2lbGXAQj7G8ZvNNCjV3d7OPKl1tVWum', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('189', 'sv170', 'student170@university.edu', '$2y$10$WsQLotfoya946YLqNOdMyetob4ftsfOYOndETAwUg6ZLM/pHtPPvm', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('190', 'sv171', 'student171@university.edu', '$2y$10$rWjBT3X81QVTfg/Y8q.uG.TrrzMaxyw25VF/AuCbPkST75fR/buxu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('191', 'sv172', 'student172@university.edu', '$2y$10$01gFwZgzFXfdILSzSFR1VOMwr.1nKgsX9icp7hey9RvBOh2LA.6km', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('192', 'sv173', 'student173@university.edu', '$2y$10$fErUYSkRZR4QyCKsw3PULOifdCFUzux2WxtsCmQqh.TcmSqsuFMxq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('193', 'sv174', 'student174@university.edu', '$2y$10$eN9eicpnYO3K0s5YKp9MzeceXJ1OBoMNxJRiL1cyoE7EocAtru4Vu', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('194', 'sv175', 'student175@university.edu', '$2y$10$/wWzHoF/mCvF.YBYy0z4a.IhC.hEmV4IAeqGoHOuAvES0bTtZW8EC', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('195', 'sv176', 'student176@university.edu', '$2y$10$uXgUr/vZbF28gtMJrIQgT.ZA.1SJL26FOQjqak5URB7mV1TyUtfdK', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('196', 'sv177', 'student177@university.edu', '$2y$10$DCGB8KLMEnwfsUTreszyaegX5tHyM4PEzu3/LUO4EzIyXOkCoYtPq', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('197', 'sv178', 'student178@university.edu', '$2y$10$DYK.fCZVASE1Sd0nda/ML.3IScX1pcZvIRKupHKcZqL6iShpWM3U6', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('198', 'sv179', 'student179@university.edu', '$2y$10$y1FjCjZXwE9Xp5mwHsYjNeidRLFJkE7A0NylqCj7D5UJ7BdgFVQfO', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('199', 'sv180', 'student180@university.edu', '$2y$10$wQ7w0Xw/DvjbvtfB5AdcWuo4RNx7cpDWVtSR1hZeIKb06heV/EnYy', '1', '2026-01-20 23:53:03', NULL, '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('201', 'minh', 'minhhvcb@gmail.com', '$2y$10$fapE17IcgzKzPtehWsCjquC5ma4wspZ/ePCPSygo3lIXYOYrXMYcG', '1', '2026-02-25 08:17:37', '2026-02-25 08:17:47', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('202', '123456', 'minhka5k@gmail.com', '$2y$10$56x8aSBQU5hsQ/d9FWiw9edAo7FEoaqGkFdJwNB6o0RoYEltoUIhC', '1', '2026-02-25 08:20:02', '2026-02-25 08:20:12', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('203', 'gvminh', 'fdsfdfs@gmail.com', '$2y$10$W2ProGTxxZ09xtvjG6TaluO41xs/EMwOW5rjlKick0CeBcINrqP6C', '1', '2026-02-25 08:24:12', '2026-02-25 08:26:24', '0', NULL);
INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES ('204', 'gvminh1', 'sadasdaa5k@gmail.com', '$2y$10$rFfgtbR/GKYPVLG5JpeBWuo1xa0FKQaDFvAfpHoVz7Vuk9pGcPPCe', '1', '2026-02-25 08:32:54', '2026-02-25 08:33:01', '0', NULL);

SET FOREIGN_KEY_CHECKS = 1