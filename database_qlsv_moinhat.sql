-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Mar 04, 2026 at 12:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `database_qlsv`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `attendance_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('Present','Absent','Late','Excused') DEFAULT 'Present',
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`audit_id`, `user_id`, `username`, `action`, `table_name`, `record_id`, `old_data`, `new_data`, `ip_address`, `created_at`) VALUES
(1, 1, 'superadmin', 'LOGIN_FAIL', 'users', 1, NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:02:37'),
(2, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:06:06'),
(3, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:07:27'),
(4, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:12:21'),
(5, 26, 'sv06', 'LOGIN_FAIL', 'users', 26, NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:29:35'),
(6, 21, 'sv01', 'LOGIN_FAIL', 'users', 21, NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-02-28 22:33:16'),
(7, 24, 'sv04', 'LOGIN_SUCCESS', 'users', 24, NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:33:52'),
(8, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:34:59'),
(9, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 5, NULL, '{\"permission_ids\":[28,63,64,67,71,72],\"count\":6}', '::1', '2026-02-28 22:36:32'),
(10, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:36:48'),
(11, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-02-28 22:40:05'),
(12, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:40:31'),
(13, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 5, NULL, '{\"permission_ids\":[28,63,64,67],\"count\":4}', '::1', '2026-02-28 22:41:59'),
(14, 24, 'sv04', 'LOGIN_SUCCESS', 'users', 24, NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:43:11'),
(15, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-02-28 22:44:20'),
(16, 26, 'sv06', 'LOGIN_SUCCESS', 'users', 26, NULL, '{\"role\":\"student\"}', '::1', '2026-02-28 22:44:58'),
(17, 201, 'student_admin1', 'LOGIN_SUCCESS', 'users', 201, NULL, '{\"role\":\"student_admin\"}', '::1', '2026-02-28 22:46:27'),
(18, 201, 'student_admin1', 'LOGIN_SUCCESS', 'users', 201, NULL, '{\"role\":\"student_admin\"}', '::1', '2026-02-28 22:46:58'),
(19, 203, 'librarian1', 'LOGIN_SUCCESS', 'users', 203, NULL, '{\"role\":\"librarian\"}', '::1', '2026-02-28 22:47:19'),
(20, 200, 'academic1', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-02-28 22:47:50'),
(21, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-01 16:19:05'),
(22, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 00:37:29'),
(23, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 00:38:57'),
(24, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:17:20'),
(25, 21, 'sv01', 'LOGIN_FAIL', 'users', 21, NULL, '{\"failed_attempts\":1,\"locked_until\":null}', '::1', '2026-03-02 01:17:36'),
(26, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 01:17:47'),
(27, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:25:43'),
(28, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 01:27:04'),
(29, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:28:35'),
(30, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 01:54:15'),
(31, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-02 01:54:59'),
(32, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 07:05:20'),
(33, 203, 'librarian1', 'LOGIN_SUCCESS', 'users', 203, NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 07:16:41'),
(34, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-02 18:12:12'),
(35, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-02 18:17:56'),
(36, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-02 18:20:38'),
(37, 200, 'academic1', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-02 18:36:49'),
(38, 203, 'librarian1', 'LOGIN_SUCCESS', 'users', 203, NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 18:38:09'),
(39, 203, 'thuvien', 'LOGIN_SUCCESS', 'users', 203, NULL, '{\"role\":\"librarian\"}', '::1', '2026-03-02 18:46:21'),
(40, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-02 18:51:11'),
(41, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 6, NULL, '{\"permission_ids\":[82],\"count\":1}', '::1', '2026-03-02 20:40:13'),
(42, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 6, NULL, '{\"permission_ids\":[68,69,82],\"count\":3}', '::1', '2026-03-02 20:42:42'),
(43, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-03 13:56:21'),
(44, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 4, NULL, '{\"permission_ids\":[18,19,45,49,54,55,56,59,60,61,62,82,84,86],\"count\":14}', '::1', '2026-03-03 14:58:45'),
(45, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":37}', '::1', '2026-03-03 15:07:12'),
(46, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-03 15:13:21'),
(47, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 5, NULL, '{\"permission_ids\":[28,49,63,64,67],\"count\":5}', '::1', '2026-03-03 15:28:01'),
(48, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":38}', '::1', '2026-03-03 15:28:19'),
(49, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":39}', '::1', '2026-03-03 15:29:52'),
(50, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[1,18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":22}', '::1', '2026-03-03 15:29:52'),
(51, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":37}', '::1', '2026-03-03 15:31:19'),
(52, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":21}', '::1', '2026-03-03 15:31:19'),
(53, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":40}', '::1', '2026-03-03 15:33:47'),
(54, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[1,2,18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":23}', '::1', '2026-03-03 15:36:31'),
(55, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":21}', '::1', '2026-03-03 15:38:30'),
(56, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[1,2,18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":23}', '::1', '2026-03-03 15:38:41'),
(57, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[2,18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":22}', '::1', '2026-03-03 15:39:21'),
(58, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,5,19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":41}', '::1', '2026-03-03 15:39:30'),
(59, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 3, NULL, '{\"permission_ids\":[18,19,20,21,22,23,24,25,26,28,62,65,71,73,74,78,80,81,82,83,86],\"count\":21}', '::1', '2026-03-03 15:39:30'),
(60, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,19,20,21,23,29,30,31,32,34,36,37,38,40,41,42,43,45,46,47,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":40}', '::1', '2026-03-03 19:00:30'),
(61, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-03 19:03:38'),
(62, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-03 19:21:45'),
(63, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-03 19:43:33'),
(64, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,19,20,21,23,26,29,30,31,32,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,62,63,64,65,66,82,83,84,86],\"count\":46}', '::1', '2026-03-03 19:45:47'),
(65, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,19,20,21,23,26,29,30,31,32,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":44}', '::1', '2026-03-03 20:09:19'),
(66, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,18,19,20,21,23,26,29,30,31,32,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":45}', '::1', '2026-03-03 21:21:18'),
(67, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-03 22:31:09'),
(68, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-03 23:42:48'),
(69, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-03 23:55:37'),
(70, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-04 00:06:29'),
(71, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-04 00:11:25'),
(72, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-04 00:14:38'),
(73, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,18,19,20,21,23,26,29,30,31,32,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,62,66,82,83,84,86],\"count\":44}', '::1', '2026-03-04 00:14:55'),
(74, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 2, NULL, '{\"permission_ids\":[1,2,3,18,19,20,21,23,26,29,30,31,32,34,35,36,37,38,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,62,65,66,82,83,84,86],\"count\":45}', '::1', '2026-03-04 00:15:16'),
(75, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-04 00:16:11'),
(76, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-04 01:16:20'),
(77, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-04 01:46:11'),
(78, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-04 04:13:35'),
(79, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-04 05:07:07'),
(80, 200, 'daotao', 'LOGIN_SUCCESS', 'users', 200, NULL, '{\"role\":\"academic_admin\"}', '::1', '2026-03-04 05:45:17'),
(81, 1, 'admin', 'LOGIN_SUCCESS', 'users', 1, NULL, '{\"role\":\"super_admin\"}', '::1', '2026-03-04 05:55:19'),
(82, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 5, NULL, '{\"permission_ids\":[28,49,54,62,63,64,67,69,71,72,75,76,78,79,83,84,85,86],\"count\":18}', '::1', '2026-03-04 05:57:55'),
(83, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-04 05:58:15'),
(84, 1, 'admin', 'ASSIGN_PERMISSIONS', 'roles', 5, NULL, '{\"permission_ids\":[3,19,28,49,54,62,63,64,67,69,71,72,75,76,78,79,83,84,85,86],\"count\":20}', '::1', '2026-03-04 06:01:58'),
(85, 21, 'sv01', 'LOGIN_SUCCESS', 'users', 21, NULL, '{\"role\":\"student\"}', '::1', '2026-03-04 06:02:16'),
(86, 2, 'gv01', 'LOGIN_SUCCESS', 'users', 2, NULL, '{\"role\":\"teacher\"}', '::1', '2026-03-04 06:06:15');

-- --------------------------------------------------------

--
-- Table structure for table `base_classes`
--

CREATE TABLE `base_classes` (
  `base_class_id` int(11) NOT NULL,
  `base_class_code` varchar(20) NOT NULL,
  `base_class_name` varchar(100) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `base_classes`
--

INSERT INTO `base_classes` (`base_class_id`, `base_class_code`, `base_class_name`, `faculty_id`, `lecturer_id`, `start_year`, `end_year`) VALUES
(1, 'CNTT2022A', 'Công nghệ thông tin K22A', 1, 1, '2022', '2026'),
(2, 'CNTT2022B', 'Công nghệ thông tin K22B', 1, 2, '2022', '2026'),
(3, 'QTKD2022A', 'Quản trị kinh doanh K22A', 2, 4, '2022', '2026'),
(4, 'QTKD2022B', 'Quản trị kinh doanh K22B', 2, 5, '2022', '2026'),
(5, 'NN2022A', 'Ngôn ngữ Anh K22A', 3, 7, '2022', '2026'),
(6, 'NN2022B', 'Ngôn ngữ Anh K22B', 3, 8, '2022', '2026'),
(7, 'KT2022A', 'Kế toán K22A', 4, 10, '2022', '2026'),
(8, 'KT2022B', 'Kế toán K22B', 4, 11, '2022', '2026'),
(9, 'TCNH2022A', 'Tài chính - Ngân hàng K22A', 5, 13, '2022', '2026'),
(10, 'TCNH2022B', 'Tài chính - Ngân hàng K22B', 5, 14, '2022', '2026'),
(11, 'SP2022A', 'Sư phạm K22A', 6, 16, '2022', '2026'),
(12, 'SP2022B', 'Sư phạm K22B', 6, 17, '2022', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `class_id` int(11) NOT NULL,
  `class_code` varchar(20) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `max_students` int(11) DEFAULT 40,
  `status` enum('Active','Closed','Cancelled') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester_id`, `max_students`, `status`) VALUES
