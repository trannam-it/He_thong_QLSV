<?php
/**
 * AcademicModel - Model cho vai trò Quản lý Đào tạo
 * Chứa toàn bộ logic truy vấn DB cho module academic_admin
 */
class AcademicModel
{
    protected mysqli $conn;

    public function __construct(mysqli $conn)
    {
        $this->conn = $conn;
    }

    // ══════════════════════════════════════════════════════
    // USER INFO
    // ══════════════════════════════════════════════════════

    public function getUserInfo(int $userId): array
    {
        $stmt = $this->conn->prepare("
            SELECT u.id AS user_id, u.username, u.email,
                   r.name AS role_name, r.code AS role_code
            FROM users u
            LEFT JOIN user_roles ur ON ur.user_id = u.id
            LEFT JOIN roles r ON r.id = ur.role_id
            WHERE u.id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?? ['user_id' => $userId, 'username' => 'N/A', 'email' => '', 'role_name' => 'Quản lý Đào tạo', 'role_code' => 'academic_admin'];
    }

    // ══════════════════════════════════════════════════════
    // DASHBOARD STATISTICS
    // ══════════════════════════════════════════════════════

    public function getDashboardStats(): array
    {
        $stats = [];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM students WHERE status = 'Studying'");
        $stats['total_students'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM lecturers");
        $stats['total_lecturers'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM subjects");
        $stats['total_subjects'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM classes WHERE status = 'Active'");
        $stats['total_active_classes'] = (int)$res->fetch_assoc()['cnt'];

        // $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM enrollments WHERE status = 'Studying'");
        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM enrollments WHERE status = 'Enrolled'");
        $stats['total_enrollments'] = (int)$res->fetch_assoc()['cnt'];

        $res = $this->conn->query("SELECT COUNT(*) AS cnt FROM faculties");
        $stats['total_faculties'] = (int)$res->fetch_assoc()['cnt'];

        return $stats;
    }

    //     public function getDashboardStats(): array
    // {
    //     $stats = [];

    //     $queries = [
    //         'total_students'        => "SELECT COUNT(*) AS cnt FROM students WHERE status = 'Studying'",
    //         'total_lecturers'       => "SELECT COUNT(*) AS cnt FROM lecturers",
    //         'total_subjects'        => "SELECT COUNT(*) AS cnt FROM subjects",
    //         'total_active_classes'  => "SELECT COUNT(*) AS cnt FROM classes",
    //         'total_enrollments'     => "SELECT COUNT(*) AS cnt FROM enrollments WHERE status = 'Enrolled'",
    //         'total_faculties'       => "SELECT COUNT(*) AS cnt FROM faculties",
    //     ];

    //     foreach ($queries as $key => $sql) {
    //         $res = $this->conn->query($sql);

    //         if (!$res) {
    //             die("SQL Error: " . $this->conn->error . "<br>Query: " . $sql);
    //         }

    //         $stats[$key] = (int)$res->fetch_assoc()['cnt'];
    //     }

    //     return $stats;
    // }


    /** Lấy danh sách lớp đang mở (5 gần nhất) */
    // public function getRecentClasses(int $limit = 5): array
    // {
    //     $stmt = $this->conn->prepare("
    //         SELECT c.class_id, c.class_code, c.class_name,
    //                s.subject_name, s.credit_hours,
    //                CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,
    //                sem.semester_name, c.status,
    //                c.max_students,
    //                (SELECT COUNT(*) FROM enrollments e WHERE e.class_id = c.class_id AND e.status='Studying') AS enrolled
    //         FROM classes c
    //         LEFT JOIN subjects s ON s.subject_id = c.subject_id
    //         LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
    //         LEFT JOIN semesters sem ON sem.semester_id = c.semester_id
    //         ORDER BY c.class_id DESC
    //         LIMIT ?
    //     ");
    //     $stmt->bind_param('i', $limit);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getRecentClasses(int $limit = 5): array
    {
        $stmt = $this->conn->prepare("
            SELECT 
                c.class_id,
                c.class_code,
                s.subject_name,
                
                s.credit_hours AS credits,   -- đổi tên tại đây
                CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,
                sem.semester_name,
                c.max_students,
                c.status,
                (
                    SELECT COUNT(*) 
                    FROM enrollments e 
                    WHERE e.class_id = c.class_id 
                    AND e.status='Enrolled'
                ) AS enrolled
            FROM classes c
            LEFT JOIN subjects s 
                ON s.subject_id = c.subject_id
            LEFT JOIN lecturers l 
                ON l.lecturer_id = c.lecturer_id
            LEFT JOIN semesters sem
                ON sem.semester_id = c.semester_id
            ORDER BY c.class_id DESC
            LIMIT ?
        ");

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param('i', $limit);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }



    // ══════════════════════════════════════════════════════
    // STUDENTS
    // ══════════════════════════════════════════════════════

    public function getAllStudents(string $search = '', string $status = '', int $facultyId = 0): array
    {
        $sql = "
            SELECT s.student_id, s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name,
                   s.email, s.phone, s.gender, s.birth_date, s.status,
                   f.faculty_name, bc.base_class_name
            FROM students s
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            LEFT JOIN base_classes bc ON bc.base_class_id = s.base_class_id
            WHERE 1=1
        ";
        $params = [];
        $types  = '';

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (s.student_code LIKE ? OR CONCAT(s.first_name,' ',s.last_name) LIKE ? OR s.email LIKE ?)";
            $params[] = $like; $params[] = $like; $params[] = $like;
            $types .= 'sss';
        }
        if ($status !== '') {
            $sql .= " AND s.status = ?";
            $params[] = $status; $types .= 's';
        }
        if ($facultyId > 0) {
            $sql .= " AND s.faculty_id = ?";
            $params[] = $facultyId; $types .= 'i';
        }

        $sql .= " ORDER BY s.student_code ASC";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getStudentById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT s.*, f.faculty_name, bc.base_class_name
            FROM students s
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            LEFT JOIN base_classes bc ON bc.base_class_id = s.base_class_id
            WHERE s.student_id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getStudentGrades(int $studentId): array
    {
        $stmt = $this->conn->prepare("
            SELECT g.grade_id, s.subject_name, s.credit_hours,
                   c.class_code, sem.semester_name,
                   g.midterm_score, g.final_score, g.total_score, g.letter_grade, g.is_passed
            FROM grades g
            JOIN enrollments e ON e.enrollment_id = g.enrollment_id
            JOIN classes c ON c.class_id = e.class_id
            JOIN subjects s ON s.subject_id = c.subject_id
            JOIN semesters sem ON sem.semester_id = c.semester_id
            WHERE e.student_id = ?
            ORDER BY sem.start_date DESC
        ");
        $stmt->bind_param('i', $studentId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // SUBJECTS
    // ══════════════════════════════════════════════════════

    // public function getAllSubjects(string $search = ''): array
    // {
    //     $sql = "SELECT s.*, f.faculty_name FROM subjects s LEFT JOIN faculties f ON f.faculty_id = s.faculty_id WHERE 1=1";
    //     $params = []; $types = '';

    //     if ($search !== '') {
    //         $like = '%' . $search . '%';
    //         $sql .= " AND (s.subject_code LIKE ? OR s.subject_name LIKE ?)";
    //         $params[] = $like; $params[] = $like; $types .= 'ss';
    //     }
    //     $sql .= " ORDER BY s.subject_code ASC";
    //     $stmt = $this->conn->prepare($sql);
    //     if ($params) $stmt->bind_param($types, ...$params);
    //     $stmt->execute();
    //     return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // }

    public function getAllSubjects(string $search = ''): array
    {
        $sql = "
            SELECT 
                s.subject_id,
                s.subject_code,
                s.subject_name,
                s.credit_hours AS credits,   -- alias ở đây
                s.prerequisite_id,
                s.faculty_id,
                s.description,
                f.faculty_name
            FROM subjects s
            LEFT JOIN faculties f 
                ON f.faculty_id = s.faculty_id
            WHERE 1=1
        ";

        $params = []; 
        $types = '';

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (s.subject_code LIKE ? OR s.subject_name LIKE ?)";
            $params[] = $like; 
            $params[] = $like; 
            $types .= 'ss';
        }

        $sql .= " ORDER BY s.subject_code ASC";

        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    public function getSubjectById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT s.*, f.faculty_name FROM subjects s LEFT JOIN faculties f ON f.faculty_id = s.faculty_id WHERE s.subject_id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createSubject(array $data): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO subjects (subject_code, subject_name, credit_hours, faculty_id, description, prerequisite_id) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssiisi',
            $data['subject_code'], $data['subject_name'], $data['credit_hours'],
            $data['faculty_id'], $data['description'], $data['prerequisite_id']
        );
        return $stmt->execute();
    }

    public function updateSubject(int $id, array $data): bool
    {
        $stmt = $this->conn->prepare("UPDATE subjects SET subject_code=?, subject_name=?, credit_hours=?, faculty_id=?, description=?, prerequisite_id=? WHERE subject_id=?");
        $stmt->bind_param('ssiisii',
            $data['subject_code'], $data['subject_name'], $data['credit_hours'],
            $data['faculty_id'], $data['description'], $data['prerequisite_id'], $id
        );
        return $stmt->execute();
    }

    public function deleteSubject(int $id): bool
    {
        $stmt = $this->conn->prepare("DELETE FROM subjects WHERE subject_id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // ══════════════════════════════════════════════════════
    // CLASSES
    // ══════════════════════════════════════════════════════

    public function getAllClasses(string $search = '', int $semesterId = 0): array
    {
        $sql = "
            SELECT 
                c.class_id,
                c.class_code AS class_name,
                c.status,
                c.max_students,
                s.subject_name,
                s.credit_hours AS credits,
                CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,
                sem.semester_name,
                (
                    SELECT COUNT(*) 
                    FROM enrollments e 
                    WHERE e.class_id = c.class_id 
                    AND e.status='Studying'
                ) AS enrolled
            FROM classes c
            LEFT JOIN subjects s 
                ON s.subject_id = c.subject_id
            LEFT JOIN lecturers l 
                ON l.lecturer_id = c.lecturer_id
            LEFT JOIN semesters sem 
                ON sem.semester_id = c.semester_id
            WHERE 1=1
        ";

        $params = [];
        $types = '';

        if ($search !== '') {
            $like = '%' . $search . '%';
            $sql .= " AND (c.class_code LIKE ? OR s.subject_name LIKE ?)";
            $params[] = $like;
            $params[] = $like;
            $types .= 'ss';
        }

        if ($semesterId > 0) {
            $sql .= " AND c.semester_id = ?";
            $params[] = $semesterId;
            $types .= 'i';
        }

        $sql .= " ORDER BY c.class_id DESC";

        $stmt = $this->conn->prepare($sql);

        // if (!$stmt) {
        //     die("Prepare failed: " . $this->conn->error);
        // }

        if ($params) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

public function createClass($data)
{
    $sql = "INSERT INTO classes 
            (class_code, subject_id, lecturer_id, semester_id, max_students, status)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $this->conn->prepare($sql);

      if (!$stmt) {
        return false;
    }


    // s = string
    // i = integer
    $stmt->bind_param(
        "siiiis",
        $data['class_code'],
        $data['subject_id'],
        $data['lecturer_id'],
        $data['semester_id'],
        $data['max_students'],
        $data['status']
    );

     if (!$stmt->execute()) {

        if ($stmt->errno == 1062) {
            return 'duplicate';
        }

        return false;
    }

    return true;
}

//     public function createClass($data)
// {
//     $sql = "INSERT INTO classes (name, course_id, semester_id) VALUES (?, ?, ?)";
//     $stmt = $this->conn->prepare($sql);

//     if (!$stmt->execute([
//         $data['name'],
//         $data['course_id'],
//         $data['semester_id']
//     ])) {
//         print_r($stmt->errorInfo());
//         exit;
//     }

//     return true;
// }

    public function getClassById(int $id): ?array
    {
        $stmt = $this->conn->prepare("
            SELECT c.*, s.subject_name, s.credit_hours,
                   CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,
                   sem.semester_name
            FROM classes c
            LEFT JOIN subjects s ON s.subject_id = c.subject_id
            LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
            LEFT JOIN semesters sem ON sem.semester_id = c.semester_id
            WHERE c.class_id = ? LIMIT 1
        ");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getClassStudents(int $classId): array
    {
        $stmt = $this->conn->prepare("
            SELECT e.enrollment_id, e.status AS enrollment_status,
                   s.student_id, s.student_code,
                   CONCAT(s.first_name,' ',s.last_name) AS full_name,
                   s.email, f.faculty_name
            FROM enrollments e
            JOIN students s ON s.student_id = e.student_id
            LEFT JOIN faculties f ON f.faculty_id = s.faculty_id
            WHERE e.class_id = ?
            ORDER BY s.student_code
        ");
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // SEMESTERS
    // ══════════════════════════════════════════════════════

    public function getAllSemesters(): array
    {
        $result = $this->conn->query("SELECT * FROM semesters ORDER BY start_date DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSemesterById(int $id): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM semesters WHERE semester_id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function createSemester(array $data): bool
    {
        $stmt = $this->conn->prepare("INSERT INTO semesters (semester_name, semester, year, start_date, end_date, is_current) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param('ssissi',
            $data['semester_name'], $data['semester'], $data['year'],
            $data['start_date'], $data['end_date'], $data['is_current']
        );
        return $stmt->execute();
    }

    public function updateSemester(int $id, array $data): bool
    {
        $stmt = $this->conn->prepare("UPDATE semesters SET semester_name=?, semester=?, year=?, start_date=?, end_date=?, is_current=? WHERE semester_id=?");
        $stmt->bind_param('ssissii',
            $data['semester_name'], $data['semester'], $data['year'],
            $data['start_date'], $data['end_date'], $data['is_current'], $id
        );
        return $stmt->execute();
    }

    public function setCurrentSemester(int $id): bool
    {
        $this->conn->query("UPDATE semesters SET is_current = 0");
        $stmt = $this->conn->prepare("UPDATE semesters SET is_current = 1 WHERE semester_id = ?");
        $stmt->bind_param('i', $id);
        return $stmt->execute();
    }

    // ══════════════════════════════════════════════════════
    // ENROLLMENTS
    // ══════════════════════════════════════════════════════

    public function getAllEnrollments(int $semesterId = 0, string $status = ''): array
{
    $sql = "
        SELECT 
            e.enrollment_id,
            e.status,
            e.enrollment_date,
            CONCAT(s.first_name,' ',s.last_name) AS student_name,
            s.student_code,
            c.class_code,
            sub.subject_name,
            sub.credit_hours AS credits,
            sem.semester_name
        FROM enrollments e
        JOIN students s ON s.student_id = e.student_id
        JOIN classes c ON c.class_id = e.class_id
        JOIN subjects sub ON sub.subject_id = c.subject_id
        JOIN semesters sem ON sem.semester_id = c.semester_id
        WHERE 1=1
    ";

    $params = [];
    $types = '';

    if ($semesterId > 0) {
        $sql .= " AND c.semester_id = ?";
        $params[] = $semesterId;
        $types .= 'i';
    }

    if ($status !== '') {
        $sql .= " AND e.status = ?";
        $params[] = $status;
        $types .= 's';
    }

    $sql .= " ORDER BY e.enrollment_date DESC LIMIT 200";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $this->conn->error);
    }

    if ($params) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}


    // ══════════════════════════════════════════════════════
    // GRADES
    // ══════════════════════════════════════════════════════

    public function getGradesByClass(int $classId): array
    {
        $stmt = $this->conn->prepare("
            SELECT g.grade_id, g.midterm_score, g.final_score, g.total_score, g.letter_grade, g.is_passed,
                   CONCAT(s.first_name,' ',s.last_name) AS student_name, s.student_code,
                   e.enrollment_id
            FROM grades g
            JOIN enrollments e ON e.enrollment_id = g.enrollment_id
            JOIN students s ON s.student_id = e.student_id
            WHERE e.class_id = ?
            ORDER BY s.student_code
        ");
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getGradeStatsByClass(int $classId): array
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*) AS total,
                   AVG(g.total_score) AS avg_score,
                   MAX(g.total_score) AS max_score,
                   MIN(g.total_score) AS min_score,
                   SUM(CASE WHEN g.is_passed=1 THEN 1 ELSE 0 END) AS passed
            FROM grades g
            JOIN enrollments e ON e.enrollment_id = g.enrollment_id
            WHERE e.class_id = ?
        ");
        $stmt->bind_param('i', $classId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: [];
    }

    // ══════════════════════════════════════════════════════
    // SCHEDULE
    // ══════════════════════════════════════════════════════

    public function getSchedule(int $semesterId = 0): array
    {
        $sql = "
            SELECT cs.schedule_id, cs.day_of_week, cs.start_period, cs.end_period, cs.room,
                   c.class_code,
                   sub.subject_name,
                   CONCAT(l.first_name,' ',l.last_name) AS lecturer_name,
                   sem.semester_name
            FROM class_schedules cs
            JOIN classes c ON c.class_id = cs.class_id
            JOIN subjects sub ON sub.subject_id = c.subject_id
            LEFT JOIN lecturers l ON l.lecturer_id = c.lecturer_id
            JOIN semesters sem ON sem.semester_id = c.semester_id
            WHERE 1=1
        ";
        $params = []; $types = '';

        if ($semesterId > 0) {
            $sql .= " AND c.semester_id = ?";
            $params[] = $semesterId; $types .= 'i';
        }
        $sql .= " ORDER BY cs.day_of_week, cs.start_period";
        $stmt = $this->conn->prepare($sql);
        if ($params) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // REPORTS
    // ══════════════════════════════════════════════════════

    public function getGradeReportBySemester(int $semesterId): array
    {
        $sql = "
            SELECT 
                sub.subject_name,
                sub.credit_hours AS credits,
                c.class_code,
                COUNT(g.grade_id) AS total,
                AVG(g.score) AS avg_score,
                SUM(CASE WHEN g.score >= 5 THEN 1 ELSE 0 END) AS passed,
                SUM(CASE WHEN g.score < 5 THEN 1 ELSE 0 END) AS failed
            FROM grades g
            JOIN enrollments e ON e.enrollment_id = g.enrollment_id
            JOIN classes c ON c.class_id = e.class_id
            JOIN subjects sub ON sub.subject_id = c.subject_id
            WHERE c.semester_id = ?
            GROUP BY c.class_id
            ORDER BY sub.subject_name
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param('i', $semesterId);
        $stmt->execute();

        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }



    public function getEnrollmentStatsByFaculty(): array
    {
        $result = $this->conn->query("
            SELECT f.faculty_name,
                   COUNT(DISTINCT s.student_id) AS total_students,
                   COUNT(DISTINCT e.enrollment_id) AS total_enrollments
            FROM faculties f
            LEFT JOIN students s ON s.faculty_id = f.faculty_id AND s.status='Studying'
            LEFT JOIN enrollments e ON e.student_id = s.student_id AND e.status='Studying'
            GROUP BY f.faculty_id
            ORDER BY f.faculty_name
        ");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // ══════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════

    public function getAllFaculties(): array
    {
        $result = $this->conn->query("SELECT faculty_id, faculty_name FROM faculties ORDER BY faculty_name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getAllLecturers(): array
    {
        $result = $this->conn->query("SELECT lecturer_id, CONCAT(first_name,' ',last_name) AS full_name FROM lecturers ORDER BY first_name");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getCurrentSemester(): ?array
    {
        $result = $this->conn->query("SELECT * FROM semesters WHERE is_current = 1 LIMIT 1");
        return $result->fetch_assoc() ?: null;
    }

    // public function getCurrentSemester(): ?array
    // {
    //     $result = $this->conn->query("SELECT * FROM semesters WHERE is_current = 1 LIMIT 1");

    //     if (!$result) {
    //         die("SQL Error: " . $this->conn->error);
    //     }

    //     return $result->fetch_assoc() ?: null;
    // }


    public function updateUserProfile(int $userId, string $email): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET email = ? WHERE id = ?");
        $stmt->bind_param('si', $email, $userId);
        return $stmt->execute();
    }

    public function changePassword(int $userId, string $newHash): bool
    {
        $stmt = $this->conn->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $stmt->bind_param('si', $newHash, $userId);
        return $stmt->execute();
    }

    public function getPasswordHash(int $userId): ?string
    {
        $stmt = $this->conn->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row['password_hash'] ?? null;
    }
}

