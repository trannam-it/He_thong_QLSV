<?php
/**
 * Dashboard Helper Functions
 * Các hàm hỗ trợ lấy dữ liệu cho Dashboard của Sinh viên và Giảng viên
 */

// ===================================
// DASHBOARD SINH VIÊN
// ===================================

/**
 * Lấy thông tin tổng quan của sinh viên
 */
function getStudentOverview($conn, $userId) {
    $sql = "
        SELECT 
            s.student_id,
            s.student_code,
            CONCAT(s.first_name, ' ', s.last_name) AS full_name,
            s.email,
            s.phone,
            s.gender,
            s.birth_date,
            f.faculty_name,
            bc.base_class_name,
            s.status
        FROM students s
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
        WHERE s.user_id = ?
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result) {
        // Nếu không tìm thấy, trả về data mặc định
        return [
            'student_id' => 0,
            'student_code' => 'N/A',
            'full_name' => 'Chưa có thông tin',
            'email' => '',
            'phone' => '',
            'gender' => 'Male',
            'birth_date' => date('Y-m-d'),
            'faculty_name' => 'Chưa xác định',
            'base_class_name' => 'Chưa có lớp',
            'status' => 'Studying'
        ];
    }
    
    return $result;
}

/**
 * Tính GPA của sinh viên
 */
