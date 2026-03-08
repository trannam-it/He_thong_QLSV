<?php
/**
 * Class Controller
 * Quản lý lớp học phần
 */
class ClassController extends BaseController
{
    public function index()
    {
        $this->auth->requirePermission('manage_classes');
        
        $pagination = $this->getPagination();
        
        $query = "SELECT c.*, s.subject_name, l.first_name, l.last_name 
                  FROM classes c
                  LEFT JOIN subjects s ON c.subject_id = s.subject_id
                  LEFT JOIN lecturers l ON c.lecturer_id = l.lecturer_id
                  ORDER BY c.class_id DESC LIMIT ? OFFSET ?";
        
        $classes = $this->db->query($query, [$pagination['limit'], $pagination['offset']])->fetch_all(MYSQLI_ASSOC);
        
        $total = $this->db->count('classes');

        return Response::paginate($classes, $total, $pagination['page'], $pagination['limit']);
    }

    public function show()
    {
        $this->auth->requirePermission('manage_classes');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Class ID required', 400);

        $class = $this->db->selectOne('classes', 'class_id = ?', [$id]);
        if (!$class) return Response::error('Class not found', 404);

        // Get students in this class
        $students = $this->db->query(
            "SELECT s.* FROM students s
             JOIN enrollments e ON s.student_id = e.student_id
             WHERE e.class_id = ?",
            [$id]
        )->fetch_all(MYSQLI_ASSOC);

        $class['students'] = $students;
        return Response::success($class);
    }

    public function store()
    {
        $this->auth->requirePermission('manage_classes');
        
        $rules = [
            'class_code' => 'required|min:3',
            'subject_id' => 'required|numeric',
            'lecturer_id' => 'required|numeric',
            'semester' => 'required',
            'year' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($this->db->count('classes', 'class_code = ?', [$_POST['class_code']]) > 0) {
            return Response::error('Class code already exists', 400);
        }

        $classId = $this->db->insert('classes', [
            'class_code' => $_POST['class_code'],
            'subject_id' => $_POST['subject_id'],
            'lecturer_id' => $_POST['lecturer_id'],
            'semester' => $_POST['semester'],
            'year' => $_POST['year']
        ]);

        if ($classId) {
            $this->logAudit('CREATE', 'classes', $classId, null, $_POST);
            return Response::success(['id' => $classId], 'Class created', 201);
        }

        return Response::error('Failed to create class', 500);
    }

    public function update()
    {
        $this->auth->requirePermission('manage_classes');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Class ID required', 400);

        $class = $this->db->selectOne('classes', 'class_id = ?', [$id]);
        if (!$class) return Response::error('Class not found', 404);

        $rules = [
            'class_code' => 'required|min:3',
            'subject_id' => 'required|numeric',
            'lecturer_id' => 'required|numeric',
            'semester' => 'required',
            'year' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $updateData = [
            'class_code' => $_POST['class_code'],
            'subject_id' => $_POST['subject_id'],
            'lecturer_id' => $_POST['lecturer_id'],
            'semester' => $_POST['semester'],
            'year' => $_POST['year']
        ];

        $this->db->update('classes', $updateData, 'class_id = ?', [$id]);
        $this->logAudit('UPDATE', 'classes', $id, $class, $updateData);
        
        return Response::success(null, 'Class updated');
    }

    public function delete()
    {
        $this->auth->requirePermission('manage_classes');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Class ID required', 400);

        $class = $this->db->selectOne('classes', 'class_id = ?', [$id]);
        if (!$class) return Response::error('Class not found', 404);

        $this->db->delete('classes', 'class_id = ?', [$id]);
        $this->logAudit('DELETE', 'classes', $id, $class, null);
        
        return Response::success(null, 'Class deleted');
    }

    public function getEnrollments()
    {
        $this->auth->requirePermission('manage_classes');
        
        $classId = $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$classId) return Response::error('Class ID required', 400);
        
        $query = "SELECT e.*, s.student_id, s.student_code, s.first_name, s.last_name, 
                         c.class_code, sub.subject_name, e.created_at
                  FROM enrollments e
                  INNER JOIN students s ON e.student_id = s.student_id
                  INNER JOIN classes c ON e.class_id = c.class_id
                  LEFT JOIN subjects sub ON c.subject_id = sub.subject_id
                  WHERE e.class_id = ?
                  ORDER BY e.created_at DESC";
        
        $enrollments = $this->db->query($query, [$classId])->fetch_all(MYSQLI_ASSOC);
        
        return Response::success($enrollments ?? []);
    }

    // Lấy danh sách lớp học phần cho nhập điểm
    public function listForGrades()
    {
        $this->auth->requirePermission('manage_grades');

        $sql = "SELECT c.class_id,
                       c.class_code AS class_name,
                       sub.subject_name,
                       c.semester,
                       c.year
                FROM classes c
                JOIN subjects sub ON c.subject_id = sub.subject_id";

        $params = [];

        // Nếu là giảng viên → chỉ hiển thị lớp mình dạy
        if (in_array($this->auth->getRole(), ['teacher', 'lecturer'], true)) {
            $sql .= " JOIN lecturers l ON c.lecturer_id = l.lecturer_id
                      WHERE l.user_id = ?";
            $params[] = $this->auth->getId();
        }

        $sql .= " ORDER BY c.year DESC, c.class_code ASC";

        return Response::success(
            $this->db->query($sql, $params)->fetch_all(MYSQLI_ASSOC)
        );
    }
}
?>