(1, 'CNTT101-01', 1, 1, 3, 40, 'Active'),
(2, 'CNTT201-01', 2, 2, 3, 40, 'Active'),
(3, 'CNTT301-01', 3, 3, 3, 40, 'Active'),
(4, 'QTKD101-01', 4, 4, 3, 40, 'Active'),
(5, 'QTKD201-01', 5, 5, 3, 40, 'Active'),
(6, 'QTKD301-01', 6, 6, 3, 40, 'Active'),
(7, 'ENGL101-01', 7, 7, 3, 40, 'Active'),
(8, 'ENGL201-01', 8, 8, 3, 40, 'Active'),
(9, 'ENGL301-01', 9, 9, 3, 40, 'Active'),
(10, 'KT101-01', 10, 10, 3, 40, 'Active'),
(11, 'KT201-01', 11, 11, 3, 40, 'Active'),
(12, 'KT301-01', 12, 12, 3, 40, 'Active'),
(13, 'QTKD301-02', 6, 1, 3, 40, 'Active'),
(14, 'CNTT101-10', 1, 7, 3, 50, 'Active'),
(21, 'CNTT101-11', 1, 15, 3, 50, ''),
(23, 'CNTT101-19', 1, 3, 3, 50, '');

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `schedule_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '2=Mon 3=Tue 4=Wed 5=Thu 6=Fri 7=Sat',
  `start_period` tinyint(4) NOT NULL,
  `end_period` tinyint(4) NOT NULL,
  `room` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`schedule_id`, `class_id`, `day_of_week`, `start_period`, `end_period`, `room`) VALUES
(1, 1, 2, 1, 3, 'A101'),
(2, 1, 4, 1, 3, 'A101'),
(3, 2, 2, 4, 6, 'A102'),
(4, 2, 5, 4, 6, 'A102'),
(5, 3, 3, 1, 3, 'A103'),
(6, 3, 6, 1, 3, 'A103'),
(7, 4, 2, 7, 9, 'B201'),
(8, 4, 4, 7, 9, 'B201'),
(9, 5, 3, 4, 6, 'B202'),
(10, 5, 5, 4, 6, 'B202'),
(11, 6, 4, 1, 3, 'B203'),
(12, 6, 6, 1, 3, 'B203');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_registrations`
--

