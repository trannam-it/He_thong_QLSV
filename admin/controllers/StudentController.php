<?php
/**
 * Student Controller
 * Quản lý sinh viên
 */
class StudentController extends BaseController
{
    /**
     * Get all students
     */
    // public function index()
    // {
    //     $this->auth->requirePermission('manage_students');
        
    //     $pagination = $this->getPagination();
    //     $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        
    //     $where = '';
    //     $params = [];
        
    //     if ($search) {
    //         $where = '(student_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
    //         $params = [$search, $search, $search, $search];
    //     }
        
    //     $query = "SELECT s.*, f.faculty_name, u.id as user_id 
    //               FROM students s
    //               LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
    //               LEFT JOIN users u ON s.user_id = u.id";
        
    //     if ($where) {
    //         $query .= " WHERE $where";
    //     }
        
    //     $query .= " ORDER BY s.student_id DESC LIMIT ? OFFSET ?";
        
    //     $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
        
    //     $students = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
    //     // Count
    //     $countQuery = "
    //         SELECT COUNT(*) as total
    //         FROM students s
    //         LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
    //         LEFT JOIN users u ON s.user_id = u.id
    //     ";

    //     if ($where) {
    //         $countQuery .= " WHERE $where";
    //     }

        
    //     $countParams = array_values($params);
    //     $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

    //     return Response::paginate($students, $total, $pagination['page'], $pagination['limit']);
    // }

    public function index()
    {
        $this->auth->requirePermission('manage_students');

        $pagination = $this->getPagination();
        $searchRaw = $_GET['search'] ?? null;
        $search = $searchRaw ? '%' . $searchRaw . '%' : null;

        $where = '';
        $params = [];

        if ($search) {
            $where = '(s.student_code LIKE ? OR s.first_name LIKE ? OR s.last_name LIKE ? OR s.email LIKE ?)';
            $params = [$search, $search, $search, $search];
        }

        $query = "
            SELECT s.*, f.faculty_name, u.id as user_id
            FROM students s
            LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
            LEFT JOIN users u ON s.user_id = u.id
        ";

        if ($where) $query .= " WHERE $where";

        $query .= " ORDER BY s.student_id DESC LIMIT ? OFFSET ?";

        $students = $this->db->query(
            $query,
            array_merge($params, [$pagination['limit'], $pagination['offset']])
        )->fetch_all(MYSQLI_ASSOC);

        // COUNT chuẩn
        $countQuery = "
            SELECT COUNT(*) as total
            FROM students s
            LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
            LEFT JOIN users u ON s.user_id = u.id
        ";
        if ($where) $countQuery .= " WHERE $where";

        $total = $this->db->query($countQuery, $params)->fetch_assoc()['total'];

        return Response::paginate($students, $total, $pagination['page'], $pagination['limit']);
    }


    /**
     * Get single student
     */
    public function show()
    {
        $this->auth->requirePermission('manage_students');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Student ID required', 400);

        $query = "SELECT s.*, f.faculty_name, bc.base_class_name, bc.base_class_code
                  FROM students s
                  LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
                  LEFT JOIN base_classes bc ON s.base_class_id = bc.base_class_id
                  WHERE s.student_id = ?";
        $result = $this->db->query($query, [$id]);
        $student = $result ? $result->fetch_assoc() : null;
        
        if (!$student) return Response::error('Student not found', 404);

        return Response::success($student);
    }

