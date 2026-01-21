-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Jan 20, 2026 at 07:01 PM
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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_students_roles` ()   BEGIN
  DECLARE i INT DEFAULT 20;   -- bắt đầu từ user_id = 21
  WHILE i <= 199 DO           -- kết thúc ở user_id = 200
    INSERT INTO user_roles (user_id, role_id)
    VALUES (i, 4);            -- role_id = 4 là Sinh viên
    SET i = i + 1;
  END WHILE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `assign_teachers_roles` ()   BEGIN
  DECLARE i INT DEFAULT 2;   -- bắt đầu từ user_id = 3
  WHILE i <= 19 DO           -- kết thúc ở user_id = 20
    INSERT INTO user_roles (user_id, role_id)
    VALUES (i, 3);           -- role_id = 3 là Giảng viên
    SET i = i + 1;
  END WHILE;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `audit_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_data` text DEFAULT NULL,
  `new_data` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `semester` enum('Spring','Summer','Fall') NOT NULL,
  `year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`class_id`, `class_code`, `subject_id`, `lecturer_id`, `semester`, `year`) VALUES
(1, 'CNTT101-01', 1, 1, 'Spring', '2026'),
(2, 'CNTT201-01', 2, 2, 'Summer', '2026'),
(3, 'CNTT301-01', 3, 3, 'Fall', '2026'),
(4, 'QTKD101-01', 4, 4, 'Spring', '2026'),
(5, 'QTKD201-01', 5, 5, 'Summer', '2026'),
(6, 'QTKD301-01', 6, 6, 'Fall', '2026'),
(7, 'ENGL101-01', 7, 7, 'Spring', '2026'),
(8, 'ENGL201-01', 8, 8, 'Summer', '2026'),
(9, 'ENGL301-01', 9, 9, 'Fall', '2026'),
(10, 'KT101-01', 10, 10, 'Spring', '2026'),
(11, 'KT201-01', 11, 11, 'Summer', '2026'),
(12, 'KT301-01', 12, 12, 'Fall', '2026'),
(13, 'TCNH101-01', 13, 13, 'Spring', '2026'),
(14, 'TCNH201-01', 14, 14, 'Summer', '2026'),
(15, 'TCNH301-01', 15, 15, 'Fall', '2026'),
(16, 'SP101-01', 16, 16, 'Spring', '2026'),
(17, 'SP201-01', 17, 17, 'Summer', '2026'),
(18, 'SP301-01', 18, 18, 'Fall', '2026');

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `enrollment_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `class_id` int(11) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `status` enum('Registered','Completed','Cancelled','Failed') DEFAULT 'Registered'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`enrollment_id`, `student_id`, `class_id`, `registration_date`, `status`) VALUES
(16, 33, 1, '2026-01-21 00:20:57', 'Registered'),
(17, 34, 2, '2026-01-21 00:20:57', 'Registered'),
(18, 35, 3, '2026-01-21 00:20:57', 'Registered'),
(19, 36, 1, '2026-01-21 00:20:57', 'Registered'),
(20, 37, 2, '2026-01-21 00:20:57', 'Registered'),
(21, 40, 3, '2026-01-21 00:20:57', 'Registered'),
(22, 43, 1, '2026-01-21 00:20:57', 'Registered'),
(23, 45, 2, '2026-01-21 00:20:57', 'Registered'),
(24, 42, 3, '2026-01-21 00:20:57', 'Registered'),
(25, 50, 1, '2026-01-21 00:20:57', 'Registered'),
(26, 51, 2, '2026-01-21 00:20:57', 'Completed'),
(27, 53, 3, '2026-01-21 00:20:57', 'Completed'),
(28, 49, 1, '2026-01-21 00:20:57', 'Completed'),
(29, 47, 2, '2026-01-21 00:20:57', 'Completed'),
(30, 46, 3, '2026-01-21 00:20:57', 'Completed'),
(125, 62, 6, '2026-01-21 00:30:18', 'Registered'),
(126, 63, 4, '2026-01-21 00:30:18', 'Registered'),
(127, 89, 5, '2026-01-21 00:30:18', 'Registered'),
(128, 88, 6, '2026-01-21 00:30:18', 'Registered'),
(129, 87, 4, '2026-01-21 00:30:18', 'Registered'),
(130, 86, 5, '2026-01-21 00:30:18', 'Registered'),
(131, 85, 6, '2026-01-21 00:30:18', 'Registered'),
(132, 79, 4, '2026-01-21 00:30:18', 'Registered'),
(133, 80, 5, '2026-01-21 00:30:18', 'Registered'),
(134, 81, 6, '2026-01-21 00:30:18', 'Registered'),
(135, 82, 4, '2026-01-21 00:30:18', 'Registered'),
(136, 83, 5, '2026-01-21 00:30:18', 'Registered'),
(137, 84, 6, '2026-01-21 00:30:18', 'Registered'),
(138, 94, 5, '2026-01-21 00:30:18', 'Completed'),
(139, 92, 6, '2026-01-21 00:31:13', 'Failed'),
(140, 94, 4, '2026-01-21 00:31:13', 'Failed'),
(141, 91, 5, '2026-01-21 00:31:13', 'Failed'),
(142, 96, 4, '2026-01-21 00:31:43', 'Cancelled'),
(143, 97, 5, '2026-01-21 00:31:43', 'Cancelled'),
(144, 98, 5, '2026-01-21 00:32:49', 'Completed'),
(145, 89, 6, '2026-01-21 00:32:49', 'Completed'),
(146, 91, 4, '2026-01-21 00:32:49', 'Completed'),
(182, 229, 16, '2026-01-21 00:50:51', 'Completed'),
(183, 230, 17, '2026-01-21 00:50:51', 'Completed'),
(184, 231, 18, '2026-01-21 00:50:51', 'Registered'),
(185, 232, 16, '2026-01-21 00:50:51', 'Registered'),
(186, 233, 17, '2026-01-21 00:50:51', 'Completed'),
(187, 234, 18, '2026-01-21 00:50:51', 'Registered'),
(188, 235, 16, '2026-01-21 00:50:51', 'Registered'),
(189, 236, 17, '2026-01-21 00:50:51', 'Registered'),
(190, 237, 18, '2026-01-21 00:50:51', 'Completed'),
(191, 238, 16, '2026-01-21 00:50:51', 'Registered'),
(192, 239, 16, '2026-01-21 00:50:51', 'Registered'),
(193, 240, 17, '2026-01-21 00:50:51', 'Registered'),
(194, 241, 18, '2026-01-21 00:50:51', 'Completed'),
(195, 173, 16, '2026-01-21 00:55:34', 'Completed'),
(196, 174, 17, '2026-01-21 00:55:34', 'Completed'),
(197, 175, 18, '2026-01-21 00:55:34', 'Registered'),
(198, 176, 16, '2026-01-21 00:55:34', 'Registered'),
(199, 177, 17, '2026-01-21 00:55:34', 'Completed'),
(200, 178, 18, '2026-01-21 00:55:34', 'Registered'),
(201, 179, 16, '2026-01-21 00:55:34', 'Registered'),
(202, 180, 17, '2026-01-21 00:55:34', 'Registered'),
(203, 181, 18, '2026-01-21 00:55:34', 'Completed'),
(204, 182, 16, '2026-01-21 00:55:34', 'Registered'),
(205, 148, 10, '2026-01-21 01:00:02', 'Completed'),
(206, 149, 11, '2026-01-21 01:00:02', 'Completed'),
(207, 150, 12, '2026-01-21 01:00:02', 'Registered'),
(208, 151, 10, '2026-01-21 01:00:02', 'Registered'),
(209, 152, 11, '2026-01-21 01:00:02', 'Completed'),
(210, 155, 11, '2026-01-21 01:00:02', 'Registered'),
(211, 156, 12, '2026-01-21 01:00:02', 'Completed'),
(212, 157, 10, '2026-01-21 01:00:02', 'Registered'),
(213, 161, 11, '2026-01-21 01:00:02', 'Completed'),
(214, 162, 12, '2026-01-21 01:00:02', 'Registered'),
(215, 163, 10, '2026-01-21 01:00:02', 'Registered'),
(216, 164, 11, '2026-01-21 01:00:02', 'Registered'),
(217, 165, 12, '2026-01-21 01:00:02', 'Completed'),
(218, 166, 10, '2026-01-21 01:00:02', 'Registered'),
(219, 167, 11, '2026-01-21 01:00:02', 'Registered');

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `faculty_id` int(11) NOT NULL,
  `faculty_code` varchar(20) NOT NULL,
  `faculty_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`faculty_id`, `faculty_code`, `faculty_name`, `description`) VALUES
