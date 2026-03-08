SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Fix library_books
TRUNCATE TABLE library_borrows;
TRUNCATE TABLE library_books;

INSERT INTO `library_books` (`title`, `author`, `isbn`, `category`, `publisher`, `published_year`, `total_copies`, `available_copies`, `description`) VALUES
('Nhập môn lập trình', 'Nguyễn Văn An', '978-604-1-001', 'Công nghệ thông tin', 'NXB ĐHQG', '2020', 5, 4, 'Giáo trình nhập môn lập trình cho sinh viên năm nhất'),
('Cấu trúc dữ liệu và giải thuật', 'Trần Thị Bình', '978-604-1-002', 'Công nghệ thông tin', 'NXB ĐHQG', '2019', 4, 3, 'Sách giáo trình về cấu trúc dữ liệu'),
('Cơ sở dữ liệu', 'Lê Văn Chi', '978-604-1-003', 'Công nghệ thông tin', 'NXB Giáo dục', '2021', 6, 5, 'Nguyên lý cơ sở dữ liệu và SQL'),
('Lập trình Java nâng cao', 'Phạm Minh Dũng', '978-604-1-004', 'Công nghệ thông tin', 'NXB ĐHQG', '2022', 3, 2, 'Java OOP và Design Patterns'),
('Nguyên lý quản trị kinh doanh', 'Vũ Thị Hoa', '978-604-2-001', 'Quản trị kinh doanh', 'NXB Kinh tế', '2020', 5, 5, 'Giáo trình quản trị căn bản'),
('Marketing hiện đại', 'Đỗ Quang Khánh', '978-604-2-002', 'Quản trị kinh doanh', 'NXB Kinh tế', '2021', 4, 3, 'Chiến lược và kế hoạch marketing'),
('Tiếng Anh thương mại', 'Hoàng Thị Lan', '978-604-3-001', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2020', 8, 6, 'Tiếng Anh dùng trong kinh doanh'),
('Ngữ pháp tiếng Anh B1-B2', 'Nguyễn Minh Thu', '978-604-3-002', 'Ngôn ngữ', 'NXB Ngoại ngữ', '2019', 10, 8, 'Hệ thống ngữ pháp tiếng Anh từ căn bản đến nâng cao'),
('Nguyên lý kế toán', 'Trần Văn Nam', '978-604-4-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2020', 6, 5, 'Giáo trình kế toán cơ bản'),
('Kế toán tài chính doanh nghiệp', 'Lê Thị Oanh', '978-604-4-002', 'Kế toán - Tài chính', 'NXB Tài chính', '2021', 5, 4, 'Kế toán tài chính thực hành'),
('Nguyên lý tài chính', 'Nguyễn Quang Phong', '978-604-5-001', 'Kế toán - Tài chính', 'NXB Tài chính', '2022', 4, 4, 'Tài chính doanh nghiệp căn bản'),
('Phương pháp nghiên cứu khoa học', 'Trần Thị Quyên', '978-604-6-001', 'Khoa học - Giáo dục', 'NXB ĐHQG', '2020', 3, 3, 'Hướng dẫn nghiên cứu khoa học'),
('Tâm lý học giáo dục', 'Lê Văn Sơn', '978-604-7-001', 'Khoa học - Giáo dục', 'NXB Giáo dục', '2021', 5, 5, 'Tâm lý học ứng dụng trong giảng dạy'),
('Lịch sử Việt Nam', 'Phạm Thị Thảo', '978-604-8-001', 'Lịch sử - Văn hóa', 'NXB Sử học', '2018', 4, 4, 'Lịch sử Việt Nam từ cổ đại đến hiện đại'),
('Toán cao cấp tập 1', 'Vũ Minh Uyên', '978-604-9-001', 'Toán học', 'NXB ĐHQG', '2020', 8, 6, 'Giải tích và đại số tuyến tính'),
('Toán cao cấp tập 2', 'Đỗ Thị Vân', '978-604-9-002', 'Toán học', 'NXB ĐHQG', '2020', 8, 7, 'Xác suất thống kê và phương trình vi phân'),
('Vật lý đại cương', 'Hoàng Xuân Yên', '978-604-10-001', 'Khoa học tự nhiên', 'NXB ĐHQG', '2019', 6, 5, 'Vật lý cho sinh viên kỹ thuật'),
('Triết học Mác – Lênin', 'Nguyễn Thị Bảo', '978-604-11-001', 'Lý luận chính trị', 'NXB Chính trị', '2021', 10, 9, 'Giáo trình triết học cho đại học');

-- Fix scholarships
TRUNCATE TABLE scholarship_applications;
TRUNCATE TABLE scholarships;

INSERT INTO `scholarships` (`name`, `description`, `value`, `min_gpa`, `max_gpa`, `semester`, `year`, `quantity`, `deadline`, `is_active`) VALUES
('Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Spring', '2026', 20, '2026-03-15', 1),
('Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Spring', '2026', 50, '2026-03-15', 1),
('Học bổng Hỗ trợ', 'Hỗ trợ sinh viên có hoàn cảnh khó khăn', 1500000, NULL, NULL, 'Spring', '2026', 30, '2026-03-20', 1),
('Học bổng Doanh nghiệp ABC', 'Học bổng từ doanh nghiệp ABC cho SV CNTT', 10000000, 85.00, NULL, 'Spring', '2026', 5, '2026-03-10', 1),
('Học bổng Xuất sắc', 'Dành cho sinh viên có GPA từ 90 trở lên', 5000000, 90.00, NULL, 'Fall', '2025', 20, '2025-09-15', 0),
('Học bổng Khuyến khích', 'Dành cho sinh viên có GPA từ 80 đến dưới 90', 2000000, 80.00, 89.99, 'Fall', '2025', 50, '2025-09-15', 0);

-- Fix dormitory_rooms
TRUNCATE TABLE dormitory_registrations;
TRUNCATE TABLE dormitory_rooms;

INSERT INTO `dormitory_rooms` (`room_number`, `building`, `room_type`, `price_per_month`, `total_beds`, `available_beds`, `floor`, `description`) VALUES
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

-- Fix tuition_settings
DELETE FROM tuition_settings;
INSERT INTO `tuition_settings` (`semester`, `year`, `price_per_credit`, `note`) VALUES
('Spring', '2026', 550000, 'Học kỳ 1 năm 2026'),
('Summer', '2026', 550000, 'Học kỳ hè năm 2026'),
('Fall',   '2026', 550000, 'Học kỳ 2 năm 2026'),
('Spring', '2025', 500000, 'Học kỳ 1 năm 2025'),
('Summer', '2025', 500000, 'Học kỳ hè năm 2025'),
('Fall',   '2025', 500000, 'Học kỳ 2 năm 2025');
SET FOREIGN_KEY_CHECKS = 1;