    /**
     * Create student
     */
    public function store()
    {
        $this->auth->requirePermission('manage_students');
        
        $rules = [
            'student_code' => 'required|min:3',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'birth_date' => 'required|date',
            'gender' => 'required',
            'faculty_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        // Check duplicate code
        if ($this->db->count('students', 'student_code = ?', [$_POST['student_code']]) > 0) {
            return Response::error('Student code already exists', 400);
        }

        $studentId = $this->db->insert('students', [
            'student_code' => $_POST['student_code'],
            'first_name'   => $_POST['first_name'],
            'last_name'    => $_POST['last_name'],
            'email'        => $_POST['email'],
            'phone'        => $_POST['phone'] ?? null,
            'birth_date'   => $_POST['birth_date'],
            'gender'       => $_POST['gender'],
            'faculty_id'   => $_POST['faculty_id'],
            'base_class_id'=> $_POST['base_class_id'] ?? null,
            'status'       => $_POST['status'] ?? 'Studying'
        ]);

        if ($studentId) {
            $this->logAudit('CREATE', 'students', $studentId, null, $_POST);
            return Response::success(['id' => $studentId], 'Student created', 201);
        }

        return Response::error('Failed to create student', 500);
    }

    /**
     * Update student
     */
    public function update()
    {
        $this->auth->requirePermission('manage_students');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Student ID required', 400);

        $student = $this->db->selectOne('students', 'student_id = ?', [$id]);
        if (!$student) return Response::error('Student not found', 404);

        $rules = [
            'student_code' => 'required|min:3',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'birth_date' => 'required|date',
            'gender' => 'required'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $updateData = [
            'student_code'  => $_POST['student_code'],
            'first_name'    => $_POST['first_name'],
            'last_name'     => $_POST['last_name'],
            'email'         => $_POST['email'],
            'phone'         => $_POST['phone'] ?? null,
            'birth_date'    => $_POST['birth_date'],
            'gender'        => $_POST['gender'],
            'faculty_id'    => $_POST['faculty_id'],
            'base_class_id' => $_POST['base_class_id'] ?? null,
            'status'        => $_POST['status'] ?? 'Studying'
        ];

        $this->db->update('students', $updateData, 'student_id = ?', [$id]);
        $this->logAudit('UPDATE', 'students', $id, $student, $updateData);
        
        return Response::success(null, 'Student updated');
    }

    /**
     * Delete student
     */
    public function delete()
    {
        $this->auth->requirePermission('manage_students');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Student ID required', 400);

        $student = $this->db->selectOne('students', 'student_id = ?', [$id]);
        if (!$student) return Response::error('Student not found', 404);

        $this->db->delete('students', 'student_id = ?', [$id]);
        $this->logAudit('DELETE', 'students', $id, $student, null);
        
        return Response::success(null, 'Student deleted');
    }

    /**
     * Change student status (Studying, Suspended, Dropped, Graduated)
     */
    public function changeStatus()
    {
        $this->auth->requirePermission('manage_students');
        
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if (!$id || !$status) return Response::error('Student ID and status required', 400);
        
        $validStatuses = ['Studying', 'Suspended', 'Dropped', 'Graduated'];
        if (!in_array($status, $validStatuses)) {
            return Response::error('Invalid status', 400);
        }
        
        $student = $this->db->selectOne('students', 'student_id = ?', [$id]);
        if (!$student) return Response::error('Student not found', 404);
        
        $this->db->update('students', ['status' => $status], 'student_id = ?', [$id]);
        $this->logAudit('CHANGE_STATUS', 'students', $id, $student, ['new_status' => $status]);
        
        return Response::success(null, "Student status changed to $status");
    }

    /**
     * Create user account for student
     */
    public function createAccount()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentId = $_POST['student_id'] ?? null;
        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;
        
        if (!$studentId || !$username || !$password) {
            return Response::error('Student ID, username, and password required', 400);
        }
        
        $student = $this->db->selectOne('students', 'student_id = ?', [$studentId]);
        if (!$student) return Response::error('Student not found', 404);
        
        // Check if already has account
        if ($student['user_id']) {
            return Response::error('Student already has account', 400);
        }
        
        // Check duplicate username
        if ($this->db->count('users', 'username = ?', [$username]) > 0) {
            return Response::error('Username already exists', 400);
        }
        
        // Create user
        $userId = $this->db->insert('users', [
            'username'      => $username,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'email'         => $student['email'],
            'is_active'     => 1,
            'failed_attempts' => 0
        ]);
        
        if ($userId) {
            // Link student to user
            $this->db->update('students', ['user_id' => $userId], 'student_id = ?', [$studentId]);
            $this->logAudit('CREATE_ACCOUNT', 'students', $studentId, null, ['username' => $username]);
            return Response::success(['user_id' => $userId], 'User account created', 201);
        }
        
        return Response::error('Failed to create account', 500);
    }

    /**
     * Reset student account password
     */
    public function resetPassword()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentId = $_POST['student_id'] ?? null;
        $newPassword = $_POST['new_password'] ?? null;
        
        if (!$studentId || !$newPassword) {
            return Response::error('Student ID and new password required', 400);
        }
        
        $student = $this->db->selectOne('students', 'student_id = ?', [$studentId]);
        if (!$student) return Response::error('Student not found', 404);
        
        if (!$student['user_id']) {
            return Response::error('Student has no account', 400);
        }
        
        // Update user password
        $this->db->update('users',
            [
                'password_hash'   => password_hash($newPassword, PASSWORD_DEFAULT),
                'failed_attempts' => 0,
                'locked_until'    => null,
            ],
            'id = ?',
            [$student['user_id']]
        );
        
        $this->logAudit('RESET_PASSWORD', 'students', $studentId, null, []);
        return Response::success(null, 'Password reset successfully');
    }

    /**
     * Lock/Unlock student account
     */
    public function lockAccount()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentId = $_POST['student_id'] ?? null;
        $isLocked = $_POST['is_locked'] ?? null;
        
        if (!$studentId || $isLocked === null) {
            return Response::error('Student ID and lock status required', 400);
        }
        
        $student = $this->db->selectOne('students', 'student_id = ?', [$studentId]);
        if (!$student) return Response::error('Student not found', 404);
        
        if (!$student['user_id']) {
            return Response::error('Student has no account', 400);
        }
        
        // Update user is_active
        $active = $isLocked ? 0 : 1;
        $this->db->update('users',
            ['is_active' => $active],
            'id = ?',
            [$student['user_id']]
        );
        
