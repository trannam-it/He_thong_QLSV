<?php
/**
 * LecturerModel - Model tập trung toàn bộ truy vấn DB cho Giảng viên
 *
 * Tách biệt hoàn toàn logic truy vấn khỏi Controller và View.
 * Controller chỉ gọi method của Model, không viết SQL trực tiếp.
 */
// countCurrentStudents()
// countPendingGrades()
// getClasses()
// getClassesStats()
// getClassOfLecturer()
// getStudentsWithGrades()
// getGradeStats()
// addStudentToClass()
// removeStudentFromClass()
// getEnrolledStudents()
// saveAttendance()
// getAttendanceByDate()
// deleteAttendanceByDate()
// getAttendanceHistory()
// getAttendanceDetail()
// getStudentAttendanceSummary()
// getTotalSessionsHeld()
// hasRegisteredClass()
// findUnassignedClass()
// createNewClass()
// getYearList()
// getAvailableClassesForRegistration()

// thêm
// removeStudentFromClass
// createNewClass
// findUnassignedClass
// hasRegisteredClass
// getTotalSessionsHeld
// saveAttendance
// getAttendanceByDate

class LecturerModel
{
    private mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ═══════════════════════════════════════════════
    // THÔNG TIN GIẢNG VIÊN
    // ═══════════════════════════════════════════════

    /**
     * Lấy thông tin tổng quan giảng viên theo user_id
     */
    public function getOverviewByUserId(int $userId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT l.lecturer_id, l.lecturer_code, l.phone, l.email, l.degree,
                   u.username, u.email AS account_email,
                   CONCAT(l.first_name,' ',l.last_name) AS full_name,
                   f.faculty_name, f.faculty_id,
                   0 AS lecturer_check
            FROM users u
            LEFT JOIN lecturers l ON u.id = l.user_id
            LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
            WHERE u.id = ?
            LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }

    /**
     * Cập nhật email và phone giảng viên
     */
    public function updateContact(int $lecturerId, string $phone, string $email): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE lecturers SET phone=?, email=? WHERE lecturer_id=?"
        );
        $stmt->bind_param('ssi', $phone, $email, $lecturerId);
        return $stmt->execute();
    }

    /**
     * Đổi mật khẩu tài khoản
     */
    public function changePassword(int $userId, string $newHash): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE users SET password_hash=? WHERE id=?"
        );
        $stmt->bind_param('si', $newHash, $userId);
        return $stmt->execute();
    }

    /**
     * Lấy password_hash hiện tại để verify
     */
    public function getPasswordHash(int $userId): ?string
    {
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE id=?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['password_hash'] ?? null;
    }

    // ═══════════════════════════════════════════════
    // THỐNG KÊ TỔNG QUAN (DASHBOARD)
    // ═══════════════════════════════════════════════

    /**
     * Tổng số lớp đang dạy trong năm hiện tại
     */
    // public function countCurrentYearClasses(int $lecturerId): int
    // {
    //     $year = (int)date('Y');
    //     $stmt = $this->conn->prepare(
    //         "SELECT COUNT(*) AS cnt FROM classes WHERE lecturer_id=? AND year=?"
    //     );
    //     $stmt->bind_param('ii', $lecturerId, $year);
    //     $stmt->execute();
    //     return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    // }
    public function countCurrentYearClasses(int $lecturerId): int
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS cnt
            FROM classes c
            JOIN semesters s ON s.semester_id = c.semester_id
            WHERE c.lecturer_id = ?
            AND s.is_current = 1
        ");

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return 0;
        }

        $stmt->bind_param('i', $lecturerId);

        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return 0;
        }

        $row = $stmt->get_result()->fetch_assoc();
        return (int)($row['cnt'] ?? 0);
    }
    /**
     * Tổng số sinh viên trong tất cả lớp hiện tại của giảng viên
     */

