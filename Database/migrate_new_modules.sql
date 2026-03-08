-- =====================================================================
--  MIGRATION: Học phí, Học bổng, Ký túc xá, Thư viện
--  Chạy file này trong phpMyAdmin hoặc MySQL để tạo các bảng mới
--  Database: database_qlsv
-- =====================================================================

USE database_qlsv;

-- =====================================================================
-- 1. HỌC PHÍ (TUITION)
-- =====================================================================

-- Đơn giá tín chỉ theo học kỳ/năm
CREATE TABLE IF NOT EXISTS `tuition_settings` (
  `setting_id`       int(11)         NOT NULL AUTO_INCREMENT,
  `semester`         enum('Spring','Summer','Fall') NOT NULL,
  `year`             year(4)         NOT NULL,
  `price_per_credit` decimal(12,0)   NOT NULL DEFAULT 500000 COMMENT 'VNĐ / tín chỉ',
  `note`             varchar(255)    DEFAULT NULL,
  `created_at`       datetime        DEFAULT current_timestamp(),
  PRIMARY KEY (`setting_id`),
  UNIQUE KEY `uq_tuition_semester` (`semester`, `year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu mẫu giá tín chỉ
INSERT IGNORE INTO `tuition_settings` (`semester`, `year`, `price_per_credit`, `note`) VALUES
('Spring', '2026', 550000, 'Học kỳ 1 năm 2026'),
('Summer', '2026', 550000, 'Học kỳ hè năm 2026'),
('Fall',   '2026', 550000, 'Học kỳ 2 năm 2026'),
('Spring', '2025', 500000, 'Học kỳ 1 năm 2025'),
('Summer', '2025', 500000, 'Học kỳ hè năm 2025'),
('Fall',   '2025', 500000, 'Học kỳ 2 năm 2025');

-- Hóa đơn học phí của từng sinh viên theo học kỳ
CREATE TABLE IF NOT EXISTS `tuition_invoices` (
  `invoice_id`    int(11)       NOT NULL AUTO_INCREMENT,
  `student_id`    int(11)       NOT NULL,
  `semester`      enum('Spring','Summer','Fall') NOT NULL,
  `year`          year(4)       NOT NULL,
  `total_credits` int(11)       NOT NULL DEFAULT 0,
  `amount_due`    decimal(12,0) NOT NULL DEFAULT 0,
  `amount_paid`   decimal(12,0) NOT NULL DEFAULT 0,
  `status`        enum('Unpaid','Partial','Paid','Overdue','Exempted') DEFAULT 'Unpaid',
  `due_date`      date          DEFAULT NULL,
  `paid_at`       datetime      DEFAULT NULL,
  `note`          text          DEFAULT NULL,
  `created_at`    datetime      DEFAULT current_timestamp(),
  PRIMARY KEY (`invoice_id`),
  UNIQUE KEY `uq_invoice` (`student_id`, `semester`, `year`),
  KEY `fk_invoice_student` (`student_id`),
  CONSTRAINT `fk_invoice_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 2. HỌC BỔNG (SCHOLARSHIP)
-- =====================================================================

CREATE TABLE IF NOT EXISTS `scholarships` (
  `scholarship_id` int(11)        NOT NULL AUTO_INCREMENT,
  `name`           varchar(200)   NOT NULL,
  `description`    text           DEFAULT NULL,
  `value`          decimal(12,0)  NOT NULL DEFAULT 0 COMMENT 'VNĐ',
  `min_gpa`        decimal(5,2)   DEFAULT NULL COMMENT 'điểm tối thiểu (thang 100)',
  `max_gpa`        decimal(5,2)   DEFAULT NULL COMMENT 'điểm tối đa (thang 100) để được xét',
  `semester`       enum('Spring','Summer','Fall') NOT NULL,
  `year`           year(4)        NOT NULL,
  `quantity`       int(11)        DEFAULT NULL COMMENT 'NULL = không giới hạn',
  `deadline`       date           DEFAULT NULL,
  `is_active`      tinyint(1)     DEFAULT 1,
  `created_at`     datetime       DEFAULT current_timestamp(),
  PRIMARY KEY (`scholarship_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu mẫu học bổng
INSERT IGNORE INTO `scholarships` (`name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`) VALUES
('Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Spring', '2026', 20, '2026-03-15', 1),
('Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Spring', '2026', 50, '2026-03-15', 1),
('Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', 1500000, NULL, NULL, 'Spring', '2026', 30, '2026-03-20', 1),
('Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', 10000000, 85.00, NULL, 'Spring', '2026', 5, '2026-03-10', 1),
('Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Fall', '2025', 20, '2025-09-15', 0),
('Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Fall', '2025', 50, '2025-09-15', 0);

-- Đơn xin học bổng
CREATE TABLE IF NOT EXISTS `scholarship_applications` (
  `application_id`  int(11)     NOT NULL AUTO_INCREMENT,
  `student_id`      int(11)     NOT NULL,
  `scholarship_id`  int(11)     NOT NULL,
  `status`          enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `applied_at`      datetime    DEFAULT current_timestamp(),
  `reviewed_at`     datetime    DEFAULT NULL,
  `note`            text        DEFAULT NULL,
  PRIMARY KEY (`application_id`),
  UNIQUE KEY `uq_application` (`student_id`, `scholarship_id`),
  KEY `fk_app_student`     (`student_id`),
  KEY `fk_app_scholarship` (`scholarship_id`),
  CONSTRAINT `fk_app_student`     FOREIGN KEY (`student_id`)     REFERENCES `students`     (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_app_scholarship` FOREIGN KEY (`scholarship_id`) REFERENCES `scholarships` (`scholarship_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 3. KÝ TÚC XÁ (DORMITORY)
-- =====================================================================

CREATE TABLE IF NOT EXISTS `dormitory_rooms` (
  `room_id`         int(11)      NOT NULL AUTO_INCREMENT,
  `room_number`     varchar(20)  NOT NULL,
  `building`        varchar(50)  NOT NULL DEFAULT 'Tòa A',
  `room_type`       enum('Single','Double','Triple','Quad') NOT NULL DEFAULT 'Double',
  `price_per_month` decimal(10,0) NOT NULL DEFAULT 500000,
  `total_beds`      int(11)      NOT NULL DEFAULT 2,
  `available_beds`  int(11)      NOT NULL DEFAULT 2,
  `floor`           int(11)      DEFAULT NULL,
  `description`     text         DEFAULT NULL,
  `is_active`       tinyint(1)   DEFAULT 1,
  PRIMARY KEY (`room_id`),
  UNIQUE KEY `uq_room` (`building`, `room_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu mẫu phòng ký túc xá
INSERT IGNORE INTO `dormitory_rooms` (`room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`) VALUES
('A101', 'Tòa A', 'Double', 400000, 2, 1, 1, 'Phòng đôi tầng 1, có điều hòa'),
('A102', 'Tòa A', 'Double', 400000, 2, 2, 1, 'Phòng đôi tầng 1, có điều hòa'),
('A201', 'Tòa A', 'Quad',   300000, 4, 2, 2, 'Phòng 4 người tầng 2'),
('A202', 'Tòa A', 'Quad',   300000, 4, 4, 2, 'Phòng 4 người tầng 2'),
('A301', 'Tòa A', 'Single', 700000, 1, 0, 3, 'Phòng đơn tầng 3, tiện nghi cao'),
('A302', 'Tòa A', 'Single', 700000, 1, 1, 3, 'Phòng đơn tầng 3, tiện nghi cao'),
('B101', 'Tòa B', 'Double', 380000, 2, 2, 1, 'Phòng đôi tòa B tầng 1'),
('B102', 'Tòa B', 'Double', 380000, 2, 1, 1, 'Phòng đôi tòa B tầng 1'),
('B201', 'Tòa B', 'Triple', 350000, 3, 3, 2, 'Phòng 3 người tòa B tầng 2'),
('B202', 'Tòa B', 'Triple', 350000, 3, 0, 2, 'Phòng 3 người tòa B tầng 2'),
('C101', 'Tòa C', 'Quad',   280000, 4, 4, 1, 'Phòng 4 người tòa C giá rẻ'),
('C102', 'Tòa C', 'Quad',   280000, 4, 2, 1, 'Phòng 4 người tòa C giá rẻ');

-- Đăng ký ký túc xá
CREATE TABLE IF NOT EXISTS `dormitory_registrations` (
  `registration_id` int(11)   NOT NULL AUTO_INCREMENT,
  `student_id`      int(11)   NOT NULL,
  `room_id`         int(11)   NOT NULL,
  `start_date`      date      NOT NULL,
  `end_date`        date      DEFAULT NULL,
  `status`          enum('Pending','Active','Ended','Cancelled') DEFAULT 'Pending',
  `note`            text      DEFAULT NULL,
  `registered_at`   datetime  DEFAULT current_timestamp(),
  `updated_at`      datetime  DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`registration_id`),
  KEY `fk_dorm_student` (`student_id`),
  KEY `fk_dorm_room`    (`room_id`),
  CONSTRAINT `fk_dorm_student` FOREIGN KEY (`student_id`) REFERENCES `students`         (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dorm_room`    FOREIGN KEY (`room_id`)    REFERENCES `dormitory_rooms`  (`room_id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- 4. THƯ VIỆN (LIBRARY)
-- =====================================================================

CREATE TABLE IF NOT EXISTS `library_books` (
  `book_id`         int(11)       NOT NULL AUTO_INCREMENT,
  `title`           varchar(255)  NOT NULL,
  `author`          varchar(200)  DEFAULT NULL,
  `isbn`            varchar(20)   DEFAULT NULL UNIQUE,
  `category`        varchar(100)  DEFAULT NULL,
  `publisher`       varchar(150)  DEFAULT NULL,
  `published_year`  year(4)       DEFAULT NULL,
  `total_copies`    int(11)       NOT NULL DEFAULT 1,
  `available_copies`int(11)       NOT NULL DEFAULT 1,
  `description`     text          DEFAULT NULL,
  `cover_image`     varchar(255)  DEFAULT NULL,
  `created_at`      datetime      DEFAULT current_timestamp(),
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dữ liệu mẫu sách thư viện
INSERT IGNORE INTO `library_books` (`title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`) VALUES
('Nhập môn lập trình', 'Nguyễn Văn An', '978-604-1-001', 'Công nghệ thông tin', 'NXB ĐHQG', '2020', 5, 4, 'Giáo trình nhập môn lập trình cho sinh viên năm nhất'),
('Cấu trúc dữ liệu và giải thuật', 'Trần Thị Bình', '978-604-1-002', 'Công nghệ thông tin', 'NXB ĐHQG', '2019', 4, 3, 'Sách giáo trình về cấu trúc dữ liệu'),
('Cơ sở dữ liệu', 'Lê Văn Chi', '978-604-1-003', 'Công nghệ thông tin', 'NXB Giáo dục', '2021', 6, 5, 'Nguyên lý cơ sở dữ liệu và SQL'),
('Lập trình Java nâng cao', 'Pham Minh Dung', '978-604-1-004', 'Công nghệ thông tin', 'NXB ĐHQG', '2022', 3, 2, 'Java OOP và Design Patterns'),
('Nguyên lý quản trị kinh doanh', 'Vu Thi Hoa', '978-604-2-001', 'Quản trị kinh doanh', 'NXB Kinh tế', '2020', 5, 5, 'Giáo trình quản trị căn bản'),
('Marketing hiện đại', 'Do Quang Khanh', '978-604-2-002', 'Quản trị kinh doanh', 'NXB Kinh tế', '2021', 4, 3, 'Chiến lược và kế hoạch marketing'),
('Tiếng Anh thương mại', 'Hoang Thi Lan', '978-604-3-001', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2020', 8, 6, 'Tiếng Anh dùng trong kinh doanh'),
('Ngữ pháp tiếng Anh B1-B2', 'Nguyen Minh Thu', '978-604-3-002', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2019', 10, 8, 'Hệ thống ngữ pháp tiếng Anh từ căn bản đến nâng cao'),
('Nguyên lý kế toán', 'Tran Van Nam', '978-604-4-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2020', 6, 5, 'Giáo trình kế toán cơ bản'),
('Kế toán tài chính doanh nghiệp', 'Le Thi Oanh', '978-604-4-002', 'Kế toán - Tài chính', 'NXB Tài chính', '2021', 5, 4, 'Kế toán tài chính thực hành'),
('Nguyên lý tài chính', 'Nguyen Quang Phong', '978-604-5-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2022', 4, 4, 'Tài chính doanh nghiệp căn bản'),
('Phương pháp nghiên cứu khoa học', 'Tran Thi Quyen', '978-604-6-001', 'Khoa học - Giáo dục', 'NXB ĐHQG', '2020', 3, 3, 'Hướng dẫn nghiên cứu khoa học'),
('Tâm lý học giáo dục', 'Le Van Son', '978-604-7-001', 'Khoa học - Giáo dục', 'NXB Giáo dục', '2021', 5, 5, 'Tâm lý học ứng dụng trong giảng dạy'),
('Lịch sử Việt Nam', 'Pham Thi Thao', '978-604-8-001', 'Lịch sử - Văn hóa', 'NXB Sử học', '2018', 4, 4, 'Lịch sử Việt Nam từ cổ đại đến hiện đại'),
('Toán cao cấp tập 1', 'Vu Minh Uyen', '978-604-9-001', 'Toán học', 'NXB ĐHQG', '2020', 8, 6, 'Giải tích và đại số tuyến tính'),
('Toán cao cấp tập 2', 'Do Thi Van', '978-604-9-002', 'Toán học', 'NXB ĐHQG', '2020', 8, 7, 'Xác suất thống kê và phương trình vi phân'),
('Vật lý đại cương', 'Hoang Xuan Yen', '978-604-10-001', 'Khoa học tự nhiên', 'NXB ĐHQG', '2019', 6, 5, 'Vật lý cho sinh viên kỹ thuật'),
('Triết học Mác – Lênin', 'Nguyen Thi Bao', '978-604-11-001', 'Lý luận chính trị', 'NXB Chính trị', '2021', 10, 9, 'Giáo trình triết học cho đại học');

-- Mượn/trả sách
CREATE TABLE IF NOT EXISTS `library_borrows` (
  `borrow_id`    int(11)        NOT NULL AUTO_INCREMENT,
  `student_id`   int(11)        NOT NULL,
  `book_id`      int(11)        NOT NULL,
  `borrow_date`  date           NOT NULL DEFAULT (CURDATE()),
  `due_date`     date           NOT NULL,
  `return_date`  date           DEFAULT NULL,
  `status`       enum('Borrowed','Returned','Overdue','Lost') DEFAULT 'Borrowed',
  `fine_amount`  decimal(10,0)  NOT NULL DEFAULT 0,
  `note`         text           DEFAULT NULL,
  `created_at`   datetime       DEFAULT current_timestamp(),
  PRIMARY KEY (`borrow_id`),
  KEY `fk_borrow_student` (`student_id`),
  KEY `fk_borrow_book`    (`book_id`),
  CONSTRAINT `fk_borrow_student` FOREIGN KEY (`student_id`) REFERENCES `students`       (`student_id`) ON DELETE CASCADE,
  CONSTRAINT `fk_borrow_book`    FOREIGN KEY (`book_id`)    REFERENCES `library_books`  (`book_id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================================
-- DONE
-- SELECT 'Migration hoàn tất!' as message;
-- =====================================================================