        $action = $isLocked ? 'LOCK_ACCOUNT' : 'UNLOCK_ACCOUNT';
        $this->logAudit($action, 'students', $studentId, null, []);
        
        $msg = $isLocked ? 'Account locked' : 'Account unlocked';
        return Response::success(null, $msg);
    }

    /**
     * Assign students to faculty/class/major
     */
    public function assignFaculty()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentIds = isset($_POST['student_ids']) ? (is_array($_POST['student_ids']) ? $_POST['student_ids'] : explode(',', $_POST['student_ids'])) : [];
        $facultyId = $_POST['faculty_id'] ?? null;
        
        if (empty($studentIds) || !$facultyId) {
            return Response::error('Student IDs and faculty ID required', 400);
        }
        
        $updated = 0;
        foreach ($studentIds as $studentId) {
            $studentId = intval($studentId);
            if (!$studentId) continue;
            
            if ($this->db->count('students', 'student_id = ?', [$studentId]) === 0) {
                continue;
            }
            
            $this->db->update('students', ['faculty_id' => $facultyId], 'student_id = ?', [$studentId]);
            $updated++;
        }
        
        $this->logAudit('ASSIGN_FACULTY', 'students', 0, null, [
            'student_ids' => $studentIds,
            'faculty_id' => $facultyId,
            'updated_count' => $updated
        ]);
        
        return Response::success(['updated' => $updated], "$updated students assigned to faculty");
    }

    /**
     * Get student grades/transcript
     */
    public function getTranscript()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentId = $_GET['student_id'] ?? $_POST['student_id'] ?? null;
        if (!$studentId) return Response::error('Student ID required', 400);
        
        // grades → enrollments → classes → subjects  (correct join path)
        $query = "SELECT g.grade_id, g.score, g.grade_letter,
                         e.enrollment_id, e.status AS enroll_status,
                         sub.subject_code, sub.subject_name, sub.credit_hours,
                         c.class_code, c.semester, c.year
                  FROM enrollments e
                  LEFT JOIN grades   g   ON g.enrollment_id  = e.enrollment_id
                  INNER JOIN classes  c   ON c.class_id        = e.class_id
                  INNER JOIN subjects sub ON sub.subject_id    = c.subject_id
                  WHERE e.student_id = ?
                  ORDER BY c.year DESC, c.semester DESC";

        $grades = $this->db->query($query, [$studentId])->fetch_all(MYSQLI_ASSOC);

        // Calculate totals from actual score column
        $totalCredits = 0;
        $completedCredits = 0;
        $totalPoints = 0;
        $gradeCounts = 0;

        foreach ($grades as $g) {
            if ($g['score'] !== null) {
                $credits = (int)($g['credit_hours'] ?? 0);
                $totalCredits     += $credits;
                $completedCredits += $credits;
                $totalPoints      += (float)$g['score'] * $credits;   // weighted
                $gradeCounts      += $credits;
            }
        }

        $gpa = $gradeCounts > 0 ? round($totalPoints / $gradeCounts, 2) : 0;
        
        return Response::success([
            'grades' => $grades,
            'statistics' => [
                'total_credits' => $totalCredits,
                'completed_credits' => $completedCredits,
                'gpa' => $gpa
            ]
        ]);
    }

    /**
     * Get list of majors by faculty
     */
    public function getMajors()
    {
        // Note: 'majors' table does not exist in the current schema.
        // Majors are represented by faculties. Return empty for now.
        return Response::success([]);
    }

    /**
     * Get list of administrative classes
     */
    public function getStudentClasses()
    {
        // Note: 'student_classes' (administrative class) table does not exist in the current schema.
        // Return empty for now.
        return Response::success([]);
    }

    /**
     * Assign students to administrative class in bulk
     */
    public function assignClass()
    {
        $this->auth->requirePermission('manage_students');
        
        $studentIds = isset($_POST['student_ids']) ? (is_array($_POST['student_ids']) ? $_POST['student_ids'] : explode(',', $_POST['student_ids'])) : [];
        $classId = $_POST['class_id'] ?? null;
        
        if (empty($studentIds) || !$classId) {
            return Response::error('Student IDs and class ID required', 400);
        }
        
        $updated = 0;
        foreach ($studentIds as $studentId) {
            $studentId = intval($studentId);
            if (!$studentId) continue;
            
            if ($this->db->count('students', 'student_id = ?', [$studentId]) === 0) {
                continue;
            }
            
            $this->db->update('students', ['student_class_id' => $classId], 'student_id = ?', [$studentId]);
            $updated++;
        }
        
        $this->logAudit('ASSIGN_CLASS', 'students', 0, null, [
            'student_ids' => $studentIds,
            'class_id' => $classId,
            'updated_count' => $updated
        ]);
        
        return Response::success(['updated' => $updated], "$updated students assigned to class");
    }
}
?>

