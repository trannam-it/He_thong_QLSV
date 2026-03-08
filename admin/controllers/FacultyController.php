<?php
/**
 * Faculty Controller
 * Quản lý khoa
 */
class FacultyController extends BaseController
{
    // public function index()
    // {
    //     $this->auth->requirePermission('manage_faculties');
        
    //     $pagination = $this->getPagination();
    //     $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        
    //     $where = '';
    //     $params = [];
        
    //     if ($search) {
    //         $where = '(faculty_code LIKE ? OR faculty_name LIKE ?)';
    //         $params = [$search, $search];
    //     }
        
    //     $query = "SELECT * FROM faculties";
    //     if ($where) $query .= " WHERE $where";
    //     $query .= " ORDER BY faculty_id DESC LIMIT ? OFFSET ?";
        
    //     $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
    //     $faculties = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
    //     $countQuery = "SELECT COUNT(*) as total FROM faculties";
    //     if ($where) $countQuery .= " WHERE $where";
        
    //     $countParams = array_values($params);
    //     $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

    //     return Response::paginate($faculties, $total, $pagination['page'], $pagination['limit']);
    // }

  public function index()
{
    $this->auth->requirePermission('manage_faculties');
    
    $pagination = $this->getPagination();
    $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;

    $where = '';
    $params = [];

    if ($search) {
        $where = '(faculty_code LIKE ? OR faculty_name LIKE ?)';
        $params = [$search, $search];
    }

    // Lấy danh sách
    $faculties = $this->db->getAll(
        'faculties',
        $pagination['limit'],
        $pagination['offset'],
        $where,
        $params
    );

    // Đếm tổng
    $total = $this->db->count(
        'faculties',
        $where,
        $params
    );

    return Response::paginate(
        $faculties,
        $total,
        $pagination['page'],
        $pagination['limit']
    );
}

    public function show()
    {
        $this->auth->requirePermission('manage_faculties');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Faculty ID required', 400);

        $faculty = $this->db->selectOne('faculties', 'faculty_id = ?', [$id]);
        if (!$faculty) return Response::error('Faculty not found', 404);

        return Response::success($faculty);
    }

    public function store()
    {
        $this->auth->requirePermission('manage_faculties');
        
        $rules = [
            'faculty_code' => 'required|min:2',
            'faculty_name' => 'required|min:3'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($this->db->count('faculties', 'faculty_code = ?', [$_POST['faculty_code']]) > 0) {
            return Response::error('Faculty code already exists', 400);
        }

        $facultyId = $this->db->insert('faculties', [
            'faculty_code' => $_POST['faculty_code'],
            'faculty_name' => $_POST['faculty_name'],
            'description' => $_POST['description'] ?? null
        ]);

        if ($facultyId) {
            $this->logAudit('CREATE', 'faculties', $facultyId, null, $_POST);
            return Response::success(['id' => $facultyId], 'Faculty created', 201);
        }

        return Response::error('Failed to create faculty', 500);
    }

    public function update()
    {
        $this->auth->requirePermission('manage_faculties');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Faculty ID required', 400);

        $faculty = $this->db->selectOne('faculties', 'faculty_id = ?', [$id]);
        if (!$faculty) return Response::error('Faculty not found', 404);

        $rules = [
            'faculty_code' => 'required|min:2',
            'faculty_name' => 'required|min:3'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $updateData = [
            'faculty_code' => $_POST['faculty_code'],
            'faculty_name' => $_POST['faculty_name'],
            'description' => $_POST['description'] ?? null
        ];

        $this->db->update('faculties', $updateData, 'faculty_id = ?', [$id]);
        $this->logAudit('UPDATE', 'faculties', $id, $faculty, $updateData);
        
        return Response::success(null, 'Faculty updated');
    }

    public function delete()
    {
        $this->auth->requirePermission('manage_faculties');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Faculty ID required', 400);

        $faculty = $this->db->selectOne('faculties', 'faculty_id = ?', [$id]);
        if (!$faculty) return Response::error('Faculty not found', 404);

        $this->db->delete('faculties', 'faculty_id = ?', [$id]);
        $this->logAudit('DELETE', 'faculties', $id, $faculty, null);
        
        return Response::success(null, 'Faculty deleted');
    }

    /**
     * Assign multiple lecturers to a faculty (bulk)
     */
    public function assignLecturers()
    {
        $this->auth->requirePermission('manage_faculties');

        $faculty_id = $_POST['faculty_id'] ?? null;
        $lecturers = $_POST['lecturers'] ?? [];

        if (!$faculty_id) return Response::error('Faculty ID required', 400);
        if (!is_array($lecturers) || empty($lecturers)) return Response::error('No lecturers provided', 400);

        $faculty = $this->db->selectOne('faculties', 'faculty_id = ?', [$faculty_id]);
        if (!$faculty) return Response::error('Faculty not found', 404);

        $updated = 0;
        foreach ($lecturers as $lid) {
            $lid = (int)$lid;
            $old = $this->db->selectOne('lecturers', 'lecturer_id = ?', [$lid]);
            if (!$old) continue;
            $this->db->update('lecturers', ['faculty_id' => $faculty_id], 'lecturer_id = ?', [$lid]);
            $this->logAudit('UPDATE', 'lecturers', $lid, $old, ['faculty_id' => $faculty_id]);
            $updated++;
        }

        return Response::success(['updated' => $updated], 'Assigned lecturers');
    }

    /**
     * Assign multiple students to a faculty (bulk)
     */
    public function assignStudents()
    {
        $this->auth->requirePermission('manage_faculties');

        $faculty_id = $_POST['faculty_id'] ?? null;
        $students = $_POST['students'] ?? [];

        if (!$faculty_id) return Response::error('Faculty ID required', 400);
        if (!is_array($students) || empty($students)) return Response::error('No students provided', 400);

        $faculty = $this->db->selectOne('faculties', 'faculty_id = ?', [$faculty_id]);
        if (!$faculty) return Response::error('Faculty not found', 404);

        $updated = 0;
        foreach ($students as $sid) {
            $sid = (int)$sid;
            $old = $this->db->selectOne('students', 'student_id = ?', [$sid]);
            if (!$old) continue;
            $this->db->update('students', ['faculty_id' => $faculty_id], 'student_id = ?', [$sid]);
            $this->logAudit('UPDATE', 'students', $sid, $old, ['faculty_id' => $faculty_id]);
            $updated++;
        }

        return Response::success(['updated' => $updated], 'Assigned students');
    }
}
?>