function calculateStudentGPA($conn, $studentId) {
    $sql = "
        SELECT 
            ROUND(AVG(g.score), 2) as gpa,
            COUNT(DISTINCT e.class_id) as total_courses,
            SUM(CASE WHEN e.status = 'Completed' THEN sub.credit_hours ELSE 0 END) as total_credits
        FROM enrollments e
        INNER JOIN classes c ON e.class_id = c.class_id
        INNER JOIN subjects sub ON c.subject_id = sub.subject_id
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE e.student_id = ? AND e.status IN ('Completed', 'Registered')
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Lấy lịch học của sinh viên
 */
function getStudentSchedule($conn, $studentId, $limit = 5) {
    $sql = "
        SELECT 
            c.class_code,
            sub.subject_name,
            CONCAT(l.first_name, ' ', l.last_name) AS lecturer_name,
            c.semester,
            c.year,
            e.status
        FROM enrollments e
        INNER JOIN classes c ON e.class_id = c.class_id
        INNER JOIN subjects sub ON c.subject_id = sub.subject_id
        INNER JOIN lecturers l ON c.lecturer_id = l.lecturer_id
        WHERE e.student_id = ? AND e.status = 'Registered'
        ORDER BY c.year DESC, c.semester DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $studentId, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy điểm của sinh viên
 */
function getStudentGrades($conn, $studentId, $limit = 10) {
    $sql = "
        SELECT 
            sub.subject_code,
            sub.subject_name,
            sub.credit_hours,
            g.score,
            g.grade_letter,
            c.semester,
            c.year
        FROM enrollments e
        INNER JOIN classes c ON e.class_id = c.class_id
        INNER JOIN subjects sub ON c.subject_id = sub.subject_id
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE e.student_id = ? AND e.status = 'Completed'
        ORDER BY c.year DESC, c.semester DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $studentId, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Thống kê điểm danh của sinh viên
 */
function getStudentAttendanceStats($conn, $studentId) {
    // Giả định có bảng attendance (chưa có trong DB hiện tại)
    // Trả về mock data
    return [
        'total_classes' => 0,
        'attended' => 0,
        'absent' => 0,
        'attendance_rate' => 0
    ];
}

// ===================================
// DASHBOARD GIẢNG VIÊN
// ===================================

/**
 * Lấy thông tin tổng quan của giảng viên
 */
function getLecturerOverview($conn, $userId) {
    $sql = "
        SELECT 
            l.lecturer_id,
            l.lecturer_code,
            CONCAT(l.first_name, ' ', l.last_name) AS full_name,
            l.email,
            l.phone,
            l.degree,
            f.faculty_name
        FROM lecturers l
        LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
        WHERE l.user_id = ?
        LIMIT 1
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    if (!$result) {
        // Nếu không tìm thấy, trả về data mặc định
        return [
            'lecturer_id' => 0,
            'lecturer_code' => 'N/A',
            'full_name' => 'Chưa có thông tin',
            'email' => '',
            'phone' => '',
            'degree' => 'Bachelor',
            'faculty_name' => 'Chưa xác định'
        ];
    }
    
    return $result;
}

/**
 * Đếm số lớp giảng viên đang dạy
 */
function countLecturerClasses($conn, $lecturerId) {
    $sql = "
        SELECT COUNT(DISTINCT class_id) as total_classes
        FROM classes
        WHERE lecturer_id = ? AND year = YEAR(CURDATE())
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_classes'] ?? 0;
}

/**
 * Đếm số sinh viên giảng viên đang phụ trách
 */
function countLecturerStudents($conn, $lecturerId) {
    $sql = "
        SELECT COUNT(DISTINCT e.student_id) as total_students
        FROM enrollments e
        INNER JOIN classes c ON e.class_id = c.class_id
        WHERE c.lecturer_id = ? AND e.status IN ('Registered', 'Completed')
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lecturerId);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total_students'] ?? 0;
}

/**
 * Lấy danh sách lớp giảng viên đang dạy
 */
function getLecturerClasses($conn, $lecturerId, $limit = 10) {
    $sql = "
        SELECT 
            c.class_id,
            c.class_code,
            sub.subject_name,
            sub.credit_hours,
            c.semester,
            c.year,
            COUNT(e.enrollment_id) as student_count
        FROM classes c
        INNER JOIN subjects sub ON c.subject_id = sub.subject_id
        LEFT JOIN enrollments e ON c.class_id = e.class_id
        WHERE c.lecturer_id = ?
        GROUP BY c.class_id, c.class_code, sub.subject_name, sub.credit_hours, c.semester, c.year
        ORDER BY c.year DESC, c.semester DESC
        LIMIT ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $lecturerId, $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy danh sách sinh viên trong lớp
 */
function getClassStudents($conn, $classId) {
    $sql = "
        SELECT 
            s.student_code,
            CONCAT(s.first_name, ' ', s.last_name) AS full_name,
            s.email,
            e.status,
            g.score,
            g.grade_letter
        FROM enrollments e
        INNER JOIN students s ON e.student_id = s.student_id
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE e.class_id = ?
        ORDER BY s.student_code ASC
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Thống kê điểm của lớp
 */
function getClassGradeStats($conn, $classId) {
    $sql = "
        SELECT 
            COUNT(*) as total_students,
            COUNT(g.grade_id) as graded_students,
            ROUND(AVG(g.score), 2) as avg_score,
            MAX(g.score) as max_score,
            MIN(g.score) as min_score
        FROM enrollments e
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE e.class_id = ?
    ";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ===================================
// THÔNG BÁO
// ===================================

/**
 * Lấy thông báo mới nhất (mock data - cần tạo bảng notifications)
 */
function getRecentNotifications($conn, $userId, $limit = 5) {
    // Mock data vì chưa có bảng notifications
    return [];
}

// ===================================
// HELPER FUNCTIONS
// ===================================

/**
 * Format điểm chữ từ điểm số
 */
function formatGradeLetter($score) {
    if ($score >= 90) return 'A+';
    if ($score >= 85) return 'A';
    if ($score >= 80) return 'B+';
    if ($score >= 75) return 'B';
    if ($score >= 70) return 'C+';
    if ($score >= 65) return 'C';
    if ($score >= 60) return 'D+';
    if ($score >= 55) return 'D';
    return 'F';
}

/**
 * Format semester tiếng Việt
 */
function formatSemester($semester) {
    $semesters = [
        'Spring' => 'Học kỳ I',
        'Summer' => 'Học kỳ Hè',
        'Fall' => 'Học kỳ II'
    ];
    return $semesters[$semester] ?? $semester;
}

/**
 * Format trạng thái enrollment
 */
// function formatEnrollmentStatus($status) {
//     $statuses = [
//         'Registered' => 'Đang học',
//         'Completed' => 'Hoàn thành',
//         'Cancelled' => 'Đã hủy',
//         'Failed' => 'Không đạt'
//     ];
//     return $statuses[$status] ?? $status;
// }

function formatEnrollmentStatus($status) {
    $statuses = [
        'Enrolled'  => 'Đang học',
        'Completed' => 'Hoàn thành',
        'Withdrawn' => 'Đã hủy',
    ];
    return $statuses[$status] ?? $status;
}

/**
 * Get badge class for status
 */
// function getStatusBadgeClass($status) {
//     $badges = [
//         'Registered' => 'bg-primary',
//         'Completed' => 'bg-success',
//         'Cancelled' => 'bg-secondary',
//         'Failed' => 'bg-danger',
//         'Studying' => 'bg-info'
//     ];
//     return $badges[$status] ?? 'bg-secondary';
// }

function getStatusBadgeClass($status) {
    $badges = [
        'Enrolled'  => 'bg-primary',   // Đang học
        'Completed' => 'bg-success',   // Hoàn thành
        'Withdrawn' => 'bg-secondary', // Đã hủy
        'Failed'    => 'bg-danger',    // Không đạt
        'Studying'  => 'bg-info'       // Đang học (cho sinh viên)
    ];
    return $badges[$status] ?? 'bg-secondary';
}

// function getStatusBadgeClass($status) {
//     if (!$status) return 'bg-secondary';

//     $badges = [
//         'Enrolled'  => 'bg-primary',
//         'Completed' => 'bg-success',
//         'Withdrawn' => 'bg-secondary',
//         'Failed'    => 'bg-danger'
//     ];

//     return $badges[$status] ?? 'bg-secondary';
// }