public function countCurrentStudents(int $lecturerId): int
{
    $stmt = $this->conn->prepare("
        SELECT COUNT(DISTINCT e.student_id) AS cnt
        FROM classes c
        JOIN semesters s ON s.semester_id = c.semester_id
        JOIN enrollments e ON c.class_id = e.class_id
        WHERE c.lecturer_id = ?
          AND s.is_current = 1
          AND e.status IN ('Enrolled','Completed')
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return 0;
    }

    $stmt->bind_param('i', $lecturerId);

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }

    $row = $stmt->get_result()->fetch_assoc();
    return (int)($row['cnt'] ?? 0);
}


    /**
     * Tổng số môn học phụ trách (không trùng)
     */
    public function countDistinctSubjects(int $lecturerId): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(DISTINCT subject_id) AS cnt FROM classes WHERE lecturer_id=?"
        );
        $stmt->bind_param('i', $lecturerId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    /**
     * Tổng số lớp tất cả thời gian
     */
    public function countAllClasses(int $lecturerId): int
    {
        $stmt = $this->conn->prepare(
            "SELECT COUNT(*) AS cnt FROM classes WHERE lecturer_id=?"
        );
        $stmt->bind_param('i', $lecturerId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    /**
     * Số sinh viên chưa được nhập điểm
     */
    // public function countPendingGrades(int $lecturerId): int
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT COUNT(*) AS cnt
    //         FROM enrollments e
    //         INNER JOIN classes c ON e.class_id = c.class_id
    //         LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
    //         WHERE c.lecturer_id = ? AND e.status = 'Registered' AND g.grade_id IS NULL
    //     ");
    //     $stmt->bind_param('i', $lecturerId);
    //     $stmt->execute();
    //     return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    // }

    public function countPendingGrades(int $lecturerId): int
{
    $stmt = $this->conn->prepare("
        SELECT COUNT(*) AS cnt
        FROM enrollments e
        INNER JOIN classes c ON e.class_id = c.class_id
        LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
        WHERE c.lecturer_id = ?
          AND e.status = 'Enrolled'
          AND g.grade_id IS NULL
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return 0;
    }

    $stmt->bind_param('i', $lecturerId);

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return 0;
    }

    $row = $stmt->get_result()->fetch_assoc();
    return (int)($row['cnt'] ?? 0);
}

    /**
     * Lấy danh sách lớp học mới nhất của giảng viên
     */
    // public function getRecentClasses(int $lecturerId, int $limit = 10): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT c.class_id, c.class_code, c.semester, c.year,
    //                sub.subject_name, sub.credit_hours, sub.subject_code,
    //                COUNT(DISTINCT e.enrollment_id) AS student_count
    //         FROM classes c
    //         JOIN subjects sub ON c.subject_id = sub.subject_id
    //         LEFT JOIN enrollments e ON c.class_id = e.class_id
    //             AND e.status IN ('Registered','Completed')
    //         WHERE c.lecturer_id = ?
    //         GROUP BY c.class_id
    //         ORDER BY c.year DESC, c.semester DESC
    //         LIMIT ?
    //     ");
    //     $stmt->bind_param('ii', $lecturerId, $limit);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getRecentClasses(int $lecturerId, int $limit = 10): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            c.class_id, 
            c.class_code,
            s.semester_code,
            s.semester_name,
            sub.subject_name, 
            sub.credit_hours, 
            sub.subject_code,
            COUNT(DISTINCT e.enrollment_id) AS student_count
        FROM classes c
        JOIN semesters s ON s.semester_id = c.semester_id
        JOIN subjects sub ON c.subject_id = sub.subject_id
        LEFT JOIN enrollments e 
            ON c.class_id = e.class_id
            AND e.status IN ('Registered','Completed')
        WHERE c.lecturer_id = ?
        GROUP BY c.class_id
        ORDER BY s.start_date DESC
        LIMIT ?
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return [];
    }

    $stmt->bind_param('ii', $lecturerId, $limit);

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return [];
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    // ═══════════════════════════════════════════════
    // LỚP HỌC (CLASSES)
    // ═══════════════════════════════════════════════

    /**
     * Lấy danh sách lớp học với filter
     */
    // public function getClasses(int $lecturerId, string $search = '', string $semester = '', int $year = 0): array
    // {
    //     $where  = ["c.lecturer_id = ?"];
    //     $params = [$lecturerId];
    //     $types  = 'i';

    //     if ($semester) {
    //         $where[]  = "c.semester = ?";
    //         $params[] = $semester;
    //         $types   .= 's';
    //     }
    //     if ($year > 0) {
    //         $where[]  = "c.year = ?";
    //         $params[] = $year;
    //         $types   .= 'i';
    //     }
    //     if ($search) {
    //         $where[]  = "(c.class_code LIKE ? OR sub.subject_name LIKE ?)";
    //         $params[] = "%{$search}%";
    //         $params[] = "%{$search}%";
    //         $types   .= 'ss';
    //     }

    //     $sql = "
    //         SELECT c.class_id, c.class_code, c.semester, c.year,
    //                sub.subject_name, sub.credit_hours, sub.subject_code,
    //                f.faculty_name,
    //                COUNT(DISTINCT e.enrollment_id)      AS total_students,
    //                COUNT(DISTINCT g.grade_id)           AS graded_students
    //         FROM classes c
    //         JOIN subjects sub ON c.subject_id = sub.subject_id
    //         JOIN lecturers lec ON c.lecturer_id = lec.lecturer_id
    //         LEFT JOIN faculties f ON lec.faculty_id = f.faculty_id
    //         LEFT JOIN enrollments e ON c.class_id = e.class_id
    //             AND e.status IN ('Registered','Completed')
    //         LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
    //         WHERE " . implode(' AND ', $where) . "
    //         GROUP BY c.class_id
    //         ORDER BY c.year DESC, c.semester DESC
    //     ";

    //     $stmt = $this->conn->prepare($sql);
    //     $stmt->bind_param($types, ...$params);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getClasses(int $lecturerId, string $search = '', string $semester = '', int $year = 0): array
{
    $where  = ["c.lecturer_id = ?"];
    $params = [$lecturerId];
    $types  = 'i';

    if ($semester) {
        $where[]  = "s.semester_name = ?";
        $params[] = $semester;
        $types   .= 's';
    }

    if ($year > 0) {
        $where[]  = "YEAR(s.start_date) = ?";
        $params[] = $year;
        $types   .= 'i';
    }

    if ($search) {
        $where[]  = "(c.class_code LIKE ? OR sub.subject_name LIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
        $types   .= 'ss';
    }

    $sql = "
        SELECT 
            c.class_id,
            c.class_code,
            s.semester_name,
            YEAR(s.start_date) AS year,
            sub.subject_name,
            sub.credit_hours,
            sub.subject_code,
            f.faculty_name,

            COUNT(DISTINCT e.enrollment_id) AS total_students,
            COUNT(DISTINCT g.grade_id) AS graded_students

        FROM classes c
        JOIN subjects sub ON c.subject_id = sub.subject_id
        JOIN semesters s ON c.semester_id = s.semester_id
        JOIN lecturers lec ON c.lecturer_id = lec.lecturer_id
        LEFT JOIN faculties f ON lec.faculty_id = f.faculty_id

        LEFT JOIN enrollments e 
            ON c.class_id = e.class_id
            AND e.status IN ('Enrolled','Completed')

        LEFT JOIN grades g 
            ON e.enrollment_id = g.enrollment_id

        WHERE " . implode(' AND ', $where) . "

        GROUP BY c.class_id
        ORDER BY s.start_date DESC
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Lấy danh sách lớp mà giảng viên có thể đăng ký dạy
     * Lọc theo: môn họ dạy, kỳ đăng ký đang hoạt động
     */
    // public function getAvailableClassesForRegistration(int $lecturerId): array
    // {
    //     // Get lecturer's faculty and taught subjects
    //     $stmt = $this->conn->prepare("
    //         SELECT DISTINCT sub.subject_id
    //         FROM subjects sub
    //         JOIN classes c ON sub.subject_id = c.subject_id
    //         WHERE c.lecturer_id = ?
    //     ");
    //     $stmt->bind_param('i', $lecturerId);
    //     $stmt->execute();
    //     $result = $stmt->get_result();
    //     $taughtSubjects = array_column($result->fetch_all(MYSQLI_ASSOC), 'subject_id');
        
    //     if (empty($taughtSubjects)) {
    //         return [];
    //     }

    //     // Get classes that match lecturer's taught subjects AND have active enrollment period
    //     $placeholders = implode(',', array_fill(0, count($taughtSubjects), '?'));
    //     $sql = "
    //         SELECT c.class_id, c.class_code, c.semester, c.year,
    //                sub.subject_code, sub.subject_name, sub.credit_hours,
    //                lec.lecturer_code, CONCAT(lec.first_name,' ',lec.last_name) AS lecturer_name,
    //                COUNT(DISTINCT e.enrollment_id) AS enrolled_count
    //         FROM classes c
    //         JOIN subjects sub ON c.subject_id = sub.subject_id
    //         JOIN lecturers lec ON c.lecturer_id = lec.lecturer_id
    //         JOIN enrollment_registration_periods erp 
    //             ON erp.semester = c.semester AND erp.year = c.year
    //             AND erp.is_active = 1
    //         LEFT JOIN enrollments e ON c.class_id = e.class_id 
    //             AND e.status IN ('Registered', 'Completed')
    //         WHERE c.subject_id IN ({$placeholders})
    //             AND c.lecturer_id != ?
    //             AND erp.enrollment_open <= NOW() AND NOW() <= erp.enrollment_close
    //         GROUP BY c.class_id
    //         ORDER BY c.semester DESC, c.year DESC, sub.subject_name ASC
    //     ";

    //     $stmt = $this->conn->prepare($sql);
    //     if (!$stmt) {
    //         return [];
    //     }

    //     // Bind parameters: subject IDs + exclude current lecturer ID
    //     $types = str_repeat('i', count($taughtSubjects)) . 'i';
    //     $params = array_merge($taughtSubjects, [$lecturerId]);
    //     $stmt->bind_param($types, ...$params);
    //     $stmt->execute();
        
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getAvailableClassesForRegistration(int $lecturerId): array
{
    $stmt = $this->conn->prepare("
        SELECT DISTINCT sub.subject_id
        FROM subjects sub
        JOIN classes c ON sub.subject_id = c.subject_id
        WHERE c.lecturer_id = ?
    ");
    $stmt->bind_param('i', $lecturerId);
    $stmt->execute();

    $result = $stmt->get_result();
    $taughtSubjects = array_column($result->fetch_all(MYSQLI_ASSOC), 'subject_id');

    if (empty($taughtSubjects)) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($taughtSubjects), '?'));

    $sql = "
        SELECT c.class_id, c.class_code,
               s.semester_name,
               YEAR(s.start_date) AS year,
               sub.subject_code,
               sub.subject_name,
               sub.credit_hours,
               lec.lecturer_code,
               CONCAT(lec.first_name,' ',lec.last_name) AS lecturer_name,
               COUNT(DISTINCT e.enrollment_id) AS enrolled_count
        FROM classes c
        JOIN semesters s ON c.semester_id = s.semester_id
        JOIN subjects sub ON c.subject_id = sub.subject_id
        JOIN lecturers lec ON c.lecturer_id = lec.lecturer_id
        LEFT JOIN enrollments e 
            ON c.class_id = e.class_id
            AND e.status IN ('Enrolled','Completed')
        WHERE c.subject_id IN ({$placeholders})
        AND c.lecturer_id != ?
        AND s.is_active = 1
        GROUP BY c.class_id
        ORDER BY s.start_date DESC, sub.subject_name ASC
    ";

    $stmt = $this->conn->prepare($sql);

    $types = str_repeat('i', count($taughtSubjects)) . 'i';
    $params = array_merge($taughtSubjects, [$lecturerId]);

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Tổng hợp thống kê lớp học
     */
    // public function getClassesStats(int $lecturerId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT COUNT(DISTINCT c.class_id)       AS total_cls,
    //                COUNT(DISTINCT e.student_id)     AS total_sv,
    //                COUNT(DISTINCT c.subject_id)     AS total_sub,
    //                SUM(CASE WHEN g.grade_id IS NULL AND e.enrollment_id IS NOT NULL THEN 1 ELSE 0 END) AS pending
    //         FROM classes c
    //         LEFT JOIN enrollments e ON c.class_id=e.class_id
    //             AND e.status IN ('Registered','Completed')
    //         LEFT JOIN grades g ON e.enrollment_id=g.enrollment_id
    //         WHERE c.lecturer_id = ?
    //     ");
    //     $stmt->bind_param('i', $lecturerId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_assoc() ?? [];
    // }

    public function getClassesStats(int $lecturerId): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            COUNT(DISTINCT c.class_id) AS total_cls,
            COUNT(DISTINCT e.student_id) AS total_sv,
            COUNT(DISTINCT c.subject_id) AS total_sub,

            SUM(
                CASE 
                    WHEN g.grade_id IS NULL 
                    AND e.enrollment_id IS NOT NULL 
                    THEN 1 
                    ELSE 0 
                END
            ) AS pending

        FROM classes c

        LEFT JOIN enrollments e 
            ON c.class_id = e.class_id
            AND e.status IN ('Enrolled','Completed')

        LEFT JOIN grades g 
            ON e.enrollment_id = g.enrollment_id

        WHERE c.lecturer_id = ?
    ");

    $stmt->bind_param('i', $lecturerId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?? [];
}

    /**
     * Lấy danh sách năm học (cho filter dropdown)
     */
    // public function getYearList(int $lecturerId): array
    // {
    //     $stmt = $this->conn->prepare(
    //         "SELECT DISTINCT year FROM classes WHERE lecturer_id=? ORDER BY year DESC"
    //     );
    //     $stmt->bind_param('i', $lecturerId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getYearList(int $lecturerId): array
{
    $stmt = $this->conn->prepare("
        SELECT DISTINCT YEAR(s.start_date) AS year
        FROM classes c
        JOIN semesters s ON c.semester_id = s.semester_id
        WHERE c.lecturer_id=?
        ORDER BY year DESC
    ");

    $stmt->bind_param('i', $lecturerId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // ═══════════════════════════════════════════════
    // NHẬP ĐIỂM (GRADES)
    // ═══════════════════════════════════════════════

    /**
     * Kiểm tra lớp có thuộc giảng viên không
     */
    // public function getClassOfLecturer(int $classId, int $lecturerId): ?array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT c.class_id, c.class_code, c.semester, c.year,
    //                sub.subject_name, sub.credit_hours, sub.subject_code
    //         FROM classes c
    //         JOIN subjects sub ON c.subject_id = sub.subject_id
    //         WHERE c.class_id = ? AND c.lecturer_id = ?
    //         LIMIT 1
    //     ");
    //     $stmt->bind_param('ii', $classId, $lecturerId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_assoc() ?: null;
    // }

    public function getClassOfLecturer(int $classId, int $lecturerId): ?array
{
    $stmt = $this->conn->prepare("
        SELECT 
            c.class_id,
            c.class_code,
            s.semester_name,
            YEAR(s.start_date) AS year,

            sub.subject_name,
            sub.credit_hours,
            sub.subject_code

        FROM classes c

        JOIN subjects sub 
            ON c.subject_id = sub.subject_id

        JOIN semesters s 
            ON c.semester_id = s.semester_id

        WHERE c.class_id = ?
        AND c.lecturer_id = ?
        LIMIT 1
    ");

    $stmt->bind_param('ii', $classId, $lecturerId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}


    /**
     * Lấy danh sách sinh viên + điểm trong lớp
     */
    public function getStudentsWithGrades(int $classId): array
    {
        $stmt = $this->conn->prepare("
            SELECT e.enrollment_id, e.student_id, e.status AS enroll_status,
                   s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name,
                   s.email,
                   g.grade_id, g.score, g.grade_letter
            FROM enrollments e
            JOIN students s ON e.student_id = s.student_id
            LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
            WHERE e.class_id = ? AND e.status IN ('Enrolled','Completed')
            ORDER BY s.student_code ASC
        ");
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Thống kê điểm trong lớp
     */
    // public function getGradeStats(int $classId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT COUNT(DISTINCT e.enrollment_id)   AS total_students,
    //                COUNT(DISTINCT g.grade_id)        AS graded_students,
    //                AVG(g.score)                      AS avg_score,
    //                MAX(g.score)                      AS max_score,
    //                MIN(g.score)                      AS min_score
    //         FROM enrollments e
    //         LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
    //         WHERE e.class_id = ? AND e.status IN ('Registered','Completed')
    //     ");
    //     $stmt->bind_param('i', $classId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_assoc() ?? [];
    // }

    public function getGradeStats(int $classId): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            COUNT(DISTINCT e.enrollment_id) AS total_students,
            COUNT(DISTINCT g.grade_id) AS graded_students,
            AVG(g.score) AS avg_score,
            MAX(g.score) AS max_score,
            MIN(g.score) AS min_score

        FROM enrollments e
        LEFT JOIN grades g 
            ON e.enrollment_id = g.enrollment_id

        WHERE e.class_id = ?
        AND e.status IN ('Enrolled','Completed')
    ");

    $stmt->bind_param('i', $classId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?? [];
}


    /**
     * Lưu / cập nhật điểm sinh viên
     */
    public function saveGrade(int $enrollmentId, ?float $score): string
    {
        // Xác định grade_letter
        $letter = null;
        if ($score !== null) {
            $letter = $this->calcGradeLetter($score);
        }

        // Kiểm tra đã có điểm chưa
        $check = $this->conn->prepare("SELECT grade_id FROM grades WHERE enrollment_id=?");
        $check->bind_param('i', $enrollmentId);
        $check->execute();
        $existing = $check->get_result()->fetch_assoc();

        if ($existing) {
            if ($score !== null) {
                $upd = $this->conn->prepare(
                    "UPDATE grades SET score=?, grade_letter=? WHERE enrollment_id=?"
                );
                $upd->bind_param('dsi', $score, $letter, $enrollmentId);
                $upd->execute();
            } else {
                $del = $this->conn->prepare("DELETE FROM grades WHERE enrollment_id=?");
                $del->bind_param('i', $enrollmentId);
                $del->execute();
            }
        } else {
            if ($score !== null) {
                $ins = $this->conn->prepare(
                    "INSERT INTO grades (enrollment_id, score, grade_letter) VALUES (?,?,?)"
                );
                $ins->bind_param('ids', $enrollmentId, $score, $letter);
                $ins->execute();
                // Cập nhật trạng thái enrollment → Completed
                $upd2 = $this->conn->prepare(
                    "UPDATE enrollments SET status='Completed' WHERE enrollment_id=?"
                );
                $upd2->bind_param('i', $enrollmentId);
                $upd2->execute();
            }
        }
        return $letter ?? '';
    }

    /**
     * Thêm sinh viên vào lớp
     */
    // public function addStudentToClass(int $classId, string $studentCode): array
    // {
    //     // Tìm sinh viên theo mã
    //     $sv = $this->conn->prepare(
    //         "SELECT student_id, student_code,
    //                 CONCAT(first_name,' ',last_name) AS full_name
    //          FROM students WHERE student_code=? AND status='Studying'"
    //     );
    //     $sv->bind_param('s', $studentCode);
    //     $sv->execute();
    //     $student = $sv->get_result()->fetch_assoc();

    //     if (!$student) {
    //         return ['success' => false, 'message' => "Không tìm thấy sinh viên \"$studentCode\" hoặc không trong trạng thái Đang học."];
    //     }

    //     // Kiểm tra đã đăng ký chưa
    //     $chk = $this->conn->prepare(
    //         "SELECT enrollment_id, status FROM enrollments WHERE student_id=? AND class_id=?"
    //     );
    //     $chk->bind_param('ii', $student['student_id'], $classId);
    //     $chk->execute();
    //     $existing = $chk->get_result()->fetch_assoc();

    //     if ($existing) {
    //         if ($existing['status'] === 'Cancelled') {
    //             $re = $this->conn->prepare(
    //                 "UPDATE enrollments SET status='Registered' WHERE enrollment_id=?"
    //             );
    //             $re->bind_param('i', $existing['enrollment_id']);
    //             $re->execute();
    //             return ['success' => true, 'message' => "Đã kích hoạt lại đăng ký cho {$student['full_name']}."];
    //         }
    //         return ['success' => false, 'message' => "{$student['full_name']} đã được đăng ký trong lớp này."];
    //     }

    //     $ins = $this->conn->prepare(
    //         "INSERT INTO enrollments (student_id, class_id, status) VALUES (?,?,'Registered')"
    //     );
    //     $ins->bind_param('ii', $student['student_id'], $classId);
    //     $ins->execute();
    //     return ['success' => true, 'message' => "Đã thêm {$student['full_name']} vào lớp!"];
    // }

    public function addStudentToClass(int $classId, string $studentCode): array
{
    // tìm sinh viên
    $sv = $this->conn->prepare("
        SELECT student_id, student_code,
        CONCAT(first_name,' ',last_name) AS full_name
        FROM students
        WHERE student_code=? AND status='Studying'
    ");

    $sv->bind_param('s', $studentCode);
    $sv->execute();

    $student = $sv->get_result()->fetch_assoc();

    if (!$student) {
        return [
            'success' => false,
            'message' => "Không tìm thấy sinh viên \"$studentCode\" hoặc không trong trạng thái Đang học."
        ];
    }

    // kiểm tra đã đăng ký chưa
    $chk = $this->conn->prepare("
        SELECT enrollment_id, status
        FROM enrollments
        WHERE student_id=? AND class_id=?
    ");

    $chk->bind_param('ii', $student['student_id'], $classId);
    $chk->execute();

    $existing = $chk->get_result()->fetch_assoc();

    if ($existing) {

        // nếu trước đó đã Withdrawn thì kích hoạt lại
        if ($existing['status'] === 'Withdrawn') {

            $re = $this->conn->prepare("
                UPDATE enrollments
                SET status='Enrolled'
                WHERE enrollment_id=?
            ");

            $re->bind_param('i', $existing['enrollment_id']);
            $re->execute();

            return [
                'success' => true,
                'message' => "Đã kích hoạt lại đăng ký cho {$student['full_name']}."
            ];
        }

        return [
            'success' => false,
            'message' => "{$student['full_name']} đã có trong lớp."
        ];
    }

    // thêm mới
    $ins = $this->conn->prepare("
        INSERT INTO enrollments (student_id, class_id, status)
        VALUES (?, ?, 'Enrolled')
    ");

    $ins->bind_param('ii', $student['student_id'], $classId);
    $ins->execute();

    return [
        'success' => true,
        'message' => "Đã thêm {$student['full_name']} vào lớp!"
    ];
}


    /**
     * Xóa sinh viên khỏi lớp (chuyển sang Cancelled)
     */
    // public function removeStudentFromClass(int $enrollmentId, int $classId): bool
    // {
    //     $stmt = $this->conn->prepare(
    //         "UPDATE enrollments SET status='Cancelled'
    //          WHERE enrollment_id=? AND class_id=?"
    //     );
    //     $stmt->bind_param('ii', $enrollmentId, $classId);
    //     return $stmt->execute() && $stmt->affected_rows > 0;
    // }

    public function removeStudentFromClass(int $enrollmentId, int $classId): bool
{
    $stmt = $this->conn->prepare("
        UPDATE enrollments
        SET status='Withdrawn'
        WHERE enrollment_id=? AND class_id=?
    ");

    $stmt->bind_param('ii', $enrollmentId, $classId);

    return $stmt->execute() && $stmt->affected_rows > 0;
}


    // ═══════════════════════════════════════════════
    // ĐIỂM DANH (ATTENDANCE)
    // ═══════════════════════════════════════════════

    /**
     * Lấy danh sách sinh viên đã đăng ký trong lớp
     */
    // public function getEnrolledStudents(int $classId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT s.student_id, s.student_code,
    //                CONCAT(s.first_name,' ',s.last_name) AS full_name,
    //                s.email
    //         FROM enrollments e
    //         JOIN students s ON e.student_id = s.student_id
    //         WHERE e.class_id = ? AND e.status IN ('Registered','Completed')
    //         ORDER BY s.student_code ASC
    //     ");
    //     $stmt->bind_param('i', $classId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getEnrolledStudents(int $classId): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            s.student_id,
            s.student_code,
            CONCAT(s.first_name,' ',s.last_name) AS full_name,
            s.email

        FROM enrollments e
        JOIN students s 
            ON e.student_id = s.student_id

        WHERE e.class_id = ?
        AND e.status IN ('Enrolled','Completed')

        ORDER BY s.student_code ASC
    ");

    $stmt->bind_param('i', $classId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Lấy điểm danh của một ngày cụ thể
     */
    // public function getAttendanceByDate(int $classId, string $date): array
    // {
    //     $stmt = $this->conn->prepare(
    //         "SELECT student_id, status, note FROM attendance
    //          WHERE class_id=? AND date=?"
    //     );
    //     $stmt->bind_param('is', $classId, $date);
    //     $stmt->execute();
    //     $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    //     // index by student_id
    //     $indexed = [];
    //     foreach ($rows as $row) {
    //         $indexed[$row['student_id']] = $row;
    //     }
    //     return $indexed;
    // }

    public function getAttendanceByDate(int $classId, string $date): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            e.student_id,
            a.status,
            a.note

        FROM attendance a
        JOIN enrollments e 
            ON a.enrollment_id = e.enrollment_id

        WHERE e.class_id = ?
        AND a.date = ?
    ");

    $stmt->bind_param('is', $classId, $date);
    $stmt->execute();

    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // index theo student_id
    $indexed = [];
    foreach ($rows as $row) {
        $indexed[$row['student_id']] = $row;
    }

    return $indexed;
}


    /**
     * Lưu điểm danh (UPSERT)
     */
    // public function saveAttendance(int $classId, int $studentId, string $date, string $status, ?string $note): bool
    // {
    //     $stmt = $this->conn->prepare("
    //         INSERT INTO attendance (class_id, student_id, date, status, note)
    //         VALUES (?, ?, ?, ?, ?)
    //         ON DUPLICATE KEY UPDATE status=VALUES(status), note=VALUES(note)
    //     ");
    //     $stmt->bind_param('iisss', $classId, $studentId, $date, $status, $note);
    //     return $stmt->execute();
    // }

    public function saveAttendance(
    int $classId,
    int $studentId,
    string $date,
    string $status,
    ?string $note
): bool {

    // tìm enrollment_id
    $en = $this->conn->prepare("
        SELECT enrollment_id
        FROM enrollments
        WHERE class_id=? AND student_id=?
        LIMIT 1
    ");

    $en->bind_param('ii', $classId, $studentId);
    $en->execute();

    $result = $en->get_result()->fetch_assoc();

    if (!$result) {
        return false;
    }

    $enrollmentId = $result['enrollment_id'];

    // insert attendance
    $stmt = $this->conn->prepare("
        INSERT INTO attendance (enrollment_id, date, status, note)
        VALUES (?, ?, ?, ?)

        ON DUPLICATE KEY UPDATE
            status = VALUES(status),
            note   = VALUES(note)
    ");

    $stmt->bind_param(
        'isss',
        $enrollmentId,
        $date,
        $status,
        $note
    );

    return $stmt->execute();
}


    /**
     * Xóa toàn bộ điểm danh của một buổi
     */
    // public function deleteAttendanceByDate(int $classId, string $date): int
    // {
    //     $stmt = $this->conn->prepare(
    //         "DELETE FROM attendance WHERE class_id=? AND date=?"
    //     );
    //     $stmt->bind_param('is', $classId, $date);
    //     $stmt->execute();
    //     return $stmt->affected_rows;
    // }

    public function deleteAttendanceByDate(int $classId, string $date): int
{
    $stmt = $this->conn->prepare("
        DELETE a
        FROM attendance a
        JOIN enrollments e ON a.enrollment_id = e.enrollment_id
        WHERE e.class_id=? AND a.date=?
    ");
    $stmt->bind_param('is', $classId, $date);
    $stmt->execute();
    return $stmt->affected_rows;
}


    /**
     * Lịch sử điểm danh (theo ngày)
     */
    // public function getAttendanceHistory(int $classId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT a.date,
    //                COUNT(*)                    AS total,
    //                SUM(a.status='Present')     AS present,
    //                SUM(a.status='Absent')      AS absent,
    //                SUM(a.status='Late')        AS late,
    //                SUM(a.status='Excused')     AS excused
    //         FROM attendance a
    //         WHERE a.class_id=?
    //         GROUP BY a.date
    //         ORDER BY a.date DESC
    //     ");
    //     $stmt->bind_param('i', $classId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getAttendanceHistory(int $classId): array
{
    $stmt = $this->conn->prepare("
        SELECT a.date,
               COUNT(*)                AS total,
               SUM(a.status='Present') AS present,
               SUM(a.status='Absent')  AS absent,
               SUM(a.status='Late')    AS late,
               SUM(a.status='Excused') AS excused
        FROM attendance a
        JOIN enrollments e ON a.enrollment_id = e.enrollment_id
        WHERE e.class_id=?
        GROUP BY a.date
        ORDER BY a.date DESC
    ");
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Chi tiết điểm danh của một ngày
     */
    // public function getAttendanceDetail(int $classId, string $date): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT s.student_id, s.student_code,
    //                CONCAT(s.first_name,' ',s.last_name) AS full_name,
    //                a.status, a.note, a.updated_at
    //         FROM attendance a
    //         JOIN students s ON a.student_id=s.student_id
    //         WHERE a.class_id=? AND a.date=?
    //         ORDER BY s.student_code
    //     ");
    //     $stmt->bind_param('is', $classId, $date);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getAttendanceDetail(int $classId, string $date): array
{
    $stmt = $this->conn->prepare("
        SELECT s.student_id,
               s.student_code,
               CONCAT(s.first_name,' ',s.last_name) AS full_name,
               a.status,
               a.note
        FROM attendance a
        JOIN enrollments e ON a.enrollment_id = e.enrollment_id
        JOIN students s ON e.student_id = s.student_id
        WHERE e.class_id=? AND a.date=?
        ORDER BY s.student_code
    ");
    $stmt->bind_param('is', $classId, $date);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Tổng hợp điểm danh theo sinh viên
     */
    // public function getStudentAttendanceSummary(int $classId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT s.student_id, s.student_code,
    //                CONCAT(s.first_name,' ',s.last_name) AS full_name,
    //                COUNT(a.attendance_id)       AS total_sessions,
    //                SUM(a.status='Present')      AS present,
    //                SUM(a.status='Absent')       AS absent,
    //                SUM(a.status='Late')         AS late,
    //                SUM(a.status='Excused')      AS excused
    //         FROM enrollments e
    //         JOIN students s ON e.student_id=s.student_id
    //         LEFT JOIN attendance a ON a.student_id=s.student_id AND a.class_id=e.class_id
    //         WHERE e.class_id=? AND e.status IN ('Registered','Completed')
    //         GROUP BY s.student_id
    //         ORDER BY s.student_code
    //     ");
    //     $stmt->bind_param('i', $classId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getStudentAttendanceSummary(int $classId): array
{
    $stmt = $this->conn->prepare("
        SELECT s.student_id,
               s.student_code,
               CONCAT(s.first_name,' ',s.last_name) AS full_name,
               COUNT(a.attendance_id)       AS total_sessions,
               SUM(a.status='Present')      AS present,
               SUM(a.status='Absent')       AS absent,
               SUM(a.status='Late')         AS late,
               SUM(a.status='Excused')      AS excused
        FROM enrollments e
        JOIN students s ON e.student_id=s.student_id
        LEFT JOIN attendance a ON a.enrollment_id=e.enrollment_id
        WHERE e.class_id=? AND e.status IN ('Enrolled','Completed')
        GROUP BY s.student_id
        ORDER BY s.student_code
    ");
    $stmt->bind_param('i', $classId);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Tổng số buổi đã điểm danh
     */
    // public function getTotalSessionsHeld(int $classId): int
    // {
    //     $stmt = $this->conn->prepare(
    //         "SELECT COUNT(DISTINCT date) AS cnt FROM attendance WHERE class_id=?"
    //     );
    //     $stmt->bind_param('i', $classId);
    //     $stmt->execute();
    //     return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    // }

    public function getTotalSessionsHeld(int $classId): int
{
    $stmt = $this->conn->prepare("
        SELECT COUNT(DISTINCT a.date) AS cnt
        FROM attendance a
        JOIN enrollments e ON a.enrollment_id = e.enrollment_id
        WHERE e.class_id = ?
    ");
    $stmt->bind_param('i', $classId);
    $stmt->execute();

    return (int)$stmt->get_result()->fetch_assoc()['cnt'];
}


    // ═══════════════════════════════════════════════
    // ĐĂNG KÝ LỚP DẠY (REGISTER)
    // ═══════════════════════════════════════════════

    /**
     * Lấy toàn bộ danh sách môn học
     */
    public function getAllSubjects(): array
    {
        return $this->conn->query(
            "SELECT subject_id, subject_code, subject_name, credit_hours
             FROM subjects ORDER BY subject_name ASC"
        )->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy thông tin môn học theo ID
     */
    public function getSubjectById(int $subjectId): ?array
    {
        $stmt = $this->conn->prepare(
            "SELECT subject_id, subject_code, subject_name, credit_hours
             FROM subjects WHERE subject_id=?"
        );
        $stmt->bind_param('i', $subjectId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Kiểm tra giảng viên đã đăng ký môn/kỳ/năm này chưa
     */
    // public function hasRegisteredClass(int $lecturerId, int $subjectId, string $semester, int $year): bool
    // {
    //     $stmt = $this->conn->prepare(
    //         "SELECT class_id FROM classes
    //          WHERE lecturer_id=? AND subject_id=? AND semester=? AND year=?
    //          LIMIT 1"
    //     );
    //     $stmt->bind_param('iisi', $lecturerId, $subjectId, $semester, $year);
    //     $stmt->execute();
    //     return (bool)$stmt->get_result()->fetch_assoc();
    // }

    public function hasRegisteredClass(int $lecturerId, int $subjectId, int $semesterId): bool
{
    $stmt = $this->conn->prepare("
        SELECT class_id
        FROM classes
        WHERE lecturer_id=? 
          AND subject_id=? 
          AND semester_id=?
        LIMIT 1
    ");

    $stmt->bind_param('iii', $lecturerId, $subjectId, $semesterId);
    $stmt->execute();

    return (bool)$stmt->get_result()->fetch_assoc();
}


    /**
     * Tìm lớp chưa có giảng viên cho môn/kỳ/năm
     */
    // public function findUnassignedClass(int $subjectId, string $semester, int $year): ?array
    // {
    //     $stmt = $this->conn->prepare(
    //         "SELECT class_id, class_code FROM classes
    //          WHERE lecturer_id IS NULL AND subject_id=? AND semester=? AND year=?
    //          LIMIT 1"
    //     );
    //     $stmt->bind_param('isi', $subjectId, $semester, $year);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_assoc() ?: null;
    // }

    public function findUnassignedClass(int $subjectId, int $semesterId): ?array
{
    $stmt = $this->conn->prepare("
        SELECT class_id, class_code
        FROM classes
        WHERE lecturer_id IS NULL
          AND subject_id=?
          AND semester_id=?
        LIMIT 1
    ");

    $stmt->bind_param('ii', $subjectId, $semesterId);
    $stmt->execute();

    return $stmt->get_result()->fetch_assoc() ?: null;
}


    /**
     * Gán giảng viên vào lớp có sẵn
     */
    public function assignLecturerToClass(int $classId, int $lecturerId): bool
    {
        $stmt = $this->conn->prepare(
            "UPDATE classes SET lecturer_id=? WHERE class_id=?"
        );
        $stmt->bind_param('ii', $lecturerId, $classId);
        return $stmt->execute();
    }

    /**
     * Tạo lớp mới với mã tự sinh
     */
    // public function createNewClass(int $subjectId, int $lecturerId, string $semester, int $year): array
    // {
    //     $sub    = $this->getSubjectById($subjectId);
    //     $prefix = strtoupper($sub['subject_code'] ?? 'CLASS');

    //     // Lấy sequence tiếp theo
    //     $seqStmt = $this->conn->prepare("
    //         SELECT MAX(CAST(SUBSTRING(class_code, LENGTH(?)+2) AS UNSIGNED)) AS maxseq
    //         FROM classes WHERE class_code LIKE CONCAT(?, '-%')
    //     ");
    //     $seqStmt->bind_param('ss', $prefix, $prefix);
    //     $seqStmt->execute();
    //     $seq     = (int)($seqStmt->get_result()->fetch_assoc()['maxseq'] ?? 0) + 1;
    //     $newCode = $prefix . '-' . str_pad($seq, 2, '0', STR_PAD_LEFT);

    //     $ins = $this->conn->prepare(
    //         "INSERT INTO classes (class_code, subject_id, lecturer_id, semester, year)
    //          VALUES (?,?,?,?,?)"
    //     );
    //     $ins->bind_param('siisi', $newCode, $subjectId, $lecturerId, $semester, $year);

    //     if ($ins->execute()) {
    //         return ['success' => true, 'class_code' => $newCode];
    //     }
    //     return ['success' => false, 'message' => $this->conn->error];
    // }

    public function createNewClass(int $subjectId, int $lecturerId, int $semesterId): array
{
    $sub = $this->getSubjectById($subjectId);
    $prefix = strtoupper($sub['subject_code'] ?? 'CLASS');

    // Lấy sequence tiếp theo
    $seqStmt = $this->conn->prepare("
        SELECT MAX(CAST(SUBSTRING(class_code, LENGTH(?)+2) AS UNSIGNED)) AS maxseq
        FROM classes 
        WHERE class_code LIKE CONCAT(?, '-%')
    ");
    $seqStmt->bind_param('ss', $prefix, $prefix);
    $seqStmt->execute();

    $seq = (int)($seqStmt->get_result()->fetch_assoc()['maxseq'] ?? 0) + 1;

    $newCode = $prefix . '-' . str_pad($seq, 2, '0', STR_PAD_LEFT);

    $ins = $this->conn->prepare("
        INSERT INTO classes 
        (class_code, subject_id, lecturer_id, semester_id)
        VALUES (?,?,?,?)
    ");

    $ins->bind_param('siii', $newCode, $subjectId, $lecturerId, $semesterId);

    if ($ins->execute()) {
        return [
            'success' => true,
            'class_id' => $ins->insert_id,
            'class_code' => $newCode
        ];
    }

    return [
        'success' => false,
        'message' => $this->conn->error
    ];
}


    /**
     * Lấy tất cả lớp của giảng viên (dùng cho dropdown + danh sách)
     */
 public function getAllLecturerClasses(int $lecturerId, int $limit = 200): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            c.class_id, 
            c.class_code,
            s.semester_code,
            s.semester_name,
            sub.subject_name, 
            sub.credit_hours, 
            sub.subject_code,
            COUNT(DISTINCT e.enrollment_id) AS student_count
        FROM classes c
        JOIN semesters s ON s.semester_id = c.semester_id
        JOIN subjects sub ON c.subject_id = sub.subject_id
        LEFT JOIN enrollments e 
            ON c.class_id = e.class_id
            AND e.status IN ('Registered','Completed')
        WHERE c.lecturer_id = ?
        GROUP BY c.class_id
        ORDER BY s.start_date DESC
        LIMIT ?
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return [];
    }

    $stmt->bind_param('ii', $lecturerId, $limit);

    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        return [];
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

    /**
     * Verify sinh viên có đăng ký vào lớp không (dùng cho attendance)
     */
    public function isStudentEnrolled(int $studentId, int $classId): bool
    {
        $stmt = $this->conn->prepare(
            "SELECT enrollment_id FROM enrollments
             WHERE student_id=? AND class_id=? AND status IN ('Registered','Completed')
             LIMIT 1"
        );
        $stmt->bind_param('ii', $studentId, $classId);
        $stmt->execute();
        return (bool)$stmt->get_result()->fetch_assoc();
    }

    // ═══════════════════════════════════════════════
    // HELPER
    // ═══════════════════════════════════════════════

    /**
     * Tính grade letter từ điểm số
     */
    public function calcGradeLetter(float $score): string
    {
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
     * Format tên học kỳ hiển thị
     */
    // public static function formatSemester(string $semester): string
    // {
    //     $map = [
    //         'Spring' => 'Học kỳ I',
    //         'Summer' => 'Học kỳ Hè',
    //         'Fall'   => 'Học kỳ II',
    //     ];
    //     return $map[$semester] ?? $semester;
    // }
    public static function formatSemester(?string $semester): string
{
    if (!$semester) return '';

    $map = [
        'Spring' => 'Học kỳ I',
        'Summer' => 'Học kỳ Hè',
        'Fall'   => 'Học kỳ II',
    ];

    return $map[$semester] ?? $semester;
}


    /**
     * Format trạng thái đăng ký
     */
    public static function formatEnrollStatus(string $status): string
    {
        $map = [
            'Registered' => 'Đang học',
            'Completed'  => 'Hoàn thành',
            'Cancelled'  => 'Đã hủy',
            'Dropped'    => 'Bỏ học',
        ];
        return $map[$status] ?? $status;
    }
}
