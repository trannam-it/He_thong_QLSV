-- ============================================================
-- Migration: Thêm bảng điểm danh (attendance)
-- ============================================================

CREATE TABLE IF NOT EXISTS `attendance` (
  `attendance_id`  int          NOT NULL AUTO_INCREMENT,
  `class_id`       int          NOT NULL,
  `student_id`     int          NOT NULL,
  `date`           date         NOT NULL,
  `status`         enum('Present','Absent','Late','Excused')
                                COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Present',
  `note`           varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at`     datetime     DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     datetime     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`attendance_id`),
  UNIQUE KEY `uq_class_student_date` (`class_id`, `student_id`, `date`),
  KEY `idx_class_date`   (`class_id`,  `date`),
  KEY `idx_student_date` (`student_id`,`date`),
  CONSTRAINT `fk_att_class`   FOREIGN KEY (`class_id`)   REFERENCES `classes`   (`class_id`)   ON DELETE CASCADE,
  CONSTRAINT `fk_att_student` FOREIGN KEY (`student_id`) REFERENCES `students`  (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
