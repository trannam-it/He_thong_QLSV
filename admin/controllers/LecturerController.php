<?php
/**
 * Lecturer Controller
 * Quản lý giảng viên
 */
class LecturerController extends BaseController
{
    // public function index()
    // {
    //     $this->auth->requirePermission('manage_lecturers');
        
    //     $pagination = $this->getPagination();
    //     $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        
    //     $where = '';
    //     $params = [];
        
    //     if ($search) {
    //         // $where = '(lecturer_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
    //         $conditions = [];
    //         $params = [];

    //         if (!empty($_GET['search'])) {
    //             $search = '%' . $_GET['search'] . '%';
    //             $conditions[] = '(lecturer_code LIKE ? OR first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
    //             $params = array_merge($params, [$search, $search, $search, $search]);
    //         }

    //         if (!empty($_GET['faculty_id'])) {
    //             $conditions[] = 'l.faculty_id = ?';
    //             $params[] = $_GET['faculty_id'];
    //         }

    //         $where = '';
    //         if (!empty($conditions)) {
    //             $where = 'WHERE ' . implode(' AND ', $conditions);
    //         }

    //         $params = [$search, $search, $search, $search];
    //     }
        
    //     // $query = "SELECT l.*, f.faculty_name FROM lecturers l
    //     //           LEFT JOIN faculties f ON l.faculty_id = f.faculty_id";

    //     $query = "SELECT l.*, f.faculty_name FROM lecturers l
    //       LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
    //       $where
    //       ORDER BY l.lecturer_id DESC
    //       LIMIT ? OFFSET ?";

        
    //     if ($where) $query .= " WHERE $where";
    //     $query .= " ORDER BY l.lecturer_id DESC LIMIT ? OFFSET ?";
        
    //     $allParams = array_merge($params, [$pagination['limit'], $pagination['offset']]);
    //     $lecturers = $this->db->query($query, $allParams)->fetch_all(MYSQLI_ASSOC);
        
    //     // $countQuery = "SELECT COUNT(*) as total FROM lecturers";
    //     $countQuery = "SELECT COUNT(*) as total FROM lecturers l $where";

    //     if ($where) $countQuery .= " WHERE $where";
        
    //     $countParams = array_values($params);
    //     $total = $this->db->query($countQuery, $countParams)->fetch_assoc()['total'];

    //     return Response::paginate($lecturers, $total, $pagination['page'], $pagination['limit']);
    // }

    public function index()
{
    $this->auth->requirePermission('manage_lecturers');

    $pagination = $this->getPagination();

    $conditions = [];
    $params = [];

    // 🔎 Search
    if (!empty($_GET['search'])) {
        $search = '%' . $_GET['search'] . '%';
        $conditions[] = '(l.lecturer_code LIKE ? 
                          OR l.first_name LIKE ? 
                          OR l.last_name LIKE ? 
                          OR l.email LIKE ?)';
        $params = array_merge($params, [$search, $search, $search, $search]);
    }

    // 🏫 Filter theo khoa
    if (!empty($_GET['faculty_id'])) {
        $conditions[] = 'l.faculty_id = ?';
        $params[] = $_GET['faculty_id'];
    }

    // Ghép WHERE
    $where = '';
    if (!empty($conditions)) {
        $where = 'WHERE ' . implode(' AND ', $conditions);
    }

    // ===== QUERY DATA =====
    $query = "
        SELECT l.*, f.faculty_name 
        FROM lecturers l
        LEFT JOIN faculties f 
            ON l.faculty_id = f.faculty_id
        $where
        ORDER BY l.lecturer_id DESC
        LIMIT ? OFFSET ?
    ";

    $dataParams = array_merge($params, [
        $pagination['limit'],
        $pagination['offset']
    ]);

    $lecturers = $this->db
        ->query($query, $dataParams)
        ->fetch_all(MYSQLI_ASSOC);

    // ===== QUERY COUNT =====
    $countQuery = "
        SELECT COUNT(*) as total 
        FROM lecturers l
        $where
    ";