(1, 'CNTT', 'Công nghệ thông tin', 'Khoa đào tạo các chuyên ngành về công nghệ thông tin'),
(2, 'QTKD', 'Quản trị kinh doanh', 'Khoa đào tạo về quản trị và kinh doanh'),
(3, 'NN', 'Ngôn ngữ Anh', 'Khoa đào tạo ngôn ngữ và văn hóa Anh'),
(4, 'KT', 'Kế toán', 'Khoa đào tạo chuyên ngành kế toán và kiểm toán'),
(5, 'TCNH', 'Tài chính - Ngân hàng', 'Khoa đào tạo chuyên ngành tài chính và ngân hàng'),
(6, 'SP', 'Sư phạm', 'Khoa đào tạo giáo viên các bộ môn');

-- --------------------------------------------------------

--
-- Table structure for table `faculty_addresses`
--

CREATE TABLE `faculty_addresses` (
  `address_id` int(11) NOT NULL,
  `faculty_code` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty_addresses`
--

INSERT INTO `faculty_addresses` (`address_id`, `faculty_code`, `address`, `email`) VALUES
(1, 'CNTT', 'Tòa nhà A1, Trường Đại học XYZ, Hà Nội', 'cntt@xyz.edu.vn'),
(2, 'QTKD', 'Tòa nhà B2, Trường Đại học XYZ, Hà Nội', 'qtkd@xyz.edu.vn'),
(3, 'NN', 'Tòa nhà C3, Trường Đại học XYZ, Hà Nội', 'nn@xyz.edu.vn'),
(4, 'KT', 'Tòa nhà D4, Trường Đại học XYZ, Hà Nội', 'kt@xyz.edu.vn'),
(5, 'TCNH', 'Tòa nhà E5, Trường Đại học XYZ, Hà Nội', 'tcnh@xyz.edu.vn'),
(6, 'SP', 'Tòa nhà F6, Trường Đại học XYZ, Hà Nội', 'sp@xyz.edu.vn');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `grade_id` int(11) NOT NULL,
  `enrollment_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `grade_letter` varchar(2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`grade_id`, `enrollment_id`, `score`, `grade_letter`) VALUES
(1, 26, 88.00, 'B'),
(2, 182, 86.00, 'B+'),
(3, 183, 86.00, 'B+'),
(4, 186, 86.00, 'B+'),
(5, 190, 86.00, 'B+'),
(6, 194, 86.00, 'B+'),
(9, 205, 86.00, 'B+'),
(10, 206, 86.00, 'B+'),
(11, 209, 86.00, 'B+'),
(12, 211, 75.00, 'C+'),
(13, 213, 75.00, 'C+'),
(14, 217, 75.00, 'C+');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `lecturer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `lecturer_code` varchar(10) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `faculty_id` int(11) NOT NULL,
  `degree` enum('Bachelor','Master','PhD','Professor') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`lecturer_id`, `user_id`, `lecturer_code`, `first_name`, `last_name`, `email`, `phone`, `faculty_id`, `degree`) VALUES
(1, 2, 'GV001', 'Anh', 'Nguyen', 'teacher01@university.edu', '0911000001', 1, 'Bachelor'),
(2, 3, 'GV002', 'Binh', 'Tran', 'teacher02@university.edu', '0911000002', 1, 'Master'),
(3, 4, 'GV003', 'Chi', 'Le', 'teacher03@university.edu', '0911000003', 1, 'PhD'),
(4, 5, 'GV004', 'Dung', 'Pham', 'teacher04@university.edu', '0911000004', 2, 'Professor'),
(5, 6, 'GV005', 'Hoa', 'Vu', 'teacher05@university.edu', '0911000005', 2, 'Bachelor'),
(6, 7, 'GV006', 'Khanh', 'Do', 'teacher06@university.edu', '0911000006', 2, 'Master'),
(7, 8, 'GV007', 'Lan', 'Hoang', 'teacher07@university.edu', '0911000007', 3, 'PhD'),
(8, 9, 'GV008', 'Minh', 'Nguyen', 'teacher08@university.edu', '0911000008', 3, 'Professor'),
(9, 10, 'GV009', 'Nam', 'Tran', 'teacher09@university.edu', '0911000009', 3, 'Bachelor'),
(10, 11, 'GV010', 'Oanh', 'Le', 'teacher10@university.edu', '0911000010', 4, 'Master'),
(11, 12, 'GV011', 'Phong', 'Nguyen', 'teacher11@university.edu', '0911000011', 4, 'PhD'),
(12, 13, 'GV012', 'Quang', 'Tran', 'teacher12@university.edu', '0911000012', 4, 'Professor'),
(13, 14, 'GV013', 'Son', 'Pham', 'teacher13@university.edu', '0911000013', 5, 'Bachelor'),
(14, 15, 'GV014', 'Thao', 'Vu', 'teacher14@university.edu', '0911000014', 5, 'Master'),
(15, 16, 'GV015', 'Uyen', 'Do', 'teacher15@university.edu', '0911000015', 5, 'PhD'),
(16, 17, 'GV016', 'Van', 'Hoang', 'teacher16@university.edu', '0911000016', 6, 'Professor'),
(17, 18, 'GV017', 'Xuan', 'Nguyen', 'teacher17@university.edu', '0911000017', 6, 'Bachelor'),
(18, 19, 'GV018', 'Yen', 'Tran', 'teacher18@university.edu', '0911000018', 6, 'Master');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `code`, `description`) VALUES
(1, 'manage_users', 'Quản lý người dùng'),
(2, 'manage_roles', 'Quản lý vai trò'),
(3, 'view_audit_logs', 'Xem nhật ký hệ thống'),
(4, 'manage_students', 'Quản lý sinh viên'),
(5, 'manage_lecturers', 'Quản lý giảng viên'),
(6, 'manage_faculties', 'Quản lý khoa'),
(7, 'manage_subjects', 'Quản lý môn học'),
(8, 'manage_classes', 'Quản lý lớp học'),
(9, 'manage_grades', 'Quản lý điểm'),
(10, 'view_reports', 'Xem báo cáo'),
(11, 'reset_passwords', 'Đặt lại mật khẩu người dùng'),
(12, 'register_courses', 'Đăng ký môn học'),
(13, 'export_reports', 'Xuất báo cáo'),
(14, 'view_transcripts', 'Xem bảng điểm chi tiết');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `code`, `name`) VALUES
(1, 'super_admin', 'Admin quản trị hệ thống'),
(2, 'content_admin', 'Admin quản trị nội dung'),
(3, 'teacher', 'Giảng viên'),
(4, 'student', 'Sinh viên');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 9),
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 14),
(4, 10),
(4, 11),
(4, 12),
(4, 14);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `user_id`, `student_code`, `first_name`, `last_name`, `gender`, `birth_date`, `email`, `phone`, `faculty_id`, `base_class_id`, `status`, `created_at`) VALUES
(33, 20, 'SV000', 'Nam', 'Tran', 'Male', '2005-03-15', 'student00@university.edu', '0123456789', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(34, 21, 'SV001', 'An', 'Nguyen', 'Male', '2004-01-15', 'student01@university.edu', '0901000001', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(35, 22, 'SV002', 'Binh', 'Tran', 'Female', '2004-02-20', 'student02@university.edu', '0901000002', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(36, 23, 'SV003', 'Chi', 'Le', 'Male', '2004-03-10', 'student03@university.edu', '0901000003', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(37, 24, 'SV004', 'Dung', 'Pham', 'Female', '2004-04-05', 'student04@university.edu', '0901000004', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(38, 25, 'SV005', 'Hoa', 'Vu', 'Male', '2004-05-25', 'student05@university.edu', '0901000005', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(39, 26, 'SV006', 'Khanh', 'Do', 'Female', '2004-06-12', 'student06@university.edu', '0901000006', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(40, 27, 'SV007', 'Lan', 'Hoang', 'Male', '2004-07-08', 'student07@university.edu', '0901000007', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(41, 28, 'SV008', 'Minh', 'Nguyen', 'Female', '2004-08-18', 'student08@university.edu', '0901000008', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(42, 29, 'SV009', 'Nam', 'Tran', 'Male', '2004-09-22', 'student09@university.edu', '0901000009', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(43, 30, 'SV010', 'Oanh', 'Le', 'Female', '2004-10-30', 'student10@university.edu', '0901000010', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(44, 31, 'SV011', 'Phong', 'Nguyen', 'Male', '2004-11-15', 'student11@university.edu', '0901000011', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(45, 32, 'SV012', 'Quang', 'Tran', 'Female', '2004-12-05', 'student12@university.edu', '0901000012', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(46, 33, 'SV013', 'Son', 'Pham', 'Male', '2005-01-10', 'student13@university.edu', '0901000013', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(47, 34, 'SV014', 'Thao', 'Vu', 'Female', '2005-02-20', 'student14@university.edu', '0901000014', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(48, 35, 'SV015', 'Uyen', 'Do', 'Male', '2005-03-15', 'student15@university.edu', '0901000015', 1, NULL, 'Studying', '2026-01-21 00:07:14'),
(49, 36, 'SV016', 'Van', 'Hoang', 'Female', '2005-04-25', 'student16@university.edu', '0901000016', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(50, 37, 'SV017', 'Xuan', 'Nguyen', 'Male', '2005-05-30', 'student17@university.edu', '0901000017', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(51, 38, 'SV018', 'Yen', 'Tran', 'Female', '2005-06-18', 'student18@university.edu', '0901000018', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(52, 39, 'SV019', 'Zung', 'Pham', 'Male', '2005-07-22', 'student19@university.edu', '0901000019', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(53, 40, 'SV020', 'Bao', 'Le', 'Female', '2005-08-10', 'student20@university.edu', '0901000020', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(54, 41, 'SV021', 'Cuong', 'Nguyen', 'Male', '2005-09-01', 'student21@university.edu', '0901000021', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(55, 42, 'SV022', 'Dao', 'Tran', 'Female', '2005-09-15', 'student22@university.edu', '0901000022', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(56, 43, 'SV023', 'Hieu', 'Le', 'Male', '2005-10-05', 'student23@university.edu', '0901000023', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(57, 44, 'SV024', 'Giang', 'Pham', 'Female', '2005-10-20', 'student24@university.edu', '0901000024', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(58, 45, 'SV025', 'Kien', 'Vu', 'Male', '2005-11-02', 'student25@university.edu', '0901000025', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(59, 46, 'SV026', 'Linh', 'Do', 'Female', '2005-11-18', 'student26@university.edu', '0901000026', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(60, 47, 'SV027', 'Manh', 'Hoang', 'Male', '2005-12-01', 'student27@university.edu', '0901000027', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(61, 48, 'SV028', 'Nga', 'Nguyen', 'Female', '2005-12-15', 'student28@university.edu', '0901000028', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(62, 49, 'SV029', 'Phuc', 'Tran', 'Male', '2006-01-05', 'student29@university.edu', '0901000029', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(63, 50, 'SV030', 'Phong', 'Nguyen', 'Male', '2004-11-15', 'student30@university.edu', '0901000030', 1, NULL, 'Studying', '2026-01-21 00:07:48'),
(79, 51, 'SV031', 'Son', 'Pham', 'Male', '2006-02-02', 'student31@university.edu', '0901000031', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(80, 52, 'SV032', 'Thao', 'Vu', 'Female', '2006-02-18', 'student32@university.edu', '0901000032', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(81, 53, 'SV033', 'Uyen', 'Do', 'Male', '2006-03-05', 'student33@university.edu', '0901000033', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(82, 54, 'SV034', 'Van', 'Hoang', 'Female', '2006-03-20', 'student34@university.edu', '0901000034', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(83, 55, 'SV035', 'Xuan', 'Nguyen', 'Male', '2006-04-05', 'student35@university.edu', '0901000035', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(84, 56, 'SV036', 'An', 'Nguyen', 'Male', '2006-04-20', 'student36@university.edu', '0901000036', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(85, 57, 'SV037', 'Binh', 'Tran', 'Female', '2006-05-05', 'student37@university.edu', '0901000037', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(86, 58, 'SV038', 'Chi', 'Le', 'Male', '2006-05-18', 'student38@university.edu', '0901000038', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(87, 59, 'SV039', 'Dung', 'Pham', 'Female', '2006-06-02', 'student39@university.edu', '0901000039', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(88, 60, 'SV040', 'Hoa', 'Vu', 'Male', '2006-06-15', 'student40@university.edu', '0901000040', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(89, 61, 'SV041', 'Khanh', 'Do', 'Female', '2006-07-01', 'student41@university.edu', '0901000041', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(90, 62, 'SV042', 'Lan', 'Hoang', 'Male', '2006-07-18', 'student42@university.edu', '0901000042', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(91, 63, 'SV043', 'Minh', 'Nguyen', 'Female', '2006-08-05', 'student43@university.edu', '0901000043', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(92, 64, 'SV044', 'Nam', 'Tran', 'Male', '2006-08-20', 'student44@university.edu', '0901000044', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(93, 65, 'SV045', 'Oanh', 'Le', 'Female', '2006-09-02', 'student45@university.edu', '0901000045', 2, NULL, 'Studying', '2026-01-21 00:09:58'),
(94, 66, 'SV046', 'Phong', 'Nguyen', 'Male', '2006-09-18', 'student46@university.edu', '0901000046', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(95, 67, 'SV047', 'Quang', 'Tran', 'Female', '2006-10-05', 'student47@university.edu', '0901000047', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(96, 68, 'SV048', 'Son', 'Pham', 'Male', '2006-10-20', 'student48@university.edu', '0901000048', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(97, 69, 'SV049', 'Thao', 'Vu', 'Female', '2006-11-02', 'student49@university.edu', '0901000049', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(98, 70, 'SV050', 'Uyen', 'Do', 'Male', '2006-11-18', 'student50@university.edu', '0901000050', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(99, 71, 'SV051', 'Van', 'Hoang', 'Female', '2006-12-05', 'student51@university.edu', '0901000051', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(100, 72, 'SV052', 'Xuan', 'Nguyen', 'Male', '2006-12-20', 'student52@university.edu', '0901000052', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(101, 73, 'SV053', 'Yen', 'Tran', 'Female', '2007-01-05', 'student53@university.edu', '0901000053', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(102, 74, 'SV054', 'Zung', 'Pham', 'Male', '2007-01-20', 'student54@university.edu', '0901000054', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(103, 75, 'SV055', 'Bao', 'Le', 'Female', '2007-02-05', 'student55@university.edu', '0901000055', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(104, 76, 'SV056', 'Bao', 'Nguyen', 'Male', '2007-02-20', 'student56@university.edu', '0912000056', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(105, 77, 'SV057', 'Cam', 'Tran', 'Female', '2007-03-05', 'student57@university.edu', '0912000057', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(106, 78, 'SV058', 'Diep', 'Le', 'Male', '2007-03-18', 'student58@university.edu', '0912000058', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(107, 79, 'SV059', 'Hanh', 'Pham', 'Female', '2007-04-02', 'student59@university.edu', '0912000059', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(108, 80, 'SV060', 'Khoa', 'Vu', 'Male', '2007-04-18', 'student60@university.edu', '0912000060', 2, NULL, 'Studying', '2026-01-21 00:10:11'),
(109, 81, 'SV061', 'Luyen', 'Do', 'Female', '2007-05-05', 'student61@university.edu', '0912000061', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(110, 82, 'SV062', 'My', 'Hoang', 'Male', '2007-05-20', 'student62@university.edu', '0912000062', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(111, 83, 'SV063', 'Ngoc', 'Nguyen', 'Female', '2007-06-02', 'student63@university.edu', '0912000063', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(112, 84, 'SV064', 'Phuong', 'Tran', 'Male', '2007-06-18', 'student64@university.edu', '0912000064', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(113, 85, 'SV065', 'Quoc', 'Le', 'Female', '2007-07-05', 'student65@university.edu', '0912000065', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(114, 86, 'SV066', 'Sinh', 'Pham', 'Male', '2007-07-20', 'student66@university.edu', '0912000066', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(115, 87, 'SV067', 'Trang', 'Vu', 'Female', '2007-08-05', 'student67@university.edu', '0912000067', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(116, 88, 'SV068', 'Uy', 'Do', 'Male', '2007-08-20', 'student68@university.edu', '0912000068', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(117, 89, 'SV069', 'Vy', 'Hoang', 'Female', '2007-09-05', 'student69@university.edu', '0912000069', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(118, 90, 'SV070', 'Xuyen', 'Nguyen', 'Male', '2007-09-20', 'student70@university.edu', '0912000070', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(119, 91, 'SV071', 'Yen', 'Tran', 'Female', '2007-10-05', 'student71@university.edu', '0912000071', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(120, 92, 'SV072', 'An', 'Le', 'Male', '2007-10-20', 'student72@university.edu', '0912000072', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(121, 93, 'SV073', 'Bich', 'Pham', 'Female', '2007-11-05', 'student73@university.edu', '0912000073', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(122, 94, 'SV074', 'Cuong', 'Vu', 'Male', '2007-11-20', 'student74@university.edu', '0912000074', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(123, 95, 'SV075', 'Dao', 'Do', 'Female', '2007-12-05', 'student75@university.edu', '0912000075', 3, NULL, 'Studying', '2026-01-21 00:10:23'),
(124, 96, 'SV076', 'AnhThu', 'Nguyen', 'Female', '2007-12-20', 'student76@university.edu', '0913000076', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(125, 97, 'SV077', 'BaoLong', 'Tran', 'Male', '2003-01-05', 'student77@university.edu', '0913000077', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(126, 98, 'SV078', 'CatTuong', 'Le', 'Female', '2003-02-20', 'student78@university.edu', '0913000078', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(127, 99, 'SV079', 'DangKhoa', 'Pham', 'Male', '2004-03-15', 'student79@university.edu', '0913000079', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(128, 100, 'SV080', 'HaVy', 'Vu', 'Female', '2004-04-10', 'student80@university.edu', '0913000080', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(129, 101, 'SV081', 'GiaBao', 'Do', 'Male', '2004-05-25', 'student81@university.edu', '0913000081', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(130, 102, 'SV082', 'HongAnh', 'Hoang', 'Female', '2004-06-18', 'student82@university.edu', '0913000082', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(131, 103, 'SV083', 'KhaiMinh', 'Nguyen', 'Male', '2005-07-05', 'student83@university.edu', '0913000083', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(132, 104, 'SV084', 'LamPhuong', 'Tran', 'Female', '2005-08-20', 'student84@university.edu', '0913000084', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(133, 105, 'SV085', 'ManhHung', 'Le', 'Male', '2005-09-12', 'student85@university.edu', '0913000085', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(134, 106, 'SV086', 'NguyenHa', 'Pham', 'Female', '2005-10-25', 'student86@university.edu', '0913000086', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(135, 107, 'SV087', 'PhuongNam', 'Vu', 'Male', '2006-01-05', 'student87@university.edu', '0913000087', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(136, 108, 'SV088', 'QuynhChi', 'Do', 'Female', '2006-02-20', 'student88@university.edu', '0913000088', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(137, 109, 'SV089', 'ThanhDat', 'Hoang', 'Male', '2006-03-15', 'student89@university.edu', '0913000089', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(138, 110, 'SV090', 'ThuyDuong', 'Nguyen', 'Female', '2006-04-10', 'student90@university.edu', '0913000090', 3, NULL, 'Studying', '2026-01-21 00:10:36'),
(139, 111, 'SV091', 'VanKiet', 'Tran', 'Male', '2008-08-05', 'student91@university.edu', '0913000091', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(140, 112, 'SV092', 'YenNhi', 'Le', 'Female', '2008-08-20', 'student92@university.edu', '0913000092', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(141, 113, 'SV093', 'AnhQuan', 'Pham', 'Male', '2008-09-05', 'student93@university.edu', '0913000093', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(142, 114, 'SV094', 'BaoNgoc', 'Vu', 'Female', '2008-09-20', 'student94@university.edu', '0913000094', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(143, 115, 'SV095', 'ChiBao', 'Do', 'Male', '2008-10-05', 'student95@university.edu', '0913000095', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(144, 116, 'SV096', 'AnKhang', 'Nguyen', 'Male', '2008-10-20', 'student96@university.edu', '0914000096', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(145, 117, 'SV097', 'BaoHan', 'Tran', 'Female', '2008-11-05', 'student97@university.edu', '0914000097', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(146, 118, 'SV098', 'CamTu', 'Le', 'Male', '2008-11-20', 'student98@university.edu', '0914000098', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(147, 119, 'SV099', 'DangQuang', 'Pham', 'Female', '2008-12-05', 'student99@university.edu', '0914000099', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(148, 120, 'SV100', 'GiaHan', 'Vu', 'Male', '2008-12-20', 'student100@university.edu', '0914000100', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(149, 121, 'SV101', 'HoaiNam', 'Do', 'Female', '2009-01-05', 'student101@university.edu', '0914000101', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(150, 122, 'SV102', 'KieuAnh', 'Hoang', 'Male', '2009-01-20', 'student102@university.edu', '0914000102', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(151, 123, 'SV103', 'LamKhanh', 'Nguyen', 'Female', '2009-02-05', 'student103@university.edu', '0914000103', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(152, 124, 'SV104', 'MinhChau', 'Tran', 'Male', '2009-02-20', 'student104@university.edu', '0914000104', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(153, 125, 'SV105', 'NguyenPhuc', 'Le', 'Female', '2009-03-05', 'student105@university.edu', '0914000105', 4, NULL, 'Studying', '2026-01-21 00:10:47'),
(154, 126, 'SV106', 'PhuongAnh', 'Pham', 'Male', '2009-03-20', 'student106@university.edu', '0914000106', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(155, 127, 'SV107', 'QuocHung', 'Vu', 'Female', '2009-04-05', 'student107@university.edu', '0914000107', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(156, 128, 'SV108', 'ThanhTung', 'Do', 'Male', '2009-04-20', 'student108@university.edu', '0914000108', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(157, 129, 'SV109', 'ThuyLinh', 'Hoang', 'Female', '2009-05-05', 'student109@university.edu', '0914000109', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(158, 130, 'SV110', 'VanAnh', 'Nguyen', 'Male', '2009-05-20', 'student110@university.edu', '0914000110', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(159, 131, 'SV111', 'XuanMai', 'Tran', 'Female', '2009-06-05', 'student111@university.edu', '0914000111', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(160, 132, 'SV112', 'YenVy', 'Le', 'Male', '2009-06-20', 'student112@university.edu', '0914000112', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(161, 133, 'SV113', 'AnhTuan', 'Pham', 'Female', '2009-07-05', 'student113@university.edu', '0914000113', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(162, 134, 'SV114', 'BaoTran', 'Vu', 'Male', '2009-07-20', 'student114@university.edu', '0914000114', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(163, 135, 'SV115', 'ChiLan', 'Do', 'Female', '2009-08-05', 'student115@university.edu', '0914000115', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(164, 136, 'SV116', 'AnHoa', 'Nguyen', 'Female', '2009-08-20', 'student116@university.edu', '0915000116', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(165, 137, 'SV117', 'BaoMinh', 'Tran', 'Male', '2009-09-05', 'student117@university.edu', '0915000117', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(166, 138, 'SV118', 'CamVan', 'Le', 'Female', '2009-09-20', 'student118@university.edu', '0915000118', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(167, 139, 'SV119', 'DangSon', 'Pham', 'Male', '2009-10-05', 'student119@university.edu', '0915000119', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(168, 140, 'SV120', 'GiaHuy', 'Vu', 'Female', '2009-10-20', 'student120@university.edu', '0915000120', 4, NULL, 'Studying', '2026-01-21 00:11:04'),
(169, 141, 'SV121', 'HoangLam', 'Do', 'Male', '2006-11-05', 'student121@university.edu', '0915000121', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(170, 142, 'SV122', 'KieuMy', 'Hoang', 'Female', '2006-01-10', 'student122@university.edu', '0915000122', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(171, 143, 'SV123', 'LanHuong', 'Nguyen', 'Male', '2006-02-15', 'student123@university.edu', '0915000123', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(172, 144, 'SV124', 'MinhTri', 'Tran', 'Female', '2006-03-05', 'student124@university.edu', '0915000124', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(173, 145, 'SV125', 'NgocAnh', 'Le', 'Male', '2006-03-20', 'student125@university.edu', '0915000125', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(174, 146, 'SV126', 'PhuongThao', 'Pham', 'Female', '2006-04-10', 'student126@university.edu', '0915000126', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(175, 147, 'SV127', 'QuangVinh', 'Vu', 'Male', '2006-04-25', 'student127@university.edu', '0915000127', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(176, 148, 'SV128', 'ThanhHa', 'Do', 'Female', '2006-05-12', 'student128@university.edu', '0915000128', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(177, 149, 'SV129', 'ThuyTien', 'Hoang', 'Male', '2006-05-28', 'student129@university.edu', '0915000129', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(178, 150, 'SV130', 'VanLong', 'Nguyen', 'Female', '2006-06-15', 'student130@university.edu', '0915000130', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(179, 151, 'SV131', 'XuanPhuc', 'Tran', 'Male', '2006-07-02', 'student131@university.edu', '0915000131', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(180, 152, 'SV132', 'YenHoa', 'Le', 'Female', '2006-07-18', 'student132@university.edu', '0915000132', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(181, 153, 'SV133', 'AnhThu', 'Pham', 'Male', '2006-08-05', 'student133@university.edu', '0915000133', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(182, 154, 'SV134', 'BaoNguyen', 'Vu', 'Female', '2006-08-22', 'student134@university.edu', '0915000134', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(183, 155, 'SV135', 'ChiMai', 'Do', 'Male', '2006-09-10', 'student135@university.edu', '0915000135', 5, NULL, 'Studying', '2026-01-21 00:11:16'),
(184, 156, 'SV136', 'AnVy', 'Nguyen', 'Female', '2006-06-20', 'student136@university.edu', '0916000136', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(185, 157, 'SV137', 'BaoLam', 'Tran', 'Male', '2006-01-05', 'student137@university.edu', '0916000137', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(186, 158, 'SV138', 'CamAnh', 'Le', 'Female', '2006-02-10', 'student138@university.edu', '0916000138', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(187, 159, 'SV139', 'DangHuy', 'Pham', 'Male', '2006-03-15', 'student139@university.edu', '0916000139', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(188, 160, 'SV140', 'GiaThinh', 'Vu', 'Female', '2006-04-20', 'student140@university.edu', '0916000140', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(189, 161, 'SV141', 'HoaiThu', 'Do', 'Male', '2006-05-05', 'student141@university.edu', '0916000141', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(190, 162, 'SV142', 'KhanhLinh', 'Hoang', 'Female', '2006-06-10', 'student142@university.edu', '0916000142', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(191, 163, 'SV143', 'LanAnh', 'Nguyen', 'Male', '2006-07-05', 'student143@university.edu', '0916000143', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(192, 164, 'SV144', 'MinhQuan', 'Tran', 'Female', '2006-08-20', 'student144@university.edu', '0916000144', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(193, 165, 'SV145', 'NgocHa', 'Le', 'Male', '2006-09-12', 'student145@university.edu', '0916000145', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(194, 166, 'SV146', 'PhuongVy', 'Pham', 'Female', '2006-10-25', 'student146@university.edu', '0916000146', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(195, 167, 'SV147', 'QuangAnh', 'Vu', 'Male', '2006-11-05', 'student147@university.edu', '0916000147', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(196, 168, 'SV148', 'ThanhHa', 'Do', 'Female', '2006-11-20', 'student148@university.edu', '0916000148', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(197, 169, 'SV149', 'ThuyTrang', 'Hoang', 'Male', '2006-12-05', 'student149@university.edu', '0916000149', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(198, 170, 'SV150', 'VanNhi', 'Nguyen', 'Female', '2006-12-20', 'student150@university.edu', '0916000150', 5, NULL, 'Studying', '2026-01-21 00:11:28'),
(199, 171, 'SV151', 'XuanBach', 'Tran', 'Male', '2011-02-05', 'student151@university.edu', '0916000151', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(200, 172, 'SV152', 'YenNgoc', 'Le', 'Female', '2005-01-10', 'student152@university.edu', '0916000152', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(201, 173, 'SV153', 'AnhKiet', 'Pham', 'Male', '2005-02-15', 'student153@university.edu', '0916000153', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(202, 174, 'SV154', 'BaoChau', 'Vu', 'Female', '2005-03-05', 'student154@university.edu', '0916000154', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(203, 175, 'SV155', 'ChiThanh', 'Do', 'Male', '2005-03-20', 'student155@university.edu', '0916000155', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(204, 176, 'SV156', 'AnDuong', 'Nguyen', 'Male', '2005-04-05', 'student156@university.edu', '0917000156', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(205, 177, 'SV157', 'BaoAnh', 'Tran', 'Female', '2005-04-20', 'student157@university.edu', '0917000157', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(206, 178, 'SV158', 'CamLy', 'Le', 'Male', '2005-05-05', 'student158@university.edu', '0917000158', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(207, 179, 'SV159', 'DangKhanh', 'Pham', 'Female', '2005-05-20', 'student159@university.edu', '0917000159', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(208, 180, 'SV160', 'GiaBao', 'Vu', 'Male', '2005-06-05', 'student160@university.edu', '0917000160', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(209, 181, 'SV161', 'HoaiPhuong', 'Do', 'Female', '2005-06-20', 'student161@university.edu', '0917000161', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(210, 182, 'SV162', 'KhanhNgoc', 'Hoang', 'Male', '2005-07-05', 'student162@university.edu', '0917000162', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(211, 183, 'SV163', 'LanHuong', 'Nguyen', 'Female', '2005-07-20', 'student163@university.edu', '0917000163', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(212, 184, 'SV164', 'MinhTuan', 'Tran', 'Male', '2005-08-05', 'student164@university.edu', '0917000164', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(213, 185, 'SV165', 'NgocMai', 'Le', 'Female', '2005-08-20', 'student165@university.edu', '0917000165', 6, NULL, 'Studying', '2026-01-21 00:11:40'),
(229, 186, 'SV166', 'PhuongLinh', 'Pham', 'Male', '2011-09-20', 'student166@university.edu', '0917000166', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(230, 187, 'SV167', 'QuangHuy', 'Vu', 'Female', '2005-01-05', 'student167@university.edu', '0917000167', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(231, 188, 'SV168', 'ThanhBinh', 'Do', 'Male', '2005-01-20', 'student168@university.edu', '0917000168', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(232, 189, 'SV169', 'ThuyDuong', 'Hoang', 'Female', '2005-02-05', 'student169@university.edu', '0917000169', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(233, 190, 'SV170', 'VanPhong', 'Nguyen', 'Male', '2005-02-20', 'student170@university.edu', '0917000170', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(234, 191, 'SV171', 'XuanThao', 'Tran', 'Female', '2005-03-05', 'student171@university.edu', '0917000171', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(235, 192, 'SV172', 'YenNhi', 'Le', 'Male', '2005-03-20', 'student172@university.edu', '0917000172', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(236, 193, 'SV173', 'AnhVu', 'Pham', 'Female', '2005-04-05', 'student173@university.edu', '0917000173', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(237, 194, 'SV174', 'BaoNgoc', 'Vu', 'Male', '2005-04-20', 'student174@university.edu', '0917000174', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(238, 195, 'SV175', 'ChiBao', 'Do', 'Female', '2005-05-05', 'student175@university.edu', '0917000175', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(239, 196, 'SV176', 'BaLuong', 'Nguyen', 'Female', '2005-05-20', 'student176@university.edu', '0917000176', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(240, 197, 'SV177', 'BaoTrung', 'Tran', 'Male', '2005-06-05', 'student177@university.edu', '0918000177', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(241, 198, 'SV178', 'CamNhi', 'Le', 'Female', '2005-06-20', 'student178@university.edu', '0918000178', 6, NULL, 'Studying', '2026-01-21 00:12:10'),
(242, 199, 'SV179', 'DangPhat', 'Pham', 'Male', '2005-07-05', 'student179@university.edu', '0918000179', 6, NULL, 'Studying', '2026-01-21 00:12:10');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `subject_id` int(11) NOT NULL,
  `subject_code` varchar(10) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `credit_hours` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `prerequisite_code` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`subject_id`, `subject_code`, `subject_name`, `credit_hours`, `description`, `prerequisite_code`) VALUES
(1, 'CNTT101', 'Nhập môn Công nghệ thông tin', 3, 'Giới thiệu tổng quan về CNTT', NULL),
(2, 'CNTT201', 'Lập trình Java', 4, 'Môn học về lập trình hướng đối tượng với Java', 'CNTT101'),
(3, 'CNTT301', 'Cơ sở dữ liệu', 3, 'Nguyên lý thiết kế và quản trị cơ sở dữ liệu', 'CNTT101'),
(4, 'QTKD101', 'Nguyên lý quản trị', 3, 'Khái niệm và nguyên lý cơ bản của quản trị', NULL),
(5, 'QTKD201', 'Marketing căn bản', 3, 'Các khái niệm và chiến lược marketing', 'QTKD101'),
(6, 'QTKD301', 'Quản trị nhân sự', 3, 'Nguyên lý và kỹ năng quản trị nguồn nhân lực', 'QTKD101'),
(7, 'ENGL101', 'Tiếng Anh cơ bản', 3, 'Môn học tiếng Anh dành cho người mới bắt đầu', NULL),
(8, 'ENGL201', 'Ngữ pháp tiếng Anh nâng cao', 3, 'Hệ thống ngữ pháp nâng cao', 'ENGL101'),
(9, 'ENGL301', 'Văn hóa Anh - Mỹ', 2, 'Giới thiệu văn hóa và xã hội Anh - Mỹ', 'ENGL101'),
(10, 'KT101', 'Nguyên lý kế toán', 3, 'Khái niệm và nguyên lý cơ bản của kế toán', NULL),
(11, 'KT201', 'Kế toán tài chính', 4, 'Nguyên lý kế toán tài chính doanh nghiệp', 'KT101'),
(12, 'KT301', 'Kiểm toán căn bản', 3, 'Nguyên lý và quy trình kiểm toán', 'KT201'),
(13, 'TCNH101', 'Nguyên lý tài chính', 3, 'Khái niệm và nguyên lý cơ bản về tài chính', NULL),
(14, 'TCNH201', 'Ngân hàng thương mại', 3, 'Hoạt động và quản trị ngân hàng thương mại', 'TCNH101'),
(15, 'TCNH301', 'Đầu tư tài chính', 3, 'Nguyên lý và kỹ năng đầu tư tài chính', 'TCNH101'),
(16, 'SP101', 'Tâm lý học giáo dục', 3, 'Khái niệm và nguyên lý tâm lý học trong giáo dục', NULL),
(17, 'SP201', 'Phương pháp giảng dạy', 3, 'Các phương pháp giảng dạy hiện đại', 'SP101'),
(18, 'SP301', 'Quản lý lớp học', 2, 'Kỹ năng quản lý lớp học hiệu quả', 'SP101');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_auth`
--

CREATE TABLE `two_factor_auth` (
  `user_id` int(11) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `enabled` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `last_login` datetime DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `locked_until` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `is_active`, `created_at`, `last_login`, `failed_attempts`, `locked_until`) VALUES
(1, 'superadmin', 'superadmin@university.edu', 'hash_superadmin', 1, '2026-01-20 23:51:36', NULL, 0, NULL),
(2, 'gv01', 'teacher01@university.edu', 'hash_teacher01', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(3, 'gv02', 'teacher02@university.edu', 'hash_teacher02', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(4, 'gv03', 'teacher03@university.edu', 'hash_teacher03', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(5, 'gv04', 'teacher04@university.edu', 'hash_teacher04', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(6, 'gv05', 'teacher05@university.edu', 'hash_teacher05', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(7, 'gv06', 'teacher06@university.edu', 'hash_teacher06', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(8, 'gv07', 'teacher07@university.edu', 'hash_teacher07', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(9, 'gv08', 'teacher08@university.edu', 'hash_teacher08', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(10, 'gv09', 'teacher09@university.edu', 'hash_teacher09', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(11, 'gv10', 'teacher10@university.edu', 'hash_teacher10', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(12, 'gv11', 'teacher11@university.edu', 'hash_teacher11', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(13, 'gv12', 'teacher12@university.edu', 'hash_teacher12', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(14, 'gv13', 'teacher13@university.edu', 'hash_teacher13', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(15, 'gv14', 'teacher14@university.edu', 'hash_teacher14', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(16, 'gv15', 'teacher15@university.edu', 'hash_teacher15', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(17, 'gv16', 'teacher16@university.edu', 'hash_teacher16', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(18, 'gv17', 'teacher17@university.edu', 'hash_teacher17', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(19, 'gv18', 'teacher18@university.edu', 'hash_teacher18', 1, '2026-01-20 23:52:16', NULL, 0, NULL),
(20, 'sv01', 'student01@university.edu', 'hash_student01', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(21, 'sv02', 'student02@university.edu', 'hash_student02', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(22, 'sv03', 'student03@university.edu', 'hash_student03', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(23, 'sv04', 'student04@university.edu', 'hash_student04', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(24, 'sv05', 'student05@university.edu', 'hash_student05', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(25, 'sv06', 'student06@university.edu', 'hash_student06', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(26, 'sv07', 'student07@university.edu', 'hash_student07', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(27, 'sv08', 'student08@university.edu', 'hash_student08', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(28, 'sv09', 'student09@university.edu', 'hash_student09', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(29, 'sv10', 'student10@university.edu', 'hash_student10', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(30, 'sv11', 'student11@university.edu', 'hash_student11', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(31, 'sv12', 'student12@university.edu', 'hash_student12', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(32, 'sv13', 'student13@university.edu', 'hash_student13', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(33, 'sv14', 'student14@university.edu', 'hash_student14', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(34, 'sv15', 'student15@university.edu', 'hash_student15', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(35, 'sv16', 'student16@university.edu', 'hash_student16', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(36, 'sv17', 'student17@university.edu', 'hash_student17', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(37, 'sv18', 'student18@university.edu', 'hash_student18', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(38, 'sv19', 'student19@university.edu', 'hash_student19', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(39, 'sv20', 'student20@university.edu', 'hash_student20', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(40, 'sv21', 'student21@university.edu', 'hash_student21', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(41, 'sv22', 'student22@university.edu', 'hash_student22', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(42, 'sv23', 'student23@university.edu', 'hash_student23', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(43, 'sv24', 'student24@university.edu', 'hash_student24', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(44, 'sv25', 'student25@university.edu', 'hash_student25', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(45, 'sv26', 'student26@university.edu', 'hash_student26', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(46, 'sv27', 'student27@university.edu', 'hash_student27', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(47, 'sv28', 'student28@university.edu', 'hash_student28', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(48, 'sv29', 'student29@university.edu', 'hash_student29', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(49, 'sv30', 'student30@university.edu', 'hash_student30', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(50, 'sv31', 'student31@university.edu', 'hash_student31', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(51, 'sv32', 'student32@university.edu', 'hash_student32', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(52, 'sv33', 'student33@university.edu', 'hash_student33', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(53, 'sv34', 'student34@university.edu', 'hash_student34', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(54, 'sv35', 'student35@university.edu', 'hash_student35', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(55, 'sv36', 'student36@university.edu', 'hash_student36', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(56, 'sv37', 'student37@university.edu', 'hash_student37', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(57, 'sv38', 'student38@university.edu', 'hash_student38', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(58, 'sv39', 'student39@university.edu', 'hash_student39', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(59, 'sv40', 'student40@university.edu', 'hash_student40', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(60, 'sv41', 'student41@university.edu', 'hash_student41', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(61, 'sv42', 'student42@university.edu', 'hash_student42', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(62, 'sv43', 'student43@university.edu', 'hash_student43', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(63, 'sv44', 'student44@university.edu', 'hash_student44', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(64, 'sv45', 'student45@university.edu', 'hash_student45', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(65, 'sv46', 'student46@university.edu', 'hash_student46', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(66, 'sv47', 'student47@university.edu', 'hash_student47', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(67, 'sv48', 'student48@university.edu', 'hash_student48', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(68, 'sv49', 'student49@university.edu', 'hash_student49', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(69, 'sv50', 'student50@university.edu', 'hash_student50', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(70, 'sv51', 'student51@university.edu', 'hash_student51', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(71, 'sv52', 'student52@university.edu', 'hash_student52', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(72, 'sv53', 'student53@university.edu', 'hash_student53', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(73, 'sv54', 'student54@university.edu', 'hash_student54', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(74, 'sv55', 'student55@university.edu', 'hash_student55', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(75, 'sv56', 'student56@university.edu', 'hash_student56', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(76, 'sv57', 'student57@university.edu', 'hash_student57', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(77, 'sv58', 'student58@university.edu', 'hash_student58', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(78, 'sv59', 'student59@university.edu', 'hash_student59', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(79, 'sv60', 'student60@university.edu', 'hash_student60', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(80, 'sv61', 'student61@university.edu', 'hash_student61', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(81, 'sv62', 'student62@university.edu', 'hash_student62', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(82, 'sv63', 'student63@university.edu', 'hash_student63', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(83, 'sv64', 'student64@university.edu', 'hash_student64', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(84, 'sv65', 'student65@university.edu', 'hash_student65', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(85, 'sv66', 'student66@university.edu', 'hash_student66', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(86, 'sv67', 'student67@university.edu', 'hash_student67', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(87, 'sv68', 'student68@university.edu', 'hash_student68', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(88, 'sv69', 'student69@university.edu', 'hash_student69', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(89, 'sv70', 'student70@university.edu', 'hash_student70', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(90, 'sv71', 'student71@university.edu', 'hash_student71', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(91, 'sv72', 'student72@university.edu', 'hash_student72', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(92, 'sv73', 'student73@university.edu', 'hash_student73', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(93, 'sv74', 'student74@university.edu', 'hash_student74', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(94, 'sv75', 'student75@university.edu', 'hash_student75', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(95, 'sv76', 'student76@university.edu', 'hash_student76', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(96, 'sv77', 'student77@university.edu', 'hash_student77', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(97, 'sv78', 'student78@university.edu', 'hash_student78', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(98, 'sv79', 'student79@university.edu', 'hash_student79', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(99, 'sv80', 'student80@university.edu', 'hash_student80', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(100, 'sv81', 'student81@university.edu', 'hash_student81', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(101, 'sv82', 'student82@university.edu', 'hash_student82', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(102, 'sv83', 'student83@university.edu', 'hash_student83', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(103, 'sv84', 'student84@university.edu', 'hash_student84', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(104, 'sv85', 'student85@university.edu', 'hash_student85', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(105, 'sv86', 'student86@university.edu', 'hash_student86', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(106, 'sv87', 'student87@university.edu', 'hash_student87', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(107, 'sv88', 'student88@university.edu', 'hash_student88', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(108, 'sv89', 'student89@university.edu', 'hash_student89', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(109, 'sv90', 'student90@university.edu', 'hash_student90', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(110, 'sv91', 'student91@university.edu', 'hash_student91', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(111, 'sv92', 'student92@university.edu', 'hash_student92', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(112, 'sv93', 'student93@university.edu', 'hash_student93', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(113, 'sv94', 'student94@university.edu', 'hash_student94', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(114, 'sv95', 'student95@university.edu', 'hash_student95', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(115, 'sv96', 'student96@university.edu', 'hash_student96', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(116, 'sv97', 'student97@university.edu', 'hash_student97', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(117, 'sv98', 'student98@university.edu', 'hash_student98', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(118, 'sv99', 'student99@university.edu', 'hash_student99', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(119, 'sv100', 'student100@university.edu', 'hash_student100', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(120, 'sv101', 'student101@university.edu', 'hash_student101', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(121, 'sv102', 'student102@university.edu', 'hash_student102', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(122, 'sv103', 'student103@university.edu', 'hash_student103', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(123, 'sv104', 'student104@university.edu', 'hash_student104', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(124, 'sv105', 'student105@university.edu', 'hash_student105', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(125, 'sv106', 'student106@university.edu', 'hash_student106', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(126, 'sv107', 'student107@university.edu', 'hash_student107', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(127, 'sv108', 'student108@university.edu', 'hash_student108', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(128, 'sv109', 'student109@university.edu', 'hash_student109', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(129, 'sv110', 'student110@university.edu', 'hash_student110', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(130, 'sv111', 'student111@university.edu', 'hash_student111', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(131, 'sv112', 'student112@university.edu', 'hash_student112', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(132, 'sv113', 'student113@university.edu', 'hash_student113', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(133, 'sv114', 'student114@university.edu', 'hash_student114', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(134, 'sv115', 'student115@university.edu', 'hash_student115', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(135, 'sv116', 'student116@university.edu', 'hash_student116', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(136, 'sv117', 'student117@university.edu', 'hash_student117', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(137, 'sv118', 'student118@university.edu', 'hash_student118', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(138, 'sv119', 'student119@university.edu', 'hash_student119', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(139, 'sv120', 'student120@university.edu', 'hash_student120', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(140, 'sv121', 'student121@university.edu', 'hash_student121', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(141, 'sv122', 'student122@university.edu', 'hash_student122', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(142, 'sv123', 'student123@university.edu', 'hash_student123', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(143, 'sv124', 'student124@university.edu', 'hash_student124', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(144, 'sv125', 'student125@university.edu', 'hash_student125', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(145, 'sv126', 'student126@university.edu', 'hash_student126', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(146, 'sv127', 'student127@university.edu', 'hash_student127', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(147, 'sv128', 'student128@university.edu', 'hash_student128', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(148, 'sv129', 'student129@university.edu', 'hash_student129', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(149, 'sv130', 'student130@university.edu', 'hash_student130', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(150, 'sv131', 'student131@university.edu', 'hash_student131', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(151, 'sv132', 'student132@university.edu', 'hash_student132', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(152, 'sv133', 'student133@university.edu', 'hash_student133', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(153, 'sv134', 'student134@university.edu', 'hash_student134', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(154, 'sv135', 'student135@university.edu', 'hash_student135', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(155, 'sv136', 'student136@university.edu', 'hash_student136', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(156, 'sv137', 'student137@university.edu', 'hash_student137', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(157, 'sv138', 'student138@university.edu', 'hash_student138', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(158, 'sv139', 'student139@university.edu', 'hash_student139', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(159, 'sv140', 'student140@university.edu', 'hash_student140', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(160, 'sv141', 'student141@university.edu', 'hash_student141', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(161, 'sv142', 'student142@university.edu', 'hash_student142', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(162, 'sv143', 'student143@university.edu', 'hash_student143', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(163, 'sv144', 'student144@university.edu', 'hash_student144', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(164, 'sv145', 'student145@university.edu', 'hash_student145', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(165, 'sv146', 'student146@university.edu', 'hash_student146', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(166, 'sv147', 'student147@university.edu', 'hash_student147', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(167, 'sv148', 'student148@university.edu', 'hash_student148', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(168, 'sv149', 'student149@university.edu', 'hash_student149', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(169, 'sv150', 'student150@university.edu', 'hash_student150', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(170, 'sv151', 'student151@university.edu', 'hash_student151', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(171, 'sv152', 'student152@university.edu', 'hash_student152', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(172, 'sv153', 'student153@university.edu', 'hash_student153', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(173, 'sv154', 'student154@university.edu', 'hash_student154', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(174, 'sv155', 'student155@university.edu', 'hash_student155', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(175, 'sv156', 'student156@university.edu', 'hash_student156', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(176, 'sv157', 'student157@university.edu', 'hash_student157', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(177, 'sv158', 'student158@university.edu', 'hash_student158', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(178, 'sv159', 'student159@university.edu', 'hash_student159', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(179, 'sv160', 'student160@university.edu', 'hash_student160', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(180, 'sv161', 'student161@university.edu', 'hash_student161', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(181, 'sv162', 'student162@university.edu', 'hash_student162', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(182, 'sv163', 'student163@university.edu', 'hash_student163', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(183, 'sv164', 'student164@university.edu', 'hash_student164', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(184, 'sv165', 'student165@university.edu', 'hash_student165', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(185, 'sv166', 'student166@university.edu', 'hash_student166', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(186, 'sv167', 'student167@university.edu', 'hash_student167', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(187, 'sv168', 'student168@university.edu', 'hash_student168', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(188, 'sv169', 'student169@university.edu', 'hash_student169', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(189, 'sv170', 'student170@university.edu', 'hash_student170', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(190, 'sv171', 'student171@university.edu', 'hash_student171', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(191, 'sv172', 'student172@university.edu', 'hash_student172', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(192, 'sv173', 'student173@university.edu', 'hash_student173', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(193, 'sv174', 'student174@university.edu', 'hash_student174', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(194, 'sv175', 'student175@university.edu', 'hash_student175', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(195, 'sv176', 'student176@university.edu', 'hash_student176', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(196, 'sv177', 'student177@university.edu', 'hash_student177', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(197, 'sv178', 'student178@university.edu', 'hash_student178', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(198, 'sv179', 'student179@university.edu', 'hash_student179', 1, '2026-01-20 23:53:03', NULL, 0, NULL),
(199, 'sv180', 'student180@university.edu', 'hash_student180', 1, '2026-01-20 23:53:03', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`user_id`, `role_id`) VALUES
(1, 1),
(2, 3),
(3, 3),
(4, 3),
(5, 3),
(6, 3),
(7, 3),
(8, 3),
(9, 3),
(10, 3),
(11, 3),
(12, 3),
(13, 3),
(14, 3),
(15, 3),
(16, 3),
(17, 3),
(18, 3),
(19, 3),
(20, 4),
(21, 4),
(22, 4),
(23, 4),
(24, 4),
(25, 4),
(26, 4),
(27, 4),
(28, 4),
(29, 4),
(30, 4),
(31, 4),
(32, 4),
(33, 4),
(34, 4),
(35, 4),
(36, 4),
(37, 4),
(38, 4),
(39, 4),
(40, 4),
(41, 4),
(42, 4),
(43, 4),
(44, 4),
(45, 4),
(46, 4),
(47, 4),
(48, 4),
(49, 4),
(50, 4),
(51, 4),
(52, 4),
(53, 4),
(54, 4),
(55, 4),
(56, 4),
(57, 4),
(58, 4),
(59, 4),
(60, 4),
(61, 4),
(62, 4),
(63, 4),
(64, 4),
(65, 4),
(66, 4),
(67, 4),
(68, 4),
(69, 4),
(70, 4),
(71, 4),
(72, 4),
(73, 4),
(74, 4),
(75, 4),
(76, 4),
(77, 4),
(78, 4),
(79, 4),
(80, 4),
(81, 4),
(82, 4),
(83, 4),
(84, 4),
(85, 4),
(86, 4),
(87, 4),
(88, 4),
(89, 4),
(90, 4),
(91, 4),
(92, 4),
(93, 4),
(94, 4),
(95, 4),
(96, 4),
(97, 4),
(98, 4),
(99, 4),
(100, 4),
(101, 4),
(102, 4),
(103, 4),
(104, 4),
(105, 4),
(106, 4),
(107, 4),
(108, 4),
(109, 4),
(110, 4),
(111, 4),
(112, 4),
(113, 4),
(114, 4),
(115, 4),
(116, 4),
(117, 4),
(118, 4),
(119, 4),
(120, 4),
(121, 4),
(122, 4),
(123, 4),
(124, 4),
(125, 4),
(126, 4),
(127, 4),
(128, 4),
(129, 4),
(130, 4),
(131, 4),
(132, 4),
(133, 4),
(134, 4),
(135, 4),
(136, 4),
(137, 4),
(138, 4),
(139, 4),
(140, 4),
(141, 4),
(142, 4),
(143, 4),
(144, 4),
(145, 4),
(146, 4),
(147, 4),
(148, 4),
(149, 4),
(150, 4),
(151, 4),
(152, 4),
(153, 4),
(154, 4),
(155, 4),
(156, 4),
(157, 4),
(158, 4),
(159, 4),
(160, 4),
(161, 4),
(162, 4),
(163, 4),
(164, 4),
(165, 4),
(166, 4),
(167, 4),
(168, 4),
(169, 4),
(170, 4),
(171, 4),
(172, 4),
(173, 4),
(174, 4),
(175, 4),
(176, 4),
(177, 4),
(178, 4),
(179, 4),
(180, 4),
(181, 4),
(182, 4),
(183, 4),
(184, 4),
(185, 4),
(186, 4),
(187, 4),
(188, 4),
(189, 4),
(190, 4),
(191, 4),
(192, 4),
(193, 4),
(194, 4),
(195, 4),
(196, 4),
(197, 4),
(198, 4),
(199, 4);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_avg_score_by_subject`
-- (See below for the actual view)
--
CREATE TABLE `view_avg_score_by_subject` (
`subject_name` varchar(100)
,`avg_score` decimal(9,6)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_students_by_faculty`
-- (See below for the actual view)
--
CREATE TABLE `view_students_by_faculty` (
`faculty_name` varchar(100)
,`total_students` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_student_results`
-- (See below for the actual view)
--
CREATE TABLE `view_student_results` (
`student_code` varchar(10)
,`full_name` varchar(151)
,`subject_name` varchar(100)
,`class_code` varchar(20)
,`semester` enum('Spring','Summer','Fall')
,`year` year(4)
,`score` decimal(5,2)
,`grade_letter` varchar(2)
);

-- --------------------------------------------------------

--
-- Structure for view `view_avg_score_by_subject`
--
DROP TABLE IF EXISTS `view_avg_score_by_subject`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_avg_score_by_subject`  AS SELECT `sub`.`subject_name` AS `subject_name`, avg(`g`.`score`) AS `avg_score` FROM (((`grades` `g` join `enrollments` `e` on(`g`.`enrollment_id` = `e`.`enrollment_id`)) join `classes` `c` on(`e`.`class_id` = `c`.`class_id`)) join `subjects` `sub` on(`c`.`subject_id` = `sub`.`subject_id`)) GROUP BY `sub`.`subject_id` ;

-- --------------------------------------------------------

--
-- Structure for view `view_students_by_faculty`
--
DROP TABLE IF EXISTS `view_students_by_faculty`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_students_by_faculty`  AS SELECT `f`.`faculty_name` AS `faculty_name`, count(`s`.`student_id`) AS `total_students` FROM (`faculties` `f` left join `students` `s` on(`f`.`faculty_id` = `s`.`faculty_id`)) GROUP BY `f`.`faculty_id` ;

-- --------------------------------------------------------

--
-- Structure for view `view_student_results`
--
DROP TABLE IF EXISTS `view_student_results`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_student_results`  AS SELECT `s`.`student_code` AS `student_code`, concat(`s`.`last_name`,' ',`s`.`first_name`) AS `full_name`, `sub`.`subject_name` AS `subject_name`, `c`.`class_code` AS `class_code`, `c`.`semester` AS `semester`, `c`.`year` AS `year`, `g`.`score` AS `score`, `g`.`grade_letter` AS `grade_letter` FROM ((((`grades` `g` join `enrollments` `e` on(`g`.`enrollment_id` = `e`.`enrollment_id`)) join `students` `s` on(`e`.`student_id` = `s`.`student_id`)) join `classes` `c` on(`e`.`class_id` = `c`.`class_id`)) join `subjects` `sub` on(`c`.`subject_id` = `sub`.`subject_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`audit_id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_table` (`table_name`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `base_classes`
--
ALTER TABLE `base_classes`
  ADD PRIMARY KEY (`base_class_id`),
  ADD UNIQUE KEY `base_class_code` (`base_class_code`),
  ADD KEY `idx_base_classes_faculty` (`faculty_id`),
  ADD KEY `idx_base_classes_lecturer` (`lecturer_id`),
  ADD KEY `idx_base_classes_year` (`start_year`,`end_year`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`class_id`),
  ADD UNIQUE KEY `class_code` (`class_code`),
  ADD KEY `idx_classes_subject` (`subject_id`),
  ADD KEY `idx_classes_lecturer` (`lecturer_id`),
  ADD KEY `idx_classes_year_semester` (`year`,`semester`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`enrollment_id`),
  ADD UNIQUE KEY `student_id` (`student_id`,`class_id`),
  ADD KEY `idx_enrollments_student` (`student_id`),
  ADD KEY `idx_enrollments_class` (`class_id`),
  ADD KEY `idx_enrollments_status` (`status`),
  ADD KEY `idx_view_results_enroll` (`enrollment_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`faculty_id`),
  ADD UNIQUE KEY `faculty_code` (`faculty_code`),
  ADD UNIQUE KEY `faculty_name` (`faculty_name`);

--
-- Indexes for table `faculty_addresses`
--
ALTER TABLE `faculty_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_faculty_addresses_code` (`faculty_code`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`grade_id`),
  ADD UNIQUE KEY `enrollment_id` (`enrollment_id`),
  ADD KEY `idx_grades_score` (`score`),
  ADD KEY `idx_grades_letter` (`grade_letter`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`lecturer_id`),
  ADD UNIQUE KEY `lecturer_code` (`lecturer_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_lecturers_faculty` (`faculty_id`),
  ADD KEY `idx_lecturers_user` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_password_resets_user` (`user_id`),
  ADD KEY `idx_password_resets_token` (`token`),
  ADD KEY `idx_password_resets_expires` (`expires_at`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

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
  ADD KEY `idx_role_permissions_role` (`role_id`),
  ADD KEY `idx_role_permissions_permission` (`permission_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `student_code` (`student_code`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `idx_students_faculty` (`faculty_id`),
  ADD KEY `idx_view_results_student` (`student_id`),
  ADD KEY `idx_students_base_class` (`base_class_id`),
  ADD KEY `idx_students_status` (`status`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`subject_id`),
  ADD UNIQUE KEY `subject_code` (`subject_code`),
  ADD KEY `idx_subjects_prerequisite` (`prerequisite_code`);

--
-- Indexes for table `two_factor_auth`
--
ALTER TABLE `two_factor_auth`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `idx_2fa_enabled` (`enabled`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_users_is_active` (`is_active`),
  ADD KEY `idx_users_created_at` (`created_at`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `idx_user_roles_user` (`user_id`),
  ADD KEY `idx_user_roles_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `audit_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `base_classes`
--
ALTER TABLE `base_classes`
  MODIFY `base_class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `class_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `enrollment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `faculty_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `faculty_addresses`
--
ALTER TABLE `faculty_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `lecturer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=243;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `subject_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `base_classes`
--
ALTER TABLE `base_classes`
  ADD CONSTRAINT `base_classes_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `base_classes_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`subject_id`),
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`lecturer_id`);

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`class_id`);

--
-- Constraints for table `faculty_addresses`
--
ALTER TABLE `faculty_addresses`
  ADD CONSTRAINT `faculty_addresses_ibfk_1` FOREIGN KEY (`faculty_code`) REFERENCES `faculties` (`faculty_code`);

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`enrollment_id`);

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `lecturers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD CONSTRAINT `role_permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_permissions_ibfk_2` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`faculty_id`),
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_3` FOREIGN KEY (`base_class_id`) REFERENCES `base_classes` (`base_class_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_prerequisite` FOREIGN KEY (`prerequisite_code`) REFERENCES `subjects` (`subject_code`);

--
-- Constraints for table `two_factor_auth`
--
ALTER TABLE `two_factor_auth`
  ADD CONSTRAINT `two_factor_auth_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