CREATE TABLE `dormitory_registrations` (
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` enum('Pending','Active','Ended','Cancelled') DEFAULT 'Pending',
  `note` text DEFAULT NULL,
  `registered_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_registrations`
--

INSERT INTO `dormitory_registrations` (`registration_id`, `student_id`, `room_id`, `start_date`, `end_date`, `status`, `note`, `registered_at`, `updated_at`) VALUES
(1, 2, 1, '2026-03-06', '2026-04-04', 'Pending', NULL, '2026-02-28 22:37:29', '2026-02-28 22:37:29');

-- --------------------------------------------------------

--
-- Table structure for table `dormitory_rooms`
--

CREATE TABLE `dormitory_rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(20) NOT NULL,
  `building` varchar(50) NOT NULL DEFAULT 'Tòa A',
  `room_type` enum('Single','Double','Triple','Quad') NOT NULL DEFAULT 'Double',
  `price_per_month` decimal(10,0) NOT NULL DEFAULT 500000,
  `total_beds` int(11) NOT NULL DEFAULT 2,
  `available_beds` int(11) NOT NULL DEFAULT 2,
  `floor` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `dormitory_rooms`
--

INSERT INTO `dormitory_rooms` (`room_id`, `room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`, `is_active`) VALUES
(1, 'A101', 'Tòa A', 'Double', 400000, 2, 0, 1, 'Phòng đôi tầng 1, có điều hòa', 1),
(2, 'A102', 'Tòa A', 'Double', 400000, 2, 2, 1, 'Phòng đôi tầng 1, có điều hòa', 1),
(3, 'A201', 'Tòa A', 'Quad', 300000, 4, 2, 2, 'Phòng 4 người tầng 2', 1),
(4, 'B101', 'Tòa B', 'Double', 380000, 2, 2, 1, 'Phòng đôi tòa B tầng 1', 1),
(5, 'B201', 'Tòa B', 'Triple', 350000, 3, 3, 2, 'Phòng 3 người tòa B tầng 2', 1),
(6, 'C101', 'Tòa C', 'Quad', 280000, 4, 4, 1, 'Phòng 4 người tòa C giá rẻ', 1),
(7, 'A101', 'Tòa A', 'Double', 400000, 2, 1, 1, 'Phòng đôi tầng 1, có điều hòa', 1),
(8, 'A102', 'Tòa A', 'Double', 400000, 2, 2, 1, 'Phòng đôi tầng 1, có điều hòa', 1),
(9, 'A201', 'Tòa A', 'Quad', 300000, 4, 2, 2, 'Phòng 4 người tầng 2', 1),
(10, 'A202', 'Tòa A', 'Quad', 300000, 4, 4, 2, 'Phòng 4 người tầng 2', 1),
(11, 'A301', 'Tòa A', 'Single', 700000, 1, 0, 3, 'Phòng đơn tầng 3, tiện nghi cao', 1),
(12, 'A302', 'Tòa A', 'Single', 700000, 1, 1, 3, 'Phòng đơn tầng 3, tiện nghi cao', 1),
(13, 'B101', 'Tòa B', 'Double', 380000, 2, 2, 1, 'Phòng đôi tòa B tầng 1', 1),
(14, 'B102', 'Tòa B', 'Double', 380000, 2, 1, 1, 'Phòng đôi tòa B tầng 1', 1),
(15, 'B201', 'Tòa B', 'Triple', 350000, 3, 3, 2, 'Phòng 3 người tòa B tầng 2', 1),
(16, 'B202', 'Tòa B', 'Triple', 350000, 3, 0, 2, 'Phòng 3 người tòa B tầng 2', 1),
(17, 'C101', 'Tòa C', 'Quad', 280000, 4, 4, 1, 'Phòng 4 người tòa C giá rẻ', 1),
(18, 'C102', 'Tòa C', 'Quad', 280000, 4, 2, 1, 'Phòng 4 người tòa C giá rẻ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `enrollment_date` datetime DEFAULT current_timestamp(),
  `status` enum('Enrolled','Withdrawn','Completed') NOT NULL DEFAULT 'Enrolled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `enrollment_date`, `status`) VALUES
(1, 1, 1, '2026-02-28 21:58:01', 'Completed'),
(2, 1, 2, '2026-02-28 21:58:01', 'Completed'),
(3, 1, 3, '2026-02-28 21:58:01', 'Completed'),
(4, 2, 1, '2026-02-28 21:58:01', 'Withdrawn'),
(5, 2, 2, '2026-02-28 21:58:01', 'Withdrawn'),
(6, 3, 1, '2026-02-28 21:58:01', 'Enrolled'),
(7, 3, 7, '2026-02-28 21:58:01', 'Enrolled'),
(8, 4, 4, '2026-02-28 21:58:01', 'Enrolled'),
(9, 4, 5, '2026-02-28 21:58:01', 'Enrolled'),
(10, 5, 4, '2026-02-28 21:58:01', 'Completed'),
(11, 5, 6, '2026-02-28 21:58:01', 'Completed'),
(12, 8, 4, '2026-02-28 21:58:01', 'Enrolled'),
(13, 8, 5, '2026-02-28 21:58:01', 'Enrolled'),
(14, 9, 4, '2026-02-28 21:58:01', 'Enrolled'),
(15, 9, 6, '2026-02-28 21:58:01', 'Enrolled'),
(16, 10, 4, '2026-02-28 21:58:01', 'Enrolled'),
(18, 2, 10, '2026-03-04 02:24:22', 'Withdrawn'),
(19, 2, 12, '2026-03-04 02:24:34', 'Enrolled'),
(22, 2, 8, '2026-03-04 02:46:54', 'Withdrawn'),
(23, 2, 9, '2026-03-04 02:58:13', 'Enrolled');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment_registration_periods`
--

CREATE TABLE `enrollment_registration_periods` (
  `period_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL COMMENT 'Spring, Summer, Fall',
  `year` int(11) NOT NULL,
  `enrollment_open` datetime NOT NULL COMMENT 'When enrollment opens',
  `enrollment_close` datetime NOT NULL COMMENT 'When enrollment closes',
  `is_active` tinyint(1) DEFAULT 1,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollment_registration_periods`
--

INSERT INTO `enrollment_registration_periods` (`period_id`, `semester`, `year`, `enrollment_open`, `enrollment_close`, `is_active`, `note`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Spring', 2026, '2026-01-15 07:00:00', '2026-02-15 23:59:59', 0, NULL, 1, '2026-03-03 08:52:39', '2026-03-03 17:09:22'),
(2, 'Summer', 2026, '2026-03-03 23:57:00', '2026-03-05 23:57:00', 1, 'demo', 1, '2026-03-03 08:52:39', '2026-03-03 21:13:12'),
(3, 'Fall', 2026, '2026-08-15 07:00:00', '2026-09-15 23:59:59', 0, NULL, 1, '2026-03-03 08:52:39', '2026-03-03 17:09:54');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_code` varchar(20) NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`, `created_at`) VALUES
(1, 'CNTT', 'Công nghệ thông tin', NULL, '2026-02-28 21:58:01'),
(2, 'QTKD', 'Quản trị kinh doanh', NULL, '2026-02-28 21:58:01'),
(3, 'NN', 'Ngôn ngữ Anh', NULL, '2026-02-28 21:58:01'),
(4, 'KT', 'Kế toán', NULL, '2026-02-28 21:58:01'),
(5, 'TCNH', 'Tài chính - Ngân hàng', NULL, '2026-02-28 21:58:01'),
(6, 'SP', 'Sư phạm', NULL, '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `attendance_score` decimal(4,2) DEFAULT NULL,
  `midterm_score` decimal(5,2) DEFAULT NULL,
  `final_score` decimal(5,2) DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL COMMENT 'Điểm tổng kết',
  `grade_letter` varchar(5) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `attendance_score`, `midterm_score`, `final_score`, `score`, `grade_letter`, `updated_by`, `updated_at`) VALUES
(1, 1, NULL, 8.50, 8.00, 8.20, 'B+', NULL, '2026-02-28 21:58:01'),
(2, 2, NULL, 7.00, 7.50, 7.30, 'B', NULL, '2026-02-28 21:58:01'),
(3, 3, NULL, 9.00, 9.50, 9.30, 'A', NULL, '2026-02-28 21:58:01'),
(4, 4, NULL, 6.50, 7.00, 6.80, 'C+', NULL, '2026-02-28 21:58:01'),
(5, 5, NULL, 8.00, 8.50, 8.30, 'B+', NULL, '2026-02-28 21:58:01'),
(6, 8, NULL, 7.50, 8.00, 7.80, 'B+', NULL, '2026-02-28 21:58:01'),
(7, 10, NULL, 9.00, 9.00, 9.00, 'A', NULL, '2026-02-28 21:58:01'),
(8, 12, NULL, 6.00, 6.50, 6.30, 'C+', NULL, '2026-02-28 21:58:01'),
(9, 14, NULL, 8.00, 7.50, 7.70, 'B+', NULL, '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `degree`, `faculty_id`, `created_at`) VALUES
(1, 2, 'GV001', 'Minh', 'Nguyen', 'Male', NULL, 'gv01@university.edu', NULL, 'PhD', 1, '2026-02-28 21:58:01'),
(2, 3, 'GV002', 'Hoa', 'Tran', 'Male', NULL, 'gv02@university.edu', NULL, 'Master', 1, '2026-02-28 21:58:01'),
(3, 4, 'GV003', 'An', 'Le', 'Male', NULL, 'gv03@university.edu', NULL, 'Master', 1, '2026-02-28 21:58:01'),
(4, 5, 'GV004', 'Binh', 'Pham', 'Male', NULL, 'gv04@university.edu', NULL, 'PhD', 2, '2026-02-28 21:58:01'),
(5, 6, 'GV005', 'Chi', 'Vu', 'Male', NULL, 'gv05@university.edu', NULL, 'Master', 2, '2026-02-28 21:58:01'),
(6, 7, 'GV006', 'Dung', 'Do', 'Male', NULL, 'gv06@university.edu', NULL, 'Master', 2, '2026-02-28 21:58:01'),
(7, 8, 'GV007', 'Hieu', 'Hoang', 'Male', NULL, 'gv07@university.edu', NULL, 'PhD', 3, '2026-02-28 21:58:01'),
(8, 9, 'GV008', 'Khanh', 'Nguyen', 'Male', NULL, 'gv08@university.edu', NULL, 'Master', 3, '2026-02-28 21:58:01'),
(9, 10, 'GV009', 'Linh', 'Tran', 'Male', NULL, 'gv09@university.edu', NULL, 'Master', 3, '2026-02-28 21:58:01'),
(10, 11, 'GV010', 'Manh', 'Le', 'Male', NULL, 'gv10@university.edu', NULL, 'PhD', 4, '2026-02-28 21:58:01'),
(11, 12, 'GV011', 'Nga', 'Pham', 'Male', NULL, 'gv11@university.edu', NULL, 'Master', 4, '2026-02-28 21:58:01'),
(12, 13, 'GV012', 'Phong', 'Vu', 'Male', NULL, 'gv12@university.edu', NULL, 'Master', 4, '2026-02-28 21:58:01'),
(13, 14, 'GV013', 'Quang', 'Do', 'Male', NULL, 'gv13@university.edu', NULL, 'PhD', 5, '2026-02-28 21:58:01'),
(14, 15, 'GV014', 'Son', 'Hoang', 'Male', NULL, 'gv14@university.edu', NULL, 'Master', 5, '2026-02-28 21:58:01'),
(15, 16, 'GV015', 'Thao', 'Nguyen', 'Male', NULL, 'gv15@university.edu', NULL, 'Master', 5, '2026-02-28 21:58:01'),
(16, 17, 'GV016', 'Uyen', 'Tran', 'Male', NULL, 'gv16@university.edu', NULL, 'PhD', 6, '2026-02-28 21:58:01'),
(17, 18, 'GV017', 'Van', 'Le', 'Male', NULL, 'gv17@university.edu', NULL, 'Master', 6, '2026-02-28 21:58:01'),
(18, 19, 'GV018', 'Xuan', 'Pham', 'Male', NULL, 'gv18@university.edu', NULL, 'Professor', 6, '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `library_books`
--

CREATE TABLE `library_books` (
  `book_id` int(11) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `title` varchar(300) NOT NULL,
  `author` varchar(200) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `total_copies` int(11) DEFAULT 1,
  `available_copies` int(11) DEFAULT 1,
  `published_year` year(4) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `library_books`
--

INSERT INTO `library_books` (`book_id`, `isbn`, `title`, `author`, `category`, `total_copies`, `available_copies`, `published_year`, `is_active`) VALUES
(1, NULL, 'Lập trình PHP căn bản', 'Nguyễn Văn A', 'Công nghệ thông tin', 5, 2, '2020', 1),
(2, NULL, 'Cơ sở dữ liệu MySQL', 'Trần Thị B', 'Công nghệ thông tin', 3, 0, '2019', 1),
(3, NULL, 'Quản trị kinh doanh hiện đại', 'Lê Văn C', 'Kinh tế', 4, 3, '2021', 1),
(4, NULL, 'Tiếng Anh thương mại', 'Phạm Thị D', 'Ngoại ngữ', 6, 5, '2022', 1),
(5, NULL, 'Kế toán tài chính doanh nghiệp', 'Vũ Văn E', 'Kế toán', 4, 3, '2020', 1);

-- --------------------------------------------------------

--
-- Table structure for table `library_borrows`
--

CREATE TABLE `library_borrows` (
  `borrow_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `borrow_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Borrowed','Returned','Overdue') DEFAULT 'Borrowed',
  `created_at` datetime DEFAULT current_timestamp(),
  `fine_amount` decimal(10,2) DEFAULT 0.00,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `library_borrows`
--

INSERT INTO `library_borrows` (`borrow_id`, `student_id`, `book_id`, `borrow_date`, `due_date`, `return_date`, `status`, `created_at`, `fine_amount`, `note`) VALUES
(1, 2, 2, '0000-00-00', '2026-03-18', NULL, 'Borrowed', '2026-03-04 06:00:12', 0.00, NULL),
(2, 2, 1, '0000-00-00', '2026-03-18', NULL, 'Borrowed', '2026-03-04 06:00:15', 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL COMMENT 'Thuộc nhóm quyền nào',
  `code` varchar(100) NOT NULL COMMENT 'Mã quyền: students.view, students.create...',
  `name` varchar(200) NOT NULL COMMENT 'Tên hiển thị: Xem danh sách sinh viên',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết quyền này làm gì',
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1 = quyền hệ thống, không thể xóa',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `group_id`, `code`, `name`, `description`, `is_system`, `created_at`) VALUES
(1, 1, 'users.view', 'Xem danh sách tài khoản', 'Xem được danh sách tất cả tài khoản người dùng', 1, '2026-02-28 21:58:01'),
(2, 1, 'users.create', 'Tạo tài khoản mới', 'Tạo tài khoản người dùng mới', 1, '2026-02-28 21:58:01'),
(3, 1, 'users.edit', 'Sửa thông tin tài khoản', 'Chỉnh sửa thông tin tài khoản người dùng', 1, '2026-02-28 21:58:01'),
(4, 1, 'users.delete', 'Xóa tài khoản', 'Xóa tài khoản người dùng khỏi hệ thống', 1, '2026-02-28 21:58:01'),
(5, 1, 'users.toggle_status', 'Kích hoạt/Khóa tài khoản', 'Bật/tắt trạng thái hoạt động của tài khoản', 1, '2026-02-28 21:58:01'),
(6, 1, 'users.reset_password', 'Đặt lại mật khẩu', 'Reset mật khẩu cho người dùng bất kỳ', 1, '2026-02-28 21:58:01'),
(7, 1, 'users.unlock', 'Mở khóa tài khoản bị khoá', 'Mở khóa tài khoản bị chặn do nhập sai mật khẩu', 1, '2026-02-28 21:58:01'),
(8, 1, 'users.assign_role', 'Gán/Đổi vai trò cho tài khoản', 'Thay đổi vai trò (role) của người dùng', 1, '2026-02-28 21:58:01'),
(9, 2, 'roles.view', 'Xem danh sách vai trò', 'Xem được danh sách các vai trò hiện có', 1, '2026-02-28 21:58:01'),
(10, 2, 'roles.create', 'Tạo vai trò mới', 'Thêm vai trò mới vào hệ thống', 1, '2026-02-28 21:58:01'),
(11, 2, 'roles.edit', 'Sửa thông tin vai trò', 'Chỉnh sửa tên/mô tả vai trò', 1, '2026-02-28 21:58:01'),
(12, 2, 'roles.delete', 'Xóa vai trò', 'Xóa vai trò không còn dùng', 1, '2026-02-28 21:58:01'),
(13, 2, 'roles.assign_perm', 'Gán quyền cho vai trò', 'Cấp hoặc thu hồi quyền của một vai trò', 1, '2026-02-28 21:58:01'),
(14, 2, 'permissions.view', 'Xem danh sách quyền hạn', 'Xem toàn bộ danh sách quyền trong hệ thống', 1, '2026-02-28 21:58:01'),
(15, 2, 'permissions.create', 'Tạo quyền hạn mới', 'Thêm quyền hạn mới vào hệ thống', 1, '2026-02-28 21:58:01'),
(16, 2, 'permissions.edit', 'Sửa thông tin quyền hạn', 'Chỉnh sửa mô tả quyền hạn', 1, '2026-02-28 21:58:01'),
(17, 2, 'permissions.delete', 'Xóa quyền hạn', 'Xóa quyền hạn khỏi hệ thống', 1, '2026-02-28 21:58:01'),
(18, 3, 'students.view', 'Xem danh sách sinh viên', 'Xem được danh sách sinh viên', 0, '2026-02-28 21:58:01'),
(19, 3, 'students.view_detail', 'Xem chi tiết hồ sơ sinh viên', 'Xem thông tin chi tiết từng sinh viên', 0, '2026-02-28 21:58:01'),
(20, 3, 'students.create', 'Thêm sinh viên mới', 'Nhập thông tin sinh viên mới vào hệ thống', 0, '2026-02-28 21:58:01'),
(21, 3, 'students.edit', 'Sửa thông tin sinh viên', 'Cập nhật thông tin hồ sơ sinh viên', 0, '2026-02-28 21:58:01'),
(22, 3, 'students.delete', 'Xóa sinh viên', 'Xóa hồ sơ sinh viên khỏi hệ thống', 0, '2026-02-28 21:58:01'),
(23, 3, 'students.change_status', 'Đổi trạng thái học tập', 'Thay đổi trạng thái: Đang học, Bảo lưu, Thôi học, Tốt nghiệp', 0, '2026-02-28 21:58:01'),
(24, 3, 'students.create_account', 'Tạo tài khoản cho sinh viên', 'Khởi tạo tài khoản đăng nhập cho sinh viên', 0, '2026-02-28 21:58:01'),
(25, 3, 'students.reset_password', 'Reset mật khẩu sinh viên', 'Đặt lại mật khẩu tài khoản sinh viên', 0, '2026-02-28 21:58:01'),
(26, 3, 'students.export', 'Xuất danh sách sinh viên', 'Xuất file Excel/PDF danh sách sinh viên', 0, '2026-02-28 21:58:01'),
(27, 3, 'students.import', 'Nhập danh sách sinh viên', 'Import sinh viên từ file Excel', 0, '2026-02-28 21:58:01'),
(28, 3, 'students.view_transcript', 'Xem bảng điểm sinh viên', 'Xem bảng điểm học tập của sinh viên', 0, '2026-02-28 21:58:01'),
(29, 4, 'lecturers.view', 'Xem danh sách giảng viên', 'Xem danh sách tất cả giảng viên', 0, '2026-02-28 21:58:01'),
(30, 4, 'lecturers.view_detail', 'Xem chi tiết hồ sơ giảng viên', 'Xem thông tin chi tiết giảng viên', 0, '2026-02-28 21:58:01'),
(31, 4, 'lecturers.create', 'Thêm giảng viên mới', 'Thêm hồ sơ giảng viên mới', 0, '2026-02-28 21:58:01'),
(32, 4, 'lecturers.edit', 'Sửa thông tin giảng viên', 'Cập nhật thông tin hồ sơ giảng viên', 0, '2026-02-28 21:58:01'),
(33, 4, 'lecturers.delete', 'Xóa giảng viên', 'Xóa hồ sơ giảng viên', 0, '2026-02-28 21:58:01'),
(34, 4, 'lecturers.assign_class', 'Phân công lớp học', 'Phân công giảng viên cho lớp học phần', 0, '2026-02-28 21:58:01'),
(35, 4, 'lecturers.view_schedule', 'Xem lịch giảng dạy', 'Xem lịch dạy của giảng viên', 0, '2026-02-28 21:58:01'),
(36, 5, 'faculties.view', 'Xem danh sách khoa', 'Xem danh sách các khoa/ngành', 0, '2026-02-28 21:58:01'),
(37, 5, 'faculties.create', 'Thêm khoa mới', 'Tạo mới khoa/ngành đào tạo', 0, '2026-02-28 21:58:01'),
(38, 5, 'faculties.edit', 'Sửa thông tin khoa', 'Cập nhật thông tin khoa/ngành', 0, '2026-02-28 21:58:01'),
(39, 5, 'faculties.delete', 'Xóa khoa', 'Xóa khoa/ngành khỏi hệ thống', 0, '2026-02-28 21:58:01'),
(40, 5, 'faculties.view_stats', 'Xem thống kê theo khoa', 'Xem báo cáo thống kê theo từng khoa', 0, '2026-02-28 21:58:01'),
(41, 6, 'subjects.view', 'Xem danh sách môn học', 'Xem danh sách tất cả môn học', 0, '2026-02-28 21:58:01'),
(42, 6, 'subjects.create', 'Thêm môn học mới', 'Thêm môn học mới vào chương trình', 0, '2026-02-28 21:58:01'),
(43, 6, 'subjects.edit', 'Sửa thông tin môn học', 'Cập nhật thông tin môn học', 0, '2026-02-28 21:58:01'),
(44, 6, 'subjects.delete', 'Xóa môn học', 'Xóa môn học khỏi hệ thống', 0, '2026-02-28 21:58:01'),
(45, 6, 'classes.view', 'Xem danh sách lớp học phần', 'Xem danh sách các lớp học phần', 0, '2026-02-28 21:58:01'),
(46, 6, 'classes.create', 'Mở lớp học phần mới', 'Tạo lớp học phần mới trong học kỳ', 0, '2026-02-28 21:58:01'),
(47, 6, 'classes.edit', 'Sửa thông tin lớp học phần', 'Cập nhật thông tin lớp học phần', 0, '2026-02-28 21:58:01'),
(48, 6, 'classes.delete', 'Xóa lớp học phần', 'Xóa lớp học phần', 0, '2026-02-28 21:58:01'),
(49, 6, 'classes.view_schedule', 'Xem thời khóa biểu lớp', 'Xem lịch học của lớp học phần', 0, '2026-02-28 21:58:01'),
(50, 6, 'base_classes.view', 'Xem lớp cơ sở', 'Xem danh sách lớp hành chính', 0, '2026-02-28 21:58:01'),
(51, 6, 'base_classes.manage', 'Quản lý lớp cơ sở', 'Tạo, sửa, xóa lớp hành chính', 0, '2026-02-28 21:58:01'),
(52, 6, 'semesters.view', 'Xem học kỳ', 'Xem danh sách học kỳ', 0, '2026-02-28 21:58:01'),
(53, 6, 'semesters.manage', 'Quản lý học kỳ', 'Tạo, cập nhật học kỳ', 0, '2026-02-28 21:58:01'),
(54, 7, 'grades.view', 'Xem điểm', 'Xem điểm số của sinh viên', 0, '2026-02-28 21:58:01'),
(55, 7, 'grades.enter', 'Nhập điểm', 'Nhập/cập nhật điểm số cho sinh viên', 0, '2026-02-28 21:58:01'),
(56, 7, 'grades.edit', 'Sửa điểm đã nhập', 'Chỉnh sửa điểm đã nhập (có log)', 0, '2026-02-28 21:58:01'),
(57, 7, 'grades.approve', 'Duyệt/Khóa điểm', 'Phê duyệt và khóa điểm không cho sửa', 0, '2026-02-28 21:58:01'),
(58, 7, 'grades.view_all', 'Xem điểm tất cả các lớp', 'Xem điểm của toàn trường, không giới hạn', 0, '2026-02-28 21:58:01'),
(59, 7, 'grades.export', 'Xuất bảng điểm', 'Xuất bảng điểm ra file Excel/PDF', 0, '2026-02-28 21:58:01'),
(60, 7, 'attendance.view', 'Xem điểm danh', 'Xem kết quả điểm danh', 0, '2026-02-28 21:58:01'),
(61, 7, 'attendance.enter', 'Nhập điểm danh', 'Điểm danh sinh viên', 0, '2026-02-28 21:58:01'),
(62, 8, 'enrollment.view', 'Xem đăng ký học phần', 'Xem danh sách đăng ký học phần', 0, '2026-02-28 21:58:01'),
(63, 8, 'enrollment.register', 'Đăng ký học phần', 'Sinh viên tự đăng ký học phần', 0, '2026-02-28 21:58:01'),
(64, 8, 'enrollment.cancel', 'Hủy đăng ký học phần', 'Hủy đăng ký học phần đã đăng ký', 0, '2026-02-28 21:58:01'),
(65, 8, 'enrollment.manage', 'Quản lý đăng ký học phần', 'Admin/Giáo vụ quản lý đăng ký của sinh viên', 0, '2026-02-28 21:58:01'),
(66, 8, 'enrollment.approve', 'Duyệt đăng ký học phần', 'Phê duyệt yêu cầu đăng ký học phần', 0, '2026-02-28 21:58:01'),
(67, 9, 'tuition.view', 'Xem học phí của mình', 'Sinh viên xem học phí của bản thân', 0, '2026-02-28 21:58:01'),
(68, 9, 'tuition.view_all', 'Xem học phí tất cả sinh viên', 'Admin/Kế toán xem học phí toàn trường', 0, '2026-02-28 21:58:01'),
(69, 9, 'tuition.manage', 'Quản lý học phí', 'Tạo, cập nhật thông tin học phí', 0, '2026-02-28 21:58:01'),
(70, 9, 'tuition.record_payment', 'Ghi nhận thanh toán', 'Ghi nhận SV đã nộp học phí', 0, '2026-02-28 21:58:01'),
(71, 10, 'dormitory.view', 'Xem thông tin ký túc xá', 'Xem thông tin phòng ký túc xá', 0, '2026-02-28 21:58:01'),
(72, 10, 'dormitory.register', 'Đăng ký ký túc xá', 'Sinh viên đăng ký phòng ký túc xá', 0, '2026-02-28 21:58:01'),
(73, 10, 'dormitory.manage', 'Quản lý ký túc xá', 'Admin quản lý phòng và đăng ký KTX', 0, '2026-02-28 21:58:01'),
(74, 10, 'dormitory.approve', 'Duyệt đăng ký ký túc xá', 'Phê duyệt yêu cầu đăng ký KTX', 0, '2026-02-28 21:58:01'),
(75, 11, 'library.view', 'Xem thư viện', 'Tìm kiếm và xem sách trong thư viện', 0, '2026-02-28 21:58:01'),
(76, 11, 'library.borrow', 'Mượn sách', 'Đăng ký mượn sách thư viện', 0, '2026-02-28 21:58:01'),
(77, 11, 'library.manage', 'Quản lý thư viện', 'Quản lý sách, mượn trả trong thư viện', 0, '2026-02-28 21:58:01'),
(78, 12, 'scholarship.view', 'Xem học bổng', 'Xem danh sách học bổng đang mở', 0, '2026-02-28 21:58:01'),
(79, 12, 'scholarship.apply', 'Nộp đơn học bổng', 'Sinh viên nộp đơn xin học bổng', 0, '2026-02-28 21:58:01'),
(80, 12, 'scholarship.manage', 'Quản lý học bổng', 'Tạo, sửa, xóa thông tin học bổng', 0, '2026-02-28 21:58:01'),
(81, 12, 'scholarship.approve', 'Xét duyệt học bổng', 'Phê duyệt/từ chối đơn học bổng', 0, '2026-02-28 21:58:01'),
(82, 13, 'reports.view', 'Xem báo cáo tổng quan', 'Xem báo cáo thống kê tổng quan hệ thống', 0, '2026-02-28 21:58:01'),
(83, 13, 'reports.student', 'Xem báo cáo sinh viên', 'Báo cáo thống kê sinh viên', 0, '2026-02-28 21:58:01'),
(84, 13, 'reports.grade', 'Xem báo cáo điểm', 'Báo cáo phân phối điểm, kết quả học tập', 0, '2026-02-28 21:58:01'),
(85, 13, 'reports.finance', 'Xem báo cáo tài chính', 'Báo cáo thu học phí, nợ học phí', 0, '2026-02-28 21:58:01'),
(86, 13, 'reports.export', 'Xuất báo cáo', 'Xuất báo cáo ra file Excel/PDF', 0, '2026-02-28 21:58:01'),
(87, 14, 'system.audit_logs', 'Xem nhật ký hệ thống', 'Xem lịch sử mọi thao tác trong hệ thống', 1, '2026-02-28 21:58:01'),
(88, 14, 'system.backup', 'Sao lưu & khôi phục CSDL', 'Tạo bản sao lưu và khôi phục dữ liệu', 1, '2026-02-28 21:58:01'),
(89, 14, 'system.settings', 'Cài đặt hệ thống', 'Thay đổi cấu hình hệ thống', 1, '2026-02-28 21:58:01'),
(90, 14, 'system.dashboard', 'Xem Dashboard quản trị', 'Xem trang tổng quan quản trị', 1, '2026-02-28 21:58:01'),
(91, 7, 'classes.register', 'Đăng ký dạy lớp', 'Cho phép giảng viên đăng ký dạy lớp học phần', 0, '2026-03-03 19:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `permission_groups`
--

CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL,
  `code` varchar(60) NOT NULL COMMENT 'Mã nhóm: users_management, training_management...',
  `name` varchar(150) NOT NULL COMMENT 'Tên hiển thị: Quản lý Người dùng',
  `icon` varchar(60) DEFAULT 'bi-shield-lock' COMMENT 'Bootstrap icon class',
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_groups`
--

INSERT INTO `permission_groups` (`id`, `code`, `name`, `icon`, `sort_order`, `created_at`) VALUES
(1, 'user_management', 'Quản lý Người dùng', 'bi-people-fill', 1, '2026-02-28 21:58:01'),
(2, 'role_management', 'Quản lý Vai trò & Quyền', 'bi-shield-lock-fill', 2, '2026-02-28 21:58:01'),
(3, 'student_management', 'Quản lý Sinh viên', 'bi-mortarboard-fill', 3, '2026-02-28 21:58:01'),
(4, 'lecturer_management', 'Quản lý Giảng viên', 'bi-person-badge-fill', 4, '2026-02-28 21:58:01'),
(5, 'faculty_management', 'Quản lý Khoa / Ngành', 'bi-building-fill', 5, '2026-02-28 21:58:01'),
(6, 'training_management', 'Quản lý Đào tạo', 'bi-journal-bookmark-fill', 6, '2026-02-28 21:58:01'),
(7, 'grade_management', 'Quản lý Điểm số', 'bi-graph-up-arrow', 7, '2026-02-28 21:58:01'),
(8, 'enrollment_mgmt', 'Quản lý Đăng ký môn học', 'bi-clipboard-check-fill', 8, '2026-02-28 21:58:01'),
(9, 'finance_management', 'Quản lý Tài chính', 'bi-cash-stack', 9, '2026-02-28 21:58:01'),
(10, 'dormitory_mgmt', 'Quản lý Ký túc xá', 'bi-house-fill', 10, '2026-02-28 21:58:01'),
(11, 'library_management', 'Quản lý Thư viện', 'bi-book-fill', 11, '2026-02-28 21:58:01'),
(12, 'scholarship_mgmt', 'Quản lý Học bổng', 'bi-award-fill', 12, '2026-02-28 21:58:01'),
(13, 'report_analytics', 'Báo cáo & Thống kê', 'bi-bar-chart-fill', 13, '2026-02-28 21:58:01'),
(14, 'system_admin', 'Quản trị Hệ thống', 'bi-gear-fill', 14, '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(10) DEFAULT '#6c757d' COMMENT 'Màu badge hiển thị',
  `is_system` tinyint(1) DEFAULT 0 COMMENT '1 = role hệ thống, không thể xóa',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`, `description`, `color`, `is_system`, `created_at`) VALUES
(1, 'super_admin', 'Super Admin', 'Quản trị viên tối cao - toàn quyền hệ thống', '#dc3545', 1, '2026-02-28 21:58:01'),
(2, 'academic_admin', 'Quản lý Đào tạo', 'Quản lý chuyên trách về đào tạo, chương trình học', '#0d6efd', 0, '2026-02-28 21:58:01'),
(3, 'student_admin', 'Quản lý Sinh viên', 'Phòng công tác sinh viên', '#198754', 0, '2026-02-28 21:58:01'),
(4, 'teacher', 'Giảng viên', 'Giảng viên giảng dạy', '#0dcaf0', 0, '2026-02-28 21:58:01'),
(5, 'student', 'Sinh viên', 'Sinh viên theo học', '#6f42c1', 0, '2026-02-28 21:58:01'),
(6, 'accountant', 'Kế toán', 'Bộ phận kế toán, tài chính', '#fd7e14', 0, '2026-02-28 21:58:01'),
(7, 'librarian', 'Thủ thư', 'Quản lý thư viện', '#20c997', 0, '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted_at` datetime DEFAULT current_timestamp(),
  `granted_by` int(11) DEFAULT NULL COMMENT 'Admin nào đã gán quyền này'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`, `granted_at`, `granted_by`) VALUES
(2, 1, '2026-03-04 00:15:16', 1),
(2, 2, '2026-03-04 00:15:16', 1),
(2, 3, '2026-03-04 00:15:16', 1),
(2, 18, '2026-03-04 00:15:16', 1),
(2, 19, '2026-03-04 00:15:16', 1),
(2, 20, '2026-03-04 00:15:16', 1),
(2, 21, '2026-03-04 00:15:16', 1),
(2, 23, '2026-03-04 00:15:16', 1),
(2, 26, '2026-03-04 00:15:16', 1),
(2, 29, '2026-03-04 00:15:16', 1),
(2, 30, '2026-03-04 00:15:16', 1),
(2, 31, '2026-03-04 00:15:16', 1),
(2, 32, '2026-03-04 00:15:16', 1),
(2, 34, '2026-03-04 00:15:16', 1),
(2, 35, '2026-03-04 00:15:16', 1),
(2, 36, '2026-03-04 00:15:16', 1),
(2, 37, '2026-03-04 00:15:16', 1),
(2, 38, '2026-03-04 00:15:16', 1),
(2, 40, '2026-03-04 00:15:16', 1),
(2, 41, '2026-03-04 00:15:16', 1),
(2, 42, '2026-03-04 00:15:16', 1),
(2, 43, '2026-03-04 00:15:16', 1),
(2, 44, '2026-03-04 00:15:16', 1),
(2, 45, '2026-03-04 00:15:16', 1),
(2, 46, '2026-03-04 00:15:16', 1),
(2, 47, '2026-03-04 00:15:16', 1),
(2, 48, '2026-03-04 00:15:16', 1),
(2, 49, '2026-03-04 00:15:16', 1),
(2, 50, '2026-03-04 00:15:16', 1),
(2, 51, '2026-03-04 00:15:16', 1),
(2, 52, '2026-03-04 00:15:16', 1),
(2, 53, '2026-03-04 00:15:16', 1),
(2, 54, '2026-03-04 00:15:16', 1),
(2, 55, '2026-03-04 00:15:16', 1),
(2, 56, '2026-03-04 00:15:16', 1),
(2, 57, '2026-03-04 00:15:16', 1),
(2, 58, '2026-03-04 00:15:16', 1),
(2, 59, '2026-03-04 00:15:16', 1),
(2, 62, '2026-03-04 00:15:16', 1),
(2, 65, '2026-03-04 00:15:16', 1),
(2, 66, '2026-03-04 00:15:16', 1),
(2, 82, '2026-03-04 00:15:16', 1),
(2, 83, '2026-03-04 00:15:16', 1),
(2, 84, '2026-03-04 00:15:16', 1),
(2, 86, '2026-03-04 00:15:16', 1),
(3, 18, '2026-03-03 15:39:30', 1),
(3, 19, '2026-03-03 15:39:30', 1),
(3, 20, '2026-03-03 15:39:30', 1),
(3, 21, '2026-03-03 15:39:30', 1),
(3, 22, '2026-03-03 15:39:30', 1),
(3, 23, '2026-03-03 15:39:30', 1),
(3, 24, '2026-03-03 15:39:30', 1),
(3, 25, '2026-03-03 15:39:30', 1),
(3, 26, '2026-03-03 15:39:30', 1),
(3, 28, '2026-03-03 15:39:30', 1),
(3, 62, '2026-03-03 19:08:20', 1),
(3, 63, '2026-03-03 15:52:39', 1),
(3, 64, '2026-03-03 15:52:39', 1),
(3, 65, '2026-03-03 15:39:30', 1),
(3, 71, '2026-03-03 15:39:30', 1),
(3, 73, '2026-03-03 15:39:30', 1),
(3, 74, '2026-03-03 15:39:30', 1),
(3, 78, '2026-03-03 15:39:30', 1),
(3, 80, '2026-03-03 15:39:30', 1),
(3, 81, '2026-03-03 15:39:30', 1),
(3, 82, '2026-03-03 15:39:30', 1),
(3, 83, '2026-03-03 15:39:30', 1),
(3, 86, '2026-03-03 15:39:30', 1),
(4, 18, '2026-03-03 14:58:45', 1),
(4, 19, '2026-03-03 14:58:45', 1),
(4, 45, '2026-03-03 14:58:45', 1),
(4, 49, '2026-03-03 14:58:45', 1),
(4, 54, '2026-03-03 14:58:45', 1),
(4, 55, '2026-03-03 14:58:45', 1),
(4, 56, '2026-03-03 14:58:45', 1),
(4, 59, '2026-03-03 14:58:45', 1),
(4, 60, '2026-03-03 14:58:45', 1),
(4, 61, '2026-03-03 14:58:45', 1),
(4, 62, '2026-03-03 14:58:45', 1),
(4, 82, '2026-03-03 14:58:45', 1),
(4, 84, '2026-03-03 14:58:45', 1),
(4, 86, '2026-03-03 14:58:45', 1),
(4, 91, '2026-03-03 19:59:45', 1),
(5, 3, '2026-03-04 06:01:58', 1),
(5, 19, '2026-03-04 06:01:58', 1),
(5, 28, '2026-03-04 06:01:58', 1),
(5, 49, '2026-03-04 06:01:58', 1),
(5, 54, '2026-03-04 06:01:58', 1),
(5, 62, '2026-03-04 06:01:58', 1),
(5, 63, '2026-03-04 06:01:58', 1),
(5, 64, '2026-03-04 06:01:58', 1),
(5, 67, '2026-03-04 06:01:58', 1),
(5, 69, '2026-03-04 06:01:58', 1),
(5, 71, '2026-03-04 06:01:58', 1),
(5, 72, '2026-03-04 06:01:58', 1),
(5, 75, '2026-03-04 06:01:58', 1),
(5, 76, '2026-03-04 06:01:58', 1),
(5, 78, '2026-03-04 06:01:58', 1),
(5, 79, '2026-03-04 06:01:58', 1),
(5, 83, '2026-03-04 06:01:58', 1),
(5, 84, '2026-03-04 06:01:58', 1),
(5, 85, '2026-03-04 06:01:58', 1),
(5, 86, '2026-03-04 06:01:58', 1),
(6, 68, '2026-03-02 20:42:42', 1),
(6, 69, '2026-03-02 20:42:42', 1),
(6, 82, '2026-03-02 20:42:42', 1),
(7, 75, '2026-02-28 21:58:01', NULL),
(7, 76, '2026-02-28 21:58:01', NULL),
(7, 77, '2026-02-28 21:58:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `scholarships`
--

CREATE TABLE `scholarships` (
  `scholarship_id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scholarships`
--

INSERT INTO `scholarships` (`scholarship_id`, `name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`, `created_at`) VALUES
(1, 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 8.5 trở lên', 5000000, 8.50, NULL, 'Spring', '2026', 20, '2026-03-15', 1, '2026-02-28 21:58:01'),
(2, 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 7.5 đến dưới 8.5', 2000000, 7.50, 8.49, 'Spring', '2026', 50, '2026-03-15', 1, '2026-02-28 21:58:01'),
(3, 'Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', 1500000, NULL, NULL, 'Spring', '2026', 30, '2026-03-20', 1, '2026-02-28 21:58:01'),
(4, 'Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', 10000000, 8.00, NULL, 'Spring', '2026', 5, '2026-03-10', 1, '2026-02-28 21:58:01'),
(5, 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Spring', '2026', 20, '2026-03-15', 1, '2026-03-03 20:01:42'),
(6, 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Spring', '2026', 50, '2026-03-15', 1, '2026-03-03 20:01:42'),
(7, 'Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', 1500000, NULL, NULL, 'Spring', '2026', 30, '2026-03-20', 1, '2026-03-03 20:01:42'),
(8, 'Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', 10000000, 85.00, NULL, 'Spring', '2026', 5, '2026-03-10', 1, '2026-03-03 20:01:42'),
(9, 'Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Fall', '2025', 20, '2025-09-15', 0, '2026-03-03 20:01:42'),
(10, 'Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Fall', '2025', 50, '2025-09-15', 0, '2026-03-03 20:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `scholarship_applications`
--

CREATE TABLE `scholarship_applications` (
  `application_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `scholarship_id` int(11) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scholarship_applications`
--

INSERT INTO `scholarship_applications` (`application_id`, `student_id`, `scholarship_id`, `status`, `applied_at`, `reviewed_at`, `note`) VALUES
(1, 2, 3, 'Pending', '2026-02-28 22:41:07', NULL, NULL),
(2, 2, 7, 'Pending', '2026-03-04 05:59:16', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

CREATE TABLE `semesters` (
  `semester_id` int(11) NOT NULL,
  `semester_code` varchar(20) NOT NULL,
  `semester_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `is_current` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`semester_id`, `semester_code`, `semester_name`, `start_date`, `end_date`, `is_active`, `created_at`, `is_current`) VALUES
(1, 'HK1-2025', 'Học kỳ 1 năm 2025', '2025-01-06', '2025-05-30', 0, '2026-02-28 21:58:01', 0),
(2, 'HK2-2025', 'Học kỳ 2 năm 2025', '2025-06-02', '2025-09-30', 0, '2026-02-28 21:58:01', 1),
(3, 'HK1-2026', 'Học kỳ 1 năm 2026', '2026-01-05', '2026-05-30', 1, '2026-02-28 21:58:01', 0);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES
(1, 20, 'SV000', 'Nam', 'Tran', 'Male', '2004-03-15', 'student00@university.edu', '0123456789', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(2, 21, 'SV001', 'An', 'Nguyen', 'Male', '2004-01-15', 'student01@university.edu', '0901000001', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(3, 22, 'SV002', 'Binh', 'Tran', 'Female', '2004-02-20', 'student02@university.edu', '0901000002', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(4, 23, 'SV003', 'Chi', 'Le', 'Male', '2004-03-10', 'student03@university.edu', '0901000003', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(5, 24, 'SV004', 'Dung', 'Pham', 'Female', '2004-04-05', 'student04@university.edu', '0901000004', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(6, 25, 'SV005', 'Hoa', 'Vu', 'Male', '2004-05-25', 'student05@university.edu', '0901000005', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(7, 26, 'SV006', 'Khanh', 'Do', 'Female', '2004-06-12', 'student06@university.edu', '0901000006', 1, 1, 'Studying', '2026-02-28 21:58:01'),
(8, 27, 'SV007', 'Lan', 'Hoang', 'Male', '2004-07-08', 'student07@university.edu', '0901000007', 1, 2, 'Studying', '2026-02-28 21:58:01'),
(9, 28, 'SV008', 'Minh', 'Nguyen', 'Female', '2004-08-18', 'student08@university.edu', '0901000008', 2, 3, 'Studying', '2026-02-28 21:58:01'),
(10, 29, 'SV009', 'Nam', 'Tran', 'Male', '2004-09-22', 'student09@university.edu', '0901000009', 2, 3, 'Studying', '2026-02-28 21:58:01'),
(11, 30, 'SV010', 'Oanh', 'Le', 'Female', '2004-10-30', 'student10@university.edu', '0901000010', 2, 4, 'Studying', '2026-02-28 21:58:01'),
(12, NULL, 'SV011', 'Huy', 'Pham', 'Male', '2004-11-11', 'student11@university.edu', '0901000011', 2, 3, 'Studying', '2026-03-04 05:24:04'),
(13, NULL, 'SV012', 'Linh', 'Tran', 'Female', '2004-12-01', 'student12@university.edu', '0901000012', 2, 3, 'Studying', '2026-03-04 05:24:04'),
(14, NULL, 'SV013', 'Mai', 'Nguyen', 'Female', '2004-02-02', 'student13@university.edu', '0901000013', 3, 5, 'Studying', '2026-03-04 05:24:04'),
(15, NULL, 'SV014', 'Tuan', 'Le', 'Male', '2004-03-03', 'student14@university.edu', '0901000014', 3, 5, 'Studying', '2026-03-04 05:24:04'),
(16, NULL, 'SV015', 'Trang', 'Hoang', 'Female', '2004-04-04', 'student15@university.edu', '0901000015', 3, 5, 'Studying', '2026-03-04 05:24:04'),
(17, NULL, 'SV016', 'Quang', 'Vo', 'Male', '2004-05-05', 'student16@university.edu', '0901000016', 4, 6, 'Studying', '2026-03-04 05:24:04'),
(18, NULL, 'SV017', 'Nhung', 'Do', 'Female', '2004-06-06', 'student17@university.edu', '0901000017', 4, 6, 'Studying', '2026-03-04 05:24:04'),
(19, NULL, 'SV033', 'Bao', 'Nguyen', 'Male', '2004-01-05', 'student33@university.edu', '0901000033', 5, 7, 'Studying', '2026-03-04 05:25:01'),
(20, NULL, 'SV034', 'Cam', 'Tran', 'Female', '2004-02-06', 'student34@university.edu', '0901000034', 5, 7, 'Studying', '2026-03-04 05:25:01'),
(21, NULL, 'SV035', 'Duc', 'Pham', 'Male', '2004-03-07', 'student35@university.edu', '0901000035', 5, 7, 'Studying', '2026-03-04 05:25:01'),
(22, NULL, 'SV036', 'Hanh', 'Le', 'Female', '2004-04-08', 'student36@university.edu', '0901000036', 5, 7, 'Studying', '2026-03-04 05:25:01'),
(23, NULL, 'SV037', 'Kiet', 'Hoang', 'Male', '2004-05-09', 'student37@university.edu', '0901000037', 5, 7, 'Studying', '2026-03-04 05:25:01'),
(24, NULL, 'SV038', 'Luan', 'Vo', 'Male', '2004-06-10', 'student38@university.edu', '0901000038', 6, 8, 'Studying', '2026-03-04 05:25:01'),
(25, NULL, 'SV039', 'My', 'Do', 'Female', '2004-07-11', 'student39@university.edu', '0901000039', 6, 8, 'Studying', '2026-03-04 05:25:01'),
(26, NULL, 'SV040', 'Nam', 'Bui', 'Male', '2004-08-12', 'student40@university.edu', '0901000040', 6, 8, 'Studying', '2026-03-04 05:25:01'),
(27, NULL, 'SV041', 'Oanh', 'Dang', 'Female', '2004-09-13', 'student41@university.edu', '0901000041', 6, 8, 'Studying', '2026-03-04 05:25:01'),
(28, NULL, 'SV042', 'Phuc', 'Nguyen', 'Male', '2004-10-14', 'student42@university.edu', '0901000042', 6, 8, 'Studying', '2026-03-04 05:25:01'),
(29, NULL, 'SV018', 'An', 'Nguyen', 'Male', '2004-07-07', 'student18@university.edu', '0901000018', 2, 3, 'Studying', '2026-03-04 05:25:52'),
(30, NULL, 'SV019', 'Binh', 'Tran', 'Male', '2004-08-08', 'student19@university.edu', '0901000019', 2, 3, 'Studying', '2026-03-04 05:25:52'),
(31, NULL, 'SV020', 'Chi', 'Pham', 'Female', '2004-09-09', 'student20@university.edu', '0901000020', 2, 3, 'Studying', '2026-03-04 05:25:52'),
(32, NULL, 'SV021', 'Dung', 'Le', 'Female', '2004-10-10', 'student21@university.edu', '0901000021', 2, 3, 'Studying', '2026-03-04 05:25:52'),
(33, NULL, 'SV022', 'Dat', 'Hoang', 'Male', '2004-01-15', 'student22@university.edu', '0901000022', 2, 3, 'Studying', '2026-03-04 05:25:52');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(20) NOT NULL,
  `subject_name` varchar(200) NOT NULL,
  `credit_hours` int(11) NOT NULL DEFAULT 3,
  `faculty_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `prerequisite_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `faculty_id`, `description`, `prerequisite_id`) VALUES
(1, 'CNTT101', 'Lập trình cơ bản', 3, 1, NULL, NULL),
(2, 'CNTT201', 'Cấu trúc dữ liệu và giải thuật', 3, 1, NULL, NULL),
(3, 'CNTT301', 'Cơ sở dữ liệu', 3, 1, NULL, NULL),
(4, 'QTKD101', 'Quản trị kinh doanh đại cương', 3, 2, NULL, NULL),
(5, 'QTKD201', 'Marketing căn bản', 3, 2, NULL, NULL),
(6, 'QTKD301', 'Quản trị chiến lược', 3, 2, NULL, NULL),
(7, 'ENGL101', 'Tiếng Anh cơ bản 1', 4, 3, NULL, NULL),
(8, 'ENGL201', 'Tiếng Anh cơ bản 2', 4, 3, NULL, NULL),
(9, 'ENGL301', 'Tiếng Anh nâng cao', 4, 3, NULL, NULL),
(10, 'KT101', 'Kế toán đại cương', 3, 4, NULL, NULL),
(11, 'KT201', 'Kế toán tài chính', 3, 4, NULL, NULL),
(12, 'KT301', 'Kiểm toán', 3, 4, NULL, NULL),
(13, 'TCNH101', 'Tài chính tiền tệ', 3, 5, NULL, NULL),
(14, 'TCNH201', 'Nghiệp vụ ngân hàng', 3, 5, NULL, NULL),
(15, 'TCNH301', 'Thị trường chứng khoán', 3, 5, NULL, NULL),
(16, 'SP101', 'Tâm lý học giáo dục', 3, 6, NULL, NULL),
(17, 'SP201', 'Lý luận dạy học', 3, 6, NULL, NULL),
(18, 'SP301', 'Thực hành giảng dạy', 3, 6, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','integer','boolean','json') DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `updated_at`) VALUES
('allow_register', '0', 'boolean', 'Cho phép tự đăng ký', '2026-02-28 21:58:01'),
('lock_duration', '15', 'integer', 'Thời gian khóa tài khoản (phút)', '2026-02-28 21:58:01'),
('max_login_fail', '5', 'integer', 'Số lần nhập sai tối đa trước khi khóa', '2026-02-28 21:58:01'),
('rbac_cache_ttl', '300', 'integer', 'Thời gian cache permission (giây)', '2026-02-28 21:58:01'),
('site_name', 'Hệ thống Quản lý Đào tạo', 'string', 'Tên hệ thống', '2026-02-28 21:58:01'),
('site_url', 'http://localhost/web_QLSV', 'string', 'URL gốc', '2026-02-28 21:58:01'),
('smtp_host', '', 'string', 'SMTP server', '2026-02-28 21:58:01'),
('smtp_pass', '', 'string', 'SMTP password', '2026-02-28 21:58:01'),
('smtp_port', '587', 'integer', 'SMTP port', '2026-02-28 21:58:01'),
('smtp_user', '', 'string', 'SMTP email', '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_fees`
--

CREATE TABLE `tuition_fees` (
  `fee_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `amount` decimal(12,0) NOT NULL DEFAULT 0,
  `paid_amount` decimal(12,0) DEFAULT 0,
  `due_date` date DEFAULT NULL,
  `status` enum('Unpaid','PartialPaid','Paid') DEFAULT 'Unpaid',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tuition_fees`
--

INSERT INTO `tuition_fees` (`fee_id`, `student_id`, `semester`, `year`, `amount`, `paid_amount`, `due_date`, `status`, `created_at`) VALUES
(1, 1, 'Spring', '2026', 8000000, 8000000, '2026-02-15', 'Paid', '2026-02-28 21:58:01'),
(2, 2, 'Spring', '2026', 8000000, 8000000, '2026-02-15', 'Paid', '2026-02-28 21:58:01'),
(3, 3, 'Spring', '2026', 8000000, 4000000, '2026-02-15', 'PartialPaid', '2026-02-28 21:58:01'),
(4, 4, 'Spring', '2026', 8000000, 0, '2026-02-15', 'Unpaid', '2026-02-28 21:58:01'),
(5, 5, 'Spring', '2026', 8000000, 8000000, '2026-02-15', 'Paid', '2026-02-28 21:58:01');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_invoices`
--

CREATE TABLE `tuition_invoices` (
  `invoice_id` int(11) NOT NULL,
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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_invoices`
--

INSERT INTO `tuition_invoices` (`invoice_id`, `student_id`, `semester`, `year`, `total_credits`, `amount_due`, `amount_paid`, `status`, `due_date`, `paid_at`, `note`, `created_at`) VALUES
(1, 2, '', '2026', 20, 11000000, 0, 'Unpaid', '2026-10-01', NULL, NULL, '2026-03-04 04:24:23');

-- --------------------------------------------------------

--
-- Table structure for table `tuition_settings`
--

CREATE TABLE `tuition_settings` (
  `setting_id` int(11) NOT NULL,
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL,
  `price_per_credit` decimal(12,0) NOT NULL DEFAULT 500000 COMMENT 'VNĐ / tín chỉ',
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tuition_settings`
--

INSERT INTO `tuition_settings` (`setting_id`, `semester`, `year`, `price_per_credit`, `note`, `created_at`) VALUES
(1, 'Spring', '2026', 550000, 'Học kỳ 1 năm 2026', '2026-03-03 20:01:42'),
(2, 'Summer', '2026', 550000, 'Học kỳ hè năm 2026', '2026-03-03 20:01:42'),
(3, 'Fall', '2026', 550000, 'Học kỳ 2 năm 2026', '2026-03-03 20:01:42'),
(4, 'Spring', '2025', 500000, 'Học kỳ 1 năm 2025', '2026-03-03 20:01:42'),
(5, 'Summer', '2025', 500000, 'Học kỳ hè năm 2025', '2026-03-03 20:01:42'),
(6, 'Fall', '2025', 500000, 'Học kỳ 2 năm 2025', '2026-03-03 20:01:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `failed_attempts`, `locked_until`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'superadmin@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-03-04 05:55:19', '2026-02-28 21:58:01', '2026-03-04 05:55:19'),
(2, 'gv01', 'gv01@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-03-04 06:06:15', '2026-02-28 21:58:01', '2026-03-04 06:06:15'),
(3, 'gv02', 'gv02@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(4, 'gv03', 'gv03@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(5, 'gv04', 'gv04@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(6, 'gv05', 'gv05@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(7, 'gv06', 'gv06@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(8, 'gv07', 'gv07@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(9, 'gv08', 'gv08@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(10, 'gv09', 'gv09@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(11, 'gv10', 'gv10@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(12, 'gv11', 'gv11@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(13, 'gv12', 'gv12@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(14, 'gv13', 'gv13@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(15, 'gv14', 'gv14@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(16, 'gv15', 'gv15@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(17, 'gv16', 'gv16@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(18, 'gv17', 'gv17@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(19, 'gv18', 'gv18@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(20, 'sv00', 'student00@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(21, 'sv01', 'student01@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-03-04 06:02:16', '2026-02-28 21:58:01', '2026-03-04 06:02:16'),
(22, 'sv02', 'student02@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(23, 'sv03', 'student03@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(24, 'sv04', 'student04@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-02-28 22:43:11', '2026-02-28 21:58:01', '2026-02-28 22:43:11'),
(25, 'sv05', 'student05@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(26, 'sv06', 'student06@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-02-28 22:44:58', '2026-02-28 21:58:01', '2026-02-28 22:44:58'),
(27, 'sv07', 'student07@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(28, 'sv08', 'student08@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(29, 'sv09', 'student09@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(30, 'sv10', 'student10@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(200, 'daotao', 'academic1@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-03-04 05:45:17', '2026-02-28 21:58:01', '2026-03-04 05:45:17'),
(201, 'student_admin1', 'studentadmin@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-02-28 22:46:58', '2026-02-28 21:58:01', '2026-02-28 22:46:58'),
(202, 'accountant1', 'accountant@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, NULL, '2026-02-28 21:58:01', '2026-02-28 22:32:46'),
(203, 'thuvien', 'librarian@university.edu', '$2y$10$yVh4s4Iphz.xmOpGIMZ3HubnSr7MZMGhxRv5k917MIOJhYS0HnZjO', 1, 0, NULL, '2026-03-02 18:46:21', '2026-02-28 21:58:01', '2026-03-02 18:46:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_permission_cache`
--

CREATE TABLE `user_permission_cache` (
  `user_id` int(11) NOT NULL,
  `permission_code` varchar(100) NOT NULL,
  `cached_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `assigned_at` datetime DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`, `assigned_at`, `assigned_by`) VALUES
(1, 1, '2026-02-28 21:58:01', NULL),
(2, 4, '2026-02-28 21:58:01', NULL),
(3, 4, '2026-02-28 21:58:01', NULL),
(4, 4, '2026-02-28 21:58:01', NULL),
(5, 4, '2026-02-28 21:58:01', NULL),
(6, 4, '2026-02-28 21:58:01', NULL),
(7, 4, '2026-02-28 21:58:01', NULL),
(8, 4, '2026-02-28 21:58:01', NULL),
(9, 4, '2026-02-28 21:58:01', NULL),
(10, 4, '2026-02-28 21:58:01', NULL),
(11, 4, '2026-02-28 21:58:01', NULL),
(12, 4, '2026-02-28 21:58:01', NULL),
(13, 4, '2026-02-28 21:58:01', NULL),
(14, 4, '2026-02-28 21:58:01', NULL),
(15, 4, '2026-02-28 21:58:01', NULL),
(16, 4, '2026-02-28 21:58:01', NULL),
(17, 4, '2026-02-28 21:58:01', NULL),
(18, 4, '2026-02-28 21:58:01', NULL),
(19, 4, '2026-02-28 21:58:01', NULL),
(20, 5, '2026-02-28 21:58:01', NULL),
(21, 5, '2026-02-28 21:58:01', NULL),
(22, 5, '2026-02-28 21:58:01', NULL),
(23, 5, '2026-02-28 21:58:01', NULL),
(24, 5, '2026-02-28 21:58:01', NULL),
(25, 5, '2026-02-28 21:58:01', NULL),
(26, 5, '2026-02-28 21:58:01', NULL),
(27, 5, '2026-02-28 21:58:01', NULL),
(28, 5, '2026-02-28 21:58:01', NULL),
(29, 5, '2026-02-28 21:58:01', NULL),
(30, 5, '2026-02-28 21:58:01', NULL),
(200, 2, '2026-02-28 21:58:01', NULL),
(201, 3, '2026-02-28 21:58:01', NULL),
(202, 6, '2026-02-28 21:58:01', NULL),
(203, 7, '2026-02-28 21:58:01', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`attendance_id`),
  ADD KEY `fk_att_enrollment` (`enrollment_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_created` (`created_at`),
  ADD KEY `idx_audit_table` (`table_name`);

--
-- Indexes for table `base_classes`
--
ALTER TABLE `base_classes`
  ADD PRIMARY KEY (`base_class_id`),
  ADD UNIQUE KEY `base_class_code` (`base_class_code`),
  ADD KEY `fk_bc_faculty` (`faculty_id`),
  ADD KEY `fk_bc_lecturer` (`lecturer_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `fk_cls_subject` (`subject_id`),
  ADD KEY `fk_cls_lecturer` (`lecturer_id`),
  ADD KEY `fk_class_semester` (`semester_id`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`schedule_id`),
  ADD KEY `fk_cs_class` (`class_id`);

--
-- Indexes for table `dormitory_registrations`
--
ALTER TABLE `dormitory_registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `fk_dr_student` (`student_id`),
  ADD KEY `fk_dr_room` (`room_id`);

--
-- Indexes for table `dormitory_rooms`
--
ALTER TABLE `dormitory_rooms`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `uq_enroll` (`student_id`,`class_id`),
  ADD KEY `fk_enr_class` (`class_id`),
  ADD KEY `idx_enrollments_student` (`student_id`),
  ADD KEY `idx_enrollments_class` (`class_id`);

--
-- Indexes for table `enrollment_registration_periods`
--
ALTER TABLE `enrollment_registration_periods`
  ADD PRIMARY KEY (`period_id`),
  ADD UNIQUE KEY `semester_year` (`semester`,`year`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `faculty_code` (`faculty_code`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `fk_gr_enrollment` (`enrollment_id`),
  ADD KEY `idx_grades_enrollment` (`enrollment_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`),
  ADD UNIQUE KEY `lecturer_code` (`lecturer_code`),
  ADD KEY `fk_lec_user` (`user_id`),
  ADD KEY `fk_lec_faculty` (`faculty_id`),
  ADD KEY `idx_lecturers_faculty` (`faculty_id`);

--
-- Indexes for table `library_books`
--
ALTER TABLE `library_books`
  ADD PRIMARY KEY (`book_id`);

--
-- Indexes for table `library_borrows`
--
ALTER TABLE `library_borrows`
  ADD PRIMARY KEY (`borrow_id`),
  ADD KEY `fk_lb_student` (`student_id`),
  ADD KEY `fk_lb_book` (`book_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pr_user` (`user_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `uq_perm_code` (`code`),
  ADD KEY `fk_perm_group` (`group_id`);

--
-- Indexes for table `permission_groups`
--
ALTER TABLE `permission_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD UNIQUE KEY `uq_pg_code` (`code`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `fk_rp_perm` (`permission_id`);

--
-- Indexes for table `scholarships`
--
ALTER TABLE `scholarships`
  ADD PRIMARY KEY (`scholarship_id`);

--
-- Indexes for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `fk_sa_student` (`student_id`),
  ADD KEY `fk_sa_scholarship` (`scholarship_id`);

--
-- Indexes for table `semesters`
--
ALTER TABLE `semesters`
  ADD PRIMARY KEY (`semester_id`),
  ADD UNIQUE KEY `semester_code` (`semester_code`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD KEY `fk_stu_user` (`user_id`),
  ADD KEY `fk_stu_faculty` (`faculty_id`),
  ADD KEY `fk_stu_class` (`base_class_id`),
  ADD KEY `idx_students_faculty` (`faculty_id`),
  ADD KEY `idx_students_status` (`status`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `fk_sub_faculty` (`faculty_id`),
  ADD KEY `prerequisite_id` (`prerequisite_id`);

--
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`setting_key`);

--
-- Indexes for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD PRIMARY KEY (`fee_id`),
  ADD KEY `fk_tf_student` (`student_id`);

--
-- Indexes for table `tuition_invoices`
--
ALTER TABLE `tuition_invoices`
  ADD PRIMARY KEY (`invoice_id`),
  ADD UNIQUE KEY `uq_invoice` (`student_id`,`semester`,`year`),
  ADD KEY `fk_invoice_student` (`student_id`);

--
-- Indexes for table `tuition_settings`
--
ALTER TABLE `tuition_settings`
  ADD PRIMARY KEY (`setting_id`),
  ADD UNIQUE KEY `uq_tuition_semester` (`semester`,`year`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_permission_cache`
--
ALTER TABLE `user_permission_cache`
  ADD PRIMARY KEY (`user_id`,`permission_code`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `fk_ur_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT for table `base_classes`
--
ALTER TABLE `base_classes`
  MODIFY `base_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `dormitory_registrations`
--
ALTER TABLE `dormitory_registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dormitory_rooms`
--
ALTER TABLE `dormitory_rooms`
  MODIFY `room_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `enrollment_registration_periods`
--
ALTER TABLE `enrollment_registration_periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `library_books`
--
ALTER TABLE `library_books`
  MODIFY `book_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `library_borrows`
--
ALTER TABLE `library_borrows`
  MODIFY `borrow_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `permission_groups`
--
ALTER TABLE `permission_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `scholarships`
--
ALTER TABLE `scholarships`
  MODIFY `scholarship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `semesters`
--
ALTER TABLE `semesters`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  MODIFY `fee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tuition_invoices`
--
ALTER TABLE `tuition_invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tuition_settings`
--
ALTER TABLE `tuition_settings`
  MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `fk_att_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `base_classes`
--
ALTER TABLE `base_classes`
  ADD CONSTRAINT `fk_bc_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `fk_bc_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `fk_class_semester` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`semester_id`),
  ADD CONSTRAINT `fk_cls_lecturer` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`),
  ADD CONSTRAINT `fk_cls_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`);

--
-- Constraints for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD CONSTRAINT `fk_cs_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`) ON DELETE CASCADE;

--
-- Constraints for table `dormitory_registrations`
--
ALTER TABLE `dormitory_registrations`
  ADD CONSTRAINT `fk_dr_room` FOREIGN KEY (`room_id`) REFERENCES `dormitory_rooms` (`room_id`),
  ADD CONSTRAINT `fk_dr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `fk_enr_class` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`),
  ADD CONSTRAINT `fk_enr_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `fk_gr_enrollment` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `fk_lec_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `fk_lec_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `library_borrows`
--
ALTER TABLE `library_borrows`
  ADD CONSTRAINT `fk_lb_book` FOREIGN KEY (`book_id`) REFERENCES `library_books` (`book_id`),
  ADD CONSTRAINT `fk_lb_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `fk_perm_group` FOREIGN KEY (`group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `fk_rp_perm` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `scholarship_applications`
--
ALTER TABLE `scholarship_applications`
  ADD CONSTRAINT `fk_sa_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`),
  ADD CONSTRAINT `fk_sa_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_stu_class` FOREIGN KEY (`base_class_id`) REFERENCES `base_classes` (`base_class_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_stu_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `fk_stu_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_sub_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`prerequisite_id`) REFERENCES `subjects` (`subject_id`);

--
-- Constraints for table `tuition_fees`
--
ALTER TABLE `tuition_fees`
  ADD CONSTRAINT `fk_tf_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);

--
-- Constraints for table `tuition_invoices`
--
ALTER TABLE `tuition_invoices`
  ADD CONSTRAINT `fk_invoice_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permission_cache`
--
ALTER TABLE `user_permission_cache`
  ADD CONSTRAINT `fk_upc_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `fk_ur_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ur_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