    $total = $this->db
        ->query($countQuery, $params)
        ->fetch_assoc()['total'];

    return Response::paginate(
        $lecturers,
        $total,
        $pagination['page'],
        $pagination['limit']
    );
}


    public function show()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $id = $_GET['id'] ?? null;
        if (!$id) return Response::error('Lecturer ID required', 400);

        $query = "SELECT l.*, f.faculty_name FROM lecturers l
                  LEFT JOIN faculties f ON l.faculty_id = f.faculty_id
                  WHERE l.lecturer_id = ?";
        $result = $this->db->query($query, [$id]);
        $lecturer = $result ? $result->fetch_assoc() : null;
        
        if (!$lecturer) return Response::error('Lecturer not found', 404);

        return Response::success($lecturer);
    }

    public function store()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $rules = [
            'lecturer_code' => 'required|min:3',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'degree' => 'required',
            'faculty_id' => 'required|numeric'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        if ($this->db->count('lecturers', 'lecturer_code = ?', [$_POST['lecturer_code']]) > 0) {
            return Response::error('Lecturer code already exists', 400);
        }

        $lecturerId = $this->db->insert('lecturers', [
            'lecturer_code' => $_POST['lecturer_code'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? null,
            'degree' => $_POST['degree'],
            'faculty_id' => $_POST['faculty_id']
        ]);

        if ($lecturerId) {
            $this->logAudit('CREATE', 'lecturers', $lecturerId, null, $_POST);
            return Response::success(['id' => $lecturerId], 'Lecturer created', 201);
        }

        return Response::error('Failed to create lecturer', 500);
    }

    public function update()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Lecturer ID required', 400);

        $lecturer = $this->db->selectOne('lecturers', 'lecturer_id = ?', [$id]);
        if (!$lecturer) return Response::error('Lecturer not found', 404);

        $rules = [
            'lecturer_code' => 'required|min:3',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'degree' => 'required'
        ];

        if (!$this->validator->validate($_POST, $rules)) {
            return Response::error('Validation failed', 400, $this->validator->getErrors());
        }

        $updateData = [
            'lecturer_code' => $_POST['lecturer_code'],
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? null,
            'degree' => $_POST['degree'],
            'faculty_id' => $_POST['faculty_id']
        ];

        $this->db->update('lecturers', $updateData, 'lecturer_id = ?', [$id]);
        $this->logAudit('UPDATE', 'lecturers', $id, $lecturer, $updateData);
        
        return Response::success(null, 'Lecturer updated');
    }

    public function delete()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $id = $_POST['id'] ?? $_GET['id'] ?? null;
        if (!$id) return Response::error('Lecturer ID required', 400);

        $lecturer = $this->db->selectOne('lecturers', 'lecturer_id = ?', [$id]);
        if (!$lecturer) return Response::error('Lecturer not found', 404);

        $this->db->delete('lecturers', 'lecturer_id = ?', [$id]);
        $this->logAudit('DELETE', 'lecturers', $id, $lecturer, null);
        
        return Response::success(null, 'Lecturer deleted');
    }

    public function assignSubjects()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $lecturerId = $_POST['lecturer_id'] ?? null;
        $subjectIds = isset($_POST['subject_ids']) ? (is_array($_POST['subject_ids']) ? $_POST['subject_ids'] : explode(',', $_POST['subject_ids'])) : [];
        
        if (!$lecturerId) return Response::error('Lecturer ID required', 400);
        if (!$lecturerId = intval($lecturerId)) return Response::error('Invalid lecturer ID', 400);
        
        if (empty($subjectIds)) return Response::error('Subject IDs required', 400);
        
        // Verify lecturer exists
        if (!$this->db->selectOne('lecturers', 'lecturer_id = ?', [$lecturerId])) {
            return Response::error('Lecturer not found', 404);
        }

        $updated = 0;
        $errors = [];
        
        foreach ($subjectIds as $subjectId) {
            $subjectId = intval($subjectId);
            if (!$subjectId) continue;
            
            // Verify subject exists
            if (!$this->db->selectOne('subjects', 'subject_id = ?', [$subjectId])) {
                $errors[] = "Subject ID $subjectId not found";
                continue;
            }
            
            // Update all classes for this subject to assign to this lecturer
            $this->db->update('classes', 
                ['lecturer_id' => $lecturerId],
                'subject_id = ?',
                [$subjectId]
            );
            $updated++;
        }

        $this->logAudit('ASSIGN_SUBJECTS', 'lecturers', $lecturerId, null, [
            'subject_ids' => $subjectIds,
            'updated_count' => $updated
        ]);
        
        $message = "$updated subjects assigned to lecturer";
        if (!empty($errors)) $message .= '; Errors: ' . implode(', ', $errors);

        return Response::success(['updated' => $updated], $message);
    }

    public function assignClasses()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $lecturerId = $_POST['lecturer_id'] ?? null;
        $classIds = isset($_POST['class_ids']) ? (is_array($_POST['class_ids']) ? $_POST['class_ids'] : explode(',', $_POST['class_ids'])) : [];
        
        if (!$lecturerId) return Response::error('Lecturer ID required', 400);
        if (!$lecturerId = intval($lecturerId)) return Response::error('Invalid lecturer ID', 400);
        
        if (empty($classIds)) return Response::error('Class IDs required', 400);
        
        // Verify lecturer exists
        if (!$this->db->selectOne('lecturers', 'lecturer_id = ?', [$lecturerId])) {
            return Response::error('Lecturer not found', 404);
        }

        $updated = 0;
        $errors = [];
        
        foreach ($classIds as $classId) {
            $classId = intval($classId);
            if (!$classId) continue;
            
            // Verify class exists
            if (!$this->db->selectOne('classes', 'class_id = ?', [$classId])) {
                $errors[] = "Class ID $classId not found";
                continue;
            }
            
            // Assign lecturer to class
            $this->db->update('classes',
                ['lecturer_id' => $lecturerId],
                'class_id = ?',
                [$classId]
            );
            $updated++;
        }

        $this->logAudit('ASSIGN_CLASSES', 'lecturers', $lecturerId, null, [
            'class_ids' => $classIds,
            'updated_count' => $updated
        ]);
        
        $message = "$updated classes assigned to lecturer";
        if (!empty($errors)) $message .= '; Errors: ' . implode(', ', $errors);

        return Response::success(['updated' => $updated], $message);
    }

    public function getLecturerClasses()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $lecturerId = $_GET['lecturer_id'] ?? $_POST['lecturer_id'] ?? null;
        if (!$lecturerId) return Response::error('Lecturer ID required', 400);
        
        $query = "SELECT c.*, s.subject_code, s.subject_name, 
                  (SELECT COUNT(*) FROM enrollments WHERE class_id = c.class_id) as student_count
                  FROM classes c
                  LEFT JOIN subjects s ON c.subject_id = s.subject_id
                  WHERE c.lecturer_id = ?
                  ORDER BY c.year DESC, c.semester DESC";
        
        $classes = $this->db->query($query, [$lecturerId])->fetch_all(MYSQLI_ASSOC);
        
        return Response::success($classes ?? []);
    }

    public function getLecturerSubjects()
    {
        $this->auth->requirePermission('manage_lecturers');
        
        $lecturerId = $_GET['lecturer_id'] ?? $_POST['lecturer_id'] ?? null;
        if (!$lecturerId) return Response::error('Lecturer ID required', 400);
        
        $query = "SELECT DISTINCT s.* FROM subjects s
                  INNER JOIN classes c ON s.subject_id = c.subject_id
                  WHERE c.lecturer_id = ?
                  ORDER BY s.subject_code";
        
        $subjects = $this->db->query($query, [$lecturerId])->fetch_all(MYSQLI_ASSOC);
        
        return Response::success($subjects ?? []);
    }
}
?>