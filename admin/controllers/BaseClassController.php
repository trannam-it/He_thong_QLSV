<?php
/**
 * Base Class Controller
 * Quản lý lớp cơ sở (lớp hành chính)
 */
class BaseClassController extends BaseController
{
    /**
     * Lấy danh sách lớp cơ sở với phân trang
     */
    public function index()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $pagination = $this->getPagination();
        
        $query = "SELECT 
                    bc.*, 
                    f.faculty_code, f.faculty_name,
                    l.lecturer_code, l.first_name, l.last_name,
                    COUNT(DISTINCT s.student_id) as total_students,
                    SUM(CASE WHEN s.status = 'Studying' THEN 1 ELSE 0 END) as studying_students,
                    SUM(CASE WHEN s.status = 'Graduated' THEN 1 ELSE 0 END) as graduated_students,
                    SUM(CASE WHEN s.status = 'Suspended' THEN 1 ELSE 0 END) as suspended_students,
                    SUM(CASE WHEN s.status = 'Dropped' THEN 1 ELSE 0 END) as dropped_students
                  FROM base_classes bc
                  LEFT JOIN faculties f ON bc.faculty_id = f.faculty_id
                  LEFT JOIN lecturers l ON bc.lecturer_id = l.lecturer_id
                  LEFT JOIN students s ON bc.base_class_id = s.base_class_id
                  GROUP BY bc.base_class_id
                  ORDER BY bc.base_class_code DESC
                  LIMIT ? OFFSET ?";
        
        $classes = $this->db->query($query, [$pagination['limit'], $pagination['offset']])->fetch_all(MYSQLI_ASSOC);
        
        $total = $this->db->count('base_classes');

        return Response::paginate($classes, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Lấy chi tiết 1 lớp cơ sở
     */
    public function show()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Base Class ID required', 400);

        $query = "SELECT 
                    bc.*, 
                    f.faculty_code, f.faculty_name,
                    l.lecturer_code, l.first_name, l.last_name,
                    COUNT(DISTINCT s.student_id) as total_students,
                    SUM(CASE WHEN s.status = 'Studying' THEN 1 ELSE 0 END) as studying_students,
                    SUM(CASE WHEN s.status = 'Graduated' THEN 1 ELSE 0 END) as graduated_students,
                    SUM(CASE WHEN s.status = 'Suspended' THEN 1 ELSE 0 END) as suspended_students,
                    SUM(CASE WHEN s.status = 'Dropped' THEN 1 ELSE 0 END) as dropped_students
                  FROM base_classes bc
                  LEFT JOIN faculties f ON bc.faculty_id = f.faculty_id
                  LEFT JOIN lecturers l ON bc.lecturer_id = l.lecturer_id
                  LEFT JOIN students s ON bc.base_class_id = s.base_class_id
                  WHERE bc.base_class_id = ?
                  GROUP BY bc.base_class_id";

        $class = $this->db->query($query, [$id])->fetch_assoc();
        if (!$class) return Response::error('Base class not found', 404);

        return Response::success($class);
    }

    /**
     * Tạo lớp cơ sở mới
     */
    public function store()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'base_class_code' => 'required|min:3|max:20',
            'base_class_name' => 'required|min:5|max:100',
            'faculty_id' => 'required|numeric',
            'lecturer_id' => 'required|numeric',
            'start_year' => 'required|numeric|min:2000|max:2099',
            'end_year' => 'required|numeric|min:2000|max:2099'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        // Kiểm tra mã lớp không trùng
        if ($this->db->count('base_classes', 'base_class_code = ?', [$_POST['base_class_code']]) > 0) {
            return Response::error('Class code already exists', 400);
        }

        // Kiểm tra faculty tồn tại
        if (!$this->db->selectOne('faculties', 'faculty_id = ?', [$_POST['faculty_id']])) {
            return Response::error('Faculty not found', 404);
        }

        // Kiểm tra lecturer tồn tại
        if (!$this->db->selectOne('lecturers', 'lecturer_id = ?', [$_POST['lecturer_id']])) {
            return Response::error('Lecturer not found', 404);
        }

        // Kiểm tra end_year >= start_year
        if ((int)$_POST['end_year'] < (int)$_POST['start_year']) {
            return Response::error('End year must be >= start year', 400);
        }

        $classId = $this->db->insert('base_classes', [
            'base_class_code' => $_POST['base_class_code'],
            'base_class_name' => $_POST['base_class_name'],
            'faculty_id' => $_POST['faculty_id'],
            'lecturer_id' => $_POST['lecturer_id'],
            'start_year' => $_POST['start_year'],
            'end_year' => $_POST['end_year']
        ]);

