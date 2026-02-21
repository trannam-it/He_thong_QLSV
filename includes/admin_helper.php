<?php
/**
 * Admin Dashboard Helper Functions
 * Các hàm hỗ trợ lấy dữ liệu cho Dashboard Admin
 */

// ===================================
// THỐNG KÊ TỔNG QUAN
// ===================================

/**
 * Đếm tổng số sinh viên
 */
function countTotalStudents($conn) {
    $sql = "SELECT COUNT(*) as total FROM students WHERE status = 'Studying'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Đếm tổng số giảng viên
 */
function countTotalLecturers($conn) {
    $sql = "SELECT COUNT(*) as total FROM lecturers";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Đếm tổng số lớp học
 */
function countTotalClasses($conn) {
    $sql = "SELECT COUNT(*) as total FROM classes WHERE year = YEAR(CURDATE())";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Đếm tổng số khoa
 */
function countTotalFaculties($conn) {
    $sql = "SELECT COUNT(*) as total FROM faculties";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

/**
 * Thống kê sinh viên theo khoa
 */
function getStudentsByFaculty($conn) {
    $sql = "
        SELECT 
            f.faculty_name,
            COUNT(s.student_id) as student_count
        FROM faculties f
        LEFT JOIN students s ON f.faculty_id = s.faculty_id
        WHERE s.status = 'Studying'
        GROUP BY f.faculty_id, f.faculty_name
        ORDER BY student_count DESC
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Thống kê sinh viên theo trạng thái
 */
function getStudentsByStatus($conn) {
    $sql = "
        SELECT 
            status,
            COUNT(*) as count
        FROM students
        GROUP BY status
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy danh sách sinh viên mới nhất
 */
function getRecentStudents($conn, $limit = 10) {
    $sql = "
        SELECT 
            s.student_id,
            s.student_code,
            CONCAT(s.first_name, ' ', s.last_name) as full_name,
            s.email,
            s.phone,
            f.faculty_name,
            bc.base_class_name,
            s.status,
            s.created_at
        FROM students s
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
        ORDER BY s.created_at DESC
        LIMIT ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ===================================
// QUẢN LÝ KHOA
// ===================================

/**
 * Lấy danh sách tất cả khoa
 */
function getAllFaculties($conn) {
    $sql = "
        SELECT 
            f.faculty_id,
            f.faculty_code,
            f.faculty_name,
            f.description,
            COUNT(DISTINCT s.student_id) as student_count,
            COUNT(DISTINCT l.lecturer_id) as lecturer_count
        FROM faculties f
        LEFT JOIN students s ON f.faculty_id = s.faculty_id AND s.status = 'Studying'
        LEFT JOIN lecturers l ON f.faculty_id = l.faculty_id
        GROUP BY f.faculty_id, f.faculty_code, f.faculty_name, f.description
        ORDER BY f.faculty_name
    ";
    $result = $conn->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy thông tin 1 khoa
 */
function getFacultyById($conn, $facultyId) {
    $sql = "SELECT * FROM faculties WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facultyId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Thêm khoa mới
 */
function addFaculty($conn, $facultyCode, $facultyName, $description) {
    // Kiểm tra trùng mã khoa
    $check = $conn->prepare("SELECT faculty_id FROM faculties WHERE faculty_code = ?");
    $check->bind_param("s", $facultyCode);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Mã khoa đã tồn tại'];
    }
    
    $sql = "INSERT INTO faculties (faculty_code, faculty_name, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $facultyCode, $facultyName, $description);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Thêm khoa thành công', 'id' => $conn->insert_id];
    }
    return ['success' => false, 'message' => 'Lỗi: ' . $conn->error];
}

/**
 * Cập nhật khoa
 */
function updateFaculty($conn, $facultyId, $facultyCode, $facultyName, $description) {
    // Kiểm tra trùng mã khoa (trừ chính nó)
    $check = $conn->prepare("SELECT faculty_id FROM faculties WHERE faculty_code = ? AND faculty_id != ?");
    $check->bind_param("si", $facultyCode, $facultyId);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        return ['success' => false, 'message' => 'Mã khoa đã tồn tại'];
    }
    
    $sql = "UPDATE faculties SET faculty_code = ?, faculty_name = ?, description = ? WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $facultyCode, $facultyName, $description, $facultyId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Cập nhật khoa thành công'];
    }
    return ['success' => false, 'message' => 'Lỗi: ' . $conn->error];
}

/**
 * Xóa khoa (có kiểm tra ràng buộc)
 */
function deleteFaculty($conn, $facultyId) {
    // Kiểm tra còn sinh viên không
    $checkStudent = $conn->prepare("SELECT COUNT(*) as count FROM students WHERE faculty_id = ?");
    $checkStudent->bind_param("i", $facultyId);
    $checkStudent->execute();
    $studentCount = $checkStudent->get_result()->fetch_assoc()['count'];
    
    if ($studentCount > 0) {
        return ['success' => false, 'message' => 'Không thể xóa khoa vì còn ' . $studentCount . ' sinh viên'];
    }
    
    // Kiểm tra còn giảng viên không
    $checkLecturer = $conn->prepare("SELECT COUNT(*) as count FROM lecturers WHERE faculty_id = ?");
    $checkLecturer->bind_param("i", $facultyId);
    $checkLecturer->execute();
    $lecturerCount = $checkLecturer->get_result()->fetch_assoc()['count'];
    
    if ($lecturerCount > 0) {
        return ['success' => false, 'message' => 'Không thể xóa khoa vì còn ' . $lecturerCount . ' giảng viên'];
    }
    
    // Xóa khoa
    $sql = "DELETE FROM faculties WHERE faculty_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facultyId);
    
    if ($stmt->execute()) {
        return ['success' => true, 'message' => 'Xóa khoa thành công'];
    }
    return ['success' => false, 'message' => 'Lỗi: ' . $conn->error];
}

// ===================================
// QUẢN LÝ SINH VIÊN
// ===================================

/**
 * Tìm kiếm sinh viên
 */
function searchStudents($conn, $keyword, $facultyId = null, $status = null) {
    $sql = "
        SELECT 
            s.student_id,
            s.student_code,
            CONCAT(s.first_name, ' ', s.last_name) as full_name,
            s.email,
            s.phone,
            s.gender,
            f.faculty_name,
            bc.base_class_name,
            s.status
        FROM students s
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    if (!empty($keyword)) {
        $sql .= " AND (s.student_code LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ?)";
        $searchTerm = "%$keyword%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $types .= 'ssss';
    }
    
    if ($facultyId) {
        $sql .= " AND s.faculty_id = ?";
        $params[] = $facultyId;
        $types .= 'i';
    }
    
    if ($status) {
        $sql .= " AND s.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $sql .= " ORDER BY s.student_code";
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy thông tin admin
 */
function getAdminInfo($conn, $userId) {
    $sql = "
        SELECT 
            u.id,
            u.username,
            u.email,
            r.name as role_name,
            CONCAT(u.username, ' (', r.name, ')') as fullname
        FROM users u
        JOIN user_roles ur ON u.id = ur.user_id
        JOIN roles r ON ur.role_id = r.id
        WHERE u.id = ?
        LIMIT 1
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Lấy tất cả sinh viên (có phân trang)
 */
function getAllStudents($conn, $page = 1, $perPage = 20, $facultyId = null, $status = null) {
    $offset = ($page - 1) * $perPage;
    
    $sql = "
        SELECT 
            s.student_id,
            s.student_code,
            s.first_name,
            s.last_name,
            CONCAT(s.first_name, ' ', s.last_name) as fullname,
            s.email,
            s.phone,
            s.gender,
            s.date_of_birth,
            s.address,
            f.faculty_id,
            f.faculty_name,
            bc.base_class_id,
            bc.base_class_name,
            s.status,
            s.created_at
        FROM students s
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
        WHERE 1=1
    ";
    
    $params = [];
    $types = '';
    
    if ($facultyId) {
        $sql .= " AND s.faculty_id = ?";
        $params[] = $facultyId;
        $types .= 'i';
    }
    
    if ($status) {
        $sql .= " AND s.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $sql .= " ORDER BY s.student_code LIMIT ? OFFSET ?";
    $params[] = $perPage;
    $params[] = $offset;
    $types .= 'ii';
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

/**
 * Lấy tổng số sinh viên (cho phân trang)
 */
function countAllStudents($conn, $facultyId = null, $status = null) {
    $sql = "SELECT COUNT(*) as total FROM students WHERE 1=1";
    
    $params = [];
    $types = '';
    
    if ($facultyId) {
        $sql .= " AND faculty_id = ?";
        $params[] = $facultyId;
        $types .= 'i';
    }
    
    if ($status) {
        $sql .= " AND status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    $stmt = $conn->prepare($sql);
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['total'] ?? 0;
}

/**
 * Lấy thông tin sinh viên theo ID
 */
function getStudentById($conn, $studentId) {
    $sql = "
        SELECT 
            s.*,
            f.faculty_name,
            bc.base_class_name
        FROM students s
        LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
        LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
        WHERE s.student_id = ?
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

/**
 * Thêm sinh viên mới
 */
function addStudent($conn, $data) {
    $sql = "INSERT INTO students (
        student_code, first_name, last_name, email, phone, 
        gender, date_of_birth, address, faculty_id, base_class_id, status
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssiis", 
        $data['student_code'],
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['gender'],
        $data['date_of_birth'],
        $data['address'],
        $data['faculty_id'],
        $data['base_class_id'],
        $data['status']
    );
    
    if ($stmt->execute()) {
        return ['success' => true, 'student_id' => $conn->insert_id];
    }
    return ['success' => false, 'message' => $conn->error];
}

/**
 * Cập nhật thông tin sinh viên
 */
function updateStudent($conn, $studentId, $data) {
    $sql = "UPDATE students SET 
        first_name = ?, last_name = ?, email = ?, phone = ?,
        gender = ?, date_of_birth = ?, address = ?, 
        faculty_id = ?, base_class_id = ?, status = ?
    WHERE student_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssiisi", 
        $data['first_name'],
        $data['last_name'],
        $data['email'],
        $data['phone'],
        $data['gender'],
        $data['date_of_birth'],
        $data['address'],
        $data['faculty_id'],
        $data['base_class_id'],
        $data['status'],
        $studentId
    );
    
    return $stmt->execute();
}

/**
 * Xóa sinh viên
 */
function deleteStudent($conn, $studentId) {
    // Kiểm tra ràng buộc với enrollments và grades
    $checkEnrollments = $conn->query("SELECT COUNT(*) as c FROM enrollments WHERE student_id = $studentId")->fetch_assoc()['c'];
    $checkGrades = $conn->query("SELECT COUNT(*) as c FROM grades WHERE student_id = $studentId")->fetch_assoc()['c'];
    
    if ($checkEnrollments > 0 || $checkGrades > 0) {
        return [
            'success' => false, 
            'message' => 'Không thể xóa sinh viên vì đã có đăng ký học phần hoặc điểm số!'
        ];
    }
    
    $sql = "DELETE FROM students WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);
    
    if ($stmt->execute()) {
        return ['success' => true];
    }
    return ['success' => false, 'message' => $conn->error];
}

/**
 * Lấy danh sách lớp cơ sở theo khoa
 */
function getBaseClassesByFaculty($conn, $facultyId) {
    $sql = "SELECT * FROM base_classes WHERE faculty_id = ? ORDER BY base_class_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facultyId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
