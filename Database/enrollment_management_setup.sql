-- ================================================================
-- Enrollment Management System Setup
-- Purpose: Enable enrollment registration period management
-- Created: 2026-03-03
-- ================================================================

-- 1. Create enrollment_registration_periods table
CREATE TABLE IF NOT EXISTS `enrollment_registration_periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `semester` varchar(20) NOT NULL COMMENT 'Spring, Summer, Fall',
  `year` int(11) NOT NULL,
  `enrollment_open` datetime NOT NULL COMMENT 'When enrollment opens',
  `enrollment_close` datetime NOT NULL COMMENT 'When enrollment closes',
  `is_active` tinyint(1) DEFAULT 1,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`period_id`),
  UNIQUE KEY `semester_year` (`semester`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Grant enrollment.view to the "student" role (lookup by code)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`)
SELECT r.id, p.id, 1
FROM `roles` r
JOIN `permissions` p ON p.code = 'enrollment.view'
WHERE r.code = 'student';

-- 3. Grant registration / cancel perms to student as well
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`)
SELECT r.id, p.id, 1
FROM `roles` r
JOIN `permissions` p ON p.code IN ('enrollment.register', 'enrollment.cancel')
WHERE r.code = 'student';

-- 4. Add enrollment management permissions for academic_admin role
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`)
SELECT r.id, p.id, 1
FROM `roles` r
JOIN `permissions` p ON p.code IN ('enrollment.view', 'enrollment.manage_period')
WHERE r.code = 'academic_admin';

-- 5. Ensure lecturer class registration permission exists and assign to lecturer role (ID 4 maybe)
-- add permission if missing
INSERT IGNORE INTO `permissions` (`group_id`, `code`, `name`, `description`, `is_system`, `created_at`)
VALUES (7, 'classes.register', 'Đăng ký dạy lớp', 'Cho phép giảng viên đăng ký dạy lớp học phần', 0, NOW());

-- assign to all lecturers (lookup by role code)
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`, `granted_by`)
SELECT r.id, p.id, 1
FROM `roles` r
JOIN `permissions` p ON p.code = 'classes.register'
WHERE r.code = 'teacher';

-- 5. Optional: Pre-populate current enrollment period (adjust as needed)
INSERT IGNORE INTO `enrollment_registration_periods` 
  (`semester`, `year`, `enrollment_open`, `enrollment_close`, `is_active`, `created_by`)
VALUES 
  ('Spring', 2026, '2026-01-15 07:00:00', '2026-02-15 23:59:59', 1, 1),
  ('Summer', 2026, '2026-04-15 07:00:00', '2026-05-15 23:59:59', 0, 1),
  ('Fall', 2026, '2026-08-15 07:00:00', '2026-09-15 23:59:59', 0, 1);

-- Verification queries:
-- SELECT * FROM `enrollment_registration_periods` WHERE is_active = 1;
-- SELECT COUNT(*) FROM `role_permissions` WHERE role_id = 3 AND permission_id = 62;
-- SELECT r.role_id, r.code, p.code FROM role_permissions rp 
--   JOIN roles r ON r.id = rp.role_id 
--   JOIN permissions p ON p.id = rp.permission_id 
--   WHERE rp.role_id = 3 AND p.code LIKE 'enrollment%';