        if ($classId) {
            $this->logAudit('CREATE', 'base_classes', $classId, null, $_POST);
            return Response::success(['id' => $classId], 'Base class created', 201);
        }

        return Response::error('Failed to create base class', 500);
    }

    /**
     * Cập nhật thông tin lớp cơ sở
     */
    public function update()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Base Class ID required', 400);

        $class = $this->db->selectOne('base_classes', 'base_class_id = ?', [$id]);
        if (!$class) return Response::error('Base class not found', 404);

        $rules = [
            'base_class_code' => 'required|min:3|max:20',
            'base_class_name' => 'required|min:5|max:100',
            'faculty_id' => 'required|numeric',
            'lecturer_id' => 'required|numeric',
            'start_year' => 'required|numeric|min:2000|max:2099',
            'end_year' => 'required|numeric|min:2000|max:2099'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        // Kiểm tra mã lớp không trùng (nếu thay đổi)
        if ($class['base_class_code'] !== $_POST['base_class_code']) {
            if ($this->db->count('base_classes', 'base_class_code = ?', [$_POST['base_class_code']]) > 0) {
                return Response::error('Class code already exists', 400);
            }
        }

        // Kiểm tra end_year >= start_year
        if ((int)$_POST['end_year'] < (int)$_POST['start_year']) {
            return Response::error('End year must be >= start year', 400);
        }

        $updateData = [
            'base_class_code' => $_POST['base_class_code'],
            'base_class_name' => $_POST['base_class_name'],
            'faculty_id' => $_POST['faculty_id'],
            'lecturer_id' => $_POST['lecturer_id'],
            'start_year' => $_POST['start_year'],
            'end_year' => $_POST['end_year']
        ];

        $this->db->update('base_classes', $updateData, 'base_class_id = ?', [$id]);
        $this->logAudit('UPDATE', 'base_classes', $id, $class, $updateData);
        
        return Response::success(null, 'Base class updated');
    }

    /**
     * Xóa lớp cơ sở
     */
    public function delete()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Base Class ID required', 400);

        $class = $this->db->selectOne('base_classes', 'base_class_id = ?', [$id]);
        if (!$class) return Response::error('Base class not found', 404);

        // Kiểm tra có sinh viên trong lớp không
        $studentCount = $this->db->count('students', 'base_class_id = ?', [$id]);
        if ($studentCount > 0) {
            return Response::error('Cannot delete class with students. Remove students first.', 400);
        }

        $this->db->delete('base_classes', 'base_class_id = ?', [$id]);
        $this->logAudit('DELETE', 'base_classes', $id, $class, null);
        
        return Response::success(null, 'Base class deleted');
    }

    /**
     * Lấy danh sách sinh viên trong lớp
     */
    public function getStudents()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $classId = $_GET['class_id'] ?? null;
        if (!$classId) return Response::error('Class ID required', 400);

        $pagination = $this->getPagination();

        $query = "SELECT 
                    s.*, 
                    f.faculty_name,
                    u.username, u.email as user_email
                  FROM students s
                  LEFT JOIN faculties f ON s.faculty_id = f.faculty_id
                  LEFT JOIN users u ON s.user_id = u.id
                  WHERE s.base_class_id = ?
                  ORDER BY s.student_code
                  LIMIT ? OFFSET ?";

        $students = $this->db->query($query, [$classId, $pagination['limit'], $pagination['offset']])->fetch_all(MYSQLI_ASSOC);
        $total = $this->db->count('students', 'base_class_id = ?', [$classId]);

        return Response::paginate($students, $total, $pagination['page'], $pagination['limit']);
    }

    /**
     * Gán sinh viên vào lớp (thủ công)
     */
    public function assignStudent()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'base_class_id' => 'required|numeric',
            'student_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $classId = $_POST['base_class_id'];
        $studentId = $_POST['student_id'];

        // Kiểm tra lớp tồn tại
        if (!$this->db->selectOne('base_classes', 'base_class_id = ?', [$classId])) {
            return Response::error('Base class not found', 404);
        }

        // Kiểm tra sinh viên tồn tại
        $student = $this->db->selectOne('students', 'student_id = ?', [$studentId]);
        if (!$student) return Response::error('Student not found', 404);

        // Kiểm tra sinh viên đã có lớp chưa
        if (!is_null($student['base_class_id'])) {
            return Response::error('Student is already assigned to a class. Use transfer instead.', 400);
        }

        // Cập nhật sinh viên
        $this->db->update('students', ['base_class_id' => $classId], 'student_id = ?', [$studentId]);
        $this->logAudit('ASSIGN_STUDENT', 'base_classes', $classId, null, ['student_id' => $studentId]);

        return Response::success(null, 'Student assigned to class', 201);
    }

    /**
     * Gán hàng loạt sinh viên theo khóa/ngành
     */
    public function bulkAssignStudents()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'base_class_id' => 'required|numeric',
            'filters' => 'required'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $classId = $_POST['base_class_id'];
        $filters = json_decode($_POST['filters'], true) ?? [];

        // Kiểm tra lớp tồn tại
        $class = $this->db->selectOne('base_classes', 'base_class_id = ?', [$classId]);
        if (!$class) return Response::error('Base class not found', 404);

        // Xây dựng query dựa trên filters
        $conditions = ["s.base_class_id IS NULL"];
        $params = [];

        if (!empty($filters['faculty_id'])) {
            $conditions[] = "s.faculty_id = ?";
            $params[] = $filters['faculty_id'];
        }

        // Nếu không có filter khoa, đừng gán
        if (count($conditions) === 1) {
            return Response::error('At least one filter (faculty) is required', 400);
        }

        $whereClause = implode(' AND ', $conditions);
        
        // Lấy sinh viên thỏa điều kiện
        $query = "SELECT student_id FROM students WHERE $whereClause";
        $result = $this->db->query($query, $params);
        
        if (!$result) {
            return Response::error('Query error', 500);
        }

        $studentIds = [];
        while ($row = $result->fetch_assoc()) {
            $studentIds[] = $row['student_id'];
        }

        if (empty($studentIds)) {
            return Response::error('No students matching the filters', 400);
        }

        // Cập nhật tất cả sinh viên
        $count = 0;
        foreach ($studentIds as $studentId) {
            $this->db->update('students', ['base_class_id' => $classId], 'student_id = ?', [$studentId]);
            $count++;
        }

        $this->logAudit('BULK_ASSIGN_STUDENTS', 'base_classes', $classId, null, [
            'count' => $count,
            'filters' => $filters
        ]);
        
        return Response::success(['count' => $count], 'Students bulk assigned', 201);
    }

    /**
     * Chuyển sinh viên sang lớp khác
     */
    public function transferStudent()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'student_id' => 'required|numeric',
            'from_class_id' => 'required|numeric',
            'to_class_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $studentId = $_POST['student_id'];
        $fromClassId = $_POST['from_class_id'];
        $toClassId = $_POST['to_class_id'];

        // Kiểm tra lớp đích tồn tại
        if (!$this->db->selectOne('base_classes', 'base_class_id = ?', [$toClassId])) {
            return Response::error('Destination class not found', 404);
        }

        // Kiểm tra sinh viên tồn tại và đang ở lớp nguồn
        $student = $this->db->selectOne('students', 'student_id = ? AND base_class_id = ?', [$studentId, $fromClassId]);
        if (!$student) return Response::error('Student not found in source class', 404);

        // Cập nhật lớp sinh viên
        $oldData = ['base_class_id' => $fromClassId];
        $newData = ['base_class_id' => $toClassId];

        $this->db->update('students', $newData, 'student_id = ?', [$studentId]);
        $this->logAudit('TRANSFER_STUDENT', 'base_classes', $toClassId, $oldData, $newData);

        return Response::success(null, 'Student transferred to new class', 200);
    }

    /**
     * Gán giảng viên chủ nhiệm (GVCN)
     */
    public function assignLecturer()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'base_class_id' => 'required|numeric',
            'lecturer_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $classId = $_POST['base_class_id'];
        $lecturerId = $_POST['lecturer_id'];

        // Kiểm tra lớp tồn tại
        $class = $this->db->selectOne('base_classes', 'base_class_id = ?', [$classId]);
        if (!$class) return Response::error('Base class not found', 404);

        // Kiểm tra giảng viên tồn tại
        if (!$this->db->selectOne('lecturers', 'lecturer_id = ?', [$lecturerId])) {
            return Response::error('Lecturer not found', 404);
        }

        $oldData = ['lecturer_id' => $class['lecturer_id']];
        $newData = ['lecturer_id' => $lecturerId];

        $this->db->update('base_classes', $newData, 'base_class_id = ?', [$classId]);
        $this->logAudit('UPDATE', 'base_classes', $classId, $oldData, $newData);

        return Response::success(null, 'Lecturer assigned to class', 200);
    }

    /**
     * Xóa sinh viên khỏi lớp
     */
    public function removeStudent()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $rules = [
            'student_id' => 'required|numeric',
            'base_class_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $studentId = $_POST['student_id'];
        $classId = $_POST['base_class_id'];

        // Kiểm tra sinh viên tồn tại trong lớp
        $student = $this->db->selectOne('students', 'student_id = ? AND base_class_id = ?', [$studentId, $classId]);
        if (!$student) return Response::error('Student not found in this class', 404);

        $this->db->update('students', ['base_class_id' => null], 'student_id = ?', [$studentId]);
        $this->logAudit('REMOVE_STUDENT', 'base_classes', $classId, ['student_id' => $studentId], null);

        return Response::success(null, 'Student removed from class', 200);
    }

    /**
     * Lấy thống kê sĩ số theo lớp
     */
    public function getClassStatistics()
    {
        $this->auth->requirePermission('manage_base_classes');
        
        $classId = $_GET['class_id'] ?? null;
        if (!$classId) return Response::error('Class ID required', 400);

        $query = "SELECT 
                    bc.base_class_id,
                    bc.base_class_code,
                    bc.base_class_name,
                    COUNT(DISTINCT s.student_id) as total_students,
                    SUM(CASE WHEN s.status = 'Studying' THEN 1 ELSE 0 END) as studying,
                    SUM(CASE WHEN s.status = 'Graduated' THEN 1 ELSE 0 END) as graduated,
                    SUM(CASE WHEN s.status = 'Suspended' THEN 1 ELSE 0 END) as suspended,
                    SUM(CASE WHEN s.status = 'Dropped' THEN 1 ELSE 0 END) as dropped
                  FROM base_classes bc
                  LEFT JOIN students s ON bc.base_class_id = s.base_class_id
                  WHERE bc.base_class_id = ?
                  GROUP BY bc.base_class_id";

        $stats = $this->db->query($query, [$classId])->fetch_assoc();
        if (!$stats) return Response::error('Class not found', 404);

        return Response::success($stats);
    }

    /**
     * Lấy thống kê theo khóa
     */
    public function getYearStatistics()
    {
        $this->auth->requirePermission('manage_base_classes');

        $query = "SELECT 
                    bc.start_year as year,
                    COUNT(DISTINCT bc.base_class_id) as total_classes,
                    COUNT(DISTINCT f.faculty_id) as total_faculties,
                    COUNT(DISTINCT s.student_id) as total_students,
                    SUM(CASE WHEN s.status = 'Studying' THEN 1 ELSE 0 END) as studying,
                    SUM(CASE WHEN s.status = 'Graduated' THEN 1 ELSE 0 END) as graduated,
                    SUM(CASE WHEN s.status = 'Suspended' THEN 1 ELSE 0 END) as suspended,
                    SUM(CASE WHEN s.status = 'Dropped' THEN 1 ELSE 0 END) as dropped
                  FROM base_classes bc
                  LEFT JOIN faculties f ON bc.faculty_id = f.faculty_id
                  LEFT JOIN students s ON bc.base_class_id = s.base_class_id
                  GROUP BY bc.start_year
                  ORDER BY bc.start_year DESC";

        $stats = $this->db->query($query, [])->fetch_all(MYSQLI_ASSOC);

        return Response::success($stats ?? []);
    }

    /**
     * Lấy thống kê theo khoa
     */
    public function getFacultyStatistics()
    {
        $this->auth->requirePermission('manage_base_classes');

        $query = "SELECT 
                    f.faculty_id,
                    f.faculty_code,
                    f.faculty_name,
                    COUNT(DISTINCT bc.base_class_id) as total_classes,
                    COUNT(DISTINCT s.student_id) as total_students,
                    SUM(CASE WHEN s.status = 'Studying' THEN 1 ELSE 0 END) as studying,
                    SUM(CASE WHEN s.status = 'Graduated' THEN 1 ELSE 0 END) as graduated,
                    SUM(CASE WHEN s.status = 'Suspended' THEN 1 ELSE 0 END) as suspended,
                    SUM(CASE WHEN s.status = 'Dropped' THEN 1 ELSE 0 END) as dropped
                  FROM faculties f
                  LEFT JOIN base_classes bc ON f.faculty_id = bc.faculty_id
                  LEFT JOIN students s ON bc.base_class_id = s.base_class_id
                  GROUP BY f.faculty_id
                  ORDER BY f.faculty_name ASC";

        $stats = $this->db->query($query, [])->fetch_all(MYSQLI_ASSOC);

        return Response::success($stats ?? []);
    }
}
?>
