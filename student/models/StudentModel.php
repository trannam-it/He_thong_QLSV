<?php
/**
 * StudentModel - Model dành cho vai trò Sinh viên
 * Chứa toàn bộ logic truy vấn database cho sinh viên
 * Tuân theo chuẩn MVC: tách biệt hoàn toàn data access khỏi view
 */
// getAvailableClasses
// isEnrollmentPeriodActive
// getMyEnrollments
// Enrolled
// getStatusBadgeClass
// formatEnrollmentStatus
// cancelEnrollment
// getEnrolledSemesterCredits
// getStudentByUserId
// getOverviewByUserId
// getStudentOverview
class StudentModel
{
    protected mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Helper: Prepare statement với error logging
     * Trả về mysqli_stmt hoặc null nếu prepare fail
     */
    protected function safePrepare(string $sql): ?\mysqli_stmt
    {
        $stmt = $this->conn->prepare($sql);
        if ($stmt === false) {
            error_log('[StudentModel::safePrepare] Prepare failed: ' . $this->conn->error . ' | SQL: ' . substr($sql, 0, 100));
            return null;
        }
        return $stmt;
    }

    public function getStudentByUserId($userId)
    {
        $sql = "SELECT * FROM students WHERE user_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // ══════════════════════════════════════════════════════
    // THÔNG TIN SINH VIÊN
    // ══════════════════════════════════════════════════════

    /**
     * Lấy thông tin tổng quan của sinh viên theo user_id
     */
    public function getOverviewByUserId(int $userId): ?array
    {
    //     var_dump($userId);   // 👈 THÊM DÒNG NÀY
    // exit; 

        $stmt = $this->conn->prepare("
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
            LEFT JOIN faculties  f  ON s.faculty_id      = f.faculty_id
            LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
            WHERE s.user_id = ?
            LIMIT 1
        ");
        // $stmt->bind_param('i', $userId);
        // $stmt->execute();
        // $result = $stmt->get_result()->fetch_assoc();

        // if (!$result) {
        //     return [
        //         'student_id'      => 0,
        //         'student_code'    => 'N/A',
        //         'full_name'       => 'Chưa có thông tin',
        //         'email'           => '',
        //         'phone'           => '',
        //         'gender'          => 'Male',
        //         'birth_date'      => date('Y-m-d'),
        //         'faculty_name'    => 'Chưa xác định',
        //         'base_class_name' => 'Chưa có lớp',
        //         'status'          => 'Studying',
        //     ];
        // }

        // return $result;

        $stmt->bind_param('i', $userId);
$stmt->execute();

// $result = $stmt->get_result();
// var_dump($result->num_rows);
// exit;
// $db = $this->conn->query("SELECT DATABASE()")->fetch_row()[0];
// var_dump("Current DB:", $db);
// exit;

$stmt->bind_result(
    $student_id,
    $student_code,
    $full_name,
    $email,
    $phone,
    $gender,
    $birth_date,
    $faculty_name,
    $base_class_name,
    $status
);

if ($stmt->fetch()) {
    return [
        'student_id'      => $student_id,
        'student_code'    => $student_code,
        'full_name'       => $full_name,
        'email'           => $email,
        'phone'           => $phone,
        'gender'          => $gender,
        'birth_date'      => $birth_date,
        'faculty_name'    => $faculty_name,
        'base_class_name' => $base_class_name,
        'status'          => $status,
    ];
}

return [
    'student_id'      => 0,
    'student_code'    => 'N/A',
    'full_name'       => 'Chưa có thông tin',
    'email'           => '',
    'phone'           => '',
    'gender'          => 'Male',
    'birth_date'      => date('Y-m-d'),
    'faculty_name'    => 'Chưa xác định',
    'base_class_name' => 'Chưa có lớp',
    'status'          => 'Studying',
];
// var_dump("FETCH FAIL");
// exit;
    }

    /**
     * Cập nhật thông tin liên hệ (phone, email)
     */
    public function updateContactInfo(int $studentId, string $phone, string $email): bool
    {
        $stmt = $this->conn->prepare(
            'UPDATE students SET phone = ?, email = ? WHERE student_id = ?'
        );
        $stmt->bind_param('ssi', $phone, $email, $studentId);
        return $stmt->execute();
    }

    /**
     * Đổi mật khẩu người dùng
     */
    public function changePassword(int $userId, string $newHash): bool
    {
        $stmt = $this->conn->prepare(
            'UPDATE users SET password_hash = ? WHERE id = ?'
        );
        $stmt->bind_param('si', $newHash, $userId);
        return $stmt->execute();
    }

    /**
     * Lấy password hash hiện tại của user
     */
    public function getPasswordHash(int $userId): ?string
    {
        $stmt = $this->conn->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['password_hash'] ?? null;
    }

    // ══════════════════════════════════════════════════════
    // GPA & ĐIỂM SỐ
    // ══════════════════════════════════════════════════════

    /**
     * Tính GPA và thống kê tích lũy của sinh viên
     */
    public function calculateGPA(int $studentId): array
    {
        $stmt = $this->safePrepare("
            SELECT
                ROUND(AVG(g.score), 2)                                             AS gpa,
                COUNT(DISTINCT e.class_id)                                         AS total_courses,
                SUM(CASE WHEN e.status = 'Completed' THEN sub.credit_hours ELSE 0 END) AS total_credits
            FROM enrollments e
            JOIN classes  c   ON e.class_id    = c.class_id
            JOIN subjects sub ON c.subject_id  = sub.subject_id
            LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
            WHERE e.student_id = ? AND e.status IN ('Completed','Enrolled')
        ");
        if (!$stmt) return ['gpa' => null, 'total_courses' => 0, 'total_credits' => 0];
        
        $stmt->bind_param('i', $studentId);
        if (!$stmt->execute()) {
            error_log('[StudentModel::calculateGPA] Execute failed: ' . $stmt->error);
            return ['gpa' => null, 'total_courses' => 0, 'total_credits' => 0];
        }
        
        $result = $stmt->get_result()->fetch_assoc();
        return $result ?: ['gpa' => null, 'total_courses' => 0, 'total_credits' => 0];
    }

    /**
     * Lấy toàn bộ kết quả học tập (tất cả trạng thái)
     */
    // public function getAllGrades(int $studentId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT
    //             e.enrollment_id,
    //             e.status        AS enroll_status,
    //             c.class_code,
    //             c.semester,
    //             c.year,
    //             sub.subject_code,
    //             sub.subject_name,
    //             sub.credit_hours,
    //             CONCAT(l.last_name,' ',l.first_name) AS lecturer_name,
    //             g.score,
    //             g.grade_letter
    //         FROM enrollments e
    //         JOIN classes  c   ON c.class_id    = e.class_id
    //         JOIN subjects sub ON sub.subject_id = c.subject_id
    //         LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
    //         LEFT JOIN grades    g ON g.enrollment_id = e.enrollment_id
    //         WHERE e.student_id = ?
    //         ORDER BY c.year DESC,
    //                  FIELD(c.semester,'Fall','Summer','Spring'),
    //                  sub.subject_name
    //     ");
    //     $stmt->bind_param('i', $studentId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getAllGrades(int $studentId): array
{
    $stmt = $this->conn->prepare("
        SELECT
            e.enrollment_id,
            e.status AS enroll_status,

            c.class_code,

            sem.semester_name AS semester,
            YEAR(sem.start_date) AS year,

            sub.subject_code,
            sub.subject_name,
            sub.credit_hours,

            CONCAT(l.last_name,' ',l.first_name) AS lecturer_name,

            g.score,
            g.grade_letter

        FROM enrollments e

        JOIN classes c 
            ON c.class_id = e.class_id

        JOIN subjects sub 
            ON sub.subject_id = c.subject_id

        LEFT JOIN lecturers l 
            ON l.lecturer_id = c.lecturer_id

        LEFT JOIN semesters sem
            ON sem.semester_id = c.semester_id

        LEFT JOIN grades g 
            ON g.enrollment_id = e.enrollment_id

        WHERE e.student_id = ?

        ORDER BY 
            YEAR(sem.start_date) DESC,
            FIELD(sem.semester_name,'Fall','Summer','Spring'),
            sub.subject_name
    ");

    $stmt->bind_param('i', $studentId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Lấy điểm gần nhất (dùng cho dashboard)
     */
    // public function getRecentGrades(int $studentId, int $limit = 10): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT
    //             sub.subject_code,
    //             sub.subject_name,
    //             sub.credit_hours,
    //             g.score,
    //             g.grade_letter,
    //             c.semester,
    //             c.year
    //         FROM enrollments e
    //         JOIN classes  c   ON e.class_id    = c.class_id
    //         JOIN subjects sub ON c.subject_id  = sub.subject_id
    //         LEFT JOIN grades g ON e.enrollment_id = g.enrollment_id
    //         WHERE e.student_id = ? AND e.status = 'Completed'
    //         ORDER BY c.year DESC, c.semester DESC
    //         LIMIT ?
    //     ");
        
    //     if ($stmt === false) {
    //         error_log('[StudentModel::getRecentGrades] Prepare failed: ' . $this->conn->error);
    //         return [];
    //     }
        
    //     if (!$stmt->bind_param('ii', $studentId, $limit)) {
    //         error_log('[StudentModel::getRecentGrades] bind_param failed: ' . $stmt->error);
    //         return [];
    //     }
        
    //     if (!$stmt->execute()) {
    //         error_log('[StudentModel::getRecentGrades] execute failed: ' . $stmt->error);
    //         return [];
    //     }
        
    //     $res = $stmt->get_result();
    //     if ($res === false) {
    //         error_log('[StudentModel::getRecentGrades] get_result failed: ' . $stmt->error);
    //         return [];
    //     }
        
    //     return $res->fetch_all(MYSQLI_ASSOC);
    // }

    public function getRecentGrades(int $studentId, int $limit = 10): array
{
    $stmt = $this->conn->prepare("
        SELECT
            sub.subject_code,
            sub.subject_name,
            sub.credit_hours,
            g.score,
            g.grade_letter,

            sem.semester_name AS semester,
            YEAR(sem.start_date) AS year

        FROM enrollments e

        JOIN classes c 
            ON e.class_id = c.class_id

        JOIN subjects sub 
            ON c.subject_id = sub.subject_id

        LEFT JOIN semesters sem
            ON sem.semester_id = c.semester_id

        LEFT JOIN grades g 
            ON e.enrollment_id = g.enrollment_id

        WHERE e.student_id = ?
        AND e.status = 'Completed'

        ORDER BY 
            YEAR(sem.start_date) DESC,
            FIELD(sem.semester_name,'Fall','Summer','Spring')

        LIMIT ?
    ");

    if ($stmt === false) {
        error_log('[StudentModel::getRecentGrades] Prepare failed: ' . $this->conn->error);
        return [];
    }

    if (!$stmt->bind_param('ii', $studentId, $limit)) {
        error_log('[StudentModel::getRecentGrades] bind_param failed: ' . $stmt->error);
        return [];
    }

    if (!$stmt->execute()) {
        error_log('[StudentModel::getRecentGrades] execute failed: ' . $stmt->error);
        return [];
    }

    $res = $stmt->get_result();
    if ($res === false) {
        error_log('[StudentModel::getRecentGrades] get_result failed: ' . $stmt->error);
        return [];
    }

    return $res->fetch_all(MYSQLI_ASSOC);
}


    // ══════════════════════════════════════════════════════
    // ĐĂNG KÝ HỌC PHẦN (ENROLLMENT)
    // ══════════════════════════════════════════════════════

    /**
     * Lấy danh sách học phần sinh viên đã đăng ký
     */
    // public function getMyEnrollments(int $studentId): array
    // {
    //     $stmt = $this->safePrepare("
    //         SELECT
    //             e.enrollment_id,
    //             e.status,
    //             e.registration_date,
    //             c.class_id,
    //             c.class_code,
    //             c.semester,
    //             c.year,
    //             sub.subject_code,
    //             sub.subject_name,
    //             sub.credit_hours,
    //             CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
    //             g.score,
    //             g.grade_letter
    //         FROM enrollments e
    //         JOIN classes c      ON c.class_id    = e.class_id
    //         JOIN subjects sub   ON sub.subject_id = c.subject_id
    //         LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
    //         LEFT JOIN grades g  ON g.enrollment_id = e.enrollment_id
    //         WHERE e.student_id = ?
    //         ORDER BY e.registration_date DESC
    //     ");
    //     if (!$stmt) return [];
        
    //     $stmt->bind_param('i', $studentId);
    //     if (!$stmt->execute()) {
    //         error_log('[StudentModel::getMyEnrollments] Execute failed: ' . $stmt->error);
    //         return [];
    //     }
        
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    // }

    public function getMyEnrollments(int $studentId): array
{
    $stmt = $this->safePrepare("
        SELECT
            e.enrollment_id,
            e.status,
            e.enrollment_date,

            c.class_id,
            c.class_code,

            sem.semester_name AS semester,
            YEAR(sem.start_date) AS year,

            sub.subject_code,
            sub.subject_name,
            sub.credit_hours,

            CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,

            g.score,
            g.grade_letter

        FROM enrollments e

        JOIN classes c 
            ON c.class_id = e.class_id

        JOIN subjects sub 
            ON sub.subject_id = c.subject_id

        LEFT JOIN lecturers l 
            ON l.lecturer_id = c.lecturer_id

        LEFT JOIN semesters sem
            ON sem.semester_id = c.semester_id

        LEFT JOIN grades g
            ON g.enrollment_id = e.enrollment_id

        WHERE e.student_id = ?

        ORDER BY e.enrollment_date DESC
    ");

    if (!$stmt) return [];

    $stmt->bind_param('i', $studentId);

    if (!$stmt->execute()) {
        error_log('[StudentModel::getMyEnrollments] Execute failed: ' . $stmt->error);
        return [];
    }

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
}


    /**
     * Lấy danh sách lớp học phần còn mở để đăng ký
     */
    public function getAvailableClasses(int $studentId, int $fromYear): array
    {
        // Check enrollment period is active

        // test
        // if (!$this->isEnrollmentPeriodActive()) {
        //     return []; // Enrollment period closed
        // }

        // Get student's faculty
        $studentStmt = $this->safePrepare("SELECT faculty_id FROM students WHERE student_id = ? LIMIT 1");
        if (!$studentStmt) return [];
        $studentStmt->bind_param('i', $studentId);
        $studentStmt->execute();
        $studentRow = $studentStmt->get_result()->fetch_assoc();
        $studentFacultyId = $studentRow ? (int)$studentRow['faculty_id'] : 0;

        // Get available classes filtered by:
        // 1. Year >= fromYear
        // 2. Subject belongs to student's faculty OR subject is shared (faculty_id = 0)
        // 3. Student not already enrolled
        // $stmt = $this->safePrepare("
        //     SELECT
        //         c.class_id,
        //         c.class_code,
        //         c.semester,
        //         c.year,
        //         sub.subject_code,
        //         sub.subject_name,
        //         sub.credit_hours,
        //         CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
        //         f.faculty_name,
        //         COUNT(DISTINCT e2.enrollment_id) AS enrolled_count,
        //         GROUP_CONCAT(
        //             DISTINCT CONCAT(cs.day_of_week,'|',cs.start_period,'|',cs.end_period,'|',COALESCE(cs.room,''))
        //             ORDER BY cs.day_of_week, cs.start_period
        //             SEPARATOR ';'
        //         ) AS schedule_raw
        //     FROM classes c
        //     JOIN subjects sub ON sub.subject_id = c.subject_id
        //     LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
        //     LEFT JOIN faculties f ON f.faculty_id = l.faculty_id
        //     LEFT JOIN enrollments e2 ON e2.class_id = c.class_id
        //     LEFT JOIN class_schedules cs ON cs.class_id = c.class_id
        //     WHERE c.year >= ?
        //       AND (sub.faculty_id = ? OR sub.faculty_id = 0)
        //       AND c.class_id NOT IN (
        //           SELECT class_id FROM enrollments
        //           WHERE student_id = ? AND status IN ('Enrolled','Completed')
        //       )
        //     GROUP BY c.class_id, c.class_code, c.semester, c.year,
        //              sub.subject_code, sub.subject_name, sub.credit_hours,
        //              l.last_name, l.first_name, f.faculty_name
        //     ORDER BY c.year DESC, c.semester, sub.subject_name
        // ");

        // $stmt = $this->safePrepare("
        //     SELECT
        //         c.class_id,
        //         c.class_code,
        //         sub.subject_code,
        //         sub.subject_name,
        //         sub.credit_hours,
        //         CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
        //         sem.semester_name,
        //         COUNT(DISTINCT e2.enrollment_id) AS enrolled_count
        //     FROM classes c
        //     JOIN subjects sub ON sub.subject_id = c.subject_id
        //     JOIN semesters sem ON sem.semester_id = c.semester_id
        //     LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
        //     LEFT JOIN enrollments e2 ON e2.class_id = c.class_id
        //     WHERE sem.is_active = 1
        //     AND c.status = 'Active'
        //     AND c.class_id NOT IN (
        //         SELECT class_id FROM enrollments
        //         WHERE student_id = ? 
        //         AND status IN ('Enrolled','Completed')
        //     )
        //     GROUP BY c.class_id
        //     ORDER BY sub.subject_name
        // ");

        $stmt = $this->safePrepare("
    SELECT
        c.class_id,
        c.class_code,
        sub.subject_code,
        sub.subject_name,
        sub.credit_hours,
        CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
        sem.semester_name,
        COUNT(DISTINCT e2.enrollment_id) AS enrolled_count,
        GROUP_CONCAT(
            DISTINCT CONCAT(cs.day_of_week,'|',cs.start_period,'|',cs.end_period,'|',COALESCE(cs.room,''))
            ORDER BY cs.day_of_week, cs.start_period
            SEPARATOR ';'
        ) AS schedule_raw
    FROM classes c
    JOIN subjects sub ON sub.subject_id = c.subject_id
    JOIN semesters sem ON sem.semester_id = c.semester_id
    LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
    LEFT JOIN enrollments e2 ON e2.class_id = c.class_id
    LEFT JOIN class_schedules cs ON cs.class_id = c.class_id
    WHERE sem.is_active = 1
      AND c.status = 'Active'
      AND c.class_id NOT IN (
          SELECT class_id FROM enrollments
          WHERE student_id = ? 
          AND status IN ('Enrolled','Completed')
      )
    GROUP BY c.class_id
    ORDER BY sub.subject_name
");
        if (!$stmt) return [];

        // $stmt->bind_param('iii', $fromYear, $studentFacultyId, $studentId);
        $stmt->bind_param('i', $studentId);
        if (!$stmt->execute()) {
            error_log('[StudentModel::getAvailableClasses] Execute failed: ' . $stmt->error);
            return [];
        }
        // echo $stmt->sql;
        // exit;
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: [];
    }

    /**
     * Helper: Kiểm tra kỳ đăng ký học phần có mở không
     */
    public function isEnrollmentPeriodActive(?string $semester = null, ?int $year = null): bool
    {
        if (!$semester || !$year) {
            $semester = $semester ?? (date('m') <= 3 ? 'Spring' : (date('m') <= 6 ? 'Summer' : 'Fall'));
            $year = $year ?? (int)date('Y');
        }

        $stmt = $this->safePrepare("
            SELECT period_id FROM enrollment_registration_periods
            WHERE semester = ? AND year = ?
              AND is_active = 1
              AND enrollment_open <= NOW()
              AND enrollment_close >= NOW()
            LIMIT 1
        ");
        if (!$stmt) return false;

        $stmt->bind_param('si', $semester, $year);
        if (!$stmt->execute()) {
            error_log('[StudentModel::isEnrollmentPeriodActive] Execute failed: ' . $stmt->error);
            return false;
        }

        return $stmt->get_result()->num_rows > 0;
    }
    
    /**
     * Kiểm tra lớp học phần có tồn tại không
     */
    public function classExists(int $classId): bool
    {
        $stmt = $this->conn->prepare('SELECT class_id FROM classes WHERE class_id = ?');
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Kiểm tra sinh viên đã đăng ký lớp này chưa (active)
     */
    public function isAlreadyEnrolled(int $studentId, int $classId): bool
    {
        $stmt = $this->conn->prepare("
            SELECT enrollment_id FROM enrollments
            WHERE student_id = ? AND class_id = ? AND status IN ('Enrolled','Completed')
        ");
        $stmt->bind_param('ii', $studentId, $classId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Đăng ký học phần
     */
    // public function registerClass(int $studentId, int $classId): bool
    // {
    //     $stmt = $this->conn->prepare(
    //         "INSERT INTO enrollments (student_id, class_id, status) VALUES (?, ?, 'Enrolled')"
    //     );
    //     $stmt->bind_param('ii', $studentId, $classId);
    //     return $stmt->execute();
    // }

    public function registerClass($studentId, $classId)
    {
        // Kiểm tra sĩ số
        $sql = "SELECT max_students,
                (SELECT COUNT(*) FROM enrollments WHERE class_id = ?) as total
                FROM classes WHERE class_id = ?";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $classId, $classId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!$result) return false;

        if ($result['total'] >= $result['max_students']) {
            return 'full';
        }

        // Insert
        $sql = "INSERT INTO enrollments (student_id, class_id)
                VALUES (?, ?)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ii", $studentId, $classId);

        if (!$stmt->execute()) {

            if ($stmt->errno == 1062) {
                return 'duplicate';
            }

            return false;
        }

        return true;
    }

    /**
     * Hủy đăng ký học phần
     */
    public function cancelEnrollment(int $enrollmentId, int $studentId): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE enrollments SET status = 'Withdrawn'
            WHERE enrollment_id = ? AND student_id = ? AND status = 'Enrolled'
        ");
        $stmt->bind_param('ii', $enrollmentId, $studentId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    // ══════════════════════════════════════════════════════
    // LỊCH HỌC (SCHEDULE)
    // ══════════════════════════════════════════════════════

    /**
     * Lấy danh sách học kỳ sinh viên đang học (đã đăng ký)
     */
    // public function getEnrolledSemesters(int $studentId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT DISTINCT c.semester, c.year
    //         FROM enrollments e
    //         JOIN classes c ON c.class_id = e.class_id
    //         WHERE e.student_id = ? AND e.status = 'Enrolled'
    //         ORDER BY c.year DESC, FIELD(c.semester,'Spring','Summer','Fall')
    //     ");
    //     $stmt->bind_param('i', $studentId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getEnrolledSemesters(int $studentId): array
{
    $stmt = $this->conn->prepare("
        SELECT DISTINCT
            sem.semester_id,
            sem.semester_name,
            YEAR(sem.start_date) AS year

        FROM enrollments e

        JOIN classes c
            ON c.class_id = e.class_id

        JOIN semesters sem
            ON sem.semester_id = c.semester_id

        WHERE e.student_id = ?
        AND e.status = 'Enrolled'

        ORDER BY sem.start_date DESC
    ");

    $stmt->bind_param('i', $studentId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Lấy lịch học chi tiết theo học kỳ
     */
    // public function getSchedule(int $studentId, ?string $semester, ?int $year): array
    // {
    //     $whereExtra = '';
    //     $params     = [$studentId];
    //     $types      = 'i';

    //     if ($semester && $year) {
    //         $whereExtra = ' AND c.semester = ? AND c.year = ?';
    //         $params[]   = $semester;
    //         $params[]   = $year;
    //         $types     .= 'si';
    //     }

    //     $stmt = $this->conn->prepare("
    //         SELECT
    //             e.enrollment_id,
    //             c.class_id,
    //             c.class_code,
    //             c.semester,
    //             c.year,
    //             sub.subject_code,
    //             sub.subject_name,
    //             sub.credit_hours,
    //             CONCAT(l.last_name, ' ', l.first_name) AS lecturer_name,
    //             cs.schedule_id,
    //             cs.day_of_week,
    //             cs.start_period,
    //             cs.end_period,
    //             cs.room
    //         FROM enrollments e
    //         JOIN classes c         ON c.class_id     = e.class_id
    //         JOIN subjects sub      ON sub.subject_id  = c.subject_id
    //         LEFT JOIN lecturers l  ON l.lecturer_id   = c.lecturer_id
    //         LEFT JOIN class_schedules cs ON cs.class_id = c.class_id
    //         WHERE e.student_id = ? AND e.status = 'Enrolled'
    //         $whereExtra
    //         ORDER BY cs.day_of_week, cs.start_period
    //     ");
    //     $stmt->bind_param($types, ...$params);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getSchedule(int $studentId, ?int $semesterId): array
{
    $whereExtra = '';
    $params     = [$studentId];
    $types      = 'i';

    if ($semesterId) {
        $whereExtra = ' AND c.semester_id = ?';
        $params[]   = $semesterId;
        $types     .= 'i';
    }

    $stmt = $this->conn->prepare("
        SELECT
            e.enrollment_id,
            c.class_id,
            c.class_code,

            sem.semester_name AS semester,
            YEAR(sem.start_date) AS year,

            sub.subject_code,
            sub.subject_name,
            sub.credit_hours,

            CONCAT(l.last_name,' ',l.first_name) AS lecturer_name,

            cs.schedule_id,
            cs.day_of_week,
            cs.start_period,
            cs.end_period,
            cs.room

        FROM enrollments e

        JOIN classes c
            ON c.class_id = e.class_id

        JOIN subjects sub
            ON sub.subject_id = c.subject_id

        LEFT JOIN semesters sem
            ON sem.semester_id = c.semester_id

        LEFT JOIN lecturers l
            ON l.lecturer_id = c.lecturer_id

        LEFT JOIN class_schedules cs
            ON cs.class_id = c.class_id

        WHERE e.student_id = ?
        AND e.status = 'Enrolled'
        $whereExtra

        ORDER BY cs.day_of_week, cs.start_period
    ");

    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    /**
     * Lấy lịch học (dùng cho dashboard - giới hạn số lượng)
     */
    // public function getDashboardSchedule(int $studentId, int $limit = 5): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT
    //             c.class_code,
    //             sub.subject_name,
    //             CONCAT(l.first_name, ' ', l.last_name) AS lecturer_name,
    //             c.semester,
    //             c.year,
    //             e.status
    //         FROM enrollments e
    //         JOIN classes c   ON e.class_id    = c.class_id
    //         JOIN subjects sub ON c.subject_id = sub.subject_id
    //         JOIN lecturers l  ON c.lecturer_id = l.lecturer_id
    //         WHERE e.student_id = ? AND e.status = 'Enrolled'
    //         ORDER BY c.year DESC, c.semester DESC
    //         LIMIT ?
    //     ");
    //     if ($stmt === false) {
    //         error_log('[StudentModel::getDashboardSchedule] Prepare failed: ' . $this->conn->error);
    //         return [];
    //     }

    //     if (!$stmt->bind_param('ii', $studentId, $limit)) {
    //         error_log('[StudentModel::getDashboardSchedule] bind_param failed: ' . $stmt->error);
    //         return [];
    //     }

    //     if (!$stmt->execute()) {
    //         error_log('[StudentModel::getDashboardSchedule] execute failed: ' . $stmt->error);
    //         return [];
    //     }

    //     $res = $stmt->get_result();
    //     if ($res === false) {
    //         error_log('[StudentModel::getDashboardSchedule] get_result failed: ' . $stmt->error);
    //         return [];
    //     }

    //     return $res->fetch_all(MYSQLI_ASSOC);
    // }

    public function getDashboardSchedule(int $studentId, int $limit = 5): array
{
    $stmt = $this->conn->prepare("
        SELECT
            c.class_code,
            sub.subject_name,

            CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,

            sem.semester_name AS semester,
            YEAR(sem.start_date) AS year,

            e.status

        FROM enrollments e

        JOIN classes c
            ON e.class_id = c.class_id

        JOIN subjects sub
            ON c.subject_id = sub.subject_id

        LEFT JOIN lecturers l
            ON c.lecturer_id = l.lecturer_id

        LEFT JOIN semesters sem
            ON sem.semester_id = c.semester_id

        WHERE e.student_id = ?
        AND e.status = 'Enrolled'

        ORDER BY sem.start_date DESC
        LIMIT ?
    ");

    if ($stmt === false) {
        error_log('[StudentModel::getDashboardSchedule] Prepare failed: ' . $this->conn->error);
        return [];
    }

    if (!$stmt->bind_param('ii', $studentId, $limit)) {
        error_log('[StudentModel::getDashboardSchedule] bind_param failed: ' . $stmt->error);
        return [];
    }

    if (!$stmt->execute()) {
        error_log('[StudentModel::getDashboardSchedule] execute failed: ' . $stmt->error);
        return [];
    }

    $res = $stmt->get_result();

    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}

    // ══════════════════════════════════════════════════════
    // HỌC PHÍ (TUITION)
    // ══════════════════════════════════════════════════════

    /**
     * Lấy danh sách học kỳ + tổng tín chỉ đã đăng ký
     */
    // public function getEnrolledSemesterCredits(int $studentId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT c.semester, c.year,
    //                SUM(sub.credit_hours) AS total_credits
    //         FROM enrollments e
    //         JOIN classes  c   ON c.class_id    = e.class_id
    //         JOIN subjects sub ON sub.subject_id = c.subject_id
    //         WHERE e.student_id = ? AND e.status IN ('Enrolled','Completed','Withdrawn')
    //         GROUP BY c.semester, c.year
    //     ");
    //     $stmt->bind_param('i', $studentId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }
public function getEnrolledSemesterCredits(int $studentId): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            s.semester_name,
            YEAR(s.start_date) AS year,
            SUM(sub.credit_hours) AS total_credits
        FROM enrollments e
        JOIN classes c     ON c.class_id = e.class_id
        JOIN semesters s   ON s.semester_id = c.semester_id
        JOIN subjects sub  ON sub.subject_id = c.subject_id
        WHERE e.student_id = ?
        AND e.status IN ('Enrolled','Completed','Withdrawn')
        GROUP BY s.semester_name, YEAR(s.start_date)
        ORDER BY YEAR(s.start_date) DESC
    ");

    if (!$stmt) {
        die('SQL Error: ' . $this->conn->error);
    }

    $stmt->bind_param('i', $studentId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
    /**
     * Lấy giá tín chỉ theo học kỳ
     */
    public function getTuitionPricePerCredit(string $semester, int $year): float
    {
        $stmt = $this->conn->prepare("
            SELECT price_per_credit FROM tuition_settings
            WHERE semester = ? AND year = ?
            LIMIT 1
        ");
        $stmt->bind_param('si', $semester, $year);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ? (float)$row['price_per_credit'] : 550000.0;
    }

    /**
     * Lấy hoá đơn học phí theo học kỳ
     */
    public function getTuitionInvoice(int $studentId, string $semester, int $year): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT invoice_id, amount_paid, status FROM tuition_invoices
            WHERE student_id = ? AND semester = ? AND year = ?
        ");
        $stmt->bind_param('isi', $studentId, $semester, $year);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Tạo mới hoá đơn học phí
     */
    public function createTuitionInvoice(int $studentId, string $semester, int $year, int $credits, float $amount): bool
    {
        $dueDate = date('Y-m-d', strtotime('+30 days', mktime(
            0, 0, 0,
            $semester === 'Spring' ? 3 : ($semester === 'Summer' ? 6 : 9),
            1, $year
        )));
        $stmt = $this->conn->prepare("
            INSERT INTO tuition_invoices
                (student_id, semester, year, total_credits, amount_due, amount_paid, status, due_date)
            VALUES (?, ?, ?, ?, ?, 0, 'Unpaid', ?)
        ");
        $stmt->bind_param('isidds', $studentId, $semester, $year, $credits, $amount, $dueDate);
        return $stmt->execute();
    }

    /**
     * Cập nhật số tín chỉ và số tiền hoá đơn
     */
    public function updateTuitionInvoice(int $invoiceId, int $credits, float $amount, string $status): bool
    {
        $stmt = $this->conn->prepare("
            UPDATE tuition_invoices
            SET total_credits = ?, amount_due = ?, status = ?
            WHERE invoice_id = ?
        ");
        $stmt->bind_param('idsi', $credits, $amount, $status, $invoiceId);
        return $stmt->execute();
    }

    /**
     * Lấy tất cả hoá đơn của sinh viên
     */
    public function getAllTuitionInvoices(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT ti.*,
                   COALESCE(ts.price_per_credit, 550000) AS price_per_credit
            FROM tuition_invoices ti
            LEFT JOIN tuition_settings ts ON ts.semester = ti.semester AND ts.year = ti.year
            WHERE ti.student_id = ?
            ORDER BY ti.year DESC, FIELD(ti.semester,'Fall','Summer','Spring')
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy hoá đơn theo ID
     */
    public function getTuitionInvoiceById(int $invoiceId, int $studentId): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT * FROM tuition_invoices WHERE invoice_id = ? AND student_id = ?
        ");
        $stmt->bind_param('ii', $invoiceId, $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Nộp học phí (cập nhật amount_paid)
     */
    public function payTuition(int $invoiceId, float $currentPaid, float $amountDue, float $payAmount): bool
    {
        $newPaid   = min($currentPaid + $payAmount, $amountDue);
        $newStatus = $newPaid >= $amountDue ? 'Paid' : 'Partial';
        $paidAt    = $newStatus === 'Paid' ? date('Y-m-d H:i:s') : null;

        $stmt = $this->conn->prepare("
            UPDATE tuition_invoices
            SET amount_paid = ?, status = ?, paid_at = ?
            WHERE invoice_id = ?
        ");
        $stmt->bind_param('dssi', $newPaid, $newStatus, $paidAt, $invoiceId);
        return $stmt->execute();
    }

    // ══════════════════════════════════════════════════════
    // HỌC BỔNG (SCHOLARSHIP)
    // ══════════════════════════════════════════════════════

    /**
     * Tính GPA thang 100 (chỉ môn Completed)
     */
    public function getCompletedGPA(int $studentId): ?float
    {
        $stmt = $this->conn->prepare("
            SELECT ROUND(AVG(g.score), 2) AS gpa
            FROM enrollments e
            JOIN grades g ON g.enrollment_id = e.enrollment_id
            WHERE e.student_id = ? AND e.status = 'Completed'
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['gpa'] !== null ? (float)$row['gpa'] : null;
    }

    /**
     * Lấy danh sách học bổng đang mở
     */
    public function getAvailableScholarships(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT s.*,
                   (SELECT COUNT(*) FROM scholarship_applications sa
                    WHERE sa.scholarship_id = s.scholarship_id AND sa.status IN ('Pending','Approved')) AS applied_count,
                   (SELECT COUNT(*) FROM scholarship_applications sa
                    WHERE sa.scholarship_id = s.scholarship_id AND sa.student_id = ?)  AS my_applied
            FROM scholarships s
            WHERE s.is_active = 1
            ORDER BY s.year DESC, FIELD(s.semester,'Fall','Summer','Spring'), s.value DESC
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy chi tiết học bổng theo ID
     */
    public function getScholarshipById(int $scholarshipId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM scholarships WHERE scholarship_id = ? AND is_active = 1");
        $stmt->bind_param('i', $scholarshipId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Kiểm tra sinh viên đã đăng ký học bổng chưa
     */
    public function hasAppliedScholarship(int $studentId, int $scholarshipId): bool
    {
        $stmt = $this->conn->prepare("
            SELECT application_id FROM scholarship_applications
            WHERE student_id = ? AND scholarship_id = ?
        ");
        $stmt->bind_param('ii', $studentId, $scholarshipId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Đếm số lượng đăng ký học bổng đang xử lý/đã được duyệt
     */
    public function countScholarshipApplicants(int $scholarshipId): int
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS cnt FROM scholarship_applications
            WHERE scholarship_id = ? AND status IN ('Pending','Approved')
        ");
        $stmt->bind_param('i', $scholarshipId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    /**
     * Đăng ký học bổng
     */
    public function applyScholarship(int $studentId, int $scholarshipId): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO scholarship_applications (student_id, scholarship_id) VALUES (?, ?)
        ");
        $stmt->bind_param('ii', $studentId, $scholarshipId);
        return $stmt->execute();
    }

    /**
     * Hủy đơn đăng ký học bổng
     */
    public function cancelScholarshipApplication(int $applicationId, int $studentId): bool
    {
        $stmt = $this->conn->prepare("
            DELETE FROM scholarship_applications
            WHERE application_id = ? AND student_id = ? AND status = 'Pending'
        ");
        $stmt->bind_param('ii', $applicationId, $studentId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    /**
     * Lấy đơn đăng ký học bổng của sinh viên
     */
    public function getMyScholarshipApplications(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT sa.*, s.name, s.value, s.semester, s.year, s.description
            FROM scholarship_applications sa
            JOIN scholarships s ON s.scholarship_id = sa.scholarship_id
            WHERE sa.student_id = ?
            ORDER BY sa.applied_at DESC
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // KÝ TÚC XÁ (DORMITORY)
    // ══════════════════════════════════════════════════════

    /**
     * Lấy danh sách phòng ký túc xá còn chỗ
     */
    public function getAvailableDormRooms(): array
    {
        $result = $this->conn->query("
            SELECT * FROM dormitory_rooms
            WHERE is_active = 1 AND available_beds > 0
            ORDER BY room_type, room_number
        ");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    /**
     * Lấy thông tin phòng theo ID
     */
    public function getDormRoomById(int $roomId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM dormitory_rooms WHERE room_id = ? AND is_active = 1");
        $stmt->bind_param('i', $roomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Kiểm tra sinh viên đang có đăng ký ký túc xá
     */
    public function hasActiveDormRegistration(int $studentId): bool
    {
        $stmt = $this->conn->prepare("
            SELECT registration_id FROM dormitory_registrations
            WHERE student_id = ? AND status IN ('Pending','Active')
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Đăng ký ký túc xá
     */
    public function registerDormRoom(int $studentId, int $roomId, string $startDate, string $endDate): bool
    {
        $stmt = $this->conn->prepare("
            INSERT INTO dormitory_registrations (student_id, room_id, start_date, end_date, status)
            VALUES (?, ?, ?, ?, 'Pending')
        ");
        $stmt->bind_param('iiss', $studentId, $roomId, $startDate, $endDate);
        if (!$stmt->execute()) return false;

        // Giảm số giường trống
        $upd = $this->conn->prepare("UPDATE dormitory_rooms SET available_beds = available_beds - 1 WHERE room_id = ? AND available_beds > 0");
        $upd->bind_param('i', $roomId);
        $upd->execute();

        return true;
    }

    /**
     * Hủy đăng ký ký túc xá
     */
    public function cancelDormRegistration(int $registrationId, int $studentId): bool
    {
        // Lấy room_id trước
        $stmt = $this->conn->prepare("
            SELECT room_id FROM dormitory_registrations
            WHERE registration_id = ? AND student_id = ? AND status IN ('Pending','Active')
        ");
        $stmt->bind_param('ii', $registrationId, $studentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;

        // Hủy đăng ký
        $upd = $this->conn->prepare("
            UPDATE dormitory_registrations SET status = 'Cancelled'
            WHERE registration_id = ? AND student_id = ?
        ");
        $upd->bind_param('ii', $registrationId, $studentId);
        $upd->execute();
        if ($upd->affected_rows <= 0) return false;

        // Hoàn trả giường
        $restore = $this->conn->prepare("UPDATE dormitory_rooms SET available_beds = available_beds + 1 WHERE room_id = ?");
        $restore->bind_param('i', $row['room_id']);
        $restore->execute();

        return true;
    }

    /**
     * Lấy lịch sử đăng ký ký túc xá của sinh viên
     */
    // public function getDormRegistrations(int $studentId): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT dr.*, r.room_number, r.room_type, r.price_per_month, r.capacity
    //         FROM dormitory_registrations dr
    //         JOIN dormitory_rooms r ON r.room_id = dr.room_id
    //         WHERE dr.student_id = ?
    //         ORDER BY dr.created_at DESC
    //     ");
    //     $stmt->bind_param('i', $studentId);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

public function getDormRegistrations(int $studentId): array
{
    $stmt = $this->conn->prepare("
        SELECT 
            dr.*, 
            r.room_number,
            r.room_type,
            r.price_per_month,
            r.total_beds
        FROM dormitory_registrations dr
        JOIN dormitory_rooms r
            ON r.room_id = dr.room_id
        WHERE dr.student_id = ?
        ORDER BY dr.registered_at DESC
    ");

    if (!$stmt) {
        error_log("Prepare failed: " . $this->conn->error);
        return [];
    }

    $stmt->bind_param('i', $studentId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}



    // ══════════════════════════════════════════════════════
    // THƯ VIỆN (LIBRARY)
    // ══════════════════════════════════════════════════════

    /**
     * Tự động cập nhật trạng thái sách quá hạn
     */
    public function updateOverdueBooks(): void
    {
        $this->conn->query("
            UPDATE library_borrows
            SET status = 'Overdue',
                fine_amount = DATEDIFF(CURDATE(), due_date) * 5000
            WHERE status = 'Borrowed' AND due_date < CURDATE()
        ");
    }

    /**
     * Đếm số sách sinh viên đang mượn
     */
    public function countActiveBorrows(int $studentId): int
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS cnt FROM library_borrows
            WHERE student_id = ? AND status IN ('Borrowed','Overdue')
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    /**
     * Kiểm tra sinh viên đang mượn cuốn sách cụ thể
     */
    public function isBookBorrowedByStudent(int $studentId, int $bookId): bool
    {
        $stmt = $this->conn->prepare("
            SELECT borrow_id FROM library_borrows
            WHERE student_id = ? AND book_id = ? AND status IN ('Borrowed','Overdue')
        ");
        $stmt->bind_param('ii', $studentId, $bookId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }

    /**
     * Lấy thông tin sách theo ID
     */
    public function getBookById(int $bookId): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM library_books WHERE book_id = ? AND available_copies > 0");
        $stmt->bind_param('i', $bookId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    /**
     * Mượn sách
     */
    public function borrowBook(int $studentId, int $bookId, int $dueDays): bool
    {
        $dueDate = date('Y-m-d', strtotime("+{$dueDays} days"));
        $stmt = $this->conn->prepare("
            INSERT INTO library_borrows (student_id, book_id, due_date, status)
            VALUES (?, ?, ?, 'Borrowed')
        ");
        $stmt->bind_param('iis', $studentId, $bookId, $dueDate);
        if (!$stmt->execute()) return false;

        // Giảm số bản sao
        $upd = $this->conn->prepare("UPDATE library_books SET available_copies = available_copies - 1 WHERE book_id = ? AND available_copies > 0");
        $upd->bind_param('i', $bookId);
        $upd->execute();

        return true;
    }

    /**
     * Trả sách
     */
    public function returnBook(int $borrowId, int $studentId): bool
    {
        // Lấy book_id trước
        $stmt = $this->conn->prepare("
            SELECT book_id FROM library_borrows
            WHERE borrow_id = ? AND student_id = ? AND status IN ('Borrowed','Overdue')
        ");
        $stmt->bind_param('ii', $borrowId, $studentId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) return false;

        // Trả sách
        $upd = $this->conn->prepare("
            UPDATE library_borrows SET status = 'Returned', returned_at = NOW()
            WHERE borrow_id = ? AND student_id = ?
        ");
        $upd->bind_param('ii', $borrowId, $studentId);
        $upd->execute();
        if ($upd->affected_rows <= 0) return false;

        // Hoàn trả số bản sao
        $restore = $this->conn->prepare("UPDATE library_books SET available_copies = available_copies + 1 WHERE book_id = ?");
        $restore->bind_param('i', $row['book_id']);
        $restore->execute();

        return true;
    }

    /**
     * Lấy danh sách sách trong thư viện (tìm kiếm)
     */
    public function searchBooks(string $keyword = ''): array
    {
        if ($keyword) {
            $like = "%{$keyword}%";
            $stmt = $this->conn->prepare("
                SELECT * FROM library_books
                WHERE title LIKE ? OR author LIKE ? OR isbn LIKE ?
                ORDER BY title
            ");
            $stmt->bind_param('sss', $like, $like, $like);
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM library_books ORDER BY title");
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lấy lịch sử mượn sách của sinh viên
     */
    public function getBorrowHistory(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT lb.*, b.title, b.author, b.isbn
            FROM library_borrows lb
            JOIN library_books b ON b.book_id = lb.book_id
            WHERE lb.student_id = ?
            ORDER BY lb.borrow_date DESC
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}

// getEnrolledSemesters()
// getSchedule()
// getDashboardSchedule()
// getDormRegistrations()